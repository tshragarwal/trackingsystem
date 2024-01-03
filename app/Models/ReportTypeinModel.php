<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTypeinModel extends Model
{
   use HasFactory;
    protected $table = 'report_typein';
    
    public function reportList($requestData = [], $size = 10) {
        $model = new ReportTypeinModel();
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
        
        $result = $model->orderBy('id', 'desc')->paginate($size);
        return $result;
    }
    
    
    
    public function downloadcsvdata($requestData = []) {
        $model = new ReportTypeinModel();
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