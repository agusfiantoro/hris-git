<?php

namespace App\Models\Kpi\Kpi;

use App\Models\Kpi\Kpi\KpiHeader;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuantitativeKpi extends Model
{
	use HasFactory;
	
    protected $table = 'hr_kpi_group';
	protected $primaryKey = 'id_kpi_group';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_kpi_group', 'id_employee', 'average_prosentase', 'description', 'notes', 'start_date', 'end_date', 'id_period', 'kpi_class', 'id_employee_appraisers', 'submitted', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getdata($period) {
		/*
        $sql = "SELECT hkg.id_kpi_group, hkg.id_employee, he.nik_employee, he.name, mjg.description AS grade, hkg.id_period,
				mp.description as period, hkg.average_prosentase, hkg.description, hkg.id_employee_appraisers, hkg.submitted
					FROM hr_kpi_group hkg
				JOIN hr_employee he
				ON hkg.id_employee = he.id_employee AND he.status = 'A'
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_period mp
				ON hkg.id_period = mp.id_period 
				LEFT JOIN hr_employee he2
				ON hkg.id_employee_appraisers = he2.id_employee AND he2.status = 'A'
				WHERE hkg.id_company = ".session('id_company')." AND he2.id_user = ".session('id_user')." AND hkg.status = 'A'
				ORDER BY he.name ASC";
		*/
		$sql = "SELECT hkg.id_kpi_group, hkg.id_employee, he.nik_employee, he.name, mjg.description AS grade, hkg.id_period,
				mp.description as period, hkg.average_prosentase, hkg.description, sfpm.id_employee_appraisers, hkg.submitted
					FROM public.sp_funct_pa_mapping(".$period.",".session('id_company').") sfpm
				JOIN hr_kpi_group hkg
				ON sfpm.id_kpi_group = hkg.id_kpi_group
				JOIN hr_employee he
				ON sfpm.id_employee = he.id_employee				
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_period mp
				ON hkg.id_period = mp.id_period 
				LEFT JOIN hr_employee he2
				ON sfpm.id_employee_appraisers = he2.id_employee
				WHERE he2.id_user = ".session('id_user')."
				ORDER BY he.name ASC";
        $result = DB::select($sql);
		/*
		$get_res = collect($get);
		if($period){
			$result = $get_res->whereIn('id_period', $period);
		}
		else{
			$result = $get_res;
		}
		*/
        return $result;
    }
	
	public static function get_report($id_employee,$period,$group_branch) {
			if($group_branch == null || $group_branch == ""){
				$where_branch = "";
			}	
			else{
				$where_branch = "mpd.id_branch in(".$group_branch.") AND ";
			}
			$sql = "SELECT k_h.id_kpi_group, k_h.id_branch, k_h.id_employee, CONCAT(k_h.name_dinilai,' (',k_h.nik_dinilai,')') AS dinilai, k_h.average_prosentase, k_h.id_period, 
					k_h.id_employee_appraisers, CONCAT(k_h.name_penilai,' (',k_h.nik_penilai,')') AS penilai,
					STRING_AGG(k_h.jan::character varying,'-') AS jan, STRING_AGG(k_h.feb::character varying,'') AS feb, 
					STRING_AGG(k_h.mar::character varying,'') AS mar, STRING_AGG(k_h.apr::character varying,'') AS apr, STRING_AGG(k_h.may::character varying,'') AS mei,
					STRING_AGG(k_h.jun::character varying,'') AS jun, STRING_AGG(k_h.jul::character varying,'') AS jul, STRING_AGG(k_h.aug::character varying,'') AS ags, 
					STRING_AGG(k_h.sep::character varying,'') AS sep, STRING_AGG(k_h.okt::character varying,'') AS okt, STRING_AGG(k_h.nov::character varying,'') AS nov,
					STRING_AGG(k_h.des::character varying,'') AS des
					FROM (
						SELECT mpd.id_branch, he.name AS name_dinilai, he.nik_employee AS nik_dinilai, he2.name AS name_penilai, he2.nik_employee AS nik_penilai,  hkg.id_kpi_group, hkg.id_employee, hkg.average_prosentase, hkg.id_period, hkg.id_employee_appraisers,  
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
						JOIN public.sp_funct_pa_mapping(null,".session('id_company').") sfpm
						ON hkh.id_kpi_group = sfpm.id_kpi_group
						JOIN hr_kpi_group hkg
						ON sfpm.id_kpi_group = hkg.id_kpi_group
						LEFT JOIN hr_employee he
						ON hkg.id_employee = he.id_employee
						LEFT JOIN hr_employee he2
						ON hkg.id_employee_appraisers = he2.id_employee
						LEFT JOIN master_position_detail mpd
						ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
						WHERE ".$where_branch." hkh.id_company = ".session('id_company')." AND hkh.status = 'A'
					) AS k_h
					GROUP BY k_h.id_branch, k_h.name_dinilai, k_h.name_penilai, k_h.nik_dinilai, k_h.nik_penilai, 
					k_h.id_kpi_group, k_h.id_employee, k_h.average_prosentase, k_h.id_period, k_h.id_employee_appraisers
					ORDER BY k_h.name_dinilai ASC";
			$get = DB::select($sql);
			$get_res = collect($get);
			if(count($id_employee) > 0){								
				if($period != null){
					$result = $get_res->whereIn('id_employee', $id_employee)->whereIn('id_period', $period);
				}
				else{
					$result = $get_res->whereIn('id_employee', $id_employee);
				}
			}
			else if($period != null){
				if(count($id_employee) > 0){
					$result = $get_res->whereIn('id_employee', $id_employee)->whereIn('id_period', $period);
				}
				else{
					$result = $get_res->whereIn('id_period', $period);
				}
			}
			else{
				$result = $get_res;
			}
			return $result;
	}
	
	public static function get_report_excel() {
			
			$sql = "SELECT k_h.id_kpi_group, k_h.id_branch, k_h.id_employee, k_h.name_dinilai AS dinilai, k_h.nik_dinilai AS nik_dinilai, k_h.average_prosentase, k_h.id_period, 
					k_h.id_employee_appraisers, k_h.name_penilai AS atasan, k_h.nik_penilai AS nik_atasan,
					STRING_AGG(k_h.jan::character varying,'-') AS jan, STRING_AGG(k_h.feb::character varying,'') AS feb, 
					STRING_AGG(k_h.mar::character varying,'') AS mar, STRING_AGG(k_h.apr::character varying,'') AS apr, STRING_AGG(k_h.may::character varying,'') AS mei,
					STRING_AGG(k_h.jun::character varying,'') AS jun, STRING_AGG(k_h.jul::character varying,'') AS jul, STRING_AGG(k_h.aug::character varying,'') AS ags, 
					STRING_AGG(k_h.sep::character varying,'') AS sep, STRING_AGG(k_h.okt::character varying,'') AS okt, STRING_AGG(k_h.nov::character varying,'') AS nov,
					STRING_AGG(k_h.des::character varying,'') AS des
					FROM (
						SELECT mpd.id_branch, he.name AS name_dinilai, he.nik_employee AS nik_dinilai, he2.name AS name_penilai, he2.nik_employee AS nik_penilai,  hkg.id_kpi_group, hkg.id_employee, hkg.average_prosentase, hkg.id_period, hkg.id_employee_appraisers,  
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
						JOIN public.sp_funct_pa_mapping(null,".session('id_company').") sfpm
						ON hkh.id_kpi_group = sfpm.id_kpi_group
						JOIN hr_kpi_group hkg
						ON sfpm.id_kpi_group = hkg.id_kpi_group
						LEFT JOIN hr_employee he
						ON hkg.id_employee = he.id_employee
						LEFT JOIN hr_employee he2
						ON hkg.id_employee_appraisers = he2.id_employee
						LEFT JOIN master_position_detail mpd
						ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
						WHERE hkh.id_company = ".session('id_company')." AND hkh.status = 'A'
					) AS k_h
					GROUP BY k_h.id_branch, k_h.name_dinilai, k_h.name_penilai, k_h.nik_dinilai, k_h.nik_penilai, 
					k_h.id_kpi_group, k_h.id_employee, k_h.average_prosentase, k_h.id_period, k_h.id_employee_appraisers
					ORDER BY k_h.name_dinilai ASC";
			$get = DB::select($sql);
			$result = collect($get);

			return $result;
	}
	
	public static function getdata_upload($id_employee,$period,$group_branch) {
		if($group_branch == null || $group_branch == ""){
			$where_branch = "";
		}	
		else{
			$where_branch = "WHERE mpd.id_branch in(".$group_branch.")";
		}
		$sql="SELECT hkg.id_kpi_group, hkg.id_employee, he.nik_employee, he.name, mjg.description AS grade, hkg.id_period,
				mp.description AS period, hkg.average_prosentase, hkg.description, sfpm.id_employee_appraisers, mpd.id_branch,
				CONCAT(he2.name,' (',he2.nik_employee,')') AS penilai, hkg.status, hkg.submitted
				FROM public.sp_funct_pa_mapping(".$period.",".session('id_company').") sfpm
				JOIN hr_kpi_group hkg
				ON sfpm.id_kpi_group = hkg.id_kpi_group
				JOIN hr_employee he
				ON sfpm.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_period mp
				ON hkg.id_period = mp.id_period 
				LEFT JOIN hr_employee he2
				ON sfpm.id_employee_appraisers = he2.id_employee
				".$where_branch." 
				ORDER BY he2.name ASC, he.name ASC";
		$get = DB::select($sql);
		$get_res = collect($get);
		if($id_employee != null){
			$result = $get_res->whereIn('id_employee', $id_employee);
		}
		else{
			$result = $get_res;
		}
		/*
        $sql = "SELECT hkg.id_kpi_group, hkg.id_employee, he.nik_employee, he.name, mjg.description AS grade, hkg.id_period,
				mp.description AS period, hkg.average_prosentase, hkg.description, hkg.id_employee_appraisers, mpd.id_branch,
				CONCAT(he2.name,' (',he2.nik_employee,')') AS penilai, hkg.status, hkg.submitted
					FROM hr_kpi_group hkg
				JOIN hr_employee he
				ON hkg.id_employee = he.id_employee AND he.status = 'A'
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_period mp
				ON hkg.id_period = mp.id_period 
				LEFT JOIN hr_employee he2
				ON hkg.id_employee_appraisers = he2.id_employee AND he2.status = 'A'
				WHERE ".$where_branch." hkg.id_company = ".session('id_company')."
				ORDER BY he2.name ASC, he.name ASC";
			$get = DB::select($sql);
			$get_res = collect($get);
			if($id_employee != null){								
				if($period != null){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('id_period', $period);
				}
				else{
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee);
				}
			}
			else if($period != null){
				if($id_employee != null){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('id_period', $period);
				}
				else{
					$result = $get_res->whereIn('id_period', $period);
				}
			}
			else{
				$result = $get_res;
			}
			*/
			
			return $result;
    }	
	
	public static function generate_kpi($data) {
		$id_employee = $data['id_employee'];
		$period = $data['period'];
		$id_company = $data['id_company'];
		$id_user = $data['id_user'];
		
		$sql ="INSERT INTO hr_kpi_group
                        (id_employee_appraisers, id_period, id_employee, average_prosentase,
                        description,start_date, end_date, kpi_class,
                        id_company, creation_date, created_by )
				SELECT	null as id_employee_appraisers, ".$period." as id_period,
                        mpd.id_employee as id_employee, null as average_prosentase,
                        'KPI Yearly - '::character varying || mp.year::character varying as description, 
                        mp.start_date, mp.end_date,
                        'PA' as kpi_class,
                        mpd.id_company as id_company, now(), coalesce(".$id_user.",1) as created_by
                FROM master_position_detail mpd
                JOIN hr_employee he
                ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee) AND he.status = 'A'
                LEFT JOIN master_period mp
                  ON mp.id_period = ".$period."
                WHERE he.id_employee = coalesce(?,he.id_employee)
                  AND mpd.id_company = coalesce(".$id_company.",mpd.id_company)
                  AND mpd.status = 'A'
                  AND concat(".$period.",'-','PA','-',mpd.id_employee) not in (select concat(id_period, '-', kpi_class,'-',id_employee)
                                                                                                    from hr_kpi_group
                                                                                                    where id_period = ".$period."
                                                                                                    and id_company = ".$id_company.")";
		/*
		$sql ="INSERT INTO hr_kpi_group
                        (id_employee_appraisers, id_period, id_employee, average_prosentase,
                        description,start_date, end_date, kpi_class,
                        id_company, creation_date, created_by )
				SELECT	mpd.id_employee as id_employee_appraisers, ".$period." as id_period,
                        mpd2.id_employee as id_employee, null as average_prosentase,
                        'KPI Yearly - '::character varying || mp.year::character varying as description, 
                        mp.start_date, mp.end_date,
                        'PA' as kpi_class,
                        mpd.id_company as id_company, now(), coalesce(".$id_user.",1) as created_by
                FROM master_position_detail mpd
                LEFT JOIN master_position_detail mpd2
                  ON mpd2.parent_id_position_detail =  mpd.id_position_detail
                JOIN hr_employee he
                ON mpd2.id_employee = he.id_employee AND he.status = 'A'
                LEFT JOIN master_period mp
                  ON mp.id_period = ".$period."
                WHERE mpd.id_employee = coalesce(?,mpd.id_employee)
                  AND mpd.id_company = coalesce(".$id_company.",mpd.id_company)
                  AND mpd2.id_employee is not null
                  AND mpd.id_employee is not null
                  AND mpd.status = 'A'
                  AND concat(mpd2.id_employee,'-',".$period.",'-','PA','-',mpd.id_employee) not in (select concat(id_employee, '-', id_period, '-', kpi_class,'-',id_employee_appraisers)
                                                                                                    from hr_kpi_group
                                                                                                    where id_period = ".$period."
                                                                                                    and id_company = ".$id_company.")";
		*/																						
		$result = DB::select($sql,[$id_employee]);
	//	$result = DB::select("SELECT * FROM  spgeneratekpiquantitative(?, ?, ?, ?)",[$id_employee,$period,$id_company,$id_user]);		
		return $result;		
	}
	
	public static function generate_detail($data) {
		$id_kpi_group = $data['id_kpi_group'];
		$start_date = $data['start_date'];
		$id_company = $data['id_company'];
		$id_user = $data['id_user'];
		
		$sql = "SELECT	hkg.id_kpi_group, to_char(dt, 'yyyy-mm-01')::date as kpi_month ,
                        null as notes, null as subtotal_kpi,
                        hkg.id_company, now() as creation_date, coalesce(?, 1) as created_by
                FROM hr_kpi_group hkg
                CROSS JOIN generate_series(? , hkg.end_date , interval '1 month') as dt
                WHERE hkg.id_kpi_group = ".$id_kpi_group."
                  AND hkg.id_company = ".$id_company."
                  AND concat(id_kpi_group,'-',to_char(dt, 'yyyy-mm-01')) not in (select concat(id_kpi_group,'-',kpi_month)
                                                                                 from hr_kpi_header
                                                                                 where id_company = ".$id_company."
                                                                                 and id_kpi_group = ".$id_kpi_group.")";
		$result = DB::select($sql, [$id_user,$start_date]);
	//	$result = DB::select("SELECT * FROM  spgeneratekpiheader(?, ?, ?)",[$id_kpi_group,$id_company,$id_user]);		
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
	
	public static function get_employee_filter($group_branch) {
		if($group_branch == null || $group_branch == ""){
			$where_branch = "";
		}	
		else{
			$where_branch = "mpd.id_branch in(".$group_branch.") AND ";
		}
        $sql = "SELECT DISTINCT he.name, he.id_employee AS id, CONCAT(he.name,' (',he.nik_employee,')') AS text,
				mpd.id_branch
				FROM hr_kpi_group hkg
				JOIN hr_employee he
				ON hkg.id_employee = he.id_employee 
				JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				WHERE ".$where_branch." he.status = 'A'
				ORDER BY he.name ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_period_report() {
        $sql = "SELECT DISTINCT hkg.id_period id, mp.description AS text
				FROM hr_kpi_group hkg
				JOIN master_period mp
				ON hkg.id_period = mp.id_period 
				WHERE mp.id_company = ?
				ORDER BY hkg.id_period ASC";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_category() {
        $sql = "SELECT  mkc.id_kpi_category id, 
						mkc.description text
				FROM master_kpi_category mkc
				LEFT JOIN master_general_data mgd
				ON mkc.id_group_segment = mgd.id_general_data
				WHERE mgd.code = 'PA' AND mkc.id_company = ? AND mkc.status = 'A'";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_type() {
        $sql = "SELECT 	mgd.id_general_data id, 
						mgd.description text 
				FROM master_general_data mgd
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_pa_type' 
				AND mgd.id_company = ? AND mgd.status = 'A'
				ORDER BY mgd.sequence ASC";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	public static function get_name_type($id_type) {
        $sql = "SELECT 	mgd.code 
				FROM master_general_data mgd
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_pa_type' 
				AND mgd.id_general_data = ".$id_type."
				AND mgd.id_company = ? AND mgd.status = 'A' ";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function calyearly($id_kpi_group) {
        $sql = "SELECT hkh.* FROM hr_kpi_header hkh
				WHERE hkh.id_kpi_group = ".$id_kpi_group."
				AND hkh.id_company = ? AND hkh.status = 'A'";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function calmonthly($id_kpi_header) {
        $sql = "SELECT hkd.id_kpi_detail, hkd.id_kpi_header, hkd.id_kpi_category, hkd.kpi_value, 
				hkd.weight_prosentase AS weight, hkd.id_kpi_type, hkd.kpi_target_value AS target 
				FROM hr_kpi_detail hkd
				WHERE hkd.id_kpi_header = ".$id_kpi_header."
				AND hkd.id_company = ? AND hkd.status = 'A'";
        $res = DB::select($sql, [session('id_company')]);
		$result = json_decode(json_encode($res),true);
        return $result;
    }
	
	public static function get_kpi_view($data) {
		$id_kpi_group = $data['id_kpi_group'];
		$id_company = $data['id_company'];		
		$sql = "SELECT j_k.id_kpi_category, j_k.item_kpi, j_k.kpi_desc, j_k.type_kpi, 
				STRING_AGG(j_k.jan_weight::character varying,'') AS jan_weight, STRING_AGG(j_k.jan_target::character varying,'') AS jan_target, STRING_AGG(j_k.jan_value::character varying,'') AS jan_value,
				STRING_AGG(j_k.feb_weight::character varying,'') AS feb_weight, STRING_AGG(j_k.feb_target::character varying,'') AS feb_target, STRING_AGG(j_k.feb_value::character varying,'') AS feb_value,
				STRING_AGG(j_k.mar_weight::character varying,'') AS mar_weight, STRING_AGG(j_k.mar_target::character varying,'') AS mar_target, STRING_AGG(j_k.mar_value::character varying,'') AS mar_value,
				STRING_AGG(j_k.apr_weight::character varying,'') AS apr_weight, STRING_AGG(j_k.apr_target::character varying,'') AS apr_target, STRING_AGG(j_k.apr_value::character varying,'') AS apr_value,
				STRING_AGG(j_k.may_weight::character varying,'') AS may_weight, STRING_AGG(j_k.may_target::character varying,'') AS may_target, STRING_AGG(j_k.may_value::character varying,'') AS may_value,
				STRING_AGG(j_k.jun_weight::character varying,'') AS jun_weight, STRING_AGG(j_k.jun_target::character varying,'') AS jun_target, STRING_AGG(j_k.jun_value::character varying,'') AS jun_value,
				STRING_AGG(j_k.jul_weight::character varying,'') AS jul_weight, STRING_AGG(j_k.jul_target::character varying,'') AS jul_target, STRING_AGG(j_k.jul_value::character varying,'') AS jul_value,
				STRING_AGG(j_k.aug_weight::character varying,'') AS aug_weight, STRING_AGG(j_k.aug_target::character varying,'') AS aug_target, STRING_AGG(j_k.aug_value::character varying,'') AS aug_value,
				STRING_AGG(j_k.sep_weight::character varying,'') AS sep_weight, STRING_AGG(j_k.sep_target::character varying,'') AS sep_target, STRING_AGG(j_k.sep_value::character varying,'') AS sep_value,
				STRING_AGG(j_k.okt_weight::character varying,'') AS okt_weight, STRING_AGG(j_k.okt_target::character varying,'') AS okt_target, STRING_AGG(j_k.okt_value::character varying,'') AS okt_value,
				STRING_AGG(j_k.nov_weight::character varying,'') AS nov_weight, STRING_AGG(j_k.nov_target::character varying,'') AS nov_target, STRING_AGG(j_k.nov_value::character varying,'') AS nov_value,
				STRING_AGG(j_k.des_weight::character varying,'') AS des_weight, STRING_AGG(j_k.des_target::character varying,'') AS des_target, STRING_AGG(j_k.des_value::character varying,'') AS des_value
				FROM
				(
				SELECT mkc.id_kpi_category, mkc.description AS item_kpi, hkd.description AS kpi_desc, mgd.code AS type_kpi, 
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jan' THEN hkd.weight_prosentase END AS jan_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jan' THEN hkd.kpi_target_value END AS jan_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jan' THEN hkd.kpi_value END AS jan_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Feb' THEN hkd.weight_prosentase END AS feb_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Feb' THEN hkd.kpi_target_value END AS feb_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Feb' THEN hkd.kpi_value END AS feb_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Mar' THEN hkd.weight_prosentase END AS mar_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Mar' THEN hkd.kpi_target_value END AS mar_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Mar' THEN hkd.kpi_value END AS mar_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Apr' THEN hkd.weight_prosentase END AS apr_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Apr' THEN hkd.kpi_target_value END AS apr_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Apr' THEN hkd.kpi_value END AS apr_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'May' THEN hkd.weight_prosentase END AS may_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'May' THEN hkd.kpi_target_value END AS may_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'May' THEN hkd.kpi_value END AS may_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jun' THEN hkd.weight_prosentase END AS jun_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jun' THEN hkd.kpi_target_value END AS jun_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jun' THEN hkd.kpi_value END AS jun_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jul' THEN hkd.weight_prosentase END AS jul_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jul' THEN hkd.kpi_target_value END AS jul_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Jul' THEN hkd.kpi_value END AS jul_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Aug' THEN hkd.weight_prosentase END AS aug_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Aug' THEN hkd.kpi_target_value END AS aug_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Aug' THEN hkd.kpi_value END AS aug_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Sep' THEN hkd.weight_prosentase END AS sep_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Sep' THEN hkd.kpi_target_value END AS sep_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Sep' THEN hkd.kpi_value END AS sep_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Oct' THEN hkd.weight_prosentase END AS okt_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Oct' THEN hkd.kpi_target_value END AS okt_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Oct' THEN hkd.kpi_value END AS okt_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Nov' THEN hkd.weight_prosentase END AS nov_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Nov' THEN hkd.kpi_target_value END AS nov_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Nov' THEN hkd.kpi_value END AS nov_value,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Dec' THEN hkd.weight_prosentase END AS des_weight,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Dec' THEN hkd.kpi_target_value END AS des_target,
					CASE WHEN  TO_CHAR(hkh.kpi_month, 'Mon') = 'Dec' THEN hkd.kpi_value END AS des_value
				FROM hr_kpi_detail hkd
				JOIN master_kpi_category mkc
				ON hkd.id_kpi_category = mkc.id_kpi_category
				JOIN hr_kpi_header hkh
				ON hkd.id_kpi_header = hkh.id_kpi_header
				JOIN hr_kpi_group hkg
				ON hkh.id_kpi_group = hkg.id_kpi_group
				JOIN master_general_data mgd
				ON hkd.id_kpi_type = mgd.id_general_data
				JOIN master_general_data mgd2
				ON mkc.id_group_segment = mgd2.id_general_data
				WHERE hkh.id_kpi_group = ? AND mgd2.code = 'PA' AND
				hkd.id_company = ? AND hkd.status = 'A'
				) AS j_k
				GROUP BY j_k.id_kpi_category, j_k.item_kpi, j_k.kpi_desc, j_k.type_kpi
				ORDER BY jan_weight, feb_weight, mar_weight, apr_weight, may_weight, 
				jun_weight, jul_weight, aug_weight, sep_weight, okt_weight, nov_weight, des_weight ASC";
		$result = DB::select($sql, [$id_kpi_group,$id_company]);	
	//	dd($result);
		return $result;
		
	}
	
	public static function get_kpi_total($data) {
		$id_kpi_group = $data['id_kpi_group'];
		$id_company = $data['id_company'];		
		$sql = "SELECT j_month.id_kpi_group, STRING_AGG(j_month.jan::character varying,'') AS jan,
				STRING_AGG(j_month.feb::character varying,'') AS feb, STRING_AGG(j_month.mar::character varying,'') AS mar, 
				STRING_AGG(j_month.apr::character varying,'') AS apr, STRING_AGG(j_month.may::character varying,'') AS mei,
				STRING_AGG(j_month.jun::character varying,'') AS jun, STRING_AGG(j_month.jul::character varying,'') AS jul,
				STRING_AGG(j_month.aug::character varying,'') AS ags, STRING_AGG(j_month.sep::character varying,'') AS sep,
				STRING_AGG(j_month.okt::character varying,'') AS okt, STRING_AGG(j_month.nov::character varying,'') AS nov,
				STRING_AGG(j_month.des::character varying,'') AS des FROM
				(
				SELECT hkh.id_kpi_group, 
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
					WHERE hkh.id_kpi_group = ? AND hkh.id_company = ? AND hkh.status = 'A'
				) AS j_month
				GROUP BY j_month.id_kpi_group";
		$result = DB::select($sql, [$id_kpi_group,$id_company]);	
	//	dd($result);
		return $result;		
	}
	
	public static function get_item_kpi($id_kpi_header) {
        $result = [];
        $sql = "SELECT hkh.id_kpi_header,
				CASE 
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'January' THEN 'Januari'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'February' THEN 'Februari'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'March' THEN 'Maret'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'April' THEN 'April'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'May' THEN 'Mei'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'June' THEN 'Juni'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'July' THEN 'Juli'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'August' THEN 'Agustus'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'September' THEN 'September'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'October' THEN 'Oktober'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'November' THEN 'November'
					WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'December' THEN 'Desember'
				END AS month, hkh.id_kpi_group, hkh.kpi_month
				FROM hr_kpi_header hkh
				WHERE hkh.id_kpi_header = ? AND 
				hkh.id_company = ? AND hkh.status = 'A'";
        $result = (Array) DB::select($sql, [$id_kpi_header,session('id_company')])[0];
		
		$sql2 = "SELECT hkd.*
					FROM hr_kpi_detail hkd
					LEFT JOIN hr_kpi_header hkh
					ON hkd.id_kpi_header = hkh.id_kpi_header
				WHERE hkd.id_kpi_header = ? AND 
				hkd.id_company = ? AND hkd.status = 'A'";
		$result_menu2 = DB::select($sql2, [$id_kpi_header,session('id_company')]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_kpi_detail')->toArray();
		
		$result['itemkpi'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['itemkpi'][] = [
                'id_kpi_detail' => $group_menu2[$value][0]->id_kpi_detail,
                'id_kpi_category' => $group_menu2[$value][0]->id_kpi_category,
                'kpi_desc' => $group_menu2[$value][0]->description,
                'kpi_value' => $group_menu2[$value][0]->kpi_value,
                'weight' => $group_menu2[$value][0]->weight_prosentase,
                'id_kpi_type' => $group_menu2[$value][0]->id_kpi_type,
                'target' => $group_menu2[$value][0]->kpi_target_value,
                'status' => $group_menu2[$value][0]->status,
            ];
        }
        return $result;
    }
	
	public static function get_kpi_edit($id_kpi_group) {
        $result = [];
        $sql = "SELECT hkg.id_kpi_group, mp.description AS period,  hkg.id_employee, CONCAT(he.name,' (',he.nik_employee,')') AS employee, hkg.average_prosentase, hkg.description,
					mpr.description AS position, mjg.description AS grade, md.description AS dept
					FROM hr_kpi_group hkg
					JOIN hr_employee he
					ON hkg.id_employee = he.id_employee
					LEFT JOIN master_position_detail mpd
					ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2)
					LEFT JOIN master_position_routing mpr
					ON mpd.id_position_routing = mpr.id_routing
					LEFT JOIN master_job_grade mjg
					ON mpr.id_job_grade = mjg.id_job_grade
					LEFT JOIN master_job_position mjp
					ON mpr.id_position = mjp.id_position
					LEFT JOIN master_department md
					ON mjp.id_dept = md.id_dept
					JOIN master_period mp
					ON hkg.id_period = mp.id_period
				WHERE hkg.id_kpi_group = ? AND hkg.id_company = ? AND hkg.status = 'A'";
        $result = (Array) DB::select($sql, [$id_kpi_group,session('id_company')])[0];
	
		$sql2 = "SELECT hkh.id_kpi_header, hkh.id_kpi_group, 
					CASE 
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'January' THEN CONCAT('Januari ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'February' THEN CONCAT('Februari ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'March' THEN CONCAT('Maret ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'April' THEN CONCAT('April ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'May' THEN CONCAT('Mei ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'June' THEN CONCAT('Juni ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'July' THEN CONCAT('Juli ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'August' THEN CONCAT('Agustus ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'September' THEN CONCAT('September ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'October' THEN CONCAT('Oktober ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'November' THEN CONCAT('November ',TO_CHAR(hkh.kpi_month ,'YYYY'))
						WHEN TO_CHAR(hkh.kpi_month ,'fmMonth') = 'December' THEN CONCAT('Desember ',TO_CHAR(hkh.kpi_month ,'YYYY'))
					END AS month, 
					hkh.notes, hkh.subtotal_kpi, hkh.status
					FROM hr_kpi_header hkh
					JOIN hr_kpi_group hkg
					ON hkh.id_kpi_group = hkg.id_kpi_group
				WHERE hkg.id_kpi_group = ? AND hkh.id_company = ? AND hkh.status = 'A'
					ORDER BY hkh.kpi_month ASC";
        $result_menu2 = DB::select($sql2, [$id_kpi_group,session('id_company')]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_kpi_header')->toArray();
		
		$result['monthly'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['monthly'][] = (object)[
                'id_kpi_header' => $group_menu2[$value][0]->id_kpi_header,
                'month' => $group_menu2[$value][0]->month,
             //   'notes' => $group_menu2[$value][0]->notes,
                'subtotal_kpi' => $group_menu2[$value][0]->subtotal_kpi,
                'status' => $group_menu2[$value][0]->status,
            ];
			KpiHeader::where('id_kpi_header',$group_menu2[$value][0]->id_kpi_header)->update(array(
				'notes' => 'Score Total '.explode(" ",$group_menu2[$value][0]->month)[0],
			));
			$result['monthly'][count($result['monthly'])-1]->notes = 'Score Total '.explode(" ",$group_menu2[$value][0]->month)[0];
        }
        return $result;
    }

	public static function get_employee_atasan_bawahan($id_kpi_group) {
        $getEmployee =  DB::table('hr_kpi_group as hkg')
        	->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hkg.id_employee')
        	->leftJoin('hr_employee as he2', 'he2.id_employee', '=', 'hkg.id_employee_appraisers')
        	->select('he.nik_employee as nik_karyawan', 'he2.nik_employee as nik_atasan', 'he.name as nama_karyawan', 'he2.name as nama_atasan')
        	->where('hkg.id_kpi_group', $id_kpi_group)
        	->first();
        return $getEmployee;
	}
	
	public static function get_prog_detail($idheader) {
        $sql = "SELECT hkd.* 
					FROM hr_kpi_detail hkd
					LEFT JOIN master_general_data mgd
					ON hkd.id_kpi_type = mgd.id_general_data
					WHERE mgd.code = 'Progressif' AND hkd.id_kpi_header = ".$idheader;
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_emp_inactive($data) {
		$id_employee = $data['id_employee'];
		$id_kpi_group = $data['id_kpi_group'];
        $sql = "SELECT hkg.id_kpi_group id, CONCAT(he2.name,' (',he2.nik_employee,')') AS text 
				FROM hr_kpi_group hkg
				LEFT JOIN hr_employee he
				ON hkg.id_employee = he.id_employee AND he.status = 'A'
				LEFT JOIN hr_employee he2
				ON hkg.id_employee_appraisers = he2.id_employee AND he2.status = 'A'
				WHERE hkg.id_employee = ".$id_employee." AND hkg.id_kpi_group != ".$id_kpi_group." AND hkg.status = 'A'";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_mapping($id_kpi_group) {
		$idEmployee = Employee::where('id_user', session('id_user'))->where('status','A')->first();
        $sql = "SELECT sfpm.* FROM public.sp_funct_pa_mapping(null,".session('id_company').") sfpm
				WHERE sfpm.id_kpi_group = ".$id_kpi_group." AND (sfpm.id_employee_appraisers = ".$idEmployee['id_employee']." OR sfpm.id_employee = ".$idEmployee['id_employee'].")";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_mapping_all($id_kpi_group) {
        $sql = "SELECT sfpm.* FROM public.sp_funct_pa_mapping(null,".session('id_company').") sfpm
				WHERE sfpm.id_kpi_group = ".$id_kpi_group;
        $result = DB::select($sql);
        return $result;
    }
}
