<?php

/** MPesa Additonal Routes */

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Thegr8dev\Eclassmpesa\Http\Controllers','middleware' => ['web','is_active']], function () {

    Route::post('/payvia/mpesa/stkpush', 'PayPesaController@stkpush')->name('stkpush');
    Route::get('/verify/payment/{paymentid}','PayPesaController@verifypay')->name('verifypay');

    Route::group(['middleware' => ['web', 'auth', 'is_admin', 'switch_languages']], function () {
        
        Route::get('admin/mpesa/settings', 'PayPesaController@adminsettings')->name('mpesa.setting');
        Route::post('admin/mpesa/keys/update', 'PayPesaController@updatesetting')->name('mpesa.update');
    
    });

    Route::prefix('api')->group(function () {
        Route::post('payment/confirm/callback','PayPesaController@callback');
        Route::get('payment/validation','PayPesaController@mpesaValidation')->middleware('api');
    });


});

/** */
