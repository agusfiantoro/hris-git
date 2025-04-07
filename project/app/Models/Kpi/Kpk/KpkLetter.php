<?php

namespace App\Models\Kpi\Kpk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Employee\Employee\Employee;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;

class KpkLetter extends Model
{
	use HasFactory;
	
    protected $table = 'hr_electronic_letter';
	protected $primaryKey = 'id_letter';
	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'updated_date';
	
	public static function id_letter_type_save()
	{
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.description as description'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.code','PIP')
		->where('master_general_data.id_company',session('id_company'))
		->first();
		return $id_letter_type;
	}
	public static function format_save($request)
	{
		$id_letter_type = self::id_letter_type_save();
		$code_letter = DB::table('master_general_data')->where('id_company', session('id_company'))->where('code', 'P2K')->first();
		$company = DB::table('master_company')->where('id_company',session('id_company'))->first();
		$position = DB::table('master_position_detail')->where('id_employee',$request->id_employee_request)->where('secondary_position',false)->first();
		$branch = DB::table('master_branch')->where('id_branch',$position->id_branch)->first();
		$bulan = date('m', strtotime($request->date));
		$tahun = date('Y', strtotime($request->date));
		$id_letter = self::join('master_company','hr_electronic_letter.id_company','=','master_company.id_company')
		->whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_company.company_code',$company->company_code)
		->where('hr_electronic_letter.id_region',$branch->id_region)
		->where('hr_electronic_letter.id_category',$code_letter->id_general_data)
		->max('hr_electronic_letter.reference_number');
		$no_urut = substr($id_letter, 0,4);
		$no_urut++;
		$kode = sprintf("%04s", abs($no_urut));
		$region = MasterRegional::where('id_region',$branch->id_region)->first();
		if ($region->description == "Pusat") {
			$code_region = "HQ";
		}else{
			$code_region = $region->region_code;
		}

		$format = $kode."/".$company->company_code."-".$code_letter->code."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
		return ['id_letter_type'=>$id_letter_type,'format'=>$format];
	}
	
	public static function getdata() {
		$sql = "SELECT hel.id_letter, hel.reference_number, hel.date AS letter_date, mgd.description AS type, 
				he.name AS chief_name, mpr.description AS pos_routing, hel.remark_1 AS month_kpk, 
				hel.remark_2 AS on_perform, hel.remark_3 AS dept, hel.remark_4 AS day_text, 
				CASE
					WHEN hel.remark_6 = 'corporate' THEN 'Organik'
					ELSE 'OS'
				END AS com_type,
				CASE
					WHEN hel.remark_5 = 'draft' THEN 'Draft'
					ELSE 'Submit'
				END AS draft_submit,
				md.description AS dept_name, hel.status, md.department_code AS dept_code
				FROM hr_electronic_letter hel
				JOIN hr_employee he
				ON hel.id_employee_chief = he.id_employee
				LEFT JOIN master_position_routing mpr
				ON hel.id_position_routing_chief = mpr.id_routing
				JOIN master_general_data mgd
				ON hel.id_category = mgd.id_general_data AND mgd.id_company = ".session('id_company')."
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_letter_category'
				JOIN master_department md
				ON hel.remark_3::integer = md.id_dept 
				WHERE hel.id_company = ".session('id_company')." AND mgd.code = 'P2K' AND he.id_user = ".session('id_user')."
				ORDER BY hel.id_letter DESC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_dept_search() {
		$sql = "SELECT md.department_code id, md.description text
				FROM master_department md
				WHERE md.id_company = ".session('id_company')." 
				AND md.department_code IN('130_FA','140D_LOGD','140W_LOGW','150_HR','170_SAL')
				ORDER BY md.description ASC";	
        $result = DB::select($sql);
        return $result;
	}
	
	public static function get_employee_search($group_branch, $code_dept = null, $status = 'A') {
		$branch = " ";
		if($group_branch || $group_branch != ""){
			$branch = " AND id_branch IN(".$group_branch.")";
		}
		$dept = "";
		if($code_dept) {
			$dept = " AND code_dept = '".$code_dept."'";
		}
		$sql = "SELECT * FROM (
				SELECT sfer.id_employee id, CONCAT(sfer.name,' (',sfer.nik_employee,')') text, sfer.id_branch,
				md.department_code As code_dept, sfer.status_active
				FROM public.sp_funct_get_employee_report(null) sfer
				JOIN master_position_detail mpd
				ON sfer.id_position_detail = mpd.id_position_detail
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				JOIN master_department md
				ON mjp.id_dept = md.id_dept
				ORDER BY sfer.name ASC
			) AS fix
			WHERE status_active='".$status."' ".$branch." ".$dept;
	
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }

	
	public static function getdata_monitoring($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = "AND mpd.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT hpe.id_performance_evaluation, hel.reference_number, hel.effective_date, he.id_employee, 
					he.nik_employee, he.name, CONCAT(mtg.group_name,' (',mtg.subgroup_name,')') AS group_name, 
					mtg.minimum_score_kpi_level as th, hel.remark_1 AS month_ba, hpe.evaluation_date, hpe.kpi_average, 
					hpe.sales_offtake, he2.name AS created_name, hpe.evaluation_status,
					CASE
						WHEN hpe.status = 'O' THEN 'Need Review'
						ELSE 'Done Review'
					END AS status, 
					CASE
						WHEN mpd2.id_employee IS NOT NULL THEN he3.id_user
						WHEN mpd2.id_employee IS NULL THEN he4.id_user
						WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN he5.id_user
					END AS id_direct,
					CASE
						WHEN mpd2.id_employee IS NOT NULL THEN he4.id_user
						WHEN mpd2.id_employee IS NULL THEN he5.id_user
						WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN he5.id_user
					END AS id_indirect, j_all.treatment,
					CASE
						WHEN j_all.review_date IS NULL THEN '-'
						ELSE TO_CHAR(j_all.review_date:: DATE, 'dd Mon yyyy')
					END AS review_date,
					CASE
						WHEN j_all.month_eval IS NULL THEN 'Not Reviewed'
						ELSE CONCAT('Reviewed Month ',j_all.month_eval)
					END AS month_eval, mpd.id_position_detail
					FROM hr_performance_evaluation hpe
					LEFT JOIN hr_electronic_letter hel
					ON hpe.id_letter = hel.id_letter
					LEFT JOIN hr_employee he
					ON hpe.id_employee = he.id_employee
					LEFT JOIN master_threshold_group mtg
					ON hpe.id_threshold_group = mtg.id_threshold_group
					LEFT JOIN hr_employee he2
					ON hel.created_by = he2.id_user
					LEFT JOIN master_position_detail mpd
					ON hpe.id_employee = mpd.id_employee
					LEFT JOIN master_position_detail mpd2
					ON mpd.parent_id_position_detail = mpd2.id_position_detail
					LEFT JOIN hr_employee he3
					ON mpd2.id_employee = he3.id_employee
					LEFT JOIN master_position_detail mpd3
					ON mpd2.parent_id_position_detail = mpd3.id_position_detail
					LEFT JOIN hr_employee he4
					ON mpd3.id_employee = he4.id_employee
					LEFT JOIN master_position_detail mpd4
					ON mpd3.parent_id_position_detail = mpd4.id_position_detail
					LEFT JOIN hr_employee he5
					ON mpd4.id_employee = he5.id_employee
					LEFT JOIN (
						SELECT hpr.id_performance_review, hpr.id_performance_evaluation, 
						hpr.treatment, hpr.review_date, j_fix.month_eval 
						FROM hr_performance_review hpr
						JOIN (				
							SELECT j_review.id_performance_evaluation, max(j_review.id_performance_review) AS id_performance_review, 
							count(j_review.id_performance_evaluation) AS month_eval
							FROM (
								SELECT hpr.id_performance_evaluation, hpr.id_performance_review
								FROM hr_performance_review hpr
								WHERE hpr.review_date IS NOT NULL
								GROUP BY hpr.id_performance_evaluation, hpr.id_performance_review	
							) AS j_review
							GROUP BY j_review.id_performance_evaluation
						) AS j_fix
						ON hpr.id_performance_review = j_fix.id_performance_review 
						AND hpr.id_performance_evaluation = j_fix.id_performance_evaluation
					) AS j_all
					ON (hpe.id_performance_evaluation = j_all.id_performance_evaluation)
					JOIN (
						SELECT DISTINCT hpr.id_performance_evaluation
						FROM hr_performance_review hpr
						WHERE hpr.id_company = ".session('id_company')."
					) AS group_all
					ON hpe.id_performance_evaluation = group_all.id_performance_evaluation
					WHERE hpe.id_company = ".session('id_company')." AND hel.remark_5 = 'submit' AND hel.status = 'A'
					".$branch."
					ORDER BY hpe.id_performance_evaluation DESC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getdata_review() {
		$sql = "SELECT mix.* FROM (
					SELECT hpe.id_performance_evaluation, hel.reference_number, hel.effective_date, he.id_employee, 
					he.nik_employee, he.name, CONCAT(mtg.group_name,' (',mtg.subgroup_name,')') AS group_name, 
					mtg.minimum_score_kpi_level as th, hel.remark_1 AS month_ba, hpe.evaluation_date, hpe.kpi_average, 
					hpe.sales_offtake, he2.name AS created_name, hpe.evaluation_status,
					CASE
						WHEN hpe.status = 'O' THEN 'Need Review'
						ELSE 'Done Review'
					END AS status, 
					CASE
						WHEN mpd2.id_employee IS NOT NULL THEN he3.id_user
						WHEN mpd2.id_employee IS NULL THEN he4.id_user
						WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN he5.id_user
					END AS id_direct,
					CASE
						WHEN mpd2.id_employee IS NOT NULL THEN he4.id_user
						WHEN mpd2.id_employee IS NULL THEN he5.id_user
						WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN he5.id_user
					END AS id_indirect, j_all.treatment,
					CASE
						WHEN j_all.review_date IS NULL THEN '-'
						ELSE TO_CHAR(j_all.review_date:: DATE, 'dd Mon yyyy')
					END AS review_date,
					CASE
						WHEN j_all.month_eval IS NULL THEN 'Not Reviewed'
						ELSE CONCAT('Reviewed Month ',j_all.month_eval)
					END AS month_eval, mpd.id_position_detail
					FROM hr_performance_evaluation hpe
					LEFT JOIN hr_electronic_letter hel
					ON hpe.id_letter = hel.id_letter
					LEFT JOIN hr_employee he
					ON hpe.id_employee = he.id_employee
					LEFT JOIN master_threshold_group mtg
					ON hpe.id_threshold_group = mtg.id_threshold_group
					LEFT JOIN hr_employee he2
					ON hel.created_by = he2.id_user
					LEFT JOIN master_position_detail mpd
					ON hpe.id_employee = mpd.id_employee
					LEFT JOIN master_position_detail mpd2
					ON mpd.parent_id_position_detail = mpd2.id_position_detail
					LEFT JOIN hr_employee he3
					ON mpd2.id_employee = he3.id_employee
					LEFT JOIN master_position_detail mpd3
					ON mpd2.parent_id_position_detail = mpd3.id_position_detail
					LEFT JOIN hr_employee he4
					ON mpd3.id_employee = he4.id_employee
					LEFT JOIN master_position_detail mpd4
					ON mpd3.parent_id_position_detail = mpd4.id_position_detail
					LEFT JOIN hr_employee he5
					ON mpd4.id_employee = he5.id_employee
					LEFT JOIN (
						SELECT hpr.id_performance_review, hpr.id_performance_evaluation, 
						hpr.treatment, hpr.review_date, j_fix.month_eval 
						FROM hr_performance_review hpr
						JOIN (				
							SELECT j_review.id_performance_evaluation, max(j_review.id_performance_review) AS id_performance_review, 
							count(j_review.id_performance_evaluation) AS month_eval
							FROM (
								SELECT hpr.id_performance_evaluation, hpr.id_performance_review
								FROM hr_performance_review hpr
								WHERE hpr.review_date IS NOT NULL
								GROUP BY hpr.id_performance_evaluation, hpr.id_performance_review	
							) AS j_review
							GROUP BY j_review.id_performance_evaluation
						) AS j_fix
						ON hpr.id_performance_review = j_fix.id_performance_review 
						AND hpr.id_performance_evaluation = j_fix.id_performance_evaluation
					) AS j_all
					ON (hpe.id_performance_evaluation = j_all.id_performance_evaluation)
					JOIN (
						SELECT DISTINCT hpr.id_performance_evaluation
						FROM hr_performance_review hpr
						WHERE hpr.id_company = ".session('id_company')."
					) AS group_all
					ON hpe.id_performance_evaluation = group_all.id_performance_evaluation
					WHERE hpe.id_company = ".session('id_company')." AND hel.remark_5 = 'submit' AND hel.status = 'A'
					ORDER BY hpe.id_performance_evaluation DESC							
				) AS mix
				WHERE mix.id_direct = ? OR mix.id_indirect = ?";	
        $result = DB::select($sql,[session('id_user'),session('id_user')]);
        return $result;
    }
	
	public static function get_employee_by() {
        $sql = "SELECT he.id_employee id, he.name text, mpr.id_routing, mpd.id_position_detail, mpd.id_location, mpr.description AS route_name, 
				mjg.job_class_group, mb.id_branch, mb.id_region, mjp.id_dept
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				WHERE mpd.id_company = ? AND he.id_user = ? AND he.status = 'A'";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
        return $result;
    }
	
	public static function get_dept($idCom) {
		$sql = "SELECT DISTINCT md.id_dept id, md.description text, md.department_code
				FROM master_position_detail mpd
				LEFT JOIN master_company mc
				ON (mpd.assigned_to_company = mc.id_company OR mpd.id_company = mc.id_company) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				WHERE mpd.id_company = ".session('id_company')." AND mpd.id_employee IS NOT NULL AND mc.company_type = '".$idCom."' 
				AND md.department_code IN('130_FA','140D_LOGD','140W_LOGW','150_HR','170_SAL')
				ORDER BY md.description ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_division() {
		$sql = "SELECT mdiv.id_division id, mdiv.description text
				FROM master_division mdiv
				WHERE mdiv.performance_evaluation_flag = true AND mdiv.id_company = ".session('id_company');	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_participant($idComType,$idDept,$month,$year,$new_edit) {
		$groupDept = 'NULL';
	//	$month = 5;
	//	$year = 2025;
		if($idDept[0] != null){
			$logDept = collect(DB::select("SELECT * FROM master_department WHERE department_code IN ('140D_LOGD', '140W_LOGW') AND id_company = ?", [session('id_company')]))->pluck('id_dept')->toArray();
			if(array_intersect($idDept, $logDept)) {
				$groupDept = implode(",",array_merge($idDept, $logDept));
			} else {
				$groupDept = implode(",",$idDept);
			}
		}
		if($new_edit == 'view'){
			$sql = "SELECT he.id_employee id, CONCAT(he.name,' (',he.nik_employee,')') AS text
					FROM hr_employee he";
		}
		else{
			if($new_edit == 'new'){
				$month_year = " AND (emp_period.plus_month IS NULL OR ".$month." >= emp_period.plus_month OR ".$year." > emp_period.period_year)";
			}
			else{
				$month_year = "";
			}
			$sql = "SELECT he.id_employee id, CONCAT(he.name,' (',he.nik_employee,')') AS text, 
					mpr.description AS position_name, mr.description AS region, mb.description AS branch,
					CASE
						WHEN he2.name IS NOT NULL THEN he2.name
						WHEN he2.name IS NULL AND he3.name IS NOT NULL THEN he3.name
					END AS name_supervisor, he.nik_employee, mpd.id_position_detail
					FROM hr_employee he
					JOIN master_company mc
					ON he.id_company = mc.id_company
					JOIN master_position_detail mpd
					ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
					JOIN master_position_routing mpr
					ON mpd.id_position_routing = mpr.id_routing
					JOIN master_job_position mjp
					ON mpr.id_position = mjp.id_position
					JOIN master_department md
					ON mjp.id_dept = md.id_dept
					JOIN master_branch mb
					ON mpd.id_branch = mb.id_branch
					JOIN master_region mr
					ON mb.id_region = mr.id_region
					LEFT JOIN master_position_detail mpd2
					ON mpd.parent_id_position_detail = mpd2.id_position_detail
					LEFT JOIN hr_employee he2
					ON mpd2.id_employee = he2.id_employee
					LEFT JOIN master_position_detail mpd3
					ON mpd2.parent_id_position_detail = mpd3.id_position_detail
					LEFT JOIN hr_employee he3
					ON mpd3.id_employee = he3.id_employee
					LEFT JOIN (
						SELECT hpe.id_employee, hpr.period_date, 
						date_part('month', (SELECT hpr.period_date + '3 month'::interval)) AS plus_month,
						date_part('year', (SELECT hpr.period_date)) AS period_year
						FROM hr_performance_review hpr
						LEFT JOIN hr_performance_evaluation hpe
						ON hpr.id_performance_evaluation = hpe.id_performance_evaluation
						WHERE hpr.decision IS NOT NULL AND hpr.decision = 'HIT'
					) AS emp_period
					ON he.id_employee = emp_period.id_employee
					WHERE mpd.id_company = ".session('id_company')." AND md.id_dept IN(".$groupDept.") AND he.status = 'A'
					AND mc.company_type = '".$idComType."' ".$month_year." 			
					ORDER BY he.name ASC";	
		}
        $result = DB::select($sql);
        return $result;
    }

	public static function get_pos_detail($idPosDetail) {
		return DB::select("SELECT mpr.description AS position_name, mr.description AS region, mb.description AS branch,
				CASE
					WHEN he2.name IS NOT NULL THEN he2.name
					WHEN he2.name IS NULL AND he3.name IS NOT NULL THEN he3.name
				END AS name_supervisor, mpd.id_position_detail 
				FROM master_position_detail mpd
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				JOIN master_department md
				ON mjp.id_dept = md.id_dept
				JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				JOIN master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail = mpd2.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd2.id_employee = he2.id_employee
				LEFT JOIN master_position_detail mpd3
				ON mpd2.parent_id_position_detail = mpd3.id_position_detail
				LEFT JOIN hr_employee he3
				ON mpd3.id_employee = he3.id_employee
				WHERE mpd.id_position_detail = ? ", 
				[$idPosDetail]);
	}
	
	public static function get_threshold_group($idEmployee,$idPos,$edit=null) {
		if($edit == 'view'){
			return DB::select("SELECT mtg.id_threshold_group as id,
								mtg.subgroup_name as text, NULL as pos_routing
								FROM master_threshold_group mtg");
		}
		else{
			return DB::select("SELECT * FROM(
							SELECT mtg.id_threshold_group as id,
								mtg.subgroup_name as text,
								mtg.minimum_score_kpi_level, md.department_code AS dept_code, pos_routing	
							FROM hr_employee he 
							JOIN master_position_detail mpd ON he.id_employee = mpd.id_employee 
							JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing 
							JOIN master_job_position mjp ON mpr.id_position = mjp.id_position
							JOIN master_branch mb ON mpd.id_branch = mb.id_branch
							JOIN master_department md
							ON mjp.id_dept = md.id_dept
							LEFT JOIN master_threshold_group mtg 
								ON mb.id_region = mtg.id_region 
								AND mpr.id_job_grade = mtg.id_job_grade 
								AND mjp.id_dept = mtg.id_department 
							LEFT JOIN LATERAL unnest(mtg.id_position_route) pos_routing ON true
							WHERE he.id_employee = ?
								AND mtg.status = 'A'		
						) AS fix
						WHERE fix.pos_routing = ? OR fix.pos_routing IS NULL
						ORDER BY fix.pos_routing ASC", 
				[$idEmployee,$idPos]);
		}
	}
	
	public static function get_edit($data) {
		$id_kpk = $data['id_kpk'];
        $result = [];
		$sql = "SELECT hel.id_letter, hel.reference_number, hel.date, hel.id_dept, hel.id_branch, 
				hel.id_region, hel.id_position_detail, hel.id_employee_chief, hel.id_position_routing_chief, 
				hel.effective_date, hel.remark_1, hel.remark_2, hel.remark_3, hel.remark_4, hel.remark_6, hel.remark_7,
				md.department_code, hel.status
				FROM hr_electronic_letter hel
				JOIN master_department md
				ON hel.remark_3::integer = md.id_dept
				WHERE hel.id_letter = ".$id_kpk;				
        $result = (Array) DB::select($sql)[0];
		
		$sql2 = "SELECT hpe.*, mtg.minimum_score_kpi_level as val_threshold
				FROM hr_performance_evaluation hpe
				LEFT JOIN master_threshold_group mtg
				ON hpe.id_threshold_group = mtg.id_threshold_group
				WHERE hpe.id_letter = ?";
        $result_menu2 = DB::select($sql2, [$id_kpk]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_performance_evaluation')->toArray();
        
		$result['kpk'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['kpk'][] = [
                'id_performance_evaluation' => $group_menu2[$value][0]->id_performance_evaluation,
                'id_employee' => $group_menu2[$value][0]->id_employee,
                'id_position_detail' => $group_menu2[$value][0]->id_position_detail,
                'id_threshold_group' => $group_menu2[$value][0]->id_threshold_group,
                'evaluation_date' => $group_menu2[$value][0]->evaluation_date,
                'val_threshold' => $group_menu2[$value][0]->val_threshold,
                'kpi_value' => $group_menu2[$value][0]->kpi_average ?? $group_menu2[$value][0]->sales_offtake,
            //    'kpi_average' => $group_menu2[$value][0]->kpi_average,
                'sales_offtake' => $group_menu2[$value][0]->sales_offtake,
                'index_sales_percentage' => $group_menu2[$value][0]->index_sales_percentage,
                'evaluation_status' => $group_menu2[$value][0]->evaluation_status,
                'status' => $group_menu2[$value][0]->status,             
			];
        }
        return $result;
    }
	
	public static function get_edit_review($data) {
		$id_kpk = $data['id_kpk'];
		$id_position_detail = $data['id_position_detail'];
		$id_employee = $data['id_employee'];
        $result = [];
		$sql = "SELECT hpe.*, mtg.minimum_score_kpi_level, hel.remark_1 AS month_ba, CONCAT(sfer.name,' / ', sfer.nik_employee) AS name, 
				md.description AS department, mpr.description AS position_routing, mgd.description AS employment_status, 
				mp.principal_code AS principal, mr.description AS regional, mb.description AS branch, mjg.description AS job_grade, 
				fix.* 
				FROM hr_employee sfer
				JOIN(
					SELECT tes.id_employee, 
						he.name AS parent_emp_name, he2.name AS indirect_emp_name
					FROM (
						SELECT ".$id_employee." AS id_employee, ARRAY_AGG (id_employee_approval) as id_employee_approval
						FROM public.sp_funct_approval_organization_hierarchy_view(".$id_employee.",".session('id_company').",".$id_position_detail.",null)
						group by sequence
					) as tes
					LEFT JOIN hr_employee he
					ON tes.id_employee_approval[1] = he.id_employee
					LEFT JOIN hr_employee he2
					ON tes.id_employee_approval[2] = he2.id_employee
				) AS fix
				ON sfer.id_employee = fix.id_employee
				JOIN master_position_detail mpd
				ON sfer.id_employee = mpd.id_employee AND mpd.secondary_position = false
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN relation_positiondetail_principal rpp
				ON mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				JOIN master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_general_data mgd
				ON sfer.id_employment_status = mgd.id_general_data
				JOIN hr_performance_evaluation hpe	
				ON sfer.id_employee = hpe.id_employee
				LEFT JOIN master_threshold_group mtg
				ON hpe.id_threshold_group = mtg.id_threshold_group
				JOIN hr_electronic_letter hel
				ON hpe.id_letter = hel.id_letter
				WHERE hpe.id_performance_evaluation = ".$id_kpk;				
        $result = (Array) DB::select($sql)[0];
		
		$sql2 = "SELECT hpr.* FROM hr_performance_review hpr	
				WHERE hpr.id_performance_evaluation = ?
				ORDER BY hpr.period_date ASC";
        $result_menu2 = DB::select($sql2, [$id_kpk]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_performance_review')->toArray();
        
		$result['review'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['review'][] = [
                'id_performance_review' => $group_menu2[$value][0]->id_performance_review,
                'review_date' => $group_menu2[$value][0]->review_date,
                'period_date' => $group_menu2[$value][0]->period_date,
                'target_volume' => $group_menu2[$value][0]->target_volume,
                'result_value' => $group_menu2[$value][0]->result_value,
                'decision' => $group_menu2[$value][0]->decision,
                'treatment' => $group_menu2[$value][0]->treatment,
			];
        }
        return $result;
    }
	
	public static function get_pdf_potrait($data) {
		$idLetter = $data['id_letter'];
		$sql = "SELECT md.description AS dept, md.department_code AS dept_code, hel.*, he.name AS emp_name, he.nik_employee, mpr.description AS position, 
				mp.principal_code AS principal,
				mr.description AS region, mdiv.description AS division, mc.description AS com_name
				FROM hr_electronic_letter hel
				LEFT JOIN hr_employee he
				ON hel.id_employee_chief = he.id_employee
				LEFT JOIN master_position_routing mpr
				ON hel.id_position_routing_chief = mpr.id_routing
				LEFT JOIN relation_positiondetail_principal rpp
				ON hel.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal
				LEFT JOIN master_region mr
				ON hel.id_region = mr.id_region
				LEFT JOIN master_department md
				ON hel.remark_3::integer = md.id_dept
				LEFT JOIN master_division mdiv
				ON hel.remark_7::integer = mdiv.id_division
				LEFT JOIN master_company mc
				ON hel.id_company = mc.id_company
				WHERE hel.id_letter = ".$idLetter;				
        $result = DB::select($sql)[0];

        return $result;
    }
	
	public static function get_pdf_landscape($data) {
		$idLetter = $data['id_letter'];
		$sql = "SELECT he.name AS emp_name, he.nik_employee, 
				CONCAT(mtg.group_name,'-',mtg.subgroup_name) AS group, 
				mpr.description AS position, 
				CASE
					WHEN he2.name IS NOT NULL THEN he2.name
					WHEN he2.name IS NULL AND he3.name IS NOT NULL THEN he3.name
				END AS name_supervisor, mr.description As region, mb.description AS branch,
				mtg.minimum_score_kpi_level, hpe.*, mc.company_name
				FROM hr_performance_evaluation hpe
				LEFT JOIN hr_employee he
				ON hpe.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON hpe.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail = mpd2.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd2.id_employee = he2.id_employee
				LEFT JOIN master_position_detail mpd3
				ON mpd2.parent_id_position_detail = mpd3.id_position_detail
				LEFT JOIN hr_employee he3
				ON mpd3.id_employee = he3.id_employee
				LEFT JOIN master_threshold_group mtg
				ON hpe.id_threshold_group = mtg.id_threshold_group
				LEFT JOIN master_company mc
				ON he.id_company = mc.id_company
				WHERE hpe.id_letter = ".$idLetter;				
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_pdf_review($data) {
		$idReview = $data['id_review'];
		$sql = "SELECT he.name, he.nik_employee, mgd.description AS emp_status, mb.description AS branch, mc.description AS com_name, 
				hel.date AS ba_date, he2.name AS name_ba_create, he2.nik_employee AS nik_ba_create, mpr.description AS pos_ba_create,
				md2.description AS ba_dept, md2.department_code, hel.remark_1, he3.name AS direct_name, he3.nik_employee AS direct_nik, 
				mpr2.description AS direct_pos, ml2.description AS review_city, he4.name AS indirect_name, he4.nik_employee AS indirect_nik, mpr3.description AS indirect_pos, hpr.*
				FROM hr_performance_review hpr
				JOIN hr_performance_evaluation hpe
				ON hpr.id_performance_evaluation = hpe.id_performance_evaluation
				JOIN hr_employee he
				ON hpe.id_employee = he.id_employee
				LEFT JOIN master_company mc
				ON he.id_company = mc.id_company
				LEFT JOIN master_position_detail mpd
				ON hpe.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data
				JOIN hr_electronic_letter hel
				ON hpe.id_letter = hel.id_letter
			--	LEFT JOIN master_position_detail mpd2
			--	ON hel.id_position_detail = mpd2.id_position_detail
				JOIN hr_employee he2
				ON hel.id_employee_chief = he2.id_employee
				JOIN master_position_routing mpr
				ON hel.id_position_routing_chief = mpr.id_routing
				JOIN master_department md2
				ON hel.remark_3::integer = md2.id_dept
				LEFT JOIN hr_employee he3
				ON hpr.id_employee_reviewer = he3.id_employee
				LEFT JOIN master_position_detail mpd3
				ON hpr.id_position_detail_reviewer = mpd3.id_position_detail
				LEFT JOIN master_position_routing mpr2
				ON mpd3.id_position_routing = mpr2.id_routing
				LEFT JOIN master_location ml2
				ON mpd3.id_location = ml2.id_location
				LEFT JOIN hr_employee he4
				ON hpr.id_employee_acknowledge = he4.id_employee
				LEFT JOIN master_position_detail mpd4
				ON hpr.id_position_detail_acknowledge = mpd4.id_position_detail
				LEFT JOIN master_position_routing mpr3
				ON mpd4.id_position_routing = mpr3.id_routing
				WHERE hpr.id_performance_review = ".$idReview;				
        $result = DB::select($sql)[0];

        return $result;
    }
	
/*	public static function get_direct_career($idEmp) {
		$sql = "SELECT max(hct.id_career_transaction) AS id_career_transaction, 
				he.name AS name_direct, he.nik_employee AS nik_direct, mpr.description AS pos_direct, mpd.id_company
				FROM hr_career_transaction hct
				LEFT JOIN master_general_data mgd
				ON hct.id_transition_category = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hct.id_approval_status = mgd2.id_general_data
				LEFT JOIN master_position_detail mpd
				ON hct.id_old_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN hr_employee he
				ON hct.id_employee = he.id_employee
				WHERE hct.id_employee = ".$idEmp." AND mgd.code = 'Termination' AND mgd2.code = 'Approved'
				GROUP BY he.name, he.nik_employee, mpr.description, mpd.id_company";	
        $result = DB::select($sql);
        return $result;
    }
*/	
	public static function get_emp_ori($idEmp,$idCompany) {
		$sql = "SELECT he.name AS name_emp, he.nik_employee AS nik_employee, 
				mpr.description AS pos_emp, mpd.id_company
				FROM master_position_detail mpd
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				WHERE mpd.id_employee = ".$idEmp." AND mpd.id_company = ".$idCompany;	
        $result = DB::select($sql);
        return $result;
    }
   
	public static function get_indirect($idDirect,$id_company) {
		$sql = "SELECT 
			CASE
				WHEN mpd2.id_employee IS NOT NULL THEN mpd2.id_employee
				WHEN mpd2.id_employee IS NULL THEN mpd3.id_employee
				WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN mpd4.id_employee
			END AS id_indirect,
			CASE
				WHEN mpd2.id_employee IS NOT NULL THEN mpd2.id_position_detail
				WHEN mpd2.id_employee IS NULL THEN mpd3.id_position_detail
				WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN mpd4.id_position_detail
			END AS id_pos_indirect
			FROM master_position_detail mpd
			LEFT JOIN master_position_detail mpd2
			ON mpd.parent_id_position_detail = mpd2.id_position_detail
			LEFT JOIN master_position_detail mpd3
			ON mpd2.parent_id_position_detail = mpd3.id_position_detail
			LEFT JOIN master_position_detail mpd4
			ON mpd3.parent_id_position_detail = mpd4.id_position_detail
			WHERE mpd.secondary_position = false AND mpd.id_employee = ".$idDirect." AND mpd.id_company = ".$id_company;	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_name_mail($idDirect) {
		$sql = "SELECT he.id_employee AS id_direct, he.name AS name_direct, he.nik_employee AS nik_direct,
				he.private_mail, he.mobile_phone
				FROM hr_employee he
				WHERE he.status = 'A' AND he.id_employee = ".$idDirect;	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_emp_direct() {
		$sql = "SELECT mpd.id_employee, mpd.id_position_detail, mpd.id_company
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				WHERE he.id_user = ".session('id_user')." AND he.status = 'A' AND mpd.secondary_position = false
				AND mpd.id_company = ".session('id_company');	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_check($param) {
		$idEmployee = $param['id_employee'];
		$dateFirst = $param['start_date'];
		$dateEnd = $param['end_date'];
		$idCompany = $param['id_company'];
		$sql = "SELECT hrd.request_start_to, hrd.request_end_to, hrd.qty_days
				FROM hr_request_detail hrd
				JOIN hr_request_header hrh
				ON hrd.id_request_header = hrh.id_request_header
				JOIN master_general_data mgd
				ON hrh.id_approval_status = mgd.id_general_data
				LEFT JOIN master_leave_type mlt
				ON hrh.id_leave_type = mlt.id_leave_type
				WHERE hrd.id_employee = ".$idEmployee." AND hrd.id_company = ".$idCompany."
				AND mgd.code = 'Approved' AND mlt.leave_code IN('MAT','HJL','PSL') AND hrd.qty_days > 10
				AND (hrd.request_start_to BETWEEN '".$dateFirst."' AND '".$dateEnd."' OR  hrd.request_end_to BETWEEN '".$dateFirst."' AND '".$dateEnd."')";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getExport($group_branch, $nik=null, $dept_code = null, $status) {
		$branch = "";
		$idNik = "";
		if($group_branch || $group_branch != ""){
			$branch = " AND fix.id_branch IN(".$group_branch.")";
		}
		if($nik){
			$idNik = " AND fix.id_employee IN(".implode(",", $nik).")";
		}
		$dept = "";
		if($dept_code != 'null') {
			$dept = " AND fix.dept_code = '".$dept_code."'";
		}
		$sql= "SELECT fix.name, fix.nik_employee, fix.id_employee, fix.emp_pos, fix.dept, fix.division,
				fix.branch, fix.region, fix.company_code, fix.grade, fix.id_branch,
				fix.direct_name, fix.ba_month, fix.ba_perf, fix.dept_code, fix.com_type,
				STRING_AGG(fix.perf_month_1,'') AS perf_month_1,
				STRING_AGG(fix.vol_kpi_month_1::text,'') AS vol_kpi_month_1,
				STRING_AGG(fix.res_month_1,'') AS res_month_1,
				STRING_AGG(fix.treat_month_1,'') AS treat_month_1,
				STRING_AGG(fix.review_date_1::text,'') AS review_date_1,
				STRING_AGG(fix.perf_month_2,'') AS perf_month_2,
				STRING_AGG(fix.vol_kpi_month_2::text,'') AS vol_kpi_month_2,
				STRING_AGG(fix.res_month_2,'') AS res_month_2,
				STRING_AGG(fix.treat_month_2,'') AS treat_month_2,
				STRING_AGG(fix.review_date_2::text,'') AS review_date_2,
				STRING_AGG(fix.perf_month_3,'') AS perf_month_3,
				STRING_AGG(fix.vol_kpi_month_3::text,'') AS vol_kpi_month_3,
				STRING_AGG(fix.res_month_3,'') AS res_month_3,
				STRING_AGG(fix.treat_month_3,'') AS treat_month_3,
				STRING_AGG(fix.review_date_3::text,'') AS review_date_3,
				STRING_AGG(fix.perf_month_4,'') AS perf_month_4,
				STRING_AGG(fix.vol_kpi_month_4::text,'') AS vol_kpi_month_4,
				STRING_AGG(fix.res_month_4,'') AS res_month_4,
				STRING_AGG(fix.treat_month_4,'') AS treat_month_4,
				STRING_AGG(fix.review_date_4::text,'') AS review_date_4,
				STRING_AGG(fix.perf_month_5,'') AS perf_month_5,
				STRING_AGG(fix.vol_kpi_month_5::text,'') AS vol_kpi_month_5,
				STRING_AGG(fix.res_month_5,'') AS res_month_5,
				STRING_AGG(fix.treat_month_5,'') AS treat_month_5,
				STRING_AGG(fix.review_date_5::text,'') AS review_date_5,
				STRING_AGG(fix.perf_month_6,'') AS perf_month_6,
				STRING_AGG(fix.vol_kpi_month_6::text,'') AS vol_kpi_month_6,
				STRING_AGG(fix.res_month_6,'') AS res_month_6,
				STRING_AGG(fix.treat_month_6,'') AS treat_month_6,
				STRING_AGG(fix.review_date_6::text,'') AS review_date_6,
				STRING_AGG(fix.perf_month_7,'') AS perf_month_7,
				STRING_AGG(fix.vol_kpi_month_7::text,'') AS vol_kpi_month_7,
				STRING_AGG(fix.res_month_7,'') AS res_month_7,
				STRING_AGG(fix.treat_month_7,'') AS treat_month_7,
				STRING_AGG(fix.review_date_7::text,'') AS review_date_7,
				STRING_AGG(fix.perf_month_8,'') AS perf_month_8,
				STRING_AGG(fix.vol_kpi_month_8::text,'') AS vol_kpi_month_8,
				STRING_AGG(fix.res_month_8,'') AS res_month_8,
				STRING_AGG(fix.treat_month_8,'') AS treat_month_8,
				STRING_AGG(fix.review_date_8::text,'') AS review_date_8
			FROM (
			SELECT tes.name, tes.nik_employee, tes.id_employee, tes.emp_pos, tes.dept, tes.division,
				tes.branch, tes.region, tes.company_code, tes.grade, tes.id_branch,
				tes.direct_name, tes.review_by, tes.ba_month, tes.ba_perf, tes.dept_code, tes.com_type,
			CASE WHEN tes.num = 1 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_1,
			CASE WHEN tes.num = 1 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_1,
			CASE 
				WHEN tes.num = 1 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 1 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_1,
			CASE WHEN tes.num = 1 THEN tes.treatment END AS treat_month_1,
			CASE WHEN tes.num = 1 THEN tes.review_date END AS review_date_1,
			CASE WHEN tes.num = 2 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_2,
			CASE WHEN tes.num = 2 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_2,
			CASE 
				WHEN tes.num = 2 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 2 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_2,
			CASE WHEN tes.num = 2 THEN tes.treatment END AS treat_month_2,
			CASE WHEN tes.num = 2 THEN tes.review_date END AS review_date_2,
			CASE WHEN tes.num = 3 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_3,
			CASE WHEN tes.num = 3 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_3,
			CASE 
				WHEN tes.num = 3 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 3 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_3,
			CASE WHEN tes.num = 3 THEN tes.treatment END AS treat_month_3,
			CASE WHEN tes.num = 3 THEN tes.review_date END AS review_date_3,
			CASE WHEN tes.num = 4 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_4,
			CASE WHEN tes.num = 4 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_4,
			CASE 
				WHEN tes.num = 4 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 4 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_4,
			CASE WHEN tes.num = 4 THEN tes.treatment END AS treat_month_4,
			CASE WHEN tes.num = 4 THEN tes.review_date END AS review_date_4,
			CASE WHEN tes.num = 5 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_5,
			CASE WHEN tes.num = 5 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_5,
			CASE 
				WHEN tes.num = 5 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 5 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_5,
			CASE WHEN tes.num = 5 THEN tes.treatment END AS treat_month_5,
			CASE WHEN tes.num = 5 THEN tes.review_date END AS review_date_5,
			CASE WHEN tes.num = 6 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_6,
			CASE WHEN tes.num = 6 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_6,
			CASE 
				WHEN tes.num = 6 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 6 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_6,
			CASE WHEN tes.num = 6 THEN tes.treatment END AS treat_month_6,
			CASE WHEN tes.num = 6 THEN tes.review_date END AS review_date_6,
			CASE WHEN tes.num = 7 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_7,
			CASE WHEN tes.num = 7 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_7,
			CASE 
				WHEN tes.num = 7 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 7 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_7,
			CASE WHEN tes.num = 7 THEN tes.treatment END AS treat_month_7,
			CASE WHEN tes.num = 7 THEN tes.review_date END AS review_date_7,
			CASE WHEN tes.num = 8 THEN CONCAT(TO_CHAR(tes.period_date, 'Mon'),' ',TO_CHAR(tes.period_date, 'YY')) END AS perf_month_8,
			CASE WHEN tes.num = 8 THEN round(((tes.result_value::numeric/tes.target_volume::numeric))*100,2) END AS vol_kpi_month_8,
			CASE 
				WHEN tes.num = 8 AND tes.decision = 'MISS' THEN 'Tidak Lulus'
				WHEN tes.num = 8 AND tes.decision = 'HIT' THEN 'Lulus'
			END AS res_month_8,
			CASE WHEN tes.num = 8 THEN tes.treatment END AS treat_month_8,
			CASE WHEN tes.num = 8 THEN tes.review_date END AS review_date_8
			FROM (
				SELECT he.name, he.nik_employee, he.id_employee, mpr.description AS emp_pos, md.description AS dept, mdiv.description AS division,
				mb.description AS branch, mr.description AS region, mc.company_code, mjg.description AS grade, mpd.id_branch,
				CASE
					WHEN mpd2.id_employee IS NOT NULL THEN he2.name
					WHEN mpd2.id_employee IS NULL THEN he3.name
					WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN he4.name
				END AS direct_name, he5.name AS review_by,
				hel.remark_1 AS ba_month, hel.remark_2 AS ba_perf, md.department_code AS dept_code,
				hel.remark_6 AS com_type,
				hpr.period_date, hpr.review_date, hpr.result_value, hpr.target_volume, hpr.decision, hpr.treatment,
				ROW_NUMBER () OVER (PARTITION BY he.id_employee ORDER BY hpr.period_date) AS num
				FROM hr_performance_review hpr
				JOIN hr_performance_evaluation hpe
				ON hpr.id_performance_evaluation = hpe.id_performance_evaluation
				JOIN hr_employee he
				ON hpe.id_employee = he.id_employee
				JOIN hr_electronic_letter hel
				ON hpe.id_letter = hel.id_letter
				LEFT JOIN master_department md
				ON hel.remark_3::integer = md.id_dept
				LEFT JOIN master_division mdiv
				ON hel.remark_7::integer = mdiv.id_division
				LEFT JOIN master_position_detail mpd
				ON hpe.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_company mc
				ON he.id_company = mc.id_company
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail = mpd2.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd2.id_employee = he2.id_employee
				LEFT JOIN master_position_detail mpd3
				ON mpd2.parent_id_position_detail = mpd3.id_position_detail
				LEFT JOIN hr_employee he3
				ON mpd3.id_employee = he3.id_employee
				LEFT JOIN master_position_detail mpd4
				ON mpd3.parent_id_position_detail = mpd4.id_position_detail
				LEFT JOIN hr_employee he4
				ON mpd4.id_employee = he4.id_employee
				LEFT JOIN hr_employee he5
				ON hpr.id_employee_reviewer = he5.id_employee
				WHERE hel.status = '".$status."' AND hel.id_company = ".session('id_company')."
				ORDER BY he.name ASC, hpr.period_date ASC
			) AS tes
		) AS fix
		WHERE 1=1 ".$branch." ".$idNik." ".$dept."
		GROUP BY fix.name, fix.nik_employee, fix.id_employee, fix.emp_pos, fix.dept, fix.division,
				fix.branch, fix.region, fix.company_code, fix.grade, fix.id_branch,
				fix.direct_name, fix.ba_month, fix.ba_perf, fix.dept_code, fix.com_type";	
        $result = DB::select($sql);
        return $result;
    }

   
}
