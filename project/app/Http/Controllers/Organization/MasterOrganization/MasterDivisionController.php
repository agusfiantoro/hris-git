<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterDivision;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class MasterDivisionController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterDivision::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_division . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_division . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.master_division.index');
    }

    protected function save(Request $request) {
        $request->validate([
            'description' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'description' => 'Division',
                    'status' => 'Status',
        ]);

        $form_data = array(
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
			'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterDivision::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Division Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterDivision::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
		 $request->validate([
            'description' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'description' => 'Division',
                    'status' => 'Status',
        ]);
        $form_data = array(
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
			'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterDivision::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Division Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = MasterDivision::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = MasterDivision::get_company();
        return response()->json($result);
    }
}
