<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublisherJobModel;
use App\Models\TrackingPublisherJobModel;
use Exception;
use Illuminate\Support\Facades\Redirect;

class TrckWinnersSearchController extends Controller
{

  public function search(request $request) {
    try{
      $code = $request->code;
      if(empty($code)) {
        return response()->json(['message' => 'Invalid code'], 403);
      }

      $searchKeyword = $request->get('q') ?? '';
      if($searchKeyword === '' || $searchKeyword === '{keyword}') {
        return response()->json(['message' => 'Please provide query string'], 403);
      }

      $pjmObj = new PublisherJobModel();
      $job = $pjmObj->get_record_of_proxy_url($code);
      if(!empty($job)){ 
        if($job->status == 1 && !empty($job->campaign) && !empty($job->campaign->id)) {

          if(($job->id === 846 || $job->id === 895 ) && $job->target_count <= $job->tracking_count) {
            return response()->json(['message' => 'Daily Target count limit reached.'], 200);
          }

          if($job->campaign->status == 2){
            return response()->json(['message' => 'Campaign is Paused'], 200);
          }else if($job->campaign->status == 3){
            return response()->json(['message' => 'Campaign already Completed.'], 200);
          }

          if(isset($_SERVER['HTTP_REFERER']) && !($job->campaign->enable_referer_redirection)) {
            return response()->json(['message' => 'Referer Not Allowed'], 406);
          }
          
          $user_agent = $this->getUserAgentDetails(); //$this->getBrowser();
          
          if(!empty($user_agent)) {
              if( !$job->campaign->allow_tablet && (($user_agent['is_mobile'] == 1) && ($user_agent['is_tablet'] == 1)) ){
                  return response()->json(['message' => 'Tablet Not Allowed'], 406);
              }
              if(!$job->campaign->allow_mobile && ($user_agent['is_mobile'] == 1 && ($user_agent['is_tablet'] == 0) )){
                  return response()->json(['message' => 'Mobile Not Allowed'], 406);
              }
              if(!$job->campaign->allow_desktop && ($user_agent['is_mobile'] == 0 && $user_agent['is_tablet'] == 0 )){
                  return response()->json(['message' => 'Desktop Not Allowed'], 406);
              }
          }

          // ------ save data into Tracking table ----------//
          $tableObj = new TrackingPublisherJobModel();
          $tableObj->publisher_job_id = $job->id;
          $tableObj->publisher_id = $job->publisher_id;
          $tableObj->campaign_id = $job->campaign->id;
          $tableObj->advertiser_id = $job->campaign->advertiser_id;
          $tableObj->subid = $job->campaign->subid;
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

          $requestData = $request->all();
          
          $finalRedirectUrl = str_replace('{keyword}', $requestData['q'], $job->campaign->target_url);
          
          if (strpos($job->campaign->target_url, '{clkid}') !== false) {
              $finalRedirectUrl = str_replace('{clkid}', base64_encode($tableObj->id), $finalRedirectUrl);
          }
          unset($requestData['q']);
          
          if (strpos($finalRedirectUrl, 'http://') !== 0 && strpos($finalRedirectUrl, 'https://') !== 0) {
              $finalRedirectUrl = 'http://'.$finalRedirectUrl;
          }

          $job->tracking_count++;
          $job->update();

          return Redirect::away($finalRedirectUrl);

        } else {
          return response()->json(['message' => 'Inactive Job'], 406);
        }
    }
        
      return response()->json(['message' => 'Record not found.'], 404);
      
    } catch(Exception $e) {
      return response()->json(['message' => $e->getMessage()], $e->getCode());
    }
  }

}
