<?php

use App\Http\Controllers\API\Fleet\FleetController;
use App\Http\Controllers\API\Fleet\LoadAndDispatchFleetController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    //resource
    Route::apiResource('', FleetController::class);
    //load vehicle
    Route::post('load-vehicle',[LoadAndDispatchFleetController::class,'loadVehicle'])->name('LoadingFleet');
    //dispatch
    Route::post('dispatch-vehicle',[LoadAndDispatchFleetController::class,'dispatchVehicle'])->name('Dispatch');
});
