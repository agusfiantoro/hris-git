<?php

namespace App\Traits;

/**
 * StandardResponse Trait
 * provides simple methods to return a JSON response in a standardized way.
 * @author Faisal
 */
trait StandardResponse {
    

    /**
     * This method is used to return success response with the HRIS standard of 
     * status, message, data JSON structure
     * @param   mixed   $data        accepts any value to be encoded into JSON
     * @param   string  $message     accepts string value for the response message
     * @param   int     $code        accepts HTTP Status Code to be sent on the the response
     * @param   array   $headers     accepts keyed array as HTTP response header
     * @return  \Illuminate\HTTP\JsonResponse
     */
    public function success(
        $data = [],
        string $message = "Success",
        int $code = 200,
        array $headers = []
    ) {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code, $headers);
    }

    /**
     * This method is used to return error response with the HRIS standard of 
     * status, message, data JSON structure
     * @param   mixed   $data        accepts any value to be encoded into JSON
     * @param   string  $message     accepts string value for the response message
     * @param   int     $code        accepts HTTP Status Code to be sent on the the response. Any argumets outside the range of 100-599 will be converted to 500.
     * @param   array   $headers     accepts keyed array as HTTP response header
     * @return  \Illuminate\HTTP\JsonResponse
     */
    public function error(
        $data = [],
        $message = "Error",
        $code = 500,
        $headers = []
    ) {
        $code = $code >= 100 && $code < 600 ? $code : 500;
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $code, $headers);
    }

    /**
     * This method is used to return success response with JSON structure that is non-compliant
     * to HRIS standard response
     * @param   mixed   $payload     accepts any value to be encoded into JSON
     * @param   int     $code        accepts HTTP Status Code to be sent on the the response
     * @param   array   $headers     accepts keyed array as HTTP response header
     * @return  \Illuminate\HTTP\JsonResponse
     */
    public function responseJson($payload, $code, $headers) {
        return response()->json($payload, $code, $headers);
    }
}