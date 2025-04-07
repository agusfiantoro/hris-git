<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bank extends Model {

    use HasFactory;

    protected $table = 'hr_bank_employee';
    protected $primaryKey = 'id_bank_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_bank_employee','id_employee','id_bank', 'bank_name', 'bank_account', 'account_name', 'bank_currency', 'default_bank', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
