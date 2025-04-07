<?php

namespace App\Models\Recruitment\HiringRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HiringReco extends Model
{
	use HasFactory;
	
    protected $table = 'hr_hiring_request_recommendation';
	protected $primaryKey = 'id_hiring_request_recommendation';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_hiring_request_recommendation', 'id_hiring_request_header', 'id_employee_recommendation', 'id_position_detail', 'id_location', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
