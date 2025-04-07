<?php
namespace App\Http\Controllers\Assets\RetirementDisposal;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRetirementUpdateRequest;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\HrEmployee;
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
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssetRetirementController extends Controller {	
    use StandardResponse, AssetsApproval;
    
    public function index(Request $request) {
        if($request->ajax()) {
            $retirements = RetirementHeader::currentCompany()->chronological(true)->get();
            foreach($retirements as $retirement) {
                $retirement->approval;
                $retirement->documentStatus;
                $retirement->journalHeader;
                $retirement->gl_transfer_flag = $retirement->gl_transfer_flag ? 'Yes' : 'No';
            }
            return DataTables::of($retirements)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_retirement_header;
                })
                ->make();
        }
        return view('assets.retirement_disposal.retirement.index');
    }

    public function getData(Request $request) {
        if($request->id_asset) {
            return $this->success([
                'asset' => Asset::withAssignedEmployeeAndDetails(null, true)->select('asset.fa_asset.*', 'fea.*', 'fac.id_depreciation_reserve_account as id_account', 'fac.id_retired_gain_or_loss_account as id_counterpart_account')->findOrFail($request->id_asset),
            ]);
        }
        $approvalStatus = MasterGeneralData::join('public.master_general_type as mgt', 'master_general_data.id_general_type', 'mgt.id_general_type')
                        ->where('mgt.general_type', 'master_approval_status')
                        ->where('master_general_data.id_company', session('id_company'))
                        ->where('master_general_data.status', 'A')
                        ->get(['master_general_data.id_general_data', 'master_general_data.description as text']);
        $retiredAssets = Asset::retiredAssets(session('id_company'), "fa.id_asset as id, fa.asset_number as text");
        $unassignedAssets = Asset::currentCompany()
                                ->active()
                                ->withAssignedEmployeeAndDetails(null, true)
                                ->whereNotIn('asset.fa_asset.id_asset', $retiredAssets->pluck('id'))
                                ->whereNull('fea.id_employee')
                                ->formatSelect2(null, 'fa_asset.asset_number')
                                ->get();
        $employee = HrEmployee::currentCompany()->active()->orderByDesc('id_employee')->where('id_user', session('id_user'))->first();
        $approvalHeader = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Retirement')->first();
        return $this->success([
            'approval_status' => $approvalStatus,
            'approval_hierarchy' => $this->getApprovalList($approvalHeader, $employee->id_employee, session('id_company')),
            'periods' => MasterPeriod::currentCompany()->chronological()->get(['id_period as id', 'description as text']),
            'assets' => Asset::currentCompany()->formatSelect2(null, 'fa_asset.asset_number')->get(),
            'unassigned_assets' => $unassignedAssets,
            'retired_assets' => $retiredAssets,
            'employees' => HrEmployee::currentCompany()->get(['id_employee as id', 'name as text']),
            'id_employee' => $employee->id_employee,
            'branches' => MasterBranch::where('id_company', session('id_company'))->where('status', 'A')->get(['id_branch as id', 'description as text']),
            'accounts' => MasterChartAccount::currentCompany()->formatSelect2(null, 'account_name')->get(),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_retirement_header' => 'required',
        ]);
        $retirement = RetirementHeader::with([
                'details',
                'documentStatus',
                'approval',
            ])->findOrFail($request->id_retirement_header);

        $retirement->id_employee = HrEmployee::where('id_user', $retirement->created_by)->orderByDesc('creation_date')->first()->id_employee ?? null;
        $retirement->approval_transactions = $this->getApprovalTransaction($retirement);
        
        return $this->success($retirement);
    }

    protected function generateRefNumber($idCompany) {
        $company = DB::table('master_company')->where('id_company', $idCompany)->where('status', 'A')->first();
        $time = now()->format('Y');
        $refNumber = "$company->company_code/RET/$time/";
        $maxNumber = DB::selectOne("SELECT max(reference_number) FROM asset.fa_retirement_header frh WHERE reference_number LIKE ?", [$refNumber."%"]);
        $maxNumber = $maxNumber && $maxNumber->max ? sprintf("%04s", abs(substr($maxNumber->max, -4)+1)) : "0001";
        $refNumber .= now()->format('m/').$maxNumber;
        return $refNumber;
    }

    private function saveDetail(RetirementHeader $retirement, array $retirementDetailArray, int $idUser) {
        $retirementDetailResults = [];
        foreach($retirementDetailArray as $detail) {
            $asset = Asset::findOrFail($detail['id_asset']);
            $asset->asset_category = $asset->assetCategory;

            if(isset($detail['id_retirement_detail'])) {
                $retirementDetail = RetirementDetail::findorFail($detail['id_retirement_detail']);
                $detailData = array_merge($detail, [
                    'updated_by' => $idUser,
                    'id_account' => $asset->asset_category->id_depreciation_reserve_account,
                    'id_counterpart_account' => $asset->asset_category->id_depreciation_expense_account,
                    'id_approval_status' => $retirement->id_document_status,
                ]);
                $retirementDetail->update($detailData);
            } else {
                $detailData = array_merge($detail, [
                    'created_by' => $idUser,
                    'id_company' => $retirement->id_company,
                    'id_retirement_header' => $retirement->id_retirement_header,
                    'id_account' => $asset->asset_category->id_depreciation_reserve_account,
                    'id_counterpart_account' => $asset->asset_category->id_retired_gain_or_loss_account,
                    'id_approval_status' => $retirement->id_document_status,
                ]);
                $retirementDetail = RetirementDetail::create($detailData);
            }
            $retirementDetailResults[] = $retirementDetail;
        }
        return $retirementDetailResults;
    }

    public function save(AssetRetirementUpdateRequest $request) {

        $data = $request->only(array_keys($request->rules()));
        try {
            DB::beginTransaction();
            $mgdRetirement = MasterGeneralData::where('code', 'Asset_Retirement')->where('id_company', session('id_company'))->first();
            $approvalHeader = MasterApprovalAsset::currentCompany()->where('id_approval_doc_type', $mgdRetirement->id_general_data)->first();
            if($request->id_retirement_header) {
                $draftIdGeneralData = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                ]);
                $retirement = RetirementHeader::findOrFail($request->id_retirement_header);
                if($retirement->id_document_status == $draftIdGeneralData && $request->is_submit == "true") {
                    $data['id_document_status'] = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $retirement->update($data);
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($retirement, session('id_user'));
                }
                if($request->detail && count($request->detail) > 0) {
                    $retirement->detail = $this->saveDetail($retirement, $request->detail, session('id_user'));
                }
            } else {
                if(!$approvalHeader) {
                    throw new Exception("Cannot find approval for this transaction type");
                }
                if($request->is_submit == "true") {
                    $documentStatus = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                } else {
                    $documentStatus = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                    'reference_number' => $this->generateRefNumber(session('id_company')),
                    'id_approval' => $approvalHeader->id_approval,
                    'id_document_status' => $documentStatus,
                    'request_date' => now()->format('Y-m-d'),
                ]);
                $retirement = RetirementHeader::create($data);
                if($request->detail && count($request->detail) > 0) {
                    $retirement->detail = $this->saveDetail($retirement, $request->detail, session('id_user'));
                } else {
                    throw new Exception("Retirement detail is required!");
                }
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($retirement, session('id_user'));
                }
            }
            DB::commit();
            return $this->success($retirement, "Asset retirement has been saved successfully!");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function generateJournalTransfer(Request $request) {
        $request->validate([
            'id_retirement_header' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $idCompany = session('id_company');
            $retirement = RetirementHeader::findOrFail($request->id_retirement_header);
            $data = $this->generateJournal($retirement, $idCompany);
            DB::commit();
            return $this->success($data, 'Journal has been generated successfully!');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

}