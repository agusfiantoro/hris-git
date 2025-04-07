<?php

namespace App\Http\Controllers\GeneralSetting\CompanySetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class MasterApiController extends Controller {

    public function index(Request $request) {
        return view('general_setting.company_setting.master_api_key.index');
    }

    public function get_data(Request $request) {
        $data = DB::table('master_api_key')->orderBy('name', 'asc')->get();

        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            return '';
                        })
                        ->make(true);
    }

    public function save_master_api_key(Request $request) {
        ini_set('max_execution_time', -1);

        try{
            DB::beginTransaction();
            $id_user    = session('id_user');
            $id_company = session('id_company');
            $idMasterApiKey = @$request->id;

            $dataApi = [
                'id'            => $idMasterApiKey,
                'name'          => $request->name,
                'url'           => $request->url,
                'user'          => $request->user,
                'password'      => $request->password,
                'key'           => $request->key,
                'token'         => $request->token,
                'attribute_1'   => $request->attribute_1,
                'attribute_2'   => $request->attribute_2,
                'attribute_3'   => $request->attribute_3,
                'attribute_4'   => $request->attribute_4,
                'attribute_5'   => $request->attribute_5,
                'attribute_6'   => $request->attribute_6,
            ];

            if($idMasterApiKey){
                $update = DB::table('master_api_key')->where('id', $idMasterApiKey)->update($dataApi);
            } else {
                $insert = DB::table('master_api_key')->insert($dataApi);
            }

            DB::commit();   
            return response(['status' => 'true', 'message' => 'Master Api Key saved successfully', 'data' => $idMasterApiKey]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => 'Cannot update Master Api Key. '.$e->getMessage(), 'data' => null]);
        }
    }

    public function get_detail_master_api_key(Request $request) {
        $idMasterApiKey = $request->id_master_api_key;
        $result = DB::table('master_api_key')->where('id', $idMasterApiKey)->first();
        return response()->json($result);
    }

}
