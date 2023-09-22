<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AdvertizerRequest;

class AdvertiserCampaignModel extends Model
{
    use HasFactory;
    protected $table = 'advertiser_campaigns';
//    protected $fillable = ['advertiser_id'];
    
    public function list(){
      
       return static::with('advertiser')->orderBy('updated_at', 'desc')->paginate(5);
    }
    
    
    public function advertiser(){
        return $this->belongsTo(AdvertizerRequest::class);
    }
    
    public function detail($id){
       return static::where('id', $id)->with('advertiser')->first();
    }    
    
    public function active_list(){
      
       return static::where('status', '!=', 3)->with('advertiser')->orderBy('updated_at', 'desc')->paginate(5);
    }
    
    public function get_advertiser_campaigns($advertiserId){
        return static::where('advertiser_id', $advertiserId)->orderBy('updated_at', 'desc')->get();
    }
    
    
}
