<?php

namespace App\Models\Hospital;

use App\Models\Admin\CaseForward;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function hospital()
    {
        return $this->belongsTo(User::class);
    }

    public function forward_case(){
        return $this->hasMany(CaseForward::class,'report_id','id');
    }
}
