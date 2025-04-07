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
use App\Models\Eletter\MasterEletter\NotifLetter;
use App\Models\Eletter\MasterEletter\MasterGeneralLetter;
use PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NotifLetterController extends Controller
{
	public function index(Request $request)
	{
		// $allowedAccess = [1954];
		// if(!in_array(session('id_user'), $allowedAccess)) {
		// 	return response("<script>alert('Menu Notification Letter ditutup sementara untuk maintenance. Menu ini dapat dibuka kembali pada Jumat, 19 Juli 2024.');window.history.back();</script>");
		// }
		if ($request->ajax()) {
			$data = NotifLetter::get_notif_etter($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				if ($data->category_code == "SPB-PHK") {
					$file = asset('project/storage/app/public/spb_phk/SPB_PHK.doc');
				}else{
					$file = '';
				}
				$button = '';
				$button .= '<a href="javascript:void(0)" more_type="SPB" more_token="'.$data->token.'" more_id="'.$data->id_letter.'" name="mail" id="mail_send-'.$data->id_letter.'" class="btn btn-info btn-sm btn-mail" title="Mail"><span class="fa fa-envelope"></span></a>';
				if ($data->category_code == 'SPB-PKWT') {
					$button .= ' <a href="'.route('print.spb',$data->token).'" target="_blank" name="print" id="" class="btn-print btn btn-success btn-sm" more_id="'.$data->id_letter.'" title="Print"><span class="fa fa-file-pdf"></span></a>';
				}else{
					$button .= ' <a href="'.$file.'" download="" name="download" id="" class="btn btn-success btn-sm" title="Download"><span class="fa fa-download"></span></a>';
				}
				$button .= '<br><button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm mt-1" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit mt-1" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.notif_letter.index');
	}
	public function get_data(Request $request)
	{
		$user = NotifLetter::get_user($request);
		$category = NotifLetter::get_category();
		$location = NotifLetter::get_location();
		return response()->json(['user'=>$user,'category'=>$category,'location'=>$location]);
	}
	public function change_employee(Request $request)
	{
		$data = NotifLetter::change_employee_notifletter($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
		$request->validate([
			'date' => 'required|date',
			'id_category' => 'required',
			'id_employee' => 'required',
			'id_region' => 'required',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_2' => 'required',
			'remark_4' => 'required',
			'email' => 'required|email'
		],[
			'date.required' => 'The Letter Date field is required.',
			'id_category.required' => 'The Category field is required.',
			'id_employee.required' => 'The Name field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'remark_2.required' => 'The Letter Location field is required.',
			'remark_4.required' => 'The Alamat field is required.',
			'email.required' => 'The Email field is required.'
		]);
		$category = MasterGeneralLetter::where('id_general_data',$request->id_category)->first();
		if ($category->code == 'SPB-PKWT') {
			$request->validate([
				'remark_1' => 'required',
				'remark_3' => 'required',
				'expired_date' => 'required'
			],[
				'remark_1.required' => 'The PKWT Number field is required.',
				'remark_3.required' => 'The PKWT Date field is required.',
				'expired_date.required' => 'The End Date field is required.'
			]);
		}
		try {
			DB::beginTransaction();
			$result = NotifLetter::format_save($request);
			$format = $result['format'];
			$principalTextValue = $result['principalTextValue'];
			$token = $result['token'];
			$id_letter_type = $result['id_letter_type'];
			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> id_employee = $request->id_employee;
			$data -> id_employment_status = $request->id_employment_status;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_job_grade = $request->id_job_grade;
			$data -> id_region = $request->id_region;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> id_principal = $principalTextValue;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_2 = $request->remark_2;
			$data -> remark_3 = $request->remark_3;
			$data -> remark_4 = $request->remark_4;
			$data -> token = $token;
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Notification Letter Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Notification Letter !! [' . $e->getMessage() . ']']);   
		}
	}
	public function get_edit($id_letter)
	{
		$data = NotifLetter::get_edit_notifletter($id_letter);
		return response()->json($data);
	}
	public function update(Request $request)
	{
		$request->validate([
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_2' => 'required',
			'remark_4' => 'required',
			'email' => 'required|email'
		],[
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'remark_2.required' => 'The Letter Location field is required.',
			'remark_4.required' => 'The Alamat field is required.',
			'email.required' => 'The Email field is required.'
		]);
		$category = MasterGeneralLetter::where('id_general_data',$request->id_category)->first();
		if ($category->code == 'SPB-PKWT') {
			$request->validate([
				'remark_1' => 'required',
				'remark_3' => 'required',
				'expired_date' => 'required'
			],[
				'remark_1.required' => 'The PKWT Number field is required.',
				'remark_3.required' => 'The PKWT Date field is required.',
				'expired_date.required' => 'The End Date field is required.'
			]);
		}
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> remark_2 = $request->remark_2;
			$data -> remark_3 = $request->remark_3;
			$data -> remark_4 = $request->remark_4;
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Notification Letter Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Notification Letter !! [' . $e->getMessage() . ']']);   
		}
	}
	public function delete($id_letter)
	{
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$id_letter)->first();
			if ($data) {
				$data -> delete();
			}
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Notification Letter Succesfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Delete Notification Letter !! [' . $e->getMessage() . ']']); 
		}
	}
	public function print($token)
	{
		$data = NotifLetter::get_print_notifletter($token);
		$pdf=PDF::loadview('eletter.notif_letter.print',compact('data'))->setPaper('A4','potrait');
		return $pdf->stream();
	}
	public function pkwt_number(Request $request)
	{
		$data = NotifLetter::get_pkwt_number($request);
		if ($request->ajax()) {
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('action', function($data) {
				$button = '<button type="button" more_id="'.$data->id_letter.'" more_number="'.$data->reference_number.'" more_date="'.$data->date.'" more_end="'.$data->expired_date.'" class="btn btn-success btn-sm choose_number" title="PKWT Number"><span class="fas fa-check"></span></button> ';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
	}
}
