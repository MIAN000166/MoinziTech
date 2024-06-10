<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Admin\CaseForward;
use App\Models\AutoAssign;
use App\Models\Hospital\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function store(Request $request)
    {
//        Validator::extend('micro_dicom_format', function ($attribute, $value, $parameters, $validator) {
//            $extension = strtolower($value->getClientOriginalExtension());
//            return $extension === 'dcm'; // Assuming MicroDICOM files have the ".dcm" extension
//        });

        $validator = Validator::make($request->all(), [
            'patient_name' => 'required|max:100',
//            'file_number' => 'required|unique:reports,file_number',
            'file_number' => 'required|max:9999999999',
            'age' => 'required|numeric',
            'relevant_history' => 'required',
            'image' => ['required', 'file', 'mimes:dcm,pdf,doc,docx,png,jpg,jpeg,gif'],
            "report_type"=>'required|in:CT Scan,MRI,XRAY,ULTRASOUND',
            "insurance"=>"required|in:NHIF,Jubilee,Strategis,MO Assurance,OTHER,Aetna,NSSF,Cash,Britam,Cigna,GA Insurance,Assemble",
            'other_insurance_type' => Rule::when($request->insurance=="OTHER",'required|string|max:100|min:1'),
            'xray_type' => Rule::when($request->report_type=="XRAY",'required|string|max:100|min:1'),
            'ct_scan_type' => Rule::when($request->report_type=="CT Scan",'required|string|max:100|min:1'),
            'mri_type' => Rule::when($request->report_type=="MRI",'required|string|max:100|min:1'),
            'ultrasound_type' => Rule::when($request->report_type=="ULTRASOUND",'required|string|max:100|min:1'),

        ]);



        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }
        $latestReportStatus=AutoAssign::first();
        if ($latestReportStatus){
            $assign=$latestReportStatus->auto_assign;

        }else{
            $assign=0;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('image'), $imageName);
            $imagePath = asset('image/' . $imageName);
        }
        $report = new Report();
        $report->hospital_id=$request->user()->id;
        $report->patient_name = $request->patient_name;
        $report->file_number = $request->file_number;
        $report->age = $request->age;
        $report->relevant_history = $request->relevant_history;
        $report->image_path = $imagePath;
        $report->approval="pending";
        $report->forwarded=0;
        $report->report_type=$request->report_type;
        $report->insurance=$request->insurance;
        $report->other_insurance_type=$request->other_insurance_type;
        $report->auto_assign=$assign;
        if ($request->report_type=="XRAY"){
            $report->xray_type=$request->xray_type;
        } else if($request->report_type=="MRI"){
            $report->mri_type=$request->mri_type;
        }else if($request->report_type=="CT Scan"){
            $report->ct_scan_type=$request->ct_scan_type;
        }else{
            $report->ultrasound_type=$request->ultrasound_type;
        }




//        $report->report=$request->report;

        $report->save();
        $response=array(
            'status'=>true,
            'data'=>$report,
            'message'=>"Success"
        );
        return response()->json($response, 200);

    }

    public function index()
    {
        $reports = Report::where('hospital_id',request()->user()->id)->orderBy('created_at', 'asc')->get();
        $response=array(
            'status'=>true,
            'data'=>$reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }

    public function single_report($id){
        $report = Report::where('id',$id)->first();
        $response=array(
            'status'=>true,
            'data'=>$report,
            'message'=>"Success"
        );
        return response()->json($response, 200);


    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_name' => 'required',
            'file_number' => 'required',
            'age' => 'required|numeric',
//            'relevant_history' => 'required',
            'image' => ['required', 'file', 'mimes:dcm,pdf,doc,docx,png,jpg,jpeg,gif'],
            "report_type"=>"required|max:50",
//            "report"=>"required|max:1000",
            "insurance"=>"required|in:NHIF,Jubilee,Strategis,MO Assurance,OTHER",
            'other_insurance_type' => Rule::when($request->insurance=="OTHER",'required|string|max:100|min:1'),
            'xray_type' => Rule::when($request->report_type=="XRAY",'required|string|max:100|min:1'),
            'ct_scan_type' => Rule::when($request->report_type=="CT Scan",'required|string|max:100|min:1'),
            'mri_type' => Rule::when($request->report_type=="MRI",'required|string|max:100|min:1'),
            'ultrasound_type' => Rule::when($request->report_type=="ULTRASOUND",'required|string|max:100|min:1'),

            'id'=>"required|numeric|exists:reports,id",
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

        $report->hospital_id=$request->user()->id;
        $report->patient_name = $request->patient_name;
        $report->file_number = $request->file_number;
        $report->age = $request->age;
        $report->insurance=$request->insurance;
        $report->other_insurance_type=$request->other_insurance_type;
        $report->report_type=$request->report_type;
        if ($request->report_type=="XRAY"){
            $report->xray_type=$request->xray_type;
        } else if($request->report_type=="MRI"){
            $report->mri_type=$request->mri_type;
        }else if($request->report_type=="CT Scan"){
            $report->ct_scan_type=$request->ct_scan_type;
        }else{
            $report->ultrasound_type=$request->ultrasound_type;
        }
//        $report->relevant_history = $request->relevant_history;
        $report->save();
        $response=array(
            'status'=>true,
            'data'=>$report,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }

    public function pending_reports(){
        $reports=Report::where(['hospital_id'=>request()->user()->id,'approval'=>"pending"]) ->orderBy('created_at', 'asc')->get();
        $response=array(
            'status'=>true,
            'data'=>$reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }
    public function approved_reports(){
        $reports=Report::where(['hospital_id'=>request()->user()->id,'approval'=>"approved"]) ->orderBy('created_at', 'asc')->get();
        $response=array(
            'status'=>true,
            'data'=>$reports,
            'message'=>"Success"
        );
        return response()->json($response, 200);
    }

    public function destroy($id)
    {
        $report = Report::where('id',$id)->first();
        $case_report=CaseForward::where('report_id',$id)->first();
        if ($case_report){
            $errors=array(
                'errors'=>["This report cannot be deleted as it has been assigned to radiologist"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "failed",
            ];
            return response()->json($response, 404);
        }

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

public function checked_reports(){



    $reports=CaseForward::where('hospital_id',request()->user()->id)->with('forwardedCase','radiologists.radiologist_details')->wherehas('forwardedCase',function ($q){
        $q->whereNotNull('report');
    }) ->orderBy('created_at', 'asc')->get();
    $response = [
        'status' => true,
        'data'=>$reports,
        'message' => "success",
    ];
    return response()->json($response, 200);

}

public function add_extra_file(Request $request)
{
    $validator = Validator::make($request->all(), [

        'doc_file' => ['required', 'file', 'mimes:doc,docx'],
        'id'=>"required|numeric|exists:reports,id",
    ]);
    if ($validator->fails()) {

        $response = [
            'status' => false,
            'errors' => $validator->errors(),
            'message' => "Validation Fails",
        ];


        return response()->json($response, 404);
    }

    if ($request->hasFile('doc_file')) {
//        dd($request->user()->id);
        $report =  Report::where(['id'=>$request->id,'hospital_id'=>$request->user()->id])->first();
        $image = $request->file('doc_file');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('doc/file'), $imageName);
        $imagePath = asset('/doc/file/' . $imageName);
        $report->image_path2 = $imagePath;
        $report->save();
        $response = [
            'status' => true,
            'data'=>$report->image_path2,
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
}

