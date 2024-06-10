<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\HospitalProfile;
use App\Models\RadiologistProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function register(Request $request){

        $findEmail=User::where(['email'=>$request->email,'username'=>$request->username,'user_verified'=>0])->first();
        if ($findEmail){
            $otp=mt_rand(1000,9999);
            $findEmail->email_otp=$otp;
            $findEmail->save();
            Mail::raw('Your verification code is'.$otp ,function ($message) use ($findEmail) {
                $message->from(env('MAIL_USERNAME'));
                $message->to($findEmail->email);
                $message->subject('Verification Code');
            });
            $response = [
                'status' => true,
                'data'    => $findEmail,
                'message' => "As user is already been registered but not verified so a verification code has been sent to your registered email",
            ];
            return response()->json($response, 200);

        }


        $validator = Validator::make($request->all(), [
//            "first_name"=>"required|min:3|max:50",
            "username"=>"required|max:50|min:3|unique:users,username",
            "name"=>"required|max:30|min:3",
            "city"=>"required|max:50",
            "region"=>"required|max:50",
            "address"=>"required|max:500",
            "authorized_rep_name"=>"required|max:20|min:3",
            "authorized_rep_phone"=>"required|max:25",
//           "phone"=>"required|max:20",
//            "last_name"=>"required|max:50|min:3",
            "email"=>"required|unique:users|email|max:30|min:11",
            "phone"=>"required|min:3|max:30",
            "password"=>"required|min:4|max:16",


        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $otp=mt_rand(1000,9999);

        try {
            $user=User::create(['name'=>$request->name,'username'=>request()->username, 'email' => request()->email,
                'password' => bcrypt(request()->password),'role_id'=>2,'slug'=>"hospital",'email_otp'=>$otp,'status'=>'inactive','phone'=>request()->phone]);





            if (!empty($user)){
                $profile=HospitalProfile::create(['address'=>$request->address,'city'=>$request->city,'region'=>$request->region,
                    'authorized_rep_name'=>$request->authorized_rep_name,'authorized_rep_phone'=>$request->authorized_rep_phone,'user_id'=>$user->id]);

                Mail::raw('Your verification code is'.$otp ,function ($message) use ($user) {
                    $message->from(env('MAIL_USERNAME'));
                    $message->to($user->email);
                    $message->subject('Verification Code');
                });

                $response = [
                    'status' => true,
                    'data'    => $user,
                    'message' => "Verification code has been sent to your registered email",
                ];
                return response()->json($response, 200);
            }




        }catch (\Exception $e){
            $response = [
                'status' => false,
                'errors'    => $e->getMessage(),
                'message' => "failed",
            ];
            return response()->json($response, 404);
        }




    }

    public function verify(Request $request){
        $validator = Validator::make($request->all(), [

            "user_id"=>"required|numeric|exists:users,id|digits_between:1,111111111",
            "email_otp"=>"required|digits_between:1,9999|exists:users,email_otp",
        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $user=User::where(['email_otp'=>request()->email_otp,'id'=>request()->user_id])->first();
        if ($user){
            $user->user_verified=true;
            $user->email_verified=true;
            $user->email_otp=null;

            $user->save();

            $response = [
                'status' => true,
                'data'    => $user,
                'message' => "User has been verified",
            ];


            return response()->json($response, 200);
        }else{


            $errors=array(
                'errors'=>["Verification failed"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "Try again",
            ];
            return response()->json($response, 404);
        }
    }
}
