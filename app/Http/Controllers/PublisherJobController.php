<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\CommonTrait;
use App\Models\AdvertiserCampaignModel;
use App\Models\AdvertizerRequest;
use App\Models\User;
use App\Http\Requests\AssignPublisherJob;
use App\Models\PublisherJobModel;
use App\Models\TrackingPublisherJobModel;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PublisherJobController extends Controller
{
    public function list(Request $request){

        
//        if(!CommonTrait::is_super_admin()){
//            return view('access_denied');
//        }
        
        $user = Auth::guard('web')->user();
        
        $publisherId = ($user->user_type =='publisher') ? $user->id:0;
        $modelObj = new PublisherJobModel();
        $result = $modelObj->list($publisherId, 10);
        
        $domainName = env('APP_DOMAIN');
        return view('publisher_job.list', ['data' => $result, 'success' => $request->s??0, 'domain' => $domainName, 'user_type' => $user->user_type]);
        
    }
    
    public function form(){
//        if(!CommonTrait::is_super_admin()){
//            return view('access_denied');
//        }
        
        $userObj = new User();
        $publisherList = $userObj->get_publisher_list(1000);
        
        
        $modelObj = new AdvertiserCampaignModel();
        $advertiserCampaign = $modelObj->active_list();
        
        $allAdvertiserObj = AdvertizerRequest::all();
        
        
        
        return view('publisher_job.form', ['publisher' => $publisherList, 'advertiserCampaign' => $advertiserCampaign, 'advertiserObj' => $allAdvertiserObj]);
    }
    
    public function save(AssignPublisherJob $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }        
        $requestData = $request->post();
        
        do {
            $uid = uniqid().time();
            $existsUrl = PublisherJobModel::where('proxy_url', $uid)->first();
        }while(!empty($existsUrl));
        
        
        $tableObj = new PublisherJobModel();
        $tableObj->publisher_id = $requestData['publisher_id'];
        $tableObj->advertiser_campaign_id = $requestData['advertiser_campaign_id'];
        $tableObj->proxy_url = $uid;
        $tableObj->target_count = $requestData['target_count']??0;
        $tableObj->save();
        
        return redirect()->back()->with('success_status','Successfully Campaign is assigned to Publisher');
        
    }
    
    public function tracking_url(request $request){
        if(!empty($request->proxy_url)){
            $publisherJobModel = new PublisherJobModel();
            $publisherJobObj = $publisherJobModel->get_record_of_proxy_url($request->proxy_url);
           
            if(!empty($publisherJobObj->id) && $publisherJobObj->status == 1 && !empty($publisherJobObj->campaign) && !empty($publisherJobObj->campaign->id)){
                
                // ------ save data into Tracking table ----------//
                $tableObj = new TrackingPublisherJobModel();
                $tableObj->publisher_job_id = $publisherJobObj->id;
                $tableObj->ip = $request->ip();
                $tableObj->created_at = date('Y-m-d H:i:s');
                $tableObj->save();


                // --------- Now redirect it to target url --------//
                $queryParams = [];
                parse_str($publisherJobObj->campaign->query_string, $queryParams);
                $final_queryString = http_build_query(array_merge($queryParams, request()->all()));
                $result = strstr($publisherJobObj->campaign->target_url, '?', true);
                $finalRedirectUrl = ($result !== false ? $result : $publisherJobObj->campaign->target_url).  '?'. $final_queryString;
                if (strpos($finalRedirectUrl, 'http://') !== 0 && strpos($finalRedirectUrl, 'https://') !== 0) {
                    $finalRedirectUrl = 'http://'.$finalRedirectUrl;
                }
                return Redirect::away($finalRedirectUrl);
            
            }else if(!empty($publisherJobObj->id) && $publisherJobObj->status != 1){
                return response()->json(['message' => 'Inactive Job'], 406);
            }else{
                return response()->json(['message' => 'Record not found.'], 404);
            }
        }
    }
}
