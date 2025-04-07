<?php

namespace App\Models\Employee\EmployeeApproval;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeeApproval extends Model {

    protected $table = 'hr_approval_transaction';
    protected $primaryKey = 'id_approval_transaction';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = null;

    protected $fillable = [
		'id_approval_transaction', 'id_source_transaction', 'source_transaction_type', 'id_approval', 'id_approval_detail', 'id_approval_mode', 'id_position_detail', 'sequence', 'id_employee_approval', 'id_approval_status', 'id_company', 'note_rejected', 'note_revised',  'created_by', 'updated_by'
    ];
	
	public static function getdata($emp_approval,$req_type) {
		if($req_type == null){
			$type = "";
		}
		else{
			$type = "WHERE request_group_code = '".$req_type."'";
		}
		$id_user = session('id_user');
        $sql = "SELECT *, CONCAT(request_group,' (',REPLACE(request_group_code,'_',' '),')') AS group_detail FROM sp_funct_approval_view(?,?,null,null,null,?,null,null)
				".$type."
				ORDER BY id_approval_transaction DESC";
        $x = DB::select($sql, [$emp_approval,session('id_company'),$id_user]);
		$result = collect($x);
        return $result;
    }
	
	public static function getdata_history($emp_approval,$startdate,$enddate) {
		$id_user = session('id_user');
        $sql = "select * from sp_funct_approved_view(?,?,null,null,null,?,?,?)
				ORDER BY id_approval_transaction DESC";
        $x = DB::select($sql, [$emp_approval,session('id_company'),$id_user,$startdate,$enddate]);
		$result = collect($x);
        return $result;
    }
	
	public static function get_view_approval($data) {
		$id_user = session('id_user');
        $result = [];
        $sql = "SELECT 
				sfav.*, hea.content_letter, hct.effective_date, hea.start_date, hea.end_date, 
				hrd.id_employee, hrd.request_start_to, hrd.request_end_to, he.nik_employee,
				CASE 
					WHEN hrd.day_type = 'Full_Day' THEN 'Full Day'
					WHEN hrd.day_type = 'Half_Day1' THEN 'Half Day (Awal)'
					WHEN hrd.day_type = 'Half_Day2' THEN 'Half Day (Akhir)'
				END AS day_type,
			hhrh.effective_date AS fpk_effective_date, j_detail.count_req, hhrh.pkwt_duration, 
			hhrh.reason_notes, mgd.description AS travel_type, CONCAT(hot.location_to,' (',hot.reason_notes ,')') AS travel_note,
			mb.description AS branch, mr.description AS region, CONCAT(mgd2.description,' (',mgd3.description,')') AS note_reco,
			hrh2.effective_date AS eff_date_reco, hrh2.expired_date AS ex_date_reco, hrh2.reco_flag
			FROM sp_funct_approval_view(?,null,null,null,null,?,null,null) sfav
			LEFT JOIN hr_employee_anouncement hea
			ON sfav.id_source_transaction = hea.id_announcement 
			AND sfav.source_transaction_type = 'Announcement_Request'
			LEFT JOIN hr_career_transaction hct
			ON sfav.id_source_transaction = hct.id_career_transaction
			AND sfav.source_transaction_type = 'Career_Request'
			LEFT JOIN hr_request_header hrh
			ON sfav.id_source_transaction = hrh.id_request_header
			AND sfav.source_transaction_type IN ('Leave_Request','Overtime_Request','Attendance_Correction','Cancel_Leave','Change_Day_off')
			LEFT JOIN hr_request_detail hrd
			ON hrh.id_request_header = hrd.id_request_header
			LEFT JOIN hr_hiring_request_header hhrh
			ON sfav.id_source_transaction = hhrh.id_hiring_request_header
			AND sfav.source_transaction_type = 'FPK_Request'
			LEFT JOIN master_branch mb
			ON hhrh.id_branch = mb.id_branch
			LEFT JOIN master_region mr
			ON mb.id_region = mr.id_region
			LEFT JOIN public.hr_official_travel hot
			ON sfav.id_source_transaction = hot.id_official_travel
			AND sfav.source_transaction_type = 'Official_Travel'
			LEFT JOIN master_general_data mgd
			ON hot.id_reason_group = mgd.id_general_data
			LEFT JOIN (
					SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_detail hhrd 
					GROUP BY id_hiring_request_header
				) AS j_detail
			ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
			LEFT JOIN hr_recommendation_header hrh2
			ON sfav.id_source_transaction = hrh2.id_recommendation_header
			AND sfav.source_transaction_type = 'Form_Reco'
			LEFT JOIN master_general_data mgd2
			ON hrh2.id_transition_category = mgd2.id_general_data
			LEFT JOIN master_general_data mgd3
			ON hrh2.id_transition_type = mgd3.id_general_data
			LEFT JOIN hr_employee he
			ON he.id_employee = sfav.id_employee_request
			WHERE sfav.id_approval_transaction  = ?
			ORDER BY sfav.id_approval_transaction DESC";
        $result = (Array) DB::select($sql, [$data['id_employee'],$id_user,$data['id_approval_transaction']])[0];
       // 	dd($result);
        return $result;
    }
	
