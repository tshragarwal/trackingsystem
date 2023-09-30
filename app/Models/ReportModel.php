<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportModel extends Model
{
    use HasFactory;
    protected $table = 'reports';
    
    public function reportList($requestData = [], $size = 10) {
        $model = new ReportModel();
        if(isset($requestData['subid']) && !empty($requestData['subid'])){
            $model = $model->where('subid', $requestData['subid']);
        }
        if(isset($requestData['start_date']) && !empty($requestData['start_date'])){
            $model =  $model->where('date','>=', $requestData['start_date']);
        }
        if(isset($requestData['end_date']) && !empty($requestData['end_date'])){
            $model =  $model->where('date', '<=', $requestData['end_date']);
        }
        
        $result = $model->orderBy('id', 'desc')->paginate($size);
        
        return $result;
    }
    
    
    public function downloadcsvdata($requestData = []) {
        $model = new ReportModel();
        if(isset($requestData['subid']) && !empty($requestData['subid'])){
            $model = $model->where('subid', $requestData['subid']);
        }
        if(isset($requestData['start_date']) && !empty($requestData['start_date'])){
            $model =  $model->where('date','>=', $requestData['start_date']);
        }
        if(isset($requestData['end_date']) && !empty($requestData['end_date'])){
            $model =  $model->where('date', '<=', $requestData['end_date']);
        }
        
        $result = $model->orderBy('id', 'desc')->get();
        
        return $result->toArray();
    }
     
     
}
