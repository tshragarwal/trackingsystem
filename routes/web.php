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

$prepix = "/tracking";

Route::get($prepix.'/', function () {
    return view('welcome');
});

//Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth'])->name('dashboard');
Route::get($prepix.'/dashboard', function () { return redirect('/tracking/report/list'); })->middleware(['auth'])->name('dashboard');


require __DIR__.'/auth.php';

Auth::routes();

Route::get($prepix.'/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



//--------- Advertiser  ----------------- //
Route::get($prepix.'/advertiser/form', [App\Http\Controllers\AdvertizerController::class, 'form'] )->middleware(['auth'])->name('advertiser.form');
Route::post($prepix.'/advertiser/form/save', [App\Http\Controllers\AdvertizerController::class, 'form_save'] )->middleware(['auth'])->name('advertiser.formsave');
Route::get($prepix.'/advertiser/campaign', [App\Http\Controllers\AdvertizerController::class, 'campaign'] )->middleware(['auth'])->name('advertiser.campaign');
Route::get($prepix.'/advertiser/campaign/list/{advertiser_id}', [App\Http\Controllers\AdvertizerController::class, 'advertiser_campaign_list'] )->middleware(['auth']);
Route::post($prepix.'/advertiser/campaignsave', [App\Http\Controllers\AdvertizerController::class, 'campaignsave'] )->middleware(['auth'])->name('advertiser.campaignsave');


//--------- Campaign  ----------------- //
Route::get($prepix.'/campaign/list', [App\Http\Controllers\AdvertizerController::class, 'campaignlist'] )->middleware(['auth'])->name('campaign.list');
Route::post($prepix.'/campaign/update', [App\Http\Controllers\AdvertizerController::class, 'campaignupdate'] )->middleware(['auth'])->name('campaign.update');
Route::get($prepix.'/campaign/detail/{id}', [App\Http\Controllers\AdvertizerController::class, 'campaigndetail'] )->middleware(['auth'])->name('advertiser.detail');




//--------- Publisher  ----------------- //
Route::get($prepix.'/publisher/list', [App\Http\Controllers\PublisherController::class, 'list'] )->middleware(['auth'])->name('publisher.list');
Route::get($prepix.'/publisher/form', [App\Http\Controllers\PublisherController::class, 'form'] )->middleware(['auth'])->name('publisher.form');
Route::post($prepix.'/publisher/save', [App\Http\Controllers\PublisherController::class, 'save'] )->middleware(['auth'])->name('publisher.save');
Route::get($prepix.'/publisher/detail/{id}', [App\Http\Controllers\PublisherController::class, 'publisher_detail'] )->middleware(['auth'])->name('publisher.detail');
Route::post($prepix.'/publisher/update', [App\Http\Controllers\PublisherController::class, 'publisher_update'] )->middleware(['auth'])->name('publisher.update');


//--------- Publisher Jobs ----------------- //
Route::get($prepix.'/publisher/job/list', [App\Http\Controllers\PublisherJobController::class, 'list'] )->middleware(['auth'])->name('publisher.job.list');
Route::get($prepix.'/publisher/job/form', [App\Http\Controllers\PublisherJobController::class, 'form'] )->middleware(['auth'])->name('publisher.job.form');
Route::post($prepix.'/publisher/job/save', [App\Http\Controllers\PublisherJobController::class, 'save'] )->middleware(['auth'])->name('publisher.job.save');


// --------- Tracking Url --------------- //
Route::get($prepix.'/ts/{proxy_url}', [App\Http\Controllers\PublisherJobController::class, 'tracking_url']);

// ---------- CSV && Report
Route::get($prepix.'/report/list', [App\Http\Controllers\ReportController::class, 'list'] )->middleware(['auth'])->name('report.list');
Route::get($prepix.'/report/csv', [App\Http\Controllers\ReportController::class, 'csv'] )->middleware(['auth'])->name('report.csv');
Route::post($prepix.'/report/uploadcsv', [App\Http\Controllers\ReportController::class, 'uploadcsv'] )->middleware(['auth'])->name('report.uploadcsv');
Route::get($prepix.'/report/download', [App\Http\Controllers\ReportController::class, 'downloadcsv'] )->middleware(['auth'])->name('report.downloadcsv');