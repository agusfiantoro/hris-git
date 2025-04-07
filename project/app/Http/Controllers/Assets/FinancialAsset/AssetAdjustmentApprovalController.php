<?php
namespace App\Http\Controllers\Assets\FinancialAsset;

use App\Http\Controllers\Controller;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\AssetEmployeeAssigned;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\FinancialAsset\AdjustmentHeader;
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

class AssetAdjustmentApprovalController extends Controller {	
    use StandardResponse, AssetsApproval;

    public function index(Request $request) {
        if($request->ajax()) {
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            try {
                $data = $this->getApprovalData('Asset_Adjustment', $employee->id_employee, "Request_Approval");
            } catch(Exception $e) {
                $data = $this->getApprovalDataLegacy('Adjustment', $employee->id_employee);
            }
            
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function ($row) {
                                return @$row->id_adjustment_header ?? $row->id_source_transaction;
                            })
                            ->make();
        }
        return view('assets.financial_asset.adjustment_approval.index');
    }

    public function approve(Request $request) {
        $request->validate([
            'id_adjustment_header' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $header = AdjustmentHeader::findOrFail($request->id_adjustment_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($header, $employee->id_employee, "Approved");
            $now = now()->format('Y-m-d');
            DB::select("SELECT * FROM asset.sp_funct_generate_journal_asset($header->id_adjustment_header, $header->id_company, '$now', 'Adjustment')");
            DB::commit();
            return $this->success($approval, "Adjustment approval success!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function reject(Request $request) {
        $request->validate([
            'id_adjustment_header' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $header = AdjustmentHeader::findOrFail($request->id_adjustment_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($header, $employee->id_employee, "Rejected");

            DB::commit();
            return $this->success($approval, "Adjustment has been rejected!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}