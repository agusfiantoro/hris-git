<?php

namespace App\Models\API;

use App\Models\CashAdvance\OfficialTravel\HrApprovalTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OasysIntegrationHeader extends Model {
    protected $table = "integration.biz_approval_integration_request_header";
    protected $primaryKey = 'id_integration_request_header';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_employee',
        'transaction_sources',
        'is_synchronize_flag',
        'synchronize_message',
        'id_approval',
        'id_approval_status',
        'note_rejected',
        'note_revised',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function approvals() {
        return $this->hasMany(HrApprovalTransaction::class, 'id_source_transaction', 'id_integration_request_header')
                ->join('master_general_data as mgd', 'hr_approval_transaction.id_approval_status', 'mgd.id_general_data')
                ->join('hr_employee as he', 'hr_approval_transaction.id_employee_approval', 'he.id_employee')
                ->select(
                    'mgd.description as approval_status', 
                    'he.name as name_employee_approval',
                    'he.nik_employee as nik_employee_approval'
                )
                ->where('source_transaction_type', 'Biz_Approval');
    }

    public function details() {
        return $this->hasMany(OasysIntegrationDetail::class, 'id_integration_request_header', 'id_integration_request_header')
                ->join('master_branch as mb', 'biz_approval_integration_request_detail.id_branch', 'mb.id_branch')
                ->join("master_principal as mp", "mp.id_principal", DB::raw("ANY(biz_approval_integration_request_detail.id_principal)"))
                ->select(
                    'id_integration_request_detail',
                    'mb.description as branch_name',
                    'mp.description as division_name',
                );
    }
}