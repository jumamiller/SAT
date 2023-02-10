<?php

use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', UserController::class);
});
