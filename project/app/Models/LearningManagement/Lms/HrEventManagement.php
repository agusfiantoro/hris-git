<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrEventManagement extends Model {

    use HasFactory;

    protected $table = 'hr_event_management';
    protected $primaryKey = 'id_event_management';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'description', 'id_employee_request', 'start_date', 'end_date', 'id_region', 'id_branch', 'event_category', 'id_event_type', 'attachment', 'notes', 'id_timezone', 'organized_by', 'responsible_by', 'venue', 'limit_registration', 'end_date_registration', 'public', 'long_description', 'link_event', 'id_event_room', 'status', 'id_company', 'created_by', 'updated_by', 'is_mandatory_flag'
    ];

    public static function get_event($idEmployee=null) {
        $getManagedProgram = DB::table('relation_responsible_event')->where('id_employee', $idEmployee)->get()->pluck('id_event_management')->unique();

        $data = DB::table('hr_event_management as hem')
                ->join('master_general_data as mgd', 'hem.id_event_type', '=', 'mgd.id_general_data')
                ->join('hr_employee as he', 'he.id_employee', '=', 'hem.organized_by')
                ->join('hr_employee as he2', 'he2.id_employee', '=', 'hem.responsible_by')
                ->leftJoin('relation_responsible_event as rre', function($join) use($idEmployee) {
                    $join->on('hem.id_event_management', 'rre.id_event_management');
                    $join->where('rre.id_employee', $idEmployee);
                })
                ->select('hem.id_event_management', 'hem.description as event', 'hem.start_date', 'hem.end_date', 'hem.event_category', 'mgd.description as type', 'he.name as organizer', 'he2.name as responsible', 'hem.venue', 'hem.status', 'hem.notes', 'hem.public')
                ->orderByDesc('hem.id_event_management')
                ->where('hem.id_company', '=', session('id_company'))
                ->where(function ($where) use($idEmployee){
                    $where->where('rre.id_employee', $idEmployee);
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

    public static function get_employee_managed_by() {
        // $emp = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
        $employee = DB::select("SELECT 
                        he.id_employee AS id,  
                        CONCAT(name, ' (', nik_employee, ')') as text
                    FROM hr_employee he
                    JOIN master_position_detail mpd ON 
                    he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2 
                    JOIN master_position_routing mpr ON 
                    mpd.id_position_routing = mpr.id_routing 
                    JOIN master_job_position mjp ON 
                    mpr.id_position = mjp.id_position 
                    JOIN master_department md ON 
                    mjp.id_dept = md.id_dept 
                    WHERE 
                    mpd.secondary_position = FALSE AND
                    md.department_code = '150_HR'
                    ORDER BY he.name");
        return $employee;
    }

    public static function get_employee($id_user=null, $region = null, $branch = null, $name = null, $selected_employee = []) {
        $emp = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
        $selected_employee[] = $emp->id_employee;

        if(session('id_company') != $emp->id_company) {
            if($region) {
                $codeRegion = DB::table('master_region')->where('id_region', $region)->first()->region_code;
                $branchQuery = "";
                if($branch) {
                    $codeBranch = DB::table('master_branch')->where('id_branch', $branch)->first()->branch_code;
                    $sql = "SELECT DISTINCT
                            he.id_employee,
                            he.name,
                            he.id_company,
                            he.nik_employee,
                            he.expired_date
                        FROM
                            hr_employee AS he
                        LEFT JOIN master_position_detail AS mpd ON
                            he.id_employee = mpd.id_employee
                            OR he.id_employee = mpd.id_employee2
                        LEFT JOIN master_position_routing mpr ON
                            mpd.id_position_routing = mpr.id_routing
                        LEFT JOIN master_branch AS mb ON
                            mpd.id_branch = mb.id_branch
                        LEFT JOIN master_branch AS mb2 ON
                            mb.branch_code = mb2.branch_code
                            AND mb2.id_company = he.id_company
                            AND mb.branch_code = ?
                        LEFT JOIN master_region AS mr ON
                            mb.id_region = mr.id_region
                        JOIN master_region mr2 ON
                            mr.region_code = mr2.region_code 
                            AND mr2.id_company = he.id_company
                            AND mr.region_code = ?
                            OR (mpr.description IN ('HRBP REGIONAL MANAGER', 'HRBP MANAGER', 'HRBP CORPORATE MANAGER') AND mr2.region_code = 'PST')
                        WHERE
                            he.status = 'A'
                            AND mpd.secondary_position = FALSE
                            AND (he.id_company = ?
                                OR he.id_company = ?)
                        ORDER BY
                            he.name ASC";
                    return DB::select($sql, [
                        $codeBranch, 
                        $codeRegion, 
                        session('id_company'), 
                        $emp->id_company
                    ]);
                }
                $sql = "SELECT DISTINCT
                            he.id_employee,
                            he.name,
                            he.id_company,
                            he.nik_employee,
                            he.expired_date
                        FROM
                            hr_employee AS he
                        LEFT JOIN master_position_detail AS mpd ON
                            he.id_employee = mpd.id_employee
                            OR he.id_employee = mpd.id_employee2
                        LEFT JOIN master_position_routing mpr ON
                            mpd.id_position_routing = mpr.id_routing
                        LEFT JOIN master_branch AS mb ON
                            mpd.id_branch = mb.id_branch
                        LEFT JOIN master_region AS mr ON
                            mb.id_region = mr.id_region
                        JOIN master_region mr2 ON
                            mr.region_code = mr2.region_code 
                            AND mr2.id_company = he.id_company
                            AND mr.region_code = ?
                            OR (mpr.description IN ('HRBP REGIONAL MANAGER', 'HRBP MANAGER', 'HRBP CORPORATE MANAGER') AND mr2.region_code = 'PST')
                        WHERE
                            he.status = 'A'
                            AND mpd.secondary_position = FALSE
                            AND (he.id_company = ?
                                OR he.id_company = ?)
                        ORDER BY
                            he.name ASC";
                return DB::select($sql, [$codeRegion, session('id_company'), $emp->id_company]);
            }
        }
        $data = DB::table('hr_employee as he')
                ->leftJoin('master_position_detail as mpd', function($query) {
                    $query->on('he.id_employee', 'mpd.id_employee');
                    $query->orOn('he.id_employee', 'mpd.id_employee2');
                })
                ->leftJoin('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
                ->leftJoin('master_branch as mb', 'mpd.id_branch', 'mb.id_branch')
                ->leftJoin('master_region as mr', 'mb.id_region', 'mr.id_region')
                ->select('he.id_employee', 'he.name', 'he.id_company', 'he.nik_employee', 'he.expired_date')
                ->where('he.status', '=', 'A')
                ->where('mpd.secondary_position', '=', FALSE)
                ->where(function($query) use($emp) {
                    $query->where('he.id_company', '=', session('id_company'));
                    $query->orWhere('he.id_company', $emp->id_company);
                })    
                ->orderBy('he.name');
        if($id_user){
            $data->where('he.id_user', $id_user);
        }
        if($name) {
            $name = strtolower($name);
            $data->where('lower(he.name)', 'LIKE', '%'.$name.'%');
        }
        if($region) {
            $regionQuery = 'mr.id_region = '.$region;
        } else $regionQuery = '1=1';
        if($branch) {
            $branchQuery = 'mb.id_branch = '.$branch;
        } else $branchQuery = '1=1';
        $data->whereRaw('('.$regionQuery.' AND '.$branchQuery.')');
        $data->orWhereIn('mpr.description', ['HRBP REGIONAL MANAGER', 'HRBP MANAGER', 'HRBP CORPORATE MANAGER']);
        if(count($selected_employee) > 0) {
            $data->orWhereIn('he.id_employee', $selected_employee);
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

    public static function getEmployeeHR($region = null, $branch = null) {
        $emp = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
        $data = DB::table('hr_config_email_recruitment as hcer')
            ->leftJoin('master_position_detail as mpd', 'mpd.id_position_detail', '=', 'hcer.id_position_detail')
            ->leftJoin('master_branch as mb', 'mpd.id_branch', 'mb.id_branch')
            ->leftJoin('master_region as mr', 'mb.id_region', 'mr.id_region')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'mpd.id_employee')
            ->select('he.id_employee', 'he.name', 'he.nik_employee', 'he.expired_date', 'mr.id_region', 'mb.id_branch')
            ->whereNotNull('hcer.id_position_detail')
            ->where(function($query) use($emp) {
                $query->where('hcer.id_company', session('id_company'));
                $query->orWhere('hcer.id_company', $emp->id_company);
            });
        if($region) {
            $data = $data->where('mr.id_region', $region);
        }
        if($branch) {
            $data = $data->where('mb.id_branch', $branch);
        }
        $data = $data->orderBy('he.name');
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
            ->orderByRaw('hep.sequence, hep.start_date ASC')
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

    public static function get_training_score_data($idUser, $idEventManagement = null) {
        $evtMgmtQuery = '';
        if($idEventManagement) {
            $evtMgmtQuery .= ' AND hem.id_event_management = '.$idEventManagement;
        }
        return DB::select("SELECT
            hem.id_event_management,
            hem.description,
            hem.start_date,
            hem.end_date,
            he2.name AS organized_by,
            hem.venue,
            CASE
                WHEN mgd.id_general_data IS NOT NULL THEN TRUE
                ELSE FALSE 
            END AS has_essay
        FROM
            hr_event_management hem
        JOIN hr_event_program hep ON
            hem.id_event_management = hep.id_event_management
        JOIN hr_employee he ON
            hep.id_trainer = he.id_employee
        JOIN master_course_detail mcd ON
            hep.id_course_header = mcd.id_course_header
        JOIN hr_survey_answer_user_header hsauh ON
            mcd.id_course_detail = hsauh.id_course_detail
        JOIN hr_survey_answer_user hsau ON
            hsauh.id_survey_answer_user_header = hsau.id_survey_answer_user_header
        LEFT JOIN hr_employee he2 ON
            hem.organized_by = he2.id_employee
            AND he2.status = 'A'
        LEFT JOIN hr_survey_question hsq ON
            hsau.id_survey_question = hsq.id_survey_question
        LEFT JOIN master_general_data mgd ON
            hsq.id_question_type = mgd.id_general_data
            AND mgd.code IN('Essay', 'Upload_Files')
        WHERE
            he.id_user = ?
            AND he.status = 'A'
            $evtMgmtQuery
            AND mgd.id_general_data IS NOT NULL
            -- AND hsau.essay_score IS NULL
        GROUP BY
            hem.id_event_management,
            hem.description,
            hem.start_date,
            hem.end_date,
            he2.name,
            hem.venue,
            mgd.id_general_data
        ORDER BY hem.end_date DESC", 
        [$idUser]);
    }

    public static function get_training_score_answers($idEventManagement, $idEmployee = null, $idCourseDetail = null) {
        $idEmployeeQuery = '';
        $bindings = [$idEventManagement];
        if($idEmployee) {
            $idEmployeeQuery .= 'AND hsauh.id_employee in('.implode(',', $idEmployee).')';
        }
        if($idCourseDetail) {
            $idEmployeeQuery .= 'AND mcd.id_course_detail in('.implode(',', $idCourseDetail).')';
        }
        $query = "SELECT 
                    hep.id_event_program,
                    hep.description,
                    mcd.id_course_detail,
                    mcd.course_name,
                    hsauh.id_employee,
                    hsq.question,
                    hsau.id_survey_user,
                    COALESCE(hsau.description_answer, hsau.attachment) AS description_answer,
                    hsau.essay_score,
                    mgd.code,
                    concat(he.name, ' (', he.nik_employee, ')') AS employee
                FROM
                    hr_event_program hep
                JOIN master_course_detail mcd ON
                    hep.id_course_header = mcd.id_course_header
                JOIN hr_survey_answer_user_header hsauh ON
                    mcd.id_course_detail = hsauh.id_course_detail
                JOIN hr_survey_answer_user hsau ON
                    hsauh.id_survey_answer_user_header = hsau.id_survey_answer_user_header
                JOIN hr_survey_question hsq ON
                    hsau.id_survey_question = hsq.id_survey_question
                JOIN master_general_data mgd ON
                    hsq.id_question_type = mgd.id_general_data
                LEFT JOIN hr_employee he ON
                    hsauh.id_employee = he.id_employee
                JOIN hr_employee he2 ON 
	                hep.id_trainer = he2.id_employee
                WHERE
                    hep.id_event_management = ?
                    $idEmployeeQuery
                    AND he2.id_user = ".session('id_user')."
                    AND mgd.code IN('Essay', 'Upload_Files')
                GROUP BY 
                    hep.id_event_program, hsauh.id_employee, hsq.question, hsau.id_survey_user, hsau.description_answer, mgd.code, he.name, he.nik_employee, mcd.id_course_detail, mcd.course_name";
        return DB::select($query, $bindings);
    }
}
