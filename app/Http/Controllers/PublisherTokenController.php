<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\PublisherTokenDataRequest;

class PublisherTokenController extends Controller
{
    public function publisher_token_list(){
        $user = Auth::guard('web')->user();
        if($user->user_type == 'admin'){
             $user = false;
        }
        return view('publisher_token.token_list', ['user' => $user]);
    } 
    
    public function publisher_token_generate(){
        $user = Auth::guard('web')->user();
      
        if($user->user_type == 'publisher'){
            do {
                $api_token = $this->token();
                $existsToken = User::where('api_token', $api_token)->first();
            }while(!empty($existsToken));

            $user->api_token = $api_token;
            $user->save();
            
            return redirect()->back()->with(['success_status' => 'Successfully Generated Publisher Api Token']);
            
        }
    }
    
    private function token(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz01abhdyk23456789';
        $randomString = '';

        for ($i = 0; $i < 4; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
    
    public function publisher_token_data(PublisherTokenDataRequest $request){
        $requestData= $request->all();
    }
}
