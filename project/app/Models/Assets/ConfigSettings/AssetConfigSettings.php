<?php

namespace App\Models\Assets\ConfigSettings;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetConfigSettings extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_config_settings';
    protected $primaryKey = 'id_config_setting';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'revaluation_flag',
        'impairment_flag',
        'id_je_source',
        'id_asset_journal_category',
        'id_depreciation_journal_category',
        'id_adjustment_journal_category',
        'id_retirement_journal_category',
        'id_revaluation_journal_category',
        'id_impairment_journal_category',
        'id_reinstate_journal_category',
        'next_month_depreciation_start',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function jeSource() {
        return $this->hasOne(GlJeSources::class, 'id_je_source', 'id_je_source');
    }

    public function assetJournalCategory() {
        return $this->belongsTo(GlJeCategories::class, 'id_asset_journal_category', 'id_je_category');
    }

    public function depreciationJournalCategory() {
        return $this->belongsTo(GlJeCategories::class, 'id_depreciation_journal_category', 'id_je_category');
    }

    public function adjustmentJournalCategory() {
        return $this->belongsTo(GlJeCategories::class, 'id_adjustment_journal_category', 'id_je_category');
    }

    public function retirementJournalCategory() {
        return $this->belongsTo(GlJeCategories::class, 'id_retirement_journal_category', 'id_je_category');
    }

    public function revaluationJournalCategory() {
        return $this->belongsTo(GlJeCategories::class, 'id_revaluation_journal_category', 'id_je_category');
    }

    public function impairmentJournalCategory() {
        return $this->belongsTo(GlJeCategories::class, 'id_impairment_journal_category', 'id_je_category');
    }

    public function reinstateJournalCategory() {
        return $this->belongsTo(GlJeCategories::class, 'id_reinstate_journal_category', 'id_je_category');
    }
}