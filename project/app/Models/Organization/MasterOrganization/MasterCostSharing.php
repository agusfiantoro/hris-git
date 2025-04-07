<?php

namespace App\Models\Organization\MasterOrganization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterCostSharing extends Model {

    use HasFactory;

    protected $table = 'master_cost_sharing';
    protected $primaryKey = 'id_cost_sharing';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_cost_sharing', 'cost_sharing_code', 'id_branch', 'id_dept', 'id_principal', 'status', 'id_company', 'inactive_date', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_cost_sharing')
                ->join('master_company', 'master_cost_sharing.id_company', '=', 'master_company.id_company')
                ->join('master_branch', 'master_cost_sharing.id_branch', '=', 'master_branch.id_branch')
                ->join('master_department', 'master_cost_sharing.id_dept', '=', 'master_department.id_dept')
                ->join('master_principal', 'master_cost_sharing.id_principal', '=', 'master_principal.id_principal')
                ->select('master_cost_sharing.*', 'master_company.company_name', 'master_branch.description as branch', 'master_department.description as department', 'master_principal.description as principal')
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

    public static function get_branch() {
        $sql = "SELECT 
                        id_branch id,
                        description text
                FROM  master_branch where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_dept() {
        $sql = "SELECT 
                        id_dept id,
                        description text
                FROM  master_department where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_principal() {
        $sql = "SELECT 
                        id_principal id,
                        description text
                FROM  master_principal where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }

}
