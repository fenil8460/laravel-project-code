<?php

use Illuminate\Support\Facades\Auth;
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
    return view('welcome');
});


Route::get('/index', '\App\Controllers\TestController@index');
Route::get('/show/{id}', '\App\Controllers\TestController@show');
Route::any('/store', '\App\Controllers\TestController@store');
Route::any('/update/{id}', '\App\Controllers\TestController@update');
Route::any('/delete/{id}', '\App\Controllers\TestController@destroy');
Route::any('/sendsms', '\App\Controllers\TestController@sendSMS');

Route::get('/phone', '\App\Controllers\TestController@searchPhone');





Route::get('auth/{provider}', '\App\Controllers\Auth\SocialAuthLoginController@redirect')->name('socialLogin');
Route::get('auth/{provider}/callback', '\App\Controllers\Auth\SocialAuthLoginController@handleCallback');
Route::get('twitter_auth/{provider}', '\App\Controllers\Auth\SocialAuthLoginController@redirectTwitter');
Route::get('twitter_auth/{provider}/callback', '\App\Controllers\Auth\SocialAuthLoginController@handleTwitterCallback');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
