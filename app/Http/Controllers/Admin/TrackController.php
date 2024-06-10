<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CaseForward;
use App\Models\AutoAssign;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Hospital\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrackController extends Controller
{

    public function tracking(Request $request){
        $validator = Validator::make($request->all(), [

            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',

        ]);



        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $start = date('Y-m-d H:i:s', strtotime($request->start_date));
        $end = date('Y-m-d H:i:s', strtotime($request->end_date));
        if ($start==$end){
//            $rad_reports = User::where('role_id', 3)->with('case.forwardedCase', function ($query) use ($start, $end) {
//                $query->whereNotNull('report')->where('created_at', $start);
//            })->get();
//
            $rad_reports=Report::whereNotNull('report')->where('created_at', $start)->with('hospital')->with('forward_case.radiologists')->whereHas('forward_case.radiologists',function ($query) use ($start, $end) {
                $query->where('role_id',3);


            })->get();
        }else{

            $rad_reports=Report::whereNotNull('report')->whereBetween('created_at', [$start, $end])->with('hospital')->with('forward_case.radiologists')->whereHas('forward_case.radiologists',function ($query) use ($start, $end) {
                $query->where('role_id',3);
            })->get();




//            $rad_reports = User::where('role_id', 3)->with('case.forwardedCase', function ($query) use ($start, $end) {
//                $query->whereNotNull('report')->whereBetween('created_at', [$start, $end]);
//            })->get();

        }

        $response=array(
            'status'=>true,
            'data'=>$rad_reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);


    }

    public function hospital_tracking(Request $request){

        $validator = Validator::make($request->all(), [

            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',

        ]);



        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];

//abc
            return response()->json($response, 404);
        }

        $start = date('Y-m-d H:i:s', strtotime($request->start_date));
        $end = date('Y-m-d H:i:s', strtotime($request->end_date));
        if ($start==$end){
//            $hos_reports = User::where('role_id', 2)->with('report_details', function ($query) use ($start, $end) {
//                $query->whereNotNull('report')->where('created_at', $start);
//            })->get();
            $hos_reports = Report::whereNotNull('report')->where('created_at', $start)->with('hospital')->with('forward_case.radiologists')->get();
        }else{
//            $hos_reports = User::where('role_id', 2)->with('report_details', function ($query) use ($start, $end) {
//                $query->whereNotNull('report')->whereBetween('created_at', [$start, $end]);
//            })->get();
            $hos_reports = Report::whereNotNull('report')->whereBetween('created_at', [$start, $end])->with('hospital')->with('forward_case.radiologists')->get();


        }



//        $radiologists = User::where('role_id', 2)
//            ->withCount(['report_details as total_checked_reports' => function ($query) use ($start, $end) {
//
//                    $query->whereNotNull('report')
//                        ->whereBetween('created_at', [$start, $end]);
//
//            }])
//            ->withCount(['report_details as total_assigned_reports' => function ($query) use ($start, $end) {
//
//                $query->where('forwarded',1)
//                    ->whereBetween('created_at', [$start, $end]);
//
//            }])
//            ->withCount(['report_details as total_unassigned_reports' => function ($query) use ($start, $end) {
//
//                $query->where('forwarded',0)
//                    ->whereBetween('created_at', [$start, $end]);
//
//            }])
//            ->withCount(['report_details as total_unchecked_reports' => function ($query) use ($start, $end) {
//
//                    $query->whereNull('report')
//                        ->whereBetween('created_at', [$start, $end]);
//
//            }])
//            ->get();
//        $radiologists->each(function ($radiologist) {
//            $radiologist->total_reports = $radiologist->total_checked_reports + $radiologist->total_unchecked_reports;
//        });


        $response=array(
            'status'=>true,
            'data'=>$hos_reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }
    public function track_count(){
        $total_users=User::where('role_id','!=',1)->count();
        $total_radio=User::where('role_id','=',3)->count();
        $total_hos=User::where('role_id','=',2)->count();
        $total_reports=Report::count();
        $total_approved_reports=Report::where(['approval'=>"approved"])->count();
        $total_pending_reports=Report::where(['approval'=>"pending"])->count();
        $total_complete_forwarded_case=CaseForward::where('user_id',request()->user()->id)->wherehas('forwardedCase',function ($q){
            $q->where('report','!=',null);
        })->count();
        $total_incomplete_forwarded_case=CaseForward::where('user_id',request()->user()->id)->wherehas('forwardedCase',function ($q){
            $q->where('report','=',null);
        })->count();
        $total_forwarded_case=CaseForward::where('user_id',request()->user()->id)->count();
        $data=array(
            'total_users'=>$total_users,
            'total_radio'=>$total_radio,
            'total_hospital'=>$total_hos,
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
    public function toggle_status(){
        $status=AutoAssign::first();
        $response=array(
            'status'=>true,
            'data'=>$status,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }



}
