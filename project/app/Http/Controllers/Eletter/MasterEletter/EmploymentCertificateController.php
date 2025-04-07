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
use App\Models\Eletter\MasterEletter\EmploymentCertificate;
use PDF;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class EmploymentCertificateController extends Controller
{
	public function index(Request $request)
	{
    	// category 17,18,19
		if ($request->ajax()) {
			$data = EmploymentCertificate::get_skk($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<a href="javascript:void(0)" more_type="SKK" more_token="'.$data->token.'" more_id="'.$data->id_letter.'" name="mail" id="mail_send-'.$data->id_letter.'" class="btn btn-info btn-sm btn-mail" title="Mail"><span class="fa fa-envelope"></span></a> ';
				$button .= '<a href="'.route('print.skk',$data->token).'" target="_blank" name="print" id="" class="btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></a> ';
				$button .= '<button type="button" name="publish" more_id="'.$data->id_letter.'" class="btn btn-success btn-sm btn-publish" title="Publish"><span class="fas fa-user-check"></span></button><br>';
				$button .= '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm mt-1" more_id="'.$data->id_letter.'" title="View"><span class="fa fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit mt-1" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				if ($data->publish == false) {
					$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				}
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.employment_certificate.index');
	}
	public function get_data(Request $request)
	{
		// $session = session('id_company');
		// $sql_employee = "SELECT *
		// FROM hr_employee AS he_all
		// JOIN (
		// SELECT identification_number, MAX(id_employee) AS id_em1
		// FROM hr_employee
		// GROUP BY identification_number
		// ) AS e1
		// ON he_all.id_employee = e1.id_em1
		// JOIN master_position_detail AS mpd
		// ON mpd.id_employee = he_all.id_employee
		// WHERE he_all.id_company='$session' AND mpd.secondary_position='false'
		// ";
		$location = EmploymentCertificate::get_location();
		$category = EmploymentCertificate::get_category();
		$region = EmploymentCertificate::get_region();
		return response()->json(['category'=>$category,'location'=>$location,'region'=>$region]);
	}
	public function change_employee(Request $request)
	{
		$data = EmploymentCertificate::change_employee_skk($request);
		return response()->json(['data'=>$data]);
	}
	public function get_employee(Request $request)
	{
		$data = EmploymentCertificate::get_employee($request);
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
	public function save(Request $request)
	{
		$request->validate([
			'date' => 'required|date',
			'id_category' => 'required',
			'id_region' => 'required',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_2' => 'required',
			'email' => 'required|email'
		],[
			'date.required' => 'The Letter Date field is required.',
			'id_category.required' => 'The Category field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'remark_2.required' => 'The Letter Location field is required.',
			'email.required' => 'The Email field is required.'
		]);
		if (isset($request->no_name)) {
			$request->validate([
				'name_input' => 'required',
				'status_input' => 'required',
				'dept_input' => 'required',
				'branch_input' => 'required',
				'position_detail_input' => 'required',
				'job_grade_input' => 'required',
				'principal_input' => 'required',
				'effective_date' => 'required|date',
				'expired_date' => 'required|date'
			],[
				'name_input.required' => 'The Name field is required.',
				'status_input.required' => 'The Status field is required.',
				'dept_input.required' => 'The Department field is required.',
				'branch_input.required' => 'The Branch field is required.',
				'position_detail_input.required' => 'The Position field is required.',
				'job_grade_input.required' => 'The Job Grade field is required.',
				'principal_input.required' => 'The Principal field is required.',
				'effective_date.required' => 'The Join Date field is required.',
				'expired_date.required' => 'The End Date field is required.'
			]);
			$remark_3 = implode(';', [$request->name_input, $request->branch_input, $request->position_detail_input, $request->dept_input, $request->job_grade_input, $request->status_input]) . ';';
		}else{
			$request->validate([
				'id_employee' => 'required',
				'id_employment_status' => 'required',
				'id_dept' => 'required',
				'id_branch' => 'required',
				'id_position_detail' => 'required',
				'id_job_grade' => 'required',
				'id_principal' => 'required',
				'effective_date' => 'required|date'
			],[
				'id_employee.required' => 'The Name field is required.',
				'id_employment_status.required' => 'The Status field is required.',
				'id_dept.required' => 'The Department field is required.',
				'id_branch.required' => 'The Branch field is required.',
				'id_position_detail.required' => 'The Position field is required.',
				'id_job_grade.required' => 'The Job Grade field is required.',
				'id_principal.required' => 'The Principal field is required.',
				'effective_date.required' => 'The Join Date field is required.'
			]);
				// $remark_3 = NULL
		}
		$cek_category = MasterGeneralData::where('id_general_data',$request->id_category)->first();
		if ($cek_category->code != "PUB") {
			if (!isset($request->no_name)) {
				$request->validate([
					'expired_date' => 'required|date'
				],[
					'expired_date.required' => 'The End Date field is required.'
				]);
			}
		}else{
			$request->validate([
				'keterangan' => 'required'
			],[
				'keterangan.required' => 'The Keterangan field is required.'
			]);
		}
		try {
			DB::beginTransaction();
			$result = EmploymentCertificate::format_save($request);
			$format = $result['format'];
			$principalTextValue = $result['principalTextValue'];
			$token = $result['token'];
			$id_letter_type = $result['id_letter_type'];
			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> notes = $request->keterangan;
			$data -> id_region = $request->id_region;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_2 = $request->remark_2;
			if (isset($request->no_name)) {
				$data -> remark_3 = $remark_3;
			}else{
				$data -> id_employee = $request->id_employee;
				$data -> id_employment_status = $request->id_employment_status;
				$data -> id_dept = $request->id_dept;
				$data -> id_branch = $request->id_branch;
				$data -> id_position_detail = $request->id_position_detail;
				$data -> id_job_grade = $request->id_job_grade;
			}
			$data -> id_principal = $principalTextValue;
			$data -> token = $token;
			$data -> email = $request->email;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Decree Employment Certificate Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Employment Certificate !! [' . $e->getMessage() . ']']);   
		}
	}
	public function get_edit($id_letter)
	{
		$data = EmploymentCertificate::get_edit_skk($id_letter);
		$file = asset("project/storage/app/public/upload/skk/".$data[0]->remark_4);
		return response()->json(['data'=>$data,'file'=>$file]);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_2' => 'required',
			'email' => 'required|email'
		],[
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'remark_2.required' => 'The Letter Location field is required.',
			'email.required' => 'The Email field is required.'
		]);
		try {
			DB::beginTransaction();
			if ($request->id_employee == 'null') {
				$request->validate([
					'name_input' => 'required',
					'status_input' => 'required',
					'dept_input' => 'required',
					'branch_input' => 'required',
					'position_detail_input' => 'required',
					'job_grade_input' => 'required',
					'principal_input' => 'required',
					'effective_date' => 'required|date',
					'expired_date' => 'required|date'
				],[
					'name_input.required' => 'The Name field is required.',
					'status_input.required' => 'The Status field is required.',
					'dept_input.required' => 'The Department field is required.',
					'branch_input.required' => 'The Branch field is required.',
					'position_detail_input.required' => 'The Position field is required.',
					'job_grade_input.required' => 'The Job Grade field is required.',
					'principal_input.required' => 'The Principal field is required.',
					'effective_date.required' => 'The Join Date field is required.',
					'expired_date.required' => 'The End Date field is required.'
				]);
				$remark_3 = implode(';', [$request->name_input, $request->branch_input, $request->position_detail_input, $request->dept_input, $request->job_grade_input, $request->status_input]) . ';';
			}
			// dd($request->id_employee);
			$cek_category = MasterGeneralData::where('id_general_data',$request->id_category)->first();
			if ($cek_category->code == "PUB") {
				$request->validate([
					'keterangan' => 'required'
				],[
					'keterangan.required' => 'The Keterangan field is required.'
				]);
			}
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			if ($data) {
				$data -> notes = $request->keterangan;
				$data -> id_employee_chief = $request->id_employee_chief;
				$data -> id_position_routing_chief = $request->id_position_routing_chief;
				if ($request->id_employee == 'null') {
					$data -> effective_date = $request->effective_date;
					$data -> expired_date = $request->expired_date;
					$data -> remark_3 = $remark_3;
					$data -> id_principal = $request->principal_input;
				}
				$data -> remark_2 = $request->remark_2;
				$data -> email = $request->email;
				$data -> id_company = session('id_company');
				$data -> updated_by = session('id_user');
				$data -> save();
				DB::commit();
				return response()->json(['status'=>'true','message'=>'Decree Employment Certificate Edit Succesfully !!']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Employment Certificate !! [' . $e->getMessage() . ']']); 
		}
	}
	public function print($token)
	{
		$data = EmploymentCertificate::get_print_skk($token);
		// $branch = EmploymentCertificate::branch_print();
		$pdf=PDF::loadview('eletter.employment_certificate.print',compact('data'))->setPaper('A4','potrait');
		return $pdf->stream();
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::where('id_letter',$id_letter)->first();
		if ($data) {
			$data -> delete();
		}
	}
	public function get_view($token)
	{
		$data = EmploymentCertificate::get_print_skk($token);
		return response()->json(['data'=>$data]);
	}
	public function publish(Request $request)
	{
		if (isset($request->publish)) {
			$request->validate([
				'remark_1' => 'required|date',
				'remark_4' => 'required|file'
			],[
				'remark_1.required' => 'The Publish Date field is required.',
				'remark_4.required' => 'The Upload File field is required.',
				'remark_1.date' => 'The Publish Date is not a valid date.',
				'remark_4.max' => 'The File Upload must not be greater than 300 kilobytes.'
			]);
			$publish = true;
		}else{
			$publish = NULL;
		}
		try {
			DB::beginTransaction();
			if (!empty($request->file('remark_4'))) {
				$files = $request->file('remark_4');
				$file_publish = $files->getClientOriginalName();
				$namaFileBaru = uniqid();
				$namaFileBaru .= $file_publish;
				$files->storeAs("public/upload/skk",$namaFileBaru);
			}else{
				$namaFileBaru = NULL;
			}
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			if ($data->publish == NULL) {
				$data -> remark_1 = $request->remark_1;
				$data -> remark_4 = $namaFileBaru;
				$data -> publish = $publish;
				$data -> save();
			}
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Publish Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Publish Employment Certificate !! [' . $e->getMessage() . ']']); 
		}
	}
}
