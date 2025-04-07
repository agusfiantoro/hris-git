<?php

namespace App\Http\Controllers\TalentManagement\MasterTalent;

use App\Models\TalentManagement\MasterTalent\MasterTalent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MasterTalentController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterTalent::getdata();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
						$onclick = "loadedit(".$data->id_talent_matrix.")";
                        $button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_talent_matrix . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('talent_management.master_talent_setting.master_talent_rating.index');
    }
	
	public function modal_detail(Request $request) {
		$global_talent = $request->global_talent;
        return view('talent_management.master_talent_setting.master_talent_rating.modal_detail', compact('global_talent'));
    }
	
	protected function validateTalent(Request $request) {
        $arr_form_validate = [
            'name_talent' => 'required|string',
            'group_matrix' => 'required',
            'kpi_value_min' => 'required|numeric',
            'kpi_value_max' => 'required|numeric',
            'job_grade' => 'required',
            'rating' => 'required',
            'psychogram' => 'required',
            'bei' => 'required',
            'readyness' => 'required|string',
            'color' => 'required|string',
        ];
	
        $arr_msg_form_validate = [
            'name_talent.required' => 'The Box Name field is required',
            'group_matrix.required' => 'The Matrix Group field is required',
            'kpi_value_min.required' => 'The KPI Min field is required',
            'kpi_value_max.required' => 'The KPI Max field is required',
            'job_grade.required' => 'The Job Grade field is required',
            'rating.required' => 'The Rating field is required',
            'psychogram.required' => 'The Potencies field is required',
            'bei.required' => 'The Competencies field is required',
            'readyness.required' => 'The Readiness field is required',
            'color.required' => 'The Color field is required',
        ];
		
		$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
	//	dd($request->all());
		$this->validateTalent($request);
		try{
			DB::beginTransaction();
	
			$form_data = array(
				'name' => $request->name_talent,
				'id_group_matrix' => $request->group_matrix,
				'kpi_value_min' => $request->kpi_value_min,
				'kpi_value_max' => $request->kpi_value_max,
				'id_job_grade' => $request->job_grade,
				'id_grade_promotion' => $request->rating,
				'id_conclusion_psychotest' => $request->psychogram,
				'id_conclusion_assessment' => $request->bei,
				'readyness' => $request->readyness,
				'color' => $request->color,
			//	'status' => $request->status,
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
			
			MasterTalent::create($form_data);
			
			DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Talent Matrix Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Talent Matrix !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update(Request $request) {
		$this->validateTalent($request);
		try{
			DB::beginTransaction();
			$form_data = array(
				'name' => $request->name_talent,
				'id_group_matrix' => $request->group_matrix,
				'kpi_value_min' => $request->kpi_value_min,
				'kpi_value_max' => $request->kpi_value_max,
				'id_job_grade' => $request->job_grade,
				'id_grade_promotion' => $request->rating,
				'id_conclusion_psychotest' => $request->psychogram,
				'id_conclusion_assessment' => $request->bei,
				'readyness' => $request->readyness,
				'color' => $request->color,
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			);
			
			MasterTalent::findOrFail($request->id_talent_matrix)->update($form_data);
					
			DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Talent Matrix Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Talent Matrix !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	
	public function get_talent_edit(Request $request) {
        $data = [
            'id_talent' => $request->id_talent
        ];
        $result = MasterTalent::get_talent_edit($data);
        return response()->json($result);
    }
	
	public function get_grade() {
        $result = MasterTalent::get_grade();
        return response()->json($result);
    }
	
	public function get_rating(Request $request) {
		 $data = [
            'id_job_grade' => $request->id_job_grade
        ];
        $result = MasterTalent::get_rating($data);
        return response()->json($result);
    }
	
	public function get_group_matrix() {
        $result = MasterTalent::get_group_matrix();
        return response()->json($result);
    }
	public function get_conclusion() {
        $result = MasterTalent::get_conclusion();
        return response()->json($result);
    }
	
	public function destroy($id) {
		try{
			DB::beginTransaction();
        $data = MasterTalent::findOrFail($id);
        $data->delete();
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Master Talet Delete Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Delete Master Talent !! [There are Relation Another Table]']);           
		}	
    }
}
