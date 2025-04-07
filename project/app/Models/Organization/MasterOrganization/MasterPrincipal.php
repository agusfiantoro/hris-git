<?php

namespace App\Models\Organization\MasterOrganization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterPrincipal extends Model {

    use HasFactory;

    protected $table = 'master_principal';
    protected $primaryKey = 'id_principal';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_principal', 'id_division', 'principal_code', 'description', 'principal_logo','status', 'id_company', 'inactive_date', 'is_published_to_web', 'erp_principal_code', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_principal')
                ->join('master_company', 'master_principal.id_company', '=', 'master_company.id_company')
                ->join('master_division', 'master_principal.id_division', '=', 'master_division.id_division')
                ->select('master_principal.*', 'master_division.description as division_name', 'master_company.company_name')
                ->where('master_company.id_company', session('id_company'))
				->orderBy('master_principal.id_division','ASC')
				->orderBy('master_principal.id_principal','DESC')
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

	public static function get_division() {
        $sql = "SELECT 
                        id_division id,
                        description text
                FROM  master_division where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }

}
