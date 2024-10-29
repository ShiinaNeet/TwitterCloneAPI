<?php
namespace App\SharedFunctions;

class ResponseBuilder
{
    public static function buildResponse($data, $message, $status)
    {
        return response()->json([
            "data" => $data,
            "message" => $message,
            "status" => $status
        ]);
    }
}


