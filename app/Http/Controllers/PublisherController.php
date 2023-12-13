<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddPublisherRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\CommonTrait;
use App\Http\Requests\UpdatePublisherRequest;
use App\Models\PublisherJobModel;

class PublisherController extends Controller
{
    
    public function form(){
       return view('publisher.form');
    }
    
    public function list(Request $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $requestData = $request->all();
        $userObj = new User();
        $publisherList = $userObj->get_publisher_list($requestData, 200);
        return view('publisher.list', ['data'=> $publisherList, 'filter' => $requestData]);
    }
    
    public function save(AddPublisherRequest $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        
        $requestData = $request->post();
        if(!empty($requestData) && !empty($requestData['name']) && !empty($requestData['email']) && !empty($requestData['password'])){
            
            $userExists = User::where('email', $requestData['email'])->get();
            if($userExists->count()){
                return redirect()->back()->with('error_status','Email already exists');
            }
            
            $user = new User();
            $user->name = $requestData['name'];
            $user->email = $requestData['email'];
            $user->password = Hash::make($requestData['password']);
            $user->user_type ='publisher';
            $user->save();
            
            return redirect()->back()->with('success_status','Publisher Details Successfully Added.');
       }  
    }
    
    public function publisher_detail(Request $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        if(!empty($request) && !empty($request->id) && is_numeric($request->id)){
            $modelObj =User::find($request->id);
          
            if(empty($modelObj)){
                return view('publisher.detail', ['error' => "Invalid Detail", 'data' => '']);
            }
           

            return view('publisher.detail', ['data' => $modelObj, 'error' => '']);
        }
    }
    
    public function publisher_update(UpdatePublisherRequest $request){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $requestData = $request->post();
      
        if(!empty($requestData) && !empty($requestData['id'])){
             $dbObj = User::find($requestData['id']);
             
//             dd($dbObj);
            
             if(!empty($dbObj)){
                if ($dbObj->user_type != 'publisher'){
                    return redirect()->back()->with('error_status','Sent details not matched with Publisher details');
                }
                 
                if (($dbObj->email != $requestData['email']) && User::where('email', $requestData['email'])->first()){
                    return redirect()->back()->with('error_status','Email Already Exists with other users');
                }
                 
                $dbObj->email = $requestData['email'];
                $dbObj->name = $requestData['name'];
                if(!empty($requestData['password'])){
                    $dbObj->password = Hash::make($requestData['password']);
                }
                $dbObj->updated_at = date('Y-m-d H:i:s');
                $dbObj->update();
                
                return redirect()->back()->with('success_status','Campaign data Updated Successfully');
                 
             }
        }
    }
    
    public function delete_publisher(Request $request){
        $requestData = $request->all();

        if(!empty($requestData['publisher_id'])){
            
            $record = User::find($requestData['publisher_id']);
            if ($record) {
                PublisherJobModel::where('publisher_id', $requestData['publisher_id'])->delete();
              
                $record->delete();
                $message = 'Publisher deleted successfully';
                
                return response()->json(['message' => $message, 'status' => 1]);
            } else {
                return response()->json(['message' => 'Publisher not found'], 404);
            }
        }
    }    
}
