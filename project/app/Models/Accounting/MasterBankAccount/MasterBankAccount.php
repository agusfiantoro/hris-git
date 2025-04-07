<?php

namespace App\Models\Accounting\MasterBankAccount;

use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterBankAccount extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'accounting.master_bank_account';
    protected $primaryKey = 'id_bank_account';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_bank',
        'account_number',
        'account_name',
        'branch_name',
        'id_branch',
        'bank_type',
        'status',
        'is_default',
        'id_company',
        'created_by'
    ];
}