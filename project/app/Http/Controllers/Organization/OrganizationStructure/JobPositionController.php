<?php

namespace App\Http\Controllers\Organization\OrganizationStructure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization\OrganizationStructure\JobPosition;
use App\Imports\PositionImport;
use Maatwebsite\Excel\Facades\Excel;

class JobPositionController extends Controller {

    public function index() {
        return view('organization.organization_structure.job_position.index');
    }

    public function get_data(Request $request) {
        $result = JobPosition::get_data();
        return \Yajra\DataTables\DataTables::of($result)
                        ->addIndexColumn()
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_position . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button>'
                                    . '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_position . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    public function get_company() {
        $result = JobPosition::get_company();
        return response()->json($result);
    }

    public function get_department() {
        $result = JobPosition::get_department();
        return response()->json($result);
    }

    public function get_superior_position(Request $request) {
        $data = [
            'id_dept' => $request->id_dept
        ];
        $result = JobPosition::get_superior_position($data);
        return response()->json($result);
    }

    protected function validatePosition(Request $request) {
        $request->validate([
            'job_position' => 'required|string',
            'company' => 'required|string',
            'department' => 'required|string',
            'job_description' => 'required|string',
            'status' => 'required|string'
        ]);
    }

    public function save_position(Request $request) {
        $this->validatePosition($request);
        $data = [
            'id_position' => $request->get('id_position') != null || $request->get('id_position') != "" ? $request->get('id_position') : "",
            'job_position' => $request->job_position,
            'company' => $request->company,
            'department' => $request->department,
            'superior_position' => $request->superior_position,
            'job_description' => $request->job_description,
            'status' => $request->status
        ];
        $result = JobPosition::save_position($data);
        return response()->json($result);
    }

    public function get_detail_position(Request $request) {
        $data = [
            'id_position' => $request->id_position
        ];
        $result = JobPosition::get_detail_position($data);
        return response()->json($result);
    }

    public function destroy_position(Request $request) {
        $data = [
            'id_position' => $request->id_position
        ];
        $result = JobPosition::destroy_position($data);
        return response()->json($result);
    }

    public function import(Request $request) {
        if ($request->file('imported_file')) {
            Excel::import(new PositionImport(), request()->file('imported_file'));
            return response()->json(['status' => 'true', 'message' => 'Imported Successfully !!']);
        }
    }

    public function mass_destroy(Request $request) {
        $result = JobPosition::whereIn('id_position', request('ids'))->delete();
        return response()->json($result);
    }

    public function mass_inactive(Request $request) {
        foreach (request('ids') as $key => $value) {
            $view = JobPosition::where('id_position', $value)->first();
            if ($view->status == 'A') {
                $data = array(
                    'status' => 'I'
                );
            } else {
                $data = array(
                    'status' => 'A'
                );
            }
            $result = JobPosition::where('id_position', $value)->update($data);
        }

        return response()->json($result);
    }

}
