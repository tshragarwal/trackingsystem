<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingPublisherJobModel extends Model
{
    use HasFactory;
    protected $table = 'tracking_publisher_jobs';
    public $timestamps = false;
    
}
