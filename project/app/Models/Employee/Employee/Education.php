<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Education extends Model {

    use HasFactory;

    protected $table = 'hr_education_employee';
    protected $primaryKey = 'id_education_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_education_employee','id_employee', 'major', 'education_name', 'id_education_level', 'start_year', 'end_year', 'education_city', 'id_company', 'created_by', 'updated_by'
    ];

}
