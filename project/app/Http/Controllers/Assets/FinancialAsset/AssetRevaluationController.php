<?php
namespace App\Http\Controllers\Assets\FinancialAsset;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRevaluationUpdateRequest;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\FinancialAsset\AdjustmentHeader;
use App\Models\Assets\FinancialAsset\RevaluationDetail;
use App\Models\Assets\FinancialAsset\RevaluationHeader;
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

class AssetRevaluationController extends Controller {	
    use StandardResponse, FinancialAsset, AssetsApproval;
    
    public function index(Request $request) {
        if($request->ajax()) {
            $revaluations = RevaluationHeader::currentCompany()->chronological(true)->get();
            foreach($revaluations as $revaluation) {
                $revaluation->gl_transfer_flag = $revaluation->gl_transfer_flag ? 'Yes' : 'No';
                $revaluation->approval;
                $revaluation->documentStatus;
                $revaluation->journalHeader;
            }
            return DataTables::of($revaluations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_revaluation_header;
                })
                ->make();
        }
        return view('assets.financial_asset.revaluation.index');
    }

    public function getData(Request $request) {
        if($request->id_asset_category) {
            return $this->success([
                'assets' => Asset::where('id_asset_category', $request->id_asset_category)->currentCompany()->active()->hasAssignedEmployee()->get(['fa_asset.id_asset as id', 'fa_asset.asset_number as text']),
            ]);
        }
        if($request->id_asset) {
            $asset = Asset::findOrFail($request->id_asset);
            $asset->asset_category = $asset->assetCategory()->formatSelect2(null, null, ['id_revaluation_amortization_account as id_account', 'id_revaluation_reserve_account as id_counterpart_account'])->first();
            return $this->success([
                'asset_category' => $asset->asset_category,
            ]);
        }
        $approvalStatus = MasterGeneralData::join('public.master_general_type as mgt', 'master_general_data.id_general_type', 'mgt.id_general_type')
                        ->where('mgt.general_type', 'master_approval_status')
                        ->where('master_general_data.id_company', session('id_company'))
                        ->where('master_general_data.status', 'A')
                        ->get(['master_general_data.id_general_data', 'master_general_data.description as text']);
        $employee = HrEmployee::currentCompany()->active()->orderByDesc('id_employee')->where('id_user', session('id_user'))->first();
        $approvalHeader = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Revaluation')->first();
        return $this->success([
            'approval_status' => $approvalStatus,
            'approval_hierarchy' => $this->getApprovalList($approvalHeader, $employee->id_employee, session('id_company')),
            'periods' => MasterPeriod::currentCompany()->chronological()->get(['id_period as id', 'description as text']),
            'asset_categories' => MasterAssetCategory::currentCompany()->active()->formatSelect2(null, null, ['id_revaluation_amortization_account as id_account', 'id_revaluation_reserve_account as id_counterpart_account'])->get(),
            'employees' => HrEmployee::currentCompany()->get(['id_employee as id', 'name as text']),
            'id_employee' => $employee->id_employee,
            'branches' => MasterBranch::where('id_company', session('id_company'))->where('status', 'A')->get(['id_branch as id', 'description as text']),
            'accounts' => MasterChartAccount::currentCompany()->active()->formatSelect2(null, 'account_name')->get(),
            'assets' => Asset::currentCompany()->active()->hasAssignedEmployee()->get(['fa_asset.id_asset as id', 'fa_asset.asset_number as text']),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_revaluation_header' => 'required',
        ]);
        $revaluation = RevaluationHeader::with([
                'details',
                'documentStatus',
                'approval',
            ])->findOrFail($request->id_revaluation_header);

        $revaluation->id_employee = HrEmployee::where('id_user', $revaluation->created_by)->orderByDesc('creation_date')->first()->id_employee ?? null;
        $revaluation->approval_transactions = $this->getApprovalTransaction($revaluation);
        
        return $this->success($revaluation);
    }

    protected function generateRefNumber($idCompany) {
        $company = DB::table('master_company')->where('id_company', $idCompany)->where('status', 'A')->first();
        $time = now()->format('Y');
        $refNumber = "$company->company_code/RVL/$time/";
        $maxNumber = DB::selectOne("SELECT max(reference_number) FROM asset.fa_revaluation_header frh WHERE reference_number LIKE ?", [$refNumber."%"]);
        $maxNumber = $maxNumber && $maxNumber->max ? sprintf("%04s", abs(substr($maxNumber->max, -4)+1)) : "0001";
        $refNumber .= now()->format('m/').$maxNumber;
        return $refNumber;
    }

    private function saveDetail(RevaluationHeader $revaluation, array $detailArray, int $idUser) {
        $detailResults = [];
        foreach($detailArray as $detail) {
            if(isset($detail['id_revaluation_detail'])) {
                $detailModel = RevaluationDetail::findorFail($detail['id_revaluation_detail']);
                $detailData = array_merge($detail, [
                    'updated_by' => $idUser,
                ]);
                $detailModel->update($detailData);
            } else {
                $detailData = array_merge($detail, [
                    'created_by' => $idUser,
                    'id_company' => $revaluation->id_company,
                    'id_revaluation_header' => $revaluation->id_revaluation_header,
                    'id_approval_status' => MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data,
                ]);
                $detailModel = RevaluationDetail::create($detailData);
            }
            $detailResults[] = $detailModel;
        }
        return $detailResults;
    }

    public function save(AssetRevaluationUpdateRequest $request) {

        $data = $request->only(array_keys($request->rules()));
        DB::beginTransaction();
        try {
            $approvalHeader = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Revaluation')->first();
            $idApproval = $approvalHeader->id_approval;

            if($request->id_revaluation_header) {
                $draftIdGeneralData = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                ]);
                $revaluation = RevaluationHeader::findOrFail($request->id_revaluation_header);
                if($revaluation->id_document_status == $draftIdGeneralData && $request->is_submit == "true") {
                    $data['id_document_status'] = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $revaluation->update($data);
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($revaluation, session('id_user'));
                }
                if($request->detail && count($request->detail) > 0) {
                    $revaluation->detail = $this->saveDetail($revaluation, $request->detail, session('id_user'));
                }
            } else {
                if($request->is_submit == "true") {
                    $documentStatus = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                } else {
                    $documentStatus = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                    'reference_number' => $this->generateRefNumber(session('id_company')),
                    'id_approval' => $idApproval,
                    'id_document_status' => $documentStatus,
                    'revaluation_date' => now()->format('Y-m-d'),
                    // 'id_je_header' => $this->findGlJeHeader("REVALUATION", $request->id_period)->id_je_header,
                ]);
                $revaluation = RevaluationHeader::create($data);
                if($request->detail && count($request->detail) > 0) {
                    $revaluation->detail = $this->saveDetail($revaluation, $request->detail, session('id_user'));
                } else {
                    throw new Exception("Adjustment detail is required!");
                }
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($revaluation, session('id_user'));
                }
            }
            DB::commit();
            return $this->success($revaluation, "Asset revaluation has been saved successfully!");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

}