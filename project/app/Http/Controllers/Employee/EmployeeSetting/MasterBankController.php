<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\EmployeeSetting\MasterBank;
use Yajra\DataTables\DataTables;

class MasterBankController extends Controller {

    public function index(Request $request) {
        return view('employee.employee_setting.master_bank.index');
    }
    
    public function get_data(Request $request) {
        $data = MasterBank::getdata();
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_bank . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                            $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_bank . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
    
    protected function validateMasterBank(Request $request) {
        $request->validate([
            'bank_code' => 'required|string',
            'bank_name' => 'required|string',
            'transfer_code' => 'required|string',
            'status' => 'required|string'
        ]);
    }

    public function save_master_bank(Request $request) {
        $this->validateMasterBank($request);
        
        $data = [
            'id_bank' => $request->get('id_bank') != null || $request->get('id_bank') != "" ? $request->get('id_bank') : "",
            'bank_code' => $request->bank_code,
            'bank_name' => $request->bank_name,
            'transfer_code' => $request->transfer_code,
            'status' => $request->status
        ];
        $result = MasterBank::save_master_bank($data);
        return response()->json($result);
        
    }

    public function get_detail_master_bank(Request $request) {
        $data = [
            'id_bank' => $request->id_bank
        ];
        $result = MasterBank::get_detail_master_bank($data);
        return response()->json($result);
    }

    public function destroy_master_bank(Request $request) {
        $data = [
            'id_bank' => $request->id_bank
        ];
        $result = MasterBank::destroy_master_bank($data);
        return response()->json($result);
    }

}
