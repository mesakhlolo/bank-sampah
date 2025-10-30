<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class CustomerPortalController extends Controller
{
    /**
     * Show check code form
     */
    public function showCheckCode()
    {
        // If already in session, redirect to dashboard
        if (session()->has('portal_customer_id')) {
            return redirect()->route('customer-portal.dashboard');
        }

        return view('customer-portal.check-code');
    }

    /**
     * 🔒 Verify customer unique code with SECURITY MEASURES
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'unique_code' => ['required', 'string', 'size:16'], // BSB- + 12 chars
        ], [
            'unique_code.required' => 'Kode unik harus diisi.',
            'unique_code.size' => 'Format kode tidak valid. Contoh: BSB-X7K9M2P4N8Q1',
        ]);

        $code = strtoupper($request->unique_code);

        // 🔒 RATE LIMITING: Prevent brute force attack
        $key = 'portal-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            return back()->withErrors([
                'unique_code' => "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$minutes} menit.",
            ])->withInput();
        }

        // Search customer by unique_code
        $customer = Customer::where('unique_code', $code)->first();

        if (! $customer) {
            // 🔒 Increment rate limiter on failed attempt
            RateLimiter::hit($key, 300); // 5 minutes cooldown

            // 🔒 LOG failed attempt for security monitoring
            Log::warning('Failed portal login attempt', [
                'ip' => $request->ip(),
                'code_prefix' => substr($code, 0, 6).'******', // Don't log full code
                'timestamp' => now(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors([
                'unique_code' => 'Kode unik tidak ditemukan. Silakan periksa kembali atau hubungi operator.',
            ])->withInput();
        }

        // ✅ Login successful - Clear rate limiter
        RateLimiter::clear($key);

        // 🔒 Store in session with security metadata
        session([
            'portal_customer_id' => $customer->id,
            'portal_login_time' => now(),
            'portal_ip' => $request->ip(), // For IP validation in middleware
        ]);

        // Update last portal access
        $customer->update(['last_portal_access' => now()]);

        // 🔒 LOG successful login
        Log::info('Customer portal login successful', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('customer-portal.dashboard')
            ->with('success', "Selamat datang, {$customer->name}!");
    }

    /**
     * Customer dashboard
     */
    public function dashboard()
    {
        $customer = Customer::find(session('portal_customer_id'));

        $customer->load(['ledgerEntries' => function ($query) {
            $query->latest()->limit(5);
        }]);

        // Statistics
        $stats = [
            'total_weight' => $customer->deposits()
                ->where('status', 'final')
                ->sum('total_weight'),
            'transaction_count' => $customer->deposits()->count() + $customer->withdrawals()->count(),
            'last_deposit' => $customer->deposits()->latest()->first(),
        ];

        return view('customer-portal.dashboard', compact('customer', 'stats'));
    }

    /**
     * Transaction history
     */
    public function transactions(Request $request)
    {
        $customer = Customer::find(session('portal_customer_id'));

        $type = $request->input('type', 'all'); // all, deposits, withdrawals

        // Build query for combined transactions
        $ledgerEntries = $customer->ledgerEntries()
            ->with(['referenceable'])
            ->when($type === 'deposits', function ($query) {
                $query->where('ref_type', Deposit::class);
            })
            ->when($type === 'withdrawals', function ($query) {
                $query->where('ref_type', \App\Models\Withdrawal::class);
            })
            ->latest()
            ->paginate(20);

        // Calculate running balance
        $runningBalance = $customer->balance; // Start from current balance
        foreach ($ledgerEntries as $entry) {
            $entry->running_balance = $runningBalance;
            // Go backwards in time
            $runningBalance += ($entry->direction === 'KREDIT' ? $entry->amount : -$entry->amount);
        }

        return view('customer-portal.transactions', compact('customer', 'ledgerEntries', 'type'));
    }

    /**
     * Deposit detail
     */
    public function depositDetail(Deposit $deposit)
    {
        $customer = Customer::find(session('portal_customer_id'));

        // 🔒 AUTHORIZATION: Verify deposit belongs to this customer
        if ($deposit->customer_id !== $customer->id) {
            Log::warning('Unauthorized portal deposit access attempt', [
                'customer_id' => $customer->id,
                'attempted_deposit_id' => $deposit->id,
                'deposit_owner_id' => $deposit->customer_id,
            ]);

            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $deposit->load(['items.wasteCategory', 'creator']);

        return view('customer-portal.deposit-detail', compact('customer', 'deposit'));
    }

    /**
     * Info page
     */
    public function info()
    {
        $customer = Customer::find(session('portal_customer_id'));

        // Get current waste prices
        $wastePrices = \App\Models\WasteCategory::with(['wastePrices' => function ($query) {
            $query->where('effective_from', '<=', now())
                ->latest('effective_from')
                ->limit(1);
        }])
            ->where('is_active', true)
            ->get();

        return view('customer-portal.info', compact('customer', 'wastePrices'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        $customerId = session('portal_customer_id');

        // 🔒 LOG logout for audit trail
        Log::info('Customer portal logout', [
            'customer_id' => $customerId,
        ]);

        // Clear all session data
        session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);

        return redirect()->route('customer-portal.check-code')
            ->with('success', 'Anda telah keluar dari portal nasabah.');
    }
}
