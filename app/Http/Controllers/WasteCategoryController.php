<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWasteCategoryRequest;
use App\Http\Requests\UpdateWasteCategoryRequest;
use App\Models\WasteCategory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class WasteCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', WasteCategory::class);

        $categories = WasteCategory::with(['prices' => function ($query) {
            $query->orderBy('effective_from', 'desc');
        }])->latest()->paginate(15);

        return view('waste-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', WasteCategory::class);

        return view('waste-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWasteCategoryRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $category = WasteCategory::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            if (isset($validated['price_per_kg'])) {
                $category->prices()->create([
                    'price_per_kg' => $validated['price_per_kg'],
                    'effective_from' => $validated['effective_from'],
                ]);
            }
        });

        return redirect()->route('waste-categories.index')
            ->with('success', 'Kategori sampah berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WasteCategory $wasteCategory)
    {
        $this->authorize('update', $wasteCategory);

        return view('waste-categories.edit', compact('wasteCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWasteCategoryRequest $request, WasteCategory $wasteCategory)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $wasteCategory): void {
            $wasteCategory->update([
                'name' => $validated['name'],
                'description' => array_key_exists('description', $validated)
                    ? $validated['description']
                    : $wasteCategory->description,
                'is_active' => array_key_exists('is_active', $validated)
                    ? $validated['is_active']
                    : $wasteCategory->is_active,
            ]);

            if (isset($validated['price_per_kg'])) {
                $wasteCategory->prices()->create([
                    'price_per_kg' => $validated['price_per_kg'],
                    'effective_from' => $validated['effective_from'],
                ]);
            }
        });

        return redirect()->route('waste-categories.index')
            ->with('success', 'Kategori sampah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WasteCategory $wasteCategory)
    {
        $this->authorize('delete', $wasteCategory);

        if ($wasteCategory->hasTransactions()) {
            return redirect()->route('waste-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena sudah dipakai pada transaksi setoran. Nonaktifkan kategori ini sebagai gantinya.');
        }

        try {
            $wasteCategory->delete();
        } catch (QueryException $exception) {
            report($exception);

            return redirect()->route('waste-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih terhubung dengan data transaksi.');
        }

        return redirect()->route('waste-categories.index')
            ->with('success', 'Kategori sampah berhasil dihapus.');
    }
}
