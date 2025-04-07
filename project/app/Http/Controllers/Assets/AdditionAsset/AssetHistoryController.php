<?php
namespace App\Http\Controllers\Assets\AdditionAsset;

use App\Http\Controllers\Controller;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\AssetEmployeeAssigned;
use App\Models\Assets\AdditionAsset\AssetLocation;
use App\Models\Assets\AdditionAsset\MassAddition;
use Exception;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterAssetGroup;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\TransferSettings\MasterAssetLocation;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetHistoryController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $assets = Asset::listAsset(
                $request->id_asset ?? 'null',
                $request->id_company ?? session('id_company'),
                $request->id_employee ?? 'null',
                $request->nik_employee ?? 'null',
                $request->asset_number ?? 'null',
                $request->serial_number ?? 'null',
                $request->id_asset_group ?? 'null',
                $request->id_asset_category ?? 'null',
                $request->id_branch ?? 'null',
                $request->id_location ?? 'null',
                $request->id_asset_location ?? 'null',
                $request->ownership ?? 'null',
            );
            return DataTables::of($assets)
                    ->addIndexColumn()
                    ->make();
        }
        return view('assets.addition_asset.asset_history.index');
    }

    public function viewAsset(Request $request) {
        if($request->ajax()) {
            $assets = Asset::listAsset(
                $request->id_asset ?? 'null',
                $request->id_company ?? session('id_company'),
                $request->id_employee ?? 'null',
                $request->nik_employee ?? 'null',
                $request->asset_number ?? 'null',
                $request->serial_number ?? 'null',
                $request->id_asset_group ?? 'null',
                $request->id_asset_category ?? 'null',
                $request->id_branch ?? 'null',
                $request->id_location ?? 'null',
                $request->id_asset_location ?? 'null',
                $request->ownership ?? 'null',
            );
            return DataTables::of($assets)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) {
                        return $row->id_asset;
                    })
                    ->make();
        }
        return view('assets.addition_asset.view_asset.index');
    }

    public function getData(Request $request) {
        if($request->id_branch) {
            return $this->success([
                'locations' => MasterLocation::where('id_branch', $request->id_branch)->where('status', 'A')->get(['id_location as id', 'description as text']),
            ]);
        }
        if($request->id_location) {
            return $this->success([
                'asset_locations' => AssetLocation::currentCompany()->active()->where('id_location', $request->id_location)->get(['id_asset_location as id', 'description as text']),
            ]);
        }
        return $this->success([
            'asset_categories' => MasterAssetCategory::currentCompany()->active()->get(['id_asset_category as id', 'description as text']),
            'asset_groups' => MasterAssetGroup::currentCompany()->active()->get(['id_asset_group as id', 'description as text', '*']),
            'depreciation_methods' => DepreciationMethod::currentCompany()->active()->get(['id_depreciation_method as id', 'description as text']),
            'assets' => Asset::currentCompany()->active()->queueProcessStatus("!=", ['New', 'Confirm'])->get(['id_asset as id', DB::raw("asset_number || ' - ' || description  as text")]),
            'employees' => HrEmployee::selectRaw("id_employee as id, CONCAT(name, ' (', nik_employee, ')') as text")->currentCompany()->active()->orderBy('name')->get(),
            'branches' => MasterBranch::where('id_company', session('id_company'))->where('status', 'A')->get(['id_branch as id', 'description as text']),
            'locations' => MasterLocation::where('id_company', session('id_company'))->where('status', 'A')->get(['id_location as id', 'description as text', 'id_branch']),
            'asset_locations' => MasterAssetLocation::currentCompany()->active()->formatSelect2(null, null, ['id_location'])->get(),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_asset' => 'required',
        ]);
        $asset = Asset::with([
            'assignedEmployee.employee',
            'assignedEmployee.branch',
            'assignedEmployee.location',
            'assignedEmployee.assetLocation',
            'costHistory.account',
            'costHistory.period',
            'costHistory.jeHeader',
            'depreciation' => function($query) {
                $query->active();
            },
            'depreciation.depreciationAccount',
            'depreciation.depreciationReserveAccount',
            'depreciation.period',
            'depreciation.jeHeader',
            'image',
        ])->findOrFail($request->id_asset);
        
        foreach($asset->assignedEmployee as $assignedEmployee) {
            $assignedEmployee->human_transaction_date = Carbon::parse($assignedEmployee->transaction_date)->format('d M Y');
        }
        $asset->assigned_employee_history = Asset::assigneeHistory($request->id_asset);

        return $this->success($asset);
    }

    public function save(Request $request) {
        $acceptedRequests = [
            'id_asset' => 'nullable',
            'id_mass_addition' => 'nullable',
            'id_asset_category' => 'required',
            'asset_type' => 'required',
            'id_asset_group' => 'required',
            'asset_number' => 'nullable',
            'manufacture_name' => 'nullable',
            'model_number' => 'nullable',
            'serial_number' => 'nullable',
            'tag_number'  => 'nullable',
            'warranty_number' => 'nullable',
            'warranty_date' => 'nullable',
            'tax_expired_date' => 'nullable',
            'id_parent_asset' => 'nullable',
            'in_used_flag' => 'nullable',
            'depreciation_flag' => 'nullable',
            'depreciation_start_date' => 'nullable',
            'description' => 'required',
            'original_cost' => 'required',
            'adjusted_cost' => 'nullable',
            'depreciation_cost' => 'nullable',
            'current_cost' => 'nullable',
            'salvage_type' => 'required',
            'salvage_value' => 'required',
            'property_type' => 'required',
            'ownership' => 'required',
            'bought' => 'required',
            'leased_number' => 'nullable',
            'lease_effective_date' => 'nullable',
            'lease_expired_date' => 'nullable',
            'lease_contract_expired_date' => 'nullable',
            'partner_name' => 'nullable',
            'receiving_number' => 'nullable',
            'purchase_invoice_number' => 'nullable',
            'project_number' => 'nullable',
            'batch_number' => 'nullable',
            'current_units' => 'required|min:1:max:1',
            'id_depreciation_method' => 'nullable',
            'depreciation_start_date' => 'nullable',
            'life_in_month' => 'nullable',
            'queue_process_status' => 'required',
            'status' => 'required|in:A,I',
        ];
        $request->validate($acceptedRequests);
        if($request->detail && count($request->detail) > 0) {
            $request->validate([
                'detail.*.transaction_date' => 'required',
                'detail.*.id_employee' => 'required',
                'detail.*.unit_assigned' => 'required|numeric|min:1|max:'.$request->current_units,
                'detail.*.id_branch' => 'required',
                'detail.*.id_location' => 'required',
                'detail.*.id_asset_location' => 'required',
                'detail.*.status' => 'required',
            ], [
                'detail.*.transaction_date.required' => 'Transaction date is required',
                'detail.*.id_employee.required' => 'Employee is required',
                'detail.*.unit_assigned.required' => 'Unit assigned is required',
                'detail.*.unit_assigned.max' => 'Unit assigned cannot be more than total asset unit',
                'detail.*.id_branch.required' => 'Branch is required',
                'detail.*.id_location.required' => 'Location is required',
                'detail.*.id_asset_location.required' => 'Room is required',
                'detail.*.status.required' => 'Status is required',
            ]);
            $sum = 0;
            foreach($request->detail as $detail) {
                $sum += $detail['unit_assigned'];
                if($sum > $request->current_units) {
                    return $this->error(null, 'Total assigned units to employee exceeds asset total units!');
                }
            }
        }
        $data = $request->only(array_keys($acceptedRequests));
        $data = array_merge($data, [
            'in_used_flag' => $request->in_used_flag != NULL ? true : false,
            'depreciation_flag' => $request->depreciation_flag ?  true : false,
        ]);
        try {
            DB::beginTransaction();
            if($request->id_asset) {
                $asset = Asset::findOrFail($request->id_asset);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                    'original_cost' => str_replace('.', '', $request->original_cost),
                ]);
                $asset->update($data);
            } else {
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                    'original_cost' => str_replace('.', '', $request->original_cost),
                    'asset_number' => $this->generateAssetNumber(session('id_company'), $request->id_asset_group),
                ]);
                $asset = Asset::create($data);
                if($request->detail && count($request->detail) > 0) {
                    foreach($request->detail as $detail) {
                        $detailData = array_merge($detail, [
                            'id_asset' => $asset->id_asset,
                            'id_company' => $asset->id_company,
                            'created_by' => session('id_user'),
                        ]);
                        AssetEmployeeAssigned::create($detailData);
                    }
                }
            }
            DB::commit();
            return $this->success($asset, 'Asset has been saved successfully!');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function assetAssigneeHistory(Request $request) {
        $request->validate([
            'id_asset' => 'required'
        ]);
        try {
            return $this->success(Asset::assigneeHistory($request->id_asset));
        } catch(Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function printLabel(Request $request) {
        $request->validate([
            'id_assets' => 'required',
        ]);
        $idAssets = explode(",", $request->id_assets);
        try {
            $assets = Asset::whereIn('id_asset', $idAssets)->get(['id_asset', 'asset_number', 'description']);
            foreach($assets as $key => $asset) {
                $assets[$key]->qr = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($asset->asset_number));
            }
            $pdf = Pdf::loadView('assets.addition_asset.asset_history.print', [
                'assets' => $assets,
            ]);
            return $pdf->setPaper([0,0,'33mm','15mm'], 'portrait')->stream();
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }
}