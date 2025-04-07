<?php

namespace App\Models\Accounting\GeneralLedger;

use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GlJeLines extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'accounting.gl_je_lines';
    protected $primaryKey = 'id_je_lines';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_je_header',
        'sequence',
        'id_account',
        'description',
        'currency_rate',
        'entered_debit_amount',
        'entered_credit_amount',
        'transaction_reference',
        'id_taxes',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];
}