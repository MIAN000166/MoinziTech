<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Admin\CaseForward;
use App\Models\Hospital\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RadiologistController extends Controller
{
    public function del_radiologist($id){
        $user=User::where('id',$id)->first();
        $radiologist_case=CaseForward::where('radiologist_id',$id)->first();
        if ($radiologist_case){
            $errors=array(
                'errors'=>["This radiologist cannot be deleted as reports  are in process with this radiologist"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "failed",
            ];
            return response()->json($response, 404);
        }
        if ($user){
            $user->delete();
            $response = [
                'status' => true,
                'message' => "success",
            ];
            return response()->json($response, 200);
        }
        $errors=array(
            'errors'=>["invalid id"],
        );
        $response = [
            'status' => false,
            'errors'    => $errors,
            'message' => "failed",
        ];
        return response()->json($response, 404);
    }

    public function single_radiologist($id){
        $user=User::where('id',$id)->with('radiologist_details')->first();


        $response = [
            'status' => true,
            'data'=>$user,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function index(){
        $users=User::where('role_id',3)->with('radiologist_details')->get();
        $response = [
            'status' => true,
            'data'    => $users,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function change_radiologist_status(Request $request){

        $validator = Validator::make($request->all(), [
            "status" => ["required", "string", "in:active,inactive"],
            "user_id"=>"required|numeric|exists:users,id|between:1,99999999",

        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }


        $user=User::where('id',$request->user_id)->with('radiologist_details')->first();

        if ($user){
            $user->status=$request->status;
            $user->save();
            $response = [
                'status' => true,
                'message' => "success",
            ];
            return response()->json($response, 200);
        }
        $errors=array(
            'errors'=>["invalid id"],
        );
        $response = [
            'status' => false,
            'errors'    => $errors,
            'message' => "failed",
        ];
        return response()->json($response, 404);
    }

    public function get_checked_reports(Request $request){
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
        $reports=CaseForward::where('hospital_id',request()->user()->id)->get();
//        dd($reports);
        if ($start==$end){
            $reports=CaseForward::where('hospital_id',request()->user()->id)->with('forwardedCase','radiologists.radiologist_details')->wherehas('forwardedCase',function ($q) use ($start,$end){
                $q->whereNotNull('report')->where('created_at', $start);;
            }) ->orderBy('created_at', 'asc')->get();
            $response = [
                'status' => true,
                'data'=>$reports,
                'message' => "success",
            ];
            return response()->json($response, 200);
        }

        $reports=CaseForward::where('hospital_id',request()->user()->id)->with('forwardedCase','radiologists.radiologist_details')->wherehas('forwardedCase',function ($q) use ($start,$end){
            $q->whereNotNull('report')->whereBetween('created_at', [$start, $end]);;
        }) ->orderBy('created_at', 'asc')->get();
        $response = [
            'status' => true,
            'data'=>$reports,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
}
