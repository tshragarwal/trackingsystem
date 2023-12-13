<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertizerRequest extends Model
{
    use HasFactory;
    
    protected $table = 'advertisers';
    
    
    public function list(){
      
       return static::orderBy('updated_at', 'desc')->paginate(10);
    }

    public function detail($id){
       return static::where('id', $id)->first();
    }
    
    public function get_publisher_list($filter, $size = 10){
        if(!empty($filter) && !empty($filter['type']) && $filter['type'] =='id' && $filter['v'] !=0){
            return self::where($filter['type'], $filter['v'])->orderby('id','desc')->paginate($size);
        }else if (!empty($filter) && !empty($filter['type']) && $filter['type'] =='name' && $filter['v'] !=''){
            return self::where($filter['type'], 'like','%'.$filter['v'].'%')->orderby('id','desc')->paginate($size);
        }else{
            return self::orderby('id','desc')->paginate($size);
        }
    }    
    
//    public function update($data){
//        if(!empty($data)){
//            return static::update()->where('id', $data['id']);
//        }
//    }    
}
