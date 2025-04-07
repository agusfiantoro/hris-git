<?php

namespace App\Models\Recruitment\HiringRequest;

use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HiringRequest extends Model
{
	use HasFactory;
	
    protected $table = 'hr_hiring_request_header';
	protected $primaryKey = 'id_hiring_request_header';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_hiring_request_header', 'reference_number', 'id_employee_request', 'id_position_detail_employee_request', 'id_location_employee_request', 'recruitment_source', 'request_type', 'request_date', 'effective_date', 'approval_date', 'target_date', 'id_request_reason', 'id_position_routing_request', 'id_branch', 'pkwt_duration', 'assigned_to', 'skill_notes', 'id_approval_request', 'id_approval', 'id_approval_status', 'cc_email_to', 'is_web_posting', 'hiring_request_status', 'have_recommended_employee', 'attachment', 'reason_notes', 'note_rejected', 'note_revised', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getkode($idCompany=null){
		$idCompany = $idCompany ?? session('id_company');
		
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', $idCompany)->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-FPK-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%FPK-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%FPK-{$monthyear}%")->max('reference_number'), 15, 21);
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
	
	public static function get_dept_code() {
        $sql = "SELECT md.department_code
				FROM public.master_position_detail mpd
				LEFT JOIN public.hr_employee he
				ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
				LEFT JOIN public.master_position_routing mpr 
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN public.master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN public.master_department md
				ON mjp.id_dept = md.id_dept
				WHERE mpd.id_company = ? AND he.id_user  = ? ";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
	//	dd($result);
        return $result;
    }
	public static function getdata($group_branch,$dept_code) {
		if($group_branch == null){
			if($dept_code == "150_HR"){
				$branch = "";
			}
			else{
				$branch = " AND he.id_user = ".session('id_user');
			}			
		}
		else{
			$branch = " AND hhrh.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT j_detail.count_req, he.nik_employee, he.name AS emp_name, hhrh.*, mpr.description AS route_name, 
				mgd.description as desc_app_status, mgd.code as code_app_status, mb.description AS branch, 
				mr.description AS region, he2.name AS name_approval
				FROM hr_hiring_request_header hhrh
				LEFT JOIN hr_employee he
				ON hhrh.id_employee_request = he.id_employee
				LEFT JOIN master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN public.master_branch mb
				ON hhrh.id_branch = mb.id_branch
				LEFT JOIN public.master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN (
					SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_detail hhrd 
					GROUP BY id_hiring_request_header
				) AS j_detail
				ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
				LEFT JOIN hr_employee he2
				ON hhrh.id_approval_request = he2.id_employee
				WHERE hhrh.id_company = ? ".$branch."
				ORDER BY hhrh.id_hiring_request_header DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function getdata_summary($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = "AND hhrh.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT j_detail.count_req, he.nik_employee, he.name AS emp_name, hhrh.*, 
				mpr.description AS route_name, mb.description AS branch, mgd.description as desc_app_status, mgd.code as code_app_status, mr.description AS region
				FROM hr_hiring_request_header hhrh
				LEFT JOIN hr_employee he
				ON hhrh.id_employee_request = he.id_employee
				LEFT JOIN master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN master_branch mb
				ON hhrh.id_branch = mb.id_branch
				LEFT JOIN public.master_region mr
				ON mb.id_region = mr.id_region
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN (
					SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_detail hhrd 
					GROUP BY id_hiring_request_header
				) AS j_detail
				ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
				WHERE hhrh.id_company = ? AND mgd.code = 'Approved' ".$branch."
				ORDER BY hhrh.id_hiring_request_header DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function getdata_detail_summary($group_branch) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = "AND mpd.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT DISTINCT hhrh.reference_number, he.name AS emp_name,  mpd.description AS position_detail, 
				ml.description AS location, hhrh.request_type, he2.name AS emp_replace,  hhrd.*,
				he3.name AS created_by, hc.name AS name_candidate, mpd.id_branch,
				(hhrh.target_date::date - hc.hired_date::date) AS sla_days,
				CASE 
					WHEN (hhrh.target_date::date - hc.hired_date::date) >= 0  THEN 'plus'
					WHEN (hhrh.target_date::date - hc.hired_date::date) < 0  THEN 'minus'
					WHEN hc.hired_date::date IS NULL AND (hhrh.target_date::date - current_date) >= 0 THEN 'plus'
					WHEN hc.hired_date::date IS NULL AND (hhrh.target_date::date - current_date) < 0 THEN 'minus'
				END AS sla_sign,
				CASE 
					WHEN (hhrh.target_date::date - hc.hired_date::date) >= 0  THEN 'meet'
					WHEN (hhrh.target_date::date - hc.hired_date::date) < 0  THEN 'unmeet'
					ELSE '-'
				END AS sla_status
				FROM hr_hiring_request_detail hhrd
				LEFT JOIN hr_hiring_request_header hhrh
				ON hhrd.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN hr_employee he
				ON hhrh.id_employee_request = he.id_employee
				LEFT JOIN hr_employee he2
				ON hhrd.id_employee_replacement = he2.id_employee
				LEFT JOIN hr_employee he3
				ON hhrd.updated_by = he3.id_user
				LEFT JOIN master_position_detail mpd
				ON hhrd.id_position_detail_request = mpd.id_position_detail
				LEFT JOIN master_location ml
				ON hhrd.id_location = ml.id_location
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN web.hr_applied_candidate hac
				ON hhrd.id_hiring_request_detail = hac.id_hiring_request_detail 
				LEFT JOIN web.hr_candidate hc
				ON hac.id_candidate = hc.id_candidate
				WHERE hhrh.id_company = ? AND mgd.code = 'Approved' ".$branch."
				ORDER BY hhrd.id_hiring_request_header DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_employee_by() {
        $sql = "SELECT he.id_employee id, he.name text, mpr.id_routing, mpd.id_position_detail, mpd.id_location, mpr.description AS route_name, 
				mjg.job_class_group, ml.description AS location_name
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				WHERE mpd.id_company = ? AND he.id_user = ? AND he.status = 'A'";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
        return $result;
    }
	
	public static function get_emp_edit($data) {
		$id_pos_detail = $data['id_pos_detail'];
        $sql = "SELECT mpd.id_position_detail, mpr.id_routing, mpr.description AS route_name, mjg.job_class_group, ml.description AS location_name, mpd.id_employee 
				FROM master_position_detail mpd
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				WHERE mpd.id_position_detail = ?";
        $result = DB::select($sql,[$id_pos_detail])[0];
        return $result;
    }
	
	public static function get_approval_status() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
						FROM  master_general_data where status = 'A' and id_general_type = 7 and id_company =" . session('id_company')."
						ORDER BY sequence ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_pos_by($data) {
		$posDetail = JobPositionDetail::where('id_position_detail',$data['id_pos_detail'])->first();
		$posDetailGet = JobPositionDetail::where('id_employee',$posDetail->id_employee)->where('id_company',$posDetail->id_company)->get();
		foreach($posDetailGet as $key=>$val){
			$x[] = $val->id_position_detail;
			$y[] = $val->id_position_routing;
		}
		$id_pos_detail = implode(',',$x);
		$id_routing = implode(',',$y);
		$job_class_group = $data['job_class_group'];
		$route_name = $data['route_name'];
		$name_com_type = $data['name_com_type'];
		$name_req_type = $data['name_req_type'];
		if($name_req_type == 'REPLACEMENT'){
			$replace_select = "master_position_detail";
			$replace = "AND mpd_route.id_employee IS NOT NULL AND mpd_route.status = 'A'";
		}
		else{
			$replace_select = "sp_funct_cross_recruitment(null)";
			$replace = "AND mpd_route.id_employee IS NULL";
		}
		if($name_com_type == 'Corporate'){
			$where = "WHERE mpd_route.assigned_to_company IS NULL " .$replace. " AND mpd_route.id_company = ".session('id_company')."
						ORDER BY text ASC";
		}
		else{
			$where = "LEFT JOIN master_company mc
						ON mpd_route.assigned_to_company = mc.id_company
						WHERE mpd_route.assigned_to_company IS NOT NULL	" .$replace. " AND mc.company_type = 'os' AND mpd_route.id_company = ".session('id_company')."
						ORDER BY text ASC";
		}
		
		if($route_name == 'HEAD OF HR'){
			$sql = "SELECT DISTINCT mpd_route.id_position_routing id, CONCAT(u_route.description,' (',mb2.description,')') AS text, 
					mpd_route.id_branch 
					FROM ".$replace_select." mpd_route
					JOIN(
						SELECT DISTINCT mpr.id_routing, mpr.description 
						FROM master_position_detail mpd2
							LEFT JOIN master_position_routing mpr
							ON mpd2.id_position_routing = mpr.id_routing
							JOIN master_branch mb
							ON mpd2.id_branch = mb.id_branch
						WHERE mpd2.status = 'A'	
					UNION
					SELECT DISTINCT mpr.id_routing, mpr.description 
					FROM master_position_detail mpd2
						LEFT JOIN master_position_routing mpr
						ON mpd2.id_position_routing = mpr.id_routing
						JOIN master_branch mb
						ON mpd2.id_branch = mb.id_branch
						WHERE mpd2.parent_id_position_detail in(
							SELECT mpd.id_position_detail  FROM master_position_detail mpd
							WHERE mpd.id_position_routing IN(".$id_routing.") AND mpd.status = 'A'
						) AND mpd2.status = 'A'	
					UNION
					SELECT DISTINCT mpr.id_routing, mpr.description
					FROM master_position_detail mpd3
						LEFT JOIN master_position_routing mpr
						ON mpd3.id_position_routing = mpr.id_routing
						LEFT JOIN master_job_grade mjg
						ON mpr.id_job_grade = mjg.id_job_grade
						JOIN master_branch mb
						ON mpd3.id_branch = mb.id_branch
						WHERE mpd3.parent_id_position_detail IN(
							SELECT mpd2.id_position_detail FROM master_position_detail mpd2
							WHERE mpd2.parent_id_position_detail in(
								SELECT mpd.id_position_detail  FROM master_position_detail mpd
								WHERE mpd.id_position_routing IN(".$id_routing.") AND mpd.status = 'A'
							) AND mpd2.status = 'A'
						) AND mpd3.status = 'A' AND mjg.job_class_group = 'manager-level'
					) AS u_route
					ON mpd_route.id_position_routing = u_route.id_routing 
					JOIN master_branch mb2
					ON mpd_route.id_branch = mb2.id_branch "
					.$where;
		}
		else if($job_class_group == 'gm-level'){
			$sql="SELECT DISTINCT mpd_route.id_position_routing id, u_route.description text, mpd_route.id_branch FROM ".$replace_select." mpd_route
					JOIN(
						SELECT DISTINCT mpr.id_routing, CONCAT(mpr.description,' (',mb.description,')') AS description, mpd2.id_position_detail 
						FROM master_position_detail mpd2
						LEFT JOIN master_position_routing mpr
						ON mpd2.id_position_routing = mpr.id_routing
						JOIN master_branch mb
						ON mpd2.id_branch = mb.id_branch
						WHERE mpd2.parent_id_position_detail in(
							SELECT mpd.id_position_detail  FROM master_position_detail mpd
							WHERE mpd.id_position_routing IN(".$id_routing.") AND mpd.status = 'A' AND mpd.id_position_detail IN(".$id_pos_detail.")
						) AND mpd2.status = 'A'	
					UNION
					SELECT DISTINCT mpr.id_routing, CONCAT(mpr.description,' (',mb2.description,')') AS description, mpd3.id_position_detail 
					FROM master_position_detail mpd3
						LEFT JOIN master_position_routing mpr
						ON mpd3.id_position_routing = mpr.id_routing
						LEFT JOIN master_job_grade mjg
						ON mpr.id_job_grade = mjg.id_job_grade
						JOIN master_branch mb2
						ON mpd3.id_branch = mb2.id_branch
						WHERE mpd3.parent_id_position_detail IN(
							SELECT mpd2.id_position_detail FROM master_position_detail mpd2
							WHERE mpd2.parent_id_position_detail in(
								SELECT mpd.id_position_detail  FROM master_position_detail mpd
								WHERE mpd.id_position_routing IN(".$id_routing.") AND mpd.status = 'A' AND mpd.id_position_detail IN(".$id_pos_detail.")
							) AND mpd2.status = 'A'
						) AND mpd3.status = 'A' AND mjg.job_class_group in('manager-level','spv-level','ant-level')
					) AS u_route
					ON mpd_route.id_position_routing = u_route.id_routing AND mpd_route.id_position_detail = u_route.id_position_detail "
					.$where;
		}
		else if($job_class_group == 'manager-level'){
			$sql="SELECT DISTINCT mpd_route.id_position_routing id, u_route.description text, mpd_route.id_branch FROM ".$replace_select." mpd_route
					JOIN(
						SELECT DISTINCT mpr.id_routing, CONCAT(mpr.description,' (',mb.description,')') AS description, mpd2.id_position_detail 
						FROM master_position_detail mpd2
						LEFT JOIN master_position_routing mpr
						ON mpd2.id_position_routing = mpr.id_routing
						JOIN master_branch mb
						ON mpd2.id_branch = mb.id_branch
						WHERE mpd2.parent_id_position_detail in(
							SELECT mpd.id_position_detail  FROM master_position_detail mpd
							WHERE mpd.id_position_routing IN(".$id_routing.") AND mpd.status = 'A' AND mpd.id_position_detail IN(".$id_pos_detail.")
						) AND mpd2.status = 'A'
					UNION
					SELECT DISTINCT mpr.id_routing, CONCAT(mpr.description,' (',mb.description,')') AS description, mpd3.id_position_detail 
					FROM master_position_detail mpd3
						LEFT JOIN master_position_routing mpr
						ON mpd3.id_position_routing = mpr.id_routing
						JOIN master_branch mb
						ON mpd3.id_branch = mb.id_branch
						WHERE mpd3.parent_id_position_detail IN(
							SELECT mpd2.id_position_detail FROM master_position_detail mpd2
							WHERE mpd2.parent_id_position_detail in(
								SELECT mpd.id_position_detail  FROM master_position_detail mpd
								WHERE mpd.id_position_routing IN(".$id_routing.") AND mpd.status = 'A' AND mpd.id_position_detail IN(".$id_pos_detail.")
							) AND mpd2.status = 'A'
						) AND mpd3.status = 'A'
					UNION
					SELECT DISTINCT mpr.id_routing, CONCAT(mpr.description,' (',mb.description,')') AS description, mpd4.id_position_detail 
					FROM master_position_detail mpd4
						LEFT JOIN master_position_routing mpr
						ON mpd4.id_position_routing = mpr.id_routing
						JOIN master_branch mb
						ON mpd4.id_branch = mb.id_branch
						WHERE mpd4.parent_id_position_detail IN(
							SELECT mpd3.id_position_detail FROM master_position_detail mpd3
							LEFT JOIN master_position_routing mpr
							ON mpd3.id_position_routing = mpr.id_routing
							WHERE mpd3.parent_id_position_detail IN(
								SELECT mpd2.id_position_detail FROM master_position_detail mpd2
								WHERE mpd2.parent_id_position_detail in(
									SELECT mpd.id_position_detail  FROM master_position_detail mpd
									WHERE mpd.id_position_routing IN(".$id_routing.") AND mpd.status = 'A' AND mpd.id_position_detail IN(".$id_pos_detail.")
								) AND mpd2.status = 'A'
							) AND mpd3.status = 'A'
						) AND mpd4.status = 'A'
					) AS u_route
					ON mpd_route.id_position_routing = u_route.id_routing AND mpd_route.id_position_detail = u_route.id_position_detail "
					.$where;
		}
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_sla($data) {
		$route_sla = $data['route_sla'];
        $sql = "SELECT DISTINCT mjg.description AS grade_req, coalesce(mpr.max_hiring_days,mjg.max_hiring_days) AS sla 
				FROM master_position_detail mpd 
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE mpd.id_position_routing = ".$route_sla;
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function get_branch($data) {
		$id_route = $data['id_route'];
		$id_branch = $data['id_branch'];
		$name_req_type = $data['name_req_type'];
		if($name_req_type == 'REPLACEMENT'){
			$replace_select = "mb.description text, mpd_route.id_position_routing 
								FROM master_position_detail mpd_route
								LEFT JOIN master_branch mb
								ON mpd_route.id_branch = mb.id_branch";
			$replace = "AND mpd_route.id_employee IS NOT NULL ";
		}
		else{
			$replace_select = "mpd_route.branch text, mpd_route.id_position_routing  FROM sp_funct_cross_recruitment(null) mpd_route";
			$replace = "AND mpd_route.id_employee IS NULL ";
		}
        $sql = "SELECT DISTINCT mpd_route.id_branch id, ".$replace_select."
				WHERE mpd_route.id_position_routing = ".$id_route ." AND mpd_route.id_branch = ".$id_branch."  ".$replace;
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_pos_detail($data) {
		$id_route = $data['id_route'];
		$id_branch = $data['id_branch'];
		$name_req_type = $data['name_req_type'];
		$status_view = $data['status_view'];
		
		if($name_req_type == 'REPLACEMENT'){
			$replace_select = "SELECT * FROM (
				SELECT mpd.id_position_detail id, CONCAT(mpd.description,' (',he.name,'-',he.nik_employee,')') text, mpd.description AS position_detail, 
				mpd.id_position_routing, mpd.id_branch, he.id_employee, 
				CASE
					WHEN he.name IS NOT NULL THEN CONCAT(he.name,' (',he.nik_employee,')')
					ELSE '-'
				END AS name,
				ml.id_location, ml.description AS location, 
				he2.name AS name_supervisor 
				FROM master_position_detail mpd
				LEFT JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail = mpd2.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd2.id_employee = he2.id_employee 
				LEFT JOIN master_location ml 
				ON mpd.id_location = ml.id_location	
				WHERE mpd.status = 'A'
			) sfcjl";
			if($status_view == 'view'){
				$replace = "AND (sfcjl.id_employee IS NOT NULL OR sfcjl.id_employee IS NULL) ";
			}
			else{
				$replace = "AND sfcjl.id_employee IS NOT NULL ";				
			}
		}
		else{
			$replace_select = "SELECT id_position_detail id, position_detail text, sfcjl.* from sp_funct_cross_recruitment(null) sfcjl";
			$replace = "AND sfcjl.id_employee IS NULL ";
		}
	//	dd($id_route);
        $sql = $replace_select."  
					LEFT JOIN (
							SELECT hct.id_career_transaction, hct.id_employee,  hct.id_transaction_type, mgd2.description AS trans_type, hct.effective_date 
								FROM hr_career_transaction hct
								JOIN (
									SELECT hct_emp.id_employee, max(hct_emp.id_career_transaction) AS max_id_career_transaction
									FROM hr_career_transaction hct_emp
									JOIN master_general_data mgd
									ON hct_emp.id_approval_status = mgd.id_general_data
									WHERE mgd.code = 'Approved'
									GROUP BY id_employee
								) AS j_hct
							ON hct.id_career_transaction = j_hct.max_id_career_transaction
							JOIN master_general_data mgd2
							ON hct.id_transaction_type = mgd2.id_general_data
						) AS j_emp
					ON sfcjl.id_employee = j_emp.id_employee
				WHERE sfcjl.id_branch = ".$id_branch." AND sfcjl.id_position_routing = " .$id_route. " " .$replace.
				"ORDER BY sfcjl.position_detail ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_emp_reco() {
        $sql = "SELECT  he.id_employee id, CONCAT(he.name,' (',he.nik_employee,')') AS text, mpd.id_position_detail AS id_position_detail_reco, mpd.description AS position_name, 
						ml.id_location AS id_location_reco, ml.description AS location_reco, mc.company_name AS company_reco
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_company mc
				ON he.id_company = mc.id_company 
				WHERE mpd.id_company = ?  AND he.status = 'A' AND mpd.secondary_position = false AND mc.company_type = 'corporate'
				ORDER BY he.name ASC";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_hiring_edit($data) {
        $result = [];
        $sql = "SELECT he.id_employee id, he.name text, he.nik_employee, he.name AS emp_name, hhrh.*, mpr.description AS route_name, 
				mgd.description as desc_app_status, mgd.code as code_app_status
				FROM hr_hiring_request_header hhrh
				LEFT JOIN hr_employee he
				ON hhrh.id_employee_request = he.id_employee
				LEFT JOIN master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				WHERE hhrh.id_hiring_request_header   = ?";
        $result = (Array) DB::select($sql, [$data['id_hiring_request_header']])[0];

        $sql_detail = "SELECT * FROM hr_hiring_request_detail WHERE id_hiring_request_header  = ?";
        $result_menu = DB::select($sql_detail, [$data['id_hiring_request_header']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_hiring_request_detail')->toArray();
		
		$sql2 = "SELECT * FROM hr_hiring_request_recommendation WHERE id_hiring_request_header  = ?";
        $result_menu2 = DB::select($sql2, [$data['id_hiring_request_header']]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_hiring_request_recommendation')->toArray();

        $result['hiring'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['hiring'][] = [
                'id_hiring_request_detail' => $group_menu[$value][0]->id_hiring_request_detail,
                'id_employee_replacement' => $group_menu[$value][0]->id_employee_replacement,
                'id_position_detail_request' => $group_menu[$value][0]->id_position_detail_request,
                'id_location' => $group_menu[$value][0]->id_location,
                'id_assigned_to_company' => $group_menu[$value][0]->id_assigned_to_company,
                'hiring_status' => $group_menu[$value][0]->hiring_status, 
			];
        }
		
		$result['reco'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['reco'][] = [
                'id_hiring_request_recommendation' => $group_menu2[$value][0]->id_hiring_request_recommendation,
                'id_employee_recommendation' => $group_menu2[$value][0]->id_employee_recommendation,
                'id_position_detail_reco' => $group_menu2[$value][0]->id_position_detail,
                'id_location_reco' => $group_menu2[$value][0]->id_location,
			];
        }
    //    dd($result);
        return $result;
    }
	
	public static function get_summary_edit($data) {
        $result = [];
        $sql = "SELECT mpr2.job_description_detail, mpr2.skill_requirement, j_detail.count_req, he.nik_employee, he.name AS emp_name, mpr.description AS emp_pos_req, hhrh.*, 
				mpr2.description AS pos_req, mb.description AS branch, mjg.description AS grade_req, mjg.max_hiring_days AS sla, mgd.description as desc_app_status, 
				mgd.code as code_app_status, hah.description AS app_hierarchy, he2.name AS approval_name
				FROM hr_hiring_request_header hhrh
				LEFT JOIN hr_employee he
				ON hhrh.id_employee_request = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON hhrh.id_position_detail_employee_request = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing  = mpr.id_routing
				LEFT JOIN master_branch mb
				ON hhrh.id_branch = mb.id_branch
				LEFT JOIN master_position_routing mpr2
				ON hhrh.id_position_routing_request = mpr2.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr2.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_general_data mgd
				ON hhrh.id_approval_status = mgd.id_general_data
				LEFT JOIN hr_approval_header hah
				ON hhrh.id_approval = hah.id_approval 
				LEFT JOIN hr_employee he2
				ON hhrh.id_approval_request = he2.id_employee
				LEFT JOIN (
					SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_detail hhrd 
					GROUP BY id_hiring_request_header
				) AS j_detail
				ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
				WHERE hhrh.id_hiring_request_header   = ?";
        $result = (Array) DB::select($sql, [$data['id_hiring_request_header']])[0];

        $sql_detail = "SELECT hhrd.*, mpd.description AS pos_detail, he.name AS replace_name, ml.description AS loc_detail, hc.name AS name_candidate 
				FROM hr_hiring_request_detail hhrd
				LEFT JOIN master_position_detail mpd
				ON hhrd.id_position_detail_request = mpd.id_position_detail
				LEFT JOIN hr_employee he
				ON hhrd.id_employee_replacement = he.id_employee
				LEFT JOIN master_location ml
				ON hhrd.id_location = ml.id_location
				LEFT JOIN web.hr_applied_candidate hac
				ON hhrd.id_hiring_request_detail = hac.id_hiring_request_detail 
				LEFT JOIN web.hr_candidate hc
				ON hac.id_candidate = hc.id_candidate
				WHERE hhrd.id_hiring_request_header  = ?";
        $result_menu = DB::select($sql_detail, [$data['id_hiring_request_header']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_hiring_request_detail')->toArray();		
		$sql2 = "SELECT hhrr.*, CONCAT(he.name,' (',he.nik_employee,')') AS emp_reco, mpd.description AS pos_reco,
				ml.description AS loc_reco, mc.company_name AS com_reco
				FROM hr_hiring_request_recommendation hhrr
				LEFT JOIN hr_employee he
				ON hhrr.id_employee_recommendation = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON hhrr.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_location ml
				ON hhrr.id_location = ml.id_location
				LEFT JOIN master_company mc
				ON he.id_company = mc.id_company
				WHERE hhrr.id_hiring_request_header  = ?";
        $result_menu2 = DB::select($sql2, [$data['id_hiring_request_header']]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_hiring_request_recommendation')->toArray();

        $result['hiring'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['hiring'][] = [
                'id_hiring_request_detail' => $group_menu[$value][0]->id_hiring_request_detail,
                'replace_name' => $group_menu[$value][0]->replace_name,
                'pos_detail' => $group_menu[$value][0]->pos_detail,
                'loc_detail' => $group_menu[$value][0]->loc_detail,
            //    'id_assigned_to_company' => $group_menu[$value][0]->id_assigned_to_company,
                'hiring_status' => $group_menu[$value][0]->hiring_status,
                'notes' => $group_menu[$value][0]->notes,
                'update_date_notes' => $group_menu[$value][0]->update_date_notes,
                'name_candidate' => $group_menu[$value][0]->name_candidate,
			];
        }
		
		$result['reco'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['reco'][] = [
                'id_hiring_request_recommendation' => $group_menu2[$value][0]->id_hiring_request_recommendation,
                'emp_reco' => $group_menu2[$value][0]->emp_reco,
                'pos_reco' => $group_menu2[$value][0]->pos_reco,
                'loc_reco' => $group_menu2[$value][0]->loc_reco,
                'com_reco' => $group_menu2[$value][0]->com_reco,
			];
        }
    //    dd($result);
        return $result;
    }
	
	public static function get_detail_summary_edit($data) {
		$sql = "SELECT mpd.description AS position_detail, hhrd.*
				FROM hr_hiring_request_detail hhrd
				LEFT JOIN master_position_detail mpd
				ON hhrd.id_position_detail_request = mpd.id_position_detail
				WHERE hhrd.id_hiring_request_detail   = ?";
        $result = (Array) DB::select($sql, [$data['id_hiring_request_detail']])[0];
		return $result;
	}
	public static function submit_approve() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Request_Approval' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function approved() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Approved' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Cancel' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function reject() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Rejected' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function getdata_approval_status($data) {
		$id_hiring_request_header = $data['id_hiring_request_header'];
		$id_approval = $data['id_approval'];
        $sql = "select  hat.*, he.name, mgd.description as code, he.id_user 
				from    hr_approval_transaction hat
				left join    hr_employee he
				on      he.id_employee = hat.id_employee_approval
				join    master_general_data mgd
				on      hat.id_approval_status = mgd.id_general_data
				where   hat.id_company = ?
				and     hat.id_source_transaction = ?
				and     hat.id_approval = coalesce(?,hat.id_approval)";
        $result = DB::select($sql,[session('id_company'),$id_hiring_request_header,$id_approval]);

        return $result;
    }
	
	public static function get_hierachy_approver($data) {
		$id_approve = $data['id_approve'];
		$sql="SELECT	sfacus.id_approval, he.id_employee AS emp_approval, he.name AS name_employee
				FROM  sp_funct_approval_custom_hierarchy_view (null,".session('id_company').",".$id_approve.") sfacus	
				LEFT JOIN hr_employee he
				ON sfacus.id_employee = he.id_employee 
				WHERE sfacus.sequence = 1";
        $result = DB::select($sql);
        return $result;
    }
	
	/*
	public static function max_hiring($max_hiring) {
		$sql="SELECT mjg.id_job_grade, mjg.max_hiring_days FROM master_position_routing mpr
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade 
				WHERE mpr.id_routing = ".$max_hiring;
        $result = DB::select($sql);
        return $result;
    }
	*/
	
	public static function max_hiring($idCompany,$max_hiring,$idBranch) {
		$sql="SELECT total_days AS max_hiring_days FROM get_total_workdays_sla_fpk(?,?,?)";
        $result = DB::select($sql,[$idCompany,$max_hiring,$idBranch]);
        return $result;
    }
	public static function cek_duplicate($id_detail) {
		$sql="SELECT mpd.description AS job_name, hhrd.*  
			FROM hr_hiring_request_detail hhrd
			LEFT JOIN master_position_detail mpd
			ON hhrd.id_position_detail_request = mpd.id_position_detail
			LEFT JOIN hr_hiring_request_header hhrh
			ON hhrd.id_hiring_request_header = hhrh.id_hiring_request_header
			LEFT JOIN master_general_data mgd
			ON hhrh.id_approval_status = mgd.id_general_data
			WHERE hhrd.hiring_status = 'Hiring' AND hhrd.status = 'A' AND mgd.code NOT IN('Cancel','Rejected') AND hhrh.hiring_request_status NOT IN ('C','D') AND hhrd.id_position_detail_request = ".$id_detail;
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getdata_approval_mail($data) {
		$id_hiring_request_header = $data['id_hiring_request_header'];
		$id_approval = $data['id_approval'];
		$sql="SELECT hat.*, he.name, he.private_mail, 'FPK Request' AS permohonan
			FROM hr_approval_transaction hat
			LEFT JOIN hr_employee he
			ON hat.id_employee_approval = he.id_employee
			WHERE hat.id_source_transaction = ? AND hat.id_company = ? AND hat.id_approval = ?";
        $result = DB::select($sql,[$id_hiring_request_header,session('id_company'),$id_approval]);
        return $result;
    }
	
	public static function get_def_mail() {
        $sql = "SELECT hcer.recruitment_email 
				FROM hr_config_email_recruitment hcer
				WHERE hcer.id_position_detail IS NULL AND hcer.id_company = " . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_hr_email() {
        $sql = "SELECT he.private_mail AS id, CONCAT(he.name,' (',he.private_mail,')') AS text
				FROM hr_config_email_recruitment hcer
				LEFT JOIN master_position_detail mpd
				ON hcer.id_position_detail = mpd.id_position_detail
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				WHERE hcer.id_position_detail IS NOT NULL AND hcer.id_company = " . session('id_company')."
				ORDER BY he.name ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getdata_info($data) {
		$id_hiring_request_header = $data['id_hiring_request_header'];
		$sql = "SELECT hash.*, hc.name, mpr.description AS routing, mgd.code, mgd.description AS stage,
				CASE 
					WHEN hash.end_date IS NOT NULL THEN (hash.end_date::date - hash.start_date::date)
					ELSE (current_date - hash.start_date::date)
				END AS count_days
				FROM web.hr_applied_stage_history hash
				LEFT JOIN web.hr_applied_candidate hac
				ON hash.id_applied_candidate = hac.id_applied_candidate
				LEFT JOIN web.hr_candidate hc
				ON hac.id_candidate = hc.id_candidate
				LEFT JOIN public.master_general_data mgd
				ON hash.id_candidate_status = mgd.id_general_data
				LEFT JOIN public.hr_hiring_request_header hhrh
				ON hac.id_hiring_request_header = hhrh.id_hiring_request_header
				LEFT JOIN public.master_position_routing mpr
				ON hhrh.id_position_routing_request = mpr.id_routing
				WHERE hac.id_hiring_request_header = ?
				ORDER BY hash.id_applied_stage_history DESC";	
        $result = DB::select($sql,[$id_hiring_request_header]);
        return $result;
    }

	public static function get_existing_data_edit($result) {
		$result['request_position'] = DB::table('master_position_routing')
			->where('id_routing', $result['id_position_routing_request'])
			->select('id_routing as id', 'description as text')
			->first();
		$result['hierarchy_approval'] = DB::table('hr_approval_header')
			->where('id_approval', $result['id_approval'])
			->select('id_approval as id', 'description as text')
			->first();
		$result['branch'] = DB::table('master_branch')
			->where('id_branch', $result['id_branch'])
			->select('id_branch as id', 'description as text')
			->first();
		$result['approval_request'] = DB::table('hr_employee')
			->where('id_employee', $result['id_approval_request'])
			->select('id_employee as id', 'name as text')
			->first();
		return $result;
	}
}
