<?php

namespace App\Http\Controllers\Employee\EmployeeRequestManagement;

use App\Models\Employee\EmployeeRequest\RequestHeader;
use App\Models\Employee\EmployeeRequest\RequestDetail;
use App\Models\Employee\EmployeeRequest\ApprovalDelegation;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeSetting\CustomReport;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class EmployeeRequestManagementController extends Controller {

    public function index(Request $request) {
	//	dd($codeid);
    	$id_employee 	= [];
    	if(session('access_group') != 'Default_Administrator'){
            $path_menu 		= 'employee/employee_setting/workdays';
        	$id_branch      = [];
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu);
            if(count($get_ur) > 0){
                $get_ur = $get_ur[0];
                $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->id_user_responsibility);
                if(count($get_branch) > 0){
                    $id_branch        = $get_branch->pluck('id_branch')->all();
                }
            }
            if(count($id_branch) > 0){
                $id_employee = JobPositionDetail::whereIn('id_branch', $id_branch)->pluck('id_employee')->all();
            }
        }

        $getEmployeeRequest = DB::table('hr_request_header as hrh')
	            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hrh.id_employee_request')
	            ->select('he.id_employee', 'he.nik_employee', 'he.name')
	            ->where('hrh.id_company', session('id_company'));
	        if(count($id_employee) > 0){
	            $getEmployeeRequest->whereIn('hrh.id_employee_request', $id_employee);
	        }
	        $getEmployeeRequest->groupBy('he.id_employee');
	        $getEmployeeRequest = $getEmployeeRequest->get();

        $getReffNumberRequest = DB::table('hr_request_header as hrh')
	            ->select('hrh.id_request_header', 'hrh.reference_number')
	            ->where('hrh.id_company', session('id_company'));
            if(count($id_employee) > 0){
	            $getReffNumberRequest->whereIn('hrh.id_employee_request', $id_employee);
	        }
			$getReffNumberRequest = $getReffNumberRequest->get();

		$employeeRequest 	= [];
		$reffNumber 		= [];

		if(count($getEmployeeRequest) > 0){
			$getEmployeeRequest = $getEmployeeRequest->sortBy('name')->unique('nik_employee')->all();
			foreach ($getEmployeeRequest as $key => $val) {
				$employeeRequest[] = [
					'id' => $val->nik_employee,
					'text' => $val->name. ' ('.$val->nik_employee.')',
				];
			}
		}
		if(count($getReffNumberRequest) > 0){
			$getReffNumberRequest = $getReffNumberRequest->sortBy('reference_number')->values()->all();
			foreach ($getReffNumberRequest as $key => $val) {
				$reffNumber[] = [
					'id' => $val->id_request_header,
					'text' => $val->reference_number,
				];
			}
		}

        if ($request->ajax()) {
			$data_access = Employee::get_access($request->id_url);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}

			$startdate 	= $request->startdate ?? date('Y-m-d', strtotime($today."-14 days"));
			$enddate 	= $request->enddate ?? date('Y-m-d');
			$dateType 	= $request->date_type ?? 'creation_date';
			$nik = $request->employee ?? null;

            $data = RequestHeader::getdatareport($group_branch, $startdate, $enddate, $dateType, $nik);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('employee_name', function($row) {
                                return $row->employee_name;
                            })->addColumn('desc_request_type', function($row) {
                                return $row->desc_request_type;
                            })->addColumn('desc_app_status', function($row) {
                                return $row->desc_app_status;
                            })->addColumn('attachmentFile', function($row) {
                            	$storagePath = '';
    					// 		if(is_null($row->attachment_type)){
						   //      	if (Storage::exists('public/upload/employee_request/'. $row->attachment)) {
									// 	$storagePath = url('project/storage/app/public/upload/employee_request').'/'.$row->attachment;
									// } else {
									// 	$storagePath = url('project/storage/app/public/thumbnail').'/'.$row->attachment;
									// }
						   //      }
                                return $storagePath;
                            })
							->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$button = '<button style="color:white;" type="button" name="view" id="' .$data->id_request_header. '" class="view btn btn-warning btn-sm" title="View"><span class="far fa-eye fa-lg"></span><br><b>View</b></button> '; 
								                              
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
							
			
        }
        return view('employee.employee.employee_request_management.index', compact('employeeRequest','reffNumber'));
    }
	
	public function attachment(Request $request) {
        ini_set('max_execution_time', -1);
    	
        if ($request->ajax()) {
        	$nik 			= $request->nik ?? [];
        	$idRequestHeader= $request->id_request_header ?? [];

        	$get = DB::table('hr_request_header as hrh')
                    ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hrh.id_employee_request')
                    ->leftJoin('master_general_data as mgd', 'hrh.id_request_type', '=', 'mgd.id_general_data')
                    ->leftJoin('master_general_data as mgd2', 'hrh.id_approval_status', '=', 'mgd2.id_general_data')
                    ->leftJoin('master_leave_type as mlt', 'hrh.id_leave_type', '=', 'mlt.id_leave_type')
                    ->select('hrh.id_request_header', 'hrh.reference_number', 'he.nik_employee', 'he.name', 'hrh.reference_number', 'hrh.creation_date', 'mgd.description as request_type', 'mgd2.description as approval_status', 'hrh.attachment_type', 'hrh.attachment')
                    ->where('hrh.id_company', session('id_company'));
	        if(count($nik) > 0){
	        	$get->whereIn('he.nik_employee', $nik)->orWhereIn('hrh.id_request_header', $idRequestHeader);
	        }
	        if(count($idRequestHeader) > 0){
	        	$get->whereIn('hrh.id_request_header', $idRequestHeader)->orWhereIn('he.nik_employee', $nik);
	        }
	        $data = $get->get();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data) {
                        return '';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function loadAttachment(Request $request) {
        ini_set('max_execution_time', -1);
    	
    	$startdate 	= $request->startdate ?? date('Y-m-d', strtotime($today."-14 days"));
		$enddate 	= $request->enddate ?? date('Y-m-d');
		$enddate 	= date('Y-m-d', strtotime($enddate."+1 days")); //end-to harus ditambahi 1 hari agr sesuai tgl filternya, default dr laravel
		$dateType 	= $request->date_type ?? 'creation_date';

        $id_employee 	= [];
    	if(session('access_group') != 'Default_Administrator'){
            $path_menu 		= 'employee/employee_setting/workdays';
        	$id_branch      = [];
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu);
            if(count($get_ur) > 0){
                $get_ur = $get_ur[0];
                $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->id_user_responsibility);
                if(count($get_branch) > 0){
                    $id_branch        = $get_branch->pluck('id_branch')->all();
                }
            }
            if(count($id_branch) > 0){
                $id_employee = JobPositionDetail::whereIn('id_branch', $id_branch)->pluck('id_employee')->all();
            }
        }

        $getEmployeeRequest = DB::table('hr_request_header as hrh')
	            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hrh.id_employee_request')
	            ->leftJoin('hr_request_detail as hrd', 'hrh.id_request_header', '=', 'hrd.id_request_header')
	            ->select('he.id_employee', 'he.nik_employee', 'he.name')
	            ->where('hrh.id_company', session('id_company'));
        if(count($id_employee) > 0){
            $getEmployeeRequest->whereIn('hrh.id_employee_request', $id_employee);
        }
        if(in_array($dateType, ['request_start_to', 'request_end_to'])){
        	$getEmployeeRequest->whereBetween('hrd.'.$dateType,  [$startdate, $enddate]);
		} else {
        	$getEmployeeRequest->whereBetween('hrh.'.$dateType,  [$startdate, $enddate]);
		}
        $getEmployeeRequest->groupBy('he.id_employee');
        $getEmployeeRequest = $getEmployeeRequest->get();

        $getReffNumberRequest = DB::table('hr_request_header as hrh')
            ->leftJoin('hr_request_detail as hrd', 'hrh.id_request_header', '=', 'hrd.id_request_header')
            ->select('hrh.id_request_header', 'hrh.reference_number')
            ->where('hrh.id_company', session('id_company'));
        if(count($id_employee) > 0){
            $getReffNumberRequest->whereIn('hrh.id_employee_request', $id_employee);
        }
        if(in_array($dateType, ['request_start_to', 'request_end_to'])){
        	$getReffNumberRequest->whereBetween('hrd.'.$dateType,  [$startdate, $enddate]);
		} else {
        	$getReffNumberRequest->whereBetween('hrh.'.$dateType,  [$startdate, $enddate]);
		}
		$getReffNumberRequest = $getReffNumberRequest->get();

		$employeeRequest 	= [];
		$reffNumber 		= [];

		if(count($getEmployeeRequest) > 0){
			$getEmployeeRequest = $getEmployeeRequest->sortBy('name')->unique('nik_employee')->all();
			foreach ($getEmployeeRequest as $key => $val) {
				$employeeRequest[] = [
					'id' => $val->nik_employee,
					'text' => $val->name. ' ('.$val->nik_employee.')',
				];
			}
		}
		if(count($getReffNumberRequest) > 0){
			$getReffNumberRequest = $getReffNumberRequest->sortBy('reference_number')->values()->all();
			foreach ($getReffNumberRequest as $key => $val) {
				$reffNumber[] = [
					'id' => $val->id_request_header,
					'text' => $val->reference_number,
				];
			}
		}
		$return['employeeRequest'] = $employeeRequest;
		$return['reffNumber'] = $reffNumber;
		return response()->json($return);
    }
}
