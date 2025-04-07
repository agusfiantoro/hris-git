<?php

namespace App\Models\Assets\AdditionAsset;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLocation extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_asset_location';
    protected $primaryKey = 'id_asset_location';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'location_code',
        'description',
        'id_location',
        'id_branch',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

}