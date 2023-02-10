<?php

use App\Http\Controllers\API\Account\AccountController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', AccountController::class);
});
