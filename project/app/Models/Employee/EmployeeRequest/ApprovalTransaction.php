<?php

namespace App\Models\Employee\EmployeeRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovalTransaction extends Model {

    protected $table = 'hr_approval_transaction';
    protected $primaryKey = 'id_approval_transaction';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_approval_transaction', 'id_source_transaction', 'source_transaction_type', 'id_approval', 'id_approval_detail', 'id_approval_mode', 'id_position_detail', 'sequence', 'id_employee_approval', 'id_approval_status', 'id_company', 'note_rejected', 'note_revised', 'created_by', 'updated_by'
    ];
	
	public static function get_approval($data) {
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
	
	public static function get_app_org($id_employee,$id_company) {
        $sql = "select	sfao.*, sfao.id_detail_chief as id_position_detail 
				from  sp_funct_approval_organization_hierarchy_view (".$id_employee.",".$id_company.",null,null) sfao";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	public static function get_app_combine($id_employee,$id_company,$id_approval) {
        $sql = "select	sfac.*, sfac.id_employee as id_employee_approval
				from  sp_funct_approval_combine_hierarchy_view (".$id_employee.",".$id_company.",".$id_approval.") sfac
				where sfac.id_employee != ".$id_employee;
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	public static function get_app_custom($id_employee,$id_company,$id_approval) {
        $sql = "select	sfacus.*, sfacus.id_employee as id_employee_approval
				from  sp_funct_approval_custom_hierarchy_view (".$id_employee.",".$id_company.",".$id_approval.") sfacus
				where sfacus.id_employee != ".$id_employee;
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	public static function get_app_custom_career($id_employee,$id_company,$id_approval) {
        $sql = "select	sfacus.*, sfacus.id_employee as id_employee_approval, he.id_user
				from  sp_funct_approval_custom_hierarchy_view (".$id_employee.",".$id_company.",".$id_approval.") sfacus
				left join hr_employee he
				on sfacus.id_employee = he.id_employee
				where sfacus.id_employee IS NOT NULL";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_career_req($data_emp) {
		$emp = $data_emp['emp'];
		$sql = "SELECT 
						he.id_employee,
						he.id_user,
						he.name as name_employee,
						ml.id_location
				FROM master_position_detail mpd
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				 AND (mpd.id_company =  ".session('id_company')."
                     OR mpd.assigned_to_company = ".session('id_company')."
                  )
				JOIN master_location ml
			  ON mpd.id_location = ml.id_location
			 AND mpd.id_company = ml.id_company
				WHERE he.id_employee = ".$emp." AND mpd.status = 'A' AND mpd.secondary_position = false";
        $result = DB::select($sql);
        return $result;
	}
	public static function get_user_req() {
		$sql = "SELECT 
						he.id_employee,
						he.id_user,
						he.name as name_employee,
						ml.id_location
				FROM master_position_detail mpd
				JOIN hr_employee he
				ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
				 AND (mpd.id_company =  ".session('id_company')."
                     OR mpd.assigned_to_company = ".session('id_company')."
                  )
				JOIN master_location ml
			  ON mpd.id_location = ml.id_location
			 AND mpd.id_company = ml.id_company
			WHERE he.id_user = ".session('id_user')."
			  AND mpd.status = 'A'
			  AND mpd.secondary_position = false";
        $result = DB::select($sql);
        return $result;
	}
	
	public static function get_hierachy($data,$req_location) {
		$code = $data['code'];
		$sql="select	*
				from	sp_funct_list_approval_hierarchy_view (array[".$req_location."],".session('id_company').",'".$code."','Combine')";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_hierachy_custom($data,$req_location) {
		$code = $data['code'];
		$sql="select	*
				from	sp_funct_list_approval_hierarchy_view (array[".$req_location."],".session('id_company').",'".$code."','Custom')";
        $result = DB::select($sql);
        return $result;
    }
	public static function get_hierachy_announ($data,$req_location) {
		$code = $data['code'];
		$sql="select	*
				from	sp_funct_list_approval_hierarchy_view (array[".$req_location."],".session('id_company').",'".$code."','Custom')";
        $result = DB::select($sql);
        return $result;
    }
	public static function get_hierachy_fpk($data) {
		$code = $data['code'];
		$id_employee = $data['id_employee'];
		$sql="SELECT DISTINCT id, text
				FROM sp_funct_list_approval_hierarchy_view(null,".session('id_company').",'".$code."','Custom',".$id_employee.")
			UNION ALL
			SELECT DISTINCT id, text
				FROM sp_funct_list_approval_hierarchy_view(null,".session('id_company').",'".$code."','Custom',null)
				WHERE id NOT IN (
					SELECT DISTINCT id
					FROM sp_funct_list_approval_hierarchy_view(null,".session('id_company').",'".$code."','Custom',".$id_employee.")
				) AND emp_approval NOT IN (".$id_employee.")";
        $result = DB::select($sql);
        return $result;
    }
	

}
