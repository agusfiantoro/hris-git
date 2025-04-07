<?php

namespace App\Models\EventManagement;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrEventManagement extends Model {

    use HasFactory;
    use StandardModelScope;

    protected $table = 'hr_event_management';
    protected $primaryKey = 'id_event_management';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'description', 'id_employee_request', 'start_date', 'end_date', 'id_region', 'id_branch', 'event_category', 'id_event_type', 'attachment', 'notes', 'id_timezone', 'organized_by', 'responsible_by', 'venue', 'limit_registration', 'end_date_registration', 'public', 'long_description', 'link_event', 'id_event_room', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_event($idEmployee=null) {
        $getManagedProgram = DB::table('relation_responsible_event')->where('id_employee', $idEmployee)->get()->pluck('id_event_management')->unique();

        $data = DB::table('hr_event_management as hem')
                ->join('master_general_data as mgd', 'hem.id_event_type', '=', 'mgd.id_general_data')
                ->join('hr_employee as he', 'he.id_employee', '=', 'hem.organized_by')
                ->join('hr_employee as he2', 'he2.id_employee', '=', 'hem.responsible_by')
                ->select('hem.id_event_management', 'hem.description as event', 'hem.start_date', 'hem.end_date', 'hem.event_category', 'mgd.description as type', 'he.name as organizer', 'he2.name as responsible', 'hem.venue', 'hem.status', 'hem.notes', 'hem.public')
                ->orderByDesc('hem.id_event_management')
                ->where('hem.id_company', '=', session('id_company'))
                ->where(function ($where) use($idEmployee, $getManagedProgram){
                    $where->whereIn('hem.id_event_management', $getManagedProgram);
                    $where->orWhere('hem.organized_by', $idEmployee);
                    $where->orWhere('hem.responsible_by', $idEmployee);
                });
        return $data->get();
    }

    public static function get_employee_detail($id_employee=null) {
        $data = DB::table('hr_employee as he')
                ->select('he.*')
                ->where('he.status', '=', 'A')
                ->where('he.id_company', '=', session('id_company'))
                ->orderBy('he.name');
        if($id_employee){
            $data->where('he.id_employee', $id_employee);
        }
        return $data->get();
    }

    public static function get_employee($id_user=null) {
        $data = DB::table('hr_employee as he')
                ->select('he.id_employee', 'he.name', 'he.id_company', 'he.nik_employee')
                ->where('he.status', '=', 'A')
                ->where('he.id_company', '=', session('id_company'))
                ->orderBy('he.name');
        if($id_user){
            $data->where('he.id_user', $id_user);
        }
        return $data->get();
    }

