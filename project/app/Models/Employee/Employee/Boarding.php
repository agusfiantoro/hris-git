<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Boarding extends Model {

    use HasFactory;

    protected $table = 'hr_checklist_employee';
    protected $primaryKey = 'id_checklist_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_checklist_employee','id_employee','id_checklist', 'completed', 'remark', 'attachment_type', 'attachment', 'effective_date', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
