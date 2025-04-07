<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrEventAttendees extends Model {

    use HasFactory;

    protected $table = 'hr_event_attendees';
    protected $primaryKey = 'id_event_attendees';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_event_attendees', 'id_event_management', 'id_event_program', 'booked_by', 'national_identity_card', 'attendee_name', 'attendee_email', 'attendee_phone', 'start_date', 'end_date', 'attachment', 'notes', 'invited_by', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function getAttendeesMinMaxDate($id_employee, $id_course_header, $id_event_program) {
        $findMinMax = "SELECT
                            max(hsauh.creation_date) AS max,
                            min(hsauh.creation_date) AS min,
                            hsauh.id_employee,
                            hsauh.id_course_detail,
                            mcd.id_course_header
                        FROM
                            hr_survey_answer_user_header hsauh
                        LEFT JOIN master_course_detail mcd ON
                            hsauh.id_course_detail = mcd.id_course_detail
                        LEFT JOIN hr_event_program hep ON
                            mcd.id_course_header = hep.id_course_header 
                        WHERE
                            hsauh.id_employee = ?
                            AND mcd.id_course_header = ?
                            AND hep.id_event_program = ?
                        GROUP BY
                            hsauh.id_employee,
                            hsauh.id_course_detail,
                            mcd.id_course_header";
        return DB::selectOne($findMinMax, [$id_employee, $id_course_header, $id_event_program]);
    }
}
