<?php

namespace App\Models\Recruitment\Psychotest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Psychotest extends Model
{
	use HasFactory;
	
	public function get_disc_can($startdate,$enddate,$group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT hc.name, hc.identification_number, pdrr.*, pdrr.creation_date::date AS created_date, pmb.batch_code, 
				pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch 
				FROM web.psycho_disc_result_report pdrr
				LEFT JOIN public.psycho_master_batch pmb
				ON pdrr.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN web.hr_candidate hc
				ON pdrr.id_candidate = hc.id_candidate
				WHERE ".$branch." pmb.id_company = ".session('id_company')." AND pdrr.id_candidate IS NOT NULL
				AND pdrr.creation_date::date BETWEEN '" .$startdate. "' AND '" .$enddate. "'
				ORDER BY pdrr.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_disc_emp($startdate,$enddate,$group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT he.name, he.nik_employee, mpr.description AS position, mb.description AS branch,
				pdrr.*, pdrr.creation_date::date AS created_date, pmb.batch_code, 
				pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch 
				FROM web.psycho_disc_result_report pdrr
				LEFT JOIN public.psycho_master_batch pmb
				ON pdrr.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN public.hr_employee he
				ON pdrr.id_employee = he.id_employee
				LEFT JOIN public.master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN public.master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch 
				WHERE ".$branch." pmb.id_company = ".session('id_company')." AND pdrr.id_employee IS NOT NULL
				AND pdrr.creation_date::date BETWEEN '" .$startdate. "' AND '" .$enddate. "'
				ORDER BY pdrr.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_papi_can($startdate,$enddate,$group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT hc.name, hc.identification_number, papi_result.*, pmb.batch_code, pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch
				FROM (SELECT pprr.id_candidate, pprr.id_batch, pprr.id_company, pprr.creation_date::date 
						FROM web.psycho_papicostick_result_report pprr
						WHERE pprr.id_candidate  IS NOT NULL
						GROUP BY pprr.id_candidate, pprr.id_batch, pprr.id_company, pprr.creation_date::date  
				) AS papi_result
				LEFT JOIN public.psycho_master_batch pmb
				ON papi_result.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN web.hr_candidate hc
				ON papi_result.id_candidate = hc.id_candidate
				WHERE ".$branch." pmb.id_company = ".session('id_company')." AND papi_result.creation_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'
				ORDER BY papi_result.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_papi_emp($startdate,$enddate,$group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT he.name, he.nik_employee, mpr.description AS position, mb.description AS branch,
				papi_result.*, pmb.batch_code, pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch
				FROM (
				SELECT pprr.id_employee, pprr.id_batch, pprr.id_company, pprr.creation_date::date 
						FROM web.psycho_papicostick_result_report pprr
						WHERE pprr.id_employee  IS NOT NULL
						GROUP BY pprr.id_employee, pprr.id_batch, pprr.id_company, pprr.creation_date::date  		
				) AS papi_result
				LEFT JOIN public.psycho_master_batch pmb
				ON papi_result.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN public.hr_employee he
				ON papi_result.id_employee = he.id_employee
				LEFT JOIN public.master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN public.master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch 
				WHERE ".$branch." pmb.id_company = ".session('id_company')." AND papi_result.creation_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'
				ORDER BY papi_result.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_kraepelin_can($startdate,$enddate,$group_branch, $idBatch=null) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}

		$whereThis = '';
		if(!is_null($startdate) && !is_null($enddate)){
			$whereThis .= " AND kraepelin_result.creation_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'";
		}
		if($idBatch){
			$whereThis .= " AND kraepelin_result.id_batch = '".$idBatch."' ";
		}

		$sql = "SELECT hc.name, hc.identification_number, kraepelin_result.*, pmb.batch_code, pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch
				FROM (
				SELECT pkau.id_candidate, pkau.id_batch, pkau.id_company, pkau.creation_date::date 
						FROM web.psycho_kraepelin_answer_user pkau	
						WHERE pkau.id_candidate  IS NOT NULL
						GROUP BY pkau.id_candidate, pkau.id_batch, pkau.id_company, pkau.creation_date::date  		
				) AS kraepelin_result
				LEFT JOIN public.psycho_master_batch pmb
				ON kraepelin_result.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN web.hr_candidate hc
				ON kraepelin_result.id_candidate = hc.id_candidate
				WHERE ".$branch." pmb.id_company = ".session('id_company')." ".$whereThis."
				ORDER BY kraepelin_result.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_kraepelin_emp($startdate,$enddate,$group_branch, $idBatch=null) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}

		$whereThis = '';
		if(!is_null($startdate) && !is_null($enddate)){
			$whereThis .= " AND kraepelin_result.creation_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'";
		}
		if($idBatch){
			$whereThis .= " AND kraepelin_result.id_batch = '".$idBatch."' ";
		}

		$sql = "SELECT he.name, he.nik_employee, mpr.description AS position, mb.description AS branch,
				kraepelin_result.*, pmb.batch_code, pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch
				FROM (
				SELECT pkau.id_employee, pkau.id_batch, pkau.id_company, pkau.creation_date::date 
						FROM web.psycho_kraepelin_answer_user pkau
						WHERE pkau.id_employee  IS NOT NULL
						GROUP BY pkau.id_employee, pkau.id_batch, pkau.id_company, pkau.creation_date::date  		
				) AS kraepelin_result
				LEFT JOIN public.psycho_master_batch pmb
				ON kraepelin_result.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN public.hr_employee he
				ON kraepelin_result.id_employee = he.id_employee
				LEFT JOIN public.master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN public.master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch 
				WHERE ".$branch." pmb.id_company = ".session('id_company')." ".$whereThis."
				ORDER BY kraepelin_result.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_bct_can($startdate,$enddate,$group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT hc.name, hc.identification_number, bct_result.*, pmb.batch_code, pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch
				FROM (
				SELECT min(pbau.creation_date::date) AS creation_date, pbau.id_candidate, pbau.id_batch, pbau.id_company
						FROM web.psycho_bct_answer_user pbau
						WHERE pbau.id_candidate  IS NOT NULL
						GROUP BY pbau.id_candidate, pbau.id_batch, pbau.id_company	
				) AS bct_result
				LEFT JOIN public.psycho_master_batch pmb
				ON bct_result.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN web.hr_candidate hc
				ON bct_result.id_candidate = hc.id_candidate
				WHERE ".$branch." pmb.id_company = ".session('id_company')." AND bct_result.creation_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'
				ORDER BY bct_result.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_bct_emp($startdate,$enddate,$group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT he.name, he.nik_employee, mpr.description AS position, mb.description AS branch,
				bct_result.*, pmb.batch_code, pmb.batch_name, pmb.location, pmb.id_region, pmb.id_branch
				FROM (
				SELECT min(pbau.creation_date::date) AS creation_date, pbau.id_employee, pbau.id_batch, pbau.id_company
						FROM web.psycho_bct_answer_user pbau
						WHERE pbau.id_employee  IS NOT NULL
						GROUP BY pbau.id_employee, pbau.id_batch, pbau.id_company	
				) AS bct_result
				LEFT JOIN public.psycho_master_batch pmb
				ON bct_result.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN public.hr_employee he
				ON bct_result.id_employee = he.id_employee
				LEFT JOIN public.master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN public.master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch 
				WHERE ".$branch." pmb.id_company = ".session('id_company')." AND bct_result.creation_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'
				ORDER BY bct_result.creation_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_psychogram_can($startdate,$enddate,$group_branch, $idBatch=null, $idPosition=null, $idJobGrade = null) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		
		$whereThis = '';
		if(!is_null($startdate) && !is_null($enddate)){
			$whereThis .= " AND summary.test_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'";
		}

		if($idBatch){
			$whereThis .= " AND summary.id_batch_ = '".$idBatch."' ";
		} 

		if($idPosition && count($idPosition) > 0) {
			$whereThis .= " AND summary.id_position_routing_request IN (".implode(',', $idPosition).")";
		}

		if($idJobGrade) {
			if(is_array($idJobGrade)) {
				$whereThis .= " AND summary.id_grade IN (".implode(',', $idJobGrade).")";
			} else {
				$whereThis .= " AND summary.id_grade = $idJobGrade ";
			}
		}

		$sql = "SELECT 
				CASE
					WHEN status_disc = 1 AND status_papi = 1 AND status_kraepelin = 1 
						AND status_bct = 1 THEN 1
					ELSE 0
				END AS summary_test,
				summary.* FROM (					
				SELECT 
				CASE
					WHEN hc.id_candidate = pdrr.id_candidate THEN 1
					ELSE 0
				END AS status_disc,
				CASE
					WHEN hc.id_candidate = j_papi.id_candidate THEN 1
					ELSE 0
				END AS status_papi,
				CASE
					WHEN hc.id_candidate = j_kraepelin.id_candidate THEN 1
					ELSE 0
				END AS status_kraepelin,
				CASE
					WHEN hc.id_candidate = j_bct.id_candidate THEN 1
					ELSE 0
				END AS status_bct,
				hc.name, hc.identification_number, pgp.*, pmb.batch_name, pmb.location, pmb.id_batch as id_batch_, mjp.id_dept, mpr.id_job_grade AS id_grade, mjg2.id_job_grade AS selected_position_grade, hhrh.id_position_routing_request, md.description as dept_description, mjg.description as job_grade_description, md.id_dept as id_dept_generated, mjg.id_job_grade as id_job_grade_generated 
				FROM web.psycho_group_psychotest pgp
				LEFT JOIN web.hr_applied_candidate hac
				ON pgp.id_applied_candidate = hac.id_applied_candidate
				LEFT JOIN public.hr_hiring_request_header hhrh
				ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.master_job_grade mjg2
				ON mpr.id_job_grade = mjg2.id_job_grade
				LEFT JOIN web.psycho_psychogram_result_report pprr
				ON pgp.id_candidate = pprr.id_candidate
				LEFT JOIN public.master_job_grade mjg
				ON pprr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_department md
				ON pprr.id_department = md.id_dept
				LEFT JOIN public.master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN web.hr_candidate hc
				ON pgp.id_candidate = hc.id_candidate
				JOIN public.psycho_master_batch pmb
				ON pgp.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN web.psycho_disc_result_report pdrr
				ON pgp.id_candidate = pdrr.id_candidate AND pgp.id_batch = pdrr.id_batch
				LEFT JOIN (
					SELECT pprr.id_candidate, pprr.id_batch 
					FROM web.psycho_papicostick_result_report pprr
					GROUP BY pprr.id_candidate, pprr.id_batch  
				) AS j_papi
				ON pgp.id_candidate = j_papi.id_candidate AND pgp.id_batch = j_papi.id_batch
				LEFT JOIN (
					SELECT pkau.id_candidate, pkau.id_batch 
					FROM web.psycho_kraepelin_answer_user pkau
					GROUP BY pkau.id_candidate, pkau.id_batch  
				) AS j_kraepelin
				ON pgp.id_candidate = j_kraepelin.id_candidate AND pgp.id_batch = j_kraepelin.id_batch
				LEFT JOIN (
					SELECT pbau.id_candidate, pbau.id_batch 
					FROM web.psycho_bct_answer_user pbau
					GROUP BY pbau.id_candidate, pbau.id_batch  
				) AS j_bct
				ON pgp.id_candidate = j_bct.id_candidate AND pgp.id_batch = j_bct.id_batch
				WHERE ".$branch." pgp.id_candidate IS NOT NULL AND pgp.id_company = ".session('id_company')."
			) AS summary
			WHERE 1=1 ".$whereThis."
			ORDER BY summary.test_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_psychogram_emp($startdate,$enddate,$group_branch, $idBatch=null, $idPosition=null, $idJobGrade=null, $idDept = null) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}

		$whereThis = '';
		if(!is_null($startdate) && !is_null($enddate)){
			$whereThis .= " AND summary.test_date BETWEEN '" .$startdate. "' AND '" .$enddate. "'";
		}

		if($idBatch){
			$whereThis .= " AND summary.id_batch_ = '".$idBatch."' ";
		}
		if($idPosition && count($idPosition) > 0) {
			$whereThis .= " AND summary.id_routing IN (".implode(",", $idPosition).")";
		}

		if($idJobGrade) {
			if(is_array($idJobGrade)) {
				$whereThis .= " AND summary.id_grade IN (".implode(',', $idJobGrade).")";
			} else {
				$whereThis .= " AND summary.id_grade = $idJobGrade ";
			}
		}

		if($idDept) {
			if(is_array($idDept)) {
				$whereThis .= " AND summary.employee_id_dept IN (".implode(',', $idDept).")";
			} else {
				$whereThis .= " AND summary.employee_id_dept = $idDept ";
			}
		}

		$sql = "SELECT
				CASE
					WHEN status_disc = 1 AND status_papi = 1 AND status_kraepelin = 1 
						AND status_bct = 1 THEN 1
					ELSE 0
				END AS summary_test,
				summary.* FROM (					
				SELECT 
				CASE
					WHEN he.id_employee = pdrr.id_employee THEN 1
					ELSE 0
				END AS status_disc,
				CASE
					WHEN he.id_employee = j_papi.id_employee THEN 1
					ELSE 0
				END AS status_papi,
				CASE
					WHEN he.id_employee = j_kraepelin.id_employee THEN 1
					ELSE 0
				END AS status_kraepelin,
				CASE
					WHEN he.id_employee = j_bct.id_employee THEN 1
					ELSE 0
				END AS status_bct,
				he.name, he.nik_employee, mpr.description AS position, mb.description AS branch, emp_md.id_dept AS employee_id_dept,
				pgp.*, pmb.batch_name, pmb.location, pmb.id_batch as id_batch_, mjp.id_dept, mpr.id_job_grade AS id_grade, mjg2.description AS current_grade_desc, mpr.id_routing, md.description as dept_description, mjg.description as job_grade_description,
				pprr.id_psychogram_result_report 
				FROM web.psycho_group_psychotest pgp
				LEFT JOIN hr_employee he2
				ON pgp.id_employee = he2.id_employee
				LEFT JOIN hr_employee he3
				ON he2.nik_employee = he3.nik_employee
				AND he3.status = 'A'
				LEFT JOIN public.master_position_detail mpd
				ON (he3.id_employee = mpd.id_employee OR he3.id_employee = mpd.id_employee2) 
				AND mpd.secondary_position = false
				LEFT JOIN public.master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN public.master_job_position emp_mjp
				ON mpr.id_position = emp_mjp.id_position
				LEFT JOIN master_department emp_md
				ON emp_mjp.id_dept = emp_md.id_dept
				LEFT JOIN web.psycho_psychogram_result_report pprr
				ON pgp.id_employee = pprr.id_employee
				AND pgp.id_batch = pprr.id_batch
				LEFT JOIN public.master_job_grade mjg
				ON pprr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_department md
				ON pprr.id_department = md.id_dept
				LEFT JOIN public.master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch 
				LEFT JOIN public.hr_employee he
				ON pgp.id_employee = he.id_employee
				LEFT JOIN public.master_job_grade mjg2
				ON mpr.id_job_grade = mjg2.id_job_grade
				JOIN public.psycho_master_batch pmb
				ON pgp.id_batch = pmb.id_batch AND pmb.status = 'A'
				LEFT JOIN web.psycho_disc_result_report pdrr
				ON pgp.id_employee = pdrr.id_employee AND pgp.id_batch = pdrr.id_batch
				LEFT JOIN (
					SELECT pprr.id_employee, pprr.id_batch 
					FROM web.psycho_papicostick_result_report pprr
					GROUP BY pprr.id_employee, pprr.id_batch  
				) AS j_papi
				ON pgp.id_employee = j_papi.id_employee AND pgp.id_batch = j_papi.id_batch
				LEFT JOIN (
					SELECT pkau.id_employee, pkau.id_batch 
					FROM web.psycho_kraepelin_answer_user pkau
					GROUP BY pkau.id_employee, pkau.id_batch  
				) AS j_kraepelin
				ON pgp.id_employee = j_kraepelin.id_employee AND pgp.id_batch = j_kraepelin.id_batch
				LEFT JOIN (
					SELECT pbau.id_employee, pbau.id_batch 
					FROM web.psycho_bct_answer_user pbau
					GROUP BY pbau.id_employee, pbau.id_batch  
				) AS j_bct
				ON pgp.id_employee = j_bct.id_employee AND pgp.id_batch = j_bct.id_batch
				WHERE ".$branch." pgp.id_employee IS NOT NULL AND pgp.id_company = ".session('id_company')."
			) AS summary
			WHERE 1=1 ".$whereThis."
			ORDER BY summary.test_date DESC";
        $result = DB::select($sql);		 
        return $result;
    }

    public static function get_grade($idCompany=null) {
    	$idCompany = $idCompany ?? 1;
        $result = DB::table('master_job_grade as mjg')
    		->where('id_company', $idCompany)
    		->where('status', 'A')
    		->where(function ($where){
	            $where->where('job_level', '!=', 1)
    				->orWherenull('job_level');
	        })
            ->orderBy('job_class_group')
            ->orderBy('job_level')
            ->get();
        return $result;
    }

    public static function get_department($idCompany=null) {
    	$idCompany = $idCompany ?? 1;
        $result = DB::table('master_department')
        	->select('id_dept','department_code','description','status','id_company')
    		->where('id_company', $idCompany)
    		->where('status', 'A')
            ->where('department_code', 'not like', '%_DIR%')
            ->orderBy('description')
            ->get();
        return $result;
    }

    public static function get_batch($idCompany=null) {
    	$idCompany = $idCompany ?? 1;
        $result = DB::table('psycho_master_batch')
        	->select('id_batch','batch_name','location','status','id_company')
    		->where('id_company', $idCompany)
    		->where('status', 'A')
            ->orderBy('batch_name')
            ->get();
        return $result;
    }

	public static function get_position($idCompany=null) {
    	$idCompany = $idCompany ?? 1;
        $result = DB::table('master_position_routing')
			->leftJoin('master_job_position', 'master_position_routing.id_position', 'master_job_position.id_position')
    		->where('master_position_routing.id_company', $idCompany)
    		->where('master_position_routing.status', 'A')
            ->orderBy('master_position_routing.description')
			->selectRaw("id_routing as id, concat(master_position_routing.description, ' (', master_job_position.description, ')') as text")
            ->get();
        return $result;
    }

    public static function get_kraepelin_result($idBatch=null, $type='candidate', $idUserAssessment=null) {
    	$idCompany = $idCompany ?? 1;
        $get = DB::table('web.psycho_kraepelin_answer_user')
        	->select('id_batch','column_number','id_employee','id_candidate','id_company')
            ->orderByDesc('id_kreapelin_answer_user');

        if($type=='candidate'){
        	$get->whereNull('id_employee');
        	if($idUserAssessment){
        		$get->where('id_candidate', $idUserAssessment);
        	}
        } else {
        	$get->whereNull('id_candidate');
        	if($idUserAssessment){
        		$get->where('id_employee', $idUserAssessment);
        	}
        }
        $result = $get->get();
        return $result;
    }

    public static function reset_kraepelin_result($idBatch=null, $type='candidate', $idUserAssessment=null) {
        $result = false;
        $reset = DB::table('web.psycho_kraepelin_answer_user')
        	->where('id_batch', $idBatch);

        if($idUserAssessment){
    		if($type=='candidate'){
        		$reset->where('id_candidate', $idUserAssessment);
    		} else {
        		$reset->where('id_employee', $idUserAssessment);
    		}
        	$result = $reset->delete();
    	}

        return $result;
    }
}	
