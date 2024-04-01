<?php

namespace App\Http\Controllers;

use App\Models\PriceCalculation;
use Illuminate\Http\Request;

class PriceCalculationController extends Controller
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
    public function show(PriceCalculation $priceCalculation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PriceCalculation $priceCalculation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PriceCalculation $priceCalculation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $priceCalculation = PriceCalculation::findOrFail($id);
    
        // Delete the part number
        $priceCalculation->delete();
    
        // Return a response indicating success
        return response()->json(['message' => 'priceCalculation deleted successfully'], 200);
    }
}
