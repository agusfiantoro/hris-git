<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\EmployeeSetting\MasterInsurance;
use Yajra\DataTables\DataTables;

class MasterInsuranceController extends Controller {

    public function index(Request $request) {
        return view('employee.employee_setting.master_insurance.index');
    }

    public function get_data(Request $request) {
        $data = MasterInsurance::getdata();
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_insurance . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                            $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_insurance . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    protected function validateMasterInsurance(Request $request) {
        $request->validate([
            'insurance_code' => 'required|string',
            'insurance_name' => 'required|string',
            'insurance_class' => 'required|string',
            'bill_amount' => 'required|string',
            'address' => 'required|string',
            'status' => 'required|string'
        ]);
    }

    public function save_master_insurance(Request $request) {
        $this->validateMasterInsurance($request);
        
        $data = [
            'id_master_insurance' => $request->get('id_master_insurance') != null || $request->get('id_master_insurance') != "" ? $request->get('id_master_insurance') : "",
            'insurance_code' => $request->insurance_code,
            'insurance_name' => $request->insurance_name,
            'insurance_class' => $request->insurance_class,
            'bill_amount' => $request->bill_amount,
            'address' => $request->address,
            'status' => $request->status
        ];
        $result = MasterInsurance::save_master_insurance($data);
        return response()->json($result);
        
    }

    public function get_detail_master_insurance(Request $request) {
        $data = [
            'id_master_insurance' => $request->id_master_insurance
        ];
        $result = MasterInsurance::get_detail_master_insurance($data);
        return response()->json($result);
    }

    public function destroy_master_insurance(Request $request) {
        $data = [
            'id_master_insurance' => $request->id_master_insurance
        ];
        $result = MasterInsurance::destroy_master_insurance($data);
        return response()->json($result);
    }

}
