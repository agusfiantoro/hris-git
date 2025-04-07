<?php
namespace App\Http\Controllers\Assets\AdditionAsset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Exception;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterAssetGroup;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Models\Assets\MasterChartAccount;
use App\Traits\StandardResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MasterAssetGroupController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $assetGroup = MasterAssetGroup::currentCompany()
                ->leftJoin('asset.fa_depreciation_method as fdm', 'fa_asset_group.id_depreciation_method', 'fdm.id_depreciation_method')
                ->select('fa_asset_group.*', 'fdm.description as depreciation_method')
                ->chronological(true)
                ->get();
            return DataTables::of($assetGroup)
                ->addIndexColumn()
                ->addColumn('action', function($data) {
                    return $data->id_asset_group;
                })
                ->make();
        }
        return view('assets.addition_asset.master_asset_group.index');
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_asset_group' => 'required',
        ]);
        $assetGroup = MasterAssetGroup::findOrFail($request->id_asset_group);
        return $this->success($assetGroup);
    }

    public function getData() {
        return [
            'depreciation_methods' => DepreciationMethod::active()->currentCompany()->formatSelect2()->get(),
        ];
    }

    public function save(Request $request) {
        $acceptedRequests = [
            'id_asset_group' => 'nullable',
            'asset_group_code' => 'required|string',
            'description' => 'required|string',
            'note' => 'nullable|string',
            'depreciation_flag' => 'nullable',
            'id_depreciation_method' => 'nullable',
            'life_in_month' => 'nullable|numeric',
            'salvage_type' => 'nullable',
            'salvage_value' => 'nullable',
            'status' => 'required|in:A,I',
        ];
        $request = SanitizedForm::sanitizeStringInput($request, $acceptedRequests);
        $request->validate($acceptedRequests);

        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($acceptedRequests));
            if($request->id_asset_group) {
                $assetGroup = MasterAssetGroup::findOrFail($request->id_asset_group);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                    'depreciation_flag' => $request->depreciation_flag !== NULL ? true : false,
                    'salvage_value' => str_replace(".", "", $request->salvage_value),
                ]);
                $assetGroup->update($data);
            } else {
                $data = array_merge($data, [
                    'depreciation_flag' => $request->depreciation_flag !== NULL ? true : false,
                    'salvage_value' => str_replace(".", "", $request->salvage_value),
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ]);
                $assetGroup = MasterAssetGroup::create($data);
            }
            DB::commit();
            return $this->success($assetGroup, 'Asset group has been saved successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}