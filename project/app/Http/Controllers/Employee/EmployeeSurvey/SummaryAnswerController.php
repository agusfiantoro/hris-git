<?php

namespace App\Http\Controllers\Employee\EmployeeSurvey;

use App\Models\Employee\EmployeeSetting\HrSurveyAnswerUser;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswerUserHeader;
use App\Models\Employee\EmployeeSetting\HrSurveyHeader;
use App\Http\Controllers\Controller;
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
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;

class SummaryAnswerController extends Controller {

    public function index(Request $request) {
        $survey = 0;
        $surveyName = '';
        $id_survey_header = @$request->id;
        if($id_survey_header){
            $checkSurvey = HrSurveyHeader::where('id_survey_header', $id_survey_header)->first();
            if($checkSurvey){
                $survey = $id_survey_header;
                $surveyName = $checkSurvey->description;
            }
        } 
        if($survey == 0){
            if ($request->ajax()) {
                $data = HrSurveyHeader::getAllPublished();
                foreach ($data as $key => $item) {
                    if(HrSurveyAnswerUserHeader::where('id_survey_header', $item->id_survey_header)->first() != null){
                        $data[$key]->status_survey = 'done';
                    } else {
                        $data[$key]->status_survey = '';
                    }
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        return $data->id_survey_header;
                    })
                    ->editColumn('start_date', function ($data){
                        $return = '-';
                        if(!is_null($data->start_date)){
                            $return = Carbon::parse($data->start_date)->translatedFormat('d F Y');
                        }
                        return $return;
                    })
                    ->editColumn('end_date', function ($data){
                        $return = '-';
                        if(!is_null($data->end_date)){
                            $return = Carbon::parse($data->end_date)->translatedFormat('d F Y');
                        }
                        return $return;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }
        return view('employee.employee_survey.summary_answer.index', compact('survey', 'surveyName'));
    }

    public function get_survey(Request $request) {
        if ($request->ajax()) {
            $data = DB::table('hr_survey_answer_user_header as hsauh')
                            ->join('hr_employee as he', 'hsauh.id_employee', '=', 'he.id_employee', 'left')
                            ->join('hr_survey_header as hsh', 'hsh.id_survey_header', '=', 'hsauh.id_survey_header', 'left')
                            ->select('he.name', 'he.nik_employee', 'hsh.description as survey_name', 'hsauh.id_survey_header', 'hsauh.id_employee', 'hsauh.total_score', 'hsauh.creation_date', 'hsh.with_score')
                            ->where('hsauh.id_survey_header', $request->id_survey_header)->get();
            foreach ($data as $key => $item) {
                if(HrSurveyAnswerUserHeader::where('id_survey_header', $item->id_survey_header)->first() != null){
                    $data[$key]->status_survey = 'done';
                } else {
                    $data[$key]->status_survey = '';
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('action', function($data) {
                    return $data->id_survey_header;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function get_result(Request $request) {
        try {
            $id_survey_header   = $request->id_survey_header;
            $id_employee        = $request->id_employee;
            $all_result         = [];
            $show_score         = null;

            $getEmployee = DB::table('hr_employee as he')
                            ->select('he.name', 'he.nik_employee')
                            ->where('he.id_employee', $id_employee)->first();
            
            $getResult = HrSurveyAnswerUserHeader::where('id_employee', $id_employee)->where('id_survey_header', $id_survey_header)->first();
            if(is_null($getResult)){
                throw new \Exception('Tidak ada hasil pada karyawan tersebut');
            }

            $getSurveyHeader = DB::table('hr_survey_header as hsh')
                            ->select('hsh.*')->where('hsh.id_survey_header', $id_survey_header)->first();
            $show_score = $getSurveyHeader->with_score;
            
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
                    $myAnswer = @$getDescriptionAnswer->suggested_answer;

                    if(!is_null(@$getDescriptionAnswer->id_answer)){
                        $getMasterAnswer = DB::table('master_survey_answer as msa')
                                ->select('msa.description_answer')
                                ->where('msa.id_answer', @$getDescriptionAnswer->id_answer)->first();
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
            $result['show_score']   = $show_score;
            $result['name']         = $getEmployee->name;
            $result['nik_employee'] = $getEmployee->nik_employee;

            return response()->json(['status' => 'true', 'message' => 'Berhasil', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function summary_survey_single(Request $request) {
        ini_set('max_execution_time', -1);
        
        $id_survey_header   = $request->id_survey_header;
        $thisSurvey = DB::table('hr_survey_header as hsh')->where('id_survey_header', $id_survey_header)->first();
        if(!$thisSurvey){
            return redirect(url('home'));
        }

        $getSurveyHistory = DB::table('hr_survey_history')->where('id_survey_header', $id_survey_header)->get();

        $exportType = 'excel';
        $filename   = 'summary-'.@$thisSurvey->description;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        if($getSurveyHistory->count() > 0){
            $thisWorkSheet = [];

            foreach ($getSurveyHistory as $k_h => $val_h) {
                $idSurveyHistory = $val_h->id_survey_history;
                $getSurveyHeader = DB::table('hr_survey_header as hsh')
                    ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                    ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hsh.id_employee_request')
                    ->leftJoin('hr_survey_history as hshy', 'hshy.id_survey_header', '=', 'hsh.id_survey_header')
                    ->select('hsh.*', 'mgd.description as survey_type', 'he.name as request_by', 'hshy.start_date as date_start', 'hshy.end_date as date_end')
                    ->where('hsh.id_survey_header', $id_survey_header)
                    ->where('hshy.id_survey_history', $idSurveyHistory)
                    ->first();
                $surveyName = $getSurveyHeader->description;
                $requestBy = $getSurveyHeader->request_by;
                $surveyType = $getSurveyHeader->survey_type;
                $start = date("j M Y", strtotime($getSurveyHeader->date_start));
                $end = date("j M Y", strtotime($getSurveyHeader->date_end));
                $withScore = ($getSurveyHeader->with_score == null || $getSurveyHeader->with_score == false) ? 'No' : 'Yes';

                $id_department = DB::table('relation_department_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_dept')->all();
                $id_region = DB::table('relation_regional_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_region')->all();
                $id_branch = DB::table('relation_branch_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_branch')->all();
                $id_job_grade = DB::table('relation_jobgrade_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_job_grade')->all();
                $department = 'All';
                $region = 'All';
                $branch = 'All';
                $jobGrade = 'All';

                if($id_department){
                    $getDepartment = DB::table('master_department')->whereIn('id_dept', $id_department)->get()->pluck('description')->all();
                    $department = implode(', ', $getDepartment);
                }
                if($id_region){
                    $getRegion = DB::table('master_region')->whereIn('id_region', $id_region)->get()->pluck('description')->all();
                    $region = implode(', ', $getRegion);
                }
                if($id_branch){
                    $getbranch = DB::table('master_branch')->whereIn('id_branch', $id_branch)->get()->pluck('description')->all();
                    $branch = implode(', ', $getbranch);
                }
                if($id_job_grade){
                    $getJobGrade = DB::table('master_job_grade')->whereIn('id_job_grade', $id_job_grade)->get()->pluck('description')->all();
                    $jobGrade = implode(', ', $getJobGrade);
                }

                $getAnswer = DB::table('hr_survey_answer_user as hsau')
                            ->leftJoin('hr_survey_answer_user_header as hsauh', 'hsau.id_survey_answer_user_header', '=', 'hsauh.id_survey_answer_user_header')
                            ->leftJoin('hr_survey_answer as hsa', 'hsa.id_survey_answer', '=', 'hsau.id_survey_answer')
                            ->leftJoin('master_survey_answer as msa', 'msa.id_answer', '=', 'hsa.id_answer')
                            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hsauh.id_employee')
                            ->select(
                                'hsau.*', 
                                'msa.description_answer as choice_answer', 
                                'hsauh.total_score', 
                                'he.name', 
                                DB::raw("CASE
                                            WHEN he.status = 'A' THEN he.nik_employee
                                            ELSE he.nik_employee || ' (Inactive)'
                                        END AS nik_employee")
                            )
                            ->where('hsauh.id_survey_header', $id_survey_header)
                            ->where('hsauh.id_company', session('id_company'))
                            ->whereRaw("(hsauh.creation_date::date >= '$val_h->start_date' AND hsauh.creation_date::date <= '$val_h->end_date')") // filter answer created during this period
                            ->get();

                $dataSheet = [
                    [''],
                    ['Summary Employee Survey'],
                    [''],
                    ['Name', ' : '.$surveyName],
                    ['Request By', ' : '.$requestBy],
                    ['Survey Type', ' : '.$surveyType],
                    ['Start', ' : '.$start],
                    ['End', ' : '.$end],
                    ['With Score', ' : '.$withScore],
                    ['Department', ' : '.$department],
                    ['Region', ' : '.$region],
                    ['Branch', ' : '.$branch],
                    [''],
                ];

                $title          = ['No', 'NIK', 'Name', 'Created At'];
                // $title          = ['No', 'Surveyor', 'Created At'];
				if($withScore == 'Yes'){
					array_push($title,"Score");
				}
                $countTitle     = count($title);
                $getQuestion    = DB::table('hr_survey_question as hsq')
                                    ->select('hsq.sequence', 'hsq.id_survey_question', 'hsq.question')
                                    ->where('hsq.id_survey_header', $id_survey_header)
                                    ->orderBy('hsq.sequence')
                                    ->get();
									
                if($getAnswer){
                    $all_nik        = $getAnswer->pluck('nik_employee');
                    $all_name       = $getAnswer->pluck('name');
                    $all_created_at = $getAnswer->pluck('creation_date');
                    $all_total		= $getAnswer->pluck('total_score');
                    $all_id_question= $getAnswer->pluck('id_survey_question')->unique();

                    $thisQuestion = [];
                    foreach ($getQuestion as $key => $val) {
                        $thisQuestion[$val->id_survey_question] = $val->sequence;
                        $title[$countTitle] = $val->sequence;
                        $countTitle++;
                    }
                    $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel

                    $summary        = [];
                    foreach ($all_nik as $k => $v) {
                        if(!in_array($v, $summary)){
                            $summary[$v]['nik_employee'] = @$v;
                            $summary[$v]['name'] = @$all_name[$k];
                            $summary[$v]['created_at'] = date("j M Y H:i:s", strtotime(@$all_created_at[$k]));
                        //    $summary[$v]['score'] = @$all_total[$k];
                        }
                    }

                    foreach ($getAnswer as $k => $val) {
                        if($val->nik_employee == $summary[$val->nik_employee]['nik_employee']){
                            if(is_null($val->id_survey_answer)){
                                if(!is_null($val->description_answer)){
                                    $summary[$val->nik_employee][$thisQuestion[$val->id_survey_question]] = "'".strip_tags(html_entity_decode($val->description_answer, ENT_QUOTES));
                                } else {
                                    $surveyPathStorage = 'public/upload/survey/';
                                    if($val->attachment){
                                        //jika bukan essay dan kolom attachment tidak null
                                        if(Storage::exists($surveyPathStorage.$val->attachment)) {
                                            $urlAttachment = url('project/storage/app/'.$surveyPathStorage.@$val->attachment);
                                        } else {
                                            //jika kolom attachment tidak null tapi tidak ditemukan di storage sistem
                                            $urlAttachment = 'File Moved / Not Found';
                                        }
                                    } else {
                                        //jika bukan essay dan kolom attachment null
                                        $urlAttachment = 'null';
                                    }
                                    $summary[$val->nik_employee][$thisQuestion[$val->id_survey_question]] = $urlAttachment;
                                }
                            } else {
                                $shortcut = @$summary[$val->nik_employee][$thisQuestion[$val->id_survey_question]];
                                if(!is_null($shortcut)){
                                    //Kalau pertanyaan multi answer jawabannya digabung langsung dengan koma
                                    $merge = $shortcut.', '.$val->choice_answer;
                                    $summary[$val->nik_employee][$thisQuestion[$val->id_survey_question]] = strip_tags(html_entity_decode($merge, ENT_QUOTES));
                                } else {
                                    $summary[$val->nik_employee][$thisQuestion[$val->id_survey_question]] = strip_tags(html_entity_decode($val->choice_answer, ENT_QUOTES));
                                }
                            }
                             $summary[$val->nik_employee]['score'] = ($withScore == 'No') ? '' : $val->total_score;
                        }
                    }
                    $sort = array_column($summary, 'name'); // sort result by name
                    array_multisort($sort, SORT_ASC, $summary);

                    $number = 1;
                    $resultValue = [];
                    $countTableContent = 4;
                    if(count($summary) > 0){
                        foreach ($summary as $key => $val) {
                            $resultValue = [
                                $number,
                                // $val['name'].' ('.$val['nik_employee'].')',
                                // Str::random(15),
                                $val['nik_employee'],
                                $val['name'],
                                $val['created_at'],
                            //    $val['score'],
                            ];
							if($withScore == 'Yes'){
								array_push($resultValue,$val['score']);
							}
                            foreach ($getQuestion as $k => $item) {
                                $resultValue[] = @$val[@$item->sequence];
                            }
                            $countTableContent = count($resultValue);
                            $number++;
                            $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
                        }
                    }
                }

                $dataSheet[] = ['']; 
                $dataSheet[] = ['Question']; 
                foreach ($getQuestion as $key => $val) {
                    $dataSheet[] = [$val->sequence, ' : ', $val->question]; 
                }

                $sheetName = $start.' - '.$end;
                $workSheetTab = new Worksheet($spreadsheet, $sheetName);
                $spreadsheet->addSheet($workSheetTab, 0);
                $spreadsheet->setActiveSheetIndexByName($sheetName); // utk set sheet yg aktif
                $workSheetTab->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2

                $activeSheet = $spreadsheet->getActiveSheet();
                $rowStyle = [2,14]; // Identitas Row yang akan di style kan
                foreach ($rowStyle as $k => $val) {
                    $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
                    $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
                }
                $rowStyle = [3,4,5,6,7,8,9,10,11,12]; // Identitas Row yang akan di style kan
                foreach ($rowStyle as $k => $val) {
                    $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
                    $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
                    $activeSheet->mergeCells('B'.$val.':E'.$val);
                }

                // styling manual berdasar cell
                $lastColumn         = $activeSheet->getHighestColumn();
                $lastRow            = $activeSheet->getHighestRow();
                $columnAfterTitle   = count($title)+1;
                $columnQuestion     = count($getQuestion);
                $columnAllData      = $lastColumn.($lastRow - ($columnQuestion+2));

                $activeSheet->mergeCells('A2:E2');
                $activeSheet->getStyle('A14:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $activeSheet->getStyle('A15:'.'A'.$lastRow)->getAlignment()->setHorizontal('center');
                $activeSheet->getStyle('B15:'.$columnAllData)->getAlignment()->setHorizontal('left');
				if($withScore == 'Yes'){
					$activeSheet->getStyle('E15:'.'E'.$lastRow)->getAlignment()->setHorizontal('center');					
				}
                $thisWorkSheet[] = $workSheetTab;
            }
        }
        // dd($thisWorkSheet);

        // $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        $worksheets = $thisWorkSheet;
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

    public function summary_survey(Request $request) {
        ini_set('max_execution_time', -1);
        
        $id_survey_header   = $request->id_survey_header;
        $getSurveyHeader = DB::table('hr_survey_header as hsh')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hsh.id_employee_request')
            ->select('hsh.*', 'mgd.description as survey_type', 'he.name as request_by')->where('hsh.id_survey_header', $id_survey_header)->first();
        $surveyName = $getSurveyHeader->description;
        $requestBy = $getSurveyHeader->request_by;
        $surveyType = $getSurveyHeader->survey_type;
        $start = date("j M Y", strtotime($getSurveyHeader->start_date));
        $end = date("j M Y", strtotime($getSurveyHeader->end_date));
        $withScore = ($getSurveyHeader->with_score == null || $getSurveyHeader->with_score == false) ? 'No' : 'Yes';
        $id_department = DB::table('relation_department_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_dept')->all();
        $id_region = DB::table('relation_regional_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_region')->all();
        $id_branch = DB::table('relation_branch_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_branch')->all();
        $id_job_grade = DB::table('relation_jobgrade_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_job_grade')->all();
        $department = '';
        $region = '';
        $branch = '';
        $jobGrade = '';

        if($id_department){
            $getDepartment = DB::table('master_department')->whereIn('id_dept', $id_department)->get()->pluck('description')->all();
            $department = implode(', ', $getDepartment);
        }
        if($id_region){
            $getRegion = DB::table('master_region')->whereIn('id_region', $id_region)->get()->pluck('description')->all();
            $region = implode(', ', $getRegion);
        }
        if($id_branch){
            $getbranch = DB::table('master_branch')->whereIn('id_branch', $id_branch)->get()->pluck('description')->all();
            $branch = implode(', ', $getbranch);
        }
        if($id_job_grade){
            $getJobGrade = DB::table('master_job_grade')->whereIn('id_job_grade', $id_job_grade)->get()->pluck('description')->all();
            $jobGrade = implode(', ', $getJobGrade);
        }

        $getAnswer = DB::table(DB::raw("sp_funct_detail_survey_view(".$id_survey_header.", ".session('id_company').", null) sfdsv"))
                ->select('sfdsv.*')->get();

        $headerSheet = [
            [''],
            ['Summary Employee Survey'],
            [''],
            ['Name', ' : '.$surveyName],
            ['Request By', ' : '.$requestBy],
            ['Survey Type', ' : '.$surveyType],
            ['Start', ' : '.$start],
            ['End', ' : '.$end],
            ['With Score', ' : '.$withScore],
            ['Department', ' : '.$department],
            ['Region', ' : '.$region],
            ['Branch', ' : '.$branch],
            ['Job Grade', ' : '.$jobGrade],
            [''],
        ];

        $dataSheet      = $headerSheet;
        $title          = ['No', 'Surveyor', 'Department', 'Regional', 'Branch', 'Point'];
        $countTitle     = count($title);
        $getQuestion    = DB::table('hr_survey_question as hsq')
                            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsq.id_question_group')
                            ->select('hsq.sequence', 'hsq.id_survey_question', 'hsq.question', 'mgd.description as group')->where('hsq.id_survey_header', $id_survey_header)->get();
        if($getAnswer){
            $allSurveyor = $getAnswer->pluck('unique_surveyor');
            $allSurveyDepartment = $getAnswer->pluck('department');
            $allSurveyRegion = $getAnswer->pluck('regional');
            $allSurveyBranch = $getAnswer->pluck('branch');
            $allSurveyGroup = $getAnswer->pluck('group_survey');

            $thisQuestion = [];
            foreach ($getQuestion as $key => $val) {
                $thisQuestion[$val->sequence] = $val->sequence;
                $title[$countTitle] = $val->sequence;
                $countTitle++;
            }
            $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel

            $summary        = [];
            foreach ($allSurveyor as $k => $v) {
                if(!in_array($v, $summary)){
                    $summary[$v]['surveyor'] = @$v;
                    $summary[$v]['department'] = @$allSurveyDepartment[$k];
                    $summary[$v]['regional'] = @$allSurveyRegion[$k];
                    $summary[$v]['branch'] = @$allSurveyBranch[$k];
                    $summary[$v]['point'] = 0;
                }
            }

            foreach ($getAnswer as $k => $val) {
                if($val->unique_surveyor == $summary[$val->unique_surveyor]['surveyor']){
                    if(is_null($val->multiple_answer)){
                        $summary[$val->unique_surveyor][$thisQuestion[$val->nomor]] = $val->essay_answer;
                    } else {
                        $shortcut = strip_tags(@$summary[$val->unique_surveyor][$thisQuestion[$val->nomor]]);
                        if(!is_null($shortcut)){
                            $merge = $shortcut.', '.$val->multiple_answer;
                            $summary[$val->unique_surveyor][$thisQuestion[$val->nomor]] = $merge;
                        } else {
                            $summary[$val->unique_surveyor][$thisQuestion[$val->nomor]] = $val->multiple_answer;
                        }
                        $surveyorPoint[$val->unique_surveyor][] = $val->point;
                    }
                    if($withScore == 'No'){
                        $summary[$val->unique_surveyor]['point'] = '';
                    } else {
                        $summary[$val->unique_surveyor]['point'] += $val->point;
                    }
                }
            }
            $sort = array_column($summary, 'surveyor'); // sort result by name
            array_multisort($sort, SORT_ASC, $summary);

            $number = 1;
            $resultValue = [];
            $countTableContent = 4;
            foreach ($summary as $key => $val) {
                $resultValue = [
                    $number,
                    $val['surveyor'],
                    $val['department'],
                    $val['regional'],
                    $val['branch'],
                    $val['point'],
                ];
                foreach ($getQuestion as $k => $item) {
                    $resultValue[] = $val[$item->sequence];
                }
                $countTableContent = count($resultValue);
                $number++;
                $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
            }
        }
        $dataSheet[] = ['']; 
        $dataSheet[] = ['No', 'Group', 'Question']; 
        foreach ($getQuestion as $key => $val) {
            $dataSheet[] = [$val->sequence, $val->group, $val->question]; 
        }

        $exportType = 'excel';
        $filename   = 'summary-'.$surveyName;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->getDefaultStyle()->getNumberFormat()->setFormatCode('#');
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        $workSheet1 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet1, 0);
        $workSheet1->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2
        $sheet1 = $spreadsheet->getSheet(0)->setTitle("By Surveyor");

        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet1->mergeCells('B'.$val.':E'.$val);
        }

        $lastColumn         = $sheet1->getHighestColumn();
        $lastRow            = $sheet1->getHighestRow();
        $columnAfterTitle   = count($title)+1;
        $columnQuestion     = count($getQuestion);
        $columnAllData      = $lastColumn.($lastRow - ($columnQuestion+2));
        $rowQuestion        = $lastRow - count($getQuestion)+1;

        for ($x=0; $x < count($getQuestion); $x++) { 
            $rowQuestionFor = $rowQuestion+$x;
            $sheet1->mergeCells('C'.$rowQuestionFor.':'.$lastColumn.$rowQuestionFor);
        }
        $sheet1->mergeCells('A2:E2');
        $sheet1->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet1->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        // Sheet 2
        $workSheet2 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet2, 1);
        $dataSheet2 = $headerSheet;
        $getQ1 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 1) sfdsv"))->select('sfdsv.*')->get();
        $title2     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        $dataSheet2[] = $title2; 
        $numSheet2 = 1;
        $resultValue = [];
        foreach ($getQ1 as $key => $val) {
            $resultValue = [
                $numSheet2,
                $val->survey_name,
                $val->department,
                $val->regional,
                $val->branch,
                $val->group_survey,
                $val->nomor,
                $val->question_survey,
                $val->essay_answer,
                $val->total_surveyor,
                $val->max_point,
                $val->total_hit,
                round($val->percent, 2).' %',
            ];
            $numSheet2++;
            $dataSheet2[] = $resultValue; 
        }
        $workSheet2->fromArray($dataSheet2);
        $sheet2 = $spreadsheet->getSheet(1)->setTitle("By Question");
        $sheet2->mergeCells('A2:E2');
        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet2->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet2->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet2->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet2->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet2->mergeCells('B'.$val.':G'.$val);
        }
        $lastColumn         = $sheet2->getHighestColumn();
        $lastRow            = $sheet2->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
        $sheet2->mergeCells('A2:E2');
        $sheet2->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet2->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');

        

        // Sheet 3
        $workSheet3 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet3, 2);
        $dataSheet3 = $headerSheet;
        $getQ2 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 2) sfdsv"))->select('sfdsv.*')->get();
        $title3     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        $dataSheet3[] = $title3; 
        $numSheet3 = 1;
        $resultValue = [];
        foreach ($getQ2 as $key => $val) {
            $resultValue = [
                $numSheet3,
                $val->survey_name,
                $val->department,
                $val->regional,
                $val->branch,
                $val->group_survey,
                $val->nomor,
                $val->question_survey,
                $val->essay_answer,
                $val->total_surveyor,
                $val->max_point,
                $val->total_hit,
                round($val->percent, 2).' %',
            ];
            $numSheet3++;
            $dataSheet3[] = $resultValue; 
        }
        $workSheet3->fromArray($dataSheet3);
        $sheet3 = $spreadsheet->getSheet(2)->setTitle("By Question per Branch");
        $sheet3->mergeCells('A2:E2');
        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet3->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet3->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet3->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet3->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet3->mergeCells('B'.$val.':G'.$val);
        }
        $lastColumn         = $sheet3->getHighestColumn();
        $lastRow            = $sheet3->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
        $sheet3->mergeCells('A2:E2');
        $sheet3->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet3->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        // Sheet 4
        $workSheet4 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet4, 3);
        $dataSheet4 = $headerSheet;
        $getQ3 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 3) sfdsv"))->select('sfdsv.*')->get();
        $title4     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        $dataSheet4[] = $title4; 
        $numSheet4 = 1;
        $resultValue = [];
        foreach ($getQ3 as $key => $val) {
            $resultValue = [
                $numSheet4,
                $val->survey_name,
                $val->department,
                $val->regional,
                $val->branch,
                $val->group_survey,
                $val->nomor,
                $val->question_survey,
                $val->essay_answer,
                $val->total_surveyor,
                $val->max_point,
                $val->total_hit,
                round($val->percent, 2).' %',
            ];
            $numSheet4++;
            $dataSheet4[] = $resultValue; 
        }
        $workSheet4->fromArray($dataSheet4);
        $sheet4 = $spreadsheet->getSheet(3)->setTitle("By Region");
        $sheet4->mergeCells('A2:E2');
        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet4->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet4->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet4->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet4->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet4->mergeCells('B'.$val.':G'.$val);
        }
        $lastColumn         = $sheet4->getHighestColumn();
        $lastRow            = $sheet4->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
        $sheet4->mergeCells('A2:E2');
        $sheet4->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet4->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        // Sheet 5
        $workSheet5 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet5, 4);
        $dataSheet5 = $headerSheet;
        $getQ4 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 4) sfdsv"))->select('sfdsv.*')->get();
        $title5     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        $dataSheet5[] = $title5; 
        $numSheet5 = 1;
        $resultValue = [];
        foreach ($getQ4 as $key => $val) {
            $resultValue = [
                $numSheet5,
                $val->survey_name,
                $val->department,
                $val->regional,
                $val->branch,
                $val->group_survey,
                $val->nomor,
                $val->question_survey,
                $val->essay_answer,
                $val->total_surveyor,
                $val->max_point,
                $val->total_hit,
                round($val->percent, 2).' %',
            ];
            $numSheet5++;
            $dataSheet5[] = $resultValue; 
        }
        $workSheet5->fromArray($dataSheet5);
        $sheet5 = $spreadsheet->getSheet(4)->setTitle("By Group");
        $sheet5->mergeCells('A2:E2');
        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet5->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet5->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet5->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet5->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet5->mergeCells('B'.$val.':G'.$val);
        }
        $lastColumn         = $sheet5->getHighestColumn();
        $lastRow            = $sheet5->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
        $sheet5->mergeCells('A2:E2');
        $sheet5->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet5->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        // Sheet 6
        $workSheet6 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet6, 5);
        $dataSheet6 = $headerSheet;
        $getQ5 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 5) sfdsv"))->select('sfdsv.*')->get();
        $title6     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        $dataSheet6[] = $title6; 
        $numSheet6 = 1;
        $resultValue = [];
        foreach ($getQ5 as $key => $val) {
            $resultValue = [
                $numSheet6,
                $val->survey_name,
                $val->department,
                $val->regional,
                $val->branch,
                $val->group_survey,
                $val->nomor,
                $val->question_survey,
                $val->essay_answer,
                $val->total_surveyor,
                $val->max_point,
                $val->total_hit,
                round($val->percent, 2).' %',
            ];
            $numSheet6++;
            $dataSheet6[] = $resultValue; 
        }
        $workSheet6->fromArray($dataSheet6);
        $sheet6 = $spreadsheet->getSheet(5)->setTitle("By Region per Group");
        $sheet6->mergeCells('A2:E2');
        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet6->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet6->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet6->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet6->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet6->mergeCells('B'.$val.':G'.$val);
        }
        $lastColumn         = $sheet6->getHighestColumn();
        $lastRow            = $sheet6->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
        $sheet6->mergeCells('A2:E2');
        $sheet6->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet6->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        // Sheet 7
        $workSheet7 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet7, 6);
        $dataSheet7 = $headerSheet;
        $getQ6 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 6) sfdsv"))->select('sfdsv.*')->get();
        $title7     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        $dataSheet7[] = $title7; 
        $numSheet7 = 1;
        $resultValue = [];
        foreach ($getQ6 as $key => $val) {
            $resultValue = [
                $numSheet7,
                $val->survey_name,
                $val->department,
                $val->regional,
                $val->branch,
                $val->group_survey,
                $val->nomor,
                $val->question_survey,
                $val->essay_answer,
                $val->total_surveyor,
                $val->max_point,
                $val->total_hit,
                round($val->percent, 2).' %',
            ];
            $numSheet7++;
            $dataSheet7[] = $resultValue; 
        }
        $workSheet7->fromArray($dataSheet7);
        $sheet7 = $spreadsheet->getSheet(6)->setTitle("By Multiple Choice");
        $sheet7->mergeCells('A2:E2');
        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet7->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet7->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet7->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet7->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet7->mergeCells('B'.$val.':G'.$val);
        }
        $lastColumn         = $sheet7->getHighestColumn();
        $lastRow            = $sheet7->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
        $sheet7->mergeCells('A2:E2');
        $sheet7->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet7->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        // Sheet 8
        $workSheet8 = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($workSheet8, 7);
        $dataSheet8 = $headerSheet;
        $getQ7 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 7) sfdsv"))->select('sfdsv.*')->get();
        $title8     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        $dataSheet8[] = $title8; 
        $numSheet8 = 1;
        $resultValue = [];
        foreach ($getQ7 as $key => $val) {
            $resultValue = [
                $numSheet8,
                $val->survey_name,
                $val->department,
                $val->regional,
                $val->branch,
                $val->group_survey,
                $val->nomor,
                $val->question_survey,
                $val->essay_answer,
                $val->total_surveyor,
                $val->max_point,
                $val->total_hit,
                round($val->percent, 2).' %',
            ];
            $numSheet8++;
            $dataSheet8[] = $resultValue; 
        }
        $workSheet8->fromArray($dataSheet8);
        $sheet8 = $spreadsheet->getSheet(7)->setTitle("By Essay");
        $sheet8->mergeCells('A2:E2');
        $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet8->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet8->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }
        $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $sheet8->getStyle($val.':'.$val)->getFont()->setBold(true);
            $sheet8->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
            $sheet8->mergeCells('B'.$val.':G'.$val);
        }
        $lastColumn         = $sheet8->getHighestColumn();
        $lastRow            = $sheet8->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
        $sheet8->mergeCells('A2:E2');
        $sheet8->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet8->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        if(session('access_group') == 'Default_Administrator'){
            // Sheet 9
            $workSheet9 = new Worksheet($spreadsheet);
            $spreadsheet->addSheet($workSheet9, 8);
            $dataSheet9 = $headerSheet;
            $getQ8 = DB::table('hr_survey_answer_user as hsau')
                        ->leftJoin('hr_survey_answer_user_header as hsauh', 'hsau.id_survey_answer_user_header', '=', 'hsauh.id_survey_answer_user_header')
                        ->leftJoin('hr_survey_answer as hsa', 'hsa.id_survey_answer', '=', 'hsau.id_survey_answer')
                        ->leftJoin('master_survey_answer as msa', 'msa.id_answer', '=', 'hsa.id_answer')
                        ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hsauh.id_employee')
                        ->select('hsau.*', 'msa.code as choice_answer', 'hsauh.total_score', 'he.name', 'he.nik_employee')
                        ->where('hsauh.id_survey_header', $id_survey_header)
                        ->where('hsauh.id_company', session('id_company'))
                        ->get();
            $title9     = ['No', 'NIK', 'Name', 'Created At', 'Score'];
            $countTitle9  = count($title9);
            $getQuestion9 = DB::table('hr_survey_question as hsq')
                                ->select('hsq.sequence', 'hsq.id_survey_question', 'hsq.question')->where('hsq.id_survey_header', $id_survey_header)->get();
            $dataSheet9[] = $title9; 
            $numSheet9 = 1;
            $resultValue = [];

            if($getQ8){
                $all_nik        = $getQ8->pluck('nik_employee');
                $all_name       = $getQ8->pluck('name');
                $all_created_at = $getQ8->pluck('creation_date');
                $all_id_question= $getQ8->pluck('id_survey_question')->unique();
                $thisQuestion = [];
                foreach ($getQuestion9 as $key => $val) {
                    $thisQuestion[$val->id_survey_question] = $val->sequence;
                    $title[$countTitle] = $val->sequence;
                    $countTitle++;
                }

                $summary9 = [];
                foreach ($all_nik as $k => $v) {
                    if(!in_array($v, $summary9)){
                        $summary9[$v]['nik_employee'] = @$v;
                        $summary9[$v]['name'] = @$all_name[$k];
                        $summary9[$v]['created_at'] = date("j M Y H:i:s", strtotime(@$all_created_at[$k]));
                        $summary9[$v]['score'] = 0;
                    }
                }

                foreach ($getQ8 as $k => $val) {
                    if($val->nik_employee == $summary9[$val->nik_employee]['nik_employee']){
                        if(is_null($val->id_survey_answer)){
                            $summary9[$val->nik_employee][$thisQuestion[$val->id_survey_question]] = $val->description_answer;
                        } else {
                            $shortcut = @$summary9[$val->nik_employee][$thisQuestion[$val->id_survey_question]];
                            if(!is_null($shortcut)){
                                $merge = $shortcut.', '.$val->choice_answer;
                                $summary9[$val->nik_employee][$thisQuestion[$val->id_survey_question]] = $merge;
                            } else {
                                $summary9[$val->nik_employee][$thisQuestion[$val->id_survey_question]] = $val->choice_answer;
                            }
                        }
                        $summary9[$val->nik_employee]['score'] = ($withScore == 'No') ? '' : $val->total_score;
                    }
                }
                $sort = array_column($summary9, 'name'); // sort result by name
                array_multisort($sort, SORT_ASC, $summary9);

                $number = 1;
                $resultValue = [];
                $countTableContent = 4;
                foreach ($summary9 as $key => $val) {
                    $resultValue = [
                        $number,
                        $val['nik_employee'],
                        $val['name'],
                        $val['created_at'],
                        $val['score'],
                    ];
                    foreach ($getQuestion9 as $k => $item) {
                        $resultValue[] = $val[$item->sequence];
                    }
                    $countTableContent = count($resultValue);
                    $number++;
                    $dataSheet9[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
                }
            }

            $dataSheet9[] = ['']; 

            $workSheet9->fromArray($dataSheet9);
            $sheet9 = $spreadsheet->getSheet(8)->setTitle("By Employee");
            $sheet9->mergeCells('A2:E2');
            $rowStyle = [2,15]; // Identitas Row yang akan di style kan
            foreach ($rowStyle as $k => $val) {
                $sheet9->getStyle($val.':'.$val)->getFont()->setBold(true);
                $sheet9->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
            }
            $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
            foreach ($rowStyle as $k => $val) {
                $sheet9->getStyle($val.':'.$val)->getFont()->setBold(true);
                $sheet9->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
                $sheet9->mergeCells('B'.$val.':G'.$val);
            }
            $lastColumn         = $sheet9->getHighestColumn();
            $lastRow            = $sheet9->getHighestRow();
            $columnAllData      = $lastColumn.$lastRow;
            $sheet9->mergeCells('A2:E2');
            $sheet9->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet9->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');
        }

        $spreadsheet->setActiveSheetIndexByName('By Surveyor'); // utk set sheet yg aktif
        $worksheets = [$workSheet1, $workSheet2, $workSheet3, $workSheet4, $workSheet5, $workSheet6, $workSheet7, $workSheet8]; // array utk kondisi nanti jika butuh bnyk sheet

        if(session('access_group') == 'Default_Administrator'){
            $worksheets[] = $workSheet9;
        }
        
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
