<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function successResponse($data = null , $message = 'Success' , $status = 200){
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ] , $status);
    }

    public function errorResponse($message = 'Error' , $error = [] ,$status = 400){
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error' => $error,
        ] , $status);
    }
}
