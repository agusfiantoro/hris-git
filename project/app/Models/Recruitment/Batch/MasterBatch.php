<?php

namespace App\Models\Recruitment\Batch;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\Organization\MasterOrganization\MasterBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterBatch extends Model
{
	use HasFactory;
	
    protected $table = 'psycho_master_batch';
	protected $primaryKey = 'id_batch';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_batch', 'batch_code', 'batch_name', 'location', 'start_date', 'end_date', 'id_region', 'id_branch', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getkode($idBranch){
	
		$idCompany = session('id_company');
	//	$em = Employee::where('id_user', session('id_user'))->where('status','A')->where('id_company', $idCompany)->first();
	//	$jobdetail = JobPositionDetail::where('id_employee', $em->id_employee)->where('status','A')->where('secondary_position',false)->where('id_company', $idCompany)->first();
		$branch = MasterBranch::where('id_branch', $idBranch)->where('id_company', $idCompany)->first();
		$branch_code = $branch->branch_code;
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', $idCompany)->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-'.$branch_code.'-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('batch_code', 'LIKE', "%{$branch_code}-{$monthyear}%")->max('batch_code'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('batch_code', 'LIKE', "%{$branch_code}-{$monthyear}%")->max('batch_code'), 15, 21);
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
	
	public static function getdata($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT
				CASE 
					WHEN j_detail.count_participant IS NOT NULL THEN j_detail.count_participant
					ELSE 0
				END AS count_participant,
				pmb.* FROM psycho_master_batch pmb
				LEFT JOIN (
					SELECT pbp.id_batch, count(pbp.id_batch) AS count_participant
					FROM psycho_batch_participant pbp 
					GROUP BY id_batch
				) AS j_detail
				ON pmb.id_batch = j_detail.id_batch
				WHERE ".$branch." pmb.id_company = ?
				ORDER BY pmb.id_batch DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_participant($data,$group_branch) {
		$id_source_type = $data['id_source_type'];
		if($group_branch == null){
			$branch = "";
			$branch_hrbp = "";
		}
		else{
			$branch = " mpd.id_branch IN(".$group_branch.") AND ";
			$branch_hrbp = " (pmb.id_branch IN(".$group_branch.") OR hhrh.id_branch IN(".$group_branch.")) AND";
		}
		if($id_source_type == 'Internal'){
			$sql = "SELECT he2.id_employee id, CONCAT(he2.name,' (',he2.nik_employee,')') text, mpd2.id_position_routing 
					FROM master_position_detail mpd2
					JOIN hr_employee he2
					ON mpd2.id_employee = he2.id_employee
					LEFT JOIN(
						SELECT he.id_employee,
						CASE
							WHEN cast(now() as date) > pgp.expired_date THEN 1
							WHEN pbp.id_employee IS NULL THEN 1
							ELSE 0
						END AS tampil
						FROM master_position_detail mpd
						JOIN hr_employee he
						ON mpd.id_employee = he.id_employee
						LEFT JOIN web.psycho_group_psychotest pgp
						ON he.id_employee = pgp.id_employee AND cast(now() as date) > pgp.expired_date
						LEFT JOIN public.psycho_batch_participant pbp
						ON he.id_employee = pbp.id_employee
						AND pbp.status = 'A'
						WHERE ".$branch."  he.status = 'A' AND he.id_company = ".session('id_company')." AND mpd.secondary_position = 'false'
					) AS cek
					ON he2.id_employee = cek.id_employee
					WHERE he2.status = 'A' AND mpd2.secondary_position = 'false' AND cek.tampil = 1
					ORDER BY he2.name ASC";
			}
		else if($id_source_type == 'External'){
			$sql = "SELECT hc2.id_candidate id, CONCAT(hc2.name,' (',hc2.identification_number,')') text
					FROM web.hr_candidate hc2
					LEFT JOIN (						
						SELECT hc.id_candidate,
						CASE
							WHEN cast(now() as date) > pgp.expired_date THEN 1
							WHEN pbp.id_candidate IS NULL THEN 1
							ELSE 0
						END AS tampil, mpr.description AS routing, mb.description AS branch
						FROM web.hr_candidate hc
						LEFT JOIN web.hr_applied_candidate hac
						ON hc.id_candidate = hac.id_candidate 
						LEFT JOIN public.hr_hiring_request_header hhrh
						ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
						LEFT JOIN public.master_position_routing mpr
						ON hhrh.id_position_routing_request = mpr.id_routing
						LEFT JOIN public.master_branch mb
						ON hhrh.id_branch = mb.id_branch 
						LEFT JOIN web.psycho_group_psychotest pgp
						ON hc.id_candidate = pgp.id_candidate AND cast(now() as date) > pgp.expired_date
						LEFT JOIN public.psycho_batch_participant pbp
						ON hc.id_candidate = pbp.id_candidate
						AND pbp.status = 'A'
						LEFT JOIN public.psycho_master_batch pmb
						ON pbp.id_batch = pmb.id_batch
						WHERE ".$branch_hrbp." hc.status = 'A'
					) AS cek
					ON hc2.id_candidate = cek.id_candidate
					WHERE cek.tampil = 1 AND hc2.is_profil_completed = 'true'
					GROUP BY id, text
					ORDER BY hc2.name ASC";
			}			
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_participant_edit($data,$group_branch) {
		$id_source_type = $data['id_source_type'];
		$id_employee = $data['id_employee'];
		$id_candidate = $data['id_candidate'];
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch IN(".$group_branch.") ";
		}
		if($id_source_type == 'Internal'){
			$sql = "SELECT he.id_employee id, CONCAT(he.name,' (',he.nik_employee,')') text, pmb.id_position_routing 
				FROM master_position_detail pmb
				JOIN hr_employee he
				ON pmb.id_employee = he.id_employee
				WHERE he.id_employee = ".$id_employee." AND he.status = 'A' AND he.id_company = ".session('id_company')." AND pmb.secondary_position = 'false'						
			UNION			
				SELECT 	he.id_employee id, CONCAT(he.name,' (',he.nik_employee,')') text, pmb.id_position_routing 
				FROM hr_employee he
				JOIN hr_career_transaction hct
				ON he.id_employee = hct.id_employee
				JOIN master_general_data mgd
				ON hct.id_transaction_type = mgd.id_general_data AND mgd.code = 'Termination'
				LEFT JOIN master_position_detail pmb
				ON hct.id_old_position_detail = pmb.id_position_detail
				WHERE he.id_employee = ".$id_employee." AND he.id_company = ".session('id_company')."			
				ORDER BY text ASC";
			}
		else if($id_source_type == 'External'){
			$sql = "SELECT hc.id_candidate id, CONCAT(hc.name,' (',hc.identification_number,')') text
					FROM public.psycho_batch_participant pbp
					LEFT JOIN web.hr_candidate hc
					ON pbp.id_candidate = hc.id_candidate
					LEFT JOIN web.hr_applied_candidate hac
					ON hc.id_candidate = hac.id_candidate 
					LEFT JOIN public.hr_hiring_request_header hhrh
					ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
					LEFT JOIN public.master_position_routing mpr
					ON hhrh.id_position_routing_request = mpr.id_routing
					LEFT JOIN public.master_branch mb
					ON hhrh.id_branch = mb.id_branch 
					WHERE pbp.id_candidate = ".$id_candidate."
					GROUP BY id, text
					ORDER BY text ASC";
			}			
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_batch_edit($data) {
        $result = [];
        $sql = "SELECT * FROM psycho_master_batch pmb
				WHERE pmb.id_batch   = ?";
        $result = (Array) DB::select($sql, [$data['id_batch']])[0];

        $sql_detail = "SELECT 
					CASE
						WHEN j_summary.status_disc = 1 OR j_summary.status_papi = 1 
							OR j_summary.status_kraepelin = 1 OR j_summary.status_bct = 1 THEN 1
						ELSE 0
					END AS summary, j_summary.* FROM (	
						SELECT 
						CASE
							WHEN pbp.id_candidate = pdrr.id_candidate OR pbp.id_employee = pdrr.id_employee THEN 1
							ELSE 0
						END AS status_disc,
						CASE
							WHEN pbp.id_candidate = j_papi.id_candidate OR pbp.id_employee = j_papi.id_employee THEN 1
							ELSE 0
						END AS status_papi,
						CASE
							WHEN pbp.id_candidate = j_kraepelin.id_candidate OR pbp.id_employee = j_kraepelin.id_employee THEN 1
							ELSE 0
						END AS status_kraepelin,
						CASE
							WHEN pbp.id_candidate = j_bct.id_candidate OR pbp.id_employee = j_bct.id_employee THEN 1
							ELSE 0
						END AS status_bct,pbp.*
						FROM public.psycho_master_batch pmb
						JOIN public.psycho_batch_participant pbp
						ON pmb.id_batch = pbp.id_batch
						LEFT JOIN web.psycho_disc_result_report pdrr
						ON pmb.id_batch = pdrr.id_batch AND (pbp.id_candidate = pdrr.id_candidate OR pbp.id_employee = pdrr.id_employee) 
						LEFT JOIN (
							SELECT pprr.id_candidate, pprr.id_employee, pprr.id_batch 
							FROM web.psycho_papicostick_result_report pprr
							WHERE pprr.id_batch = ".$data['id_batch']."
							GROUP BY pprr.id_candidate, pprr.id_employee, pprr.id_batch  
						) AS j_papi
						ON pmb.id_batch = j_papi.id_batch AND (pbp.id_candidate = j_papi.id_candidate OR pbp.id_employee = j_papi.id_employee)
						LEFT JOIN (
							SELECT pkau.id_candidate, pkau.id_employee, pkau.id_batch 
							FROM web.psycho_kraepelin_answer_user pkau
							WHERE pkau.id_batch = ".$data['id_batch']."
							GROUP BY pkau.id_candidate, pkau.id_employee, pkau.id_batch  
						) AS j_kraepelin
						ON pmb.id_batch = j_kraepelin.id_batch AND (pbp.id_candidate = j_kraepelin.id_candidate OR pbp.id_employee = j_kraepelin.id_employee) 
						LEFT JOIN (
							SELECT pbau.id_candidate, pbau.id_employee, pbau.id_batch 
							FROM web.psycho_bct_answer_user pbau
							WHERE pbau.id_batch = ".$data['id_batch']." AND pbau.subtest_name = 6
							GROUP BY pbau.id_candidate, pbau.id_employee, pbau.id_batch  
						) AS j_bct
						ON pmb.id_batch = j_bct.id_batch AND (pbp.id_candidate = j_bct.id_candidate OR pbp.id_employee = j_bct.id_employee)
						WHERE pmb.id_batch = ".$data['id_batch']."
					) AS j_summary";
        $result_menu = DB::select($sql_detail);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_batch_participant')->toArray();
		
        $result['participant'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['participant'][] = [
                'id_batch_participant' => $group_menu[$value][0]->id_batch_participant,
                'source_type' => $group_menu[$value][0]->source_type,
                'id_candidate' => $group_menu[$value][0]->id_candidate,
                'id_employee' => $group_menu[$value][0]->id_employee,
                'id_position_routing' => $group_menu[$value][0]->id_position_routing,
                'status_disc' => $group_menu[$value][0]->status_disc,
                'status_papi' => $group_menu[$value][0]->status_papi,
                'status_kraepelin' => $group_menu[$value][0]->status_kraepelin,
                'status_bct' => $group_menu[$value][0]->status_bct,
                'summary' => $group_menu[$value][0]->summary,
                'status' => $group_menu[$value][0]->status,
			];
        }
		if(array_key_exists('source_type', $data)) {
			$result = collect($result['participant']);
			$result = $result->where('id_batch_participant', '=', $data['id_batch_participant']);
			return $result->first();
		}
		
		
        return $result;
    }
	
	public static function get_region_batch($idRegion) {
		$sql = "SELECT mr.id_region id, mr.description text 
				FROM public.master_region mr
				WHERE mr.id_region = coalesce(?,mr.id_region) AND mr.id_company = ?";	
        $result = DB::select($sql,[$idRegion,session('id_company')]);
        return $result;
    }
	
	public static function get_branch_batch($group_branch) {
		if($group_branch == null){
			$branch = " FROM public.master_branch mb";
			$where = "";
			$pusat = "UNION
						SELECT mb.id_branch id, mb.description text 
				FROM hr_employee he
				LEFT JOIN public.master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch
				WHERE he.id_user = ".session('id_user')." AND mpd.id_company = ".session('id_company')." AND he.status = 'A'";
		}
		else{
			$branch = "FROM public.relation_branch_users rbu
				LEFT JOIN public.master_user_responsibility mur
				ON rbu.id_user_responsibility = mur.id_user_responsibility
				LEFT JOIN public.master_branch mb
				ON rbu.id_branch = mb.id_branch ";
			$where = " mur.id_user = ".session('id_user')." AND rbu.id_company = ".session('id_company')." AND";
			$pusat = "";
		}
		$sql = "SELECT DISTINCT mb.id_branch id, mb.description text 
				".$branch."
				WHERE ".$where." mb.id_company = ".session('id_company')."
				".$pusat."
				ORDER BY text ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_user_region() {
		$sql = "SELECT mr2.id_region
				FROM hr_employee he
				LEFT JOIN public.master_position_detail mpd
				ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) 
				AND he.id_company = mpd.id_company
				LEFT JOIN public.master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_region mr2
				ON mr.region_code = mr2.region_code
				WHERE he.id_user = ? AND he.status = 'A' AND mr2.id_company = ?";	
        $result = DB::select($sql,[session('id_user'),session('id_company')]);
        return $result;
    }
	public static function get_user_region_edit() {
		$sql = "SELECT mr.id_region id, mr.description text FROM public.master_region mr
				WHERE mr.id_company = ? ";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_review($group_branch,$source_type) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " pmb.id_branch in(".$group_branch.") AND ";
		}
		$sql = "SELECT pmb.id_batch, pmb.batch_name, pmb.start_date, 
				pmb.end_date, pmb.id_branch, pbp.id_batch_participant, pbp.source_type, 
				CASE 
					WHEN pbp.id_candidate IS NOT NULL THEN hc.name
					WHEN pbp.id_employee IS NOT NULL THEN he.name
				END AS participant_name,
				CASE 
					WHEN pbp.id_candidate IS NOT NULL THEN hc.identification_number
					WHEN pbp.id_employee IS NOT NULL THEN he.nik_employee
				END AS nik
				FROM psycho_batch_participant pbp
				JOIN psycho_master_batch pmb
				ON pbp.id_batch = pmb.id_batch AND pmb.id_company = ".session('id_company')."
				LEFT JOIN hr_employee he
				ON pbp.id_employee = he.id_employee
				LEFT JOIN web.hr_candidate hc
				ON pbp.id_candidate = hc.id_candidate
				WHERE ".$branch." pbp.source_type = '".$source_type."' AND pbp.id_company = ".session('id_company')."
				ORDER BY pmb.id_batch DESC";	

		$sql = "SELECT
					pmb.id_batch,
					pmb.batch_name,
					pmb.start_date, 
					pmb.end_date,
					pmb.id_branch,
					pbp.id_batch_participant,
					pbp.source_type, 
					pbp.id_candidate,
					pbp.id_employee,
					CASE
											WHEN pbp.id_candidate = pdrr.id_candidate
						OR pbp.id_employee = pdrr.id_employee THEN 1
						ELSE 0
					END AS status_disc,
										CASE
											WHEN pbp.id_candidate = j_papi.id_candidate
						OR pbp.id_employee = j_papi.id_employee THEN 1
						ELSE 0
					END AS status_papi,
										CASE
											WHEN pbp.id_candidate = j_kraepelin.id_candidate
						OR pbp.id_employee = j_kraepelin.id_employee THEN 1
						ELSE 0
					END AS status_kraepelin,
										CASE
											WHEN pbp.id_candidate = j_bct.id_candidate
						OR pbp.id_employee = j_bct.id_employee THEN 1
						ELSE 0
					END AS status_bct,
								CASE 
									WHEN pbp.id_candidate IS NOT NULL THEN hc.name
						WHEN pbp.id_employee IS NOT NULL THEN he.name
					END AS participant_name,
								CASE 
									WHEN pbp.id_candidate IS NOT NULL THEN hc.identification_number
						WHEN pbp.id_employee IS NOT NULL THEN he.nik_employee
					END AS nik
				FROM
					psycho_batch_participant pbp
				JOIN psycho_master_batch pmb
								ON
					pbp.id_batch = pmb.id_batch
					AND pmb.id_company = ".session('id_company')."
				LEFT JOIN hr_employee he
								ON
					pbp.id_employee = he.id_employee
				LEFT JOIN web.hr_candidate hc
								ON
					pbp.id_candidate = hc.id_candidate
				LEFT JOIN web.psycho_disc_result_report pdrr
										ON
					pmb.id_batch = pdrr.id_batch
					AND (pbp.id_candidate = pdrr.id_candidate
						OR pbp.id_employee = pdrr.id_employee)
				LEFT JOIN (
					SELECT
						pprr.id_candidate,
						pprr.id_employee,
						pprr.id_batch
					FROM
						web.psycho_papicostick_result_report pprr
					WHERE
						pprr.status = 'A'
					GROUP BY
						pprr.id_candidate,
						pprr.id_employee,
						pprr.id_batch
				) AS j_papi ON
					pmb.id_batch = j_papi.id_batch
					AND (pbp.id_candidate = j_papi.id_candidate
						OR pbp.id_employee = j_papi.id_employee)
				LEFT JOIN (
					SELECT
						pkau.id_candidate,
						pkau.id_employee,
						pkau.id_batch
					FROM
						web.psycho_kraepelin_answer_user pkau
					WHERE
						pkau.status = 'A'
					GROUP BY
						pkau.id_candidate,
						pkau.id_employee,
						pkau.id_batch  
										) AS j_kraepelin
										ON
					pmb.id_batch = j_kraepelin.id_batch
					AND (pbp.id_candidate = j_kraepelin.id_candidate
						OR pbp.id_employee = j_kraepelin.id_employee)
				LEFT JOIN (
					SELECT
						pbau.id_candidate,
						pbau.id_employee,
						pbau.id_batch
					FROM
						web.psycho_bct_answer_user pbau
					WHERE
						pbau.status = 'A'
						AND pbau.subtest_name = 6
					GROUP BY
						pbau.id_candidate,
						pbau.id_employee,
						pbau.id_batch  
										) AS j_bct
										ON
					pmb.id_batch = j_bct.id_batch
					AND (pbp.id_candidate = j_bct.id_candidate
						OR pbp.id_employee = j_bct.id_employee)
				WHERE
					".$branch."
					pbp.source_type = '".$source_type."'
					AND pbp.id_company = ".session('id_company')."
				ORDER BY
					pmb.id_batch DESC";
        $result = DB::select($sql);
        return $result;
    }
		
}
