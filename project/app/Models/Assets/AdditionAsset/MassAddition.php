<?php

namespace App\Models\Assets\AdditionAsset;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MassAddition extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_mass_additions';
    protected $primaryKey = 'id_mass_addition';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_asset_category',
        'asset_type',
        'id_asset_group',
        'description',
        'original_cost',
        'current_units',
        'id_partner',
        'id_purchase_receiving_header',
        'id_purchase_invoice_detail',
        'id_project',
        'id_batch',
        'id_depreciation_method',
        'depreciation_start_date',
        'life_in_month',
        'queue_process_status',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];


    public function assetCategory() {
        return $this->hasOne(MasterAssetCategory::class, 'id_asset_category', 'id_asset_category');
    }

    public function assetGroup() {
        return $this->hasOne(MasterAssetGroup::class, 'id_asset_group', 'id_asset_group');
    }
}