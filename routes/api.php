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
        //users
        Route::prefix('users')->group(base_path('routes/modules/user.php'));
        //customers
        Route::prefix('customers')->group(base_path('routes/modules/customer.php'));
        //address
        Route::prefix('address')->group(base_path('routes/modules/address.php'));
        //drivers
        Route::prefix('drivers')->group(base_path('routes/modules/driver.php'));
        //fleet
        Route::prefix('fleet')->group(base_path('routes/modules/fleet.php'));
        //order
        Route::prefix('orders')->group(base_path('routes/modules/order.php'));
        //statistics
        Route::prefix('statistics')->group(base_path('routes/modules/statistics.php'));
    });
});
