<?php

namespace App\Models\TalentManagement\TalentProfile;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TalentProfile extends Model
{	
	public static function get_reco() {
        $sql = "SELECT htrh.id_talent_recommendation_header id, htrh.notes text 
				FROM hr_talent_recommendation_header htrh
				WHERE htrh.status = 'A' AND htrh.id_company = ".session('id_company')."
				ORDER BY text ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_employee($group_branch) {
		$branch = " ";
		if($group_branch || $group_branch != ""){
			$branch = " AND se.id_branch IN(".$group_branch.")";
		}
        $sql = "SELECT DISTINCT se.nik_employee id, CONCAT(se.employee_name,' (',se.nik_employee,')') text
				FROM sp_funct_get_employee(".session('id_company').") se
				JOIN hr_talent_recommendation_detail htrd 
				ON se.id_employee = htrd.id_employee
				WHERE se.status = 'A' ".$branch."
				ORDER BY text ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function getdata($group_branch, $reco=null, $nik=null) {
		$branch = "";
		$idReco = "";
		$idNik = "";
		if($group_branch || $group_branch != ""){
			$branch = " AND se.id_branch IN(".$group_branch.")";
		}
		if($reco){
			$idReco = " AND htrh.id_talent_recommendation_header IN(".implode(",", $reco).")";
		}
		if($nik){
			$idNik = " AND se.nik_employee IN(".implode(",", $nik).")";
		}
		$sql = "SELECT DISTINCT se.*, he.identification_number
				FROM sp_funct_get_employee(".session('id_company').") se
				JOIN hr_employee he
				ON se.id_employee = he.id_employee
				JOIN hr_talent_recommendation_detail htrd 
				ON se.id_employee = htrd.id_employee
				JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
				WHERE htrh.status = 'A' AND se.status = 'A' ".$branch." 
				".$idReco." 
				".$idNik." 
				ORDER BY se.employee_name ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
	}	
	
	public static function get_editProfile($data) {
		$result = [];
		$id_employee = $data['id_employee'];
        $sql_1 = "SELECT sftp.*, CONCAT(he.name,' / ',he.nik_employee) AS name, he.image_attachment, date_part('year',age(current_date,sftp.birthdate)) AS age,
		CONCAT(date_part('year',age(current_date,sftp.join_date)),' Year(s) ', date_part('month',age(current_date,sftp.join_date)),' Month(s) ',
		date_part('days',age(current_date,sftp.join_date)),' Day(s)')::text as duration, edu.edu_level, talent.box, talent.desc_box, talent.readyness 
		FROM  public.sp_funct_get_employee_report_all(".session('id_company').") sftp
		JOIN hr_employee he
		ON sftp.id_employee = he.id_employee
		LEFT JOIN (
			SELECT hee.id_employee, mgd.code AS edu_level 
			FROM hr_education_employee hee
			LEFT JOIN master_general_data mgd
			ON hee.id_education_level = mgd.id_general_data AND mgd.id_company = ".session('id_company')."
			WHERE hee.id_employee = ".$id_employee." AND hee.id_company = ".session('id_company')."
			ORDER BY mgd.sequence DESC 
			LIMIT 1	
		) AS edu
		ON sftp.id_employee = edu.id_employee
		LEFT JOIN (
			SELECT htrd.id_talent_recommendation_detail, htrd.id_employee, mtm.name AS box, 
			mgd.description AS desc_box, htrd.kpi_desc, mtm.readyness
			FROM hr_talent_recommendation_detail htrd
			JOIN master_talent_matrix mtm
			ON htrd.id_job_grade = mtm.id_job_grade AND (htrd.id_grade_promotion = mtm.id_grade_promotion OR (htrd.id_grade_promotion IS NULL AND mtm.id_grade_promotion IS NULL))
			AND (htrd.kpi_average >= mtm.kpi_value_min AND htrd.kpi_average <= mtm.kpi_value_max) 
			AND htrd.id_conclusion_psychotest = mtm.id_conclusion_psychotest AND htrd.id_conclusion_assessment = mtm.id_conclusion_assessment
			LEFT JOIN master_general_data mgd
			ON mtm.id_group_matrix = mgd.id_general_data
			WHERE htrd.id_employee = ".$id_employee." AND htrd.id_company = ".session('id_company')."
		UNION
			SELECT htrd.id_talent_recommendation_detail, htrd.id_employee, mtm.name AS box, 
			mgd.description AS desc_box, htrs.kpi_desc, mtm.readyness
			FROM hr_talent_recommendation_summary htrs
			LEFT JOIN hr_talent_recommendation_detail htrd
			ON htrs.id_talent_recommendation_detail = htrd.id_talent_recommendation_detail
			LEFT JOIN master_talent_matrix mtm
			ON htrd.id_job_grade = mtm.id_job_grade AND (htrs.id_grade_promotion = mtm.id_grade_promotion OR (htrs.id_grade_promotion IS NULL AND mtm.id_grade_promotion IS NULL))
			AND (htrs.kpi_average >= mtm.kpi_value_min AND htrs.kpi_average <= mtm.kpi_value_max) 
			AND mtm.id_conclusion_psychotest = coalesce(htrs.id_conclusion_psychotest,htrd.id_conclusion_psychotest) 
			AND mtm.id_conclusion_assessment = coalesce(htrs.id_conclusion_assessment,htrd.id_conclusion_assessment)
			LEFT JOIN master_general_data mgd
			ON mtm.id_group_matrix = mgd.id_general_data
			WHERE htrd.id_employee = ".$id_employee." AND htrd.id_company = ".session('id_company')."
			ORDER BY kpi_desc DESC
			LIMIT 1
		) AS talent
		ON sftp.id_employee = talent.id_employee
		WHERE sftp.id_employee = ".$id_employee."
		LIMIT 1";
        $profile_emp = DB::select($sql_1);
	//	$sql_2 = "SELECT * FROM public.sp_funct_talent_profile_career_data(".session('id_company').", ".$id_employee.")";
    //    $career_emp = DB::select($sql_2);
		$result['profile'] = [];
	//	$result['career'] = [];
		if(count($profile_emp) > 0){
			$result['profile'] = $profile_emp[0];			
		}
		
		$sql_detail = "SELECT htcn.*, mgd.code
						FROM hr_talent_commite_note htcn
						JOIN master_general_data mgd
						ON htcn.id_category_profile = mgd.id_general_data
						WHERE htcn.id_employee = ".$id_employee."
						ORDER BY htcn.id_talent_profile_note ASC";
        $result_menu = DB::select($sql_detail);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_talent_profile_note')->toArray();
		
		$result['comit_risk'] = [];
		$result['comit_plan'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
			$comit = [
				'id_talent_profile_note' => $group_menu[$value][0]->id_talent_profile_note,
				'id_position_routing' => $group_menu[$value][0]->id_position_routing,
				'id_category_profile' => $group_menu[$value][0]->id_category_profile,
				'code' => $group_menu[$value][0]->code,
				'talent_commite_date' => $group_menu[$value][0]->talent_commite_date,
				'id_activity' => $group_menu[$value][0]->id_activity,
				'notes' => $group_menu[$value][0]->notes,
				'is_submitted_flag' => $group_menu[$value][0]->is_submitted_flag,
				'status' => $group_menu[$value][0]->status,
				'id_company' => $group_menu[$value][0]->id_company,
			];
			if($group_menu[$value][0]->code == 'Flight_Risk'){
				$result['comit_risk'][] = $comit;
			}
			if($group_menu[$value][0]->code == 'Talent_Note'){
				$result['comit_plan'][] = $comit;
			}
        }
        return $result;
    }
	
	public static function get_cert_profile($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT hce.certification_name, hce.certified_by 
				FROM hr_certification_employee hce
				WHERE hce.id_employee = ? AND hce.id_company = ?
				ORDER BY hce.id_certification_employee ASC";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }
	
	public static function get_working($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT * FROM hr_experience_employee hee 
				WHERE hee.id_employee = ? AND hee.id_company = ?
				ORDER BY hee.start_year DESC";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }	

	public static function get_emp_career_history($data) {
		$id_number = $data['id_number'];
        $sql = "SELECT DISTINCT hct.id_career_transaction, hct.reference_number, mgd.description as transition_category, mgd2.description as transaction_type, 
				mgd3.description as employment_status, mpd.description as position_detail, mpr.description as position_routing, 
				mjg.description as job_grade, mjs.description as job_status, ml.description as location,
				hct.effective_date, hct.expired_date,
				CONCAT(date_part('month',age(coalesce(hct.expired_date,new_position.effective_date, current_date ),hct.effective_date)),' Month(s) ')::text as duration,
				mc.company_name
				FROM hr_career_transaction hct
				JOIN hr_employee he
				ON hct.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON hct.id_transition_category = mgd.id_general_data
				JOIN master_general_data mgd2
				ON hct.id_transaction_type = mgd2.id_general_data
				JOIN master_general_data mgd3
				ON hct.id_employment_status = mgd3.id_general_data
				JOIN master_general_data mgd4
				ON hct.id_approval_status = mgd4.id_general_data
				LEFT JOIN (					
                       SELECT ROW_NUMBER() OVER (ORDER BY hct.id_career_transaction ASC) as id_number,
					      hct.id_career_transaction,
					      CASE
					      	WHEN hct.id_old_position_detail IS NOT NULL AND hct.id_position_detail IS NULL THEN hct.id_old_position_detail
					      	WHEN hct.id_position_detail IS NOT NULL THEN hct.id_position_detail
					      END AS id_position_detail, hct.effective_date
					      FROM hr_career_transaction hct
						  JOIN hr_employee he
							ON hct.id_employee = he.id_employee
					      JOIN master_general_data mgd
					        ON hct.id_approval_status = mgd.id_general_data
					       AND hct.id_company = mgd.id_company
					      WHERE he.identification_number = '".$id_number."'
					        AND mgd.code = 'Approved'  
                      ) AS new_position
				ON hct.id_career_transaction = new_position.id_career_transaction
				LEFT JOIN master_position_detail mpd
				ON new_position.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_status mjs
				ON mpr.id_job_status = mjs.id_job_status
				JOIN master_company mc
				ON hct.id_company = mc.id_company
				WHERE he.identification_number = '".$id_number."' AND mgd4.code = 'Approved'
				AND mgd.id_general_type = 5 AND mgd2.id_general_type = 6
				ORDER BY hct.id_career_transaction DESC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_pro_position($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT htrh.id_talent_recommendation_header, mpr.description AS pro_position, htrd.kpi_desc
				FROM hr_talent_recommendation_detail htrd
				JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
				JOIN master_position_routing mpr
				ON htrh.id_position_routing = mpr.id_routing 
				WHERE htrh.status = 'A' AND htrd.id_employee = ? AND htrh.id_company = ?
				ORDER BY htrd.kpi_desc DESC";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }
	
	public static function get_history_talent($data) {
		$id_employee = $data['id_employee'];
		$nik_employee = $data['nik_employee'];
		$type = $data['type'];
        $sql = "SELECT DISTINCT htrd.id_talent_recommendation_detail, mpr.description AS pro_position, htrd.id_employee, he.name AS emp_name, mgd.code, mtm.name AS matrix_box, 
				mgd.description AS matrix_name, htrd.kpi_desc AS period, htrd.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies,
				mtm.readyness, htrd.engagement_survey_result AS engagement_level,
				CASE WHEN htrd.id_grade_promotion = ANY(ARRAY[hcs.id_grade_promotion])
                      AND htrd.kpi_average::double precision >= hcs.minimum_annual_kpi
                      AND htrd.is_have_sp = false
                      AND htrd.is_have_kpk = false
                     THEN 'ELIGIBLE'::character varying
                     ELSE 'NOT ELIGIBLE'::character varying 
                END AS eligible_status
				FROM hr_talent_recommendation_detail htrd
				LEFT JOIN master_talent_matrix mtm
				ON htrd.id_job_grade = mtm.id_job_grade AND (htrd.id_grade_promotion = mtm.id_grade_promotion OR (htrd.id_grade_promotion IS NULL AND mtm.id_grade_promotion IS NULL))
				AND (htrd.kpi_average >= mtm.kpi_value_min AND htrd.kpi_average <= mtm.kpi_value_max) 
				AND htrd.id_conclusion_psychotest = mtm.id_conclusion_psychotest AND htrd.id_conclusion_assessment = mtm.id_conclusion_assessment
				LEFT JOIN hr_employee he
				ON htrd.id_employee = he.id_employee
				LEFT JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
				LEFT JOIN master_position_routing mpr
				ON htrh.id_position_routing = mpr.id_routing
				LEFT JOIN master_position_detail mpd
				ON htrd.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
				LEFT JOIN master_general_data mgd
				ON mtm.id_group_matrix = mgd.id_general_data
				LEFT JOIN master_grade_promotion mgp
				ON htrd.id_grade_promotion = mgp.id_grade_promotion
				LEFT JOIN master_general_data mgd2
				ON htrd.id_conclusion_psychotest = mgd2.id_general_data
				LEFT JOIN master_general_data mgd3
				ON htrd.id_conclusion_assessment = mgd3.id_general_data
				LEFT JOIN master_region mr
				ON htrd.id_region = mr.id_region
				LEFT JOIN master_job_grade mjg
				ON htrd.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_department md
				ON htrd.id_dept = md.id_dept
				LEFT JOIN relation_positiondetail_principal rpp
				ON htrd.id_position_detail = rpp.id_position_detail
				JOIN hr_config_settings hcs
                ON mpd.id_company = hcs.id_company
				WHERE htrh.status = 'A' AND htrd.id_company = ".session('id_company')." AND he.nik_employee = '".$nik_employee."' AND htrh.type = '".$type."'
			UNION	
			SELECT DISTINCT htrs.id_talent_recommendation_detail, mpr.description AS pro_position, htrd.id_employee, he.name AS emp_name, mgd.code, mtm.name AS matrix_box, 
				mgd.description AS matrix_name, htrs.kpi_desc AS period, htrs.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies,
				mtm.readyness, htrd.engagement_survey_result AS engagement_level,
				CASE WHEN htrs.id_grade_promotion = ANY(ARRAY[hcs.id_grade_promotion])
                      AND htrs.kpi_average::double precision >= hcs.minimum_annual_kpi
                      AND htrs.is_have_sp = false
                      AND htrs.is_have_kpk = false
                     THEN 'ELIGIBLE'::character varying
                     ELSE 'NOT ELIGIBLE'::character varying 
                END AS eligible_status
				FROM hr_talent_recommendation_summary htrs
				LEFT JOIN hr_talent_recommendation_detail htrd
				ON htrs.id_talent_recommendation_detail = htrd.id_talent_recommendation_detail
				LEFT JOIN master_talent_matrix mtm
				ON htrd.id_job_grade = mtm.id_job_grade AND (htrs.id_grade_promotion = mtm.id_grade_promotion OR (htrs.id_grade_promotion IS NULL AND mtm.id_grade_promotion IS NULL))
				AND (htrs.kpi_average >= mtm.kpi_value_min AND htrs.kpi_average <= mtm.kpi_value_max) 
				AND mtm.id_conclusion_psychotest = coalesce(htrs.id_conclusion_psychotest,htrd.id_conclusion_psychotest) 
				AND mtm.id_conclusion_assessment = coalesce(htrs.id_conclusion_assessment,htrd.id_conclusion_assessment)
				LEFT JOIN hr_employee he
				ON htrd.id_employee = he.id_employee
				LEFT JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
				LEFT JOIN master_position_routing mpr
				ON htrh.id_position_routing = mpr.id_routing
				LEFT JOIN master_position_detail mpd
				ON htrd.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
				JOIN master_general_data mgd
				ON mtm.id_group_matrix = mgd.id_general_data
				LEFT JOIN master_grade_promotion mgp
				ON htrs.id_grade_promotion = mgp.id_grade_promotion
				LEFT JOIN master_general_data mgd2
				ON mgd2.id_general_data = coalesce(htrs.id_conclusion_psychotest,htrd.id_conclusion_psychotest)
				LEFT JOIN master_general_data mgd3
				ON mgd3.id_general_data = coalesce(htrs.id_conclusion_assessment,htrd.id_conclusion_assessment)
				LEFT JOIN master_region mr
				ON htrd.id_region = mr.id_region
				LEFT JOIN master_job_grade mjg
				ON htrd.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_department md
				ON htrd.id_dept = md.id_dept
				LEFT JOIN relation_positiondetail_principal rpp
				ON htrd.id_position_detail = rpp.id_position_detail
				JOIN hr_config_settings hcs
                ON mpd.id_company = hcs.id_company
				WHERE htrh.status = 'A' AND htrs.id_company = ".session('id_company')." AND he.nik_employee = '".$nik_employee."' AND htrh.type = '".$type."'
				ORDER BY period DESC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_last_rating($data) {
		$id_employee = $data['id_employee'];
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT mp.year, hkt.final_rating 
				FROM hr_kpi_total hkt
				JOIN hr_employee he
				ON hkt.id_employee_participant = he.id_employee
				JOIN master_period mp
				ON hkt.id_period = mp.id_period
				WHERE he.nik_employee = '".$nik_employee."' AND hkt.id_company = ?
				AND hkt.final_rating IS NOT NULL
				ORDER BY mp.year DESC
				LIMIT 1";
        $result = DB::select($sql,[session('id_company')]);
	//	dd($result);
        return $result;
    }
	
	public static function get_old_rating($data) {
		$id_employee = $data['id_employee'];
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT mp.year, hkt.final_rating 
				FROM hr_kpi_total hkt
				JOIN hr_employee he
				ON hkt.id_employee_participant = he.id_employee
				JOIN master_period mp
				ON hkt.id_period = mp.id_period
				WHERE he.nik_employee = '".$nik_employee."' AND hkt.id_company = ".session('id_company')."
				AND hkt.final_rating IS NOT NULL
				AND hkt.id_kpi_total != (
					SELECT hkt.id_kpi_total
					FROM hr_kpi_total hkt
					JOIN hr_employee he
					ON hkt.id_employee_participant = he.id_employee
					JOIN master_period mp
					ON hkt.id_period = mp.id_period
					WHERE  he.nik_employee = '".$nik_employee."' AND hkt.id_company = ".session('id_company')."
					AND hkt.final_rating IS NOT NULL
					ORDER BY mp.year DESC
					LIMIT 1
				)
				ORDER BY mp.year DESC
				LIMIT 2";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_training($data) {
		$id_employee = $data['id_employee'];
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT DISTINCT hem.id_event_management, hep.id_course_header,  hem.description AS program_name, 
				hep.description AS course_name, hsauh.creation_date, hsauh.total_score AS user_score, 
				hep.pass_scores,
				CASE 
					WHEN (hep.pass_scores = 1 AND hsauh.total_score =  1)  THEN 1
					WHEN (hep.pass_scores = 1 AND hsauh.total_score =  0) THEN 0
					ELSE NULL
				END AS hit_miss,
				CASE 
					WHEN hep.pass_scores = 1  THEN hsau.description_answer
					ELSE NULL
				END AS rating,
				CASE 
					WHEN hsauh.total_score >= hep.pass_scores THEN 'Pass'
					ELSE 'Failed'
				END AS status
				FROM hr_event_attendees hea
				JOIN hr_employee he
				ON hea.booked_by = he.id_employee				
				LEFT JOIN hr_event_program hep
				ON hea.id_event_program = hep.id_event_program
				LEFT JOIN hr_survey_answer_user_header hsauh
				ON hea.id_event_program = hsauh.id_event_program
				JOIN hr_employee he2 
				ON hsauh.id_employee = he2.id_employee
				JOIN master_course_detail mcd
				ON hsauh.id_course_detail = mcd.id_course_detail
				JOIN master_course_header mch
				ON mcd.id_course_header = mch.id_course_header
				JOIN hr_event_management hem
				ON hep.id_event_management = hem.id_event_management 
				LEFT JOIN hr_survey_answer_user hsau
				ON hsauh.id_survey_answer_user_header = hsau.id_survey_answer_user_header AND hep.pass_scores = 1
				WHERE he.nik_employee = '".$nik_employee."' AND he2.nik_employee = '".$nik_employee."' AND hea.id_company = ".session('id_company')."
				AND hsauh.id_course_detail IS NOT NULL AND mcd.course_type = 'Quiz_Posttest'";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_kpi_list($data) {
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT k_h.id_kpi_group, k_h.id_branch, k_h.id_employee, CONCAT(k_h.name_dinilai,' (',k_h.nik_dinilai,')') AS dinilai, k_h.average_prosentase, k_h.id_period, k_h.period, 
					STRING_AGG(k_h.jan::character varying,'-') AS jan, STRING_AGG(k_h.feb::character varying,'') AS feb, 
					STRING_AGG(k_h.mar::character varying,'') AS mar, STRING_AGG(k_h.apr::character varying,'') AS apr, STRING_AGG(k_h.may::character varying,'') AS mei,
					STRING_AGG(k_h.jun::character varying,'') AS jun, STRING_AGG(k_h.jul::character varying,'') AS jul, STRING_AGG(k_h.aug::character varying,'') AS ags, 
					STRING_AGG(k_h.sep::character varying,'') AS sep, STRING_AGG(k_h.okt::character varying,'') AS okt, STRING_AGG(k_h.nov::character varying,'') AS nov,
					STRING_AGG(k_h.des::character varying,'') AS des
					FROM (							
						SELECT mpd.id_branch, he.name AS name_dinilai, he.nik_employee AS nik_dinilai, hkg.id_kpi_group, 
						hkg.id_employee, hkg.average_prosentase, hkg.id_period, mp.description AS period, hkg.id_employee_appraisers,  
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jan' THEN hkh.subtotal_kpi END AS jan,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Feb' THEN hkh.subtotal_kpi END AS feb,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Mar' THEN hkh.subtotal_kpi END AS mar,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Apr' THEN hkh.subtotal_kpi END AS apr,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'May' THEN hkh.subtotal_kpi END AS may,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jun' THEN hkh.subtotal_kpi END AS jun,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jul' THEN hkh.subtotal_kpi END AS jul,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Aug' THEN hkh.subtotal_kpi END AS aug,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Sep' THEN hkh.subtotal_kpi END AS sep,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Oct' THEN hkh.subtotal_kpi END AS okt,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Nov' THEN hkh.subtotal_kpi END AS nov,
							CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Dec' THEN hkh.subtotal_kpi END AS des
						FROM hr_kpi_header hkh
						JOIN hr_kpi_group hkg
						ON hkh.id_kpi_group = hkg.id_kpi_group
						JOIN (
							SELECT max(hkg.id_kpi_group) AS id_kpi_group, mp.year
								FROM hr_kpi_group hkg
								JOIN master_period mp
								ON hkg.id_period = mp.id_period
								JOIN hr_employee he 
								ON hkg.id_employee = he.id_employee 
								WHERE hkg.id_company = ".session('id_company')." AND hkg.status = 'A'
								AND he.nik_employee = '".$nik_employee."'
								GROUP BY mp.year
						) AS g_new
						ON hkg.id_kpi_group = g_new.id_kpi_group
						LEFT JOIN hr_employee he
						ON hkg.id_employee = he.id_employee
						LEFT JOIN master_position_detail mpd
						ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
						LEFT JOIN master_period mp
						ON hkg.id_period = mp.id_period
						WHERE hkh.id_company = ".session('id_company')." AND hkh.status = 'A'  AND he.nik_employee = '".$nik_employee."'								
					) AS k_h
					WHERE k_h.nik_dinilai = '".$nik_employee."'
					GROUP BY k_h.id_branch, k_h.name_dinilai, k_h.nik_dinilai, k_h.id_kpi_group, k_h.id_employee, 
					k_h.average_prosentase, k_h.id_period, k_h.period, k_h.id_employee_appraisers
					ORDER BY k_h.period DESC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_edu($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT hee.*, mgd.description AS level
				FROM hr_education_employee hee
				JOIN master_general_data mgd
				ON hee.id_education_level = mgd.id_general_data
				WHERE hee.id_employee = ".$id_employee." AND hee.id_company = ".session('id_company')."
				ORDER BY hee.start_year DESC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_award($data) {
		$id_employee = $data['id_employee'];
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT * 
				FROM hr_award_dicipline_transaction hadt
				JOIN hr_employee he
				ON hadt.id_employee = he.id_employee 
				WHERE he.nik_employee = '".$nik_employee."' AND hadt.id_company = ".session('id_company')."
				AND hadt.transaction_type = 'A'";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_sp($data) {
		$id_employee = $data['id_employee'];
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT hadt.id_transaction, hadt.award_letter_number AS sp_number, 
				mgd.description AS sp_name, hadt.effective_date, hadt.expired_date  
				FROM hr_award_dicipline_transaction hadt
				JOIN hr_employee he
				ON hadt.id_employee = he.id_employee 
				JOIN master_general_data mgd
				ON hadt.dicipline_type = mgd.id_general_data
				WHERE he.nik_employee = '".$nik_employee."' AND hadt.id_company = ".session('id_company')."
				AND mgd.code IN('SP1','SP2','SP3') AND hadt.transaction_type = 'D'";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_aspiration($data) {
		$id_employee = $data['id_employee'];
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT hpq.id_pa_question, mp.description AS period, hpq.sequence, hfd.description_answer, j_q.answer
				FROM hr_pa_question hpq
				JOIN hr_fpr_detail hfd
				ON hpq.id_pa_question = hfd.id_pa_question
				JOIN hr_fpr_header hfh
				ON hfd.id_fpr_header = hfh.id_fpr_header
				JOIN hr_kpi_group hkg
				ON hfh.id_kpi_group = hkg.id_kpi_group
				JOIN hr_employee he
				ON hkg.id_employee = he.id_employee
				JOIN master_period mp 
				ON hfd.id_period = mp.id_period
				LEFT JOIN (
					SELECT j_answer.id_period,j_answer.id_pa_question, STRING_AGG(hpa.description,', ') AS answer FROM
						(			
						SELECT mp.id_period,hpq.id_pa_question, UNNEST(hfd.id_pa_answer) AS id_pa_answer
										FROM hr_pa_question hpq
										JOIN hr_fpr_detail hfd
										ON hpq.id_pa_question = hfd.id_pa_question
										JOIN hr_fpr_header hfh
										ON hfd.id_fpr_header = hfh.id_fpr_header
										JOIN hr_kpi_group hkg
										ON hfh.id_kpi_group = hkg.id_kpi_group
										JOIN hr_employee he
										ON hkg.id_employee = he.id_employee
										JOIN master_period mp 
										ON hfd.id_period = mp.id_period
										WHERE hpq.id_company = ".session('id_company')."
										AND hpq.notes = 'Yearly' AND hpq.question_pa_type = 'QUANTITATIVE' AND hpq.sequence IN(12,13) 
										AND he.nik_employee = '".$nik_employee."'			
						) j_answer
						    JOIN hr_pa_answer hpa 
						    ON j_answer.id_pa_answer = hpa.id_pa_answer
						    GROUP BY j_answer.id_period,j_answer.id_pa_question
				) AS j_q
				ON hpq.id_pa_question = j_q.id_pa_question AND mp.id_period = j_q.id_period
				WHERE hpq.id_company = ".session('id_company')."
				AND hpq.notes = 'Yearly' AND hpq.question_pa_type = 'QUANTITATIVE' AND hpq.sequence IN(12,13,14,15) 
				AND he.nik_employee = '".$nik_employee."'
				ORDER BY mp.year DESC, hpq.sequence ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_risk() {
        $sql = "SELECT mgd.id_general_data id, mgd.description text, mgd.code 
				FROM master_general_data mgd
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_talent_commite_category' AND mgd.code IN('Flight_Risk')
				AND mgd.id_company = ".session('id_company')." AND mgd.status = 'A'";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_plan() {
        $sql = "SELECT mgd.id_general_data id, mgd.description text, mgd.code 
				FROM master_general_data mgd
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_talent_commite_category' AND mgd.code IN('Talent_Note')
				AND mgd.id_company = ".session('id_company')." AND mgd.status = 'A'";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_activity($id) {
        $sql = "SELECT mgd.id_general_data id, mgd.description text
				FROM master_general_data mgd
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_talent_commite_activity' 
				AND mgd.id_company = ".session('id_company')." AND mgd.relation_to_id_general_data = ?";
        $result = DB::select($sql,[$id]);
	//	dd($result);
        return $result;
    }
	
	public static function get_committee($data,$cat_code) {
		$nik_employee = $data['nik_employee'];
        $sql = "SELECT htcn.*, mpr.description AS pos_route, mgd.code AS cat_code,
				mgd.description AS category, mgd2.description AS activity 
				FROM hr_talent_commite_note htcn
				JOIN hr_employee he
				ON htcn.id_employee = he.id_employee
				JOIN master_position_routing mpr
				ON htcn.id_position_routing = mpr.id_routing
				JOIN master_general_data mgd
				ON htcn.id_category_profile = mgd.id_general_data
				JOIN master_general_data mgd2
				ON htcn.id_activity = mgd2.id_general_data
				WHERE he.nik_employee = '".$nik_employee."' AND mgd.code = '".$cat_code."'
				ORDER BY htcn.id_talent_profile_note ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_id($id_employee) {
        $sql = "SELECT he.id_employee, he.nik_employee, he.identification_number
				FROM hr_employee he
				WHERE he.id_employee = ".$id_employee;
        $result = DB::select($sql);
        return $result;
    }

}
