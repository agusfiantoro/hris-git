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
use App\Models\Eletter\MasterEletter\FreelanceWorkAgreement;

class FreelanceWorkAgreementController extends Controller
{
	public function pkhl_index(Request $request)
	{
		if ($request->ajax()) {
			$data = FreelanceWorkAgreement::get_freelance($request);
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
		return view('eletter.freelance_work_agreement.index');
	}
	public function get_data_new()
	{
		$position = FreelanceWorkAgreement::get_position();
		return response()->json([
			'position'=>$position
		]);
	}
	public function get_position(Request $request)
	{
		$position = ElectronicLetter::get_position_data($request);
		return response()->json($position);
	}
	public function change_branch(Request $request)
	{
		$data = ElectronicLetter::change_branch_data($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
		$request->validate([
			'date' => 'required',
			'remark_1' => 'required',
			'id_dept' => 'required',
			'id_branch' => 'required',
			'id_region' => 'required',
			'id_position_detail' => 'required',
			'id_job_grade' => 'required',
			'id_location' => 'required',
			'effective_date' => 'required',
			'email' => 'required|email',
			'notes' => 'required'
		],[
			'date.required' => 'The Letter Date field is required.',
			'remark_1.required' => 'The Name field is required.',
			'id_dept.required' => 'The Department field is required.',
			'id_branch.required' => 'The Branch field is required.',
			'id_region.required' => 'The Region field is required.',
			'id_position_detail.required' => 'The Position field is required.',
			'id_job_grade.required' => 'The Job Grade field is required.',
			'id_location.required' => 'The Location field is required.',
			'effective_date.required' => 'The Start Date - End Date field is required.',
			'email.required' => 'The Email field is required.',
			'notes.required' => 'The Notes field is required.'
		]);
		try {
			DB::beginTransaction();
			$id_letter_type = FreelanceWorkAgreement::id_letter_type_save();
			$result = FreelanceWorkAgreement::format_save($request);
			$start = $result['start'];
			$end = $result['end'];
			$format = $result['format'];
			$hari = $result['hari'];
			$id_category = $result['id_category'];
			if ($hari > 21) {
				return response()->json(['status'=>'warning','message'=>'Start Date - End Date tidak boleh lebih dari 21 hari.']);
			}else{
				$data = New ElectronicLetter();
				$data -> reference_number = $format;
				$data -> date = $request->date;
				$data -> id_letter_type = $id_letter_type->id_general_data;
				$data -> id_category = $id_category;
				$data -> notes = $request->notes;
				$data -> id_dept = $request->id_dept;
				$data -> id_branch = $request->id_branch;
				$data -> id_region = $request->id_region;
				$data -> id_job_grade = $request->id_job_grade;
				$data -> id_location = $request->id_location;
				$data -> effective_date = $start;
				$data -> expired_date = $end;
				$data -> remark_1 = $request->remark_1;
				$data -> email = $request->email;
				$data -> status = 'A';
				$data -> id_company = session('id_company');
				$data -> created_by = session('id_user');
				$data -> id_position_routing = $request->id_position_detail;
				$data -> save();
				DB::commit();
				return response()->json(['status'=>'true','message'=>'Freelance Work Agreement Succesfullly !!']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Freelance Work Agreement !! [' . $e->getMessage() . ']']);
		}
		
	}
	public function get_edit($id_letter)
	{
		$data = FreelanceWorkAgreement::get_edit_freelance($id_letter);
		return response()->json($data);
	}
	public function update(Request $request)
	{
		$request->validate([
			'remark_1' => 'required',
			'effective_date' => 'required',
			'email' => 'required|email',
			'notes' => 'required'
		],[
			'remark_1.required' => 'The Name field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'email.required' => 'The Email field is required.',
			'notes.required' => 'The Notes field is required.'
		]);
		try {
			DB::beginTransaction();
			$dateRange = $request->effective_date;
			list($start, $end) = explode(" - ", $dateRange);
			$tgl1 = strtotime($start); 
			$tgl2 = strtotime($end); 
			$jarak = $tgl2 - $tgl1;
			$hari = $jarak / 60 / 60 / 24;
			if ($hari > 21) {
				return response()->json(['status'=>'warning','message'=>'Start Date - End Date tidak boleh lebih dari 21 hari.']);
			}else{
				$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();	
				$data -> notes = $request->notes;
				$data -> effective_date = $start;
				$data -> expired_date = $end;
				$data -> remark_1 = $request->remark_1;
				$data -> email = $request->email;
				$data -> id_company = session('id_company');
				$data -> updated_by = session('id_user');
				$data -> save();
				DB::commit();
				return response(['status'=>'true','message'=>'Freelance Work Agreement Edit Succesfullly !!']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Freelance Work Agreement !! [' . $e->getMessage() . ']']);
		}

	}
	public function destroy($id_letter)
	{
		if ($id_letter) {
			ElectronicLetter::where('id_letter',$id_letter)->delete();
		}
	}
}
