<?php

namespace App\Http\Controllers\Task\TaskAssignment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Task\Task\TaskAssignment;
use App\Models\Task\Task\TaskActivity;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Employee\Employee\Employee;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Spipu\Html2Pdf\Html2Pdf;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\Crypt;

class TaskAssignmentController extends Controller
{
	protected function accessBranch(Request $request) {
		$data_access = Employee::get_access($request->id_url);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}

			return $group_branch;
	}
	
	public function index(Request $request) {
        if ($request->ajax()) {
            $data = TaskAssignment::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$onclick = "loadedit(".$data->id_task_management.")";
                                $buttonEdit = '&nbsp;<button type="button" name="edit" onclick="'.$onclick.'" id="' . $data->id_task_management . '" class="edit btn btn-edit btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> '; 
							//	$buttonView = '&nbsp;<button type="button" name="view" id="' . $data->id_task_management . '" class="edit btn btn-view btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
								$returnButton = $buttonEdit;
								return $returnButton;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('task.task.task_assignment.index');
    }
	
	public function modal_detail(Request $request) {
		$global_task = $request->global_task;
        return view('task.task.task_assignment.modal_detail', compact('global_task'));
    }
	
	public function get_dept() {		
        $result = TaskAssignment::get_dept();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_grade(Request $request) {		
        $result = TaskAssignment::get_grade($request->id_dept);
	//	dd($result);
        return response()->json($result);
    }
	public function get_position(Request $request) {		
        $result = TaskAssignment::get_position($request->id_dept,$request->id_grade);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_employee_by() {		
        $result = TaskAssignment::get_employee_by();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_respon(Request $request) {		
        $result = TaskAssignment::get_respon($request->id_dept);
	//	dd($result);
        return response()->json($result);
    }
/*	
	public function get_task(Request $request) {	
        $result = TaskAssignment::get_task($request->id_pos);
        return response()->json($result);
    }
	
	public function get_activity(Request $request) {	
        $result = TaskAssignment::get_activity($request->id_task);
        return response()->json($result);
    }
	
	public function get_detail_act(Request $request) {	
        $result = TaskAssignment::get_detail_act($request->id_activity);
        return response()->json($result);
    }
*/	
	public function get_validate(Request $request) {	
//	dd($request->all());
        $arr_form_validate = [
        //    'id_pos' => 'required',
            'id_dept' => 'required',
            'id_grade' => 'required',
        ];
		$arr_msg_form_validate = [
        //    'id_pos.required' => 'The Position field is required',
            'id_dept.required' => 'The Department field is required',
            'id_grade.required' => 'The Grade field is required',
        ];
		
		$request->validate($arr_form_validate, $arr_msg_form_validate);
		return response()->json(['status' => 'true']);
    }
	
	public function modal_list(Request $request) {
	//	$exPos = implode(",",$request->global_id_position);
	//	$global_id_position = $exPos;
		$global_dept = $request->global_dept;
		$global_grade = $request->global_grade;
        return view('task.task.task_assignment.modal_list', compact('global_dept','global_grade'));
    }
	
	public function list_task(Request $request) {
		if ($request->ajax()) {		
		$result = TaskAssignment::get_task_list($request->id_dept,$request->id_grade);
	//	dd($result);
		return DataTables::of($result)
					->addIndexColumn()
					->make(true);		
		}
	}
	
	public function task_assign(Request $request) {
		$result = TaskAssignment::get_task_assign($request->arr_task);
		foreach($result as $key=>$val){
			$result[$key]->activity = explode("$$$",$val->activity);
			$result[$key]->target_evidence = explode("$$$",$val->target_evidence);
			$result[$key]->evidence_type = explode("$$$",$val->evidence_type);
			$result[$key]->task_cycle = explode("$$$",$val->task_cycle);
			$result[$key]->time = explode("$$$",$val->time);
			$result[$key]->maximum_score = explode("$$$",$val->maximum_score);		
		}
//		dd($result);
		return response()->json($result);
	}
	
	public function get_edit(Request $request) {
		$result = TaskAssignment::get_edit($request->id_task_management);
		$arPos = explode(',', trim($result['id_position_routing'],'{}'));
		$result['id_position_routing'] = $arPos;
	//	dd($result);
		return response()->json($result);
	}
	
	protected function validateReq(Request $request) {
	//	dd($request->all());
        $arr_form_validate = [
            'desc' => 'required',
            'id_dept' => 'required',
            'id_grade' => 'required',
            'id_pos' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'id_respon' => 'required',
            'task.*.random_type' => 'required',
        //    'task.*.id_task' => 'required',
        //    'task.*.id_activity' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'desc.required' => 'The Description field is required',
            'id_dept.required' => 'The Department field is required',
            'id_grade.required' => 'The Grade field is required',
            'id_pos.required' => 'The Position field is required',
            'start_date.required' => 'The Period Date (Start) field is required',
            'end_date.required' => 'The Period Date (End) field is required',
            'id_respon.required' => 'The Responsible By field is required',
            'task.*.random_type.required' => 'The Activity Status field is required',
         //   'task.*.id_task.required' => 'The Task field is required',
         //   'task.*.id_activity.required' => 'The Activity field is required',
        ];
		
        if ($request->post('task') == null) {
            $validate_emprequest = ['table_rec_detail' => 'required|string'];
            $validate_msg_emprequest = ['table_rec_detail.required' => 'Transactions cannot empty'];
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
			$data = New TaskAssignment();
			$data -> description = $request->desc;
			$data -> notes = $request->note;
			$data -> id_department = $request->id_dept;
			$data -> job_class_group = $request->id_grade;
			$data -> id_position_routing = "{".implode(',',$request->id_pos)."}";
			$data -> start_date = $request->start_date;
			$data -> end_date = $request->end_date;
			$data -> id_responsible_by = $request->id_respon;
			$data -> id_managed_by = $request->id_managed;
			$data -> status = $request->status;
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			
			$all = collect($request->task)->groupBy('id_task')->toArray();
			$x = [];
			foreach($all as $key=>$value){
				foreach($value as $k=>$val){
					if($val['random_type'] == 'Automatic'){
						$x[$key]['act'][] = $val['id_activity'];
						$x[$key]['random_type'] = $val['random_type'];
					}
				}			
			}
			if(count($x) > 0){
				foreach($x as $k=>$val){
					$data_form = new TaskActivity();
					$data_form -> id_task_management = $data->id_task_management;
					$data_form -> id_task = $k;
					$data_form -> id_activity = "{".implode(',',$val['act'])."}";
					$data_form -> random_type = $val['random_type'];
					$data_form -> id_company = session('id_company');
					$data_form -> created_by = session('id_user');         
					$data_form -> save();
				}
			}
			foreach ($request->task as $key => $value) {
				$data_form = new TaskActivity();
				$data_form -> id_task_management = $data->id_task_management;
				$data_form -> id_task = $value['id_task'];
				$data_form -> random_type = $value['random_type'];
				$data_form -> id_company = session('id_company');
				$data_form -> created_by = session('id_user');         
				if($value['random_type'] == 'All'){
					$data_form -> id_activity = null;
					$data_form -> save();
				}
				else if($value['random_type'] == 'Manual'){
					$data_form -> id_activity = "{".$value['id_activity']."}";
					$data_form -> save();
				}								
			}							
		DB::commit();
			return response()->json(['status' => 'true', 'id_task_management' => $data->id_task_management, 'message' => 'Assignment Task Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'id_task_management' => null, 'message' => 'Cannot Save Assignment Task !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update(Request $request) {
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$data = TaskAssignment::where('id_task_management',$request->id_task_management)->first();
			$data -> description = $request->desc;
			$data -> notes = $request->note;
			$data -> id_department = $request->id_dept;
			$data -> job_class_group = $request->id_grade;
			$data -> id_position_routing = "{".implode(',',$request->id_pos)."}";
			$data -> start_date = $request->start_date;
			$data -> end_date = $request->end_date;
			$data -> id_responsible_by = $request->id_respon;
			$data -> id_managed_by = $request->id_managed;
			$data -> status = $request->status;
			$data -> updated_by = session('id_user');
			$data -> save();
			
			$listId    = [];
			$idDetail  = [];
			if(TaskActivity::where('id_task_management', $request->id_task_management)->whereIn('random_type', ['All','Manual'])->first() != null){
				$listId = TaskActivity::where('id_task_management', $request->id_task_management)->whereIn('random_type', ['All','Manual'])->where('id_company', session('id_company'))->get()->pluck('id_task_activity')->all();
			}
			
			$listIdRan    = [];
			$idDetailRan  = [];
			if(TaskActivity::where('id_task_management', $request->id_task_management)->where('random_type', 'Automatic')->first() != null){
				$listIdRan = TaskActivity::where('id_task_management', $request->id_task_management)->where('random_type', 'Automatic')->where('id_company', session('id_company'))->get()->pluck('id_task_activity')->all();
			}
			$x = [];
			$all = collect($request->task)->whereNull('id_task_activity')->groupBy('id_task')->toArray();
			foreach($all as $key=>$value){
				foreach($value as $k=>$val){
					if($val['random_type'] == 'Automatic'){
						$x[$key]['act'][] = $val['id_activity'];
						$x[$key]['random_type'] = $val['random_type'];
						$x[$key]['id_task_activity'] = $val['id_task_activity'];
						$x[$key]['id_activity'] = $val['id_activity'];
					}
				}			
			}
		//	dd($x);
			foreach($x as $k=>$val){
				if ($val['id_task_activity'] == null) {
					$data_form = new TaskActivity();
					$data_form -> id_task_management = $data->id_task_management;
					$data_form -> id_task = $k;
					$data_form -> id_activity = "{".implode(',',$val['act'])."}";
					$data_form -> random_type = $val['random_type'];
					$data_form -> id_company = session('id_company');
					$data_form -> created_by = session('id_user');         
					$data_form -> save();
				}
				else{
					$idDetailRan[] = $val['id_task_activity'];
				}
			}
			
			foreach ($request->task as $key => $value) {
				if ($value['random_type'] == 'All' || $value['random_type'] == 'Manual') {
					if ($value['id_task_activity'] == "") {
						$detail = New TaskActivity();
						$detail -> id_task_management = $request->id_task_management;
						$detail -> id_task = $value['id_task'];
						if($value['random_type'] == 'All'){
							$detail -> id_activity = null;
						}
						else if($value['random_type'] == 'Manual'){
							$detail -> id_activity = "{".$value['id_activity']."}";
						}
						$detail -> random_type = $value['random_type'];
						$detail -> id_company = session('id_company');
						$detail -> created_by = session('id_user');
						$detail -> save();
					}
					else {
						$idDetail[] = $value['id_task_activity'];
						$detail = TaskActivity::where('id_task_activity', $value['id_task_activity'])->first();
						$detail -> id_task = $value['id_task'];
						if($value['random_type'] == 'All'){
							$detail -> id_activity = null;
						}
						else if($value['random_type'] == 'Manual'){
							$detail -> id_activity = "{".$value['id_activity']."}";
						}
						$detail -> random_type = $value['random_type'];
						$detail -> updated_by = session('id_user');
						$detail -> save();
					}				
				}
					
			}
			
			$diff = array_diff($listId, $idDetail);
			if(count($diff) > 0){
				foreach ($diff as $key => $value) { 
					TaskActivity::where('id_task_activity', $value)->delete();
				}
			}
			
			$diffRan = array_diff($listIdRan, $idDetailRan);
			if(count($diffRan) > 0){
				foreach ($diffRan as $key => $value) { 
					$arr[$value] = [];
					foreach ($request->task as $k => $val) {
						if($val['id_task_activity'] == $value){
							$arr[$value]['actAfter'][] = $val['id_activity'];
						}
						
					}	
				}	
				if(count($arr) > 0){
					foreach($arr as $i => $v){
						if(count($v) > 0){
							$dataForm = TaskActivity::where('id_task_activity', $i)->first();
							$dataForm -> id_activity = "{".implode(',',$v['actAfter'])."}";;
							$dataForm -> save();
						}
						else{
						//	$a = [];
							TaskActivity::where('id_task_activity', $i)->delete();
						}
					
					}
				}
				
			}
			
		DB::commit();
			return response()->json(['status' => 'true', 'id_task_management' => $request->id_task_management, 'message' => 'Assignment Task Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'id_task_management' => null, 'message' => 'Cannot Update Assignment Task !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	public function gen_task(Request $request) {
		try{
			DB::beginTransaction();
			$result = TaskAssignment::gen_task($request->id_task_management,session('id_company'));
			$data = TaskActivity::where('id_task_management',$request->id_task_management)->get();
			foreach($data as $key=>$val){
				$val['last_date_generated'] = date('Y-m-d');
				$val->save();
			}
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'generate Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Generate Failed !! [' . $e->getMessage() . ']']);           
		}	
	}
	
}