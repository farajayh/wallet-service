<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::simplePaginate(10);
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'result'  => $customers
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        //validate input using CustomerRequest form request
        $validated = $request->validated();

        // create a new customer
        $customer = (new Customer())->create($validated);

        if($customer){
            $response = [
                'status'  => true,
                'message' => "New customer created successfully",
                'data'    => $customer
            ];

            return response()->json($response, 201);
        }

        return response()->json([
           'status'  => false,
           'message' => "Failed to create new customer",
           'data'    => null
        ], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'data'    => $customer
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        //validate input using CustomerRequest form request
        $validated = $request->validated();

        if($customer->update($validated)){
            $response = [
                'status'  => true,
                'message' => "Customer updated successfully",
                'data'    => $customer
            ];

            return response()->json($response, 200);
        }

        return response()->json([
           'status'  => false,
           'message' => "Failed to update customer",
           'data'    => null
        ], 500);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if($customer->delete()){
            return response()->json([
                'status'  => true,
                'message' => "Customer deleted successfully",
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => "Failed to delete customer",
        ], 500);
    }
}
