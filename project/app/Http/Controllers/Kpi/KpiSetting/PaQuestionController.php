<?php
namespace App\Http\Controllers\Kpi\KpiSetting;

use App\Models\Kpi\KpiSetting\PaQuestion;
use App\Models\Kpi\KpiSetting\PaAnswer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PaQuestionController extends Controller {

     public function index(Request $request) {
        if ($request->ajax()) {
            $data = PaQuestion::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_pa_question . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';

                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_pa_question . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.kpi_settings.master_kpi_question.index');
    }
	
	protected function validateQuestion(Request $request) {

        $arr_form_validate = [
            'sequence' => 'required',
            'description' => 'required',
        ];
        $arr_msg_form_validate = [
            'sequence.required' => 'The Sequence field is required',
            'description.required' => 'The Question field is required',            
        ];
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
        $this->validateQuestion($request);
		try{
            DB::beginTransaction();
        $form_data = array(
            'sequence' => $request->sequence,
            'description' => $request->description,
            'id_question_type' => $request->id_question_type,
            'question_pa_type' => $request->question_pa_type,
            'id_question_group' => $request->id_question_group,
            'notes' => $request->notes,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
       
        $question = PaQuestion::create($form_data);      
        if ($request->answer != null) {
            foreach ($request->answer as $key => $value) {
                $form_answer = array(
                    'id_pa_question' => $question->id_pa_question,
                    'description' => $value['desc_answer'],                  
                    'sequence' => $value['sequence'],
                    'weight_score' => $value['weight_score'],                  
                    'is_corrected_answer' => isset($value['is_corrected_answer']) == 'on' ? 1 : 0,
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                );              
                $answer = PaAnswer::create($form_answer);              
            }           
        }
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Question Answer Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Save Question Answer !! [' . $e->getMessage() . ']']);           
        }
    }
	
	protected function update(Request $request) {
        $this->validateQuestion($request);
		try{
            DB::beginTransaction();
        $form_data = array(
            'id_pa_question' => $request->id_pa_question,
            'sequence' => $request->sequence,
            'description' => $request->description,
            'id_question_type' => $request->id_question_type,
            'question_pa_type' => $request->question_pa_type,
            'id_question_group' => $request->id_question_group,
            'notes' => $request->notes,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
       
        $question = PaQuestion::findOrFail($request->id_pa_question)->update($form_data);
		
		$listIdAnswer    = [];
        $idAnswer      = [];
		if(PaAnswer::where('id_pa_question', $request->id_pa_question)->first() != null){
			$listIdAnswer = PaAnswer::where('id_pa_question', $request->id_pa_question)->where('id_company', session('id_company'))->get()->pluck('id_pa_answer')->all();
		}
		if ($request->answer) {
			foreach ($request->answer as $key => $value) {
				if ($value['id_pa_answer'] == "") {
					PaAnswer::create(array(
						'id_pa_question' => $request->id_pa_question,
						'description' =>  $value['desc_answer'],
						'sequence' =>  $value['sequence'],
						'weight_score' =>  $value['weight_score'],
						'is_corrected_answer' =>   isset($value['is_corrected_answer']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					$idAnswer[] = $value['id_pa_answer'];
					PaAnswer::where('id_pa_answer', $value['id_pa_answer'])->update(array(
						'description' =>  $value['desc_answer'],
						'sequence' =>  $value['sequence'],
						'weight_score' =>  $value['weight_score'],
						'is_corrected_answer' =>   isset($value['is_corrected_answer']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		$diff = array_diff($listIdAnswer, $idAnswer);
		if(count($diff) > 0){
			foreach ($diff as $key => $value) { 
				PaAnswer::where('id_pa_answer', $value)->delete();
			}
		}
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Question Answer Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Save Question Answer !! [' . $e->getMessage() . ']']);           
        }
    }

	public function destroy($id) {
		try{
            DB::beginTransaction();
			$data = PaQuestion::findOrFail($id);
			$data->delete();
		 DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Success Delete']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Delete Question Answer !! [Answers is not Blank]']);           
        }
    }
	
	public function get_question_edit(Request $request) {
        $data = [
            'id_pa_question' => $request->id_pa_question
        ];
        $result = PaQuestion::get_question_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_question_type() {
        $result = PaQuestion::get_question_type();
        return response()->json($result);
    }
	
	public function get_question_group() {		
        $result = PaQuestion::get_question_group();
        return response()->json($result);
    }
}
