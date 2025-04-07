<?php

namespace App\Http\Controllers\GeneralSetting\CompanySetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class MasterGeneralDataController extends Controller {

    public function index(Request $request) {
        return view('general_setting.company_setting.master_general_data.index');
    }

    public function get_data(Request $request) {
        // $sql = "SELECT mgt.general_type FROM master_general_data mgd 
        // JOIN master_general_type mgt ON mgd.id_general_type = mgt.id_general_type
        // WHERE mgd.id_general_type = ? LIMIT 1";
        // $relation = [
        //     // id child => parent
        //     "33" => $sql = DB::select($sql, [32])[0]
        // ];

        // $sql = "SELECT mgd.* 
        //         from master_general_type mgt
        //         join master_general_data mgd ON mgt.relation_to_id_general_type = mgd.id_general_type
        //         where mgt.id_general_type = ? and mgt.id_company = ".session('id_company')." and mgd.id_company = ".session('id_company')."";
        // dd($relation[strval(33)]->general_type);
        $data = MasterGeneralData::getdata();
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $sql = "SELECT mgt.relation_to_id_general_type from master_general_type mgt where mgt.id_general_type = ? and relation_feature = 'D'";
                            $parent = DB::selectOne($sql, [$data->id_general_type]);
                            $parent = $parent->relation_to_id_general_type ?? null;
                            // $parent = array_key_exists(strval($data->id_general_type), $relation) ? $relation[strval($data->id_general_type)]->general_type : null;
                            $button = '<button type="button" name="edit" id="' . $data->id_general_type . '" parent="'. $parent .'" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
    
    protected function validateMasterGeneralData(Request $request) {
        $arr_form_validate = [
            'general_data_detail.*.sequence' => 'required|string',
            'general_data_detail.*.code' => 'required|string',
            'general_data_detail.*.description' => 'required|string',
        ];
        
        $arr_msg_form_validate = [
            'general_data_detail.*.sequence.required' => 'The field is required',
            'general_data_detail.*.code.required' => 'The field is required',
            'general_data_detail.*.description.required' => 'The field is required',
        ];
        
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    public function save_master_general_data(Request $request) {
        ini_set('max_execution_time', -1);
        $this->validateMasterGeneralData($request);

        try{
            DB::beginTransaction();
            $id_user    = session('id_user');
            $id_company = session('id_company');
            $detail     = $request['general_data_detail'];
            $idGeneralType = $request['id_general_type'];

            foreach ($detail as $key => $val) {
                $status = @$val['active'] ? 'A' : 'I';

                $id_general_data = $val['id_general_data'];
                $masterGeneralData = DB::table('master_general_data')->where('id_general_data', $id_general_data)->first();
                if(!is_null($id_general_data)){
                    $dataUpdate = [
                        'sequence'      => $val['sequence'],
                        'code'          => $val['code'],
                        'description'   => $val['description'],
                        'status'        => $status,
                        'relation_to_id_general_data' => $val['relation'] ?? null,
                    ];
                    
                    $update = DB::table('master_general_data as mgd')
                            ->where('mgd.id_general_data', $id_general_data)
                            ->update($dataUpdate);
                } else {
                    $dataInsert = [
                        'id_general_type' => $idGeneralType,
                        'sequence'      => $val['sequence'],
                        'code'          => $val['code'],
                        'description'   => $val['description'],
                        'restrict_by'   => 'User',
                        'id_company'    => session('id_company'),
                        'creation_date' => date('Y-m-d H:i:s'),
                        'created_by'    => session('id_user'),
                    ];
                    
                    $dataInsert['relation_to_id_general_data'] = $val['relation'] ?? null;
                    
                    $update = DB::table('master_general_data as mgd')->insert($dataInsert);
                }
            }

            DB::commit();   
            return response(['status' => 'true', 'message' => 'Master general data updated successfully', 'data' => $request['general_type']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => 'Cannot update master general data. '.$e->getMessage(), 'data' => null]);
        }
    }

    public function get_detail_master_general_data(Request $request) {
        $data = [
            'id_general_type' => $request->id_general_type
        ];
        $result = MasterGeneralData::get_detail_master_general_data($data);
        if($request->parent_id) {
            $result["parent_relation_options"] = MasterGeneralData::get_parent_options($request->parent_id);
        }
        return response()->json($result);
    }

    public function generate_data_to_all_companies(Request $request) {
        $request->validate([
            'id_general_data' => 'required',
            'code' => 'required',
        ]);

        try {
            DB::select('SELECT * FROM SpGenerateGeneralDataToAllCompany(?, ?, ?)', [
                $request->id_general_data, $request->code, session('id_user')
            ]);
            return response()->json([
                "message" => "Data generated for all companies"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

}
