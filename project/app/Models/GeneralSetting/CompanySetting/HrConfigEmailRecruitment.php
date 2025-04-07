<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrConfigEmailRecruitment extends Model
{
    // use HasFactory;
	protected $table = 'hr_config_email_recruitment';
	protected $primaryKey = 'id_config_email_recruitment';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}
