<?php
namespace App\Http\Controllers\Assets\Dashboard\Financial;

use App\Helpers\AssetStats;
use App\Http\Controllers\Controller;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class FinancialDashboardController extends Controller {	
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
        return view('assets.dashboard.financial.index');
    }

    public function getStats() {
        return $this->success([
            "total_annually_purchased_assets" => AssetStats::totalAnnuallyPurchasedAssets(session('id_company')),
            "total_monthly_purchased_assets" => AssetStats::totalMonthlyPurchasedAssets(session('id_company')),
            "total_monthly_asset_qty" => AssetStats::totalMonthlyAssetQuantity(session('id_company')),
            "total_asset_value" => AssetStats::totalAssetValue(session('id_company')),
            "total_net_book_by_group" => AssetStats::totalNetBookByGroup(session('id_company')),
            "annual_depreciation_value" => AssetStats::annualDepreciationValue(session('id_company')),
            "monthly_depreciation_value" => AssetStats::monthlyDepreciationValue(session('id_company')),
            "depreciation_by_group" => AssetStats::depreciationByGroup(session('id_company')),
            "total_original_cost_by_group" => AssetStats::totalOriginalCostByGroup(session('id_company')),
        ]);
    }
}