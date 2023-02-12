<?php

use App\Http\Controllers\API\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', OrderController::class);
    //show
    Route::get('{id}', [OrderController::class,'show']);
});
