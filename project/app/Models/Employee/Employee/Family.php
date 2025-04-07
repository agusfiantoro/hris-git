<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Family extends Model {

    use HasFactory;

    protected $table = 'hr_family_employee';
    protected $primaryKey = 'id_family_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_family_employee','id_employee', 'family_name', 'gender', 'relationship', 'mobile_phone', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
