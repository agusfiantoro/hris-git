<?php

namespace App\Http\Controllers\Organization\OrganizationStructure;

use App\Models\Organization\OrganizationStructure\OrganizationHierarchy;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use DB;

class OrganizationHierarchyController extends Controller {
	
/*	function makeNested($sou) {
		$keys = array_column($sou, 'sequence');
		array_multisort($keys, SORT_ASC, $sou);
			$nested = array();	
			$tree= array();	
            foreach ($sou as $id=>&$s) {
				$nested[$s['id']] = &$s;				
                if (is_null($s['child_id'])) {
					$tree = &$s;
                } 
				else {
					$nested[$s['child_id']]['children'][] = &$s;
                }
            }		
            return $tree;
    }
*/
	function makeNested($sou, $parentId = null) {
		$branch = array();
		foreach ($sou as $element) {
			if ($element['child_id'] == $parentId) {
				$children = self::makeNested($sou, $element['id']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
    }
	
    public function index() {
		$emp = "SELECT he.* FROM hr_employee he
				WHERE he.id_user = ".session('id_user')." AND he.id_company = ".session('id_company');
		$res = DB::select($emp);
		if(session('id_user') != 1 && count($res) > 0){
			$query = "SELECT mjp.id_dept 
						FROM master_position_detail mpd
					LEFT JOIN master_position_routing mpr
					ON mpd.id_position_routing = mpr.id_routing
					LEFT JOIN master_job_position mjp
					ON mpr.id_position = mjp.id_position
					LEFT JOIN hr_employee he
					ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee) AND he.status = 'A'
					WHERE he.id_user = COALESCE(". session('id_user').",1)  AND he.id_company = COALESCE(". session('id_company').",1)";
			$source_query = DB::select($query)[0];
			
			$dept="SELECT mpd.id_position_detail 
						FROM master_position_detail mpd
					LEFT JOIN master_position_routing mpr
					ON mpd.id_position_routing = mpr.id_routing
					LEFT JOIN master_job_position mjp
					ON mpr.id_position = mjp.id_position
					WHERE mjp.id_dept = ".$source_query->id_dept."
					ORDER BY mpd.id_position_detail ASC
					limit 1";
			$source_dept = DB::select($dept)[0];
			$source_pos = $source_dept->id_position_detail;
		}
		
		else{
			$source_pos = 1;
		}
		
			$sql = "SELECT  mpd.id_position_detail as id,  mpr.description as name, mjg.id_job_grade AS sequence,  
						   case when row_number() over() = 1
								then null
							else mpd.parent_id_position_detail
							end as child_id,  mpd.status as collapsed,  
							mjg.job_class_group as classname, he.name as title, mpd.secondary_position, he.image_attachment
					FROM master_position_detail mpd
					INNER JOIN  master_position_routing mpr 
					  ON mpd.id_position_routing =  mpr.id_routing
					 AND mpd.id_company = mpr.id_company
					LEFT JOIN  hr_employee he 
					  ON (mpd.id_employee =  he.id_employee OR mpd.id_employee2 = he.id_employee)
					 AND (mpd.id_company = he.id_company
						 OR mpd.assigned_to_company = he.id_company)
					INNER JOIN  master_job_grade mjg 
					  ON mpr.id_job_grade =  mjg.id_job_grade
					 AND mpr.id_company = mjg.id_company
					WHERE  mpd.id_company=". session('id_company')." 
					  AND mpd.status = 'A'  
					  AND (mpd.id_position_detail = COALESCE(".$source_pos.",mpd.id_position_detail) 
					   OR mpd.parent_id_position_detail = COALESCE(".$source_pos.",mpd.id_position_detail))";
			$source = DB::select($sql);
        $sou = array();
        foreach ($source as $key=>$s) {
            if($s->image_attachment != null){
                if(strlen($s->image_attachment) > 1000){
                    $s->image_attachment = "data:image;base64,".$s->image_attachment;
                } else {
                    $urlPhoto = 'public/upload/photo/'. $s->image_attachment;
                    if (Storage::exists($urlPhoto)) {
                        $s->image_attachment = url('project/storage/app/'.$urlPhoto);
                    } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
                }
            } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
			
			$sou [$key]['id'] = $s->id;
			$sou [$key]['sequence'] = $s->sequence;
            $sou [$key]['name'] = $s->name;
            $sou [$key]['title'] = $s->title;
            $sou [$key]['child_id'] = $s->child_id;
            $sou [$key]['className'] = $s->classname;		
			$sou [$key]['image_attachment'] = $s->image_attachment;
        }
		if(count($sou) > 0){
			$so = self::makeNested($sou)[0];
		}
		else{
			$so = [];
		}

        return view('organization.organization_structure.organization_hierarchy.index', compact('so'));
    }
	
	protected function queryChart(Request $request) {
		try{
			DB::beginTransaction();
		if($request->fill == null){
			$fill = "null";
			return $fill;
		}
		else{
			$fill = $request->fill;
		
		  $sql_a = "SELECT  mpd.id_position_detail as id,  mpr.description as name, mjg.id_job_grade AS sequence,  
                          mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                          mjg.job_class_group as classname,  he.name as title, mpd.secondary_position, he.image_attachment
                  FROM master_position_detail mpd
                  INNER JOIN  master_position_routing mpr 
                    ON mpd.id_position_routing =  mpr.id_routing
                   AND mpd.id_company = mpr.id_company
                  LEFT JOIN  hr_employee he 
                    ON (mpd.id_employee =  he.id_employee OR mpd.id_employee2 = he.id_employee)
                   AND (mpd.id_company = he.id_company
                        OR mpd.assigned_to_company = he.id_company)
                  INNER JOIN  master_job_grade mjg 
                    ON mpr.id_job_grade =  mjg.id_job_grade
                   AND mpr.id_company = mjg.id_company                 
                   WHERE  mpd.id_company=". session('id_company')." 
                     AND mpd.status = 'A'  
                     AND mpd.id_position_detail = COALESCE(".$fill.",mpd.id_position_detail)  AND mpd.status = 'A'
                      OR mpd.parent_id_position_detail = COALESCE(".$fill.",mpd.id_position_detail)  AND mpd.status = 'A'";
        $source_a = DB::select($sql_a);
		
		foreach($source_a as $sc_a){
			if($sc_a->id != $fill){
				$x_a[] = $sc_a->id;		
			}
			else{
				$x_a = [];
			}
		}
	//	dd($x_a);
		if(count($x_a) > 0){
			$count_a = "OR mpd.parent_id_position_detail IN(".(implode(',',$x_a)).") AND mpd.status = 'A' ";
		}
		else{
			$count_a = "";
		}
	//	dd($x_a);
		 $sql = "SELECT  mpd.id_position_detail as id,  mpr.description as name, mjg.id_job_grade AS sequence,  
                          mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                          mjg.job_class_group as classname,  he.name as title, mpd.secondary_position, he.image_attachment
                  FROM master_position_detail mpd
                  INNER JOIN  master_position_routing mpr 
                    ON mpd.id_position_routing =  mpr.id_routing AND mpd.status = 'A'
                   AND mpd.id_company = mpr.id_company
                  LEFT JOIN  hr_employee he 
                    ON (mpd.id_employee =  he.id_employee OR mpd.id_employee2 = he.id_employee)
                   AND (mpd.id_company = he.id_company
                        OR mpd.assigned_to_company = he.id_company)
                  INNER JOIN  master_job_grade mjg 
                    ON mpr.id_job_grade =  mjg.id_job_grade
                   AND mpr.id_company = mjg.id_company                 
                   WHERE  mpd.id_company=". session('id_company')." 
                     AND mpd.status = 'A'  
                     AND mpd.id_position_detail = COALESCE(".$fill.",mpd.id_position_detail)
                      OR mpd.parent_id_position_detail = COALESCE(".$fill.",mpd.id_position_detail)"
					  .$count_a;
        $source = DB::select($sql);
	//	dd(implode(",",$x_a));
		$sou = array();
        foreach ($source as $key=>$s) {
            if($s->image_attachment != null){
                if(strlen($s->image_attachment) > 1000){
                    $s->image_attachment = "data:image;base64,".$s->image_attachment;
                } else {
                    $urlPhoto = 'public/upload/photo/'. $s->image_attachment;
                    if (Storage::exists($urlPhoto)) {
                        $s->image_attachment = url('project/storage/app/'.$urlPhoto);
                    } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
                }
            } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }

            $sou [$key]['id'] = $s->id;
            $sou [$key]['sequence'] = $s->sequence;
            $sou [$key]['name'] = $s->name;
            $sou [$key]['title'] = $s->title;
            $sou [$key]['child_id'] = $s->child_id;
            $sou [$key]['className'] = $s->classname;
            $sou [$key]['secondary_position'] = $s->secondary_position;
			$sou [$key]['image_attachment'] = $s->image_attachment;
        }
		foreach($source as $sc){
			if($sc->id == $fill){
				$x[] = $sc->child_id;			
			}
		}
	}
	if(isset($x[0]) != null){	
		$sql2 = "SELECT mpd.id_position_detail as id,  mpr.description as name, mjg.id_job_grade AS sequence,  
                        mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                        mjg.job_class_group as classname,  he.name as title, mpd.secondary_position, he.image_attachment
                FROM master_position_detail mpd
                INNER JOIN  master_position_routing mpr 
                  ON mpd.id_position_routing = mpr.id_routing AND mpd.status = 'A'
                 AND mpd.id_company = mpr.id_company
                LEFT JOIN  hr_employee he 
                  ON (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
                 AND (mpd.id_company = he.id_company 
                     OR mpd.assigned_to_company = he.id_company)
                INNER JOIN  master_job_grade mjg 
                  ON mpr.id_job_grade = mjg.id_job_grade
                 AND mpr.id_company = mjg.id_company
                WHERE mpd.id_company=". session('id_company')." 
                  AND mpd.status = 'A'  
                  AND (mpd.id_position_detail = COALESCE(".$fill.",mpd.id_position_detail) AND mpd.status = 'A') 
                   OR (mpd.parent_id_position_detail = COALESCE(".$fill.",mpd.id_position_detail) AND mpd.status = 'A')
                   OR mpd.id_position_detail = COALESCE(".$x[0].",mpd.id_position_detail)"
				    .$count_a;
        $source2 = DB::select($sql2);
		
		$sou = array();
        foreach ($source2 as $key=>$s) {
            if($s->image_attachment != null){
                if(strlen($s->image_attachment) > 1000){
                    $s->image_attachment = "data:image;base64,".$s->image_attachment;
                } else {
                    $urlPhoto = 'public/upload/photo/'. $s->image_attachment;
                    if (Storage::exists($urlPhoto)) {
                        $s->image_attachment = url('project/storage/app/'.$urlPhoto);
                    } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
                }
            } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }

            $sou [$key]['id'] = $s->id;
			$sou [$key]['sequence'] = $s->sequence;
            $sou [$key]['name'] = $s->name;
            $sou [$key]['title'] = $s->title;
            $sou [$key]['child_id'] = $s->child_id;
            $sou [$key]['className'] = $s->classname;		
			$sou [$key]['secondary_position'] = $s->secondary_position;
			$sou [$key]['image_attachment'] = $s->image_attachment;
        }		
		foreach($source2 as $sc2){
			if($sc2->id == $x[0]){
				$y[] = $sc2->child_id;
			}		
		}
	}
//	dd($y[0]);
	if(isset($y[0]) != null){	
		$sql3 = "SELECT  mpd.id_position_detail as id,  mpr.description as name, mjg.id_job_grade AS sequence,  
                        case when row_number() over() = 1
                            then	null
                            else mpd.parent_id_position_detail
                            end as child_id,  mpd.status as collapsed,  
                        mjg.job_class_group as classname,  he.name as title, mpd.secondary_position, he.image_attachment
                FROM master_position_detail mpd
                INNER JOIN  master_position_routing mpr 
                  ON  mpd.id_position_routing =  mpr.id_routing AND mpd.status = 'A'
                 AND  mpd.id_company = mpr.id_company 
                LEFT JOIN  hr_employee he 
                  ON  mpd.id_employee =  he.id_employee OR mpd.id_employee2 = he.id_employee
                 AND  (mpd.id_company = he.id_company 
                      OR mpd.assigned_to_company = he.id_company )
                INNER JOIN  master_job_grade mjg 
                  ON  mpr.id_job_grade =  mjg.id_job_grade
                 AND  mpr.id_company = mjg.id_company 
                WHERE mpd.id_company= ". session('id_company')." 
                  AND mpd.status = 'A'  
                  AND mpd.id_position_detail = COALESCE(".$y[0].",mpd.id_position_detail)
                UNION ALL
                SELECT  mpd.id_position_detail as id,  mpr.description as name, mjg.id_job_grade AS sequence,  
                        mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                        mjg.job_class_group as classname,  he.name as title, mpd.secondary_position, he.image_attachment
                FROM master_position_detail mpd
                INNER JOIN  master_position_routing mpr
                  ON  mpd.id_position_routing = mpr.id_routing AND mpd.status = 'A'
                 AND  mpd.id_company = mpr.id_company 
                LEFT JOIN  hr_employee he 
                  ON  mpd.id_employee =  he.id_employee OR mpd.id_employee2 = he.id_employee
                 AND  (mpd.id_company = he.id_company  
                       OR mpd.assigned_to_company = he.id_company)
                INNER JOIN  master_job_grade mjg 
                  ON  mpr.id_job_grade =  mjg.id_job_grade
                WHERE  mpd.id_company=". session('id_company')." 
                  and mpd.status = 'A'  
                  and mpd.id_position_detail = COALESCE(".$x[0].",mpd.id_position_detail)
                UNION ALL
                SELECT  mpd.id_position_detail as id,  mpr.description as name, mjg.id_job_grade AS sequence,  
                        mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                        mjg.job_class_group as classname,  he.name as title, mpd.secondary_position, he.image_attachment
                FROM master_position_detail mpd
                INNER JOIN  master_position_routing mpr 
                  ON  mpd.id_position_routing =  mpr.id_routing AND mpd.status = 'A'
                 AND mpd.id_company = mpr.id_company
                LEFT JOIN  hr_employee he 
                  ON  (mpd.id_employee = he.id_employee OR mpd.id_employee2 = he.id_employee)
                 AND  (mpd.id_company = he.id_company  
                       OR mpd.assigned_to_company = he.id_company)
                INNER JOIN  master_job_grade mjg 
                  ON  mpr.id_job_grade = mjg.id_job_grade
                 AND  mpr.id_company = mjg.id_company
                WHERE  mpd.id_company=". session('id_company')." 
				  AND  mpd.status = 'A'  
                  AND (mpd.id_position_detail = COALESCE(".$fill.",mpd.id_position_detail) AND mpd.status = 'A') 
                  OR (mpd.parent_id_position_detail = COALESCE(".$fill.",mpd.id_position_detail) AND mpd.status = 'A')"
				  .$count_a;
        $source3 = DB::select($sql3);
	//	dd($source3);
		$sou = array();
        foreach ($source3 as $key=>$s) {
            if($s->image_attachment != null){
                if(strlen($s->image_attachment) > 1000){
                    $s->image_attachment = "data:image;base64,".$s->image_attachment;
                } else {
                    $urlPhoto = 'public/upload/photo/'. $s->image_attachment;
                    if (Storage::exists($urlPhoto)) {
                        $s->image_attachment = url('project/storage/app/'.$urlPhoto);
                    } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
                }
            } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
		/*
            $sou [$s->id]['id'] = $s->id;
            $sou [$s->id]['name'] = $s->name;
            $sou [$s->id]['title'] = $s->title;
            $sou [$s->id]['child_id'] = $s->child_id;
            $sou [$s->id]['className'] = $s->classname;
		*/
		
			$sou [$key]['id'] = $s->id;
			$sou [$key]['sequence'] = $s->sequence;
            $sou [$key]['name'] = $s->name;
            $sou [$key]['title'] = $s->title;
            $sou [$key]['child_id'] = $s->child_id;
            $sou [$key]['className'] = $s->classname;		
			$sou [$key]['secondary_position'] = $s->secondary_position;
			$sou [$key]['image_attachment'] = $s->image_attachment;
        }
	}	
	//	dd($sou);
        $quechart = self::makeNested($sou)[0];
	//	dd($quechart);
		DB::commit();
				return response()->json($quechart);
        } catch (\Exception $e) {
            DB::rollBack();
         //   Log::error($e);
        }
	}
	
	public function get_position_filter() {       
		 $sql = "SELECT mpr.id_routing, mpr.description as desc_routing, mpd.id_position_detail, mpd.description as desc_position_detail
					FROM master_position_routing mpr
					LEFT JOIN master_position_detail mpd
					ON mpr.id_routing = mpd.id_position_routing
                    AND mpr.id_company = mpd.id_company
					WHERE mpd.status = 'A' 
                    and mpd.id_company =" . session('id_company');
        $result = DB::select($sql);
		 $result_list = DB::select($sql);
		$result = array();
        foreach ($result_list as $key=>$value) {
			 $result[$value->id_routing -1]['id'] = $value->id_routing;
			 $result[$value->id_routing -1]['text'] = $value->desc_routing;		
			 $result[$value->id_routing -1]['children'][] = [
				'id' => $value->id_position_detail,
				'text' => $value->desc_position_detail,
			 ];			 
		}
	//	dd($result);
        return response()->json($result);
    }

}
