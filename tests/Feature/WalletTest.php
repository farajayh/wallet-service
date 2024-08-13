<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Wallet;
use App\Models\Merchant;

class WalletTest extends TestCase
{
    use RefreshDatabase;
        
    private $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        $merchants = Merchant::factory(12)->haswallets()->create();
        $this->wallet = $merchants->first()->wallets()->first();
    }

     /**
     * Test retrieval of paginated lists of wallets
     */
    public function test_can_retrieve_paginated_wallets(): void
    {
        $response = $this->get('/api/v1/wallets');

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

        // Assert that at least one wallet is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);
    }

    /**
     * Test retrieval of paginated lists of wallets
     */
    public function test_can_retrieve_paginated_wallets_with_page_query(): void
    {
        $response = $this->get('/api/v1/wallets?page=2');

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

        // Assert that at least one wallet is in the result set
        $response->assertJsonPath('result.data.0.id', fn (string $id) => strlen($id) > 1);
    }

    public function test_can_retrieve_single_wallet(): void
    {
        $response = $this->getJson("/api/v1/wallets/{$this->wallet->id}");

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
     * Can credit wallet
     */
    public function test_can_credit_wallet(): void
    {
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/credit", [
                            'amount' => 5000,
                            'narration' => 'sales'
                         ]);

        // Assert that the response status code is 200               
        $response->assertStatus(200);
        
        // Assert that the returned corect response
        $response->assertJson([
           'status' => true,
           'message' => 'Transaction was successful',
           'balance' => $this->wallet->balance + 5000
        ]);
    }

    /**
     * Cannot credit wallet without complete parameters
     */
    public function test_cannot_credit_wallet_without_complete_parameters(): void
    {
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/credit", [
                            
                         ]);

        // Assert that the response status code is 422              
        $response->assertStatus(422);
        
        // Assert that the returned corect response
        $response->assertJson([
           'status' => false,
           'message' => 'Request Failed',
           'errors' => [
                'amount' => ['The amount field is required.']
           ]
        ]);
    }

    /**
     * Cannot credit wallet with invalid amount field
     */
    public function test_cannot_credit_wallet_with_invalid_amount(): void
    {
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/credit", [
                                'amount' => 'Five thousand',
                                'narration' => 'sales'
                         ]);

        // Assert that the response status code is 422              
        $response->assertStatus(422);
        
        // Assert that the returned corect response
        $response->assertJson([
           'status' => false,
           'message' => 'Request Failed',
           'errors' => [
                'amount' => [
                    'The amount field must be a number.',
                    'The amount field must be greater than 0.'
                ]
           ]
        ]);
    }

    /**
     * Can debit wallet
     */
    public function test_can_debit_wallet(): void
    {
        $credit_amount = 5000;
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/credit", [
                            'amount' => $credit_amount,
                            'narration' => 'sales'
                         ]);

        // Assert that the response status code is 200               
        $response->assertStatus(200);

        $debit_amount = 2000;
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/debit", [
            'amount' => $debit_amount,
            'narration' => 'sales'
        ]);

        // Assert that the response status code is 200               
        $response->assertStatus(200);
        
        // Assert that the returned corect response
        $response->assertJson([
           'status' => true,
           'message' => 'Transaction was successful',
           'balance' => $credit_amount - $debit_amount
        ]);
    }

    /**
     * Can debit wallet without complete parameters
     */
    public function test_cannot_debit_wallet_without_complete_parameters(): void
    {
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/debit", [
            
        ]);
        
        // Assert that the response status code is 422              
        $response->assertStatus(422);

        // Assert that the returned corect response
        $response->assertJson([
            'status' => false,
            'message' => 'Request Failed',
            'errors' => [
                'amount' => ['The amount field is required.']
            ]
        ]);
    }
    
    /**
     * Cannot debit wallet with invalid amount field
     */
    public function test_cannot_debit_wallet_with_invalid_amount(): void
    {
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/debit", [
                                'amount' => 'Five thousand',
                                'narration' => 'sales'
                         ]);

        // Assert that the response status code is 422              
        $response->assertStatus(422);
        
        // Assert that the returned corect response
        $response->assertJson([
           'status' => false,
           'message' => 'Request Failed',
           'errors' => [
                'amount' => [
                    'The amount field must be a number.',
                    'The amount field must be greater than 0.'
                ]
           ]
        ]);
    }

    /**
     * Cannot debit more than wallet balance
     */
    public function test_cannot_debit_moret_han_wallet_balance(): void
    {
        $credit_amount = 5000;
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/credit", [
                            'amount' => $credit_amount,
                            'narration' => 'sales'
                         ]);

        // Assert that the response status code is 200               
        $response->assertStatus(200);

        $debit_amount = 6000;
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/debit", [
            'amount' => $debit_amount,
            'narration' => 'sales'
        ]);
        
        // Assert that the response status code is 422               
        $response->assertStatus(422);
        
        // Assert that the returned corect response
        $response->assertJson([
           'status' => false,
           'message' => 'Insufficient wallet balance',
           'balance' => $credit_amount
        ]);
    }

    /**
     * Can retrieve wallet transactions history
     */
    public function test_can_retrieve_wallet_transaction_history(): void
    {
        $credit_amount = 5000;
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/credit", [
                            'amount' => $credit_amount,
                            'narration' => 'sales'
                         ]);

        // Assert that the response status code is 200               
        $response->assertStatus(200);

        $debit_amount = 2000;
        $response = $this->postJson("/api/v1/wallets/{$this->wallet->id}/debit", [
            'amount' => $debit_amount,
            'narration' => 'sales'
        ]);

        // Assert that the response status code is 200               
        $response->assertStatus(200);

        $response = $this->get("/api/v1/wallets/{$this->wallet->id}/history");

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

        // Assert that the current number of transaction is returned
        $response->assertJsonPath('result.data', fn (array $data) => count($data) == 2);
        
    }
}
