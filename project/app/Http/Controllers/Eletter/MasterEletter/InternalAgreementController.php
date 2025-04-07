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
use Illuminate\Support\Facades\Log;
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Eletter\MasterEletter\InternalAgreement;

class InternalAgreementController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = InternalAgreement::get_pi($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<button type="button" action="view" name="view" id="" class="btn-view btn btn-warning text-white btn-sm" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" action="edit" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.internal_agreement.index');
	}
	public function get_data()
	{
		$category = InternalAgreement::get_category();
		return response()->json($category);
	}
	public function get_employee(Request $request)
	{
		$data = InternalAgreement::get_employee_pi($request);
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
		$data = InternalAgreement::change_employee_pi($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
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
			// 'expired_date' => 'required',
			'remark_1' => 'required',
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
			// 'expired_date.required' => 'The Expired Date field is required.',
			'remark_1.required' => 'The Status field is required.',
			'notes.required' => 'The Notes field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = InternalAgreement::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];

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
			$data -> id_job_grade = $request->id_job_grade;
			$data -> id_location = $request->id_location;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->remark_1;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> id_position_routing = $request->id_position_detail;
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Internal Agreement Succesfullly !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Internal Agreement !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_letter)
	{
		$data = InternalAgreement::get_edit_pi($id_letter);
		return response()->json($data);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'effective_date' => 'required',
			// 'expired_date' => 'required',
			'notes' => 'required'
		],[
			'effective_date.required' => 'The Effective Date field is required.',
			// 'expired_date.required' => 'The Expired Date field is required.',
			'notes.required' => 'The Notes field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();	
			$data -> notes = $request->notes;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response(['status'=>'true','message'=>'Internal Agreement Edit Succesfullly !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Internal Agreement !! [' . $e->getMessage() . ']']);
		}
	}
	public function modal_detail()
	{
		return view('eletter.internal_agreement.modal_detail');
		// return response()->json($modalDetailHtml);
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::where('id_letter',$id_letter)->first();
		if ($data) {
			$data->delete();
		}
	}
}
