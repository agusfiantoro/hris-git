<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterChecklist extends Model {
	
	protected $table = 'master_checklist_employee';
    protected $primaryKey = 'id_checklist';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_checklist', 'document_name', 'checklist_type', 'id_dept', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $sql = "SELECT *
                    FROM master_checklist_employee mce
                    WHERE mce.id_company = ? ";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }

    public static function get_detail_master_checklist($data) {
        $sql = "SELECT *
                    FROM master_checklist_employee mce
                    WHERE mce.id_checklist = ?";
        $result = DB::select($sql, [$data['id_checklist']])[0];
        return $result;
    }
}
