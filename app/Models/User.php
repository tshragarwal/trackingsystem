<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function get_publisher_list($filter, $size = 10){
         $obj = self::where('user_type','publisher');
        
        if(!empty($filter) && !empty($filter['type']) && $filter['type'] =='id' && $filter['v'] !=0){
             $obj->where($filter['type'], $filter['v']);
        }else if (!empty($filter) && !empty($filter['type']) && $filter['type'] =='name' && $filter['v'] !=''){
             $obj->where($filter['type'], 'like','%'.$filter['v'].'%');
        }
        
        return $obj->orderby('id','desc')->paginate($size);
        
    }
    public function all_publisher_list(){
        return self::select(['id', 'name'])->where('user_type','publisher')->get();
    }
}
