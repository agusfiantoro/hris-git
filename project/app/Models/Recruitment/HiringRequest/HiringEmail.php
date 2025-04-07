<?php

namespace App\Models\Recruitment\HiringRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HiringEmail extends Model
{
	use HasFactory;
	
    protected $table = 'hr_config_email_recruitment';
	protected $primaryKey = 'id_config_email_recruitment';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_config_email_recruitment', 'id_hr_config', 'id_position_detail', 'recruitment_email', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
