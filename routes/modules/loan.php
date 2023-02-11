<?php

use App\Http\Controllers\API\Loan\LoanController;
use Illuminate\Support\Facades\Route;

Route::group([],function(){
    //award loans
    Route::post('process-request', [LoanController::class,'awardLoan'])->name('AwardLoan');
    //repay loan
    Route::post('repay', [LoanController::class,'repayLoan'])->name('RepayLoan');
});
