<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrackingKeywordModel;

class GeoLocationModel extends Model
{
    use HasFactory;
    
    protected $table = 'geo_location';
    
    
    // Profile model
    public function tracking() {
        return $this->belongsTo(TrackingKeywordModel::class, 'ip');
    }
}
