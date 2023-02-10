<?php

use App\Http\Controllers\API\KYC\KYCController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', KYCController::class);
});
