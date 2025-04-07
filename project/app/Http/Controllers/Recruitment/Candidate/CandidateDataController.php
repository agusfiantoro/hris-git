<?php
namespace App\Http\Controllers\Recruitment\Candidate;

use App\Models\Recruitment\Candidate\Candidate;
use App\Models\Recruitment\Candidate\CandidateData;
use App\Models\Recruitment\Candidate\AppliedHistory;
use App\Models\Recruitment\HiringRequest\HiringRequest;
use App\Models\Recruitment\HiringRequest\HiringDetail;
use App\Models\Recruitment\MasterInterview\AnswerHeader;
use App\Models\Recruitment\MasterInterview\AnswerDetail;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Recruitment\Batch\BatchParticipant;
use App\Models\Recruitment\Batch\MasterBatch;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spipu\Html2Pdf\Html2Pdf;
use GuzzleHttp\Client;

class CandidateDataController extends Controller {
	
	protected $urls = [
		'https://career.borwita.co.id/', // default
		'http://103.149.176.106/home', // diberi home agar tidak diredirect ke https
		'http://192.168.122.38/home',
	];

	public function __construct() {
		$this->servercareer = $this->urls[0];
    }

	protected function getValidServerCareer() {
		$found = false;
		foreach($this->urls as $url) {
			try {
				$options = [
					'verify' => false,
					'Accept' => 'application/json', 
				];
				// dump($url);
				$client = new Client();
				$body = $client->get($url, $options)->getBody()->getContents();
				$this->servercareer = str_replace('home', '', $url);
				$found = true;
				break;
			} catch(\Exception $e) {
				// dump($e->getMessage());
				// continue;
			}
		}
		// No valid server found, fallback to default server
		if(!$found) $this->servercareer = $this->urls[0];
		return $this->servercareer;
	}

