<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CustomerSessionGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🔒 Check if session exists
        if (! session()->has('portal_customer_id')) {
            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Silakan masukkan kode unik Anda terlebih dahulu.');
        }

        // 🔒 SESSION TIMEOUT: 30 menit inactive
        $loginTime = session('portal_login_time');
        if ($loginTime && now()->diffInMinutes($loginTime) > 30) {
            $customerId = session('portal_customer_id');
            session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);

            Log::info('Portal session expired', [
                'customer_id' => $customerId,
            ]);

            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
        }

        // 🔒 IP VALIDATION: Prevent session hijacking
        if (session('portal_ip') !== $request->ip()) {
            $customerId = session('portal_customer_id');
            session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);

            Log::warning('Portal session IP mismatch - possible hijacking attempt', [
                'customer_id' => $customerId,
                'session_ip' => session('portal_ip'),
                'request_ip' => $request->ip(),
            ]);

            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Terdeteksi akses tidak valid. Silakan login kembali.');
        }

        // Verify customer still exists
        $customer = Customer::find(session('portal_customer_id'));

        if (! $customer) {
            session()->forget(['portal_customer_id', 'portal_login_time', 'portal_ip']);

            return redirect()->route('customer-portal.check-code')
                ->with('error', 'Data tidak ditemukan.');
        }

        // Share customer to all views
        view()->share('portalCustomer', $customer);

        return $next($request);
    }
}
