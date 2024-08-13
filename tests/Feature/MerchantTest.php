<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Merchant;

class MerchantTest extends TestCase
{
    use RefreshDatabase;

    private $merchant;

    
    protected function setUp(): void
    {
        parent::setUp();

        $merchants = Merchant::factory(12)->create();
        $this->merchant = $merchants->first();
    }

    /**
     * Test retrieval of paginated lists of merchants
     */
    public function test_can_retrieve_paginated_merchants(): void
    {
        $response = $this->get('/api/v1/merchants');

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

        // Assert that at least one merchant is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);
    }

    /**
     * Test retrieval of merchants with page query
     */
    public function test_can_retrieve_merchants_with_page_query(): void
    {
        $response = $this->getJson('/api/v1/merchants?page=2');

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

        // Assert that at least one merchant is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);
    }

    /**
     * Test retrieval of single merchant data
     */
    public function test_can_retrieve_single_merchant(): void
    {
        $response = $this->getJson("/api/v1/merchants/{$this->merchant->id}");

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
     * Can create a new merchant
     */
    public function test_can_create_merchant(): void
    {
        $response = $this->postJson('/api/v1/merchants', [
                            'name' => 'New Merchant',
                            'email' => 'merchant@example.com',
                            'phone_no' => '+2349011330044',
                            'brand_name' => 'Merchant',
                            'brand_description' => 'description',
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
            'message' => 'New merchant created successfully',
        ]);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }
    
    /**
     * Merchant email must be unique
     */
    public function test_merchant_email_must_be_unique(): void
    {
        $response = $this->postJson('/api/v1/merchants', [
                            'name' => 'New Merchant',
                            'email' => $this->merchant->email,
                            'phone_no' => '+2349011330044',
                            'brand_name' => 'Merchant',
                            'brand_description' => 'description',
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
     * Cannot create a new merchant without complete parameters
     */
    public function test_cannot_create_merchant_without_complete_parameters(): void
    {
        $response = $this->postJson('/api/v1/merchants', [
                            
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
                'brand_name' => ["The brand name field is required."],
                'brand_description' => ["The brand description field is required."],
                'city' => ["The city field is required."],
                'state' => ["The state field is required."],
                'country' => ["The country field is required."],
                'postal_code' => ["The postal code field is required."]
            ]
        ]);
    }    

     /**
     * Cannot create a new merchant without valid parameters
     */
    public function test_cannot_create_merchant_without_valid_parameters(): void
    {
        $response = $this->postJson('/api/v1/merchants', [
                            'name' => 'New Merchant',
                            'email' => 'merchantexample.com',
                            'phone_no' => '+23490113300',
                            'brand_name' => 'Merchant',
                            'brand_description' => 'description',
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
     * Can update an existing merchant
     */
    public function test_can_update_merchant(): void
    {
        $response = $this->putJson("/api/v1/merchants/{$this->merchant->id}", [
                            'name' => 'Updated Merchant',
                            'email' => 'merchant@example.com',
                            'phone_no' => '+2349011330044',
                            'brand_name' => 'Merchant',
                            'brand_description' => 'description',
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
            'message' => 'Merchant updated successfully',
        ]);
        
        // Assert that the returned response structure matches the expected structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }

    /**
     * Cannot update a merchant without valid parameters
     */
    public function test_cannot_update_merchant_without_valid_parameters(): void
    {
        $response = $this->putJson("/api/v1/merchants/{$this->merchant->id}", [
                            'name' => 'New Merchant',
                            'email' => 'merchantexample.com',
                            'phone_no' => '+23490113300',
                            'brand_name' => 'Merchant',
                            'brand_description' => 'description',
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
     * Can delete a merchant
     */
    public function test_can_delete_merchant(): void
    {
        $merchant_id = $this->merchant->id;
        $response = $this->deleteJson("/api/v1/merchants/$merchant_id");

        // Assert that the response status code is 200               
        $response->assertStatus(200);
        
        // Assert that the returned response has a success messsage
        $response->assertJson([
            'status' => true,
            'message' => 'Merchant deleted successfully',
        ]);
        
        $this->assertDatabaseMissing('merchants', [
            'id' => $merchant_id,
        ]);
    }    

    /**
     * Merchant can create wallet
     */
    public function test_merchant_can_create_wallet(): void
    {
        $response = $this->postJson("/api/v1/merchants/{$this->merchant->id}/wallets", [
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
     * Merchant can create only one wallet per currency
     */
    public function test_merchant_can_create_one_wallet_per_currency(): void
    {
        $merchant = Merchant::factory()->haswallets()->create();
        $currency = $merchant->wallets()->first()->currency;
        $response = $this->postJson("/api/v1/merchants/{$merchant->id}/wallets", [
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
     * Can retrieve merchant wallet information
     */
    public function test_can_retrieve_merchant_wallet(): void
    {
        $merchant = Merchant::factory()->haswallets()->create();
        $currency = $merchant->wallets()->first()->currency;
        $response = $this->get("/api/v1/merchants/{$merchant->id}/wallets");

        // Assert that the response status code is 200              
        $response->assertStatus(200);

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

        // Assert that at least one wallet is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);
    }
}
