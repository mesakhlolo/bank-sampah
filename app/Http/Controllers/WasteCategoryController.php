<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWasteCategoryRequest;
use App\Http\Requests\UpdateWasteCategoryRequest;
use App\Models\WasteCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
        }])->latest()->get();

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
        $category = WasteCategory::create($request->validated());

        return redirect()->route('waste-categories.index')
            ->with('success', 'Kategori sampah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WasteCategory $wasteCategory)
    {
        $this->authorize('view', $wasteCategory);

        $wasteCategory->load(['prices' => fn ($q) => $q->orderBy('effective_from', 'desc')]);

        return view('waste-categories.show', compact('wasteCategory'));
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
        $wasteCategory->update($request->validated());

        return redirect()->route('waste-categories.index')
            ->with('success', 'Kategori sampah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WasteCategory $wasteCategory)
    {
        $this->authorize('delete', $wasteCategory);

        $wasteCategory->delete();

        return redirect()->route('waste-categories.index')
            ->with('success', 'Kategori sampah berhasil dihapus.');
    }
}
