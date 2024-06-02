<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    use HasFactory;
    
    protected $table = 'advertisers';
    protected $guarded = [];
    
    
    public function list(){
      
       return static::orderBy('updated_at', 'desc')->paginate(10);
    }

    public function detail($id){
       return static::where('id', $id)->first();
    }
    
    public function get_publisher_list($filter, $size = 10){
        $query = self::where('company_id', $filter['companyID']);
        if(!empty($filter) && !empty($filter['type']) && $filter['type'] =='id' && $filter['v'] !=0){
            $query->where($filter['type'], $filter['v']);
        }else if (!empty($filter) && !empty($filter['type']) && $filter['type'] =='name' && $filter['v'] !=''){
            $query->where($filter['type'], 'like','%'.$filter['v'].'%');
        }
            
        return $query->orderby('id','desc')->paginate($size);
    }    
    
//    public function update($data){
//        if(!empty($data)){
//            return static::update()->where('id', $data['id']);
//        }
//    }    
}
