<?php

use App\Http\Controllers\API\Driver\DriverController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', DriverController::class);
});
