<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\EmployeeSetting\MasterChecklist;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MasterChecklistController extends Controller {

    public function index(Request $request) {
        return view('employee.employee_setting.master_checklist.index');
    }

    public function get_data(Request $request) {
        $data = MasterChecklist::getdata();
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_checklist . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                            $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_checklist . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    protected function validateMasterChecklist(Request $request) {
        $request->validate([
            'checklist_type' => 'required|string',
            'document_name' => 'required|string',
            'status' => 'required|string'
        ]);
        
    }

    public function save(Request $request) {
        $this->validateMasterChecklist($request);
        try{
        DB::beginTransaction();
			$form_data = [
				'document_name' => $request->document_name,
				'checklist_type' => $request->checklist_type,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			];
			$result = MasterChecklist::create($form_data);
		DB::commit();
		 return response()->json(['status' => 'true', 'message' => 'Master Checklist Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Master Checklist !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function update(Request $request) {
        $this->validateMasterChecklist($request);
        try{
        DB::beginTransaction();
			$form_data = [
				'document_name' => $request->document_name,
				'checklist_type' => $request->checklist_type,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			];
			$result = MasterChecklist::where('id_checklist', $request->id_checklist)->update($form_data);
		DB::commit();
		 return response()->json(['status' => 'true', 'message' => 'Master Checklist Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Master Checklist !! [' . $e->getMessage() . ']']);           
        }
    }

    public function get_detail_master_checklist(Request $request) {
        $data = [
            'id_checklist' => $request->id_checklist
        ];
        $result = MasterChecklist::get_detail_master_checklist($data);
        return response()->json($result);
    }

    public function destroy(Request $request) {
		$result = MasterChecklist::where('id_checklist', $request->id_checklist)->delete();
    }

}
