<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class MasterRegionalController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterRegional::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
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

    public function browse(Request $request) {
        if ($request->ajax()) {
            $data = Company::all();
            echo json_encode($data);
        }
    }

    public function checkid($id) {
        $data = Company::findOrFail($id);
        return response()->json(['result' => $data]);
    }

    public function addMorePost(Request $request) {
        $request->validate([
            'regional.*.region_code' => 'required',
            'regional.*.description' => 'required',
            'regional.*.status' => 'required',
            'regional.*.id_company' => 'required',
            'regional.*.inactive_date' => 'required',
                ], [],
                [
                    'regional.*.region_code' => 'Region Code',
                    'regional.*.description' => 'Description',
                    'regional.*.status' => 'Status',
                    'regional.*.id_company' => 'Company name',
                    'regional.*.inactive_date' => 'Inactive Date',
        ]);

        foreach ($request->regional as $key => $value) {

            $form_data = array(
                'region_code' => $value['region_code'],
                'description' => $value['description'],
                'status' => $value['status'],
                'id_company' => $value['id_company'],
                'inactive_date' => $value['inactive_date'],
                'created_by' => session('id_user'),
            );
            //  dd($form_data);
            MasterRegional::create($form_data);
        }
        return back()->with('success', 'Record Created Successfully.');
    }

    public function destroy($id) {
        $data = MasterRegional::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request, MasterRegional $regional) {
        $rules = array(
            'regional.*.region_code' => 'required',
            'regional.*.description' => 'required',
            'regional.*.status' => 'required',
            'regional.*.id_company' => 'required',
            'regional.*.inactive_date' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'region_code' => $request->region_code,
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => $request->id_company,
            'inactive_date' => $request->inactive_date,
        );

        MasterRegional::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function edit($id) {
        if (request()->ajax()) {
            $data = MasterRegional::findOrFail($id);
            $master = Company::findOrFail($data->id_company);
            return response()->json(['result' => $data, 'hasil' => $master]);
        }
    }

}
