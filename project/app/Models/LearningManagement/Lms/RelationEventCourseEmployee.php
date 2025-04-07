<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationEventCourseEmployee extends Model {
	
	protected $table = 'relation_event_course_employee';
    protected $primaryKey = 'id_event_course_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
}