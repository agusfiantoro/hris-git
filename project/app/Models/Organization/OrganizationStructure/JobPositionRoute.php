<?php

namespace App\Models\Organization\OrganizationStructure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobPositionRoute extends Model {

    public static function get_data() {
        $id_company = session()->get('id_company');
        $sql = "SELECT  
                    id_routing,
                    description,
                    job_description_detail,
                    parent_id_routing,
                    parent_job_route,
                    id_job_grade,
                    id_job_status,
                    job_status,
                    expected_employee,
                    existing_employee,
                    total_forecast_employee,
                    status,
                    id_position,
                    job_position,
                    id_company,
                    id_dept,
                    department_name,
                    company_name
                FROM  sp_funct_job_position_route_list_view() 
				WHERE id_company = ? order by description";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_job_position() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_position id,
                        description text,
						parent_id_position,
                        id_dept
                FROM  master_job_position 
				WHERE status = 'A' 
				AND id_company = ?
                ORDER BY description";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_parent_job() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        mpr.id_routing id,
                        mpr.description text,
                        mjp.description position
                FROM  master_position_routing as mpr
                LEFT JOIN master_job_position as mjp ON mpr.id_position = mjp.id_position
				WHERE mpr.status = 'A' 
				AND mpr.id_company = ?
                ORDER BY mpr.description";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_grade() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_job_grade id,
                        description text
                FROM  master_job_grade 
				WHERE status = 'A' 
				AND id_company = ?
                ORDER BY description";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_job_status() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_job_status id,
                        description text
                FROM  master_job_status 
				WHERE status = 'A' 
				AND id_company = ?
                ORDER BY description";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_company() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company 
				WHERE id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }
	
