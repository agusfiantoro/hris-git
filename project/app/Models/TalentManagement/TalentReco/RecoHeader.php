<?php

namespace App\Models\TalentManagement\TalentReco;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecoHeader extends Model
{
	use HasFactory;
	
    protected $table = 'hr_talent_recommendation_header';
	protected $primaryKey = 'id_talent_recommendation_header';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_talent_recommendation_header', 'reference_number', 'id_employee_request', 'id_position_routing', 'id_region', 'id_branch', 'id_survey_header', 'id_hiring_request_header', 'notes', 'type', 'period_date', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getkode($idCompany=null){
		$idCompany = $idCompany ?? session('id_company');
		
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', $idCompany)->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-TRF-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%TRF-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%TRF-{$monthyear}%")->max('reference_number'), 15, 21);
		}	
		$no = 1;
		if($noUrutAkhir) {
			$kode =  sprintf("%06s",abs($noUrutAkhir + 1));
			$nomorbaru = $nomor.$kode;
			}
		else {
			$kode =  sprintf("%06s",$no);
			$nomorbaru = $nomor.$kode;
		}
		return $nomorbaru;		
	}
	
	public static function getdata($status) {
		if($status){
			$status = " AND htrh.status = '".$status."'";
		}
		$sql = "SELECT he.name AS emp_name, mpr.description AS pro_pos, hsh.description AS survey,
				CASE 
					WHEN hhrh.reference_number IS NULL  THEN  null
					ELSE CONCAT(hhrh.reference_number,' (',mpr2.description,')')
				END AS fpk_req,
				htrh.*
				FROM hr_talent_recommendation_header htrh
				LEFT JOIN hr_employee he
				ON htrh.id_employee_request = he.id_employee
				LEFT JOIN master_position_routing mpr
				ON htrh.id_position_routing = mpr.id_routing
				LEFT JOIN hr_survey_header hsh
				ON htrh.id_survey_header = hsh.id_survey_header
				LEFT JOIN hr_hiring_request_header hhrh
				ON htrh.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN master_position_routing mpr2
				ON hhrh.id_position_routing_request = mpr2.id_routing
				WHERE htrh.id_company = ? ".$status."
				ORDER BY htrh.id_talent_recommendation_header DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_employee_by() {
        $sql = "SELECT he.id_employee id, he.name text
				FROM hr_employee he
				WHERE he.id_company = ? AND he.id_user = ? AND he.status = 'A'";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
        return $result;
    }
	
	public static function get_grade() {
        $sql = "SELECT mjg.id_job_grade id, mjg.description text
				FROM master_job_grade mjg
				WHERE mjg.id_company = ?
				ORDER BY mjg.description ASC";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_region() {
        $sql = "SELECT mr.id_region id, mr.description text
				FROM master_region mr
				WHERE mr.id_company = ? AND mr.status = 'A'
				ORDER BY mr.description ASC";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_branch($idRegion) {
		if($idRegion != null){
			$region = " AND mb.id_region = ".$idRegion;
		}
		else{
			$region =" ";
		}
        $sql = "SELECT mb.id_branch id, mb.description text
				FROM master_branch mb
				WHERE mb.id_company = ? ".$region." AND mb.status = 'A'
				ORDER BY mb.description ASC";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_projected() {
        $sql = "SELECT mpr.id_routing id, mpr.description text
				FROM master_position_routing mpr
				JOIN (
					SELECT DISTINCT mpd.id_position_routing FROM master_position_detail mpd
					WHERE mpd.assigned_to_company IS NULL AND mpd.id_company = ".session('id_company')." AND mpd.status = 'A'
				) AS pos_detail
				ON mpr.id_routing = pos_detail.id_position_routing
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade AND mjg.id_company = ".session('id_company')."
				WHERE mpr.id_company = ".session('id_company')." AND mpr.status = 'A' 
			--	AND mjg.job_class_group NOT IN('ant-level')
				ORDER BY mpr.description ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_trigger_pos($data) {
		$id_routing = $data['id_routing'];
        $sql = "SELECT DISTINCT mpr.id_routing, mpr.description text
				FROM master_position_detail mpd
				JOIN master_position_detail mpd2
				ON mpd.id_position_detail = mpd2.parent_id_position_detail AND mpd2.status = 'A'
				JOIN master_position_routing mpr
				ON mpd2.id_position_routing = mpr.id_routing
				WHERE mpd.id_position_routing = ".$id_routing." AND mpd.status = 'A'
				AND mpd.assigned_to_company IS NULL AND mpd.id_company = ".session('id_company')."
				AND mpd.secondary_position = false";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_source_pos() {
        $sql = "SELECT mpr.id_routing id, mpr.description text
				FROM master_position_routing mpr
				JOIN (
					SELECT DISTINCT mpd.id_position_routing FROM master_position_detail mpd
					WHERE mpd.assigned_to_company IS NULL AND mpd.id_company = ".session('id_company')." AND mpd.status = 'A'
				) AS pos_detail
				ON mpr.id_routing = pos_detail.id_position_routing
				WHERE mpr.id_company = ".session('id_company')." AND mpr.status = 'A'
				ORDER BY mpr.description ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_fpk() {
        $sql = "SELECT hhrh.id_hiring_request_header id, 
				CONCAT(hhrh.reference_number,' (',mpr.description,')') text
				FROM hr_hiring_request_header hhrh
				JOIN (
					SELECT hhrr.id_hiring_request_header, count(hhrr.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_recommendation hhrr
					GROUP BY id_hiring_request_header
				) AS h_r
				ON hhrh.id_hiring_request_header = h_r.id_hiring_request_header
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				WHERE hhrh.id_company = ? AND mgd.code = 'Approved'
				ORDER BY hhrh.reference_number ASC";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_survey() {
        $sql = "SELECT DISTINCT hsh.id_survey_header id, hsh.description text
				FROM hr_config_engagement_category hcec
				JOIN hr_survey_header hsh
				ON hcec.id_survey_header = hsh.id_survey_header
				WHERE hsh.id_company = ? AND hsh.status = 'A' AND hsh.survey_category = 'Survey' AND hsh.published = true";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_list_talent($data) {
		ini_set('max_execution_time', 300); // limit execution time to 1 hour
	//	dd($data['source_branch_list']);
		$period_date = $data['period_date'];
		$source_grade_array = $data['source_grade_list'];
		if($source_grade_array != NULL){
			$source_grade_in = implode(",",$source_grade_array);
			$source_grade = "array[".$source_grade_in."]";
		}
		else{
			$source_grade = "NULL";
		}
		$source_pos_array = $data['source_pos_list'];
		if($source_pos_array != NULL){
			$source_pos_in = implode(",",$source_pos_array);
			$source_pos = "array[".$source_pos_in."]";
		}
		else{
			$source_pos = "NULL";
		}
		
		$source_region_array = $data['source_region_list'];
		if($source_region_array != NULL){
			$source_region = "array[".$source_region_array."]";
		}
		else{
			$source_region = "NULL";
		}
		
		$source_branch_array = $data['source_branch_list'];
		if($source_branch_array != NULL){
			$source_branch_in = implode(",",$source_branch_array);
			$source_branch = "array[".$source_branch_in."]";
		}
		else{
			$source_branch = "NULL";
		}
		
		$fpk_list = $data['fpk_list'];
		$survey_list = $data['survey_list'];
		$submit = $data['submit'];			
		if($submit == true){
			$id_employee = $data['id_employee'];		
			$list_employee = implode(",",$id_employee);
			$emp = " AND gtlv.id_employee IN(".$list_employee.")";
		}
		else{
			$emp = "";
		}
		/*
        $sql = "SELECT gtlv.*, j_batch.id_batch, j_batch.batch_name, j_psy.id_potencies, j_psy.potencies 
				FROM get_talent_list_view(".session('id_company').",".$source_pos.",".$source_region.",".$source_branch.",?,?,?,?) gtlv
				LEFT JOIN (
					SELECT max_id.*, pmb.id_batch, pmb.batch_name  FROM public.psycho_batch_participant pbp2
					JOIN (
						SELECT pbp.id_employee, max(pbp.id_batch_participant) AS max_id_batch_participant
						FROM public.psycho_batch_participant pbp
						WHERE pbp.id_employee  IS NOT NULL AND pbp.id_company = ".session('id_company')."
						GROUP BY pbp.id_employee	
					) AS max_id
					ON pbp2.id_batch_participant = max_id.max_id_batch_participant
					JOIN public.psycho_master_batch pmb
					ON pbp2.id_batch = pmb.id_batch
				) AS j_batch
				ON gtlv.id_employee = j_batch.id_employee
				LEFT JOIN (
					SELECT pmpm.id_conclusion AS id_potencies, mgd.description AS potencies, 
					pprr.id_employee, pprr.id_batch, pprr.result_value 
					FROM web.psycho_psychogram_result_report pprr
					LEFT JOIN web.psycho_master_psychogram_matrix pmpm
					ON pprr.id_department = pmpm.id_department AND pprr.id_job_grade = pmpm.id_job_grade AND pmpm.id_company = ".session('id_company')."
					LEFT JOIN public.master_general_data mgd
					ON pmpm.id_conclusion = mgd.id_general_data AND mgd.status = 'A'
					WHERE pprr.result_value >= pmpm.value_min AND pprr.result_value <= pmpm.value_max 
					AND pprr.id_company = ".session('id_company')."
				) AS j_psy
				ON gtlv.id_employee = j_psy.id_employee AND j_batch.id_batch = j_psy.id_batch";
		*/
		$sql = "SELECT 
					gtlv.*, 
					gtlv.id_psychogram_result AS id_potencies, 
					gtlv.last_psychogram_result AS potencies, 
					pprr.id_psychogram_result_report, 
					pgp.test_date AS psycho_test_date, 
					he2.status AS employee_psychotest_status, 
					md.description AS psychogram_dept,
					mjg.description AS psychogram_jobgrade,
					pprr.id_employee as id_employee_psychogram, 
					pprr.id_candidate as id_candidate_psychogram,
					pprr.id_department AS id_psychogram_department,
					pprr.id_job_grade AS id_psychogram_jobgrade,
					CASE
						WHEN pgp.id_group_psychotest IS NOT NULL AND hc.id_candidate IS NOT NULL AND pgp.id_employee IS NULL AND hc.id_candidate = pgp.id_candidate THEN 'Candidate'
						WHEN pgp.id_group_psychotest IS NOT NULL AND he2.status = 'I' THEN 'Rehire'
						WHEN pgp.id_group_psychotest IS NOT NULL THEN 'Active'
						ELSE NULL
					END AS psychotest_source, pbp.creation_date AS batch_assign_date, pgp.id_batch AS id_batch
				FROM get_talent_list_view(".session('id_company').",".$source_pos.",".$source_region.",".$source_branch.",".$source_grade.",?,?,?) gtlv
				JOIN hr_employee he
				ON gtlv.id_employee = he.id_employee
				LEFT JOIN hr_employee he2
				ON he.nik_employee = he2.nik_employee
				LEFT JOIN web.hr_candidate hc
				ON he2.identification_number = hc.identification_number
				LEFT JOIN web.psycho_psychogram_result_report pprr 
				ON (he2.id_employee = pprr.id_employee OR hc.id_candidate = pprr.id_candidate) --AND gtlv.id_batch = pprr.id_batch
				LEFT JOIN web.psycho_group_psychotest pgp
				ON (he2.id_employee = pgp.id_employee OR hc.id_candidate = pgp.id_candidate) --AND gtlv.id_batch = pgp.id_batch
				LEFT JOIN master_department md
				ON pprr.id_department = md.id_dept
				LEFT JOIN master_job_grade mjg
				ON pprr.id_job_grade = mjg.id_job_grade
				LEFT JOIN psycho_batch_participant pbp
				ON pgp.id_batch = pbp.id_batch
				WHERE he.status = 'A' ".$emp;
        $result = DB::select($sql,[$survey_list,$fpk_list,$period_date]);
		$grouped = collect($result)->groupBy('nik_employee');
		$ret = [];
		foreach($grouped as $k => $group) {
			$group = collect($group);
			$maxIdPsycho = null;
			$toInsert = null;
			foreach($group as $key => $data) {
				if($data->id_psychogram_result_report > $maxIdPsycho) {
					$maxIdPsycho = $data->id_psychogram_result_report;
					$toInsert = $data;
					$sql = DB::selectOne("SELECT pmpm.id_conclusion, mgd.description,
						CASE
							WHEN pprr.id_employee IS NULL THEN 'Candidate'
							WHEN pprr.id_employee IS NOT NULL AND he.status = 'I' THEN 'Rehire'
							ELSE 'Active'
						END AS psychotest_source
						FROM web.psycho_psychogram_result_report pprr
						LEFT JOIN web.psycho_master_psychogram_matrix pmpm ON
						pprr.id_department = pmpm.id_department AND pprr.id_job_grade = pmpm.id_job_grade
						LEFT JOIN master_general_data mgd ON
						pmpm.id_conclusion = mgd.id_general_data 
						LEFT JOIN hr_employee he ON
						pprr.id_employee = he.id_employee
						WHERE pprr.id_psychogram_result_report = ? AND pprr.result_value BETWEEN pmpm.value_min AND pmpm.value_max"
					, [$data->id_psychogram_result_report]);
					$toInsert->id_psychogram_result = @$sql->id_conclusion;
					$toInsert->id_potencies = @$sql->id_conclusion;
					$toInsert->potencies = @$sql->description;
					if($sql) {
						$toInsert->psychotest_source = $sql->psychotest_source;
					}
				}
			}
			if($maxIdPsycho) {
				$ret[] = $toInsert;
			} else {
				$ret[] = $group[count($group)-1];
			}
		}
	//	dd($ret);
        return $ret;
    }

	public static function get_employee_kpi($request) {
		$survey_list = $request->survey_list;
		$fpk_list = $request->fpk_list;
		$period_date = $request->period_date;
		$jobGrade = DB::select("SELECT mjg.*
				FROM hr_employee he
				JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE he.nik_employee = ?
				AND he.status = 'A'
				AND mjg.description = COALESCE(?, mjg.description)
				AND mjg.id_company = he.id_company
				", [$request->nik_employee, $request->desc_job_grade]);
		$idJobGrade = collect($jobGrade)->pluck('id_job_grade')->toArray();
		$idJobGrade = "array[".implode(",", $idJobGrade)."]";
		$sql = "SELECT 
					gtlv.*, 
					gtlv.id_psychogram_result AS id_potencies, 
					gtlv.last_psychogram_result AS potencies
				FROM get_talent_list_view(".session('id_company').",NULL,NULL,NULL,".$idJobGrade.",?,?,?) gtlv
				WHERE gtlv.nik_employee = ?";
		$kpi = DB::select($sql, [$survey_list,$fpk_list,$period_date, $request->nik_employee]);
		return $kpi;
	}
	
	public static function get_reco_talent($data) {
		$period_date = $data['period_date'];
		$source_grade_array = $data['source_grade_list'];
		if($source_grade_array != NULL){
			$source_grade_in = implode(",",$source_grade_array);
			$source_grade = "array[".$source_grade_in."]";
		}
		else{
			$source_grade = "NULL";
		}
		$source_pos_array = $data['source_pos_list'];
		if($source_pos_array != NULL){
			$source_pos_in = implode(",",$source_pos_array);
			$source_pos = "array[".$source_pos_in."]";
		}
		else{
			$source_pos = "NULL";
		}
		
		$source_region_array = $data['source_region_list'];
		if($source_region_array != NULL){
			$source_region = "array[".$source_region_array."]";
		}
		else{
			$source_region = "NULL";
		}
		
		$source_branch_array = $data['source_branch_list'];
		if($source_branch_array != NULL){
			$source_branch_in = implode(",",$source_branch_array);
			$source_branch = "array[".$source_branch_in."]";
		}
		else{
			$source_branch = "NULL";
		}
		
		$fpk_list = $data['fpk_list'];
		$survey_list = $data['survey_list'];
		$id_employee = $data['id_employee'];		
		$list_employee = implode(",",$id_employee);
		/*
        $sql = "SELECT gtlv.*, j_batch.id_batch, j_batch.batch_name, j_psy.id_potencies, j_psy.potencies  
					FROM get_talent_list_view(".session('id_company').",".$source_pos.",".$source_region.",".$source_branch.",?,?,?,?) gtlv
					LEFT JOIN (
						SELECT max_id.*, pmb.id_batch, pmb.batch_name  FROM public.psycho_batch_participant pbp2
						JOIN (
							SELECT pbp.id_employee, max(pbp.id_batch_participant) AS max_id_batch_participant
							FROM public.psycho_batch_participant pbp
							WHERE pbp.id_employee  IS NOT NULL AND pbp.id_company = ".session('id_company')."
							GROUP BY pbp.id_employee	
						) AS max_id
						ON pbp2.id_batch_participant = max_id.max_id_batch_participant
						JOIN public.psycho_master_batch pmb
						ON pbp2.id_batch = pmb.id_batch
					) AS j_batch
					ON gtlv.id_employee = j_batch.id_employee
					LEFT JOIN (
						SELECT pmpm.id_conclusion AS id_potencies, mgd.description AS potencies, 
						pprr.id_employee, pprr.id_batch, pprr.result_value 
						FROM web.psycho_psychogram_result_report pprr
						LEFT JOIN web.psycho_master_psychogram_matrix pmpm
						ON pprr.id_department = pmpm.id_department AND pprr.id_job_grade = pmpm.id_job_grade AND pmpm.id_company = ".session('id_company')."
						LEFT JOIN public.master_general_data mgd
						ON pmpm.id_conclusion = mgd.id_general_data AND mgd.status = 'A'
						WHERE pprr.result_value >= pmpm.value_min AND pprr.result_value <= pmpm.value_max 
						AND pprr.id_company = ".session('id_company')."
					) AS j_psy
					ON gtlv.id_employee = j_psy.id_employee AND j_batch.id_batch = j_psy.id_batch
					WHERE gtlv.id_employee IN(".$list_employee.")";
		*/
		$sql = "SELECT gtlv.*, gtlv.id_psychogram_result AS id_potencies, gtlv.last_psychogram_result AS potencies 
				FROM get_talent_list_view(".session('id_company').",".$source_pos.",".$source_region.",".$source_branch.",".$source_grade.",?,?,?) gtlv
				WHERE gtlv.id_employee IN(".$list_employee.")";
        $result = DB::select($sql,[$survey_list,$fpk_list,$period_date]);
        return $result;
    }
	
	public static function gen_batch($data) {
		$id_company = $data['id_company'];
		$id_talent_recommendation_header = $data['id_talent_recommendation_header_batch'];
		$id_branch = $data['id_branch'];
		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
		$location = $data['location'];
	//	$id_emp_batch = $data['id_emp_batch'];
		if($data['id_emp_batch'] != null){
			$id_emp_batch = "array[".$data['id_emp_batch']."]";
		}
		else{
			$id_emp_batch = 'null';
		}
		$result = DB::select("select * from  spgeneratepsychobatch(?, ?, ?, ?, ?, ?, ".$id_emp_batch.")",
			[$id_company,$id_talent_recommendation_header,$id_branch,$start_date,$end_date,$location]
		);
		
		return $result;
	}
	
	public static function get_talent_edit($data) {
        $result = [];
        $sql = "SELECT * FROM hr_talent_recommendation_header htrh
				WHERE htrh.id_talent_recommendation_header = ?";
        $result = (Array) DB::select($sql, [$data['id_talent_recommendation_header']])[0];

        $sql_detail = "SELECT htrd.*, he.nik_employee, he.name AS name_employee, mpr.description AS position_route,
						md.description AS department, mjg.description AS job_grade, mr.description AS region, 
						mb.description AS branch, mgp.code AS final_rating, pmb.batch_name, 
						mgd.description AS potencies,
						md_psychogram1.description as psychogram_dept, mjg_psychogram1.description as psychogram_jobgrade,
						mgd2.code as competencies_desc, 
						htrd.id_batch AS id_batch, pbp.creation_date AS batch_assign_date,
						COALESCE(pprr.id_employee, pprr.id_candidate) AS id_user_assessment, pprr.id_batch AS id_batch_assessment,
						md_psychogram1.id_dept as id_dept_psychogram, mjg_psychogram1.id_job_grade AS id_job_grade_psychogram,
						CASE
							WHEN pprr.id_employee = he.id_employee THEN 'Active'
							WHEN pprr.id_employee = he2.id_employee AND he2.status = 'I' THEN 'Rehire'
							WHEN pprr.id_employee IS NULL THEN 'Candidate'
						END AS source
						FROM hr_talent_recommendation_detail htrd
						JOIN hr_employee he
						ON htrd.id_employee = he.id_employee AND he.status = 'A'
						LEFT JOIN hr_employee he2
						ON he.nik_employee = he2.nik_employee
						LEFT JOIN web.hr_candidate hc
						ON he.identification_number = hc.identification_number
						LEFT JOIN master_position_detail mpd
						ON htrd.id_position_detail = mpd.id_position_detail
						LEFT JOIN master_position_routing mpr
						ON mpd.id_position_routing = mpr.id_routing
						LEFT JOIN master_department md
						ON htrd.id_dept = md.id_dept
						LEFT JOIN master_job_grade mjg
						ON htrd.id_job_grade = mjg.id_job_grade
						LEFT JOIN master_region mr
						ON htrd.id_region = mr.id_region
						LEFT JOIN master_branch mb
						ON htrd.id_branch = mb.id_branch 
						LEFT JOIN master_grade_promotion mgp
						ON htrd.id_grade_promotion = mgp.id_grade_promotion
						LEFT JOIN psycho_master_batch pmb
						ON htrd.id_batch = pmb.id_batch 
						LEFT JOIN master_general_data mgd
						ON htrd.id_conclusion_psychotest = mgd.id_general_data
						LEFT JOIN master_general_data mgd2
						ON htrd.id_conclusion_assessment = mgd2.id_general_data
						LEFT JOIN master_job_grade mjg_psychogram1
						ON htrd.id_job_grade_psychogram = mjg_psychogram1.id_job_grade
						LEFT JOIN master_department md_psychogram1
						ON htrd.id_dept_psychogram = md_psychogram1.id_dept
						LEFT JOIN web.psycho_psychogram_result_report pprr
						ON (he2.id_employee = pprr.id_employee OR hc.id_candidate = pprr.id_candidate)
--						AND htrd.id_conclusion_psychotest IS NOT NULL
						AND (htrd.id_batch = pprr.id_batch OR htrd.id_batch IS NULL)
						LEFT JOIN web.psycho_master_psychogram_matrix pmpm 
						ON pprr.id_department = pmpm.id_department 
						AND pprr.id_job_grade = pmpm.id_job_grade 
						AND pprr.id_company = pmpm.id_company 
						AND pprr.result_value BETWEEN pmpm.value_min AND pmpm.value_max 
						LEFT JOIN master_general_data mgd3
						ON pmpm.id_conclusion = mgd3.id_general_data 
						LEFT JOIN master_department md_psychogram 
						ON pprr.id_department = md_psychogram.id_dept 
						LEFT JOIN master_job_grade mjg_psychogram 
						ON pprr.id_job_grade = mjg_psychogram.id_job_grade
						LEFT JOIN psycho_batch_participant pbp 
						ON htrd.id_batch = pbp.id_batch 
						AND htrd.id_employee = pbp.id_employee 
						WHERE htrd.id_talent_recommendation_header = ?
						ORDER BY he.name";
        $result_menu = DB::select($sql_detail, [$data['id_talent_recommendation_header']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_talent_recommendation_detail')->toArray();
		
        $result['emp'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['emp'][] = [
                'id_talent_recommendation_detail' => $group_menu[$value][0]->id_talent_recommendation_detail,
                'id_employee' => $group_menu[$value][0]->id_employee,
                'nik_employee' => $group_menu[$value][0]->nik_employee,
                'name_employee' => $group_menu[$value][0]->name_employee,
                'position_route' => $group_menu[$value][0]->position_route,
                'job_grade' => $group_menu[$value][0]->job_grade,
                'branch' => $group_menu[$value][0]->branch,
                'id_rating' => $group_menu[$value][0]->id_grade_promotion,
                'final_rating' => $group_menu[$value][0]->final_rating,
                'kpi_average' => $group_menu[$value][0]->kpi_average,
                'potencies' => $group_menu[$value][0]->potencies,
                'competencies' => $group_menu[$value][0]->id_conclusion_assessment,
				'competencies_desc' => $group_menu[$value][0]->competencies_desc,
                'batch_name' => $group_menu[$value][0]->batch_name,
				'id_batch' => $group_menu[$value][0]->id_batch,
                'sp' => $group_menu[$value][0]->is_have_sp,
                'kpk' => $group_menu[$value][0]->is_have_kpk,
                'potencies' => $group_menu[$value][0]->potencies,
				'id_potencies' => $group_menu[$value][0]->id_conclusion_psychotest,
				'psychogram_dept' => $group_menu[$value][0]->psychogram_dept,
				'psychogram_jobgrade' => $group_menu[$value][0]->psychogram_jobgrade,
                'eng_level' => $group_menu[$value][0]->engagement_survey_result,
				'batch_assign_date' => $group_menu[$value][0]->batch_assign_date ? Carbon::parse($group_menu[$value][0]->batch_assign_date)->format('d M Y') : null,
				'id_user_assessment' => $group_menu[$value][0]->id_user_assessment,
				'id_batch_assessment' => $group_menu[$value][0]->id_batch_assessment,
				'source_assessment' => $group_menu[$value][0]->source,
				'id_dept_psychogram' => $group_menu[$value][0]->id_dept_psychogram,
				'id_job_grade_psychogram' => $group_menu[$value][0]->id_job_grade_psychogram,
			];
			$x = explode(" - ",$group_menu[$value][0]->kpi_desc);
			$result['emp'][$key]['kpi_desc'] = date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));
        }
//        dd($result);
        return $result;
    }
	
	public static function get_list_batch($idRecoHeader) {
        $sql = "SELECT he.id_employee, he.nik_employee, he.name AS emp_name, mpr.description AS position_route,
				mjg.description AS job_grade, htrd.id_conclusion_psychotest, htrd.id_batch, pgp.expired_date, pmb.batch_name,
				CASE 
					WHEN current_date > pgp.expired_date THEN 'Expired'
					WHEN current_date <= pgp.expired_date THEN 'Valid'
					ELSE NULL
				END AS status_expired, mgd.description AS bei_conclusion
				FROM hr_talent_recommendation_detail htrd
				LEFT JOIN hr_employee he
				ON htrd.id_employee = he.id_employee AND htrd.id_company = he.id_company
				LEFT JOIN master_position_detail mpd
				ON htrd.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN web.psycho_group_psychotest pgp
				ON htrd.id_batch = pgp.id_batch AND htrd.id_employee = pgp.id_employee
				LEFT JOIN hr_recruitment_answer_header hrah
				ON htrd.id_employee = hrah.id_employee 
				LEFT JOIN master_general_data mgd
				ON hrah.id_conclusion = mgd.id_general_data
				LEFT JOIN psycho_master_batch pmb
				ON htrd.id_batch = pmb.id_batch
				WHERE htrd.id_talent_recommendation_header = ? AND htrd.id_company = ?";
        $result = DB::select($sql,[$idRecoHeader,session('id_company')]);
        return $result;
    }
	
	public static function get_type_bei() {
        $sql = "SELECT mrqg.id_recruitment_question_group id, mrqg.description text
				FROM master_recruitment_question_group mrqg
				LEFT JOIN master_general_data mgd
				ON mrqg.id_question_group = mgd.id_general_data
				WHERE mrqg.id_company = ? AND mgd.code = 'BEI' AND mrqg.question_type = 'Talent'";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function gen_bei($data) {
		$id_company = $data['id_company'];
		$id_talent_recommendation_header = $data['id_talent_recommendation_header_bei'];
		$int_type = $data['int_type'];
		$int_date = $data['int_date'];
		if($data['id_emp_bei'] != null){
			$id_emp_bei = "array[".$data['id_emp_bei']."]";
		}
		else{
			$id_emp_bei = 'null';
		}
		$result = DB::select("select * from  spgenerateassessmentinterview(?, ?, ?, ?, ".$id_emp_bei.")",
			[$id_company,$id_talent_recommendation_header,$int_type,$int_date]
		);	
		return $result;
	}

	
	public function get_val_psychogram($data) {   
		$id_employee = $data['id_employee'];
		// $id_candidate = $data['id_candidate'];
		$id_batch = $data['id_batch'];
		$sql = "SELECT htrd.id_conclusion_psychotest AS id_potencies, mgd.description AS potencies, md.description as department, mjg.description as job_grade, md.id_dept as id_department, mjg.id_job_grade
				FROM hr_talent_recommendation_detail htrd
				LEFT JOIN master_general_data mgd
				ON htrd.id_conclusion_psychotest = mgd.id_general_data
				LEFT JOIN master_department md
				ON htrd.id_dept_psychogram = md.id_dept
				LEFT JOIN master_job_grade mjg
				ON htrd.id_job_grade_psychogram = mjg.id_job_grade
				WHERE htrd.id_company = ? AND htrd.id_employee = ? AND htrd.id_batch = ?";
        $result = DB::select($sql,[session('id_company'),$id_employee,$id_batch]);
		// $result = DB::select("SELECT mgd.description AS potencies, md.description AS department, mjg.description AS job_grade 
		// 	FROM web.psycho_psychogram_result_report pprr 
		// 	LEFT JOIN master_department md ON pprr.id_department = md.id_dept 
		// 	LEFT JOIN master_job_grade mjg ON pprr.id_job_grade = mjg.id_job_grade 
		// 	LEFT JOIN web.psycho_master_psychogram_matrix pmpm ON
		// 		pprr.id_department = pmpm.id_department 
		// 		AND pprr.id_job_grade = pmpm.id_job_grade 
		// 		AND pprr.result_value BETWEEN pmpm.value_min AND pmpm.value_max 
		// 	LEFT JOIN master_general_data mgd ON pmpm.id_conclusion = mgd.id_general_data 
		// 	WHERE (id_employee = ? OR id_candidate = ?) 
		// 		AND id_batch = ?
		// 		AND pprr.id_company = ?", [$id_employee, $id_candidate, $id_batch, session('id_company')]);
		if(count($result) > 0){
			return $result;
		}
		else{
			return [];
		}        
    }

}
