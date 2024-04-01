<?php

namespace App\Http\Controllers;

use App\Models\part_number;
use App\Models\PartNumber;
use Illuminate\Http\Request;

class PartNumberController extends Controller
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
    public function show(int $part_number)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $part_number)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $part_number)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the part number by its ID
        $partNumber = PartNumber::findOrFail($id);
    
        // Delete the part number
        $partNumber->delete();
    
        // Return a response indicating success
        return response()->json(['message' => 'Part number deleted successfully'], 200);
    }
}
