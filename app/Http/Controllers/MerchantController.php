<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantRequest;
use App\Models\Merchant;


class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $merchants = Merchant::simplePaginate(10);
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'result'  => $merchants
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MerchantRequest $request)
    {
        //validate input using MerchantRequest form request
        $validated = $request->validated();

        // create a new merchant
        $merchant = (new Merchant())->create($validated);

        if($merchant){
            $response = [
                'status'  => true,
                'message' => "New merchant created successfully",
                'data'    => $merchant
            ];

            return response()->json($response, 201);
        }

        return response()->json([
           'status'  => false,
           'message' => "Failed to create new merchant",
           'data'    => null
        ], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Merchant $merchant)
    {
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'data'    => $merchant
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MerchantRequest $request, Merchant $merchant)
    {
        //validate input using MerchantRequest form request
        $validated = $request->validated();

        if($merchant->update($validated)){
            $response = [
                'status'  => true,
                'message' => "Merchant updated successfully",
                'data'    => $merchant
            ];

            return response()->json($response, 200);
        }

        return response()->json([
           'status'  => false,
           'message' => "Failed to update merchant",
           'data'    => null
        ], 500);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchant $merchant)
    {
        if($merchant->delete()){
            return response()->json([
                'status'  => true,
                'message' => "Merchant deleted successfully",
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => "Failed to delete merchant",
        ], 500);
    }
}
