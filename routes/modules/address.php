<?php

use App\Http\Controllers\API\Address\AddressController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', AddressController::class);
});
