<?php
namespace App\Http\Controllers\Kpi\Rating;

use App\Models\Kpi\Rating\Rating;
use App\Models\Kpi\Rating\MasterRating;
use App\Models\Employee\Employee\Employee;
use App\Models\Kpi\Kpi\QualitativeParticipant;
use App\Models\Kpi\Kpi\QuantitativeKpi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
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

class RatingController extends Controller {	
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
	
    public function index(Request $request) {
        if ($request->ajax()) {
			$period 				= $request->period ?? null;
            $data = Rating::getdata($period);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_kpi_total . '" class="edit btn btn-success" style="padding:1px 4px 1px 4px;" title="Propose Rating"><span style="font-size:16px;" class="fas fa-list-alt"></span></button> ';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.pa_rating.pa_employee_result.index');
    }
	
	public function index_report(Request $request) {
        if ($request->ajax()) {
			$period 				= $request->period ?? null;
			$idEmployeeAppraiser	= $request->id_employee ?? null;
            $data = Rating::getdata_report($period,$this->accessBranch($request), $idEmployeeAppraiser);
            return DataTables::of($data)
                            ->addIndexColumn()                        
                            ->make(true);
        }
        return view('kpi.report_pa.report_all.index_report');
    }
	
	public function calpa(Request $request) {
		DB::beginTransaction();
		try {
		$period = $request->period;	
		$path_url = $request->path();
		if($path_url == 'kpi/pa_rating/pa_employee_result/calpa_id_emp') {
			$request->validate([
				'id_employee' => 'required'
			]);
			$emp = Employee::findOrFail($request->id_employee);
		} else {
			$emp = Employee::where('id_user',session('id_user'))->where('status','A')->first();
		}
		$subkpi = Rating::get_calpa($emp['id_employee'],$period);
		if(count($subkpi) > 0){
			foreach($subkpi as $val){
				$quali = Rating::get_quali($val['id_employee'],$val['year']);		
				$getrating = Rating::where('id_kpi_group',$val['id_kpi_group'])->where('id_employee_participant',$val['id_employee'])->where('id_period',$val['id_period'])->where('status','A')->first();
				if(!$getrating){
					if(count($quali) > 0){
							$total = ((round($quali[0]['total_percent'],2) * 40 ) / 100) + ((round($val['average_prosentase'],2) * 60) / 100);
							$form_data = array(
								'id_employee_participant' => $val['id_employee'],
								'id_period' => $val['id_period'],
								'id_qualitative_participant' =>  $quali[0]['id_qualitative_participant'],
								'total_qualitative_score' => round($quali[0]['total_percent'],2),
								'id_kpi_group' =>  $val['id_kpi_group'],
								'total_quantitative_score' => isset($val['average_prosentase']) ? round($val['average_prosentase'],2) : null,
								'total_kpi_score' => round($total,2),
								'total_maximum_score' => 100,
								'total_percent' => 100,
								'status' => $val['status'],
								'id_company' => $val['id_company'],
								'created_by' => session('id_user'),
							);
							$res =	Rating::create($form_data); 
					}
					else{
						$total_kpi = ((round($val['average_prosentase'],2) * 60) / 100);
						$form_data = array(
							'id_employee_participant' => $val['id_employee'],
							'id_period' => $val['id_period'],
							'id_kpi_group' =>  $val['id_kpi_group'],
							'total_quantitative_score' => isset($val['average_prosentase']) ? round($val['average_prosentase'],2) : null,
							'total_kpi_score' => isset($val['average_prosentase']) ? round($total_kpi,2) : null,
							'total_maximum_score' => 100,
							'total_percent' => 100,
							'status' => $val['status'],
							'id_company' => $val['id_company'],
							'created_by' => session('id_user'),
						);
						$res =	Rating::create($form_data); 
					}
				}
				else{
					if(count($quali) > 0){
							$total = ((round($quali[0]['total_percent'],2) * 40 ) / 100) + ((round($val['average_prosentase'],2) * 60) / 100);
							$res =	Rating::where('id_employee_participant', $val['id_employee'])->where('id_period',$val['id_period'])->update(array(
									'total_qualitative_score' => round($quali[0]['total_percent'],2),
									'total_quantitative_score' => isset($val['average_prosentase']) ? round($val['average_prosentase'],2) : null,
									'total_kpi_score' => round($total,2),
									'id_company' => $val['id_company'],
									'updated_by' => session('id_user'),
								));
					}
					else{
						$total_kpi = ((round($val['average_prosentase'],2) * 60) / 100);
						$res =	Rating::where('id_employee_participant', $val['id_employee'])->where('id_period',$val['id_period'])->update(array(
								'total_quantitative_score' => isset($val['average_prosentase']) ? round($val['average_prosentase'],2) : null,
								'total_kpi_score' => isset($val['average_prosentase']) ? round($total_kpi,2) : null,
								'id_company' => $val['id_company'],
								'updated_by' => session('id_user'),
							));
					}
				}	
			}
		}
		//	dd($x);
         DB::commit();   
            return response(['status' => 'true', 'message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function get_rating_edit(Request $request) {
		$data = [
				'id_kpi_total' => $request->id_kpi_total
			];
        $result = Rating::get_rating_edit($data['id_kpi_total']);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_rating(Request $request) {
		$data = [
				'id_job_grade' => $request->id_job_grade,
				'emp_status' => $request->emp_status,
				'day_join' => $request->day_join,
			];
        $result = Rating::get_rating($data['id_job_grade'],$data['emp_status'],$data['day_join']);
        return response()->json($result);
    }
	
	protected function update(Request $request) {
		try{
			DB::beginTransaction();
			$form_data = array(
				'id_code_promotion' => $request->id_code_promotion,
				'notes' => $request->notes,            
				'updated_by' => session('id_user'),
			);
			
			Rating::where('id_kpi_total', $request->id_kpi_total)->update($form_data);
				
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Propose Rating Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Save Propose Rating !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function get_period() {
        $result = Rating::get_period();
        return response()->json($result);
    }
	
	public function upload_final_rating(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        $allFile    = [];
        $filePath   = 'public/';
        try {
            $dataReturn = [];
            $today      = date('Y-m-d');
            $validator 	= Validator::make($request->all(), [
                'attachment' => 'required|mimes:xls,xlsx'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            if($request->attachment) {
                if ($request->attachment->isValid()) {
                    $fileName       = $request->attachment->getClientOriginalName();
                    $newFileName    = Str::random(3).'_'.$fileName;
                    $storageFile   = Storage::putFileAs($filePath, $request->attachment, $newFileName);
                    $allFile[]      = $newFileName;
                }
            }

            if(count($allFile) > 0){
                foreach ($allFile as $key => $file) {
                    if(Storage::disk('local')->exists($filePath.$file)){
                        $thisExt = pathinfo(storage_path('app/'.$filePath.$file))['extension'];
                        if($thisExt == 'csv'){
                            $reader = new Csv(); 
                        } else if($thisExt == 'xlsx'){
                            $reader = new Xlsx(); 
                        } else {
                            $reader = new Xls(); 
                        }
                        $reader->setReadDataOnly(true);
                        $spreadsheet    	= $reader->load(storage_path('app/'.$filePath.$file));
                        $sheetData      	= $spreadsheet->getActiveSheet()->toArray();
 						
 						$allNikKaryawan		= [];
 						$dataRating			= [];
 						$idPeriod 			= $request->period;

                        foreach ($sheetData as $k => $row) {
                    	   if($k > 0){
                            	$nikKaryawan 		= $row[0] ?? null;
                            	$namaKaryawan 		= $row[1] ?? null;
                            	$rating 			= $row[2] ?? null;

                                if(!is_null($nikKaryawan) && !in_array($nikKaryawan, $allNikKaryawan)){
                                	$allNikKaryawan[] = $nikKaryawan;
                                }

                                if(!is_null($nikKaryawan)){
                                	//utk menampung sementara data dari excel ke dalam array
                                	$dataRating[$nikKaryawan]['rating'] = $rating;
                                }
                            }
                        }

        				// Get Karyawan
        				$getKaryawan = DB::table('hr_employee')
        					->select('id_employee', 'name', 'nik_employee', 'id_company')
        					->where('status', 'A')
        					->whereIn('nik_employee', $allNikKaryawan)
        					->get();

        				//$getKaryawan[BCP000001] = [....]
        				$getKaryawan = $getKaryawan->keyBy(function ($item) {
						    return $item->nik_employee;
						});
        				
        				foreach ($getKaryawan as $nik => $val) {
        					$finalRating = $dataRating[$nik]['rating'];
        					$getKaryawan = DB::table('hr_kpi_total')
	        					->where('id_employee_participant', $val->id_employee)
	        					->where('id_period', $idPeriod);

	        				if($getKaryawan->first()){
	        					$getKaryawan->update(['final_rating' => $finalRating]);
	        				}
        				}

                        Storage::disk('local')->delete($filePath.$file);
                    }
                }
            }
            DB::commit();   
            return response(['status' => 'true', 'message' => 'Upload Success', 'data' => $dataReturn]);
        } catch (\Exception $e) {
            DB::rollBack();
            if(count($allFile) > 0){
                foreach ($allFile as $key => $file) {
                    if(Storage::disk('local')->exists($filePath.$file)){
            			Storage::disk('local')->delete($filePath.$file);
                    }
                }
            }
            return response(['status' => 'false', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

}
