<?php

namespace App\Models\Assets\ConfigSettings;

use App\Models\Accounting\GeneralLedger\GlJeLines;
use App\Models\Accounting\GeneralLedger\GlPeriod;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class GlJeHeaders extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'accounting.gl_je_headers';
    protected $primaryKey = 'id_je_header';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'description',
        'id_period',
        'accounting_date',
        'id_je_source',
        'id_je_category',
        'id_currency',
        'currency_rate',
        'status',
        'posted_date',
        'document_status',
        'updated_by',
    ];

    public function lines(): HasMany {
        return $this->hasMany(GlJeLines::class, 'id_je_header', 'id_je_header');
    }

    public function source(): HasOne {
        return $this->hasOne(GlJeSources::class, 'id_je_source', 'id_je_source');
    }

    public function category(): HasOne {
        return $this->hasOne(GlJeCategories::class, 'id_je_category', 'id_je_category');
    }

    public function scopeJoinCategoryPeriod($query, $usePredefinedSelect = true) {
        $query->leftJoin('accounting.gl_je_categories as xgjc', "$this->table.id_je_category", "xgjc.id_je_category");
        $query->leftJoin('accounting.gl_period as xgp', "$this->table.id_period", "xgp.id_period");
        $query->select("$this->table.*", "xgjc.category_name", "xgp.description as period_name");
    }

    public function period(): HasOne {
        return $this->hasOne(GlPeriod::class, 'id_period', 'id_period');
    }

    public static function getGlJeHeader($module, $type, $idPeriod) {
        return DB::selectOne("SELECT gjh.* FROM accounting.gl_je_headers gjh
                                JOIN accounting.gl_je_sources gjs ON gjh.id_je_source = gjs.id_je_source 
                                JOIN accounting.gl_je_categories gjc ON gjh.id_je_category = gjc.id_je_category
                                JOIN asset.fa_period fp ON gjh.id_period = fp.id_period
                                WHERE gjs.source_code = ?
                                AND gjc.category_code = ?
                                AND fp.id_period = ?
                                -- AND gjh.description LIKE 'BCP/NTB/2024/08/0008%'
                                "
                                , [$module, $type, $idPeriod]);
    }
}