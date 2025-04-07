<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterPeriod extends Model
{
	use HasFactory;
	
    protected $table = 'master_period';
	protected $primaryKey = 'id_period';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_period', 'period_code', 'description', 'year', 'start_date', 'end_date', 'id_function_type', 'status', 'id_company', 'created_by', 'updated_by'
    ];
			
	public static function getdata() {
		$sql = "SELECT mp.*, mgd.description as period_type
					FROM master_period mp
					LEFT JOIN master_general_data mgd 
					ON mp.id_function_type = mgd.id_general_data
					WHERE mp.id_company = ?
					ORDER BY mp.id_period ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_period_type() {
        $sql = "SELECT 
					mgd.id_general_data id,
					mgd.description text
				  FROM master_general_data mgd
				  JOIN master_general_type mgt
				  ON mgd.id_general_type = mgt.id_general_type
				  WHERE mgt.general_type = 'master_categories_period' AND mgd.status = 'A' AND mgd.id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_period_edit($data) {
        $result = [];
        $sql = "SELECT * 
					FROM master_period
				WHERE id_period  = ?";
        $result = (Array) DB::select($sql, [$data['id_period']])[0];
       // 	dd($result);
        return $result;
    }
}
