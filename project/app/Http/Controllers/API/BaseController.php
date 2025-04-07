<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Curl;
use Carbon\Carbon;


class BaseController extends Controller
{
    private function apiKey(){
        $key = env('AUTH_API_KEY');
        return $key;
    }

    private function apiKeyAccounting(){
        $key = env('AUTH_API_KEY_ACCOUNTING');
        return $key;
    }

	private function apiKeyOrbiz(){
        $key = env('AUTH_API_KEY_ORBIZ');
        return $key;
    }

    private function basicAuth(){
        
        $return['user'] = env('AUTH_API_USER'); //env('AUTH_API_USER', 'itborwita');
        $return['password'] = env('AUTH_API_PASSWORD'); //env('AUTH_API_PASSWORD', 'borwitaglory2022');
        
        return $return;
    }

    public function auth($request){
        $authorization  = $request->header('Authorization');
        $key            = self::apiKey();
        $keyAccounting  = self::apiKeyAccounting();
        $keyOrbiz	    = self::apiKeyOrbiz();
        if(!$authorization){
            throw new \Exception('Authentication failed', 401);
        }
        if($authorization != $key){
            if($authorization != $keyAccounting) {
                if($authorization != $keyOrbiz) {
                    throw new \Exception('Authentication failed', 401);
                }
            }
        }
        return true;
    }

    public function authMobile($request){
        // $authorization  = $request->header('Authorization');
        $user           = $request->getUser();
        $password       = $request->getPassword();
        if($user != self::basicAuth()['user'] || $password != self::basicAuth()['password']){
            throw new \Exception('Authentication failed', 401);
        }
        return true;
    }

    public function sendSuccess($message='', $result = [], $legacyMode = true)
    {   
        $code = 200;
    	$response = [
            'status'    => "success",
            'message'   => $message,
            'data'      => $result,
        ];
        if($legacyMode) {
            $response['code'] = $code;
            $response['status'] = true;
        }
        return response()->json($response, $code);
    }

    public function sendError($httpCode, $message='', $result = [])
    {   
        $code = $httpCode == 0 ? 404 : (strlen($httpCode) > 3 ? 500 : $httpCode);
    	$response = [
            'status'    => "error",
            'message'   => $message,
            'data'      => $result,
        ];
        return response()->json($response, $code);
    }

    public function mobileSuccess($message=null, $result = null)
    {   
        // if(is_array($result)){ ksort($result); }
        $code = 200;
        $response = [
            'status'    => self::statusByCode($code),
            'message'   => $message,
            'data'      => $result,
        ];
        return response()->json($response, $code);
    }

    public function mobileErrorCustom($e)
    {   
        $code = $e->getCode() == 0 ? 422 : (strlen($e->getCode()) > 3 ? 500 : $e->getCode());
        $logId = 'ErrorLogID: '.uniqid();
        $response = [
            'status'    => self::statusByCode($code),
            'message'   => $e->getMessage(),
            'data'      => null,
        ];
        if($code != 422){
            \Log::channel('mobile')->error('['.$logId.'] '.$e->getMessage());
        }
        return response()->json($response, $code);
    }

    public function mobileErrorValidation($e)
    {   
        $code = 422;
        $logId = 'ErrorLogID: '.uniqid();
        $response = [
            'status'    => self::statusByCode($code),
            'message'   => $e->getMessage(),
            'data'      => $e->validator->errors()->all(),
        ];
        \Log::channel('mobile')->error('['.$logId.'] '.$e->getMessage().' ('.collect($e->validator->errors()->all())->implode(', ').')');
        return response()->json($response, $code);
    }

    public function mobileErrorDuplicate($e)
    {   
        $code = 422;
        $logId = 'ErrorLogID: '.uniqid();
        $response = [
            'status'    => 'duplicate',
            'message'   => $e->getMessage(),
            'data'      => $e->errors(),
        ];
        \Log::channel('mobile')->error('['.$logId.'] '.$e->getMessage().' ('.$e->errors().')');
        return response()->json($response, $code);
    }

    public function mobileErrorQuery($e)
    {   
        $code = 500;
        $logId = 'ErrorLogID: '.uniqid();
        $response = [
            'status'    => self::statusByCode($code),
            'message'   => 'Error encountered processing to database server. '.$logId,
            'data'      => null,
        ];
        \Log::channel('mobile')->error('['.$logId.'] '.$e->getMessage());
        return response()->json($response, $code);
    }

    public function mobileError($e)
    {   
        $code = $e->getCode() == 0 ? 500 : (strlen($e->getCode()) > 3 ? 500 : $e->getCode());
        $logId = 'ErrorLogID: '.uniqid();
        $response = [
            'status'    => self::statusByCode($code),
            'message'   => 'Internal server Error. '.$logId,
            'data'      => null,
        ];
        \Log::channel('mobile')->error('['.$logId.'] '.$e->getMessage());
        return response()->json($response, $code);
    }

    public static function statusByCode($number)
    {   
        $code = [
            200 => 'success',
            401 => 'failed',
            403 => 'forbidden',
            404 => 'not_found',
            422 => 'failed',
            500 => 'failed',
        ];
        return $code[$number];
    }
}
