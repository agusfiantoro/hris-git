<?php
namespace App\Http\Controllers\Recruitment\Psychotest;

use App\Models\Recruitment\Psychotest\Psychotest;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Spipu\Html2Pdf\Html2Pdf;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class PsychotestController extends Controller {
	
	protected $urls = [
		'https://career.borwita.co.id/', // default
		'http://103.149.176.106/home', // diberi home agar tidak diredirect ke https
		'http://192.168.122.38/home', // diberi home agar tidak diredirect ke https
	];
	public $servercareer = '';

	public function __construct() {
		$this->servercareer = $this->urls[0];
    }

	public function getValidServerCareer() {
		$found = false;
		foreach($this->urls as $url) {
			try {
				$options = [
					'verify' => false,
					'Accept' => 'application/json', 
				];
				// dump($url);
				$client = new Client();
				$body = $client->get($url, $options)->getBody()->getContents();
				$this->servercareer = str_replace('home', '', $url);
				$found = true;
				break;
			} catch(\Exception $e) {
				// dump($e->getMessage());
				// continue;
			}
		}
		// No valid server found, fallback to default server
		if(!$found) $this->servercareer = $this->urls[0];
		return $this->servercareer;
	}
	
	public function index_disc(Request $request) {
        return view('recruitment.psychotest.disc_summary.index');
    }
	
	public function index_disc_can(Request $request) {    	
        if ($request->ajax()) {
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
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_disc_can($request->startdate,$request->enddate,$group_branch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$data->id_candidate.'&id_batch='.$id_batch.'&test=disc","_new","","")';
						$button = '<button type="button" name="pdf" id="' . $data->id_candidate . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="DISC Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';						
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_disc_emp(Request $request) {    	
        if ($request->ajax()) {
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
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_disc_emp($request->startdate,$request->enddate,$group_branch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$data->id_employee.'&id_batch='.$id_batch.'&test=disc","_new","","")';
						$button = '<button type="button" name="pdf" id="' . $data->id_employee . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="DISC Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';						
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

	public function index_papi(Request $request) {
        return view('recruitment.psychotest.papikostick_summary.index');
    }
	
	public function index_papi_can(Request $request) {    	
        if ($request->ajax()) {
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
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_papi_can($request->startdate,$request->enddate,$group_branch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$data->id_candidate.'&id_batch='.$id_batch.'&test=papi","_new","","")';
						$button = '<button type="button" name="pdf" id="' . $data->id_candidate . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="Papikostick Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';						
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_papi_emp(Request $request) {    	
        if ($request->ajax()) {
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
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_papi_emp($request->startdate,$request->enddate,$group_branch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$data->id_employee.'&id_batch='.$id_batch.'&test=papi","_new","","")';
						$button = '<button type="button" name="pdf" id="' . $data->id_employee . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="Papikostick Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';						
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_kraepelin(Request $request) {
        return view('recruitment.psychotest.kreapelin_summary.index');
    }
	
	public function index_kraepelin_can(Request $request) {    	
        if ($request->ajax()) {
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

			$searchBy = $request->search_by ?? null;
			if($searchBy=='batch'){
				$idBatch = $request->id_batch ?? null;
				$start = null;
				$end = null;
			} else {
				$idBatch = null;
				$start = $request->startdate;
				$end = $request->enddate;
			}
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_kraepelin_can($start,$end,$group_branch,$idBatch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$data->id_candidate.'&id_batch='.$id_batch.'&test=kraepelin","_new","","")';

						$button = '<button type="button" id_user="'.$data->id_candidate.'" id_batch="'.$id_batch.'" user_type="candidate" class="reset btn btn-sm btn-danger" title="Reset Kraepelin"><span class="fas fa-trash-alt" style="font-size:16px;"></span></button> ';		
						$button .= '&nbsp;&nbsp;<button type="button" name="pdf" id="'.$data->id_candidate.'"  onclick='.$onclick.' class="pdf btn btn-sm btn-success download" title="Kraepelin Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';						
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_kraepelin_emp(Request $request) {    	
        if ($request->ajax()) {
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

			$searchBy = $request->search_by ?? null;
			if($searchBy=='batch'){
				$idBatch = $request->id_batch ?? null;
				$start = null;
				$end = null;
			} else {
				$idBatch = null;
				$start = $request->startdate;
				$end = $request->enddate;
			}
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_kraepelin_emp($start,$end,$group_branch,$idBatch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$data->id_employee.'&id_batch='.$id_batch.'&test=kraepelin","_new","","")';
						$button = '<button type="button" id_user="'.$data->id_employee.'" id_batch="'.$id_batch.'" user_type="employee" class="reset btn btn-sm btn-danger" title="Reset Kraepelin"><span class="fas fa-trash-alt" style="font-size:16px;"></span></button> ';		
						$button .= '&nbsp;&nbsp;<button type="button" name="pdf" id="'.$data->id_employee.'"  onclick='.$onclick.' class="pdf btn btn-sm btn-success download" title="Kraepelin Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_bct(Request $request) {
        return view('recruitment.psychotest.bct_summary.index');
    }
	
	public function index_bct_can(Request $request) {    	
        if ($request->ajax()) {
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
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_bct_can($request->startdate,$request->enddate,$group_branch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=candidate&id_user_assessment='.$data->id_candidate.'&id_batch='.$id_batch.'&test=bct","_new","","")';
						$button = '<button type="button" name="pdf" id="' . $data->id_candidate . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="BCT Report"><span class="fas fa-file-excel" style="font-size:16px;"></span></button> ';						
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_bct_emp(Request $request) {    	
        if ($request->ajax()) {
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
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_bct_emp($request->startdate,$request->enddate,$group_branch);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick='window.open("'.$serverCareer.'assessment/result?user_type=employee&id_user_assessment='.$data->id_employee.'&id_batch='.$id_batch.'&test=bct","_new","","")';
						$button = '<button type="button" name="pdf" id="' . $data->id_employee . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="BCT Report"><span class="fas fa-file-excel" style="font-size:16px;"></span></button> ';						
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_psychogram(Request $request) {
		$urlWebCareer = self::getValidServerCareer();
		$id_url = "recruitment/psychotest/psychogram";
		$data_access = Employee::get_access($id_url);
		if($data_access != null){
			foreach($data_access as $value){
				$x[] = $value->id_branch;
			}
			$group_branch = implode(",", $x);
		}
		else{
			$group_branch = null;
		}
        return view('recruitment.psychotest.psychogram.index', compact('urlWebCareer', 'group_branch'));
    }

	public function index_psychogram_can(Request $request) {    
        if ($request->ajax()) {
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
			$searchBy = $request->search_by ?? null;
			$idPosition = null;
			$idJobGrade = null;
			$idDept = null;
			if($searchBy=='batch'){
				$idBatch = $request->id_batch ?? null;
				$start = null;
				$end = null;
			} else if($searchBy == 'created_date') {
				$idBatch = null;
				$start = $request->startdate;
				$end = $request->enddate;
			} else if($searchBy == 'position') {
				$idBatch = null;
				$start = null;
				$end = null;
				if($request->id_position_routing) {
					$idPosition = is_array($request->id_position_routing) ? $request->id_position_routing : [$request->id_position_routing];
				}
			} else if($searchBy == 'job_grade') {
				$idBatch = null;
				$start = null;
				$end = null;
				$idJobGrade = $request->id_job_grade;
			}
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_psychogram_can($start,$end,$group_branch,$idBatch,$idPosition, $idJobGrade);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_dept && $data->id_grade){
							$id_dept = $data->id_dept;
							$id_grade = $data->id_grade;
						}
						else{
							$id_dept = "null";
							$id_grade = "null";
						}
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick = 'download("'.$serverCareer.'psikogram/summary?id_user_assessment='.$data->id_candidate.'&user_type=candidate&id_department='.($data->id_dept ?? $data->id_dept_generated).'&id_grade='.($data->id_grade ?? $data->id_job_grade_generated).'&id_batch='.$data->id_batch.'&request_type=pdf")';
						$button = '<button type="button" name="pdf" id="' . $data->id_candidate . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="psychogram Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';	
						$buttonPreview = '<span id_user_assessment="'.$data->id_candidate.'" id_batch="'.$data->id_batch.'" user_type="candidate" class="btn btn-primary preview_psychogram" style="padding:3px 7px;" title="Preview Psychogram"><span class="fas fa-list" style="font-size:16px;"></span></span> ';
                        return $button.$buttonPreview;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function index_psychogram_emp(Request $request) {    	
        if ($request->ajax()) {
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
			$searchBy = $request->search_by ?? null;
			$idPosition = null;
			$idJobGrade = null;
			$idDept = null;
			if($searchBy=='batch'){
				$idBatch = $request->id_batch ?? null;
				$start = null;
				$end = null;
			} else if($searchBy == 'created_date') {
				$idBatch = null;
				$start = $request->startdate;
				$end = $request->enddate;
			} else if($searchBy == 'position') {
				$idBatch = null;
				$start = null;
				$end = null;
				if($request->id_position_routing) {
					$idPosition = is_array($request->id_position_routing) ? $request->id_position_routing : [$request->id_position_routing];
				}
			} else if($searchBy == 'job_grade') {
				$idBatch = null;
				$start = null;
				$end = null;
				$idJobGrade = $request->id_job_grade;
			} else if($searchBy == 'department') {
				$idBatch = null;
				$start = null;
				$end = null;
				$idDept = $request->id_department;
			}
			$serverCareer = self::getValidServerCareer();
            $data = Psychotest::get_psychogram_emp($start,$end,$group_branch,$idBatch,$idPosition, $idJobGrade, $idDept);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) use($serverCareer) {	
						if($data->id_dept && $data->id_grade){
							$id_dept = $data->id_dept;
							$id_grade = $data->id_grade;
						}
						else{
							$id_dept = "null";
							$id_grade = "null";
						}
						if($data->id_batch){
							$id_batch = $data->id_batch;
						}
						else{
							$id_batch = "null";
						}
						$onclick = 'window.open("'.$serverCareer.'psikogram/summary?id_user_assessment='.$data->id_employee.'&user_type=employee&id_department='.$data->id_dept.'&id_grade='.$data->id_grade.'&id_batch='.$data->id_batch.'&request_type=pdf","_new","","")';
						$button = '<button type="button" name="pdf" id="' . $data->id_employee . '"  onclick='.$onclick.' class="pdf btn btn-success download" style="padding:4px 8px;" title="Kraepelin Report"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';			
						$buttonPreview = '<span id_user_assessment="'.$data->id_employee.'" id_batch="'.$data->id_batch.'" user_type="employee" class="btn btn-primary preview_psychogram" style="padding:3px 7px;" title="Preview Psychogram"><span class="fas fa-list" style="font-size:16px;"></span></span> ';
                        return $button.$buttonPreview;			
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function get_grade() {
        $result = Psychotest::get_grade();
        return response()->json($result);
    }

    public function get_department() {
        $result = Psychotest::get_department();
        return response()->json($result);
    }

    public function get_batch() {
        $result = Psychotest::get_batch(session('id_company'));
        return response()->json($result);
    }

	public function get_position() {
        $result = Psychotest::get_position(session('id_company'));
        return response()->json($result);
    }

    public function get_kraepelin_result(Request $request) {
		$idBatch = $request->id_batch ?? null;
		$userType = $request->user_type ?? 'candidate';
		$idUserAssessment = $request->id_user_assessment ?? null;
        $result = Psychotest::get_kraepelin_result($idBatch, $userType, $idUserAssessment);
        return response()->json($result);
    }

    public function reset_kraepelin_result(Request $request) {
    	try{
			DB::beginTransaction();
			$idBatch = $request->id_batch ?? null;
			$userType = $request->user_type ?? 'candidate';
			$idUserAssessment = $request->id_user_assessment ?? null;	
        	$reset = Psychotest::reset_kraepelin_result($idBatch, $userType, $idUserAssessment);
        	if(!$reset){
                throw new \Exception('Failed');
        	}
			DB::commit();
			return response()->json(['status' => true, 'message' => 'Success', 'data'=>$reset]);
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => false, 'message' => 'Failed', 'data'=>'']);
        }
    }

    public function get_psychogram(Request $request) {
    	try{
			DB::beginTransaction();
			$url = $request->url;
	    	$client = new Client();
			$options = [
	            'verify' => false,
	            'Accept' => 'application/json', 
	        ];
	        $response = $client->get($url, $options)->getBody()->getContents();		
			DB::commit();
			return response()->json(['status' => true, 'message' => 'Success', 'data'=>$response]);
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => false, 'message' => 'Failed', 'data'=>'']);
        }
    }

    public function psychogramExport(Request $request)
    {   
    	ini_set('max_execution_time', -1);
		$serverCareer = self::getValidServerCareer();
    	$start = Carbon::parse(@$request->startdate)->translatedFormat('d F Y');
    	$end = Carbon::parse(@$request->enddate)->translatedFormat('d F Y');
    	$jobgrade = @$request->jobgrade ?? 7;
    	$department = @$request->department ?? null;
    	$masterGrade = DB::table('master_job_grade')->where('id_job_grade', $jobgrade)->first();

        $identityTitle = (@$request->type == 'candidate') ? 'Email' : 'NIK Employee';
        $fileType = (@$request->type == 'candidate') ? 'Candidate' : 'Employee';

        $filename = 'Psychogram_Summary_'.$fileType.'_as_'.@$masterGrade->description.' ('.$start.' - '.$end.')';

        $t = '<table border="1">';
        $t .= '<tr>';
            $t .= '<th style="background-color:#7ecc61;">No</th>';
            $t .= '<th style="background-color:#7ecc61;">Type</th>';
            $t .= '<th style="background-color:#7ecc61;">'.$identityTitle.'</th>';
            $t .= '<th style="background-color:#7ecc61;">Tanggal Tes</th>';
            $t .= '<th style="background-color:#7ecc61;">Batch</th>';
            $t .= '<th style="background-color:#7ecc61;">Nama</th>';
            $t .= '<th style="background-color:#7ecc61;">Pendidikan Terakhir</th>';
            $t .= '<th style="background-color:#7ecc61;">Usia</th>';

        if(@$request->type != 'candidate'){
        	$t .= '<th style="background-color:#7ecc61;">Regional</th>';
            $t .= '<th style="background-color:#7ecc61;">Cabang</th>';
        }

            $t .= '<th style="background-color:#7ecc61;">Profil</th>';
            $t .= '<th style="background-color:#7ecc61;">DISC</th>';
            $t .= '<th style="background-color:#7ecc61;">PAPI</th>';
            $t .= '<th style="background-color:#7ecc61;">KRAEPELIN</th>';
            $t .= '<th style="background-color:#7ecc61;">BCT</th>';
            $t .= '<th style="background-color:#7ecc61;">Keterangan</th>';

        if(@$request->type == 'candidate'){
            $t .= '<th style="background-color:#7ecc61;">URL DCK</th>';
        }

            $t .= '<th style="background-color:#7ecc61;">URL Psikogram</th>';
            $t .= '<th style="background-color:#7ecc61;">URL DISC</th>';
            $t .= '<th style="background-color:#7ecc61;">URL Kraepelin</th>';
            $t .= '<th style="background-color:#7ecc61;">URL Papikostic</th>';


        $t .= '</tr>'; 

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

		$searchBy = $request->search_by ?? null;
		$position = null;
		$jobGrade = null;
		$idDept = null;
		if($searchBy=='batch'){
			$idBatch = $request->id_batch ?? null;
			$start = null;
			$end = null;
		} else if($searchBy == 'created_date') {
			$idBatch = null;
			$start = $request->startdate;
			$end = $request->enddate;
		} else if($searchBy == 'position') {
			$idBatch = null;
			$start = null;
			$end = null;
			$position = $request->id_position;
		} else if($searchBy == 'job_grade') {
			$idBatch = null;
			$start = null;
			$end = null;
			$position = null;
			$jobGrade = explode(",", $request->id_job_grade);
		} else if($searchBy == 'department') {
			$idBatch = null;
			$start = null;
			$end = null;
			$jobGrade = explode(",", $request->id_job_grade);
			$idDept = $request->employee_id_department;
		}

		if(@$request->type == 'candidate'){
        	$data = Psychotest::get_psychogram_can($start,$end,$group_branch, $idBatch, $position, $jobGrade);
		} else {
        	$data = Psychotest::get_psychogram_emp($start,$end,$group_branch, $idBatch, $position, $jobGrade, $idDept);
		}
		$payload = collect($data)->map(function($row) {
			return collect($row)
					->only(['id_candidate', 'id_employee', 'id_batch', 'id_dept'])
					->all();
		});

		$summaryResults = Http::accept('application/json')
						->get($serverCareer.'psikogram/batch-summary', [
							"query" => compact('start', 'end', 'group_branch', 'idBatch', 'position', 'jobGrade', 'idDept'),
							"selected_job_grade" => $jobgrade,
							"request_type" => "raw",
							"type" => $request->type,
							"id_company" => session('id_company'),
						]);
		try {
			$summaryResults = $summaryResults->object()->data;
		} catch(Exception $e) {
			return response()->json([
				'status' => $summaryResults->status(),
				'message' => 'An error occured during data fetching process.',
				'data' => $summaryResults->body(),
			], $summaryResults->status());
		}
		

    	if(count($summaryResults) > 0){
    		$no = 1;
    		foreach ($summaryResults as $k => $response) {
    			$idBatch = $data[$k]->id_batch ?? null;
		        $idUserAssessment = $data[$k]->id_candidate ?? $data[$k]->id_employee;
		        $userType = $data[$k]->id_candidate ? 'candidate' : 'employee';
		        $idDepartment = $department ? $department : (@$data[$k]->id_dept ? @$data[$k]->id_dept : 2);
		        $idGrade = $jobgrade;
		        $requestType = 'raw';

		        $param = '';
		        $param.= 'request_type='.$requestType;
		        $param.= '&id_user_assessment='.$idUserAssessment;
		        $param.= '&id_batch='.$idBatch;
		        $param.= '&id_department='.$idDepartment;
		        $param.= '&id_grade='.$idGrade;
		        $param.= '&user_type='.$userType;

		        $paramViewPsikogram = '';
		        $paramViewPsikogram.= 'request_type=table';
		        $paramViewPsikogram.= '&id_user_assessment='.$idUserAssessment;
		        $paramViewPsikogram.= '&id_batch='.$idBatch;
		        $paramViewPsikogram.= '&id_department='.$idDepartment;
		        $paramViewPsikogram.= '&id_grade='.$idGrade;
		        $paramViewPsikogram.= '&user_type='.$userType;

		        $paramByTest = '';
		        $paramByTest.= 'user_type='.$userType;
		        $paramByTest.= '&id_user_assessment='.$idUserAssessment;
		        $paramByTest.= '&id_batch='.$idBatch;

		        $urlViewPsikogram = $serverCareer.'psikogram/summary?'.$paramViewPsikogram;
		        $urlDisc = $serverCareer.'assessment/result?'.$paramByTest.'&test=disc';
		        $urlKraepelin = $serverCareer.'assessment/result?'.$paramByTest.'&test=kraepelin';
		        $urlPapi = $serverCareer.'assessment/result?'.$paramByTest.'&test=papi';
		        $urlDCK = url('recruitment/recruitment/candidate/download').'?id_candidate='.$idUserAssessment;
		    	

		        $thisIdentity = $response->identity;
		        $type = $thisIdentity->type;
		        $identitas = $thisIdentity->nik_or_email;
		        $tanggalTes = $thisIdentity->test_date;
		        $batch = $thisIdentity->batch;
		        $nama = $thisIdentity->name;
		        $pendidikan = $thisIdentity->education;
		        $usia = $thisIdentity->age;
		        $regional = $thisIdentity->region;
		        $cabang = $thisIdentity->branch;

		        $thisResult = @$response->result;
		        $statusResult = @$thisResult->status;
		        $profilResult = @$thisResult->profil;
		        $summaryResult = @$thisResult->summary_test;
		        $disarankanResult = @$thisResult->div_disarankan;
		        $dipertimbangkanResult = @$thisResult->div_dipertimbangkan;
		        $tidakDisarankanResult = @$thisResult->div_tidak_disarankan;

		        $disc = @$summaryResult->disc ? 'done' : 'not yet';
		        $papi = @$summaryResult->papi ? 'done' : 'not yet';
		        $kraepelin = @$summaryResult->kraepelin ? 'done' : 'not yet';
		        $bct = @$summaryResult->bct ? 'done' : 'not yet';
		        $message = '';

		        $linkDisc = ($disc=='done') ? $urlDisc : '';
		        $linkKraepelin = ($kraepelin=='done') ? $urlKraepelin : '';
		        $linkPapi = ($papi=='done') ? $urlPapi : '';

		        if($statusResult == false){
		        	$message = str_replace('<br>', ' -', @$thisResult->message) ;
		        } else {
		        	if($disarankanResult != ''){ $message = 'Disarankan' ; }
		        	if($dipertimbangkanResult != ''){ $message = 'Dipertimbangkan' ; }
		        	if($tidakDisarankanResult != ''){ $message = 'Tidak Disarankan' ; }
		        }

		        $t .= '<tr>';
                    $t .= '<td>'.$no.'</td>';
                    $t .= '<td>'.$type.'</td>';
                    $t .= '<td>'.$identitas.'</td>';
                    $t .= '<td>'.$tanggalTes.'</td>';
                    $t .= '<td>'.$batch.'</td>';
                    $t .= '<td>'.$nama.'</td>';
                    $t .= '<td>'.$pendidikan.'</td>';
                    $t .= '<td>'.$usia.'</td>';

                if(@$request->type != 'candidate'){
                	$t .= '<td>'.$regional.'</td>';
                    $t .= '<td>'.$cabang.'</td>';
                }

                    $t .= '<td>'.$profilResult.'</td>';
                    $t .= '<td>'.$disc.'</td>';
                    $t .= '<td>'.$papi.'</td>';
                    $t .= '<td>'.$kraepelin.'</td>';
                    $t .= '<td>'.$bct.'</td>';
                    $t .= '<td>'.$message.'</td>';

                if(@$request->type == 'candidate'){
                	$t .= '<td>'.$urlDCK.'</td>';
                }

                    $t .= '<td>'.$urlViewPsikogram.'</td>';
                    $t .= '<td>'.$linkDisc.'</td>';
                    $t .= '<td>'.$linkKraepelin.'</td>';
                    $t .= '<td>'.$linkPapi.'</td>';

        		$t .= '</tr>'; 

                $no++;
    		}
    	}
        $t .= '</table>';

		header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=".$filename.".xls");
        echo $t;
    }
}
