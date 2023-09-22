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

//Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth'])->name('dashboard');
Route::get('/dashboard', function () { return redirect('/publisher/job/list'); })->middleware(['auth'])->name('dashboard');


require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



//--------- Advertiser  ----------------- //
Route::get('/advertiser/form', [App\Http\Controllers\AdvertizerController::class, 'form'] )->middleware(['auth'])->name('advertiser.form');
Route::post('/advertiser/form/save', [App\Http\Controllers\AdvertizerController::class, 'form_save'] )->middleware(['auth'])->name('advertiser.formsave');
Route::get('/advertiser/campaign', [App\Http\Controllers\AdvertizerController::class, 'campaign'] )->middleware(['auth'])->name('advertiser.campaign');
Route::get('/advertiser/campaign/list/{advertiser_id}', [App\Http\Controllers\AdvertizerController::class, 'advertiser_campaign_list'] )->middleware(['auth']);
Route::post('/advertiser/campaignsave', [App\Http\Controllers\AdvertizerController::class, 'campaignsave'] )->middleware(['auth'])->name('advertiser.campaignsave');


//--------- Campaign  ----------------- //
Route::get('/campaign/list', [App\Http\Controllers\AdvertizerController::class, 'campaignlist'] )->middleware(['auth'])->name('campaign.list');
Route::post('/campaign/update', [App\Http\Controllers\AdvertizerController::class, 'campaignupdate'] )->middleware(['auth'])->name('campaign.update');
Route::get('/campaign/detail/{id}', [App\Http\Controllers\AdvertizerController::class, 'campaigndetail'] )->middleware(['auth'])->name('advertiser.detail');




//--------- Publisher  ----------------- //
Route::get('/publisher/list', [App\Http\Controllers\PublisherController::class, 'list'] )->middleware(['auth'])->name('publisher.list');
Route::get('/publisher/form', [App\Http\Controllers\PublisherController::class, 'form'] )->middleware(['auth'])->name('publisher.form');
Route::post('/publisher/save', [App\Http\Controllers\PublisherController::class, 'save'] )->middleware(['auth'])->name('publisher.save');
Route::get('/publisher/detail/{id}', [App\Http\Controllers\PublisherController::class, 'publisher_detail'] )->middleware(['auth'])->name('publisher.detail');
Route::post('/publisher/update', [App\Http\Controllers\PublisherController::class, 'publisher_update'] )->middleware(['auth'])->name('publisher.update');


//--------- Publisher Jobs ----------------- //
Route::get('/publisher/job/list', [App\Http\Controllers\PublisherJobController::class, 'list'] )->middleware(['auth'])->name('publisher.job.list');
Route::get('/publisher/job/form', [App\Http\Controllers\PublisherJobController::class, 'form'] )->middleware(['auth'])->name('publisher.job.form');
Route::post('/publisher/job/save', [App\Http\Controllers\PublisherJobController::class, 'save'] )->middleware(['auth'])->name('publisher.job.save');


// --------- Tracking Url --------------- //
Route::get('/ts/{proxy_url}', [App\Http\Controllers\PublisherJobController::class, 'tracking_url']);