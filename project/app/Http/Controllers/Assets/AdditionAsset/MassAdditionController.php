<?php
namespace App\Http\Controllers\Assets\AdditionAsset;

use App\Http\Controllers\Controller;
use App\Models\Assets\AdditionAsset\MassAddition;
use Exception;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterAssetGroup;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Models\Assets\MasterChartAccount;
use App\Traits\StandardResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MassAdditionController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $massAdd = MassAddition::currentCompany()->active()->chronological(true)->get();
            foreach($massAdd as $row) {
                $row->asset_category = $row->assetCategory;
                $row->asset_group = $row->assetGroup;
            }
            return DataTables::of($massAdd)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) {
                        return $row->id_mass_addition;
                    })
                    ->make();
        }
        return view('assets.addition_asset.mass_addition.index');
    }

    public function getData() {
        return $this->success([
            'asset_categories' => MasterAssetCategory::currentCompany()->active()->get(['id_asset_category as id', 'description as text']),
            'asset_groups' => MasterAssetGroup::currentCompany()->active()->get(['id_asset_group as id', 'description as text']),
            'depreciation_methods' => DepreciationMethod::currentCompany()->active()->get(['id_depreciation_method as id', 'description as text']),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_mass_addition' => 'required',
        ]);
        $massAdd = MassAddition::findOrFail($request->id_mass_addition);
        return $this->success($massAdd);
    }

    // public function save(Request $request) {
    //     $acceptedRequests = [
    //         'id_mass_addition' => 'nullable',
    //         'id_asset_category' => 'required',
    //         'asset_type' => 'required',
    //         'id_asset_group' => 'required',
    //         'description' => 'required',
    //         'original_cost' => 'required',
    //         'current_units' => 'required',
    //         'id_partner' => 'nullable',
    //         'id_purchase_receiving_header' => 'nullable',
    //         'id_purchase_invoice_detail' => 'nullable',
    //         'id_project' => 'nullable',
    //         'id_batch' => 'nullable',
    //         'id_depreciation_method' => 'nullable',
    //         'depreciation_start_date' => 'nullable',
    //         'life_in_month' => 'nullable',
    //         'queue_process_status' => 'required',
    //         'status' => 'required|in:A,I',
    //     ];
    //     $request->validate($acceptedRequests);
    //     $data = $request->only(array_keys($acceptedRequests));
    //     try {
    //         if($request->id_mass_addition) {
    //             $massAdd = MassAddition::findOrFail($request->id_mass_addition);
    //             $data = array_merge($data, [
    //                 'updated_by' => session('id_user'),
    //                 'original_cost' => str_replace('.', '', $request->original_cost),
    //             ]);
    //             $massAdd->update($data);
    //         } else {
    //             $data = array_merge($data, [
    //                 'created_by' => session('id_user'),
    //                 'id_company' => session('id_company'),
    //                 'original_cost' => str_replace('.', '', $request->original_cost),
    //             ]);
    //             $massAdd = MassAddition::create($data);
    //         }
    //         return $this->success($massAdd, 'Asset mass addition has been saved successfully!');
    //     } catch(Exception $e) {
    //         return $this->error(null, $e->getMessage());
    //     }
    // }
}