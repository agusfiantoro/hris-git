<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterCostSharing;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterPrincipal;
use App\Models\Organization\OrganizationStructure\OrganizationUnit;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class MasterCostSharingController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterCostSharing::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_cost_sharing . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_cost_sharing . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.master_cost_sharing.index');
    }

    protected function save(Request $request) {
        $request->validate([
            'cost_sharing_code' => 'required', Rule::unique('master_cost_sharing')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'status' => 'required|string',
                ], [],
                [
                    'cost_sharing_code' => 'Cost Sharing Code',
                    'status' => 'Status',
        ]);

        $form_data = array(
            'cost_sharing_code' => $request->cost_sharing_code,
            'status' => $request->status,
            'id_branch' => $request->id_branch,
            'id_dept' => $request->id_dept,
            'id_principal' => $request->id_principal,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterCostSharing::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Cost Sharing Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterCostSharing::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
		 $request->validate([
            'cost_sharing_code' => 'required',
            'status' => 'required|string',
                ], [],
                [
                    'cost_sharing_code' => 'Cost Sharing Code',
                    'status' => 'Status',
        ]);
        $form_data = array(
           'cost_sharing_code' => $request->cost_sharing_code,
            'status' => $request->status,
            'id_branch' => $request->id_branch,
            'id_dept' => $request->id_dept,
            'id_principal' => $request->id_principal,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterCostSharing::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Cost Sharing Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = MasterCostSharing::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = MasterCostSharing::get_company();
        return response()->json($result);
    }

    public function get_branch() {
        $result = MasterCostSharing::get_branch();
        return response()->json($result);
    }
	public function get_dept() {
        $result = MasterCostSharing::get_dept();
        return response()->json($result);
    }
	public function get_principal() {
        $result = MasterCostSharing::get_principal();
        return response()->json($result);
    }

}
