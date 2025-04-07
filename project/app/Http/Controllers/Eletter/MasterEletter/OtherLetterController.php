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
use Illuminate\Support\Facades\Log;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Organization\MasterOrganization\MasterDivision;
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Eletter\MasterEletter\OtherLetter;
use PDF;

class OtherLetterController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = OtherLetter::get_other_letter($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '';
				if ($data->name != NULL) {
					$button .= '<a href="javascript:void(0)" more_token="'.$data->id_letter.'" more_id="'.$data->id_letter.'" name="mail" more_type="Other Letter" id="mail_send-'.$data->id_letter.'" class="btn btn-info btn-sm btn-mail" title="Mail"><span class="fa fa-envelope"></span></a> ';
				}
				$button .= '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.other_letter.index');
	}
	public function get_data(Request $request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$category = OtherLetter::get_category();
		$employee = OtherLetter::get_employee($request);
		$employee_chief = OtherLetter::get_employee_chief();
		$department = OtherLetter::get_dept();
		$region = OtherLetter::get_region();
		return response()->json(['category'=>$category,'employee'=>$employee,'employee_chief'=>$employee_chief,'region'=>$region,'department'=>$department]);
	}
	public function get_branch(Request $request)
	{
		$branch = OtherLetter::get_branch($request);
		return response()->json($branch);
	}
	public function change_employee(Request $request)
	{
		$data = OtherLetter::change_employee_other($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
		try {
			DB::beginTransaction();
			$result = OtherLetter::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];
			$token = $result['token'];
			$principalTextValue = $result['principalTextValue'];

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
			return response()->json(['status'=>'true','message'=>'Other Letter Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Other Letter !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_letter)
	{
		$data = OtherLetter::get_edit_otherletter($id_letter);
		return response()->json($data);
	}
	public function edit(Request $request)
	{
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
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Other Letter Edit Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Other Letter !! [' . $e->getMessage() . ']']);
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
