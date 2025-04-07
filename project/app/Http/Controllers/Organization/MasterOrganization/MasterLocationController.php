<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class MasterLocationController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterLocation::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_location . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_location . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('desc_branch', function($row) {
                                return $row->desc_branch;
                            })->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.job_location.index');
    }

    protected function save(Request $request) {
        $request->validate([
            'location_code' => 'required', Rule::unique('master_location')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'description' => 'required|string',
            'address_location' => 'required|string',
            'status' => 'required|string',
            'longitude' => 'required',
            'latitude' => 'required',
            ], 
            [],
            [
            'location_code' => 'Location Code',
            'description' => 'Description',
            'address_location' => 'Address Location',
            'status' => 'Status',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
        ]);

        $form_data = array(
            'location_code' => $request->location_code,
            'description' => $request->description,
            'address_location' => $request->address_location,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'status' => $request->status,
            'id_branch' => $request->id_branch,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterLocation::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Location Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterLocation::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
        $request->validate([
            'location_code' => 'required', Rule::unique('master_location')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'description' => 'required|string',
            'address_location' => 'required|string',
            'status' => 'required|string',
            'longitude' => 'required',
            'latitude' => 'required',
            ], 
            [],
            [
            'location_code' => 'Location Code',
            'description' => 'Description',
            'address_location' => 'Address Location',
            'status' => 'Status',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
        ]);
        $form_data = array(
            'location_code' => $request->location_code,
            'description' => $request->description,
            'address_location' => $request->address_location,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'status' => $request->status,
            'id_branch' => $request->id_branch,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterLocation::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Location Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = MasterLocation::findOrFail($id);
            $master = MasterBranch::findOrFail($data->id_branch);
            return response()->json(['result' => $data, 'hasil' => $master]);
        }
    }

    public function get_company() {
        $result = MasterLocation::get_company();
        return response()->json($result);
    }

    public function get_branch() {
        $result = MasterLocation::get_branch();
        return response()->json($result);
    }

}
