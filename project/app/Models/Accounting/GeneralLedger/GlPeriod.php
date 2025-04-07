<?php

namespace App\Models\Accounting\GeneralLedger;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlPeriod extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'accounting.gl_period';
    protected $primaryKey = 'id_period';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_period',
        'period_code',
        'description',
        'start_date',
        'end_date',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

}