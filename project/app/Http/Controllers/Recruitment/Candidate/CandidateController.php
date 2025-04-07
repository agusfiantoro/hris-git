<?php
namespace App\Http\Controllers\Recruitment\Candidate;

use App\Models\Recruitment\Candidate\Candidate;
use App\Models\Recruitment\Candidate\CandidateData;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CandidateController extends Controller {

    public function index(Request $request) {
        return view('recruitment.recruitment.recruitment_tracking.index');
    }
	
	public function get_status_tracking(Request $request) {
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
        $result = Candidate::get_status_tracking($group_branch,$request->id_applied_candidate,$request->id_hiring_request_header);
        return response()->json($result);
    }
	
	public function get_tracking(Request $request) {
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
			$res = Candidate::get_tracking($group_branch,$request->id_applied_candidate,$request->id_hiring_request_header);
			$result['cards'] = $res;
			foreach($result['cards']  as $key=>$val){
				$diff = date_diff(date_create($val->start_year), date_create($val->end_year));
				$year = $diff->format('%y');
				$month = $diff->format('%m');
				$result['cards'][$key]->work_duration = $year.' Year '.$month. ' Month';
			}
			if(count($result['cards']) > 0){
				$result['config']['maxid'] =  $res[array_key_last($res)]->id;
			}
			return response()->json($result);
    }
	
	protected function update(Request $request) {
		try{
			DB::beginTransaction();
			$can_status = Candidate::get_can_status($request->candidate_status);
			$form_data = array(
				'id_candidate_status' => $can_status->id,
				'updated_by' => session('id_user'),
			);
			$can_data = Candidate::where('id_applied_candidate', $request->id_applied_candidate)->first();
			$can_view = CandidateData::where('id_candidate', $can_data['id_candidate'])->first();
		Candidate::where('id_applied_candidate', $request->id_applied_candidate)->update($form_data);
				
        DB::commit();
            return response()->json(['status' => 'true', 'name_can'=>$can_view['name'], 'message' => 'Moving Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false',   'name_can'=>null, 'message' => 'Cannot Moving !! [' . $e->getMessage() . ']']);           
        }
    }
	
	protected function update_status(Request $request) {
		try{
			DB::beginTransaction();
			$form_data = array(
				'status' => $request->can_status,
				'updated_by' => session('id_user'),
			);
		Candidate::where('id_applied_candidate', $request->id_applied_candidate)->update($form_data);
				
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Update Status Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Update Status !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function get_total(Request $request) {
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
        $result = Candidate::get_total($request->color,$group_branch);
        return response()->json($result);
    }
	
	public function filter_candidate(Request $request) {
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
        $result = Candidate::filter_candidate($group_branch);
        return response()->json($result);
    }
	public function filter_job(Request $request) {
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
        $result = Candidate::filter_job($group_branch);
        return response()->json($result);
    }
	
}
