<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrackingKeywordModel;
use App\Models\AdvertizerRequest;
use App\Models\User;
use App\Http\Traits\CommonTrait;

class TrackingKeywordController extends Controller
{
    use CommonTrait;
    public function keyword_list(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->keyword_list($data, 1000);
        
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.keywordlist', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
    }
    public function count_list(Request $request){
        
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->count_list($data, 1000);
        
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.countlist', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
    }
   
    private function get_advertizer_publisher_list(): array {
        // --- in case of superadmin -------
        $advertizer_list = [];
        $publisher_list = [];
        if(CommonTrait::is_super_admin()) {
            $allAdvertizer = AdvertizerRequest::all('id','name');
            $advertizer_list = $allAdvertizer->toArray();
            
            $userModel = new User();
            $publisher_list = $userModel->all_publisher_list();
            $publisher_list = $publisher_list->toArray();
        }
        
        return ['advertizer_list' => $advertizer_list, 'publisher_list' => $publisher_list];
    }
    
    
    public function agent_report(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->agent_report($data, 1000);
        
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.user_agent_report', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
    }
    
    
    public function location_report(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->location_wise_report($data, 1000);
        
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.location_report', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
    }
    
    
    public function device_report(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->device_wise_report($data, 1000);
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.device_report', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
    }
    
    public function ip_report(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->ip_wise_report($data, 1000);
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.ip_report', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
    }
    
    public function platform_report(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->platform_wise_report($data, 1000);
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.platform_report', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
    }
    
    public function tracking_report(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        
        $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        $type = $data['type']?? 'count';
        
        
        $result = [];
        switch($type) {
            case 'count':
                $result = $model->count_list($data, 1000);
                break;
            case 'keyword':
                $result = $model->keyword_list($data, 1000);
                break;
            case 'browser':
                $result = $model->agent_report($data, 1000);
                break;
            case 'location':
                $result = $model->location_wise_report($data, 1000);
                break;
            case 'device':
                $result = $model->device_wise_report($data, 1000);
                break;
            case 'ip':
                $result = $model->ip_wise_report($data, 1000);
                break;
            case 'platform':
                $result = $model->platform_wise_report($data, 1000);
                break;
            
        }
//        dd($result);
        return view('trackingurl.tracking_report', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list, 'type' => $type]);
    }
}
