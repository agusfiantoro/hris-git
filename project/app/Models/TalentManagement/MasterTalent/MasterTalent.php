<?php

namespace App\Models\TalentManagement\MasterTalent;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterTalent extends Model
{
	use HasFactory;
	
    protected $table = 'master_talent_matrix';
	protected $primaryKey = 'id_talent_matrix';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_talent_matrix', 'name', 'id_group_matrix', 'kpi_value_min', 'kpi_value_max', 'id_job_grade', 'id_grade_promotion', 'id_conclusion_psychotest', 'id_conclusion_assessment', 'readyness', 'color', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getdata() {
		$sql = "SELECT mtm.id_talent_matrix, mtm.name AS matrix_box, mjg.description AS job_grade, 
				mgp.code AS rating, mtm.kpi_value_min, mtm.kpi_value_max, mgd.description AS matrix_name, 
				mgd2.description AS psychogram, mgd3.description AS bei
				FROM master_talent_matrix mtm
				LEFT JOIN master_grade_promotion mgp
				ON mtm.id_grade_promotion = mgp.id_grade_promotion
				LEFT JOIN master_job_grade mjg
				ON mtm.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_general_data mgd
				ON mtm.id_group_matrix = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON mtm.id_conclusion_psychotest = mgd2.id_general_data
				LEFT JOIN master_general_data mgd3
				ON mtm.id_conclusion_assessment = mgd3.id_general_data
				WHERE mtm.id_company = ?
				ORDER BY mtm.id_talent_matrix DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_grade() {
		$sql = "SELECT mjg.id_job_grade id, mjg.description text 
				FROM master_job_grade mjg
				WHERE mjg.id_company = ?
				ORDER BY mjg.id_job_grade DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	public static function get_rating($data) {
		$id_job_grade = $data['id_job_grade'];
		$sql = "SELECT mgp.id_grade_promotion id, mgp.code text 
				FROM master_grade_promotion mgp
				WHERE mgp.id_job_grade = ? AND mgp.id_company = ? AND mgp.code NOT IN('NE','NR')
				ORDER BY mgp.id_grade_promotion ASC";	
        $result = DB::select($sql,[$id_job_grade,session('id_company')]);
        return $result;
    }
	
	public static function get_group_matrix() {
		$sql = "SELECT mgd.id_general_data id, mgd.code text
				FROM master_general_data mgd
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgd.id_company = ? AND mgt.general_type = 'master_talent_matrix'";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	public static function get_conclusion() {
		$sql = "SELECT mgd.id_general_data id, mgd.description text
				FROM master_general_data mgd
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgd.id_company = ? AND mgt.general_type = 'master_psychotest_conclusion'";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_talent_edit($data) {
        $sql = "SELECT * FROM master_talent_matrix mtm
				WHERE mtm.id_talent_matrix   = ?";
        $result = DB::select($sql, [$data['id_talent']])[0];
   
        return $result;
    }
	
	public static function getdataMatrix($fil_name,$fil_type,$fil_period,$fil_pro,$fil_reg,$fil_grade,$fil_dept,$fil_principal) {
		if($fil_name != null){
			$access_name = " AND htrh.id_talent_recommendation_header IN(".$fil_name.")";
		}
		else{
			$access_name = "";
		}
		if($fil_period != null){
			$talent_period = " AND htrd.kpi_desc = '".$fil_period."'";
			$suc_period = " AND htrs.kpi_desc = '".$fil_period."'";
		}
		else{
			$talent_period = "";
			$suc_period = "";
		}
		if($fil_pro != null){
			$access_pro = " AND htrh.id_position_routing IN(".$fil_pro.")";
		}
		else{
			$access_pro = "";
		}
		if($fil_reg != null){
			$access_reg = " AND mr.id_region IN(".$fil_reg.")";
		}
		else{
			$access_reg = "";
		}
		if($fil_grade != null){
			$access_grade = " AND mjg.id_job_grade IN(".$fil_grade.")";
		}
		else{
			$access_grade = "";
		}
		if($fil_dept != null){
			$access_dept = " AND md.id_dept IN(".$fil_dept.")";
		}
		else{
			$access_dept = "";
		}
		if($fil_principal != null){
			$access_principal = " AND rpp.id_principal IN(".$fil_principal.")";
		}
		else{
			$access_principal = "";
		}
		$sql = "SELECT DISTINCT htrd.id_talent_recommendation_detail, htrd.id_employee, he.nik_employee, he.name AS emp_name, he.identification_number, mgd.code, mtm.name AS matrix_box, 
				mgd.description AS matrix_name, htrd.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies,
				mtm.readyness, htrd.engagement_survey_result AS engagement_level,
				CASE WHEN htrd.id_grade_promotion = ANY(ARRAY[hcs.id_grade_promotion])
                      AND htrd.kpi_average::double precision >= hcs.minimum_annual_kpi
                      AND htrd.is_have_sp = false
                      AND htrd.is_have_kpk = false
                     THEN 'ELIGIBLE'::character varying
                     ELSE 'NOT ELIGIBLE'::character varying 
                END AS eligible_status
				FROM hr_talent_recommendation_detail htrd
				JOIN master_talent_matrix mtm
				ON htrd.id_job_grade = mtm.id_job_grade AND (htrd.id_grade_promotion = mtm.id_grade_promotion OR (htrd.id_grade_promotion IS NULL AND mtm.id_grade_promotion IS NULL))
				AND (htrd.kpi_average >= mtm.kpi_value_min AND htrd.kpi_average <= mtm.kpi_value_max) 
				AND htrd.id_conclusion_psychotest = mtm.id_conclusion_psychotest AND htrd.id_conclusion_assessment = mtm.id_conclusion_assessment
				LEFT JOIN hr_employee he
				ON htrd.id_employee = he.id_employee
				LEFT JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
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
				WHERE htrh.status = 'A' AND htrd.id_company = ? AND htrh.type= '".$fil_type."' ".$talent_period." ".$access_name." ".$access_pro." ".$access_reg." ".$access_grade." ".$access_dept." ".$access_principal."
			UNION	
			SELECT DISTINCT htrs.id_talent_recommendation_detail, htrd.id_employee, he.nik_employee, he.name AS emp_name, he.identification_number, mgd.code, mtm.name AS matrix_box, 
				mgd.description AS matrix_name, htrs.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies,
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
				WHERE htrh.status = 'A' AND htrs.id_company = ? AND htrh.type= '".$fil_type."' ".$suc_period." ".$access_name." ".$access_pro." ".$access_reg." ".$access_grade." ".$access_dept." ".$access_principal."
				ORDER BY code ASC";	
        $result = DB::select($sql,[session('id_company'),session('id_company')]);
        return $result;
    }
	
	public static function getBoxMatrix($code_box,$fil_name,$fil_type,$fil_period,$fil_pro,$fil_reg,$fil_grade,$fil_dept,$fil_principal) {
		if($fil_name != null){
			$access_name = " AND htrh.id_talent_recommendation_header IN(".$fil_name.")";
		}
		else{
			$access_name = "";
		}
		if($fil_period != null){
			$talent_period = " AND htrd.kpi_desc = '".$fil_period."'";
			$suc_period = " AND htrs.kpi_desc = '".$fil_period."'";
		}
		else{
			$talent_period = "";
			$suc_period = "";
		}
		if($fil_pro != null){
			$access_pro = " AND htrh.id_position_routing IN(".$fil_pro.")";
		}
		else{
			$access_pro = "";
		}
		if($fil_reg != null){
			$access_reg = " AND mr.id_region IN(".$fil_reg.")";
		}
		else{
			$access_reg = "";
		}
		if($fil_grade != null){
			$access_grade = " AND mjg.id_job_grade IN(".$fil_grade.")";
		}
		else{
			$access_grade = "";
		}
		if($fil_dept != null){
			$access_dept = " AND md.id_dept IN(".$fil_dept.")";
		}
		else{
			$access_dept = "";
		}
		if($fil_principal != null){
			$access_principal = " AND rpp.id_principal IN(".$fil_principal.")";
		}
		else{
			$access_principal = "";
		}
		$sql = "SELECT DISTINCT htrd.id_talent_recommendation_detail, htrd.id_employee, he.name AS emp_name, mgd.code, mtm.name AS matrix_box, 
				mgd.description AS matrix_name, htrd.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies,
				mtm.readyness, htrd.engagement_survey_result AS engagement_level,
				CASE WHEN htrd.id_grade_promotion = ANY(ARRAY[hcs.id_grade_promotion])
                      AND htrd.kpi_average::double precision >= hcs.minimum_annual_kpi
                      AND htrd.is_have_sp = false
                      AND htrd.is_have_kpk = false
                     THEN 'ELIGIBLE'::character varying
                     ELSE 'NOT ELIGIBLE'::character varying 
                END AS eligible_status
				FROM hr_talent_recommendation_detail htrd
				JOIN master_talent_matrix mtm
				ON htrd.id_job_grade = mtm.id_job_grade AND (htrd.id_grade_promotion = mtm.id_grade_promotion OR (htrd.id_grade_promotion IS NULL AND mtm.id_grade_promotion IS NULL))
				AND (htrd.kpi_average >= mtm.kpi_value_min AND htrd.kpi_average <= mtm.kpi_value_max) 
				AND htrd.id_conclusion_psychotest = mtm.id_conclusion_psychotest AND htrd.id_conclusion_assessment = mtm.id_conclusion_assessment
				LEFT JOIN hr_employee he
				ON htrd.id_employee = he.id_employee
				LEFT JOIN master_general_data mgd
				ON mtm.id_group_matrix = mgd.id_general_data
				LEFT JOIN master_grade_promotion mgp
				ON htrd.id_grade_promotion = mgp.id_grade_promotion
				LEFT JOIN master_general_data mgd2
				ON htrd.id_conclusion_psychotest = mgd2.id_general_data
				LEFT JOIN master_general_data mgd3
				ON htrd.id_conclusion_assessment = mgd3.id_general_data
				LEFT JOIN master_position_detail mpd
				ON htrd.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
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
				WHERE htrh.status = 'A' AND htrd.id_company = ? AND mgd.code = '".$code_box."' AND htrh.type= '".$fil_type."' ".$talent_period." ".$access_name." ".$access_pro." ".$access_reg." ".$access_grade." ".$access_dept." ".$access_principal."
			UNION	
				SELECT DISTINCT htrs.id_talent_recommendation_detail, htrd.id_employee, he.name AS emp_name, mgd.code, mtm.name AS matrix_box, 
				mgd.description AS matrix_name, htrs.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies,
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
				JOIN master_general_data mgd
				ON mtm.id_group_matrix = mgd.id_general_data
				LEFT JOIN master_grade_promotion mgp
				ON htrs.id_grade_promotion = mgp.id_grade_promotion
				LEFT JOIN master_general_data mgd2
				ON mgd2.id_general_data = coalesce(htrs.id_conclusion_psychotest,htrd.id_conclusion_psychotest)
				LEFT JOIN master_general_data mgd3
				ON mgd3.id_general_data = coalesce(htrs.id_conclusion_assessment,htrd.id_conclusion_assessment)
				LEFT JOIN master_position_detail mpd
				ON htrd.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
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
				WHERE htrh.status = 'A' AND htrs.id_company = ? AND mgd.code = '".$code_box."' AND htrh.type= '".$fil_type."' ".$suc_period." ".$access_name." ".$access_pro." ".$access_reg." ".$access_grade." ".$access_dept." ".$access_principal."
				ORDER BY emp_name ASC";	
        $result = DB::select($sql,[session('id_company'),session('id_company')]);
        return $result;
    }
	
	public static function get_box($fil_name,$fil_type,$fil_period,$fil_pro,$fil_reg,$fil_grade,$fil_dept,$fil_principal) {
		if($fil_name != null){
			$access_name = " AND htrh.id_talent_recommendation_header IN(".$fil_name.")";
		}
		else{
			$access_name = "";
		}
		if($fil_period != null){
			$talent_period = " AND htrd.kpi_desc = '".$fil_period."'";
			$suc_period = " AND htrs.kpi_desc = '".$fil_period."'";
		}
		else{
			$talent_period = "";
			$suc_period = "";
		}
		if($fil_pro != null){
			$access_pro = " AND htrh.id_position_routing IN(".$fil_pro.")";
		}
		else{
			$access_pro = "";
		}
		if($fil_reg != null){
			$access_reg = " AND mr.id_region IN(".$fil_reg.")";
		}
		else{
			$access_reg = "";
		}
		if($fil_grade != null){
			$access_grade = " AND mjg.id_job_grade IN(".$fil_grade.")";
		}
		else{
			$access_grade = "";
		}
		if($fil_dept != null){
			$access_dept = " AND md.id_dept IN(".$fil_dept.")";
		}
		else{
			$access_dept = "";
		}
		if($fil_principal != null){
			$access_principal = " AND rpp.id_principal IN(".$fil_principal.")";
		}
		else{
			$access_principal = "";
		}
		$sql = "SELECT mgd.id_general_data, mgd.code, j_all.matrix_box, mgd.description AS matrix_name, j_all.count AS count_emp
					FROM master_general_data mgd
					JOIN master_general_type mgt
					ON mgd.id_general_type = mgt.id_general_type
					LEFT JOIN (
						SELECT j_box.matrix_box, j_box.code, j_box.matrix_name, count(j_box.code) FROM (				
							SELECT DISTINCT htrd.id_talent_recommendation_detail, htrh.id_position_routing, mr.id_region, mjg.id_job_grade,  
							md.id_dept, htrd.id_employee, he.name AS emp_name, mgd.code,  mtm.name AS matrix_box, mgd.description AS matrix_name, 
							htrd.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies
							FROM hr_talent_recommendation_detail htrd
							JOIN master_talent_matrix mtm
							ON htrd.id_job_grade = mtm.id_job_grade AND (htrd.id_grade_promotion = mtm.id_grade_promotion OR (htrd.id_grade_promotion IS NULL AND mtm.id_grade_promotion IS NULL))
							AND (htrd.kpi_average >= mtm.kpi_value_min AND htrd.kpi_average <= mtm.kpi_value_max) 
							AND htrd.id_conclusion_psychotest = mtm.id_conclusion_psychotest AND htrd.id_conclusion_assessment = mtm.id_conclusion_assessment
							LEFT JOIN hr_employee he
							ON htrd.id_employee = he.id_employee
							LEFT JOIN master_general_data mgd
							ON mtm.id_group_matrix = mgd.id_general_data
							LEFT JOIN master_grade_promotion mgp
							ON htrd.id_grade_promotion = mgp.id_grade_promotion
							LEFT JOIN master_general_data mgd2
							ON htrd.id_conclusion_psychotest = mgd2.id_general_data
							LEFT JOIN master_general_data mgd3
							ON htrd.id_conclusion_assessment = mgd3.id_general_data
							LEFT JOIN hr_talent_recommendation_header htrh
							ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
							LEFT JOIN master_position_detail mpd
							ON htrd.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
							LEFT JOIN master_region mr
							ON htrd.id_region = mr.id_region
							LEFT JOIN master_job_grade mjg
							ON htrd.id_job_grade = mjg.id_job_grade
							LEFT JOIN master_department md
							ON htrd.id_dept = md.id_dept
							LEFT JOIN relation_positiondetail_principal rpp
							ON htrd.id_position_detail = rpp.id_position_detail
							WHERE htrh.status = 'A' AND htrd.id_company = ? AND htrh.type= '".$fil_type."' ".$talent_period." ".$access_name." ".$access_pro." ".$access_reg." ".$access_grade." ".$access_dept." ".$access_principal."
						UNION	
							SELECT DISTINCT htrs.id_talent_recommendation_detail, htrh.id_position_routing, mr.id_region, mjg.id_job_grade,  
							md.id_dept, htrd.id_employee, he.name AS emp_name, mgd.code,  mtm.name AS matrix_box, mgd.description AS matrix_name, 
							htrs.kpi_average, mgp.code AS rating, mgd2.description AS potencies, mgd3.description AS competencies
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
							JOIN master_general_data mgd
							ON mtm.id_group_matrix = mgd.id_general_data
							LEFT JOIN master_grade_promotion mgp
							ON htrs.id_grade_promotion = mgp.id_grade_promotion
							LEFT JOIN master_general_data mgd2
							ON mgd2.id_general_data = coalesce(htrs.id_conclusion_psychotest,htrd.id_conclusion_psychotest)
							LEFT JOIN master_general_data mgd3
							ON mgd3.id_general_data = coalesce(htrs.id_conclusion_assessment,htrd.id_conclusion_assessment)
							LEFT JOIN hr_talent_recommendation_header htrh
							ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
							LEFT JOIN master_position_detail mpd
							ON htrd.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
							LEFT JOIN master_region mr
							ON htrd.id_region = mr.id_region
							LEFT JOIN master_job_grade mjg
							ON htrd.id_job_grade = mjg.id_job_grade
							LEFT JOIN master_department md
							ON htrd.id_dept = md.id_dept
							LEFT JOIN relation_positiondetail_principal rpp
							ON htrd.id_position_detail = rpp.id_position_detail
							WHERE htrh.status = 'A' AND htrs.id_company = ? AND htrh.type= '".$fil_type."' ".$suc_period." ".$access_name." ".$access_pro." ".$access_reg." ".$access_grade." ".$access_dept." ".$access_principal."
						) AS j_box
					GROUP BY j_box.code, j_box.matrix_name, j_box.matrix_box
					) AS j_all
					ON mgd.code = j_all.code
					WHERE mgt.general_type = 'master_talent_matrix' AND mgd.id_company = ?";	
        $result = DB::select($sql,[session('id_company'),session('id_company'),session('id_company')]);
        return $result;
    }
	
	public static function filter_type() {
		$sql = "SELECT DISTINCT htrh.type id,
				CASE
					WHEN htrh.type = 'P' THEN 'TALENT'
					WHEN htrh.type = 'S' THEN 'SUCCESSOR'
				END AS text
				FROM hr_talent_recommendation_header htrh
				WHERE htrh.id_company = ?";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function filter_period($arrReco) {
		if($arrReco != null){
			$inReco = $arrReco;
		}
		else{
			$inReco = 'null';
		}
		$sql = "SELECT DISTINCT htrd.kpi_desc id, htrd.kpi_desc text
					FROM hr_talent_recommendation_detail htrd
					JOIN hr_talent_recommendation_header htrh
					ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header 
					WHERE htrh.status = 'A' AND htrd.id_company = ? AND htrd.id_talent_recommendation_header IN(".$inReco.")
				UNION		
				SELECT DISTINCT htrs.kpi_desc id, htrs.kpi_desc text 
					FROM hr_talent_recommendation_summary htrs
					LEFT JOIN hr_talent_recommendation_detail htrd
					ON htrs.id_talent_recommendation_detail = htrd.id_talent_recommendation_detail
					JOIN hr_talent_recommendation_header htrh
					ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header 
					WHERE htrh.status = 'A' AND htrs.id_company = ? AND htrd.id_talent_recommendation_header IN(".$inReco.")
					ORDER BY id DESC";	
        $result = DB::select($sql,[session('id_company'),session('id_company')]);
        return $result;
    }
	
	public static function filter_projected() {
		$sql = "SELECT DISTINCT mpr.id_routing id, mpr.description text 
				FROM hr_talent_recommendation_header htrh
				LEFT JOIN master_position_routing mpr
				ON htrh.id_position_routing = mpr.id_routing
				WHERE htrh.status = 'A' AND htrh.id_company = ? 
				ORDER BY mpr.description DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function filter_region() {
		$sql = "SELECT DISTINCT mr.id_region id, mr.description text 
				FROM hr_talent_recommendation_detail htrd
				LEFT JOIN master_position_detail mpd
				ON htrd.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_region mr
				ON mb.id_region = mr.id_region
				WHERE htrd.id_company = ? 
				ORDER BY mr.description ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function filter_grade() {
		$sql = "SELECT DISTINCT mjg.id_job_grade id, mjg.description text
				FROM hr_talent_recommendation_detail htrd
				LEFT JOIN master_position_detail mpd
				ON htrd.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE htrd.id_company = ?
				ORDER BY mjg.description ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function filter_dept() {
		$sql = "SELECT DISTINCT md.id_dept id, md.description text
				FROM hr_talent_recommendation_detail htrd
				LEFT JOIN master_position_detail mpd
				ON htrd.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				WHERE htrd.id_company = ?
				ORDER BY md.description ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function filter_principal() {
		$sql = "SELECT DISTINCT mp.id_principal id, mp.description text
				FROM hr_talent_recommendation_detail htrd
				LEFT JOIN master_position_detail mpd
				ON htrd.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN relation_positiondetail_principal rpp
				ON mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal
				WHERE htrd.id_company = ?
				ORDER BY mp.description ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_reco_name() {
		$sql = "SELECT htrh.id_talent_recommendation_header id, htrh.notes text
				FROM hr_talent_recommendation_header htrh
				WHERE htrh.status = 'A' AND htrh.id_company = ?
				ORDER BY htrh.notes ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_reco_detail($fil_id_reco_name) {
		$sql = "SELECT htrd.id_talent_recommendation_detail
				FROM hr_talent_recommendation_header htrh
				LEFT JOIN hr_talent_recommendation_detail htrd
				ON htrh.id_talent_recommendation_header = htrd.id_talent_recommendation_header
				WHERE htrh.status = 'A' AND htrh.id_company = ? AND htrh.id_talent_recommendation_header IN(".$fil_id_reco_name.")";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function gen_box_talent($fil_id_reco_detail,$fil_reco_date) {
		$id_talent_reco_detail = "array[".$fil_id_reco_detail."]";
        $result = DB::select("select * from  spgeneratetalentsummary('".$fil_reco_date."', ".session('id_company').", ".$id_talent_reco_detail.")");
        return $result;
    }
		
}
