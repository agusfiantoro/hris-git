<?php

namespace App\Models\Recruitment\Candidate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CandidateData extends Model
{
	use HasFactory;
	
    protected $table = 'web.hr_candidate';
    protected $primaryKey = 'id_candidate';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	 protected $fillable = [
        'id_candidate','name','photo_candidate','identification_number','address_home','idcard_address','id_country','gender','id_religion','marital','place_of_birth','id_country_of_birth','ptkp_status','id_candidate_user','status','mobile_phone','emergency_phone','emergency_contact','email','about_me','duration_work','estimation_join','hired_date','join_date','additional_note','cv_upload','app_letter_upload','info_1','info_2','info_3','info_4','info_5','info_6','info_7','info_8','info_9','info_10','link_facebook','link_instagram','link_twitter','link_linkedin', 'date_of_birth', 'driving_license_number', 'taxpayer_identification_number', 'is_profil_completed', 'creation_date','update_date','created_by','updated_by'
    ];
	
	public static function get_group($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " hhrh.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT hhrh.reference_number AS fpk_no, mpr.description, 
				hhrh.id_hiring_request_header,
				count(hac.id_hiring_request_header) AS applicant_total,
				hhrh.hiring_request_status, hhrh.approval_date, hhrh.target_date, hhrh.id_branch, mb.description AS branch 
				FROM public.hr_hiring_request_header hhrh
				LEFT JOIN public.master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN web.hr_applied_candidate hac
				ON hhrh.id_hiring_request_header = hac.id_hiring_request_header
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON hhrh.id_branch = mb.id_branch
				WHERE ".$branch." hhrh.id_company = ".session('id_company')." AND hhrh.hiring_request_status != 'C' AND mgd.code = 'Approved'
				GROUP BY hhrh.reference_number, mpr.description, hhrh.id_hiring_request_header, 
				hac.id_hiring_request_header, hhrh.creation_date, hhrh.hiring_request_status,
				hhrh.approval_date, hhrh.target_date, hhrh.id_branch, mb.description
				ORDER BY hhrh.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_detail_group($data) {
		$id_hiring_request_header = $data['id_hiring_request_header'];
		$sql = "SELECT j_c.*, pmb.id_batch, pmb.batch_name FROM (SELECT hc.id_candidate, hhrh.id_hiring_request_header, hc.photo_candidate, hc.name, hc.identification_number, 
					hc.mobile_phone, hc.is_profil_completed, md.description AS interest, mpr.description AS request_position, mgd.description AS rec_stage, mgd.code AS code_stage, 
					hac.id_applied_candidate, hac.status, ml.id_branch, mjp.id_dept, mpr.id_job_grade
					FROM web.hr_candidate hc
					LEFT JOIN web.web_master_users wmu
					ON hc.id_candidate_user = wmu.id_candidate_user
					LEFT JOIN public.master_department md
					ON wmu.id_interest_department = md.id_dept
					LEFT JOIN web.hr_applied_candidate hac
					ON hc.id_candidate = hac.id_candidate
					LEFT JOIN public.hr_hiring_request_header hhrh
					ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
					LEFT JOIN public.master_position_routing mpr
					ON hhrh.id_position_routing_request = mpr.id_routing
					LEFT JOIN public.master_location ml
					ON hhrh.id_location_employee_request = ml.id_location
					LEFT JOIN public.master_job_position mjp
					ON mpr.id_position = mjp.id_position
					LEFT JOIN public.master_general_data mgd
					ON hac.id_candidate_status = mgd.id_general_data
					WHERE hhrh.id_hiring_request_header = ?
				) AS j_c
				LEFT JOIN public.psycho_batch_participant pbp
				ON j_c.id_candidate = pbp.id_candidate AND pbp.status = 'A'
				LEFT JOIN public.psycho_master_batch pmb
				ON pbp.id_batch = pmb.id_batch";
        $result = DB::select($sql,[$id_hiring_request_header]);		 
        return $result;
    }
	
