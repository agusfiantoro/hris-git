<?php

namespace App\Http\Controllers\Task\MissionsReview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Task\Missions\Missions;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\API\BaseController;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class MissionsReviewController extends Controller
{
	public function index(Request $request) {
        if ($request->ajax()) {
            $data = Missions::getdataReview($request->myData);
			foreach($data as $key=>$val){
				$startTime = date('H:i',strtotime($val->start_date));
				$endTime = date('H:i',strtotime($val->end_date));
				$completeTime = date('H:i',strtotime($val->completion_date));
				$startDateTime = Carbon::parse($val->start_date)->translatedFormat('d M Y')." ".$startTime;
				$endDateTime = Carbon::parse($val->end_date)->translatedFormat('d M Y')." ".$endTime;
				$completeDateTime = Carbon::parse($val->completion_date)->translatedFormat('d M Y')." ".$completeTime;
				$data[$key]->start_date = $startDateTime;
				$data[$key]->end_date = $endDateTime;
				$data[$key]->completion_date = $val->completion_date != null ? $completeDateTime : "-";
			}
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								if($data->completion_date != '-'){
									$onclick = "loadedit(".$data->id_task_activity_answer.",'".$data->evidence_type."',".$data->score_answer.")";
									$button = '<button type="button" onclick="'.$onclick.'" class="btn btn-success" style="padding:2px 5px 0px 5px;" title="Scoring"><span style="font-size:16px;" class="fas fa-list-alt"></span></button> ';	
									return $button;
								}
							})
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('task.missions.missions_review.index');
    }
	
	public function modal_upload(Request $request) {
			
			$id_task_activity_answer = $request->id_task_activity_answer;
			$type = $request->type;
			$score = $request->score;
			if($type == 'Document'){
				$view = view('task.missions.missions_review.modal_upload', compact('id_task_activity_answer','type','score'))->render();
			}
			else if($type == 'Photo' || $type == 'GPS'){
				$view = view('task.missions.missions_review.modal_photo', compact('id_task_activity_answer','type','score'))->render();
			}
			else if($type == 'Essay'){
				$view = view('task.missions.missions_review.modal_essay', compact('id_task_activity_answer','type','score'))->render();
			}
			else if($type == 'Video'){
				$view = view('task.missions.missions_review.modal_video', compact('id_task_activity_answer','type','score'))->render();
			}     	
			return response()->json(['status' => 'true', 'view' => $view]);
		
    }
	
	public function get_score(Request $request) {		
        $res = Missions::get_score($request->id_task_activity_answer);
		$sc = 0;
		$notes = "";
		$act_text = "";
		$evi_text = "";
		if(count($res) > 0 ){
			$sc =  $res[0]->maximum_score;
			$notes = $res[0]->note_rejected;
			$act_text = $res[0]->activity;
			$evi_text = $res[0]->target_evidence;
		}
		$result['score'][0]['id'] = 0;
		$result['score'][0]['text'] = 0;
		$result['score'][1]['id'] = $sc;
		$result['score'][1]['text'] = $sc;
		$result['notes'] = $notes;
		$result['act_text'] = $act_text;
		$result['evi_text'] = $evi_text;
        return response()->json($result);
    }
	
	public function get_photo(Request $request) {
        $result = Missions::get_photo($request->id_task_activity_answer);
		foreach($result as $key=>$val){
			$createdTime = date('H:i',strtotime($val->creation_date));
			$createdDateTime = Carbon::parse($val->creation_date)->translatedFormat('d M Y')." ".$createdTime;
			$result[$key]->creation_date = $val->creation_date != null ? $createdDateTime : "-";
		}
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_doc(Request $request) {
        $result = Missions::get_photo($request->id_task_activity_answer);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_essay(Request $request) {
		$result = Missions::get_essay($request->id_task_activity_answer);
	//	dd($result);
		return response()->json($result);
	}
	
	public function get_search_emp(Request $request) {
		$result = Missions::get_search_emp();
	//	dd($result);
		return response()->json($result);
	}
	
	protected function validateReq(Request $request) {
        $arr_form_validate = [
            'score' => 'required',
            'desc_review' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'score.required' => 'The Score field is required',
            'desc_review.required' => 'The Notes field is required',
        ];
		
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$data = Missions::where('id_task_activity_answer',$request->id_task_activity_answer)->first();
			$data->score_answer = $request->score;
			if($request->score != 0){
				$data->status_answer = true;
			}
			else{
				$data->status_answer = false;
			}
			$data->note_rejected = $request->desc_review;
			$data->save();
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Data Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Data !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	public function get_month(Request $request) {
		$result = Missions::get_month();
	//	dd($result);
		return response()->json($result);
	}
	
	public function getLeaderboard(Request $request) {
        if ($request->ajax()) {
            $data = Missions::getLeaderboard($request->month_search,$request->emp_search);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->make(true);
        }
        return view('task.missions.leaderboard.index');
    }
	
	public function getSummary(Request $request) {
        if ($request->ajax()) {
            $data = Missions::getSummary($request->month_search,$request->emp_search);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->make(true);
        }
        return view('task.missions.task_summary.index');
    }
	
}