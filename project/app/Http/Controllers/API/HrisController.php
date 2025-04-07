<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HrisController extends Controller
{
    private function apiKey(){
        $key = 'MIIC3DCCAcQCAQAwgZYxCzAJBgNVBAYTAklEMRAwDgYDVQQIDAdKQUtBUlRBMRQwEgYDVQQHDAtES0kgSmFrYXJ0YTEUMBIGA1UECgwLUFQuIEJvcndpdGExDjAMBgNVBAsMBXNmYXBpMRIwEAYDVQQDDAlwdGJvcndpdGExJTAjBgkqhkiG9w0BCQEWFnlva2kuYW5kaWthQGRhdGFvbi5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQClytZtdVQ4LO8H5uzSRGMrDsuha0tM3rTUn/IaLNgxItiGb8U1tYHN9oOF2WqmmVhaQZQHdlDTt+YUYqI4lSFJfcwHSVhYpAvzyMeK+dYbSamsj7++E7FsmPltveLRoIWjPWQqb4/jZxXqblX6HKtbypyx/xgGtjvgOF+J7dm2FMSAp9TJdhXpiJUlSYtB3CDeW0XPAoMtZtr8NBrcy0p+LfNvMZc3tx2YL1qOyS1M4bdEQ7Ln6WfRXOiuo9icCVQwoJviZmOsqqYtnOYiAik1CfphvcWwPlY5LOANPsZCUXH7pbWCqy1mq4TlFHeph8AyHLBDc6x2MEVntNXXorbtAgMBAAGgADANBgkqhkiG9w0BAQsFAAOCAQEATIBxvRYUQwuuMXzQrWFhuMV6D/9ve4xuG6R/shv2U4cK30B19/MPw55bBp/yqvRSCt2QJADPrY4E7Bh7vtSGxOtxuRLzpbTT/hgEt1jQ+H/oAKORSbXEMEFnY18Ff5isflukcsDOyo8g7OV7Yx6Nj81ERc6e9pIA1sPNk5RPL1ebKDBJuGnb23eThcWwWImvyejiH3QDgzjBzVhwnqFLAT+36DDRJx8YggduvX7Yza5dJweAgGHuVlRYW2/Pd5iWrh83PLdUlxZNLc/+p3oQ84ADhJr7xQIJ/OuaxmeH/XNOgFUlVoK1oT8QS8/7SkjhYnkT/3mtHiOmiDxsc3CkMw==';
        return $key;
    }

    public function auth($request){
        $authorization  = $request->header('X-SFAPI-RSAKey');
        $key            = self::apiKey();
        if(!$authorization || $authorization != $key){
            throw new \Exception('Access Not Allowed', Response::HTTP_UNAUTHORIZED);
        }
        return true;
    }

    public function sendSuccess($message='', $result = [])
    {   
        $code = 200;
    	$response = [
            'DSNAUTHPAYROLL'    => 'dbsf_ptborwita_payroll',
            'STATUS'            => true,
            'APPREQUEST'        => 'sfapi',
            'MESSAGE'           => $message,
            'COUNT'             => count($result),
            'RESULT'            => $result,
        ];
        return response()->json($response, $code);
    }

    public function sendError($httpCode, $message='', $result = [])
    {   
    	$code = 200;
        $response = [
            'DSNAUTHPAYROLL'    => 'dbsf_ptborwita_payroll',
            'STATUS'            => false,
            'APPREQUEST'        => 'sfapi',
            'MESSAGE'           => $message,
            'COUNT'             => count($result),
            'RESULT'            => $result,
        ];
        return response()->json($response, $code);
    }

    public function employeeDetailHris(Request $request)
    {
        try {
            $this->auth($request);

            $id_company = $request->id_company ?? null;
            $start_date = $request->start_date ?? null;
            $end_date   = $request->end_date ?? null;
            $limit      = $request->limit ?? null;
            $date       = $request->date ?? null;

            if(!$id_company){
                throw new \Exception('Id Company must be set');
            }

            $return = [];
            $today = date('Y-m-d');
            $data = DB::table(DB::raw('sp_funct_get_employee_report ('.$id_company.') sfger'))
                ->leftJoin('master_position_detail as mpd', function ($join) {
                    $join->on('sfger.id_employee', '=', 'mpd.id_employee');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->select('sfger.*');
            if($limit){
                $data->limit($limit);
            }
            if($date){
                $now = date('Y-m-d', strtotime($today." +1 days")); //pengecekan laravel jika range sampai hari ini maka harus +1 day
                $data->where(function($q) use ($date, $now){
                    $q->whereBetween('sfger.update_date', [$date, $now])
                        ->orWhereBetween('mpd.update_date', [$date, $now])
                        ->orWhereBetween('mpr.update_date', [$date, $now]);
                });
            } else {
                $now = date('Y-m-d', strtotime($today." +1 days")); //pengecekan laravel jika range sampai hari ini maka harus +1 day
                $min_3 = date('Y-m-d', strtotime($today." -3 days"));
                $data->where(function($q) use ($min_3, $now){
                    $q->whereBetween('sfger.update_date', [$min_3, $now])
                        ->orWhereBetween('mpd.update_date', [$min_3, $now])
                        ->orWhereBetween('mpr.update_date', [$min_3, $now]);
                });
            }

            $result = $data->get();
            if(count($result) < 1){
                throw new \Exception('Data Not Found');
            }

            foreach ($result as $key => $val) {
                $thisData = [
                    'PRINCIPAL' => @$val->principal,
                    'NIKDIRECTSUPERVISOR' => @$val->nik_parent_emp_name,
                    'MARITALSTATUS' => @$val->marital,
                    'IMMEDIATEMANAGER' => @$val->indirect_emp_name,
                    'DEPARTMENT' => @$val->department,
                    'TERMINATE_DATE' => @$val->resign_date,
                    'GRADE' => @$val->job_grade,
                    'JOBTITLE' => @$val->position_routing,
                    'FIRSTNAME' => @$val->name,
                    'EMPLOYEESTATUS' => @$val->status_active!='A' ? 'Inactive' : 'Active',
                    'CITY' => @$val->home_base,
                    'EMAIL' => @$val->private_mail,
                    'BIRTHPLACE' => @$val->place_of_birth,
                    'EMPLOYEENAME' => @$val->name,
                    'EMPLOYMENTSTATUS' => @$val->employment_status,
                    'REGIONAL' => @$val->regional,
                    'CABANG' => @$val->branch,
                    'JOINDATE' => @$val->join_date,
                    'COSTCENTER' => '',
                    'EMPLOYMENTENDDATE' => @$val->expired_date,
                    'DIRECTSUPERVISOR' => @$val->parent_emp_name,
                    'EMPLOYMENTSTARTDATE' => @$val->join_date,
                    'EMPLOYEENO' => @$val->nik_employee,
                    'RELIGION' => @$val->religion,
                    'NATIONALITY' => '',
                    'GENDER' => @$val->gender,
                    'BIRTHDATE' => @$val->birthdate,
                    'IDNUMBER' => @$val->identification_number,
                    'NIKIMMEDIATEMANAGER' => @$val->nik_indirect_emp_name,
                    'TAXNO' => @$val->npwp_number ?? '',
                    'PHONE' => @$val->mobile_phone,
                    'WORKLOCATION' => @$val->work_location,
                    'ADDRESS' => @$val->idcard_address,
                ];
                $return[] = $thisData;
            }
            
            $message    = 'Get data success';
            return $this->sendSuccess($message, $return);
        } catch (\Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }
    }
}
