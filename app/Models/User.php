<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Admin\CaseForward;
use App\Models\Hospital\Report;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded=[];
//    protected $fillable = [
//        'name',
//        'email',
//        'password',
//        'role_id',
//        'slug',
//        'username',
//        'first_name',
//        'last_name',
//        'email_opt',
//    ];

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
        'password' => 'hashed',
    ];
    public function hospital_details(){
        return $this->hasOne(HospitalProfile::class,'user_id','id');
    }
    public function radiologist_details(){
        return $this->hasOne(RadiologistProfile::class,'user_id','id');
    }

    public function case(){
        return $this->hasMany(CaseForward::class,'radiologist_id','id');
    }
    public function hospital_case(){
        return $this->hasMany(CaseForward::class,'hospital_id','id');
    }
    public function report_details(){
        return $this->hasMany(Report::class,'hospital_id','id');
    }
}
