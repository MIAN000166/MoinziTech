<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CaseForward;
use App\Models\AutoAssign;
use App\Models\Hospital\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
  public function index(){
      $reports = Report::with('hospital.hospital_details')->orderBy('created_at', 'asc')->get();
      $response=array(
          'status'=>true,
          'data'=>$reports,
          'message'=>"Success"
      );
      return response()->json($response, 200);
  }
  public function update_status(Request $request){
      $validator = Validator::make($request->all(), [
          'approval' => 'required|in:pending,approved',
//          'forwarded' => 'required|boolean',
          'id'=>"required|numeric|exists:reports,id",
//          'radiologist_id' => Rule::when($request->approval=="approved" && $request->forwarded==true ,'required|numeric|exists:users,id'),
      ]);
      if ($validator->fails()) {

          $response = [
              'status' => false,
              'errors' => $validator->errors(),
              'message' => "Validation Fails",
          ];


          return response()->json($response, 404);
      }
      try {
          $report=Report::where('id',$request->id)->first();
//          $report->forwarded=$request->forwarded;
          $report->approval=$request->approval;
          $report->save();
//          if ($request->approval=="pending" && $request->forwarded==true){
//              $response = [
//                  'status' => false,
//                  'message' => "In order to forward the case report status should be approved",
//              ];
//
//
//              return response()->json($response, 404);
//          }
//
//          if ($request->approval=="approved" && $request->forwarded==true ){
//
//              $case=CaseForward::where('report_id',$request->id)->first();
//              if ($case){
//                  $case->delete();
//
//              }
//              $assign_report=new CaseForward();
//              $assign_report->report_id=$request->id;
//              $assign_report->radiologist_id=$request->radiologist_id;
//              $assign_report->save();
//          }


          $response=array(
              'status'=>true,
              'data'=>$report,
              'message'=>"Success"
          );
          return response()->json($response, 200);
      }catch (\Exception $exception){
          $response = [
              'status' => false,
              'errors' => $exception->getMessage(),
              'message' => "Something went wrong",
          ];


          return response()->json($response, 404);

      }



  }

    public function forward_case(Request $request){
        $validator = Validator::make($request->all(), [
//            'approval' => 'required|in:pending,approved',
          'forwarded' => 'required|boolean',
            'id'=>"required|numeric|exists:reports,id",
          'radiologist_id' => Rule::when( $request->forwarded==1 ,'required|numeric|exists:users,id'),
        ]);
        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }
        try {
            $report=Report::where('id',$request->id)->first();
          $report->forwarded=$request->forwarded;

//            $report->approval=$request->approval;

          if ($report->approval=="pending" && $request->forwarded==1){
              $response = [
                  'status' => false,
                  'message' => "In order to forward the case report status should be approved",
              ];


              return response()->json($response, 404);
          }

          if ($report->approval=="approved" && $request->forwarded==1){

              $report->save();
              $case=CaseForward::where(['report_id'=>$request->id,'radiologist_id'=>$request->radiologist_id])->first();
              if (CaseForward::where(['report_id'=>$request->id,'radiologist_id'=>$request->radiologist_id])->first()){
                  $response = [
                      'status' => false,
                      'message' => "This case has already been assigned to same radiologist",
                  ];


                  return response()->json($response, 404);

              }else if(CaseForward::where(['report_id'=>$request->id])->first()){
                  $response = [
                      'status' => false,
                      'message' => "This case has already been assigned to other radiologist",
                  ];


                  return response()->json($response, 404);
              }else{
                  $assign_report=new CaseForward();
                  $assign_report->report_id=$request->id;
                  $assign_report->radiologist_id=$request->radiologist_id;

                  $assign_report->hospital_id=$report->hospital_id;
                  $assign_report->user_id=$request->user()->id;
                  $assign_report->save();
              }

          }
          if ($request->forwarded==false ||$request->forwarded==0 ){
               if (Report::where('id',$request->id)->whereNotNull('report')->first()){
                   $response = [
                       'status' => false,
                       'message' => "Status cannot be changed as the report has been checked",
                   ];


                   return response()->json($response, 404);
               }else{
                   $case=CaseForward::where('report_id',$request->id)->first();
                   if ($case){
                       $report=Report::where('id',$request->id)->first();
                       $report->forwarded=$request->forwarded;
                       $report->save();
                       $case->delete();

                   }
               }

          }


            $response=array(
                'status'=>true,
                'data'=>$report,
                'message'=>"Success"
            );
            return response()->json($response, 200);
        }catch (\Exception $exception){
            $response = [
                'status' => false,
                'errors' => $exception->getMessage(),
                'message' => "Something went wrong",
            ];


            return response()->json($response, 404);

        }



    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_name' => 'required',
            'file_number' => 'required',
            'age' => 'required|numeric',
//            'relevant_history' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,jpeg|max:5048',
            'id'=>"required|numeric|exists:reports,id",
//            "report"=>"required|string|max:5000",
            "report"=>"nullable|max:4294967",
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
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('image'), $imageName);
            $imagePath = asset('image/' . $imageName);
            $report->image_path = $imagePath;
        }

//        $report->hospital_id=$request->user()->id;
        $report->patient_name = $request->patient_name;
        $report->file_number = $request->file_number;
        $report->age = $request->age;
