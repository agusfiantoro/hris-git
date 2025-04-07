<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Insurance extends Model {

    use HasFactory;

    protected $table = 'hr_insurance_employee';
    protected $primaryKey = 'id_insurance_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_insurance_employee','id_employee','id_insurance', 'emp_insurance_number', 'emp_insurance_name', 'effective_date', 'expired_date', 'beneficiary_name', 'id_company', 'created_by', 'updated_by'
    ];

}
