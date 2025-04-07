<?php

namespace App\Models\Employee\Contract;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contract extends Model {

    protected $table = 'hr_contract_employee';
    protected $primaryKey = 'id_contract';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_contract', 'id_employee', 'contract_number', 'reference_number', 'id_contract_category', 'id_salary_structure', 'effective_date', 'expired_date', 'notice_period', 'id_working_schedule', 'schedule_payroll', 'attachment_type', 'attachment', 'id_company', 'is_upload', 'created_by', 'updated_by'
    ];
	
	public static function getkode_ctr(){			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', session('id_company'))->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-CNTR-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%CNTR-{$monthyear}%")->max('reference_number'), 15, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%CNTR-{$monthyear}%")->max('reference_number'), 16, 21);
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
	
    public static function getdata($group_branch) {
		if($group_branch == null){
			$where_branch = "";
		}
		else{
			$where_branch = "AND se.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT hce.id_contract, hce.contract_number, hce.effective_date, hce.expired_date, hce.attachment_type, hce.is_upload, se.nik_employee, se.employee_name, 
				msh.description as working_time, mgd.description as category, se.id_branch, se.status
					FROM hr_contract_employee hce
					JOIN sp_funct_get_employee (".session('id_company').") se
					ON hce.id_employee = se.id_employee
					JOIN master_shiftgroup_header msh
					ON hce.id_working_schedule = msh.id_shiftgroup
					JOIN master_general_data mgd
					ON hce.id_contract_category = mgd.id_general_data
					--LEFT JOIN master_position_detail mpd
					--ON (hce.id_employee = mpd.id_employee OR hce.id_employee = mpd.id_employee2)
					WHERE hce.id_company = ".session('id_company')." ".$where_branch."
					ORDER BY hce.id_contract DESC";
        $result = DB::select($sql);
        return $result;
    }
	public static function get_employee() {
        $sql = "SELECT 
					he2.id_employee id,
					CONCAT(name,' (',he2.nik_employee,')') as text,
					he2.id_employee,
					he2.id_employment_status,
					he2.id_shift_group
				FROM  hr_employee he2
				JOIN master_position_detail mpd
				ON (he2.id_employee = mpd.id_employee OR he2.id_employee = mpd.id_employee2)				
				WHERE he2.id_employee NOT IN(
				SELECT he.id_employee
					FROM  hr_employee he
					JOIN hr_contract_employee hce
					ON he.id_employee = hce.id_employee 
					WHERE he.status = 'A' AND he.id_company = ".session('id_company')."
				) AND he2.status = 'A' AND mpd.secondary_position = false AND he2.id_company = ".session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_employee_edit() {
        $sql = "SELECT 
                        id_employee id,
                        CONCAT(name,' (',nik_employee,')') as text,
                        id_employee,
						id_employment_status,
						id_shift_group
                FROM  hr_employee where id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
		
	public static function get_position($data) {
        $id_company = session()->get('id_company');
        $id_employee = $data['id_employee'];
		$sql = "SELECT  mpd.id_employee, md.id_dept, md.description AS department,
                  mpd.id_position_detail, mpd.description AS position_detail,
                  mpd.parent_id_position_detail, mpd2.description AS parent_position_detail,
                  mpd2.id_employee AS parent_id_employee, he.name AS parent_emp_name,
                  mpr.id_routing, mpr.description AS position_routing,
                  mjg.id_job_grade, mjg.description AS job_grade,
                  mjs.id_job_status, mjs.description AS job_status,
                  mjp.id_position, mjp.description AS job_position,
                  ml.id_location, ml.description AS work_location,
                  mb.id_branch, mb.description AS branch,
                  mr.id_region, mr.description AS regional,
                  mpd.assigned_to_company,
                  mpd.id_company
          FROM  master_position_detail mpd
          JOIN  master_position_routing mpr
            ON  mpd.id_position_routing = mpr.id_routing
           AND  mpd.id_company = mpr.id_company
           AND  mpr.status = 'A'
          JOIN  master_job_grade mjg
            ON  mpr.id_job_grade = mjg.id_job_grade
           AND  mpr.id_company = mjg.id_company
          JOIN  master_job_status mjs
            ON  mpr.id_job_status = mjs.id_job_status
           AND  mpr.id_company = mjs.id_company
          LEFT JOIN  master_position_detail mpd2
            ON  mpd.parent_id_position_detail = mpd2.id_position_detail
           AND  mpd.id_company = mpd2.id_company
           AND  mpd2.status = 'A'
          LEFT JOIN  hr_employee he
            ON  (mpd2.id_employee = he.id_employee
                OR mpd2.id_employee2 = he.id_employee)
           AND  (mpd2.id_company = he.id_company
                OR mpd2.assigned_to_company = he.id_company)
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
          WHERE mpd.status = 'A' 
            AND (mpd.id_company = COALESCE(".$id_company.",mpd.id_company)
                OR mpd.assigned_to_company = COALESCE(".$id_company.",mpd.assigned_to_company))
			AND (mpd.id_employee = COALESCE(".$id_employee.",mpd.id_employee) OR mpd.id_employee2 = COALESCE(".$id_employee.",mpd.id_employee2))";
       /* $sql = "SELECT 
                        *                   
                FROM   sp_funct_employee_position_view(?,?) order by id_position_detail DESC";
		*/		
    //    $result = DB::select($sql, [$id_employee, $id_company]);
        $result = DB::select($sql);

        return $result;
    }
	/*
	public static function get_position_detail($data) {
        $id_company = session()->get('id_company');
        $id_employee = $data['id_employee'];
        $sql = "SELECT 
                        id_employee id,
                        description text,
                        id_position_routing
                FROM  master_position_detail 
				WHERE status = 'A' 
				AND id_employee = ?
				AND id_company = ?";
        $result = DB::select($sql, [$id_employee, $id_company]);

        return $result;
    }
	*/
    public static function get_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }

/*	public static function get_shift_group() {
        $sql = "SELECT 
                        id_shiftgroup id,
                        description text
                FROM  master_shiftgroup_header where status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
*/	
	public static function get_shift_group($data) {
		$id_shift_group = $data['id_shift_group'];
        $sql = "SELECT 
                        id_shiftgroup id,
                        description text
                FROM  master_shiftgroup_header where status = 'A' and id_shiftgroup = ? and id_company =" . session('id_company');
        $result = DB::select($sql,[$id_shift_group]);
        return $result;
    }
	
	public static function get_contract_category($data) {
		$id_employment_status = $data['id_employment_status'];
        $sql = "SELECT 
                        id_general_data id,
                        description text
                FROM  master_general_data where status = 'A' and id_general_data = ? and id_company =" . session('id_company');
        $result = DB::select($sql,[$id_employment_status]);

        return $result;
    }
	public static function submit_approve() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Approved' and mgd.status = 'A' AND mgd.id_company =".session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }

    public static function getAttachment($nik, $id_contract) {
        $get = DB::table('hr_contract_employee as hce')
                    ->join('hr_employee as he', 'he.id_employee', '=', 'hce.id_employee', 'left')
                    ->join('master_shiftgroup_header as msh', 'hce.id_working_schedule', '=', 'msh.id_shiftgroup', 'left')
                    ->join('master_general_data as mgd', 'hce.id_contract_category', '=', 'mgd.id_general_data', 'left')
                    ->select('hce.id_contract', 'hce.contract_number', 'he.nik_employee', 'he.name', 'hce.contract_number', 'hce.effective_date', 'hce.expired_date', 'hce.attachment_type', 'hce.attachment', 'he.status', 'msh.description as working_time', 'mgd.description as category')
                    ->where('hce.id_company', session('id_company'));
        if(count($nik) > 0){
        	$get->whereIn('he.nik_employee', $nik)->orWhereIn('hce.id_contract', $id_contract);
        }
        if(count($id_contract) > 0){
        	$get->whereIn('hce.id_contract', $id_contract)->orWhereIn('he.nik_employee', $nik);
        }
        return $get->get();
    }

    public static function getEmployeeContract($idEmployee=[]) {
/*        $get = DB::table('hr_contract_employee as hce')
                ->join('hr_employee as he', 'he.id_employee', '=', 'hce.id_employee', 'left')
                ->select('he.id_employee', 'he.nik_employee', 'he.name')
                ->where('hce.id_company', session('id_company'));
        if(count($idEmployee) > 0){
            $get->whereIn('hce.id_employee', $idEmployee);
        }
		dd($get->get());
        return $get->get();
	*/	
		
		 $sql = "SELECT he.id_employee, he.nik_employee, he.employee_name as name
			FROM
				hr_contract_employee as hce
			JOIN sp_funct_get_employee (".session('id_company').") as he 
			ON he.id_employee = hce.id_employee";
        $res = DB::select($sql);
		$result = collect($res);
		if(count($idEmployee) > 0){
            $result->whereIn('hce.id_employee', $idEmployee);
        }
        return $result;	
    }

    public static function getContractNumber($idEmployee=[]) {
        $get = DB::table('hr_contract_employee as hce')
                ->select('hce.id_contract', 'hce.contract_number')
                ->where('hce.id_company', session('id_company'));
        if(count($idEmployee) > 0){
            $get->whereIn('hce.id_employee', $idEmployee);
        }
        return $get->get();
    }
	
	public static function get_emp_status($idEmployee) {
        $sql = "SELECT mgd.code 
				 FROM hr_employee he
				 JOIN master_general_data mgd
				 ON he.id_employment_status = mgd.id_general_data
				 WHERE he.id_employee = ".$idEmployee." AND he.id_company = " . session('id_company');
        $result = DB::select($sql)[0];

        return $result;
    }
	
	public static function get_api_contract($codeContract,$idContract=null) {
		if($idContract != null){
			$queryContract = " AND hel.id_letter = ".$idContract;
		}
		else{
			$queryContract = "";
		}
        $sql = "SELECT hel.id_letter AS id, hel.date AS tgl_surat, hel.reference_number AS no_surat,
				CASE 
					WHEN hel.id_employee IS NOT NULL THEN he.name
					ELSE hel.remark_1
				END AS name, ml.description AS area, hel.notes AS keterangan
				FROM hr_electronic_letter hel
				LEFT JOIN hr_employee he
				ON hel.id_employee = he.id_employee
				LEFT JOIN master_location ml
				ON hel.id_location = ml.id_location
				JOIN master_general_data mgd
				ON hel.id_letter_type = mgd.id_general_data AND mgd.code IN('PKK','PKHL')
				JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_letter_type_group'
				LEFT JOIN master_general_data mgd2
				ON hel.id_category = mgd2.id_general_data
				LEFT JOIN master_general_type mgt2
				ON mgd2.id_general_type = mgt2.id_general_type AND mgt.general_type = 'master_letter_category'
				WHERE mgd.id_company = ".session('id_company')." AND mgd2.code = '".$codeContract."' ".$queryContract."
				ORDER BY he.name ASC";
        $result = DB::select($sql);

        return $result;
    }
}