    public static function get_event_type() {
        $data = DB::table('master_general_data as mgd')
                ->join('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
                ->select('mgd.id_general_data as id', 'mgd.description as text')
                ->where('mgt.general_type', '=', 'master_event_type')
                ->where('mgd.id_company', '=', session('id_company'))
                ->orderBy('mgd.description');
        return $data->get();
    }

    public static function get_timezone() {
        $data = DB::table('master_general_data as mgd')
                ->join('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
                ->select('mgd.id_general_data as id', 'mgd.description as text')
                ->where('mgt.general_type', '=', 'master_timezone')
                ->where('mgd.id_company', '=', session('id_company'))
                ->orderBy('mgd.description');
        return $data->get();
    }

    public static function get_checklist() {
        $data = DB::table('master_checklist_employee as mce')
                // ->select('mce.id_checklist as id', 'mce.checklist_type as text')
                ->where('mce.status', '=', 'A')
                ->where('mce.id_company', '=', session('id_company'))
                ->orderBy('document_name');
        return $data->get();
    }

    public static function get_course() {
        $data = DB::table('master_course_header as mch')
                ->where('mch.id_company', '=', session('id_company'))
                ->where('mch.status', '=', 'A')
                ->orderBy('course_name')
                ->get();
        return $data;
    }

    public static function get_course_by_program($idEventManagement) {
        $data = DB::table('hr_event_program as hep')
            ->leftJoin('master_course_header as mch', 'hep.id_course_header', '=', 'mch.id_course_header')
            ->select('hep.*', 'mch.course_name')
            ->where('hep.id_event_management', $idEventManagement)
            ->orderBy('hep.start_date')
            ->get();
        return $data;
    }

    public static function getEmployeeHR() {
        $data = DB::table('hr_config_email_recruitment as hcer')
            ->leftJoin('master_position_detail as mpd', 'mpd.id_position_detail', '=', 'hcer.id_position_detail')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'mpd.id_employee')
            ->select('he.id_employee', 'he.name', 'he.nik_employee')
            ->whereNotNull('hcer.id_position_detail')
            ->where('hcer.id_company', session('id_company'))
            ->orderBy('he.name');
        return $data->get();
    }

    public static function get_survey_edit($param) {
        $result = (object)[];
        $data = DB::table('hr_event_management as hem')
            // ->where('hem.id_company', '=', $param['id_company'])
            ->where('hem.id_event_management', '=', $param['id_event_management'])
            ->orderByDesc('hem.id_event_management')
            ->first();
        if(@$data->attachment){
            $data->attachment_path = asset("project/storage/app/public/upload/course_event").'/'.@$data->attachment;
        }

        $data2 = DB::table('hr_event_program as hep')
            // ->where('hep.id_company', '=', $param['id_company'])
            ->where('hep.id_event_management', '=', $param['id_event_management'])
            ->orderBy('hep.start_date')
            ->get();

        $data3 = DB::table('hr_event_attendees as hea')
            ->select('id_event_management','booked_by','national_identity_card','attendee_name','attendee_email','attendee_phone','invited_by', 'start_date', 'end_date')
            // ->where('hea.id_company', '=', $param['id_company'])
            ->where('hea.id_event_management', '=', $param['id_event_management'])
            ->groupBy('id_event_management','booked_by','national_identity_card','attendee_name','attendee_email','attendee_phone','invited_by', 'start_date', 'end_date')
            ->orderBy('attendee_name')
            ->get();

        $data4 = DB::table('relation_responsible_event')
            ->where('id_event_management', $param['id_event_management'])
            ->pluck('id_employee')->all();

        foreach ($data3 as $k => $val) {
            $programByAttendee = DB::table('hr_event_attendees')
                ->where('id_event_management', $val->id_event_management)
                ->where('booked_by', $val->booked_by);

            $allIdEventProgram = $programByAttendee->pluck('id_event_program')->all();
            $allStatusEventProgram = $programByAttendee->pluck('status')->all();
            $statusByAttendee = $allStatusEventProgram[0];

            $data3[$k]->event_program = $allIdEventProgram;
            if(in_array('Cancel', $allStatusEventProgram)){
                $statusByAttendee = 'Cancel';
            }
            $data3[$k]->status = $statusByAttendee;
        }

        $result             = $data;
        $result->event      = $data2;
        $result->attendee   = $data3;
        $result->managed_by = $data4;

        return $result;
    }

    public static function get_event_result($param) {
        $result = (object)[];
        $data = DB::table('hr_event_management as hem')
                ->where('hem.id_company', '=', $param['id_company'])
                ->where('hem.id_event_management', '=', $param['id_event_management'])
                ->first();

        $data2 = DB::table('hr_event_program as hep')
                ->where('hep.id_company', '=', $param['id_company'])
                ->where('hep.id_event_management', '=', $param['id_event_management'])
                ->get();

        $data3 = DB::table('hr_event_attendees as hea')
                ->join('hr_survey_answer_user_header as hsauh', 'hsauh.id_employee', '=', 'hea.booked_by', 'left')
                ->select('hea.*', 'hsauh.total_score', 'hsauh.id_survey_header')
                ->where('hea.id_company', '=', $param['id_company'])
                ->where('hea.id_event_management', '=', $param['id_event_management'])
                ->get();

        $result             = $data;
        $result->event      = $data2;
        $result->attendee   = $data3;

        return $result;
    }

    public static function getRoom() {
        $get = DB::table('master_event_room as mer')
            ->leftJoin('master_location as ml', 'ml.id_location', '=', 'mer.id_location')
            ->select('mer.*', 'ml.description as location')
            ->where('mer.status', 'A')
            ->where('mer.id_company', session('id_company'))
            ->orderBy('mer.description');
            
        $result = $get->get();
        return $result;
    }
}
