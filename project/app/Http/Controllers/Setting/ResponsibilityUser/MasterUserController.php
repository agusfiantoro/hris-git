<?php

namespace App\Http\Controllers\Setting\ResponsibilityUser;

use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationCompanyUser;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\Setting\Responsibility\MasterMenu;
use App\Models\Setting\Responsibility\Responsibility;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DataTables;
use Validator;

class MasterUserController extends Controller {

    public function index(Request $request) {
        $allEmployee   = $this->employeeAll(true);
        if ($request->ajax()) {
            $id_user = $request->id_user ?? null;
            $is_null = $request->is_null ?? null;
            if($is_null){
                $data = MasterUser::getEmployeeIdUserNull();
            } else {
                $data = MasterUser::getassigned($id_user);
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        $button = '<button type="button" name="edit" id="' . $data->id_user . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_user . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                        return $button;
                    })->addColumn('assigned_company', function($row) {
                        return $row->assigned_company;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        $path_menu      = $request->path();

        return view('setting.responsibility_menu.access_right_user.index', compact('path_menu', 'allEmployee'));
    }

    protected function validateResponsibility(Request $request) {

        $arr_form_validate = [
            'user_name' => 'required|string',
            'password' => 'required|min:6',
            'email' => 'required|email',
            'assigned_company' => 'required|array',
            'responsibility_user.*.id_menu' => 'required|string',
            'responsibility_user.*.description' => 'required|string',
        ];
        $arr_msg_form_validate = [
            'user_name.required' => 'The User Name field is required',
            'description_name.required' => 'The Description Name field is required',
            'assigned_company.required' => 'The Assigned Company field is required',
            'responsibility_user.*.responsibility_name.required' => 'The Menu Name field is required',
            'responsibility_user.*.description.required' => 'The Description field is required',
        ];
        /*
          if ($request->post('responsibility_user') == null) {
          $validate_responsibility = ['table_menu_detail' => 'required|string'];
          $validate_msg_responsibility = ['table_menu_detail.required' => 'Menu cannot empty'];
          $arr_form_validate = array_merge($arr_form_validate, $validate_responsibility);
          $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_responsibility);
          }
         */
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function validateResponsibilityUpdate(Request $request) {

        $arr_form_validate = [
            'user_name' => 'required|string',
            'email' => 'required|email',
            'assigned_company' => 'required|array',
            'responsibility_user.*.id_menu' => 'required|string',
            'responsibility_user.*.description' => 'required|string',
            'responsibility_user.*.id_company' => 'required|string',
        ];
        $arr_msg_form_validate = [
            'user_name.required' => 'The User Name field is required',
            'description_name.required' => 'The Description Name field is required',
            'assigned_company.required' => 'The Assigned Company field is required',
            'responsibility_user.*.responsibility_name.required' => 'The Menu Name field is required',
            'responsibility_user.*.description.required' => 'The Description field is required',
            'responsibility_user.*.id_company.required' => 'Company field is required',
        ];
        /*
          if ($request->post('responsibility_user') == null) {
          $validate_responsibility = ['table_menu_detail' => 'required|string'];
          $validate_msg_responsibility = ['table_menu_detail.required' => 'Menu cannot empty'];
          $arr_form_validate = array_merge($arr_form_validate, $validate_responsibility);
          $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_responsibility);
          }
         */
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        ini_set('max_execution_time', -1);
        ini_set('max_input_vars', -1);
        $this->validateResponsibility($request);
        $form_data = array(
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'default_company' => $request->default_company,
            'description_name' => $request->description_name,
            'access_group' => $request->default_access,
            'created_by' => session('id_user'),
        );
        try{
            DB::beginTransaction();
        $responsibility = MasterUser::create($form_data);
        foreach ($request->assigned_company as $key => $value) {
            RelationCompanyUser::create(array(
                'id_user' => $responsibility->id_user,
                'id_company' => $value,
                'created_by' => session('id_user'),
            ));
        }       
        if ($request->responsibility_user != null) {
            foreach ($request->responsibility_user as $key => $value) {
                $mm = MasterMenu::where('id_menu', $value['id_menu'])->first();
                $r = Responsibility::where('id_responsibility', $mm->id_responsibility)->first();
                $form_respon = array(
                    'id_user' => $responsibility->id_user,
                    'id_menu' => $value['id_menu'],
                    'id_responsibility' => $mm->id_responsibility,
                    'id_responsibility_menu' => $r->id_responsibility_menu,
                    'sequence' => $value['sequence'],
                    'description_name' => $value['description'],
                    'start_date' => $value['start_date'],
                    'end_date' => $value['end_date'],
                    'can_create' => isset($value['can_create']) == "on" ? 1 : 0,
                    'can_update' => isset($value['can_update']) == "on" ? 1 : 0,
                    'can_delete' => isset($value['can_delete']) == 'on' ? 1 : 0,
                    'can_print' => isset($value['can_print']) == 'on' ? 1 : 0,
                    'id_company' => $value['id_company'],
                    'created_by' => session('id_user'),
                );              
                $mur = MasterUserResponsibility::create($form_respon);
                if(isset($value['branch'])){
                    foreach ($value['branch'] as $key2 => $value2) {
                        RelationBranchUser::create(array(
                            'id_user_responsibility' => $mur->id_user_responsibility,
                            'id_branch' => $value2,
                            'id_company' => $value['id_company'],
                            'created_by' => session('id_user'),
                        ));
                    }
                }
            }           
        }
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Access Right User Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Save Access Right User !! [' . $e->getMessage() . ']']);           
        }
    }

    protected function update(Request $request) {
        ini_set('max_execution_time', -1);
        $this->validateResponsibilityUpdate($request);
        $form_data = array(
            'id_user' => $request->id_user,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'default_company' => $request->default_company,
            'description_name' => $request->description_name,
            'access_group' => $request->default_access,
            'status' => $request->status,
            'updated_by' => session('id_user'),
        );
        if ($request->password != "") {
            $form_data['password'] = Hash::make($request->password);
        }
        try{
            DB::beginTransaction();

            $responsibility = MasterUser::findOrFail($request->id_user)->update($form_data);
            RelationCompanyUser::where('id_user', $request->id_user)->delete();
            foreach ($request->assigned_company as $key => $value) {
                RelationCompanyUser::create(array(
                    'id_user' => $request->id_user,
                    'id_company' => $value,
                    'created_by' => session('id_user'),
                    'updated_by' => session('id_user'),
                ));
            }

            $collect_responsibility_user = collect($request->responsibility_user)->groupBy('id_user_responsibility')->toArray();
            $list_id_user_responsibility = array_filter(array_keys($collect_responsibility_user));

            if ($request->responsibility_user != null) {
                if (implode(",", $list_id_user_responsibility) != "") {
                    DB::delete("DELETE FROM  relation_branch_users rbu 
                                USING master_user_responsibility mur
                                WHERE rbu.id_user_responsibility = mur.id_user_responsibility
                                AND mur.id_user = ? AND rbu.id_user_responsibility NOT IN (" . implode(",", $list_id_user_responsibility) . ")", [$request->id_user]);
                                
                    DB::delete("DELETE FROM  master_user_responsibility mur 
                                WHERE mur.id_user = ? AND mur.id_user_responsibility NOT IN (" . implode(",", $list_id_user_responsibility) . ")", [$request->id_user]);
                }
                
                foreach ($request->responsibility_user as $key => $value) {
                    $mm = MasterMenu::where('id_menu', $value['id_menu'])->first();
                    $r = Responsibility::where('id_responsibility', $mm->id_responsibility)->first();
                    if ($value['id_user_responsibility'] == "") {
                       $form_respon = array(
                            'id_user' => $request->id_user,
                            'id_menu' => $value['id_menu'],
                            'id_responsibility' => $mm->id_responsibility,
                            'id_responsibility_menu' => $r->id_responsibility_menu,
                            'sequence' => $value['sequence'],
                            'description_name' => $value['description'],
                            'start_date' => $value['start_date'],
                            'end_date' => $value['end_date'],
                            'can_create' => isset($value['can_create']) == "on" ? 1 : 0,
                            'can_update' => isset($value['can_update']) == "on" ? 1 : 0,
                            'can_delete' => isset($value['can_delete']) == 'on' ? 1 : 0,
                            'can_print' => isset($value['can_print']) == 'on' ? 1 : 0,
                            'id_company' => $value['id_company'],
                            'created_by' => session('id_user'),
                        );
                        $mur =  MasterUserResponsibility::create($form_respon);
                        if(isset($value['branch'])){
                            foreach ($value['branch'] as $key2 => $value2) {
                                RelationBranchUser::create(array(
                                    'id_user_responsibility' => $mur->id_user_responsibility,
                                    'id_branch' => $value2,
                                    'id_company' => $value['id_company'],
                                    'created_by' => session('id_user'),
                                ));
                            }
                        }
                    } else {
                        $form_respon = array(
                            'id_menu' => $value['id_menu'],
                            'id_responsibility' => $mm->id_responsibility,
                            'id_responsibility_menu' => $r->id_responsibility_menu,
                            'sequence' => $value['sequence'],
                            'description_name' => $value['description'],
                            'start_date' => $value['start_date'],
                            'end_date' => $value['end_date'],
                            'can_create' => isset($value['can_create']) == "on" ? 1 : 0,
                            'can_update' => isset($value['can_update']) == "on" ? 1 : 0,
                            'can_delete' => isset($value['can_delete']) == 'on' ? 1 : 0,
                            'can_print' => isset($value['can_print']) == 'on' ? 1 : 0,
                            'id_company' => $value['id_company'],
                            'updated_by' => session('id_user'),
                        );
                        $mur = MasterUserResponsibility::where('id_user_responsibility', $value['id_user_responsibility'])->update($form_respon);
                        if(isset($value['branch'])){
                            RelationBranchUser::where('id_user_responsibility', $value['id_user_responsibility'])->delete();
                            foreach ($value['branch'] as $key2 => $value2) {
                                RelationBranchUser::create(array(
                                    'id_user_responsibility' => $value['id_user_responsibility'],
                                    'id_branch' => $value2,
                                    'id_company' => $value['id_company'],
                                    'created_by' => session('id_user'),
                                ));
                            }
                        }
                        else{
                            RelationBranchUser::where('id_user_responsibility', $value['id_user_responsibility'])->delete();
                        }
                    }
                }
            } else if (implode(",", $list_id_user_responsibility) == null) {
                DB::delete("DELETE FROM  relation_branch_users rbu 
                                USING  master_user_responsibility mur 
                                WHERE rbu.id_user_responsibility = mur.id_user_responsibility
                                AND mur.id_user = ?",  [$request->id_user]);
                DB::delete("DELETE FROM  master_user_responsibility mur 
                            WHERE mur.id_user = ?", [$request->id_user]);
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Access Right User Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => 'Cannot Update Access Right User !! [' . $e->getMessage() . ']']);           
        }
    }

    public function destroy($id) {
        try{
            DB::beginTransaction();
            $data = MasterUser::findOrFail($id);
            $mur = MasterUserResponsibility::where('id_user', $id)->get();
            foreach($mur as $value){
                RelationBranchUser::where('id_user_responsibility', $value['id_user_responsibility'])->delete();
            }
            MasterUserResponsibility::where('id_user', $id)->delete();
            RelationCompanyUser::where('id_user', $id)->delete();
            $data->delete();
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Data Deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => 'Cannot Delete Data [' . $e->getMessage() . ']']);           
        }
    }

    public function get_menu_name() {
        $result = MasterUser::get_menu_name();
        return response()->json($result);
    }

    public function get_menu() {
        $result = MasterUser::get_menu();
        return response()->json($result);
    }

    public function get_grade() {
        $result = MasterUser::get_grade();
        return response()->json($result);
    }

    public function get_menu_desc(Request $request) {
        $id = $request->get('id');
        $result = MasterUser::get_menu_desc($id);
        return response()->json($result);
    }

    public function get_assigned_company(Request $request) {
        $id = $request->get('id');
        $result = MasterUser::get_assigned_company($id);
        return response()->json($result);
    }

    public function get_default_company() {
        $result = MasterUser::get_default_company();
        return response()->json($result);
    }
    public function get_region() {
        $result = MasterUser::get_region();
        return response()->json($result);
    }
     public function get_region_branch(Request $request) {
        $id = $request->get('id');
        $result = MasterUser::get_region_branch($id);   
        return response()->json($result);
    }

    public function get_branch(Request $request) {
        $id_company = @$request->id_company ?? session('id_company');
        // $region = MasterRegional::where('id_company', $id_company)->get();
        // $branch = MasterBranch::where('id_company', $id_company)->get();
        $region = DB::table('master_region as mr')
                    ->join('master_company as mc', 'mc.id_company', '=', 'mr.id_company')
                    ->select('mr.id_region','mc.company_code','mr.description','mr.id_company')
                    ->get();
        // $region = MasterRegional::select('id_region','region_code','description','id_company')->get();
        $branch = MasterBranch::select('id_branch','branch_code','description','id_region')->get();
        $result = [];
        foreach ($region as $key => $value) {
            $child = [];
            foreach ($branch as $k => $item) {
                if($value->id_region == $item->id_region){
                    $child[] = [
                        'id'        => $item->id_branch,
                        'text'      => $item->description,
                    ];
                }
            }
            $result[] = [
                'id'        => $value->id_region,
                'text'      => $value->description.' ('.$value->company_code.')',
                'children'  => $child
            ];
        }

        // $result = MasterUser::get_branch($id_company);
        return response()->json($result);
    }

    public function get_user_responsibility(Request $request) {
        $data = [
            'id_user' => $request->id_user
        ];
        $result = MasterUser::get_user_responsibility($data);
        return response()->json($result);
    }
    public function get_default_access(Request $request) {
        $data = [
            'code_default' => $request->code_default,
            'id_company' => $request->id_company
        ];
        $result = MasterUser::get_default_access($data);
        return response()->json($result);
    }
    public function get_default_access_edit(Request $request) {
        $data = [
            'id_user' => $request->id_user,
            'id_company' => $request->id_company
        ];
        $result = MasterUser::get_default_access_edit($data);
        return response()->json($result);
    }

    public function role(Request $request) {
        $param = $request->url;
        $collection = collect(session('user_right'));
        $result = $collection->where('address_menu', $param);
        $data = $result->toArray();
        $arr = array_values($data);
        foreach($arr as $i => $employeeAccess) {
            unset($arr[$i]->password);
        }
        return response()->json($arr);
    }

    public function employeeAll($showNik=null){
        $default_company = null;
        if(session('company_type') == 'os'){
            $getUser = DB::table('master_users as mu')->select('mu.default_company')->where('mu.user_name', session('username'))->first();
            $default_company = $getUser->default_company;
            if($default_company == session('id_company')){
                $getOther = DB::table('hr_employee as he')
                        ->join('master_users as mu', 'mu.user_name', '=', 'he.nik_employee')
                        ->select("mu.default_company")
                        ->where('mu.default_company', '!=', session('id_company'))
                        ->where('he.id_employee', session('id_company'))
                        ->limit(2)
                        ->get();
                if($getOther){
                    $default_company = $getOther->pluck('default_company')->unique();
                }
            }
        }
        $department     = WorkDays::getdepartment($default_company);
        $get_employee   = WorkDays::getemployeename()->unique(['id_user'])->values()->all();
        $employee       = [];
        
        foreach ($get_employee as $k => $item) {
            if($showNik){
                $desc = $item->name.' ('.$item->nik_employee.')';
            } else {
                $desc = $item->name;
            }
            $employee[] = [
                'id'        => $item->id_user,
                'text'      => $desc,
            ];
        }

        return json_encode($employee);
    }

    public function set_menu($id_company) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try{
            $listNIK = [];
            $getUser = DB::table('hr_employee as he')
                    ->join('master_users as mu', 'he.id_user', '=', 'mu.id_user')
                    ->select('he.id_user', 'he.id_employee', 'mu.user_name', 'he.nik_employee')
                    ->where('mu.access_group', null)
                    ->where('mu.access_group', '=', 'Default_User')
                    ->where('he.id_company', '=', $id_company)
                    ->where('mu.user_name', '!=', 'admin')
                    // ->whereIn('he.nik_employee', $listNIK)
                    ->limit(500)
                    ->get();
            if(count($getUser) < 1){
                return true;
            }
            $user           = [];
            $access_group   = 'Default_User';
            $sql = "SELECT sfdmu.id_responsibility_menu, sfdmu.id_menu, sfdmu.id_responsibility, sfdmu.menu_name, mc.id_company
                    FROM sp_funct_default_menu_user(?) sfdmu
                    CROSS JOIN master_company mc
                    WHERE mc.id_company IN (".$id_company.")";
            //$default_menu   = DB::select($sql, [ $access_group ]);

            foreach ($getUser as $key => $val) {
                // $mur = DB::table('master_user_responsibility as mur')
                //     ->select('mur.id_menu', 'mur.id_user', 'mur.sequence')
                //     ->where('mur.id_user', $val->id_user)
                //     ->get();

                // $id_user_resp   = $val->id_user;

                // $responsibility_data = [
                //     'id_user'    => $id_user_resp,
                //     'start_date' => date('Y-m-d'),
                //     'can_create' => 1,
                //     'can_update' => 1,
                //     'can_delete' => 0,
                //     'can_print'  => 1,
                //     'id_company' => $id_company,
                //     'created_by' => session('id_user'),
                // ];

                // if(count($mur) > 0){
                //     $id_menu_mur    = $mur->pluck('id_menu')->all();
                //     $sequence_mur   = max($mur->pluck('sequence')->all());

                //     foreach ($default_menu as $item) {
                //         if(!in_array($item->id_menu, $id_menu_mur)){
                //             $sequence_mur++;
                //             $responsibility_data['id_menu']             = $item->id_menu;
                //             $responsibility_data['id_responsibility']   = $item->id_responsibility;
                //             $responsibility_data['id_responsibility_menu'] = $item->id_responsibility_menu;
                //             $responsibility_data['sequence']            = $sequence_mur;
                //             $responsibility_data['description_name']    = $item->menu_name;
                //             if(!MasterUserResponsibility::create($responsibility_data)){
                //                 throw new \Exception('Insert user responsibility failed');
                //             }
                //         }
                //     }
                // } else {
                    // MasterUserResponsibility::where('id_user', $id_user_resp)->delete();
                    // $sequence = 1;
                    // foreach ($default_menu as $item) {
                    //     $responsibility_data['id_menu']             = $item->id_menu;
                    //     $responsibility_data['id_responsibility']   = $item->id_responsibility;
                    //     $responsibility_data['id_responsibility_menu'] = $item->id_responsibility_menu;
                    //     $responsibility_data['sequence']            = $sequence;
                    //     $responsibility_data['description_name']    = $item->menu_name;
                    //     if(!MasterUserResponsibility::create($responsibility_data)){
                    //         throw new \Exception('Insert user responsibility failed');
                    //     }
                    //     $sequence++;
                    // }
                //}

                $m_user = [
                    // 'user_name'     => $val->nik_employee,
                    // 'password'      => Hash::make($val->nik_employee),
                    // 'access_group'  => $access_group,
                    'updated_by'    => session('id_user'),
                    'update_date'  => date('Y-m-d H:i:s'),
                ];

                $mu_pass = DB::table('master_users as mu')
                    ->where('mu.user_name', $val->nik_employee)
                    ->update($m_user);

                // MasterUser::findOrFail($id_user_resp)->update($m_user);
                // RelationCompanyUser::where('id_user', $id_user_resp)->delete();
                // $data_relation = [
                //     'id_user' => $id_user_resp,
                //     'id_company' => $id_company,
                //     'created_by' => session('id_user'),
                //     'updated_by' => session('id_user'),
                // ];
                // RelationCompanyUser::create($data_relation);

                $user[] = $val->user_name;
            }

            $return['user'] = $user;
            DB::commit();
            return $return;
        } catch (\Exception $e) {
            DB::rollBack();
            $return['err'] = $e->getMessage();
            return $return;
        }
    }

    public function assignMenu(Request $req)
    {
        ini_set('max_execution_time', -1);
        try{
            $menu           = $req->menu ?? null;
            $action_by      = $req->action_by ?? null;
            $access_group   = $req->access_group ?? null;
            $grade          = $req->grade ?? null;
            $idCompany      = $req->company ?? [session('id_company')];
            $except_user    = $req->except_user ?? null;
            $assign_user    = $req->assign_user ?? null;
            $action         = $req->action ?? null;
            $idUser         = [];
            
            if($action){
                foreach($idCompany as $company) {
                    if($action_by == 'user'){
                        $idUser = $assign_user;
                        MasterUserResponsibility::insertOrDeleteMenu($menu, $idUser, $action, $company);
                    } else if($action_by == 'access_group_without_grade'){
                        $getUsers = MasterUserResponsibility::getUserByAccessGroupOrGrade($access_group, $grade, $except_user, $idCompany, false);
                        if($getUsers->count() > 0){
                            $idUser = $getUsers->pluck('id_user')->all();
                            MasterUserResponsibility::insertOrDeleteMenu($menu, $idUser, $action, $company);
                        }
                    } else {
                        $getUsers = MasterUserResponsibility::getUserByAccessGroupOrGrade($access_group, $grade, $except_user, $idCompany);
                        if($getUsers->count() > 0){
                            $idUser = $getUsers->pluck('id_user')->all();
                            MasterUserResponsibility::insertOrDeleteMenu($menu, $idUser, $action, $company);
                        }
                    }
                }
            }
            DB::commit();   
            return response(['status' => 'true', 'message' => ucwords($action).' menu for '.count($idUser).' User Success', 'data' => $menu]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function getUserByCompany(Request $req) {
        $idCompany = $req->id_company ?? [session('id_company')];

        $getUser = DB::table('master_users as mu')
            ->join('hr_employee as he', 'he.id_user', '=', 'mu.id_user')
            ->select('mu.id_user', 'mu.user_name', 'he.name', 'he.nik_employee', 'he.status')
            ->whereIn('mu.default_company', $idCompany)
            ->where('mu.user_name', '!=', 'admin')
            ->orderBy('he.name')
            ->get();

        return response()->json($getUser);
    }
}
