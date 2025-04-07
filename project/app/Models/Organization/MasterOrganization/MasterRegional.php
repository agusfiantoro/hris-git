<?php

namespace App\Models\Organization\MasterOrganization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterRegional extends Model {

    use HasFactory;

    protected $table = 'master_region';
    protected $primaryKey = 'id_region';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_region', 'region_code', 'description', 'status', 'id_company', 'inactive_date', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_region')
                ->join('master_company', 'master_region.id_company', '=', 'master_company.id_company')
                ->select('master_region.*', 'master_company.company_name')
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
