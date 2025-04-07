<?php

namespace App\Models\CareerAdministration\Termination;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Termination extends Model {

    protected $table = 'hr_termination_request';
    protected $primaryKey = 'id_termination';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_termination', 'id_employee', 'reference_number', 'id_transition_category', 'id_transaction_type', 
		'id_employment_status', 'id_position_detail', 'id_position_routing', 'id_job_grade', 'id_job_status', 
		'id_location', 'effective_resign_date', 'remark', 'attachment_type', 'attachment', 'enable_approval', 
		'id_approval', 'id_approval_status',  'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getkode(){
			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', session('id_company'))->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-TRM-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%TRM-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%TRM-{$monthyear}%")->max('reference_number'), 15, 21);
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
	
	public static function getdata() {
        $sql = "SELECT htr.id_termination, htr.reference_number, he.nik_employee, he.name, mgd.description as transition_category, mgd2.description as transaction_type, 
				mgd3.description as employment_status, mpd.description as position_detail, mpr.description as position_routing, 
				mjg.description as job_grade, mjs.description as job_status, ml.description as location,
				htr.effective_resign_date, htr.attachment, mgd4.code as code_app_status, mgd4.description as desc_app_status
				FROM hr_termination_request htr
				JOIN hr_employee he
				ON htr.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON htr.id_transition_category = mgd.id_general_data
				JOIN master_general_data mgd2
				ON htr.id_transaction_type = mgd2.id_general_data
				JOIN master_general_data mgd3
				ON htr.id_employment_status = mgd3.id_general_data
				LEFT JOIN master_general_data mgd4
				ON htr.id_approval_status = mgd4.id_general_data
				LEFT JOIN master_position_detail mpd
				ON htr.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_status mjs
				ON mpr.id_job_status = mjs.id_job_status
				WHERE htr.id_company = ? and mgd.id_general_type = 5 
				and mgd2.id_general_type = 6 and mgd3.id_general_type = 2
				and mgd4.code != 'Approved' and he.id_user = ?
				ORDER BY htr.id_termination DESC";
        $result = DB::select($sql,[session('id_company'), session('id_user')]);
	//	dd($result);
        return $result;
    }
	
	public static function get_career_category() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
                FROM  master_general_data where status = 'A' and id_general_type = 5 and code = 'Termination' and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	public static function get_career_type($data) {
		$id_company = session('id_company');
        $id = $data['id'];
        $sql = "SELECT 
                        id_general_data id,
                        description text                  
                FROM   sp_funct_career_type(?,?)";
        $result = DB::select($sql, [$id, $id_company]);
        return $result;
    }
	public static function get_employment_status() {
        $sql = "SELECT 
                        id_general_data id,
                        description text
                FROM  master_general_data where status = 'A' and id_general_type = 2 and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }

	public static function get_employee() {
        $sql = "SELECT DISTINCT
                        he.id_employee id,
						CONCAT(he.name,' (',he.nik_employee,')') as text,
                        he.id_employee,
						he.id_employment_status
                FROM  hr_employee he
				JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				where he.status = 'A' and he.id_company = " . session('id_company')."
				ORDER BY id_employee ASC";
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_position($data) {
		$id_employee = $data['id_employee'];
        $id_company = session()->get('id_company');      
        $sql = "SELECT 
					id_position_detail id,
					description text,
					id_employee
				FROM  master_position_detail where id_employee = ? AND id_company = ?";
        $result = DB::select($sql, [$id_employee, $id_company]);
	//	dd($result);
        return $result;
    }
	
	public static function get_position_detail($data) {
	//	$id_employee = $data['id_employee'];
        $id_company = session()->get('id_company');   
        $id_position_detail = $data['id_position_detail'];   
        $sql = "SELECT sfepv.*, he.id_employment_status, mgd.description as employment_status, mc.company_name as company
                FROM  sp_funct_employee_position_view(null,?) sfepv
				JOIN hr_employee he
				ON sfepv.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data
				JOIN master_company mc
				ON sfepv.id_company = mc.id_company
				WHERE sfepv.id_position_detail = ?";
        $result = DB::select($sql, [$id_company, $id_position_detail]);
	//	dd($result);
        return $result;
    }
	
	public static function get_hierachy($data) {
		$code = $data['code'];
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval_doc_type
                FROM  hr_approval_header hah
				join master_general_data mgd
				on hah.id_approval_doc_type = mgd.id_general_data
				where mgd.code='".$code."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
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
/*	
	public static function get_approval($data) {
		$id_approval = $data;
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						mgd.code,
						(select id_general_data from master_general_data where code = 'New' and id_company =".session('id_company').") as id_approval_status,
						ad.id_approval_detail,
						ad.sequence,
						ad.id_employee
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				JOIN  hr_approval_detail ad ON ad.id_approval = hah.id_approval
				WHERE hah.id_approval ='".$id_approval."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
*/
	public static function get_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company !=" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function transition_career() {
        $sql = "SELECT htr.*, mgd.code
				  FROM hr_termination_request htr
				  JOIN master_general_data mgd
				  ON htr.id_approval_status = mgd.id_general_data
				  WHERE htr.status = 'A' and htr.id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_career_edit($data) {
        $result = [];
        $sql = "SELECT htr.*, mjp.id_dept, mpr.id_routing, ml.id_location, mjg.id_job_grade,mjs.id_job_status, mgd.code as code_status
                FROM hr_termination_request htr
				LEFT JOIN  master_general_data mgd
                ON  htr.id_approval_status = mgd.id_general_data
				LEFT JOIN master_position_detail mpd
				ON htr.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_status mjs
				ON mpr.id_job_status = mjs.id_job_status
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				WHERE htr.id_termination = ?";
        $result = (Array) DB::select($sql, [$data['id_termination']])[0];
       // 	dd($result);
        return $result;
    }
	
	public static function approve() {
        $sql = "SELECT htr.id_termination, mgd.code
				  FROM hr_termination_request htr
				  JOIN master_general_data mgd
				  ON htr.id_approval_status = mgd.id_general_data
				  WHERE htr.status = 'A' and htr.id_company = ". session('id_company');
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
		$id_termination = $data['id_termination'];
		$id_approval = $data['id_approval'];
        $sql = "select  *
				from    hr_approval_transaction hat
				join    hr_approval_detail had
				on      hat.id_approval_detail = had.id_approval_detail
				and     hat.id_company = had.id_company
				join    hr_employee he
				on      he.id_employee = had.id_employee
				and     hat.id_company = he.id_company
				join    master_general_data mgd
				on      hat.id_approval_status = mgd.id_general_data
				where   hat.id_company = ?
				and     hat.id_source_transaction = ?
				and     hat.id_approval = coalesce(?,hat.id_approval)";
        $result = DB::select($sql,[session('id_company'),$id_termination,$id_approval]);

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
