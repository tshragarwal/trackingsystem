<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\AdvertiserCampaignModel;
use App\Models\PublisherJobModel;
use App\Models\TrackingPublisherJobModel;

class PublisherJobModel extends Model
{
    use HasFactory;
    protected $table = 'publisher_jobs';
    
    
    public function list($filter =[], $publisherId = 0, $size = 10){
       if($publisherId > 0){
           return static::where('publisher_id', $publisherId)->withCount('tracking')->with('publisher')->with('campaign')->orderBy('updated_at', 'desc')->paginate($size);
        }else{
            if(!empty($filter) && !empty($filter['type'])){
                $publisherJob = static::orderBy('updated_at', 'desc');

                if($filter['type'] == 'id' && $filter['v'] != 0 ){
                    $publisherJob->where($filter['type'], $filter['v'])->with('publisher')->with('campaign');
                }else if ($filter['type'] == 'pub_name' && $filter['v'] != '' ){
                     $publisherJob->whereHas('publisher', function ($query) use ($filter) {
                            $query->where('name', 'like', '%' . $filter['v'] . '%');
                        })->with('campaign');
                }
                else if ( $filter['type'] =='campaign_name' && $filter['v'] != '' ){
                     $publisherJob->whereHas('campaign', function ($query) use ($filter) {
                            $query->where('campaign_name', 'like', '%' . $filter['v'] . '%');
                        })->with('publisher');
                }else if ($filter['type'] =='adver_name' && $filter['v'] != '' ){
                    $publisherJob->whereHas('campaign.advertiser', function ($query) use ($filter) {
                            $query->where('name', 'like', '%' . $filter['v'] . '%');
                        })->with('publisher');
                }
                return $publisherJob->withCount('tracking')->paginate($size);
            }
            
            return static::with('publisher')->withCount('tracking')->with('campaign')->orderBy('updated_at', 'desc')->paginate($size);
        }
    }
    
    
    public function publisher(){
        return $this->belongsTo(User::class, 'publisher_id');
    }
    public function campaign(){
        return $this->belongsTo(AdvertiserCampaignModel::class, 'advertiser_campaign_id');
    }
    

    public function get_record_of_proxy_url($proxy_url){
        return static::where('proxy_url', $proxy_url)->with('campaign')->first();
    }
    
    public function tracking()
    {
        return $this->hasMany(TrackingPublisherJobModel::class, 'publisher_job_id');
    }
    
    public function get_all_campaign_publisher_job_count($advertizer_campaign_id){
        return static::where('advertiser_campaign_id', $advertizer_campaign_id)->count();
    }
}
