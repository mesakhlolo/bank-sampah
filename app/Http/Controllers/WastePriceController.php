<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWastePriceRequest;
use App\Models\WastePrice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WastePriceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWastePriceRequest $request)
    {
        WastePrice::create($request->validated());

        return redirect()->back()
            ->with('success', 'Harga berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WastePrice $wastePrice)
    {
        $this->authorize('delete', $wastePrice);

        $wastePrice->delete();

        return redirect()->back()
            ->with('success', 'Harga berhasil dihapus.');
    }
}
