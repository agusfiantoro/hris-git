<?php
namespace App\Http\Controllers\Assets\TransferSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetApprovalUpdateRequest;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\MasterJobGrade;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\Assets\TransferSettings\MasterApprovalAssetDetail;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MasterApprovalController extends Controller {	
    use StandardResponse;
    
    public function index(Request $request) {
        if($request->ajax()) {
            $approvals = MasterApprovalAsset::currentCompany()->get();
            foreach($approvals as $approval) {
                $approval->job_grade = $approval->jobGrade;
                $approval->approval_document_type = $approval->approvalDocumentType;
            }
            return DataTables::of($approvals)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_approval;
                })
                ->make();
        }
        return view('assets.transfer_settings.master_asset_approval.index');
    }

    public function getData() {
        $approvalMode = MasterGeneralData::join('public.master_general_type as mgt', 'master_general_data.id_general_type', 'mgt.id_general_type')
                        ->where('mgt.general_type', 'master_approval_mode')
                        ->where('master_general_data.id_company', session('id_company'))
                        ->where('master_general_data.status', 'A')
                        ->get(['master_general_data.id_general_data as id', 'master_general_data.description as text']);
        $approvalDocType = MasterGeneralData::join('public.master_general_type as mgt', 'master_general_data.id_general_type', 'mgt.id_general_type')
                        ->where('master_general_data.id_company', session('id_company'))
                        ->where('master_general_data.status', 'A')
                        ->where('mgt.general_type', 'master_asset_approval_document_type')
                        ->get(['master_general_data.id_general_data as id', 'master_general_data.description as text']);
        return $this->success([
            'job_grade' => MasterJobGrade::currentCompany()->active()->get(['id_job_grade as id', 'description as text']),
            'approval_doc_type' => $approvalDocType,
            'approval_mode' => $approvalMode,
            'employee' => HrEmployee::selectRaw("id_employee as id, CONCAT(name, ' (', nik_employee, ')') as text")->currentCompany()->active()->orderBy('name')->get(),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_approval' => 'required',
        ]);

        $approval = MasterApprovalAsset::findOrFail($request->id_approval);
        $approval->approval_detail = $approval->approvalDetail;
        return $this->success($approval);
    }

    private function saveDetail(MasterApprovalAsset $headerInstance, array $detailData) {
        foreach($detailData as $detail) {
            $positionDetail = DB::table('master_position_detail')->where('id_employee', $detail['id_employee'])->first();
            if($detail['id_approval_detail']) {
                $approvalDetail = MasterApprovalAssetDetail::findOrFail($detail['id_approval_detail']);
                $data = array_merge($detail, [
                    'id_approval' => $headerInstance->id_approval,
                    'updated_by' => session('id_user'),
                    'id_position_detail' => $positionDetail->id_position_detail,
                ]);
                $approvalDetail->update($data);
            } else {
                $data = array_merge($detail, [
                    'id_approval' => $headerInstance->id_approval,
                    'created_by' => session('id_user'),
                    'id_company' => $headerInstance->id_company,
                    'id_position_detail' => $positionDetail->id_position_detail,
                ]);
                $approvalDetail = MasterApprovalAssetDetail::create($data);
            }
        }
    }

    public function save(AssetApprovalUpdateRequest $request) {

        DB::beginTransaction();
        try {
            $autoApprove = false;
            if($request->is_auto_approved !== NULL) {
                $autoApprove = true;
            }
            $data = $request->only(array_keys($request->rules()));
            if($request->id_approval) {
                $approval = MasterApprovalAsset::findOrFail($request->id_approval);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                    'is_auto_approved' => $autoApprove,
                ]);
                $approval->update($data);
            } else {
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                    'is_auto_approved' => $autoApprove,
                    'id_company' => session('id_company'),
                ]);
                $approval = MasterApprovalAsset::create($data);
            }

            if($request->detail && count($request->detail) > 0) {
                $this->saveDetail($approval, $request->detail);
            }
            DB::commit();
            return $this->success($approval, 'Transfer approval data has been saved successfully!');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}