<?php

namespace App\Models\Assets\ConfigSettings;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlJeCategories extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'accounting.gl_je_categories';
    protected $primaryKey = 'id_je_category';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_je_source',
        'category_code',
        'category_name',
        'restrict_by',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function scopeRestrictBy($query, string $restrictBy = 'User') {
        $query->where('restrict_by', $restrictBy);
    }
}