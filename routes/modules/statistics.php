<?php

use App\Http\Controllers\API\Statistics\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    Route::apiResource('', StatisticsController::class);
});
