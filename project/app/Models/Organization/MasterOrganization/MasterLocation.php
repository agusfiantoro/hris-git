<?php

namespace App\Models\Organization\MasterOrganization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterLocation extends Model {

    use HasFactory;

    protected $table = 'master_location';
    protected $primaryKey = 'id_location';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_location', 'location_code', 'id_branch', 'description', 'address_location', 'longitude', 'latitude', 'status', 'id_company', 'inactive_date', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_location')
                ->join('master_company', 'master_location.id_company', '=', 'master_company.id_company')
                ->join('master_branch', 'master_location.id_branch', '=', 'master_branch.id_branch')
                ->select('master_location.*', 'master_company.company_name', 'master_branch.description as desc_branch')
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
                FROM  master_branch where status='A' AND id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }

}
