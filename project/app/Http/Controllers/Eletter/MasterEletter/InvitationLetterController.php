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
use App\Models\Eletter\MasterEletter\InvitationLetter;
use Illuminate\Support\Facades\Log;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvitationLetterController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = InvitationLetter::get_supa($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<a href="javascript:void(0)" more_token="'.$data->token.'" more_id="'.$data->id_letter.'" name="mail" more_type="SUPA" id="mail_send-'.$data->id_letter.'" class="btn btn-info btn-sm btn-mail" title="Mail"><span class="fa fa-envelope"></span></a> ';
				$button .= '<a href="'.route('print.supa',$data->token).'" target="_blank" name="print" id="" class="btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></a><br>';
				$button .= '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn-edit btn btn-primary btn-sm mt-1" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.invitation_letter.index');
	}
	public function get_supa2_data(Request $request)
	{
		if ($request->ajax()) {
			$data = InvitationLetter::get_supa2($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('action', function($data) {
				$button = '<button type="button" more_id="'.$data->id_letter.'" class="btn btn-success btn-sm select_supa_2" title="Select Supa Number"><span class="fas fa-check"></span></button> ';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
	}
	public function get_data(Request $request)
	{
		$category = InvitationLetter::get_category();
		$location = InvitationLetter::get_location();
		$employee = InvitationLetter::get_employee();
		$supa1 = InvitationLetter::table_view_supa1($request);
		return response()->json(['category'=>$category,'employee'=>$employee,'supa1'=>$supa1,'location'=>$location]);
	}
	public function select_supa1(Request $request)
	{
		$data = InvitationLetter::select_change_supa1($request);
		return response()->json($data);
	}
	public function select_supa2(Request $request)
	{
		$data = InvitationLetter::select_change_supa2($request);
		return response()->json($data);
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
			'date' => 'required',
			'effective_date' => 'required',
			'expired_date' => 'required',
			'email' => 'required|email',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_1' => 'required',
			'remark_2' => 'required',
			'remark_3' => 'required',
			'remark_5' => 'required',
			'remark_6' => 'required',
			'remark_8' => 'required',
			'hari' => 'required',
			'tanggal_panggil' => 'required',
			'waktu' => 'required',
			'zona' => 'required'
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
			'expired_date.required' => 'The Expired Date field is required.',
			'remark_1.required' => 'The Nama HRBP/Atasan field is required.',
			'remark_2.required' => 'The Jabatan HRBP/Atasan field is required.',
			'remark_3.required' => 'The Alamat field is required.',
			'remark_5.required' => 'The Tempat Panggil field is required.',
			'remark_6.required' => 'The Kota field is required.',
			'remark_8.required' => 'The Letter Location field is required.',
			'hari.required' => 'The Hari field is required.',
			'tanggal_panggil.required' => 'The Tanggal Panggil field is required.',
			'waktu.required' => 'The Waktu field is required.',
			'zona.required' => 'The Zona field is required.',
			'email.required' => 'The Email field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = InvitationLetter::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];
			$remark_4 = $result['remark_4'];
			$token = $result['token'];

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
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_2 = $request->remark_2;
			$data -> remark_3 = $request->remark_3;
			$data -> remark_4 = $remark_4;
			$data -> remark_5 = $request->remark_5;
			$data -> remark_6 = $request->remark_6;
			$data -> remark_7 = $request->remark_7;
			$data -> remark_8 = $request->remark_8;
			$data -> token = $token; 
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Invitation Letter Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Invitation Letter !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_letter)
	{
		$data = InvitationLetter::get_edit_supa($id_letter);
		if ($data->code == 'SUPA2') {
			$number_supa1 = ElectronicLetter::join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
			->select(
				\DB::RAW('hr_electronic_letter.reference_number as reference_number_supa1')
			)
			->where('hr_electronic_letter.id_employee',$data->id_employee)
			->where('master_general_data.code','SUPA1')
			->where('hr_electronic_letter.id_company',session('id_company'))
			->orderBy('hr_electronic_letter.id_letter','DESC')
			->first();
			$no_supa_1 = $number_supa1->reference_number_supa1;
		}else{
			$no_supa_1 = NULL;
		}
		$remark_4 = $data->remark_4;
		$explode = explode(" ", $remark_4);
		$hari = $explode[0];
		$tanggal_panggil = $explode[1];
		$waktu = $explode[2];
		$zona = $explode[3];
		return response()->json([
			'data'=>$data,
			'hari'=>$hari,
			'tanggal_panggil'=>$tanggal_panggil,
			'waktu'=>$waktu,
			'zona'=>$zona,
			'no_supa_1'=>$no_supa_1
		]);
	}
	public function edit(Request $request)
	{
		$request->validate([
			// 'date' => 'required',
			'effective_date' => 'required',
			'expired_date' => 'required',
			'email' => 'required|email',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_1' => 'required',
			'remark_2' => 'required',
			'remark_3' => 'required',
			'remark_5' => 'required',
			'remark_6' => 'required',
			'remark_8' => 'required',
			'hari' => 'required',
			'tanggal_panggil' => 'required',
			'waktu' => 'required',
			'zona' => 'required'
		],[
			// 'date.required' => 'The Letter Date field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'expired_date.required' => 'The Expired Date field is required.',
			'remark_1.required' => 'The Nama HRBP/Atasan field is required.',
			'remark_2.required' => 'The Jabatan HRBP/Atasan field is required.',
			'remark_3.required' => 'The Alamat field is required.',
			'remark_5.required' => 'The Tempat Panggil field is required.',
			'remark_6.required' => 'The Kota field is required.',
			'remark_8.required' => 'The Letter Location field is required.',
			'hari.required' => 'The Hari field is required.',
			'tanggal_panggil.required' => 'The Tanggal Panggil field is required.',
			'waktu.required' => 'The Waktu field is required.',
			'zona.required' => 'The Zona field is required.',
			'email.required' => 'The Email field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			if ($data) {
				$remark_4 = $request->hari.' '.$request->tanggal_panggil.' '.$request->waktu.' '.$request->zona;
				// $data -> date = $request->date;
				$data -> effective_date = $request->effective_date;
				$data -> expired_date = $request->expired_date;
				$data -> email = $request->email;
				$data -> remark_1 = $request->remark_1;
				$data -> remark_2 = $request->remark_2;
				$data -> remark_3 = $request->remark_3;
				$data -> remark_4 = $remark_4;
				$data -> remark_5 = $request->remark_5;
				$data -> remark_6 = $request->remark_6;
				$data -> remark_7 = $request->remark_7;
				$data -> remark_8 = $request->remark_8;
				$data -> id_employee_chief = $request->id_employee_chief;
				$data -> id_position_routing_chief = $request->id_position_routing_chief;
				$data -> id_company = session('id_company');
				$data -> updated_by = session('id_user');
				$data -> save();
				DB::commit();
				return response()->json(['status'=>'true','message'=>'Invitation Letter Edit Succesfully !!']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Invitation Letter !! [' . $e->getMessage() . ']']);
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
			->whereIn('master_general_data.code',['SUPA1','SUPA2'])
			->orderBy('hr_electronic_letter.id_letter','DESC')
			->first();
			if ($data->code == $cek->code) {
				$data -> delete();
				return response()->json(['status'=>'success']);
			}else{
				return response()->json(['status'=>'warning','message'=>'There is still '.$cek->code. ' data. Deletion must be sequential!']);
			}
		}
	}
	public function print($token)
	{
		$data = InvitationLetter::print_supa($token);
		if (!empty($data)) {
			$remark_4 = $data->remark_4;
			$explode = explode(" ", $remark_4);
			$hari = $explode[0];
			$tanggal_panggil = $explode[1];
			$waktu = $explode[2];
			$zona = $explode[3];
			$tempat = $data->remark_5;
			// $branch = InvitationLetter::branch_print();
			$pdf=PDF::loadview('eletter.invitation_letter.print',compact('data','hari','tanggal_panggil','waktu','zona', 'tempat'))->setPaper('A4','potrait');
			return $pdf->stream();
		}
	}

}
