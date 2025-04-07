<?php

namespace App\Models\Employee\Employee;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;


class Employee extends Model {

    protected $table = 'hr_employee';
    protected $primaryKey = 'id_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_employee', 'name', 'identification_number', 'address_home', 'idcard_address', 'home_base', 'id_country', 'gender', 'marital', 'spouse_complete_name', 'spouse_birthdate', 'place_of_birth', 'id_country_of_birth', 'ptkp_status', 'id_user', 'id_finger', 'status', 'nik_employee', 'mobile_phone', 'work_phone', 'work_mail', 'private_mail', 'join_date', 'expired_date', 'permanent_date', 'resign_date', 'work_address', 'additional_note', 'emergency_phone', 'emergency_contact', 'npwp_number', 'id_company', 'birthdate', 'id_leave', 'id_religion', 'id_employment_status', 'id_shift_group', 'id_timezone', 'image_attachment', 'created_by', 'updated_by','sales_code','id_vaccination_status','lasted_date_vaccine', 'terminate_reason', 'last_position_routing', 'last_position_detail', 'last_department', 'number_of_children','expired_date'
	];

/*	
	public static function getcode($join_date){
		$sql = "SELECT 
                        company_code
                FROM  master_company where id_company =" . session('id_company');
        $company_code = DB::select($sql);
	
		$year = date_format(date_create($join_date),"Y");
		$month =date_format(date_create($join_date),"m");
			
		$nomor = $year.$month;
		
		$get_max['data'] = (int)substr(self::where('nik_employee','like',$nomor."%")->where('id_company', '=', session('id_company'))->max('nik_employee'),6,10);
	//	dd($noUrutAkhir);
			$no = 1;
		if($company_code[0]->company_code == 'BCP'){		
			if($noUrutAkhir) {
				$kode =  sprintf("%04s",abs($noUrutAkhir + 1));
				$nomorbaru = $nomor.$kode;
				}
			else {
				$kode =  sprintf("%04s",$no);
				$nomorbaru = $nomor.$kode;
			}
		}
		else if($company_code[0]->company_code == 'BI'){			
			if($noUrutAkhir) {
				$kode =  sprintf("%03s",abs($noUrutAkhir + 1));
				$nomorbaru = $nomor.$kode;
				}
			else {
				$kode =  sprintf("%03s",$no);
				$nomorbaru = $nomor.$kode;
			}
		}
		
		return $nomorbaru;		
	}
*/
	public static function max_nik($id_company=null){
		$id_company 	= $id_company ?? session('id_company');
		$company 		= Company::where('id_company', $id_company)->first();
		$companyCode 	= $company->company_code;
		$char_com 		= strlen($companyCode);	
		if($companyCode == 'KAP'){
			$nik		= self::where('id_company', $id_company)->where('nik_employee', 'like' ,"KAP0%")->max('nik_employee');
		}
		else{
			$nik 		= self::where('id_company', $id_company)->where('nik_employee', 'like' ,"{$companyCode}%")->max('nik_employee');
		}
		if($nik){
			if($char_com == 2){	
				$start 	= 2;
				$length = 9;
			}
			else if($char_com == 3){
				$start 	= 3;
				$length = 10;
			}
			$max_num = (int)substr($nik, $start, $length);
		} else {
			if($companyCode == 'BCP'){
				$max_num = 8000;
			} else if($companyCode == 'BIZ'){
				$max_num = 100;
			} else if($companyCode == 'KAP'){
				$max_num = 5;
			} else{
				$max_num = 1;
			}
		}
		$return['code'] = $companyCode;
		$return['number'] = $max_num;
		$return['data'] = $nik;

		return $return;
	}

	public static function getcode($id_company=null){
		$get_max = self::max_nik($id_company);
		if(!is_null($get_max['data']) || !empty($get_max['data'])) {
			$numbering =  sprintf("%07s",abs($get_max['number'] + 1));
			$nomorbaru = $get_max['code'].$numbering;
		}
		else {
			$numbering =  sprintf("%07s",$get_max['number']);
			$nomorbaru = $get_max['code'].$numbering;
		}
		return $nomorbaru;		
	}
	
    public static function get_access($id_url) {
         $sql = "SELECT rbu.id_branch, mu.id_user, mm.address_menu, rbu.id_company
					FROM public.relation_branch_users rbu
					JOIN master_user_responsibility mur
					ON rbu.id_user_responsibility = mur.id_user_responsibility
					JOIN master_users mu
					ON mur.id_user = mu.id_user
					JOIN master_menu mm
					ON mur.id_menu = mm.id_menu
					WHERE mu.id_user = ? AND mm.address_menu = ? 
					AND mu.status = 'A' AND rbu.id_company = ?";				
        $data = DB::select($sql,[session('id_user'),$id_url,session('id_company')]);
        return $data;
    }
	public static function getdata($group_branch, $nik=null, $status=null) {
		$get = DB::table(DB::raw("sp_funct_get_employee ('".session('id_company')."') se"))
                ->select('se.*');

        if($group_branch || $group_branch != ""){
			$get->whereIn('se.id_branch', explode(',', $group_branch));
		}
		if($nik){
			$get->whereIn('se.nik_employee', $nik);
		}
		if($status){
			$get->whereIn('se.status', $status);
		}
        $return = $get->get();
        return $return;

		// if($group_branch == null || $group_branch == ""){
		// 	$where_branch = "";
		// }	
		// else{
		// 	$where_branch = "WHERE se.id_branch in(".$group_branch.")";
		// }
		// $sql = "SELECT se.* 
		// 		FROM sp_funct_get_employee(".session('id_company').") se "
		// 		.$where_branch;
		// 		var_dump($sql);die;
        // $data = DB::select($sql);
        // return $data;
	}
	/*
	public static function getdata($group_branch) {
				$id_company = session('id_company');
				$data = DB::table(DB::raw("sp_funct_employee_position_tree_view(null,".$id_company.") sfeptv"))
                ->select('sfeptv.id_employee', 'sfeptv.nik_employee','sfeptv.employee_name', 'sfeptv.private_mail', 'sfeptv.join_date', 'sfeptv.position_routing', 'sfeptv.department', 'sfeptv.branch', 'sfeptv.job_grade', 'sfeptv.status', 'sfeptv.employment_status');
        if($group_branch){
        	$data->whereIn('sfeptv.id_branch', explode(',',$group_branch));
        }
        $data->orderBy('sfeptv.employee_name','asc');
        
        $result = $data->get();

        return $result;
    }
	*/
	public static function getreport($group_branch) {
		if($group_branch == null || $group_branch == ""){
		$where_branch = "";
	}	
	else{
		$where_branch = "WHERE sr.id_branch in(".$group_branch.")";
	}
		$sql = "SELECT sr.* 
			FROM sp_funct_get_employee_report_all(".session('id_company').") sr "
			.$where_branch;
        $data = DB::select($sql);
        return $data;
    }
	
	/*
	public static function browse_job() {
        $id_company = session('id_company');
         $sql = "SELECT  he2.name as emp_name, md.description AS department,
                    mpd.id_position_detail, mpd.description AS position_detail,
                   mpd2.description AS parent_position_detail,
				   he.name AS name_supervisor,
                   mpr.description AS position_routing,                 
                   mjp.description AS job_position,
                   mb.description AS branch,
				   ml.description AS location,
                   mr.description AS regional,
				   STRING_AGG(mp.description,', ') AS principal
            FROM  master_position_detail mpd
            JOIN  master_position_routing mpr
              ON  mpd.id_position_routing = mpr.id_routing
             AND  mpd.id_company = mpr.id_company
             AND  mpr.status = 'A'          
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
            WHERE (mpd.id_company = ? AND mpd.assigned_to_company IS NULL
              AND mpd.status = 'A' AND mpd.id_employee IS NULL)
			  OR (mpd.assigned_to_company = ?
              AND mpd.status = 'A' AND mpd.id_employee IS NULL)
			  GROUP BY he2.name, md.description, mpd.id_position_detail, mpd.description, mpd2.description, he.name,
                 mpr.description, mjp.description, mb.description,
				 ml.description, mr.description";
        $result = DB::select($sql,[$id_company,$id_company]);
	//	dd($result);
        return $result;
    }
	*/
	
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
	
	public static function browse_job() {
		$cancel = self::cancel()->id_general_data;
		$reject = self::reject()->id_general_data;
		$id_company = session('id_company');
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
	public static function browse_check($data) {
		$id_position_detail = $data;
         $sql = "SELECT  he2.name, md.description AS department,
                    mpd.id_position_detail, mpd.description AS position_detail,
                   mpd2.description AS parent_position_detail,
				   he.name AS name_supervisor,                   
                   mpr.description AS position_routing,                 
                   mjp.description AS job_position,
                   mb.description AS branch,
				   ml.description AS location,
                   mr.description AS regional,
				   mp.description AS principal 
            FROM  master_position_detail mpd
            JOIN  master_position_routing mpr
              ON  mpd.id_position_routing = mpr.id_routing
             AND  mpd.id_company = mpr.id_company
             AND  mpr.status = 'A'          
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
            WHERE mpd.status = 'A' AND  mpd.id_position_detail=".$id_position_detail;
        $result = DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
	
	public static function get_effective_date($id_position){
		$sql = "SELECT hct.id_career_transaction, hct.id_employee, hct.id_old_position_detail, hct.effective_date, mgd.code
					FROM hr_career_transaction hct
					LEFT JOIN master_general_data mgd 
					ON hct.id_transition_category = mgd.id_general_data 
					WHERE hct.id_old_position_detail = ?
					ORDER BY id_career_transaction DESC 
					LIMIT 1";
        $result = DB::select($sql,[$id_position]);
        return $result;
	}
    public static function get_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_empstatus() {
         $sql = "SELECT id_general_data id,
						description text
				  FROM master_general_data 
				  WHERE id_general_type = 2 and status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	public static function get_shift() {
         $sql = "SELECT id_shiftgroup id,
						description text
				  FROM master_shiftgroup_header
				  WHERE status = 'A' and id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_leave() {
         $sql = "SELECT id_leave_header id,
						leave_group_name text
				  FROM master_leave_header
				  WHERE status = 'A' and id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_leave_id($data) {
		$id = $data;
		$sql = "SELECT *
				  FROM master_leave_detail
				  WHERE id_leave_header ='".$id."' and status = 'A' and id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_user() {
		if(isset($_GET['search'])){
			$search = $_GET['search'];
			$sql = "SELECT mu.id_user id, 
							CONCAT(mu.description_name,' (',mu.user_name,')') as text
					FROM master_users mu
					JOIN relation_company_users rcu
					 ON rcu.id_user = mu.id_user
					 WHERE mu.status = 'A' and mu.user_name LIKE '%".$search."%' and rcu.id_company =". session('id_company');
		}
		else{
			 $sql = "SELECT mu.id_user id, 
					CONCAT(mu.description_name,' (',mu.user_name,')') as text
				FROM master_users mu
				JOIN relation_company_users rcu
				 ON rcu.id_user = mu.id_user
				 WHERE mu.status = 'A' and rcu.id_company =". session('id_company');
		}
		$result = DB::select($sql);
        return $result;
    }
	public static function get_timezone() {
         $sql = "SELECT id_general_data id,
						description text
				  FROM master_general_data 
				  WHERE id_general_type = 3 and status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	
	public static function get_country() {
         $sql = "SELECT id_country id,
						description text
				  FROM master_country
				  WHERE status = 'A' and country_code = 'IDN'
			UNION ALL
			SELECT id_country id,
						description text
				  FROM master_country
				  WHERE status = 'A' and country_code != 'IDN'";
        $result = DB::select($sql);
        return $result;
    }
	public static function get_religion() {
         $sql = "SELECT id_general_data id,
						description text
				  FROM master_general_data 
				  WHERE id_general_type = 1 and status = 'A' AND id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_education_level() {
         $sql = "SELECT id_general_data id,
						description text
				  FROM master_general_data 
				  WHERE id_general_type = 4 and status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	
	public static function get_bank() {
         $sql = "SELECT id_bank id,
						bank_code text
				  FROM master_bank
				  WHERE status = 'A' and id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	
	public static function get_currency() {
         $sql = "SELECT id_currency id, long_description text
					FROM master_currency
					WHERE status = 'A' AND currency_code = 'IDR'
				UNION ALL
				SELECT id_currency id, long_description text
					FROM master_currency
					WHERE status = 'A' AND currency_code != 'IDR'";
        $result = DB::select($sql);
        return $result;
    }	
	
	public static function get_insurance() {
         $sql = "SELECT id_insurance id,
						insurance_code text
				  FROM master_insurance
				  WHERE status = 'A' and id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	public static function get_checklist_onboarding() {
         $sql = "SELECT id_checklist id,
						document_name text
				  FROM master_checklist_employee
				  WHERE checklist_type = 'Onboarding' and status = 'A' and id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	public static function get_checklist_offboarding() {
         $sql = "SELECT id_checklist id,
						document_name text
				  FROM master_checklist_employee
				  WHERE checklist_type = 'Offboarding' and status = 'A' and id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_ex_concurent() {
         $sql = "SELECT mgd.description
				FROM master_general_data mgd
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type 
				WHERE mgt.general_type = 'master_employment_status'
				AND mgd.status = 'A' AND mgd.code != 'Concurent' AND mgd.id_company = ". session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	
	public static function get_emp_leave($data) {
		$id_employee = $data['id_employee'];
		$sql = "SELECT mlt.description as leave_type, mlt.restrict_by, hlbe.* 
					FROM hr_leave_balance_emp hlbe 
					JOIN hr_employee he
					ON hlbe.id_employee = he.id_employee AND he.status = 'A'
					JOIN master_leave_type mlt 
					ON hlbe.id_leave_type = mlt.id_leave_type 
					WHERE hlbe.status = 'A' AND hlbe.id_employee = ".$id_employee." and hlbe.id_company = ".session('id_company')."
			UNION
				SELECT mlt.description as leave_type, mlt.restrict_by, hlbe.* 
					FROM hr_leave_balance_emp hlbe 
					JOIN hr_employee he
					ON hlbe.id_employee = he.id_employee AND he.status = 'I'
					JOIN master_leave_type mlt 
					ON hlbe.id_leave_type = mlt.id_leave_type 
					WHERE he.status = 'I' AND he.resign_date BETWEEN hlbe.effective_date AND hlbe.expired_date AND hlbe.id_employee = ".$id_employee." and hlbe.id_company = ".session('id_company');
	
	//	$sql = "select * from sp_funct_leave_balance_view(?,?,null)";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	public static function get_emp_contract($data) {
		$id_employee = $data['id_employee'];
        $sql = "select * from sp_funct_employee_contract_view(?,?,null)";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }	
		
	public static function get_emp_career($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT hct.reference_number, mgd.description as transition_category, mgd2.description as transaction_type, 
				mgd3.description as employment_status, mpd.description as position_detail, mpr.description as position_routing, 
				mjg.description as job_grade, mjs.description as job_status, ml.description as location,
				hct.effective_date, hct.expired_date, age (coalesce(hct.expired_date,new_position.effective_date, current_date ),hct.effective_date)::text as duration
				FROM hr_career_transaction hct
				JOIN master_general_data mgd
				ON hct.id_transition_category = mgd.id_general_data
				JOIN master_general_data mgd2
				ON hct.id_transaction_type = mgd2.id_general_data
				JOIN master_general_data mgd3
				ON hct.id_employment_status = mgd3.id_general_data
				JOIN master_general_data mgd4
				ON hct.id_approval_status = mgd4.id_general_data
				JOIN master_position_detail mpd
				ON hct.id_position_detail = mpd.id_position_detail
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_location ml
				ON mpd.id_location = ml.id_location
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_job_status mjs
				ON mpr.id_job_status = mjs.id_job_status
				LEFT JOIN (
                      SELECT ROW_NUMBER() OVER (ORDER BY hct.id_career_transaction ASC) as id_number,
                      hct.id_career_transaction, hct.id_old_position_detail, hct.effective_date
                      FROM hr_career_transaction hct
                      JOIN master_general_data mgd
                        ON hct.id_approval_status = mgd.id_general_data
                       AND hct.id_company = mgd.id_company
                      WHERE hct.id_employee = ".$id_employee."
                        AND hct.id_company= ".session('id_company')."
                        AND mgd.code = 'Approved' 
                      ) AS new_position
				ON hct.id_position_detail = new_position.id_old_position_detail
				AND hct.id_career_transaction < new_position.id_career_transaction
				WHERE hct.id_employee = ? AND hct.id_company = ? AND mgd4.code = 'Approved'
				AND mgd.id_general_type = 5 AND mgd2.id_general_type = 6 AND mgd3.id_general_type = 2";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }	
	public static function get_emp_awdcp($data) {
		$id_employee = $data['id_employee'];
        $sql = "select *,
				case when transaction_type = 'A' then 'Award'
					 when transaction_type = 'D' then 'Dicipline'
					 end as transaction_type
				 from hr_award_dicipline_transaction
				where id_employee = ? and status = 'A' and id_company = ?";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }
	public static function get_emp_history($data) {
		$no_ktp = $data['no_ktp'];
        $sql = "select he.*, mc.description as company,
				 case when he.status = 'A' then 'Active'
					 when he.status = 'I' then 'Inactive'
					 end as status_emp
				 from hr_employee he
				 left join master_company as mc
				 on he.id_company = mc.id_company
				 where he.identification_number = ?";
        $result = DB::select($sql,[$no_ktp]);
	//	dd($result);
        return $result;
    }
	public static function generateleave($data) {
		$id_employee = $data['id_employee'];
		$current_date = date('Y-m-d');
        $result = DB::select("select * from  GenerateMassLeave (?, ?, ?, ?)",[session('id_company'),session('id_user'),$id_employee,$current_date]);
        return $result;
    }	
	public static function ktpcheck($data) {
		$no_ktp = $data['no_ktp'];
		$sql = "select * from sp_funct_employee_view (null,null) 
				where identification_number = ?";
        $result = DB::select($sql,[$no_ktp]);
        return $result;
    }	
	
	public static function get_vaccine() {
         $sql = "SELECT id_general_data id,
						description text
				  FROM master_general_data 
				  WHERE id_general_type = 20 and status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }	
	
	public static function get_employee_edit($data) {
        $result = [];
        $sql = "select he.*, mgd.description as employee_status
					from hr_employee he				
					left join master_general_data as mgd
					on he.id_employment_status = mgd.id_general_data
					where he.id_employee=?";
        $result = (Array) DB::select($sql, [$data['id_employee']])[0];
	//	dd($result);
		
        $sql = "SELECT  sfepv.id_position_detail, sfepv.job_position, sfepv.position_routing,
				sfepv.position_detail, sfepv.branch, sfepv.work_location, sfepv.parent_position_detail,
				sfepv.parent_emp_name, sfepv.department,STRING_AGG(mp.description,', ') AS principal
			FROM sp_funct_employee_position_view(?,?) sfepv
			LEFT JOIN  relation_positiondetail_principal rpp
	  		  ON  sfepv.id_position_detail = rpp.id_position_detail
			LEFT JOIN  master_principal mp
			  ON  rpp.id_principal = mp.id_principal
			GROUP BY sfepv.id_position_detail, sfepv.job_position, sfepv.position_routing,
				 sfepv.position_detail, sfepv.branch, sfepv.work_location, sfepv.parent_position_detail,
				 sfepv.parent_emp_name, sfepv.department";
        $result_menu = DB::select($sql, [$data['id_employee'],session('id_company')]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_position_detail')->toArray();
	
		$sql3 = "select * from hr_education_employee where id_employee = ? and id_company = ?";
        $result_menu3 = DB::select($sql3, [$data['id_employee'],session('id_company')]);
        $collect_menu3 = collect($result_menu3);
        $group_menu3 = $collect_menu3->groupBy('id_education_employee')->toArray();

		$sql4 = "select * from hr_family_employee where id_employee = ? and id_company = ?";
        $result_menu4 = DB::select($sql4, [$data['id_employee'],session('id_company')]);
        $collect_menu4 = collect($result_menu4);
        $group_menu4 = $collect_menu4->groupBy('id_family_employee')->toArray();
		
		$sql10 = "select * from hr_experience_employee where id_employee = ? and id_company = ?";
        $result_menu10 = DB::select($sql10, [$data['id_employee'],session('id_company')]);
        $collect_menu10 = collect($result_menu10);
        $group_menu10 = $collect_menu10->groupBy('id_experience_employee')->toArray();
		
		$sql11 = "select * from hr_skill_employee where id_employee = ? and id_company = ?";
        $result_menu11 = DB::select($sql11, [$data['id_employee'],session('id_company')]);
        $collect_menu11 = collect($result_menu11);
        $group_menu11 = $collect_menu11->groupBy('id_skill_employee')->toArray();
		
		$sql12 = "select * from hr_certification_employee where id_employee = ? and id_company = ?";
        $result_menu12 = DB::select($sql12, [$data['id_employee'],session('id_company')]);
        $collect_menu12 = collect($result_menu12);
        $group_menu12 = $collect_menu12->groupBy('id_certification_employee')->toArray();

		$sql5 = "select * from hr_bank_employee where id_employee = ? and id_company = ?";
        $result_menu5 = DB::select($sql5, [$data['id_employee'],session('id_company')]);
        $collect_menu5 = collect($result_menu5);
        $group_menu5 = $collect_menu5->groupBy('id_bank_employee')->toArray();
		
		$sql6 = "select * from hr_insurance_employee where id_employee = ? and id_company = ?";
        $result_menu6 = DB::select($sql6, [$data['id_employee'],session('id_company')]);
        $collect_menu6 = collect($result_menu6);
        $group_menu6 = $collect_menu6->groupBy('id_insurance_employee')->toArray();
		
		$sql7 = "select * from hr_document_employee where id_employee = ? and id_company = ?";
        $result_menu7 = DB::select($sql7, [$data['id_employee'],session('id_company')]);
        $collect_menu7 = collect($result_menu7);
        $group_menu7 = $collect_menu7->groupBy('id_document_employee')->toArray();
		
		$sql8 = "SELECT hce.*, mce.checklist_type
					FROM hr_checklist_employee hce
					JOIN master_checklist_employee mce
					ON hce.id_checklist = mce.id_checklist
					WHERE mce.checklist_type = 'Onboarding' AND hce.id_employee = ? and hce.id_company = ?";
        $result_menu8 = DB::select($sql8, [$data['id_employee'],session('id_company')]);
        $collect_menu8 = collect($result_menu8);
        $group_menu8 = $collect_menu8->groupBy('id_checklist_employee')->toArray();
		
		$sql9 = "SELECT hce.*, mce.checklist_type
					FROM hr_checklist_employee hce
					JOIN master_checklist_employee mce
					ON hce.id_checklist = mce.id_checklist
					WHERE mce.checklist_type = 'Offboarding' AND hce.id_employee = ? and hce.id_company = ?";
        $result_menu9 = DB::select($sql9, [$data['id_employee'],session('id_company')]);
        $collect_menu9 = collect($result_menu9);
        $group_menu9 = $collect_menu9->groupBy('id_checklist_employee')->toArray();

        $result['job'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['job'][] = [
                'id_position_detail' => $group_menu[$value][0]->id_position_detail,
                'job_position' => $group_menu[$value][0]->job_position,
                'position_routing' => $group_menu[$value][0]->position_routing,
                'position_detail' => $group_menu[$value][0]->position_detail,
                'branch' => $group_menu[$value][0]->branch,
                'location' => $group_menu[$value][0]->work_location,
                'parent_position_detail' => $group_menu[$value][0]->parent_position_detail,
                'name_supervisor' => $group_menu[$value][0]->parent_emp_name,
                'department' => $group_menu[$value][0]->department,
                'principal' => $group_menu[$value][0]->principal,
            ];
        }

		$result['edu'] = [];
        foreach (array_keys($group_menu3) as $key => $value) {
            $result['edu'][] = [
                'id_education_employee' => $group_menu3[$value][0]->id_education_employee,
                'major' => $group_menu3[$value][0]->major,
                'education_name' => $group_menu3[$value][0]->education_name,
                'id_education_level' => $group_menu3[$value][0]->id_education_level,
                'education_city' => $group_menu3[$value][0]->education_city,
                'start_year' => $group_menu3[$value][0]->start_year,
                'end_year' => $group_menu3[$value][0]->end_year,
            ];
        }
		$result['fam'] = [];
        foreach (array_keys($group_menu4) as $key => $value) {
            $result['fam'][] = [
                'id_family_employee' => $group_menu4[$value][0]->id_family_employee,
                'family_name' => $group_menu4[$value][0]->family_name,
                'gender' => $group_menu4[$value][0]->gender,
                'relationship' => $group_menu4[$value][0]->relationship,
                'mobile_phone' => $group_menu4[$value][0]->mobile_phone,               
            ];
        }
		
		$result['ex'] = [];
        foreach (array_keys($group_menu10) as $key => $value) {
            $result['ex'][] = [
                'id_experience_employee' => $group_menu10[$value][0]->id_experience_employee,
                'position_name' => $group_menu10[$value][0]->position_name,
                'company_name' => $group_menu10[$value][0]->company_name,
                'company_city' => $group_menu10[$value][0]->company_city,
                'start_year' => $group_menu10[$value][0]->start_year,
                'end_year' => $group_menu10[$value][0]->end_year,
            ];
        }
		
		$result['skill'] = [];
        foreach (array_keys($group_menu11) as $key => $value) {
            $result['skill'][] = [
                'id_skill_employee' => $group_menu11[$value][0]->id_skill_employee,
                'skill_name' => $group_menu11[$value][0]->skill_name,
                'skill_level' => $group_menu11[$value][0]->skill_level,
            ];
        }
		
		$result['cert'] = [];
        foreach (array_keys($group_menu12) as $key => $value) {
            $result['cert'][] = [
                'id_certification_employee' => $group_menu12[$value][0]->id_certification_employee,
                'certification_name' => $group_menu12[$value][0]->certification_name,
                'certified_by' => $group_menu12[$value][0]->certified_by,
                'years_issued' => $group_menu12[$value][0]->years_issued,
                'validity_period' => $group_menu12[$value][0]->validity_period,
            ];
        }
		
		$result['bank'] = [];
        foreach (array_keys($group_menu5) as $key => $value) {
            $result['bank'][] = [
                'id_bank_employee' => $group_menu5[$value][0]->id_bank_employee,
                'id_bank' => $group_menu5[$value][0]->id_bank,
                'bank_name' => $group_menu5[$value][0]->bank_name,
                'bank_account' => $group_menu5[$value][0]->bank_account,
                'account_name' => $group_menu5[$value][0]->account_name,
                'bank_currency' => $group_menu5[$value][0]->bank_currency,
                'default_bank' => $group_menu5[$value][0]->default_bank,             
            ];
        }
		
		$result['ins'] = [];
        foreach (array_keys($group_menu6) as $key => $value) {
            $result['ins'][] = [
                'id_insurance_employee' => $group_menu6[$value][0]->id_insurance_employee,
                'id_insurance' => $group_menu6[$value][0]->id_insurance,
                'emp_insurance_number' => $group_menu6[$value][0]->emp_insurance_number,
                'effective_date' => $group_menu6[$value][0]->effective_date,
                'expired_date' => $group_menu6[$value][0]->expired_date,
                'beneficiary_name' => $group_menu6[$value][0]->beneficiary_name,             
            ];
        }
		
		$result['doc'] = [];
		$checkDocumentType = [];
        foreach (array_keys($group_menu7) as $key => $value) {
        		$checkDocumentType[] = $group_menu7[$value][0]->document_name;
            $result['doc'][] = [
                'id_document_employee' => $group_menu7[$value][0]->id_document_employee,
                'document_name' => $group_menu7[$value][0]->document_name,
                'document_number' => $group_menu7[$value][0]->document_number,
                'effective_date' => $group_menu7[$value][0]->effective_date,
                'expired_date' => $group_menu7[$value][0]->expired_date,
                'attachment' => $group_menu7[$value][0]->attachment,
            ];
        }
    		if(!in_array('Surat Pernyataan', $checkDocumentType)){
    				$result['doc'][] = [
                'id_document_employee' => null,
                'document_name' => 'Surat Pernyataan',
                'document_number' => null,
                'effective_date' => null,
                'expired_date' => null,
                'attachment' => null,
            ];
    		}
    		
		$result['onboarding'] = [];
        foreach (array_keys($group_menu8) as $key => $value) {
            $result['onboarding'][] = [
                'id_checklist_employee' => $group_menu8[$value][0]->id_checklist_employee,
                'id_checklist' => $group_menu8[$value][0]->id_checklist,
                'effective_date' => $group_menu8[$value][0]->effective_date,
                'remark' => $group_menu8[$value][0]->remark,
                'attachment' => $group_menu8[$value][0]->attachment,
                'completed' => $group_menu8[$value][0]->completed,
            ];
        }
		$result['offboarding'] = [];
        foreach (array_keys($group_menu9) as $key => $value) {
            $result['offboarding'][] = [
                'id_checklist_employee' => $group_menu9[$value][0]->id_checklist_employee,
                'id_checklist' => $group_menu9[$value][0]->id_checklist,
                'effective_date' => $group_menu9[$value][0]->effective_date,
                'remark' => $group_menu9[$value][0]->remark,
                'attachment' => $group_menu9[$value][0]->attachment,
                'completed' => $group_menu9[$value][0]->completed,
            ];
        }
	//	dd($result);	   
        return $result;
    }
	
	public static function checkDoubleJoinByIdNumberAndStatus($idNumber=null) {
		$master = DB::table('master_general_data as mgd')
                ->leftJoin('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
                ->select('mgd.id_general_data', 'mgd.code')
                ->where('mgd.code', 'Join')
				->where('mgd.id_company', session('id_company'))
				->first();

        $getMax = DB::table('hr_career_transaction as hct')
				->select('hct.*')
                ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hct.id_employee');
		if($idNumber){
        	$getMax->where('he.identification_number', $idNumber);
        }
		$getMax->orderBy('hct.id_career_transaction', 'desc')->limit(1);
		$getMax = $getMax->first();

        $data = DB::table('hr_career_transaction as hct')
                ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hct.id_employee')
                ->select('he.identification_number', 'he.nik_employee', 'he.name', 'hct.*')
                ->where('hct.id_transition_category', $master->id_general_data)
                ->where('hct.id_career_transaction', @$getMax->id_career_transaction)
                ->where('he.status', 'A')
                ->where('hct.id_company', session('id_company'));
        if($idNumber){
        	$data->where('he.identification_number', $idNumber);
        }
        $career = $data->get();
        return $career;
    }
	
	public static function getEmployeeDetail($nik=null, $idEmployee=null, $full=false, $isActive=true, $parentIdPositionDetail=false) {
        $return = collect([]);
        if($nik){
            if(!is_array($nik)){
                $nik = [$nik];
            }
        }
        if($idEmployee){
            if(!is_array($idEmployee)){
                $idEmployee = [$idEmployee];
            }
        }
        if($parentIdPositionDetail){
            if(!is_array($parentIdPositionDetail)){
                $parentIdPositionDetail = [$parentIdPositionDetail];
            }
        }
            
        $q1 = DB::table('hr_employee as he')
                ->leftJoin('master_position_detail as mpd', function ($join) {
                    $join->where('mpd.secondary_position', 0);
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                    $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->leftJoin('master_job_position as mjp', function ($join) {
                    $join->on('mpr.id_position', '=', 'mjp.id_position');
                    $join->on('mpr.id_company', '=', 'mjp.id_company');
                })
                ->leftJoin('master_department as md', function ($join) {
                    $join->on('mjp.id_dept', '=', 'md.id_dept');
                    $join->on('mjp.id_company', '=', 'md.id_company');
                })
                ->leftJoin('master_branch as mb', function ($join) {
                    $join->on('mpd.id_branch', '=', 'mb.id_branch');
                    $join->on('mpd.id_company', '=', 'mb.id_company');
                })
                ->leftJoin('master_region as mr', function ($join) {
                    $join->on('mb.id_region', '=', 'mr.id_region');
                    $join->on('mb.id_company', '=', 'mr.id_company');
                })
                ->leftJoin('master_location as ml', function ($join) {
                    $join->on('ml.id_location', '=', 'mpd.id_location');
                    $join->on('ml.id_company', '=', 'mpd.id_company');
                });

        if($full){
            $q1->leftJoin('master_position_detail as mpd2', function ($join) {
                    $join->on('mpd.parent_id_position_detail', '=', 'mpd2.id_position_detail');
                    $join->on('mpd.id_company', '=', 'mpd2.id_company');
                    $join->where('mpd2.status', 'A');
                })
                ->leftJoin('hr_employee as he2', function ($join) {
                    $join->whereRaw('(mpd2.id_employee = he2.id_employee OR mpd2.id_employee2 = he2.id_employee)');
                    $join->whereRaw('(mpd2.id_company = he2.id_company OR mpd2.assigned_to_company = he2.id_company)');
                    $join->where('he2.status', 'A');
                })
                ->leftJoin('master_position_detail as mpd3', function ($join) {
                    $join->on('mpd2.parent_id_position_detail', '=', 'mpd3.id_position_detail');
                    $join->on('mpd2.id_company', '=', 'mpd3.id_company');
                    $join->where('mpd3.status', 'A');
                })
                ->leftJoin('hr_employee as he3', function ($join) {
                    $join->whereRaw('(mpd3.id_employee = he3.id_employee)');
                    $join->whereRaw('(mpd3.id_company = he3.id_company OR mpd3.assigned_to_company = he3.id_company)');
                    $join->where('he3.status', 'A');
                })
                ->leftJoin('master_job_grade as mjg', function ($join) {
                    $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
                    $join->on('mpr.id_company', '=', 'mjg.id_company');
                })
                ->leftJoin('master_job_status as mjs', function ($join) {
                    $join->on('mpr.id_job_status', '=', 'mjs.id_job_status');
                    $join->on('mpr.id_company', '=', 'mjs.id_company');
                })
                ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                    $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                    $join->on('rpp.id_company', '=', 'mpd.id_company');
                })
                ->leftJoin('master_principal as mp', function ($join) {
                    $join->on('mp.id_principal', '=', 'rpp.id_principal');
                })
                ->leftJoin('master_general_data as mgd2', 'mgd2.id_general_data', '=', 'he.id_timezone');
        }
                
        $q1->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'he.id_employment_status')
                ->leftJoin('master_company as mc', 'mc.id_company', '=', 'he.id_company')
                ->leftJoin('master_company as mc2', 'mc.id_company', '=', 'mpd.assigned_to_company')
                ->leftJoin('master_shiftgroup_header as msh', 'msh.id_shiftgroup', '=', 'he.id_shift_group');

        if($full){
            $q1->select('he.id_employee', 'he.id_user', 'he.id_company', 'mc.company_code', 'mc.company_code', 'mc.company_type', 'mc.description as company', 'he.nik_employee', 'he.name', 'he.gender', 'he.marital', 'he.status', 'he.mobile_phone', 'he.private_mail', 'he.work_phone', 'he.work_mail', 'he.join_date', 'he.expired_date', 'he.permanent_date', 'he.resign_date', 'he.image_attachment', 'mgd.description as employment_status', 'mc2.company_code as assigned_to_company', 'md.id_dept as id_department', 'md.department_code', 'md.description as department', 'mpr.id_routing', 'mpr.description as position_routing', 'mpd.id_position_detail', 'mpd.description as position_detail', 'mpd.parent_id_position_detail', 'mr.id_region', 'mr.region_code', 'mr.description as region', 'mb.id_branch', 'mb.branch_code', 'mb.description as branch', 'ml.id_location', 'ml.location_code', 'ml.description as location', 'msh.id_shiftgroup', 'msh.shiftgroup_code', 'msh.description as shift_group', 'msh.automatic_absence', 'msh.allow_checkout_nextdays','he.lock_gps_location', 'mgd2.code as timezone_code', 'mgd2.description as timezone', 'mjg.id_job_grade', 'mjg.description as job_grade', 'mjg.job_class_group', 'mjs.description as job_status', 'mp.description as principal', 'he2.id_employee as direct_id_employee', 'he2.name as direct_employee_name', 'he3.id_employee as indirect_id_employee', 'he3.name as indirect_employee_name');
        } else {
            $q1->select('he.id_employee', 'he.id_user', 'he.id_company', 'mc.company_code', 'mc.company_code', 'mc.company_type', 'mc.description as company', 'he.nik_employee', 'he.name', 'he.gender', 'he.marital', 'he.status', 'he.mobile_phone', 'he.private_mail', 'he.work_phone', 'he.work_mail', 'he.join_date', 'he.expired_date', 'he.permanent_date', 'he.resign_date', 'he.image_attachment', 'mgd.description as employment_status', 'mc2.company_code as assigned_to_company', 'md.id_dept as id_department', 'md.department_code', 'md.description as department', 'mpr.id_routing', 'mpr.description as position_routing', 'mpd.id_position_detail', 'mpd.description as position_detail', 'mpd.parent_id_position_detail', 'mr.id_region', 'mr.region_code', 'mr.description as region', 'mb.id_branch', 'mb.branch_code', 'mb.description as branch', 'ml.id_location', 'ml.location_code', 'ml.description as location', 'msh.id_shiftgroup', 'msh.shiftgroup_code', 'msh.description as shift_group', 'msh.automatic_absence', 'msh.allow_checkout_nextdays', 'he.lock_gps_location');
        }

        if($isActive){
            $q1->where('he.status', 'A');
        }
        if($idEmployee){
            $q1->whereIn('he.id_employee', $idEmployee);
        }
        if($nik){
            $q1->whereIn('he.nik_employee', $nik);
        }
        if($parentIdPositionDetail){
            $q1->whereIn('mpd.parent_id_position_detail', $parentIdPositionDetail);
        }
        $result = $q1->get();

        if($result->count() > 0){
            foreach ($result as $key => $val) {
                if(@$result[$key]->image_attachment != null){
                    if(strlen(@$result[$key]->image_attachment) > 1000){
                        @$result[$key]->image_attachment = "data:image;base64,".@$result[$key]->image_attachment;
                    } else {
                        $urlPhoto = 'public/upload/photo/'. @$result[$key]->image_attachment;
                        if (Storage::exists($urlPhoto)) {
                            @$result[$key]->image_attachment = url('project/storage/app/'.$urlPhoto);
                        } else {
                            @$result[$key]->image_attachment = asset('public/global/img/avatar.jpg');
                        }
                    }
                } else {
                    @$result[$key]->image_attachment = asset('public/global/img/avatar.jpg');
                }
            }
            $return = $result;
        }
        return $return;
    }

    public static function getPositionDetail($idPositionDetail=null, $parentIdPositionDetail=null, $idCompany=false, $full=false, $isActive=true) {
        $return = collect([]);
        $allIdPositionDetail = collect([]);

        if($idPositionDetail){
            if(!is_array($idPositionDetail)){
                $idPositionDetail = [$idPositionDetail];
            }
        }
        if($parentIdPositionDetail){
            if(!is_array($parentIdPositionDetail)){
                $parentIdPositionDetail = [$parentIdPositionDetail];
            }
            $idPositionDetailByParent = DB::table('master_position_detail as mpd')->whereIn('parent_id_position_detail', $parentIdPositionDetail)->get()->pluck('id_position_detail')->all();
        }
        if($idCompany){
            if(!is_array($idCompany)){
                $idCompany = [$idCompany];
            }
        }

        $q1 = DB::table('master_position_detail as mpd')
                ->leftJoin('hr_employee as he', function ($join) {
                	$join->on(function($query){
	                    $query->on('mpd.id_employee', '=', 'he.id_employee');
	                    $query->orOn('mpd.id_employee2', '=', 'he.id_employee');
	                    $query->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
	                });
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->leftJoin('master_job_position as mjp', function ($join) {
                    $join->on('mpr.id_position', '=', 'mjp.id_position');
                    $join->on('mpr.id_company', '=', 'mjp.id_company');
                })
                ->leftJoin('master_department as md', function ($join) {
                    $join->on('mjp.id_dept', '=', 'md.id_dept');
                    $join->on('mjp.id_company', '=', 'md.id_company');
                })
                ->leftJoin('master_branch as mb', function ($join) {
                    $join->on('mpd.id_branch', '=', 'mb.id_branch');
                    $join->on('mpd.id_company', '=', 'mb.id_company');
                })
                ->leftJoin('master_region as mr', function ($join) {
                    $join->on('mb.id_region', '=', 'mr.id_region');
                    $join->on('mb.id_company', '=', 'mr.id_company');
                })
                ->leftJoin('master_location as ml', function ($join) {
                    $join->on('ml.id_location', '=', 'mpd.id_location');
                    $join->on('ml.id_company', '=', 'mpd.id_company');
                });

        if($full){
            $q1->leftJoin('master_position_detail as mpd2', function ($join) {
                    $join->on('mpd.parent_id_position_detail', '=', 'mpd2.id_position_detail');
                    $join->on('mpd.id_company', '=', 'mpd2.id_company');
                    $join->where('mpd2.status', 'A');
                })
                ->leftJoin('hr_employee as he2', function ($join) {
                    $join->whereRaw('(mpd2.id_employee = he2.id_employee OR mpd2.id_employee2 = he2.id_employee)');
                    $join->whereRaw('(mpd2.id_company = he2.id_company OR mpd2.assigned_to_company = he2.id_company)');
                    $join->where('he2.status', 'A');
                })
                ->leftJoin('master_position_detail as mpd3', function ($join) {
                    $join->on('mpd2.parent_id_position_detail', '=', 'mpd3.id_position_detail');
                    $join->on('mpd2.id_company', '=', 'mpd3.id_company');
                    $join->where('mpd3.status', 'A');
                })
                ->leftJoin('hr_employee as he3', function ($join) {
                    $join->whereRaw('(mpd3.id_employee = he3.id_employee)');
                    $join->whereRaw('(mpd3.id_company = he3.id_company OR mpd3.assigned_to_company = he3.id_company)');
                    $join->where('he3.status', 'A');
                })
                ->leftJoin('master_job_grade as mjg', function ($join) {
                    $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
                    $join->on('mpr.id_company', '=', 'mjg.id_company');
                })
                ->leftJoin('master_job_status as mjs', function ($join) {
                    $join->on('mpr.id_job_status', '=', 'mjs.id_job_status');
                    $join->on('mpr.id_company', '=', 'mjs.id_company');
                })
                ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                    $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                    $join->on('rpp.id_company', '=', 'mpd.id_company');
                })
                ->leftJoin('master_principal as mp', function ($join) {
                    $join->on('mp.id_principal', '=', 'rpp.id_principal');
                })
                ->leftJoin('master_general_data as mgd2', 'mgd2.id_general_data', '=', 'he.id_timezone');
        }
                
        $q1->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'he.id_employment_status')
                ->leftJoin('master_company as mc', 'mc.id_company', '=', 'he.id_company')
                ->leftJoin('master_company as mc2', 'mc2.id_company', '=', 'mpd.assigned_to_company')
                ->leftJoin('master_shiftgroup_header as msh', 'msh.id_shiftgroup', '=', 'he.id_shift_group');

        if($full){
            $q1->select('he.id_employee', 'he.id_user', 'he.id_company', 'mc.company_code', 'mc.company_code', 'mc.company_type', 'mc.description as company', 'he.name', 'he.gender', 'he.marital', 'he.status', 'he.mobile_phone', 'he.private_mail', 'he.join_date', 'he.expired_date', 'he.permanent_date', 'he.resign_date', 'he.resign_date', 'he.image_attachment', 'mgd.description as employment_status', 'mc2.company_code as assigned_to_company', 'md.id_dept as id_department', 'md.department_code', 'md.description as department', 'mpr.id_routing', 'mpr.description as position_routing', 'mpd.id_position_detail', 'mpd.description as position_detail', 'mpd.parent_id_position_detail', 'mr.id_region', 'mr.region_code', 'mr.description as region', 'mb.id_branch', 'mb.branch_code', 'mb.description as branch', 'ml.id_location', 'ml.location_code', 'ml.description as location', 'msh.id_shiftgroup', 'msh.shiftgroup_code', 'msh.description as shift_group', 'msh.automatic_absence', 'msh.allow_checkout_nextdays','he.lock_gps_location', 'mgd2.code as timezone_code', 'mgd2.description as timezone', 'mjg.id_job_grade', 'mjg.description as job_grade', 'mjg.job_class_group', 'mjs.description as job_status', 'mp.description as principal', 'he2.id_employee as direct_id_employee', 'he2.name as direct_employee_name', 'he3.id_employee as indirect_id_employee', 'he3.name as indirect_employee_name', );
        } else {
            $q1->select('he.id_employee', 'he.id_user', 'he.id_company', 'mc.company_code', 'mc.company_code', 'mc.company_type', 'mc.description as company', 'he.name', 'he.gender', 'he.marital', 'he.status', 'he.mobile_phone', 'he.private_mail', 'he.join_date', 'he.expired_date', 'he.permanent_date', 'he.resign_date', 'he.resign_date', 'he.image_attachment', 'mgd.description as employment_status', 'mc2.company_code as assigned_to_company', 'md.id_dept as id_department', 'md.department_code', 'md.description as department', 'mpr.id_routing', 'mpr.description as position_routing', 'mpd.id_position_detail', 'mpd.description as position_detail', 'mpd.parent_id_position_detail', 'mr.id_region', 'mr.region_code', 'mr.description as region', 'mb.id_branch', 'mb.branch_code', 'mb.description as branch', 'ml.id_location', 'ml.location_code', 'ml.description as location', 'msh.id_shiftgroup', 'msh.shiftgroup_code', 'msh.description as shift_group', 'msh.automatic_absence', 'msh.allow_checkout_nextdays', 'he.lock_gps_location');
        }

        if($isActive){
            $q1->where('mpd.status', 'A');
        }
        if($parentIdPositionDetail){
            $q1->whereIn('mpd.id_position_detail', $idPositionDetailByParent);
        }
        if($idPositionDetail){
            $q1->whereIn('mpd.id_position_detail', $idPositionDetail);
        }
        $result = $q1->get();

        if($result->count() > 0){
            foreach ($result as $key => $val) {
                if(@$result[$key]->image_attachment != null){
                    if(strlen(@$result[$key]->image_attachment) > 1000){
                        @$result[$key]->image_attachment = "data:image;base64,".@$result[$key]->image_attachment;
                    } else {
                        $urlPhoto = 'public/upload/photo/'. @$result[$key]->image_attachment;
                        if (Storage::exists($urlPhoto)) {
                            @$result[$key]->image_attachment = url('project/storage/app/'.$urlPhoto);
                        } else {
                            @$result[$key]->image_attachment = asset('public/global/img/avatar.jpg');
                        }
                    }
                } else {
                    @$result[$key]->image_attachment = asset('public/global/img/avatar.jpg');
                }
            }
            $return = $result;
        }
        return $return;
    }

	public static function get_ptkp($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT * FROM hr_ptkp_history hph
				WHERE id_employee = ? AND id_company = ? 
				ORDER BY date_change_ptkp DESC";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }
	public static function get_ptkp_edit($id_employee) {
        $sql = "SELECT he.id_employee, he.ptkp_status, hph.date_change_ptkp  
				FROM  hr_employee he
				LEFT JOIN hr_ptkp_history hph
				ON he.id_employee = hph.id_employee
				WHERE he.id_employee = ? AND he.id_company = ?
				ORDER BY hph.date_change_ptkp DESC
				LIMIT 1";
        $result = DB::select($sql,[$id_employee,session('id_company')])[0];
        return $result;
    }
	public static function get_ptkp_report($cat_date,$startdate,$enddate,$group_branch) {
	//	dd($cat_date);
	if($group_branch == null || $group_branch == ""){
		$where_branch = "";
	}	
	else{
		$where_branch = "WHERE se.id_branch in(".$group_branch.")";
	}
		$sql = "SELECT se.id_employee, se.employee_name, se.nik_employee,
				CASE 
					WHEN he.gender = 'M' THEN 'Male'
					WHEN he.gender = 'F' THEN 'Female'
				END AS gender, he.npwp_number, se.join_date, 
				se.resign_date, se.position_routing, se.branch, hph3.ptkp_status, hph3.date_change_ptkp, hph3.creation_date,
				se.id_branch, se.status  
				FROM hr_ptkp_history hph3
				JOIN (
					SELECT max(hph.id_ptkp_history) AS id_ptkp_history, hph.id_employee
						FROM hr_ptkp_history hph
					WHERE hph.id_company = ".session('id_company')."
					GROUP BY hph.id_employee
				) AS j_hph3
				ON hph3.id_ptkp_history = j_hph3.id_ptkp_history
				LEFT JOIN sp_funct_get_employee (".session('id_company').") se
				ON hph3.id_employee = se.id_employee 
				LEFT JOIN hr_employee he
				ON hph3.id_employee = he.id_employee "
				.$where_branch;
        $data = DB::select($sql);
		$res = collect($data);
		if($cat_date == 'creation_date'){
			$end = date('Y-m-d',strtotime("+1 day", strtotime($enddate)));
			$result = $res->where($cat_date, '>=', $startdate)                                 
							->where($cat_date, '<=', $end);
		}
		else{
			$result = $res->where($cat_date, '>=', $startdate)                                 
							->where($cat_date, '<=', $enddate);
		}
	//	dd($result);
        return $result;
    }

	public static function get_data_verification($idSurvey, $idSurveyHistory) {
		$dataVerification = [];
		$getConfig = DB::table('hr_config_survey_update as hcsu')
            ->where('hcsu.id_survey_header', $idSurvey)
            ->where('hcsu.id_survey_history', $idSurveyHistory)
			->whereDate('hcsu.expired_date', '>=', date('Y-m-d'))
            ->get();

        if($getConfig->count() > 0){
			$dataVerification = DB::table('hr_survey_answer_user_header as hsauh')
	            ->leftJoin('hr_employee as he', 'hsauh.id_employee', '=', 'he.id_employee')
	            ->leftJoin('hr_survey_header as hsh', 'hsh.id_survey_header', '=', 'hsauh.id_survey_header')
	            ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_history', '=', 'hsauh.id_survey_history')
	            ->selectRaw('he.name, he.nik_employee, hsh.description as survey_name, hsauh.id_survey_header, hsauh.is_processed, hsauh.id_survey_history, hsauh.id_employee, hshi.start_date, hshi.end_date, '.$getConfig->count().' as data_count')
	            ->where('hsauh.id_survey_header', $idSurvey)
	            ->where('hsauh.id_survey_history', $idSurveyHistory)
	            ->get();
        }
        return $dataVerification;
   	}

   	public static function get_detail_verification($idSurvey, $idSurveyHistory, $idEmployee) {
		$dataVerification = [];
		$surveyPathStorage = 'public/upload/survey/';
		$getConfig = DB::table('hr_config_survey_update as hcsu')
			->join('hr_survey_question as hsq', 'hsq.id_survey_question', '=', 'hcsu.id_survey_question')
			->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsq.id_question_type')
			->leftJoin('hr_survey_answer_user_header as hsauh', 'hsauh.id_survey_header', '=', 'hsq.id_survey_header')
			->leftJoin('hr_employee as he', 'hsauh.id_employee', '=', 'he.id_employee')
			->leftJoin('hr_survey_answer_user as hsau', function ($join) {
                $join->on('hsau.id_survey_answer_user_header', '=', 'hsauh.id_survey_answer_user_header');
                $join->on('hsau.id_survey_question', '=', 'hcsu.id_survey_question');
            })
			->leftJoin('hr_survey_answer as hsa', 'hsa.id_survey_answer', '=', 'hsau.id_survey_answer')
			->leftJoin('master_survey_answer as msa', 'msa.id_answer', '=', 'hsa.id_answer')
			->select('hcsu.*', 'hsq.sequence', 'hsq.question', 'mgd.code as question_type', 'msa.code as answer_code', 'hsau.description_answer as answer_essay', 'hsau.attachment as answer_attachment', 'hsauh.id_employee', 'he.nik_employee')
            ->where('hcsu.id_survey_header', $idSurvey)
            ->where('hcsu.id_survey_history', $idSurveyHistory)
            ->where('hsauh.id_employee', $idEmployee)
			->whereDate('hcsu.expired_date', '>=', date('Y-m-d'))
            ->orderBy('id_config_survey_update')
            ->get();

        if($getConfig->count() > 0){
    		$uniqueColumn = '';
    		$uniqueValue = '';

        	foreach ($getConfig as $k => $val) {
        		if($val->unique_key){
        			$uniqueValue = @$val->answer_code;
        			$uniqueColumn = @$val->update_to_column;
        		}
        		$getConfig[$k]->answer_attachment_path = null;
        		$getConfig[$k]->answer_attachment_extension = null;
        		$getConfig[$k]->data_existing = 'Not Found';

        		if(@$val->question_type=='Single_Answer' || @$val->question_type=='Multiple_Answer'){
        			if(in_array(@$val->action_type, ['INSERT OR UPDATE', 'UPDATE', 'VIEW'])){
						$getThisQuery = DB::table(@$val->update_to_table)
							// ->select(@$val->update_to_column)
							->where(@$val->update_to_column, @$val->answer_code)
							->where('id_employee', @$val->id_employee)
							->first();
						if($getThisQuery){
	        				$getConfig[$k]->data_existing = 'Already Exist';
						}
	        		}
                } else if(@$val->question_type=='Essay'){
                	if(in_array(@$val->action_type, ['INSERT OR UPDATE', 'UPDATE', 'VIEW'])){
                		$thisAnswer = strip_tags(html_entity_decode(@$val->answer_code, ENT_QUOTES));
						$thisQ = DB::table(@$val->update_to_table)
							->select(@$val->update_to_column)
							->where('id_employee', @$val->id_employee);

						if($uniqueColumn!='' && $uniqueValue!=''){
							if (Schema::hasColumn($val->update_to_table, $uniqueColumn)) {
				            	$thisQ->where($uniqueColumn, $uniqueValue);
					        }
						}
						$getThisQuery = $thisQ->first();
							
						if($getThisQuery){
							$thisSelectedColumn = $val->update_to_column;
	        				$getConfig[$k]->data_existing = @$getThisQuery->$thisSelectedColumn ?? 'Not Found';
						}
	        		}
                } else if(@$val->question_type=='Upload_Files'){
					if($uniqueColumn!='' && $uniqueValue!=''){
                		$thisQ = DB::table(@$val->update_to_table)
							->select(@$val->update_to_column)
							->where('id_employee', @$val->id_employee);

						if($uniqueColumn!='' && $uniqueValue!=''){
							if (Schema::hasColumn($val->update_to_table, $uniqueColumn)) {
				            	$thisQ->where($uniqueColumn, $uniqueValue);
					        }
						}
						$getThisQuery = $thisQ->first();
						
						if($getThisQuery){
							if(strpos(@$val->update_to_path, '{nik_employee}') !== false){
	        					$getConfig[$k]->update_to_path = str_replace('{nik_employee}', @$val->nik_employee, @$val->update_to_path);
								$updatePath =  $getConfig[$k]->update_to_path;
							} else {
								$updatePath =  $getConfig[$k]->update_to_path;
							}

							$selectedUpdateColumn = $val->update_to_column;
							if (Storage::exists($updatePath.@$getThisQuery->$selectedUpdateColumn)) {
								$infoPath = Storage::mimeType($updatePath.@$getThisQuery->$selectedUpdateColumn);
								if($infoPath == 'application/pdf'){
									$getConfig[$k]->data_existing = '<a href="'.url('project/storage/app/'.$updatePath.@$getThisQuery->$selectedUpdateColumn).'" target="_blank">Download</a>';
								} else {
									$thisAttachmentPath = url('project/storage/app/'.$updatePath.@$getThisQuery->$selectedUpdateColumn);
									$getConfig[$k]->data_existing = '<a href="'.$thisAttachmentPath.'" data-toggle="lightbox"><img class="img-fluid" src="'.$thisAttachmentPath.'" style="max-width:150px; object-fit: cover;"></a>';
								}
							}
	        			}
					}
                }

        		if(!is_null(@$val->answer_attachment)){
        			if (Storage::exists($surveyPathStorage.@$val->answer_attachment)) {
						$infoPath = Storage::mimeType($surveyPathStorage.@$val->answer_attachment);
						if($infoPath == 'application/pdf'){
							$getConfig[$k]->answer_attachment_path = 'project/storage/app/'.$surveyPathStorage.@$val->answer_attachment;
        					$getConfig[$k]->answer_attachment_extension = 'pdf';
						} else {
							$getConfig[$k]->answer_attachment_path = url('project/storage/app/'.$surveyPathStorage.@$val->answer_attachment);
        					$getConfig[$k]->answer_attachment_extension = 'jpg';
						}
					}
        		}
        	}
        }

        return $getConfig;
   	}
	
	public static function get_editProfile($data) {
		$result = [];
		$id_employee = $data['id_employee'];
        $sql_1 = "SELECT sftp.*, he.image_attachment, date_part('year',age(current_date,sftp.birthdate)) AS age,
		CONCAT(date_part('year',age(current_date,sftp.join_date)),' Year(s) ', date_part('month',age(current_date,sftp.join_date)),' Month(s) ',
		date_part('days',age(current_date,sftp.join_date)),' Day(s)')::text as duration, edu.edu_level, talent.box, talent.desc_box 
		FROM  public.sp_funct_get_employee_report_all(".session('id_company').") sftp
		JOIN hr_employee he
		ON sftp.id_employee = he.id_employee
		LEFT JOIN (
			SELECT hee.id_employee, mgd.code AS edu_level FROM hr_education_employee hee
			LEFT JOIN master_general_data mgd
			ON hee.id_education_level = mgd.id_general_data AND mgd.id_company = ".session('id_company')."
			WHERE hee.id_employee = ".$id_employee." AND hee.id_company = ".session('id_company')."
			ORDER BY mgd.sequence DESC 
			LIMIT 1	
		) AS edu
		ON sftp.id_employee = edu.id_employee
		LEFT JOIN (
			SELECT htrd.id_talent_recommendation_detail, htrd.id_employee, mtm.name AS box, 
			mgd.description AS desc_box, htrd.kpi_desc
			FROM hr_talent_recommendation_detail htrd
			JOIN master_talent_matrix mtm
			ON htrd.id_job_grade = mtm.id_job_grade AND htrd.id_grade_promotion = mtm.id_grade_promotion
			AND (htrd.kpi_average >= mtm.kpi_value_min AND htrd.kpi_average <= mtm.kpi_value_max) 
			AND htrd.id_conclusion_psychotest = mtm.id_conclusion_psychotest AND htrd.id_conclusion_assessment = mtm.id_conclusion_assessment
			LEFT JOIN master_general_data mgd
			ON mtm.id_group_matrix = mgd.id_general_data
			WHERE htrd.id_employee = ".$id_employee." AND htrd.id_company = ".session('id_company')."
		UNION
			SELECT htrd.id_talent_recommendation_detail, htrd.id_employee, mtm.name AS box, 
			mgd.description AS desc_box, htrs.kpi_desc
			FROM hr_talent_recommendation_summary htrs
			LEFT JOIN hr_talent_recommendation_detail htrd
			ON htrs.id_talent_recommendation_detail = htrd.id_talent_recommendation_detail
			LEFT JOIN master_talent_matrix mtm
			ON htrd.id_job_grade = mtm.id_job_grade AND htrd.id_grade_promotion = mtm.id_grade_promotion
			AND (htrs.kpi_average >= mtm.kpi_value_min AND htrs.kpi_average <= mtm.kpi_value_max) 
			AND mtm.id_conclusion_psychotest = coalesce(htrs.id_conclusion_psychotest,htrd.id_conclusion_psychotest) 
			AND mtm.id_conclusion_assessment = coalesce(htrs.id_conclusion_assessment,htrd.id_conclusion_assessment)
			LEFT JOIN master_general_data mgd
			ON mtm.id_group_matrix = mgd.id_general_data
			WHERE htrd.id_employee = ".$id_employee." AND htrd.id_company = ".session('id_company')."
			ORDER BY kpi_desc DESC
			LIMIT 1
		) AS talent
		ON sftp.id_employee = talent.id_employee
		WHERE sftp.id_employee = ".$id_employee."
		LIMIT 1";
        $profile_emp = DB::select($sql_1);
		$sql_2 = "SELECT * FROM public.sp_funct_talent_profile_career_data(".session('id_company').", ".$id_employee.")";
        $career_emp = DB::select($sql_2);
		$result['profile'] = [];
		$result['career'] = [];
		if(count($profile_emp) > 0){
			$result['profile'] = $profile_emp[0];			
		}
        return $result;
    }
	
	public static function get_cert_profile($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT hce.certification_name, hce.certified_by 
				FROM hr_certification_employee hce
				WHERE hce.id_employee = ? AND hce.id_company = ?";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }	
	
	public static function get_skill_profile($data) {
		$id_employee = $data['id_employee'];
        $sql = "SELECT hse.skill_name, 
				CASE 
					WHEN hse.skill_level = '1' THEN '1 (Basic)'
					WHEN hse.skill_level = '2' THEN '2 (Advanced Beginner)'
					WHEN hse.skill_level = '3' THEN '3 (Intermediate)'
					WHEN hse.skill_level = '4' THEN '4 (Proficient)'
					WHEN hse.skill_level = '5' THEN '5 (Expert)'
				END AS skill_level
				FROM hr_skill_employee hse
				WHERE hse.id_employee = ? AND hse.id_company = ?";
        $result = DB::select($sql,[$id_employee,session('id_company')]);
	//	dd($result);
        return $result;
    }	
}
