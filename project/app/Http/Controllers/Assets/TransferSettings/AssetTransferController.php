<?php
namespace App\Http\Controllers\Assets\TransferSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetTransferUpdateRequest;
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

class AssetTransferController extends Controller {	
    use StandardResponse, AssetsApproval;
    
    public function index(Request $request) {
        if($request->ajax()) {
            $transfers = AssetTransferHeader::currentCompany()->chronological(true)->where('created_by', session('id_user'))->get();
            foreach($transfers as $transfer) {
                $transfer->approval;
                $transfer->documentStatus;
            }
            return DataTables::of($transfers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_transfer_header;
                })
                ->make();
        }
        return view('assets.transfer_settings.asset_transfer.index', [
            'source' => 'request'
        ]);
    }

    public function getData(Request $request) {
        if($request->id_branch) {
            return $this->success([
                'locations' => MasterLocation::where('status', 'A')->where('id_branch', $request->id_branch)->get(['id_location as id', 'description as text']),
            ]);
        }
        if($request->id_location) {
            return $this->success([
                'asset_locations' => MasterAssetLocation::active()->where('id_location', $request->id_location)->get(['id_asset_location as id', 'description as text']),
            ]);
        }
        $assets = Asset::currentCompany()
                        ->withAssignedEmployeeAndDetails()
                        ->select(
                            'fa_asset.id_asset as id', 
                            'fa_asset.description', 
                            'asset_number as text', 
                            'current_units', 
                            'mb.description as branch', 
                            'ml.description as location', 
                            'fal.description as asset_location', 
                            DB::raw("CASE
                                        WHEN he.nik_employee IS NOT NULL THEN CONCAT(he.name, ' (', he.nik_employee, ')')
                                        ELSE null
                                    END AS employee")
                        )
                        ->get();
        $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
        $assetsAllowed = Asset::currentCompany()
                                ->active()
                                ->withAssignedEmployeeAndDetails($employee->id_employee, true)
                                ->inUse()
                                ->select(
                                    'fa_asset.id_asset as id', 
                                    'fa_asset.description', 
                                    'asset_number as text', 
                                    'current_units', 
                                    'mb.description as branch', 
                                    'ml.description as location', 
                                    'fal.description as asset_location', 
                                    DB::raw("CASE
                                                WHEN he.nik_employee IS NOT NULL THEN CONCAT(he.name, ' (', he.nik_employee, ')')
                                                ELSE null
                                            END AS employee")
                                )
                                ->get();
        $employee = HrEmployee::currentCompany()->active()->orderByDesc('id_employee')->where('id_user', session('id_user'))->first();
        $approvalHeader = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Transfer')->first();
        return $this->success([
            'approval_hierarchy' => $this->getApprovalList($approvalHeader, $employee->id_employee, session('id_company')),
            'employees' => HrEmployee::currentCompany()->get(['id_employee as id', 'name as text']),
            'id_employee' => $employee->id_employee,
            'branches' => MasterBranch::where('id_company', session('id_company'))->where('status', 'A')->get(['id_branch as id', 'description as text']),
            'assets' => $assets,
            'assets_allowed' => $assetsAllowed,
            'periods' => MasterPeriod::currentCompany()->chronological()->get(['id_period as id', 'description as text']),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_transfer_header' => 'required',
        ]);
        $transfer = AssetTransferHeader::with([
                'details.sourceBranch',
                'details.sourceLocation',
                'details.sourceAssetLocation',
                'details.sourceEmployee',
                'details.asset',
                'documentStatus',
            ])->findOrFail($request->id_transfer_header);
        
        $transfer->id_employee = HrEmployee::where('id_user', $transfer->created_by)->orderByDesc('id_employee')->first()->id_employee ?? null;
        $transfer->approval_transactions = $this->getApprovalTransaction($transfer, $transfer->id_transfer_header);
        
        return $this->success($transfer);
    }

    protected function generateRefNumber(int $idCompany) {
        $company = DB::table('master_company')->where('id_company', $idCompany)->where('status', 'A')->first();
        $time = now()->format('Y');
        $assetNumber = "$company->company_code/TRF/$time/";
        $maxNumber = DB::selectOne("SELECT max(reference_number) FROM asset.fa_transfer_header fth WHERE reference_number LIKE ?", [$assetNumber."%"]);
        $maxNumber = $maxNumber && $maxNumber->max ? sprintf("%04s", abs(substr($maxNumber->max, -4)+1)) : "0001";
        $assetNumber .= now()->format('m/').$maxNumber;
        return $assetNumber;
    }

    public function createApprovalTransaction(MasterApprovalAsset $approvalHeader, AssetTransferHeader $transfer) {
        if(!$approvalHeader) {
            throw new Exception("Approval code not found.");
        }
        $employee = HrEmployee::where('id_user', session('id_user'))->active()->first();
        $approvalDetails = $this->getApprovalList($approvalHeader, $employee->id_employee, session('id_company'), $approvalHeader->id_asset_category);
        foreach($approvalDetails as $detail) {
            AssetApprovalTransaction::create([
                'id_source_transaction' => $transfer->id_transfer_header,
                'source_transaction_type' => 'Asset_Transfer',
                'id_approval' => $transfer->id_approval,
                'id_approval_detail' => $detail->id_approval_detail ?? null,
                'id_approval_mode' => $detail->id_approval_mode,
                'sequence' => $detail->sequence,
                'id_position_detail' => $detail->id_position_detail ?? $detail->id_detail_chief,
                'id_employee_approval' => $detail->id_employee ?? $detail->id_employee_approval,
                'id_approval_status' => $transfer->id_document_status,
                'id_company' => $transfer->id_company,
                'created_by' => session('id_user'),
            ]);
        }
        return $approvalDetails;
    }

    public function save(AssetTransferUpdateRequest $request) {

        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($request->rules()));
            $mgdTransferAsset = MasterGeneralData::where('code', 'Asset_Transfer')->where('id_company', session('id_company'))->first();
            $approvalHeader = MasterApprovalAsset::currentCompany()->where('id_approval_doc_type', $mgdTransferAsset->id_general_data)->first();

            $assetTransferDocumentStatus = null;
            if($request->id_transfer_header) {
                $transfer = AssetTransferHeader::findOrFail($request->id_transfer_header);
                $assetTransferDocumentStatus = $transfer['id_document_status'];
                $new = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                if($request->submission_type == 'submit') {
                    $data['id_document_status'] = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                ]);
                $transfer->update($data);
                if($request->submission_type == 'submit' && $assetTransferDocumentStatus == $new) {
                    $this->createApprovalTransaction($approvalHeader, $transfer);
                }
            } else {
                if($request->submission_type == 'submit') {
                    $docStatus = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                } else {
                    $docStatus = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $data = array_merge($data, [
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                    'reference_number' => $this->generateRefNumber(session('id_company')),
                    'request_date' => now()->format('Y-m-d'),
                    'id_approval' => $approvalHeader->id_approval,
                    'id_document_status' => $docStatus,
                ]);
                $transfer = AssetTransferHeader::create($data);
                if($request->submission_type == 'submit') {
                    $this->createApprovalTransaction($approvalHeader, $transfer);
                }
            }
            if($request->detail && !in_array($assetTransferDocumentStatus, ['Approved', 'Rejected', 'Request_Approval'])) {
                foreach($request->detail as $detail) {
                    $detail = array_merge($detail, [
                        'id_employee_destination' => @$detail['id_employee_destination'],
                        'id_account_destination' => @$detail['id_account_destination'],
                        'id_branch_destination' => @$detail['id_branch_destination'],
                        'id_location_destination' => @$detail['id_location_destination'],
                        'id_asset_location_destination' => @$detail['id_asset_location_destination'],
                        'unit_assigned' => $detail['unit_assigned'],
                        'effective_date' => $detail['effective_date'],
                        'status' => $detail['status'],
                    ]);
                    if(isset($detail['id_transfer_detail'])) {
                        $transferDetail = AssetTransferDetail::findOrFail($detail['id_transfer_detail']);
                        $transferDetail->update($detail);
                    } else {
                        $asset = Asset::findOrFail($detail['id_asset']);
                        $asset->assigned_employee = $asset->assignedEmployee;
                        foreach($asset->assigned_employee as $assignedEmployee) {
                            $detailData = array_merge($detail, [
                                'id_transfer_header' => $transfer->id_transfer_header,
                                'id_company' => session('id_company'),
                                'created_by' => session('id_user'),
                                'id_employee_source' => $assignedEmployee->id_employee,
                                'id_account_source' => $assignedEmployee->id_account,
                                'id_branch_source' => $assignedEmployee->id_branch,
                                'id_location_source' => $assignedEmployee->id_location,
                                'id_asset_location_source' => $assignedEmployee->id_asset_location,
                                'id_employee_destination' => @$detail['id_employee_destination'],
                                'id_account_destination' => @$detail['id_account_destination'],
                                'id_branch_destination' => @$detail['id_branch_destination'],
                                'id_location_destination' => @$detail['id_location_destination'],
                                'id_asset_location_destination' => @$detail['id_asset_location_destination'],
                                'unit_assigned' => $detail['unit_assigned'],
                                'effective_date' => $detail['effective_date'],
                                'status' => $detail['status'],
                            ]);
                            $transferDetail = AssetTransferDetail::create($detailData);
                        }
                        $totalAssignedUnits = AssetEmployeeAssigned::active()->where('id_asset', $asset->id_asset)->sum('unit_assigned');
                        if($totalAssignedUnits > $asset->current_units) {
                            throw new Exception("Asset ".$asset->asset_number." (".$asset->description.") has more units assigned ($totalAssignedUnits) than asset total ($asset->current_units)!");
                        }
                    }
                }
            } else {
                if($request->submission_type == 'submit') {
                    throw new Exception("Transfer detail is required!");
                }
            }
            DB::commit();
            $msg = $request->submission_type == 'submit' ? 'submitted' : 'saved';
            return $this->success($transfer, "Asset transfer request $msg successfully!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function summaryIndex(Request $request) {
        if($request->ajax()) {
            $transfers = AssetTransferHeader::currentCompany()->chronological(true)->get();
            foreach($transfers as $transfer) {
                $transfer->approval;
                $transfer->documentStatus;
            }
            return DataTables::of($transfers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_transfer_header;
                })
                ->addColumn('source', function($row) {
                    return 'summary';
                })
                ->make();
        }
        return view('assets.transfer_settings.asset_transfer.index', [
            'source' => 'summary',
        ]);
    }

    public function generateJournalTransfer(Request $request) {
        $request->validate([
            'id_transfer_header' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $idCompany = session('id_company');
            $transfer = AssetTransferHeader::findOrFail($request->id_transfer_header);
            $data = $this->generateJournal($transfer, $idCompany);
            DB::commit();
            return $this->success($data, 'Journal has been generated successfully!');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}