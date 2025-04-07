<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Models\API\Employee;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class EmployeeController extends BaseController
{
    public function employeeDetail(Request $request)
    {
        $authError = null;
        try {
            $this->auth($request); //required
        } catch (Exception $e) {
            $authError = $e;
        }

        if($authError) {
            try {
                $this->authMobile($request); //required
                $authError = null;
            } catch (Exception $e) {
                $authError = $e;
            }
        }
        
        $nik = $request->nik ?? null;
        $idCompany = $request->id_company ?? "NULL";
        try {
            if($authError) throw new Exception($authError->getMessage(), $authError->getCode());

            // Begin For Testing Dummy / Custom
            if(!$nik){
                throw new \Exception('Data Not Found', Response::HTTP_NOT_FOUND);
            }
            // if($nik == 'NIKDEMO123'){
            //     $nik_septa = '2019040739VI';

            //     $result = DB::table('hr_employee as he')
            //         ->leftJoin('hr_career_transaction as hct', 'hct.id_employee', '=', 'he.id_employee')
            //         ->leftJoin('master_position_detail as mpd', function ($join) {
            //             $join->on('hct.id_old_position_detail', '=', 'mpd.id_position_detail');
            //             $join->whereRaw('(mpd.id_company = hct.id_company OR mpd.assigned_to_company = hct.id_company)');
            //         })
            //         ->leftJoin('master_position_routing as mpr', function ($join) {
            //             $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
            //             $join->on('mpd.id_company', '=', 'mpr.id_company');
            //         })
            //         ->leftJoin('master_job_grade as mjg', function ($join) {
            //             $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
            //             $join->on('mpr.id_company', '=', 'mjg.id_company');
            //         })
            //         ->leftJoin('master_job_status as mjs', function ($join) {
            //             $join->on('mpr.id_job_status', '=', 'mjs.id_job_status');
            //             $join->on('mpr.id_company', '=', 'mjs.id_company');
            //         })
            //         ->leftJoin('master_job_position as mjp', function ($join) {
            //             $join->on('mpr.id_position', '=', 'mjp.id_position');
            //             $join->on('mpr.id_company', '=', 'mjp.id_company');
            //         })
            //         ->leftJoin('master_department as md', function ($join) {
            //             $join->on('mjp.id_dept', '=', 'md.id_dept');
            //             $join->on('mjp.id_company', '=', 'md.id_company');
            //         })
            //         ->leftJoin('master_branch as mb', function ($join) {
            //             $join->on('mpd.id_branch', '=', 'mb.id_branch');
            //             $join->on('mpd.id_company', '=', 'mb.id_company');
            //         })
            //         ->leftJoin('master_region as mr', function ($join) {
            //             $join->on('mb.id_region', '=', 'mr.id_region');
            //             $join->on('mb.id_company', '=', 'mr.id_company');
            //         })
            //         ->leftJoin('master_company as mc', function ($join) {
            //             $join->on('he.id_company', '=', 'mc.id_company');
            //         })
            //         ->leftJoin('master_company as mc2', function ($join) {
            //             $join->on('mpd.id_company', '=', 'mc2.id_company');
            //         })
            //         ->selectRaw(" 'NIKDEMO123' as nik_employee, he.name as employee_name, md.id_dept as id_department, md.description as department, mpr.id_routing as id_routing, mpr.description as position_routing, mpd.id_position_detail, mpd.description as position_detail, mjg.description as job_grade, mb.id_branch, mb.description as branch, mr.id_region, mr.description as regional, mc2.company_code as assigned_to_company, mc.external_company_code as company_code, mc.company_type, mc.description")
            //         ->where('he.nik_employee', $nik_septa)
            //         ->get();
            //     if(count($result) < 1){
            //         throw new \Exception('Data Not Found', Response::HTTP_NOT_FOUND);
            //     }
            // } else {
                $result = Employee::get_employee($nik, $idCompany);
                // $result = Employee::getEmployeeDetail($nik,null,true);
                if(count($result) < 1){
                    throw new \Exception('Data Not Found', Response::HTTP_NOT_FOUND);
                }
            // }
            // End For Testing Dummy


            // $result = Employee::get_employee($nik);
            // if(count($result) < 1){
            //     throw new \Exception('Data Not Found', Response::HTTP_NOT_FOUND);
            // }
            $message    = 'Get data success';
            $logging = "select * from sp_funct_api_employee_view('".$nik."')";
            \Log::channel('function')->info("success. ".$logging);
            return $this->sendSuccess($message, $result);
        } catch (\Illuminate\Database\QueryException $e) {
            $logging = "select * from sp_funct_api_employee_view('".$nik."'). ".$e->getMessage();
            \Log::channel('function')->error("error. ".$logging);
            return $this->sendError($e->getCode(), 'NIK Not Found!');
        } catch (\Exception $e) {
            $logging = "select * from sp_funct_api_employee_view('".$nik."'). ".$e->getMessage();
            \Log::channel('function')->error("error. ".$logging);
            return $this->sendError($e->getCode(), $e->getMessage());
        }
    }

    public function employeeDetailAll(Request $request)
    {
        try {
            $this->auth($request); //required
            $nik = $request->nik ?? null;

            if(!$nik){
                throw new \Exception('Data Not Found', Response::HTTP_NOT_FOUND);
            }

            $result = Employee::get_employee_all($nik);
            if(count($result) < 1){
                throw new \Exception('Data Not Found', Response::HTTP_NOT_FOUND);
            }

            $message    = 'Get data success';
            return $this->sendSuccess($message, $result);
        } catch (\Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }
    }

    public function getEmployeeCompanies(Request $request)
    {
        /*
        * Trello Issue 149.004 API Get Company by User
        */
        try {
            $this->authMobile($request); //required
            $nik = $request->nik ?? null;

            if(!$nik){
                throw new \Exception('Data Not Found', Response::HTTP_NOT_FOUND);
            }
            $companies = Employee::getCompanies($nik);
            // $employee->companies = $companies;
            return $this->sendSuccess("Data was successfully sent", $companies, false);
        } catch(\Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }
    }

}
