<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix'=>'v1'], function (){
    //auth routes
    Route::prefix('auth')->group(base_path('routes/modules/auth.php'));
    //protected
    Route::group(['middleware'=>'auth:api'],function(){
        //get users
        Route::prefix('users')->group(base_path('routes/modules/user.php'));
    });
});
