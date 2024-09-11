<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function successResponse($result, $message){
        $response = [
            "success" => true,
            "data" => $result,
            "message"=> $message
        ];
        return response()->json($response, 200);

    }

    public function errorResponse($error, $errorMessage = [], $code = 400){
        $response = [
            "success"=> false,
            "message"=> $error
            ];
            if(!empty($errorMessage)){
                $response["data"] = $errorMessage;
            }
            return response()->json($response, $code);
    }
}
