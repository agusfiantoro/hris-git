<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Curl extends Model
{
    use HasFactory;

    public function __construct()
    {
        parent::__construct();
    }

    public static function _send_($url, $data, $headers=[], $method = "POST", $jsonEncode = true, $build_query = false, $timeout = 30)
    {
        if ($jsonEncode) {
            $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        }
        if ($build_query) {
            $data = http_build_query($data);
        }

        $curlHandle = curl_init($url);

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        if ($timeout > 0) {
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, $timeout);
        }
        $exec = curl_exec($curlHandle);
        $respon = json_decode($exec);
        if (curl_error($curlHandle) ) {
            throw new \Exception(curl_error($curlHandle));
        }
        curl_close($curlHandle);

        return $respon;
    }

    public static function findApi($name='', $data=null)
    {
        ini_set('max_execution_time', -1);
        $getApi = DB::table('master_api_key as mak')
                ->select('mak.*')
                ->where('mak.name', $name)
                ->first();
                
        if(is_null($getApi)){
            return false;
        }
        return $getApi;
    }

    public static function formatPhone($number, $type='whatsapp')
    {
        $numbers_only   = preg_replace("/[^\d]/", "", $number);
        $res            = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1$2$3", $numbers_only);
        $first          = substr($res, 0, 1);
        if($first == '0'){
            $wa = '62'.substr($res, 1);
            $sms = $res;
        } else { 
            $wa = $res;
            $sms = '0'.substr($res, 2);
        }

        $return = [
            'whatsapp'  => trim($wa),
            'sms'       => trim($sms)
        ];
        return $return[$type];
    }

}
