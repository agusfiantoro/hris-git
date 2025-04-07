<?php
namespace App\Http\Controllers\Assets\RetirementDisposal;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetReinstatementUpdateRequest;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\MasterChartAccount;
use App\Models\Assets\RetirementDisposal\ReinstateDetail;
use App\Models\Assets\RetirementDisposal\ReinstateHeader;
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

class AssetReinstatementController extends Controller {	
    use StandardResponse, AssetsApproval;
    
    public function index(Request $request) {
        if($request->ajax()) {
            $reinstatements = ReinstateHeader::currentCompany()->chronological(true)->get();
            foreach($reinstatements as $reinstate) {
                $reinstate->approval;
                $reinstate->documentStatus;
                $reinstate->journalHeader;
                $reinstate->gl_transfer_flag = $reinstate->gl_transfer_flag ? 'Yes' : 'No';
            }
            return DataTables::of($reinstatements)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_reinstate_header;
                })
                ->make();
        }
        return view('assets.retirement_disposal.reinstatement.index');
    }

    public function getData(Request $request) {
        if($request->id_retirement_header_source) {
            return $this->success([
                'retirement_details' => RetirementDetail::join('asset.fa_asset as fa', 'fa_retirement_detail.id_asset', 'fa.id_asset')
                                        ->join('asset.fa_asset_category as fac', 'fa.id_asset_category', 'fac.id_asset_category')
                                        ->where('fa_retirement_detail.id_retirement_header', $request->id_retirement_header_source)
                                        ->formatSelect2(null, 'fa.asset_number', ['fa_retirement_detail.*', 'fa.*', 'fac.id_depreciation_expense_account as id_account', 'fac.id_depreciation_reserve_account as id_counterpart_account'])
                                        ->currentCompany()
                                        ->active()
                                        ->get(),
            ]);
        }
        $approvalStatus = MasterGeneralData::join('public.master_general_type as mgt', 'master_general_data.id_general_type', 'mgt.id_general_type')
                        ->where('mgt.general_type', 'master_approval_status')
                        ->where('master_general_data.id_company', session('id_company'))
                        ->where('master_general_data.status', 'A')
                        ->get(['master_general_data.id_general_data', 'master_general_data.description as text']);
        $employee = HrEmployee::currentCompany()->active()->orderByDesc('id_employee')->where('id_user', session('id_user'))->first();
        $approvalHeader = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Reinstate')->first();
        return $this->success([
            'approval_status' => $approvalStatus,
            'approval_hierarchy' => $this->getApprovalList($approvalHeader, $employee->id_employee, session('id_company')),
            'periods' => MasterPeriod::currentCompany()->chronological()->get(['id_period as id', 'description as text']),
            'assets' => Asset::currentCompany()->active()->hasAssignedEmployee()->get(['fa_asset.id_asset as id', 'fa_asset.asset_number as text']),
            'employees' => HrEmployee::currentCompany()->get(['id_employee as id', 'name as text']),
            'id_employee' => $employee->id_employee,
            'branches' => MasterBranch::where('id_company', session('id_company'))->where('status', 'A')->get(['id_branch as id', 'description as text']),
            'retirement_headers' => RetirementHeader::currentCompany()->active()->approved()->formatSelect2(null, null, [DB::raw("CONCAT(fa_retirement_header.reference_number, ' - ', fa_retirement_header.description) as text")])->get(),
            'accounts' => MasterChartAccount::currentCompany()->formatSelect2(null, 'account_name')->get(),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_reinstate_header' => 'required',
        ]);
        $reinstatement = ReinstateHeader::with([
                'details',
                'documentStatus',
                'approval',
            ])->findOrFail($request->id_reinstate_header);

        $reinstatement->id_employee = HrEmployee::where('id_user', $reinstatement->created_by)->orderByDesc('creation_date')->first()->id_employee ?? null;
        $reinstatement->approval_transactions = $this->getApprovalTransaction($reinstatement);
        
        return $this->success($reinstatement);
    }

    protected function generateRefNumber($idCompany) {
        $company = DB::table('master_company')->where('id_company', $idCompany)->where('status', 'A')->first();
        $time = now()->format('Y');
        $refNumber = "$company->company_code/RIN/$time/";
        $maxNumber = DB::selectOne("SELECT max(reference_number) FROM asset.fa_retirement_header frh WHERE reference_number LIKE ?", [$refNumber."%"]);
        $maxNumber = $maxNumber && $maxNumber->max ? sprintf("%04s", abs(substr($maxNumber->max, -4)+1)) : "0001";
        $refNumber .= now()->format('m/').$maxNumber;
        return $refNumber;
    }

    private function saveDetail(ReinstateHeader $reinstate, array $reinstateDetailArray, int $idUser) {
        $reinstateDetailResults = [];
        foreach($reinstateDetailArray as $detail) {
            if(isset($detail['id_reinstate_detail'])) {
                $reinstateDetail = ReinstateDetail::findorFail($detail['id_reinstate_detail']);
                $detailData = array_merge($detail, [
                    'updated_by' => $idUser,
                ]);
                $reinstateDetail->update($detailData);
            } else {
                $retirementDetail = RetirementDetail::findOrFail($detail['id_retirement_detail_source']);
                $asset = Asset::findOrFail($retirementDetail->id_asset);
                $asset->asset_category = $asset->assetCategory;

                $detailData = array_merge($detail, [
                    'created_by' => $idUser,
                    'id_company' => $reinstate->id_company,
                    'id_reinstate_header' => $reinstate->id_reinstate_header,
                    'id_asset' => RetirementDetail::findOrFail($detail['id_retirement_detail_source'])->id_asset,
                    'id_account' => $asset->asset_category->id_depreciation_expense_account,
                    'id_counterpart_account' => $asset->asset_category->id_depreciation_reserve_account,
                    'id_approval_status' => MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data,
                ]);
                $reinstateDetail = ReinstateDetail::create($detailData);
            }
            $reinstateDetailResults[] = $reinstateDetail;
        }
        return $reinstateDetailResults;
    }

    public function save(AssetReinstatementUpdateRequest $request) {

        $data = $request->only(array_keys($request->rules()));
        DB::beginTransaction();
        try {
            $idApproval = MasterApprovalAsset::currentCompany()->active()->findByCode('Asset_Reinstate')->first();
            if(!$idApproval) {
                throw new Exception("Cannot find approval for this transaction type");
            }
            $idApproval = $idApproval->id_approval;
            if($request->id_reinstate_header) {
                $draftIdGeneralData = MasterGeneralData::where('code', 'New')->where('id_company', session('id_company'))->first()->id_general_data;
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                ]);
                $reinstate = ReinstateHeader::findOrFail($request->id_reinstate_header);
                if($reinstate->id_document_status == $draftIdGeneralData && $request->is_submit == "true") {
                    $data['id_document_status'] = MasterGeneralData::where('code', 'Request_Approval')->where('id_company', session('id_company'))->first()->id_general_data;
                }
                $reinstate->update($data);
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($reinstate, session('id_user'));
                }
                if($request->detail && count($request->detail) > 0) {
                    $reinstate->detail = $this->saveDetail($reinstate, $request->detail, session('id_user'));
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
                    'request_date' => now()->format('Y-m-d'),
                    'id_je_header' => null,
                ]);
                $reinstate = ReinstateHeader::create($data);
                if($request->detail && count($request->detail) > 0) {
                    $reinstate->detail = $this->saveDetail($reinstate, $request->detail, session('id_user'));
                } else {
                    throw new Exception("Reinstatement detail is required!");
                }
                if($request->is_submit == "true") {
                    $this->generateApprovalTransaction($reinstate, session('id_user'));
                }
            }
            DB::commit();
            return $this->success($reinstate, "Asset reinstatement has been saved successfully!");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function generateJournalTransfer(Request $request) {
        $request->validate([
            'id_reinstate_header' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $idCompany = session('id_company');
            $reinstate = ReinstateHeader::findOrFail($request->id_reinstate_header);
            $data = $this->generateJournal($reinstate, $idCompany);
            DB::commit();
            return $this->success($data, 'Journal has been generated successfully!');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}