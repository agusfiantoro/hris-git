<?php

namespace App\Models\Assets\FinancialAsset;

use App\Models\Assets\ConfigSettings\GlJeHeaders;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevaluationHeader extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_revaluation_header';
    protected $primaryKey = 'id_revaluation_header';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'reference_number',
        'revaluation_date',
        'revaluation_method',
        'id_period',
        'description',
        'id_approval',
        'note_revised',
        'note_rejected',
        'id_document_status',
        'gl_transfer_flag',
        'id_je_header',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];

    public function approval() {
        return $this->hasOne(MasterApprovalAsset::class, 'id_approval', 'id_approval');
    }

    public function documentStatus() {
        return $this->belongsTo(MasterGeneralData::class, 'id_document_status', 'id_general_data');
    }

    public function details() {
        return $this->hasMany(RevaluationDetail::class, 'id_revaluation_header', 'id_revaluation_header');
    }

    public function journalHeader() {
        return $this->hasOne(GlJeHeaders::class, 'id_je_header', 'id_je_header');
    }
}