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
	
	public static function getdata() {
		$id_user = session('id_user');
        $sql = "select * from sp_funct_approval_view(null,?,null,null,null,?)
				ORDER BY id_approval_transaction DESC";
        $x = DB::select($sql, [session('id_company'),$id_user]);
		$result = collect($x);
        return $result;
    }
	
	public static function getdata_history() {
		$id_user = session('id_user');
        $sql = "select * from sp_funct_approved_view(null,?,null,null,null,?)
				ORDER BY id_approval_transaction DESC";
        $x = DB::select($sql, [session('id_company'),$id_user]);
		$result = collect($x);
        return $result;
    }
	
	public static function get_view_approval($data) {
		$id_user = session('id_user');
        $result = [];
        $sql = "SELECT 
				sfav.*, hea.content_letter, hct.effective_date, hea.start_date, hea.end_date, 
				hrd.id_employee, hrd.request_start_to, hrd.request_end_to,
				CASE 
					WHEN hrd.day_type = 'Full_Day' THEN 'Full Day'
					WHEN hrd.day_type = 'Half_Day1' THEN 'Half Day (Awal)'
					WHEN hrd.day_type = 'Half_Day2' THEN 'Half Day (Akhir)'
				END AS day_type,
				coalesce(hrh.creation_date,hct.creation_date) as creation_date
			FROM sp_funct_approval_view(null,null,null,null,null,?) sfav
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
			WHERE sfav.id_approval_transaction  = ?
			ORDER BY sfav.id_approval_transaction DESC";
        $result = (Array) DB::select($sql, [$id_user,$data['id_approval_transaction']])[0];
       // 	dd($result);
        return $result;
    }
	
	public static function get_view_approval_history($data) {
		$id_user = session('id_user');
        $result = [];
        $sql = "SELECT 
				sfav.*, hea.content_letter, hct.effective_date, hea.start_date, hea.end_date, 
				hrd.id_employee, hrd.request_start_to, hrd.request_end_to,
				CASE 
					WHEN hrd.day_type = 'Full_Day' THEN 'Full Day'
					WHEN hrd.day_type = 'Half_Day1' THEN 'Half Day (Awal)'
					WHEN hrd.day_type = 'Half_Day2' THEN 'Half Day (Akhir)'
				END AS day_type,
				coalesce(hrh.creation_date,hct.creation_date) as creation_date
			FROM sp_funct_approved_view(null,null,null,null,null,?) sfav
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
			WHERE sfav.id_approval_transaction  = ?
			ORDER BY sfav.id_approval_transaction DESC";
        $result = (Array) DB::select($sql, [$id_user,$data['id_approval_transaction']])[0];
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

}
