<?php

namespace App\Models\Assets\RetirementDisposal;

use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetirementDetail extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_retirement_detail';
    protected $primaryKey = 'id_retirement_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_retirement_header',
        'id_asset',
        'effective_date',
        'id_account',
        'id_counterpart_account',
        'id_branch_destination',
        'id_location_destination',
        'id_asset_location_destination',
        'unit_assigned',
        'id_approval_status',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];
}