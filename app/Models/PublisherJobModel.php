<?php

namespace App\Models;

use App\Models\User;
use App\Models\AdvertiserCampaignModel;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrackingPublisherJobModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    
    
    public function list($filter =[], $size = 10): LengthAwarePaginator{

        if(!empty($filter['publisher_id'])) {
            //->withCount('tracking')
            return static::where(['publisher_id' => $filter['publisher_id'], 'company_id' => $filter['company_id']])->with('publisher')->with('campaign')->orderBy('updated_at', 'desc')->paginate($size);
        }
    
        if(!empty($filter) && (!empty($filter['id']) || !empty($filter['pub_name']) || !empty($filter['adver_name']) || !empty($filter['campaign_name']))){
            $publisherJob = static::where('company_id', $filter['company_id'])->orderBy('updated_at', 'desc');

            if(isset($filter['id']) && $filter['id'] != 0 ){
                $publisherJob->where('id', $filter['id'])->with('publisher')->with('campaign');
            }
            if (isset($filter['pub_name']) && $filter['pub_name'] != '' ){
                    $publisherJob->whereHas('publisher', function ($query) use ($filter) {
                        $query->where('name', 'like', '%' . $filter['pub_name'] . '%');
                    })->with('campaign');
            }
            
            if (isset($filter['campaign_name']) && $filter['campaign_name'] != '' ){
                    $publisherJob->whereHas('campaign', function ($query) use ($filter) {
                        $query->where('campaign_name', 'like', '%' . $filter['campaign_name'] . '%');
                    })->with('publisher');
            }
            if (isset($filter['adver_name']) && $filter['adver_name'] != '' ){
                $publisherJob->whereHas('campaign.advertiser', function ($query) use ($filter) {
                        $query->where('name', 'like', '%' . $filter['adver_name'] . '%');
                    })->with('publisher');
            }
            return $publisherJob->withCount('tracking')->paginate($size);
        }
        
        return static::with(['publisher', 'campaign'])->where('company_id', $filter['company_id'])->orderBy('updated_at', 'desc')->paginate($size);
        
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
