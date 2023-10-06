<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdvertizerFormSaveRequest;
use App\Http\Requests\CampaignDetailUpdateRequest;
use App\Http\Requests\CampaignSaveRequest;
use App\Models\AdvertizerRequest;
use App\Models\AdvertiserCampaignModel;
use App\Http\Traits\CommonTrait;



class AdvertizerController extends Controller
{
    use CommonTrait;
    
    public function form(){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        return view('advertiser.form');
    }
    
    public function form_save(AdvertizerFormSaveRequest $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }      

        $requestData = $request->post();
        
        if(!empty($requestData['name'])){
        
            $modelObj = new AdvertizerRequest();
            $modelObj->name = $requestData['name'];
            if(!empty($requestData['manual_id'])){
                $modelObj->manual_id = $requestData['manual_id'];
            }
//            $modelObj->target_url = $requestData['target_url'];

            $modelObj->save();
           
            return redirect('/tracking/campaign/list?s=1');
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
        if(!empty($requestData) && !empty($requestData['advertiser_id']) && !empty($requestData['target_url']) && !empty($requestData['target_count']) && !empty($requestData['query_string'])){
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
                $tableObj->query_string = $requestData['query_string'];
                $tableObj->target_count = $requestData['target_count'];
                $tableObj->updated_at = date('Y-m-d H:i:s');
                $tableObj->save();

                return redirect()->back()->with('success_status','Campaign Details Added Successfully');
             }
        }
    }
    
    public function campaignlist(Request $request){
        
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
        $result = $modelObj->list();
        return view('advertiser.list', ['data' => $result, 'success' => $request->s??0]);
        
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
                $dbObj->target_url = $requestData['target_url'];
                
                $dbObj->campaign_name = $requestData['campaign_name'];
                if(!empty($requestData['subid'])){
                    $dbObj->subid = $requestData['subid'];
                }
                $dbObj->link_type = $requestData['link_type'];
                
                $dbObj->query_string = $requestData['query_string'];
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
                $d['query_string'] = $data->query_string;
                $d['status'] = $data->status;
                
                $response[] = $d;
            }
            return $response;
        }
        return [];
    }
}
