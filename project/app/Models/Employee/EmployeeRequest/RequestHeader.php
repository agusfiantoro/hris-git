<?php

namespace App\Models\Employee\EmployeeRequest;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequestHeader extends Model {

    protected $table = 'hr_request_header';
    protected $primaryKey = 'id_request_header';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_request_header', 'reference_number', 'id_employee_request', 'start_date', 'end_date', 'id_request_type', 'id_leave_type', 'id_overtime_type', 'attachment_type', 'attachment', 'delegate_approval', 'note', 'enable_approval', 'id_approval', 'id_approval_status', 'enable_cancel', 'reference_number_cancel', 'executed', 'status', 'id_company', 'note_rejected', 'note_revised', 'created_by', 'updated_by'
    ];
	
	public static function getkode($idCompany=null){
		$idCompany = $idCompany ?? session('id_company');
		
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', $idCompany)->first();
		$char_com = strlen($com->company_code);	
		$nomor = $com->company_code.'-REQ-'.$monthyear.'-';
		if($char_com == 2){	
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%REQ-{$monthyear}%")->max('reference_number'), 14, 20);
		}
		else if($char_com == 3){
			$noUrutAkhir = (int) substr(self::where('id_company', $idCompany)->where('reference_number', 'LIKE', "%REQ-{$monthyear}%")->max('reference_number'), 15, 21);
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
       $sql = "SELECT DISTINCT hrh.*, he.name as employee_name,
					mgd.description as desc_request_type, 
					mgd.code as code_request_type, 
					mgd2.description as desc_app_status, 
					mgd2.code as code_app_status, mlt.leave_code, hlbe.status AS bal_status
				from hr_request_header as hrh 
				join hr_employee as he 
				on hrh.id_employee_request = he.id_employee and he.status = 'A'
				join master_general_data as mgd 
				on hrh.id_request_type = mgd.id_general_data 
				left join master_general_data as mgd2 
				on hrh.id_approval_status = mgd2.id_general_data 
				LEFT JOIN hr_request_detail hrd
				ON hrh.id_request_header = hrd.id_request_header
				LEFT JOIN master_leave_type mlt
				ON hrh.id_leave_type = mlt.id_leave_type
				LEFT JOIN hr_leave_balance_emp hlbe
				ON hrd.id_employee = hlbe.id_employee AND hrh.id_leave_type = hlbe.id_leave_type AND hrd.request_start_to BETWEEN hlbe.effective_date AND hlbe.expired_date
				where hrh.id_company = ? and he.id_user = ?
				order by id_request_header desc";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
	//	dd($result);
        return $result;
    }
	public static function getdatareport($group_branch, $startTo, $endTo, $dateType, $nik=null) {
	//	$endTo = date('Y-m-d', strtotime($endTo."+1 days")); //end-to harus ditambahi 1 hari agr sesuai tgl filternya, default dr laravel
		$endTo = date('Y-m-d', strtotime($endTo));

		if(in_array($dateType, ['request_start_to', 'request_end_to'])){
			$dateType = 'hrd.'.$dateType.'::date';
		} else {
			$dateType = 'hrh.'.$dateType.'::date';
		}

		if($nik){
			$getIdEmployee = DB::table('hr_employee as he')->select("he.id_employee")->whereIn('he.nik_employee', $nik);
            $allIdEmployee = $getIdEmployee->get()->pluck('id_employee')->all();
			$whereEmployee = 'and he.id_employee IN ('.implode(',',$allIdEmployee).')';
		} else {
			$whereEmployee = '';
		}
		if($group_branch == null){
          $sql = "select hrh.attachment_type, hrh.created_by, hrh.creation_date, hrh.delegate_approval, hrh.enable_approval, hrh.enable_cancel, hrh.end_date, hrh.executed, hrh.id_approval, hrh.id_approval_status, hrh.id_company, hrh.id_employee_request, hrh.id_leave_type, hrh.id_overtime_type, hrh.id_request_header, hrh.id_request_type, hrh.note, hrh.note_rejected, hrh.note_revised, hrh.reference_number, hrh.reference_number_cancel,
			CASE 
				WHEN number_cancel.reference_number IS NULL THEN ''
				ELSE CONCAT(number_cancel.reference_number,' ','(Approved)')
			END AS request_cancel,
			hrh.start_date, hrh.status, hrh.update_date, hrh.updated_by,
					he.nik_employee,
					he.name as employee_name,
					mgd.description as desc_request_type, 
					mlt.description as desc_leave_type,
					mot.description as desc_overtime_type,
					hrd.request_start_to,
					hrd.request_end_to,
					hrd.actual_start_to,
					hrd.actual_end_to,
					hrd.day_type,
					hrd.qty_days,
					mgd2.description as desc_app_status, 
					mgd2.code as code_app_status,
					he.status as employee_status
				from hr_request_header hrh 
				left join hr_request_detail hrd
				on hrh.id_request_header = hrd.id_request_header
				join hr_employee he 
				on hrd.id_employee = he.id_employee
				join master_general_data mgd 
				on hrh.id_request_type = mgd.id_general_data 
				left join master_general_data mgd2 
				on hrh.id_approval_status = mgd2.id_general_data 
				left join master_leave_type mlt
				on hrh.id_leave_type = mlt.id_leave_type
				left join master_overtime_type mot
				on hrh.id_overtime_type = mot.id_overtime_type
				left join (							
					select hrh2.reference_number, hrh2.reference_number_cancel 
					from hr_request_header hrh2
					join master_general_data mgd3
					on hrh2.id_approval_status = mgd3.id_general_data
					where mgd3.code = 'Approved' and hrh2.reference_number_cancel is not null					
				) as number_cancel
				on hrh.reference_number = number_cancel.reference_number_cancel
				where hrh.id_company = ? and (".$dateType." between '$startTo' and '$endTo') ".$whereEmployee."
				order by id_request_header desc";
		}
		else{
			$sql = "select hrh.attachment_type, hrh.created_by, hrh.creation_date, hrh.delegate_approval, hrh.enable_approval, hrh.enable_cancel, hrh.end_date, hrh.executed, hrh.id_approval, hrh.id_approval_status, hrh.id_company, hrh.id_employee_request, hrh.id_leave_type, hrh.id_overtime_type, hrh.id_request_header, hrh.id_request_type, hrh.note, hrh.note_rejected, hrh.note_revised, hrh.reference_number, hrh.reference_number_cancel,
					CASE 
						WHEN number_cancel.reference_number IS NULL THEN ''
						ELSE CONCAT(number_cancel.reference_number,' ','(Approved)')
					END AS request_cancel,
					he.nik_employee,
					he.employee_name,
					mgd.description as desc_request_type, 
					mlt.description as desc_leave_type,
					mot.description as desc_overtime_type,
					hrd.request_start_to,
					hrd.request_end_to,
					hrd.actual_start_to,
					hrd.actual_end_to,
					hrd.day_type,
					hrd.qty_days,
					mgd2.description as desc_app_status, 
					mgd2.code as code_app_status,
					he.status as employee_status
				from hr_request_header hrh 
				left join hr_request_detail hrd
				on hrh.id_request_header = hrd.id_request_header
				JOIN sp_funct_get_employee (".session('id_company').") he
				on hrd.id_employee = he.id_employee
				join master_general_data mgd 
				on hrh.id_request_type = mgd.id_general_data 
				left join master_general_data mgd2 
				on hrh.id_approval_status = mgd2.id_general_data 
				left join master_leave_type mlt
				on hrh.id_leave_type = mlt.id_leave_type
				left join master_overtime_type mot
				on hrh.id_overtime_type = mot.id_overtime_type
				left join (							
					select hrh2.reference_number, hrh2.reference_number_cancel 
					from hr_request_header hrh2
					join master_general_data mgd3
					on hrh2.id_approval_status = mgd3.id_general_data
					where mgd3.code = 'Approved' and hrh2.reference_number_cancel is not null					
				) as number_cancel
				on hrh.reference_number = number_cancel.reference_number_cancel
				where hrh.id_company = ? and he.id_branch in(".$group_branch.") and (".$dateType." between '$startTo' and '$endTo') ".$whereEmployee."
				order by id_request_header desc";
		}

        $result = DB::select($sql,[session('id_company')]);

        return $result;
    }

	/*
	public static function getdata_approval_status($data) {
		$id_request_header = $data['id_request_header'];
		$id_approval = $data['id_approval'];
	//	dd($id_request_header."-".$id_approval);
        $data = DB::table('hr_approval_transaction')
                ->join('hr_approval_detail', 'hr_approval_detail.id_approval_detail', '=', 'hr_approval_transaction.id_approval_detail')
                ->join('hr_employee', 'hr_employee.id_employee', '=', 'hr_approval_detail.id_employee')
                ->join('master_general_data', 'master_general_data.id_general_data', '=', 'hr_approval_transaction.id_approval_status')
                ->select('hr_approval_transaction.sequence',  'hr_employee.name', 'master_general_data.code')
                ->where('hr_approval_transaction.id_company', session('id_company'))
                ->where('hr_approval_transaction.id_source_transaction', $id_request_header)
                ->where('hr_approval_transaction.id_approval', $id_approval)
                ->get();
        return $data;
    }
	*/
	
	
	public static function get_employee() {
        $sql = "SELECT 
                        id_employee id,
                        CONCAT(name,' (',nik_employee,')') as text,
                        id_employee
                FROM  hr_employee where status = 'A' and id_company =" . session('id_company')." and id_user = ".session('id_user');
        $result = DB::select($sql);

        return $result;
    }
	public static function get_employee_by() {
        $sql = "SELECT 
                        id_employee id,
                        name text,
                        id_employee
                FROM  hr_employee where status = 'A' and id_user = ".session('id_user')." and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	public static function get_employee_delegate() {
        $sql = "SELECT 
                        id_employee id,
						CONCAT(name,' (',nik_employee,')') as text,
                        id_employee
                FROM  hr_employee where status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);

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
	
	public static function get_request_type() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						id_general_data,
						code
                FROM  master_general_data where code not in('Overtime_Request') and status = 'A' 
				and id_general_type = 10 and id_company =" . session('id_company').
				"ORDER BY sequence ASC";
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_request_type_param($type) {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						id_general_data,
						code
                FROM  master_general_data where id_general_data = ".$type." and code not in('Overtime_Request') and status = 'A' 
				and id_general_type = 10 and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_leave_type($data) {
		$id_employee = $data['id_employee'];
		/*
        $sql = "SELECT 
                        id_leave_type id,
                        description text
                FROM  master_leave_type where status = 'A' and id_company =" . session('id_company');
		*/
		$sql ="SELECT 
					mlt.id_leave_type id,
					mlt.description text,
					mlt.leave_code,
					hlb.leave_quota,
					CURRENT_DATE,
					hlb.effective_date,
					hlb.expired_date
				FROM hr_leave_balance_emp hlb
				LEFT JOIN master_leave_type mlt
				ON hlb.id_leave_type = mlt.id_leave_type
				WHERE hlb.status = 'A' AND hlb.id_employee = ?  AND hlb.id_company = ?
				AND mlt.leave_code != 'EDO'
				AND CURRENT_DATE >= hlb.effective_date";
        $result = collect(DB::select($sql,[$id_employee,session('id_company')]));

        $sql_edo ="SELECT 
					mlt.id_leave_type id,
					mlt.description text,
					mlt.leave_code,
					hlb.leave_quota,
					CURRENT_DATE,
					hlb.effective_date,
					hlb.expired_date
				FROM hr_leave_balance_emp hlb
				LEFT JOIN master_leave_type mlt
				ON hlb.id_leave_type = mlt.id_leave_type
				WHERE hlb.status = 'A' AND hlb.id_employee = ?  AND hlb.id_company = ?
				AND mlt.leave_code = 'EDO'
				AND hlb.leave_quota - coalesce(hlb.used_leave,0) > 0 
				AND hlb.effective_date = (SELECT MIN(hlb.effective_date) FROM hr_leave_balance_emp hlb LEFT JOIN master_leave_type mlt ON hlb.id_leave_type = mlt.id_leave_type WHERE hlb.status = 'A' AND hlb.id_employee = ? AND hlb.id_company = ? AND mlt.leave_code = 'EDO' AND hlb.leave_quota - coalesce(hlb.used_leave,0) > 0 )";
        $result_edo = collect(DB::select($sql_edo,[$id_employee,session('id_company'),$id_employee,session('id_company')]));

        $merged = collect($result->merge($result_edo)->sortBy('id')->values()->all());
        return $merged;
    }
	
	public static function get_leave_type_param($id_leave) {		
        $sql = "SELECT 
                        id_leave_type id,
                        description text
                FROM  master_leave_type where id_leave_type = ".$id_leave." and status = 'A' and id_company =" . session('id_company');		
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_leave_mat($id_request) {		
        $sql = "SELECT mlt.leave_code, hrh.id_request_header  FROM hr_request_header hrh
			LEFT JOIN master_leave_type mlt
			ON hrh.id_leave_type = mlt.id_leave_type
			WHERE hrh.id_request_header = ".$id_request." AND hrh.id_company = " . session('id_company');		
        $result = DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
	
	public static function get_leave_attachment($id_leave_type) {
        $sql = "SELECT 
                        id_leave_type id,
                        description text,
						leave_code, req_attachment
                FROM  master_leave_type 
				WHERE id_leave_type = ? 
				AND status = 'A' AND id_company = ?";		
        $result = DB::select($sql,[$id_leave_type,session('id_company')]);
	//	dd($result);
        return $result;
    }
	
	public static function get_overtime_type() {
        $sql = "SELECT 
                        id_overtime_type id,
                        description text
                FROM  master_overtime_type where status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	/*
	public static function get_user_req() {
		$sql = "SELECT 
						he.id_employee,
						he.id_user,
						he.name as name_employee,
						mpd.id_location
				FROM master_position_detail mpd
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				WHERE he.id_user = ".session('id_user')." AND mpd.status = 'A' AND mpd.id_company = ".session('id_company')."
				GROUP BY he.id_employee, he.name, mpd.id_location";
        $result = DB::select($sql);
        return $result;
	}
	public static function get_hierachy($data,$req_location) {
		$code = $data['code'];
		$sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval_doc_type,
						had.id_position_detail, 
						mpd.id_employee as emp_approval,
						he.name as name_employee,
						mpd.id_location,
						STRING_AGG(rpw.id_location::text,', ') AS assigned_location
                FROM  hr_approval_header hah
				JOIN hr_approval_detail had
				ON hah.id_approval = had.id_approval
				JOIN master_position_detail mpd
				ON had.id_position_detail = mpd.id_position_detail
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				LEFT JOIN relation_positiondetail_workarround rpw
				ON mpd.id_position_detail = rpw.id_position_detail
				JOIN master_general_data mgd
				ON hah.id_approval_doc_type = mgd.id_general_data
				WHERE mgd.code='".$code."' AND hah.hierarchy_type = 'Combine' 
				AND hah.status = 'A' AND hah.id_company = ".session('id_company')."
				AND rpw.id_location IN(".$req_location.")
				GROUP BY  hah.id_approval, hah.description, hah.id_approval_doc_type, had.id_position_detail, 
				mpd.id_employee, he.name, mpd.id_location";
        $result = DB::select($sql);
        return $result;
    }
	*/
	public static function get_approval_status() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
						FROM  master_general_data where status = 'A' and id_general_type = 7 and id_company =" . session('id_company') . " ORDER BY sequence ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_cancel_status() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
						FROM  master_general_data where code = 'Cancel_Leave' and status = 'A' and id_general_type = 10 and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_emp_leave($data) {
		$id = $data['id'];
   /*
	$sql =	"select he.id_employee id, CONCAT(he.name,' (',he.nik_employee,')') as text, he.id_user, sflbv.*
				from  sp_funct_leave_balance_view(null,".session('id_company').",".$id.") sflbv
				LEFT join hr_employee he
				on sflbv.id_employee = he.id_employee
				where he.id_user = ".session('id_user');	
	*/
		$leaveType = DB::table('master_leave_type')->where([
	    	['id_leave_type', '=', $id],
	    	['id_company', '=', session('id_company')],
		])->first();

		$sql	= "SELECT  he.id_employee id, CONCAT(he.name,' (',he.nik_employee,')') as text, he.id_user, 
					hlbe.id_leave_balance_emp, hlbe.id_employee, hlbe.id_leave_type,
					hlbe.leave_quota - hlbe.used_leave as leave_quota,
	                  hlbe.used_leave, hlbe.note, hlbe.effective_date, hlbe.expired_date, hlbe.status,
	                  mlt.leave_code, mlt.description, mlt.restrict_by
	          FROM  hr_leave_balance_emp hlbe
	          JOIN  master_leave_type mlt
	            ON  hlbe.id_leave_type = mlt.id_leave_type
			  JOIN hr_employee he
				ON hlbe.id_employee = he.id_employee AND he.status = 'A'
	          WHERE hlbe.id_leave_type = ".$id."
	            AND hlbe.id_company = ".session('id_company')."
	            AND hlbe.status = 'A'
	            AND mlt.enable_minus_leave = FALSE
	            AND CURRENT_DATE >= hlbe.effective_date
				AND he.id_user = ".session('id_user');

		if($leaveType->leave_code == 'EDO'){
			$employee = DB::table('hr_employee as he')->select('he.*')->where([
				['he.id_company', '=', session('id_company')],
				['he.status', '=', 'A'],
				['he.id_user', '=', session('id_user')],
			])->first();

			$sql .= " AND hlbe.effective_date = (SELECT MIN(hlb.effective_date) FROM hr_leave_balance_emp hlb LEFT JOIN master_leave_type mlt ON hlb.id_leave_type = mlt.id_leave_type WHERE hlb.status = 'A' AND hlb.id_employee = ".$employee->id_employee." AND hlb.id_company = ".session('id_company')." AND mlt.leave_code = 'EDO' AND hlb.leave_quota - coalesce(hlb.used_leave,0) > 0 )";
		}

        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
/*	public static function get_approval($data) {
		$id_approval = $data;
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						hah.hierarchy_type,
						mgd.code,
						(select id_general_data from master_general_data where code = 'New' and id_company =".session('id_company').") as id_approval_status
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				WHERE hah.id_approval ='".$id_approval."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_approval_edit($data) {
		$id_approval = $data;
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						hah.hierarchy_type,
						mgd.code
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				WHERE hah.id_approval ='".$id_approval."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
*/	
	public static function get_request_edit($data) {
        $result = [];
        $sql = "SELECT  mgd.code, mgd2.code as code_status, mgd.description AS leave_name, mlt.leave_code, mlt.description, hrh.*,
                    hrd.id_request_detail, hrd.id_employee, hrd.request_start_to, hrd.request_end_to,
                    hrd.note AS employee_notes, hrd.id_employee_delegation, hrd.qty_days
              FROM  hr_request_header hrh
              JOIN  hr_request_detail hrd
                ON  hrh.id_request_header = hrd.id_request_header
               AND  hrh.id_company = hrd.id_company
               JOIN  master_general_data mgd
                ON  hrh.id_request_type = mgd.id_general_data
			JOIN  master_general_data mgd2
                ON  hrh.id_approval_status = mgd2.id_general_data
            LEFT JOIN  master_leave_type mlt
                ON  hrh.id_leave_type = mlt.id_leave_type
            LEFT JOIN  master_overtime_type mot
                ON  hrh.id_overtime_type = mot.id_overtime_type
             WHERE  hrh.id_request_header =?";
        $result = (array) DB::select($sql, [$data['id_request_header']])[0];
		
        $sql = "SELECT * FROM hr_request_detail WHERE id_request_header  = ?";
        $result_menu = DB::select($sql, [$data['id_request_header']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_request_detail')->toArray();

        $result['emprequest'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['emprequest'][] = [
                'id_request_detail' => $group_menu[$value][0]->id_request_detail,
                'id_employee' => $group_menu[$value][0]->id_employee,
                'request_start_to' => $group_menu[$value][0]->request_start_to,
                'request_end_to' => $group_menu[$value][0]->request_end_to,
                'day_type' => $group_menu[$value][0]->day_type,
                'qty_days' => $group_menu[$value][0]->qty_days,
                'actual_start_to' => $group_menu[$value][0]->actual_start_to,
                'actual_end_to' => $group_menu[$value][0]->actual_end_to,
                'id_employee_delegation' => $group_menu[$value][0]->id_employee_delegation,              
            ];
        }
    //    dd($result);
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
		$id_request_header = $data['id_request_header'];
		$id_approval = $data['id_approval'];
	//	dd($id_request_header."-".$id_approval);
        $sql = "select  hat.*, he.name, mgd.code
				from    hr_approval_transaction hat
				left join    hr_employee he
				on      hat.id_employee_approval = he.id_employee
				join    master_general_data mgd
				on      hat.id_approval_status = mgd.id_general_data
				where   hat.id_company = ?
				and     hat.id_source_transaction = ?
				and     hat.id_approval = coalesce(?,hat.id_approval)";
        $result = DB::select($sql,[session('id_company'),$id_request_header,$id_approval]);

        return $result;
    }
	
	public static function getdata_approval_mail($data) {
		$id_request_header = $data['id_request_header'];
		$id_approval = $data['id_approval'];
	//	dd($id_request_header."-".$id_approval);
		
        $sql = "select  case when (sfepv.parent_id_employee in (select hat2.id_employee_approval from hr_approval_transaction hat2 where hat2.id_source_transaction = ? and hat2.id_approval = coalesce(?, hat2.id_approval) )) then hat.\"sequence\"::numeric else concat(hat.\"sequence\" ::text, '.2')::numeric end as new_seq, hrh.id_request_header, hrh.reference_number, hrh.id_employee_request, hrh.id_request_type, hrh.id_leave_type, hrh.note, hrh.creation_date as creation_date_request, hrd.id_employee, hrd.request_start_to, hrd.request_end_to, hrd.qty_days, hrd.day_type, hat.*, he.name, he.private_mail, mgd.description as code
		from    hr_approval_transaction hat
		join hr_request_header hrh
		on hat.id_source_transaction = hrh.id_request_header and hat.id_company = hrh.id_company
		join hr_request_detail hrd
		on hrh.id_request_header = hrd.id_request_header and hrh.id_company = hrd.id_company
		left join    hr_employee he
		on      hat.id_employee_approval = he.id_employee
		join    master_general_data mgd
		on      hat.id_approval_status = mgd.id_general_data and hat.id_company = mgd.id_company
		join sp_funct_employee_position_view (null,null) sfepv
		on (he.id_employee = sfepv.id_employee) and he.id_company = sfepv.id_company
		where   hat.id_company = ?
		and     hat.id_source_transaction = ?
		and     hat.id_approval = coalesce(?,hat.id_approval)
		order by new_seq asc";

        $result = DB::select($sql,[$id_request_header,$id_approval,session('id_company'),$id_request_header,$id_approval]);

        return $result;
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Cancel' and mgd.status = 'A' and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function get_workdays($data) {
		$id_employee = $data['id_employee'];
		$code = $data['code'];
		if($code == 'Attendance_Correction'){
        $sql = "SELECT        
				MIN(current_dates) as min_date, MAX(current_date) as max_date
			FROM            
				hr_work_days
			WHERE id_employee = ?";
		}
		else{
			$sql = "SELECT        
				MIN(current_dates) as min_date, MAX(current_dates) as max_date
			FROM            
				hr_work_days
			WHERE id_employee = ?";		
		}
        $result = DB::select($sql,[$id_employee]);
	//	dd($result);
        return $result;
    }
	public static function get_count_days($data) {
		$id_employee = $data['id_employee'];
		$min_date = $data['min_date'];
		$max_date = $data['max_date'];
        $sql = "SELECT * FROM hr_work_days
			WHERE id_employee = ? AND day_type = 'WD' AND current_dates BETWEEN ? AND ?";
        $result = DB::select($sql,[$id_employee,$min_date,$max_date]);
	//	dd($result);
        return $result;
    }
	public static function get_count_days_od($data) {
		$id_employee = $data['id_employee'];
		$min_date = $data['min_date'];
		$max_date = $data['max_date'];
		$sql = "SELECT id_workdays, current_dates, actual_time_out, actual_time_in,
				 CASE
					WHEN (extract(epoch  FROM (actual_time_out - actual_time_in))/3600) > 4 THEN 1
				 	WHEN (extract(epoch  FROM (actual_time_out - actual_time_in))/3600) <= 4 AND (extract(epoch  FROM (actual_time_out - actual_time_in))/3600) > 0 THEN 0.5
					ELSE NULL
				 END AS qty_days
				 FROM hr_work_days
							WHERE id_employee = ?  AND day_type = 'OD' AND current_dates BETWEEN ? AND ?
							AND (actual_time_in IS NOT NULL OR actual_time_out IS NOT NULL)";
    /*    $sql = "SELECT * FROM hr_work_days
			WHERE id_employee = ? AND day_type = 'OD' AND current_dates BETWEEN ? AND ?";
	*/
        $result = DB::select($sql,[$id_employee,$min_date,$max_date]);
        return $result;
    }
	public static function get_actual_time($data) {
		$id_employee = $data['id_employee'];
		$current_dates = $data['current_dates'];
        $sql = "SELECT actual_time_in, actual_time_out 
				FROM hr_work_days
				WHERE id_employee = ? AND current_dates  = ?";
        $result = DB::select($sql,[$id_employee,$current_dates]);
	//	dd($result);
        return $result;
    }
	
	public static function get_mail_employee($data) {
        $sql = "SELECT 
                       id_employee, name, private_mail
                FROM  hr_employee 
				WHERE id_employee in(".$data.") AND status = 'A'";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	/*
	public static function submit_approve($data) {
        $id_request_header = $data;
        $sql = "select hrh.id_approval_status,mgd.code
				from hr_request_header hrh
				join master_general_data mgd
				on hrh.id_approval_status = mgd.id_general_data
				where hrh.id_request_header = ". $id_request_header." and mgd.code = 'new' and hrh.status = 'A' and hrh.id_company =". session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	*/

	public static function checkAvailableLeave($leave_code=null, $id_employee=null, $except_id_request=null, $idCompany=null, $idUser=null) {
		$idCompany = $idCompany ?? session('id_company');
		$idUser = $idUser ?? session('id_user');

		$date = date('Y-m-d');
		$leaveCode = $leave_code ?? 'ANL'; //jika tanpa parameter leave_code maka default ke annual leave
		$employee = DB::table('hr_employee')->select('id_employee')
                    ->where('id_company', $idCompany)->where('status', 'A');
        if($id_employee){
            $employee->where('id_employee', $id_employee);
        } else {
            $employee->where('id_user', $idUser);
        }
        $getEmployee = $employee->first();

        $leaveType = DB::table('master_leave_type')->where([
        	['leave_code', '=', $leaveCode],
        	['status', '=', 'A'],
        	['id_company', '=', $idCompany],
    	])->first();

        $except = ['Approved', 'Rejected', 'Cancel'];
        $generalData = DB::table('master_general_data as mgd')
        		->leftJoin('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
        		->whereIn('mgd.code', $except)
        		->where([
		        	['mgt.general_type', '=', 'master_approval_status'],
		        	['mgd.id_company', '=', $idCompany],
		    	])->get()->pluck('id_general_data')->all();

    	$leaveBalance = DB::table('hr_leave_balance_emp')->where([
        	['id_leave_type', '=', @$leaveType->id_leave_type],
        	['status', '=', 'A'],
        	['id_employee', '=', @$getEmployee->id_employee],
        	['id_company', '=', $idCompany],
    	]);
    	$leaveBalance->whereRaw('leave_quota > used_leave');
    	if($leaveCode == 'ANL'){
    		$leaveBalance->whereRaw('? between effective_date and expired_date', [date('Y-m-d')]);
    	}
    	$balance = $leaveBalance->first();

    	$leaveRequest = DB::table('hr_request_header as hrh')
                ->leftJoin('hr_request_detail as hrd', 'hrh.id_request_header', '=', 'hrd.id_request_header')
                ->select('hrd.id_request_header', 'hrd.qty_days')
                ->where('hrh.id_leave_type', @$leaveType->id_leave_type)
                ->whereNotIn('hrh.id_approval_status', @$generalData)
                ->where('hrh.id_employee_request', @$getEmployee->id_employee);

        if($leaveCode == 'ANL'){
        	//jika anuual leave maka cari request header yg create date nya antara eff_Date dan end_date
    		$leaveRequest->whereBetween('hrh.creation_date', [@$balance->effective_date, @$balance->expired_date]);
    	} 
        if($except_id_request){
        	//utk pengecekan proses edit request, menghitung yg tanpa id_request tsb
        	$leaveRequest->where('hrd.id_request_header', '!=', $except_id_request);
        }
        $requestHeader = $leaveRequest->get();

        $usedLeave = $requestHeader->pluck('qty_days')->all();
        $countUsedLeave = 0;
        if(count($usedLeave) > 0){
        	$countUsedLeave = array_sum($usedLeave);
        }
        $remaining = (float)@$balance->leave_quota - (float)@$balance->used_leave;
        $available = $remaining - (float)$countUsedLeave;
        return $available;
    }

    public static function checkDateRequest($start=null, $end=null, $id_employee=null, $except_id_request=null, $idCompany=null, $idUser=null) {
		$idCompany = $idCompany ?? session('id_company');
		$idUser = $idUser ?? session('id_user');
		$date = date('Y-m-d');
		$employee = DB::table('hr_employee')->select('id_employee')
                    ->where('id_company', $idCompany)->where('status', 'A');
        if($id_employee){
            $employee->where('id_employee', $id_employee);
        } else {
            $employee->where('id_user', $idUser);
        }
        $getEmployee = $employee->first();

        $except = ['Rejected', 'Cancel'];
        $generalData = DB::table('master_general_data as mgd')
            ->leftJoin('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
            ->whereIn('mgd.code', $except)
            ->where([
                ['mgt.general_type', '=', 'master_approval_status'],
                ['mgd.id_company', '=', $idCompany],
            ])->get()->pluck('id_general_data')->all();

        $generalDataApprove = DB::table('master_general_data as mgd')
            ->leftJoin('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
            ->where('mgd.code', 'Approved')
            ->where('mgd.id_company', $idCompany)
            ->first();

        $exceptRequestType = ['Cancel_Leave'];
        $requestType = DB::table('master_general_data as mgd')
            ->leftJoin('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
            ->whereIn('mgd.code', $exceptRequestType)
            ->where([
                ['mgt.general_type', '=', 'master_request_type'],
                ['mgd.id_company', '=', $idCompany],
            ])->get()->pluck('id_general_data')->all();

        $thisReq = DB::table('hr_request_header as hrh')
            ->leftJoin('hr_request_detail as hrd', 'hrh.id_request_header', '=', 'hrd.id_request_header')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hrh.id_approval_status')
            ->leftJoin('master_general_data as mgd2', 'mgd2.id_general_data', '=', 'hrh.id_request_type')
            ->select('hrd.id_request_header', 'hrd.request_start_to', 'hrd.request_end_to', 'mgd.code as status_approval', 'mgd2.code as status_request')
            // ->where('hrh.id_leave_type', @$leaveType->id_leave_type)
            ->where('hrh.id_employee_request', @$getEmployee->id_employee)
            ->where('mgd2.code', '!=', 'Attendance_Correction')
            ->where(function ($q) use ($generalData, $generalDataApprove, $requestType, $start, $end){
            	$q->where(function ($q2) use ($generalData, $requestType){
            		$q2->whereNotIn('hrh.id_approval_status', @$generalData);
                	$q2->whereNotIn('hrh.id_request_type', @$requestType);
            	});
                $q->where(function ($q2) use ($generalData, $start, $end){
                	$q2->whereNotIn('hrh.id_approval_status', @$generalData);
                    $q2->where('hrh.enable_cancel', '!=', true);
                    $q2->whereNull('hrh.reference_number_cancel');
                });
                $q->orWhere(function ($q2) use ($generalDataApprove, $requestType){
                	$q2->where('hrh.id_approval_status', '!=', @$generalDataApprove->id_general_data);
                    $q2->where('hrh.enable_cancel', true);
                    $q2->whereNotNull('hrh.reference_number_cancel');
                	$q2->whereIn('hrh.id_request_type', @$requestType);
                });
            });

        if($start || $end){
            $thisReq->where(function ($q) use ($start, $end){
                $q->where(function ($query) use ($start, $end){
                    $query->whereDate('hrd.request_start_to', '=', $start);
                    $query->orWhereDate('hrd.request_start_to', '=', $end);
                });
                $q->orWhere(function ($query) use ($start, $end){
                    $query->whereDate('hrd.request_end_to', '=', $start);
                    $query->orWhereDate('hrd.request_end_to', '=', $end);
                });
            });
        }
        if($except_id_request){
            //utk pengecekan proses edit request, menghitung yg tanpa id_request tsb
            $thisReq->where('hrd.id_request_header', '!=', $except_id_request);
        }
        $requestHeader = $thisReq->get();
        $res_start = [];
        $res_end = [];
        $dateUsed = [];

        if($requestHeader && $requestHeader->count() > 0){
            if($requestHeader->count() > 1){
                $status_request = $requestHeader->pluck('status_request')->unique();
                $status_approval = $requestHeader->pluck('status_approval')->unique();
                //jika terdapat 2 record di tgl start yg sama maka cek status kedua record tsb
                if(count($status_request) > 0 && count($status_approval) > 0){
                    //jika status approval tidak sama berarti yg 1 ad yg blm di approve, shg msuk kondisi pencegahan
                    foreach ($requestHeader as $key => $val) {
                        if(!is_null($val->request_start_to)){
                            $res_start[] = Carbon::parse($val->request_start_to)->format('Y-m-d');
                        }
                        if(!is_null($val->request_end_to)){
                            $res_end[] = Carbon::parse($val->request_end_to)->format('Y-m-d');
                        }
                    }
                    //kalo status approvalnya sama maka artinya yg dicancel suda diapprove shg bs dgunakan lagi tgl tsb utk dilakukan request kembali.
                }
            } else {
                foreach ($requestHeader as $key => $val) {
                    if(!is_null($val->request_start_to)){
                        $res_start[] = Carbon::parse($val->request_start_to)->format('Y-m-d');
                    }
                    if(!is_null($val->request_end_to)){
                        $res_end[] = Carbon::parse($val->request_end_to)->format('Y-m-d');
                    }
                }
            }
        }
        if(in_array($start, $res_start) || in_array($start, $res_end)){
            $dateUsed[] = Carbon::parse($start)->format('d F Y');
        }
        if(in_array($end, $res_start) || in_array($end, $res_end)){
            $dateUsed[] = Carbon::parse($end)->format('d F Y');
        }
        $merge = collect($dateUsed)->unique()->toArray();
        return $merge;
    }

    public static function checkLeaveQuotaByDate($idLeaveType, $start=null, $end=null, $id_employee=null, $idCompany=null, $idUser=null, $except_id_request=null) {
		$idCompany = $idCompany ?? session('id_company');
		$idUser = $idUser ?? session('id_user');
		$date = date('Y-m-d');
		$employee = DB::table('hr_employee')->select('id_employee')
                    ->where('id_company', $idCompany)->where('status', 'A');
        if($id_employee){
            $employee->where('id_employee', $id_employee);
        } else {
            $employee->where('id_user', $idUser);
        }
        $getEmployee = $employee->first();

        $except = ['Rejected', 'Cancel'];
        $generalData = DB::table('master_general_data as mgd')
        		->leftJoin('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
        		->whereIn('mgd.code', $except)
        		->where([
		        	['mgt.general_type', '=', 'master_approval_status'],
		        	['mgd.id_company', '=', $idCompany],
		    	])->get()->pluck('id_general_data')->all();


        $leaveTypeExceptANL_EDO = DB::table('master_leave_type')->where([
        	['status', '=', 'A'],
        	['id_company', '=', $idCompany],
    	])->whereNotIn('leave_code', ['ANL', 'EDO'])->get()->pluck('id_leave_type')->all();

        $whereExpired = true;
        if(in_array($idLeaveType, $leaveTypeExceptANL_EDO)){
        	$whereExpired = false;
        }

       	$getBalance = DB::table('hr_leave_balance_emp as hlb')
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('hlb.id_leave_type', '=', 'mlt.id_leave_type');
            })
            ->select('mlt.id_leave_type as id_leave_type', 'hlb.leave_quota','hlb.effective_date', 'hlb.expired_date', 'hlb.status')
            ->where('hlb.id_company', $idCompany)
            ->where('hlb.id_employee', $id_employee)
            ->where('hlb.id_leave_type', $idLeaveType)
            ->where('hlb.effective_date', '<=', $start);
        if($whereExpired){
            $getBalance->where('hlb.expired_date', '>=', $end);
        }

        $resultBalance = $getBalance->get();
        $resultBalance = $resultBalance->keyBy(function ($item) {
            return $item->id_leave_type;
        });
        $startBetween = @$resultBalance[$idLeaveType]->effective_date;
        $endBetween = $end ?? date('Y-m-d');
        $sumDays = @$resultBalance[$idLeaveType]->leave_quota;
        $balanceStatus = @$resultBalance[$idLeaveType]->status;

    	$thisReq = DB::table('hr_request_header as hrh')
                ->leftJoin('hr_request_detail as hrd', 'hrh.id_request_header', '=', 'hrd.id_request_header')
                ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hrh.id_approval_status')
                ->leftJoin('master_general_data as mgd2', 'mgd2.id_general_data', '=', 'hrh.id_request_type')
                ->selectRaw('SUM(hrd.qty_days) as days')
                ->where('hrh.id_leave_type', $idLeaveType)
                ->whereNotIn('hrh.id_approval_status', @$generalData)
                ->where('mgd2.code', '!=', 'Attendance_Correction')
                ->whereBetween('hrd.request_start_to', [$startBetween, $endBetween])
                ->where('hrh.id_employee_request', @$getEmployee->id_employee);

        if($except_id_request){
        	//utk pengecekan proses edit request, menghitung yg tanpa id_request tsb
        	$thisReq->where('hrd.id_request_header', '!=', $except_id_request);
        }
        $thisReq->groupBy('hrh.id_employee_request');
        $requestHeader = $thisReq->first();
        if($requestHeader){
        	if(!in_array($idLeaveType, $leaveTypeExceptANL_EDO)){ // jika ANL dan EDO
	        	$sumDays = (float)$sumDays - (float)$requestHeader->days;
	        	if($sumDays < 0){ $sumDays = 0; }
	        }
        }

        $return['effective_date'] = @$resultBalance[$idLeaveType]->effective_date;
        $return['expired_date'] = @$resultBalance[$idLeaveType]->expired_date;
        $return['remaining'] = $sumDays;
        $return['status'] = $balanceStatus;
        return $return;
    }
	
	public static function checkExpired($start=null, $end=null, $id_employee=null, $code) {
        $sql = "SELECT hlbe.*
				FROM hr_leave_balance_emp hlbe
				JOIN master_leave_type mlt
				ON hlbe.id_leave_type = mlt.id_leave_type
				WHERE hlbe.id_employee = ".$id_employee." AND mlt.leave_code = '".$code."' AND hlbe.id_company = " . session('id_company')."
				AND hlbe.status = 'A' AND (hlbe.expired_date >= '".$start."' AND hlbe.expired_date >= '".$end."')";
        $result = DB::select($sql);

        return $result;
    }
}
