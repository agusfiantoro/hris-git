<?php

namespace App\Models\Assets\TransferSettings;

use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterApprovalAssetDetail extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_approval_detail';
    protected $primaryKey = 'id_approval_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_approval',
        'sequence',
        'id_position_detail',
        'id_employee',
        'id_approval_mode',
        'limit',
        'note',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function header() {
        return $this->belongsTo(MasterApprovalAsset::class, 'id_approval', 'id_approval');
    }
}