<?php

use App\Http\Controllers\API\Fleet\FleetController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', FleetController::class);
});
