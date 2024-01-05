<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\AdvertiserCampaignModel;
use App\Models\AdvertizerRequest;
use Illuminate\Support\Facades\DB;

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
            if(isset($filter['publisher_id']) && !empty($filter['publisher_id'])){
                $trackingKeyword->where('publisher_id', $filter['publisher_id']);
            }
            if(isset($filter['subid']) && !empty($filter['subid'])){
                $trackingKeyword->where('subid', $filter['subid']);
            }
           
            if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
                $trackingKeyword->where('advertiser_id', $filter['advertiser_id']);
            }
            if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
                $trackingKeyword->where('advertiser_id', $filter['advertiser_id']);
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
        if(isset($filter['publisher_id']) && !empty($filter['publisher_id'])){
            $trackingKeyword->where('publisher_id', $filter['publisher_id']);
        }
        if(isset($filter['subid']) && !empty($filter['subid'])){
            $trackingKeyword->where('subid', $filter['subid']);
        }

        if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
            $trackingKeyword->where('advertiser_id', $filter['advertiser_id']);
        }
        if(isset($filter['advertiser_id']) && !empty($filter['advertiser_id'])){
            $trackingKeyword->where('advertiser_id', $filter['advertiser_id']);
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
    
    
}
