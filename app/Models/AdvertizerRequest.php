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
    
    
//    public function update($data){
//        if(!empty($data)){
//            return static::update()->where('id', $data['id']);
//        }
//    }    
}