	public static function get_view_approval_history($data) {
		$id_user = session('id_user');
        $result = [];
        $sql = "SELECT 
				sfav.*, hea.content_letter, hct.effective_date, hea.start_date, hea.end_date, 
				hrd.id_employee, hrd.request_start_to, hrd.request_end_to, he.nik_employee,
				CASE 
					WHEN hrd.day_type = 'Full_Day' THEN 'Full Day'
					WHEN hrd.day_type = 'Half_Day1' THEN 'Half Day (Awal)'
					WHEN hrd.day_type = 'Half_Day2' THEN 'Half Day (Akhir)'
				END AS day_type,
				hhrh.effective_date AS fpk_effective_date, j_detail.count_req, hhrh.pkwt_duration, 
				hhrh.reason_notes, mgd.description AS travel_type, CONCAT(hot.location_to,' (',hot.reason_notes ,')') AS travel_note,
				mb.description AS branch, mr.description AS region, CONCAT(mgd2.description,' (',mgd3.description,')') AS note_reco,
			hrh2.effective_date AS eff_date_reco, hrh2.expired_date AS ex_date_reco, hrh2.reco_flag
			FROM sp_funct_approved_view(?,null,null,null,null,?,null,null) sfav
			LEFT JOIN hr_employee_anouncement hea
			ON sfav.id_source_transaction = hea.id_announcement 
			AND sfav.source_transaction_type = 'Announcement_Request'
			LEFT JOIN hr_career_transaction hct
			ON sfav.id_source_transaction = hct.id_career_transaction
			AND sfav.source_transaction_type = 'Career_Request'
			LEFT JOIN hr_request_header hrh
			ON sfav.id_source_transaction = hrh.id_request_header
			AND sfav.source_transaction_type IN ('Leave_Request','Overtime_Request','Attendance_Correction','Cancel_Leave','Change_Day_off')
			LEFT JOIN hr_request_detail hrd
			ON hrh.id_request_header = hrd.id_request_header
			LEFT JOIN hr_hiring_request_header hhrh
			ON sfav.id_source_transaction = hhrh.id_hiring_request_header
			AND sfav.source_transaction_type = 'FPK_Request'
			LEFT JOIN master_branch mb
			ON hhrh.id_branch = mb.id_branch
			LEFT JOIN master_region mr
			ON mb.id_region = mr.id_region
			LEFT JOIN public.hr_official_travel hot
			ON sfav.id_source_transaction = hot.id_official_travel
			AND sfav.source_transaction_type = 'Official_Travel'
			LEFT JOIN master_general_data mgd
			ON hot.id_reason_group = mgd.id_general_data
			LEFT JOIN (
					SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
					FROM hr_hiring_request_detail hhrd 
					GROUP BY id_hiring_request_header
				) AS j_detail
			ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
			LEFT JOIN hr_recommendation_header hrh2
			ON sfav.id_source_transaction = hrh2.id_recommendation_header
			AND sfav.source_transaction_type = 'Form_Reco'
			LEFT JOIN master_general_data mgd2
			ON hrh2.id_transition_category = mgd2.id_general_data
			LEFT JOIN master_general_data mgd3
			ON hrh2.id_transition_type = mgd3.id_general_data
			LEFT JOIN hr_employee he
			ON he.id_employee = sfav.id_employee_request
			WHERE sfav.id_approval_transaction  = ?
			ORDER BY sfav.id_approval_transaction DESC";
        $result = (Array) DB::select($sql, [$data['id_employee'],$id_user,$data['id_approval_transaction']])[0];
       // 	dd($result);
        return $result;
    }
	
	public static function getdata_approval_status($data) {
		$id_source_transaction = $data['id_source_transaction'];
		$source_transaction_type = $data['source_transaction_type'];
        $sql = "select  hat.*, he.name, mgd.description as code
				from    hr_approval_transaction hat
				left join    hr_employee he
				on      hat.id_employee_approval = he.id_employee
				join    master_general_data mgd
				on      hat.id_approval_status = mgd.id_general_data
				where   hat.id_source_transaction = ?
				and 	hat.source_transaction_type = ?";
        $result = DB::select($sql,[$id_source_transaction,$source_transaction_type]);

        return $result;
    }
		
