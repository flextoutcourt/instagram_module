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

Route::get('/instagram/publish/post', [\App\Http\Controllers\InstagramPublishController::class, 'index'])->name('instagram.publish.post.get');
Route::post('/instagram/publish/post', [\App\Http\Controllers\InstagramPublishController::class, 'store'])->name('instagram.publish.post.post');

Route::get('/instagram/publish/carousel', [\App\Http\Controllers\InstagramCarouselController::class, 'index'])->name('instagram.publish.carousel.get');
Route::post('/instagram/publish/carousel', [\App\Http\Controllers\InstagramCarouselController::class, 'store'])->name('instagram.publish.carousel.post');

Route::get('/instagram/publish/reels', [\App\Http\Controllers\InstagramReelController::class, 'index'])->name('instagram.publish.reels.get');
Route::post('/instagram/publish/reels', [\App\Http\Controllers\InstagramReelController::class, 'store'])->name('instagram.publish.reels.post');

Route::get('/instagram/publish/success', [\App\Http\Controllers\InstagramController::class, 'success'])->name('instagram.publish.success');

Route::get('/instagram/publish/oauth_redirect', [\App\Http\Controllers\InstagramPublishController::class, 'oauth_redirect'])->name('oauth_redirect');

