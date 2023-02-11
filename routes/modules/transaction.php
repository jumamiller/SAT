<?php

use App\Http\Controllers\API\Transaction\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    //CRUD
    Route::apiResource('', TransactionController::class);
    //transfer funds
    Route::post('deposit',[TransactionController::class,'deposit'])->name('Deposit');
    Route::post('transfer-funds',[TransactionController::class,'transferFunds'])->name('TransferFunds');
});