	public static function get_trans($source_trans,$trans_type) {
        $sql = "select distinct hat.sequence, mgd.description as status_app
				from hr_approval_transaction hat
				join master_general_data mgd 
				on hat.id_approval_status = mgd.id_general_data
				where hat.id_source_transaction = ".$source_trans." and hat.source_transaction_type = '".$trans_type."' and mgd.code='Request_Approval'";
        $result = DB::select($sql);		
        return $result;
    }
	
	public static function general_approve($id,$id_company=null) {
		if($id_company){
			$company = "and mgd.id_company =".$id_company;
		}
		else{
			$company = "and mgd.id_company =".session('id_company');
		}
        $sql = "SELECT mgd.code
				  FROM master_general_data mgd
				  WHERE mgd.id_general_data = ".$id." and mgd.status = 'A' ".$company;
        $result = DB::select($sql)[0];		
        return $result;
    }
	public static function submit_approve($id_company=null) {
		if($id_company){
			$company = "and mgd.id_company =".$id_company;
		}
		else{
			$company = "and mgd.id_company =".session('id_company');
		}
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Approved' and mgd.status = 'A' ".$company;
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function partial_approve($id_company=null) {
		if($id_company){
			$company = "and mgd.id_company =".$id_company;
		}
		else{
			$company = "and mgd.id_company =".session('id_company');
		}
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Partial_Approved' and mgd.status = 'A' ".$company;
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function revised_approve($id_company=null) {
		if($id_company){
			$company = "and mgd.id_company =".$id_company;
		}
		else{
			$company = "and mgd.id_company =".session('id_company');
		}
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Revised' and mgd.status = 'A' ".$company;
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function rejected_approve($id_company=null) {
		if($id_company){
			$company = "and mgd.id_company =".$id_company;
		}
		else{
			$company = "and mgd.id_company =".session('id_company');
		}
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Rejected' and mgd.status = 'A' ".$company;
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function all_approve($company,$id) {
		$result = DB::select("select * from  compute_approval_status (?,?)",[$company,$id]);		
		return $result;
	}
	
	public static function get_mail_approval($data) {
        $sql = "SELECT hrh.reference_number, hrd.request_start_to, hrd.request_end_to, 
				hrd.qty_days, hrh.note, he.name, he.private_mail, mgd.code as request_code, 
				mgd.description as desc_request, mlt.description as leave_name
				FROM hr_request_header hrh
				JOIN hr_request_detail hrd
				ON hrh.id_request_header = hrd.id_request_header
				JOIN hr_employee he
				ON hrd.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON hrh.id_request_type = mgd.id_general_data
				LEFT JOIN master_leave_type mlt
				ON hrh.id_leave_type = mlt.id_leave_type
				WHERE hrd.id_request_header =".$data;
        $result = DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
	
	public static function get_mail_approval_travel($data) {
        $sql = "SELECT hot.reference_number, he.name, he.private_mail,hot.start_date::date AS request_start_to, 
				hot.end_date::date AS request_end_to, CONCAT(hot.location_to,' (',hot.reason_notes,')') AS note,  
				hat.source_transaction_type AS request_code, mgd.description as desc_request, hot.travel_status
				FROM hr_official_travel hot
				JOIN hr_employee he
				ON hot.request_by = he.id_employee
				JOIN hr_approval_transaction hat
				ON hot.id_official_travel = hat.id_source_transaction 
				AND hat.source_transaction_type = 'Official_Travel'
				JOIN master_general_data mgd
				ON hot.id_reason_group = mgd.id_general_data
				WHERE hot.id_official_travel = ".$data;
        $result = DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
	
	public static function list_req_type($emp_approval) {
		$id_user = session('id_user');
        $sql = "SELECT sfav.request_group_code id, CONCAT(sfav.request_group,' (',REPLACE(sfav.source_transaction_type,'_',' '),')') text
				FROM sp_funct_approval_view(?,?,null,null,null,?,null,null) sfav
				GROUP BY sfav.request_group_code, sfav.source_transaction_type, sfav.request_group
				ORDER BY sfav.source_transaction_type ASC";
        $result= DB::select($sql, [$emp_approval,session('id_company'),$id_user]);
        return $result;
    }
	
	public static function get_session_emp() {
		$id_user = session('id_user');
		$re = null;
        $sql = "SELECT he.id_employee
				FROM hr_employee he
				WHERE he.id_user = ? AND he.status = 'A'";
        $res= DB::select($sql, [$id_user]);
		if(count($res) > 0){
			$re = $res[0];
		}
		$result = json_decode(json_encode($re),true); 
        return $result;
    }

}
