<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomReport extends Model {

    protected $table = 'master_request_report';
    protected $primaryKey = 'id_req_report';
	
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
       'id_req_report', 'name_report', 'address_menu', 'group_column', 'detail_column', 'id_user', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getdata() {
	//	$attr = Schema::getColumnListing('hr_employee');
			$sql = "SELECT * 
					FROM master_request_report
					WHERE id_company = ? AND id_user = ?
					ORDER BY id_req_report ASC";	
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
        return $result;
    }
	public static function get_custom_edit($data) {
        $result = [];
        $sql = "SELECT * 
					FROM master_request_report
				WHERE id_req_report  = ?";
        $result = (Array) DB::select($sql, [$data['id_req_report']])[0];
       // 	dd($result);
        return $result;
    }
	
	public static function get_data_custom($data) {
		$address = $data['address'];
        $sql = "SELECT 
					id_req_report id,
					group_column text,
					detail_column
				FROM master_request_report
			WHERE id_company = ? AND id_user = ? AND address_menu = ?";
        $result = DB::select($sql,[session('id_company'),session('id_user'),$address]);

        return $result;
    }
}
