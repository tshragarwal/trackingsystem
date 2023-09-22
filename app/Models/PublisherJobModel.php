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
    
    
    public function list($publisherId = 0, $size = 10){
       if($publisherId > 0){
           return static::where('publisher_id', $publisherId)->withCount('tracking')->with('publisher')->with('campaign')->orderBy('updated_at', 'desc')->paginate($size);
        }else{
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
}
