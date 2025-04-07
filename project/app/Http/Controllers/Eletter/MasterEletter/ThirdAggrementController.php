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
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use Illuminate\Support\Facades\Log;
use App\Models\Eletter\MasterEletter\ThirdAggrement;

class ThirdAggrementController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = ThirdAggrement::get_tag($request);
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
		return view('eletter.third_aggrement.index');
	}
	public function modal_detail()
	{
		return view('eletter.third_aggrement.modal_detail');
	}
	public function get_data()
	{
		$category = ThirdAggrement::get_category();
		$region = ThirdAggrement::get_region();
		$dept = ThirdAggrement::get_dept();
		return response()->json([
			'category'=>$category,
			'region'=>$region,
			'dept'=>$dept
		]);
	}
	public function change_region(Request $request)
	{
		$data = ThirdAggrement::change_region($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
		$request->validate([
			'date' => 'required',
			'id_category' => 'required',
			'id_dept' => 'required',
			'id_branch' => 'required',
			'id_region' => 'required',
			'effective_date' => 'required',
			'expired_date' => 'required',
			'notes' => 'required'
		],[
			'date.required' => 'The Letter Date field is required.',
			'id_category.required' => 'The Category field is required.',
			'id_dept.required' => 'The Department field is required.',
			'id_branch.required' => 'The Branch field is required.',
			'id_region.required' => 'The Region field is required.',
			'effective_date.required' => 'The Effective Date field is required.',
			'expired_date.required' => 'The Expired Date field is required.',
			'notes.required' => 'The Notes field is required.'
		]);
		try {
			DB::beginTransaction();
			$result = ThirdAggrement::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];

			$data = New ElectronicLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
			$data -> id_category = $request->id_category;
			$data -> notes = $request->notes;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_region = $request->id_region;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->number_notes ?? null;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Third Aggrement Succesfullly !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Third Aggrement !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_letter)
	{
		$data = ThirdAggrement::get_edit($id_letter);
		return response()->json($data);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'effective_date' => 'required',
			'expired_date' => 'required',
			'id_branch' => 'required',
			'notes' => 'required'
		],[
			'effective_date.required' => 'The Effective Date field is required.',
			'expired_date.required' => 'The Expired Date field is required.',
			'id_branch.required' => 'The Branch field is required.',
			'notes.required' => 'The Notes field is required.'
		]);
		try {
			DB::beginTransaction();
			$data = ElectronicLetter::where('id_letter',$request->id_letter)->first();	
			$data -> notes = $request->notes;
			$data -> id_branch = $request->id_branch;
			$data -> effective_date = $request->effective_date;
			$data -> expired_date = $request->expired_date;
			$data -> remark_1 = $request->number_notes ?? null;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response(['status'=>'true','message'=>'Third Aggrement Edit Succesfullly !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Third Aggrement !! [' . $e->getMessage() . ']']);
		}
	}
	public function destroy($id_letter)
	{
		$data = ElectronicLetter::where('id_letter',$id_letter)->first();
		if ($data) {
			$data->delete();
		}
	}
}
