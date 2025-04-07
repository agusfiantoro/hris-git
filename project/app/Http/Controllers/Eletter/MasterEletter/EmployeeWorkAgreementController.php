<?php

namespace App\Http\Controllers\Eletter\MasterEletter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Employee\Employee\Employee;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Eletter\MasterEletter\EmployeeWorkAgreement;
use Illuminate\Support\Facades\Log;

class EmployeeWorkAgreementController extends Controller
{
	public function pkk_index(Request $request)
	{
		if ($request->ajax()) {
			$data = EmployeeWorkAgreement::getpkk($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.pkk.index');
	}
	public function get_data_new(Request $request)
	{
		$category = EmployeeWorkAgreement::get_category();
		$position = EmployeeWorkAgreement::get_position();
		$employee = EmployeeWorkAgreement::get_employee($request);
		return response()->json([
			'category'=>$category,
			'position'=>$position,
			'employee'=>$employee
		]);
	}
	public function change_branch(Request $request)
	{
		$data = ElectronicLetter::change_branch_data($request);
		return response()->json($data);
	}
	public function change_employee(Request $request)
	{
		$data = EmployeeWorkAgreement::change_employee_pkwt($request);
		return response()->json($data);
	}
	public function get_position(Request $request)
	{
		$position = ElectronicLetter::get_position_data($request);
		return response()->json($position);
	}
	public function save(Request $request)
	{
		if (empty($request->remark_2)) {
			$request->validate([
				'remark_1' => 'required',
				'date' => 'required',
				'id_category' => 'required',
				'id_dept' => 'required',
				'id_branch' => 'required',
				'id_region' => 'required',
				'id_position_detail' => 'required',
				'id_job_grade' => 'required',
				'id_location' => 'required',
				'effective_date' => 'required',
				'email' => 'required|email',
				'notes' => 'required'
			],[
				'remark_1.required' => 'The Name field is required.',
				'date.required' => 'The Letter Date field is required.',
				'id_category.required' => 'The Category field is required.',
				'id_dept.required' => 'The Department field is required.',
				'id_branch.required' => 'The Branch field is required.',
				'id_region.required' => 'The Region field is required.',
				'id_position_detail.required' => 'The Position field is required.',
				'id_job_grade.required' => 'The Job Grade field is required.',
				'id_location.required' => 'The Location field is required.',
				'effective_date.required' => 'The Effective Date field is required.',
				'email.required' => 'The Email field is required.',
				'notes.required' => 'The Notes field is required.'
			]);
			$name = $request->remark_1;
		}else{
			$request->validate([
				'id_employee' => 'required',
				'date' => 'required',
				'id_category' => 'required',
				'id_dept' => 'required',
				'id_branch' => 'required',
				'id_region' => 'required',
				'id_position_detail' => 'required',
				'id_job_grade' => 'required',
				'id_location' => 'required',
				'effective_date' => 'required',
				'email' => 'required|email',
				'notes' => 'required'
			],[
				'id_employee.required' => 'The Name field is required.',
				'date.required' => 'The Letter Date field is required.',
				'id_category.required' => 'The Category field is required.',
				'id_dept.required' => 'The Department field is required.',
				'id_branch.required' => 'The Branch field is required.',
				'id_region.required' => 'The Region field is required.',
				'id_position_detail.required' => 'The Position field is required.',
				'id_job_grade.required' => 'The Job Grade field is required.',
				'id_location.required' => 'The Location field is required.',
				'effective_date.required' => 'The Effective Date field is required.',
				'email.required' => 'The Email field is required.',
				'notes.required' => 'The Notes field is required.'
			]);
			$name = $request->nama_employee;
		}
		try {
			DB::beginTransaction();
			$id_letter_type = EmployeeWorkAgreement::id_letter_type_save();
			$format = EmployeeWorkAgreement::format_surat($request);
			
			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> notes = $request->notes;
			$data -> id_employee = $request->id_employee;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_region = $request->id_region;
			// $data -> id_position_detail = $request->id_position_detail;
			$data -> id_job_grade = $request->id_job_grade;
			$data -> id_location = $request->id_location;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $name;
			$data -> remark_2 = $request->remark_2;
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> id_position_routing = $request->id_position_detail;
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Employee Work Agreement Succesfullly !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Employee Work Agreement !! [' . $e->getMessage() . ']']);
		}

	}
	public function get_edit(Request $request, $id_letter)
	{
		$data = EmployeeWorkAgreement::get_edit_pkk($id_letter);
		return response()->json($data);
	}
	public function update(Request $request)
	{
		if($request->cek_rehire == ''){
			$request->validate([
				'remark_1' => 'required',
				'effective_date' => 'required',
				'email' => 'required|email',
				'notes' => 'required'
			],[
				'remark_1.required' => 'The Name field is required.',
				'effective_date.required' => 'The Effective Date field is required.',
				'email.required' => 'The Email field is required.',
				'notes.required' => 'The Notes field is required.'
			]);
		}else{
			$request->validate([
				'effective_date' => 'required',
				'email' => 'required|email',
				'notes' => 'required'
			],[
				'effective_date.required' => 'The Effective Date field is required.',
				'email.required' => 'The Email field is required.',
				'notes.required' => 'The Notes field is required.'
			]);
		}
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();	
			$data -> notes = $request->notes;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response(['status'=>'true','message'=>'Employee Work Agreement Edit Succesfullly !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Employee Work Agreement !! [' . $e->getMessage() . ']']);
		}
		
	}
	public function destroy($id_letter)
	{
		if ($id_letter) {
			ElectronicLetter::where('id_letter',$id_letter)->delete();
		}
	}
}
