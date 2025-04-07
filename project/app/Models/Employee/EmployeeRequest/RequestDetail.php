<?php

namespace App\Models\Employee\EmployeeRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RequestDetail extends Model {

    protected $table = 'hr_request_detail';
    protected $primaryKey = 'id_request_detail';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_request_detail', 'id_request_header', 'id_employee', 'request_start_to', 'request_end_to', 'actual_start_to', 'actual_end_to', 'note', 'id_employee_delegation', 'status', 'id_company', 'created_by', 'updated_by', 'qty_days', 'day_type'
    ];

}
