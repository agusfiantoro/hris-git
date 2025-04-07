<?php

namespace App\Models\Recruitment\Candidate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Candidate extends Model
{
	use HasFactory;
	
    protected $table = 'web.hr_applied_candidate';
	protected $primaryKey = 'id_applied_candidate';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_applied_candidate', 'id_candidate', 'id_hiring_request_header', 'applied_date', 'work_base', 'id_candidate_status', 'status', 'id_company', 'id_hiring_request_detail', 'created_by', 'updated_by'
    ];
	
	public function get_status_tracking($group_branch,$id_applied_candidate,$id_hiring_request_header) {
		if($id_hiring_request_header == null && $id_applied_candidate == null){
			$result = [];
		}
		else{
			if($id_hiring_request_header == null){
				$group_hiring = "";
			}
			else{
				$group_hiring = "hhrh.id_hiring_request_header = ".$id_hiring_request_header." AND ";
			}
			if($id_applied_candidate == null){
				$group_applied = "";
			}
			else{
				$group_applied = "hac.id_applied_candidate = ".$id_applied_candidate." AND ";
			}
			if($group_branch == null){
				$branch = "";
			}
			else{
				$branch = " AND hhrh.id_branch in(".$group_branch.")";
			}
			$sql = "SELECT rec_status.color, rec_status.title, SUM(rec_status.total) AS total FROM (
					SELECT mgd.sequence,  mgd.code AS color, mgd.description AS title,
									CASE 
										WHEN ".$group_applied." ".$group_hiring." (mgd.code = 'DRF' OR mgd.code = 'SHL' OR mgd.code = 'PRE' 
											  OR mgd.code = 'PSY' OR mgd.code = 'BEI' OR mgd.code = 'OBS' OR mgd.code = 'REF' 
											  ) ".$branch."
											  THEN count(hac.id_candidate_status)
										ELSE 0
									END AS total
									FROM public.master_general_data mgd
									LEFT JOIN public.master_general_type mgt
									ON mgd.id_general_type = mgt.id_general_type
									LEFT JOIN web.hr_applied_candidate hac
									ON mgd.id_general_data = hac.id_candidate_status
									LEFT JOIN public.hr_hiring_request_header hhrh
									ON hac.id_hiring_request_header = hhrh.id_hiring_request_header 
									WHERE mgt.general_type = 'master_recruitment_stage' AND mgd.code NOT IN('DBS','OFL','HIR','CLJ') AND mgd.id_company = " . session('id_company'). "
									GROUP BY mgd.code, mgd.description, mgd.sequence, hac.id_candidate_status, hhrh.id_branch, hac.id_applied_candidate, hhrh.id_hiring_request_header
									ORDER BY mgd.sequence ASC								
					) AS rec_status			
					GROUP BY rec_status.sequence, rec_status.color, rec_status.title
					ORDER BY rec_status.sequence ASC";
			$result = DB::select($sql);		
		}
        return $result;
    }
	
	public function get_tracking($group_branch,$id_applied_candidate,$id_hiring_request_header) {
		if($id_hiring_request_header == null && $id_applied_candidate == null){
			$result = [];
		}
		else{
			if($id_hiring_request_header == null){
				$group_hiring = "";
			}
			else{
				$group_hiring = "hhrh.id_hiring_request_header = ".$id_hiring_request_header." AND ";
			}
			if($id_applied_candidate == null){
				$group_applied = "";
			}
			else{
				$group_applied = "hac.id_applied_candidate = ".$id_applied_candidate." AND ";
			}
			if($group_branch == null){
				$branch = "";
			}
			else{
				$branch = " AND hhrh.id_branch in(".$group_branch.")";
			}
			 $sql = "SELECT j_c.*, pmb.id_batch FROM (
					SELECT hac.id_applied_candidate AS id, hc.name AS title, mpr.description, hc.id_candidate, hc.photo_candidate, hec.major, hac.update_date, 
					mgd2.code AS level, mgd.code AS position, ex.company_name, ex.position_name, ex.start_year, mjp.id_dept, mpr.id_job_grade,
					CASE 
						WHEN ex.end_year IS NOT NULL THEN ex.end_year
						ELSE now()::date
					END AS end_year, hhrh.id_branch, mb.description AS branch,
					false AS priority, hac.status
					FROM web.hr_applied_candidate hac
					LEFT JOIN web.hr_candidate hc
					ON hac.id_candidate = hc.id_candidate
					LEFT JOIN public.hr_hiring_request_header hhrh
					ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
					LEFT JOIN public.master_position_routing mpr
					ON hhrh.id_position_routing_request = mpr.id_routing
					LEFT JOIN public.master_job_position mjp
					ON mpr.id_position = mjp.id_position
					LEFT JOIN public.master_general_data mgd
					ON hac.id_candidate_status = mgd.id_general_data
					LEFT JOIN web.hr_education_candidate hec
					ON hc.id_candidate = hec.id_candidate
					LEFT JOIN public.master_branch mb
					ON hhrh.id_branch = mb.id_branch
					LEFT JOIN public.master_general_data mgd2
					ON hec.id_education_level = mgd2.id_general_data
					LEFT JOIN (
						SELECT hexc2.* FROM web.hr_experience_candidate hexc2
						JOIN (
							SELECT hexc.id_candidate, max(hexc.start_year) AS max_start_year
							FROM web.hr_experience_candidate hexc
							GROUP BY id_candidate
						) AS exs
						ON hexc2.id_candidate = exs.id_candidate AND hexc2.start_year = exs.max_start_year
					) AS ex
					ON hc.id_candidate = ex.id_candidate
					WHERE ".$group_applied." ".$group_hiring." hac.id_company = " . session('id_company'). $branch. "
					) AS j_c
					LEFT JOIN public.psycho_batch_participant pbp
					ON j_c.id_candidate = pbp.id_candidate AND pbp.status = 'A'
					LEFT JOIN public.psycho_master_batch pmb
					ON pbp.id_batch = pmb.id_batch		
					ORDER BY j_c.update_date DESC";
			$result = DB::select($sql);		
		}
        return $result;
    }
	
	public function get_can_status($status) {   
		 $sql = "SELECT mgd.id_general_data AS id
				FROM master_general_data mgd
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_recruitment_stage' AND mgd.code = '".$status."' AND mgd.id_company = " . session('id_company');
        $result = DB::select($sql)[0];		 
        return $result;
    }
	
	public function get_total($color,$group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND hhrh.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT rec_status.color, SUM(rec_status.total) AS total FROM (
					SELECT mgd.sequence, mgd.code AS color,
					CASE 
						WHEN (mgd.code = 'DRF' OR mgd.code = 'SHL' OR mgd.code = 'PRE' 
							  OR mgd.code = 'PSY'  OR mgd.code = 'BEI' OR mgd.code = 'OBS' OR mgd.code = 'REF' 
							  OR mgd.code = 'OFL' OR mgd.code = 'HIR') ".$branch."
							  THEN count(hac.id_candidate_status)
						ELSE 0
					END AS total
					FROM public.master_general_data mgd
					LEFT JOIN public.master_general_type mgt
					ON mgd.id_general_type = mgt.id_general_type
					LEFT JOIN web.hr_applied_candidate hac
					ON mgd.id_general_data = hac.id_candidate_status
					LEFT JOIN public.hr_hiring_request_header hhrh
					ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
					WHERE mgt.general_type = 'master_recruitment_stage' AND mgd.code= '".$color."' AND mgd.id_company = ".session('id_company'). "
					GROUP BY mgd.code, mgd.sequence, hac.id_candidate_status, hhrh.id_branch
					ORDER BY mgd.sequence ASC
				) AS rec_status			
				GROUP BY rec_status.sequence, rec_status.color
				ORDER BY rec_status.sequence ASC" ;
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function filter_candidate($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND hhrh.id_branch in(".$group_branch.")";
		}
        $sql = "SELECT hac.id_applied_candidate id, CONCAT(hc.name,' (',mpr.description,' - ',mb.description,')') text,
				hhrh.id_branch
				FROM web.hr_applied_candidate hac
				LEFT JOIN web.hr_candidate hc
				ON hac.id_candidate = hc.id_candidate
				LEFT JOIN public.hr_hiring_request_header hhrh
				ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON hhrh.id_branch = mb.id_branch
				LEFT JOIN public.master_general_data mgd
				ON hac.id_candidate_status = mgd.id_general_data
				WHERE mgd.code NOT IN('DBS','OFL','HIR','CLJ') AND hac.id_company = ".session('id_company'). $branch."
				ORDER BY hc.name ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function filter_job($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND hhrh.id_branch in(".$group_branch.")";
		}
        $sql = "SELECT hhrh.id_hiring_request_header id, CONCAT(hhrh.reference_number,' (',mpr.description,' - ',mb.description,')') text,
				hhrh.id_branch
				FROM web.hr_applied_candidate hac
				LEFT JOIN web.hr_candidate hc
				ON hac.id_candidate = hc.id_candidate
				LEFT JOIN public.hr_hiring_request_header hhrh
				ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON hhrh.id_branch = mb.id_branch
				LEFT JOIN public.master_general_data mgd
				ON hac.id_candidate_status = mgd.id_general_data
				WHERE mgd.code NOT IN('DBS','OFL','HIR','CLJ') AND hac.id_company = ".session('id_company'). $branch."
				GROUP BY hhrh.id_hiring_request_header, hhrh.reference_number, mpr.description, mb.description
				ORDER BY text ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
		
}
