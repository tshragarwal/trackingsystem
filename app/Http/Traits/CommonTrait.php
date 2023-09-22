<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\Auth;

trait CommonTrait {
    
    public static function is_super_admin(){
        $webGaud = Auth::guard('web')->check();
        if($webGaud){
            if( Auth::guard('web')->user()->user_type != "admin"){
                return false;
            }
        }else{
            return false;
        }
        return true;
    }
}