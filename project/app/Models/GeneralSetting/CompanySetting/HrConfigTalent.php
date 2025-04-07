<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrConfigTalent extends Model
{
    // use HasFactory;
    protected $table = 'hr_config_engagement_category';
	protected $primaryKey = 'id_config_engagement_category';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}
