<?php

namespace App\Models\Admin;

use App\Models\Hospital\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseForward extends Model
{
    protected $guarded=[];
    use HasFactory;

    public function forwardedCase(){
        return $this->belongsTo(Report::class,'report_id','id');
    }

    public function radiologists(){
        return $this->belongsTo(User::class,'radiologist_id','id');
    }
    public function hospital(){
        return $this->belongsTo(User::class,'hospital_id','id');
    }



}
