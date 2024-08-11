<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\WalletRequest;
use App\Http\Requests\WalletTransactionRequest;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Merchant;

use App\WalletTransactionType;

use App\Currency;

use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    private $defaultCurrency = Currency::NGN;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallets = Wallet::simplePaginate(10);
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'result'  => $wallets
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'data'    => $wallet
        ], 200);
    }

    public function createCustomerWallet(WalletRequest $request, Customer $customer)
    {
        return $this->createWallet($customer, $request->validated());
    }

    public function getCustomerWallet(Customer $customer)
    {
        $wallets = $customer->wallets()->simplePaginate(10);
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'result'  => $wallets
        ], 200);
    }

    public function createMerchantWallet(WalletRequest $request, Merchant $merchant)
    {
        return $this->createWallet($merchant, $request->validated());
    }

    public function getMerchantWallet(Merchant $merchant)
    {
        $wallets = $merchant->wallets()->simplePaginate(10);
        return response()->json([
            'status'  => true,
            'message' => "Success",
            'result'  => $wallets
        ], 200);
    }

    public function creditWallet(WalletTransactionRequest $request, Wallet $wallet)
    {
        $amount = $request->validated()['amount'];
        $narration = $request->validated()['narration'];
        return $this->executeWalletTransaction($wallet, $amount, WalletTransactionType::CREDIT, $narration);
    }

    public function debitWallet(WalletTransactionRequest $request, Wallet $wallet)
    {
        $amount = $request->validated()['amount'];
        $narration = $request->validated()['narration'];

        if ($amount > $wallet->balance) {
            return response()->json([
                'status'  => false,
                'message' => "Insufficient wallet balance",
                'balance' => $wallet->balance
            ], 422);
        }
        
        $amount = $amount * -1;
        return $this->executeWalletTransaction($wallet, $amount, WalletTransactionType::DEBIT, $narration);
    }


    public function walletTransactionHistory(Wallet $wallet){
        $transactions = $wallet->transactions()
            ->selectRaw('id, wallet_id, abs(amount) as amount, result_balance, type, narration, created_at')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        

        return response()->json([
            'status'  => true,
            'message' => "Success",
            'result'  => $transactions
        ], 200);
    }

    private function createWallet(Customer|Merchant $owner, $walletDetails){
        $currency = $walletDetails['currency'] ?? $this->defaultCurrency;
        if($owner->wallets()->where('currency',  $currency)->first()){
            return response()->json([
                'status'  => false,
                'message' => "A wallet for $currency already exists",
            ], 422);
        }

        $wallet = $owner->wallets()->create($walletDetails);

        if($wallet){            
            return response()->json([
                'status'  => true,
                'message' => "Wallet created successfully",
                'data'    => $wallet
            ], 201);
        }

        return response()->json([
            'status'  => false,
            'message' => "Failed to create wallet",
            'data'    => null
        ], 500);
    }

    private function executeWalletTransaction(Wallet $wallet, $amount, $type, $narration){
        try{
            DB::transaction(function () use ($wallet, $amount, $type, $narration) {
            
                $wallet->balance += $amount;
                $wallet->save();
    
                $transaction = new Transaction();
                $transaction->wallet_id = $wallet->id;
                $transaction->type = $type;
                $transaction->amount = $amount;
                $transaction->result_balance = $wallet->balance;
                $transaction->narration = $narration;
                $transaction->save();
            });
    
            return response()->json([
                'status'  => true,
                'message' => "Transaction was successful",
                'balance'    => $wallet->balance
            ], 200);
        }catch(\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => "Transaction failed"
            ], 500);
        }    
        
        
    }    
}