//        $report->comment = $request->comment;

        $report->report=$request->report;

        $report->save();
        $response=array(
            'status'=>true,
            'data'=>$report,
            'message'=>"Success"
        );
        return response()->json($response, 200);

    }

    public function destroy($id)
    {
        $case=CaseForward::where('report_id',$id)->first();
        if ($case){
            $case->delete();
        }

        $report = Report::where('id',$id)->first();
        if ($report){
            $report->delete();
            $response=array(
                'status'=>true,
                'message'=>"Success"
            );
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
    public function get_report($id){
        $report = Report::where('id',$id)->with('hospital.hospital_details')->first();

            $response=array(
                'status'=>true,
                'data'=>$report,
                'message'=>"Success"
            );
            return response()->json($response, 200);

    }
    public function all_approved_reports(){
        $reports=Report::where(['approval'=>'approved','report'=>null])->orderBy('created_at', 'asc')->with('hospital.hospital_details')->get();

//        dd($reports);
        $response=array(
            'status'=>true,
            'data'=>$reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);

    }
    public function all_pending_reports(){
        $reports=Report::where('approval','pending')->orderBy('created_at', 'asc')->with('hospital.hospital_details')->get();
        $response=array(
            'status'=>true,
            'data'=>$reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);

    }
    public function all_forward_case_reports(){

      $reports=CaseForward::with('radiologists.radiologist_details','forwardedCase.hospital.hospital_details')->wherehas('forwardedCase',function ($q){
          $q->with('hospital')->orderBy('created_at', 'asc');
      })->get();

//        $reports=Report::where('forwarded',true)->with('hospital')->wherehas('forward_case',function ($q){
//            $q->with('radiologists');
//        })->get();

        $response=array(
            'status'=>true,
            'data'=>$reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }
    public function add_comment(Request $request){
        $validator = Validator::make($request->all(), [
            'id'=>"required|numeric|exists:reports,id",
            "report"=>"required|max:4294967",
        ]);
        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }
        if (Report::where(['id'=>$request->id,'forwarded'=>0])->first()){
            $response = [
                'status' => false,
                'message' => "This report can not be added as this report is not assigned to any of the radiologist",
            ];
            return response()->json($response, 404);

        }


        $report=Report::where('id',$request->id)->first();
        $report->report=$request->report;
        $report->save();
        $response=array(
            'status'=>true,
            'data'=>$report,
            'message'=>"Success"
        );
        return response()->json($response, 200);
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
        $reports=CaseForward::with('forwardedCase','hospital.hospital_details','radiologists.radiologist_details')->wherehas('forwardedCase',function ($q){
            $q->where('report','!=',null)->whereBetween('created_at', [request()->start_date, request()->end_date]);
        })->orderBy('created_at', 'asc')->get();
        $response = [
            'status' => true,
            'data'=>$reports,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function checked_reports(){

        $reports=CaseForward::with('forwardedCase','hospital.hospital_details','radiologists.radiologist_details')->wherehas('forwardedCase',function ($q){
            $q->where('report','!=',null);
        })->orderBy('created_at', 'asc')->get();
        $response = [
            'status' => true,
            'data'=>$reports,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }

    public function self_taking(Request $request){
        $validator = Validator::make($request->all(), [
            'id'=>"required|numeric|exists:reports,id",
            "report"=>"required|max:4294967",
        ]);
        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }
        if (Report::where(['id'=>$request->id,'forwarded'=>0,'report'=>null])->first()){
            $report=Report::where('id',$request->id)->first();
            $report->report=$request->report;
            $report->forwarded=true;
            $report->save();
            $case=new CaseForward();
            $case->radiologist_id=$request->user()->id;
            $case->report_id=$request->id;
            $case->user_id=$request->user()->id;
            $case->hospital_id=$report->hospital_id;
            $case->save();
            $response = [
                'status' => true,
                'data'=>$report,
                'message' => "success",
            ];
            return response()->json($response, 200);
        }else if (Report::where(['id'=>$request->id,'forwarded'=>1])->first()){
            $response = [
                'status' => false,
                'message' => "This Case has already been assigned to radiologist",
            ];
            return response()->json($response, 404);

        }else{
            $response = [
                'status' => false,
                'message' => "This case has been checked already",
            ];
            return response()->json($response, 404);
        }
    }

    public function auto_assign_case(Request $request){
        $validator = Validator::make($request->all(), [
            'toggle'=>"required|numeric|between:0,1",
        ]);
        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }

        $reports=Report::where('approval', 'pending')->where('forwarded', false)->where('report',null)->update(['auto_assign' => $request->toggle]);
        $findToggleStatus=AutoAssign::first();
        if ($findToggleStatus){
            $findToggleStatus->auto_assign=$request->toggle;
            $findToggleStatus->save();
        }else{
            $add_status=new AutoAssign();
            $add_status->auto_assign=$request->toggle;
            $add_status->save();
        }
        $pendingReports = Report::where('approval', 'pending')
            ->where('forwarded', false)
            ->where('report',null)
            ->where('auto_assign',1)

            ->get();


        $activeRadiologists = User::where('role_id', 3)
            ->where('status', 'active')
            ->where('user_verified', 1)
            ->get();

        if (count($pendingReports)>0 && count($activeRadiologists)>0){


            $pendingReportsCount = count($pendingReports);
            $radiologistIndex = 0;
            foreach ($pendingReports as $report) {

                $radiologist = $activeRadiologists[$radiologistIndex];


                CaseForward::create([
                    'hospital_id' => $report->hospital_id,
                    'report_id' => $report->id,
                    'radiologist_id' => $radiologist->id,
                    'user_id'=>request()->user()->id,
                ]);

                $report->update(['forwarded' => 1,'approval'=>"approved"]);

                // Move to the next radiologist in a round-robin fashion
                $radiologistIndex = ($radiologistIndex + 1) % count($activeRadiologists);
            }
        }

        $response = [
            'status' => true,
            'message' => "success",
        ];
        return response()->json($response, 200);




    }


}
