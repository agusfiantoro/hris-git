<?php
namespace App\Http\Controllers\Assets\FinancialAsset;

use App\Http\Controllers\Controller;
use App\Models\Assets\FinancialAsset\ImpairmentHeader;
use App\Models\Assets\FinancialAsset\RevaluationHeader;
use App\Models\Assets\HrEmployee;
use Exception;
use App\Traits\Assets\AssetsApproval;
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssetImpairmentApprovalController extends Controller {	
    use StandardResponse, AssetsApproval;

    public function index(Request $request) {
        if($request->ajax()) {
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            try {
                $data = $this->getApprovalData('Asset_Impairment', $employee->id_employee, "Request_Approval");
            } catch(Exception $e) {
                $data = $this->getApprovalDataLegacy('Impairment', $employee->id_employee);
            }
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function ($row) {
                                return @$row->id_transfer_header ?? $row->id_source_transaction;
                            })
                            ->make();
        }
        return view('assets.financial_asset.impairment_approval.index');
    }

    public function approve(Request $request) {
        $request->validate([
            'id_impairment_header' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $header = ImpairmentHeader::findOrFail($request->id_impairment_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($header, $employee->id_employee, "Approved");
            $now = now()->format('Y-m-d');
            DB::select("SELECT * FROM asset.sp_funct_generate_journal_asset($header->id_impairment_header, $header->id_company, '$now', 'Impairment')");
            DB::commit();
            return $this->success($approval, "Impairment approval success!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function reject(Request $request) {
        $request->validate([
            'id_impairment_header' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $header = ImpairmentHeader::findOrFail($request->id_impairment_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($header, $employee->id_employee, "Rejected");

            DB::commit();
            return $this->success($approval, "Impairment has been rejected!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}