<?php

namespace App\Http\Controllers\Organization\OrganizationStructure;

use App\Models\Organization\OrganizationStructure\OrganizationUnit;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class OrganizationUnitController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = OrganizationUnit::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_dept . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_dept . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.organization_structure.organization_unit.index');
    }

    protected function save(Request $request) {
        $request->validate([
            'department_code' => 'required', Rule::unique('master_department')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'description' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'department_code' => 'Department Code',
                    'description' => 'Description',
                    'status' => 'Status',
        ]);

        $form_data = array(
            'department_code' => $request->department_code,
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        OrganizationUnit::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Organization Unit Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = OrganizationUnit::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
        $form_data = array(
            'department_code' => $request->department_code,
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        OrganizationUnit::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Organization Unit Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = OrganizationUnit::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = OrganizationUnit::get_company();
        return response()->json($result);
    }

}
