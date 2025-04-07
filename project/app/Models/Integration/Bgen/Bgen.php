<?php

namespace App\Models\Integration\Bgen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Bgen extends Model
{
	use HasFactory;
	
    protected $table = 'integration.bgen_integration_sales_code';
	protected $primaryKey = 'id_integration_sales_code';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

	protected $fillable = [
		'id_approval_status',
		'update_date',
		'updated_by',
	];
	
	public static function getdata($group_branch, $nik=null, $id_dept = null, $status = 'A') {
		$branch = "";
		$idNik = "";
		if($group_branch || $group_branch != ""){
			$branch = " AND id_branch IN(".$group_branch.")";
		}
		if($nik){
			$idNik = " AND nik_employee IN(".implode(",", $nik).")";
		}
		$dept = "";
		if($id_dept && is_numeric($id_dept)) {
			$dept = " AND id_dept = ".$id_dept;
		}
		$sql= "SELECT * FROM sp_funct_list_employee_bgen(".session('id_company').")
				WHERE status='".$status."' ".$branch." ".$idNik." ".$dept;
		
	/*	$sql = "SELECT sfger.id_employee, sfger.nik_employee, sfger.name, sfger.position_routing, 
				sfger.job_grade, sfger.department, sfger.regional, sfger.branch, 
				sfger.principal, sfger.parent_emp_name AS direct_spv, sfger.indirect_emp_name AS imm_manager, sfger.status_active   
				FROM public.sp_funct_get_employee_report_all(".session('id_company').") sfger
				JOIN (
					SELECT max(sfger.id_employee) AS id_employee, sfger.nik_employee
					FROM public.sp_funct_get_employee_report_all(".session('id_company').") sfger
					WHERE sfger.status_active = 'A'
					GROUP BY sfger.nik_employee				
				) AS j_id
				ON sfger.id_employee = j_id.id_employee
				LEFT JOIN master_position_detail mpd ON 
					j_id.id_employee = mpd.id_employee
					AND mpd.secondary_position = FALSE
				LEFT JOIN master_position_routing mpr ON 
					mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_position mjp ON 
					mpr.id_position = mjp.id_position
				LEFT JOIN master_department md ON 
					mjp.id_dept = md.id_dept
				WHERE sfger.nik_employee IS NOT NULL ".$branch." ".$idNik.$dept."
				ORDER BY sfger.name ASC";	
	*/	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_employee($group_branch, $id_dept = null, $status = 'A') {
		$branch = " ";
		if($group_branch || $group_branch != ""){
			$branch = " AND id_branch IN(".$group_branch.")";
		}
		$dept = "";
		if($id_dept && is_numeric($id_dept)) {
			$dept = " AND id_dept = ".$id_dept;
		}
		$sql = "SELECT nik_employee id, CONCAT(name,' (',nik_employee,')(',status,')') text, id_dept, department AS dept
				FROM sp_funct_list_employee_bgen(".session('id_company').")
				WHERE status='".$status."' ".$branch." ".$dept;
	/*   $sql = "SELECT sfger.nik_employee id, CONCAT(sfger.name,' (',sfger.nik_employee,')(',sfger.status_active,')') text, md.id_dept, md.description AS dept
				FROM public.sp_funct_get_employee_report_all(".session('id_company').") sfger
				JOIN (
					SELECT max(sfger.id_employee) AS id_employee, sfger.nik_employee
					FROM public.sp_funct_get_employee_report_all(".session('id_company').") sfger
					WHERE sfger.status_active = 'A'
					GROUP BY sfger.nik_employee				
				) AS j_id
				ON sfger.id_employee = j_id.id_employee
				LEFT JOIN master_position_detail mpd ON 
					j_id.id_employee = mpd.id_employee
					AND mpd.secondary_position = FALSE
				LEFT JOIN master_position_routing mpr ON 
					mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_position mjp ON 
					mpr.id_position = mjp.id_position
				LEFT JOIN master_department md ON 
					mjp.id_dept = md.id_dept
				WHERE 1=1
				".$branch.$dept."
				ORDER BY sfger.name ASC";
		*/
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }

	public static function get_department() {
		return DB::select("SELECT DISTINCT sfleb.id_dept AS id, concat(sfleb.department, ' (', mc.company_code, ')') AS TEXT, mc.id_company, md.department_code
							FROM sp_funct_list_employee_bgen(?) sfleb
							JOIN master_department md ON sfleb.id_dept = md.id_dept
							JOIN master_company mc ON md.id_company = mc.id_company
							ORDER BY mc.id_company, sfleb.id_dept", [session('id_company')]);
	}
	
	public static function get_status() {
		$sql = "SELECT mgd.id_general_data id, mgd.description text
				FROM master_general_data mgd
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_status'
				WHERE mgd.id_company = ".session('id_company')." AND mgd.status = 'A'
				ORDER BY mgd.sequence ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_category() {
		$sql = "SELECT mgd.id_general_data id, mgd.description text, mgd.code
				FROM public.master_general_data mgd 
				WHERE mgd.id_general_type = 5 AND mgd.id_company = ".session('id_company')."
				AND mgd.status = 'A' AND mgd.code IN('Join','Movement')
				ORDER BY mgd.code ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_branch() {
		$sql = "SELECT DISTINCT
					mb.id_branch id,
					CONCAT(mb.description, ' (', mb.id_branch_erp, ' - ', mc.company_code ,')') TEXT,
					mb.description
				FROM
				master_position_detail mpd 
				JOIN master_branch mb ON 
					mpd.id_branch = mb.id_branch
				JOIN master_company mc ON 
					mb.id_company = mc.id_company
				WHERE
					mb.status = 'A'
					AND (mpd.assigned_to_company = ? OR (mpd.id_company = ? AND mc.company_type != 'os'))
					AND mb.id_branch_erp IS NOT NULL
					AND mpd.id_employee  IS NOT NULL 
					AND mpd.secondary_position = FALSE
				ORDER BY
					mb.description DESC";	
        $result = DB::select($sql, [session('id_company'), session('id_company')]);
        return $result;
    }
	
	public static function get_principal() {
	/*	
		$sql = "SELECT DISTINCT 
					mp.id_principal id,
					CONCAT(mp.description, ' (', mp.erp_principal_code, ' - ', mc.company_code,')') TEXT,
					mp.description
				FROM
					master_position_detail mpd
				JOIN relation_positiondetail_principal rpp ON 
					mpd.id_position_detail = rpp.id_position_detail
				JOIN master_principal mp ON 
					rpp.id_principal = mp.id_principal
				JOIN master_company mc ON 
					mpd.id_company = mc.id_company
				WHERE 
					mp.erp_principal_code IS NOT NULL
					AND (mpd.assigned_to_company = ?
						OR mpd.id_company = ?)
					AND mpd.secondary_position = FALSE
				--	AND mpd.id_employee IS NOT NULL
					AND mp.status = 'A'
				ORDER BY
					mp.description ASC";	
		*/
		$company = "SELECT mc.company_type 
					FROM master_company mc
					WHERE mc.id_company = ".session('id_company');
		$res = DB::select($company)[0];
		if($res->company_type == 'os'){
			$sql = "SELECT mp.id_principal id,
				CONCAT(mp.description, ' (', mp.erp_principal_code, ' - ', mc.company_code,')') TEXT,
				mp.description
				FROM master_principal mp 
				JOIN master_company mc ON 
				mp.id_company = mc.id_company
				WHERE mc.id_company IN (
					SELECT mc2.id_company FROM master_company mc2
					WHERE mc2.company_type = 'corporate'	
				)
				AND mp.status = 'A' AND mp.erp_principal_code IS NOT NULL 
				AND mp.principal_code != 'CORP'
				ORDER BY mp.description ASC";
		}
		else{
			$sql = "SELECT mp.id_principal id,
					CONCAT(mp.description, ' (', mp.erp_principal_code, ' - ', mc.company_code,')') TEXT,
					mp.description
					FROM master_principal mp
					JOIN master_company mc ON 
					mp.id_company = mc.id_company
					WHERE mp.status = 'A' AND mp.id_company = ".session('id_company')." 
					AND mp.erp_principal_code IS NOT NULL AND mp.principal_code != 'CORP'
					ORDER BY mp.description ASC";	
			}
		$result = DB::select($sql);
        return $result;
    }

/*	
	public static function get_position() {
		$sql = "SELECT DISTINCT mpr.id_routing id,
				CASE 
					WHEN mpd.assigned_to_company IS NULL THEN mpr.description
					ELSE CONCAT(mpr.description,' (',mc.company_code,')')
				END AS text
				FROM public.master_position_routing mpr
				JOIN public.master_position_detail mpd
				ON mpr.id_routing = mpd.id_position_routing
				LEFT JOIN public.master_company mc 
				ON mpd.assigned_to_company = mc.id_company
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				WHERE mpr.status = 'A' AND md.department_code = '170_SAL' AND mpr.id_company = ".session('id_company')."
				ORDER BY text ASC";	
		$result = DB::select($sql);
        return $result;
    }
	
	public static function get_location() {
		$sql = "SELECT ml.id_location id, ml.description text 
				FROM public.master_location ml
				WHERE ml.status = 'A' AND ml.id_company = ".session('id_company')."
				ORDER BY ml.description ASC";	
		$result = DB::select($sql);
        return $result;
    }
*/	
	public static function get_edit($data, $integrationType = 'Sales_Code') {
        $result = [];
		$sql = "SELECT sb.*, mpr.id_routing AS id_position_routing, mpd.id_location,
				ml.description AS work_location
				FROM sp_funct_list_employee_bgen(".session('id_company').") sb
				JOIN master_position_detail mpd
				ON sb.id_position_detail = mpd.id_position_detail
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_location ml
				ON mpd.id_location = ml.id_location 
				WHERE nik_employee = '".$data['nik_employee']."' AND sb.status = 'A'";
				
    /*    $sql = "SELECT sfger.id_employee, sfger.nik_employee, sfger.name, sfger.position_routing,
				mpd.id_position_routing, mpd.id_location, sfger.work_location,
				sfger.job_grade, sfger.department, sfger.regional, sfger.branch, 
				sfger.principal, sfger.parent_emp_name AS direct_spv, sfger.indirect_emp_name AS imm_manager   
				FROM public.sp_funct_get_employee_report_all(".session('id_company').") sfger
				JOIN (
					SELECT max(sfger.id_employee) AS id_employee, sfger.nik_employee
					FROM public.sp_funct_get_employee_report_all(".session('id_company').") sfger	
					GROUP BY sfger.nik_employee				
				) AS j_id
				ON sfger.id_employee = j_id.id_employee
				LEFT JOIN public.master_position_detail mpd
				ON sfger.position_detail = mpd.description
				WHERE sfger.nik_employee = '".$data['nik_employee']."'
				ORDER BY sfger.name ASC";
	*/
        $result = (Array) DB::select($sql)[0];
        $sql_detail = "SELECT he3.id_employee AS id_direct, he3.name AS direct_spv, hat.id_employee_approval, he2.name AS direct_mgr, mgd.code AS app_code, 
						mgd.description AS app_status, hah.description AS app_hierarchy, mgd2.code AS cat_code,
						mpr.description AS desc_route, ml.description AS desc_location, bisc.* 
						FROM integration.bgen_integration_sales_code bisc
						JOIN public.hr_employee he
						ON bisc.id_employee = he.id_employee
						LEFT JOIN public.hr_approval_transaction hat
						ON bisc.id_integration_sales_code = hat.id_source_transaction AND hat.source_transaction_type = '$integrationType'
						LEFT JOIN public.hr_employee he2
						ON hat.id_employee_approval = he2.id_employee
						LEFT JOIN public.master_general_data mgd
						ON bisc.id_approval_status = mgd.id_general_data
						LEFT JOIN public.hr_approval_header hah
						ON bisc.id_approval = hah.id_approval
						LEFT JOIN public.master_general_data mgd2
						ON bisc.id_transition_category = mgd2.id_general_data
						LEFT JOIN public.master_position_routing mpr
						ON bisc.id_position_route_destination = mpr.id_routing
						LEFT JOIN public.master_location ml
						ON bisc.id_location_destination = ml.id_location 
						LEFT JOIN public.hr_employee he3
						ON bisc.id_direct_chief = he3.id_employee
						WHERE he.nik_employee = '".$data['nik_employee']."'
						AND bisc.integration_type = '$integrationType'
						ORDER BY bisc.id_integration_sales_code ASC";
        $result_menu = DB::select($sql_detail);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_integration_sales_code')->toArray();
		
        $result['bgen'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['bgen'][] = [
                'id_integration_sales_code' => $group_menu[$value][0]->id_integration_sales_code,
                'id_branch' => $group_menu[$value][0]->id_branch,
                'id_principal' => $group_menu[$value][0]->id_principal,
                'id_transition_category' => $group_menu[$value][0]->id_transition_category,
                'cat_code' => $group_menu[$value][0]->cat_code,
                'id_location_destination' => $group_menu[$value][0]->id_location_destination,
                'desc_location' => $group_menu[$value][0]->desc_location,
                'id_position_route_destination' => $group_menu[$value][0]->id_position_route_destination,
                'desc_route' => $group_menu[$value][0]->desc_route,
                'sales_code' => $group_menu[$value][0]->sales_code,
                'parent_sales_code' => $group_menu[$value][0]->parent_sales_code,
                'is_synchronize_flag' => $group_menu[$value][0]->is_synchronize_flag,
                'id_approval' => $group_menu[$value][0]->id_approval,
                'app_hierarchy' => $group_menu[$value][0]->app_hierarchy,
                'id_employee_approval' => $group_menu[$value][0]->id_employee_approval,
                'direct_mgr' => $group_menu[$value][0]->direct_mgr,
                'direct_spv' => $group_menu[$value][0]->direct_spv,
				'id_direct' => $group_menu[$value][0]->id_direct,
                'id_approval_status' => $group_menu[$value][0]->id_approval_status,
                'app_code' => $group_menu[$value][0]->app_code,
                'app_status' => $group_menu[$value][0]->app_status,
                'note_rejected' => $group_menu[$value][0]->note_rejected,
                'note_revised' => $group_menu[$value][0]->note_revised,
				'synchronize_message' => $group_menu[$value][0]->synchronize_message,
                'status' => $group_menu[$value][0]->status,
                'id_company' => $group_menu[$value][0]->id_company,
			];
        }
     //   dd($result);
        return $result;
    }
	
	public static function get_approval_by($idEmp)
	{
		$sql_approval_hirarki = "SELECT hah.id_approval id, hah.description text
		FROM master_general_data mgd
		JOIN hr_approval_header hah
		ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = ".session('id_company')."
		LEFT JOIN master_general_type mgt
		ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
		WHERE mgd.id_company = ".session('id_company')." AND mgd.code = 'Sales_Code'";
		$sql_approval_hirarki = DB::select($sql_approval_hirarki);
		
			$sql_approval_by = "SELECT * FROM (
										SELECT he.id_employee id, he.name text
											FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,Array['gm-level','manager-level']) sfao
											LEFT JOIN hr_employee he 
											ON he.id_employee=sfao.id_employee_approval
											LIMIT 1			
								) AS ok							
								UNION ALL
								SELECT * FROM (
									SELECT DISTINCT he.id_employee id, he.name text
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
													FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,Array['gm-level','manager-level']) sfao
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
										FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,Array['gm-level','manager-level']) sfao
										LIMIT 1
									)
									ORDER BY he.name ASC
								) AS oke";
		/*
			$sql_approval_by = "SELECT he2.id_employee id, he2.name text FROM (
				SELECT (
				SELECT hah.id_approval 
				FROM master_general_data mgd
				JOIN hr_approval_header hah
				ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = ".session('id_company')."
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
				WHERE mgd.id_company = ".session('id_company')." AND mgd.code = 'Sales_Code'
				), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
				FROM sp_funct_approval_organization_hierarchy_view (? ,".session('id_company').",null,Array['gm-level','manager-level']) sfao
				left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval
				LIMIT 1
				) as hirar
				join hr_employee he2 
				on hirar.id_employee_approval=he2.id_employee";
		*/
				$sql_approval_by = DB::select($sql_approval_by);

