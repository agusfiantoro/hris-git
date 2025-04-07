<?php

namespace App\Models\Assets\TransferSettings;

use App\Models\Assets\MasterJobGrade;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterApprovalAsset extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_approval_header';
    protected $primaryKey = 'id_approval';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'description',
        'hierarchy_type',
        'approval_mode',
        'id_approval_doc_type',
        'id_job_grade',
        'is_auto_approved',
        'note',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function approvalDocumentType() {
        return $this->belongsTo(MasterGeneralData::class, 'id_approval_doc_type', 'id_general_data');
    }

    public function approvalDetail() {
        return $this->hasMany(MasterApprovalAssetDetail::class, 'id_approval', 'id_approval');
    }

    public function jobGrade() {
        return $this->hasOne(MasterJobGrade::class, 'id_job_grade', 'id_job_grade');
    }

    public function scopeFindByCode($query, $code) {
        $query ->join('public.master_general_data as mgd', "$this->table.id_approval_doc_type", 'mgd.id_general_data')
                ->select("$this->table.*")
                ->where('mgd.code', $code);
    }

    /**
     * Scope a query to only show master approval data from current company (by active session)
     */
    public function scopeCurrentCompany($query) {
        $query->where("$this->table.id_company", session('id_company'));
    }
}