	public static function get_can($fil_can,$daterange,$group_branch) {		
		if($daterange == null && $fil_can == null){
			$filMix = "j_c.reg_date BETWEEN null AND null";
		}
		else if($daterange == null && $fil_can != null){
			$filMix = "j_c.id_candidate IN(".$fil_can.")";
		}
		else if($daterange != null && $fil_can == null){
			$exDate = explode("   to   ",$daterange);
			$startdate = $exDate[0];
			$enddate = $exDate[1];
			$filMix = "j_c.reg_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'";
		}
		else{
			$exDate = explode("   to   ",$daterange);
			$startdate = $exDate[0];
			$enddate = $exDate[1];
			$filMix = "(j_c.reg_date BETWEEN '" .$startdate. "' AND '" .$enddate. "' AND j_c.id_candidate IN(".$fil_can."))";
		}
	
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND (j_c.id_branch in(".$group_branch.") OR j_c.id_branch IS NULL)";
		}
		$sql = "SELECT j_c.*, pmb.id_batch, pmb.batch_name FROM (SELECT hc.id_candidate, hc.photo_candidate, hc.name, hc.identification_number, hc.email,
					hc.mobile_phone, hc.is_profil_completed, md.description AS interest, mpr.description AS request_position, mgd.description AS rec_stage, 
					mgd.code AS code_stage, hac.id_applied_candidate, hac.status, hhrh.id_branch, mjp.id_dept, mpr.id_job_grade, hac.id_company,
					CASE
						WHEN hac.applied_date IS NULL THEN hc.creation_date::date
						ELSE hac.applied_date
					END AS reg_date				
					FROM web.hr_candidate hc
					LEFT JOIN web.web_master_users wmu
					ON hc.id_candidate_user = wmu.id_candidate_user
					LEFT JOIN public.master_department md
					ON wmu.id_interest_department = md.id_dept
					LEFT JOIN web.hr_applied_candidate hac
					ON hc.id_candidate = hac.id_candidate
					LEFT JOIN public.hr_hiring_request_header hhrh
					ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
					LEFT JOIN public.master_position_routing mpr
					ON hhrh.id_position_routing_request = mpr.id_routing
					LEFT JOIN public.master_job_position mjp
					ON mpr.id_position = mjp.id_position
					LEFT JOIN public.master_general_data mgd
					ON hac.id_candidate_status = mgd.id_general_data
				) AS j_c
				LEFT JOIN public.psycho_batch_participant pbp
				ON j_c.id_candidate = pbp.id_candidate AND pbp.status = 'A'
				LEFT JOIN public.psycho_master_batch pmb
				ON pbp.id_batch = pmb.id_batch
				WHERE ".$filMix." AND (j_c.id_company = ".session('id_company')." OR j_c.id_branch IS NULL) ".$branch."
				ORDER BY j_c.reg_date DESC	";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_edit_detail($data) {
		$sql = "SELECT hc.*, md.description AS dept, mc.description AS country, 
				mgd.description AS religion
				FROM web.hr_candidate hc
				LEFT JOIN web.web_master_users wmu
				ON hc.id_candidate_user = wmu.id_candidate_user
				LEFT JOIN public.master_department md
				ON wmu.id_interest_department = md.id_dept
				LEFT JOIN public.master_country mc
				ON hc.id_country = mc.id_country
				LEFT JOIN public.master_general_data mgd
				ON hc.id_religion = mgd.id_general_data 
				WHERE hc.id_candidate =  ?";
        $result = (Array) DB::select($sql, [$data['id_candidate']])[0];
		
		$sql_applied = "SELECT hac.*, hhrh.reference_number, mb.description AS branch, mgd.sequence, mgd.code AS code_status 
						FROM web.hr_applied_candidate hac
						LEFT JOIN public.hr_hiring_request_header hhrh
						ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
						LEFT JOIN public.master_branch mb
						ON hhrh.id_branch = mb.id_branch
						LEFT JOIN public.master_general_data mgd
						ON hac.id_candidate_status = mgd.id_general_data
						WHERE hac.id_candidate = ? AND hac.id_applied_candidate = ?";
        $result_menu3 = DB::select($sql_applied, [$data['id_candidate'],$data['id_applied']]);
        $collect_menu3 = collect($result_menu3);
        $group_menu3 = $collect_menu3->groupBy('id_applied_candidate')->toArray();
		$result['applied'] = [];
        foreach (array_keys($group_menu3) as $key => $value) {
            $result['applied'][] = [
                'id_applied_candidate' => $group_menu3[$value][0]->id_applied_candidate,
                'id_hiring_request_header' => $group_menu3[$value][0]->id_hiring_request_header,
                'reference_number' => $group_menu3[$value][0]->reference_number,
                'applied_date' => $group_menu3[$value][0]->applied_date,
                'id_candidate_status' => $group_menu3[$value][0]->id_candidate_status,
                'status' => $group_menu3[$value][0]->status,
                'branch' => $group_menu3[$value][0]->branch,
                'code_status' => $group_menu3[$value][0]->code_status,
                'sequence' => $group_menu3[$value][0]->sequence,
			];
        }
		
		$sql_edu = "SELECT hec.*, mgd.description AS edu_level 
						FROM web.hr_education_candidate hec
						LEFT JOIN public.master_general_data mgd
						ON hec.id_education_level = mgd.id_general_data
						WHERE hec.id_candidate = ?";
        $result_menu = DB::select($sql_edu, [$data['id_candidate']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_education_candidate')->toArray();
		$result['edu'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['edu'][] = [
                'id_education_candidate' => $group_menu[$value][0]->id_education_candidate,
                'major' => $group_menu[$value][0]->major,
                'education_name' => $group_menu[$value][0]->education_name,
                'edu_level' => $group_menu[$value][0]->edu_level,
                'start_year' => $group_menu[$value][0]->start_year,
                'end_year' => $group_menu[$value][0]->end_year,
                'education_city' => $group_menu[$value][0]->education_city,
                'grade_point_average' => $group_menu[$value][0]->grade_point_average,
			];
        }
	//	dd($result);
		return $result;
	}
	
	/*
	public static function get_question($data) {
        $result = [];
        $sql = "SELECT mrq.id_recruitment_question, mrq.id_recruitment_question_group, mrq.sequence, mrq.description, mrq.id_question_type,
				hrah.id_recruitment_answer_header, hrah.interview_date, hrah.total_score, hrah.id_conclusion, hrah.notes, hrad.essay_answer 
				FROM public.master_recruitment_question mrq
				LEFT JOIN public.master_recruitment_question_group mrqg
				ON mrq.id_recruitment_question_group = mrqg.id_recruitment_question_group
				LEFT JOIN public.hr_recruitment_answer_header hrah
				ON mrqg.id_recruitment_question_group = hrah.id_recruitment_question_group
				LEFT JOIN public.hr_recruitment_answer_detail hrad
				ON hrah.id_recruitment_answer_header = hrad.id_recruitment_answer_header AND mrq.id_recruitment_question = hrad.id_recruitment_question
				WHERE mrqg.id_question_group  = ? AND mrq.id_company = ?
				ORDER BY mrq.sequence ASC";
        $result = (Array) DB::select($sql,[$data['id_candidate_status'],session('id_company')]);
        return $result;
    }
	*/
	
	public static function get_question($data) {
        $result = [];
		$ans = [];
        $sql = "SELECT mrq.id_recruitment_question, mrq.id_recruitment_question_group, mrq.sequence, mrq.description, mrq.competency_group, mrq.category_group, mrq.id_question_type, mgd.code AS question_type,
				hrah.id_recruitment_answer_header, hrah.interview_date, hrah.total_score, hrah.id_conclusion, hrah.notes, hrad.essay_answer, hrad.id_recruitment_answer AS id_answer 
				FROM public.master_recruitment_question mrq
				LEFT JOIN public.master_recruitment_question_group mrqg
				ON mrq.id_recruitment_question_group = mrqg.id_recruitment_question_group AND mrqg.question_type = 'Recruitment'
				LEFT JOIN public.hr_recruitment_answer_header hrah
				ON mrqg.id_recruitment_question_group = hrah.id_recruitment_question_group AND hrah.id_candidate = ? AND hrah.id_applied_candidate = ?
				LEFT JOIN public.hr_recruitment_answer_detail hrad
				ON hrah.id_recruitment_answer_header = hrad.id_recruitment_answer_header AND mrq.id_recruitment_question = hrad.id_recruitment_question
				LEFT JOIN public.master_general_data mgd
				ON mrq.id_question_type = mgd.id_general_data
				WHERE mrqg.id_question_group  = ? AND mrq.id_company = ?
				ORDER BY mrq.sequence ASC";
        $result = (Array) DB::select($sql,[$data['id_candidate'],$data['id_applied'],$data['id_candidate_status'],session('id_company')]);
		foreach($result as $key=>$res){
			$sql2 = "SELECT rrqa.*, mra.sequence, mra.description 
					FROM public.relation_recruitment_question_answer rrqa
					LEFT JOIN public.master_recruitment_answer mra
					ON rrqa.id_recruitment_answer = mra.id_recruitment_answer
					WHERE rrqa.id_recruitment_question = ? AND rrqa.id_company = ?
					ORDER BY rrqa.id_recruitment_question ASC, mra.sequence ASC";
			$result_menu2 = DB::select($sql2, [$res->id_recruitment_question,session('id_company')]);
			$ans[$res->sequence]=[
				'id_recruitment_question' => $res->id_recruitment_question,
				'id_recruitment_question_group' => $res->id_recruitment_question_group,
				'sequence_soal' => $res->sequence,
				'competency_group' => $res->competency_group,
				'category_group' => $res->category_group,
				'question_type' => $res->question_type,
				'soal' => $res->description,
				'essay_answer' => $res->essay_answer,
				'id_answer' => $res->id_answer,
				'opt_answer' => [],
			];	
			foreach ($result_menu2 as $key => $value) {
				$ans[$res->sequence]['opt_answer'][] = [					
					'id_recruitment_answer' => $value->id_recruitment_answer,
					'desc_answer' => $value->description,
					'weight_score' => $value->weight_score,
					'is_corrected_answer' => $value->is_corrected_answer,         
				];
			}
		}
	//	dd($ans);
        return $ans;
    }
	
	public function get_pos() {   
		$sql = "SELECT hhrh.id_hiring_request_header id, CONCAT(mpr.description,' (',mb.description,')') AS text 
				FROM public.hr_hiring_request_header hhrh
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON hhrh.id_branch = mb.id_branch
				LEFT JOIN public.master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				WHERE hhrh.hiring_request_status IN ('O','P') AND mgd.code = 'Approved' AND hhrh.id_company = " . session('id_company'). "
				ORDER BY mpr.description ASC ";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_pos_done($id_hiring_header) {   
		$sql = "SELECT hhrh.id_hiring_request_header id, CONCAT(mpr.description,' (',mb.description,')') AS text 
					FROM public.hr_hiring_request_header hhrh
					LEFT JOIN public.master_position_routing mpr
					ON hhrh.id_position_routing_request = mpr.id_routing
					LEFT JOIN public.master_branch mb
					ON hhrh.id_branch = mb.id_branch
					LEFT JOIN public.master_general_data mgd
					ON hhrh.id_approval_status = mgd.id_general_data
					WHERE hhrh.hiring_request_status IN ('O','P') AND mgd.code = 'Approved' AND hhrh.id_company = " . session('id_company'). "
				UNION
				SELECT hhrh.id_hiring_request_header id, CONCAT(mpr.description,' (',mb.description,')') AS text 
					FROM public.hr_hiring_request_header hhrh
					LEFT JOIN public.master_position_routing mpr
					ON hhrh.id_position_routing_request = mpr.id_routing
					LEFT JOIN public.master_branch mb
					ON hhrh.id_branch = mb.id_branch
					LEFT JOIN public.master_general_data mgd
					ON hhrh.id_approval_status = mgd.id_general_data
					WHERE hhrh.id_hiring_request_header = ".$id_hiring_header." AND mgd.code = 'Approved' AND hhrh.id_company = " . session('id_company'). "
					ORDER BY text ASC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_pos_change($data) {
		$id_hiring_header = $data['id_hiring_header'];
		$sql = "SELECT hhrh.id_hiring_request_header, hhrh.reference_number AS ref_number, 
					mb.description AS branch
					FROM public.hr_hiring_request_header hhrh
					LEFT JOIN public.master_branch mb
					ON hhrh.id_branch = mb.id_branch 
					WHERE hhrh.id_hiring_request_header = ".$id_hiring_header;
        $result = DB::select($sql)[0];		 
        return $result;
    }
	
	public function get_stage() {   
		$sql = "SELECT mgd.id_general_data id, CONCAT(mgd.code,' (',mgd.description,')') text, mgd.sequence, mgd.code
				FROM public.master_general_data mgd
				LEFT JOIN public.master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_recruitment_stage' AND mgd.id_company = " . session('id_company'). "
				ORDER BY mgd.sequence ASC ";
        $result = DB::select($sql);		 
        return $result;
    }
	public function get_mass_stage() {   
		$sql = "SELECT mgd.id_general_data id, CONCAT(mgd.code,' (',mgd.description,')') text, mgd.sequence, mgd.code
				FROM public.master_general_data mgd
				LEFT JOIN public.master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_recruitment_stage' AND mgd.code IN('DBS','SHL','PSY') AND mgd.id_company = " . session('id_company'). "
				ORDER BY mgd.sequence ASC ";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_conclusion() {   
		$sql = "SELECT mgd.id_general_data id, mgd.description text, mgd.code
				FROM public.master_general_data mgd
				LEFT JOIN public.master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_psychotest_conclusion' AND mgd.id_company = " . session('id_company'). "
				ORDER BY mgd.sequence ASC ";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_family($data) {
		$id_candidate = $data['id_candidate'];
        $sql = "SELECT hfc.*, mgd.code AS last_edu 
					FROM web.hr_family_candidate hfc
					LEFT JOIN public.master_general_data mgd
					ON hfc.last_education = mgd.id_general_data
					WHERE hfc.id_candidate = ?";
        $result = DB::select($sql,[$id_candidate]);
	//	dd($result);
        return $result;
    }
	
	public static function get_edu($data) {
		$id_candidate = $data['id_candidate'];
        $sql = "SELECT hec.* FROM web.hr_education_candidate hec
				WHERE hec.id_candidate = ?";
        $result = DB::select($sql,[$id_candidate]);
	//	dd($result);
        return $result;
    }
	
	public static function get_ex($data) {
		$id_candidate = $data['id_candidate'];
        $sql = "SELECT hec.* 
				FROM web.hr_experience_candidate hec
				WHERE hec.id_candidate = ?
				ORDER BY hec.id_experience_candidate ASC";
        $result = DB::select($sql,[$id_candidate]);
	//	dd($result);
        return $result;
    }
	
	public static function get_cert($data) {
		$id_candidate = $data['id_candidate'];
        $sql = "SELECT hcc.* 
				FROM web.hr_certification_candidate hcc
				WHERE hcc.id_candidate = ?
				ORDER BY hcc.id_certification_candidate ASC";
        $result = DB::select($sql,[$id_candidate]);
	//	dd($result);
        return $result;
    }
	
	public static function get_skill($data) {
		$id_candidate = $data['id_candidate'];
        $sql = "SELECT hsc.* 
				FROM web.hr_skill_candidate hsc
				WHERE hsc.id_candidate = ?
				ORDER BY hsc.id_skill_candidate ASC";
        $result = DB::select($sql,[$id_candidate]);
	//	dd($result);
        return $result;
    }
	
	public static function get_interview($data) {
		$id_candidate = $data['id_candidate'];
		$id_applied = $data['id_applied'];
        $sql = "SELECT hrah.*, mrqg.description AS interview_type, mgd.description AS conclusion 
				FROM public.hr_recruitment_answer_header hrah
				LEFT JOIN public.master_recruitment_question_group mrqg
				ON hrah.id_recruitment_question_group = mrqg.id_recruitment_question_group AND mrqg.question_type = 'Recruitment'
				LEFT JOIN public.master_general_data mgd
				ON hrah.id_conclusion = mgd.id_general_data
				WHERE hrah.id_candidate = ? AND hrah.id_applied_candidate = ?";
        $result = DB::select($sql,[$id_candidate,$id_applied]);
	//	dd($result);
        return $result;
    }
	
	public static function get_status($can_stage) {
        $sql = "SELECT mgd.id_general_data, mgd.code FROM master_general_data mgd
				WHERE mgd.id_general_data = ?";
        $result = DB::select($sql,[$can_stage]);
		if(count($result) > 0){
			 return $result[0];
		}
		else{
			 return null;
		}
    }
	
	public static function get_request_detail($id_hiring_request_header) {
        $sql = "SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
				FROM hr_hiring_request_detail hhrd 
				WHERE hhrd.hiring_status = 'Hiring' AND hhrd.id_hiring_request_header = ?
				GROUP BY id_hiring_request_header";
        $result = DB::select($sql,[$id_hiring_request_header]);
	//	dd($result);
        return $result;
    }
	
	public static function get_request_hiring($id_hiring_request_header) {
        $sql = "SELECT j_detail.count_req, hhrh.id_hiring_request_header, mgd.code as code_app_status
				FROM hr_hiring_request_header hhrh
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN (					
					SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_detail hhrd 
					WHERE hhrd.hiring_status = 'Hiring' AND hhrd.id_hiring_request_header = ?
					GROUP BY id_hiring_request_header					
				) AS j_detail
				ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
				WHERE hhrh.id_company = " . session('id_company'). " AND mgd.code = 'Approved' AND hhrh.id_hiring_request_header = ?";
        $result = DB::select($sql,[$id_hiring_request_header,$id_hiring_request_header])[0];
	//	dd($result);
        return $result;
    }
	
	public static function get_request_total($id_hiring_request_header) {
        $sql = "SELECT j_detail.count_req, hhrh.id_hiring_request_header, mgd.code as code_app_status
				FROM hr_hiring_request_header hhrh
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN (					
					SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_detail hhrd 
					WHERE hhrd.id_hiring_request_header = ?
					GROUP BY id_hiring_request_header					
				) AS j_detail
				ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
				WHERE hhrh.id_company = " . session('id_company'). " AND mgd.code = 'Approved' AND hhrh.id_hiring_request_header = ?";
        $result = DB::select($sql,[$id_hiring_request_header,$id_hiring_request_header])[0];
	//	dd($result);
        return $result;
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'CLJ' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function check_stage($data) {
		$code_stage = $data['code_stage'];
        $sql = "SELECT mrqg.id_recruitment_question_group, mrqg.id_question_group, mgd.code AS code_group
				FROM public.master_recruitment_question_group mrqg
				LEFT JOIN public.master_general_data mgd AND mrqg.question_type = 'Recruitment'
				ON mrqg.id_question_group = mgd.id_general_data
				WHERE mgd.code = '" .$code_stage. "' AND mrqg.id_company = ". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_can_interview($group_branch) {   
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND hhrh.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT hc.id_candidate, hc.photo_candidate, hc.name, hc.identification_number, hc.mobile_phone, 
				md.description AS interest, mpr.description AS request_position, mgd.description AS rec_stage, mgd.code AS code_stage,
				hac.id_applied_candidate, hac.status, hhrh.id_branch, mrqg.id_recruitment_question_group
				FROM web.hr_candidate hc
				LEFT JOIN web.web_master_users wmu
				ON hc.id_candidate_user = wmu.id_candidate_user
				LEFT JOIN public.master_department md
				ON wmu.id_interest_department = md.id_dept
				LEFT JOIN web.hr_applied_candidate hac
				ON hc.id_candidate = hac.id_candidate
				LEFT JOIN public.hr_hiring_request_header hhrh
				ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.master_general_data mgd
				ON hac.id_candidate_status = mgd.id_general_data
				JOIN public.master_recruitment_question_group mrqg
				ON hac.id_candidate_status = mrqg.id_question_group AND mrqg.question_type = 'Recruitment' 
				WHERE hac.status IN('Review','Failed') " .$branch. " AND hac.id_company = " . session('id_company'). "
				ORDER BY hc.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_can_interview_summary($group_branch,$access_group,$dept_code) {
	//	dd($group_branch." ".$access_group);
		if($group_branch == null){
			$branch = "";
			if($access_group == 'Default_User' && $dept_code != "150_HR"){
				$user = " AND he.id_user = ".session('id_user');
			}
			else{
				$user = "";
			}
		}
		else{
			$user = "";
			$branch = " AND hhrh.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT hc.id_candidate, hc.photo_candidate, hc.name, hc.identification_number, 
				mpr.description AS request_position, 
				mrqg.id_question_group,
				CONCAT(mgd.description,' (',mgd.code,')') AS rec_stage,
				hac.id_applied_candidate, fix.status, hhrh.id_branch, mrqg.id_recruitment_question_group, he.id_user
				FROM public.hr_recruitment_answer_header hrah
				JOIN web.hr_candidate hc
				ON hrah.id_candidate = hc.id_candidate
				LEFT JOIN web.hr_applied_candidate hac
				ON hrah.id_applied_candidate = hac.id_applied_candidate
				LEFT JOIN public.hr_hiring_request_header hhrh
				ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.hr_employee he
				ON hhrh.id_employee_request = he.id_employee
				JOIN public.master_recruitment_question_group mrqg
				ON hrah.id_recruitment_question_group = mrqg.id_recruitment_question_group AND mrqg.question_type = 'Recruitment' 
				LEFT JOIN public.master_general_data mgd
				ON mrqg.id_question_group  = mgd.id_general_data
				JOIN (				
					SELECT hash.* FROM web.hr_applied_stage_history hash
					JOIN (
						SELECT hash.id_applied_candidate, max(hash.id_applied_stage_history) AS id_applied_stage_history
						FROM web.hr_applied_stage_history hash	
						GROUP BY hash.id_applied_candidate
					) AS j_fix
					ON hash.id_applied_stage_history = j_fix.id_applied_stage_history					
				) AS fix
				ON hac.id_applied_candidate = fix.id_applied_candidate AND mrqg.id_question_group = fix.id_candidate_status
				WHERE hrah.assessment_type = 'External' "
				.$branch. " ".$user." AND hac.id_company = " . session('id_company'). "
				GROUP BY hc.id_candidate, hc.photo_candidate, hc.name, hc.identification_number, 
				mpr.description, mrqg.id_question_group,CONCAT(mgd.description,' (',mgd.code,')'),
				hac.id_applied_candidate, fix.status, hhrh.id_branch, mrqg.id_recruitment_question_group, he.id_user, hrah.update_date				
				ORDER BY hrah.update_date DESC";
        $result = DB::select($sql);		
        return $result;
    }
	
	public static function get_edit_question($data) {
		$sql = "SELECT hc.*, md.description AS dept, mc.description AS country, 
				mgd.description AS religion
				FROM web.hr_candidate hc
				LEFT JOIN web.web_master_users wmu
				ON hc.id_candidate_user = wmu.id_candidate_user
				LEFT JOIN public.master_department md
				ON wmu.id_interest_department = md.id_dept
				LEFT JOIN public.master_country mc
				ON hc.id_country = mc.id_country
				LEFT JOIN public.master_general_data mgd
				ON hc.id_religion = mgd.id_general_data 
				WHERE hc.id_candidate =  ?";
        $result = (Array) DB::select($sql, [$data['id_candidate']])[0];
		
		$sql_applied = "SELECT hac.*, hhrh.reference_number, mb.description AS branch, mgd.code AS code_stage,
						CONCAT(mgd.description,' (',mgd.code,')') AS code_status, mpr.description AS pos_req 
						FROM web.hr_applied_candidate hac
						LEFT JOIN public.hr_hiring_request_header hhrh
						ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
						LEFT JOIN master_position_routing mpr
						ON hhrh.id_position_routing_request = mpr.id_routing
						LEFT JOIN public.master_branch mb
						ON hhrh.id_branch = mb.id_branch
						LEFT JOIN public.master_general_data mgd
						ON hac.id_candidate_status = mgd.id_general_data
						WHERE hac.id_candidate = ? AND hac.id_applied_candidate = ?";
        $result_menu3 = DB::select($sql_applied, [$data['id_candidate'],$data['id_applied']]);
        $collect_menu3 = collect($result_menu3);
        $group_menu3 = $collect_menu3->groupBy('id_applied_candidate')->toArray();
		$result['applied'] = [];
        foreach (array_keys($group_menu3) as $key => $value) {
            $result['applied'][] = [
                'id_applied_candidate' => $group_menu3[$value][0]->id_applied_candidate,
                'id_hiring_request_header' => $group_menu3[$value][0]->id_hiring_request_header,
                'reference_number' => $group_menu3[$value][0]->reference_number,
                'applied_date' => $group_menu3[$value][0]->applied_date,
                'id_candidate_status' => $group_menu3[$value][0]->id_candidate_status,
                'status' => $group_menu3[$value][0]->status,
                'branch' => $group_menu3[$value][0]->branch,
                'code_status' => $group_menu3[$value][0]->code_status,
                'code_stage' => $group_menu3[$value][0]->code_stage,
                'pos_req' => $group_menu3[$value][0]->pos_req,
			];
        }
		
		$sql_answer = "SELECT hrah.* FROM public.hr_recruitment_answer_header hrah
						WHERE hrah.id_candidate = ? AND hrah.id_applied_candidate = ? AND hrah.id_recruitment_question_group = ?";
		$result_menu2 = DB::select($sql_answer, [$data['id_candidate'],$data['id_applied'],$data['id_group']]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_recruitment_answer_header')->toArray();
		$result['hr_answer'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['hr_answer'][] = [			
                'id_recruitment_answer_header' => $group_menu2[$value][0]->id_recruitment_answer_header,
                'interview_date' => $group_menu2[$value][0]->interview_date,
                'total_score' => $group_menu2[$value][0]->total_score,
                'id_conclusion' => $group_menu2[$value][0]->id_conclusion,
                'int_notes' => $group_menu2[$value][0]->notes,
			];
        }
	//	dd($result);
		return $result;
	}
	
	public static function get_edit_question_summary($data) {
		$sql = "SELECT hc.*, md.description AS dept, mc.description AS country, 
				mgd.description AS religion
				FROM web.hr_candidate hc
				LEFT JOIN web.web_master_users wmu
				ON hc.id_candidate_user = wmu.id_candidate_user
				LEFT JOIN public.master_department md
				ON wmu.id_interest_department = md.id_dept
				LEFT JOIN public.master_country mc
				ON hc.id_country = mc.id_country
				LEFT JOIN public.master_general_data mgd
				ON hc.id_religion = mgd.id_general_data 
				WHERE hc.id_candidate =  ?";
        $result = (Array) DB::select($sql, [$data['id_candidate']])[0];
		
		$sql_applied = "SELECT mrqg.id_question_group, hac.*, hhrh.reference_number, mb.description AS branch, mgd.code AS code_stage, 
						CONCAT(mgd.description,' (',mgd.code,')') AS code_status, mpr.description AS pos_req 
						FROM public.hr_recruitment_answer_header hrah
						LEFT JOIN web.hr_applied_candidate hac
						ON hrah.id_applied_candidate = hac.id_applied_candidate
						LEFT JOIN public.hr_hiring_request_header hhrh
						ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
						LEFT JOIN master_position_routing mpr
						ON hhrh.id_position_routing_request = mpr.id_routing
						LEFT JOIN public.master_branch mb
						ON hhrh.id_branch = mb.id_branch
						JOIN public.master_recruitment_question_group mrqg
						ON hrah.id_recruitment_question_group = mrqg.id_recruitment_question_group AND mrqg.question_type = 'Recruitment' 
						LEFT JOIN public.master_general_data mgd
						ON mrqg.id_question_group  = mgd.id_general_data
						WHERE hrah.id_candidate = ? AND hrah.id_applied_candidate = ? AND hrah.id_recruitment_question_group = ?";
        $result_menu3 = DB::select($sql_applied, [$data['id_candidate'],$data['id_applied'],$data['id_group']]);
        $collect_menu3 = collect($result_menu3);
        $group_menu3 = $collect_menu3->groupBy('id_applied_candidate')->toArray();
		$result['applied'] = [];
        foreach (array_keys($group_menu3) as $key => $value) {
            $result['applied'][] = [
                'id_applied_candidate' => $group_menu3[$value][0]->id_applied_candidate,
                'id_hiring_request_header' => $group_menu3[$value][0]->id_hiring_request_header,
                'reference_number' => $group_menu3[$value][0]->reference_number,
                'applied_date' => $group_menu3[$value][0]->applied_date,
                'id_candidate_status' => $group_menu3[$value][0]->id_question_group,
                'branch' => $group_menu3[$value][0]->branch,
                'code_status' => $group_menu3[$value][0]->code_status,
				'code_stage' => $group_menu3[$value][0]->code_stage,
                'status' => $group_menu3[$value][0]->status,
                'pos_req' => $group_menu3[$value][0]->pos_req,
			];
        }
		
		$sql_answer = "SELECT hrah.*, mgd.description AS conclusion, mgd.code 
						FROM public.hr_recruitment_answer_header hrah
						LEFT JOIN public.master_general_data mgd
						ON hrah.id_conclusion = mgd.id_general_data
						WHERE hrah.id_candidate = ? AND hrah.id_applied_candidate = ? AND hrah.id_recruitment_question_group = ?";
		$result_menu2 = DB::select($sql_answer, [$data['id_candidate'],$data['id_applied'],$data['id_group']]);
		if(count($result_menu2) > 0) {
			$result_menu2_detail = DB::select("SELECT 
													hrad.*, mrq.description as question, mra.description as answer
												FROM public.hr_recruitment_answer_detail hrad 
												LEFT JOIN public.master_recruitment_question mrq
												ON hrad.id_recruitment_question = mrq.id_recruitment_question
												LEFT JOIN public.master_recruitment_answer mra
												ON hrad.id_recruitment_answer = mra.id_recruitment_answer
												WHERE hrad.status = 'A' AND hrad.id_recruitment_answer_header = ?", [$result_menu2[0]->id_recruitment_answer_header]);
		}
		foreach($result_menu2 as $k => $val) {
			$result_menu2[$k]->detail = @$result_menu2_detail;
		}
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_recruitment_answer_header')->toArray();
		$result['hr_answer'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['hr_answer'][] = [			
                'id_recruitment_answer_header' => $group_menu2[$value][0]->id_recruitment_answer_header,
                'interview_date' => $group_menu2[$value][0]->interview_date,
                'total_score' => $group_menu2[$value][0]->total_score,
                'id_conclusion' => $group_menu2[$value][0]->id_conclusion,
                'conclusion' => $group_menu2[$value][0]->conclusion,
                'code' => $group_menu2[$value][0]->code,
                'int_notes' => $group_menu2[$value][0]->notes,
				'detail' => $group_menu2[$value][0]->detail,
			];
        }
	//	dd($result);
		return $result;
	}
	
	public static function get_branch_user() {
        $sql = "SELECT mb.description as branch, mc.company_code 
				FROM public.hr_employee he
				LEFT JOIN public.master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN public.master_company mc
				ON he.id_company = mc.id_company
				WHERE he.id_user = ".session('id_user');
        $result = DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
	
	public static function getCandidate($data) {
		$sql = "SELECT * FROM web.hr_candidate hc
				WHERE hc.identification_number = '".$data['no_ktp']."' AND (hc.hired_date IS NOT NULL OR hc.join_date IS NOT NULL)";
        $result = (Array) DB::select($sql);
		return $result;
	}
	
	public static function get_can_name($name = null, $limit = null) {
		$where = "where 1=1";
		$limitQuery = "";
		if($name) {
			$name = strtolower($name);
			$where .= "and lower(hc.name) like '%".$name."%'";
		}
		if($limit) {
			// $limitQuery .= "LIMIT ".(int)$limit;
		}
		$sql = "SELECT hc.id_candidate id, hc.name text
				FROM web.hr_candidate hc
				LEFT JOIN web.hr_applied_candidate hac 
				ON hc.id_candidate = hac.id_candidate 
				AND (hac.id_company = ".session('id_company')." OR hac.id_company = NULL)
				$where
				GROUP BY hc.id_candidate, hc.name
				ORDER BY hc.name ASC
				$limitQuery";
        $result = (Array) DB::select($sql);
		return $result;
	}
	
	
}
