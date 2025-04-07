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
use App\Models\Eletter\MasterEletter\InternshipCertificates;
use Illuminate\Support\Facades\Log;
use PDF;
use App\Mail\SendMail;

class InternshipCertificateController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = InternshipCertificates::get_ski();
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<a href="javascript:void(0)" more_token="'.$data->token.'" more_id="'.$data->id_letter.'" name="mail" id="mail_send-'.$data->id_letter.'" more_type="SKI" class="btn btn-info btn-sm btn-mail" title="Mail"><span class="fa fa-envelope"></span></a> ';
				$button .= '<a href="'.route('print.ski',$data->token).'" target="_blank" name="print" id="" class="btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></a><br>';
				$button .= '<button type="button" name="view" id="" class="btn btn-view btn btn-warning text-white btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit mt-1" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm mt-1" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.internship_certificate.index');
	}
	public function get_data()
	{
		$dept = InternshipCertificates::get_dept();
		$category = InternshipCertificates::get_category();
		$location = InternshipCertificates::get_location();
		// Employee Chief
		$employee = InternshipCertificates::get_employee();
		return response()->json(['dept'=>$dept,'employee'=>$employee,'category'=>$category,'location'=>$location]);
	}
	public function save(Request $request)
	{
		$request->validate([
			'id_category' => 'required',
			'id_dept' => 'required',
			'date' => 'required',
			'effective_date' => 'required',
			'expired_date' => 'required',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_1' => 'required',
			'remark_2' => 'required',
			'remark_3' => 'required',
			'remark_4' => 'required',
			'remark_5' => 'required'
		],[
			'id_category.required' => 'The Category field is required.',
			'id_dept.required' => 'The Department field is required.',
			'date.required' => 'The Letter Date field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'expired_date.required' => 'The Expired Date field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'remark_1.required' => 'The Name field is required.',
			'remark_2.required' => 'The Status field is required.',
			'remark_3.required' => 'The Universitas field is required.',
			'remark_4.required' => 'The Jurusan field is required.',
			'remark_5.required' => 'The Letter Location field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = InternshipCertificates::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];
			$token = $result['token'];
			$region = $result['region'];

			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> id_region = $region->id_region;
			$data -> date = $request->date;
			$data -> notes = $request->notes;
			$data -> id_dept = $request->id_dept;
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
			return response()->json(['status'=>'true','message'=>'Internship Certificate Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Internship Certificates !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_letter)
	{
		$data = InternshipCertificates::get_edit_ski($id_letter);
		return response()->json($data);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'effective_date' => 'required',
			'expired_date' => 'required',
			'id_employee_chief' => 'required',
			'id_position_routing_chief' => 'required',
			'remark_1' => 'required',
			'remark_2' => 'required',
			'remark_3' => 'required',
			'remark_4' => 'required',
			'remark_5' => 'required'
		],[
			'effective_date.required' => 'The Effective Date field is required.',
			'expired_date.required' => 'The Expired Date field is required.',
			'id_employee_chief.required' => 'The Employee Chief field is required.',
			'id_position_routing_chief.required' => 'The Position Chief field is required.',
			'remark_1.required' => 'The Name field is required.',
			'remark_2.required' => 'The Status field is required.',
			'remark_3.required' => 'The Universitas field is required.',
			'remark_4.required' => 'The Jurusan field is required.',
			'remark_5.required' => 'The Letter Location field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			$data -> notes = $request->notes;
			$data -> id_employee_chief = $request->id_employee_chief;
			$data -> id_position_routing_chief = $request->id_position_routing_chief;
			$data -> effective_date = $request->effective_date;
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
			return response()->json(['status'=>'true','message'=>'Internship Certificate Edit Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Internship Certificates !! [' . $e->getMessage() . ']']);
		}
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::where('id_letter',$id_letter)->first();
		if ($data) {
			$data -> delete();
		}
	}
	public function print($token)
	{
		$data = InternshipCertificates::get_print_ski($token);
		// $branch = InternshipCertificates::branch_print();
		$pdf=PDF::loadview('eletter.internship_certificate.print',compact('data'))->setPaper('A4','potrait');
		return $pdf->stream();
	}
}
