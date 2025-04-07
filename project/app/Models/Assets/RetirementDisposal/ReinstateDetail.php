<?php

namespace App\Models\Assets\RetirementDisposal;

use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReinstateDetail extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_reinstate_detail';
    protected $primaryKey = 'id_reinstate_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_reinstate_header',
        'id_retirement_detail_source',
        'id_asset',
        'effective_date',
        'id_account',
        'id_counterpart_account',
        'id_approval_status',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];
}