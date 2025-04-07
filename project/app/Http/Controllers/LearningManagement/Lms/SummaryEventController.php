<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\HrEventManagement;
use App\Models\LearningManagement\Lms\HrEventProgram;
use App\Models\LearningManagement\Lms\HrEventAttendees;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUserHeader;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUser;
use App\Http\Controllers\Controller;
use App\Models\LearningManagement\Lms\MasterCourseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;
use Exception;

class SummaryEventController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $getEmployee = DB::table('hr_employee')->select('id_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();

            $data = HrEventManagement::get_event(@$getEmployee->id_employee);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        return $data->id_event_management;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('learning_management.lms.summary_event.index');
    }

    public function get_event_result(Request $request) {
        $data = [
            'id_event_management'   => $request->id_event_management,
            'id_company'            => session('id_company'),
        ];
        $result['event']    = HrEventManagement::get_event_result($data);
        $result['program']  = HrEventProgram::get_event_program($request->id_event_management);
        return response()->json($result);
    }

    public function get_course(Request $request) {
        $data = HrEventManagement::get_course_by_program($request->id_event_management);
        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('action', function($data) {
                    return $data->id_event_management;
                })
                ->editColumn('start_date', function($data) {
                    return Carbon::parse($data->start_date)->format('d F Y');
                })
                ->editColumn('end_date', function($data) {
                    return Carbon::parse($data->end_date)->format('d F Y');
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function get_course_result(Request $request) {
        try {
            $id_survey_header   = $request->id_survey_header;
            $id_employee        = $request->id_employee;
            $all_result         = [];

            $getEmployee = DB::table('hr_employee as he')
                            ->select('he.name', 'he.identification_number')
                            ->where('he.id_employee', $id_employee)->first();
            $getResult = HrSurveyAnswerUserHeader::where('id_employee', $id_employee)->where('id_survey_header', $id_survey_header)->first();
            if(is_null($getResult)){
                throw new \Exception('Tidak ada hasil pada karyawan tersebut');
            }
            
            $getAnswer = HrSurveyAnswerUser::where('id_survey_answer_user_header', $getResult->id_survey_answer_user_header)->get();

            foreach ($getAnswer as $key => $value) {
                $getQuestion = DB::table('hr_survey_question as hsq')
                            ->select('hsq.question')
                            ->where('hsq.id_survey_question', $value->id_survey_question)->first();

                if(!is_null($value->description_answer) || $value->description_answer != ''){
                    $myAnswer = $value->description_answer;
                } else {
                    $getDescriptionAnswer = DB::table('hr_survey_answer as hsa')
                            ->select('hsa.id_answer', 'hsa.reference_number', 'hsa.suggested_answer')
                            ->where('hsa.id_survey_answer', $value->id_survey_answer)->first();
                    $myAnswer = $getDescriptionAnswer->suggested_answer;

                    if(!is_null($getDescriptionAnswer->id_answer)){
                        $getMasterAnswer = DB::table('master_survey_answer as msa')
                                ->select('msa.description_answer')
                                ->where('msa.id_answer', $getDescriptionAnswer->id_answer)->first();
                        $myAnswer = $getMasterAnswer->description_answer;
                    } 
                }

                $all_result[] = [
                    'question'  => $getQuestion->question,
                    'answer'    => $myAnswer
                ];
            }
            $result['result']       = $all_result;
            $result['score']        = round($getResult->total_score, 2);
            $result['name']         = $getEmployee->name;
            $result['id_number']    = $getEmployee->identification_number;

            return response()->json(['status' => 'true', 'message' => 'Berhasil', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function getPrograms() {
        $getEmployee = DB::table('hr_employee')->select('id_employee')->where('id_user', session('id_user'))->first();
        $data = HrEventManagement::get_event(@$getEmployee->id_employee);
        $dataObj = collect($data)->pluck('event', 'id_event_management');
        $data = [];
        foreach($dataObj as $key => $val) {
            $data[] = [
                "id" => $key,
                "text" => $val
            ];
        }
        return response()->json($data);
    }

    public function getCourse(Request $request) {
        $request->validate([
            'id_event_management' => 'nullable'
        ]);
        $select = ['id_event_program as id', 'description as text'];
        if ($request->id_event_management) {
            $data = HrEventProgram::whereIn('id_event_management', $request->id_event_management)->get($select);
        } else {
            $data = HrEventProgram::get($select);
        }
        return response()->json($data);
    }

    public function getEmployee(Request $request) {
        $request->validate([
            'id_event_program' => 'nullable'
        ]);
        $select = ['hr_event_attendees.id_event_attendees as id', 'he.name as text'];
        if($request->id_event_program) {
            $data = HrEventAttendees::leftJoin('hr_employee as he', 'hr_event_attendees.booked_by', 'he.id_employee')
                    ->whereIn('hr_event_attendees.id_event_program', $request->id_event_program)
                    ->distinct()
                    ->get($select);
        } else {
            $data = HrEventAttendees::leftJoin('hr_employee as he', 'hr_event_attendees.booked_by', 'he.id_employee')->get($select);
        }
        return $data;
    }

    public function download_detail(Request $request)
    { 
		$idEventProgram = $request->id_event_program;
		$getProgram = DB::table('hr_event_program')->where('id_event_program', $idEventProgram)->first();
		$sql = "SELECT DISTINCT rece.id_event_program, he.name, he.nik_employee 
				FROM relation_event_course_employee rece
				JOIN hr_employee he
				ON rece.id_employee = he.id_employee
				JOIN master_course_detail mcd
				ON rece.id_course_detail = mcd.id_course_detail 
				WHERE rece.id_company = ? AND rece.id_event_program = ? AND mcd.course_type IN('Quiz','Quiz_Pretest','Quiz_Posttest','Quiz_Remidial')
				ORDER BY he.name ASC";
        $resNik = DB::select($sql,[session('id_company'),$idEventProgram]);
		$result = [];
		if(count($resNik) > 0){
			foreach($resNik as $key=>$valNik){
				$resVal[] = "'".$valNik->nik_employee."'";
			}
			$groupNik = implode(",", $resVal);

			$sqlVal = "SELECT he.id_employee, he.name, he.nik_employee, mpr.description AS pos_route,  mcd.course_name, 
					mcd.course_type, mcd.id_survey_header,  mcd.id_course_detail, hsau.id_survey_question,  
					hsq.sequence, hsq.question, hsa.suggested_answer, hsa.is_corrected_answer, hsa2.suggested_answer AS correct_answer
					FROM relation_event_course_employee rece
					JOIN hr_employee he
					ON rece.id_employee = he.id_employee
					JOIN (
						SELECT he.id_employee, he.nik_employee  
						FROM hr_employee he
						WHERE he.status = 'A'
					) AS emp_active
					ON he.nik_employee = emp_active.nik_employee 
					LEFT JOIN master_position_detail mpd
					ON emp_active.id_employee = mpd.id_employee
					LEFT JOIN master_position_routing mpr 
					ON mpd.id_position_routing = mpr.id_routing
					LEFT JOIN master_course_detail mcd
					ON rece.id_course_detail = mcd.id_course_detail 
					LEFT JOIN hr_survey_answer_user_header hsauh
					ON mcd.id_course_detail = hsauh.id_course_detail AND mcd.id_survey_header = hsauh.id_survey_header 
					AND rece.id_employee = hsauh.id_employee 
					LEFT JOIN hr_survey_answer_user hsau
					ON hsauh.id_survey_answer_user_header = hsau.id_survey_answer_user_header 
					LEFT JOIN hr_survey_question hsq
					ON hsau.id_survey_question = hsq.id_survey_question 
					LEFT JOIN hr_survey_answer hsa 
					ON hsau.id_survey_answer = hsa.id_survey_answer
                    LEFT JOIN hr_survey_answer hsa2
                    ON hsq.id_survey_question = hsa2.id_survey_question
                    AND hsa2.is_corrected_answer = TRUE
					WHERE rece.id_company  = ? AND rece.id_event_program = ? AND emp_active.nik_employee IN(".$groupNik.") 
					AND mcd.course_type IN('Quiz','Quiz_Pretest','Quiz_Posttest','Quiz_Remidial')
					ORDER BY he.name ASC, mcd.course_type DESC, hsq.sequence ASC";
				$result = DB::select($sqlVal,[session('id_company'),$idEventProgram]);
		}		
			$filename = 'Detail Course Program : ('.@$getProgram->description.')';
			
			$t = '<table style="font-family:Calibri;">';
			$t .= '<tr>';
				$t .= '<th style="background-color:#b9e8c4;">No</th>';
				$t .= '<th style="background-color:#b9e8c4;">Name</th>';
				$t .= '<th style="background-color:#b9e8c4;">NIK Employee</th>';
				$t .= '<th style="background-color:#b9e8c4;">Position</th>';
				$t .= '<th style="background-color:#b9e8c4;">Course Name</th>';
				$t .= '<th style="background-color:#b9e8c4;">Question</th>';
				$t .= '<th style="background-color:#b9e8c4;">Answer</th>';       
                $t .= '<th style="background-color:#b9e8c4;">Correct</th>';
                $t .= '<th style="background-color:#b9e8c4;">Correct Answer</th>';
			$t .= '</tr>'; 

			if(count($result) > 0){
				$no = 1;
				foreach ($result as $k => $val) {
					if($val->id_survey_question != null){
                        $correct = $val->is_corrected_answer ? 'TRUE' : 'FALSE';
						$t .= '<tr>';
							$t .= '<td align="center">'.$no.'</td>';
							$t .= '<td>'.$val->name.'</td>';
							$t .= '<td>'.$val->nik_employee.'</td>';
							$t .= '<td>'.$val->pos_route.'</td>';
							$t .= '<td>'.$val->course_name.'</td>';
							$t .= '<td>'.$val->question.'</td>';
							$t .= '<td>'.$val->suggested_answer.'</td>';
                            $t .= '<td>'.$correct.'</td>';
                            $t .= '<td>'.$val->correct_answer.'</td>';
						$t .= '</tr>'; 
						$no++;
					}
				}
			}
			$t .= '</table>';

			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=".$filename.".xls");
			echo $t;
		
	}
	
/*	public function download_detail_old(Request $request)
    {   
        $idEventProgram = $request->id_event_program;
        $getProgram = DB::table('hr_event_program')->where('id_event_program', $idEventProgram)->first();
        $getCourseDetail = DB::table('master_course_detail as mcd')
            ->leftJoin('hr_survey_header as hsh', 'mcd.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('hr_event_program as hep', 'mcd.id_course_header', '=', 'hep.id_course_header')
         //   ->select('mcd.id_survey_header', 'mcd.course_name', 'hsh.notes')
			->selectRaw("DISTINCT mcd.id_survey_header, mcd.course_name, hsh.notes, mcd.sequence")
            ->where('hep.id_course_header', $getProgram->id_course_header)
            ->whereIn('mcd.course_type', ['Quiz','Quiz_Pretest','Quiz_Posttest','Quiz_Remidial'])
            ->orderBy('mcd.sequence')
            ->get();
        $allIdQuiz = $getCourseDetail->pluck('id_survey_header')->all();
        $courseNameByIdQuiz = [];
        if($getCourseDetail->count() > 0){
            foreach ($getCourseDetail as $k => $val) {
                $courseNameByIdQuiz[$val->id_survey_header] = $val->course_name.' ('.$val->notes.')';
            }
        }

        $getScoreQuiz = DB::table('hr_survey_answer_user_header as hsauh')
            ->whereIn('hsauh.id_survey_header', $allIdQuiz)
            ->get();

        $mappingScoreByAttendees = [];
        if($getScoreQuiz->count() > 0){
            foreach ($getScoreQuiz as $k => $val) {
                $thisIdSurveyHeader = $val->id_survey_header;
                $mappingScoreByAttendees[$val->id_employee][$thisIdSurveyHeader] = [
                    'name' => @$courseNameByIdQuiz[$thisIdSurveyHeader],
                    'score' => $val->total_score,
                ];
            }
        }

        $getAttendees = DB::table('hr_event_attendees as hea')
            ->leftJoin('hr_event_program as hep', 'hea.id_event_program', '=', 'hep.id_event_program')
            ->leftJoin('hr_employee as he', 'hea.booked_by', '=', 'he.id_employee')
            ->select('hea.*', 'he.nik_employee', 'he.id_employee')
            ->where('hep.id_event_program', $idEventProgram)
            ->orderBy('he.name')
            ->get();

        $filename = 'Summary_Course_Program : '.@$getProgram->description;

        $t = '<table border="1">';
        $t .= '<tr>';
            $t .= '<th style="background-color:#7ecc61;">No</th>';
            $t .= '<th style="background-color:#7ecc61;">NIK Employee</th>';
            $t .= '<th style="background-color:#7ecc61;">Name</th>';
        if(count($allIdQuiz) > 0){
            foreach ($allIdQuiz as $k => $val) {
                $t .= '<th style="background-color:#7ecc61;">'.$courseNameByIdQuiz[$val].'</th>';
            }
        }
        $t .= '</tr>'; 

        if($getAttendees->count() > 0){
            $no = 1;
            foreach ($getAttendees as $k => $val) {
                $t .= '<tr>';
                    $t .= '<td>'.$no.'</td>';
                    $t .= '<td>'.$val->nik_employee.'</td>';
                    $t .= '<td>'.$val->attendee_name.'</td>';
                if(count($allIdQuiz) > 0){
                    foreach ($allIdQuiz as $k2 => $val2) {
                        if(array_key_exists($val->id_employee, @$mappingScoreByAttendees)){
                            if(array_key_exists($val2, @$mappingScoreByAttendees[$val->id_employee])){
                                $t .= '<td>'.$mappingScoreByAttendees[$val->id_employee][$val2]['score'].'</td>';
                            } else {
                                $t .= '<td>not yet</td>';
                            }
                        } else {
                            $t .= '<td>not yet</td>';
                        }
                    }
                }
                $t .= '</tr>'; 
                $no++;
            }
        }
        $t .= '</table>';

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=".$filename.".xls");
        echo $t;
    }
*/

    public function downloadReportByCourse(Request $request) {
        try {
            $results = DB::select("SELECT 
                        * 
                    FROM sp_funct_summary_total_lms(?, ?, ?)", 
                    [session('id_company'), $request->id_event_management, $request->id_course_header]);

            $dataRows = '';
            foreach($results as $i => $result) {
                $index = $i+1;
                $dataRows .= <<<EOD
                    <tr>
                        <td>$index</td>
                        <td>$result->event_name</td>
                        <td>$result->course_name</td>
                        <td>$result->nik_employee</td>
                        <td>$result->employee_name</td>
                        <td>$result->average_value</td>
                    </tr>
                EOD;
            }
            $html = <<<EOD
                <table>
                    <tr>
                        <th>No</th>
                        <th>Program</th>
                        <th>Course</th>
                        <th>NIK</th>
                        <th>Name</th>
                        <th>Average Score</th>
                    </tr>
                    $dataRows
                </table>
            EOD;
            return response($html)
                ->header("Content-Type", "application/vnd-ms-excel")
                ->header("Content-Disposition", "attachment; filename=OnboardingReport.xls");
        } catch(Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function download(Request $request) {
        ini_set('max_execution_time', -1);
        
        $idEventProgram = $request->id_event_program;
        $idEventManagement = $request->id_event_management;
        $getProgram = DB::table('hr_event_program as hep')
            ->leftJoin('hr_event_management as hem', 'hem.id_event_management', '=', 'hep.id_event_management')
            ->select('hep.*', 'hem.description as program');
        if($idEventManagement){
            $getProgram->where('hem.id_event_management', $idEventManagement);
        }
        if($idEventProgram){
            $getProgram->where('hep.id_event_program', $idEventProgram);
        }
        $resultProgram = $getProgram->get();

        $allIdEventProgram = [];
        $programName = '';
        if($resultProgram->count() > 0){
            $programName = $resultProgram[0]->program;
            $allIdEventProgram = $resultProgram->pluck('id_event_program')->unique();
        }
        $courseType = [
            'Materi' => 'Materi',
            'Quiz' => 'Quiz',
            'Quiz_Pretest' => 'Quiz Pretest',
            'Quiz_Posttest' => 'Quiz Post Test',
            'Quiz_Remidial' => 'Quiz Remidial',
            // null => '',
        ];

        $getCourseResult = DB::select("SELECT
                                        hsauh.id_survey_answer_user_header,
                                        he2.id_employee,
                                        he2.nik_employee,
                                        he2.name,
                                        hsauh.total_score,
                                        hep.pass_scores,
                                        hem.description AS PROGRAM,
                                        mcd.course_type,
                                        mch.course_name AS course,
                                        hep.description as course_description,
                                        md.description as department,
                                        hsauh.creation_date,
                                        hsauh.update_date,
                                        mpr.description as position,
                                        mb.description as branch,
                                        mr.description as region,
                                        CASE 
                                            WHEN hsauh.total_score >= hep.pass_scores THEN 'Pass'
                                            ELSE 'Fail'
                                        END AS pass
                                    FROM
                                        hr_event_management hem
                                    JOIN hr_event_program hep ON
                                        hem.id_event_management = hep.id_event_management
                                        -- AND hep.status = 'A'
                                    JOIN master_course_header mch ON 
                                        hep.id_course_header = mch.id_course_header
                                    JOIN master_course_detail mcd ON 
                                        mch.id_course_header = mcd.id_course_header
                                    JOIN hr_survey_answer_user_header hsauh ON 
                                        mcd.id_course_detail = hsauh.id_course_detail
                                        AND hep.id_event_program = hsauh.id_event_program
                                    JOIN hr_employee he ON
                                        hsauh.id_employee = he.id_employee
                                    LEFT JOIN hr_employee he2 ON
                                        he.nik_employee = he2.nik_employee
                                        AND he2.status = 'A'
                                    LEFT JOIN master_position_detail mpd ON 
                                        he2.id_employee = mpd.id_employee
                                        OR he2.id_employee = mpd.id_employee2
                                    LEFT JOIN master_position_routing mpr ON 
                                        mpd.id_position_routing = mpr.id_routing
                                    LEFT JOIN master_job_position mjp ON 
                                        mpr.id_position = mjp.id_position
                                    LEFT JOIN master_department md ON 
                                        mjp.id_dept = md.id_dept
                                    LEFT JOIN master_branch mb ON 
                                        mpd.id_branch = mb.id_branch
                                    LEFT JOIN master_region mr ON 
                                        mb.id_region = mr.id_region
                                    WHERE
                                        hem.id_event_management = ?
                                    ORDER BY mch.course_name, hep.start_date, he2.name ASC", 
        [$idEventManagement]);
            // ->orderBy('mch.course_name')
            // ->orderBy('hep.start_date')
            // ->orderBy('he.name')
            // ->get();
        $getCourseResult = collect($getCourseResult);
        $exportType = 'excel';
        $filename = 'Summary '.$programName;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan
        $allWorkSheet = [];


        // ========================== Sheet PERTAMA ==================================
        if(@$getCourseResult[0]->pass_scores == 1) {
            return self::downloadEpstp($programName, $getCourseResult, $allIdEventProgram, $idEventManagement);
        }
        $dataSheet1 = [
            [''],
            ['Summary Employee Course'],
            ['Program', ' : '.$programName],
            [''],
        ];
        $getCourseResult = $getCourseResult->groupBy('nik_employee');
        // dd($getCourseResult['2019120666']);
        $titleSheet1        = ['No', 'NIK', 'Name', 'Department', 'Position', 'Branch', 'Region', 'Passed Course', '% Hit Rate Pass', 'Number of Course', 'Course Name', 'Description', 'Start', 'End'];
        $scoreTitle         = [];
        foreach($courseType as $key => $val) {
            array_push($titleSheet1, $key);
            array_push($scoreTitle, $key);
        }
        
        $countTitleSheet1   = count($titleSheet1);
        array_push($titleSheet1, 'Passing Grade');
        array_push($titleSheet1, 'Result');
        $dataSheet1[]       = $titleSheet1;

        if($getCourseResult->count() > 0){
            $number = 1;
            foreach ($getCourseResult as $k => $items) {
                // dd($k, $items);
                $groupByCourse = $items->groupBy('course');
                // dd($groupByCourse);
                foreach($groupByCourse as $course) {
                    $dates = [];
                    $scores = [];
                    foreach($course as $testType) {
                        if($testType->creation_date) {
                            array_push($dates, $testType->creation_date);
                        }
                        // dd($testType);
                        if($testType->course_type=='Materi'){
                            $scores[$testType->course_type] = [$testType->course, 'done'];
                        } else {
                            $scores[$testType->course_type] = [$testType->course, @$testType->total_score ?? '-'];
                        }
                    }

                    $scorables = $getCourseResult[$course[0]->nik_employee]->whereIn('course_type', ['Quiz_Posttest', 'Quiz_Remidial'])->whereNotNull('pass_scores')->unique('id_survey_answer_user_header');
                    $pass = 0;
                    foreach($scorables as $allCourse) {
                        if($allCourse->total_score >= $allCourse->pass_scores) {
                            $pass++;
                        }
                    }
                    $passPercent = count($scorables) > 0 ? number_format(($pass/count($scorables)*100), 2)."%" : "0.00%";

                    $resultValue = [
                        $number,
                        $course[0]->nik_employee,
                        $course[0]->name,
                        $course[0]->department,
                        $course[0]->position,
                        $course[0]->branch,
                        $course[0]->region,
                        $pass." of ".count($scorables),
                        $passPercent,
                        count($groupByCourse),
                        // $val->program,
                        $course[0]->course,
                        $course[0]->course_description,
                        // @$courseType[@$val->course_type],
                        // $score,
                    ];
                    if(count($dates) > 0) {
                        array_push($resultValue, min($dates));
                        array_push($resultValue, max($dates));
                    } else {
                        array_push($resultValue, '');
                        array_push($resultValue, '');
                    }
                    
                    // Materi
                    // if(!key_exists('Materi', $scores)) {
                        // dd($course[0], $scores);
                    // }
                    
                    
                    // $keys = ['Materi', 'Quiz', 'Quiz_Pretest', 'Quiz_Posttest', 'Quiz_Remidial'];
                    foreach($scoreTitle as $key) {
                        if(key_exists($key, $scores)) {
                            if($course[0]->course == $scores[$key][0]) {
                                $val = $scores[$key][1];
                                if($key == 'Materi' && $scores[$key][1] == '-') {
                                    if($course->where('course_type', 'Materi')->first()) {
                                        $val = 'assign';
                                    }
                                }
                                array_push($resultValue, $val);
                            } else {
                                array_push($resultValue, '-');
                            }
                        } else {
                            array_push($resultValue, '-');
                        }
                    }
                    
                    
                    
                    $score_result = '-';
                    if(key_exists('Quiz_Posttest', $scores)) {
                        if($course[0]->pass_scores == NULL) {
                            $score_result = 'No Passing Grade';
                        } else if($scores['Quiz_Posttest'][1] >= $course[0]->pass_scores) {
                            $score_result = 'PASS';
                        } else {
                            $score_result = 'FAIL';
                        }
                    } else if(key_exists('Quiz_Remidial', $scores)) {
                        if($course[0]->pass_scores == NULL) {
                            $score_result = 'No Passing Grade';
                        } else if($scores['Quiz_Remidial'][1] >= $course[0]->pass_scores) {
                            $score_result = 'PASS';
                        } else {
                            $score_result = 'FAIL';
                        }
                    } 
                    // else if($course[0]->total_score) {
                    //     if($course[0]->pass_scores == null) {
                    //         $score_result = 'No Passing Grade';
                    //     } else if($course[0]->total_score >= $course[0]->pass_scores) {
                    //         $score_result = 'PASS';
                    //     } else {
                    //         $score_result = 'FAIL';
                    //     }
                    // }
                    
                    array_push($resultValue, $course[0]->pass_scores ?? 'N/A');
                    array_push($resultValue, $score_result);
                    $number++;
                    $dataSheet1[] = $resultValue; 
                }

            }
        }
        // dd($dataSheet1);

        $nameSheet1 = 'Score Result';
        $indexSheet1 = 0;
        $workSheet1 = new Worksheet($spreadsheet, $nameSheet1);
        $spreadsheet->addSheet($workSheet1, $indexSheet1);
        $thisSheet1 = $spreadsheet->getSheet($indexSheet1)->setTitle($nameSheet1);
        $workSheet1->fromArray($dataSheet1); //ngolah array dimasukkan ke cell masing2
        
        
        $currentNik = null;
        $currentNikFirstRow = null;
        $cols = ["B", "C", "D", "E", "F", "G", "H", "I", "J"];
        foreach ($workSheet1->getRowIterator() as $rowIndex => $row) {
            if($rowIndex > 5) {
                if($thisSheet1->getCell("B".$rowIndex)->getValue() != $currentNik || $rowIndex == $thisSheet1->getHighestDataRow()) {
                    foreach($cols as $col) {
                        if($rowIndex == $thisSheet1->getHighestDataRow()) {
                            // last row
                            if($thisSheet1->getCell("B".$rowIndex)->getValue()) {
                                $nikValue = $thisSheet1->getCell("B".$rowIndex)->getValue();
                            }
                            if($rowIndex != 6) {
                                if($currentNik == @$nikValue) {
                                    $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex));
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                                } else {
                                    $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex-1));
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                                }
                            }
                        } else {
                            if($rowIndex != 6) {
                                $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex-1));
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                            }
                        }
                    }
                    $currentNik = $thisSheet1->getCell("B".$rowIndex)->getValue();
                    $currentNikFirstRow = $rowIndex;
                }
            }
        }
        // dd("-");

        $rowStyle = [2,3]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [2,3,5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            // $thisSheet1->mergeCells('B'.$val.':E'.$val);
        }

        // styling manual berdasar cell
        $lastColumnSheet1         = $thisSheet1->getHighestColumn();
        $lastRowSheet1            = $thisSheet1->getHighestRow();
        $columnAfterTitleSheet1   = count($titleSheet1)+1;
        $columnQuestionSheet1     = 0;
        $columnAllDataSheet1      = $lastColumnSheet1.($lastRowSheet1 - ($columnQuestionSheet1+2));

        $thisSheet1->mergeCells('A2:E2');
        $allWorkSheet[] = $workSheet1;


        // ========================== Sheet KEDUA ==================================

        $allIdGeneralDataEssay = DB::table('master_general_data')->where('code','Essay')->where('id_company', session('id_company'))->get()->pluck('id_general_data')->all();
        $getAnswerEssay = DB::table('hr_event_program as hep')
            ->leftJoin('master_course_header as mch', 'mch.id_course_header', '=', 'hep.id_course_header')
            ->leftJoin('master_course_detail as mcd', function ($join) {
                $join->on('mcd.id_course_header', '=', 'mch.id_course_header');
                $join->whereNull('mcd.id_content_learning');
            })
            ->join('hr_survey_question as hsq', function ($join) {
                $join->on('hsq.id_survey_header', '=', 'mcd.id_survey_header');
                // $join->whereIn('hsq.id_question_type', $allIdGeneralDataEssay);
            })
            ->join('master_general_data as mgd', 'hsq.id_question_type', 'mgd.id_general_data')
            ->leftJoin('hr_survey_header as hsh', 'mcd.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('hr_event_management as hem', 'hem.id_event_management', '=', 'hep.id_event_management')
            ->leftJoin('hr_event_attendees as hea', 'hea.id_event_program', '=', 'hep.id_event_program')
            ->leftJoin('hr_employee as he', 'hea.booked_by', '=', 'he.id_employee')
            ->leftJoin('hr_survey_answer_user_header as hsauh', function ($join) {
                $join->on('hsauh.id_survey_header', '=', 'hsh.id_survey_header');
                $join->on('hsauh.id_employee', '=', 'hea.booked_by');
                $join->on('hsauh.id_course_detail', '=', 'mcd.id_course_detail');
                $join->on('hsauh.id_event_program', '=', 'hep.id_event_program');
            })
            ->leftJoin('hr_survey_answer_user as hsau', function ($join) {
                $join->on('hsau.id_survey_answer_user_header', '=', 'hsauh.id_survey_answer_user_header');
                $join->on('hsq.id_survey_question', '=', 'hsau.id_survey_question');
            })
            ->leftJoin('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
            ->leftJoin('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
            ->leftJoin('master_branch as mb', 'mpd.id_branch', 'mb.id_branch')
            ->leftJoin('master_region as mr', 'mb.id_region', 'mr.id_region')
            ->select('hem.description as program', 'mcd.course_type', 'mch.course_name as course', 'hep.description as course_description', 'hsh.notes', 'hea.booked_by', 'he.nik_employee', 'he.name', 'hsq.question', 'hsau.description_answer', 'hsau.essay_score', 'hsauh.total_score', 'hep.pass_scores', 'hsau.creation_date as answered_at', 'mpr.description as position', 'mb.description as branch', 'mr.description as region')
            ->whereIn('hep.id_event_program', $allIdEventProgram)
            ->where('hem.id_event_management', $idEventManagement)
            ->where('mgd.code', 'Essay')
            // ->where('mpd.status', 'A')
            // ->whereIn('mcd.course_type', $courseType)
            // ->groupBy('hem.description', 'mcd.course_type', 'mch.course_name', 'hep.description', 'hsh.notes', 'hea.booked_by', 'he.nik_employee', 'he.name', 'hep.start_date', 'hsq.question', 'hsau.description_answer', 'hsau.essay_score', 'hsau.creation_date')
            // ->orderBy('mch.course_name')
            // ->orderBy('hep.start_date')
            // ->orderBy('he.name')
            ->orderByRaw('he.name ASC, hep.sequence ASC, hsq.sequence ASC')
            ->get();
            // dd($getAnswerEssay->get(), $getAnswerEssay->toSql(), $allIdGeneralDataEssay, $allIdEventProgram);

        $dataSheet2         = [];
        $titleSheet2        = ['No', 'Date', 'NIK', 'Name', 'Position', 'Branch', 'Region', 'Program', 'Course Type', 'Course', 'Description', 'Question', 'Answer', 'Score', 'Total Score', 'Passing Grade', 'Result'];
        $countTitleSheet2   = count($titleSheet2);
        $dataSheet2[]       = $titleSheet2;

        if($getAnswerEssay->count() > 0){
            $number = 1;
            foreach ($getAnswerEssay as $k => $val) {
                if($val->pass_scores) {
                    if($val->pass_scores != 1) {
                        $result = $val->total_score >= $val->pass_scores ? "Pass" : "Fail";
                    } else {
                        $result = $val->total_score >= $val->pass_scores ? "Hit" : "Miss";
                    }
                } else {
                    $result = "No Passing Grade";
                }
                $resultValue = [
                    $number,
                    $val->answered_at,
                    $val->nik_employee,
                    $val->name,
                    $val->position,
                    $val->branch,
                    $val->region,
                    $val->program,
                    @$courseType[@$val->course_type],
                    $val->course,
                    $val->course_description,
                    $val->question,
                    $val->description_answer,
                    $val->essay_score,
                    $val->total_score,
                    $val->pass_scores,
                    $result
                ];
                $number++;
                $dataSheet2[] = $resultValue; 
            }
        }

        $nameSheet2 = 'Essay Result';
        $indexSheet2 = 1;
        $workSheet2 = new Worksheet($spreadsheet, $nameSheet2);
        $spreadsheet->addSheet($workSheet2, $indexSheet2);
        $thisSheet2 = $spreadsheet->getSheet($indexSheet2)->setTitle($nameSheet2);
        $workSheet2->fromArray($dataSheet2); //ngolah array dimasukkan ke cell masing2

        $rowStyle = [1]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet2->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet2->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
        }

        $lastColumnSheet2         = $thisSheet2->getHighestColumn();
        $lastRowSheet2            = $thisSheet2->getHighestRow();
        $columnAfterTitleSheet2   = count($titleSheet2)+1;
        $columnQuestionSheet2     = 0;
        $columnAllDataSheet2      = $lastColumnSheet2.($lastRowSheet2 - ($columnQuestionSheet2+2));

        $allWorkSheet[] = $workSheet2;

        // ========================================================================

        // $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        $spreadsheet->setActiveSheetIndexByName($nameSheet1); // utk set sheet yg aktif
        $worksheets = $allWorkSheet;
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        if($exportType == 'excel'){
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer->save('php://output');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            $writer->save('php://output');
        }
    }

    public function download_multiple_programs(Request $request) {
        ini_set('max_execution_time', -1);
        $request->validate([
            'id_event_management' => 'required',
        ]);
        // return response($request->all());

        $idEventManagement = explode(',', $request->id_event_management);
        $getProgram = DB::table('hr_event_program as hep')
            ->leftJoin('hr_event_management as hem', 'hem.id_event_management', '=', 'hep.id_event_management')
            ->select('hep.*', 'hem.description as program');
        if($idEventManagement){
            $getProgram->whereIn('hem.id_event_management', $idEventManagement);
        }
        $resultProgram = $getProgram->get();

        // dd($resultProgram);

        $courseType = [
            'Materi' => 'Materi',
            'Quiz' => 'Quiz',
            'Quiz_Pretest' => 'Quiz Pretest',
            'Quiz_Posttest' => 'Quiz Post Test',
            'Quiz_Remidial' => 'Quiz Remidial',
            // null => '',
        ];

        $titleSheet1        = ['No', 'Program', 'NIK', 'Name', 'Department', 'Position', 'Branch', 'Region', 'Passed Course', '% Hit Rate Pass', 'Number of Course', 'Course Name', 'Description', 'Start', 'End'];
        $scoreTitle         = [];
        foreach($courseType as $key => $val) {
            array_push($titleSheet1, $key);
            array_push($scoreTitle, $key);
        }

        $dataSheet1 = [
            [''],
            ['Summary Employee Course'],
            [''],
            [''],
        ];
        
        $countTitleSheet1   = count($titleSheet1);
        array_push($titleSheet1, 'Passing Grade');
        array_push($titleSheet1, 'Result');
        $dataSheet1[]       = $titleSheet1;

        $allIdEventProgram = [];
        $programName = '';
        $exportType = 'excel';
        $filename = 'Summary Multiple Program';
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan
        $allWorkSheet = [];
            $allIdEventProgram = $resultProgram->pluck('id_event_program')->unique();

            $getCourseResult = DB::select("SELECT
                                        hsauh.id_survey_answer_user_header,
                                        he2.id_employee,
                                        CASE 
                                        	WHEN he.status = 'I' AND he2.id_employee IS NULL THEN concat(he.nik_employee, ' (', 'Inactive', ')')
                                        	ELSE he2.nik_employee
                                        END AS nik_employee,
                                        CASE 
                                        	WHEN he.status = 'I' AND he2.id_employee IS NULL THEN concat(he.name, ' (', 'Inactive', ')')
                                        	ELSE he2.name
                                        END AS name,
                                        hsauh.total_score,
                                        hep.pass_scores,
                                        hem.description AS program,
                                        hem.id_event_management,
                                        mcd.course_type,
                                        mch.course_name AS course,
                                        hep.description as course_description,
                                        md.description as department,
                                        hsauh.creation_date,
                                        hsauh.update_date,
                                        mpr.description as position,
                                        mb.description as branch,
                                        mr.description as region,
                                        CASE 
                                            WHEN hsauh.total_score >= hep.pass_scores THEN 'Pass'
                                            ELSE 'Fail'
                                        END AS pass
                                    FROM
                                        hr_event_management hem
                                    JOIN hr_event_program hep ON
                                        hem.id_event_management = hep.id_event_management
                                        AND hep.status = 'A'
                                    JOIN master_course_header mch ON 
                                        hep.id_course_header = mch.id_course_header
                                    JOIN master_course_detail mcd ON 
                                        mch.id_course_header = mcd.id_course_header
                                    JOIN hr_survey_answer_user_header hsauh ON 
                                        mcd.id_course_detail = hsauh.id_course_detail
                                        AND hep.id_event_program = hsauh.id_event_program
                                    JOIN hr_employee he ON
                                        hsauh.id_employee = he.id_employee
                                    LEFT JOIN hr_employee he2 ON
                                        he.nik_employee = he2.nik_employee
                                        AND he2.status = 'A'
                                    LEFT JOIN master_position_detail mpd ON 
                                        he2.id_employee = mpd.id_employee
                                        OR he2.id_employee = mpd.id_employee2
                                    LEFT JOIN master_position_routing mpr ON 
                                        mpd.id_position_routing = mpr.id_routing
                                    LEFT JOIN master_job_position mjp ON 
                                        mpr.id_position = mjp.id_position
                                    LEFT JOIN master_department md ON 
                                        mjp.id_dept = md.id_dept
                                    LEFT JOIN master_branch mb ON 
                                        mpd.id_branch = mb.id_branch
                                    LEFT JOIN master_region mr ON 
                                        mb.id_region = mr.id_region
                                    WHERE
                                        hem.id_event_management in(".$request->id_event_management.")
                                    ORDER BY hem.id_event_management, mch.course_name, hep.start_date, he2.name ASC", 
            []);
            // dd(collect($getCourseResult));
            $getCourseResult = collect($getCourseResult)->groupBy('id_event_management');
        // dd($getCourseResult['BCP0009351'], $getCourseResult->count());

        if($getCourseResult->count() > 0){
            $number = 1;
            foreach($getCourseResult as $gcr) {
                $gcr = $gcr->groupBy('nik_employee');
                
                foreach ($gcr as $k => $items) {
                    $groupByCourse = $items->groupBy('course');
                    // dd($groupByCourse);
                    foreach($groupByCourse as $course) {
                        $dates = [];
                        $scores = [];
                        foreach($course as $testType) {
                            if($testType->creation_date) {
                                array_push($dates, $testType->creation_date);
                            }
                            // dd($testType);
                            if($testType->course_type=='Materi'){
                                $scores[$testType->course_type] = [$testType->course, 'done'];
                            } else {
                                $scores[$testType->course_type] = [$testType->course, @$testType->total_score ?? '-'];
                            }
                        }
                        $scorables = $gcr[$course[0]->nik_employee]->whereIn('course_type', ['Quiz_Posttest', 'Quiz_Remidial'])->whereNotNull('pass_scores')->unique('id_survey_answer_user_header');
                        $pass = 0;
                        foreach($scorables as $allCourse) {
                            if($allCourse->total_score >= $allCourse->pass_scores) {
                                $pass++;
                            }
                        }
                        $passPercent = count($scorables) > 0 ? number_format(($pass/count($scorables)*100), 2)."%" : "0.00%";
    
                        $resultValue = [
                            $number,
                            $course[0]->program,
                            $course[0]->nik_employee,
                            $course[0]->name,
                            $course[0]->department,
                            $course[0]->position,
                            $course[0]->branch,
                            $course[0]->region,
                            $pass." of ".count($scorables),
                            $passPercent,
                            count($groupByCourse),
                            // $val->program,
                            $course[0]->course,
                            $course[0]->course_description,
                            // @$courseType[@$val->course_type],
                            // $score,
                        ];
                        if(count($dates) > 0) {
                            array_push($resultValue, min($dates));
                            array_push($resultValue, max($dates));
                        } else {
                            array_push($resultValue, '');
                            array_push($resultValue, '');
                        }
                        
                        // Materi
                        // if(!key_exists('Materi', $scores)) {
                            // dd($course[0], $scores);
                        // }
                        
                        
                        // $keys = ['Materi', 'Quiz', 'Quiz_Pretest', 'Quiz_Posttest', 'Quiz_Remidial'];
                        foreach($scoreTitle as $key) {
                            if(key_exists($key, $scores)) {
                                if($course[0]->course == $scores[$key][0]) {
                                    $val = $scores[$key][1];
                                    if($key == 'Materi' && $scores[$key][1] == '-') {
                                        if($course->where('course_type', 'Materi')->first()) {
                                            $val = 'assign';
                                        }
                                    }
                                    array_push($resultValue, $val);
                                } else {
                                    array_push($resultValue, '-');
                                }
                            } else {
                                array_push($resultValue, '-');
                            }
                        }
                        
                        
                        
                        $score_result = '-';
                        if(key_exists('Quiz_Posttest', $scores)) {
                            if($course[0]->pass_scores == NULL) {
                                $score_result = 'No Passing Grade';
                            } else if($scores['Quiz_Posttest'][1] >= $course[0]->pass_scores) {
                                $score_result = 'PASS';
                            } else {
                                $score_result = 'FAIL';
                            }
                        } else if(key_exists('Quiz_Remidial', $scores)) {
                            if($course[0]->pass_scores == NULL) {
                                $score_result = 'No Passing Grade';
                            } else if($scores['Quiz_Remidial'][1] >= $course[0]->pass_scores) {
                                $score_result = 'PASS';
                            } else {
                                $score_result = 'FAIL';
                            }
                        } 
                        // else if($course[0]->total_score) {
                        //     dump($course[0]);
                        //     if($course[0]->pass_scores == null) {
                        //         $score_result = 'No Passing Grade';
                        //     } else if($course[0]->total_score >= $course[0]->pass_scores) {
                        //         dump($course[0]);
                        //         $score_result = 'PASS';
                        //     } else {
                        //         dump($course[0]);
                        //         $score_result = 'FAIL';
                        //     }
                        // }
                        
                        array_push($resultValue, $course[0]->pass_scores ?? 'N/A');
                        array_push($resultValue, $score_result);
                        $number++;
                        $dataSheet1[] = $resultValue; 
                    }
    
                }
            }
        }
        

        // ========================== Sheet PERTAMA ==================================

        // return response($dataSheet1);

        $nameSheet1 = 'Score Result';
        $indexSheet1 = 0;
        $workSheet1 = new Worksheet($spreadsheet, $nameSheet1);
        $spreadsheet->addSheet($workSheet1, $indexSheet1);
        $thisSheet1 = $spreadsheet->getSheet($indexSheet1)->setTitle($nameSheet1);
        $workSheet1->fromArray($dataSheet1); //ngolah array dimasukkan ke cell masing2
        
        // dd($thisSheet1);
        $currentNik = null;
        $currentNikFirstRow = null;
        // $currentProgram = null;
        // $currentProgramFirstRow = null;
        foreach ($workSheet1->getRowIterator() as $rowIndex => $row) {
            if($rowIndex > 5) {
                $thisSheet1->setCellValue("A".$rowIndex, $rowIndex-5);
                if($thisSheet1->getCell("C".$rowIndex)->getValue() != $currentNik || $rowIndex == $thisSheet1->getHighestDataRow()) {
                    
                    $cols = ["B", "C", "D", "E", "F", "G", "H", "I", "J", "K"];
                    foreach($cols as $col) {
                        if($rowIndex == $thisSheet1->getHighestDataRow()) {
                            if($thisSheet1->getCell("C".$rowIndex)->getValue()) {
                                $nikValue = $thisSheet1->getCell("C".$rowIndex)->getValue();
                            }
                            if($rowIndex != 6) {
                                if($currentNik == @$nikValue) {
                                    $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex));
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                                } else {
                                    $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex-1));
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                    $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                                }
                            }
                        } else {
                            if($rowIndex != 6) {
                                $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex-1));
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                            }
                        }
                    }
                    $currentNik = $thisSheet1->getCell("C".$rowIndex)->getValue();
                    $currentNikFirstRow = $rowIndex;
                }
                // if($thisSheet1->getCell("B".$rowIndex)->getValue() != $currentProgram || $rowIndex == $thisSheet1->getHighestDataRow()) {
                //     if($rowIndex != 6) {
                //         $thisSheet1->mergeCells("B".$currentProgramFirstRow.":"."B".($rowIndex));
                //     }
                //     $currentProgram = $thisSheet1->getCell("B".$rowIndex)->getValue();
                //     $currentProgramFirstRow = $rowIndex;
                // }
            }
        }

        $rowStyle = [2,3]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [2,3,5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            // $thisSheet1->mergeCells('B'.$val.':E'.$val);
        }

        // styling manual berdasar cell
        $lastColumnSheet1         = $thisSheet1->getHighestColumn();
        $lastRowSheet1            = $thisSheet1->getHighestRow();
        $columnAfterTitleSheet1   = count($titleSheet1)+1;
        $columnQuestionSheet1     = 0;
        $columnAllDataSheet1      = $lastColumnSheet1.($lastRowSheet1 - ($columnQuestionSheet1+2));

        $thisSheet1->mergeCells('A2:E2');
        $allWorkSheet[] = $workSheet1;


        // ========================== Sheet KEDUA ==================================

        foreach($resultProgram as $program) {
            $programName = $program->program;
            $allIdEventProgram = $resultProgram->pluck('id_event_program')->unique();
            // dd($allIdEventProgram);

            $getAnswerEssay = DB::table('hr_event_program as hep')
                ->leftJoin('master_course_header as mch', 'mch.id_course_header', '=', 'hep.id_course_header')
                ->leftJoin('master_course_detail as mcd', function ($join) {
                    $join->on('mcd.id_course_header', '=', 'mch.id_course_header');
                    $join->whereNull('mcd.id_content_learning');
                })
                ->join('hr_survey_question as hsq', function ($join) {
                    $join->on('hsq.id_survey_header', '=', 'mcd.id_survey_header');
                    // $join->whereIn('hsq.id_question_type', $allIdGeneralDataEssay);
                })
                ->join('master_general_data as mgd', 'hsq.id_question_type', 'mgd.id_general_data')
                ->leftJoin('hr_survey_header as hsh', 'mcd.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('hr_event_management as hem', 'hem.id_event_management', '=', 'hep.id_event_management')
                ->leftJoin('hr_event_attendees as hea', 'hea.id_event_program', '=', 'hep.id_event_program')
                ->leftJoin('hr_employee as he', 'hea.booked_by', '=', 'he.id_employee')
                ->leftJoin('hr_survey_answer_user_header as hsauh', function ($join) {
                    $join->on('hsauh.id_survey_header', '=', 'hsh.id_survey_header');
                    $join->on('hsauh.id_employee', '=', 'hea.booked_by');
                    $join->on('hsauh.id_course_detail', '=', 'mcd.id_course_detail');
                })
                ->leftJoin('hr_survey_answer_user as hsau', function ($join) {
                    $join->on('hsau.id_survey_answer_user_header', '=', 'hsauh.id_survey_answer_user_header');
                    $join->on('hsq.id_survey_question', '=', 'hsau.id_survey_question');
                })
                ->leftJoin('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
                ->leftJoin('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
                ->leftJoin('master_branch as mb', 'mpd.id_branch', 'mb.id_branch')
                ->leftJoin('master_region as mr', 'mb.id_region', 'mr.id_region')
                ->select('hem.description as program', 'mcd.course_type', 'mch.course_name as course', 'hep.description as course_description', 'hsh.notes', 'hea.booked_by', 'he.nik_employee', 'he.name', 'hsq.question', 'hsau.description_answer', 'hsau.essay_score', 'hsauh.total_score', 'hep.pass_scores', 'hsau.creation_date as answered_at', 'mpr.description as position', 'mb.description as branch', 'mr.description as region')
                ->whereIn('hep.id_event_program', $allIdEventProgram)
                // ->whereIn('hem.id_event_management', $idEventManagement)
                ->where('mgd.code', 'Essay')
                // ->where('mpd.status', 'A')
                // ->whereIn('mcd.course_type', $courseType)
                // ->groupBy('hem.description', 'mcd.course_type', 'mch.course_name', 'hep.description', 'hsh.notes', 'hea.booked_by', 'he.nik_employee', 'he.name', 'hep.start_date', 'hsq.question', 'hsau.description_answer', 'hsau.essay_score', 'hsau.creation_date')
                ->orderByRaw('he.name ASC, hep.sequence ASC, hsq.sequence ASC')
                ->get();
                // dd($getAnswerEssay->get(), $getAnswerEssay->toSql(), $allIdGeneralDataEssay, $allIdEventProgram);

            $dataSheet2         = [];
            $titleSheet2        = ['No', 'Date', 'NIK', 'Name', 'Position', 'Branch', 'Region', 'Program', 'Course Type', 'Course', 'Description', 'Question', 'Answer', 'Score', 'Total Score', 'Passing Grade', 'Result'];
            $countTitleSheet2   = count($titleSheet2);
            $dataSheet2[]       = $titleSheet2;

            if($getAnswerEssay->count() > 0){
                $number = 1;
                foreach ($getAnswerEssay as $k => $val) {
                    if($val->pass_scores) {
                        if($val->pass_scores != 1) {
                            $result = $val->total_score >= $val->pass_scores ? "Pass" : "Fail";
                        } else {
                            $result = $val->total_score >= $val->pass_scores ? "Hit" : "Miss";
                        }
                    } else {
                        $result = "No Passing Grade";
                    }
                    $resultValue = [
                        $number,
                        $val->answered_at,
                        $val->nik_employee,
                        $val->name,
                        $val->position,
                        $val->branch,
                        $val->region,
                        $val->program,
                        @$courseType[@$val->course_type],
                        $val->course,
                        $val->course_description,
                        $val->question,
                        $val->description_answer,
                        $val->essay_score,
                        $val->total_score,
                        $val->pass_scores,
                        $result
                    ];
                    $number++;
                    $dataSheet2[] = $resultValue; 
                }
            }
        }
        

        $nameSheet2 = 'Essay Result';
        $indexSheet2 = 1;
        $workSheet2 = new Worksheet($spreadsheet, $nameSheet2);
        $spreadsheet->addSheet($workSheet2, $indexSheet2);
        $thisSheet2 = $spreadsheet->getSheet($indexSheet2)->setTitle($nameSheet2);
        $workSheet2->fromArray($dataSheet2); //ngolah array dimasukkan ke cell masing2

        $rowStyle = [1]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet2->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet2->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
        }

        $lastColumnSheet2         = $thisSheet2->getHighestColumn();
        $lastRowSheet2            = $thisSheet2->getHighestRow();
        $columnAfterTitleSheet2   = count($titleSheet2)+1;
        $columnQuestionSheet2     = 0;
        $columnAllDataSheet2      = $lastColumnSheet2.($lastRowSheet2 - ($columnQuestionSheet2+2));

        $allWorkSheet[] = $workSheet2;

        // ========================================================================

        // $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        $spreadsheet->setActiveSheetIndexByName($nameSheet1); // utk set sheet yg aktif
        $worksheets = $allWorkSheet;
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        if($exportType == 'excel'){
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer->save('php://output');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            $writer->save('php://output');
        }
    }

    public function downloadEpstp($programName, $getCourseResult, $allIdEventProgram, $idEventManagement) {
        $exportType = 'excel';
        $filename = 'Summary '.$programName;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan
        $allWorkSheet = [];
        $getCourseResult = $getCourseResult->groupBy('nik_employee');
        $courseType = [
            'Materi' => 'Materi',
            'Quiz' => 'Quiz',
            'Quiz_Pretest' => 'Quiz Pretest',
            'Quiz_Posttest' => 'Quiz Post Test',
            'Quiz_Remidial' => 'Quiz Remidial',
            // null => '',
        ];
        // dd($getCourseResult['2019120666']);
        $dataSheet1 = [
            [''],
            ['Summary Employee Course'],
            ['Program', ' : '.$programName],
            [''],
        ];
        $titleSheet1        = ['No', 'NIK', 'Name', 'Department', 'Position', 'Branch', 'Region', 'Passed Course', '% Hit Rate Pass', 'Number of Course', 'Course Name', 'Description', 'Start', 'End'];
        $scoreTitle         = [];
        foreach($courseType as $key => $val) {
            array_push($titleSheet1, $key);
            array_push($scoreTitle, $key);
        }
        
        $countTitleSheet1   = count($titleSheet1);
        array_push($titleSheet1, 'Passing Grade');
        array_push($titleSheet1, 'Result');
        $dataSheet1[]       = $titleSheet1;
        // dd($getCourseResult['BCP0008376']);
        if($getCourseResult->count() > 0){
            $number = 1;
            foreach ($getCourseResult as $k => $items) {
                // dd($k, $items);
                $groupByCourse = $items->groupBy('course');
                // dd($groupByCourse);
                foreach($groupByCourse as $course) {
                    $dates = [];
                    $scores = [];
                    foreach($course as $testType) {
                        if($testType->creation_date) {
                            array_push($dates, $testType->creation_date);
                        }
                        // dd($testType);
                        if($testType->course_type=='Materi'){
                            $scores[$testType->course_type] = [$testType->course, 'done'];
                        } else {
                            $scores[$testType->course_type] = [$testType->course, @$testType->total_score ?? '-'];
                        }


                        $scorables = $getCourseResult[$testType->nik_employee]->whereIn('course_type', ['Quiz_Posttest', 'Quiz_Remidial'])->whereNotNull('pass_scores');
                        $pass = 0;
                        foreach($scorables as $allCourse) {
                            if($allCourse->total_score >= $allCourse->pass_scores) {
                                $pass++;
                            }
                        }

                        $resultValue = [
                            $number,
                            $testType->nik_employee,
                            $testType->name,
                            $testType->department,
                            $testType->position,
                            $testType->branch,
                            $testType->region,
                            $pass." of ".count($scorables),
                            number_format(($pass/count($scorables)*100), 2)."%",
                            count($groupByCourse),
                            // $val->program,
                            $testType->course,
                            $testType->course_description,
                            // @$courseType[@$val->course_type],
                            // $score,
                        ];
                        array_push($resultValue, $testType->creation_date);
                        array_push($resultValue, $testType->creation_date);

                        foreach($scoreTitle as $key) {
                            if(key_exists($key, $scores)) {
                                if($course[0]->course == $scores[$key][0]) {
                                    $val = $scores[$key][1];
                                    if($key == 'Materi' && $scores[$key][1] == '-') {
                                        if($course->where('course_type', 'Materi')->first()) {
                                            $val = 'assign';
                                        }
                                    }
                                    array_push($resultValue, $val);
                                } else {
                                    array_push($resultValue, '-');
                                }
                            } else {
                                array_push($resultValue, '-');
                            }
                        }
                        
                        
                        
                        $score_result = '-';
                        if($testType->total_score) {
                            if($testType->pass_scores == null) {
                                $score_result = 'No Passing Grade';
                            } else if($testType->total_score >= $testType->pass_scores) {
                                $score_result = 'PASS';
                            } else {
                                $score_result = 'FAIL';
                            }
                        } else if(key_exists('Quiz_Posttest', $scores)) {
                            if($scores['Quiz_Posttest'][1] >= $testType->pass_scores) {
                                $score_result = 'PASS';
                            } else {
                                $score_result = 'FAIL';
                            }
                        } else if(key_exists('Quiz_Remidial', $scores)) {
                            if($scores['Quiz_Remidial'][1] >= $testType->pass_scores) {
                                $score_result = 'PASS';
                            } else {
                                $score_result = 'FAIL';
                            }
                        }
                        
                        array_push($resultValue, $testType->pass_scores ?? 'N/A');
                        array_push($resultValue, $score_result);
                        $number++;
                        $dataSheet1[] = $resultValue; 
                    }

                }

            }
        }
        // dd($dataSheet1);

        $nameSheet1 = 'Score Result';
        $indexSheet1 = 0;
        $workSheet1 = new Worksheet($spreadsheet, $nameSheet1);
        $spreadsheet->addSheet($workSheet1, $indexSheet1);
        $thisSheet1 = $spreadsheet->getSheet($indexSheet1)->setTitle($nameSheet1);
        $workSheet1->fromArray($dataSheet1); //ngolah array dimasukkan ke cell masing2
        
        
        $currentNik = null;
        $currentNikFirstRow = null;
        foreach ($workSheet1->getRowIterator() as $rowIndex => $row) {
            if($rowIndex > 5) {
                if($thisSheet1->getCell("B".$rowIndex)->getValue() != $currentNik || $rowIndex == $thisSheet1->getHighestDataRow()) {
                    
                    $cols = ["B", "C", "D", "E", "F", "G", "H", "I", "J"];
                    foreach($cols as $col) {
                        if($rowIndex == $thisSheet1->getHighestDataRow()) {
                            // last row
                            if($rowIndex != 6) {
                                $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex));
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                            }
                        } else {
                            if($rowIndex != 6) {
                                $thisSheet1->mergeCells($col.$currentNikFirstRow.":".$col.($rowIndex-1));
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setHorizontal('center');
                                $thisSheet1->getStyle($col.$currentNikFirstRow.":".$col.($rowIndex))->getAlignment()->setVertical('center');
                            }
                        }
                    }
                    $currentNik = $thisSheet1->getCell("B".$rowIndex)->getValue();
                    $currentNikFirstRow = $rowIndex;
                }
            }
        }
        // dd("-");

        $rowStyle = [2,3]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [2,3,5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            // $thisSheet1->mergeCells('B'.$val.':E'.$val);
        }

        // styling manual berdasar cell
        $lastColumnSheet1         = $thisSheet1->getHighestColumn();
        $lastRowSheet1            = $thisSheet1->getHighestRow();
        $columnAfterTitleSheet1   = count($titleSheet1)+1;
        $columnQuestionSheet1     = 0;
        $columnAllDataSheet1      = $lastColumnSheet1.($lastRowSheet1 - ($columnQuestionSheet1+2));

        $thisSheet1->mergeCells('A2:E2');
        $allWorkSheet[] = $workSheet1;
        // dd($workSheet1);

        // ==============================SHEET 2

        $allIdGeneralDataEssay = DB::table('master_general_data')->where('code','Essay')->where('id_company', session('id_company'))->get()->pluck('id_general_data')->all();
        $getAnswerEssay = DB::table('hr_event_program as hep')
            ->leftJoin('master_course_header as mch', 'mch.id_course_header', '=', 'hep.id_course_header')
            ->leftJoin('master_course_detail as mcd', function ($join) {
                $join->on('mcd.id_course_header', '=', 'mch.id_course_header');
                $join->whereNull('mcd.id_content_learning');
            })
            ->join('hr_survey_question as hsq', function ($join) {
                $join->on('hsq.id_survey_header', '=', 'mcd.id_survey_header');
                // $join->whereIn('hsq.id_question_type', $allIdGeneralDataEssay);
            })
            ->join('master_general_data as mgd', 'hsq.id_question_type', 'mgd.id_general_data')
            ->leftJoin('hr_survey_header as hsh', 'mcd.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('hr_event_management as hem', 'hem.id_event_management', '=', 'hep.id_event_management')
            ->leftJoin('hr_event_attendees as hea', 'hea.id_event_program', '=', 'hep.id_event_program')
            ->leftJoin('hr_employee as he', 'hea.booked_by', '=', 'he.id_employee')
            ->leftJoin('hr_survey_answer_user_header as hsauh', function ($join) {
                $join->on('hsauh.id_survey_header', '=', 'hsh.id_survey_header');
                $join->on('hsauh.id_employee', '=', 'hea.booked_by');
                $join->on('hsauh.id_course_detail', '=', 'mcd.id_course_detail');
                $join->on('hsauh.id_event_program', '=', 'hep.id_event_program');
            })
            ->leftJoin('hr_survey_answer_user as hsau', function ($join) {
                $join->on('hsau.id_survey_answer_user_header', '=', 'hsauh.id_survey_answer_user_header');
                $join->on('hsq.id_survey_question', '=', 'hsau.id_survey_question');
            })
            ->leftJoin('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
            ->leftJoin('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
            ->leftJoin('master_branch as mb', 'mpd.id_branch', 'mb.id_branch')
            ->leftJoin('master_region as mr', 'mb.id_region', 'mr.id_region')
            ->select('hem.description as program', 'mcd.course_type', 'mch.course_name as course', 'hep.description as course_description', 'hsh.notes', 'hea.booked_by', 'he.nik_employee', 'he.name', 'hsq.question', 'hsau.description_answer', 'hsau.essay_score', 'hsauh.total_score', 'hep.pass_scores', 'hsau.creation_date as answered_at', 'mpr.description as position', 'mb.description as branch', 'mr.description as region')
            ->whereIn('hep.id_event_program', $allIdEventProgram)
            ->where('hem.id_event_management', $idEventManagement)
            ->where('mgd.code', 'Essay')
            ->where('mpd.status', 'A')
            ->where('hep.status', 'A')
            // ->whereIn('mcd.course_type', $courseType)
            // ->groupBy('hem.description', 'mcd.course_type', 'mch.course_name', 'hep.description', 'hsh.notes', 'hea.booked_by', 'he.nik_employee', 'he.name', 'hep.start_date', 'hsq.question', 'hsau.description_answer', 'hsau.essay_score', 'hsau.creation_date')
            ->orderBy('mch.course_name')
            ->orderBy('hep.start_date')
            ->orderBy('he.name')
            ->get();
            // dd($getAnswerEssay->get(), $getAnswerEssay->toSql(), $allIdGeneralDataEssay, $allIdEventProgram);

        $dataSheet2         = [];
        $titleSheet2        = ['No', 'Date', 'NIK', 'Name', 'Position', 'Branch', 'Region', 'Program', 'Course Type', 'Course', 'Description', 'Question', 'Answer', 'Score', 'Total Score', 'Passing Grade', 'Result'];
        $countTitleSheet2   = count($titleSheet2);
        $dataSheet2[]       = $titleSheet2;

        if($getAnswerEssay->count() > 0){
            $number = 1;
            foreach ($getAnswerEssay as $k => $val) {
                if($val->pass_scores) {
                    if($val->pass_scores != 1) {
                        $result = $val->total_score >= $val->pass_scores ? "Pass" : "Fail";
                    } else {
                        $result = $val->total_score >= $val->pass_scores ? "Hit" : "Miss";
                    }
                } else {
                    $result = "No Passing Grade";
                }
                $resultValue = [
                    $number,
                    $val->answered_at,
                    $val->nik_employee,
                    $val->name,
                    $val->position,
                    $val->branch,
                    $val->region,
                    $val->program,
                    @$courseType[@$val->course_type],
                    $val->course,
                    $val->course_description,
                    $val->question,
                    $val->description_answer,
                    $val->essay_score,
                    $val->total_score,
                    $val->pass_scores,
                    $result
                ];
                $number++;
                $dataSheet2[] = $resultValue; 
            }
        }

        $nameSheet2 = 'Essay Result';
        $indexSheet2 = 1;
        $workSheet2 = new Worksheet($spreadsheet, $nameSheet2);
        $spreadsheet->addSheet($workSheet2, $indexSheet2);
        $thisSheet2 = $spreadsheet->getSheet($indexSheet2)->setTitle($nameSheet2);
        $workSheet2->fromArray($dataSheet2); //ngolah array dimasukkan ke cell masing2

        $rowStyle = [1]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $thisSheet2->getStyle($val.':'.$val)->getFont()->setBold(true);
            $thisSheet2->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
        }

        $lastColumnSheet2         = $thisSheet2->getHighestColumn();
        $lastRowSheet2            = $thisSheet2->getHighestRow();
        $columnAfterTitleSheet2   = count($titleSheet2)+1;
        $columnQuestionSheet2     = 0;
        $columnAllDataSheet2      = $lastColumnSheet2.($lastRowSheet2 - ($columnQuestionSheet2+2));

        $allWorkSheet[] = $workSheet2;

        // ========================================================================

        // $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        $spreadsheet->setActiveSheetIndexByName($nameSheet1); // utk set sheet yg aktif
        $worksheets = $allWorkSheet;
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        if($exportType == 'excel'){
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer->save('php://output');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            $writer->save('php://output');
        }
    }
}
