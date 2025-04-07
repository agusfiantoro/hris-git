<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrEventProgram extends Model {

    use HasFactory;

    protected $table = 'hr_event_program';
    protected $primaryKey = 'id_event_program';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_event_management', 'description', 'id_course_header', 'id_checklist', 'id_survey_question', 'start_date', 'end_date', 'pass_scores', 'maximum', 'status', 'id_company', 'created_by', 'updated_by', 'sequence', 'id_trainer',
    ];

    public static function get_program() {
        $data = DB::select("SELECT
                                hem.id_event_management,
                                hep.id_event_program,
                                hep.id_course_header,
                                he2.id_employee,
                                hep.start_date,
                                hep.end_date,
                                he.name,
                                hem.description AS event_name,
                                mch.course_name AS course_name,
                                mch.notes,
                                hem.venue,
                                hem.status,
                                hea.booked_by AS id_employee,
                                hea.start_date AS start_date_attendee,
                                hea.end_date AS end_date_attendee,
                                hsauh.id_survey_answer_user_header,
                                hsauh.total_score,
                                hsauh.creation_date AS answered_date,
                                CASE
                                    WHEN hep.pass_scores = 1 THEN hsauh.creation_date
                                    ELSE NULL
                                END as epstp_schedule,
                                CASE
                                    WHEN hep.start_date < hea.start_date THEN hea.start_date
                                    WHEN hea.start_date < hep.start_date THEN hep.start_date
                                    ELSE hep.start_date
                                END as combined_start_date
                            FROM
                                hr_event_program AS hep
                            JOIN hr_event_management AS hem ON
                                hem.id_event_management = hep.id_event_management
                            JOIN hr_event_attendees AS hea ON
                                hea.id_event_program = hep.id_event_program
                            JOIN master_course_header AS mch ON
                                hep.id_course_header = mch.id_course_header
                            JOIN master_course_detail AS mcd ON
                                mch.id_course_header = mcd.id_course_header
                            JOIN hr_employee AS he ON
                                he.id_employee = hem.responsible_by
                            JOIN hr_employee AS he2 ON
                                he2.id_employee = hea.booked_by
                                -- AND (he2.status = 'A')
                            LEFT JOIN hr_survey_answer_user_header AS hsauh ON
                                mcd.id_course_detail = hsauh.id_course_detail
                                AND hep.id_event_program = hsauh.id_event_program
                                AND hsauh.id_employee = he2.id_employee
                                AND (hep.pass_scores = 1
                                    OR mcd.course_type NOT IN('Materi', 'Quiz_Pretest', 'Quiz'))
                            WHERE
                                hep.status = 'A'
                                AND hea.status != 'Cancel'
                                AND he2.id_user = ?
                                -- AND he2.status = 'A'
                                AND mcd.course_type NOT IN('Materi', 'Quiz_Pretest', 'Quiz')
                                AND now() >= hea.start_date --AND hea.end_date 
                                AND now() >= hep.start_date --AND hep.end_date
                            ORDER BY
                                hep.start_date,
                                hep.sequence ASC
                            ", [session('id_user')]);
            // dd($data->toSql(), session('id_user'));
        $data = collect($data);
        return $data;
    }

    public static function get_event_program($idEventMgt) {
        $data = DB::table('hr_event_program as hep')
                ->select('hep.id_event_program as id', 'mch.course_name as text')
                ->join('master_course_header as mch', 'hep.id_course_header', '=', 'mch.id_course_header')
                // ->where('hep.status', 'A')
                ->where('hep.id_event_management', $idEventMgt)
                // ->where('hep.id_company', '=', session('id_company'))
                ->orderBy('hep.start_date');
        return $data->get();
    }

}
