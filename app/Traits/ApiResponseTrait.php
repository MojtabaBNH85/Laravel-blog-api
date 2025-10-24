<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function successResponse($data = null , $massage = 'Success' , $status = 200){
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'massage' => $massage,
        ] , $status);
    }

    public function errorResponse($massage = 'Error' , $error = [] ,$status = 400){
        return response()->json([
            'status' => 'error',
            'massage' => $massage,
            'error' => $error,
        ] , $status);
    }
}
