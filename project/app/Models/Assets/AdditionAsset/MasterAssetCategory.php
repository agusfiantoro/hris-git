<?php

namespace App\Models\Assets\AdditionAsset;

use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAssetCategory extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_asset_category';
    protected $primaryKey = 'id_asset_category';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'asset_category_code',
        'description',
        'category_type',
        'ownership',
        'property_type',
        'asset_classification',
        'physical_inventory_flag',
        'depreciation_flag',
        'id_depreciation_method',
        'life_in_month',
        'id_asset_cost_account',
        'id_asset_clearing_account',
        'id_cip_cost_account',
        'id_cip_clearing_account',
        'id_expense_cost_account',
        'id_expense_clearing_account',
        'id_depreciation_expense_account',
        'id_depreciation_reserve_account',
        'id_revaluation_amortization_account',
        'id_revaluation_reserve_account',
        'id_impairment_expense_account',
        'id_impairment_reserve_account',
        'id_proceeds_sale_gain_or_loss_account',
        'id_proceeds_sale_clearing_account',
        'id_cost_removal_gain_or_loss_account',
        'id_cost_removal_clearing_account',
        'id_retired_gain_or_loss_account',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function depreciationMethod() {
        return $this->hasOne(DepreciationMethod::class, 'id_depreciation_method', 'id_depreciation_method');
    }
    
}