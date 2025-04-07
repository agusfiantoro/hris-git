<?php

namespace App\Models\Assets\FinancialAsset;

use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\ConfigSettings\GlJeHeaders;
use App\Models\Assets\MasterChartAccount;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAsset extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_financial_asset';
    protected $primaryKey = 'id_financial_asset';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_asset',
        'accounting_date',
        'id_period',
        'id_account',
        'id_counterpart_account',
        'financial_type',
        'basic_amount',
        'adjustment_amount',
        'current_amount',
        'gl_transfer_flag',
        'id_je_header',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];

    public function account() {
        return $this->belongsTo(MasterChartAccount::class, 'id_account', 'id_account');
    }

    public function period() {
        return $this->belongsTo(MasterPeriod::class, 'id_period', 'id_period');
    }

    public function jeHeader() {
        return $this->belongsTo(GlJeHeaders::class, 'id_je_header', 'id_je_header');
    }

    
}