<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Skill extends Model {

    use HasFactory;

    protected $table = 'hr_skill_employee';
    protected $primaryKey = 'id_skill_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_skill_employee', 'id_employee', 'skill_name', 'skill_level', 'id_company', 'created_by', 'updated_by'
    ];

}
