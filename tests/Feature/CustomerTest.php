<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Customer;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private $customer;

    
    protected function setUp(): void
    {
        parent::setUp();

        $customers = Customer::factory(12)->create();
        $this->customer = $customers->first();
    }

    /**
     * Test retrieval of paginated lists of customers
     */
    public function test_can_retrieve_paginated_customers(): void
    {
        $response = $this->get('/api/v1/customers');

        // Assert that the status code is 200
        $response->assertStatus(200);

        // Assert that the returned response has a success messsage
        $response->assertJson([
            'status' => true,
            'message' => 'Success',
        ]);

        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => [
                'current_page',
                'data',
                'first_page_url',
                'from',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
            ],
        ]);

        // Assert that at least one customer is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);
    }

    /**
     * Test retrieval of customers with page query
     */
    public function test_can_retrieve_customers_with_page_query(): void
    {
        $response = $this->getJson('/api/v1/customers?page=2');

        // Assert that the status code is 200
        $response->assertStatus(200);

        // Assert that the returned response has a success messsage
        $response->assertJson([
           'status' => true,
           'message' => 'Success',
        ]);

        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
           'status',
           'message',
            'result' => [
                'current_page',
                'data',
                'first_page_url',
                'from',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
            ],
        ]);

        // Assert that at least one customer is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);
    }

    /**
     * Test retrieval of single customer data
     */
    public function test_can_retrieve_single_customer(): void
    {
        $response = $this->getJson("/api/v1/customers/{$this->customer->id}");

        // Assert that the status code is 200
        $response->assertStatus(200);

        // Assert that the returned response has a success messsage
        $response->assertJson([
            'status' => true,
            'message' => 'Success',
        ]);

        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }

    /**
     * Can create a new customer
     */
    public function test_can_create_customer(): void
    {
        $response = $this->postJson('/api/v1/customers', [
                            'name' => 'New Customer',
                            'email' => 'customer@example.com',
                            'phone_no' => '+2349011330044',
                            'address' => 'address line',
                            'city' => 'city',
                            'state' => 'state',
                            'country' => 'country',
                            'postal_code' => '123455',
                         ]);

        // Assert that the response status code is 201                 
        $response->assertStatus(201);
        
        // Assert that the returned response has a success messsage
        $response->assertJson([
            'status' => true,
            'message' => 'New customer created successfully',
        ]);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }
    
    /**
     * Customer email must be unique
     */
    public function test_customer_email_must_be_unique(): void
    {
        $response = $this->postJson('/api/v1/customers', [
                            'name' => 'New Customer',
                            'email' => $this->customer->email,
                            'phone_no' => '+2349011330044',
                            'address' => 'address line',
                            'city' => 'city',
                            'state' => 'state',
                            'country' => 'country',
                            'postal_code' => '123455',
                         ]);

        // Assert that the response status code is 422                 
        $response->assertStatus(422);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJson([
            'status' => false,
            'message' => 'Request Failed',
            'errors' => [
                'email' => ["The email has already been taken."]
            ]
        ]);
    }

    /**
     * Cannot create a new customer without complete parameters
     */
    public function test_cannot_create_customer_without_complete_parameters(): void
    {
        $response = $this->postJson('/api/v1/customers', [
                            
                         ]);

        // Assert that the response status code is 422                 
        $response->assertStatus(422);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJson([
            'status' => false,
            'message' => 'Request Failed',
            'errors' => [
                'name' => ["The name field is required."],
                'email' => ["The email field is required."],
                'phone_no' => ["The phone no field is required."],
                'address' => ["The address field is required."],
                'city' => ["The city field is required."],
                'state' => ["The state field is required."],
                'country' => ["The country field is required."],
                'postal_code' => ["The postal code field is required."]
            ]
        ]);
    }    

     /**
     * Cannot create a new customer without valid parameters
     */
    public function test_cannot_create_customer_without_valid_parameters(): void
    {
        $response = $this->postJson('/api/v1/customers', [
                            'name' => 'New Customer',
                            'email' => 'customerexample.com',
                            'phone_no' => '+23490113300',
                            'address' => 'address line',
                            'city' => 'city',
                            'state' => 'state',
                            'country' => 'country',
                            'postal_code' => '12345',
                         ]);

        // Assert that the response status code is 422                 
        $response->assertStatus(422);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJson([
            'status' => false,
            'message' => 'Request Failed',
            'errors' => [
                'email' => ["The email field must be a valid email address."],
                'phone_no' => ["The phone no field must be a valid phone number."],
                'postal_code' => ["The postal code field must be 6 digits."]
            ]
        ]);
    }  
    
    /**
     * Can update an existing customer
     */
    public function test_can_update_customer(): void
    {
        $response = $this->putJson("/api/v1/customers/{$this->customer->id}", [
                            'name' => 'Updated Customer',
                            'email' => 'customer@example.com',
                            'phone_no' => '+2349011330044',
                            'address' => 'address line',
                            'city' => 'city',
                            'state' => 'state',
                            'country' => 'country',
                            'postal_code' => '123455',
                         ]);

        // Assert that the response status code is 200               
        $response->assertStatus(200);
        
        // Assert that the returned response has a success messsage
        $response->assertJson([
            'status' => true,
            'message' => 'Customer updated successfully',
        ]);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }

    /**
     * Cannot update a customer without valid parameters
     */
    public function test_cannot_update_customer_without_valid_parameters(): void
    {
        $response = $this->putJson("/api/v1/customers/{$this->customer->id}", [
                            'name' => 'New Customer',
                            'email' => 'customerexample.com',
                            'phone_no' => '+23490113300',
                            'address' => 'address line',
                            'city' => 'city',
                            'state' => 'state',
                            'country' => 'country',
                            'postal_code' => '12345',
                         ]);

        // Assert that the response status code is 422                 
        $response->assertStatus(422);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJson([
            'status' => false,
            'message' => 'Request Failed',
            'errors' => [
                'email' => ["The email field must be a valid email address."],
                'phone_no' => ["The phone no field must be a valid phone number."],
                'postal_code' => ["The postal code field must be 6 digits."]
            ]
        ]);
    }

    /**
     * Can delete a customer
     */
    public function test_can_delete_customer(): void
    {
        $customer_id = $this->customer->id;
        $response = $this->deleteJson("/api/v1/customers/$customer_id");

        // Assert that the response status code is 200               
        $response->assertStatus(200);
        
        // Assert that the returned response has a success messsage
        $response->assertJson([
            'status' => true,
            'message' => 'Customer deleted successfully',
        ]);
        
        $this->assertDatabaseMissing('customers', [
            'id' => $customer_id,
        ]);
    }    

    /**
     * Customer can create wallet
     */
    public function test_customer_can_create_wallet(): void
    {
        $response = $this->postJson("/api/v1/customers/{$this->customer->id}/wallets", [
                            'name' => 'new wallet',
                            'currency' => 'USD'
                         ]);

        // Assert that the response status code is 201               
        $response->assertStatus(201);
        
        // Assert that the returned response has a success messsage
        $response->assertJson([
           'status' => true,
           'message' => 'Wallet created successfully',
        ]);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
           'status',
           'message',
            'data'
        ]);
    }

    /**
     * Customer can create only one wallet per currency
     */
    public function test_customer_can_create_one_wallet_per_currency(): void
    {
        $customer = Customer::factory()->haswallets()->create();
        $currency = $customer->wallets()->first()->currency;
        $response = $this->postJson("/api/v1/customers/{$customer->id}/wallets", [
            'name' => 'new wallet',
            'currency' => $currency
        ]);

        // Assert that the response status code is 422              
        $response->assertStatus(422);

        // Assert that the returned response has an error messsage
        $response->assertJson([
        'status' => false,
        'message' => "A wallet for $currency already exists"
        ]);
    }

    /**
     * Can retrieve customer wallet information
     */
    public function test_can_retrieve_customer_wallet(): void
    {
        $customer = Customer::factory()->haswallets()->create();
        $currency = $customer->wallets()->first()->currency;
        $response = $this->get("/api/v1/customers/{$customer->id}/wallets");

        // Assert that the response status code is 200              
        $response->assertStatus(200);

        // Assert that at least one wallet is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);

         // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
            'status',
            'message',
             'result' => [
                 'current_page',
                 'data',
                 'first_page_url',
                 'from',
                 'next_page_url',
                 'path',
                 'per_page',
                 'prev_page_url',
                 'to',
             ],
        ]);
    }
}
