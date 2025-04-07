<?php
namespace App\Http\Controllers\Assets\AdditionAsset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Exception;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\ConfigSettings\AssetConfigSettings;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Models\Assets\MasterChartAccount;
use App\Traits\StandardResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class MasterAssetCategoryController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $assetCategories = MasterAssetCategory::currentCompany()->chronological(true)->get();
            return DataTables::of($assetCategories)
                ->addIndexColumn()
                ->addColumn('action', function($data) {
                    return $data->id_asset_category;
                })
                ->make();
        }
        return view('assets.addition_asset.master_asset_category.index');
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_asset_category' => 'required'
        ]);
        $assetCategory = MasterAssetCategory::findOrFail($request->id_asset_category);
        return $this->success($assetCategory);
    }

    public function getAccounts(Request $request) {
        $account = MasterChartAccount::selectRaw('id_account as id, CONCAT(account_name, \' (\', account_number, \')\') as text')
                    ->where('status', 'A')
                    ->where('id_company', session('id_company'))
                    ->get();
        $depreciationMethod = DepreciationMethod::selectRaw('id_depreciation_method as id, depreciation_code as text')
                    ->where('id_company', session('id_company'))
                    ->where('status', 'A')
                    ->get();
        $configSettings = DB::table('asset.fa_config_settings')->where('id_company', session('id_company'))->where('status', 'A')->first();
        return $this->success([
            'accounts' => $account,
            'depreciation_methods' => $depreciationMethod,
            'config_settings' => $configSettings,
        ]);
    }

    public function save(Request $request) {
        $assetConfig = AssetConfigSettings::currentCompany()->active()->first();
        $validations = [
            'id_asset_category' => 'nullable',
            'asset_category_code' => 'required|string',
            'description' => 'required|string',
            'category_type' => 'required',
            'ownership' => 'required',
            'property_type' => 'required',
            'asset_classification' => 'required',
            'physical_inventory_flag' => 'nullable',
            'depreciation_flag' => 'nullable',
            'id_depreciation_method' => 'required_with:depreciation_flag',
            'life_in_month' => 'required|numeric',
            'id_asset_cost_account' => 'required_if:category_type,Capitalized',
            'id_asset_clearing_account' => 'required_if:category_type,Capitalized',
            'id_cip_cost_account' => 'required_if:category_type,CIP',
            'id_cip_clearing_account' => 'required_if:category_type,CIP',
            'id_expense_cost_account' => 'required_if:category_type,Expense',
            'id_expense_clearing_account' => 'required_if:category_type,Expense',
            'id_depreciation_expense_account' => 'required_with:depreciation_flag',
            'id_depreciation_reserve_account' => 'required_with:depreciation_flag',
            'id_revaluation_amortization_account' => Rule::requiredIf($assetConfig->revaluation_flag),
            'id_revaluation_reserve_account' => Rule::requiredIf($assetConfig->revaluation_flag),
            'id_impairment_expense_account' => Rule::requiredIf($assetConfig->impairment_flag),
            'id_impairment_reserve_account' => Rule::requiredIf($assetConfig->impairment_flag),
            'id_proceeds_sale_gain_or_loss_account' => 'required',
            'id_proceeds_sale_clearing_account' => 'required',
            'id_cost_removal_gain_or_loss_account' => 'required',
            'id_cost_removal_clearing_account' => 'required',
            'id_retired_gain_or_loss_account' => 'required',
            'status' => 'required|in:A,I',
        ];
        $request = SanitizedForm::sanitizeStringInput($request, $validations);
        $request->validate($validations);
        $physicalInventoryFlag = false;
        $depreciationFlag = false;
        if($request->physical_inventory_flag !== NULL) {
            $physicalInventoryFlag = true;
        }
        if($request->depreciation_flag !== NULL) {
            $depreciationFlag = true;
        }
        try {
            $data = $request->only(array_keys($validations));
            if($request->id_asset_category) {
                $assetCategory = MasterAssetCategory::findOrFail($request->id_asset_category);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                    'physical_inventory_flag' => $physicalInventoryFlag,
                    'depreciation_flag' => $depreciationFlag,
                ]);
                $assetCategory->update($data);
            } else {
                $data = array_merge($data, [
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                    'physical_inventory_flag' => $physicalInventoryFlag,
                    'depreciation_flag' => $depreciationFlag,
                ]);
                $assetCategory = MasterAssetCategory::create($data);
            }
            return $this->success($assetCategory, 'Asset category has been saved successfully!');
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }
}