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

class MappingQualitativeController extends Controller {

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
            $data = QualitativeParticipant::getdata($this->accessBranch($request));
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_employee_participant . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
							/*	
								$button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_employee_participant . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
							*/	
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.360_feedback.mapping_qualitative_review.index');
    }
	
	public function modal_mail(Request $request) {
        return view('kpi.360_feedback.mapping_qualitative_review.modal_mail');
    }
	public function list_mail(Request $request) {
        ini_set('max_execution_time', -1);
    	
        if ($request->ajax()) {
        	$id_employee 			= $request->id_employee ?? [];
        	$period 				= $request->period ?? [];
        	$submit_type 				= $request->submit_type ?? [];
        	
            $data = QualitativeAppraiser::get_mail($id_employee,$period,$submit_type);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data) {
						$button = '<button type="button" name="mail" id="' . $data->id_qualitative_appraisers . '" class="mail btn btn-success btn-sm" title="Send Mail"><span class="fas fa-envelope" onclick="send_mail('.$data->id_qualitative_appraisers.')"></span></button> ';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	protected function update(Request $request) {
		try{
			DB::beginTransaction();
		$listIdQualitative    = [];
        $idQualitative      = [];
		if(QualitativeAppraiser::where('id_employee_participant', $request->id_employee_participant)->first() != null){
			$listIdQualitative = QualitativeAppraiser::where('id_employee_participant', $request->id_employee_participant)->where('id_company', session('id_company'))->get()->pluck('id_qualitative_appraisers')->all();
		}
		
		if ($request->mapping) {			
			foreach ($request->mapping as $key => $value) {
				if ($value['id_qualitative_appraisers'] == "") {
					QualitativeAppraiser::create(array(
						'id_employee_participant' => $request->id_employee_participant,
						'id_period' => $request->id_period,
						'id_employee_appraisers' =>  $value['id_employee_appraisers'],
						'appraisers_hierarchy' =>  $value['appraisers_hierarchy'],
						'status' =>  $value['status'],
						'id_cross_company' =>  $value['id_cross_company'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					$idQualitative[] = $value['id_qualitative_appraisers'];
					QualitativeAppraiser::where('id_qualitative_appraisers', $value['id_qualitative_appraisers'])->update(array(
						'id_employee_appraisers' =>  $value['id_employee_appraisers'],
						'appraisers_hierarchy' =>  $value['appraisers_hierarchy'],
						'status' =>  $value['status'],
						'id_cross_company' =>  $value['id_cross_company'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		$diff = array_diff($listIdQualitative, $idQualitative);
		if(count($diff) > 0){
			foreach ($diff as $key => $value) { 
				QualitativeAppraiser::where('id_qualitative_appraisers', $value)->delete();
			}
		}
		
		$get_appraiser = QualitativeAppraiser::where('id_employee_participant', $request->id_employee_participant)->where('status', 'A')->get();
		$sub_score = array_sum($get_appraiser->pluck('subtotal_score')->all());				
		$max_score =  array_sum($get_appraiser->pluck('maximum_score')->all());
		if($max_score == 0){
			$total = 0;
		}
		else{
			$total = ($sub_score / $max_score)*100;
		}
		$update_employee = QualitativeParticipant::where('id_employee_participant', $request->id_employee_participant)->update(array(
			'total_hit_score' => $sub_score,
			'total_maximum_score' => $max_score,
			'total_percent' => $total,
		));
				
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Mapping 360 Feedback Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Save Mapping 360 Feedback !! [' . $e->getMessage() . ']']);           
        }
    }
	
	
	public function reset_appraiser($id) {
		try{
			DB::beginTransaction();
			$data = QualitativeAppraiser::findOrFail($id);
			QualitativeAppraiser::where('id_qualitative_appraisers', $id)->where('status', 'A')->update(array(
				'subtotal_score' => 0,
				'maximum_score' => 0,
				'submitted' => 0,
			));
			
			$get_appraiser = QualitativeAppraiser::where('id_employee_participant', $data['id_employee_participant'])->where('status', 'A')->get();
			$sub_score = array_sum($get_appraiser->pluck('subtotal_score')->all());				
			$max_score =  array_sum($get_appraiser->pluck('maximum_score')->all());
			if($max_score == 0){
				$total = 0;
			}
			else{
				$total = ($sub_score / $max_score)*100;
			}				
			$update_employee = QualitativeParticipant::where('id_employee_participant', $data['id_employee_participant'])->update(array(
				'total_hit_score' => $sub_score,
				'total_maximum_score' => $max_score,
				'total_percent' => $total,
			));
			AppraiserResult::where('id_qualitative_appraisers', $id)->delete();
		DB::commit();
		} catch (\Exception $e) {
            DB::rollBack();
        }
    }
	
	public function get_qualitative_edit(Request $request) {
        $data = [
            'id_employee_participant' => $request->id_employee_participant
        ];
        $result = QualitativeParticipant::get_qualitative_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_employee_filter(Request $request) {
        $result = QualitativeParticipant::get_employee_filter($this->accessBranch($request));
        return response()->json($result);
    }
	public function get_employee_mail() {
        $result = QualitativeAppraiser::get_employee_mail();
        return response()->json($result);
    }
	public function get_employee() {
        $result = QualitativeParticipant::get_employee();
        return response()->json($result);
    }
	public function get_grade(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];
        $result = QualitativeParticipant::get_grade($data);
        return response()->json($result);
    }
	
	public function mass_mail(Request $request) {
        foreach (request('ids') as $key => $value) {
        /*    $view = JobPosition::where('id_position', $value)->first();
            if ($view->status == 'A') {
                $data = array(
                    'status' => 'I'
                );
            } else {
                $data = array(
                    'status' => 'A'
                );
            }
		*/
         //   $result = JobPosition::where('id_position', $value)->update($data);
        }

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
                        $allNikParticipant	= [];
                        $allNikApprasier	= [];
                        $appraiserByParticipant	= [];
                        $dataParticipant	= [];
                        $record 			= [];
                        $nikAppraiserNotFound   = [];
                        $nikParticipantNotFound = [];
                        $periodParticipantNotFound = [];
                        foreach ($sheetData as $k => $row) {
                            if($k > 0){
                            	$nikAppraiser 		= $row[0];
                            	$namaAppraiser 		= $row[1];
                            	$periodeCode 	    = $row[2];
                            	$appraiserHierarchy	= $row[3];
                            	$nikParticipant 	= $row[4];
                            	$namaParticipant 	= $row[5];
                            	$status 			= $row[6];

                                if(!is_null($nikParticipant) && !in_array($nikParticipant, $allNikParticipant)){
                                    $allNikParticipant[] = $nikParticipant;
                                    $dataParticipant[$nikParticipant] = [
                                    	'id_employee'  => null,
                                    	'id_company'   => null,
                                    	'nik' 	       => $nikParticipant,
                                    	'name' 	       => $namaParticipant,
                                    	'status' 	   => $status,
                                    	'appraiser'    => []
                                    ];
                                }
                                if(!is_null($nikAppraiser) && !in_array($nikAppraiser, $allNikApprasier)){
                                	$allNikApprasier[] = $nikAppraiser;
                                }

                                if(!is_null($nikAppraiser)){
                                    $appraiserByParticipant[] = [
                                        'nik_participant' 	=> $nikParticipant,
                                        'id_employee'       => null,
                                        'id_company'        => null,
                                        'nik' 	            => $nikAppraiser,
                                        'name' 	            => $namaAppraiser,
                                        'periode_code'      => $periodeCode,
                                        'hierarchy'         => $appraiserHierarchy,
                                    ];
                                }
                            }
                        }

                        $nikParticipantFromDb = [];
                        $getEmployeeParticipant = DB::table('hr_employee as he')
                    		->select('he.id_employee', 'he.id_company', 'he.nik_employee')
                    		->where('he.status', 'A')
                    		->whereIn('he.nik_employee', $allNikParticipant)
                    		->get();
                    	foreach ($getEmployeeParticipant as $key => $val) {
                            $nikParticipantFromDb[] = $val->nik_employee;
                    		$idEmployeeParticipant[$val->nik_employee] = [
                    			'id_company' 	=> $val->id_company,
                    			'id_employee' 	=> $val->id_employee,
                    		];
                    	}

                    	$idCompanyParticipant = $getEmployeeParticipant->pluck('id_company')->unique();
                    	if(session('id_company') != $idCompanyParticipant[0] || count($idCompanyParticipant) > 1){
            				return response(['status' => 'false_other', 'message' => 'Terdapat NIK Partisipan yang berbeda dengan session company saat ini', 'data' => null]);
                    	}

                    	$getEmployeeAppraiser = DB::table('hr_employee as he')
                    		->select('he.id_employee', 'he.id_company', 'he.nik_employee')
                    		->where('he.status', 'A')
                    		->whereIn('he.nik_employee', $allNikApprasier)
                    		->get();
                    	foreach ($getEmployeeAppraiser as $key => $val) {
                    		$idEmployeeAppraiser[$val->nik_employee] = [
                    			'id_company' 	=> $val->id_company,
                    			'id_employee' 	=> $val->id_employee,
                    		];
                    	}

                        foreach ($appraiserByParticipant as $key => $val) {
                        	$nikParticipant_ = @$val['nik_participant'];
                        	$nikAppraiser_ = @$val['nik'];

                        	$appraiserByParticipant[$key]['id_employee'] = @$idEmployeeAppraiser[@$nikAppraiser_]['id_employee'];
                        	$appraiserByParticipant[$key]['id_company'] = @$idEmployeeAppraiser[@$nikAppraiser_]['id_company'];

                        	if($nikParticipant_ == @$dataParticipant[$nikParticipant_]['nik']){
                        		$dataParticipant[$nikParticipant_]['id_employee'] = @$idEmployeeParticipant[@$nikParticipant_]['id_employee'];
                        		$dataParticipant[$nikParticipant_]['id_company'] = @$idEmployeeParticipant[@$nikParticipant_]['id_company'];
                        		$dataParticipant[$nikParticipant_]['appraiser'][] = $appraiserByParticipant[$key];
                        	}
                        }

                        $notifError = '';
                        foreach ($dataParticipant as $key => $val) {
                            if(!in_array($val['nik'], $nikParticipantFromDb)){
                                $nikParticipantNotFound[] = @$val['nik'];
                            }
                        	$idPartisipan 	       = $val['id_employee'];
                        	$idCompanyPartisipan   = $val['id_company'];

                        	foreach ($val['appraiser'] as $i => $item) {
                                $periodeCode_  = $item['periode_code'];
                                $getPeriod = DB::table('master_period')
                                    ->select('id_period')
                                    ->where('period_code', $periodeCode_)
                                    ->where('id_company', $idCompanyPartisipan)
                                    ->first();
                                if(!$getPeriod){
                                    $periodParticipantNotFound[] = [
                                        'code'  => @$periodeCode_,
                                        'nik'   => @$item['nik_participant']
                                    ];
                                    continue;
                                }
                                $idPeriod_ = @$getPeriod->id_period;

                                $findParticipant = QualitativeParticipant::where('id_employee_participant', $idPartisipan)
                                    ->where('id_period', $idPeriod_)
                                    ->first();

                                if(!$findParticipant){
                                    continue;
                                }

                        		$idAppraiser = $item['id_employee'];
                        		$idCompanyAppraiser = $item['id_company'];

                                if(is_null($idAppraiser) || is_null($idCompanyAppraiser)){
                                    $nikAppraiserNotFound[] = @$item['nik'];
                                    continue;
                                }
                        		$findAppraiser = QualitativeAppraiser::where('id_employee_participant', $idPartisipan)
                        			->where('id_employee_appraisers', $idAppraiser)
                        			->where('id_period', $idPeriod_)
                        			->first();

	                        	if(!$findAppraiser){
	                        		$formAppraiser = [
		                        		'id_employee_appraisers' => $idAppraiser,
		                        		'id_employee_participant' => $idPartisipan,
		                        		'id_period' => $idPeriod_,
		                        		'id_company' => $idCompanyPartisipan,
		                        		'created_by' => session('id_user')
		                        	];
		                        	if($idCompanyPartisipan != $idCompanyAppraiser){
		                        		$formAppraiser['id_cross_company'] = $idCompanyAppraiser;
		                        	}
		                        	QualitativeAppraiser::create($formAppraiser);
	                        	}
                        	}
                        }
                        
                        if(count($nikAppraiserNotFound) > 0){
                            $notifError .= "NIK Appraiser Not Found : ".implode(', ',$nikAppraiserNotFound)." \n";
                        }
                        if(count($nikParticipantNotFound) > 0){
                            $notifError .= "NIK Participant Not Found : ".implode(', ',$nikParticipantNotFound)." \n";
                        }
                        if(count($periodParticipantNotFound) > 0){
                            $periodNotFound = [];
                            foreach ($periodParticipantNotFound as $k => $v) {
                                $periodNotFound[] = $v['code'].'('.$v['nik'].')';
                            }
                            $notifError .= 'Period code Participant Not Found : '.collect($periodNotFound)->unique()->implode(', ');
                        }
                        if($notifError != ''){
                            DB::rollBack();
                            Storage::disk('local')->delete($filePath.$file);
                            return response(['status' => 'false_other', 'message' => $notifError, 'data' => null]);
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
	
	public function get_cross_company() {
        $result = QualitativeParticipant::get_cross_company();
        return response()->json($result);
    }
	
	public function get_cross_company_emp(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];
        $result = QualitativeParticipant::get_cross_company_emp($data);
        return response()->json($result);
    }
	public function get_period() {
        $result = QualitativeParticipant::get_period();
        return response()->json($result);
    }
	
	public function generate(Request $request) {
		 $request->validate([
            'gen_period' => 'required',
                ], [],
                [
                    'gen_period' => 'Period',
        ]);
		DB::beginTransaction();
        try {
			foreach ($request->emp_participant as $key => $value) {
				QualitativeParticipant::generate($value,$request->gen_period);
			}
		 DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Generate Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
	}

}
