<?php
namespace App\Http\Controllers\Assets\TransferSettings;

use App\Http\Controllers\Controller;
use App\Models\Assets\HrEmployee;
use Exception;
use App\Traits\Assets\AssetsApproval;
use App\Traits\StandardResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssetTransferApprovalController extends Controller {	
    use StandardResponse, AssetsApproval;

    public function index(Request $request) {
        if($request->ajax()) {
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $data = $this->getApprovalData(null, $employee->id_employee, 'Request_Approval');
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function ($row) {
                                return @$row->id_transfer_header ?? $row->id_source_transaction;
                            })
                            ->make();
        }
        return view('assets.transfer_settings.asset_transfer_approval.index');
    }

    public function approve(Request $request) {
        $request->validate([
            'id_header' => 'required',
            'source_transaction_type' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $header = $this->getHeaderInstance($request->source_transaction_type)::findOrFail($request->id_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($header, $employee->id_employee, "Approved");
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
            $transactionType = str_replace("_", " ", $request->source_transaction_type);
            return $this->success($approval, "$transactionType approval success!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), $e->getCode());
        }
    }

    public function reject(Request $request) {
        $request->validate([
            'id_header' => 'required',
            'source_transaction_type' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $header = $this->getHeaderInstance($request->source_transaction_type)::findOrFail($request->id_header);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $approval = $this->assetApproval($header, $employee->id_employee, "Rejected");

            DB::commit();
            $transactionType = str_replace("_", " ", $request->source_transaction_type);
            return $this->success($approval, "$transactionType has been rejected!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function getModal(Request $request) {
        $request->validate([
            'source_transaction_type' => 'required',
        ]);
        return view("assets.transfer_settings.asset_transfer_approval.modal-".strtolower($request->source_transaction_type));
    }
}