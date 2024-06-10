<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function download($filename)
    {
//        return 1;
        $filePath = public_path('image/' . $filename);
//dd($filePath);
        // Check if the file exists
        if (file_exists($filePath)) {
            // Return the file as a response with a 200 OK status
//            return response()->download($filePath, null, [], null);
            $mimeType = mime_content_type($filePath);

            // Return the file as a response with a 200 OK status
            return response()->download($filePath, $filename, ['Content-Type' => $mimeType]);

        }

        // If the file does not exist, return a 404 Not Found status
        $response = [
            'status' => false,
            'errors'    => "Something went wrong",
            'message' => "failed",
        ];
        return response()->json($response, 404);
    }
}
