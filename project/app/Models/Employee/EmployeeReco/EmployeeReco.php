<?php

namespace App\Models\Employee\EmployeeReco;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmployeeReco extends Model
{
	use HasFactory;
	
    protected $table = 'public.hr_recommendation_header';
	protected $primaryKey = 'id_recommendation_header';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	public static function getkode($idCompany=null){
		$idCompany = $idCompany ?? session('id_company');
		
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', $idCompany)->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-FRC-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%FRC-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%FRC-{$monthyear}%")->max('reference_number'), 15, 21);
		}	
	//	$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%{$monthyear}%")->max('reference_number'), 15, 21);
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
	
	public static function getdata() {
		$sql = "SELECT hrh.id_recommendation_header, hrh.reference_number, he.nik_employee, he.name, mpr.description AS position,
				mgd.description AS category, mgd2.description AS type, hrh.reco_flag, mgd3.code AS code_app_status, 
				mgd3.description AS desc_app_status, hrh.note_revised, hrh.note_rejected  
				FROM hr_recommendation_header hrh
				LEFT JOIN hr_employee he
				ON hrh.id_employee = he.id_employee
				LEFT JOIN master_general_data mgd
				ON hrh.id_transition_category = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hrh.id_transition_type = mgd2.id_general_data 
				LEFT JOIN master_position_detail mpd
				ON hrh.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing 
				LEFT JOIN master_general_data mgd3
				ON hrh.id_approval_status = mgd3.id_general_data
				WHERE hrh.id_company = ".session('id_company')." AND hrh.created_by = ".session('id_user')."
				ORDER BY hrh.id_recommendation_header DESC";
		$result = DB::select($sql);
        return $result;
    }
	public static function getdata_summary($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = "AND mpd.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT DISTINCT hrh.id_recommendation_header, hrh.reference_number, he.nik_employee, he.name, mpr.description AS position,
				mgd.description AS category, mgd2.description AS type, hrh.reco_flag, mgd3.code AS code_app_status, 
				mgd3.description AS desc_app_status, hrh.note_revised, hrh.note_rejected, mpd.id_branch, he2.name AS req_by, mb.description AS branch
				FROM hr_recommendation_header hrh
				LEFT JOIN hr_employee he
				ON hrh.id_employee = he.id_employee
				LEFT JOIN master_general_data mgd
				ON hrh.id_transition_category = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hrh.id_transition_type = mgd2.id_general_data 
				LEFT JOIN master_position_detail mpd
				ON hrh.id_position_detail = mpd.id_position_detail AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_general_data mgd3
				ON hrh.id_approval_status = mgd3.id_general_data
				LEFT JOIN hr_employee he2
				ON hrh.created_by = he2.id_user AND he2.status = 'A'
				WHERE hrh.id_company = ".session('id_company')." ".$branch."
				ORDER BY hrh.id_recommendation_header DESC";
		$result = DB::select($sql);
        return $result;
    }
	
