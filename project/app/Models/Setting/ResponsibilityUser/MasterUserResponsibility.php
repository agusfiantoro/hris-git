<?php

namespace App\Models\Setting\ResponsibilityUser;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;

class MasterUserResponsibility extends Model {

    use HasFactory;

    protected $table = 'master_user_responsibility';
    protected $primaryKey = 'id_user_responsibility';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_user_responsibility', 'id_user', 'id_menu', 'sequence', 'description_name', 'start_date', 'end_date', 'id_company', 'can_create', 'can_update', 'can_delete', 'can_print', 'id_responsibility_menu', 'id_responsibility', 'created_by', 'updated_by'
    ];

    public static function get_address_menu($id_user=null) {
        if(is_null($id_user)){
            $id_user = session('id_user');
        }
        $data = DB::table('master_user_responsibility as mur')
                ->join('master_menu as mm', 'mur.id_menu', '=', 'mm.id_menu')
                ->select('mm.address_menu')
                ->where('mur.id_user', '=', $id_user)
                ->get()
                ->pluck('address_menu')
                ->all();
        return $data;
    }

    public static function getAccessByAddressMenu($id_user=null) {
        if(is_null($id_user)){
            $id_user = session('id_user');
        }
        $data = DB::table('master_user_responsibility as mur')
                ->join('master_menu as mm', 'mur.id_menu', '=', 'mm.id_menu')
                ->select('mm.address_menu', 'mur.can_create', 'mur.can_update', 'mur.can_delete', 'mur.can_print', 'mur.id_company')
                ->where('mur.id_user', '=', $id_user)
                ->get();

        $accessByAddressMenu = [];
        if($data->count() > 0){
            foreach ($data as $k => $val) {
                $accessByAddressMenu[$val->address_menu][$val->id_company]['can_create'] = $val->can_create;
                $accessByAddressMenu[$val->address_menu][$val->id_company]['can_update'] = $val->can_update;
                $accessByAddressMenu[$val->address_menu][$val->id_company]['can_delete'] = $val->can_delete;
                $accessByAddressMenu[$val->address_menu][$val->id_company]['can_print'] = $val->can_print;
            }
        }
        return $accessByAddressMenu;
    }

    public static function get_user_responsibility($id_user=null, $menu_name=null) {
        $id_user = $id_user ?? session('id_user');
        $menu_name = is_array($menu_name) ? $menu_name : [$menu_name] ;
        
        $data = DB::table('master_user_responsibility as mur')
                ->join('master_menu as mm', 'mur.id_menu', '=', 'mm.id_menu')
                ->select('mur.*')
                ->where('mur.id_user', '=', $id_user)
                ->whereIn('mm.address_menu', $menu_name);

        return $data->get();
    }

    public static function getMenuByAccessGroup($accessGroup='Default_User', $idCompany=null) {
        $idCompany =  $idCompany ?? session('id_company');
        $getAccessByGroup = DB::table(DB::raw("sp_funct_default_menu_user('".$accessGroup."') sfdmu"))
            ->crossJoin('master_company as mc')
            ->select('sfdmu.*', 'mc.id_company')
            ->where('mc.id_company', $idCompany)
            ->get();

        return $getAccessByGroup;
    }

    public static function getMenuById($idMenu) {
        if(!is_array($idMenu)){
            if(strpos($idMenu, ',') !== false) {
                $idMenu = explode(',', $idMenu);
            } else {
                $idMenu = [$idMenu];
            }
        }
        $getMenu = DB::table('master_menu as mm')
            ->select('mm.*', 'mr.id_responsibility_menu', 'mr.responsibility_name')
            ->leftJoin('master_responsibility as mr', 'mm.id_responsibility', '=', 'mr.id_responsibility')
            ->whereIn('mm.id_menu', $idMenu)
            ->get();

        return $getMenu;
    }

    public static function getResponsibilityByIdUser($idUser=null, $idCompany=null) {
        $idUser =  $idUser ?? session('id_user');
        $mur = DB::table('master_user_responsibility as mur')
                ->select('mur.id_menu', 'mur.id_user', 'mur.sequence', 'mur.id_company')
                ->where('mur.id_user', $idUser)
                ->orderBy('mur.id_user')
                ->orderBy('mur.sequence')
                ->orderBy('mur.id_company');

        if($idCompany){
            $mur->where('mur.id_company', $idCompany);
        }
        $result = $mur->get();
        return $result;
    }

    public static function getUserByAccessGroupOrGrade($accessGroup=null, $idGrade=null, $exceptUser=null, $idCompany=null, $withGrade=true, $status='A') {
        $accessGroup    =  $accessGroup ?? 'Default_User';

        if($idGrade){
            if(!is_array($idGrade)){
                if(strpos($idGrade, ',') !== false) {
                    $idGrade = explode(',', $idGrade);
                } else {
                    $idGrade = [$idGrade];
                }
            }
        }
        $user = DB::table('master_users as mu')
                ->select('mu.*')
                ->where('mu.status', $status)
                ->whereIn('mu.default_company', $idCompany);

        if($withGrade){
            $user->leftJoin('hr_employee as he', 'he.id_user', '=', 'mu.id_user')
                ->leftJoin('master_position_detail as mpd', 'mpd.id_employee', '=', 'he.id_employee')
                ->leftJoin('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing');

            if($idGrade){
                $user->whereIn('mpr.id_job_grade', $idGrade);
            }
        }
        
        if($accessGroup){
            $user->where('mu.access_group', $accessGroup);
        }
        if($exceptUser){
            $user->whereNotIn('mu.id_user', $exceptUser);
        }
        return $user->get();
    }

    public static function insertOrDeleteMenu($idMenu, $id_user=null, $action=null, $idCompany = null) {
        if(is_array($id_user) && count($id_user) > 0){
            $user_id = $id_user;
        } else {
            if($id_user){
                $user_id = [$id_user];
            } else {
                $user_id = [session('id_user')];
            }
        }
        if(!$idCompany) {
            $idCompany = session('id_company');
        }
        $addingMenu = [];
        $getUser = DB::table('master_users as mu')
                    ->select('mu.*', 'mu.default_company as id_company')
                    ->whereIn('mu.id_user', $user_id)
                    ->get();
        $getMenu = self::getMenuById($idMenu);

        if($getUser){
            foreach ($getUser as $key => $val) {
                $mur = self::getResponsibilityByIdUser($val->id_user);
                $getUserRelationCompany = DB::table('master_users as mu')
                    ->leftJoin('relation_company_users as rcu', 'rcu.id_user', '=', 'mu.id_user')
                    ->select('mu.*', 'rcu.id_company')
                    ->where('mu.id_user', $val->id_user)
                    ->get();

                if(count($mur) > 0){
                    $id_menu_mur    = $mur->pluck('id_menu')->all();
                    $sequence_mur   = max($mur->pluck('sequence')->all());

                    foreach ($getMenu as $item) {
                        if($action == 'delete'){
                            if(in_array($item->id_menu, $id_menu_mur)){
                                $checkMur = MasterUserResponsibility::where('id_menu', $item->id_menu)->where('id_user', $val->id_user);

                                $getIdResponsibility = $checkMur->get()->pluck('id_user_responsibility')->all();
                                if(count($getIdResponsibility) > 0){
                                    RelationBranchUser::whereIn('id_user_responsibility', $getIdResponsibility)->delete();
                                }
                                $checkMur->delete();
                            }
                        }
                        else {
                            $sequenceMenu = $sequence_mur+1;
                            if(!in_array($item->id_menu, $id_menu_mur)){
                                // jika menu belum diset di responsibility di semua relation company user tsb
                                if($getUserRelationCompany->count() > 0){
                                    foreach ($getUserRelationCompany as $k => $valRelation) {
                                        $responsibility_data = [
                                            'id_user'       => $valRelation->id_user,
                                            'start_date'    => date('Y-m-d'),
                                            'can_create'    => 1,
                                            'can_update'    => 1,
                                            'can_delete'    => 0,
                                            'can_print'     => 1,
                                            'id_company'    => $valRelation->id_company ?? $idCompany,
                                            'created_by'    => session('id_user') ?? 1,
                                            'id_menu'       => $item->id_menu,
                                            'id_responsibility'         => $item->id_responsibility,
                                            'id_responsibility_menu'    => $item->id_responsibility_menu,
                                            'sequence'      => $sequenceMenu,
                                            'description_name' => $item->menu_name,
                                            'created_by'    => session('id_user'),
                                        ];
                                        $addingMenu[] = 1;
                                        try{
                                            if(!MasterUserResponsibility::create($responsibility_data)){
                                                throw new \Exception('Insert user responsibility failed');
                                            }
                                        } catch (\Exception $e) {
                                            throw new \Exception($e->getMessage().' - IdUser : '.$valRelation->id_user);           
                                        }
                                    }
                                } else {
                                    //jika user tidak memiliki relation_company maka pakai default_company dr master_users
                                    $responsibility_data = [
                                        'id_user'       => $val->id_user,
                                        'start_date'    => date('Y-m-d'),
                                        'can_create'    => 1,
                                        'can_update'    => 1,
                                        'can_delete'    => 0,
                                        'can_print'     => 1,
                                        'id_company'    => $val->id_company ?? $idCompany,
                                        'created_by'    => session('id_user') ?? 1,
                                        'id_menu'       => $item->id_menu,
                                        'id_responsibility'         => $item->id_responsibility,
                                        'id_responsibility_menu'    => $item->id_responsibility_menu,
                                        'sequence'      => $sequenceMenu,
                                        'description_name' => $item->menu_name,
                                        'created_by'    => session('id_user'),
                                    ];
                                    $addingMenu[] = 1;
                                    try{
                                        if(!MasterUserResponsibility::create($responsibility_data)){
                                            throw new \Exception('Insert user responsibility failed');
                                        }
                                    } catch (\Exception $e) {
                                        throw new \Exception($e->getMessage().' - IdUser : '.$val->id_user);           
                                    }
                                }
                            } else {
                                if($getUserRelationCompany->count() > 0){
                                    foreach ($getUserRelationCompany as $k => $valRelation) {
                                        // jika menu belum diset di responsibility di bebrapa relation company user tsb
                                        $murByCompany = self::getResponsibilityByIdUser($val->id_user, $valRelation->id_company);
                                        $idMenuByCompany = $murByCompany->pluck('id_menu')->all();
                                        $sequenceMurByCompany = max($murByCompany->pluck('sequence')->all());
                                        
                                        if(!in_array($item->id_menu, $idMenuByCompany)){
                                            $responsibility_data = [
                                                'id_user'       => $valRelation->id_user,
                                                'start_date'    => date('Y-m-d'),
                                                'can_create'    => 1,
                                                'can_update'    => 1,
                                                'can_delete'    => 0,
                                                'can_print'     => 1,
                                                'id_company'    => $valRelation->id_company ?? $idCompany,
                                                'created_by'    => session('id_user') ?? 1,
                                                'id_menu'       => $item->id_menu,
                                                'id_responsibility'         => $item->id_responsibility,
                                                'id_responsibility_menu'    => $item->id_responsibility_menu,
                                                'sequence'      => $sequenceMurByCompany+1,
                                                'description_name' => $item->menu_name,
                                                'created_by'    => session('id_user'),
                                            ];
                                            try{
                                                if(!MasterUserResponsibility::create($responsibility_data)){
                                                    throw new \Exception('Insert user responsibility failed');
                                                }
                                            } catch (\Exception $e) {
                                                throw new \Exception($e->getMessage().' - IdUser : '.$valRelation->id_user);           
                                            }
                                            $addingMenu[] = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    //jika user belum memiliki akses di menu apapun
                    MasterUserResponsibility::where('id_user', $val->id_user)->delete();
                    $sequence = 1;
                    foreach ($getMenu as $item) {
                        $responsibility_data['id_menu']             = $item->id_menu;
                        $responsibility_data['id_responsibility']   = $item->id_responsibility;
                        $responsibility_data['id_responsibility_menu'] = $item->id_responsibility_menu;
                        $responsibility_data['sequence']            = $sequence;
                        $responsibility_data['description_name']    = $item->menu_name;
                        $responsibility_data['created_by']          = session('id_user');
                        $responsibility_data['id_user']             = $val->id_user;
                        $responsibility_data['id_company']          = $idCompany;
                        $addingMenu[] = 1;

                        try{
                            if(!MasterUserResponsibility::create($responsibility_data)){
                                throw new \Exception('Insert user responsibility failed');
                            }
                        } catch (\Exception $e) {
                            throw new \Exception($e->getMessage().' - IdUser : '.$val->id_user);           
                        }
                        $sequence++;
                    }
                }
            }
        }
        
        $return['user'] = count($getUser);
        $return['access_menu'] = count($addingMenu);

        return $return;
    }

    
}
