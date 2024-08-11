<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WalletController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\CustomerController;


Route::prefix('v1')->group(function(){
    Route::apiResources([
        'customers' => CustomerController::class,
        'merchants' => MerchantController::class
    ]);

    Route::get('/customers/{customer}/wallets', [WalletController::class, 'getCustomerWallet']);
    Route::post('/customers/{customer}/wallets', [WalletController::class, 'createCustomerWallet']);
    Route::get('/merchants/{merchant}/wallets', [WalletController::class, 'getMerchantWallet']);
    Route::post('/merchants/{merchant}/wallets', [WalletController::class, 'createMerchantWallet']);

    Route::get('/wallets', [WalletController::class, 'index']);
    Route::get('/wallets/{wallet}', [WalletController::class, 'show']);
    Route::post('/wallets/{wallet}/debit', [WalletController::class, 'debitWallet']);
    Route::post('/wallets/{wallet}/credit', [WalletController::class, 'creditWallet']);
    Route::get('/wallets/{wallet}/history', [WalletController::class, 'walletTransactionHistory']);

});