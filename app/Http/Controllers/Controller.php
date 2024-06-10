<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function download($url){

        $response = Http::get($url);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the content (image data) from the response
            $imageData = $response->body();

            // Set the appropriate content type for the response
            $contentType = $response->header('Content-Type');

            // Return the image data as the response
            return response($imageData)->header('Content-Type', $contentType);
        } else {
            // If the request was not successful, return an error response
            return response()->json(['error' => 'Failed to fetch image'], $response->status());
        }

    }
}
