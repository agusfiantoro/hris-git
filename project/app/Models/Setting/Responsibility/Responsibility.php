<?php

namespace App\Models\Setting\Responsibility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Responsibility extends Model {

    use HasFactory;

    protected $table = 'master_responsibility';
    protected $primaryKey = 'id_responsibility';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_responsibility', 'sequence', 'responsibility_name', 'address_menu', 'status', 'inactive_date', 'id_responsibility_menu', 'id_company', 'created_by', 'updated_by'
    ];

    public static function getdata() {
        $data = DB::table('master_responsibility')
                ->join('master_responsibility_menu', 'master_responsibility.id_responsibility_menu', '=', 'master_responsibility_menu.id_responsibility_menu')
                ->select('master_responsibility.*', 'master_responsibility_menu.responsibility_name as responsibility_menu')
                ->where('master_responsibility.id_company', session('id_company'))
                ->get();

        return $data;
    }

    public static function get_address_menu() {
        $sql = "SELECT 
                        address_menu id,
                        address_menu text
                FROM  master_action";
        $result = DB::select($sql);

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

    public static function get_responsibility($data) {
        $result = [];
        $sql = "SELECT 
                    mr.id_responsibility,                 
                    mr.id_responsibility_menu,                 
                    mr.sequence,
                    mr.responsibility_name,
                    mr.inactive_date,
					mrm.responsibility_name as responsibility_menu,
					mrm.id_company,
					mc.company_name,
					mm.address_menu,
					mm.default_user,
					mm.default_manager,
					mm.default_administrator,
					mm.status
                FROM master_responsibility mr
				JOIN  master_responsibility_menu mrm
				ON mr.id_responsibility_menu = mrm.id_responsibility_menu
				JOIN  master_company mc
				ON mrm.id_company = mc.id_company
				LEFT JOIN  master_menu mm
				ON mr.id_responsibility = mm.id_responsibility
				WHERE mr.id_responsibility  = ? AND mr.id_company =" . session('id_company');
        $result = (Array) DB::select($sql, [$data['id_responsibility']])[0];

        $sql = "SELECT * FROM master_menu WHERE id_responsibility  = ?";
        $result_menu = DB::select($sql, [$data['id_responsibility']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_menu')->toArray();

        $result['menu'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['menu'][] = [
                'id_menu' => $group_menu[$value][0]->id_menu,
                'menu_name' => $group_menu[$value][0]->menu_name,
                'address_menu' => $group_menu[$value][0]->address_menu,
                'default_user' => $group_menu[$value][0]->default_user,
                'default_manager' => $group_menu[$value][0]->default_manager,
                'default_administrator' => $group_menu[$value][0]->default_administrator,
                'status' => $group_menu[$value][0]->status,
            ];
        }
        //	dd($result);
        return $result;
    }

}
