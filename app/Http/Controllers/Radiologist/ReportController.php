<?php

namespace App\Http\Controllers\Radiologist;

use App\Http\Controllers\Controller;
use App\Models\Admin\CaseForward;
use App\Models\Hospital\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
   public function index(Request $request){
//       $xRayReports = Report::where(['forwarded'=>false,'approval'=>'approved'])->with('hospital')->get();
////
////
////       $caseForwards = CaseForward::where('radiologist_id', $request->user()->id)->with('forwardedCase')->with('hospital')->get();
       $data=Report::where(['forwarded'=>true,'approval'=>'approved','report'=>null])->with('hospital.hospital_details')->wherehas('forward_case', function($q){
           $q->where(['radiologist_id'=> request()->user()->id]);
       })->orderBy('created_at', 'asc')->get();
//$allReports=CaseForward::where('radiologist_id',$request->user()->id)->with('forwardedCase','hospital')->get();

       $response=array(
           'status'=>true,
           'data'=>$data,
           'message'=>"Success"
       );
       return response()->json($response, 200);
   }

    public function update_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>"required|numeric|exists:reports,id",
            "report"=>"required|string|max:4294967"
        ]);
        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }

        $report =  Report::where('id',$request->id)->first();

        $report->report=$request->report;

        $report->save();
        $response=array(
            'status'=>true,
            'data'=>$report,
            'message'=>"Success"
        );
        return response()->json($response, 200);

    }
    public function get_report($id){
     $report=Report::where('id',$id)->with('hospital.hospital_details')->first();
        $response=array(
            'status'=>true,
            'data'=>$report,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }
    public function completed_reports(){
//        $caseForwards = CaseForward::where('radiologist_id', request()->user()->id)->with('forwardedCase')->with('hospital')->get();
       $data=Report::where(['forwarded'=>true,'approval'=>'approved'])->where('report','!=',null)->with('hospital.hospital_details')->wherehas('forward_case', function($q){
           $q->where('radiologist_id', request()->user()->id);
       })->orderBy('created_at', 'asc')->get();
        $response=array(
            'status'=>true,
            'data'=>$data,
            'message'=>"Success"
        );
        return response()->json($response, 200);

    }
    public function pending_reports(){
        $data=Report::where(['forwarded'=>true,'approval'=>'approved','report'=>null])->with('hospital.hospital_details')->wherehas('forward_case', function($q){
            $q->where('radiologist_id', request()->user()->id);
        })->orderBy('created_at', 'asc')->get();
        $response=array(
            'status'=>true,
            'data'=>$data,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }
    public function track(){
        //        $total_users=User::where('role_id','!=',1)->count();
//        $total_radio=User::where('role_id','=',3)->count();
//        $total_hos=User::where('role_id','=',2)->count();
//        $total_reports=CaseForward::where('radiologist_id',request()->user()->id)->count();
//        $total_approved_reports=Report::where('hospital_id',request()->user()->id)->where(['approval'=>"approved"])->count();
//        $total_pending_reports=Report::where('hospital_id',request()->user()->id)->where(['approval'=>"pending"])->count();
        $total_complete_forwarded_case=CaseForward::where('radiologist_id',request()->user()->id)->wherehas('forwardedCase',function ($q){
            $q->where('report','!=',null);
        })->count();
        $total_incomplete_forwarded_case=CaseForward::where('radiologist_id',request()->user()->id)->wherehas('forwardedCase',function ($q){
            $q->where('report','=',null);
        })->count();
        $total_forwarded_case=CaseForward::where('radiologist_id',request()->user()->id)->count();
        $data=array(

//            'total_radio'=>$total_radio,


            'total_incomplete_forwarded_case'=>$total_incomplete_forwarded_case,
            'total_complete_forwarded_case'=>$total_complete_forwarded_case,
            'total_forwarded_case'=>$total_forwarded_case,
//            'total_checked_reports'=>$total_complete_forwarded_case,
//            'total_unchecked_reports'=>$total_incomplete_forwarded_case,
        );
        $response=array(
            'status'=>true,
            'data'=>$data,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }
    public function filter_reports(Request $request){
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
            $data=Report::where(['forwarded'=>true,'approval'=>'approved'])->where('report','!=',null)->with('hospital.hospital_details')->wherehas('forward_case', function($q){
                $q->where('radiologist_id', request()->user()->id);
            })->where('created_at', $start)->orderBy('created_at', 'asc')->get();
            $response=array(
                'status'=>true,
                'data'=>$data,
                'message'=>"Success"
            );
            return response()->json($response, 200);
        }

        $data=Report::where(['forwarded'=>true,'approval'=>'approved'])->where('report','!=',null)->with('hospital.hospital_details')->wherehas('forward_case', function($q){
            $q->where('radiologist_id', request()->user()->id);
        })->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'asc')->get();
        $response=array(
            'status'=>true,
            'data'=>$data,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }
}
