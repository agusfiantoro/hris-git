<?php

namespace App\Models\Assets\RetirementDisposal;

use App\Models\Assets\ConfigSettings\GlJeHeaders;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetirementHeader extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_retirement_header';
    protected $primaryKey = 'id_retirement_header';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'reference_number',
        'transaction_type',
        'request_date',
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

    public function scopeApproved($query) {
        $query->join('public.master_general_data as xmgd', "$this->table.id_document_status", "xmgd.id_general_data");
        $query->where('xmgd.code', 'Approved');
    }

    public function approval() {
        return $this->hasOne(MasterApprovalAsset::class, 'id_approval', 'id_approval');
    }

    public function documentStatus() {
        return $this->belongsTo(MasterGeneralData::class, 'id_document_status', 'id_general_data');
    }

    public function details() {
        return $this->hasMany(RetirementDetail::class, 'id_retirement_header', 'id_retirement_header');
    }

    public function journalHeader() {
        return $this->hasOne(GlJeHeaders::class, 'id_je_header', 'id_je_header');
    }
}