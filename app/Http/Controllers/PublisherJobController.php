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
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Crypt;

class PublisherJobController extends Controller
{
    public function list(Request $request){

        
//        if(!CommonTrait::is_super_admin()){
//            return view('access_denied');
//        }
        $user = Auth::guard('web')->user();
        
        $publisherId = ($user->user_type =='publisher') ? $user->id: (!empty($request->publisher_id)  ?$request->publisher_id : 0 );
        $modelObj = new PublisherJobModel();
        $result = $modelObj->list($request->all(), $publisherId, 1000);
        
        $domainName = env('APP_DOMAIN');
        return view('publisher_job.list', ['data' => $result, 'success' => $request->s??0, 'domain' => $domainName, 'user_type' => $user->user_type, 'filter' => $request->all()]);
        
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
                
//                dd($publisherJobObj->campaign, $_SERVER);
                if(isset($_SERVER['HTTP_REFERER']) && !($publisherJobObj->campaign->enable_referer_redirection)) {
                     return response()->json(['message' => 'Referer Not Allowed'], 406);
                }
                
                $user_agent = $this->getUserAgentDetails(); //$this->getBrowser();
                
                if(!empty($user_agent)) {
                    if( !$publisherJobObj->campaign->allow_tablet && (($user_agent['is_mobile'] == 1) && ($user_agent['is_tablet'] == 1)) ){
                        return response()->json(['message' => 'Tablet Not Allowed'], 406);
                    }
                    if(!$publisherJobObj->campaign->allow_mobile && ($user_agent['is_mobile'] == 1 && ($user_agent['is_tablet'] == 0) )){
                        return response()->json(['message' => 'Mobile Not Allowed'], 406);
                    }
                    if(!$publisherJobObj->campaign->allow_desktop && ($user_agent['is_mobile'] == 0 && $user_agent['is_tablet'] == 0 )){
                        return response()->json(['message' => 'Desktop Not Allowed'], 406);
                    }
                }
                
                
                // ------ save data into Tracking table ----------//
                $tableObj = new TrackingPublisherJobModel();
                $tableObj->publisher_job_id = $publisherJobObj->id;
                $tableObj->publisher_id = $publisherJobObj->publisher_id;
                $tableObj->campaign_id = $publisherJobObj->campaign->id;
                $tableObj->advertiser_id = $publisherJobObj->campaign->advertiser_id;
                $tableObj->subid = $publisherJobObj->campaign->subid;
                $tableObj->ip = $request->ip();
                $tableObj->created_at = date('Y-m-d H:i:s');
                $tableObj->keyword = $request->q;
                $tableObj->date =  date('Y-m-d');
                $tableObj->user_agent =  $user_agent['userAgent'];
                $tableObj->browser =  $user_agent['browser'];
                $tableObj->browser_version = $user_agent['browser_version'];
                $tableObj->platform = $user_agent['platform'];
                $tableObj->platform_version = $user_agent['platform_version'];
                $tableObj->is_mobile = $user_agent['is_mobile'];
                $tableObj->is_tablet = $user_agent['is_tablet'];
                $tableObj->device = $user_agent['device'];
                $tableObj->save();


                // --------- Now redirect it to target url --------//
//                $queryParams = [];
//                parse_str($publisherJobObj->campaign->query_string, $queryParams);
                $requestData = $request->all();
                unset($requestData['code']);
                
                
                $finalRedirectUrl = str_replace('{keyword}', $requestData['q'], $publisherJobObj->campaign->target_url);
                
                if (strpos($publisherJobObj->campaign->target_url, '{clkid}') !== false) {
                    $finalRedirectUrl = str_replace('{clkid}', base64_encode($tableObj->id), $finalRedirectUrl);
                }
                unset($requestData['q']);
                
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
    
    public function status_update(Request $request){
        
        $requestData = $request->all();
        
        if( !empty($requestData) && !empty($requestData['id']) && in_array($requestData['status'], [0,1]) ) {
            $obj = PublisherJobModel::find($requestData['id']);
            if($obj){
                $status= $requestData['status']==1? 0:1;
                $obj->update(['status' => $status]);
            }
            return true;
        }
        
        return false;
    }
    
    function getBrowser() { 
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
          $platform = 'linux';
        }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
          $platform = 'mac';
        }elseif (preg_match('/windows|win32/i', $u_agent)) {
          $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
          $bname = 'Internet Explorer';
          $ub = "MSIE";
        }elseif(preg_match('/Firefox/i',$u_agent)){
          $bname = 'Mozilla Firefox';
          $ub = "Firefox";
        }elseif(preg_match('/OPR/i',$u_agent)){
          $bname = 'Opera';
          $ub = "Opera";
        }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
          $bname = 'Google Chrome';
          $ub = "Chrome";
        }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
          $bname = 'Apple Safari';
          $ub = "Safari";
        }elseif(preg_match('/Netscape/i',$u_agent)){
          $bname = 'Netscape';
          $ub = "Netscape";
        }elseif(preg_match('/Edge/i',$u_agent)){
          $bname = 'Edge';
          $ub = "Edge";
        }elseif(preg_match('/Trident/i',$u_agent)){
          $bname = 'Internet Explorer';
          $ub = "MSIE";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
      ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
          // we have no matching number just continue
        }
        $i = count($matches['browser']);
        if ($i != 1) {
          //we will have two since we are not using 'other' argument yet
          //see if version is before or after the name
          if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
              $version= $matches['version'][0];
          }else {
              $version= $matches['version'][1];
          }
        }else {
          $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
          'userAgent' => $u_agent,
          'name'      => $bname,
          'version'   => $version,
          'platform'  => $platform,
          'pattern'    => $pattern
        );
    }

    public function getUserAgentDetails() {
      $agent = new Agent();

      // Get the browser name
      $browser = $agent->browser();

      // Get the browser version
      $version = $agent->version($browser);

      // Get the platform name (Operating System)
      $platform = $agent->platform();

      // Get the platform version
      $platformVersion = $agent->version($platform);

      // Check if the device is mobile
      $isMobile = $agent->isMobile();

      // Check if the device is a tablet
      $isTablet = $agent->isTablet();

      // Get device name
      $device = $agent->device();

      return [
          'browser' => $browser,
          'browser_version' => $version,
          'platform' => $platform,
          'platform_version' => $platformVersion,
          'is_mobile' => $isMobile,
          'is_tablet' => $isTablet,
          'device' => $device,
          'userAgent' => $_SERVER['HTTP_USER_AGENT'],
      ];
    }

}
