<?php
namespace App\Http\Controllers\Assets\Dashboard\Asset;

use App\Helpers\AssetStats;
use App\Http\Controllers\Controller;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssetDashboardController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $data = DepreciationMethod::currentCompany()->chronological(true)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_depreciation_method;
                })
                ->make();
        }
        return view('assets.dashboard.asset.index');
    }

    public function getStats() {
        return $this->success([
            "total_asset_qty" => collect(AssetStats::totalMonthlyAssetQuantity(session('id_company')))->groupBy('year'),
            "total_outstanding_assets_by_group" => AssetStats::totalOutstandingAssetsByGroup(session('id_company')),
            "total_assets_by_group_and_branch" => AssetStats::totalAssetByGroupAndBranch(session('id_company')),
            "total_assets_by_group" => AssetStats::totalAssetQuantityByGroup(session('id_company')),
        ]);
    }
}