<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

// Route::post('v1/safebet/stk/push', 'MpesaController@customerMpesaSTKPush');
// Route::post('v1/safebet/subscription/callback','MpesaController@subscriptionCallback');

Auth::routes();

// Route::get('/home', 'MainController@index')->name('home');
// Route::get('/games', 'MainController@games')->name('games');
// Route::get('/profile', 'MainController@profile')->name('profile');
