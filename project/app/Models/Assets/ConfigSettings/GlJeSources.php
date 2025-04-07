<?php

namespace App\Models\Assets\ConfigSettings;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlJeSources extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'accounting.gl_je_sources';
    protected $primaryKey = 'id_je_source';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'source_code',
        'source_name',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

}