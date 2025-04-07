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
use App\Models\Organization\MasterOrganization\MasterDivision;
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Eletter\MasterEletter\TerminationLetter;
use PDF;

class TerminationLetterController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = TerminationLetter::get_skp($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<button type="button" name="view" id="" class="btn-view btn btn-warning text-white btn-sm" more_id="'.$data->id_letter.'" title="View"><span class="far fa-eye"></span></button> ';
				$button .= '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_letter.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm" more_id="'.$data->id_letter.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.termination_letter.index');
	}
	public function get_data(Request $request)
	{
		$employee = TerminationLetter::get_employee($request);
		$category = TerminationLetter::get_category();
		$chief = TerminationLetter::get_chief();
		return response()->json(['employee'=>$employee,'category'=>$category,'chief'=>$chief]);
	}
	public function change_career(Request $request)
	{
		$data = TerminationLetter::change_career_skp($request);
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
			'id_position_detail' => 'required'
			// 'id_employee_chief' => 'required',
			// 'id_position_routing_chief' => 'required'
		],[
			'date.required' => 'The Letter Date field is required.',
			'id_category.required' => 'The Category field is required.',
			'id_employee.required' => 'The Name field is required.',
			'id_dept.required' => 'The Department field is required.',
			'id_branch.required' => 'The Branch field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_position_detail.required' => 'The Position field is required.'
			// 'id_employee_chief.required' => 'The Employee Chief field is required.',
			// 'id_position_routing_chief.required' => 'The Position Chief field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = TerminationLetter::format_save($request);
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
			$data -> id_position_detail = $request->id_position_detail;
			// $data -> id_employee_chief = $request->id_employee_chief;
			// $data -> id_position_routing_chief =  $request->id_position_routing_chief;
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Termination Letter Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Termination Letter !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_letter)
	{
		$data = TerminationLetter::get_edit_skp($id_letter);
		return response()->json($data);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'id_category' => 'required'
			// 'id_employee_chief' => 'required',
			// 'id_position_routing_chief' => 'required'
		],[
			'id_category.required' => 'The Category field is required.'
			// 'id_employee_chief.required' => 'The Employee Chief field is required.',
			// 'id_position_routing_chief.required' => 'The Position Chief field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();
			$data -> notes = $request->notes;
			// $data -> id_employee_chief = $request->id_employee_chief;
			// $data -> id_position_routing_chief =  $request->id_position_routing_chief;
			$data -> email = $request->email;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Termination Letter Edit Succesfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Termination Letter !! [' . $e->getMessage() . ']']);
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
