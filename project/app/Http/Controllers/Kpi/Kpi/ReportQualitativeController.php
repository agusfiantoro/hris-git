<?php
namespace App\Http\Controllers\Kpi\Kpi;

use App\Models\Kpi\Kpi\QualitativeParticipant;
use App\Models\Kpi\Kpi\QualitativeAppraiser;
use App\Models\Kpi\Kpi\AppraiserResult;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Employee\EmployeeController;
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

class ReportQualitativeController extends Controller {

	protected function accessBranch(Request $request) {
		$data_access = Employee::get_access($request->id_url);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}

			return $group_branch;
	}
   
   public function index_report(Request $request) {    	
        if ($request->ajax()) {
        	$id_employee 			= $request->id_employee ?? [];
        	$period 				= $request->period ?? null;
            $data = QualitativeParticipant::get_report($id_employee,$period,$this->accessBranch($request));
			
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }
		return view('kpi.report_pa.report_360.index_report');
    }
		
	public function get_employee_filter(Request $request) {
        $result = QualitativeParticipant::get_employee_filter($this->accessBranch($request));
        return response()->json($result);
    }
	
	public function get_period_report() {
        $result = QualitativeParticipant::get_period_report();
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
			'employee' => $request->employee ?? "",
		);
		
        return response()->json($valid);
    }
	
    public function export(Request $request) {
        ini_set('max_execution_time', -1);
        $idPeriod  = $request->period ?? null;
        $employee  = $request->employee ?? null;
        $accessBranch = $this->accessBranch($request);

        $title = ['No', 'Penilai', 'NIK Penilai', 'Tipe PA', 'Peserta', 'NIK Peserta', 'Submitted', 'Score', 'Total Score', 'Final Score (%)'];
        $countTitle = count($title);

        $kpi = DB::table('hr_qualitative_appraisers as hqa')
            ->leftJoin('hr_qualitative_participant as hqp', 'hqp.id_employee_participant', '=', 'hqa.id_employee_participant')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hqa.id_employee_appraisers')
            ->leftJoin('hr_employee as he2', 'he2.id_employee', '=', 'hqa.id_employee_participant')
            ->leftJoin('master_period as mp', 'mp.id_period', '=', 'hqa.id_period')
            ->leftJoin('master_period as mp2', 'mp2.id_period', '=', 'hqp.id_period')
            ->leftJoin('master_position_detail as mpd', 'mpd.id_employee', '=', 'hqa.id_employee_participant')
            ->select('he.name as penilai', 'he.nik_employee as nik_penilai', 'he2.name as peserta', 'he2.nik_employee as nik_peserta', 'hqa.appraisers_hierarchy as tipe', 'hqa.submitted', 'hqa.subtotal_score as score', 'hqp.total_hit_score as total_score', 'hqp.total_percent');

        if($accessBranch){
            if(strpos($accessBranch, ',') !== false) {
                $arrBranch = explode(',', $accessBranch);
            } else {
                $arrBranch = [$accessBranch];
            }
            $kpi->whereIn('mpd.id_branch', $arrBranch);
        }

        if($idPeriod){
            $kpi->where('mp.id_period', $idPeriod)->where('mp2.id_period', $idPeriod);
            $getPeriod =  DB::table('master_period')->where('id_period', $idPeriod)->first();
            $period = $getPeriod->description;
        } else {
            $getPeriod =  DB::table('master_period as mp')->select('mp.description')
			->leftJoin('master_general_data as mgd', 'mp.id_function_type', '=', 'mgd.id_general_data')
			->where('mp.id_company', session('id_company'))->where('mgd.description', 'HR-QUALITATIVE')->where('mp.status', 'A')->get();
            $period = collect($getPeriod->pluck('description')->all())->implode(', ');
        }

        if($employee){
            if(strpos($employee, ',') !== false) {
                $arrEmployee = explode(',', $employee);
            } else {
                $arrEmployee = [$employee];
            }
            $kpi->whereIn('hqp.id_employee_participant', $arrEmployee);
        }

        $kpi->where('hqa.id_company', session('id_company'));
        $kpi->where('hqa.submitted', true);
        $kpi->where('mpd.secondary_position', false);
        $kpi->where('hqa.transaction_type', 'PA');
        $kpi->orderBy('he2.name');
        $kpi->orderBy('hqa.appraisers_hierarchy');
        $getKpi = $kpi->get();

        if(count($getKpi) < 1){
            echo "<script>alert('Data not found');window.close();</script>";
            return false;
        }

        $dataSheet = [
            [''],
            ['Report 360'],
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
            if(!in_array($val->nik_peserta, $nikPeserta)){
                $nikPeserta[] = $val->nik_peserta;
                if(count($mergeByNik) > 0){
                    $lastNikInMerge = $nikPeserta[count($nikPeserta)-2];
                    $mergeByNik[$lastNikInMerge][] = $k-1;
                    $mergeByNik[$val->nik_peserta][] = $k;
                } else {
                    $mergeByNik[$val->nik_peserta][] = $k;
                }
            }
            if($k == count($getKpi)-1){
                if($val->nik_peserta == $nikPeserta[count($nikPeserta)-1]){
                    $mergeByNik[$val->nik_peserta][] = $k;
                }
            }

            $resultValue = [
                $number,
                $val->penilai,
                $val->nik_penilai,
                $val->tipe == 'direct' ? 'Direct Line' : ucwords($val->tipe),
                $val->peserta,
                $val->nik_peserta,
                $val->submitted == true ? 'Yes' : 'No',
                $val->score,
                $val->total_score,
                $val->total_percent,
            ];
            $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
            $number++;
        }

        $exportType = 'excel';
        $filename   = 'Report_360_'.date('Y-m-d');
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
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $activeSheet = $spreadsheet->getActiveSheet();

        foreach ([1,2,3,4] as $k => $val) {
            $activeSheet->mergeCells('A'.$val.':J'.$val);
        }

        $rowStyle = [5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
            $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
            $activeSheet->getStyle('A'.$val.':J'.$val)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fdff3b');
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
        $activeSheet->getStyle('J'.$rowTitle.':J'.$lastRow)->getFont()->setBold(true);
        $activeSheet->getStyle('J'.$rowTitle.':J'.$lastRow)->getFont()->setSize(13);
        $activeSheet->getStyle('J'.$rowTitle.':J'.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('J'.$rowTitle.':J'.$lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fdff3b');
        $activeSheet->getStyle('A1:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
		$activeSheet->getStyle('C6:F'.$lastRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        foreach ($mergeByNik as $key => $item) {
            $rowMergeAwal = $item[0]+(1+$rowTitle);
            $rowMergeAkhir = $item[1]+(1+$rowTitle);
            $columnTotalScore = 'I'.$rowMergeAwal.':I'.$rowMergeAkhir;
            $columnFinalScore = 'J'.$rowMergeAwal.':J'.$rowMergeAkhir;

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
	
	public function export_detail(Request $request) {
        ini_set('max_execution_time', -1);
        $idPeriod  = $request->period ?? null;
        $employee  = $request->employee ?? null;
        $accessBranch = $this->accessBranch($request);

        $title = ['No', 'Penilai', 'NIK Penilai', 'Tipe PA', 'Peserta', 'NIK Peserta', 'Soal', 'Level 5', 'Level 4', 'Level 3', 'Level 2', 'Level 1', 'Bukti Perilaku', 'Meet or Unmeet', 'Score'];
        $countTitle = count($title);
		
		if($idPeriod){
			$query_period = "AND hqa.id_period = ".$idPeriod;
			$getPeriod =  DB::table('master_period')->where('id_period', $idPeriod)->first();
            $period = $getPeriod->description;
		}
		else{
			$query_period = "";
			$getPeriod =  DB::table('master_period as mp')->select('mp.description')
			->leftJoin('master_general_data as mgd', 'mp.id_function_type', '=', 'mgd.id_general_data')
			->where('mp.id_company', session('id_company'))->where('mgd.description', 'HR-QUALITATIVE')->where('mp.status', 'A')->get();
            $period = collect($getPeriod->pluck('description')->all())->implode(', ');
		}
		if($employee){
            if(strpos($employee, ',') !== false) {
                $arrEmployee = explode(',', $employee);
            } else {
                $arrEmployee = [$employee];
            }
			$impEmp = implode(",",$arrEmployee);
         //   $res = $get_res->whereIn('id_employee_participant', $arrEmployee);
			$query_emp = "AND hqa.id_employee_participant IN(".$impEmp.")";
        }
		else{
			$query_emp = "";	
		}
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
				WHERE har.id_company = ".session('id_company')." ".$query_period." ".$query_emp."
				ORDER BY he2.name ASC, he.name ASC, hpq.id_pa_question ASC";
		$get = DB::select($kpi);
		$get_res = collect($get);
        if($accessBranch){
            if(strpos($accessBranch, ',') !== false) {
                $arrBranch = explode(',', $accessBranch);
            } else {
                $arrBranch = [$accessBranch];
            }
          $res =  $get_res->whereIn('id_branch', $arrBranch);
		  $getKpi = $res;
        }
		else{
		 $getKpi = $get_res;
		}

        if(count($getKpi) < 1){
            echo "<script>alert('Data not found');window.close();</script>";
            return false;
        }

        $dataSheet = [
            [''],
            ['Report Detail 360'],
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

            if($employee){
                $getLastArray = array_key_last($getKpi->toArray());
            } else {
                $getLastArray = count($getKpi)-1;
            }
            if($k == $getLastArray){
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
        $filename   = 'Report_Detail_360_'.date('Y-m-d');
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
