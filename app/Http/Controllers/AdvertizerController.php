<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdvertizerFormSaveRequest;
use App\Http\Requests\CampaignDetailUpdateRequest;
use App\Http\Requests\CampaignSaveRequest;
use App\Models\AdvertizerRequest;
use App\Models\AdvertiserCampaignModel;
use App\Http\Traits\CommonTrait;
use Illuminate\Support\Facades\Validator;
use App\Models\PublisherJobModel;



class AdvertizerController extends Controller
{
    use CommonTrait;
    
    public function form(Request $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $advertizer = [];
        if(!empty($request->adv_id)){
            $advertizerObj = AdvertizerRequest::find($request->adv_id);
            $advertizer = $advertizerObj->toArray();
        }
        
        return view('advertiser.form', ['advertizer' => $advertizer]);
    }
    
    public function form_save(AdvertizerFormSaveRequest $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }      
        
        $requestData = $request->post();
        if(!empty($requestData['manual_email'])){
            $validator = Validator::make($requestData, [
                'manual_email' => 'email',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        
        if(!empty($requestData['advertizer_id'])){
            $advertizerObj = AdvertizerRequest::find( $requestData['advertizer_id']);
           
            $advertizerObj->name =  $requestData['name'];
            $advertizerObj->manual_email =  $requestData['manual_email'];
            $advertizerObj->save();
//            return redirect('/tracking/advertiser/list?s=1');
            return redirect()->route('advertiser.list', ['s' => 1]);
        }
        
        if(!empty($requestData['name'])){
        
            $modelObj = new AdvertizerRequest();
            $modelObj->name = $requestData['name'];
            if(!empty($requestData['manual_email'])){
                $modelObj->manual_email = $requestData['manual_email'];
            }

            $modelObj->save();
//            return redirect('/tracking/advertiser/list?s=1');
            return redirect()->route('advertiser.list', ['s' => 1]);
        }
        
    }

    
    public function campaign(){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $allAdvertiserList = AdvertizerRequest::all();

        return view('advertiser.campaign', ['advertiserObj'=> $allAdvertiserList]);
    }
    public function campaignsave(CampaignSaveRequest $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        
        $requestData = $request->post();
        if(!empty($requestData) && !empty($requestData['advertiser_id']) && !empty($requestData['target_url']) && !empty($requestData['target_count'])){
            
            // --- validation for {keyword} in target url -----//
            if (strpos($requestData['target_url'], '{keyword}') === false) {
                return redirect()->back()->with('error_status',"Target Url required '{keyword}'");
            }
            
             $dbObj = AdvertizerRequest::find($requestData['advertiser_id']);
            
             if(!empty($dbObj)){
                 
                $tableObj = new AdvertiserCampaignModel();
                $tableObj->advertiser_id = $requestData['advertiser_id'];
                $tableObj->campaign_name = $requestData['campaign_name'];
                if(!empty($requestData['subid'])){
                    $tableObj->subid = $requestData['subid'];
                }
                $tableObj->link_type = $requestData['link_type'];
                $tableObj->target_url = $requestData['target_url'];
//                $tableObj->query_string = $requestData['query_string'];
                $tableObj->target_count = $requestData['target_count'];
                $tableObj->updated_at = date('Y-m-d H:i:s');
                $tableObj->save();

                return redirect()->back()->with('success_status','Campaign Details Added Successfully');
             }
        }
    }
    
    public function campaignlist(Request $request){
        $requestData = $request->all();
        /*
         * @if(Auth::guard('admin')->check())
    Hello {{Auth::guard('admin')->user()->name}}
@elseif(Auth::guard('user')->check())
    Hello {{Auth::guard('user')->user()->name}}
@endif
         */
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        
        $modelObj = new AdvertiserCampaignModel();
        $result = $modelObj->list($requestData);
        return view('advertiser.list', ['data' => $result, 'success' => $request->s??0, 'filter' => $requestData]);
        
    }
    
    public function campaigndetail(Request $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        if(!empty($request) && !empty($request->id) && is_numeric($request->id)){
            $modelObj = new AdvertiserCampaignModel();
            $result = $modelObj->detail($request->id);
            if(empty($result)){
                return view('advertiser.detail', ['error' => "Invalid Detail", 'data' => '']);
            }
           

            return view('advertiser.detail', ['data' => $result, 'error' => '']);
        }
    }
    
    public function campaignupdate(CampaignDetailUpdateRequest $request){
        
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $requestData = $request->post();
      
        if(!empty($requestData) && !empty($requestData['id'])){
             $dbObj = AdvertiserCampaignModel::find($requestData['id']);
            
             if(!empty($dbObj)){
                
                
                // --- validation for {keyword} in target url -----//
                if (strpos($requestData['target_url'], '{keyword}') === false) {
                    return redirect()->back()->with('error_status',"Target Url required '{keyword}'");
                }
                $dbObj->target_url = $requestData['target_url'];
                
                
                $dbObj->campaign_name = $requestData['campaign_name'];
                if(!empty($requestData['subid'])){
                    $dbObj->subid = $requestData['subid'];
                }
                $dbObj->link_type = $requestData['link_type'];
                
//                $dbObj->query_string = $requestData['query_string'];
                $dbObj->target_count = $requestData['target_count'];
                $dbObj->status = $requestData['status'];
                $dbObj->updated_at = date('Y-m-d H:i:s');
                $dbObj->update();
                
                return redirect()->back()->with('success_status','Campaign data Updated Successfully');
                 
             }
        }
    }
    
    public function advertiser_campaign_list($advertiser_id){
        if(!empty($advertiser_id)){
            $model = new AdvertiserCampaignModel();
            $result = $model->get_advertiser_campaigns($advertiser_id);
           
            $response = [];
            foreach($result as $data){
                $d['id'] = $data->id;
                $d['campaign_name'] = $data->campaign_name;
                $d['subid'] = $data->subid;
                $d['link_type'] = $data->link_type;
                $d['target_url'] = $data->target_url;
                $d['target_count'] = $data->target_count;
//                $d['query_string'] = $data->query_string;
                $d['status'] = $data->status;
                
                $response[] = $d;
            }
            return $response;
        }
        return [];
    }
    
    public function list(Request $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $requestData = $request->all();
        $userObj = new AdvertizerRequest();
        
        $advertizerList = $userObj->get_publisher_list($requestData, 250);
       
        return view('advertiser.advertizerlist', ['data' => $advertizerList, 'success' => $request->s, 'filter' => $requestData ]);
    }    
    
    public function destroy(Request $request){
        $requestData = $request->all();
        if(!empty($requestData['advertizer_id'])){
            
            $record = AdvertizerRequest::find($requestData['advertizer_id']);
            if ($record) {
                
                // -- check is there campaign assign to it or not ------//
                $modelObj = new AdvertiserCampaignModel();
                $count = $modelObj->get_advertizer_campaign_count($requestData['advertizer_id']);
                $s = 1;
                if( $count > 0){
                    $message = "Avertizer can not be deleted because $count campaign is assigned it advertizer. Please first delete all associated campaign then Advertizer. ";
                    $s = 0;
                }else{
                    $record->delete();
                    $message = 'Advertizer deleted successfully';
                }
                
                
                return response()->json(['message' => $message, 'status' => $s]);
            } else {
                return response()->json(['message' => 'Advertizer not found'], 404);
            }
        }
    }
    
   
    public function delete_campaign(Request $request){
        $requestData = $request->all();
        if(!empty($requestData['campaign_id'])){
            
            $record = AdvertiserCampaignModel::find($requestData['campaign_id']);
            if ($record) {
                
                // -- check is there campaign assign to it or not ------//
                $modelObj = new PublisherJobModel();
                $count = $modelObj->get_all_campaign_publisher_job_count($requestData['campaign_id']);
                $s = 1;
                if( $count > 0){
                    $message = "Campaign can not be deleted because $count Publisher Job is assigned with this Campaign. Please firstly delete all associated Publisher Job.";
                    $s = 0;
                }else{
                    $record->delete();
                    $message = 'Campaign deleted successfully';
                }
                
                
                return response()->json(['message' => $message, 'status' => $s]);
            } else {
                return response()->json(['message' => 'Campaign not found'], 404);
            }
        }
    }    
}
