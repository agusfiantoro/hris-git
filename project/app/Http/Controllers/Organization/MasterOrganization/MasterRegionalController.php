<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class MasterRegionalController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterRegional::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_region . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_region . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.master_regional.index');
    }

    protected function save(Request $request) {
        $request->validate([
			'region_code' => ['required', Rule::unique('master_region')->where('id_company',session('id_company'))],
            'description' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'region_code' => 'Region Code',
                    'description' => 'Description',
                    'status' => 'Status',
        ]);

        $form_data = array(
            'region_code' => $request->region_code,
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterRegional::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Region Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterRegional::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
        $form_data = array(
            'region_code' => $request->region_code,
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterRegional::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Regional Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = MasterRegional::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = MasterRegional::get_company();
        return response()->json($result);
    }

}
