<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Experience extends Model {

    use HasFactory;

    protected $table = 'hr_experience_employee';
    protected $primaryKey = 'id_experience_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_experience_employee', 'id_employee', 'position_name', 'company_name', 'id_education_level', 'start_year', 'end_year', 'company_city', 'id_company', 'created_by', 'updated_by'
    ];

}
