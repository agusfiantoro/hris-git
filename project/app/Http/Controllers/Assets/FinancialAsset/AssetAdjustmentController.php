<?php
namespace App\Http\Controllers\Assets\FinancialAsset;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetAdjustmentUpdateRequest;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\FinancialAsset\AdjustmentDetail;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\FinancialAsset\AdjustmentHeader;
use App\Models\Assets\MasterChartAccount;
use Exception;
use App\Models\Assets\RetirementDisposal\RetirementDetail;
use App\Models\Assets\RetirementDisposal\RetirementHeader;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\Assets\TransferSettings\MasterApprovalAssetDetail;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\Assets\AssetsApproval;
use App\Traits\Assets\FinancialAsset;
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssetAdjustmentController extends Controller {	
    use StandardResponse, FinancialAsset, AssetsApproval;
    
    public function index(Request $request) {
        if($request->ajax()) {
            $adjustments = AdjustmentHeader::currentCompany()->chronological(true)->get();
            foreach($adjustments as $adjustment) {
                $adjustment->approval;
                $adjustment->documentStatus;
                $adjustment->journalHeader;
                $adjustment->gl_transfer_flag = $adjustment->gl_transfer_flag ? 'Yes' : 'No';
            }
            return DataTables::of($adjustments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_adjustment_header;
                })
                ->make();
        }
        return view('assets.financial_asset.adjustment.index');
    }

    public function getData(Request $request) {
        if($request->id_asset) {
            $asset = Asset::findOrFail($request->id_asset);
            $select = null;
            if($asset->asset_type == 'Capitalized') {
                $select = "id_asset_cost_account";
            } else if($asset->asset_type == "CIP") {
                $select = "id_cip_cost_account";
            } else if($asset->asset_type == "Expense") {
                $select = "id_expense_cost_account";
            }
            if(!$select) {
                throw new Exception("Asset type invalid");
            }
            $asset->asset_category = $asset->assetCategory()->formatSelect2(null, null, ["$select as id_account"])->first();
            return $this->success($asset);
        }
        $approvalStatus = MasterGeneralData::join('public.master_general_type as mgt', 'master_general_data.id_general_type', 'mgt.id_general_type')
                        ->where('mgt.general_type', 'master_approval_status')
                        ->where('master_general_data.id_company', session('id_company'))
                        ->where('master_general_data.status', 'A')
                        ->get(['master_general_data.id_general_data', 'master_general_data.description as text']);
        $employee = HrEmployee::currentCompany()->active()->orderByDesc('id_employee')->where('id_user', session('id_user'))->first();
        $approvalHeader = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Adjustment')->first();
        return $this->success([
            'approval_status' => $approvalStatus,
            'approval_hierarchy' => $this->getApprovalList($approvalHeader, $employee->id_employee, session('id_company')),
            'periods' => MasterPeriod::currentCompany()->chronological()->get(['id_period as id', 'description as text']),
            'assets' => Asset::currentCompany()->active()->hasAssignedEmployee()->get(['fa_asset.id_asset as id', 'fa_asset.asset_number as text']),
            'employees' => HrEmployee::currentCompany()->get(['id_employee as id', 'name as text']),
            'id_employee' => $employee->id_employee,
            'accounts' => MasterChartAccount::currentCompany()->active()->formatSelect2('id_account', 'account_name')->get(),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_adjustment_header' => 'required',
        ]);
        $adjustment = AdjustmentHeader::with([
                'details',
                'documentStatus',
                'approval',
            ])->findOrFail($request->id_adjustment_header);

        $adjustment->id_employee = HrEmployee::where('id_user', $adjustment->created_by)->orderByDesc('creation_date')->first()->id_employee ?? null;
        $adjustment->approval_transactions = $this->getApprovalTransaction($adjustment);
        
        return $this->success($adjustment);
    }

    protected function generateRefNumber($idCompany) {
        $company = DB::table('master_company')->where('id_company', $idCompany)->where('status', 'A')->first();
        $time = now()->format('Y');
        $refNumber = "$company->company_code/ADJ/$time/";
        $maxNumber = DB::selectOne("SELECT max(reference_number) FROM asset.fa_adjustment_header frh WHERE reference_number LIKE ?", [$refNumber."%"]);
        $maxNumber = $maxNumber && $maxNumber->max ? sprintf("%04s", abs(substr($maxNumber->max, -4)+1)) : "0001";
        $refNumber .= now()->format('m/').$maxNumber;
        return $refNumber;
    }

    private function saveDetail(AdjustmentHeader $adjustment, array $adjustmentDetailArray, int $idUser) {
        $adjustmentDetailResults = [];
        foreach($adjustmentDetailArray as $detail) {
            if(isset($detail['id_adjustment_detail'])) {
                $adjustmentDetail = AdjustmentDetail::findorFail($detail['id_adjustment_detail']);
                $detailData = array_merge($detail, [
                    'current_cost' => trim(str_replace(".", "", $detail['current_cost']), " "),
                    'adjusted_cost' => trim(str_replace(".", "", $detail['adjusted_cost']), " "),
                    'updated_by' => $idUser,
                ]);
                $adjustmentDetail->update($detailData);
            } else {
                $detailData = array_merge($detail, [
                    'created_by' => $idUser,
                    'id_company' => $adjustment->id_company,
                    'id_adjustment_header' => $adjustment->id_adjustment_header,
                    'id_approval_status' => MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data,
                ]);
                $adjustmentDetail = AdjustmentDetail::create($detailData);
            }
            $adjustmentDetailResults[] = $adjustmentDetail;
        }
        return $adjustmentDetailResults;
    }

    public function save(AssetAdjustmentUpdateRequest $request) {

        $data = $request->only(array_keys($request->rules()));
        DB::beginTransaction();
        try {
            if($request->id_adjustment_header) {
                $draftIdGeneralData = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                ]);
                $adjustment = AdjustmentHeader::findOrFail($request->id_adjustment_header);
                if($adjustment->id_document_status == $draftIdGeneralData && $request->is_submit == "true") {
                    $data['id_document_status'] = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $adjustment->update($data);
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($adjustment, session('id_user'));
                }
                if($request->detail && count($request->detail) > 0) {
                    $adjustment->detail = $this->saveDetail($adjustment, $request->detail, session('id_user'));
                }
            } else {
                if($request->is_submit == "true") {
                    $documentStatus = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                } else {
                    $documentStatus = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $idApproval = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Adjustment')->first()->id_approval;
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                    'reference_number' => $this->generateRefNumber(session('id_company')),
                    'id_approval' => $idApproval,
                    'id_document_status' => $documentStatus,
                    'adjustment_date' => now()->format('Y-m-d'),
                    // 'id_je_header' => $this->findGlJeHeader("ADJUSTMENT", $request->id_period)->id_je_header,
                ]);
                $adjustment = AdjustmentHeader::create($data);
                if($request->detail && count($request->detail) > 0) {
                    $adjustment->detail = $this->saveDetail($adjustment, $request->detail, session('id_user'));
                } else {
                    throw new Exception("Adjustment detail is required!");
                }
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($adjustment, session('id_user'));
                }
            }
            DB::commit();
            return $this->success($adjustment, "Asset adjustment has been saved successfully!");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

}