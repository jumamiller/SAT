<?php

use App\Http\Controllers\API\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', CustomerController::class);
});