	public static function get_assign_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company ";
        $result = DB::select($sql);
        return $result;
    }

    public static function get_department($data) {
        $id_company = session()->get('id_company');
        $id_dept = $data['id_dept'];
        $sql = "SELECT 
                        id_dept id,
                        description text
                FROM  master_department 
				WHERE status = 'A' 
				AND id_dept = ?
				AND id_company = ?";
        $result = DB::select($sql, [$id_dept, $id_company]);

        return $result;
    }

    public static function get_default_cost_sharing() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_cost_sharing id,
                        cost_sharing_code text
                FROM  master_cost_sharing 
				WHERE status = 'A'
				AND id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_location() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_location id,
                        description text
                FROM  master_location 
				WHERE id_company = ?
                order by description";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_branch_operating_unit() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_branch id,
                        description text
                FROM  master_branch 
				WHERE status = 'A' 
				AND id_company = ?
                order by description";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_principal() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_principal id,
                        description text
                FROM  master_principal 
				WHERE status = 'A'
				AND id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }
	
	/*
    public static function get_work_arround() {
        $id_company = session()->get('id_company');
        $sql = "select mb.id_branch, mb.description as desc_branch, ml.id_location, ml.description as desc_location
					from master_branch mb
					left join master_location ml
					on mb.id_branch = ml.id_branch
				where ml.status = 'A' and ml.id_company = ?
				order by mb.id_branch ASC";
        $result_list = DB::select($sql, [$id_company]);
		$result = array();
        foreach ($result_list as $key=>$value) {
			 $result[$value->id_branch -1]['id'] = $value->id_branch;
			 $result[$value->id_branch -1]['text'] = $value->desc_branch;		
			 $result[$value->id_branch -1]['children'][] = [
				'id' => $value->id_location,
				'text' => $value->desc_location,
			 ];			 
		}
        return $result;
    }
*/
    public static function get_cost_sharing() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_cost_sharing id,
                        cost_sharing_code text
                FROM  master_cost_sharing 
				WHERE status = 'A'
				AND id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_employee_vacant($assignedIdCompany, $id_employee) {
        $id_company = $assignedIdCompany ?? session('id_company');
        $getPosDetail = DB::table('master_position_detail as mpd')
                        ->select('mpd.id_employee')
                        ->where(function ($query) use ($id_company) {
                            $query->where('mpd.id_company', '=', $id_company);
                            $query->orWhere('mpd.assigned_to_company', '=', $id_company);
                        })
                        ->where('mpd.id_employee', '!=', null);
        if($id_employee != '' || !is_null($id_employee)){
            $getPosDetail->where('mpd.id_employee', '!=', $id_employee);
        }
        $getPosDetail->get()->pluck('id_employee')->all();
                        
        $getEmployee = DB::table('hr_employee as he')
                ->select('he.id_employee as id', 'he.name as text')
                ->whereNotIn('he.id_employee', $getPosDetail)
                ->where('he.id_company', $id_company)
                ->where('he.status', 'A')
                ->orderBy('he.name');  

        $result = $getEmployee->get();
        return $result;
    }

    public static function get_employee($assignedIdCompany) {
        $id_company = $assignedIdCompany ?? session('id_company');
        $getEmployee = DB::table('hr_employee as he')
                ->select('he.id_employee as id', 'he.name as text')
                ->where('he.id_company', $id_company)
                ->where('he.status', 'A')
                ->orderBy('he.name');  
        $result = $getEmployee->get();
        return $result;
    }

    public static function get_superior_position() {
        $id_company = session()->get('id_company');
    //    $id = $data['id'];
    /*    $sql = "SELECT distinct
                    mpd2.id_position_detail id,
                    mpd2.description text
                FROM master_position_detail mpd
                JOIN  master_position_routing mpr
                  ON mpd.id_position_routing = mpr.id_routing
                JOIN master_position_detail mpd2
				ON mpr.parent_id_routing = mpd2.id_position_routing
                WHERE mpd2.id_position_routing = COALESCE(?, mpd2.id_position_routing)
				AND	mpd2.id_company = ?";
        $result = DB::select($sql, [$id, $id_company]);
	*/
    //    if (count($result) == 0) {
       /*     $sql = "SELECT distinct
                    mpd.id_position_detail id,
                    mpd.description text
                FROM master_position_detail mpd
                WHERE mpd.id_position_routing = COALESCE(?, mpd.id_position_routing) AND mpd.id_company = ?";
		*/
		$sql = "SELECT distinct 
                    mpd.id_position_detail id,
                    CONCAT(mpd.description,' (',he.name,')') text
				FROM master_position_detail mpd
				LEFT JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
                WHERE mpd.id_company = ?";
            $result = DB::select($sql, [$id_company]);
    //    }
        return $result;
    }

    public static function save_position_route($data) {
        $id_user = session()->get('id_user');
        $id_company = session()->get('id_company');
        $data_insert = [
            'id_position' => $data['job_position'],
            'description' => $data['job_position_route'],
            'parent_id_routing' => $data['parent_job'],
            'id_job_status' => $data['job_status'],
            'id_job_grade' => $data['grade'],
            'id_company' => $data['company'],
            'id_dept' => $data['department'],
            'default_cost_sharing' => $data['default_cost_sharing'],
            'expected_employee' => $data['expected_new_employee'],
            'existing_employee' => $data['existing_employee'],
            'status' => $data['status'],
            'job_description_detail' => $data['detail_position_route_job_description'],
            'skill_requirement' => $data['detail_position_route_skill_requirement'],
            'detail_position_route' => $data['detail_position_route'],
        ];
        try {
            DB::beginTransaction();
            $date_now = DB::select('SELECT NOW() date_now')[0]->date_now;
		//	dd($date_now);
            DB::insert('INSERT INTO  master_position_routing (
                                id_position
                                ,description
                                ,job_description_detail
                                ,skill_requirement
                                ,expected_employee
                                ,existing_employee
                                ,id_job_status
                                ,id_job_grade
                                ,default_cost_sharing
                                ,status
                                ,inactive_date
                                ,parent_id_routing
                                ,id_company
                                ,creation_date
                                ,update_date
                                ,created_by
                                ,updated_by
                            ) VALUES (
                                ? -- [id_position]
                                ,? -- [description]
                                ,? -- [job_description_detail]
                                ,? -- [skill_requirement]
                                ,? -- [expected_employee]
                                ,? -- [existing_employee]
                                ,? -- [id_job_status]
                                ,? -- [id_job_grade]
                                ,? -- [default_cost_sharing]
                                ,? -- [status]
                                ,null -- [inactive_date]
                                ,? -- [parent_id_routing]
                                ,? -- [id_company]
                                ,? -- [creation_date]
                                ,null -- [update_date]
                                ,? -- [created_by]
                                ,null -- [updated_by]
                            )',
                    [
                        $data_insert['id_position']
                        , $data_insert['description']
                        , $data_insert['job_description_detail']
                        , $data_insert['skill_requirement']
                        , $data_insert['expected_employee']
                        , $data_insert['existing_employee']
                        , $data_insert['id_job_status']
                        , $data_insert['id_job_grade']
                        , $data_insert['default_cost_sharing']
                        , $data_insert['status']
                        , $data_insert['parent_id_routing']
                        , $id_company
                        , $date_now
                        , $id_user
            ]);

            $id_routing = DB::getPdo()->lastInsertId();
		//	dd($id_routing);
            if($data_insert['detail_position_route'] && count($data_insert['detail_position_route']) > 0){
                foreach (array_keys($data_insert['detail_position_route']) as $key => $value) {
                    DB::insert('INSERT INTO master_position_detail
                                    (id_position_routing
                                    ,description
                                    ,status
                                    ,id_branch
                                    ,id_location
                                    ,id_employee
                                    ,parent_id_position_detail
                                    ,assigned_to_company
                                    ,inactive_date
                                    ,id_company
                                    ,creation_date
                                    ,update_date
                                    ,created_by
                                    ,updated_by
                            ) VALUES (
                                    ? -- <id_position_routing>
                                    ,? -- <description, nvarchar(100),>
                                    ,? -- <status, nvarchar(1),>
                                    ,? -- <id_branch, int,>
                                    ,? -- <id_location, int,>
                                    ,? -- <id_employee, int,>
                                    ,? -- <parent_id_position_detail, int,>
                                    ,? -- <assigned_to_company, int,>
                                    ,null -- <inactive_date, date,>
                                    ,? -- <id_company, int,>
                                    ,? -- <creation_date, int,>
                                    ,null -- <update_date, int,>
                                    ,? -- <created_by, int,>
                                    ,null -- <updated_by, int,>
                                )',
                            [
                                $id_routing
                                , @$data_insert['detail_position_route'][$value]['position_detail_name'] //[description]
                                , isset($data_insert['detail_position_route'][$value]['inactive']) ? 'I' : 'A' //[status]
                                , isset($data_insert['detail_position_route'][$value]['branch_operating_unit']) ? $data_insert['detail_position_route'][$value]['branch_operating_unit'] : null  //[id_branch]
                                , isset($data_insert['detail_position_route'][$value]['location']) ? $data_insert['detail_position_route'][$value]['location'] : null //[id_location]
                                , isset($data_insert['detail_position_route'][$value]['employee']) ? $data_insert['detail_position_route'][$value]['employee'] : null //[id_employee]
                                , isset($data_insert['detail_position_route'][$value]['superior_position']) ? $data_insert['detail_position_route'][$value]['superior_position'] : null //[parent_id_position_detail]
                                , isset($data_insert['detail_position_route'][$value]['assign_company']) ? $data_insert['detail_position_route'][$value]['assign_company'] : null //[assigned_to_company]
                                , $id_company //[id_company]
                                , $date_now
                                , $id_user //[created_by]
                            ]
                    );
                    $id_position_detail = DB::getPdo()->lastInsertId();

                    if (isset($data_insert['detail_position_route'][$value]['cost_sharing'])) {
                        foreach ($data_insert['detail_position_route'][$value]['cost_sharing'] as $key_cost_sharing => $value_cost_sharing) {
                            if ($value_cost_sharing != "") {
                                DB::insert('INSERT INTO relation_positiondetail_costsharing
                                    (id_position_detail
                                    ,id_cost_sharing
                                    ,prosentase
                                    ,id_company
                                    ,creation_date
                                    ,update_date
                                    ,created_by
                                    ,updated_by
                                ) VALUES (
                                        ? -- <id_position_detail, int,>
                                        ,? -- <id_cost_sharing, int,>
                                        ,? -- <prosentase, int,>
                                        ,? -- <id_company, int,>
                                        ,? -- <creation_date, datetime,>
                                        ,null -- <update_date, datetime,>
                                        ,? -- <created_by, int,>
                                        ,null --<updated_by, int,>
                                )', [
                                    $id_position_detail // <id_position_detail, int,>
                                    , $value_cost_sharing // <id_cost_sharing, int,>
                                    , 0 // <prosentase, int,>
                                    , $id_company // <id_company, int,>
                                    , $date_now // <creation_date, datetime,>
                                    , $id_user // <created_by, int,>
                                ]);
                            }
                        }
                    }

                    if (isset($data_insert['detail_position_route'][$value]['principal'])) {
                        foreach ($data_insert['detail_position_route'][$value]['principal'] as $key_principal => $value_principal) {
                            if ($value_principal != "") {
                                DB::insert('INSERT INTO relation_positiondetail_principal
                                (id_position_detail
                                ,id_principal
                                ,id_company
                                ,creation_date
                                ,update_date
                                ,created_by
                                ,updated_by
                            )VALUES (
                                ? -- <id_position_detail, int,>
                                ,? -- <id_principal, int,>
                                ,? -- <id_company, int,>
                                ,? -- <creation_date, datetime,>
                                ,null -- <update_date, datetime,>
                                ,? -- <created_by, int,>
                                ,null -- <updated_by, datetime,>
                            )', [
                                    $id_position_detail //[id_position_detail]
                                    , $value_principal //[id_principal]
                                    , $id_company //[id_company]
                                    , $date_now //[creation_date]
                                    , $id_user //[created_by]
                                ]);
                            }
                        }
                    }

                    if (isset($data_insert['detail_position_route'][$value]['work_arround'])) {
                        foreach ($data_insert['detail_position_route'][$value]['work_arround'] as $key_work_arround => $value_work_arround) {
                            if ($value_work_arround != "") {
                                DB::insert('INSERT INTO relation_positiondetail_workarround
                                (id_position_detail
                                ,id_location
                                ,id_company
                                ,creation_date
                                ,update_date
                                ,created_by
                                ,updated_by
                            ) VALUES (
                                ? -- <id_position_detail, int,>
                                ,? -- <id_location, int,>
                                ,? -- <id_company, int,>
                                ,? -- <creation_date, datetime,>
                                ,null -- <update_date, datetime,>
                                ,? -- <created_by, int,>
                                ,null -- <updated_by, int,>
                            )', [
                                    $id_position_detail//[id_position_detail]
                                    , $value_work_arround //[id_location]
                                    , $id_company //[id_company]
                                    , $date_now //[creation_date]
                                    , $id_user //[created_by]
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return [
                'status' => 'true',
                'message' => 'Job position route saved successfully !!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return [
                'status' => 'false',
                'message' => 'Cannot save job position route !! [' . $e->getMessage() . ']'
            ];
        }
    }

    public static function save_update_position_route($data) {
        $id_user = session()->get('id_user');
        $id_company = session()->get('id_company');
        $data_update = [
            'id_routing' => $data['id_routing'],
            'id_position' => $data['job_position'],
            'description' => $data['job_position_route'],
            'parent_id_routing' => $data['parent_job'],
            'id_job_status' => $data['job_status'],
            'id_job_grade' => $data['grade'],
            'id_company' => $data['company'],
            'id_dept' => $data['department'],
            'default_cost_sharing' => $data['default_cost_sharing'],
            'expected_employee' => $data['expected_new_employee'],
            'existing_employee' => $data['existing_employee'],
            'status' => $data['status'],
            'job_description_detail' => $data['detail_position_route_job_description'],
            'skill_requirement' => $data['detail_position_route_skill_requirement'],
            'detail_position_route' => $data['detail_position_route'],
            'row_id' => $data['row_id'],
        ];

        $inactivePositionDetail = [];
        $positionNameById = [];
        $activeParentPosition = [];

        if($data_update['detail_position_route'] && count($data_update['detail_position_route']) > 0){
            foreach ($data_update['detail_position_route'] as $k => $val) {
                if(array_key_exists('inactive', $val)){
                    $inactivePositionDetail[] = $val['id_position_detail'];
                    $positionNameById[$val['id_position_detail']] = $val['position_detail_name'];
                }
            }
        }
        if(count($inactivePositionDetail) > 0){
            foreach ($inactivePositionDetail as $k => $val) {
                $cekPositionDetail = DB::table('master_position_detail')->where('parent_id_position_detail', $val)->get();
                if(in_array('A', $cekPositionDetail->pluck('status')->all())){
                    $activeParentPosition[] = $positionNameById[$val];
                }
            }
        }
        if(count($activeParentPosition) > 0){
            return [
                'status' => 'false',
                'message' => "Cannot Inactive. This Position detail \n".implode('<br>', $activeParentPosition)."\n has active child position."
            ];
        }
        
        try {
            DB::beginTransaction();
            $date_now = DB::select('SELECT NOW() date_now')[0]->date_now;
            DB::update('UPDATE master_position_routing
                            SET id_position = ?
                               ,description = ?
                               ,job_description_detail = ?
                               ,skill_requirement = ?
                               ,expected_employee = ?
                               ,existing_employee = ?
                               ,id_job_status = ?
                               ,id_job_grade = ?
                               ,default_cost_sharing = ?
                               ,status = ?
                               ,parent_id_routing = ?
                               ,id_company = ?
                               ,update_date = ?
                               ,updated_by = ?
                          WHERE id_routing = ?',
                    [
                        $data_update['id_position']
                        , $data_update['description']
                        , $data_update['job_description_detail']
                        , $data_update['skill_requirement']
                        , $data_update['expected_employee']
                        , $data_update['existing_employee']
                        , $data_update['id_job_status']
                        , $data_update['id_job_grade']
                        , $data_update['default_cost_sharing']
                        , $data_update['status']
                        , $data_update['parent_id_routing']
                        , $id_company
                        , $date_now
                        , $id_user
                        , $data_update['id_routing']
            ]);

            $id_routing = $data_update['id_routing'];
            $rowClick = [];

            if($data_update['detail_position_route'] && count($data_update['detail_position_route']) > 0){
                $collect_detail_position_route = collect($data_update['detail_position_route'])->groupBy('id_position_detail')->toArray();
                $list_id_position_detail = array_filter(array_keys($collect_detail_position_route));

                $str_id_position_detail = implode(",", $list_id_position_detail);
                if($str_id_position_detail != ''){
                    DB::delete("DELETE FROM  relation_positiondetail_workarround rpw
                        USING  master_position_detail mpd
                        WHERE mpd.id_position_detail = rpw.id_position_detail AND mpd.id_position_routing = ? AND rpw.id_position_detail NOT IN (" . $str_id_position_detail . ")", [$id_routing]);

                    DB::delete("DELETE FROM  relation_positiondetail_principal rpp
                        USING  master_position_detail mpd
                        WHERE mpd.id_position_detail = rpp.id_position_detail AND mpd.id_position_routing = ? AND rpp.id_position_detail NOT IN (" . $str_id_position_detail . ")", [$id_routing]);

                    DB::delete("DELETE FROM  relation_positiondetail_costsharing rpc 
                        USING  master_position_detail mpd
                        WHERE mpd.id_position_detail = rpc.id_position_detail AND mpd.id_position_routing = ? AND rpc.id_position_detail NOT IN (" . $str_id_position_detail . ")", [$id_routing]);

                    DB::delete("DELETE FROM  master_position_detail mpd 
                                    WHERE mpd.id_position_routing = ? AND mpd.id_position_detail NOT IN (" . $str_id_position_detail . ")", [$id_routing]);
                }

                $getExistingEmployee = [];
                $thisPosition = [];

                foreach (array_keys(@$data_update['detail_position_route']) as $key => $value) {
                    $idPositionRouting = @$data_update['detail_position_route'][$value]['id_position_routing'];
                    $idEmployeeThisPosition = @$data_update['detail_position_route'][$value]['employee'];
                    if(!in_array($idPositionRouting, $thisPosition)){
                        $thisPosition[] = $idPositionRouting;
                        $getExistingEmployee[$idPositionRouting][] = $idEmployeeThisPosition;
                    } else {
                        $getExistingEmployee[$idPositionRouting][] = $idEmployeeThisPosition;
                    }

                    if($key == @$data_update['row_id'][$key]){
                        //yang diupdate adalah row tabel detail position di kolom mana saja yg diklik user di tampilan
                        $rowClick[] = $key;

                        if (@$data_update['detail_position_route'][$value]['id_position_detail'] == "" || @$data_update['detail_position_route'][$value]['id_position_detail'] == null) {
                            DB::insert('INSERT INTO master_position_detail
                                            (id_position_routing
                                            ,description
                                            ,status
                                            ,id_branch
                                            ,id_location
                                            ,id_employee
                                            ,parent_id_position_detail
                                            ,assigned_to_company
                                            ,inactive_date
                                            ,id_company
                                            ,creation_date
                                            ,update_date
                                            ,created_by
                                            ,updated_by
                                    ) VALUES (
                                            ? -- <id_position_routing>
                                            ,? -- <description, nvarchar(100),>
                                            ,? -- <status, nvarchar(1),>
                                            ,? -- <id_branch, int,>
                                            ,? -- <id_location, int,>
                                            ,? -- <id_employee, int,>
                                            ,? -- <parent_id_position_detail, int,>
                                            ,? -- <assigned_to_company, int,>
                                            ,null -- <inactive_date, date,>
                                            ,? -- <id_company, int,>
                                            ,? -- <creation_date, int,>
                                            ,? -- <update_date, int,>
                                            ,? -- <created_by, int,>
                                            ,? -- <updated_by, int,>
                                        )',
                                    [
                                        @$data_update['detail_position_route'][$value]['id_position_routing'] 
                                        , @$data_update['detail_position_route'][$value]['position_detail_name'] //[description]
                                        , isset($data_update['detail_position_route'][$value]['inactive']) ? 'I' : 'A' //[status]
                                        , isset($data_update['detail_position_route'][$value]['branch_operating_unit']) ? $data_update['detail_position_route'][$value]['branch_operating_unit'] : null //[id_branch]
                                        , isset($data_update['detail_position_route'][$value]['location']) ? $data_update['detail_position_route'][$value]['location'] : null //[id_location]
                                        , isset($data_update['detail_position_route'][$value]['employee']) ? $data_update['detail_position_route'][$value]['employee'] : null //[id_employee]
                                        , isset($data_update['detail_position_route'][$value]['superior_position']) ? $data_update['detail_position_route'][$value]['superior_position'] : null //[parent_id_position_detail]
                                        , isset($data_update['detail_position_route'][$value]['assign_company']) ? $data_update['detail_position_route'][$value]['assign_company'] : null //[assigned_to_company]
                                        , $id_company //[id_company]
                                        , $date_now // [creation_date]
                                        , $date_now // [update_date]
                                        , $id_user // [created_by]
                                        , $id_user // [updated_by]
                                    ]
                            );
                            $id_position_detail = DB::getPdo()->lastInsertId();

                            if (isset($data_update['detail_position_route'][$value]['cost_sharing'])) {
                                foreach ($data_update['detail_position_route'][$value]['cost_sharing'] as $key_cost_sharing => $value_cost_sharing) {
                                    if ($value_cost_sharing != "") {
                                        DB::insert('INSERT INTO relation_positiondetail_costsharing
                                        (id_position_detail
                                        ,id_cost_sharing
                                        ,prosentase
                                        ,id_company
                                        ,creation_date
                                        ,update_date
                                        ,created_by
                                        ,updated_by
                                    ) VALUES (
                                            ? -- <id_position_detail, int,>
                                            ,? -- <id_cost_sharing, int,>
                                            ,? -- <prosentase, int,>
                                            ,? -- <id_company, int,>
                                            ,? -- <creation_date, datetime,>
                                            ,? -- <update_date, datetime,>
                                            ,? -- <created_by, int,>
                                            ,? -- <updated_by, int,>
                                    )', [
                                            $id_position_detail // <id_position_detail, int,>
                                            , $value_cost_sharing // <id_principal, int,>
                                            , 0 // <prosentase, int,>
                                            , $id_company // <id_company, int,>
                                            , $date_now // <creation_date, datetime,>
                                            , $date_now // <update_date, datetime,>
                                            , $id_user // <created_by, int,>
                                            , $id_user // <updated_by, int,>
                                        ]);
                                    }
                                }
                            }

                            if (isset($data_update['detail_position_route'][$value]['principal'])) {
                                foreach ($data_update['detail_position_route'][$value]['principal'] as $key_principal => $value_principal) {
                                    if ($value_principal != "") {
                                        DB::insert('INSERT INTO relation_positiondetail_principal
                                        (id_position_detail
                                        ,id_principal
                                        ,id_company
                                        ,creation_date
                                        ,update_date
                                        ,created_by
                                        ,updated_by
                                    )VALUES (
                                        ? -- <id_position_detail, int,>
                                        ,? -- <id_principal, int,>
                                        ,? -- <id_company, int,>
                                        ,? -- <creation_date, datetime,>
                                        ,? -- <update_date, datetime,>
                                        ,? -- <created_by, int,>
                                        ,? -- <updated_by, datetime,>
                                    )', [
                                            $id_position_detail //[id_position_detail]
                                            , $value_principal //[id_principal]
                                            , $id_company //[id_company]
                                            , $date_now //[creation_date]
                                            , $date_now //[update_date]
                                            , $id_user //[created_by]
                                            , $id_user //[updated_by]
                                        ]);
                                    }
                                }
                            }

                            if (isset($data_update['detail_position_route'][$value]['work_arround'])) {
                                foreach ($data_update['detail_position_route'][$value]['work_arround'] as $key_work_arround => $value_work_arround) {
                                    if ($value_work_arround != "") {
                                        DB::insert('INSERT INTO relation_positiondetail_workarround
                                        (id_position_detail
                                        ,id_location
                                        ,id_company
                                        ,creation_date
                                        ,update_date
                                        ,created_by
                                        ,updated_by
                                    ) VALUES (
                                        ? -- <id_position_detail, int,>
                                        ,? -- <id_location, int,>
                                        ,? -- <id_company, int,>
                                        ,? -- <creation_date, datetime,>
                                        ,? -- <update_date, datetime,>
                                        ,? -- <created_by, int,>
                                        ,? -- <updated_by, int,>
                                    )', [
                                            $id_position_detail//[id_position_detail]
                                            , $value_work_arround //[id_location]
                                            , $id_company //[id_company]
                                            , $date_now //[creation_date]
                                            , $date_now //[update_date]
                                            , $id_user //[created_by]
                                            , $id_user //[updated_by]
                                        ]);
                                    }
                                }
                            }
                        } else {
                            $thisData = $data_update['detail_position_route'][$value];
                            $id_position_detail = $thisData['id_position_detail'];
                            $mpd = [
                                'id_position_routing'   => @$thisData['id_position_routing'],
                                'description'   => @$thisData['position_detail_name'], 
                                'status'   => isset($thisData['inactive']) ? 'I' : 'A' ,
                                'id_branch'   => @$thisData['branch_operating_unit'] ?? null,
                                'id_location'   => @$thisData['location'] ?? null,
                                'id_employee'   => @$thisData['employee'] ?? null,
                                'parent_id_position_detail'   => @$thisData['superior_position'] ?? null,
                                'assigned_to_company'   => @$thisData['assign_company'] ?? null,
                                'inactive_date'   => null,
                                'id_company'   => $id_company,
                                // 'creation_date'   => $date_now,
                                'update_date'   => $date_now,
                                // 'created_by'   => $id_user,
                                'updated_by'   => $id_user,
                            ];
                            if(is_null(@$thisData['employee'])){
                                //jika id employee null maka tidak akan diupdate
                                unset($mpd['id_employee']);
                            }
                            //assign_to_company dan id_employee tidak diupdate karena agr tidak bentrok dgn career execute
                            $updateMpd = DB::table('master_position_detail as mpd')
                                    ->where('mpd.id_position_detail', $id_position_detail)->update($mpd);


                            DB::delete('DELETE FROM  relation_positiondetail_workarround rpw
                            WHERE rpw.id_position_detail = ?', [$id_position_detail]);

                            DB::delete('DELETE FROM  relation_positiondetail_principal rpp
                            WHERE rpp.id_position_detail = ?', [$id_position_detail]);

                            DB::delete('DELETE FROM  relation_positiondetail_costsharing rpc
                            WHERE rpc.id_position_detail = ?', [$id_position_detail]);

                            if (isset($data_update['detail_position_route'][$value]['cost_sharing'])) {
                                foreach ($data_update['detail_position_route'][$value]['cost_sharing'] as $key_cost_sharing => $value_cost_sharing) {
                                    if ($value_cost_sharing != "") {
                                        DB::insert('INSERT INTO relation_positiondetail_costsharing
                                        (id_position_detail
                                        ,id_cost_sharing
                                        ,prosentase
                                        ,id_company
                                        ,creation_date
                                        ,update_date
                                        ,created_by
                                        ,updated_by
                                    ) VALUES (
                                            ? -- <id_position_detail, int,>
                                            ,? -- <id_cost_sharing, int,>
                                            ,? -- <prosentase, int,>
                                            ,? -- <id_company, int,>
                                            ,? -- <creation_date, datetime,>
                                            ,? -- <update_date, datetime,>
                                            ,? -- <created_by, int,>
                                            ,? -- <updated_by, int,>
                                    )', [
                                            $id_position_detail // <id_position_detail, int,>
                                            , $value_cost_sharing // <id_cost_sharing, int,>
                                            , 0 // <prosentase, int,>
                                            , $id_company // <id_company, int,>
                                            , $date_now // <creation_date, datetime,>
                                            , $date_now // <update_date, datetime,>
                                            , $id_user // <created_by, int,>
                                            , $id_user // <updated_by, int,>
                                        ]);
                                    }
                                }
                            }

                            if (isset($data_update['detail_position_route'][$value]['principal'])) {
                                foreach ($data_update['detail_position_route'][$value]['principal'] as $key_principal => $value_principal) {
                                    if ($value_principal != "") {
                                        DB::insert('INSERT INTO relation_positiondetail_principal
                                        (id_position_detail
                                        ,id_principal
                                        ,id_company
                                        ,creation_date
                                        ,update_date
                                        ,created_by
                                        ,updated_by
                                    )VALUES (
                                        ? -- <id_position_detail, int,>
                                        ,? -- <id_principal, int,>
                                        ,? -- <id_company, int,>
                                        ,? -- <creation_date, datetime,>
                                        ,? -- <update_date, datetime,>
                                        ,? -- <created_by, int,>
                                        ,? -- <updated_by, datetime,>
                                    )', [
                                            $id_position_detail //[id_position_detail]
                                            , $value_principal //[id_principal]
                                            , $id_company //[id_company]
                                            , $date_now //[creation_date]
                                            , $date_now //[update_date]
                                            , $id_user //[created_by]
                                            , $id_user //[updated_by]
                                        ]);
                                    }
                                }
                            }

                            if (isset($data_update['detail_position_route'][$value]['work_arround'])) {
                                foreach ($data_update['detail_position_route'][$value]['work_arround'] as $key_work_arround => $value_work_arround) {
                                    if ($value_work_arround != "") {
                                        DB::insert('INSERT INTO relation_positiondetail_workarround
                                        (id_position_detail
                                        ,id_location
                                        ,id_company
                                        ,creation_date
                                        ,update_date
                                        ,created_by
                                        ,updated_by
                                    ) VALUES (
                                        ? -- <id_position_detail, int,>
                                        ,? -- <id_location, int,>
                                        ,? -- <id_company, int,>
                                        ,? -- <creation_date, datetime,>
                                        ,? -- <update_date, datetime,>
                                        ,? -- <created_by, int,>
                                        ,? -- <updated_by, int,>
                                    )', [
                                            $id_position_detail//[id_position_detail]
                                            , $value_work_arround //[id_location]
                                            , $id_company //[id_company]
                                            , $date_now //[creation_date]
                                            , $date_now //[update_date]
                                            , $id_user //[created_by]
                                            , $id_user //[updated_by]
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                if(count($getExistingEmployee) > 0){
                    //utk menghitung existing dan expected employee yang ada perpindahan position detail ke routing lain
                    $updateExisting = [];
                    foreach ($getExistingEmployee as $key => $value) {
                        $exist = [];
                        foreach ($value as $k => $item) {
                            if(!is_null($item)){ $exist[] = 1; }
                        }

                        $getRouting = DB::table('master_position_routing')->where('id_routing', $key)->first();
                        if($id_routing == $key){
                            $expected_employee = count($value);
                            $existing_employee = count($exist);
                        } else {
                            $expected_employee = count($value) + $getRouting->expected_employee;
                            $existing_employee = count($exist) + $getRouting->existing_employee;
                        }
                        $edit = [
                            'expected_employee' => $expected_employee,
                            'existing_employee' => $existing_employee,
                        ];
                        $updateRouting = DB::table('master_position_routing')->where('id_routing', $key)->update($edit);
                    }
                }
            }
            DB::commit();
            return [
                'status' => 'true',
                'message' => 'Job position route updated successfully !!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return [
                'status' => 'false',
                'message' => 'Cannot update job position route !! [' . $e->getMessage() . ']'
            ];
        }
    }

    public static function get_detail_position_route($data) {
        $result = [];
        $sql = "SELECT 
                    id_routing,
                    id_position,
                    description,
                    job_description_detail,
                    skill_requirement,
                    expected_employee,
                    existing_employee,
                    id_job_status,
                    id_job_grade,
                    default_cost_sharing,
                    status,
                    inactive_date,
                    parent_id_routing,
                    id_company
                FROM master_position_routing WHERE id_routing  = ?";
        $result = (Array) DB::select($sql, [$data['id_routing']])[0];

        $sql = "SELECT sfjpdlv.*, mpd.assigned_to_company
				FROM sp_funct_job_position_detail_list_view() sfjpdlv
				LEFT JOIN master_position_detail mpd
				ON sfjpdlv.id_position_detail = mpd.id_position_detail WHERE sfjpdlv.id_position_routing  = ? order by mpd.id_position_detail";
        $result_detail_position_route = DB::select($sql, [$data['id_routing']]);
        $collect_detail_position_route = collect($result_detail_position_route);
        $group_detail_position_route = $collect_detail_position_route->groupBy('id_position_detail')->toArray();

        $result['detail_position_route'] = [];
        foreach (array_keys($group_detail_position_route) as $key => $value) {
            $group_cost_sharing = collect($group_detail_position_route[$value])->groupBy('id_cost_sharing')->toArray();
            $cost_sharing = array_keys($group_cost_sharing);
            $group_principal = collect($group_detail_position_route[$value])->groupBy('detail_id_principal')->toArray();
            $principal = array_keys($group_principal);
            $group_work_arround = collect($group_detail_position_route[$value])->groupBy('work_arround_id_location')->toArray();
            $work_arround = array_keys($group_work_arround);
            $result['detail_position_route'][] = [
                'id_position_routing' => $group_detail_position_route[$value][0]->id_position_routing,
                'id_position_detail' => $group_detail_position_route[$value][0]->id_position_detail,
                'position_detail_name' => $group_detail_position_route[$value][0]->description,
                'inactive' => $group_detail_position_route[$value][0]->status,
                'branch_operating_unit' => $group_detail_position_route[$value][0]->id_branch,
                'location' => $group_detail_position_route[$value][0]->id_location,
                'employee' => $group_detail_position_route[$value][0]->id_employee,
                'superior_position' => $group_detail_position_route[$value][0]->parent_id_position_detail,
                'assign_company' => $group_detail_position_route[$value][0]->assigned_to_company,
                'cost_sharing' => $cost_sharing,
                'principal' => $principal,
                'work_arround' => $work_arround,
            ];
        }

        return $result;
    }

    public static function destroy_position_route($data) {
        try {
            DB::beginTransaction();
            DB::delete('DELETE FROM  relation_positiondetail_workarround rpw
                USING  master_position_detail mpd
                WHERE mpd.id_position_detail = rpw.id_position_detail AND mpd.id_position_routing = ?', [$data['id_routing']]);

            DB::delete('DELETE FROM  relation_positiondetail_principal rpp
                USING  master_position_detail mpd
                WHERE mpd.id_position_detail = rpp.id_position_detail AND mpd.id_position_routing = ?', [$data['id_routing']]);

            DB::delete('DELETE FROM  relation_positiondetail_costsharing rpc 
                USING  master_position_detail mpd
                WHERE mpd.id_position_detail = rpc.id_position_detail AND mpd.id_position_routing = ?', [$data['id_routing']]);

            DB::delete('DELETE FROM  master_position_detail WHERE id_position_routing = ?', [$data['id_routing']]);
            DB::delete('DELETE FROM  master_position_routing WHERE id_routing = ?', [$data['id_routing']]);
            DB::commit();
            return [
                'status' => 'true',
                'message' => 'Job position route deleted successfully !!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return [
                'status' => 'false',
                'message' => 'Cannot delete job position route !! [' . $e->getMessage() . ']'
            ];
        }
    }

}
