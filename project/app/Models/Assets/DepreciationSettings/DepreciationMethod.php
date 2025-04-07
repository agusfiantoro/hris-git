<?php

namespace App\Models\Assets\DepreciationSettings;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepreciationMethod extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_depreciation_method';
    protected $primaryKey = 'id_depreciation_method';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'depreciation_code',
        'depreciation_rule',
        'description',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];

}