<?php
namespace App\Http\Controllers\Kpi\Kpi;

use App\Models\Kpi\Kpi\QuantitativeKpi;
use App\Models\Kpi\Kpi\KpiHeader;
use App\Models\Kpi\Kpi\KpiDetail;
use App\Models\Kpi\KpiSetting\PaWeight;
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

class ReportKpiController extends Controller {

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
            $data = QuantitativeKpi::get_report($id_employee,$period,$this->accessBranch($request));
			
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }
		return view('kpi.report_pa.report_kpi.index_report');
    }
	
	public function get_employee_filter(Request $request) {
        $result = QuantitativeKpi::get_employee_filter($this->accessBranch($request));
        return response()->json($result);
    }
	
	public function get_period_report() {
        $result = QuantitativeKpi::get_period_report();
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
		
        $getkpi =  QuantitativeKpi::get_report_excel();
        if($idPeriod){
            $kpi = $getkpi->where('id_period', $idPeriod);
            $getPeriod =  DB::table('master_period')->where('id_period', $idPeriod)->first();
            $period = $getPeriod->description;
        } else {
            $getPeriod =  DB::table('master_period as mp')->select('mp.description')
			->leftJoin('master_general_data as mgd', 'mp.id_function_type', '=', 'mgd.id_general_data')
			->where('mp.id_company', session('id_company'))->where('mgd.description', 'HR-QUANTITATIVE')->where('mp.status', 'A')->get();
            $period = collect($getPeriod->pluck('description')->all())->implode(', ');
        }

        if($employee){
            if(strpos($employee, ',') !== false) {
                $arrEmployee = explode(',', $employee);
            } else {
                $arrEmployee = [$employee];
            }
           $kpi = $getkpi->whereIn('id_employee', $arrEmployee);
        }
				
        if(count($kpi) < 1){
            echo "<script>alert('Data not found');window.close();</script>";
            return false;
        }
		
        $dataSheet = [
            [''],
            ['Report KPI'],
            ['Period : '.$period],
            [''],
			['No', 'Atasan', 'NIK Atasan', 'Dinilai', 'NIK Dinilai','Score Total Monthly (%)', '', '', '', '','', '', '', '', '', '', '', ''],
			['', '', '', '', '','Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],			
        ];

        $rows = [];
        $number = 1;
        $var = '';
    
        foreach ($kpi as $k => $val) {
            $resultValue = [
                $number,
                $val->atasan,
                $val->nik_atasan,
                $val->dinilai,
                $val->nik_dinilai,
                $val->jan,
                $val->feb,
                $val->mar,
                $val->apr,
                $val->mei,
                $val->jun,
                $val->jul,
                $val->ags,
                $val->sep,
                $val->okt,
                $val->nov,
                $val->des,
                $val->average_prosentase,
            ];
            $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
            $number++;
        }

        $exportType = 'excel';
        $filename   = 'Report_KPI_'.date('Y-m-d');
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
            $activeSheet->mergeCells('A'.$val.':R'.$val);
        }
		
        $rowStyle = [5,6]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
            $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
            $activeSheet->getStyle('A'.$val.':R'.$val)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fdff3b');
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
        $activeSheet->getStyle('F7:R'.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('F7:R'.$lastRow)->getAlignment()->setVertical('center');
		$activeSheet->getStyle('A1:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
		$activeSheet->getStyle('A1:R'.$lastRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
		$activeSheet->getStyle('R5:R'.$lastRow)->getFont()->setBold(true);
        $activeSheet->getCell('R5')->setValue("Score Total\nYearly (%)");
        $activeSheet->getStyle('R5')->getAlignment()->setWrapText(true);
		$activeSheet->mergeCells('A5:A6');
		$activeSheet->mergeCells('B5:B6');
		$activeSheet->mergeCells('C5:C6');
		$activeSheet->mergeCells('D5:D6');
		$activeSheet->mergeCells('E5:E6');
		$activeSheet->mergeCells('F5:Q5');
		$activeSheet->mergeCells('R5:R6');
		$kolomTotal = $activeSheet->getStyle('A5:R6');
		$kolomTotal->getAlignment()->setHorizontal('center');
		$kolomTotal->getAlignment()->setVertical('center');

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

	public function list_report(Request $request) {    	
        if ($request->ajax()) {
        	$id_employee 			= $request->id_employee ?? [];
        	$period 				= $request->period ?? null;
        	
            $data = QualitativeAppraiser::get_report($id_employee,$period);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }
    }
}
