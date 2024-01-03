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
    public function list(Request $request){
        $data = $request->all();
        $model = new TrackingKeywordModel();
        $result = $model->list($data, 1000);
        
        
         $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        
        return view('trackingurl.keywordlist', ['data' => $result, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list,]);
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
}
