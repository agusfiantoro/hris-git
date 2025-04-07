<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrApprovalDetail extends Model {
	
	protected $table = 'hr_approval_detail';
    protected $primaryKey = 'id_approval_detail';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_approval_detail', 'id_approval', 'sequence', 'id_employee', 'id_position_detail', 'id_approval_mode', 'limit', 'note', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
