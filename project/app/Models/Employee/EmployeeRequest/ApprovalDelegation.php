<?php

namespace App\Models\Employee\EmployeeRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovalDelegation extends Model {

    protected $table = 'hr_approval_delegation';
    protected $primaryKey = 'id_delegation';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_delegation','id_request_header', 'id_request_detail', 'id_source_employee_approval', 'id_dest_employee_approval', 'start_date', 'end_date', 'id_company', 'created_by', 'updated_by'
    ];

}
