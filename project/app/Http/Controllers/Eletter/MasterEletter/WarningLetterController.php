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
use Illuminate\Support\Facades\Log;
use App\Models\Eletter\MasterEletter\WarningLetter;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WarningLetterController extends Controller
{
	public function index(Request $request)
	{
    	// Kategory -> SP1-26 , SP2-27, SP3-28, SPDT-29
		// $allowedAccess = [1954];
		// if(!in_array(session('id_user'), $allowedAccess)) {
		// 	return response("<script>alert('Pembuatan Nomor Surat SP Ditutup Sampai Hari Senin');window.history.back();</script>");
		// }
		if ($request->ajax()) {
			$data = WarningLetter::get_sp($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<a href="javascript:void(0)" more_token="'.$data->token.'" more_id="'.$data->id_letter.'" name="mail" more_type="SP" id="mail_send-'.$data->id_letter.'" class="btn btn-info btn-sm btn-mail" title="Mail"><span class="fa fa-envelope"></span></a> ';
				$button .= '<a href="'.route('print.sp',$data->token).'" target="_blank" name="print" id="" class="btn btn-success btn-print btn-sm" title="Print"><span class="fa fa-file-pdf"></span></a><br>';
				$button .= '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm mt-1" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit mt-1" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		WarningLetter::cek_exp_sp();
		return view('eletter.warning_letter.index');
	}
	public function get_data(Request $request)
	{
		$category = WarningLetter::get_category();
		$location = WarningLetter::get_location();
		$employee = WarningLetter::get_employee_chief();
		return response()->json(['category'=>$category,'employee'=>$employee,'location'=>$location]);
	}
	public function get_category(Request $request)
	{
		$employee = WarningLetter::get_sp_employee($request);
		return response()->json(['employee'=>$employee]);
	}
	public function change_employee(Request $request)
	{
		$data = WarningLetter::change_employee_sp($request);
		return response()->json([
			'data'=>$data
		]);
	}
	public function save(Request $request)
	{
		$request->validate([
			'id_category' => 'required',
			'id_employee' => 'required',
			'id_region' => 'required',
			'id_dept' => 'required',
			'id_branch' => 'required',
			'id_position_detail' => 'required',
			'id_principal' => 'required',
			'id_job_grade' => 'required',
			'id_employment_status' => 'required',
			'date' => 'required',
			'effective_date' => 'required',
			'expired_date' => 'required',
			'email' => 'required|email',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			// 'remark_3' => 'required|max:255',
			// 'remark_4' => 'required|max:255',
			'remark_5' => 'required'
		],[
			'date.required' => 'The Letter Date field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'expired_date.required' => 'The Expired Date field is required.',
			'email.required' => 'The Email field is required.',
			'remark_3.required' => 'The Kejadian field is required.',
			// 'remark_3.max' => 'The Kejadian must not be greater than 255 characters.',
			'remark_4.required' => 'The Pelanggaran field is required.',
			// 'remark_4.max' => 'The Pelanggaran must not be greater than 255 characters.',
			'remark_5.required' => 'The Letter Location field is required.',
			'id_category.required' => 'The Category field is required.',
			'id_employee.required' => 'The Name field is required.',
			'id_dept.required' => 'The Department field is required.',
			'id_branch.required' => 'The Branch field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_position_detail.required' => 'The Position field is required.',
			'id_job_grade.required' => 'The Job Grade field is required.',
			'id_principal.required' => 'The Principal field is required.',
			'id_location.required' => 'The Location field is required.',
			'id_employment_status.required' => 'The Status field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = WarningLetter::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];
			$principalTextValue = $result['principalTextValue'];
			$token = $result['token'];

			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> id_employee = $request->id_employee;
			$data -> id_employment_status = $request->id_employment_status;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_region = $request->id_region;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_job_grade = $request->id_job_grade;
			$data -> id_principal = $principalTextValue;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_2 = $request->remark_2;
			$data -> remark_3 = $request->remark_3;
			$data -> remark_4 = $request->remark_4;
			$data -> remark_5 = $request->remark_5;
			$data -> token = $token;
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Warning Letter Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Warning Letter !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_letter)
	{
		$data = WarningLetter::get_edit_sp($id_letter);
		return response()->json($data);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'id_employment_status' => 'required',
			'date' => 'required',
			'expired_date' => 'required',
			'email' => 'required|email',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			// 'remark_3' => 'required|max:255',
			// 'remark_4' => 'required|max:255',
			'remark_5' => 'required'
		],[
			'date.required' => 'The Letter Date field is required.',
			'expired_date.required' => 'The Expired Date field is required.',
			'email.required' => 'The Email field is required.',
			'remark_3.required' => 'The Kejadian field is required.',
			// 'remark_3.max' => 'The Kejadian must not be greater than 255 characters.',
			'remark_4.required' => 'The Pelanggaran field is required.',
			// 'remark_4.max' => 'The Pelanggaran must not be greater than 255 characters.',
			'remark_5.required' => 'The Letter Location field is required.',
			'id_employment_status.required' => 'The Status field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			$data -> date = $request->date;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_2 = $request->remark_2;
			$data -> remark_3 = $request->remark_3;
			$data -> remark_4 = $request->remark_4;
			$data -> remark_5 = $request->remark_5;
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Warning Letter Edit Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Warning Letter !! [' . $e->getMessage() . ']']);
			
		}
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_employee as id_employee')
		)
		->where('hr_electronic_letter.id_letter',$id_letter)
		->first();
		if ($data) {
			$cek = ElectronicLetter::join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
			->select(
				\DB::RAW('master_general_data.code as code')
			)
			->where('hr_electronic_letter.id_employee',$data->id_employee)
			->where('master_general_data.id_company',session('id_company'))
			->whereIn('master_general_data.code',['SP1','SP2','SP3'])
			->orderBy('hr_electronic_letter.id_letter','DESC')
			->first();
			if ($data->code != "SPDT") {
				if ($data->code == $cek->code) {
					$data -> delete();
					return response()->json(['status'=>'success']);
				}else{
					return response()->json(['status'=>'warning','message'=>'There is still '.$cek->code. ' data. Deletion must be sequential!']);
				}
			}else{
				$data -> delete();
				return response()->json(['status'=>'success']);
			}
		}
	}
	public function get_view($id_letter)
	{
		$data = WarningLetter::get_view_sp($id_letter);
		return response()->json(['data'=>$data]);
	}
	public function print($token)
	{
		$data = WarningLetter::print_sp($token);
		// $branch = WarningLetter::branch_print();
		$pdf=PDF::loadview('eletter.warning_letter.print',compact('data'))->setPaper('A4','potrait');
		return $pdf->stream();
	}
}
