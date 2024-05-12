<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportN2sModel extends Model
{
    use HasFactory;
    protected $table = 'report_n2s';

    protected $guarded = [];
    
    public function scopeReportList($query, $requestData = [], $size = 10) {
        $query = $query->where(['company_id' => $requestData['company_id']]);

        if(isset($requestData['subid']) && !empty($requestData['subid'])){
            $query = $query->where('subid', $requestData['subid']);
        }
        if(isset($requestData['start_date']) && !empty($requestData['start_date'])){
            $query =  $query->where('date','>=', $requestData['start_date']);
        }
        if(isset($requestData['end_date']) && !empty($requestData['end_date'])){
            $query =  $query->where('date', '<=', $requestData['end_date']);
        }
        if(isset($requestData['end_date']) && !empty($requestData['end_date'])){
            $query =  $query->where('date', '<=', $requestData['end_date']);
        }
        if(!empty($requestData['publishers_id'])) {
            $query =  $query->whereIn('publisher_id', $requestData['publishers_id']);
        }
        if(!empty($requestData['advertizers_name'])) {
            $query =  $query->whereIn('advertiser_name', $requestData['advertizers_name']);
        }
        if(!empty($requestData['country'])) {
            $query =  $query->where('country', $requestData['country']);
        }
      
        return $query->orderBy('id', 'desc')->paginate($size);
    }
    
    
    public function scopeDownloadcsvdata($query, array $requestData = []) {
        $model = $query->where('company_id', $requestData['company_id']);
        
        if(isset($requestData['subid']) && !empty($requestData['subid'])){
            $model = $model->where('subid', $requestData['subid']);
        }
        if(isset($requestData['start_date']) && !empty($requestData['start_date'])){
            $model =  $model->where('date','>=', $requestData['start_date']);
        }
        if(isset($requestData['end_date']) && !empty($requestData['end_date'])){
            $model =  $model->where('date', '<=', $requestData['end_date']);
        }
        if(!empty($requestData['publishers_id'])) {
            $model =  $model->whereIn('publisher_id', $requestData['publishers_id']);
        }
        
        if(!empty($requestData['advertizers_name'])) {
            $model =  $model->whereIn('advertiser_name', $requestData['advertizers_name']);
        }    
        
        if(!empty($requestData['country'])) {
            $model =  $model->where('country', $requestData['country']);
        }        
        
        $result = $model->orderBy('id', 'desc')->get();
        
        return $result->toArray();
    }
     
     
}
