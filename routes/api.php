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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function () {
    
    Route::post('/apiKDS','ApiController@index');
    Route::post('/apiKDS/Premium','ApiController@indexPremium');
    
    // Ajax register validation
    Route::get('register/validation', 'ApiController@registerValidation')->name('register.validation');
    
    // Ajax active License
    Route::get('devices/active', 'ApiController@activeLicense')->name('devices.active');
    
});
