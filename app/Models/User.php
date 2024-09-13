<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'user_type',
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
    
    public function scopePublisherList($query, $filter, $size = 1000): LengthAwarePaginator{
        $query = $query->where(['user_type' => 'publisher', 'company_id' => $filter['company_id']]);
        
        if(!empty($filter['type'])){
            if($filter['type'] =='id' && $filter['v'] !=0) {
                $query->where($filter['type'], $filter['v']);
            } else if ($filter['type'] =='name' && $filter['v'] !='') {
                $query->where($filter['type'], 'like','%'.$filter['v'].'%');
            }
        }

        return $query->orderby('id','desc')->paginate($size);
    }

    public function all_publisher_list(int $companyID){
        return self::select(['id', 'name'])->where(['user_type' => 'publisher', 'company_id' => $companyID])->get();
    }
}
