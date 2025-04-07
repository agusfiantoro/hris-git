<?php

namespace App\Models\Setting\ResponsibilityUser;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterUser extends Model {

    use HasFactory;

    protected $table = 'master_users';
    protected $primaryKey = 'id_user';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_user', 'user_name', 'password', 'email', 'default_company', 'description_name', 'access_group', 'status', 'created_by', 'updated_by', 'mobile_serial_number', 'mobile_firebase_token', 'mobile_brand_manufacture', 'mobile_brand_type', 'mobile_os_version', 'mobile_ram_cappacity'
    ];

    public static function getdata() {
        $data = DB::table('master_users')
                ->join('master_user_responsibility', 'master_users.id_user', '=', 'master_user_responsibility.id_user')
                ->select('master_users.*')
                ->get();

        return $data;
    }

    public static function getassigned($id_user=null) {
        $where_idUser = '';
        if($id_user){
            $where_idUser = " AND mu.id_user IN(".implode(',',$id_user).") " ;
        }
        $sql = "SELECT
					mu.id_user, 
					mu.user_name, 
					mu.email, 
					mu.status, 
					mu.description_name,
					STRING_AGG(mc.company_name,', ') AS assigned_company 
					FROM  master_users mu
					LEFT JOIN  relation_company_users rcu 
					ON mu.id_user = rcu.id_user
					LEFT JOIN  master_company mc
					ON rcu.id_company = mc.id_company 
                    where mu.default_company = ?
                    $where_idUser
					GROUP BY mu.id_user,mu.user_name,mu.email, mu.status,mu.description_name";
        $data = DB::select($sql, [session('id_company')]);
        return $data;
    }

    public static function get_menu_desc($id) {
        $sql = "SELECT 
                        id_menu id,
                        menu_name text
                FROM  master_menu where id_menu =" . $id;
        $result = DB::select($sql);
        return $result;
    }

    public static function get_menu_name() {
        $sql = "SELECT 
                        id_menu id,
                        address_menu text
                FROM  master_menu";
        $result = DB::select($sql);

        return $result;
    }

    public static function get_menu() {
        $result = DB::table('master_menu as mm')
                ->join('master_responsibility as mr', 'mr.id_responsibility', '=', 'mm.id_responsibility')
                ->select('mm.*', 'mr.responsibility_name as responsibility')
                ->orderBy('mm.menu_name', 'asc')
                ->get();
        return $result;
    }

    public static function get_grade() {
        $sql = "SELECT * FROM  master_job_grade where id_company = ".session('id_company')." ";
        $result = DB::select($sql);
        return $result;
    }

    public static function get_user_responsibility($data) {
        $result = [];
        $sql = "SELECT 
		mu.id_user,mu.user_name,mu.password,mu.email,mu.status,mu.description_name,mu.default_company,mu.access_group, 
		mur.id_menu,mur.sequence,mur.start_date,mur.end_date,mur.description_name as description,mur.id_company,mur.can_create,mur.can_update,mur.can_delete,mur.can_print,
		mm.address_menu,
		rcu.id_company as assigned_company
		FROM master_users mu
		LEFT JOIN master_user_responsibility mur
		ON mur.id_user = mu.id_user
		LEFT JOIN master_menu mm
		ON mur.id_menu = mm.id_menu
		LEFT JOIN relation_company_users rcu
		ON rcu.id_user = mu.id_user
		LEFT JOIN master_company mc
		ON rcu.id_company = mc.id_company
		WHERE mu.id_user = ?";
        $result = (Array) DB::select($sql, [$data['id_user']])[0];

        $sql_relation = "SELECT * FROM relation_company_users WHERE id_user  = ?";
        $result_sql_relation = DB::select($sql_relation, [$data['id_user']]);
        $collect_assigned_company = collect($result_sql_relation);
        $group_assigned_company = $collect_assigned_company->groupBy('id_company')->toArray();
        $result['assigned_company'] = [];
        foreach (array_keys($group_assigned_company) as $key => $value) {
            $result['assigned_company'][] = $value;
        }
		
		  // GET MENU SESUAI ROLE
        $data_role = [
            'code_default' => $result['access_group'] ?? 'Default_User',
            'id_company' => $result['id_company'] ?? session('id_company'),
        ];
        $get_menu_role = self::get_default_access(@$data_role)['menu'];
        foreach ($get_menu_role as $k => $item) {
            unset($item['menu_name']);
            unset($item['id_company']);
            $get_menu_role[$k] = $item['id_menu'];  
        }
		

        $sql2 = "SELECT mur.*, rbu.id_branch
				FROM master_user_responsibility mur
				LEFT JOIN relation_branch_users rbu
				ON rbu.id_user_responsibility = mur.id_user_responsibility
				WHERE mur.id_user  = ?
				ORDER BY mur.sequence ASC";
        $result_menu = DB::select($sql2, [$data['id_user']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_user_responsibility')->toArray();

        $result['user_responsibility'] = [];
		$result['additional_menu'] = [];
        $result['additional_menu_id_company'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
			if(!in_array($group_menu[$value][0]->id_menu, $get_menu_role)){
                $result['additional_menu'][] = $group_menu[$value][0]->id_menu;
                $result['additional_menu_id_company'][] = $group_menu[$value][0]->id_company;
            }
			$group_branch = collect($group_menu[$value])->groupBy('id_branch')->toArray();
            $branch = array_keys($group_branch)[0] == '' ? [] : array_keys($group_branch);
            $result['user_responsibility'][] = [
                'id_user_responsibility' => $group_menu[$value][0]->id_user_responsibility,
                'sequence' => $group_menu[$value][0]->sequence,
                'id_menu' => $group_menu[$value][0]->id_menu,
                'description' => $group_menu[$value][0]->description_name,
                'id_company' => $group_menu[$value][0]->id_company,
                'branch' => $branch,
                'start_date' => $group_menu[$value][0]->start_date,
                'end_date' => $group_menu[$value][0]->end_date,
                'can_create' => $group_menu[$value][0]->can_create,
                'can_update' => $group_menu[$value][0]->can_update,
                'can_delete' => $group_menu[$value][0]->can_delete,
                'can_print' => $group_menu[$value][0]->can_print,
            ];
        }
    //   	dd($result);
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

    public static function get_assigned_company($id) {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company IN (" . $id . ")";
        $result = DB::select($sql);
        return $result;
    }

    public static function get_default_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company order by id";
        $result = DB::select($sql);
        return $result;
    }

	public static function get_region() {
        $sql = "select mr.id_region id, mr.description text
					from master_region mr
				where mr.status = 'A' and mr.id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_region_branch($id) {
        $sql = "select mr.id_region, mr.description as desc_region, mb.id_branch id, mb.description text
					from master_region mr
					join master_branch mb
					on mr.id_region = mb.id_region
				where mb.id_region IN (" . $id . ") and mb.status = 'A' and mb.id_company =". session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	
	public static function get_branch($id_company) {
        $id_company = $id_company ?? session('id_company');
        $sql = "select mr.id_region, mr.description as desc_region, mb.id_branch, mb.description as desc_branch
					from master_region mr
					left join master_branch mb
					on mr.id_region = mb.id_region
				where mb.status = 'A' and mb.id_company =" . $id_company;
        $result_list = DB::select($sql);
		$result = array();
        foreach ($result_list as $key=>$value) {
			 $result[$value->id_region -1]['id'] = $value->id_region;
			 $result[$value->id_region -1]['text'] = $value->desc_region;		
			 $result[$value->id_region -1]['children'][] = [
				'id' => $value->id_branch,
				'text' => $value->desc_branch,
			 ];			 
		}
	//	dd($result);
		return $result;
    }
	
	public static function get_default_access($data) {
		$code_default = $data['code_default'];
		$id_company = $data['id_company'];
    //  dd($code_default);
	//	dd($id_company);
		$sql = "select  sfdmu.*, mc.id_company
				  from     sp_funct_default_menu_user(?) sfdmu
				  CROSS JOIN master_company mc
				  where mc.id_company in (".$id_company.")";
        $result_menu = DB::select($sql,[$code_default]);
	/*	
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_company')->toArray();
		$result['assigned_company'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['assigned_company'][] = $value;
        }
	*/
		$result['menu'] = [];		
        foreach (array_keys($result_menu) as $key => $value) {
            $result['menu'][] = [		
                'id_menu' => $result_menu[$value]->id_menu,
                'menu_name' => $result_menu[$value]->menu_name,
                'id_company' => $result_menu[$value]->id_company,
            ];
        }
	
        return $result;
    }
	
	public static function get_default_access_edit($data) {
		$id_user = $data['id_user'];
		$id_company = $data['id_company'];
	//	dd($id_company);
		$sql = "select  mur.*, mc.id_company as id_company_assign
				  from   master_user_responsibility mur
				   CROSS JOIN master_company mc
				  where mc.id_company in (".$id_company.") and id_user = ?";
        $result_menu = DB::select($sql,[$id_user]);

		$result['menu'] = [];		
        foreach (array_keys($result_menu) as $key => $value) {
            $result['menu'][] = [		
				'id_user_responsibility' => $result_menu[$value]->id_user_responsibility,
                'sequence' => $result_menu[$value]->sequence,
                'id_menu' => $result_menu[$value]->id_menu,
                'description' => $result_menu[$value]->description_name,
            //    'branch' => $branch,
                'start_date' => $result_menu[$value]->start_date,
                'end_date' => $result_menu[$value]->end_date,
                'can_create' => $result_menu[$value]->can_create,
                'can_update' => $result_menu[$value]->can_update,
                'can_delete' => $result_menu[$value]->can_delete,
                'can_print' => $result_menu[$value]->can_print,
                'id_company' => $result_menu[$value]->id_company_assign,
            ];
        }
	
        return $result;
    }

    public static function getEmployeeIdUserNull($id_user=null) {
        $nik = DB::table('hr_employee as he')
                ->select('nik_employee')
                ->where('id_user', null)
                ->where('status', 'A')
                ->get()->pluck('nik_employee')->all();

        $data = DB::table('master_users as mu')
                ->leftJoin('master_company as mc', 'mu.default_company', '=', 'mc.id_company')
                ->select('mu.id_user', 'mu.user_name', 'mu.email', 'mu.status', 'mu.description_name', DB::raw("string_agg(mc.company_name,',') as assigned_company"))
                ->where('mu.default_company', session('id_company'))
                ->whereIn('mu.user_name', $nik)
                ->groupBy('mu.id_user', 'mu.user_name', 'mu.email', 'mu.status', 'mu.description_name');
        if($id_user){
            $data->whereIn('mu.id_user', $id_user);
        } 
        return $data->get();
    }

}
