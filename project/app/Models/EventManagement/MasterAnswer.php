<?php

namespace App\Models\EventManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterAnswer extends Model {
	
	protected $table = 'master_survey_answer';
    protected $primaryKey = 'id_answer';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_answer', 'description_answer', 'status', 'id_company', 'code', 'suggested_image', 'created_by', 'updated_by', 'answer_group_type'
    ];
}
