<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterStatus;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class MasterStatusController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterStatus::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_job_status . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_job_status . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.job_status.index');
    }

    protected function save(Request $request) {
        $request->validate([
            'description' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'description' => 'Description',
                    'status' => 'Status',
        ]);

        $form_data = array(
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterStatus::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Job Status Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterStatus::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
        $form_data = array(
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterStatus::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Job Status Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = MasterStatus::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = MasterStatus::get_company();
        return response()->json($result);
    }

}
