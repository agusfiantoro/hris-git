<?php

namespace App\Models\EventManagement;

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
        'id_event_management', 'description', 'id_course_header', 'id_checklist', 'id_survey_question', 'start_date', 'end_date', 'pass_scores', 'maximum', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_program() {
        $data = DB::table('hr_event_program as hep')
            ->leftJoin('hr_event_management as hem', 'hem.id_event_management', '=', 'hep.id_event_management')
            ->leftJoin('hr_event_attendees as hea', 'hea.id_event_program', '=', 'hep.id_event_program')
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('he.id_employee', '=', 'hem.responsible_by');
            })
            ->leftJoin('hr_employee as he2', function ($join) {
                $join->on('he2.id_employee', '=', 'hea.booked_by');
                $join->whereRaw("(he2.status = 'A')");
            })
            ->select('hep.id_event_program', 'hep.id_course_header', 'hep.start_date', 'hep.end_date', 'he.name', 'hem.description as event_name', 'hep.description as course_name', 'hem.venue', 'hem.status', 'hea.booked_by as id_employee', 'hea.start_date as start_date_attendee', 'hea.end_date as end_date_attendee',)
            // ->where('hep.id_company', '=', session('id_company'))
            ->where('hep.status', 'A')
            ->where('hea.status', '!=', 'Cancel')
            ->where('he2.id_user', '=', session('id_user'))
            ->orderBy('hep.start_date', 'desc');
        return $data->get();
    }

    public static function get_event_program($idEventMgt) {
        $data = DB::table('hr_event_program as hep')
                ->select('hep.id_event_program as id', 'hep.description as text')
                // ->where('hep.status', 'A')
                ->where('hep.id_event_management', $idEventMgt)
                // ->where('hep.id_company', '=', session('id_company'))
                ->orderBy('hep.start_date');
        return $data->get();
    }

}
