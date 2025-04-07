<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\EmployeeSetting\CustomReport;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterBranch;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class CustomReportController extends Controller {

    public function index(Request $request) {
    	$accessGroup = session('access_group');
    	
		if ($request->ajax()) {
			$data = CustomReport::getdata();
			return DataTables::of($data)
							->addIndexColumn()
							->addColumn('', function($data) {
								$a = '';
								return $a;
							})
							->addColumn('action', function($data) {
								$button = '<button type="button" name="edit" id="' . $data->id_req_report . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
								$button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_req_report . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
								return $button;
							})
							->rawColumns(['action'])
							->make(true);
		}
    	$branch = MasterBranch::where('id_company', session('id_company'))->get();
    	$allBranch = [];
    	foreach ($branch as $k => $val) {
    		$allBranch[$val->id_branch] = $val->description;
    	}
		return view('employee.employee_setting.custom_report.index', compact('allBranch','accessGroup'));
    }
    
	protected function save_custom(Request $request) {	
	 	$request->validate(
	 		['group_column' => 'required|string|unique:master_request_report',], 
	 		[],
            ['group_column' => 'Group Column',]
        );
		try{
			DB::beginTransaction();
			$form_data = array(
	            'name_report' => $request->name_report,
	            'group_column' => $request->group_column,
	            'address_menu' => $request->address_menu,
	            'id_user' => session('id_user'),
	            'id_company' => session('id_company'),
	            'created_by' => session('id_user'),
	        );
			foreach($request->duallistbox as $val){
				$x[] = $val;
			}
			
			if(strpos($request->address_menu, 'access_right_user') !== false) {
				$dc = implode(",", $x);
            } else {
				$dc = '0,1,'.implode(",",$x);
            }
			$form_data['detail_column'] = $dc;
			CustomReport::create($form_data);
			
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Custom Report Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Custom Report !! [' . $e->getMessage() . ']']);           
        }
	}
	
	protected function update_custom(Request $request) {	
	 	$request->validate(
	 		['group_column' => 'required',], 
	 		[],
            ['group_column' => 'Group Column',]
       	);
		try{
			DB::beginTransaction();
			$form_data = array(
	            'name_report' => $request->name_report,
	            'group_column' => $request->group_column,
	            'address_menu' => $request->address_menu,
	            'id_user' => session('id_user'),
	            'id_company' => session('id_company'),
	            'updated_by' => session('id_user'),
	        );
			foreach($request->duallistbox as $val){
				$x[] = $val;
			}

			if(strpos($request->address_menu, 'access_right_user') !== false) {
				$dc = implode(",", $x);
            } else {
				$dc = '0,1,'.implode(",",$x);
            }
			$form_data['detail_column'] = $dc;

			CustomReport::findOrFail($request->id_req_report)->update($form_data);
			
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Custom Report Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Updated Custom Report !! [' . $e->getMessage() . ']']);           
        }
	}
	
	public function get_custom_edit(Request $request) {
        $data = [
            'id_req_report' => $request->id_req_report
        ];
        $result = CustomReport::get_custom_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function destroy($id) {
        $data = CustomReport::findOrFail($id);
        $data->delete();
    }
	
	public function get_data_custom(Request $request) {
		$data = [
            'address' => $request->address,
        ];
        $result = CustomReport::select('id_req_report as id', 'group_column as text', 'detail_column', 'id_company')
        						->where('id_user', session('id_user'))
        						->where('address_menu', $request->address)
        						->get();
        return response()->json($result);
    }

    public function get_region_branch() {
    	// $region = MasterRegional::where('id_company', session('id_company'))->get();
    	// $branch = MasterBranch::where('id_company', session('id_company'))->get();
    	$region = DB::table('master_region as mr')
                    ->join('master_company as mc', 'mc.id_company', '=', 'mr.id_company')
                    ->select('mr.id_region','mc.company_code','mr.description','mr.id_company')
                    ->where('mr.id_company', session('id_company'))
                    ->get();
        $branch = MasterBranch::select('id_branch','branch_code','description','id_region')->where('id_company', session('id_company'))->get();
    	$dataRegion = [];
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
            $dataRegion[] = [
                'id'        => $value->id_region,
                'text'      => $value->description.' ('.$value->company_code.')',
                'children'  => $child
            ];
        }
        return response()->json($dataRegion);
    }

}
