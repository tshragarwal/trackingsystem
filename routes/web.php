<?php

use App\Http\Controllers\Askk2knowSearchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TrckWinnersSearchController;
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

$host = request()->getHttpHost();
$adminAppDomain = explode(',', env('ADMIN_APP_DOMAIN'));
$adminPublisherDomain = explode(',', env('PUBLISHER_DOMAIN'));


if(in_array($host, array_merge($adminAppDomain, $adminPublisherDomain))){
    Route::get('/', function () {
        return redirect('/login');
    });

    //Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth'])->name('dashboard');
    Route::get('/dashboard', function () {
        if(Auth::user()->user_type === 'admin') {
            return redirect()->route('companySelection');
        } else if (Auth::user()->user_type === 'publisher') {
            return redirect()->route('report.list', ['type' => 'n2s', 'company_id' => Auth::user()->company_id]);
        }
    })->middleware(['auth', 'checkdomain'])->name('dashboard');


    require __DIR__.'/auth.php';

    Auth::routes();
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/company/choose', [CompanyController::class, 'selection'])->middleware(['auth', 'admin'])->name("companySelection");

    Route::middleware(['auth', 'verifyAndSetCompany'])->prefix('{company_id}')->group(function() {

        Route::middleware(['admin'])->group(function() {
            //--------- Advertiser  ----------------- //
            // List
            Route::get('/advertiser', [App\Http\Controllers\AdvertizerController::class, 'index'] )->name('advertiser.list');

            // Create
            Route::get('/advertiser/create', [App\Http\Controllers\AdvertizerController::class, 'create'] )->name('advertiser.create');
            Route::post('/advertiser', [App\Http\Controllers\AdvertizerController::class, 'store'] )->name('advertiser.store');

            // Edit
            Route::get('/advertiser/{id}/edit', [App\Http\Controllers\AdvertizerController::class, 'edit'] )->name('advertiser.edit');
            Route::patch('/advertiser/{id}', [App\Http\Controllers\AdvertizerController::class, 'update'] )->name('advertiser.update');

            // Destroy
            Route::delete('/advertiser/{id}', [App\Http\Controllers\AdvertizerController::class, 'destroy'] )->name('advertiser.delete');

            //--------- Campaign  ----------------- //
            // List
            Route::get('/campaign', [App\Http\Controllers\CampaignController::class, 'index'] )->name('campaign.list');
            Route::get('/campaign/{id}/list', [App\Http\Controllers\CampaignController::class, 'list'] )->name('campaign.filter-list');
            Route::get('/campaign/{id}/publishers', [App\Http\Controllers\CampaignController::class, 'campaignsPublisherList'] )->name('campaign.publishers-list');

            // Create
            Route::get('/campaign/create', [App\Http\Controllers\CampaignController::class, 'create'] )->name('campaign.create');
            Route::post('/campaign', [App\Http\Controllers\CampaignController::class, 'store'] )->name('campaign.store');

            // Edit
            Route::get('/campaign/{id}/edit', [App\Http\Controllers\CampaignController::class, 'edit'] )->name('campaign.edit');
            Route::patch('/campaign/{id}', [App\Http\Controllers\CampaignController::class, 'update'] )->name('campaign.update');
            Route::patch('/campaign/{id}/status', [App\Http\Controllers\CampaignController::class, 'statusUpdate'])->name('campaign.statusUpdate');

            // Destroy
            Route::delete('/campaign/{id}', [App\Http\Controllers\CampaignController::class, 'destroy'] )->name('campaign.delete');

            // other operations
            Route::post('/campaign/sync/geolocation', [App\Http\Controllers\CampaignController::class, 'sync_geolocation'] )->name('advertiser.sync_geolocation');

            //--------- Publisher  ----------------- //
            // List
            Route::get('/publisher', [App\Http\Controllers\PublisherController::class, 'index'] )->name('publisher.list');

            // Create
            Route::get('/publisher/create', [App\Http\Controllers\PublisherController::class, 'create'] )->name('publisher.create');
            Route::post('/publisher', [App\Http\Controllers\PublisherController::class, 'store'] )->name('publisher.store');

            // Edit
            Route::get('/publisher/{id}/edit', [App\Http\Controllers\PublisherController::class, 'edit'] )->name('publisher.edit');
            Route::patch('/publisher/{id}', [App\Http\Controllers\PublisherController::class, 'update'] )->name('publisher.update');

            // Destroy
            Route::delete('/publisher/{id}', [App\Http\Controllers\PublisherController::class, 'destroy'] )->name('publisher.delete');
        
            //--------- Publisher Jobs ----------------- //
            // List
            Route::get('/publisher-job', [App\Http\Controllers\PublisherJobController::class, 'index'] )->name('publisherJob.list');

            // Create
            Route::get('/publisher-job/create/{campaign_id?}', [App\Http\Controllers\PublisherJobController::class, 'create'] )->name('publisherJob.create');
            Route::post('/publisher-job', [App\Http\Controllers\PublisherJobController::class, 'store'] )->name('publisherJob.store');

            // Edit
            Route::patch('/publisher-job/{id}/toggle-status', [App\Http\Controllers\PublisherJobController::class, 'updateStatus'] )->name('publisherJob.updateStatus');

            // Destroy
            Route::delete('/publisher-job/{id}', [App\Http\Controllers\PublisherJobController::class, 'destroy'] )->name('publisherJob.delete');
            
            // ---------------  Report  ----------//
            Route::get('/report/{type}/upload', [App\Http\Controllers\ReportController::class, 'upload'])->name('report.upload');
            Route::post('/report/{type}/upload', [App\Http\Controllers\ReportController::class, 'store'] )->name('report.saveCSV');
            Route::get('/report/{type}/download-sample-csv', [App\Http\Controllers\ReportController::class, 'downloadSampleCSV'] )->name('report.downloadSample');
            Route::delete('/report/{type}/flush-data', [App\Http\Controllers\ReportController::class, 'flushTable'] )->name('report.flushData');
            Route::get('/report/{type}/{id}/edit', [App\Http\Controllers\ReportController::class, 'edit'] )->name('report.edit');
            Route::patch('/report/{type}/{id}', [App\Http\Controllers\ReportController::class, 'update'] )->name('report.update');
            Route::delete('/report/{type}/{id}', [App\Http\Controllers\ReportController::class, 'destroy'] )->middleware(['auth'])->name('report.destroy');
            
        });

        Route::middleware(['checkdomain'])->group(function() {
            // ---- report
            Route::get('/report/{type}', [App\Http\Controllers\ReportController::class, 'index'] )->name('report.list');

            // N2S report download
            Route::get('/report/n2s/download', [App\Http\Controllers\ReportController::class, 'n2s_downloadcsv'] )->name('report.downloadcsv');
            
            // Type-IN report download
            Route::get('/report/typein/download', [App\Http\Controllers\ReportController::class, 'typein_downloadcsv'] )->name('report.typein_downloadcsv');

            //--------- Publisher ----------------- //
            Route::get('/publisher/token/list', [App\Http\Controllers\PublisherTokenController::class, 'publisher_token_list'] )->name('publisher_token.token_list');
            Route::post('/publisher/token/generate', [App\Http\Controllers\PublisherTokenController::class, 'publisher_token_generate'] )->name('publisher_token.token_generate');
        

            // ---------- CSV && Report
                        
            Route::get('/traffic/keyword/list', [App\Http\Controllers\TrackingKeywordController::class, 'keyword_list'] )->name('traffic.keyword_list');
            Route::get('/traffic/count/list', [App\Http\Controllers\TrackingKeywordController::class, 'count_list'] )->name('traffic.count_list');
            Route::get('/report/agent/list', [App\Http\Controllers\TrackingKeywordController::class, 'agent_report'] )->name('traffic.agent_report');
            Route::get('/report/location/list', [App\Http\Controllers\TrackingKeywordController::class, 'location_report'] )->name('traffic.location_report');
            Route::get('/report/device/list', [App\Http\Controllers\TrackingKeywordController::class, 'device_report'] )->name('traffic.device_report');
            Route::get('/report/ip/list', [App\Http\Controllers\TrackingKeywordController::class, 'ip_report'] )->name('traffic.ip_report');
            Route::get('/report/platform/list', [App\Http\Controllers\TrackingKeywordController::class, 'platform_report'] )->name('traffic.platform_report');
            Route::get('/reports/tracking', [App\Http\Controllers\TrackingKeywordController::class, 'tracking_report'] )->name('traffic.tracking_report');
 
        });        
    });

}

if($host == env('TRCKWINNERS_DOMAIN')){
    // --------- Tracking Url --------------- //
    Route::get('/search', [TrckWinnersSearchController::class, 'search']);
    
}

if($host == env('ASKK2KNOW_DOMAIN')){
    // --------- Tracking Url --------------- //
    Route::get('/search/{token}', [Askk2knowSearchController::class, 'search']);
    
}

if(in_array($host, [env('SEARCHOSS_API_DOMAIN'), env('RNMATRIKS_API_DOMAIN')])){
    Route::get('/publisher/token/data', [App\Http\Controllers\PublisherTokenController::class, 'publisher_token_data'] )->name('publisher_token.token_data');
    Route::get('/lead/verify', [App\Http\Controllers\PublisherTokenController::class, 'lead_verify'])->name('lead.verify');
}