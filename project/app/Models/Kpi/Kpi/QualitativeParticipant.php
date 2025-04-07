<?php

namespace App\Models\Kpi\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QualitativeParticipant extends Model
{
	use HasFactory;
	
    protected $table = 'hr_qualitative_participant';
	protected $primaryKey = 'id_qualitative_participant';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_qualitative_participant', 'id_period', 'id_employee_participant', 'total_hit_score', 'total_maximum_score', 'total_appraisers', 'total_percent', 'submitted', 'is_end_year_period', 'status', 'id_company', 'transaction_type', 'id_recommendation_header', 'created_by', 'updated_by'
    ];

	public static function getdata($period,$id_employee,$group_branch) {
		if($group_branch == null || $group_branch == ""){
			$where_branch = "";
		}	
		else{
			$where_branch = "mpd.id_branch in(".$group_branch.") AND ";
		}
        $sql = "SELECT hqp.id_qualitative_participant, hqp.id_employee_participant, he.nik_employee AS nik_dinilai, he.name AS name_dinilai,  
					hqp.id_period, mp.description as period, STRING_AGG(CONCAT(he2.name,'-',hqa.submitted),'|' ORDER BY hqa.submitted DESC, he2.name ASC) AS penilai,
					mpd.id_branch
					FROM hr_qualitative_participant hqp
					JOIN master_position_detail mpd
					ON (hqp.id_employee_participant = mpd.id_employee OR hqp.id_employee_participant = mpd.id_employee2) AND mpd.secondary_position = false
					LEFT JOIN hr_qualitative_appraisers hqa
					ON hqp.id_employee_participant = hqa.id_employee_participant AND hqp.id_period = hqa.id_period AND hqa.status = 'A'
					JOIN hr_employee he
					ON hqp.id_employee_participant = he.id_employee AND he.status = 'A'
					JOIN hr_employee he2
					ON hqa.id_employee_appraisers  = he2.id_employee AND he2.status = 'A'
					JOIN master_period mp
					ON hqp.id_period = mp.id_period
					WHERE ".$where_branch." hqp.status = 'A' AND hqp.id_company = ".session('id_company')."
					GROUP BY hqp.id_qualitative_participant, hqp.id_employee_participant, he.nik_employee, he.name, mp.description, mpd.id_branch
					ORDER BY he.name ASC";
			$get = DB::select($sql);
			$get_res = collect($get);
			if(count($id_employee) > 0){								
				if($period != null){
					$result = $get_res->whereIn('id_employee_participant', $id_employee)->whereIn('id_period', $period);
				}
				else{
					$result = $get_res->whereIn('id_employee_participant', $id_employee);
				}
			}
			else if($period != null){
				if(count($id_employee) > 0){
					$result = $get_res->whereIn('id_employee_participant', $id_employee)->whereIn('id_period', $period);
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
	
	public static function get_report($id_employee,$period,$group_branch) {
			if($group_branch == null || $group_branch == ""){
				$where_branch = "";
			}	
			else{
				$where_branch = "mpd.id_branch in(".$group_branch.") AND ";
			}
			$sql = "SELECT hqa.id_qualitative_appraisers, hqa.id_employee_appraisers, CONCAT(he.name,' (',he.nik_employee,')') AS penilai, mp.description AS period, 
							hqa.appraisers_hierarchy, hqa.id_period, hqa.id_employee_participant, CONCAT(he2.name,' (',he2.nik_employee,')') AS dinilai, 
							hqa.subtotal_score, hqa.submitted, hqp.total_hit_score AS total_score, hqp.total_percent AS final_score, mpd.id_branch
					FROM hr_qualitative_appraisers hqa
					JOIN hr_employee he
					ON hqa.id_employee_appraisers = he.id_employee
					LEFT JOIN hr_employee he2
					ON hqa.id_employee_participant = he2.id_employee
					LEFT JOIN hr_qualitative_participant hqp
					ON hqa.id_employee_participant = hqp.id_employee_participant AND hqp.id_period = hqa.id_period
					LEFT JOIN master_position_detail mpd
					ON (he2.id_employee = mpd.id_employee OR he2.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
					LEFT JOIN master_period mp
					ON hqa.id_period = mp.id_period 
					WHERE ".$where_branch." hqa.id_company = ".session('id_company')."
					ORDER BY he2.name ASC";
			$get = DB::select($sql);
			$get_res = collect($get);
			if(count($id_employee) > 0){								
				if($period != null){
					$result = $get_res->whereIn('id_employee_participant', $id_employee)->whereIn('id_period', $period);
				}
				else{
					$result = $get_res->whereIn('id_employee_participant', $id_employee);
				}
			}
			else if($period != null){
				if(count($id_employee) > 0){
					$result = $get_res->whereIn('id_employee_participant', $id_employee)->whereIn('id_period', $period);
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
	
	public static function get_employee_filter($group_branch) {
		if($group_branch == null || $group_branch == ""){
			$where_branch = "";
		}	
		else{
			$where_branch = "mpd.id_branch in(".$group_branch.") AND ";
		}
        $sql = "SELECT he.id_employee id,
				CONCAT(he.name,' (',he.nik_employee,')') AS text,
				mpd.id_branch 
				FROM hr_employee he
				JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				WHERE ".$where_branch." he.status = 'A' AND mpd.id_company = ".session('id_company')."
				ORDER BY he.name ASC";
        $result = DB::select($sql);
        return $result;
    }
	
/*	public static function get_employee() {       
		 $sql = "SELECT mc.id_company, mc.company_name, he.id_employee, CONCAT(he.name,' (',he.nik_employee,')') AS name, mjg.description AS grade
				FROM master_company mc
				JOIN hr_employee he
				ON mc.id_company = he.id_company
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE he.status = 'A' AND mc.company_type ='corporate'
				ORDER BY he.name ASC";
        $result = DB::select($sql);
		 $result_list = DB::select($sql);
		$result = array();
        foreach ($result_list as $key=>$value) {
			 $result[$value->id_company -1]['id'] = $value->id_company;
			 $result[$value->id_company -1]['text'] = $value->company_name;		
			 $result[$value->id_company -1]['children'][] = [
				'id' => $value->id_employee,
				'text' => $value->name,
				'grade' => $value->grade,
			 ];			 
		}
	//	dd($result);
        return $result;
    }
*/
	
	public static function get_employee() {
        $sql = "SELECT he.id_employee id,
				CONCAT(he.name,' (',he.nik_employee,')') AS text,
				mjg.description AS grade,
				he.id_company
				FROM hr_employee he
				JOIN master_company mc
				ON he.id_company = mc.id_company
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE he.status = 'A'
				ORDER BY he.name ASC";
        $result = DB::select($sql);
        return $result;
    }
	public static function get_grade($data) {
		$id_employee = $data['id_employee'];
		 $sql = "SELECT he.id_employee,
				mjg.description AS text
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE he.status = 'A' AND he.id_employee = ".$id_employee;
        $result = DB::select($sql);
        return $result;
	}
	public static function get_period() {
        $sql = "SELECT mp.id_period id, CONCAT(mp.description) text
				FROM master_period mp
				JOIN master_general_data mgd
				ON mp.id_function_type = mgd.id_general_data
				WHERE mgd.code ='HR' AND mgd.description = 'HR-QUALITATIVE' AND mp.status = 'A' AND mp.id_company = ?
				ORDER BY id_period ASC";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_period_report() {
        $sql = "SELECT DISTINCT hqa.id_period id, mp.description AS text
				FROM hr_qualitative_appraisers hqa
				JOIN master_period mp
				ON hqa.id_period = mp.id_period 
				WHERE mp.id_company = ? AND hqa.status = 'A'
				ORDER BY hqa.id_period ASC";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_cross_company() {
        $sql = "SELECT mc.id_company id,
				mc.company_name  AS text
				FROM master_company mc
				WHERE mc.id_company != ? ";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_cross_company_emp($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT mc.id_company id,
				mc.company_name  AS text
				FROM hr_employee he
				JOIN master_company mc
				ON he.id_company = mc.id_company
				WHERE he.status = 'A' AND he.id_employee = ? AND mc.id_company != ? ";
        $result = DB::select($sql, [$id_employee,session('id_company')]);
        return $result;
    }
	
	public static function get_qualitative_edit($data) {
        $result = [];
        $sql = "SELECT hqp.id_employee_participant, hqp.id_period, he.nik_employee AS nik_dinilai, he.name AS name_dinilai, mjg.description AS grade_dinilai, 
				mpr.description AS position_dinilai, md.description AS depart_dinilai, mb.description AS branch_dinilai, mr.description AS region_dinilai
				FROM hr_qualitative_participant hqp
				JOIN hr_employee he
				ON hqp.id_employee_participant = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON (hqp.id_employee_participant = mpd.id_employee OR hqp.id_employee_participant = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_period mp
				ON hqp.id_period = mp.id_period
				WHERE hqp.id_employee_participant = ?  AND mp.status = 'A' AND hqp.transaction_type = 'PA'";
        $result = (Array) DB::select($sql, [$data['id_employee_participant']])[0];
       // 	dd($result);
		$sql2 = "SELECT hqa.*, mjg2.description AS grade_penilai 
				FROM hr_qualitative_appraisers hqa
				LEFT JOIN hr_employee he2
				ON hqa.id_employee_appraisers = he2.id_employee
				JOIN master_position_detail mpd2
				ON hqa.id_employee_appraisers = mpd2.id_employee AND mpd2.secondary_position = false
				LEFT JOIN master_position_routing mpr2
				ON mpd2.id_position_routing = mpr2.id_routing
				LEFT JOIN master_job_grade mjg2
				ON mpr2.id_job_grade = mjg2.id_job_grade
				LEFT JOIN master_period mp
				ON hqa.id_period = mp.id_period
				JOIN master_position_detail mpd3
				ON ((hqa.id_company = mpd3.id_company AND hqa.id_company = ".session('id_company').") OR hqa.id_cross_company = mpd3.id_company) AND hqa.id_employee_appraisers = mpd3.id_employee
				WHERE hqa.id_employee_participant = ? AND mp.status = 'A' AND hqa.transaction_type = 'PA'
				ORDER BY appraisers_hierarchy ASC";
        $result_menu2 = DB::select($sql2, [$data['id_employee_participant']]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_qualitative_appraisers')->toArray();
		
		$result['mapping'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['mapping'][] = [
                'id_qualitative_appraisers' => $group_menu2[$value][0]->id_qualitative_appraisers,
                'id_employee_appraisers' => $group_menu2[$value][0]->id_employee_appraisers,
                'grade_penilai' => $group_menu2[$value][0]->grade_penilai,
                'appraisers_hierarchy' => $group_menu2[$value][0]->appraisers_hierarchy,
                'subtotal_score' => $group_menu2[$value][0]->subtotal_score,
                'submitted' => $group_menu2[$value][0]->submitted,
                'id_cross_company' => $group_menu2[$value][0]->id_cross_company,
                'status' => $group_menu2[$value][0]->status,
            ];
        }
        return $result;
    }
	
	public static function generate_participant($emp_participant,$id_period) {
	//	$result = DB::select("select * from  spgeneratequalitativeappraisers (?,?,?,?)",[$emp_participant,$id_period,session('id_company'),session('id_user')]);	
		$sql="INSERT INTO hr_qualitative_participant
							(id_period,id_employee_participant,
                                   is_end_year_period, id_company,creation_date, created_by
							)
				SELECT  DISTINCT ".$id_period." as id_period,
                            mpd.id_employee as id_employee_participant, 
                            CASE WHEN  
                                   EXTRACT(MONTH FROM mp.end_date) = 12 
                                   THEN TRUE
                                   ELSE FALSE
                            END AS is_end_year_period,
                            mpd.id_company as id_company,now(), 1 as created_by
                    FROM master_position_detail mpd
					JOIN hr_employee he
                      ON mpd.id_employee = he.id_employee AND he.status = 'A' AND mpd.secondary_position = false
                     AND extract(year from age(current_date,join_date)*12) >= 11
                    JOIN master_general_data mgd
                      ON he.id_employment_status = mgd.id_general_data
                     AND mgd.code = 'Permanent'
                    JOIN master_period mp
                      ON mpd.id_company = mp.id_company
                     AND mp.id_period = ".$id_period."
                    WHERE (mpd.id_employee = coalesce(".$emp_participant.",mpd.id_employee)
                           OR mpd.id_employee2 = coalesce(".$emp_participant.",mpd.id_employee2))
                      AND mpd.id_company = coalesce(".session('id_company').",mpd.id_company)
                      AND mpd.id_employee is not null
                      AND mpd.status = 'A'
					  AND mpd.assigned_to_company IS NULL
                   --   AND mpd.assigned_to_company IN(                      
                    --  	SELECT mc.id_company FROM master_company mc
                    --  	WHERE mc.company_type != 'os'                      
                   --   )
                      AND concat(mpd.id_employee,'-', ".$id_period.") not in (select concat(id_employee_participant,'-', id_period)
           													 from hr_qualitative_participant
                                                                            where id_period = ".$id_period."
                                                                            and id_company = ".session('id_company').")";
																			
        $result = DB::select($sql);
		return $result;
	}
	
	public static function generate_appraiser($emp_participant,$id_period) {
		$sql="INSERT INTO hr_qualitative_appraisers
                            (id_employee_appraisers, id_period, id_employee_participant, appraisers_hierarchy,
                            id_company, creation_date, created_by )
                    SELECT * 
                    FROM   (
                              SELECT  mpd2.id_employee as id_employee_appraisers, ".$id_period." as id_period,
                                   mpd.id_employee as id_employee_participant, 
                                   'direct'::character varying as appraisers_hierarchy, 
                                   mpd.id_company as id_company, now(), coalesce(".session('id_user').",1) as created_by
                              FROM master_position_detail mpd
                              LEFT JOIN	master_position_detail mpd2
                              ON mpd.parent_id_position_detail = mpd2.id_position_detail AND mpd2.status = 'A'
							  JOIN hr_employee he
		                      ON mpd.id_employee = he.id_employee AND he.status = 'A' AND extract(year from age(current_date,join_date)*12) >= 11 
							--  AND mpd.secondary_position = false
		                      JOIN master_general_data mgd
		                      ON he.id_employment_status = mgd.id_general_data
		                      AND mgd.code = 'Permanent'
                              WHERE (mpd.id_employee = coalesce(".$emp_participant.",mpd.id_employee)
                                   OR mpd.id_employee2 = coalesce(".$emp_participant.",mpd.id_employee2))
                              AND mpd.id_company = coalesce(".session('id_company').",mpd.id_company)
                              AND mpd.id_employee is not null
                              AND mpd2.id_employee is not null
                              AND mpd.status = 'A'
							UNION
                              SELECT  mpd3.id_employee as id_employee_appraisers, ".$id_period." as id_period,
                                   mpd.id_employee as id_employee_participant, 
                                   'direct'::character varying as appraisers_hierarchy, 
                                   mpd.id_company as id_company, now(), coalesce(".session('id_user').",1) as created_by
                              FROM master_position_detail mpd
                              LEFT JOIN	master_position_detail mpd2
                              ON mpd.parent_id_position_detail = mpd2.id_position_detail AND mpd2.status = 'A'
                              LEFT JOIN master_position_detail mpd3
                              ON mpd2.parent_id_position_detail = mpd3.id_position_detail AND mpd3.status = 'A'
		                      LEFT JOIN hr_employee he2
		                      ON mpd.id_employee = he2.id_employee AND he2.status = 'A' AND extract(year from age(current_date,he2.join_date)*12) >= 11 
							--  AND mpd2.secondary_position = false
		                      JOIN master_general_data mgd
		                      ON he2.id_employment_status = mgd.id_general_data
		                      AND mgd.code = 'Permanent'
                              WHERE (mpd.id_employee = coalesce(".$emp_participant.",mpd.id_employee)
                                   OR mpd.id_employee2 = coalesce(".$emp_participant.",mpd.id_employee2))
                              AND mpd.id_company = coalesce(".session('id_company').",mpd.id_company)
                              AND mpd2.id_employee IS NULL
							  AND mpd3.id_employee IS NOT NULL
                              AND mpd.status = 'A'
                            UNION
                              SELECT  mpd4.id_employee as id_employee_appraisers, ".$id_period." as id_period,
                                   mpd.id_employee as id_employee_participant, 
                                   'direct'::character varying as appraisers_hierarchy, 
                                   mpd.id_company as id_company, now(), coalesce(".session('id_user').",1) as created_by
                              FROM master_position_detail mpd
                              LEFT JOIN	master_position_detail mpd2
                              ON mpd.parent_id_position_detail = mpd2.id_position_detail AND mpd2.status = 'A'
                              LEFT JOIN master_position_detail mpd3
                              ON mpd2.parent_id_position_detail = mpd3.id_position_detail AND mpd3.status = 'A'
                              LEFT JOIN master_position_detail mpd4
                              ON mpd3.parent_id_position_detail = mpd4.id_position_detail AND mpd4.status = 'A'
		                      JOIN hr_employee he3
		                      ON mpd.id_employee = he3.id_employee AND he3.status = 'A' AND extract(year from age(current_date,he3.join_date)*12) >= 11 
							--  AND mpd3.secondary_position = false
		                      JOIN master_general_data mgd
		                      ON he3.id_employment_status = mgd.id_general_data
		                      AND mgd.code = 'Permanent'
                              WHERE (mpd.id_employee = coalesce(".$emp_participant.",mpd.id_employee)
                                   OR mpd.id_employee2 = coalesce(".$emp_participant.",mpd.id_employee2))
                              AND mpd.id_company = coalesce(".session('id_company').",mpd.id_company)
                              AND mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL AND mpd4.id_employee IS NOT NULL
                              AND mpd.status = 'A'
							UNION  
                              SELECT  mpd2.id_employee as id_employee_appraisers, ".$id_period." as id_period,
                                   mpd.id_employee as id_employee_participant, 
                                   'subordinat'::character varying as appraisers_hierarchy, 
                                   mpd.id_company as id_company, now(), coalesce(".session('id_user').",1) as created_by
                              FROM master_position_detail mpd
                              LEFT JOIN	master_position_detail mpd2
                              ON mpd2.parent_id_position_detail =  mpd.id_position_detail
							  JOIN hr_employee he
		                      ON mpd.id_employee = he.id_employee AND he.status = 'A' AND extract(year from age(current_date,join_date)*12) >= 11 
							--  AND mpd.secondary_position = false
		                      JOIN master_general_data mgd
		                      ON he.id_employment_status = mgd.id_general_data
		                      AND mgd.code = 'Permanent'
                              WHERE (mpd.id_employee = coalesce(".$emp_participant.",mpd.id_employee)
                                   OR mpd.id_employee2 = coalesce(".$emp_participant.",mpd.id_employee2))
                              AND mpd.id_company = coalesce(".session('id_company').",mpd.id_company)
                              AND mpd2.id_employee is not null
                              AND mpd.id_employee is not null
                              AND mpd.status = 'A'
                              UNION
                              SELECT  mpd.id_employee as id_employee_appraisers, ".$id_period." as id_period,
                                   mpd.id_employee as id_employee_participant, 
                                   'self'::character varying as appraisers_hierarchy, 
                                   mpd.id_company as id_company, now(), coalesce(".session('id_user').",1) as created_by
                              FROM master_position_detail mpd
							  JOIN hr_employee he
		                      ON mpd.id_employee = he.id_employee AND he.status = 'A' AND extract(year from age(current_date,join_date)*12) >= 11 
							--  AND mpd.secondary_position = false
		                      JOIN master_general_data mgd
		                      ON he.id_employment_status = mgd.id_general_data
		                      AND mgd.code = 'Permanent'
                              WHERE (mpd.id_employee = coalesce(".$emp_participant.",mpd.id_employee)
                                   OR mpd.id_employee2 = coalesce(".$emp_participant.",mpd.id_employee2))
                              AND mpd.id_company = coalesce(".session('id_company').",mpd.id_company)
                              AND mpd.id_employee is not null
                              AND mpd.status = 'A'
                           ) as a
                    WHERE concat(a.id_employee_appraisers,'-',".$id_period.",'-',a.id_employee_participant) not in  (select concat(id_employee_appraisers,'-', id_period,'-', id_employee_participant)
                                                                                                              from hr_qualitative_appraisers
                                                                                                              where id_period = ".$id_period."
                                                                                                              and id_company = ".session('id_company').")";
		$result = DB::select($sql);
		return $result;
	}
}
