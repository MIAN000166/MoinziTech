<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Admin\CaseForward;
use App\Models\Hospital\Report;
use App\Models\User;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function track_count(){
//        $total_users=User::where('role_id','!=',1)->count();
        $total_radio=User::where('role_id','=',3)->count();
//        $total_hos=User::where('role_id','=',2)->count();
        $total_reports=Report::where('hospital_id',request()->user()->id)->count();
        $total_approved_reports=Report::where('hospital_id',request()->user()->id)->where(['approval'=>"approved"])->count();
        $total_pending_reports=Report::where('hospital_id',request()->user()->id)->where(['approval'=>"pending"])->count();
        $total_complete_forwarded_case=CaseForward::where('hospital_id',request()->user()->id)->wherehas('forwardedCase',function ($q){
            $q->where('report','!=',null);
        })->count();
        $total_incomplete_forwarded_case=CaseForward::where('hospital_id',request()->user()->id)->wherehas('forwardedCase',function ($q){
            $q->where('report','=',null);
        })->count();
        $total_forwarded_case=CaseForward::where('hospital_id',request()->user()->id)->count();
        $data=array(
//            'total_users'=>$total_users,
            'total_radio'=>$total_radio,
//            'total_hospital'=>$total_hos,
            'total_reports'=>$total_reports,
            'total_pending_reports'=>$total_pending_reports,
            'total_approved_reports'=>$total_approved_reports,
            'total_incomplete_forwarded_case'=>$total_incomplete_forwarded_case,
            'total_complete_forwarded_case'=>$total_complete_forwarded_case,
            'total_forwarded_case'=>$total_forwarded_case,
            'total_checked_reports'=>$total_complete_forwarded_case,
            'total_unchecked_reports'=>$total_incomplete_forwarded_case,
        );
        $response=array(
            'status'=>true,
            'data'=>$data,
            'message'=>"Success"
        );
        return response()->json($response, 200);



    }
}
