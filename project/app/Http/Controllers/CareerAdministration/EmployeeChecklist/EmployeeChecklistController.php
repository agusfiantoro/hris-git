<?php

namespace App\Http\Controllers\CareerAdministration\EmployeeChecklist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeSetting\MasterChecklist;
use App\Models\CareerAdministration\MasterBoarding\EmployeeChecklist;
use App\Models\CareerAdministration\MasterBoarding\RelationChecklist;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EmployeeChecklistController extends Controller {

    public function index(Request $request) {
        return view('career_administration.employee_checklist.offboarding.index');
    }
	protected function accessBranch(Request $request) {
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

			return $group_branch;
	}
    public function get_data(Request $request) {
        $data = EmployeeChecklist::getdata($this->accessBranch($request));
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_checklist_employee . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                         //   $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_checklist_employee . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    protected function validateChecklist(Request $request) {
       $request->validate([              
			'id_employee' => 'required',
			'effective_date' => 'required',
			'id_checklist' => 'required',
				], [],
				[                      
			   'id_employee' => 'Employee',
			   'effective_date' => 'Effective Date',
			   'id_checklist' => 'Checklist Type',
		]);
    }

    public function save(Request $request) {
	//	dd($request->compen);
        $this->validateChecklist($request);
        try{
        DB::beginTransaction();
		if($request->attachment != ""){
			$nik_employee = Employee::where('id_employee', $request->id_employee)->first();
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/offboarding/'.$nik_employee['nik_employee'],0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/offboarding/'.$nik_employee['nik_employee'],$request->attachment,$image);
			$is_upload = 'true';
		}
		else{
			$image = NULL;
			$is_upload = 'false';
		}
		$codeid = EmployeeChecklist::getkode_ofb();
			$form_data = [
				'id_checklist' => $request->id_checklist,
				'reference_number' => $codeid,
				'id_employee' => $request->id_employee,
				'remark' => $request->remark,
				'effective_date' => $request->effective_date,
				'compensation_amount' => $request->compen != '' ? preg_replace("/[^0-9]/", "",$request->compen) : null,
				'attachment' => $image,
				'completed' => $is_upload,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			];
			$result = EmployeeChecklist::create($form_data);
		if($request->assigned_hr != null){	
				foreach ($request->assigned_hr as $key => $value) {
				RelationChecklist::create(array(
					'id_checklist_employee' => $result->id_checklist_employee,
					'id_position_detail_assigned' => $value,
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				));
			}
		}
		DB::commit();
		 return response()->json(['status' => 'true', 'message' => 'Off Boarding Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Off Boarding !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function update(Request $request) {
        $this->validateChecklist($request);
        try{
        DB::beginTransaction();
		$form_data = [
				'id_checklist' => $request->id_checklist,
				'id_employee' => $request->id_employee,
				'remark' => $request->remark,
				'effective_date' => $request->effective_date,
				'compensation_amount' => $request->compen != '' ? preg_replace("/[^0-9]/", "",$request->compen) : null,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			];	
		if($request->attachment != ""){
			$nik_employee = Employee::where('id_employee', $request->id_employee)->first();
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/offboarding/'.$nik_employee['nik_employee'],0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/offboarding/'.$nik_employee['nik_employee'],$request->attachment,$image);
			$form_data['attachment'] = $image;
			$form_data['completed'] = 'true';
		}
			$result = EmployeeChecklist::where('id_checklist_employee',$request->id_checklist_employee)->update($form_data);
			if($request->assigned_hr != null){
				RelationChecklist::where('id_checklist_employee', $request->id_checklist_employee)->delete();
				foreach ($request->assigned_hr as $key => $value) {
					RelationChecklist::create(array(
						'id_checklist_employee' => $request->id_checklist_employee,
						'id_position_detail_assigned' => $value,
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
						'updated_by' => session('id_user'),
					));
				}
			}
		DB::commit();
		 return response()->json(['status' => 'true', 'message' => 'Off Boarding Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Off Boarding !! [' . $e->getMessage() . ']']);           
        }
    }

    public function get_detail_offboarding(Request $request) {
        $data = [
            'id_checklist_employee' => $request->id_checklist_employee
        ];
        $result = EmployeeChecklist::get_detail_offboarding($data);
        return response()->json($result);
    }
	
	public function get_type() {
        $result = EmployeeChecklist::get_type();
        return response()->json($result);
    }
	
	public function get_employee(Request $request) {
        $result = EmployeeChecklist::get_employee($request->type);
        return response()->json($result);
    }
	
	public function get_hr() {
        $result = EmployeeChecklist::get_hr();
        return response()->json($result);
    }
	
	public function report(Request $request) {
        return view('career_administration.employee_checklist.offboarding.report');
    }
	public function get_report(Request $request) {
        if ($request->ajax()) {
		//	dd($request->all());
			$cat_date 				= $request->cat_date ?? null;
			$startdate 				= $request->startdate ?? null;
			$enddate 				= $request->enddate ?? null;
            $data = EmployeeChecklist::get_report($cat_date,$startdate,$enddate,$this->accessBranch($request));
            return DataTables::of($data)
                            ->addIndexColumn()
							->addColumn('attachment_custom', function($data){
                            	$storagePath = '';
                            	if(!is_null($data->attachment)){
	                                if (Storage::exists('public/upload/offboarding/'.$data->nik_employee.'/'.$data->attachment)) {
										$storagePath = url('project/storage/app/public/upload/offboarding').'/'.$data->nik_employee.'/'.$data->attachment;
									}
                            	}
                                return $storagePath;
							})
                            ->make(true);
        }
    }

    public function check_clearence(Request $request) {
    	try{
	    	$idEmployee = $request->id_employee ?? null;
	        $result = EmployeeChecklist::check_clearence($idEmployee);
	        
        	return response()->json(['status' => true, 'message' => 'Berhasil', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

}
