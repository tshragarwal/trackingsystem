<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingPublisherJobModel extends Model
{
    use HasFactory;
    protected $table = 'tracking_publisher_jobs';
    public $timestamps = false;
    
    //geo_location_updated
    public function get_non_sync_geo_location($campaign_id){
        return static::select('id', 'ip')->where('campaign_id', $campaign_id)->where('geo_location_updated', 0)->get();
    }
}
