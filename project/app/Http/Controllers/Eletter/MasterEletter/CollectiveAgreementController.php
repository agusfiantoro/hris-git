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
use App\Models\Eletter\MasterEletter\CollectiveAgreement;
use PDF;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CollectiveAgreementController extends Controller
{
	public function index(Request $request)
	{
		// $allowedAccess = [1954];
		// if(!in_array(session('id_user'), $allowedAccess)) {
		// 	return response("<script>alert('Menu PB ditutup sementara untuk maintenance. Menu ini dapat dibuka kembali pada Jumat, 19 Juli 2024.');window.history.back();</script>");
		// }
	
		if ($request->ajax()) {
			$data = CollectiveAgreement::get_pb($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				// if ($data->category == "Perjanjian Bersama") {
				// 	$file = asset('project/storage/app/public/pb/web_udsj.sql');
				// }
				if ($data->category_code == "PB_Per_Alasan") {
					$file = asset('project/storage/app/public/pb/PB_PKWTT_ALASAN LAIN-LAIN.doc');
				}elseif ($data->category_code == "PB_Per_Meninggal") {
					$file = asset('project/storage/app/public/pb/PB_PKWTT_PEKERJA MENINGGAL DUNIA.doc');
				}elseif ($data->category_code == "PB_Per_Mendesak") {
					$file = asset('project/storage/app/public/pb/PB_PKWTT_ALASAN MENDESAK.doc');
				}elseif ($data->category_code == "PB_Per_Resign") {
					$file = asset('project/storage/app/public/pb/PB_PKWTT_PEKERJA RESIGN ATAU DITAHAN PIHAK BERWENANG.doc');
				}elseif ($data->category_code == "PB_Contract_Alasan") {
					$file = asset('project/storage/app/public/pb/PB_PKWT_ALASAN LAIN-LAIN.doc');
				}elseif ($data->category_code == "PB_Contract_Meninggal") {
					$file = asset('project/storage/app/public/pb/PB_PKWT_PEKERJA MENINGGAL DUNIA.doc');
				}elseif ($data->category_code == "PB_Contract_Resign") {
					$file = asset('project/storage/app/public/pb/PB_PKWT_PEKERJA MELAKUKAN PELANGGARAN ATAU RESIGN.doc');
				}else{
					$file = '';
				}
				$button = '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<a href="'.$file.'" download="" name="download" id="" class="btn btn-success btn-sm" title="Download"><span class="fa fa-download"></span></a> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.collective_agreement.index');
	}
	public function get_data(Request $request)
	{
		$category = CollectiveAgreement::get_category();
		return response()->json(['category'=>$category]);
	}
	public function get_employee(Request $request)
	{
		$data = CollectiveAgreement::get_employee_pb($request);
		if ($request->ajax()) {
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('action', function($data) {
				$button = '<button type="button" more_id="'.$data->id_employee.'" more_status="'.$data->status.'" class="btn btn-success btn-sm choose_employee" title="Pilih Employee"><span class="fas fa-check"></span></button> ';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
	}
	public function change_employee(Request $request)
	{
		$data = CollectiveAgreement::change_employee_pb($request);
		return response()->json($data);
	}
	public function change_branch(Request $request)
	{
		$data = ElectronicLetter::change_branch_data($request);
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
			'email' => 'required|email'
			// 'id_employee_chief' => 'required',
			// 'id_position_routing_chief' => 'required'
		],[
			'id_category.required' => 'The Category field is required.',
			'id_employee.required' => 'The Name field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_dept.required' => 'The Department field is required.',
			'id_branch.required' => 'The Position field is required.',
			'id_position_detail.required' => 'The Region field is required.',
			'date.required' => 'The Letter Date field is required.',
			'email.required' => 'The Email field is required.'
			// 'id_employee_chief.required' => 'The Employee Chief field is required.',
			// 'id_position_routing_chief.required' => 'The Position Chief field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = CollectiveAgreement::format_save($request);
			$id_letter_type = $result['id_letter_type'];
			$format = $result['format'];
			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> notes = $request->notes;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> id_employee = $request->id_employee;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_region = $request->id_region;
			// $data -> id_employee_chief = $request->id_employee_chief;
			// $data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> remark_1 = $request->remark_1;
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> id_position_routing = $request->id_position_detail;
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Collective Agreement Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Collective Agreement !! [' . $e->getMessage() . ']']);  
		}
	}
	public function get_edit($id_letter)
	{
		$data = CollectiveAgreement::get_edit_pb($id_letter);
		return response()->json(['data'=>$data]);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			// 'id_employee_chief' => 'required',
			// 'id_position_routing_chief' => 'required',
			'id_category' => 'required',
			// 'id_employee' => 'required',
			'id_branch' => 'required',
			'id_region' => 'required',
			'id_position_routing' => 'required'
		],[
			'email.required' => 'The Email field is required.',
			// 'id_employee_chief.required' => 'The Employee Chief field is required.',
			// 'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'id_category.required' => 'The Category field is required.',
			// 'id_employee.required' => 'The Name field is required.',
			'id_branch.required' => 'The Branch field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_position_routing.required' => 'The Position field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			$data -> notes = $request->notes;
			// $data -> id_employee_chief = $request->id_employee_chief;
			// $data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> id_category = $request->id_category;
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> id_position_routing = $request->id_position_routing;
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Collective Agreement Edit Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Collective Agreement !! [' . $e->getMessage() . ']']);  
		}
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::where('id_letter',$id_letter)->first();
		if ($data) {
			$data -> delete();
		}
	}
}
