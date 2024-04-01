<?php

namespace App\Http\Controllers;

use App\Models\StockManagement;
use Illuminate\Http\Request;

class StockManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StockManagement $stockManagement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockManagement $stockManagement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockManagement $stockManagement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $stockManagement = StockManagement::findOrFail($id);
    
        // Delete the part number
        $stockManagement->delete();
    
        // Return a response indicating success
        return response()->json(['message' => 'priceCalculation deleted successfully'], 200);
    }
    
}
