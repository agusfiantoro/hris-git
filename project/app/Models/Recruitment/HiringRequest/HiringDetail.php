<?php

namespace App\Models\Recruitment\HiringRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HiringDetail extends Model
{
	use HasFactory;
	
    protected $table = 'hr_hiring_request_detail';
	protected $primaryKey = 'id_hiring_request_detail';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_hiring_request_detail', 'id_hiring_request_header', 'id_employee_replacement', 'id_position_detail_request', 'id_location', 'id_assigned_to_company', 'hiring_status', 'notes', 'update_date_notes', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
