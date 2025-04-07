<?php

namespace App\Models\Assets\Approval;

use App\Models\Assets\MasterJobGrade;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetApprovalTransaction extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_approval_transaction';
    protected $primaryKey = 'id_approval_transaction';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_source_transaction',
        'source_transaction_type',
        'id_approval',
        'id_approval_detail',
        'id_approval_mode',
        'sequence',
        'id_position_detail',
        'id_employee_approval',
        'id_approval_status',
        'note_revised',
        'note_rejected',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function scopeTransactionType($query, $type) {
        $query->where("$this->table.source_transaction_type", $type);
    }

    public function scopeIdSourceTransaction($query, $idSourceTransaction) {
        $query->where("$this->table.id_source_transaction", $idSourceTransaction);
    }
}