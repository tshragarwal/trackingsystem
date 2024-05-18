<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\CommonTrait;
use App\Models\AdvertiserCampaignModel;
use App\Models\Advertiser;
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
    public function index(Request $request, int $companyID) {
      $user = Auth()->user();
      $request->merge(['company_id' => $companyID]);

      $jobs = new PublisherJobModel();
      $result = $jobs->list($request->all(), 20);
      $scheme = $request->secure() ? 'https://' : 'http://';
      $domainName = $scheme . env('TRCKWINNERS_DOMAIN');
      if($companyID === 2) {
        $domainName = $scheme . env('ASKK2KNOW_DOMAIN');
      }

      return view('publisher_job.list', ['data' => $result, 'success' => $request->s??0, 'domain' => $domainName, 'user_type' => $user->user_type, 'filter' => $request->all()]);

    }

    public function create(int $companyID, int $campaignID = 0) {
      $publisherList = User::publisherList(['company_id' => $companyID]);

      // when redirected from campaign page
      $campaignDetails = [];
      if($campaignID > 0) {
        $campaignDetails = AdvertiserCampaignModel::with('advertiser')->where(['company_id' => $companyID, 'id' => $campaignID])->first();
      }
      
      $advertisers = Advertiser::where('company_id', $companyID)->get();
      return view('publisher_job.create', ['publisher' => $publisherList, 'advertisers' => $advertisers, 'optionalCampaignDetails' => $campaignDetails]);
    
    }

    public function store(AssignPublisherJob $request, int $companyID) {
      $uid = $this->getUniqueURLCode();

      $job = PublisherJobModel::create([
        'company_id' => $companyID,
        'publisher_id' => $request->get('publisher_id'),
        'advertiser_campaign_id' => $request->get('advertiser_campaign_id'),
        'proxy_url' => $uid,
        'target_count' => $request->get('target_count'),
      ]);

      $id = $job->id;
      $scheme = $request->secure() ? 'https://' : 'http://';
      $url = $scheme . env('TRCKWINNERS_DOMAIN').'/search?code='.$uid.'&offerid='.$id.'&q={keyword}';

      if($companyID === 2) {
        $uid = $job->proxy_url . str_pad($job->id, 8, '0', STR_PAD_LEFT);
        $job->proxy_url = $uid;
        $job->save();
        $url = $scheme . env('ASKK2KNOW_DOMAIN').'/'.$uid.'&q={keyword}';
      }
      
      return redirect()->back()->with(['success_status' => 'Successfully Campaign is assigned to Publisher', 'link_url' => $url]);
    }

    private function getUniqueURLCode() : string {
      
      do {
          $uid = uniqid().time();
          $existsUrl = PublisherJobModel::where('proxy_url', $uid)->first();
      }while(!empty($existsUrl));

      return $uid;
    }
    
    
    public function updateStatus(Request $request, int $companyID, int $id){
        
      $requestData = $request->all();
      $job = PublisherJobModel::where(['company_id' => $companyID, 'id' => $id])->first();

      if(empty($job)) {
        return response()->json(['message' => 'Job not found'], 404);
      }

      $job->status = !$job->status;
      $job->save();

      return response()->json(['message' => 'Status updated successfully'], 200);
  }

  public function destroy(int $companyID, int $id) {
            
    $job = PublisherJobModel::where(['company_id' => $companyID, 'id' => $id])->first();

    if(empty($job)) {
      return response()->json(['message' => 'Job not found'], 404);
    }

    $job->delete();
    return response()->json(['message' => 'Publisher Job deleted successfully', 'status' => 1]);    
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
