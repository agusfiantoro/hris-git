<?php
namespace App\Http\Controllers\Kpi\KpiSetting;

use App\Models\Kpi\KpiSetting\PaQuestion;
use App\Models\Kpi\KpiSetting\PaGrade;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PaGradeController extends Controller {

     public function index(Request $request) {
        if ($request->ajax()) {
            $data = MasterGrade::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_job_grade . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';                        
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.kpi_settings.master_grade_question.index');
    }
	
	protected function update(Request $request) {
		try{
			DB::beginTransaction();
		//	$collect_pagrade = collect($request->pagrade)->groupBy('id_grade_question')->toArray();
		//	$list_pagrade = array_filter(array_keys($collect_pagrade));
			
		$listIdGrade    = [];
        $idGrade      = [];
		if(PaGrade::where('id_job_grade', $request->id_job_grade)->first() != null){
			$listIdGrade = PaGrade::where('id_job_grade', $request->id_job_grade)->where('id_company', session('id_company'))->get()->pluck('id_grade_question')->all();
		}
		if ($request->pagrade) {
		/*	if (implode(",", $list_pagrade) != "") {
				DB::delete("DELETE FROM  hr_grade_question hgq
							WHERE hgq.id_job_grade = ? AND hgq.id_grade_question NOT IN (" . implode(",", $list_pagrade) . ")", [$request->id_job_grade]);
			}
		*/
			foreach ($request->pagrade as $key => $value) {
				if ($value['id_grade_question'] == "") {
					PaGrade::create(array(
						'id_job_grade' => $request->id_job_grade,
						'id_pa_question' =>  $value['id_pa_question'],
						'id_minimum_score_level' =>  $value['id_minimum_score_level'],
						'value' =>  $value['value'],
						'status' =>  $value['status'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					$idGrade[] = $value['id_grade_question'];
					PaGrade::where('id_grade_question', $value['id_grade_question'])->update(array(
						'id_pa_question' =>  $value['id_pa_question'],
						'id_minimum_score_level' =>  $value['id_minimum_score_level'],
						'value' =>  $value['value'],
						'status' =>  $value['status'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		$diff = array_diff($listIdGrade, $idGrade);
		if(count($diff) > 0){
			foreach ($diff as $key => $value) { 
				PaGrade::where('id_grade_question', $value)->delete();
			}
		}
		
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Standard Grade Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Save Standard Grade !! [' . $e->getMessage() . ']']);           
        }
    }
	
	
	public function get_grade_edit(Request $request) {
        $data = [
            'id_job_grade' => $request->id_job_grade
        ];
        $result = PaGrade::get_grade_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_question() {
        $result = PaGrade::get_question();
        return response()->json($result);
    }
	
	public function get_level(Request $request) {
		$id_pa_question = $request->get('id_pa_question') ?? null;
        $result = PaGrade::get_level($id_pa_question);
        return response()->json($result);
    }
	
}
