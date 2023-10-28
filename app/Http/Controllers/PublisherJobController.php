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
        
        $publisherId = ($user->user_type =='publisher') ? $user->id: (!empty($request->publisher_id)  ?$request->publisher_id : 0 );
        $modelObj = new PublisherJobModel();
        $result = $modelObj->list($publisherId, 10);
        
        $domainName = env('APP_DOMAIN');
        return view('publisher_job.list', ['data' => $result, 'success' => $request->s??0, 'domain' => $domainName, 'user_type' => $user->user_type]);
        
    }
    
    public function form(Request $request){
//        if(!CommonTrait::is_super_admin()){
//            return view('access_denied');
//        }
        $requestData = $request->all();
        $userObj = new User();
        $publisherList = $userObj->get_publisher_list(10000);
        
        $campaign_id = $requestData['campaign_id']??0;
        $modelObj = new AdvertiserCampaignModel();
        $advertiserCampaign = $modelObj->active_list($campaign_id, 50);
        
        $allAdvertiserObj = AdvertizerRequest::all();
        
        $camArray = [];
        if(!empty($campaign_id) && !empty($advertiserCampaign->first()) ) {
            $campInfo = $advertiserCampaign->first();
            $camArray['advertiser_id'] = $campInfo->advertiser->id;
            $camArray['advertizer_name'] = $campInfo->advertiser->name;
            $camArray['advertizer_email'] = $campInfo->advertiser->manual_email;
            $camArray['campaign_id'] = $campInfo->id;
            $camArray['campaign_name'] = $campInfo->campaign_name;
        }
        return view('publisher_job.form', ['publisher' => $publisherList, 'advertiserCampaign' => $advertiserCampaign, 'advertiserObj' => $allAdvertiserObj, 'camp_array' => $camArray]);
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
        
        $id = $tableObj->id;
        $url = env('APP_DOMAIN').'/search?code='.$uid.'&offerid='.$id.'&q={keyword}';
        
        return redirect()->back()->with(['success_status' => 'Successfully Campaign is assigned to Publisher', 'link_url' => $url]);
        
    }
    
    public function tracking_url(request $request){
        if(!empty($request->code)){
            $publisherJobModel = new PublisherJobModel();
            $publisherJobObj = $publisherJobModel->get_record_of_proxy_url($request->code);
           
            if(!empty($publisherJobObj->id) && $publisherJobObj->status == 1 && !empty($publisherJobObj->campaign) && !empty($publisherJobObj->campaign->id)){
                
                if($publisherJobObj->campaign->status == 2){
                    return response()->json(['message' => 'Campaign is Paused'], 200);
                }else if($publisherJobObj->campaign->status == 3){
                    return response()->json(['message' => 'Campaign already Completed.'], 200);
                }
                
                // ------ save data into Tracking table ----------//
                $tableObj = new TrackingPublisherJobModel();
                $tableObj->publisher_job_id = $publisherJobObj->id;
                $tableObj->ip = $request->ip();
                $tableObj->created_at = date('Y-m-d H:i:s');
                $tableObj->save();


                // --------- Now redirect it to target url --------//
//                $queryParams = [];
//                parse_str($publisherJobObj->campaign->query_string, $queryParams);
                $requestData = $request->all();
                unset($requestData['code']);
                
                
                $finalRedirectUrl = str_replace('{keyword}', $requestData['q'], $publisherJobObj->campaign->target_url);
                unset($requestData['q']);
                
//                $finalRedirectUrl = (!empty($requestData))? ($finalRedirectUrl. '&'. http_build_query($requestData)): $finalRedirectUrl;
                
//                $final_queryString = http_build_query(array_merge($queryParams, $requestData));
//                $result = strstr($publisherJobObj->campaign->target_url, '?', true);
//                $finalRedirectUrl = ($result !== false ? $result : $publisherJobObj->campaign->target_url).  '?'. $final_queryString;
                
                
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
        return response()->json(['message' => 'Inactive Job'], 406);
    }
    
    public function delete_publisher_job(Request $request){
        $requestData = $request->all();

        if(!empty($requestData['publisher_job_id'])){
            
            $record = PublisherJobModel::find($requestData['publisher_job_id']);
            if ($record) {
                $record->delete();
                $message = 'Publisher Job deleted successfully';
                
                return response()->json(['message' => $message, 'status' => 1]);
            } else {
                return response()->json(['message' => 'Publisher not found'], 404);
            }
        }
    }    
}
