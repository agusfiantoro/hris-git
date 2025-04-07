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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\DataTables;

class ReceiveTransferAssetController extends Controller {	
    use StandardResponse, AssetsApproval;

    public function index(Request $request) {
        if($request->ajax()) {
            if($request->status == 'FALSE') {
                $status = FALSE;
            } else if($request->status == 'TRUE') {
                $status = TRUE;
            } else {
                $status = NULL;
            }
            // $status = $request->status == 'NULL' ? NULL : ($request->status == 'TRUE' ? TRUE : FALSE);
            $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
            $data = AssetTransferHeader::active()
                                        ->approvalStatus('Approved')
                                        ->transferReceivedBy($employee->nik_employee, $status)
                                        ->groupBy('xftd.id_transfer_detail', 'xfa.asset_number', 'xfa.description', 'xhe3.name', 'asset.fa_transfer_header.process_status', 'asset.fa_transfer_header.reference_number')
                                        ->get(['xftd.*', 'xfa.asset_number', 'xfa.description', 'xhe3.name as request_by', DB::raw('xftd.creation_date::date as casted_creation_date'), 'asset.fa_transfer_header.process_status', 'asset.fa_transfer_header.reference_number']);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function ($row) {
                                return $row->id_transfer_detail;
                            })
                            ->make();
        }
        return view('assets.transfer_settings.asset_transfer_receive.index');
    }

    public function receive(Request $request) {
        $request->validate([
            'id_transfer_detail' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $transferDetail = AssetTransferDetail::findOrFail($request->id_transfer_detail);
            $transferDetail->received_flag = true;
            $transferDetail->effective_date = now()->format('Y-m-d');
            $transferDetail->updated_by = session('id_user');
            $transferDetail->save();

            $assigned = AssetEmployeeAssigned::where('id_asset', $transferDetail->id_asset)->active()->first();
            $assigned->id_employee = $transferDetail->id_employee_destination;
            $assigned->id_branch = $transferDetail->id_branch_destination;
            $assigned->id_location = $transferDetail->id_location_destination;
            $assigned->id_asset_location = $transferDetail->id_asset_location_destination;
            $assigned->id_account = $transferDetail->id_account_destination;
            $assigned->updated_by = session('id_user');
            $assigned->save();
            DB::commit();
            return $this->success($transferDetail, "Asset transfer receival confirmation success!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), $e->getCode());
        }
    }

    public function refuse(Request $request) {
        $request->validate([
            'id_transfer_detail' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $transferDetail = AssetTransferDetail::findOrFail($request->id_transfer_detail);
            $transferDetail->received_flag = false;
            $transferDetail->updated_by = session('id_user');
            $transferDetail->save();
            DB::commit();
            return $this->success($transferDetail, "Asset transfer has been refused!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function print(Request $request) {
        $request->validate([
            'id_transfer_header' => 'required',
        ]);
        $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
        $data = AssetTransferHeader::joinCompany()
                ->transferReceivedBy($employee->nik_employee, TRUE)
                ->select('asset.fa_transfer_header.*', 'xhe4.name as name_approval', 'xhe4.nik_employee as nik_approval', "xmc.description as company_name")
                ->findOrFail($request->id_transfer_header);
        $data->transfer_details = $data->details()->where('received_flag', TRUE)->get();
        $data->document_status = $data->documentStatus;
        $data->request_by = DB::selectOne("SELECT he.id_employee, he.name, he.nik_employee, mpr.description As position FROM hr_employee he 
                                            JOIN master_position_detail mpd 
                                            ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2) 
                                            AND mpd.secondary_position = FALSE 
                                            JOIN master_position_routing mpr 
                                            ON mpd.id_position_routing = mpr.id_routing
                                            WHERE he.id_employee = (
                                                SELECT max(he.id_employee) FROM hr_employee he 
                                                WHERE he.id_user = ?
                                            )", [$data->created_by]);
        $employeeRecipient = null;
        foreach($data->transfer_details as $detail) {
            $detail->asset = $detail->asset;
            $detail->employee = $detail->destinationEmployee; 
            $detail->branch = $detail->destinationBranch;
            $detail->location = $detail->destinationLocation;
            $employeeRecipient = $detail->employee;
        }
        
        $employeeRecipient->qr = QrCode::format('svg')->errorCorrection('H')->generate("$data->reference_number (".$employeeRecipient->name.'/'.$employeeRecipient->nik_employee.')');
        $approvalQr = QrCode::format('svg')->errorCorrection('H')->generate("$data->reference_number (".$data->name_approval.'/'.$data->nik_approval.')');
        $pdf = Pdf::loadView('assets.transfer_settings.asset_transfer_receive.print', [
            'data' => $data,
            'employeeRecipient' => $employeeRecipient,
            'approvalQr' => $approvalQr,
        ]);
        return $pdf->setPaper('A4', 'landscape')->stream();
    }
}