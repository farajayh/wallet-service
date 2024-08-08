<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/



Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => 'v1'], function(){
    Route::apiResources([
        'customers' => CustomerController::class,
        'merchants' => MerchantController::class
    ]);
});