	public function index_group(Request $request) {    	
        if ($request->ajax()) {
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
            $data = CandidateData::get_group($group_branch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) {
						$button = '<button style="color:white;" type="button" name="edit_group" id="' . $data->id_hiring_request_header . '" job="' .$data->description. '" class="edit_group btn btn-warning btn-sm" title="View"><span class="far fa-eye"></span></button> ';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	public function get_detail_group(Request $request) {    	
        if ($request->ajax()) {
			$mydata = array(
				'id_hiring_request_header' => $request->id_hiring_request_header,
			);
            $data = CandidateData::get_detail_group($mydata);
		//	dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) {
						if($data->id_applied_candidate){
							$id_applied_candidate = $data->id_applied_candidate;
						}
						else{
							$id_applied_candidate = "null";
						}
						if($data->id_dept && $data->id_job_grade){
							$id_dept = $data->id_dept;
							$id_job_grade = $data->id_job_grade;
						}
						else{
							$id_dept = "null";
							$id_job_grade = "null";
						}
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick = "loadprofile(".$data->id_candidate.",`".$id_applied_candidate."`,`".$data->name."`,`".$id_dept."`,`".$id_job_grade."`,`".$id_batch."`)";
						$button = '<button type="button" name="edit_can" id="' . $data->id_candidate . '" onclick="'.$onclick.'" class="edit_can btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
						$button .= '<button type="button" name="pdf" id="' . $data->id_candidate . '" class="pdf btn btn-success download" style="padding:4px 8px;" title="DCK"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';
						if($data->is_profil_completed) $button .= '<button type="button" class="btn btn-sm btn-warning assign-batch text-white" id-candidate="'.$data->id_candidate.'" title="Assign Batch"><i class="fa fa-user-plus"></i></button>';
						$button .= '&nbsp;<button type="button" style="display:none;" name="cancel" id="' . $data->id_applied_candidate . '" class="cancel_join btn btn-danger btn-sm" title="Cancel Hired"><span class="fa fa-close"></span></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function get_can_name(Request $request) {
	//	dd($request->id_hiring_header);
        $result = CandidateData::get_can_name($request->name, $request->limit);
        return response()->json($result);
    }
	
	public function index_can(Request $request) {    	
        if ($request->ajax()) {
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
            $data = CandidateData::get_can($request->fil_can,$request->daterange,$group_branch);
			// dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) {
						if($data->id_applied_candidate){
							$id_applied_candidate = $data->id_applied_candidate;
						}
						else{
							$id_applied_candidate = "null";
						}
						if($data->id_dept && $data->id_job_grade){
							$id_dept = $data->id_dept;
							$id_job_grade = $data->id_job_grade;
						}
						else{
							$id_dept = "null";
							$id_job_grade = "null";
						}
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick = "loadprofile(".$data->id_candidate.",`".$id_applied_candidate."`,`".$data->name."`,`".$id_dept."`,`".$id_job_grade."`,`".$id_batch."`)";
						$button = '<button type="button" name="edit_can" id="' . $data->id_candidate . '" onclick="'.$onclick.'" class="edit_can btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
						$button .= '<button type="button" name="pdf" id="' . $data->id_candidate . '" class="pdf btn btn-success download" style="padding:4px 8px;" title="DCK"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';
						$button .= '<button type="button" style="display:none;" name="cancel" id="' . $data->id_applied_candidate . '" class="cancel_join btn btn-danger btn-sm mr-1" title="Cancel Hired"><span class="fa fa-close"></span></button>';
						if($data->is_profil_completed) $button .= '<button type="button" class="btn btn-sm btn-warning assign-batch text-white" id-candidate="'.$data->id_candidate.'" title="Assign Batch"><i class="fa fa-user-plus"></i></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
    public function index(Request $request) {
        return view('recruitment.recruitment.candidate.index');
    }
	
	public function modal_detail(Request $request) {
		$global_id_candidate = $request->global_id_candidate;
		$global_id_applied = $request->global_id_applied;
		$global_id_dept = $request->global_id_dept;
		$global_id_job_grade = $request->global_id_job_grade;
		$global_id_batch = $request->global_id_batch;
        return view('recruitment.recruitment.candidate.modal_detail', compact('global_id_candidate','global_id_applied','global_id_dept','global_id_job_grade','global_id_batch'));
    }
	
	public function get_edit_detail(Request $request) {
		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
		);
        $result = CandidateData::get_edit_detail($data);
        return response()->json($result);
    }
	
/*	public function get_question(Request $request) {
		$data = array(
			'id_candidate_status' => $request->id_candidate_status,
		);
        $result = CandidateData::get_question($data);

        return response()->json($result);
    }
*/
	public function get_pos(Request $request) {
	//	dd($request->id_hiring_header);
        $result = CandidateData::get_pos();
        return response()->json($result);
    }
	public function get_pos_done(Request $request) {
        $result = CandidateData::get_pos_done($request->id_hiring_header);
        return response()->json($result);
    }
	public function get_pos_change(Request $request) {
		$data = array(
			'id_hiring_header' => $request->id_hiring_header,
		);
        $result = CandidateData::get_pos_change($data);
        return response()->json($result);
    }
	public function get_stage(Request $request) {
        $result = CandidateData::get_stage();
        return response()->json($result);
    }
	public function get_mass_stage(Request $request) {
        $result = CandidateData::get_mass_stage();
        return response()->json($result);
    }
/*	
	public function get_conclusion(Request $request) {
        $result = CandidateData::get_conclusion();
        return response()->json($result);
    }
*/
/*	public function check_stage(Request $request) {
		$data = [
            'code_stage' => $request->code_stage
        ];	
        $result = CandidateData::check_stage($data);
	//	dd($result);
        return response()->json($result);
    }
*/	
	public function get_family(Request $request) {
		$data = [
            'id_candidate' => $request->id_candidate
        ];	
        $result = CandidateData::get_family($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_ex(Request $request) {
		$data = [
            'id_candidate' => $request->id_candidate
        ];	
        $result = CandidateData::get_ex($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_skill(Request $request) {
		$data = [
            'id_candidate' => $request->id_candidate
        ];	
        $result = CandidateData::get_skill($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_cert(Request $request) {
		$data = [
            'id_candidate' => $request->id_candidate
        ];	
        $result = CandidateData::get_cert($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_interview(Request $request) {
		$data = [
            'id_candidate' => $request->id_candidate,
            'id_applied' => $request->id_applied,
        ];	
        $result = CandidateData::get_interview($data);
		
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_psychotest(Request $request) {
		$client = new Client();
		$data = [
            'id_candidate' => $request->id_candidate,
            'id_applied' => $request->id_applied,
            'id_dept' => $request->id_dept,
            'id_job_grade' => $request->id_job_grade,
            'id_batch' => $request->id_batch,
        ];	
		$url = self::getValidServerCareer()."psikogram/summary?id_user_assessment=".$data['id_candidate']."&user_type=candidate&id_department=".$data['id_dept']."&id_grade=".$data['id_job_grade']."&id_batch=".$data['id_batch']."&request_type=raw";
	//	dd($url);
		$options = [
            'verify' => false,
            'Accept' => 'application/json', 
        ];
        $response = $client->get($url, $options)->getBody()->getContents();		
		$decode 			= json_decode($response);
	//	$result			= [];
		$a = (object)[
			'id_candidate' =>@$data['id_candidate'],
			'id_dept' => @$data['id_dept'],
			'id_grade' => @$data['id_job_grade'],
			'id_batch' => @$data['id_batch'],
			'batch' => @$decode->identity->batch,
			'test_date' => @$decode->identity->test_date,
			'disc' => @$decode->result->profil,
			'disc_status' => @$decode->result->summary_test->disc,
			'papi' => @$decode->result->summary_test->papi,
			'kraepelin' => @$decode->result->summary_test->kraepelin,
			'bct' => @$decode->result->summary_test->bct,
			'disarankan' => @$decode->result->div_disarankan,
			'dipertimbangkan' => @$decode->result->div_dipertimbangkan,
			'tidak_disarankan' => @$decode->result->div_tidak_disarankan,
			'conclusion' => null,
		];
		$result[] = $a;
        return DataTables::of($result)
								->addIndexColumn()
								->addColumn('action', function($data) {
									if($data->id_dept && $data->id_grade){
										$id_dept = $data->id_dept;
										$id_grade = $data->id_grade;
									}
									else{
										$id_dept = "null";
										$id_grade = "null";
									}
									if($data->id_batch){
										$id_batch = $data->id_batch;
									}
									else{
										$id_batch = "null";
									}
									$onclick = 'download("'.self::getValidServerCareer().'psikogram/summary?id_user_assessment='.$data->id_candidate.'&user_type=candidate&id_department='.$data->id_dept.'&id_grade='.$data->id_grade.'&id_batch='.$data->id_batch.'&request_type=pdf")';
									$button = '<button type="button" name="pdf" onclick='.$onclick.' class="btn btn-success" style="padding:4px 8px;" title="Psikogram Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';
									return $button;
								})
								->rawColumns(['action'])
								->make(true);
    }
	
	protected function validateReq(Request $request) {
		$can_id_status = CandidateData::get_status($request->can_stage);
        $arr_form_validate = [
            'pos_req' => 'required',
            'can_stage' => 'required',
            'can_status' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'pos_req.required' => 'The Job Position field is required',
            'can_stage.required' => 'The Rec. Stage field is required',
            'can_status.required' => 'The Status field is required',
        ];
		
		if(isset($can_id_status->code,$request->can_status)){
			if($can_id_status->code == 'OFL' && $request->can_status == 'Pass'){
				$arr_form_validate['offering_date'] = 'required';
				$arr_msg_form_validate['offering_date.required'] = 'The Offering Date field is required';
			}
			else if($can_id_status->code == 'HIR' && $request->can_status == 'Pass'){
				$arr_form_validate['join_date'] = 'required';
				$arr_msg_form_validate['join_date.required'] = 'The Join Date field is required';
			}
		}
				
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function update(Request $request) {
		$this->validateReq($request);
		try{
				DB::beginTransaction();			
				$can_data = HiringRequest::where('id_hiring_request_header', $request->pos_req)->first();
				$track_view = Candidate::where('id_applied_candidate', $request->id_applied)->where('id_candidate', $request->id_candidate)->first();
				$can_view = CandidateData::where('id_candidate', $request->id_candidate)->first();
				$get_pos_can = Candidate::where('id_candidate', $request->id_candidate)->get();
				
				$form_data = array(
					'id_hiring_request_header' => $request->pos_req,
					'id_candidate_status' => $request->can_stage,
					'status' => $request->can_status,
				);
				$form_date = [];
				if(@$request->offering_date && $request->can_status == 'Pass'){
					$form_date['hired_date'] = @$request->offering_date ?? null;
				}
				else if(@$request->join_date && $request->can_status == 'Pass'){
					if($can_view['hired_date'] != null){
						$form_date['join_date'] =  @$request->join_date ?? null;
					}
					else{
					//	$form_date['hired_date'] = @$request->offering_date ?? null;
						$form_date['hired_date'] = @$request->join_date ?? null;
						$form_date['join_date'] =  @$request->join_date ?? null;	
					}		
				}
				$form_date['additional_note'] =  $request->add_note;
				$form_date['updated_by'] =  session('id_user');
				CandidateData::where('id_candidate', $request->id_candidate)->update($form_date);	
			//	dd($track_view);
				if(is_null($track_view)){
					$form_data['id_candidate'] =  $request->id_candidate;
					$form_data['applied_date'] =  date('Y-m-d');
					$form_data['id_company'] =  session('id_company');
					$form_data['created_by'] =  session('id_user');
				$applied_can = Candidate::create($form_data);
				}
				else{
					$applied_can = Candidate::where('id_applied_candidate', $request->id_applied)->where('id_candidate', $request->id_candidate)->first();
					$form_data['id_company'] =  $can_data['id_company'];
					$form_data['updated_by'] =  session('id_user');
					Candidate::where('id_applied_candidate', $request->id_applied)->where('id_candidate', $request->id_candidate)->update($form_data);
				}
				$can_id_status = CandidateData::get_status($request->can_stage);
				$view_id_status = CandidateData::get_status(@$track_view->id_candidate_status);
				if($can_id_status->code == 'HIR' && $request->can_status == 'Pass'){						
					$x=0;
					foreach($get_pos_can as $key=>$val){
						$cek_id_status = CandidateData::get_status($val['id_candidate_status']);
						if($cek_id_status->code == 'OFL' && $val['status'] == 'Pass'){
							$x = 0;
						}
						else if($cek_id_status->code == 'HIR' && $val['status'] == 'Pass'){
							$x=1;
						}
					}
				//	dd($x);
					if($x == 1){
						$titleError = "Candidate has been Hired in Other Position";
						throw new \Exception($titleError);
					}
					else{
						if($view_id_status->code == 'HIR' && $track_view->status == 'Pass'){
							$form_join = array(
								'join_date' => @$request->join_date ?? null,
								'additional_note' => @$request->add_note ?? null,
								'updated_by' =>  session('id_user'),
							);
							CandidateData::where('id_candidate', $request->id_candidate)->update($form_join);
						}
						else{
							$can_count = CandidateData::get_request_detail($request->pos_req);
							if(count($can_count) > 0){
								$detail_first = HiringDetail::where('id_hiring_request_header', $request->pos_req)->where('hiring_status','Hiring')->first();
								$header_hiring = CandidateData::get_request_hiring($request->pos_req);

								Candidate::where('id_applied_candidate', $request->id_applied)->where('id_candidate', $request->id_candidate)->update(['id_hiring_request_detail'=>$detail_first->id_hiring_request_detail]);
								
								$form_hired = array(
									'hiring_status'=>'Hired',
									'updated_by' => session('id_user'),
								);
								HiringDetail::where('id_hiring_request_detail', $detail_first->id_hiring_request_detail)->where('id_hiring_request_header',$detail_first->id_hiring_request_header)->update($form_hired);				
								if($header_hiring->count_req > 1){
									$form_partial = array(
										'hiring_request_status'=>'P',
										'updated_by' => session('id_user'),
									);
									HiringRequest::where('id_hiring_request_header', $request->pos_req)->update($form_partial);
								}
								else if($header_hiring->count_req == 1){
									$form_done = array(
										'hiring_request_status'=>'D',
										'updated_by' => session('id_user'),
									);
									HiringRequest::where('id_hiring_request_header', $request->pos_req)->update($form_done);
								}			
							}
							else{
								$titleError = "Quota is Full";
								throw new \Exception($titleError);
							}			
						}
					}					
				}	
			
			if($request->soal && $request->interview_date != null){
				$form_answer = array(
					'id_recruitment_question_group' => $request->id_recruitment_question_group,
					'id_candidate' => $request->id_candidate,
					'id_applied_candidate' => $request->id_applied,
					'interview_date' => $request->interview_date,
					'id_conclusion' => $request->id_conclusion,
					'notes' => $request->int_notes,
					'id_company' => session('id_company'),
				);
			//	dd($form_answer);
				$resapp = AnswerHeader::where('id_candidate', $request->id_candidate)->where('id_recruitment_question_group', $request->id_recruitment_question_group)->get();
				if($resapp->count() == 0){
					$form_answer['created_by'] = session('id_user');
					$res =	AnswerHeader::create($form_answer);  
				}
				else{
					$form_answer['updated_by'] = session('id_user');
					$res =	AnswerHeader::where('id_candidate',$request->id_candidate)->where('id_recruitment_question_group',$request->id_recruitment_question_group)->update($form_answer); 
				}
				$res_first = AnswerHeader::where('id_candidate', $request->id_candidate)->where('id_recruitment_question_group', $request->id_recruitment_question_group)->first();
				foreach ($request->soal as $key => $id_question) {
					$ansnote = 'ans_'.$id_question;
					$form_answer_detail = array(
						'id_recruitment_question' => $id_question,
						'id_recruitment_answer_header' => $res_first->id_recruitment_answer_header,
						'essay_answer' => $request->$ansnote,
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					$det_first = AnswerDetail::where('id_recruitment_question', $id_question)->where('id_recruitment_answer_header', $res_first->id_recruitment_answer_header)->first();
					if(!$det_first){
						$res =	AnswerDetail::create($form_answer_detail);  
					}
					else{
						$res =	AnswerDetail::where('id_recruitment_question', $id_question)->where('id_recruitment_answer_header', $res_first->id_recruitment_answer_header)->update($form_answer_detail);
					}
				}
			}
			
			$firstHistory = AppliedHistory::where('id_applied_candidate',$request->id_applied)->orderBy('id_applied_stage_history','desc')->first();
			
			$form_history = array(
			//	'id_applied_candidate' => $request->id_applied,
				'id_candidate_status' => $request->can_stage,
				'status' => $request->can_status,
				'start_date' => date('Y-m-d H:i:s' ),
				'created_by' => session('id_user'),
			);
			if($firstHistory == null){
				$form_history['id_applied_candidate'] = $applied_can->id_applied_candidate;
				if($can_id_status->code == 'HIR' && $request->can_status == 'Pass'){
					$form_history['end_date']	=  date('Y-m-d H:i:s' );
					AppliedHistory::create($form_history);
				}
				else{
					AppliedHistory::create($form_history);
				}				
			}
			else{
				$form_history['id_applied_candidate'] = $request->id_applied;
				AppliedHistory::where('id_applied_stage_history',$firstHistory->id_applied_stage_history)->update(['end_date' => date('Y-m-d H:i:s' )]);
				if($can_id_status->code == 'HIR' && $request->can_status == 'Pass'){
					$form_history['end_date']	=  date('Y-m-d H:i:s' );
					AppliedHistory::create($form_history);
				}
				else{
					AppliedHistory::create($form_history);
				}	
			}
			
		DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Update Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Update !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function cancel_join($id) {
		try{
			DB::beginTransaction();
			$cancel = CandidateData::cancel();
			$detail_first = Candidate::where('id_applied_candidate', $id)->first();
			$header_total = CandidateData::get_request_total($detail_first->id_hiring_request_header);
			Candidate::where('id_applied_candidate', $id)->update(array(
				'id_candidate_status' => $cancel->id_general_data,
			));
			HiringDetail::where('id_hiring_request_detail', $detail_first->id_hiring_request_detail)->update(['hiring_status'=>'Hiring']);			
			$header_hiring = CandidateData::get_request_hiring($detail_first->id_hiring_request_header);
			
			if($header_total->count_req == $header_hiring->count_req){
				$form_open = array(
					'hiring_request_status'=>'O',
					'updated_by' => session('id_user'),
				);
				HiringRequest::where('id_hiring_request_header', $detail_first->id_hiring_request_header)->update($form_open);
			}	
			else{
				$form_partial = array(
					'hiring_request_status'=>'P',
					'updated_by' => session('id_user'),
				);
				HiringRequest::where('id_hiring_request_header', $detail_first->id_hiring_request_header)->update($form_partial);
			}
			
			$form_data = array(
			//	'hired_date' => null,
				'join_date' => null,
				'updated_by' => session('id_user'),
			);
			CandidateData::where('id_candidate', $detail_first->id_candidate)->update($form_data);	
			
			Candidate::where('id_applied_candidate', $id)->update(array(
				'id_hiring_request_detail' => null,
			));
		
		DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
	
	public function download(Request $request) {
        ini_set('max_execution_time', -1);
		$client = new Client();
        $options = [
            'verify' => false,
            'Accept' => 'application/json', 
        ];
	//	dd($emp);
		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
		);

		$serverCareer = self::getValidServerCareer();
        $result = CandidateData::get_edit_detail($data);
		$photo = $serverCareer."storage/candidate_photo/".$result['photo_candidate'];	
		$cvLink = $serverCareer.'storage/curriculum_vitae/'.$result['cv_upload'];
	//	$photo = "http://my.borwita.co.id:8081/storage/candidate_photo/".$result['photo_candidate'];		
		try {
			$imagedata = $client->get($photo, $options)->getBody()->getContents();	
			// alternatively specify an URL, if PHP settings allow
	   		$base64 = base64_encode($imagedata);
			$src = 'data:image/jpeg;base64,'.$base64;
		} catch(\Exception $e) {
			$src = '';
		}
		
		$result['photo_candidate'] = $src;
		$result['family'] = CandidateData::get_family($data);
		$result['ex'] = CandidateData::get_ex($data);
		$result['skill'] = CandidateData::get_skill($data);
		$result['cert'] = CandidateData::get_cert($data);
		$result['branch'] = CandidateData::get_branch_user($data);
		$result['cv_link'] = $cvLink;
	//	dd($result);
		$html = view('recruitment.recruitment.candidate.download', compact('result'))->render();
	//	echo $html;
	//	die();
		
		$html2pdf = new Html2Pdf('P', 'A4', 'en'); //Landscape-A4-English
        $fileName = 'DCK_'.$result['name'].'.pdf';
        $html2pdf->writeHTML($html);
        $html2pdf->output($fileName);
    }
	
	 public function mass_submit(Request $request) {
        ini_set('max_execution_time', -1);
		$data_access = Employee::get_access($request->id_url);
		
		$request->validate([
            'mass_stage' => 'required|string',
            'mass_status' => 'required|string',
                ], [],
                [
                    'mass_stage' => 'Rec. Stage',
                    'mass_status' => 'Status',
        ]);
		
    	try{
			DB::beginTransaction();
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}
			if($group_branch != null){
				throw new \Exception('You are not Allowed');			
			}
			foreach($request->id_applied_candidate as $key=>$val){
				$can_applied = Candidate::where('id_applied_candidate', $val)->first();
				$list_stage[] = $can_applied->id_candidate_status;
			}
			$data_unique = count(array_unique($list_stage));
			if($data_unique != 1){
				throw new \Exception('Rec. Stage must be same');
			}
			else{
				foreach($request->id_applied_candidate as $key=>$val){
					Candidate::where('id_applied_candidate', $val)->update(['id_candidate_status'=>$request->mass_stage,'status'=>$request->mass_status]);
				}
			}
	        DB::commit();
			return response()->json(['status' => 'true', 'message'=>'Success Submit']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Submit !! [' . $e->getMessage() . ']']); 
        }
    }

	public function getCandidateBatch(Request $request) {
		$request->validate([
			'id_candidate' => 'required',
		]);

		$batchParticipant = DB::select("SELECT pbp.id_batch, pbp.id_candidate, hc.name, pmb.batch_code, pmb.batch_name, pmb.id_region, mr.description AS region, pmb.id_branch, mb.description AS branch
										FROM psycho_batch_participant pbp 
										LEFT JOIN web.hr_candidate hc ON pbp.id_candidate = hc.id_candidate
										LEFT JOIN psycho_master_batch pmb ON pbp.id_batch = pmb.id_batch
										LEFT JOIN master_region mr ON pmb.id_region = mr.id_region
										LEFT JOIN master_branch mb ON pmb.id_branch = mb.id_branch
										WHERE pbp.id_candidate = ? 
										AND pbp.creation_date >= ? 
										AND hc.is_profil_completed = TRUE
										AND pbp.status = 'A'",
										[$request->id_candidate, now()->subMonth(6)->startOfDay()]);
		$batch = DB::select("SELECT 
								pmb.id_batch AS id, 
								batch_name AS TEXT,
								batch_code,
								location,
								start_date,
								end_date,
								pmb.status,
								COUNT(pbp) as participant
							FROM
								psycho_master_batch pmb
							LEFT JOIN psycho_batch_participant pbp ON 
								pmb.id_batch = pbp.id_batch
							WHERE
								pmb.status = 'A'
								AND end_date >= now()
								AND pmb.id_company = ?
							GROUP BY pmb.id_batch", 
							[session('id_company')]);
		return response()->json([
			"candidate_batch" => $batchParticipant,
			"all_batch" => $batch,
		]);
	}

	public function assignCandidateToBatch(Request $request) {
		$request->validate([
			'id_candidate' => 'required',
			'id_batch' => 'required'
		]);

		DB::beginTransaction();
		try {
			$pbp = new BatchParticipant();
			$pbp->id_batch = $request->id_batch;
			$pbp->source_type = 'External';
			$pbp->id_candidate = $request->id_candidate;
			$pbp->status = 'A';
			$pbp->id_company = session('id_company');
			$pbp->created_by = session('id_user');
			$pbp->save();
			DB::commit();
			return response()->json([
				'message' => 'Candidate assigned to batch.',
			]);
		} catch(\Exception $e) {
			DB::rollBack();
			return response()->json([
				'message' => 'Failed assigning candidate to batch. '.$e->getMessage(),
			], 500);
		}
	}
	
}
