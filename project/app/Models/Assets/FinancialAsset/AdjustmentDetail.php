<?php

namespace App\Models\Assets\FinancialAsset;

use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentDetail extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_adjustment_detail';
    protected $primaryKey = 'id_adjustment_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_adjustment_header',
        'id_asset',
        'current_cost',
        'adjusted_cost',
        'id_account',
        'id_counterpart_account',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];
}