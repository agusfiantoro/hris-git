<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cert extends Model {

    use HasFactory;

    protected $table = 'hr_certification_employee';
    protected $primaryKey = 'id_certification_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_certification_employee', 'id_employee', 'certification_name', 'certified_by', 'notes', 'years_issued', 'validity_period', 'id_company', 'created_by', 'updated_by'
    ];

}
