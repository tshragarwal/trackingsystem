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
    
    public static function n2s_csv_mapping_header(){
        return [
                'Date' => 'date', 'Country'=> 'country', 'Advertizer Subid' => 'subid', 'Searches' => 'total_searches', 'Clicks' => 'ad_clicks','CTR' => 'ctr', 'Net Revenue' => 'revenue',
                'Advertizer Name' => 'advertiser_name', 'Campaign Name' => 'campaign_name', 'Campaign id' => 'campaign_id', 'TQ' => 'tq', 'Publisher RPM' => 'publisher_RPM', 
                'Publisher RPC' => 'publisher_RPC', 'Advertizer RPM' => 'advertiser_RPM', 'Advertizer CPC' => 'advertiser_CPC', 'Gross Revenue' => 'gross_revenue', 
                'Publisher name' => 'publisher_name', 'Publisher Id' => 'publisher_id', 'Offer Id' => 'offer_id'
            ];
    }
    
    public static function typein_csv_mapping_header(){
        return [
                'Date' => 'date', 'Country'=> 'country', 'Advertizer Name' => 'advertiser_name', 'Campaign Name' => 'campaign_name', 'Advertizer Subid' => 'subid', 'Campaign id' => 'campaign_id',  
                'Total Searches' => 'total_searches', 'Monetized Searches' => 'monetized_searches' ,'Ad Clicks' => 'ad_clicks','Ad Coverage' => 'ad_coverage', 'CTR' => 'ctr','CPC' => 'cpc', 
                'RPM' => 'rpm', 'Gross Revenue' => 'gross_revenue', 'Publisher name' => 'publisher_name', 'Publisher Id' => 'publisher_id', 'Offer Id' => 'offer_id',
                'Publisher RPM' => 'publisher_RPM', 'Publisher RPC' => 'publisher_RPC', 'Net Revenue' => 'net_revenue',
            ];
    }
}