	public static function get_employee($idEmployee,$type) {
		if($idEmployee == null){
			$query = " WHERE emp.id NOT IN (
						SELECT stats.id_employee FROM (
							SELECT hrh.id_employee, max(hrh.id_recommendation_header) AS id_recommendation_header 
							FROM hr_recommendation_header hrh
							JOIN master_general_data mgd
							ON hrh.id_approval_status = mgd.id_general_data
							WHERE hrh.id_company = ".session('id_company')." AND hrh.status = 'A' AND hrh.created_by = ".session('id_user')." 
							AND mgd.code NOT IN ('Approved','Cancel','Rejected')
							GROUP BY hrh.id_employee
							) AS stats
						)";
		}
		else{
			$query = " WHERE emp.id IN (".$idEmployee.")";
		}
		if($type != 'view'){
			$sql= "SELECT * FROM (		
						SELECT  sr.id_employee AS id, CONCAT(sr.bawahan,' (',sr.nik_employee,')') AS text, sr.emp_status_code
							FROM sp_funct_list_employee_reco(".session('id_company').",".session('id_user').") sr
					UNION	
						SELECT he.id_employee AS id, CONCAT(he.name,' (',he.nik_employee,')') AS text, mgd.code AS emp_status_code 
							FROM sp_funct_list_employee_reco(".session('id_company').",".session('id_user').") sr
						LEFT JOIN master_position_detail mpd
						ON sr.id_employee = mpd.id_employee 
						LEFT JOIN master_position_detail mpd2
						ON mpd.id_position_detail = mpd2.parent_id_position_detail 
						JOIN hr_employee he
						ON mpd2.id_employee = he.id_employee 
						LEFT JOIN master_general_data mgd
						ON he.id_employment_status = mgd.id_general_data
						LEFT JOIN master_company mc
						ON he.id_company = mc.id_company
						WHERE mc.company_type = 'corporate'
						ORDER BY text ASC
					) AS emp ".$query;
		}
		else{
			$sql= "SELECT id_employee id, CONCAT(he.name,' (',he.nik_employee,')') AS text, 
				mgd.code AS emp_status_code 
				FROM hr_employee he
				LEFT JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data
				WHERE he.id_company = ".session('id_company');
		}
		$result = DB::select($sql);
        return $result;
    }
	
	public static function get_detail_employee($data) {
		$idEmployee = $data['id_employee'];
		$empSatusCode = $data['emp_status_code'];
		
		if($empSatusCode != 'Acting'){
			if($empSatusCode == 'Contract' || $empSatusCode == 'Probation'){
				$param = " he.join_date AS start_date, he.expired_date AS end_date, ";
			}
			
			else if($empSatusCode == 'Permanent'){
				$param = " null AS start_date, null AS end_date, ";
			}
						
			$sql="SELECT he.id_employee, mgd.description AS emp_status, mjg.description AS grade, mpr.description AS position_route, 
						md.description AS dept, mb.description AS branch, mr.description AS region, 
						".$param." he.id_employment_status, 
						mpd.id_position_detail, mp.description AS principal, mpd.id_location, ml.description AS location 
								FROM hr_employee he
								LEFT JOIN master_position_detail mpd
								ON he.id_employee = mpd.id_employee
								JOIN master_position_routing mpr
								ON mpd.id_position_routing = mpr.id_routing 
								JOIN master_job_position mjp
								ON mpr.id_position = mjp.id_position
								JOIN master_department md
								ON mjp.id_dept = md.id_dept 
								JOIN master_job_grade mjg 
								ON mpr.id_job_grade = mjg.id_job_grade 
								JOIN master_branch mb
								ON mpd.id_branch = mb.id_branch 
								JOIN master_region mr
								ON mb.id_region = mr.id_region
								JOIN master_location ml
								ON mpd.id_location = ml.id_location
								JOIN relation_positiondetail_principal rpp
								ON mpd.id_position_detail = rpp.id_position_detail
								JOIN master_principal mp
								ON rpp.id_principal = mp.id_principal
								JOIN master_general_data mgd
								ON he.id_employment_status = mgd.id_general_data 
								WHERE mpd.secondary_position = false AND he.id_employee = ".$idEmployee." AND he.id_company = ".session('id_company');
								
			$result = DB::select($sql);
		}
		else if($empSatusCode == 'Acting'){
			$sql = "SELECT sr.*, j_p.effective_date AS start_date, j_p.end_date  
			FROM (
					SELECT he.id_employee, mgd.description AS emp_status, mjg.description AS grade, mpr.description AS position_route, 
						md.description AS dept, mb.description AS branch, mr.description AS region, he.id_employment_status, 
						mpd.id_position_detail, mp.description AS principal, mpd.id_location, ml.description AS location 
								FROM hr_employee he
								LEFT JOIN master_position_detail mpd
								ON he.id_employee = mpd.id_employee
								JOIN master_position_routing mpr
								ON mpd.id_position_routing = mpr.id_routing 
								JOIN master_job_position mjp
								ON mpr.id_position = mjp.id_position
								JOIN master_department md
								ON mjp.id_dept = md.id_dept 
								JOIN master_job_grade mjg 
								ON mpr.id_job_grade = mjg.id_job_grade 
								JOIN master_branch mb
								ON mpd.id_branch = mb.id_branch 
								JOIN master_region mr
								ON mb.id_region = mr.id_region
								JOIN master_location ml
								ON mpd.id_location = ml.id_location
								JOIN relation_positiondetail_principal rpp
								ON mpd.id_position_detail = rpp.id_position_detail
								JOIN master_principal mp
								ON rpp.id_principal = mp.id_principal
								JOIN master_general_data mgd
								ON he.id_employment_status = mgd.id_general_data 
								WHERE mpd.secondary_position = false AND he.id_employee = ".$idEmployee." AND he.id_company = ".session('id_company')." 
			) AS sr
			JOIN (
				SELECT hct2.id_employee, hct2.effective_date, hct2.expired_date AS end_date
				FROM hr_career_transaction hct2
				JOIN (	
					SELECT hct.id_employee, max(hct.id_career_transaction) AS max_id_career_transaction 
					FROM hr_career_transaction hct
					JOIN master_general_data mgd
					ON hct.id_approval_status = mgd.id_general_data
					JOIN master_general_data mgd2
					ON hct.id_transaction_type = mgd2.id_general_data AND mgd2.description != 'Temporary Assignment'
					WHERE mgd.code = 'Approved'
					GROUP BY id_employee		
				) AS j_c
				ON hct2.id_career_transaction = j_c.max_id_career_transaction AND hct2.id_employee = j_c.id_employee
			) AS j_p
			ON sr.id_employee = j_p.id_employee";
			
			$result = DB::select($sql);
		}		
        return $result;
    }
	
	public static function get_category() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
                FROM  master_general_data 
				WHERE  code not in('Rehire','New_Assignment','Join','Entity_Movement') and status = 'A' and id_general_type = 5 and id_company =" . session('id_company') ."
				ORDER BY sequence ASC";
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_type($data) {
		$code = $data['code'];
		if($code == 'Movement'){
			$sql_code = " AND mgd.description NOT IN('Pass KPR','Concurent','Perbaikan Data')";
		}
		else if($code == 'Termination'){
			$sql_code = " AND mgd.description IN('Terminate')";
		}
		else if($code == 'Entity_Movement'){
			$sql_code = " AND mgd.description IN('Entity Movement')";
		}
		else{
			$sql_code = "";
		}
		$sql = "SELECT  id_general_data id, description text
				FROM master_general_data mgd
				WHERE mgd.id_general_type = 6 AND mgd.code = '".$code."' AND mgd.id_company = ? ".$sql_code."
				ORDER BY mgd.description ASC";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_edit($data) {
		$id_recommendation_header = $data['id_recommendation_header'];
        $result = [];
		$sql = "SELECT hrh.*, he.name,
				mpr.description AS routing, mjg.description AS grade, md.description AS dept,
				mr.description AS region, mb.description AS branch, ml.description AS location, mp.description AS principal,
				hrh.id_employment_status, mgd.description AS emp_status, mgd2.code AS code_cat, mgd3.description AS code_type,
				mpr2.description AS new_position, mjg2.description AS new_grade, md2.description AS new_dept,
				mb2.description AS new_branch, ml2.description AS new_location, mp2.description AS new_principal, hqp.total_percent, mpd.id_location
				FROM hr_recommendation_header hrh
				JOIN hr_employee he
				ON hrh.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON hrh.id_position_detail = mpd.id_position_detail
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
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN relation_positiondetail_principal rpp
				ON hrh.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal 
				LEFT JOIN master_general_data mgd
				ON hrh.id_employment_status = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hrh.id_transition_category = mgd2.id_general_data
				LEFT JOIN master_position_detail mpd2
				ON hrh.id_new_position_detail = mpd2.id_position_detail
				LEFT JOIN master_position_routing mpr2
				ON mpd2.id_position_routing = mpr2.id_routing
				LEFT JOIN master_job_grade mjg2
				ON mpr2.id_job_grade = mjg2.id_job_grade
				LEFT JOIN master_job_position mjp2
				ON mpr2.id_position = mjp2.id_position
				LEFT JOIN master_department md2
				ON mjp2.id_dept = md2.id_dept
				LEFT JOIN master_branch mb2
				ON mpd2.id_branch = mb2.id_branch
				LEFT JOIN master_location ml2
				ON mpd2.id_location = ml2.id_location
				LEFT JOIN relation_positiondetail_principal rpp2
				ON hrh.id_new_position_detail = rpp2.id_position_detail
				LEFT JOIN master_principal mp2
				ON rpp2.id_principal = mp2.id_principal
				LEFT JOIN master_general_data mgd3
				ON hrh.id_transition_type = mgd3.id_general_data
				LEFT JOIN hr_qualitative_participant hqp
				ON hrh.id_recommendation_header = hqp.id_recommendation_header
				WHERE hrh.id_company = " . session('id_company')." AND hrh.status = 'A' AND hrh.id_recommendation_header = ".$id_recommendation_header;				
        $result = (Array) DB::select($sql)[0];
		
		$sql_detail = "SELECT hrq.* FROM hr_recommendation_quantitative hrq WHERE hrq.id_recommendation_header = ?";
        $result_menu = DB::select($sql_detail, [$id_recommendation_header]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_recommendation_quantitative')->toArray();
		
		$sql2 = "SELECT hrql.*, hqa.*
				FROM hr_recommendation_qualitative hrql 
				JOIN hr_qualitative_appraisers hqa
				ON hrql.id_recommendation_qualitative = hqa.id_recommendation_qualitative
				WHERE hrql.id_recommendation_header = ?";
        $result_menu2 = DB::select($sql2, [$id_recommendation_header]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_recommendation_qualitative')->toArray();
        $result['quanti'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['quanti'][] = [
                'id_recommendation_quantitative' => $group_menu[$value][0]->id_recommendation_quantitative,
                'desc_kpi' => $group_menu[$value][0]->description,
                'weight_1' => $group_menu[$value][0]->weight_prosentase_kpi_1_month_ago,
                'obj_1' => $group_menu[$value][0]->target_kpi_1_month_ago,
                'ach_1' => $group_menu[$value][0]->kpi_1_month_ago,
                'idx_1' => $group_menu[$value][0]->index_kpi_1_month_ago,
                'weight_2' => $group_menu[$value][0]->weight_prosentase_kpi_2_month_ago,
                'obj_2' => $group_menu[$value][0]->target_kpi_2_month_ago,
                'ach_2' => $group_menu[$value][0]->kpi_2_month_ago,
                'idx_2' => $group_menu[$value][0]->index_kpi_2_month_ago,
                'weight_3' => $group_menu[$value][0]->weight_prosentase_kpi_3_month_ago,
                'obj_3' => $group_menu[$value][0]->target_kpi_3_month_ago,
                'ach_3' => $group_menu[$value][0]->kpi_3_month_ago,
                'idx_3' => $group_menu[$value][0]->index_kpi_3_month_ago,
                'weight_4' => $group_menu[$value][0]->weight_prosentase_kpi_4_month_ago,
                'obj_4' => $group_menu[$value][0]->target_kpi_4_month_ago,
                'ach_4' => $group_menu[$value][0]->kpi_4_month_ago,
                'idx_4' => $group_menu[$value][0]->index_kpi_4_month_ago,
                'weight_5' => $group_menu[$value][0]->weight_prosentase_kpi_5_month_ago,
                'obj_5' => $group_menu[$value][0]->target_kpi_5_month_ago,
                'ach_5' => $group_menu[$value][0]->kpi_5_month_ago,
                'idx_5' => $group_menu[$value][0]->index_kpi_5_month_ago,
                'weight_6' => $group_menu[$value][0]->weight_prosentase_kpi_6_month_ago,
                'obj_6' => $group_menu[$value][0]->target_kpi_6_month_ago,
                'ach_6' => $group_menu[$value][0]->kpi_6_month_ago,
                'idx_6' => $group_menu[$value][0]->index_kpi_6_month_ago,             
			];
        }
		
		$result['quali'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['quali'][] = [
                'id_recommendation_qualitative' => $group_menu2[$value][0]->id_recommendation_qualitative,
                'id_employee_appraisers' => $group_menu2[$value][0]->id_employee_appraisers,
                'appraisers_hierarchy' => $group_menu2[$value][0]->appraisers_hierarchy,
                'subtotal_score' => $group_menu2[$value][0]->subtotal_score,
                'submitted' => $group_menu2[$value][0]->submitted,
                'notes' => $group_menu2[$value][0]->notes,                
			];
        }
        return $result;
    }
	
	public static function get_sumQualitative($data) {
		$id_employee_participant = $data['id_employee_participant'];
		$id_recommendation_header = $data['id_recommendation_header'];
        $sql = "SELECT hpq.sequence, hpq.description AS question,
				 COUNT(CASE WHEN hpa.sequence='Level 5' THEN 1 END) as Level_5,
				 COUNT(CASE WHEN hpa.sequence='Level 4' THEN 1 END) as Level_4,
				 COUNT(CASE WHEN hpa.sequence='Level 3' THEN 1 END) as Level_3,
				 COUNT(CASE WHEN hpa.sequence='Level 2' THEN 1 END) as Level_2,
				 COUNT(CASE WHEN hpa.sequence='Level 1' THEN 1 END) as Level_1,
				 ROUND(
				 CAST(float8 (
					(COUNT(CASE WHEN hpa.sequence='Level 5' THEN 1 END)*5) + 
					(COUNT(CASE WHEN hpa.sequence='Level 4' THEN 1 END)*4) + 
					(COUNT(CASE WHEN hpa.sequence='Level 3' THEN 1 END)*3) + 			
					(COUNT(CASE WHEN hpa.sequence='Level 2' THEN 1 END)*2) + 
					(COUNT(CASE WHEN hpa.sequence='Level 1' THEN 1 END)*1)
				) / COUNT(hpa.id_pa_answer) as numeric), 1) as avg
				FROM hr_appraisers_result har
				LEFT JOIN hr_qualitative_appraisers hqa
				ON har.id_qualitative_appraisers = hqa.id_qualitative_appraisers 
				JOIN hr_recommendation_qualitative hrq
				ON hqa.id_recommendation_qualitative = hrq.id_recommendation_qualitative 
				JOIN hr_pa_question hpq
				ON har.id_pa_question = hpq.id_pa_question
				JOIN hr_pa_answer hpa
				ON har.id_pa_answer = hpa.id_pa_answer
				WHERE hqa.id_employee_participant = ".$id_employee_participant." AND hrq.id_recommendation_header = ".$id_recommendation_header."
				GROUP BY hpq.sequence, hpq.description
				ORDER BY hpq.sequence ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_approval_status() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
						FROM  master_general_data where status = 'A' and id_general_type = 7 and id_company = " . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_new_position() {
        $sql = "SELECT DISTINCT mpr.id_routing, mpr.description AS position_routing, mjg.description AS job_grade, 
				mb.description AS branch, ml.id_location, ml.description AS location, mp.id_principal, mp.description AS principal, md.description AS department
				FROM master_position_detail mpd 
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN relation_positiondetail_principal rpp
				ON mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal 
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN hr_employee he
				ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
				WHERE mpd.id_company = ".session('id_company')." AND mpd.status = 'A' AND mpd.assigned_to_company IS NULL
				ORDER BY mpr.description ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function browse_check($data) {
		$id_position_detail = $data['jobid'];
		$id_principal = $data['jobidPrincipal'];
        $sql = "SELECT mpd.id_position_detail, mpr.description AS position_routing, mjg.description AS job_grade,
				mb.description AS branch, ml.description AS location, mp.description AS principal, he.name, md.description AS department
				FROM master_position_detail mpd 
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN relation_positiondetail_principal rpp
				ON mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal 
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN hr_employee he
				ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
				WHERE mpd.id_company = ".session('id_company')." AND mpd.status = 'A' AND mpd.id_position_detail = ".$id_position_detail." 
				AND mp.id_principal = ".$id_principal."
				ORDER BY mpr.description ASC";
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function get_new_status() {
        $sql = "SELECT mgd.id_general_data id, mgd.description text
				FROM master_general_data mgd
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_employment_status' 
				AND mgd.id_company = ".session('id_company')." AND mgd.status = 'A' AND mgd.code NOT IN('Concurent')";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_appraiser() {
        $sql = "SELECT he.id_employee id, CONCAT(he.name,' (',he.nik_employee,') / ',mpr.description,'-',mc.company_code) AS text 
				FROM hr_employee he
				JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_company mc
				ON he.id_company = mc.id_company
				WHERE he.status = 'A'
				ORDER BY mc.company_code ASC, he.name ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_approval($id_approval,$app_status) {
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						hah.hierarchy_type,
						mgd.code,
						(select id_general_data from master_general_data where code = '".$app_status."' and id_company =".session('id_company').") as id_approval_status
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				WHERE hah.id_approval ='".$id_approval."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_app_combine($id_employee,$id_company,$id_approval) {
		$sec_pos = JobPositionDetail::where('id_employee',$id_employee)->where('secondary_position',true)->where('id_company',session('id_company'))->first();
		if($sec_pos){
			$list = "SELECT un_org.* FROM (						
			SELECT	sfac.*, sfac.id_employee as id_employee_approval
				FROM  sp_funct_approval_combine_hierarchy_view (".$id_employee.",".$id_company.",".$id_approval.") sfac
				JOIN master_position_detail mpd
				ON sfac.id_position_detail = mpd.id_position_detail
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade 
			UNION ALL
				SELECT ".$id_approval." AS id_approval, null AS id_approval_mode, 1 AS sequence, mpd.id_position_detail,
				mpd.description, mpd.id_employee, mpd.id_employee AS id_employee_approval
				FROM master_position_detail mpd
				JOIN (
					SELECT 
					CASE
						WHEN mpd2.id_employee IS NOT NULL THEN mpd2.id_employee
						WHEN mpd2.id_employee IS NULL THEN mpd3.id_employee
						WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN mpd4.id_employee
					END AS id_employee_approval
					FROM master_position_detail mpd
					LEFT JOIN master_position_detail mpd2
					ON mpd.parent_id_position_detail = mpd2.id_position_detail
					LEFT JOIN master_position_detail mpd3
					ON mpd2.parent_id_position_detail = mpd3.id_position_detail
					LEFT JOIN master_position_detail mpd4
					ON mpd3.parent_id_position_detail = mpd4.id_position_detail
					WHERE mpd.id_employee = ".$id_employee." AND mpd.secondary_position = true AND mpd.id_company = ".$id_company."		
				) AS parent
				ON mpd.id_employee = parent.id_employee_approval
				WHERE mpd.id_company = ".$id_company."	
			) AS un_org
			JOIN master_position_detail mpd
			ON un_org.id_position_detail = mpd.id_position_detail
			JOIN master_position_routing mpr
			ON mpd.id_position_routing = mpr.id_routing
			JOIN master_job_grade mjg
			ON mpr.id_job_grade = mjg.id_job_grade
			WHERE un_org.id_employee != ".$id_employee." AND un_org.sequence != 2					
			AND mjg.job_class_group IN('manager-level','gm-level')
			LIMIT 1	";
		}
		else{
			$list = "SELECT	sfac.*, sfac.id_employee as id_employee_approval
							FROM  sp_funct_approval_combine_hierarchy_view (".$id_employee.",".$id_company.",".$id_approval.") sfac
							JOIN master_position_detail mpd
							ON sfac.id_position_detail = mpd.id_position_detail
							JOIN master_position_routing mpr
							ON mpd.id_position_routing = mpr.id_routing
							JOIN master_job_grade mjg
							ON mpr.id_job_grade = mjg.id_job_grade 
							WHERE sfac.id_employee != ".$id_employee." AND sfac.sequence != 2
							AND mjg.job_class_group IN('manager-level','gm-level')
							LIMIT 1";
		}
        $sql = "SELECT	sfac.*, sfac.id_employee as id_employee_approval
					FROM  sp_funct_approval_combine_hierarchy_view (".$id_employee.",".$id_company.",".$id_approval.") sfac
					WHERE sfac.id_employee != ".$id_employee." AND sfac.sequence != 1				
				UNION
					SELECT org.* FROM (
						".$list."
						) AS org";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_decision() {
        $sql = "SELECT mgd.id_general_data id, mgd.description text
				FROM master_general_data mgd
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgd.id_company = 1 AND mgt.general_type = 'master_decision_recommendation_form' AND mgd.status = 'A'
				ORDER BY mgd.sequence ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_new_mgr($data) {
		$id_position_detail = $data['id_position_detail'];
        $sql = "SELECT * FROM (
							SELECT he.id_employee id, CONCAT(he.name,' (',sfao.description_chief,')') text
								FROM sp_funct_approval_organization_hierarchy_view (null,".session('id_company').",".$id_position_detail.",Array['gm-level','manager-level']) sfao
								LEFT JOIN hr_employee he 
								ON he.id_employee=sfao.id_employee_approval
								LIMIT 1			
					) AS ok							
					UNION ALL
					SELECT * FROM (
						SELECT DISTINCT he.id_employee id, CONCAT(he.name,' (',mpr.description,')') text
						FROM hr_employee he
						JOIN master_position_detail mpd
						ON he.id_employee = mpd.id_employee
						JOIN master_position_routing mpr
						ON mpd.id_position_routing = mpr.id_routing
						JOIN master_job_grade mjg
						ON mpr.id_job_grade = mjg.id_job_grade
						JOIN master_job_position mjp
						ON mpr.id_position = mjp.id_position 
						WHERE he.status = 'A' AND mjg.job_class_group IN('gm-level','manager-level') 
						AND mjp.id_dept = (SELECT mjp.id_dept  FROM (
										SELECT sfao.id_detail_chief,sfao.id_employee_approval, he.name
										FROM sp_funct_approval_organization_hierarchy_view (null,".session('id_company').",".$id_position_detail.",Array['gm-level','manager-level']) sfao
										LEFT JOIN hr_employee he on he.id_employee=sfao.id_employee_approval
										LIMIT 1
								) AS head	
						LEFT JOIN master_position_detail mpd
						ON head.id_detail_chief = mpd.id_position_detail 
						LEFT JOIN master_position_routing mpr
						ON mpd.id_position_routing = mpr.id_routing
						LEFT JOIN master_job_position mjp
						ON mpr.id_position = mjp.id_position)
						AND he.id_employee NOT IN(
						SELECT sfao.id_employee_approval
							FROM sp_funct_approval_organization_hierarchy_view (null,".session('id_company').",".$id_position_detail.",Array['gm-level','manager-level']) sfao
							LIMIT 1
						)
						ORDER BY text ASC
					) AS oke";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function kpi_group() {
        $sql = "SELECT mkc.id_kpi_category 
				FROM master_kpi_category mkc
				WHERE mkc.id_company = ".session('id_company')." AND mkc.status = 'A' AND mkc.description = 'Other'";
        $kpi_category = DB::select($sql);
		
		$sql2 = "SELECT mgd.id_general_data AS id_kpi_type
				FROM master_general_data mgd
				WHERE mgd.id_company = ".session('id_company')." AND mgd.status = 'A' AND mgd.code = 'Lurus'";
        $kpi_type = DB::select($sql2);
	//	dd($result);
        return ['kpi_category'=>$kpi_category,'kpi_type'=>$kpi_type];
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Cancel' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function get_kpi_detail($data) {
		$idEmployee = $data['id_employee'];
		$date = $data['date'];
        $sql = "SELECT fix.description, fix.id_kpi_type, fix.kpi_type_code,
				STRING_AGG(fix._1_month_ago,'') AS _1_month_ago,
				STRING_AGG(fix._2_month_ago,'') AS _2_month_ago,
				STRING_AGG(fix._3_month_ago,'') AS _3_month_ago,
				STRING_AGG(fix._4_month_ago,'') AS _4_month_ago,
				STRING_AGG(fix._5_month_ago,'') AS _5_month_ago,
				STRING_AGG(fix._6_month_ago,'') AS _6_month_ago
				FROM get_reco_kpi_employee_view(?,?,?) fix		
				GROUP BY fix.description, fix.id_kpi_type, fix.kpi_type_code";
        $result = DB::select($sql,[session('id_company'),$idEmployee,$date]);
        return $result;
    }
	
	public static function get_mail_approve($id_recommendation_header) {
        $sql = "SELECT CONCAT(he.name,' (',he.nik_employee,')') AS emp_name, 
				mpr.description AS position, mgd.description AS category, 
				mgd2.description AS type, CONCAT(he2.name,' (',he2.nik_employee,')') AS request_by, 
				hrh.*, mpr2.description AS new_position  
				FROM hr_recommendation_header hrh
				LEFT JOIN hr_employee he
				ON hrh.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON hrh.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_general_data mgd
				ON hrh.id_transition_category = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hrh.id_transition_type = mgd2.id_general_data
				LEFT JOIN hr_employee he2
				ON hrh.created_by = he2.id_user
				LEFT JOIN master_position_detail mpd2
				ON hrh.id_new_position_detail = mpd2.id_position_detail
				LEFT JOIN master_position_routing mpr2
				ON mpd2.id_position_routing = mpr2.id_routing
				WHERE hrh.id_recommendation_header = ?";
        $result = DB::select($sql,[$id_recommendation_header])[0];
        return $result;
    }
	public static function get_mail_quali($id_recommendation_header) {
        $sql = "SELECT hrh.id_recommendation_header, hrh.reference_number, he.name AS apprasier, hrq.notes,
				CONCAT(he2.name,' (',he2.nik_employee,')') AS emp_participant, mpr.description AS position, hqa.*,
				he3.name AS request_by, CONCAT(mgd.description,' (',mgd2.description,')') AS code, hrq.notes AS notes_send
				FROM hr_recommendation_qualitative hrq
				LEFT JOIN hr_recommendation_header hrh
				ON hrq.id_recommendation_header = hrh.id_recommendation_header
				LEFT JOIN hr_employee he
				ON hrq.id_employee_appraisers = he.id_employee
				LEFT JOIN hr_qualitative_appraisers hqa
				ON hrq.id_recommendation_qualitative = hqa.id_recommendation_qualitative
				LEFT JOIN hr_employee he2
				ON hqa.id_employee_participant = he2.id_employee
				LEFT JOIN master_position_detail mpd
				ON hqa.id_employee_participant = mpd.id_employee
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN hr_employee he3
				ON hrh.created_by = he3.id_user
				LEFT JOIN master_general_data mgd
				ON hrh.id_transition_category = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				on hrh.id_transition_type = mgd2.id_general_data 
				WHERE hrq.id_recommendation_header = ?";
        $result = DB::select($sql,[$id_recommendation_header]);
        return $result;
    }
	
	public static function get_pdf($data) {
		$id_recommendation_header = $data['id_recommendation_header'];
        $result = [];
		$sql = "SELECT hrh.*, CONCAT(date_part('month',age(hrh.expired_date,hrh.effective_date)),' Month(s)') AS duration, 
				he.name, he.nik_employee, mpr.description AS position, md.description AS dept, mjg.description AS grade, mp.description AS principal, 
				mb.description AS branch, ml.description AS location, mpr2.description AS new_position, md2.description AS new_dept, mjg2.description AS new_grade, 
				mp2.description AS new_principal, mb2.description AS new_branch, ml2.description AS new_location, mgd.description AS category,
				mgd2.description AS type, he2.name AS new_mgr, he2.nik_employee AS nik_new_mgr, mgd3.description as decision, he3.name AS direct_name, he3.nik_employee AS nik_direct_name, he4.name AS manager_approval, he4.nik_employee AS nik_manager_approval, he5.name AS hr_manager, he5.nik_employee AS nik_hr_manager, mgd4.code AS code_status
				FROM hr_recommendation_header hrh
				LEFT JOIN hr_employee he
				ON hrh.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON hrh.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN relation_positiondetail_principal rpp
				ON hrh.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_position_detail mpd2
				ON hrh.id_new_position_detail = mpd2.id_position_detail
				LEFT JOIN master_position_routing mpr2
				ON mpd2.id_position_routing = mpr2.id_routing
				LEFT JOIN master_job_position mjp2
				ON mpr2.id_position = mjp2.id_position
				LEFT JOIN master_department md2
				ON mjp2.id_dept = md2.id_dept
				LEFT JOIN master_job_grade mjg2
				ON mpr2.id_job_grade = mjg2.id_job_grade
				LEFT JOIN relation_positiondetail_principal rpp2
				ON hrh.id_new_position_detail = rpp2.id_position_detail
				LEFT JOIN master_principal mp2
				ON rpp2.id_principal = mp2.id_principal
				LEFT JOIN master_branch mb2
				ON mpd2.id_branch = mb2.id_branch
				LEFT JOIN master_location ml2
				ON mpd2.id_location = ml2.id_location
				LEFT JOIN master_general_data mgd
				ON hrh.id_transition_category = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hrh.id_transition_type = mgd2.id_general_data
				LEFT JOIN hr_employee he2
				ON hrh.id_new_chief_employee = he2.id_employee
				LEFT JOIN master_general_data mgd3
				ON hrh.id_decision_recommendation = mgd3.id_general_data
				LEFT JOIN hr_employee he3
				ON hrh.created_by = he3.id_user
				LEFT JOIN hr_approval_transaction hat
				ON hrh.id_recommendation_header = hat.id_source_transaction 
				AND hat.source_transaction_type = 'Form_Reco' AND hat.sequence = 1 AND hat.updated_by IS NOT NULL
				LEFT JOIN hr_employee he4
				ON hat.id_employee_approval = he4.id_employee 
				LEFT JOIN hr_approval_transaction hat2
				ON hrh.id_recommendation_header = hat2.id_source_transaction 
				AND hat2.source_transaction_type = 'Form_Reco' AND hat2.sequence = 2 AND hat2.updated_by IS NOT NULL
				LEFT JOIN hr_employee he5
				ON hat2.id_employee_approval = he5.id_employee
				LEFT JOIN master_general_data mgd4
				ON hrh.id_approval_status = mgd4.id_general_data
				WHERE hrh.id_company = " . session('id_company')." AND hrh.status = 'A' AND hrh.id_recommendation_header = ".$id_recommendation_header;				
        $result = (Array) DB::select($sql)[0];		
		$sql_detail = "SELECT hrq.* FROM hr_recommendation_quantitative hrq WHERE hrq.id_recommendation_header = ?";
        $result_menu = DB::select($sql_detail, [$id_recommendation_header]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_recommendation_quantitative')->toArray();
		
		$sql2 = "SELECT hrql.*, hqa.*
				FROM hr_recommendation_qualitative hrql 
				JOIN hr_qualitative_appraisers hqa
				ON hrql.id_recommendation_qualitative = hqa.id_recommendation_qualitative
				WHERE hrql.id_recommendation_header = ?";
        $result_menu2 = DB::select($sql2, [$id_recommendation_header]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_recommendation_qualitative')->toArray();
        $result['quanti'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['quanti'][] = [
                'id_recommendation_quantitative' => $group_menu[$value][0]->id_recommendation_quantitative,
                'desc_kpi' => $group_menu[$value][0]->description,
                'weight_1' => $group_menu[$value][0]->weight_prosentase_kpi_1_month_ago,
                'obj_1' => $group_menu[$value][0]->target_kpi_1_month_ago,
                'ach_1' => $group_menu[$value][0]->kpi_1_month_ago,
                'idx_1' => $group_menu[$value][0]->index_kpi_1_month_ago,
                'weight_2' => $group_menu[$value][0]->weight_prosentase_kpi_2_month_ago,
                'obj_2' => $group_menu[$value][0]->target_kpi_2_month_ago,
                'ach_2' => $group_menu[$value][0]->kpi_2_month_ago,
                'idx_2' => $group_menu[$value][0]->index_kpi_2_month_ago,
                'weight_3' => $group_menu[$value][0]->weight_prosentase_kpi_3_month_ago,
                'obj_3' => $group_menu[$value][0]->target_kpi_3_month_ago,
                'ach_3' => $group_menu[$value][0]->kpi_3_month_ago,
                'idx_3' => $group_menu[$value][0]->index_kpi_3_month_ago,
                'weight_4' => $group_menu[$value][0]->weight_prosentase_kpi_4_month_ago,
                'obj_4' => $group_menu[$value][0]->target_kpi_4_month_ago,
                'ach_4' => $group_menu[$value][0]->kpi_4_month_ago,
                'idx_4' => $group_menu[$value][0]->index_kpi_4_month_ago,
                'weight_5' => $group_menu[$value][0]->weight_prosentase_kpi_5_month_ago,
                'obj_5' => $group_menu[$value][0]->target_kpi_5_month_ago,
                'ach_5' => $group_menu[$value][0]->kpi_5_month_ago,
                'idx_5' => $group_menu[$value][0]->index_kpi_5_month_ago,
                'weight_6' => $group_menu[$value][0]->weight_prosentase_kpi_6_month_ago,
                'obj_6' => $group_menu[$value][0]->target_kpi_6_month_ago,
                'ach_6' => $group_menu[$value][0]->kpi_6_month_ago,
                'idx_6' => $group_menu[$value][0]->index_kpi_6_month_ago,             
			];
        }	
		$arr = [
            'id_employee_participant' => $result['id_employee'],
            'id_recommendation_header' => $id_recommendation_header
        ];
		$result['quali'] = self::get_sumQualitative($arr);
        return $result;
    }
	
	public static function get_user_req($idUser) {
		$sql = "SELECT 
						he.id_employee,
						he.id_user,
						he.name as name_employee,
						ml.id_location
				FROM master_position_detail mpd
				JOIN hr_employee he
				ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
				 AND (mpd.id_company =  ".session('id_company')."
                     OR mpd.assigned_to_company = ".session('id_company')."
                  )
				JOIN master_location ml
			  ON mpd.id_location = ml.id_location
			 AND mpd.id_company = ml.id_company
			WHERE he.id_user = ".$idUser."
			  AND mpd.status = 'A'";
        $result = DB::select($sql);
        return $result;
	}
	
	public static function JobDetail($jobid,$id_location,$jobidPrincipal) {
		$sql = "SELECT mpd.id_position_detail FROM master_position_detail mpd
				LEFT JOIN relation_positiondetail_principal rpp
				ON mpd.id_position_detail = rpp.id_position_detail
				WHERE mpd.status = 'A' AND mpd.id_position_routing = ".$jobid." AND mpd.id_location = ".$id_location." AND rpp.id_principal = ".$jobidPrincipal;
        $result = DB::select($sql)[0];
        return $result;
	}
	
	public static function posDetail($idDetail,$idNewDetail) {
		if($idNewDetail == null){
			$queryNew = " AND hrh.id_new_position_detail IS NULL";
		}
		else{
			$queryNew = " AND hrh.id_new_position_detail = ".$idNewDetail;
		}
		$sql = "SELECT 
				mb.description AS old_branch, mr.description AS old_region,
				mp.description AS old_principal, mb2.description AS new_branch, 
				mr2.description AS new_region, mp2.description AS new_principal
				FROM hr_recommendation_header hrh
				LEFT JOIN master_position_detail mpd
				ON hrh.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN relation_positiondetail_principal rpp
				ON mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal
				LEFT JOIN master_position_detail mpd2
				ON hrh.id_new_position_detail = mpd2.id_position_detail
				LEFT JOIN master_branch mb2
				ON mpd2.id_branch = mb2.id_branch
				LEFT JOIN master_region mr2
				ON mb2.id_region = mr2.id_region
				LEFT JOIN relation_positiondetail_principal rpp2
				ON mpd2.id_position_detail = rpp2.id_position_detail
				LEFT JOIN master_principal mp2
				ON rpp2.id_principal = mp2.id_principal
				WHERE hrh.id_position_detail = ? ".$queryNew;
        $result = DB::select($sql,[$idDetail])[0];
        return $result;
	}
}
