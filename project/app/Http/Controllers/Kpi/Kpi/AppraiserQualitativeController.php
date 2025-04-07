<?php
namespace App\Http\Controllers\Kpi\Kpi;

use App\Models\Kpi\Kpi\QualitativeParticipant;
use App\Models\Kpi\Kpi\QualitativeAppraiser;
use App\Models\Kpi\Kpi\AppraiserResult;
use App\Models\Kpi\KpiSetting\PaGrade;
use App\Models\Kpi\KpiSetting\PaAnswer;
use App\Models\Kpi\KpiSetting\PaWeight;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AppraiserQualitativeController extends Controller {

     public function index(Request $request) {
        if ($request->ajax()) {
			$period 				= $request->period ?? null;
            $data = QualitativeAppraiser::getdata($period);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_qualitative_appraisers . '" class="edit btn btn-success" style="padding:1px 4px 1px 4px;" title="Assessment"><span style="font-size:16px;" class="fas fa-list-alt"></span></button> ';
							/*	
								$button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_employee_participant . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
							*/	
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.360_feedback.pa_qualitative.index');
    }
	public function review(Request $request) {
		$period_code = $request->period_code;
        return view('kpi.360_feedback.pa_qualitative.review', compact('period_code'));
    }
	public function list_review(Request $request) {    	
        if ($request->ajax()) {       
            $data = QualitativeAppraiser::get_review($request->period_code);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data) {
						$button = '<button type="button" name="'.$data->name.'" id="' . $data->id_employee_participant . '" class="view_level btn btn-success btn-sm" title="List Review"><span class="fa fa-list-alt"></span></button> ';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function get_detail_review(Request $request) {
		if ($request->ajax()) {       
			$data = [
				'period_code' => $request->period_code,
				'id_employee_participant' => $request->id_employee_participant,
			];
            $result = QualitativeAppraiser::get_detail_review($data);
            return DataTables::of($result)
                    ->addIndexColumn()
					->addColumn('soal', function($data) {
						return strip_tags($data->soal);
					})
					->addColumn('avg', function($data) {
						return round($data->avg,2);
					})
                    ->make(true);
        }
	}
	protected function update(Request $request) {	
		try{
			DB::beginTransaction();
			$grade = QualitativeAppraiser::get_grade($request->id_employee_participant);
		//	dd($grade->id_job_grade);
			if($request->soal){
				$countAnswer = [];
				$r = 0;
				$appraiser = QualitativeAppraiser::where('id_qualitative_appraisers', $request->id_qualitative_appraisers)->first();
				$paweight = PaWeight::where('appraisers_hierarchy', $appraiser['appraisers_hierarchy'])->first();
				foreach ($request->soal as $key => $id_question) {
					$no = $key+1;
					$answer = 'answers_'.$id_question;
					$ansnote = 'ans_'.$id_question;
				//	$form_data =[];
					 if(!$request->$answer){
						$countAnswer[] = 'Soal No. '.$no.' Belum Dijawab';
					 }					 
					 else{
						 foreach ($request->$answer as $i => $id_answer) {
							$ex_val = $id_answer;
						//	$ex_val =  explode('|',$id_answer)[0];
						//	$ex_level =  explode('|',$id_answer)[1];
						//	$x[] = strlen($request->$ansnote);
						//	if($ex_level == 'Level 5' || $ex_level == 'Level 1'){								
						//	if($ex_level == 'Level 5' || $ex_level == 'Level 1'){								
								 if(!$request->$ansnote){
									 $countAnswer[] = 'Soal No. '.$no.' Belum Mengisi Bukti Perilaku';
								 }
								 else if(strlen($request->$ansnote) < 30){
									 $countAnswer[] = 'Soal No. '.$no.' Bukti Perilaku (Jumlah Karakter Kurang Dari 30)';
								 }
						//	}
						//	}
						
							$pagrade = PaGrade::where('id_job_grade', $grade->id_job_grade)->where('id_pa_question', $id_question)->first();
							$paanswer = PaAnswer::where('id_pa_answer', $ex_val)->first();
							
								$form_data = array(
									'id_qualitative_appraisers' => $request->id_qualitative_appraisers,
									'id_pa_question' => $id_question,
									'id_pa_answer' => $ex_val,
									'description_answer' => $request->$ansnote,
									'total_hit_score' => $paanswer['weight_score'],
									'status' => $appraiser['status'],
									'id_company' => $appraiser['id_company'],
								//	'created_by' => session('id_user'),
								);
																
								if($paanswer['weight_score'] >= $pagrade['value']){
									$form_data['total_maximum_score'] = 1;								
								}
								else{
									$form_data['total_maximum_score'] = 0;
								}
								
								$resapp = AppraiserResult::where('id_qualitative_appraisers', $request->id_qualitative_appraisers)->where('id_pa_question',$id_question)->get();
								if($resapp->count() == 0){
									$form_data['created_by'] = session('id_user');
									$res =	AppraiserResult::create($form_data);  
								}
								else{
									$form_data['updated_by'] = session('id_user');
									$res =	AppraiserResult::where('id_qualitative_appraisers',$request->id_qualitative_appraisers)->where('id_pa_question',$id_question)->update($form_data); 
								}
								
								$r += $form_data['total_maximum_score'];
						 }
					 }
					 
				}

				$score_appraiser = ($r / count($request->soal)) * $paweight['weight_value'];				
				$update_appraiser = QualitativeAppraiser::where('id_qualitative_appraisers', $request->id_qualitative_appraisers)->where('status', 'A')->where('transaction_type', 'PA')->update(array(
						'submitted' => 1,
						'subtotal_score' => round($score_appraiser,2),
						'maximum_score' => $paweight['weight_value'],
				));
				$get_appraiser = QualitativeAppraiser::where('id_employee_participant', $request->id_employee_participant)->where('id_period', $appraiser['id_period'])->where('status', 'A')->where('transaction_type', 'PA')->get();
				$sub_score = array_sum($get_appraiser->pluck('subtotal_score')->all());				
				$max_score =  array_sum($get_appraiser->pluck('maximum_score')->all());
								
				$update_employee = QualitativeParticipant::where('id_employee_participant', $request->id_employee_participant)->where('id_period', $appraiser['id_period'])->where('status', 'A')->where('transaction_type', 'PA')->update(array(
						'total_hit_score' => round($sub_score,2),
						'total_maximum_score' => round($max_score,2),
						'total_percent' => round(($sub_score / $max_score)*100,2),
				));
			//	dd($x);
				if(count($countAnswer)){
					$showerr = collect($countAnswer)->implode("\n");
                    throw new \Exception($showerr);
                }
			}
			DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Penilaian telah sukses dilakukan']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }
	
	public function get_appraiser_edit(Request $request) {
		$appraiser = QualitativeAppraiser::where('id_qualitative_appraisers', $request->id_qualitative_appraisers)->first();
		$resapp = AppraiserResult::where('id_qualitative_appraisers', $request->id_qualitative_appraisers)->get();
		if($appraiser->submitted != true ){
			$emp = Employee::where('id_employee', $appraiser->id_employee_participant)->first();
			$data = [
				'id_qualitative_appraisers' => $request->id_qualitative_appraisers,
				'id_company' => $appraiser->id_company,
			];
			$master = QualitativeAppraiser::get_appraiser_new($data);
			if($resapp->count() == 0){
				$answer = [];
			}
			else{
				$answer = QualitativeAppraiser::get_appraiser_edit($data);
			}
			foreach($master as $key=>$val){
				$master[$key]['id_employee'] = $emp['id_employee'];
				$master[$key]['emp_name'] = $emp['name'];
			}
			$result['master'] = $master;
			$result['answers'] = $answer;
		
			return response()->json(['status' => 'true', 'result' => $result]);
		}
		else{
			return response()->json(['status' => 'false', 'message' => 'Penilaian telah dilakukan']);
		}       
    }
	
	public function get_employee() {
        $result = QualitativeAppraiser::get_employee();
        return response()->json($result);
    }
	
	public function get_period() {
        $result = QualitativeAppraiser::get_period();
        return response()->json($result);
    }
	
	public function export_validate(Request $request) {
        $request->validate([
            'period' => 'required',
                ], [],
                [
                    'period' => 'Period',
        ]);
		$valid = array(
			'period' => $request->period,
		);		
        return response()->json($valid);
    }
	
	public function export_detail(Request $request) {
        ini_set('max_execution_time', -1);
        $idPeriod  = $request->period ?? null;

        $title = ['No', 'Penilai', 'NIK Penilai', 'Tipe PA', 'Peserta', 'NIK Peserta', 'Soal', 'Level 5', 'Level 4', 'Level 3', 'Level 2', 'Level 1', 'Bukti Perilaku', 'Meet or Unmeet', 'Score'];
        $countTitle = count($title);

        $kpi = "SELECT he.name AS penilai, he.nik_employee AS nik_penilai, hqa.appraisers_hierarchy AS tipe, hqa.id_period,
				he2.name AS peserta, he2.nik_employee AS nik_peserta, hqa.id_employee_participant,  hpq.description as soal,
					CASE WHEN  hpa.sequence = 'Level 5' THEN 'Yes' END AS Level_5,
					CASE WHEN  hpa.sequence = 'Level 4' THEN 'Yes' END AS Level_4,
					CASE WHEN  hpa.sequence = 'Level 3' THEN 'Yes' END AS Level_3,
					CASE WHEN  hpa.sequence = 'Level 2' THEN 'Yes' END AS Level_2,
					CASE WHEN  hpa.sequence = 'Level 1' THEN 'Yes' END AS Level_1,
				har.description_answer, 
					CASE 
						WHEN  har.total_maximum_score = 1 THEN 'Meet'
						ELSE 'Unmeet'
					END AS meet_unmeet, 
				hqa.subtotal_score, mpd.id_branch, hqa.id_company, CONCAT(he.nik_employee,he2.nik_employee) AS join_nik
				FROM hr_appraisers_result har 
				LEFT JOIN hr_pa_question hpq 
				ON har.id_pa_question = hpq.id_pa_question
				LEFT JOIN hr_pa_answer hpa
				ON har.id_pa_answer = hpa.id_pa_answer
				LEFT JOIN hr_qualitative_appraisers hqa 
				ON har.id_qualitative_appraisers = hqa.id_qualitative_appraisers
				LEFT JOIN hr_employee he 
				ON hqa.id_employee_appraisers = he.id_employee
				LEFT JOIN hr_employee he2 
				ON hqa.id_employee_participant = he2.id_employee
				LEFT JOIN master_position_detail mpd
				ON hqa.id_employee_participant = mpd.id_employee
				WHERE hqa.transaction_type = 'PA' AND har.id_company = ".session('id_company')." AND hqa.id_employee_participant IN(
					SELECT hqa.id_employee_participant  
					FROM hr_qualitative_appraisers hqa
					LEFT JOIN hr_employee he
					ON hqa.id_employee_appraisers = he.id_employee
					WHERE he.id_user = ".session('id_user')." AND hqa.appraisers_hierarchy = 'direct')
				ORDER BY he2.name ASC, he.name ASC, hpq.id_pa_question ASC";
		$get = DB::select($kpi);
		$get_res = collect($get);
        if($idPeriod){
			$res = $get_res->where('period_code', $idPeriod);
            $getPeriod =  DB::table('master_period')->where('period_code', $idPeriod)->first();
            $period = $getPeriod->description;
        } else {
            $getPeriod =  DB::table('master_period as mp')->select('mp.description')
			->leftJoin('master_general_data as mgd', 'mp.id_function_type', '=', 'mgd.id_general_data')
			->where('mp.id_company', session('id_company'))->where('mgd.description', 'HR-QUALITATIVE')->where('mp.status', 'A')->get();
            $period = collect($getPeriod->pluck('description')->all())->implode(', ');
        }
		$res = $get_res->where('id_company', session('id_company'));
        $getKpi = $res;
        if(count($getKpi) < 1){
            echo "<script>alert('Data not found');window.close();</script>";
            return false;
        }

        $dataSheet = [
            [''],
            ['Report Detail Subordinat 360'],
            ['Period : '.$period],
            [''],
        ];

        $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel
        $rowTitle = count($dataSheet);
        $rows = [];
        $number = 1;
        $var = '';
        $nikPeserta = [];
        $mergeByNik = [];
        foreach ($getKpi as $k => $val) {
            if(!in_array($val->join_nik, $nikPeserta)){
                $nikPeserta[] = $val->join_nik;
                if(count($mergeByNik) > 0){
                    $lastNikInMerge = $nikPeserta[count($nikPeserta)-2];
                    $mergeByNik[$lastNikInMerge][] = $k-1;
                    $mergeByNik[$val->join_nik][] = $k;
                } else {
                    $mergeByNik[$val->join_nik][] = $k;
                }
            }
            if($k == count($getKpi)-1){
                if($val->join_nik == $nikPeserta[count($nikPeserta)-1]){
                    $mergeByNik[$val->join_nik][] = $k;
                }
            }

            $resultValue = [
                $number,
                $val->penilai,
                $val->nik_penilai,
                $val->tipe == 'direct' ? 'Direct Line' : ucwords($val->tipe),
                $val->peserta,
                $val->nik_peserta,
				strip_tags($val->soal),
                $val->level_5,
                $val->level_4,
                $val->level_3,
                $val->level_2,
                $val->level_1,
                $val->description_answer,
                $val->meet_unmeet,
                $val->subtotal_score,
            ];
            $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
            $number++;
        }

        $exportType = 'excel';
        $filename   = 'Report_Detail_Subordinat_360_'.date('Y-m-d');
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        $sheetName1 = 'Sheet 1';
        $workSheet1 = new Worksheet($spreadsheet, $sheetName1);
        $spreadsheet->addSheet($workSheet1, 0);
        $spreadsheet->setActiveSheetIndexByName($sheetName1); // utk set sheet yg aktif

        $workSheet1->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2
        $worksheets = [$workSheet1]; 
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
				foreach (range('A', 'F') as $col1) {            
					$worksheet->getColumnDimension($col1)->setAutoSize(true);
				}
				$worksheet->getColumnDimension('N')->setAutoSize(true);
                $worksheet->getColumnDimension('M')->setWidth(50);
                $worksheet->getColumnDimension('G')->setWidth(80);
            }
        }
	
        $activeSheet = $spreadsheet->getActiveSheet();

        foreach ([1,2,3,4] as $k => $val) {
            $activeSheet->mergeCells('A'.$val.':O'.$val);
        }

        $rowStyle = [5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
            $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
            $activeSheet->getStyle('A'.$val.':O'.$val)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fdff3b');
        }

        // styling manual berdasar cell
        $lastColumn         = $activeSheet->getHighestColumn();
        $lastRow            = $activeSheet->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;

        $activeSheet->getStyle('B2:B'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('C2:B'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('D2:B'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('E2:B'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('F2:B'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('G2:B'.$lastRow)->getAlignment()->setHorizontal('right');
        $activeSheet->getStyle('H2:B'.$lastRow)->getAlignment()->setHorizontal('right');
        $activeSheet->getStyle('I2:B'.$lastRow)->getAlignment()->setHorizontal('right');
        $activeSheet->getStyle('M'.$rowTitle.':M'.$lastRow)->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('G'.$rowTitle.':G'.$lastRow)->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('O'.$rowTitle.':O'.$lastRow)->getFont()->setBold(true);
        $activeSheet->getStyle('H'.$rowTitle.':L'.$lastRow)->getFont()->setBold(true);
        $activeSheet->getStyle('O'.$rowTitle.':O'.$lastRow)->getFont()->setSize(13);
        $activeSheet->getStyle('O'.$rowTitle.':O'.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('M'.$rowTitle.':M'.$lastRow)->getAlignment()->setVertical('center');
        $activeSheet->getStyle('A'.$rowTitle.':F'.$lastRow)->getAlignment()->setVertical('center');
        $activeSheet->getStyle('G'.$rowTitle.':G'.$lastRow)->getAlignment()->setVertical('center');
        $activeSheet->getStyle('H'.$rowTitle.':L'.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('H'.$rowTitle.':L'.$lastRow)->getAlignment()->setVertical('center');
		$activeSheet->getStyle('N'.$rowTitle.':N'.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('N'.$rowTitle.':N'.$lastRow)->getAlignment()->setVertical('center');
		$activeSheet->getStyle('A'.$rowTitle.':A'.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('A'.$rowTitle.':A'.$lastRow)->getAlignment()->setVertical('center');
        $activeSheet->getStyle('O'.$rowTitle.':O'.$lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fdff3b');
        $activeSheet->getStyle('A1:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
		$activeSheet->getStyle('C6:F'.$lastRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        foreach ($mergeByNik as $key => $item) {
            $rowMergeAwal = $item[0]+(1+$rowTitle);
            $rowMergeAkhir = $item[1]+(1+$rowTitle);
            $columnTotalScore = 'D'.$rowMergeAwal.':D'.$rowMergeAkhir;
            $columnFinalScore = 'O'.$rowMergeAwal.':O'.$rowMergeAkhir;

            $activeSheet->mergeCells($columnTotalScore);
            $kolomTotal = $activeSheet->getStyle($columnTotalScore);
            $kolomTotal->getAlignment()->setHorizontal('center');
            $kolomTotal->getAlignment()->setVertical('center');

            $activeSheet->mergeCells($columnFinalScore);
            $kolomFinal = $activeSheet->getStyle($columnFinalScore);
            $kolomFinal->getAlignment()->setHorizontal('center');
            $kolomFinal->getAlignment()->setVertical('center');
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