		return ['sql_approval_hirarki'=>$sql_approval_hirarki,'sql_approval_by'=>$sql_approval_by];
	}

	public static function get_direct_spv($idEmp) {
        $sql = "SELECT * FROM (SELECT he.id_employee id, he.name text
					FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,null) sfao
					LEFT JOIN hr_employee he 
					ON he.id_employee=sfao.id_employee_approval
				LIMIT 1
				) AS ok
				UNION ALL
				SELECT * FROM (
				SELECT DISTINCT he.id_employee id, he.name text
						FROM hr_employee he
						JOIN master_position_detail mpd
						ON he.id_employee = mpd.id_employee
						JOIN master_position_routing mpr
						ON mpd.id_position_routing = mpr.id_routing
						JOIN master_job_grade mjg
						ON mpr.id_job_grade = mjg.id_job_grade
						JOIN master_job_position mjp
						ON mpr.id_position = mjp.id_position 
						WHERE he.status = 'A' AND mjg.job_class_group IN('spv-level','manager-level') 
						AND mjp.id_dept =(SELECT mjp.id_dept FROM(
							SELECT sfao.id_detail_chief
								FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,null) sfao
							LIMIT 1
						) AS j_g
						JOIN master_position_detail mpd
						ON j_g.id_detail_chief = mpd.id_position_detail
						JOIN master_position_routing mpr
						ON mpd.id_position_routing = mpr.id_routing
						JOIN master_job_position mjp
						ON mpr.id_position = mjp.id_position)
						AND he.id_employee NOT IN(		
							SELECT sfao.id_employee_approval
								FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,null) sfao
							LIMIT 1			
						)
					UNION ALL
						SELECT he.id_employee id, he.name text
						FROM master_position_detail mpd
						JOIN master_position_detail mpd2
						ON mpd.parent_id_position_detail = mpd2.id_position_detail
						LEFT JOIN hr_employee he
						ON mpd2.id_employee = he.id_employee
						WHERE mpd.id_employee = ".$idEmp." AND mpd.id_company = ".session('id_company')." AND
						mpd.secondary_position = true
				) AS oke
				ORDER BY text ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_approval($data) {
		$id_approval = $data;
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						hah.hierarchy_type,
						mgd.code,
						(select id_general_data from master_general_data where code = 'Request_Approval' and id_company =".session('id_company').") as id_approval_status
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				WHERE hah.id_approval ='".$id_approval."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_approved($id_approval) {
        $sql = "SELECT mgd.id_general_data, mgd.code 
				FROM master_general_data mgd
				WHERE mgd.id_general_data = ".$id_approval;
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_approval_trans($idEmp) {
		$sql = "SELECT (
				SELECT hah.id_approval 
				FROM master_general_data mgd
				JOIN hr_approval_header hah
				ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = ".session('id_company')."
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
				WHERE mgd.id_company = ".session('id_company')." AND mgd.code = 'Sales_Code'
				), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
				FROM sp_funct_approval_organization_hierarchy_view (? ,".session('id_company').",null,Array['gm-level','manager-level']) sfao
				left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval
				LIMIT 1";	
        $result = DB::select($sql,[$idEmp]);
        return $result;
    }
	
	public static function get_update_status($idStatus) {
		$sql = "SELECT bisc.*, mgd.code, mgd.description AS app_status
				FROM integration.bgen_integration_sales_code bisc
				LEFT JOIN public.master_general_data mgd 
				ON bisc.id_approval_status = mgd.id_general_data
				WHERE bisc.id_company = ".session('id_company')." AND bisc.id_integration_sales_code = ?";	
        $result = DB::select($sql,[$idStatus]);
        return $result;
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Cancel' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }

	public static function getSync($nikEmployee = null) {
		$where = '';
		if($nikEmployee) {
			$where .= "AND he2.nik_employee = '".$nikEmployee."'";
		}
		$data = DB::select("SELECT
								COALESCE(mpd.assigned_to_company, mpd2.assigned_to_company) AS assigned_to_company,
								he2.id_employee,
								he2.nik_employee,
								COALESCE(he3.nik_employee, sfger.nik_parent_emp_name) AS parent_nik,
								he2.name,
								he2.idcard_address,
								he2.address_home,
								he2.work_phone,
								he2.home_base,
								mc.company_code AS company_code,
								mc.address AS company_address,
								mc.company_phone AS company_phone,
								bisc.*,
								mb.id_branch_erp,
								mp.erp_principal_code,
								ml.description AS location,
								mpr.description AS position
							FROM
								hr_employee he2
							JOIN integration.bgen_integration_sales_code bisc ON
								he2.id_employee = bisc.id_employee
							LEFT JOIN master_general_data mgd ON 
								bisc.id_approval_status = mgd.id_general_data
							LEFT JOIN master_general_data mgd2 ON 
								bisc.id_transition_category = mgd2.id_general_data
							LEFT JOIN master_branch mb ON 
								bisc.id_branch = mb.id_branch
							LEFT JOIN master_principal mp ON 
								bisc.id_principal = mp.id_principal
							LEFT JOIN master_location ml ON 
								bisc.id_location_destination = ml.id_location
							LEFT JOIN master_position_routing mpr ON 
								bisc.id_position_route_destination = mpr.id_routing
							LEFT JOIN hr_career_transaction hct ON
								he2.id_employee = hct.id_employee
							LEFT JOIN master_position_detail mpd ON 
							 	(he2.id_employee = mpd.id_employee 
							 	OR he2.id_employee = mpd.id_employee2)
							 	AND mpd.secondary_position = FALSE
							LEFT JOIN master_position_detail mpd2 ON 
							 	hct.id_position_detail = mpd2.id_position_detail
							LEFT JOIN master_company mc ON 
								COALESCE(mpd.id_company, mpd2.id_company) = mc.id_company
							LEFT JOIN hr_employee he3 ON
								bisc.id_direct_chief = he3.id_employee 
							LEFT JOIN sp_funct_get_employee_report(mpd.id_company) sfger ON
								he2.id_employee = sfger.id_employee
							WHERE 
								mc.company_code in('BCP', 'KAS')
								AND (mgd.code = 'Approved' OR mgd2.code != 'Join')
								AND bisc.integration_type = 'Sales_Code'
								-- AND bisc.is_synchronize_flag = FALSE
							".$where.";");
		return $data;
	}

	public static function getParentSalesCode($nik_employee, $branch, $division, $id_company = null) {
		if(!$id_company) {
			$id_company = session('id_company');
		}
		$sql = "SELECT
					sfger.id_employee,
					sfger.nik_employee,
					sfger.name,
					he.id_employee AS id_employee_parent,
					sfger.nik_parent_emp_name,
					sfger.parent_emp_name AS direct_spv,
					bisc_parent.sales_code AS sales_code_parent,
					sfger.status_active
				FROM
					public.sp_funct_get_employee_report_all(?) sfger
				JOIN hr_employee he ON
					sfger.nik_parent_emp_name = he.nik_employee
					AND he.status = 'A'
				JOIN integration.bgen_integration_sales_code bisc_parent ON
					he.id_employee = bisc_parent.id_employee
				WHERE
					sfger.nik_employee = ?
					AND bisc_parent.id_branch = ?
					AND bisc_parent.id_principal = ?
				";
		return DB::selectOne($sql, [$id_company, $nik_employee, $branch, $division]);
	}
	
	public static function getExport($group_branch, $nik=null, $id_dept = null, $status) {
		$branch = "";
		$idNik = "";
		if($group_branch || $group_branch != ""){
			$branch = " AND sflb.id_branch IN(".$group_branch.")";
		}
		if($nik){
			$idNik = " AND sflb.nik_employee IN(".implode(",", $nik).")";
		}
		$dept = "";
		if($id_dept && is_numeric($id_dept)) {
			$dept = " AND sflb.id_dept = ".$id_dept;
		}
		$sql= "SELECT bisc.id_integration_sales_code, sflb.id_employee, sflb.name, sflb.nik_employee, 
				sflb.position_routing AS position, sflb.department, sflb.job_grade, sflb.join_date, 
				mb.description AS branch_bgen, mp.description AS principal_bgen, mgd.description AS category, 
				bisc.sales_code, bisc.parent_sales_code, bisc.is_synchronize_flag AS sync, 
				he.name AS im_mgr_approve, he2.name AS direct_spv
				FROM public.sp_funct_list_employee_bgen(".session('id_company').") sflb
				JOIN integration.bgen_integration_sales_code bisc
				ON sflb.id_employee = bisc.id_employee
				LEFT JOIN public.master_branch mb
				ON bisc.id_branch = mb.id_branch
				LEFT JOIN public.master_principal mp
				ON bisc.id_principal = mp.id_principal 
				LEFT JOIN public.master_general_data mgd
				ON bisc.id_transition_category = mgd.id_general_data
				LEFT JOIN public.hr_approval_transaction hat
				ON bisc.id_integration_sales_code = hat.id_source_transaction AND hat.source_transaction_type = 'Sales_Code'
				LEFT JOIN public.hr_employee he
				ON hat.id_employee_approval = he.id_employee
				LEFT JOIN public.hr_employee he2
				ON bisc.id_direct_chief = he2.id_employee
				WHERE sflb.status='".$status."' ".$branch." ".$idNik." ".$dept."
				ORDER BY sflb.name ASC";	
        $result = DB::select($sql);
        return $result;
    }

	public static function getOasysSync($nikEmp = null) {
		$users = DB::select("SELECT DISTINCT
								bisc.id_integration_sales_code,
								bisc.id_branch,
								bisc.id_principal,
								he2.id_employee,
								he2.nik_employee AS nik,
								he2.name,
								mu.password,
								he2.mobile_phone AS mobile_no,
								he2.private_mail AS email,
								COALESCE(sfger.nik_parent_emp_name, he3.nik_employee) AS nik_supervisor,
								mu.mobile_firebase_token AS firebase_id,
								mpd.id_position_detail
							FROM integration.bgen_integration_sales_code bisc 
							JOIN master_general_data mgd ON bisc.id_approval_status = mgd.id_general_data
							JOIN hr_employee he ON bisc.id_employee = he.id_employee
							JOIN hr_employee he2 ON he.nik_employee = he2.nik_employee
							JOIN master_users mu ON he2.id_user = mu.id_user
							JOIN master_position_detail mpd ON he2.id_employee = mpd.id_employee OR he2.id_employee = mpd.id_employee2
							LEFT JOIN master_position_detail mpd2 ON mpd.parent_id_position_detail = mpd2.id_position_detail
							LEFT JOIN hr_employee he3 ON mpd2.id_employee = he3.id_employee OR mpd2.id_employee2 = he3.id_employee
							LEFT JOIN sp_funct_get_employee_report(bisc.id_company) sfger ON he.id_employee = sfger.id_employee
							WHERE bisc.integration_type = 'Biz_Approval' 
							AND he.nik_employee = COALESCE(?, he.nik_employee)
							AND mgd.code = 'Approved'
							AND bisc.is_synchronize_flag = FALSE
							AND mpd.id_company = bisc.id_company",
							[$nikEmp]);
		foreach($users as $key => $user) {
			if($users[$key]->mobile_no == '0') {
				$users[$key]->mobile_no = null;
			}
			$users[$key]->user_role = DB::select("SELECT 
													mc.company_name,
													mc.company_code AS company_alias,
													mpd.id_position_routing AS hris_position_route_code
												FROM master_position_detail mpd
												JOIN master_company mc ON mpd.id_company = mc.id_company
												WHERE mpd.id_position_detail = ?", 
												[$user->id_position_detail]);
			$users[$key]->user_branch_division = DB::select("SELECT 
													mc.company_name,
													mc.company_code as company_alias,
													mb.id_branch as branch_hris_code
												FROM integration.bgen_integration_sales_code bisc
												JOIN master_company mc ON bisc.id_company = mc.id_company
												JOIN master_branch mb ON bisc.id_branch = mb.id_branch
												WHERE bisc.id_integration_sales_code = ?",
												[$user->id_integration_sales_code]);
			foreach($users[$key]->user_branch_division as $branchDivisionKey => $branchDivision) {
				$users[$key]->user_branch_division[$branchDivisionKey]->branch_hris_code = $branchDivision->branch_hris_code;
				$divisions = explode(",", trim($user->id_principal, "{}"));
				// $divisions = collect(DB::select("SELECT md.id_division
				// 	FROM master_principal mp
				// 	JOIN master_division md ON mp.id_division = md.id_division
				// 	WHERE mp.id_principal = ?", 
				// [$user->id_principal]))->pluck('id_division');
				$strDivisions = [];
				foreach($divisions as $division) {
					$strDivisions[] = $division;
				}
				$users[$key]->user_branch_division[$branchDivisionKey]->division_hris_code = array_unique($strDivisions);
			}
		}

		$roles = DB::select("SELECT DISTINCT
								mpr.id_routing AS hris_position_route_code, 
								mpr.description AS name,
								mc.company_code AS company_alias, 
								mc.company_name,
								CASE
									WHEN mpr.status = 'A' THEN TRUE
									ELSE FALSE
								END AS is_active
							FROM integration.bgen_integration_sales_code bisc
							JOIN master_general_data mgd ON bisc.id_approval_status = mgd.id_general_data
							JOIN hr_employee he ON bisc.id_employee = he.id_employee
							JOIN hr_employee he2 ON he.nik_employee = he2.nik_employee
							JOIN master_users mu ON he2.id_user = mu.id_user
							JOIN master_position_detail mpd ON bisc.id_employee = mpd.id_employee
							JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing
							JOIN master_company mc ON mpr.id_company = mc.id_company
							WHERE he.nik_employee = COALESCE(?, he.nik_employee) 
							AND bisc.integration_type = 'Biz_Approval'
							AND mgd.code = 'Approved'
							AND bisc.is_synchronize_flag = FALSE
							", [$nikEmp]);

		$create = new \stdClass;
		$create->roles = $roles;
		$create->users = $users;
		return $create;
	}

	public static function getOasysUpdateSync($nikEmp = null, $syncRole = true, $unsynchronizedOnly = true) {
		$userWhere = "AND bisc.update_date >= now() - INTERVAL '1 day'";
		$bindings = [];
		if($nikEmp) {
			$userWhere = "AND he.nik_employee = COALESCE(?, he.nik_employee)";
			$bindings = [$nikEmp];
		}
		if($unsynchronizedOnly) {
			$unsynchronizedOnlyWhere = "bisc.is_synchronize_flag = FALSE";
		} else {
			$unsynchronizedOnlyWhere = "1=1";
		}
		
		$users = DB::select("SELECT 
					array_agg(bisc.id_integration_sales_code) as id_integration_sales_code,
					array_agg(bisc.id_principal) as id_principal,
					mpd.id_position_detail,
					he2.nik_employee AS nik,
					he2.name,
					mu.password,
					he2.mobile_phone AS mobile_no,
					he2.private_mail AS email,
					COALESCE(sfger.nik_parent_emp_name, he3.nik_employee) AS nik_supervisor,
					mu.mobile_firebase_token AS firebase_id
				FROM integration.bgen_integration_sales_code bisc
				JOIN master_general_data mgd ON bisc.id_approval_status = mgd.id_general_data
				JOIN hr_employee he ON bisc.id_employee = he.id_employee
				JOIN hr_employee he2 ON he.nik_employee = he2.nik_employee
				JOIN master_users mu ON he.id_user = mu.id_user
				JOIN master_position_detail mpd ON he2.id_employee = mpd.id_employee OR he2.id_employee = mpd.id_employee2
				LEFT JOIN master_position_detail mpd2 ON mpd.parent_id_position_detail = mpd2.id_position_detail
				LEFT JOIN hr_employee he3 ON mpd2.id_employee = he3.id_employee OR mpd2.id_employee2 = he3.id_employee
				LEFT JOIN sp_funct_get_employee_report(bisc.id_company) sfger ON he.id_employee = sfger.id_employee
				WHERE 
					$unsynchronizedOnlyWhere
					AND bisc.integration_type = 'Biz_Approval'
					$userWhere
					AND mgd.code = 'Approved'
					AND mpd.id_company = bisc.id_company
				GROUP BY he2.nik_employee, he2.name, he3.nik_employee, mu.PASSWORD, he2.mobile_phone, he2.private_mail, sfger.nik_parent_emp_name, mu.mobile_firebase_token, mpd.id_position_detail", $bindings);
		// $users = collect($users)->groupBy('id_employee');
		foreach($users as $key => $user) {
			if($users[$key]->mobile_no == '0') {
				$users[$key]->mobile_no = null;
			}
			$users[$key]->user_role = DB::select("SELECT
											mc.company_name,
											mc.company_code as company_alias,
											mpd.id_position_routing as hris_position_route_code
										FROM master_position_detail mpd
										JOIN master_company mc ON mpd.id_company = mc.id_company
										WHERE mpd.id_position_detail = ?", 
									[$user->id_position_detail]);

			$users[$key]->user_branch_division = DB::select("SELECT 
											mc.company_name,
											mc.company_code as company_alias,
											mb.id_branch as branch_hris_code
										FROM integration.bgen_integration_sales_code bisc
										JOIN master_company mc ON bisc.id_company = mc.id_company
										JOIN master_branch mb ON bisc.id_branch = mb.id_branch
										WHERE bisc.id_integration_sales_code IN(".trim($user->id_integration_sales_code, '{}').")
										GROUP BY mc.company_name, mc.company_code, mb.id_branch",
									[]);

			foreach($users[$key]->user_branch_division as $branchDivisionKey => $branchDivision) {
				$users[$key]->user_branch_division[$branchDivisionKey]->branch_hris_code = $branchDivision->branch_hris_code;
				$divisions = explode(",", trim($user->id_principal, "{}"));
				// collect(DB::select("SELECT md.id_division, mp.id_principal
				// 					FROM master_principal mp
				// 					JOIN master_division md ON mp.id_division = md.id_division
				// 					WHERE mp.id_principal IN(".trim($user->id_principal, "{}").")", 
				// 				[]))->pluck('id_principal');
				$strDivisions = [];
				foreach($divisions as $division) {
					$strDivisions[] = $division;
				}
				$users[$key]->user_branch_division[$branchDivisionKey]->division_hris_code = array_unique($strDivisions);
			}
		}
		$update = new \stdClass;
		if($syncRole) {
			$roles = DB::select("SELECT DISTINCT
								mpr.id_routing AS hris_position_route_code, 
								mpr.description AS name,
								mc.company_code AS company_alias, 
								mc.company_name,
								CASE
									WHEN mpr.status = 'A' THEN TRUE
									ELSE FALSE
								END AS is_active
							FROM integration.bgen_integration_sales_code bisc
							JOIN master_general_data mgd ON bisc.id_approval_status = mgd.id_general_data
							JOIN hr_employee he ON bisc.id_employee = he.id_employee
							JOIN master_users mu ON he.id_user = mu.id_user
							JOIN master_position_detail mpd ON bisc.id_employee = mpd.id_employee
							JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing
							JOIN master_company mc ON mpr.id_company = mc.id_company
							WHERE 
								bisc.is_synchronize_flag = FALSE
								AND bisc.update_date >= now() - INTERVAL '1 day'
								AND bisc.integration_type = 'Biz_Approval'
								AND mgd.code = 'Approved'");
			$update->roles = $roles;
		}
		$update->users = $users;
		return $update;
	}

	public static function oasysGetLocation($idCompany, $idEmployee) {
		return DB::select("SELECT 
								he.id_employee,
								he.id_user,
								he.name as name_employee,
								ml.id_location
						FROM master_position_detail mpd
						JOIN hr_employee he
						ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
						AND (mpd.id_company =  ?
							OR mpd.assigned_to_company = ?
						)
						JOIN master_location ml
					ON mpd.id_location = ml.id_location
					AND mpd.id_company = ml.id_company
					WHERE he.id_employee = ?
					AND mpd.status = 'A'
					--AND mpd.secondary_position = false
					",
			  [$idCompany, $idCompany, $idEmployee]);
	}

	public static function oasysGetApproval($idLocation, $idCompany, $hierarchyType) {
		$approval = DB::select("SELECT 
									had.*, mpd.id_employee AS id_employee_approval, he.name AS name_employee_approval
								FROM sp_funct_list_approval_hierarchy_view(array[$idLocation], $idCompany,'Biz_Approval', '$hierarchyType') sflav
								JOIN hr_approval_detail had ON sflav.id = had.id_approval AND sflav.id_position_detail = had.id_position_detail
								JOIN master_position_detail mpd ON had.id_position_detail = mpd.id_position_detail
								JOIN hr_employee he ON mpd.id_employee = he.id_employee",
								[]);
		return $approval;
	}

	public static function oasys_get_approval_by($idEmp)
	{
		$sql_approval_hirarki = "SELECT hah.id_approval id, hah.description text
		FROM master_general_data mgd
		JOIN hr_approval_header hah
		ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = ".session('id_company')."
		LEFT JOIN master_general_type mgt
		ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
		WHERE mgd.id_company = ".session('id_company')." AND mgd.code = 'Biz_Approval'";
		$sql_approval_hirarki = DB::select($sql_approval_hirarki);
		
			$sql_approval_by = "SELECT * FROM (
										SELECT he.id_employee id, he.name text
											FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,Array['gm-level','manager-level']) sfao
											LEFT JOIN hr_employee he 
											ON he.id_employee=sfao.id_employee_approval
											LIMIT 1			
								) AS ok							
								UNION ALL
								SELECT * FROM (
									SELECT DISTINCT he.id_employee id, he.name text
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
													FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,Array['gm-level','manager-level']) sfao
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
										FROM sp_funct_approval_organization_hierarchy_view (".$idEmp.",".session('id_company').",null,Array['gm-level','manager-level']) sfao
										LIMIT 1
									)
									ORDER BY he.name ASC
								) AS oke";
		/*
			$sql_approval_by = "SELECT he2.id_employee id, he2.name text FROM (
				SELECT (
				SELECT hah.id_approval 
				FROM master_general_data mgd
				JOIN hr_approval_header hah
				ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = ".session('id_company')."
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
				WHERE mgd.id_company = ".session('id_company')." AND mgd.code = 'Sales_Code'
				), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
				FROM sp_funct_approval_organization_hierarchy_view (? ,".session('id_company').",null,Array['gm-level','manager-level']) sfao
				left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval
				LIMIT 1
				) as hirar
				join hr_employee he2 
				on hirar.id_employee_approval=he2.id_employee";
		*/
				$sql_approval_by = DB::select($sql_approval_by);

		return ['sql_approval_hirarki'=>$sql_approval_hirarki,'sql_approval_by'=>$sql_approval_by];
	}
}
