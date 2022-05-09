<?php

use Illuminate\Http\Request;

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

// Route::post('v1/access/token', 'MpesaController@generateAccessToken');
// Route::get('v1/safebet/stk/push', 'MpesaController@customerMpesaSTKPush');
Route::post('v1/safebet/subscription/callback','MpesaController@subscriptionCallback');

Route::post('v1/safebet/stk/push', 'TransactionController@stkPushRequest');