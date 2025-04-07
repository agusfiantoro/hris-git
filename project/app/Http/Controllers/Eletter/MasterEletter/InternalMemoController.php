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
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Eletter\MasterEletter\InternalMemo;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class InternalMemoController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = InternalMemo::get_im($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '';
				if($data->code != 'GM'){
					$button .= '<a href="javascript:void(0)" more_token="'.$data->token.'" more_id="'.$data->id_letter.'" name="mail" id="mail_send-'.$data->id_letter.'" more_type="IM" class="btn btn-info btn-mail btn-sm" title="Mail"><span class="fa fa-envelope"></span></a> ';
					$button .= '<a href="'.route('print.im',$data->token).'" target="_blank" name="print" id="" class="btn btn-success btn-print btn-sm" title="Print"><span class="fa fa-file-pdf"></span></a><br>';
				}
				$button .= '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm mt-1" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit mt-1" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.internal_memo.index');
	}
	public function get_career_im(Request $request)
	{
		if ($request->ajax()) {
			$data = InternalMemo::get_im_career($request);
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
	public function get_data_new(Request $request)
	{
		$category = InternalMemo::get_category();
		$employee = InternalMemo::get_employee($request);
		$region = InternalMemo::get_region();
		$position = InternalMemo::get_position();
		$dept = InternalMemo::get_dept();
		$division = InternalMemo::get_division();
		return response()->json([
			'category'=>$category,
			'employee'=>$employee,
			'region'=>$region,
			'position'=>$position,
			'dept'=>$dept,
			'division'=>$division
		]);
	}
	public function change_employee_ta(Request $request)
	{
		$data = InternalMemo::change_employee_ta($request);
		return response()->json($data);
	}
	public function change_region(Request $request)
	{
		$data = InternalMemo::changeRegion($request);
		return response()->json($data);
	}
	public function change_branch(Request $request)
	{
		$data = InternalMemo::changeBranch($request);
		return response()->json($data);
	}
	public function get_chief_name()
	{
		$employee = InternalMemo::get_chief_name();
		return response()->json($employee);
	}
	public function get_chief(Request $request)
	{
		$data = InternalMemo::get_position_chief($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
		$request->validate([
			'date' => 'required',
			'id_category' => 'required',
			'id_employee' => 'required',
			'id_dept' => 'required',
			'id_branch' => 'required',
			'id_region' => 'required',
			'id_position_detail' => 'required',
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
			'remark_6' => 'required',
			// 'remark_2' => 'max:255',
			// 'remark_3' => 'max:255',
			'email' => 'required|email'
			// 'id_career_transaction' => 'required'
		],[
			'date.required' => 'The Letter Date field is required.',
			'id_category.required' => 'The Category field is required.',
			'id_employee.required' => 'The Name field is required.',
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
			'remark_6.required' => 'The Cc field is required.',
			// 'remark_2.max' => 'The Tugas Tanggung Jawab must not be greater than 255 characters.',
			// 'remark_3.max' => 'The Tunjangan must not be greater than 255 characters.',
			'email.required' => 'The Email field is required.'
			// 'id_career_transaction.required' => 'The Reference Number Career field is required.'
		]);
		$category = MasterGeneralData::where('id_general_data',$request->id_category)->first();
		if ($category->code == 'TA') {
			$request->validate([
				'remark_2' => 'required',
				// 'remark_3' => 'required'
			],[
				'remark_2.required' => 'The Tugas Tanggung Jawab field is required.',
				// 'remark_3.required' => 'The Tunjangan field is required.'
			]);
		}
		try {
			DB::beginTransaction();
			$result = InternalMemo::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];
			$id = $result['id'];
			$token = $result['token'];
			$principalTextValue = $result['principalTextValue'];

			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> id_employee = $request->id_employee;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_region = $request->id_region;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_principal = $principalTextValue;
			$data -> id_location = $request->id_location;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> id_dept_new = $request->id_dept_new;
			$data -> id_branch_new = $request->id_branch_new;
			if ($category->code == 'TA' || $category->code == 'GM') {
				if (isset($request->remark_8)) {
					$data -> id_position_detail_new = $request->id_position_detail_new;
				}else{
					$position = DB::table('master_position_detail')->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
					->select(
						\DB::RAW('master_position_detail.id_position_detail as id_position_detail')
					)
					->where('master_position_detail.id_position_routing',$request->id_position_detail_new)
					->where('master_position_detail.status','A')
					->where('master_position_detail.id_company',session('id_company'))
					->limit('1')
					->first();
					$data -> id_position_detail_new = $position->id_position_detail;
					$data -> id_position_routing = $request->id_position_detail_new;
				}
			}else{
				$data -> id_position_detail_new = $request->id_position_detail_new;
			}
			$data -> id_regional_new = $request->id_region_new;
			$data -> id_principal_new = $id;
			$data -> id_location_new = $request->id_location_new;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_2 = $request->remark_2;
			$data -> remark_3 = $request->remark_3;
			$data -> remark_4 = $request->remark_4;
			$data -> remark_5 = $request->remark_5;
			$data -> remark_6 = $request->remark_6;
			$data -> token = $token;
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> id_career_transaction = $request->id_career_transaction;
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Internal Memo Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Internal Memo !! [' . $e->getMessage() . ']']);
		}
	}

	public function get_edit(Request $request, $id_letter)
	{
		$data = InternalMemo::get_edit_im($id_letter);
		$array = $data->id_principal_new;
		$string = explode(',', trim($array,'{}'));
		$principal = InternalMemo::get_principal($string);
		return response()->json([
			'data'=>$data,
			'principal'=>$principal
		]);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'id_category' => 'required',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'effective_date' => 'required',
			'remark_6' => 'required',
			// 'remark_2' => 'max:255',
			// 'remark_3' => 'max:255',
			'email' => 'required|email'
		],[
			'id_category.required' => 'The Category field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'remark_6.required' => 'The Cc field is required.',
			// 'remark_2.max' => 'The Tugas Tanggung Jawab must not be greater than 255 characters.',
			// 'remark_3.max' => 'The Tunjangan must not be greater than 255 characters.',
			'email.required' => 'The Email field is required.'
		]);
		$category = MasterGeneralData::where('id_general_data',$request->id_category)
		->where('id_company',session('id_company'))
		->first();
		if ($category->code == 'TA') {
			$request->validate([
				'remark_2' => 'required',
				'remark_3' => 'required'
			],[
				'remark_2.required' => 'The Tugas Tanggung Jawab field is required..',
				'remark_3.required' => 'The Tunjangan field is required..'
			]);
		}
		try {
			DB::beginTransaction();

			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			$data -> id_category = $request->id_category;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_2 = $request->remark_2;
			$data -> remark_3 = $request->remark_3;
			$data -> remark_4 = $request->remark_4;
			$data -> remark_5 = $request->remark_5;
			$data -> remark_6 = $request->remark_6;
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Internal Memo Edit Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Internal Memo !! [' . $e->getMessage() . ']']);
		}
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::where('id_letter',$id_letter)->first();
		if ($data) {
			$data->delete();
		}
	}
	public function print_im(Request $request, $token)
	{
		$data = InternalMemo::get_im_pdf($token);
		$array = $data->id_principal_new;
		$string = explode(',', trim($array,'{}'));
		$principal = InternalMemo::get_principal_print($string);
		if ($data->code_category == "TA") {
			$path = "eletter.internal_memo.print.tugas_sementara";
		}elseif ($data->code_category == "ORIENT") {
			$path = "eletter.internal_memo.print.orient";
		}elseif ($data->code_category == "MUTA") {
			$path = "eletter.internal_memo.print.mutasi";
		}elseif ($data->code_category == "FARIENT") {
			$path = "eletter.internal_memo.print.failed_orient";
		}else{
			$path = '';
		}
		$pdf=PDF::loadview($path,compact('data','principal','string'))->setPaper('A4','potrait');
		return $pdf->stream();
	}

}
