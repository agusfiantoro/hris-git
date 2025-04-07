<?php

namespace App\Models\Assets\FinancialAsset;

use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevaluationDetail extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_revaluation_detail';
    protected $primaryKey = 'id_revaluation_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_revaluation_header',
        'id_asset_category',
        'id_asset',
        'revaluation_amount',
        'revaluation_percentage',
        'id_account',
        'id_counterpart_account',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];
}