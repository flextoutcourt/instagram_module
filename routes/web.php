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
    return view('welcome');
});



Route::get('/instagram/publish', [\App\Http\Controllers\InstagramPublish::class, 'index'])->name('index');
Route::get('/instagram/publish/login', [\App\Http\Controllers\InstagramPublish::class, 'check_user'])->name('post');
Route::get('/instagram/publish/oauth_redirect', [\App\Http\Controllers\InstagramPublish::class, 'oauth_redirect'])->name('oauth_redirect');
