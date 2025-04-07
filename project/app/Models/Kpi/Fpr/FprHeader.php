<?php

namespace App\Models\Kpi\Fpr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FprHeader extends Model
{
	use HasFactory;
	
    protected $table = 'hr_fpr_header';
	protected $primaryKey = 'id_fpr_header';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_fpr_header', 'id_kpi_group', 'id_period', 'id_approval_participant', 'id_approval_appraisers', 'id_qualitative_participant', 'is_end_year_period', 'notes', 'review_date', 'submitted', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getdata_report($id_employee,$period,$group_branch) {
		if($group_branch == null || $group_branch == ""){
			$where_branch = "";
		}	
		else{
			$where_branch = "mpd.id_branch in(".$group_branch.") AND ";
		}
        $sql = "SELECT hfh.id_fpr_header, hfh.id_kpi_group, hfh.id_period, mp.description AS period, hkg.id_employee, he.nik_employee, he.name, mpd.id_branch,
				hkg.id_employee_appraisers, he2.nik_employee AS nik_atasan, he2.name AS atasan, hfh.review_date,
				CASE
					WHEN hfh.id_approval_appraisers IS NOT NULL THEN 'YES'
					ELSE 'NO'
				END AS id_approval_appraisers,
				CASE
					WHEN hfh.id_approval_participant IS NOT NULL THEN 'YES'
					ELSE 'NO'
				END AS id_approval_participant,
				CASE
					WHEN hfh.submitted = 'true' THEN 'YES'
					ELSE 'NO'
				END AS submitted
				FROM hr_fpr_header hfh
				JOIN public.sp_funct_pa_mapping(null,".session('id_company').") hkg
			--	JOIN hr_kpi_group hkg
				ON hfh.id_kpi_group = hkg.id_kpi_group
				JOIN hr_employee he
				ON hkg.id_employee = he.id_employee AND he.status = 'A'
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				JOIN master_period mp
				ON hfh.id_period = mp.id_period 
				LEFT JOIN hr_employee he2
				ON hkg.id_employee_appraisers = he2.id_employee
				WHERE ".$where_branch." hfh.id_company = ".session('id_company')." AND hfh.status = 'A'
				ORDER BY he2.name ASC, he.name ASC";
			$get = DB::select($sql);
			$get_res = collect($get);
			if($id_employee != null){								
				if($period != null){
					$result = $get_res->whereIn('id_employee', $id_employee)->whereIn('id_period', $period);
				}
				else{
					$result = $get_res->whereIn('id_employee', $id_employee);
				}
			}
			else if($period != null){
				if($id_employee != null){
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
	
	public static function getdata($period,$path_param) {
		if($path_param == 'kpi/fpr/fpr_subordinate'){
			$where_url = "he2.id_user = ".session('id_user')." AND ";
		}
		else if ($path_param == 'kpi/fpr/fpr_self'){
			$where_url = "he.id_user = ".session('id_user')." AND ";
		}
		else{
			$where_url = "he.id_user = 0 AND ";
		}
		
        $sql = "SELECT hfh.id_fpr_header, hfh.id_kpi_group, hfh.id_period, mp.description AS period, he.nik_employee, he.name, mpd.id_branch,
				hkg.id_employee_appraisers, he2.nik_employee AS nik_atasan, he2.name AS atasan, hfh.review_date,
				CASE
					WHEN hfh.id_approval_appraisers IS NOT NULL THEN 'YES'
					ELSE 'NO'
				END AS id_approval_appraisers,
				CASE
					WHEN hfh.id_approval_participant IS NOT NULL THEN 'YES'
					ELSE 'NO'
				END AS id_approval_participant,
				CASE
					WHEN hfh.submitted = 'true' THEN 'YES'
					ELSE 'NO'
				END AS submitted
				FROM hr_fpr_header hfh
				JOIN public.sp_funct_pa_mapping(null,".session('id_company').") hkg
				ON hfh.id_kpi_group = hkg.id_kpi_group
				JOIN hr_employee he
				ON hkg.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				JOIN master_period mp
				ON hfh.id_period = mp.id_period 
				LEFT JOIN hr_employee he2
				ON hkg.id_employee_appraisers = he2.id_employee
				WHERE ".$where_url." hfh.id_company = ".session('id_company')." AND hfh.status = 'A'
				ORDER BY he2.name ASC, he.name ASC";
			$get = DB::select($sql);
			$get_res = collect($get);
			if($period != null){			
				$result = $get_res->whereIn('id_period', $period);
			}
			else{
				$result = $get_res;
			}
			return $result;
    }	
	
	public static function generate_fpr($data) {
		$id_employee = $data['id_employee'];
		if($id_employee == null){
			$id_employee = 'null';
		}
		$period = $data['period'];
		$id_company = $data['id_company'];
		$id_user = $data['id_user'];
		$sql = "INSERT INTO hr_fpr_header
                        (id_kpi_group, id_period, id_qualitative_participant, notes,
                        is_end_year_period, id_company, creation_date, created_by )
						SELECT	j_hkg.id_kpi_group,  ".$period." AS id_period, 
                        hqp.id_qualitative_participant,
                        mp2.description AS notes, 
                        CASE WHEN  
                              EXTRACT(MONTH FROM mp2.end_date) = 12 
                              THEN TRUE
                              ELSE FALSE
                        END AS is_end_year_period,
                        mpd.id_company AS id_company, now(), coalesce(".$id_user.",1) AS created_by
                FROM master_position_detail mpd           
				  JOIN master_period mp2
                  ON mpd.id_company = mp2.id_company
				  AND mp2.id_period = ".$period."				  
				  JOIN	(
				  	   SELECT max(hkg.id_kpi_group) AS id_kpi_group, hkg.id_employee, hkg.id_company, hkg.id_period 
		               FROM  hr_kpi_group hkg
		               WHERE hkg.id_company = ".$id_company." AND hkg.status = 'A'
		               GROUP BY hkg.id_employee, hkg.id_company, hkg.id_period 
		               ) AS j_hkg
		          ON (mpd.id_employee = j_hkg.id_employee OR mpd.id_employee2 = j_hkg.id_employee)
                 AND mpd.id_company = j_hkg.id_company     
                JOIN master_period mp
                  ON j_hkg.id_period = mp.id_period
                 AND j_hkg.id_company = mp.id_company
				AND mp.year = mp2.year
                LEFT JOIN (
                SELECT	hqp.id_qualitative_participant, hqp.id_company, 
                                    hqp.id_employee_participant,hqp.is_end_year_period
                              FROM hr_qualitative_participant hqp
                              JOIN master_period mp3
                                ON hqp.id_period = mp3.id_period
                               AND hqp.id_company = mp3.id_company
                               AND mp3.year = (select EXTRACT(YEAR FROM mp4.end_date) 
                               from master_period mp4 where id_period = ".$period.")
                          ) hqp
                  ON j_hkg.id_employee = hqp.id_employee_participant
                 AND j_hkg.id_company = hqp.id_company
                 AND hqp.is_end_year_period = TRUE
                 AND (false = TRUE or EXTRACT(MONTH FROM mp2.end_date) = 12)
                WHERE j_hkg.id_employee = coalesce(".$id_employee.",j_hkg.id_employee)
                  AND (mpd.id_company = coalesce(".$id_company.",mpd.id_company)
                      OR mpd.assigned_to_company = coalesce(".$id_company.",mpd.id_company)) AND mpd.secondary_position = false
                  AND concat(j_hkg.id_kpi_group,'-',".$period.") 
                  NOT IN (                  
                  SELECT concat(id_kpi_group, '-', id_period)
                                                                        FROM hr_fpr_header
                                                                        WHERE id_period = ".$period."
                                                                        AND id_company = ".$id_company."
                                                                        )";
		$result = DB::select($sql);	
	//	$result = DB::select("SELECT * FROM  SpGenerateFpr(?, ?, ?, ?, ?)",[$id_employee,$period,$id_company,$id_user,false]);		
		return $result;		
	}
	
	public static function get_period() {
        $sql = "SELECT mp.id_period id, CONCAT(mp.description) text
				FROM master_period mp
				JOIN master_general_data mgd
				ON mp.id_function_type = mgd.id_general_data
				WHERE mgd.code ='HR' AND mgd.description = 'HR-FPR' AND mp.status = 'A' AND mp.id_company = ?
				ORDER BY year DESC";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_title($id_employee) {
        $sql = "SELECT he.nik_employee, he.name, mpr.description AS jabatan, md.description AS dept, 
				mb.description AS branch, mr.description AS region
				FROM master_position_detail mpd
				LEFT JOIN hr_employee he
				ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)  AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_region mr
				ON mb.id_region = mr.id_region
				WHERE he.id_employee = ? AND mpd.id_company = ? AND he.status = 'A'";
        $res = DB::select($sql, [$id_employee,session('id_company')])[0];
		$result = json_decode(json_encode($res),true);
        return $result;
    }
	
	public static function get_fpr_new($data) {
        $result = [];
        $sql = "SELECT hpq.*, mgd2.code AS question_type 
				FROM hr_pa_question hpq
				LEFT JOIN master_general_data mgd
				ON	hpq.id_question_group = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hpq.id_question_type = mgd2.id_general_data
				WHERE hpq.question_pa_type  = 'QUANTITATIVE' AND hpq.id_company = ? AND mgd.code = 'FPR' AND hpq.notes = 'Yearly'
				ORDER BY sequence ASC";
        $result = (Array) DB::select($sql, [session('id_company')]);		
		foreach($result as $key=>$res){
			$sql2 = "SELECT * FROM hr_pa_answer where id_pa_question = ? and id_company = ? ORDER BY id_pa_answer ASC";
			$result_menu2 = DB::select($sql2, [$res->id_pa_question,session('id_company')]);
			$ans[$res->sequence]=[
				'id_pa_question' => $res->id_pa_question,
				'sequence_soal' => $res->sequence,
				'fpr_soal' => $res->description,
				'notes' => $res->notes,
				'id_fpr_header' => $data['id_fpr_header'],
				'question_type' => $res->question_type,
				'answer' => [],
			];					
			foreach ($result_menu2 as $key => $value) {
				$ans[$res->sequence]['answer'][] = [					
					'id_pa_answer' => $value->id_pa_answer,
					'sequence' => $value->sequence,
					'desc_answer' => $value->description,
				];
			}
		}
        return $ans;
    }
	
	public static function get_fpr_edit($data) {
        $sql = "SELECT hpq.sequence, hfd.* 
				FROM hr_fpr_detail hfd
				LEFT JOIN hr_pa_question hpq
				ON hfd.id_pa_question = hpq.id_pa_question
				WHERE hfd.id_fpr_header = ?
				ORDER BY hpq.sequence ASC";
        $res = (Array) DB::select($sql, [$data['id_fpr_header']]);
		foreach($res as $key=>$r){
		//	$sql2 = "SELECT * FROM hr_fpr_detail where id_fpr_header = ? AND id_pa_question = ? AND id_pa_answer IS NOT NULL";
		//	$result_menu2 = DB::select($sql2, [$data['id_fpr_header'],$r->id_pa_question]);
			$ans[$r->sequence] = [
				'id_fpr_detail' => $r->id_fpr_detail,
				'id_pa_question' => $r->id_pa_question,
				'id_pa_answer' => $r->id_pa_answer,
				'desc_answer' => $r->description_answer,
			//	'answer' => [],
			];			
		}
		$result = json_decode(json_encode($ans),true);
	//	dd($result);
        return $result;
    }
	
	public static function get_qualitative($id_qualitative_participant) {
        $sql = "SELECT hqa.id_qualitative_appraisers, hqa.id_employee_appraisers, CONCAT(he.name,' (',he.nik_employee,')') AS penilai, mp.description AS period, 
							hqa.appraisers_hierarchy, hqa.id_period, hqa.id_employee_participant, CONCAT(he2.name,' (',he2.nik_employee,')') AS dinilai, 
							hqa.subtotal_score, hqa.submitted, hqp.total_hit_score AS total_score, hqp.total_percent AS final_score, mpd.id_branch
					FROM hr_qualitative_appraisers hqa
					JOIN hr_employee he
					ON hqa.id_employee_appraisers = he.id_employee
					LEFT JOIN hr_employee he2
					ON hqa.id_employee_participant = he2.id_employee
					LEFT JOIN hr_qualitative_participant hqp
					ON hqa.id_employee_participant = hqp.id_employee_participant AND hqp.id_period = hqa.id_period AND hqp.transaction_type = 'PA'
					LEFT JOIN master_position_detail mpd
					ON (he2.id_employee = mpd.id_employee OR he2.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
					LEFT JOIN master_period mp
					ON hqa.id_period = mp.id_period 
					WHERE hqp.id_qualitative_participant = ? AND hqa.id_company = ? AND hqa.transaction_type = 'PA'
					ORDER BY he2.name ASC";
        $result = DB::select($sql, [$id_qualitative_participant,session('id_company')]);
        return $result;
    }
	
	public static function get_periodic_new($data) {
        $result = [];
        $sql = "SELECT hpq.*, mgd2.code AS question_type 
				FROM hr_pa_question hpq
				LEFT JOIN master_general_data mgd
				ON	hpq.id_question_group = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hpq.id_question_type = mgd2.id_general_data
				WHERE hpq.question_pa_type  = 'QUANTITATIVE' AND hpq.id_company = ? AND mgd.code = 'FPR' AND hpq.notes = 'Periodic'
				ORDER BY sequence ASC";
        $result = (Array) DB::select($sql, [session('id_company')]);		
		foreach($result as $key=>$res){
			$sql2 = "SELECT * FROM hr_pa_answer where id_pa_question = ? and id_company = ? ORDER BY id_pa_answer ASC";
			$result_menu2 = DB::select($sql2, [$res->id_pa_question,session('id_company')]);
			$ans[$res->sequence]=[
				'id_pa_question' => $res->id_pa_question,
				'sequence_soal' => $res->sequence,
				'fpr_soal' => $res->description,
				'notes' => $res->notes,
				'id_fpr_header' => $data['id_fpr_header'],
				'question_type' => $res->question_type,
				'answer' => [],
			];					
			foreach ($result_menu2 as $key => $value) {
				$ans[$res->sequence]['answer'][] = [					
					'id_pa_answer' => $value->id_pa_answer,
					'sequence' => $value->sequence,
					'desc_answer' => $value->description,
				];
			}
		}
        return $ans;
    }
	
	public static function get_quali_part($year,$idEmployee) {
        $sql = "SELECT hqp.id_qualitative_participant 
				FROM hr_qualitative_participant hqp
				JOIN master_period mp
				ON hqp.id_period = mp.id_period
				WHERE mp.year = '".$year."' AND hqp.id_employee_participant = ".$idEmployee." AND hqp.id_company = ".session('id_company')."
				AND hqp.transaction_type = 'PA' AND hqp.status = 'A'";
        $result = DB::select($sql);
        return $result;
    }

}