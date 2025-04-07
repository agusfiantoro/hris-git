<?php

namespace App\Models\Assets\DepreciationSettings;

use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\ConfigSettings\GlJeHeaders;
use App\Models\Assets\ConfigSettings\GlJeSources;
use App\Models\Assets\MasterChartAccount;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_depreciation_detail';
    protected $primaryKey = 'id_depreciation_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    public function depreciationAccount() {
        return $this->belongsTo(MasterChartAccount::class, 'id_depreciation_account', 'id_account');
    }

    public function depreciationReserveAccount() {
        return $this->belongsTo(MasterChartAccount::class, 'id_depreciation_reserve_account', 'id_account');
    }

    public function period() {
        return $this->belongsTo(MasterPeriod::class, 'id_period', 'id_period');
    }

    public function jeHeader() {
        return $this->belongsTo(GlJeHeaders::class, 'id_je_header', 'id_je_header');
    }

    public function scopeJoinAssetPeriodAndJournal($query, $usePredefinedSelect = true) {
        $query->leftJoin('asset.fa_asset as xfa', "$this->table.id_asset", "xfa.id_asset");
        $query->leftJoin('asset.fa_period as xfp', "$this->table.id_period", "xfp.id_period");
        $query->leftJoin('accounting.gl_je_headers as xgjh', "$this->table.id_je_header", "xgjh.id_je_header");
        if($usePredefinedSelect) {
            $query->select("$this->table.*", "xfa.asset_number", "xfa.id_asset_category", "xfp.description as period_description", "xgjh.reference_number as je_reference_number", "$this->table.gl_transfer_flag");
        }
    }
}