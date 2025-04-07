<?php

namespace App\Models\TalentManagement\TalentRequest;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TalentRequest extends Model
{
	use HasFactory;
	
    protected $table = 'hr_talent_assessment_request';
	protected $primaryKey = 'id_talent_assessment_request';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_talent_assessment_request', 'reference_number', 'id_employee_request', 'id_approval', 'id_approval_request', 'id_approval_status', 'cc_email_to', 'notes', 'note_revised', 'note_rejected', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getkode($idCompany=null){
		$idCompany = $idCompany ?? session('id_company');
		
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', $idCompany)->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-ARF-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%ARF-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%ARF-{$monthyear}%")->max('reference_number'), 15, 21);
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
			$branch = " AND mpd.id_branch in(".$group_branch.")";
		}
		
		$sql = "SELECT htar.id_talent_assessment_request, htar.reference_number, htar.id_employee_request, 
				he.name AS emp_name, mpr.description AS position_route, mpd.id_branch, htar.notes, 
				htar.note_revised, htar.note_rejected, htar.id_approval, htar.id_approval_request, 
				htar.id_approval_status, mgd.description as desc_app_status, mgd.code AS code_app_status
				FROM hr_talent_assessment_request htar
				LEFT JOIN hr_employee he
				ON htar.id_employee_request = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing 
				LEFT JOIN master_general_data mgd
				ON htar.id_approval_status = mgd.id_general_data
				WHERE htar.id_company = ? ".$branch."
				ORDER BY htar.id_talent_assessment_request DESC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_dept_code() {
        $sql = "SELECT md.department_code
				FROM public.master_position_detail mpd
				LEFT JOIN public.hr_employee he
				ON mpd.id_employee = he.id_employee
				LEFT JOIN public.master_position_routing mpr 
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN public.master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN public.master_department md
				ON mjp.id_dept = md.id_dept
				WHERE mpd.id_company = ? AND mpd.secondary_position = false AND he.id_user  = ? ";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
	//	dd($result);
        return $result;
    }
	
	public static function get_employee_by() {
        $sql = "SELECT he.id_employee id, he.name text, mpr.id_routing, mpd.id_position_detail, mpd.id_location, mpr.description AS route_name, 
				mjg.job_class_group, ml.description AS location_name, mb.description AS branch, mjg.description AS grade
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				WHERE he.id_company = ? AND he.id_user = ? AND he.status = 'A' AND mpd.secondary_position = false";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
        return $result;
    }
	
	public static function get_hr_email() {
        $sql = "SELECT he.private_mail AS id, CONCAT(he.name,' (',he.private_mail,')') AS text
				FROM hr_config_email_recruitment hcer
				LEFT JOIN master_position_detail mpd
				ON hcer.id_position_detail = mpd.id_position_detail
				LEFT JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				WHERE hcer.id_position_detail IS NOT NULL AND hcer.id_company = " . session('id_company')."
				ORDER BY he.name ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_approval() {
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						hah.hierarchy_type,
						mgd.code,
						(select id_general_data from master_general_data where code = 'New' AND id_company = ".session('id_company').") as id_approval_status
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				WHERE hah.status = 'A' AND hah.id_company = ".session('id_company')." 
				AND mgd.code = 'Assessment_Request' AND hah.hierarchy_type = 'Organization'";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	/*
	public static function get_app_combine($data) {
		$id_employee = $data['id_employee'];
		$id_company = $data['id_company'];
		$id_approval = $data['id_approval'];
        $sql = "SELECT he.id_employee id, he.name text, sfac.*, sfac.id_employee AS id_employee_approval FROM (
				 SELECT	hah.id_approval,had.id_approval_mode, had.sequence::integer, 
						had.id_position_detail,mpd.description, mpd.id_employee
						FROM	hr_approval_header hah
						JOIN	hr_approval_detail had
						ON		hah.id_approval = had.id_approval
						LEFT JOIN	master_position_detail mpd
						ON		had.id_position_detail = mpd.id_position_detail
						WHERE	hah.id_approval = ".$id_approval."
						AND		hah.id_company = ".$id_company."                       
					UNION ALL                   
						SELECT org.* FROM (
							SELECT	".$id_approval.",
									sfaoh.id_approval_mode,
									sfaoh.sequence,
									sfaoh.id_detail_chief ,
									sfaoh.description_chief ,
									sfaoh.id_employee_approval
							FROM	sp_funct_approval_organization_hierarchy_view(".$id_employee.",".$id_company.",null) sfaoh
							LIMIT 1
						) AS org
				   ) AS sfac
				LEFT JOIN hr_employee he
				ON sfac.id_employee = he.id_employee AND he.status = 'A'   
				WHERE sfac.id_employee != ".$id_employee."
				ORDER BY sequence ASC";
        $result = DB::select($sql);
        return $result;
    }
	*/
	
	public static function get_app_combine($data) {
		$id_employee = $data['id_employee'];
		$id_company = $data['id_company'];
        $sql = "SELECT he.id_employee id, he.name text, sfac.*, sfac.id_detail_chief AS id_position_detail FROM (       
						SELECT org.* FROM (
							SELECT NULL AS id_approval,
									sfaoh.id_approval_mode,
									sfaoh.sequence,
									sfaoh.id_detail_chief ,
									sfaoh.description_chief ,
									sfaoh.id_employee_approval
							FROM	sp_funct_approval_organization_hierarchy_view(".$id_employee.",".$id_company.",null,null) sfaoh
							LIMIT 1
						) AS org             
				   ) AS sfac  
				LEFT JOIN hr_employee he
				ON sfac.id_employee_approval = he.id_employee AND he.status = 'A'
				WHERE sfac.id_employee_approval != ".$id_employee."
				ORDER BY sequence ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_approval_status() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
						FROM  master_general_data where status = 'A' and id_general_type = 7 and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function submit_approve() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Request_Approval' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function getdata_approval_status($data) {
		$id_talent_assessment_request = $data['id_talent_assessment_request'];
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
        $result = DB::select($sql,[session('id_company'),$id_talent_assessment_request,$id_approval]);

        return $result;
    }
	
	public static function get_talent_edit($data) {
		$result = [];
        $sql = "SELECT  he.name AS emp_request, mpr.description AS pos_route, htar.*, mgd.description as desc_app_status, 
				mgd.code as code_app_status, he2.name AS name_emp_approval, mb.description AS branch, mjg.description AS grade
				FROM hr_talent_assessment_request htar
				LEFT JOIN hr_employee he
				ON htar.id_employee_request = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON htar.id_employee_request = mpd.id_employee AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_branch mb
				ON mpd.id_branch = mb.id_branch
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_general_data mgd
				ON htar.id_approval_status = mgd.id_general_data
				LEFT JOIN hr_employee he2
				ON htar.id_approval_request = he2.id_employee
				WHERE htar.id_talent_assessment_request =  ?";
        $result = (Array) DB::select($sql, [$data['id_talent']])[0];
   
        return $result;
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Cancel' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
		
}
