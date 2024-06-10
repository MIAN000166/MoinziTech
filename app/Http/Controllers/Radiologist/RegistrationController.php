<?php

namespace App\Http\Controllers\Radiologist;

use App\Http\Controllers\Controller;
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
            "name"=>"required|min:3|max:50",
            "username"=>"required|max:50|min:3|unique:users,username",
            "email"=>"required|unique:users|email|max:30|min:11",
            "phone"=>"required|min:3|max:30",
            "mmed_graduation_year"=>"required|max:20",
            "mmed_completed_from"=>"required|max:50",
            "experience"=>"required|numeric|digits_between:0,100",
            "mct_number"=>"required|max:20",
            "mmed_certificate"=>"required|mimes:jpeg,png,jpg,gif,pdf|max:5048",
            "mct_license"=>"required|mimes:jpeg,png,jpg,gif,pdf|max:5048",
            "cv"=>"required|mimes:jpeg,png,jpg,pdf|max:10048",
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
            $user=User::create(['name'=>$request->name,'username'=>request()->username,  'email' => request()->email,
                'password' => bcrypt(request()->password),'role_id'=>3,'slug'=>"radiologist",'email_otp'=>$otp,'status'=>'inactive','phone'=>request()->phone]);
            if ($request->hasFile('mmed_certificate')) {
                $image = $request->file('mmed_certificate');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('radiologist/certificates/'), $imageName);
                $imagePathCertificate = asset('radiologist/certificates/' . $imageName);
            }
            if ($request->hasFile('mct_license')) {
                $image = $request->file('mct_license');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('radiologist/license/'), $imageName);
                $imagePathLicense = asset('radiologist/license/' . $imageName);
            }
            if ($request->hasFile('cv')) {
                $image = $request->file('cv');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('radiologist/cv/'), $imageName);
                $imagePathCv = asset('radiologist/cv/' . $imageName);
            }


            if (!empty($user)){
                $profile=RadiologistProfile::create(['age'=>$request->age,'mmed_graduation_year'=>$request->mmed_graduation_year,'mmed_completed_from'=>$request->mmed_completed_from,
                    'experience'=>$request->experience,'mct_number'=>$request->mct_number,'mmed_certificate'=>$imagePathCertificate,'mct_license'=>$imagePathLicense,
                    'cv'=>$imagePathCv,'user_id'=>$user->id]);



                Mail::raw('Your verification code is'.$otp ,function ($message) use ($user) {
                    $message->from(env('MAIL_USERNAME'));
                    $message->to($user->email);
                    $message->subject('Verification Code');
                });

                $response = [
                    'status' => true,
                    'user'    => $user,
//                    'profile'=>$profile,
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
