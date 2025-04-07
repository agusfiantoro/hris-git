<?php

namespace App\Models\Assets\FinancialAsset;

use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpairmentDetail extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_impairment_detail';
    protected $primaryKey = 'id_impairment_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_impairment_header',
        'id_asset_category',
        'id_asset',
        'impairment_amount',
        'impairment_percentage',
        'id_account',
        'id_counterpart_account',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];
}