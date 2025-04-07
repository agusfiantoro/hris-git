<?php

namespace App\Models\Assets\AdditionAsset;

use App\Models\Assets\DepreciationSettings\AssetDepreciation;
use App\Models\Assets\FinancialAsset\FinancialAsset;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetImage extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_image_asset';
    protected $primaryKey = 'id_image_asset';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_image_asset',
        'id_asset',
        'attachment',
        'note',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];
}