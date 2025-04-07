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
use App\Models\Organization\MasterOrganization\MasterDivision;
use Illuminate\Support\Facades\Log;
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Eletter\MasterEletter\StatementLetter;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StatementLetterController extends Controller
{
	public function index(Request $request)
	{
		//  $allowedAccess = [1954,9];
		//  if(!in_array(session('id_user'), $allowedAccess)) {
		//  	return response("<script>alert('Menu SK ditutup sementara untuk maintenance. Menu ini dapat dibuka kembali pada Senin, 13 Januari 2025.');window.history.back();</script>");
		//  }
		if ($request->ajax()) {
			$data = StatementLetter::get_sk($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<a href="javascript:void(0)" more_token="'.$data->token.'" more_id="'.$data->id_letter.'" name="mail" more_type="SK" id="mail_send-'.$data->id_letter.'" class="btn btn-info btn-sm btn-mail" title="Mail"><span class="fa fa-envelope"></span></a> ';
				$button .= '<a href="'.route('print.sk',$data->token).'" target="_blank" name="print" id="" class="btn-print btn btn-success btn-sm" title="Print"><span class="fa fa-file-pdf"></span></a><br>';
				$button .= '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm mt-1" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit mt-1" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.statement_letter.index');
	}
	public function get_career(Request $request)
	{
		if ($request->ajax()) {
			$data = StatementLetter::get_sk_career($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('action', function($data) {
				$button = '<button type="button" more_id="'.$data->id_career_transaction.'" class="btn btn-success btn-sm choose_career" title="Pilih Karir"><span class="fas fa-check"></span></button> ';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
	}
	public function get_data(Request $request)
	{
		$category = StatementLetter::get_category();
		$location = StatementLetter::get_location();
		$employee = StatementLetter::get_employee($request);
		return response()->json(['category'=>$category,'employee'=>$employee,'location'=>$location]);
	}
	public function get_employee(Request $request)
	{
		// Employee Chief
		$employee = Employee::select(
			\DB::RAW('id_employee'),
			\DB::RAW('nik_employee')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->where('id_employee',$request->employee_id)
		->first();
		return response()->json($employee);
	}
	public function change_career(Request $request)
	{
		$data = StatementLetter::change_career_sk($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
		$request->validate([
			'date' => 'required',
			'id_category' => 'required',
			'id_employee' => 'required',
			'id_employment_status' => 'required',
			'id_dept' => 'required',
			'id_branch' => 'required',
			'id_region' => 'required',
			'id_position_detail' => 'required',
			'id_job_grade' => 'required',
			'id_principal' => 'required',
			'id_location' => 'required',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'id_dept_new' => 'required',
			'id_branch_new' => 'required',
			'id_position_detail_new' => 'required',
			'id_region_new' => 'required',
			'id_principal_new' => 'required',
			'id_location_new' => 'required',
			'effective_date' => 'required',
			'remark_1' => 'required',
			'remark_8' => 'required',
			'remark_9' => 'required',
			'email' => 'required|email',
			'id_career_transaction' => 'required'
		],[
			'date.required' => 'The Letter Date field is required.',
			'id_category.required' => 'The Category field is required.',
			'id_employee.required' => 'The Name field is required.',
			'id_employment_status.required' => 'The Status field is required.',
			'id_dept.required' => 'The Department field is required.',
			'id_branch.required' => 'The Branch field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_position_detail.required' => 'The Position field is required.',
			'id_job_grade.required' => 'The Job Grade field is required.',
			'id_principal.required' => 'The Principal field is required.',
			'id_location.required' => 'The Location field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'id_dept_new.required' => 'The New Department field is required.',
			'id_branch_new.required' => 'The New Branch field is required.',
			'id_position_detail_new.required' => 'The New Position field is required.',
			'id_region_new.required' => 'The New Region field is required.',
			'id_principal_new.required' => 'The New Principal field is required.',
			'id_location_new.required' => 'The New Location field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'remark_1.required' => 'The Letter Location field is required.',
			'remark_8.required' => 'The Old Area field is required.',
			'remark_9.required' => 'The New Area field is required.',
			'email.required' => 'The Email field is required.',
			'id_career_transaction.required' => 'The Reference Number Career field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = StatementLetter::format_save($request);
			$format = $result['format'];
			$token = $result['token'];
			$id_letter_type = $result['id_letter_type'];
			$principalTextValue = $result['principalTextValue'];
			$id = $result['id'];

			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> notes = $request->notes;
			$data -> id_employee = $request->id_employee;
			$data -> id_employment_status = $request->id_employment_status;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_region = $request->id_region;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_job_grade = $request->id_job_grade;
			$data -> id_principal = $principalTextValue;
			$data -> id_location = $request->id_location;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> id_dept_new = $request->id_dept_new;
			$data -> id_branch_new = $request->id_branch_new;
			$data -> id_position_detail_new = $request->id_position_detail_new;
			$data -> id_regional_new = $request->id_region_new;
			$data -> id_principal_new = $id;
			$data -> id_location_new = $request->id_location_new;
			$data -> effective_date = $request->effective_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_8 = $request->remark_8;
			$data -> remark_9 = $request->remark_9;
			$data -> token = $token;
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> id_career_transaction = $request->id_career_transaction;
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Statement Letter Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Statement Letter !! [' . $e->getMessage() . ']']);
		}
		
	}
	public function get_edit(Request $request, $id_letter)
	{
		$data = StatementLetter::get_edit_sk($id_letter);
		$array = $data->id_principal_new;
		$string = explode(',', trim($array,'{}'));
		$principal = StatementLetter::get_principal($string);
		return response()->json(['data'=>$data,'principal'=>$principal]);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'effective_date' => 'required',
			'remark_1' => 'required',
			'remark_8' => 'required',
			'remark_9' => 'required',
			'email' => 'required|email'
		],[
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'remark_1.required' => 'The Letter Location field is required.',
			'remark_8.required' => 'The Old Area field is required.',
			'remark_9.required' => 'The New Area field is required.',
			'email.required' => 'The Email field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			if ($data) {
				$data -> notes = $request->notes;
				$data -> id_employee_chief = $request->id_employee_chief;
				$data -> id_position_routing_chief = $request->id_position_routing_chief;
				$data -> effective_date = $request->effective_date;
				$data -> remark_1 = $request->remark_1;
				$data -> remark_8 = $request->remark_8;
				$data -> remark_9 = $request->remark_9;
				$data -> email = $request->email;
				$data -> id_company = session('id_company');
				$data -> updated_by = session('id_user');
				$data -> save();
				DB::commit();
				return response()->json(['status'=>'true','message'=>'Statement Letter Edit Succesfully !!']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Statement Letter !! [' . $e->getMessage() . ']']);
		}
	}
	public function print(Request $request, $token)
	{
		$data = StatementLetter::get_print_sk($token);
		// $branch = StatementLetter::branch_print();
		$array = $data->id_principal_new;
		$string = explode(',', trim($array,'{}'));
		$principal = StatementLetter::get_principal($string);
		if ($data->code_category == "PEN") {
			$path = "eletter.statement_letter.print.pen";
		}elseif ($data->code_category == "REL") {
			$path = "eletter.statement_letter.print.relocation";
		}elseif ($data->code_category == "ROT") {
			$path = "eletter.statement_letter.print.rotation";
		}elseif ($data->code_category == "DEM") {
			$path = "eletter.statement_letter.print.demotion";
		}elseif ($data->code_category == "PRO") {
			$path = "eletter.statement_letter.print.promotion";
		}elseif ($data->code_category == "MUT") {
			$path = "eletter.statement_letter.print.mutation";
		}else{
			$path = '';
		}
		$pdf=PDF::loadview($path,compact('data','principal'))->setPaper('A4','potrait');
		return $pdf->stream();
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::where('id_letter',$id_letter)->first();
		if ($data) {
			$data -> delete();
		}
	}
}
