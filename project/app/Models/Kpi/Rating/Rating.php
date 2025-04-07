<?php

namespace App\Models\Kpi\Rating;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rating extends Model
{
	use HasFactory;
	
    protected $table = 'hr_kpi_total';
	protected $primaryKey = 'id_kpi_total';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_kpi_total', 'id_employee_participant', 'id_period', 'id_qualitative_participant', 'total_qualitative_score', 'id_kpi_group', 'total_quantitative_score', 'total_kpi_score', 'total_maximum_score', 'total_percent', 'id_code_promotion', 'notes', 'final_rating', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getdata($period) {
        $sql = "SELECT he2.id_user, he.nik_employee, he.name, mjg.description AS grade,
				mp.description as period, hkt.*, mgp.code AS propose_rating  
				FROM hr_kpi_total hkt
				LEFT JOIN hr_employee he
				ON hkt.id_employee_participant = he.id_employee AND he.status = 'A'
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN hr_qualitative_participant hqp
				ON hkt.id_qualitative_participant = hqp.id_qualitative_participant
				JOIN public.sp_funct_pa_mapping_rating(".$period.",".session('id_company').") sfpm
				ON hkt.id_kpi_group = sfpm.id_kpi_group
				JOIN hr_kpi_group hkg
				ON sfpm.id_kpi_group = hkg.id_kpi_group
				JOIN hr_employee he2
				ON sfpm.id_employee_appraisers = he2.id_employee
				JOIN master_period mp
				ON hkg.id_period = mp.id_period AND mp.status = 'A'
				LEFT JOIN master_grade_promotion mgp
				ON hkt.id_code_promotion = mgp.id_grade_promotion
				WHERE he2.id_user = ".session('id_user')." AND hkt.id_company = ".session('id_company')." AND hkt.status = 'A'
				ORDER BY he.name ASC";
        $get = DB::select($sql);
		$get_res = collect($get);
		if($period){
			$result = $get_res->whereIn('id_period', $period);
		}
		else{
			$result = $get_res;
		}
        return $result;
    }
	
	public static function getdata_report($period,$group_branch,$id_employee_appraiser) {
		if($group_branch == null || $group_branch == ""){
				$where_branch = "";
		}	
		else{
			$where_branch = "mpd.id_branch in(".$group_branch.") AND ";
		}
        $sql = "SELECT he2.id_user, he.nik_employee, he.name, mjg.description AS grade, hkt.id_period,
				mp.description as period, hkt.*, mgp.code AS rating, mpd.id_branch, sfpm.id_employee_appraisers, sfpm.atasan AS name_appraiser
				FROM hr_kpi_total hkt
				LEFT JOIN hr_employee he
				ON hkt.id_employee_participant = he.id_employee AND he.status = 'A'
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN hr_qualitative_participant hqp
				ON hkt.id_qualitative_participant = hqp.id_qualitative_participant
				JOIN public.sp_funct_pa_mapping_rating(".$period.",".session('id_company').") sfpm
				ON hkt.id_kpi_group = sfpm.id_kpi_group
				JOIN hr_kpi_group hkg
				ON sfpm.id_kpi_group = hkg.id_kpi_group
				JOIN hr_employee he2
				ON sfpm.id_employee_appraisers = he2.id_employee
				JOIN master_period mp
				ON hkg.id_period = mp.id_period AND mp.status = 'A'
				LEFT JOIN master_grade_promotion mgp
				ON hkt.id_code_promotion = mgp.id_grade_promotion
				WHERE ".$where_branch." hkt.id_company = ".session('id_company')." AND hkt.status = 'A'
				ORDER BY he.name ASC";
        $get = DB::select($sql);
		$get_res = collect($get);
		if($period){
			$result = $get_res->whereIn('id_period', $period);
		}
		else{
			$result = $get_res;
		}
		if($id_employee_appraiser) {
			$result = $result->where('id_employee_appraisers', $id_employee_appraiser);
		}
        return $result;
    }
	
	public static function get_calpa($id_employee,$period) {
        $sql = "SELECT hkg.*, mp.year 
				FROM public.sp_funct_pa_mapping_rating(".$period.",".session('id_company').") sfpm
				JOIN hr_kpi_group hkg
				ON sfpm.id_kpi_group = hkg.id_kpi_group
				JOIN master_period mp
				ON hkg.id_period = mp.id_period AND mp.status = 'A'
				JOIN hr_employee he
				ON sfpm.id_employee = he.id_employee AND he.status = 'A'
				WHERE sfpm.id_employee_appraisers = ? AND hkg.status ='A' AND hkg.id_company = ".session('id_company')." AND hkg.submitted = 'false'";
        $res = DB::select($sql, [$id_employee]);
		$result = json_decode(json_encode($res),true);
        return $result;
    }
	
	public static function get_quali($id_employee,$year) {
        $sql = "SELECT hqp.*, mp.year 
				FROM hr_qualitative_participant hqp
				JOIN master_period mp
				ON hqp.id_period = mp.id_period
				JOIN master_general_data mgd
				ON mp.id_function_type = mgd.id_general_data AND mgd.description = 'HR-QUALITATIVE'
				WHERE mp.year = ? AND hqp.id_employee_participant = ? AND hqp.status = 'A' AND hqp.id_company = ? AND hqp.is_end_year_period = 'true' AND hqp.transaction_type = 'PA'";
        $res = DB::select($sql, [$year,$id_employee,session('id_company')]);
		$result = json_decode(json_encode($res),true);
        return $result;
    }
	
	public static function get_rating_edit($id_kpi_total) {
        $sql = "SELECT mjg.id_job_grade, hkt.id_kpi_total, hkt.id_employee_participant, 
				hkt.id_period, hkt.id_code_promotion, hkt.notes, he.name, mgd.code AS emp_status, current_date - he.join_date AS day_join
				FROM hr_kpi_total hkt
				JOIN hr_employee he
				ON hkt.id_employee_participant = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON (hkt.id_employee_participant = mpd.id_employee OR hkt.id_employee_participant = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data
				WHERE hkt.id_kpi_total = ? AND hkt.status ='A' AND hkt.id_company = ?";
        $result = DB::select($sql, [$id_kpi_total,session('id_company')])[0];
	//	dd($result);
        return $result;
    }
	
	public static function get_rating($id_job_grade,$emp_status,$day_join) {
		if($emp_status != 'Permanent' && $day_join <= 730){
			$code = "AND code = 'NR'";	
		}
		else if($emp_status == 'Permanent' && $day_join <= 330){
			$code = "AND code = 'NR'";	
		}
		else if($emp_status == 'Acting'){
			$code = "AND code = 'NR'";	
		}
		else{
			$code = "AND code NOT IN('NE','NR')";
		}
        $sql = "SELECT id_grade_promotion id, CONCAT(code,' (',description,')') text
				FROM master_grade_promotion mgp
				WHERE mgp.id_job_grade = ? AND mgp.status ='A' AND mgp.id_company = ? " .$code;
        $result = DB::select($sql, [$id_job_grade,session('id_company')]);
        return $result;
    }
	
	public static function get_period() {
        $sql = "SELECT mp.id_period id, CONCAT(mp.description) text
				FROM master_period mp
				JOIN master_general_data mgd
				ON mp.id_function_type = mgd.id_general_data
				WHERE mgd.code ='HR' AND mgd.description = 'HR-QUANTITATIVE' AND mp.status = 'A' AND mp.id_company = ?
				ORDER BY id_period DESC";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
}
