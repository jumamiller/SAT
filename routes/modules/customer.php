<?php

use App\Http\Controllers\API\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', CustomerController::class);
    //show
    Route::get('/{id}',[CustomerController::class,'show']);
    //remove
    Route::delete('/{id}',[CustomerController::class,'destroy']);
});
