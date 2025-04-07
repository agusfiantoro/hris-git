<?php

namespace App\Models\Setting\Responsibility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ResponsibilityMenu extends Model {

    use HasFactory;

    protected $table = 'master_responsibility_menu';
    protected $primaryKey = 'id_responsibility_menu';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_responsibility_menu', 'sequence', 'responsibility_name', 'address_menu', 'status', 'icon', 'inactive_date', 'id_company', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_responsibility_menu')
                ->join('master_company', 'master_responsibility_menu.id_company', '=', 'master_company.id_company')
                ->select('master_responsibility_menu.*', 'master_company.company_name')
                ->where('master_responsibility_menu.id_company', session('id_company'))
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
