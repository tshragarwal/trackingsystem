<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\AdvertiserCampaignModel;
use App\Models\AdvertizerRequest;
use Illuminate\Support\Facades\DB;
use App\Models\GeoLocationModel;

class TrackingKeywordModel extends Model
{
    use HasFactory;
    
    protected $table = 'tracking_publisher_jobs';
    public $timestamps = false;
    
    
    public function keyword_list($filter =[], $size = 100){
            
            
            $trackingKeyword = static::select('id', 'publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'keyword', 'date', DB::raw('count(*) as total_count'));

            if(empty($filter['start_date'])){
                $trackingKeyword->where('date', date('Y-m-d'));
            }else if(!empty(empty($filter['start_date'])) && !empty(empty($filter['end_date']))){
                $trackingKeyword->where('date', date('Y-m-d'));
            }
            
            if(isset($filter['start_date']) && !empty($filter['start_date'])){
                $trackingKeyword->where('date','>=', $filter['start_date']);
            }
            if(isset($filter['end_date']) && !empty($filter['end_date'])){
                $trackingKeyword->where('date', '<=', $filter['end_date']);
            }
            
            if(isset($filter['offer_id']) && !empty($filter['offer_id'])){
                $trackingKeyword->where('publisher_job_id', $filter['offer_id']);
            }
            if(isset($filter['publishers_id']) && !empty($filter['publishers_id'])){
                $trackingKeyword->whereIn('publisher_id', explode(",", $filter['publishers_id']));
            }
            if(isset($filter['subid']) && !empty($filter['subid'])){
                $trackingKeyword->where('subid', $filter['subid']);
            }
            if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
                $trackingKeyword->whereIn('advertiser_id',  explode(",",$filter['advertiser_id']));
            }
            
            
            return $trackingKeyword->groupBy('publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'keyword')
                    ->with('publisher')->with('advertiser')->with('campaign')
                    ->orderBy('date', 'desc')->paginate($size);
    }
    
    public function count_list($filter =[], $size = 100){
            
        $trackingKeyword = static::select('id', 'publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'date', DB::raw('count(*) as total_count'));

        if(empty($filter['start_date'])){
            $trackingKeyword->where('date', date('Y-m-d'));
        }else if(!empty(empty($filter['start_date'])) && !empty(empty($filter['end_date']))){
            $trackingKeyword->where('date', date('Y-m-d'));
        }

        if(isset($filter['start_date']) && !empty($filter['start_date'])){
            $trackingKeyword->where('date','>=', $filter['start_date']);
        }
        if(isset($filter['end_date']) && !empty($filter['end_date'])){
            $trackingKeyword->where('date', '<=', $filter['end_date']);
        }

        if(isset($filter['publisher_job_id']) && !empty($filter['publisher_job_id'])){
            $trackingKeyword->where('publisher_job_id', $filter['publisher_job_id']);
        }
        if(isset($filter['publishers_id']) && !empty($filter['publishers_id'])){
            $trackingKeyword->whereIn('publisher_id', explode(",",$filter['publishers_id']));
        }
        if(isset($filter['subid']) && !empty($filter['subid'])){
            $trackingKeyword->where('subid', $filter['subid']);
        }

        if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
            $trackingKeyword->whereIn('advertiser_id', explode(",",$filter['advertiser_id']));
        }
     

        return $trackingKeyword->groupBy('publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid')
                ->with('publisher')->with('advertiser')->with('campaign')
                ->orderBy('date', 'desc')->paginate($size);


//        return static::with('publisher')->withCount('tracking')->with('campaign')->orderBy('updated_at', 'desc')->paginate($size);
    }
    
    public function publisher(){
        return $this->belongsTo(User::class, 'publisher_id');
    }
    public function advertiser(){
        return $this->belongsTo(AdvertizerRequest::class, 'advertiser_id');
    }
    public function campaign(){
        return $this->belongsTo(AdvertiserCampaignModel::class, 'campaign_id');
    }

    public function geoLocation()
    {
        return $this->belongsTo(GeoLocationModel::class, 'ip', 'ip');
    }

    
    
    public function agent_report($filter =[], $size = 100){
            $trackingKeyword = static::select('id', 'publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'browser', 'date', DB::raw('count(*) as total_count'));

            if(empty($filter['start_date'])){
                $trackingKeyword->where('date', date('Y-m-d'));
            }else if(!empty(empty($filter['start_date'])) && !empty(empty($filter['end_date']))){
                $trackingKeyword->where('date', date('Y-m-d'));
            }
            
            if(isset($filter['start_date']) && !empty($filter['start_date'])){
                $trackingKeyword->where('date','>=', $filter['start_date']);
            }
            if(isset($filter['end_date']) && !empty($filter['end_date'])){
                $trackingKeyword->where('date', '<=', $filter['end_date']);
            }
            
            if(isset($filter['job_id']) && !empty($filter['job_id'])){
                $trackingKeyword->where('publisher_job_id', $filter['job_id']);
            }
            if(isset($filter['publishers_id']) && !empty($filter['publishers_id'])){
                $trackingKeyword->whereIn('publisher_id', explode(",", $filter['publishers_id']));
            }
            if(isset($filter['subid']) && !empty($filter['subid'])){
                $trackingKeyword->where('subid', $filter['subid']);
            }
            if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
                $trackingKeyword->whereIn('advertiser_id',  explode(",",$filter['advertiser_id']));
            }
            
            
            return $trackingKeyword->groupBy('publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'browser')
                    ->with('publisher')->with('advertiser')->with('campaign')
                    ->orderBy('date', 'desc')->paginate($size);
    }    

    
     
    public function location_wise_report($filter =[], $size = 100){

        /*SELECT
            publisher_id,
            campaign_id,
            advertiser_id,
            geo_location.city AS city,
            COUNT(*) AS city_count
        FROM
            tracking_publisher_jobs
        JOIN
            geo_location ON tracking_publisher_jobs.ip = geo_location.ip
        GROUP BY
            publisher_id,
            campaign_id,
            advertiser_id,
            geo_location.city;
         */
        
        $trackingKeyword = TrackingKeywordModel::join('geo_location', 'tracking_publisher_jobs.ip', '=', 'geo_location.ip')
                    ->select('tracking_publisher_jobs.id as id', 'tracking_publisher_jobs.publisher_job_id as publisher_job_id', 'tracking_publisher_jobs.publisher_id as publisher_id', 'tracking_publisher_jobs.campaign_id as campaign_id', 'tracking_publisher_jobs.advertiser_id as advertiser_id', 'tracking_publisher_jobs.subid as subid', 'tracking_publisher_jobs.date as date','geo_location.city as city','geo_location.country as country', DB::raw('count(geo_location.ip) as total_count'));
        

            if(empty($filter['start_date'])){
                $trackingKeyword->where('date', date('Y-m-d'));
            }else if(!empty(empty($filter['start_date'])) && !empty(empty($filter['end_date']))){
                $trackingKeyword->where('date', date('Y-m-d'));
            }
            
            if(isset($filter['start_date']) && !empty($filter['start_date'])){
                $trackingKeyword->where('date','>=', $filter['start_date']);
            }
            if(isset($filter['end_date']) && !empty($filter['end_date'])){
                $trackingKeyword->where('date', '<=', $filter['end_date']);
            }
            
            if(isset($filter['job_id']) && !empty($filter['job_id'])){
                $trackingKeyword->where('publisher_job_id', $filter['job_id']);
            }
            if(isset($filter['publishers_id']) && !empty($filter['publishers_id'])){
                $trackingKeyword->whereIn('publisher_id', explode(",", $filter['publishers_id']));
            }
            if(isset($filter['subid']) && !empty($filter['subid'])){
                $trackingKeyword->where('subid', $filter['subid']);
            }
            if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
                $trackingKeyword->whereIn('advertiser_id',  explode(",",$filter['advertiser_id']));
            }
            $trackingKeyword->where('geo_location_updated',  1);
            
            
            
        $result =  $trackingKeyword->groupBy('tracking_publisher_jobs.publisher_job_id', 'tracking_publisher_jobs.publisher_id', 'tracking_publisher_jobs.campaign_id', 'tracking_publisher_jobs.advertiser_id', 'tracking_publisher_jobs.subid', 'geo_location.city')
                    ->with('publisher')->with('advertiser')->with('campaign')
                    ->orderBy('date', 'desc')->paginate($size);
//        dd($result);
        return $result;
    }    
    
    public function device_wise_report($filter =[], $size = 100){
            $trackingKeyword = static::select('id', 'publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'device', 'date', DB::raw('count(*) as total_count'));

            if(empty($filter['start_date'])){
                $trackingKeyword->where('date', date('Y-m-d'));
            }else if(!empty(empty($filter['start_date'])) && !empty(empty($filter['end_date']))){
                $trackingKeyword->where('date', date('Y-m-d'));
            }
            
            if(isset($filter['start_date']) && !empty($filter['start_date'])){
                $trackingKeyword->where('date','>=', $filter['start_date']);
            }
            if(isset($filter['end_date']) && !empty($filter['end_date'])){
                $trackingKeyword->where('date', '<=', $filter['end_date']);
            }
            
            if(isset($filter['job_id']) && !empty($filter['job_id'])){
                $trackingKeyword->where('publisher_job_id', $filter['job_id']);
            }
            if(isset($filter['publishers_id']) && !empty($filter['publishers_id'])){
                $trackingKeyword->whereIn('publisher_id', explode(",", $filter['publishers_id']));
            }
            if(isset($filter['subid']) && !empty($filter['subid'])){
                $trackingKeyword->where('subid', $filter['subid']);
            }
            if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
                $trackingKeyword->whereIn('advertiser_id',  explode(",",$filter['advertiser_id']));
            }
            
            
            return $trackingKeyword->groupBy('publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'device')
                    ->with('publisher')->with('advertiser')->with('campaign')
                    ->orderBy('date', 'desc')->paginate($size);
    }    
    
    public function ip_wise_report($filter =[], $size = 100){
            $trackingKeyword = static::select('id', 'publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'ip', 'date', DB::raw('count(*) as total_count'));

            if(empty($filter['start_date'])){
                $trackingKeyword->where('date', date('Y-m-d'));
            }else if(!empty(empty($filter['start_date'])) && !empty(empty($filter['end_date']))){
                $trackingKeyword->where('date', date('Y-m-d'));
            }
            
            if(isset($filter['start_date']) && !empty($filter['start_date'])){
                $trackingKeyword->where('date','>=', $filter['start_date']);
            }
            if(isset($filter['end_date']) && !empty($filter['end_date'])){
                $trackingKeyword->where('date', '<=', $filter['end_date']);
            }
            
            if(isset($filter['job_id']) && !empty($filter['job_id'])){
                $trackingKeyword->where('publisher_job_id', $filter['job_id']);
            }
            if(isset($filter['publishers_id']) && !empty($filter['publishers_id'])){
                $trackingKeyword->whereIn('publisher_id', explode(",", $filter['publishers_id']));
            }
            if(isset($filter['subid']) && !empty($filter['subid'])){
                $trackingKeyword->where('subid', $filter['subid']);
            }
            if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
                $trackingKeyword->whereIn('advertiser_id',  explode(",",$filter['advertiser_id']));
            }
            
            
            return $trackingKeyword->groupBy('publisher_job_id', 'publisher_id', 'campaign_id', 'advertiser_id', 'subid', 'ip')
                    ->with('publisher')->with('advertiser')->with('campaign')
                    ->orderBy('date', 'desc')->paginate($size);
    }    
}
