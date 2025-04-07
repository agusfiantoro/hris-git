<?php

namespace App\Http\Controllers\Organization\OrganizationStructure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization\OrganizationStructure\JobPositionRoute;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;

class JobPositionRouteController extends Controller {

    public function index() {
        return view('organization.organization_structure.job_position_route.index');
    }

    public function get_data(Request $request) {
        $result = JobPositionRoute::get_data();
        return \Yajra\DataTables\DataTables::of($result)
                        ->addIndexColumn()
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_routing . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button>'
                                    . '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_routing . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    public function get_job_position() {
        $result = JobPositionRoute::get_job_position();
        return response()->json($result);
    }

    public function get_parent_job(Request $request) {
        $data = [
            'id_position' => $request->id_position
        ];
        $result = JobPositionRoute::get_parent_job($data);
        return response()->json($result);
    }

    public function get_grade() {
        $result = JobPositionRoute::get_grade();
        return response()->json($result);
    }

    public function get_job_status() {
        $result = JobPositionRoute::get_job_status();
        return response()->json($result);
    }

    public function get_company() {
        $result = JobPositionRoute::get_company();
        return response()->json($result);
    }
	public function get_assign_company() {
        $result = JobPositionRoute::get_assign_company();
        return response()->json($result);
    }

    public function get_department(Request $request) {
        $data = [
            'id_dept' => $request->id_dept
        ];
        $result = JobPositionRoute::get_department($data);
        return response()->json($result);
    }

    public function get_default_cost_sharing() {
        $result = JobPositionRoute::get_default_cost_sharing();
        return response()->json($result);
    }

    public function get_location() {
        $result = JobPositionRoute::get_location();
        return response()->json($result);
    }

    public function get_branch_operating_unit() {
        $result = JobPositionRoute::get_branch_operating_unit();
        return response()->json($result);
    }

    public function get_principal() {
        $result = JobPositionRoute::get_principal();
        return response()->json($result);
    }

    public function get_work_arround() {
    	$branch = MasterBranch::where('id_company', session('id_company'))->get();
    	$location = MasterLocation::where('id_company', session('id_company'))->get();
		$dataBranch = [];
		foreach ($branch as $key => $value) {
            $child = [];
            foreach ($location as $k => $item) {
                if($value->id_branch == $item->id_branch){
                    $child[] = [
                        'id'        => $item->id_location,
                        'text'      => $item->description,
                    ];
                }
            }
            $dataBranch[] = [
                'id'        => $value->id_branch,
                'text'      => $value->description,
                'children'  => $child
            ];
        }	
        return response()->json($dataBranch);
    }

    public function get_cost_sharing() {
        $result = JobPositionRoute::get_cost_sharing();
        return response()->json($result);
    }

    public function get_employee(Request $request) {
        $assignedIdCompany = @$request->assignedIdCompany;
        $vacant = @$request->vacant;
        $id_employee = @$request->id_employee;
        if($vacant){
            $result = JobPositionRoute::get_employee_vacant($assignedIdCompany, $id_employee);
        } else {
            $result = JobPositionRoute::get_employee($assignedIdCompany);
        }
        return response()->json($result);
    }

    public function get_superior_position(Request $request) {
    /* $id = $request->get('id');
       $data = [
            'id' => $id
        ];
	*/
        $result = JobPositionRoute::get_superior_position();
        return response()->json($result);
    }

    protected function validatePositionRoute(Request $request) {
        $arr_form_validate = [
            'job_position' => 'required|string',
            'job_position_route' => 'required|string',
            'grade' => 'required|string',
            'job_status' => 'required|string',
            'company' => 'required|string',
            'department' => 'required|string',
            'status' => 'required|string',
            'expected_new_employee' => 'required|integer',
            'detail_position_route.*.position_detail_name' => 'required|string',
            'detail_position_route.*.location' => 'required|string',
        ];

        $arr_msg_form_validate = [
            'detail_position_route.*.position_detail_name.required' => 'The field is required',
            'detail_position_route.*.location.required' => 'The field is required',
            'detail_position_route.*.branch_operating_unit.required' => 'The field is required',
            'detail_position_route.*.principal.required' => 'The field is required',
            'detail_position_route.*.work_arround.required' => 'The field is required',
            'detail_position_route.*.cost_sharing.required' => 'The field is required',
            'detail_position_route.*.employee.required' => 'The field is required',
            'detail_position_route.*.superior_position.required' => 'The field is required',
        ];

        if ($request->post('detail_position_route') == null) {
            // $validate_detail_position_route = ['table_job_position_detail' => 'required|string'];
            // $validate_msg_detail_position_route = ['table_job_position_detail.required' => 'Position details cannot empty'];
            // $arr_form_validate = array_merge($arr_form_validate, $validate_detail_position_route);
            // $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_detail_position_route);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    public function save_position_route(Request $request) {
        $this->validatePositionRoute($request);
        $data = [
            'job_position' => $request->job_position,
            'job_position_route' => $request->job_position_route,
            'parent_job' => $request->parent_job,
            'job_status' => $request->job_status,
            'grade' => $request->grade,
            'company' => $request->company,
            'department' => $request->department,
            'default_cost_sharing' => $request->default_cost_sharing,
            'expected_new_employee' => $request->expected_new_employee,
            'existing_employee' => $request->existing_employee,
			'status' => $request->status,
            'detail_position_route_job_description' => $request->detail_position_route_job_description,
            'detail_position_route_skill_requirement' => $request->detail_position_route_skill_requirement,
            'detail_position_route' => $request->detail_position_route,
        ];
        $result = JobPositionRoute::save_position_route($data);
        return response()->json($result);
    }

    public function save_update_position_route(Request $request) {
        $this->validatePositionRoute($request);
        $data = [
            'id_routing' => $request->get('id_routing'),
            'job_position' => $request->job_position,
            'job_position_route' => $request->job_position_route,
            'parent_job' => $request->parent_job,
            'job_status' => $request->job_status,
            'grade' => $request->grade,
            'company' => $request->company,
            'department' => $request->department,
            'default_cost_sharing' => $request->default_cost_sharing,
            'expected_new_employee' => $request->expected_new_employee,
            'existing_employee' => $request->existing_employee,
            'status' => $request->status,
            'detail_position_route_job_description' => $request->detail_position_route_job_description,
            'detail_position_route_skill_requirement' => $request->detail_position_route_skill_requirement,
            'detail_position_route' => $request->detail_position_route,
            'row_id' => $request->row_id,
        ];
        $result = JobPositionRoute::save_update_position_route($data);
        return response()->json($result);
    }

    public function get_detail_position_route(Request $request) {
        $data = [
            'id_routing' => $request->id_routing
        ];
        $result = JobPositionRoute::get_detail_position_route($data);
        return response()->json($result);
    }

    public function destroy_position_route(Request $request) {
        $data = [
            'id_routing' => $request->id_routing
        ];
        $result = JobPositionRoute::destroy_position_route($data);
        return response()->json($result);
    }

}
