<?php

namespace App\Http\Controllers\Task\MasterTask;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Task\MasterTask\MasterTask;
use App\Models\Task\MasterTask\MasterActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class MasterTaskController extends Controller
{
	protected function validateSearch(Request $request) {
	//	dd($request->all());
        $arr_form_validate = [
            'dept_search' => 'required',          
        ];
	
        $arr_msg_form_validate = [
            'dept_search.required' => 'The Department field is required',
        ];
		
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	public function index(Request $request) {
        if ($request->ajax()) {
			$this->validateSearch($request);
			try{
				DB::beginTransaction();
				
				$data = MasterTask::getdata($request->dept_search);		
				return DataTables::of($data)
								->addIndexColumn()
								->addColumn('', function($data) {
									$a = '';
									return $a;
								})
								->addColumn('action', function($data) {
									$onclick = "loadedit(".$data->id_task.")";
									$buttonEdit = '&nbsp;<button type="button" name="edit" onclick="'.$onclick.'" id="' . $data->id_task . '" class="edit btn btn-edit btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> '; 
									$returnButton = $buttonEdit;
									return $returnButton;
								})
								->rawColumns(['action'])
								->make(true);
								
								
			}
			catch (\Exception $e) {
				DB::rollBack();
				Log::error($e);
				return response()->json();           
			}
        }
        return view('task.master_task.task_activity.index');
    }
	
	public function modal_detail(Request $request) {
		$global_task = $request->global_task;
        return view('task.master_task.task_activity.modal_detail', compact('global_task'));
    }
	
	public function get_dept() {		
        $result = MasterTask::get_dept();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_grade(Request $request) {		
        $result = MasterTask::get_grade($request->id_dept);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_edit(Request $request) {
		$result = MasterTask::get_edit($request->id_task);
		foreach($result['res'] as $key=>$val){
			$start = explode(":",$val->start_time);
			$end = explode(":",$val->end_time);
			$result['res'][$key]->start_time = $start[0].":".$start[1];
			$result['res'][$key]->end_time = $end[0].":".$end[1];
		}
		return response()->json($result);
	}
	
	protected function validateReq(Request $request) {
	//	dd($request->all());
        $arr_form_validate = [
            'desc' => 'required',
            'id_dept' => 'required',
            'id_grade' => 'required',
            'task_type' => 'required',
            'status' => 'required',
            'task.*.activity' => 'required',
            'task.*.seq' => 'required',
            'task.*.evidence' => 'required',
            'task.*.evidence_type' => 'required',
            'task.*.task_cycle' => 'required',
            'task.*.start_time' => 'required',
            'task.*.end_time' => 'required',
            'task.*.max_score' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'desc.required' => 'The Description field is required',
            'id_dept.required' => 'The Department field is required',
            'id_grade.required' => 'The Grade field is required',
            'task_type.required' => 'The Task Type field is required',
            'status.required' => 'The Status field is required',
            'task.*.activity.required' => 'The Activity Status field is required',
            'task.*.seq.required' => 'The Sequence field is required',
            'task.*.evidence.required' => 'The Evidence field is required',
            'task.*.evidence_type.required' => 'The Evidence Type field is required',
            'task.*.task_cycle.required' => 'The Task Cycle field is required',
            'task.*.start_time.required' => 'The Start Time field is required',
            'task.*.end_time.required' => 'The End Time field is required',
            'task.*.max_score.required' => 'The Score field is required',
        ];
		
        if ($request->post('task') == null) {
            $validate_emprequest = ['table_rec_detail' => 'required|string'];
            $validate_msg_emprequest = ['table_rec_detail.required' => 'Activity cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
		
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
		$this->validateReq($request);
	//	dd($request->all());
		try{
			DB::beginTransaction();
			$data = New MasterTask();
			$data -> description = $request->desc;
			$data -> notes = $request->note;
			$data -> id_department = $request->id_dept;
			$data -> job_class_group = $request->id_grade;
			$data -> task_type = $request->task_type;
			$data -> status = $request->status;
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			foreach ($request->task as $key => $value) {
				$data_form = new MasterActivity();
				$data_form -> id_task = $data->id_task;
				$data_form -> sequence = $value['seq'];
				$data_form -> activity = $value['activity'];
				$data_form -> notes = $value['notes'];
				$data_form -> target_evidence = $value['evidence'];
				$data_form -> evidence_type = $value['evidence_type'];
				$data_form -> task_cycle = $value['task_cycle'];
				$data_form -> start_time = $value['start_time'];
				$data_form -> end_time = $value['end_time'];
				if(isset($value['multi_attach'])){
					if($value['multi_attach'] == 'on'){
						$data_form -> multiple_attachment_flag = true;
					}
				}
				else{
					$data_form -> multiple_attachment_flag = false;
				}
				
				$data_form -> link_question_task = $value['link_question'];
				$data_form -> photo_question_task = $value['photo_question'];
				if(isset($value['random_object'])){
					if($value['random_object'] == 'on'){
						$data_form -> random_object_flag = true;
					}
				}
				else{
					$data_form -> random_object_flag = false;
				}
				if(isset($value['ans_flag'])){
					if($value['ans_flag'] == 'on'){
						$data_form -> answer_type = true;
					}
				}
				else{
					$data_form -> answer_type = false;
				}
				$data_form -> answer_type = $value['ans_type'];
				$data_form -> minimum_range = $value['min_range'];
				$data_form -> maximum_range = $value['max_range'];
				$data_form -> dictionary_correct_answer = $value['dic_correct'];
				$data_form -> maximum_score = $value['max_score'];
				if(isset($value['status'])){
					if($value['status'] == 'on'){
						$data_form -> status = 'A';
					}
				}
				else{
					$data_form -> status = 'I';
				}
				$data_form -> id_company = session('id_company');
				$data_form -> created_by = session('id_user');                        
				$data_form -> save();			
			}
			
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Task Activity Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Task Activity !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update(Request $request) {
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$data = MasterTask::where('id_task',$request->id_task)->first();;
			$data -> description = $request->desc;
			$data -> notes = $request->note;
			$data -> id_department = $request->id_dept;
			$data -> job_class_group = $request->id_grade;
			$data -> task_type = $request->task_type;
			$data -> status = $request->status;
			$data -> updated_by = session('id_user');
			$data -> save();
			
			$listId    = [];
			$idDetail     = [];
			if(MasterActivity::where('id_task', $request->id_task)->first() != null){
				$listId = MasterActivity::where('id_task', $request->id_task)->where('id_company', session('id_company'))->get()->pluck('id_activity')->all();
			}
			foreach ($request->task as $key => $value) {
				if ($value['id_activity'] == "") {
					$detail = New MasterActivity();
					$detail -> id_task = $request->id_task;
					$detail -> sequence = $value['seq'];
					$detail -> activity = $value['activity'];
					$detail -> notes = $value['notes'];
					$detail -> target_evidence = $value['evidence'];
					$detail -> evidence_type = $value['evidence_type'];
					$detail -> task_cycle = $value['task_cycle'];
					$detail -> start_time = $value['start_time'];
					$detail -> end_time = $value['end_time'];
					if(isset($value['multi_attach'])){
						if($value['multi_attach'] == 'on'){
							$detail -> multiple_attachment_flag = true;
						}
					}
					else{
						$detail -> multiple_attachment_flag = false;
					}
					$detail -> link_question_task = $value['link_question'];
					$detail -> photo_question_task = $value['photo_question'];
					if(isset($value['random_object'])){
						if($value['random_object'] == 'on'){
							$detail -> random_object_flag = true;
						}
					}
					else{
						$detail -> random_object_flag = false;
					}
					if(isset($value['ans_flag'])){
						if($value['ans_flag'] == 'on'){
							$detail -> answer_type = true;
						}
					}
					else{
						$detail -> answer_type = false;
					}
					$detail -> answer_type = $value['ans_type'];
					$detail -> minimum_range = $value['min_range'];
					$detail -> maximum_range = $value['max_range'];
					$detail -> dictionary_correct_answer = $value['dic_correct'];
					$detail -> maximum_score = $value['max_score'];
					if(isset($value['status'])){
						if($value['status'] == 'on'){
							$detail -> status = 'A';
						}
					}
					else{
						$detail -> status = 'I';
					}
					$detail -> id_company = session('id_company');		
					$detail -> created_by = session('id_user');        			
					$detail -> save();
				}
				else {
					$idDetail[] = $value['id_activity'];
					$detail = MasterActivity::where('id_activity', $value['id_activity'])->first();
					$detail -> sequence = $value['seq'];
					$detail -> activity = $value['activity'];
					$detail -> notes = $value['notes'];
					$detail -> target_evidence = $value['evidence'];
					$detail -> evidence_type = $value['evidence_type'];
					$detail -> task_cycle = $value['task_cycle'];
					$detail -> start_time = $value['start_time'];
					$detail -> end_time = $value['end_time'];
					if(isset($value['multi_attach'])){
						if($value['multi_attach'] == 'on'){
							$detail -> multiple_attachment_flag = true;
						}
					}
					else{
						$detail -> multiple_attachment_flag = false;
					}
					$detail -> link_question_task = $value['link_question'];
					$detail -> photo_question_task = $value['photo_question'];
					if(isset($value['random_object'])){
						if($value['random_object'] == 'on'){
							$detail -> random_object_flag = true;
						}
					}
					else{
						$detail -> random_object_flag = false;
					}
					if(isset($value['ans_flag'])){
						if($value['ans_flag'] == 'on'){
							$detail -> answer_type = true;
						}
					}
					else{
						$detail -> answer_type = false;
					}
					$detail -> answer_type = $value['ans_type'];
					$detail -> minimum_range = $value['min_range'];
					$detail -> maximum_range = $value['max_range'];
					$detail -> dictionary_correct_answer = $value['dic_correct'];
					$detail -> maximum_score = $value['max_score'];
					if(isset($value['status'])){
						if($value['status'] == 'on'){
							$detail -> status = 'A';
						}
					}
					else{
						$detail -> status = 'I';
					}
					$detail -> updated_by = session('id_user');
					$detail -> save();
				}
					
			}
			$diff = array_diff($listId, $idDetail);
			if(count($diff) > 0){
				foreach ($diff as $key => $value) { 
					MasterActivity::where('id_activity', $value)->delete();
				}
			}
			
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Task Activity Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Task Activity !! [' . $e->getMessage() . ']']);           
		}	
	}
	
}