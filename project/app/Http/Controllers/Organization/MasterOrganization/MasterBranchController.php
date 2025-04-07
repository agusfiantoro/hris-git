<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class MasterBranchController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterBranch::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_branch . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_branch . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('desc_region', function($row) {
                                return $row->desc_region;
                            })->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.master_branch.index');
    }

    protected function save(Request $request) {
        $request->validate([
			'branch_code' => ['required', Rule::unique('master_branch')->where('id_company',session('id_company'))],
            'description' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'branch_code' => 'Branch Code',
                    'description' => 'Description',
                    'status' => 'Status',
        ]);

        $form_data = array(
            'branch_code' => $request->branch_code,
            'description' => $request->description,
            'status' => $request->status,
            'id_region' => $request->id_region,
            'id_company' => session('id_company'),
            'id_branch_erp' => $request->id_branch_erp,
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterBranch::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Branch Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterBranch::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
        $form_data = array(
            'branch_code' => $request->branch_code,
            'description' => $request->description,
            'status' => $request->status,
            'id_region' => $request->id_region,
            'id_company' => session('id_company'),
            'id_branch_erp' => $request->id_branch_erp,
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterBranch::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Branch Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = MasterBranch::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = MasterBranch::get_company();
        return response()->json($result);
    }

    public function get_regional() {
        $result = MasterBranch::get_regional();
        return response()->json($result);
    }

}
