<?php
namespace App\Http\Controllers\Assets\DepreciationTransaction;

use App\Http\Controllers\Controller;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\DepreciationSettings\AssetDepreciation;
use App\Models\Assets\MasterChartAccount;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DepreciationHistoryController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            // if(in_array($request->all(), ["id_period", "id_asset_category", "id_asset"])) {
            $depreciations = AssetDepreciation::currentCompany()
                            ->active()
                            ->joinAssetPeriodAndJournal()
                            ->chronological(true);
            if($request->id_period) {
                $depreciations = $depreciations->where('xfp.id_period', $request->id_period);
            }
            if($request->id_asset_category) {
                $depreciations = $depreciations->where('id_asset_category', $request->id_asset_category);
            }
            if($request->id_asset) {
                $depreciations = $depreciations->where('xfa.id_asset', $request->id_asset);
            }
            if($request->source == "mass_depreciation") {
                $depreciations = $depreciations->where('gl_transfer_flag', false);
            }
            $depreciations = $depreciations->get();
            // }
            return DataTables::of($depreciations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_depreciation_detail;
                })
                ->make();
        }
        return view('assets.depreciation_transaction.depreciation_history.index', [
            'param_source' => 'depreciation_history'
        ]);
    }

    public function getData(Request $request) {
        $assetCategories = MasterAssetCategory::active()->currentCompany()->formatSelect2();
        $assets = Asset::active()->currentCompany()->formatSelect2(null, 'asset_number');
        if($request->id_asset_category) {
            $assets = $assets->where('id_asset_category', $request->id_asset_category);
        }
        if($request->id_asset) {
            $assetCategories = $assetCategories->where('id_asset_category', Asset::findOrFail($request->id_asset)->id_asset_category);
        }
        try {
            return $this->success([
                'asset_categories' => $assetCategories->get(),
                'assets' => $assets->get(),
                'accounts' => MasterChartAccount::active()->currentCompany()->formatSelect2(null, 'account_name')->get(),
                'periods' => MasterPeriod::active('O')->currentCompany()->formatSelect2()->chronological(true)->get(),
            ]);
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_depreciation_detail' => 'required',
        ]);
        try {
            $depreciation = AssetDepreciation::with([
                    'depreciationAccount',
                    'depreciationReserveAccount',
                    'period',
                    'jeHeader',
                ])->joinAssetPeriodAndJournal()
                ->findOrFail($request->id_depreciation_detail);

            return $this->success($depreciation);
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }
}