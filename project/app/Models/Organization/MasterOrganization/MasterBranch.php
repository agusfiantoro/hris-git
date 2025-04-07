<?php

namespace App\Models\Organization\MasterOrganization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterBranch extends Model {

    use HasFactory;

    protected $table = 'master_branch';
    protected $primaryKey = 'id_branch';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_branch', 'id_region', 'branch_code', 'id_location', 'description', 'status', 'id_company', 'id_branch_erp', 'inactive_date', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_branch')
                ->join('master_company', 'master_branch.id_company', '=', 'master_company.id_company')
                ->join('master_region', 'master_branch.id_region', '=', 'master_region.id_region')
                ->select('master_branch.*', 'master_company.company_name', 'master_region.description as desc_region')
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

    public static function get_regional() {
        $sql = "SELECT 
                        id_region id,
                        description text
                FROM  master_region where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }

}
