<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "login_identifier"=> 'required|max:50',
            'password' => 'required|max:16|min:4',
        ], [
            'login_identifier.required' => 'The email or username field is required.',
        ]);


        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);


        }
        $loginIdentifier = $request->input('login_identifier');
        $field = filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $loginIdentifier, 'password' => $request->password])) {

            $user = Auth::user();
            if ($user->role_id==1){

                $token=  $user->createToken('Moinzi')->accessToken;
//                $user['image_path']="uploads/borrower/profile/images".$user->image;
                $response=array(
                    'status'=>true,
                    'data'=>$user,
                    'token'=>$token,
                    'message'=>"Login Successfully"
                );
                return response()->json($response, 200);

            }
            Auth::logout();
            $errors=array(
                'errors'=>["Unauthorised portal"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "Unauthorised",
            ];
            return response()->json($response, 404);

        }
        else{
            $errors=array(
                'errors'=>["Invalid Credentials"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "Unauthorised",
            ];

            return response()->json($response, 404);
        }
    }

    public function logout(){
        $user = request()->user()->token();
        $user->revoke();
        $response = [
            'status' => true,
            'message' => "Logout Successfully",
        ];
        return response()->json($response, 200);
    }
}
