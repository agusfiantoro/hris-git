<?php

namespace App\Models\Organization\OrganizationStructure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrganizationUnit extends Model {

    use HasFactory;

    protected $table = 'master_department';
    protected $primaryKey = 'id_dept';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_dept', 'department_code', 'description', 'status', 'id_company', 'inactive_date', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_department')
                ->join('master_company', 'master_department.id_company', '=', 'master_company.id_company')
                ->select('master_department.*', 'master_company.company_name')
                ->where('master_company.id_company', session('id_company'))
                ->get();
        return $data;
    }

    public static function get_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }

}
