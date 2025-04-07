<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class MasterGradeController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterGrade::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_job_grade . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_job_grade . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.job_grade.index');
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
            'job_class_group' => $request->job_class_group,
            'created_by' => session('id_user'),
        );
        MasterGrade::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Job Grade Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterGrade::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
        $form_data = array(
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'job_class_group' => $request->job_class_group,
            'updated_by' => session('id_user'),
        );

        MasterGrade::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Job Grade Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = MasterGrade::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = MasterGrade::get_company();
        return response()->json($result);
    }

}
