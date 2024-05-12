<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\AdvertiserCampaignModel;
use App\Models\TrackingPublisherJobModel;

class PublisherJobModel extends Model
{
    use HasFactory;
    protected $table = 'publisher_jobs';
    protected $gaurded = [];
    protected $fillable = [
        'company_id',
        'publisher_id',
        'advertiser_campaign_id',
        'proxy_url',
        'target_count'
    ];
    
    
    public function list($filter =[], $size = 10){

        if(!empty($filter['publisher_id'])) {
            return static::where(['publisher_id' => $filter['publisher_id'], 'company_id' => $filter['company_id']])->withCount('tracking')->with('publisher')->with('campaign')->orderBy('updated_at', 'desc')->paginate($size);
        }
    
        if(!empty($filter) && (!empty($filter['id']) || !empty($filter['pub_name']) || !empty($filter['adver_name']) || !empty($filter['campaign_name']))){
            $publisherJob = static::where('company_id', $filter['company_id'])->orderBy('updated_at', 'desc');

            if($filter['id'] != 0 ){
                $publisherJob->where('id', $filter['id'])->with('publisher')->with('campaign');
            }
            if ($filter['pub_name'] != '' ){
                    $publisherJob->whereHas('publisher', function ($query) use ($filter) {
                        $query->where('name', 'like', '%' . $filter['pub_name'] . '%');
                    })->with('campaign');
            }
            
            if ( $filter['campaign_name'] != '' ){
                    $publisherJob->whereHas('campaign', function ($query) use ($filter) {
                        $query->where('campaign_name', 'like', '%' . $filter['campaign_name'] . '%');
                    })->with('publisher');
            }
            if ($filter['adver_name'] != '' ){
                $publisherJob->whereHas('campaign.advertiser', function ($query) use ($filter) {
                        $query->where('name', 'like', '%' . $filter['adver_name'] . '%');
                    })->with('publisher');
            }
            return $publisherJob->withCount('tracking')->paginate($size);
        }
        
        return static::with(['publisher', 'campaign'])->withCount('tracking')->where('company_id', $filter['company_id'])->orderBy('updated_at', 'desc')->paginate($size);
        
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
    public function get_campaign_publisherlist($advertizer_campaign_id){
        return static::where('advertiser_campaign_id', $advertizer_campaign_id)->with('publisher')->get();
    }
}
