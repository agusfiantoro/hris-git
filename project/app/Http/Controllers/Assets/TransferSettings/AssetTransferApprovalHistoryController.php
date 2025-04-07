<?php
namespace App\Http\Controllers\Assets\TransferSettings;

use App\Http\Controllers\Controller;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\AssetEmployeeAssigned;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\TransferSettings\AssetTransferDetail;
use App\Models\Assets\TransferSettings\AssetTransferHeader;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\Assets\TransferSettings\MasterApprovalAssetDetail;
use Exception;
use App\Models\Assets\TransferSettings\MasterAssetLocation;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\Assets\AssetsApproval;
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssetTransferApprovalHistoryController extends Controller {	
    use StandardResponse, AssetsApproval;

    public function index(Request $request) {
        if($request->ajax()) {
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $data = array_merge(
                        $this->getApprovalData('Asset_Transfer', $employee->id_employee, 'Approved'), 
                        $this->getApprovalData('Asset_Transfer', $employee->id_employee, 'Rejected')
                    );
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function ($row) {
                                return @$row->id_transfer_header ?? $row->id_source_transaction;
                            })
                            ->make();
        }
        return view('assets.transfer_settings.asset_transfer_approval_history.index');
    }

    public function approve(Request $request) {
        $request->validate([
            'id_transfer_header' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $transferHeader = AssetTransferHeader::findOrFail($request->id_transfer_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($transferHeader, $employee->id_employee, "Approved");
            // $transferHeader->details;
            // foreach($transferHeader->details as $detail) {
            //     $assigned = AssetEmployeeAssigned::where('id_asset', $detail->id_asset)->active()->first();
            //     $assigned->id_employee = $detail->id_employee_destination;
            //     $assigned->id_branch = $detail->id_branch_destination;
            //     $assigned->id_location = $detail->id_location_destination;
            //     $assigned->id_asset_location = $detail->id_asset_location_destination;
            //     $assigned->id_account = $detail->id_account_destination;
            //     $assigned->updated_by = session('id_user');
            //     $assigned->save();
            // }
            DB::commit();
            return $this->success($approval, "Transfer approval success!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), $e->getCode());
        }
    }

    public function reject(Request $request) {
        $request->validate([
            'id_transfer_header' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $transferHeader = AssetTransferHeader::findOrFail($request->id_transfer_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($transferHeader, $employee->id_employee, "Rejected");

            DB::commit();
            return $this->success($approval, "Transfer has been rejected!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}