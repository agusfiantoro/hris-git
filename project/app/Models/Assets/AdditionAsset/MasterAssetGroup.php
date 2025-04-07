<?php

namespace App\Models\Assets\AdditionAsset;

use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAssetGroup extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_asset_group';
    protected $primaryKey = 'id_asset_group';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'asset_group_code',
        'description',
        'note',
        'depreciation_flag',
        'id_depreciation_method',
        'life_in_month',
        'salvage_type',
        'salvage_value',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

}