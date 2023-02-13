<?php

use App\Http\Controllers\API\Fleet\FleetController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    //resource
    Route::apiResource('', FleetController::class);
    //show fleet
    Route::get('/{id}', [FleetController::class,'show']);
    //update
    Route::patch('/{id}', [FleetController::class,'update']);
    //remove
    Route::delete('/{id}', [FleetController::class,'destroy']);
});
