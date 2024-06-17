<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CaseForward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(){
        $users=User::where('role_id', '!=',1)->with('radiologist_details','hospital_details')->get();
        $response = [
            'status' => true,
            'data'    => $users,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
//    public function store(Request $request){
//
//        $validator = Validator::make($request->all(), [
//            "first_name"=>"required|min:3|max:50",
//            "role_id"=>"required|numeric|exists:roles,id|between:1,3",
//            "last_name"=>"required|max:50|min:3",
//            "email"=>"required|unique:users|email|max:30|min:11",
//            "phone"=>"required|min:3|max:30",
//            "password"=>"required|min:4|max:16",
//            "country"=>"required|min:3|max:30",
//            "province"=>"required|min:3|max:30",
//            "city"=>"required|min:3|max:30",
//
//        ]);
//
//        if($validator->fails()){
//
//            $response = [
//                'status' => false,
//                'errors'    => $validator->errors(),
//                'message' => "Validation Fails",
//            ];
//
//
//            return response()->json($response, 404);
//
//        }
//        $data=$request->all();
////       $data['user_id']=$request->user()->id;
//        $data['role_id']=$request->role_id;
//        $data['password']=bcrypt($request->password);
//        $data['phone_verified']=1;
//        $data['email_verified']=1;
//        $data['user_verified']=1;
//        $data['status']='active';
//
//
//        $user=User::create($data);
//        $response = [
//            'status' => true,
//            'data'    => $user,
//            'message' => "success",
//        ];
//        return response()->json($response, 200);
//
//
//    }
    public function change_user_status(Request $request){

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


        $user=User::where('id',$request->user_id)->first();

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
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=>"required|min:1|max:50",
//            "last_name"=>"required|max:50|min:3",
//           "email"=>"required|unique:users|email|max:30|min:11",
            'email' => "required|max:100|email|unique:users,email,{$request->id}",
            "phone"=>"required|min:1|max:30",
//           "password"=>"required|min:4|max:16|nullable",
            'username' => "required|max:50|min:3|unique:users,username,{$request->id}",
            "id"=>"required|numeric|exists:users,id|between:1,999999999"
        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $data=$request->all();
        $data['user_id']=$request->user()->id;
        $user=User::where('id',$request->id)->first();
        $user->update($request->all());
        $response = [
            'status' => true,
            'data'    => $user,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function del_user($id){
        $user=User::where('id',$id)->first();
        $radiologist_case=CaseForward::where('radiologist_id',$id)->first();
        $hospital_case=CaseForward::where('hospital_id',$id)->first();
        if ($hospital_case){
            $errors=array(
                'errors'=>["This hospital cannot be deleted as reports of this hospital are in process with radiologist"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "failed",
            ];
            return response()->json($response, 404);
        }
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

    public function single_user($id){
        $user=User::where('id',$id)->with('radiologist_details','hospital_details')->first();



        $response = [
            'status' => true,
            'data'=>$user,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function all_radiologists(){
        $users=User::where(['role_id'=>3,'status'=>"active"])->with('radiologist_details')->get();
        $response = [
            'status' => true,
            'data'    => $users,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function update_password(Request $request){
        $validator = Validator::make($request->all(), [
 
          "password"=>"required|min:4|max:20",
            "id"=>"required|numeric|exists:users,id|between:1,999999999"
        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }

        $user=User::where('id',$request->id)->first();
        $user->password=bcrypt($request->password);
        $user->save();
        $response = [
            'status' => true,
            'data'    => $user,
            'message' => "Password Updated",
        ];
        return response()->json($response, 200);


    }
}
