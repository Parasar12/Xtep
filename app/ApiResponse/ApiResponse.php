<?php
namespace App\ApiResponse;
use Illuminate\Http\Response;

class ApiResponse
{
    public static function sendResponse($status, $success = null, $message, $data = null, $path = null)
    {
        $response['status_code']  = intval($status);
        $response['success']  = $success;
        $response['message'] = $message;

        $response['data'] = $data;

        return response()->json($response, $status);
    }

    public static function sendValidationErrorResponse($errors)
    {
        return self::sendResponse(417, true, $errors);
    }

    public static function sendExceptionResponse($message)
    {
        return self::sendResponse(400, TRUE, $message);
    }

    public static function sendSuccessResponse($message, $data = null, $path = null)
    {
        if (isset($data['status_code'])){
            return self::sendResponse($data['status_code'], TRUE, $message, $data, $path);
        }else{
            return self::sendResponse('200', TRUE, $message, $data, $path);

        }
    }

    public static function sendUnauthorizedResponse($message)
    {
        return self::sendResponse(401, TRUE, $message);
    }

    public static function sendGeneralErrorResponse($message)
    {
        return self::sendResponse(205, TRUE, $message);
    }

    public static function sendSpecificErrorResponse($code, $message, $data = null)
    {
        return self::sendResponse($code, TRUE, $message, $data);
    }

    public static function sendNoDataErrorResponse($message)
    {
        return self::sendResponse(403, FALSE, $message);
    }
}
