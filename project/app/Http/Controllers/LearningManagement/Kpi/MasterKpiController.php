<?php

namespace App\Http\Controllers\LearningManagement\Kpi;

use App\Models\LearningManagement\Kpi\MasterKpi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;

class MasterKpiController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterKpi::get_data();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        return $data->id_kpi_category;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('learning_management.kpi.master.index');
    }

    protected function validateMaster(Request $request) {
        $arr_form_validate = [
            'description'   => 'required|string',
            'weight_kpi'    => 'required|string',
            'status'        => 'required|string',
        ];
        $arr_msg_form_validate = [
            'description.required'  => 'The Description field is required',
            'weight_kpi.required'   => 'The Weight Kpi field is required',
            'status.required'       => 'The Status field is required',
        ];
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        $this->validateMaster($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'description'               => $request->description,
                'weight_kpi'                => $request->weight_kpi,
                'status'                    => $request->status,
                'id_company'                => session('id_company'),
                'created_by'                => session('id_user'),
            ];
            $insertMaster = MasterKpi::create($form_data);
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Kpi Category Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function update(Request $request) {
        $this->validateMaster($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'description'               => $request->description,
                'weight_kpi'                => $request->weight_kpi,
                'status'                    => $request->status,
                'id_company'                => session('id_company'),
                'updated_by'                => session('id_user'),
            ];

            $idKpiCategory  = $request->id_kpi_category;
            $updateMaster   = MasterKpi::findOrFail($idKpiCategory)->update($form_data);

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Kpi Category Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request) {
        $idKpiCategory  = $request->id_kpi_category;
        $data           = MasterKpi::findOrFail($idKpiCategory);
        $data->delete();
    }

    public function get_master_kpi(Request $request) {
        $idKpiCategory = $request->id_kpi_category;
        $get_content = MasterKpi::where('id_kpi_category', $idKpiCategory)->first();
        return $get_content;
    }
}
