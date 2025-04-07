<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrConfigSurvey extends Model
{
    // use HasFactory;
    protected $table = 'hr_config_survey_update';
	protected $primaryKey = 'id_config_survey_update';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}
