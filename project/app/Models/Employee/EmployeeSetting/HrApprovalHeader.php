<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrApprovalHeader extends Model {
	
	protected $table = 'hr_approval_header';
    protected $primaryKey = 'id_approval';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_approval', 'description', 'hierarchy_type', 'approval_mode', 'id_approval_doc_type','enable_limit','limit','note', 'status', 'id_job_grade', 'id_company', 'created_by', 'updated_by'
    ];


    public static function getdata() {
        $data = DB::table('hr_approval_header')
                ->select('hr_approval_header.*')
                ->where('id_company', session('id_company'))
                ->orderBy('id_approval', 'DESC')
                ->get();
        return $data;
    }
	
	
	public static function get_approval_edit($data) {
        $result = [];
        $sql = "SELECT 
                    hah.id_approval,                 
                    hah.description,
                    hah.hierarchy_type,
                    hah.id_approval_doc_type,
                    hah.approval_mode,
                    hah.enable_limit,
                    hah.limit as limit_header,
                    hah.note as note_header,
                    hah.status as status_header,
					hah.id_job_grade
                FROM hr_approval_header hah
				WHERE hah.id_approval  = ?";
        $result = (Array) DB::select($sql, [$data['id_approval']])[0];

        $sql = "SELECT * FROM hr_approval_detail WHERE id_approval  = ?";
        $result_menu = DB::select($sql, [$data['id_approval']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_approval_detail')->toArray();

        $result['approval'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['approval'][] = [
                'id_approval_detail' => $group_menu[$value][0]->id_approval_detail,
                'id_approval_mode' => $group_menu[$value][0]->id_approval_mode,
                'sequence' => $group_menu[$value][0]->sequence,
            //    'id_employee' => $group_menu[$value][0]->id_employee,
                'id_position_detail' => $group_menu[$value][0]->id_position_detail,
                'limit' => $group_menu[$value][0]->limit,
                'note' => $group_menu[$value][0]->note,
                'status' => $group_menu[$value][0]->status,
            ];
        }
        //	dd($result);
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
	
/*	public static function get_employee() {
        $sql = "SELECT 
                        id_employee id,
                        name text
                FROM  hr_employee where status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
*/	
	public static function get_position_detail() {
        $sql = "SELECT 
		mpd.id_position_detail id,
		mpd.description text,
		he.id_employee,
		he.name as employee_name
		FROM  master_position_detail mpd
		LEFT JOIN hr_employee he
		ON mpd.id_employee = he.id_employee
		WHERE mpd.status = 'A'
		AND	(mpd.id_company = ". session('id_company') ."
		OR	mpd.id_company in(select distinct id_company
						 from	master_position_detail
						 where	assigned_to_company = ". session('id_company') ."))";
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_approval_mode() {
        $sql = "SELECT 
                        id_general_data id,
                        description text
                FROM  master_general_data where status = 'A' and id_general_type = 8 and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_approval_doc() {
        $sql = "SELECT 
                        id_general_data id,
                        description text
                FROM  master_general_data where status = 'A' and id_general_type = 9 and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_grade() {
        $sql = "SELECT mjg.id_job_grade id, mjg.description text
				FROM master_job_grade mjg
				WHERE mjg.status = 'A' AND mjg.id_company = ".session('id_company')."
				ORDER BY mjg.id_job_grade ASC ";
        $result = DB::select($sql);

        return $result;
    }
}
