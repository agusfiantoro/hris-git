<?php

namespace App\Models\CareerAdministration\CareerTransition;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Employee\Employee\Document;

class CareerTransition extends Model {

    protected $table = 'hr_career_transaction';
    protected $primaryKey = 'id_career_transaction';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_career_transaction', 'id_employee', 'id_employee2', 'transaction_number', 'reference_number', 'id_transition_category', 'id_transaction_type', 'id_old_employment_status', 'id_employment_status', 'id_old_position_detail', 'id_position_detail','id_position_routing', 'id_job_grade', 'id_job_status', 'id_location', 'id_contract_employee', 'effective_date', 'expired_date', 'remark', 'attachment_type', 'attachment', 'enable_approval', 'id_approval', 'id_approval_status', 'id_company_destination', 'status', 'id_company', 'executed', 'id_terminate_reason', 'resign_category', 'id_new_leave', 'id_new_employement_status', 'id_new_shift_group', 'id_new_timezone', 'id_new_religion', 'request_resign_date', 'created_by', 'updated_by', 'id_recommendation_header'
    ];
	
	public static function getkode(){
			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', session('id_company'))->first();
		$char_com = strlen($com->company_code);
		$nomor = $com->company_code.'-CTR-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%CTR-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%CTR-{$monthyear}%")->max('reference_number'), 15, 21);
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
	
	public static function getkodeEntity($idCompany){
			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', $idCompany)->first();
		$char_com = strlen($com->company_code);
		$nomor = $com->company_code.'-CTR-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%CTR-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%CTR-{$monthyear}%")->max('reference_number'), 15, 21);
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
        $sql = "SELECT hct.id_career_transaction, hct.reference_number, hct.id_employee, he.nik_employee, he.name, mgd.description as transition_category, mgd2.description as transaction_type, 
				mgd3.description as employment_status, mc.company_name as company_destination, mpd.description as position_detail, mpr.description as position_routing, 
				mjg.description as job_grade, mjs.description as job_status, ml.description as location,
				hct.effective_date, hct.expired_date, hct.attachment, mgd4.code as code_app_status, mgd4.description as desc_app_status, hct.note_rejected, hct.note_revised, hct.id_recommendation_header
				FROM hr_career_transaction hct
				JOIN hr_employee he
				ON hct.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON hct.id_transition_category = mgd.id_general_data
				JOIN master_general_data mgd2
				ON hct.id_transaction_type = mgd2.id_general_data
				JOIN master_general_data mgd3
				ON hct.id_employment_status = mgd3.id_general_data
				LEFT JOIN master_general_data mgd4
				ON hct.id_approval_status = mgd4.id_general_data
				LEFT JOIN master_position_detail mpd
				ON hct.id_old_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_status mjs
				ON mpr.id_job_status = mjs.id_job_status
				LEFT JOIN master_company mc
				ON hct.id_company_destination = mc.id_company
				WHERE hct.id_company = ? and mgd.id_general_type = 5 
				and mgd2.id_general_type = 6 and mgd3.id_general_type = 2
				and mgd4.code != 'Approved'
				ORDER BY hct.id_career_transaction DESC";
		}
		else{
			$sql = "SELECT hct.id_career_transaction, hct.reference_number, hct.id_employee, he.nik_employee, he.name, mgd.description as transition_category, mgd2.description as transaction_type, 
				mgd3.description as employment_status, mc.company_name as company_destination, mpd.description as position_detail, mpr.description as position_routing, 
				mjg.description as job_grade, mjs.description as job_status, ml.description as location,
				hct.effective_date, hct.expired_date, hct.attachment, mgd4.code as code_app_status, mgd4.description as desc_app_status, hct.note_rejected, hct.note_revised, hct.id_recommendation_header
				FROM hr_career_transaction hct
				JOIN hr_employee he
				ON hct.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON hct.id_transition_category = mgd.id_general_data
				JOIN master_general_data mgd2
				ON hct.id_transaction_type = mgd2.id_general_data
				JOIN master_general_data mgd3
				ON hct.id_employment_status = mgd3.id_general_data
				LEFT JOIN master_general_data mgd4
				ON hct.id_approval_status = mgd4.id_general_data
				LEFT JOIN master_position_detail mpd
				ON hct.id_old_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_status mjs
				ON mpr.id_job_status = mjs.id_job_status
				LEFT JOIN master_company mc
				ON hct.id_company_destination = mc.id_company
				WHERE hct.id_company = ? and mgd.id_general_type = 5 
				and mgd2.id_general_type = 6 and mgd3.id_general_type = 2
				and mgd4.code != 'Approved' and mpd.id_branch in(".$group_branch.")
				ORDER BY hct.id_career_transaction DESC";
		}
        $result = DB::select($sql,[session('id_company')]);
	//	dd($result);
        return $result;
    }
	
	public static function get_cancel_join($id_employee) {
        $sql = "SELECT hct.id_career_transaction, hct.id_employee, hct.id_transition_category, mgd.description AS category, mgd2.description as type
					FROM hr_career_transaction hct
					LEFT JOIN master_general_data mgd
					ON hct.id_transition_category = mgd.id_general_data
					LEFT JOIN master_general_data mgd2 
					ON hct.id_transaction_type = mgd2.id_general_data
				WHERE hct.id_employee = ? AND hct.id_company = ? ";
        $result = DB::select($sql,[$id_employee,session('id_company')]);

        return $result;
    }
	public static function get_career_category() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
                FROM  master_general_data 
				WHERE  code not in('Rehire','New_Assignment') and status = 'A' and id_general_type = 5 and id_company =" . session('id_company') ."
				ORDER BY sequence ASC";
        $result = DB::select($sql);

        return $result;
    }
	public static function get_career_new($data) {
		$id_company = session('id_company');
        $id = $data['id'];
        $var_type = $data['var_type'];
		if($var_type == "Join"){
        $sql = "SELECT  
                        sc.id_general_data id,
                        sc.description text                 
                FROM   sp_funct_career_type(?,?) sc
                WHERE sc.sequence = 1";
		}
		else{
			$sql = "SELECT 
                        id_general_data id,
                        description text                  
                FROM   sp_funct_career_type(?,?)";
		}
        $result = DB::select($sql, [$id, $id_company]);
        return $result;
    }
	
	public static function get_career_type($data) {
		$id_company = session('id_company');
        $id = $data['id'];
        $var_type = $data['var_type'];
		if($var_type == "Join"){
        $sql = "SELECT 
                        id_general_data id,
                        description text                  
                FROM   sp_funct_career_type(?,?)";
		}
		else{
			$sql = "SELECT 
                        id_general_data id,
                        description text                  
                FROM   sp_funct_career_type(?,?)";
		}
        $result = DB::select($sql, [$id, $id_company]);
        return $result;
    }
	public static function get_employment_status($id) {
        $sql = "SELECT 
                        id_general_data id,
                        description text
                FROM  master_general_data where code != 'Concurent' AND status = 'A' AND id_general_type = 2 AND id_company =" . $id;
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_shift($id) {
         $sql = "SELECT id_shiftgroup id,
						description text
				  FROM master_shiftgroup_header
				  WHERE status = 'A' and id_company =". $id;
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_timezone($id) {
         $sql = "SELECT id_general_data id,
						description text
				  FROM master_general_data 
				  WHERE id_general_type = 3 and status = 'A' and id_company =" . $id;
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_new_dept($data) {
		$id = $data['id_company'];
        $sql = "SELECT 
                        id_dept id,
                        description text
                FROM  master_department where status = 'A' and id_company =" .$id;			
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	public static function get_new_route($data) {
		$id_company = $data['id_company'];
        $sql = "SELECT 
					sfjpr.id_routing id,
					sfjpr.description text
                FROM  sp_funct_job_position_route_list_view() sfjpr
				where sfjpr.id_company = ?";
			$result = DB::select($sql, [$id_company]);
		//	dd($result);
        return $result;
    }
	
	public static function get_new_positon($data) {
		$id_company = $data['id_company'];
        $sql = "SELECT *
                FROM  sp_funct_career_position_view(?,null)";
        $result = DB::select($sql, [$id_company]);
	//	dd($result);
        return $result;
    }
	
	public static function get_new_positon_detail() {
        $sql = "SELECT sfc.*, he.id_employee
                FROM  sp_funct_career_position_view(null,null) sfc
				LEFT JOIN master_position_detail mpd
				ON sfc.id = mpd.id_position_detail
				LEFT JOIN hr_employee he
				ON mpd.id_employee = he.id_employee";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_code_company() {
		$sql = "SELECT 
                        id_company,
                        company_code
                FROM  master_company where id_company = ".session('id_company');
        $result = DB::select($sql)[0];
        return $result;
	}	
	public static function get_employee($data,$group_branch) {
	    $company_code = Self::get_code_company()->company_code;
		$rehire = $data['rehire'];
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND mpd.id_branch in(".$group_branch.")";
		}
		if($rehire == 'Rehire_Employee'){
			$sql = "SELECT  he2.id_employee id,
				CONCAT(he2.name,' (',he2.nik_employee,'), Join Date (',he2.join_date,')') as text,
				he2.id_employee,
				he2.name,
				hct.id_old_position_detail,
				he2.id_employment_status,
				he2.expired_date,
				he2.id_company
				FROM hr_employee he2
				JOIN (					
					SELECT hct3.id_employee, hct3.id_transition_category, hct3.id_approval_status, hct3.id_old_position_detail  FROM hr_career_transaction hct3
						JOIN(						
						SELECT id_employee, max(effective_date) AS max_effective_date 
							FROM hr_career_transaction
							GROUP BY id_employee								
						) AS j_hct
					ON hct3.id_employee = j_hct.id_employee AND hct3.effective_date = j_hct.max_effective_date					
				) AS hct
				ON he2.id_employee = hct.id_employee AND he2.nik_employee LIKE '".$company_code."%'
				JOIN master_company mc
				ON he2.id_company = mc.id_company
				WHERE (he2.id_employee in(
					select max(id_employee) as id_employee
									FROM  hr_employee he
							where he.id_company = ". session('id_company')." AND he.nik_employee LIKE '".$company_code."%'
							GROUP BY name, nik_employee 								
			) AND he2.status = 'I' AND hct.id_old_position_detail IS NOT NULL)
		UNION
				SELECT  he2.id_employee id,
				CONCAT(he2.name,' (',he2.nik_employee,'), Join Date (',he2.join_date,')') as text,
				he2.id_employee,
				he2.name,
				hct.id_old_position_detail,
				he2.id_employment_status,
				he2.expired_date,
				he2.id_company
				FROM hr_employee he2
				JOIN (					
					SELECT hct4.id_employee, hct4.id_transition_category, hct4.id_approval_status, hct4.id_old_position_detail 
					FROM hr_career_transaction hct4
					JOIN(				
						SELECT max(hct3.id_career_transaction) AS id_career_transaction, hct3.id_employee
						FROM hr_career_transaction hct3
							JOIN(						
								SELECT max(j_id.id_employee) AS id_employee, j_id.nik_employee FROM (							
									SELECT hct.id_employee, max(hct.effective_date) AS max_effective_date, he.nik_employee  
										FROM hr_career_transaction hct
										LEFT JOIN hr_employee he
										ON hct.id_employee = he.id_employee
										GROUP BY hct.id_employee, he.nik_employee									
									) j_id
								GROUP BY j_id.nik_employee								
							) AS j_hct
						ON hct3.id_employee = j_hct.id_employee	
						LEFT JOIN master_general_data mgd
						ON hct3.id_approval_status = mgd.id_general_data
						WHERE mgd.code = 'Approved'
						GROUP BY hct3.id_employee						
					) AS max_career
					ON hct4.id_career_transaction = max_career.id_career_transaction			
				) AS hct
				ON he2.id_employee = hct.id_employee AND he2.nik_employee LIKE '".$company_code."%'
				JOIN master_company mc
				ON he2.id_company = mc.id_company
				WHERE (hct.id_transition_category  =(				
					SELECT id_general_data FROM master_general_data
					WHERE code = 'Termination' AND description = 'Termination' AND id_company = ". session('id_company')."				 
				) AND hct.id_approval_status = (
					SELECT id_general_data FROM master_general_data
					WHERE code = 'Approved' AND id_company = ". session('id_company')."
				))  ORDER BY text ASC";
		}
		else{
			$sql = "select  he2.id_employee id,
				CONCAT(he2.name,' (',he2.nik_employee,')') as text,
				he2.id_employee,
				he2.name,
				he2.id_employment_status,
				he2.expired_date,
				mpd.id_position_detail
				from hr_employee he2
				LEFT JOIN master_position_detail mpd
				ON he2.id_employee = mpd.id_employee
				where he2.id_employee in(
					select max(id_employee) as id_employee
									FROM  hr_employee he
							where he.status = 'A' and he.id_company =". session('id_company')."
							GROUP BY name, nik_employee 
			) AND mpd.secondary_position = false ".$branch."
			ORDER BY he2.name ASC";			
		}		
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
				FROM  master_position_detail where id_employee = ? AND (id_company = ? OR assigned_to_company = ?) AND secondary_position = false";
        $result = DB::select($sql, [$id_employee, $id_company, $id_company]);
	//	dd($result);
        return $result;
    }
	
	public static function get_position_detail($data) {
	//	$id_employee = $data['id_employee'];
        $id_company = session()->get('id_company');   
        $id_position_detail = $data['id_position_detail'];   
        $sql = "SELECT sfepv.*, he.id_employment_status, 
				mgd.description as employment_status, mc.company_name as company, mp.principal_code as principal
                FROM  sp_funct_employee_position_view(null,?) sfepv
				JOIN hr_employee he
				ON sfepv.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data
				JOIN master_company mc
				ON ((sfepv.id_company = mc.id_company AND sfepv.assigned_to_company IS NULL) 
				OR sfepv.assigned_to_company = mc.id_company)
				LEFT JOIN relation_positiondetail_principal rpp 
				ON sfepv.id_position_detail = rpp.id_position_detail
				AND sfepv.id_company = rpp.id_company
				LEFT JOIN master_principal mp
				ON rpp.id_principal = mp.id_principal
				AND rpp.id_company = mp.id_company
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
	public static function get_company_session($data) {
		$id = $data['id'];
	/*	if($id == 'Rehire_Employee' ){
			$sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company";	
		}
	*/				
		$sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company =" . $id;
        $result = DB::select($sql);
        return $result;
    }
	public static function get_company($data) {
	//	dd(session('company_type'));
	$type = $data['type'];
	if(session('company_type') == 'corporate'){	
		if($type == 'Movement'){
			$com = "";
		}
		else{
			$com = " AND id_company != ".session('id_company');
		}
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where company_type = 'corporate' ".$com;
	}
	else{
		 $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company = ".session('id_company');
	}
        $result = DB::select($sql);
        return $result;
    }
	
	public static function transition_career() {
        $sql = "SELECT hct.*, mgd.code, mgd2.id_general_data, mgd2.code as category, mgd3.description as type
				  FROM hr_career_transaction hct
				  JOIN master_general_data mgd
				  ON hct.id_approval_status = mgd.id_general_data
				  JOIN master_general_data mgd2
				  ON hct.id_transition_category = mgd2.id_general_data
				  JOIN master_general_data mgd3
				  ON hct.id_transaction_type = mgd3.id_general_data
				  JOIN master_general_data mgd4
				  ON hct.id_transaction_type = mgd4.id_general_data
				  WHERE hct.status = 'A' AND hct.executed = false AND mgd.code = 'Approved' 
				  AND mgd4.description != 'Temporary Assignment'
				  ORDER BY hct.id_career_transaction ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_career_edit($data) {
        $result = [];
        $sql = "SELECT hct.*, md.description AS department, md.id_dept, mpd.description AS position_detail, 
				mpr.description AS position_routing, 
				mjp.description AS job_position,
				ml.description AS location,
				mjg.description AS job_grade,
				mjs.description AS job_status,
				mgd.code AS code_status, mgd2.code AS code,
				mgd3.description AS transaction_type_code, mgd4.code AS category_code,
				CONCAT(hrh.reference_number,' (',mgd5.description,')') AS ref_number_reco
                FROM hr_career_transaction hct
				LEFT JOIN  master_general_data mgd
                ON  hct.id_approval_status = mgd.id_general_data
				JOIN hr_approval_header hah
				ON hct.id_approval = hah.id_approval
				JOIN master_general_data mgd2
				ON hah.id_approval_doc_type = mgd2.id_general_data
				LEFT JOIN master_position_detail mpd
				ON hct.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				ON hct.id_position_routing = mpr.id_routing				
				LEFT JOIN master_location ml
				ON hct.id_location = ml.id_location
				LEFT JOIN master_job_grade mjg
				ON hct.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_status mjs
				ON hct.id_job_status = mjs.id_job_status
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN  master_department md
             	ON  mjp.id_dept = md.id_dept
				LEFT JOIN master_general_data mgd3
             	ON hct.id_transaction_type = mgd3.id_general_data
             	LEFT JOIN master_general_data mgd4
             	ON hct.id_transition_category = mgd4.id_general_data
				LEFT JOIN hr_recommendation_header hrh
             	ON hct.id_recommendation_header = hrh.id_recommendation_header
             	LEFT JOIN master_general_data mgd5
             	ON hrh.id_transition_type = mgd5.id_general_data
				WHERE hct.id_career_transaction = ?";
        $result = (Array) DB::select($sql, [$data['id_career_transaction']])[0];
        //	dd($result);
        return $result;
    }
	
	public static function approve() {
        $sql = "SELECT hct.id_career_transaction, mgd.code
				  FROM hr_career_transaction hct
				  JOIN master_general_data mgd
				  ON hct.id_approval_status = mgd.id_general_data
				  WHERE hct.status = 'A' and hct.id_company = ". session('id_company');
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
	public static function approved() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Approved' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function submit_approve_api($idCompany) {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Request_Approval' and mgd.status = 'A' and mgd.id_company =". $idCompany;
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function approved_api($idCompany) {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Approved' and mgd.status = 'A' and mgd.id_company =". $idCompany;
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function getdata_approval_status($data) {
		$id_career_transaction = $data['id_career_transaction'];
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
        $result = DB::select($sql,[session('id_company'),$id_career_transaction,$id_approval]);

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
	
	public static function browse_job($data) {
		$cancel = self::cancel()->id_general_data;
		$reject = self::reject()->id_general_data;
		$id_company = $data['company'];
         $sql = "SELECT j_emp.trans_type, j_emp.effective_date, sfcjl.* from sp_funct_cross_job_list(?,?,?) sfcjl
					LEFT JOIN (
							SELECT hct.id_career_transaction, hct.id_employee,  hct.id_transaction_type, mgd2.description AS trans_type, hct.effective_date 
								FROM hr_career_transaction hct
								JOIN (
									SELECT hct_emp.id_employee, max(hct_emp.id_career_transaction) AS max_id_career_transaction
									FROM hr_career_transaction hct_emp
									JOIN master_general_data mgd
									ON hct_emp.id_approval_status = mgd.id_general_data
									WHERE mgd.code = 'Approved' AND hct_emp.id_company = ". session('id_company')."
									GROUP BY id_employee
								) AS j_hct
							ON hct.id_career_transaction = j_hct.max_id_career_transaction
							JOIN master_general_data mgd2
							ON hct.id_transaction_type = mgd2.id_general_data
						) AS j_emp
					ON sfcjl.id_employee = j_emp.id_employee";
        $result = DB::select($sql,[$id_company, $cancel, $reject]);
	//	dd($result);
        return $result;
    }
	public static function browse_reco($data) {
		$nik_employee = $data['nik_employee'];
		$cat = $data['cat'];
         $sql = "SELECT hrh.id_recommendation_header, hrh.reference_number,
					CONCAT(he.name,' (',he.nik_employee,')') AS name, mpr.description AS old_position,
					mpr2.description AS new_position, CONCAT(mgd.description,' (',mgd2.description,')') AS cat, hrh.effective_date
					FROM hr_recommendation_header hrh
					LEFT JOIN hr_employee he
					ON hrh.id_employee = he.id_employee
					LEFT JOIN master_position_detail mpd
					ON hrh.id_position_detail = mpd.id_position_detail
					JOIN master_position_routing mpr
					ON mpd.id_position_routing = mpr.id_routing
					LEFT JOIN master_position_detail mpd2
					ON hrh.id_new_position_detail = mpd2.id_position_detail
					LEFT JOIN master_position_routing mpr2
					ON mpd2.id_position_routing = mpr2.id_routing
					LEFT JOIN master_general_data mgd
					ON hrh.id_transition_category = mgd.id_general_data
					LEFT JOIN master_general_data mgd2
					ON hrh.id_transition_type = mgd2.id_general_data
					LEFT JOIN master_general_data mgd3
					ON hrh.id_approval_status = mgd3.id_general_data
					WHERE he.nik_employee = '".$nik_employee."' AND mgd.code = '".$cat."'  AND hrh.id_company = ".session('id_company')."
					AND mgd3.code = 'Approved'";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function reco_check($data) {
		$recoid = $data['recoid'];
         $sql = "SELECT hrh.id_recommendation_header, CONCAT(hrh.reference_number,' (',mgd2.description,')') AS reference_number
				FROM hr_recommendation_header hrh
				LEFT JOIN hr_employee he
				ON hrh.id_employee = he.id_employee
				LEFT JOIN master_position_detail mpd
				ON hrh.id_position_detail = mpd.id_position_detail
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_position_detail mpd2
				ON hrh.id_new_position_detail = mpd2.id_position_detail
				LEFT JOIN master_position_routing mpr2
				ON mpd2.id_position_routing = mpr2.id_routing
				LEFT JOIN master_general_data mgd
				ON hrh.id_transition_category = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON hrh.id_transition_type = mgd2.id_general_data
				LEFT JOIN master_general_data mgd3
				ON hrh.id_approval_status = mgd3.id_general_data
				WHERE hrh.id_recommendation_header = ".$recoid." AND hrh.id_company = ".session('id_company')." 
				AND mgd3.code = 'Approved'";
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function browse_check($data) {
		$id_position_detail = $data['jobid'];
         $sql = "SELECT  he2.name, he2.id_employee, md.description AS department, md.id_dept AS id_dept,
                   mpd.id_position_detail, mpd.description AS position_detail,
                   mpd2.description AS parent_position_detail,
				   he.name AS name_supervisor,                   
                   mpr.description AS position_routing, 
				   mpr.id_routing AS id_position_routing,
                   mjp.description AS job_position,
				   mjg.description AS job_grade,
				   mjg.id_job_grade AS id_job_grade,
				   mjs.description AS job_status,
				   mjs.id_job_status AS id_job_status,
				   ml.id_location,
                   mb.description AS branch,
				   ml.description AS location,
                   mr.description AS regional,
				   STRING_AGG(mp.description,', ') AS principal 
            FROM  master_position_detail mpd
            JOIN  master_position_routing mpr
              ON  mpd.id_position_routing = mpr.id_routing
             AND  mpd.id_company = mpr.id_company
             AND  mpr.status = 'A'
			LEFT JOIN master_job_grade mjg
			  ON  mpr.id_job_grade = mjg.id_job_grade
			 AND  mpr.id_company = mjg.id_company
			 AND  mjg.status = 'A'
			LEFT JOIN master_job_status mjs
			  ON  mpr.id_job_status = mjs.id_job_status
			  AND mpr.id_company = mjs.id_company 
			  AND mjs.status = 'A'
           LEFT JOIN  master_position_detail mpd2
              ON  mpd.parent_id_position_detail = mpd2.id_position_detail
             AND  mpd.id_company = mpd2.id_company
             AND  mpd2.status = 'A'
            LEFT JOIN  hr_employee he
              ON  mpd2.id_employee = he.id_employee
             AND  mpd2.id_company = he.id_company
             AND  he.status = 'A'
			LEFT JOIN  hr_employee he2
              ON  mpd.id_employee = he2.id_employee
            JOIN  master_job_position mjp
              ON  mpr.id_position = mjp.id_position
             AND  mpd.id_company = mjp.id_company
             AND  mjp.status = 'A'
            JOIN  master_location ml
              ON  mpd.id_location = ml.id_location
             AND  mpd.id_company = ml.id_company
             AND  ml.status = 'A'
            JOIN  master_branch mb
              ON  mpd.id_branch = mb.id_branch
             AND  mpd.id_company = mb.id_company
             AND  mb.status = 'A'
            JOIN  master_department md
              ON  mjp.id_dept = md.id_dept
             AND  mpd.id_company = md.id_company
             AND  md.status = 'A'
            JOIN  master_region mr
              ON  mb.id_region = mr.id_region
             AND  mb.id_company = mr.id_company
             AND  mr.status = 'A'
	  LEFT JOIN  relation_positiondetail_principal rpp
	  		  ON  mpd.id_position_detail = rpp.id_position_detail
			 AND  mpd.id_company = rpp.id_company
	   LEFT JOIN  master_principal mp
			  ON  rpp.id_principal = mp.id_principal
			 AND  rpp.id_company = mp.id_company
            WHERE mpd.id_position_detail= COALESCE(".$id_position_detail.",mpd.id_position_detail)
			GROUP BY he2.name, he2.id_employee, md.description, md.id_dept, mpd.id_position_detail, mpd.description, mpd2.description, he.name,
                 mpr.description, mpr.id_routing, mjp.description, mjg.description, mjg.id_job_grade, 
				 mjs.description, mjs.id_job_status, ml.id_location, mb.description,
				 ml.description, mr.description";
        $result = DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
	
	public static function get_effective_date($id_position){
		$sql = "SELECT hct.id_career_transaction, hct.id_employee, hct.id_old_position_detail, hct.id_position_detail, hct.effective_date, mgd.code, mgd3.description AS concurent
					FROM hr_career_transaction hct
					LEFT JOIN master_general_data mgd 
					ON hct.id_transition_category = mgd.id_general_data
					JOIN master_general_data mgd2
					ON hct.id_approval_status = mgd2.id_general_data
					LEFT JOIN master_general_data mgd3 
					ON hct.id_transaction_type = mgd3.id_general_data
					WHERE mgd2.code NOT IN('Cancel','Rejected') AND hct.id_old_position_detail = ".$id_position." AND hct.id_company =" . session('id_company')."
				UNION
				SELECT hct.id_career_transaction, hct.id_employee, hct.id_old_position_detail, hct.id_position_detail, hct.effective_date, mgd.code, mgd3.description AS concurent
					FROM hr_career_transaction hct
					LEFT JOIN master_general_data mgd 
					ON hct.id_transition_category = mgd.id_general_data 
					JOIN master_general_data mgd2
					ON hct.id_approval_status = mgd2.id_general_data
					LEFT JOIN master_general_data mgd3 
					ON hct.id_transaction_type = mgd3.id_general_data
					WHERE mgd2.code NOT IN('Cancel','Rejected') AND  hct.id_position_detail = ".$id_position." AND hct.id_company = " . session('id_company')."
					ORDER BY id_career_transaction DESC 
					LIMIT 1";
        $result = DB::select($sql);
        return $result;
	}
	
	public static function get_not_request($id_employee){
		$sql = "SELECT hct2.id_career_transaction, hct2.id_employee, hct2.effective_date, mgd.code 
				FROM hr_career_transaction hct2
				JOIN(
					SELECT hct.id_employee, max(hct.effective_date) AS effective_date 
					FROM hr_career_transaction hct
					WHERE hct.id_employee = ?
					GROUP BY hct.id_employee 
				) jc
				ON hct2.id_employee = jc.id_employee AND hct2.effective_date = jc.effective_date
				JOIN master_general_data mgd
				ON hct2.id_approval_status = mgd.id_general_data
				WHERE mgd.code NOT IN('Approved','Cancel','Rejected') AND hct2.id_company = ". session('id_company')."
				ORDER BY id_career_transaction DESC
				LIMIT 1";
        $result = DB::select($sql,[$id_employee]);
        return $result;
	}
	
	public static function get_request_termination($id_employee){
		$sql = "SELECT hct2.id_career_transaction, mgd2.code FROM hr_career_transaction hct2 
			JOIN (
			SELECT max(hct.id_career_transaction) AS id_career_transaction, hct.id_employee
								FROM hr_career_transaction hct
								JOIN master_general_data mgd
								ON hct.id_transition_category = mgd.id_general_data
								WHERE hct.id_company = ". session('id_company')." AND mgd.code = 'Termination'
								AND hct.id_employee = ?
								GROUP BY hct.id_employee
			) AS h_j
			ON hct2.id_career_transaction = h_j.id_career_transaction
			JOIN master_general_data mgd2 
			ON hct2.id_approval_status = mgd2.id_general_data
			WHERE mgd2.code NOT IN('Cancel','Rejected')";
        $result = DB::select($sql,[$id_employee]);
        return $result;
	}
	
	public static function get_request($id_employee){
		$sql = "SELECT hct2.id_career_transaction, hct2.id_employee, hct2.effective_date, mgd.code 
				FROM hr_career_transaction hct2
				JOIN(
					SELECT hct.id_employee, max(hct.effective_date) AS effective_date 
					FROM hr_career_transaction hct
					WHERE hct.id_employee = ?
					GROUP BY hct.id_employee 
				) jc
				ON hct2.id_employee = jc.id_employee AND hct2.effective_date = jc.effective_date
				JOIN master_general_data mgd
				ON hct2.id_approval_status = mgd.id_general_data
				WHERE mgd.code IN('Approved') AND hct2.id_company = ". session('id_company')."
				ORDER BY id_career_transaction DESC
				LIMIT 1";
        $result = DB::select($sql,[$id_employee]);
        return $result;
	}
	
	public static function get_terminate_reason($data) {
		$code = $data['code'];
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
                FROM  master_general_data 
				WHERE status = 'A' AND id_general_type = 21 AND code = ? AND id_company =" . session('id_company');
        $result = DB::select($sql,[$code]);
        return $result;
    }
	public static function get_type_reason($resign) {
        $sql = "SELECT 
                        id_general_data,
                        description,
						code
                FROM  master_general_data 
				WHERE status = 'A' AND id_general_data = ?";
        $result = DB::select($sql,[$resign]);
        return $result;
    }
	public static function get_last_position($pos_detail) {
        $sql = "SELECT 
					md.description AS department, mpd.id_position_detail, mpd.description AS position_detail, 
					mpr.description AS position_routing, 
					mjp.description AS job_position,
					ml.description AS location
                FROM master_position_detail mpd
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing				
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN  master_department md
             	ON  mjp.id_dept = md.id_dept
				WHERE mpd.id_position_detail = ?";
        $result = DB::select($sql,[$pos_detail]);
        return $result;
    }
	
	public static function get_transaction_type($data) {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
                FROM  master_general_data 
				WHERE status = 'A' AND id_general_data = ? ";
        $result = DB::select($sql,[$data])[0];
        return $result;
    }
	
	public static function genEntityLeave($data) {
		$id_new_employee = $data['id_new_employee'];
		$id_old_employee = $data['id_old_employee'];
		$id_new_company = $data['id_new_company'];
		$id_old_company = $data['id_old_company'];
		$id_user = $data['created_by'];
        $sql = "SELECT * FROM GenerateCarryOverLeaveBalance(?,?,?,?,?)";
        $result = DB::select($sql,[$id_old_company,$id_new_company,$id_old_employee,$id_new_employee,$id_user]);
        return $result;
    }
	
	public static function genEntityJoin($data) {
		$id_new_employee = $data['id_new_employee'];
		$id_career = $data['id_career_transaction'];
		$id_new_company = $data['id_new_company'];
		$ref_number = $data['reference_number'];
		$id_user = $data['created_by'];
        $sql = "SELECT 	'".$ref_number."' AS reference_number, ".$id_new_employee." AS id_employee,mgdtc_new.id_general_data as id_transition_category,
				  mgdtt_new.id_general_data as id_transaction_type, mgdes_new.id_general_data as id_employment_status,
				  hct.id_position_detail, hct.id_position_routing, hct.id_job_grade, hct.id_job_status, hct.id_location, 
				  hct.effective_date, hct.expired_date, 'Entity Movement'::character varying as remark, hct.enable_approval, 
				  hct.executed, coalesce(hah_new.id_approval,hct.id_approval) AS id_approval, mgdas_new.id_general_data AS id_approval_status, hct.status, 
				  hct.id_company_destination AS id_company, now()::timestamp without time zone as creation_date, coalesce(".$id_user.",1) as created_by
				  FROM hr_career_transaction hct
				  JOIN master_general_data mgdtc_old
					ON hct.id_transition_category = mgdtc_old.id_general_data
				  JOIN master_general_data mgdtc_new
					ON mgdtc_old.id_general_type = mgdtc_new.id_general_type
				   AND mgdtc_old.code = mgdtc_new.code
				   AND mgdtc_old.description = mgdtc_new.description
				   AND mgdtc_old.id_company = hct.id_company
				  JOIN master_general_data mgdtt_old
					ON hct.id_transaction_type = mgdtt_old.id_general_data
				   AND hct.id_company = mgdtt_old.id_company
				  JOIN master_general_data mgdtt_new
					ON mgdtt_new.id_general_type = mgdtt_old.id_general_type
				   AND mgdtt_new.code = 'Entity_Movement'
				   AND mgdtt_new.description = 'Join'
				   AND mgdtt_new.id_company = mgdtc_new.id_company
				  JOIN master_general_data mgdes_old
					ON hct.id_employment_status = mgdes_old.id_general_data
				  JOIN master_general_data mgdes_new
					ON mgdes_old.code = mgdes_new.code
				   AND mgdes_new.id_company = mgdtt_new.id_company
				  JOIN master_general_data mgdas_old
					ON mgdas_old.id_company = hct.id_company
				   AND mgdas_old.code = 'Approved'
				  JOIN master_general_data mgdas_new
					ON mgdas_new.id_company = mgdtc_new.id_company
				   AND mgdas_new.id_general_type = mgdas_old.id_general_type
				   AND mgdas_new.code = mgdas_old.code
				  LEFT JOIN hr_approval_header hah_new
					ON mgdas_new.id_company = hah_new.id_company
				   AND upper(hah_new.description) like '%CAREER%'
				   AND hah_new.status = 'A'
				  WHERE id_career_transaction = ".$id_career."
					AND mgdtc_new.id_company = ".$id_new_company;
        $res = DB::select($sql)[0];
		$result = json_decode(json_encode($res),true); 
        return $result;
    }
	
	public static function get_default_access($data) {
		$code_default = $data['code_default'];
		$id_company = $data['id_company'];
		$sql = "SELECT list.*, mc.id_company  FROM (
				  SELECT  mr.id_responsibility_menu, mm.*
                      FROM  master_menu mm
                      JOIN  master_responsibility mr
                        ON  mr.id_responsibility = mm.id_responsibility
                      WHERE mm.status = 'A'
                        AND mm.default_user = CASE  '".$code_default."'
                                                  WHEN 'Default_User' THEN TRUE
                                              END
                      UNION
                      SELECT  mr.id_responsibility_menu, mm.*
                      FROM  master_menu mm
                      JOIN  master_responsibility mr
                        ON  mr.id_responsibility = mm.id_responsibility
                      WHERE mm.status = 'A'
                        and mm.default_manager = CASE  '".$code_default."'
                                                      WHEN 'Default_Manager' THEN TRUE
                                                  END
                      UNION
                      SELECT  mr.id_responsibility_menu, mm.*
                      FROM  master_menu mm
                      JOIN  master_responsibility mr
                        ON  mr.id_responsibility = mm.id_responsibility
                      WHERE mm.status = 'A'
                        AND mm.default_administrator = CASE  '".$code_default."'
                                                            WHEN 'Default_Administrator' THEN TRUE
                                                        END		
                      ) AS list
                  CROSS JOIN master_company mc
				  WHERE mc.id_company in (?)";
        $result_menu = DB::select($sql,[$id_company]);
		$result['menu'] = [];		
        foreach (array_keys($result_menu) as $key => $value) {
            $result['menu'][] = [		
                'id_menu' => $result_menu[$value]->id_menu,
                'menu_name' => $result_menu[$value]->menu_name,
                'id_company' => $result_menu[$value]->id_company,
            ];
        }
        return $result;
    }

    public static function duplicateEmployeeDocument($nik=[]) {
        $employeeByNik = DB::table('hr_employee')->select('identification_number')->whereIn('nik_employee', $nik)->get();
        if($employeeByNik){
        	$dataEmployee = [];
        	foreach ($employeeByNik as $key => $val) {
        		$ktp = $val->identification_number;
        		$dataEmployee[$val->identification_number]['A'] = null;
        		$dataEmployee[$val->identification_number]['I'] = null;

        		$employeeByKtp = DB::table('hr_employee')
                    ->select('identification_number','status','id_employee','nik_employee')
                    ->where(function ($q) use ($ktp){
		        		$q->where(function ($q) use ($ktp){
			        		$q->where('identification_number', $ktp);
			        		$q->where('status', 'A');
			            });
			            $q->orWhere('id_employee', '=', function($q) use ($ktp){
			            	$q->from('hr_employee')
							    ->selectRaw('max(id_employee) as id_employee')
				        		->where('identification_number', $ktp)
				        		->where('status', 'I');
			            });
            		})->get();
                foreach ($employeeByKtp as $k => $item) {
	        		if($item->status == 'A'){
	        			$dataEmployee[$item->identification_number]['A'] = $item->id_employee;
	        		} else {
        				$dataEmployee[$item->identification_number]['I'] = $item->id_employee;
	        		}
                }
        	}
        	foreach ($dataEmployee as $idNumber => $item) {
    			$allId = [$item['I'], $item['A']];
        		if(!is_null($item['I'])){
        			$getDocument = DB::table('hr_document_employee')->whereIn('id_employee', $allId)->get();
        			$idEmployeeFromDocument = $getDocument->pluck('id_employee')->all();
        			$listDocument = $getDocument->map(function ($object, $key) {
					    return (array)$object;
					});
        			if($listDocument){
        				foreach ($listDocument as $key => $value) {
        					if($value['id_employee'] == $item['I'] && !in_array($item['A'], $idEmployeeFromDocument)){
        						//jika tidak terdapat dokumen yg id employee nya sama dengan id employee yg aktif
        						if(!is_null($value['attachment'])){
	        						$value['id_employee'] = $item['A'];
	        						unset($value['id_document_employee']);
	        						Document::create($value);
        						}
        					}
        				}
        			}
        		}
        	}
        }
    }
	
	public static function getDept($idDetail) {
        $sql = "SELECT md.department_code AS dept_code, mjg.job_class_group AS grade_code
				FROM master_position_detail mpd
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade 
				WHERE mpd.id_position_detail = ?";
        $result = DB::select($sql,[$idDetail])[0];
        return $result;
    }
}
