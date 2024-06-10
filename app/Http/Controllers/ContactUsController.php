<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContactUsController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            "name"=>"required|min:3|max:50",
            "email"=>"required|email|max:30|min:11",
            "phone"=>"required|min:3|max:30",
            "message"=>"required|min:3|max:200000",


        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }

        $senderDetails = [
            'Name: ' => $request->name,
            'Email: ' => $request->email,
            'Phone: ' => $request->phone,
            'Message: ' => $request->message
        ];

        $messageBody = "Sender Details:\n";
        foreach ($senderDetails as $key => $value) {
            $messageBody .= $key . $value . "\n";
        }

// Sending the email
        Mail::raw($messageBody, function ($message) {
            $message->from(env('MAIL_FROM_ADDRESS'));
            $message->to('khataumd@hotmail.com');
            $message->subject('User Contact Form');
        });
//        $data=$request->all();
//
//        $contactForm=ContactUs::create($validator->validated());
        $response = [
            'status' => true,
//            'data'    => $contactForm,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
}
