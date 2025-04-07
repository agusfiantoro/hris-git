<?php

namespace App\Models\CareerAdministration\MasterBoarding;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeeChecklist extends Model {

    protected $table = 'hr_checklist_employee';
    protected $primaryKey = 'id_checklist_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_checklist_employee', 'reference_number', 'id_checklist', 'id_employee', 'completed', 'effective_date', 'remark', 'attachment_type', 'attachment', 'status', 'compensation_amount', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getkode_ofb(){			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', session('id_company'))->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-OFB-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%OFB-{$monthyear}%")->max('reference_number'), 15, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%OFB-{$monthyear}%")->max('reference_number'), 16, 21);
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
		if($group_branch == null || $group_branch == ""){
				$where_branch = "";
			}	
			else{
				$where_branch = "se.id_branch in(".$group_branch.") AND ";
			}
			$sql = "SELECT hce.id_checklist_employee, hce.completed, hce.effective_date, hce.status,
					se.status AS status_emp, mce.document_name, se.nik_employee, 
					STRING_AGG(he.name,', ') AS assigned_hr, se.employee_name, se.id_branch
						FROM hr_checklist_employee hce
					LEFT JOIN master_checklist_employee mce
					ON hce.id_checklist = mce.id_checklist AND mce.id_company = ".session('id_company')."
					LEFT JOIN sp_funct_get_employee (".session('id_company').") se
					ON hce.id_employee = se.id_employee
					LEFT JOIN relation_checklist_employee rce
					ON hce.id_checklist_employee = rce.id_checklist_employee
					LEFT JOIN master_position_detail mpd
					ON rce.id_position_detail_assigned = mpd.id_position_detail
					LEFT JOIN hr_employee he
					ON mpd.id_employee = he.id_employee
					WHERE ".$where_branch." mce.checklist_type = 'Offboarding' AND (hce.created_by = COALESCE(".session('id_user').",hce.created_by) 
					OR (mce.document_name = 'Exit Clearance'
						OR rce.id_checklist_employee IN(						
								SELECT rce2.id_checklist_employee 
									FROM relation_checklist_employee rce2 
								LEFT JOIN master_position_detail mpd
								ON rce2.id_position_detail_assigned = mpd.id_position_detail
								LEFT JOIN hr_employee he
								ON mpd.id_employee = he.id_employee
								WHERE he.id_user = COALESCE(".session('id_user').",he.id_user)																	
							)
						)
					)
					GROUP BY hce.id_checklist_employee, hce.completed, hce.effective_date, hce.remark, hce.attachment, hce.status,  
					se.status, mce.document_name, se.nik_employee, se.employee_name, se.id_branch 
					ORDER BY hce.id_checklist_employee DESC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_type() {
        $sql = "SELECT mce.id_checklist id, mce.document_name text
				FROM master_checklist_employee mce
				WHERE mce.checklist_type = 'Offboarding' AND mce.id_company = ".session('id_company')."
				ORDER BY mce.document_name";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_employee($type) {
		if($type == 'new'){
			$new = " AND he.id_employee NOT IN (
					SELECT hce.id_employee 
					FROM hr_checklist_employee hce
					LEFT JOIN master_checklist_employee mce
					ON hce.id_checklist = mce.id_checklist AND mce.id_company = ".session('id_company')."
					WHERE mce.checklist_type = 'Offboarding' AND mce.document_name = 'Exit Clearance'
				)";
		}
		else{
			$new = "";
		}
        $sql = "SELECT he.id_employee id,
				CASE 
					WHEN he.resign_date IS NOT NULL THEN CONCAT(he.name,' (',he.nik_employee ,'), Resign Date: ',he.resign_date)
					ELSE CONCAT(he.name,' (',he.nik_employee ,'), Resign Date: -')
				END AS text
				FROM hr_employee he
				WHERE he.id_company = ".session('id_company')." 
				ORDER BY he.name ASC, he.id_employee DESC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_hr() {
        $sql = "SELECT mpd.id_position_detail id, CONCAT(mpr.description,' (',he.name,')')AS text 
				FROM master_position_detail mpd 
				LEFT JOIN hr_employee he
				ON mpd.id_employee = he.id_employee 
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				WHERE he.status = 'A'";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_detail_offboarding($data) {
        $sql = "SELECT *
                    FROM hr_checklist_employee hce
                    WHERE hce.id_checklist_employee = ?";
        $result = (Array)  DB::select($sql, [$data['id_checklist_employee']])[0];
		
		$sql_relation = "SELECT * FROM relation_checklist_employee WHERE id_checklist_employee  = ?";
        $result_sql_relation = DB::select($sql_relation, [$data['id_checklist_employee']]);
        $collect_assigned_company = collect($result_sql_relation);
        $group_assigned_hr = $collect_assigned_company->groupBy('id_position_detail_assigned')->toArray();
        $result['assigned_hr'] = [];
        foreach (array_keys($group_assigned_hr) as $key => $value) {
            $result['assigned_hr'][] = $value;
        }
        return $result;
    }
	
	public static function get_report($cat_date,$startdate,$enddate,$group_branch) {
		if($group_branch == null || $group_branch == ""){
				$where_branch = "";
			}	
			else{
				$where_branch = "se.id_branch in(".$group_branch.") AND ";
			}
			$sql = "SELECT hce.id_checklist_employee, hce.completed, hce.effective_date, hce.remark, hce.attachment, hce.status,
					se.status AS status_emp, mce.document_name, se.nik_employee, he2.expired_date, se.resign_date, 
					STRING_AGG(he.name,', ') AS assigned_hr, se.employee_name, se.position_routing, se.id_branch, se.branch, 
					hce.creation_date, hce.compensation_amount, he2.terminate_reason
						FROM hr_checklist_employee hce
					LEFT JOIN master_checklist_employee mce
					ON hce.id_checklist = mce.id_checklist
					JOIN sp_funct_get_employee (?) se
					ON hce.id_employee = se.id_employee
					LEFT JOIN relation_checklist_employee rce
					ON hce.id_checklist_employee = rce.id_checklist_employee
					LEFT JOIN master_position_detail mpd
					ON rce.id_position_detail_assigned = mpd.id_position_detail
					LEFT JOIN hr_employee he
					ON mpd.id_employee = he.id_employee
					LEFT JOIN hr_employee he2
					ON se.id_employee = he2.id_employee
					WHERE ".$where_branch." mce.checklist_type = 'Offboarding' AND (hce.created_by = COALESCE(?,hce.created_by) 
					OR (mce.document_name = 'Exit Clearance'
						OR rce.id_checklist_employee IN(						
								SELECT rce2.id_checklist_employee 
									FROM relation_checklist_employee rce2 
								LEFT JOIN master_position_detail mpd
								ON rce2.id_position_detail_assigned = mpd.id_position_detail
								LEFT JOIN hr_employee he
								ON mpd.id_employee = he.id_employee
								WHERE he.id_user = COALESCE(?,he.id_user)																	
							)
						)
					)
					GROUP BY hce.id_checklist_employee, hce.completed, hce.effective_date, hce.remark, hce.attachment, hce.status,  
					se.status, mce.document_name, se.nik_employee, se.employee_name, se.position_routing, se.id_branch, he2.expired_date, 
					se.resign_date, se.branch, hce.compensation_amount, he2.terminate_reason";
        $data = DB::select($sql, [session('id_company'),session('id_user'),session('id_user')]);
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
	
	public static function check_clearence($idEmployee=null) {
        $get = DB::table('hr_checklist_employee')
        	->where('id_employee', $idEmployee)
        	->where('status', 'A')
			->orderBy('id_checklist_employee','DESC')
        	->first();
        return $get;
    }
}
