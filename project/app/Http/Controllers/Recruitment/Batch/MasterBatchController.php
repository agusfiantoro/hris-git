<?php
namespace App\Http\Controllers\Recruitment\Batch;

use App\Models\Recruitment\Batch\MasterBatch;
use App\Models\Recruitment\Batch\BatchParticipant;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use App\Models\Recruitment\Candidate\CandidateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
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
use ZipArchive;

class MasterBatchController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
			$data_access = Employee::get_access($request->id_url);
		//	dd($data_access);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}
            $data = MasterBatch::getdata($group_branch);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
							 ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_batch . '" class="edit btn btn-primary btn-sm" title="Edit" value="_new"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_batch . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('recruitment.psychotest.master_batch.index');
    }
	
	public function review(Request $request) {
        return view('recruitment.psychotest.master_batch.review');
    }
	
	public function list_review(Request $request) {  
		ini_set('max_execution_time', -1);  	
        if ($request->ajax()) {
			$serverCareer = new \App\Http\Controllers\Recruitment\Psychotest\PsychotestController();
			$serverCareer->getValidServerCareer();
			$serverCareer = $serverCareer->servercareer;
			$data_access = Employee::get_access($request->id_url);
		//	dd($data_access);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}
            $data = MasterBatch::get_review($group_branch,$request->fil_type);
			// dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('psycho_result', function($data) use($serverCareer) {
						$args = [
							"id_batch" => $data->id_batch, 
							"source_type" => $data->source_type, 
							"id_batch_participant" => $data->id_batch_participant
						];
						$result = (Array)$data;
						if($data->source_type == 'External') {
							$result['disc_link'] = $serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$result["id_candidate"].'&id_batch='.$data->id_batch.'&test=disc';
							$result['papi_link'] = $serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$result["id_candidate"].'&id_batch='.$data->id_batch.'&test=papi';
							$result['kraepelin_link'] = $serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$result["id_candidate"].'&id_batch='.$data->id_batch.'&test=kraepelin';
							$result['bct_link'] = $serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$result["id_candidate"].'&id_batch='.$data->id_batch.'&test=bct';
						} else {
							$result['disc_link'] = $serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$result["id_employee"].'&id_batch='.$data->id_batch.'&test=disc';
							$result['papi_link'] = $serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$result["id_employee"].'&id_batch='.$data->id_batch.'&test=papi';
							$result['kraepelin_link'] = $serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$result["id_employee"].'&id_batch='.$data->id_batch.'&test=kraepelin';
							$result['bct_link'] = $serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$result["id_employee"].'&id_batch='.$data->id_batch.'&test=bct';
						}
						return $result;
					})
                    ->make(true);
        }
    }
	
	protected function validateBatch(Request $request, $isUpdate = false) {
		
		$validatorParticipant = $isUpdate ? '' : 'required|distinct';
        $arr_form_validate = [
			'batch_code' => 'unique:psycho_master_batch', Rule::unique('psycho_master_batch')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'batch_name' => 'required',
            'location' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',
			'participant.*.source_type' => 'required',
			'participant.*.id_candidate_emp' => $validatorParticipant,
        ];
	
        $arr_msg_form_validate = [
            'batch_name.required' => 'The Batch Name field is required',
            'location.required' => 'The Location field is required',
            'start_date.required' => 'The Start Date field is required',
            'end_date.required' => 'The End Date field is required',
			'participant.*.source_type.required' => 'The Source Type field is required',
			'participant.*.id_candidate_emp.required' => 'The Participant field is required',
			'participant.*.id_candidate_emp.distinct' => 'Participant :arrpos has a duplicate value',
        ];

		$hasNewData = false;
		$i = 0;
		if(isset($request->participant)){
			foreach($request->participant as $participant) {
				if($participant['status'] == "A") {
					foreach($request->participant as $participant2) {
						if($participant2['status'] == "A" && $participant['id_batch_participant'] != $participant2['id_batch_participant']) {
							if($participant2['id_candidate_emp'] == $participant['id_candidate_emp']) {
								$arr_form_validate['participant.*.id_candidate_emp'] = 'required|distinct';
							}
						}
					}
				}
				if(!$participant['id_batch_participant']) {
					$hasNewData = true;
				}
				if($hasNewData) {
					$arr_form_validate += ['participant.'.$i.'.id_candidate_emp' => 'distinct'];
				}
				$hasNewData = false;
				$i++;
			}
		}
		// dd($arr_form_validate);
		$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
		// dd($request->all());
		$this->validateBatch($request);
		try{
			DB::beginTransaction();
			$kode = MasterBatch::getkode($request->id_branch);
			$form_data = array(
				'batch_code' => $kode,
				'batch_name' => $request->batch_name,
				'location' => $request->location,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'id_region' => $request->id_region,
				'id_branch' => $request->id_branch,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
			
			$BatchSave = MasterBatch::create($form_data);
			
			if(isset($request->participant)){
				foreach ($request->participant as $key => $value) {
					$form_batch = array(
						'id_batch' => $BatchSave->id_batch,
						'source_type' => $value['source_type'],
						'status' => $value['status'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					if($value['source_type'] == 'Internal'){
						$form_batch['id_employee'] = $value['id_candidate_emp'];
						$form_batch['id_position_routing'] = $value['id_position_routing'];
						if(count(self::getEmployeeBatch($value['id_candidate_emp'])) > 0) {
							throw new \Exception("Cannot Save Master Batch !! [Employee no ".($key+1)." is already in another batch]");
						}
					}
					else if($value['source_type'] == 'External'){
						$form_batch['id_candidate'] = $value['id_candidate_emp'];
						if(count(self::getCandidateBatch($value['id_candidate_emp'])) > 0) {
							throw new \Exception("Cannot Save Master Batch !! [Candidate no ".($key+1)." is already in another batch]");
						}
					}
					
					BatchParticipant::create($form_batch);
				}
			}

				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Master Batch Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Master Batch !! [' . $e->getMessage() . ']']);           
		}	
	}
		
	protected function update(Request $request) {
	//	dd($request->all());
		$this->validateBatch($request, true);
		try{
			DB::beginTransaction();
			$form_data = array(
				'batch_name' => $request->batch_name,
				'location' => $request->location,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'id_region' => $request->id_region,
				'id_branch' => $request->id_branch,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			);
			
			$BatchUpdate = MasterBatch::findOrFail($request->id_batch)->update($form_data);
			
			$listIdBatch    = [];
			$idBatch      = [];
			if(BatchParticipant::where('id_batch', $request->id_batch)->first() != null){
				$listIdBatch = BatchParticipant::where('id_batch', $request->id_batch)->where('id_company', session('id_company'))->get()->pluck('id_batch_participant')->all();
			}

			if($request->participant) {
				foreach ($request->participant as $key => $value) {					
					if ($value['id_batch_participant'] == "") {						
						$form_batch = array(
							'id_batch' => $request->id_batch,
							'source_type' => $value['source_type'],
							'status' => $value['status'],
							'id_company' => session('id_company'),
							'created_by' => session('id_user'),
						);
						
						if($value['source_type'] == 'Internal'){
							$form_batch['id_employee'] = $value['id_candidate_emp'];
							$form_batch['id_position_routing'] = $value['id_position_routing'];
							if(count(self::getEmployeeBatch($value['id_candidate_emp'], $request->id_batch)) > 0) {
								throw new \Exception("Cannot Save Master Batch !! [Employee no ".($key+1)." is already in another batch]");
							}
						}
						else if($value['source_type'] == 'External'){
							$form_batch['id_candidate'] = $value['id_candidate_emp'];
							if(count(self::getCandidateBatch($value['id_candidate_emp'], $request->id_batch)) > 0) {
								throw new \Exception("Cannot Save Master Batch !! [Candidate no ".($key+1)." is already in another batch]");
							}
						}
						
						BatchParticipant::create($form_batch);
					}
					 else {
						$idBatch[] = $value['id_batch_participant'];
						$form_batch = array(
							'source_type' => $value['source_type'],
							'status' => $value['status'],
							'id_company' => session('id_company'),
							'updated_by' => session('id_user'),
						);						
						if($value['source_type'] == 'Internal'){
							$form_batch['id_employee'] = $value['id_candidate_emp'];
							$form_batch['id_position_routing'] = $value['id_position_routing'];
							if(count(self::getEmployeeBatch($value['id_candidate_emp'], $request->id_batch)) > 0) {
								throw new \Exception("Cannot Save Master Batch !! [Employee no ".($key+1)." is already in another batch]");
							}
						}
						else if($value['source_type'] == 'External'){
							$form_batch['id_candidate'] = $value['id_candidate_emp'];
							if(count(self::getCandidateBatch($value['id_candidate_emp'], $request->id_batch)) > 0) {
								throw new \Exception("Cannot Save Master Batch !! [Candidate no ".($key+1)." is already in another batch]");
							}
						}
						
						BatchParticipant::where('id_batch_participant', $value['id_batch_participant'])->update($form_batch);
					}
				}
			}	
			
			$diff = array_diff($listIdBatch, $idBatch);
			if(count($diff) > 0){
				foreach ($diff as $key => $value) { 
					BatchParticipant::where('id_batch_participant', $value)->delete();
				}
			}
					
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Master Batch Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Master Batch !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	
	public function get_batch_edit(Request $request) {
        $data = [
            'id_batch' => $request->id_batch
        ];
        $result = MasterBatch::get_batch_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_participant(Request $request) {
		$data_access = Employee::get_access($request->id_url);
		//	dd($data_access);
		if($data_access != null){
			foreach($data_access as $value){
				$x[] = $value->id_branch;
			}
			$group_branch = implode(",", $x);
		}
		else{
			$group_branch = null;
		}
        $data = [
            'id_source_type' => $request->id_source_type
        ];
        $result = MasterBatch::get_participant($data,$group_branch);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_participant_edit(Request $request) {
		$data_access = Employee::get_access($request->id_url);
		//	dd($data_access);
		if($data_access != null){
			foreach($data_access as $value){
				$x[] = $value->id_branch;
			}
			$group_branch = implode(",", $x);
		}
		else{
			$group_branch = null;
		}
        $data = [
            'id_source_type' => $request->id_source_type,
            'id_employee' => $request->id_employee,
            'id_candidate' => $request->id_candidate,
        ];
        $result = MasterBatch::get_participant_edit($data,$group_branch);
	//	dd($result);
        return response()->json($result);
    }
		
	public function get_employee_by() {
        $result = MasterBatch::get_employee_by();
        return response()->json($result);
    }
	
	public function destroy($id) {
		try{
			DB::beginTransaction();
        $data = MasterBatch::findOrFail($id);
        $data->delete();
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Master Batch Delete Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Delete Master Batch !! [There are Relation Another Table]']);           
		}	
    }
	
	public function get_region_batch(Request $request) {
		$userRegion = MasterBatch::get_user_region();
		if(count($userRegion) > 0){
			$reg = $userRegion[0]->id_region;
		}
		else{
			$reg = null;
		}
        $result = MasterBatch::get_region_batch($reg);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_region_batch_edit(Request $request) {
        $result = MasterBatch::get_user_region_edit();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_branch_batch(Request $request) {
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
        $result = MasterBatch::get_branch_batch($group_branch);
	//	dd($result);
        return response()->json($result);
    }
	
	public function upload_review(Request $request) {
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
 					//	$listType			= [];
 						$dataType			= [];

                        foreach ($sheetData as $k => $row) {
                    	   if($k > 0){
                            	$nikKaryawan 		= $row[0] ?? null;
                            	$namaKaryawan 		= $row[1] ?? null;
								$type 				= $row[2] ?? null;

                                if(!is_null($nikKaryawan) && !in_array($nikKaryawan, $allNikKaryawan)){
                                	$allNikKaryawan[] = $nikKaryawan;
                                //	$listType[] = $type;
                                }
								
								 if(!is_null($nikKaryawan)){
                                	//utk menampung sementara data dari excel ke dalam array
                                	$dataType[$k-1]['nik_employee'] = $nikKaryawan;
                                	$dataType[$k-1]['type'] = $type;
                                }
                            }
                        }
					//	dd($dataType);
						foreach ($dataType as $nik => $val) {
							if($val['type'] == 'Internal'){
								$getKaryawan = Employee::where('nik_employee', $val['nik_employee'])->where('status','A')->where('id_company',session('id_company'))->first();
								if($getKaryawan){
									$dataReturn[$nik]['source_type'] = $val['type'];
									$dataReturn[$nik]['id_participant'] = $getKaryawan->id_employee;
								}
							}
							else if($val['type'] == 'External'){
								$getKaryawan = CandidateData::where('identification_number', $val['nik_employee'])->where('is_profil_completed',true)->first();
								if($getKaryawan){
									$dataReturn[$nik]['source_type'] = $val['type'];
									$dataReturn[$nik]['id_participant'] = $getKaryawan->id_candidate;	
								}
							}							
        				}
        				// Get Karyawan
						/*
        				$getKaryawan = DB::table('public.hr_employee')
        					->select('id_employee', 'name', 'nik_employee', 'id_company')
        					->where('status', 'A')
        					->whereIn('nik_employee', $allNikKaryawan)
        					->get();

        				$getKaryawan = $getKaryawan->keyBy(function ($item) {
						    return $item->nik_employee;
						});
        				
						foreach ($getKaryawan as $nik => $val) {
							$getKaryawan[$nik]->source_type = $dataType[$nik]['type'];
        					$dataReturn[] = $val;
        				}
						*/
						$result = array_values($dataReturn);
                        Storage::disk('local')->delete($filePath.$file);
                    }
                }
            }
            DB::commit();   
            return response(['status' => 'true', 'message' => 'Upload Success', 'data' => $result]);
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

	protected function deleteOldZips($dir, $minutes = 5) {
		$files = collect(Storage::allFiles($dir));
		$files->each(function ($file) use($minutes) {
			 $lastModified =  Storage::lastModified($file);
			 $lastModified = Carbon::parse($lastModified);
	
			 if (Carbon::now()->gt($lastModified->addMinutes($minutes))) {
				 Storage::delete($file);
			 }
		 });
	
		return true;
	}

	public function downloadZip(Request $request) {
		$request->validate([
			'list' => 'required'
		]);
		
		Storage::disk('local')->makeDirectory('public/upload/psychotest_archives');
		$this->deleteOldZips('public/upload/psychotest_archives', 5);
		$zipBaseName = 'Psychotest Result '.uniqid("", true).'.zip';
		$zipName = './project/storage/app/public/upload/psychotest_archives/'.$zipBaseName;
		$zip = new ZipArchive();
		try {
			if(!$zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
				throw new \Exception("Failed creating ZIP file");
			}
		} catch(\Exception $e) {
			return response()->json([
				"message" => $e->getMessage()
			], 500);
		}
		
		$files = [];
		$args = ['papi' => 'pdf', 'bct' => 'xlsx', 'kraepelin' => 'pdf', 'disc' => 'pdf'];
		foreach($args as $arg => $format) {
			if(array_key_exists($arg, $request->list)) {
				$file = file_get_contents($request->list[$arg]);
				$fileName = 'cache/'.strtoupper($arg).'_'.session('user_id').uniqid().$request->list['index'].$request->list['source'].'.'.$format;
				$files[] = $fileName;
				$storedFile = Storage::disk('local')->put($fileName, $file);
				$zip->addFile('./project/storage/app/cache/'.basename(storage_path().$fileName), strtoupper($arg).'.'.$format);
			}
		}

		$zip->close();
		foreach($files as $file) {
			Storage::disk('local')->delete($file);
		}
		if(env('FILESYSTEM_DRIVER') == 's3') {
			Storage::put('public/upload/psychotest_archives/'.$zipBaseName, Storage::disk('local')->get('public/upload/psychotest_archives/'.$zipBaseName));
			Storage::disk('local')->delete('public/upload/psychotest_archives/'.$zipBaseName);
		}

		return Storage::download('public/upload/psychotest_archives/'.$zipBaseName, 'Psychotest Results.zip');
	}

	public static function getCandidateBatch($idCandidate, $exceptBatch = null)
	{
		$bindings = [$idCandidate, now()->subMonth(6)->startOfDay()];
		$exceptBatchQuery = "";
		if($exceptBatch) {
			$exceptBatchQuery = "AND pbp.id_batch != ?";
			$bindings[] = $exceptBatch;
		}
		return DB::select("SELECT 
				pbp.id_batch, 
				pbp.id_candidate, 
				hc.name, 
				pmb.batch_code, 
				pmb.batch_name, 
				pmb.id_region, 
				mr.description AS region, 
				pmb.id_branch, 
				mb.description AS branch
			FROM psycho_batch_participant pbp 
			LEFT JOIN web.hr_candidate hc ON pbp.id_candidate = hc.id_candidate
			LEFT JOIN psycho_master_batch pmb ON pbp.id_batch = pmb.id_batch
			LEFT JOIN master_region mr ON pmb.id_region = mr.id_region
			LEFT JOIN master_branch mb ON pmb.id_branch = mb.id_branch
			WHERE pbp.id_candidate = ? 
			AND pbp.creation_date >= ? 
			$exceptBatchQuery
			AND hc.is_profil_completed = TRUE
			AND pbp.status = 'A'",
			$bindings);
	}

	public static function getEmployeeBatch($idEmployee, $exceptBatch = null)
	{
		$bindings = [$idEmployee, now()->subMonth(6)->startOfDay()];
		$exceptBatchQuery = "";
		if($exceptBatch) {
			$exceptBatchQuery = "AND pbp.id_batch != ?";
			$bindings[] = $exceptBatch;
		}
		return DB::select("SELECT 
				pbp.id_batch, 
				pbp.id_employee, 
				he.name, 
				pmb.batch_code, 
				pmb.batch_name, 
				pmb.id_region, 
				mr.description AS region, 
				pmb.id_branch, 
				mb.description AS branch
			FROM psycho_batch_participant pbp
			LEFT JOIN hr_employee he ON pbp.id_employee = he.id_employee
			LEFT JOIN psycho_master_batch pmb ON pbp.id_batch = pmb.id_batch
			LEFT JOIN master_region mr ON pmb.id_region = mr.id_region
			LEFT JOIN master_branch mb ON pmb.id_branch = mb.id_branch
			WHERE pbp.id_candidate = ? 
			AND pbp.creation_date >= ?
			$exceptBatchQuery
			AND he.status = 'A'
			AND pbp.status = 'A'", 
			$bindings);
	}
}
