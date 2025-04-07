<?php

namespace App\Http\Controllers\Employee\EmployeeReco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeReco\EmployeeReco;
use App\Models\Employee\EmployeeReco\EmployeeRecoQuantitative;
use App\Models\Employee\EmployeeReco\EmployeeRecoQualitative;
use App\Models\Kpi\Kpi\QualitativeParticipant;
use App\Models\Kpi\Kpi\QualitativeAppraiser;
use App\Models\Kpi\Kpi\AppraiserResult;
use App\Models\Kpi\KpiSetting\PaGrade;
use App\Models\Kpi\KpiSetting\PaAnswer;
use App\Models\Kpi\KpiSetting\PaWeight;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 

class QualitativeRecoController extends Controller
{
	
	public function index(Request $request) {
         if ($request->ajax()) {
            $data = EmployeeRecoQualitative::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$onclick = "loadedit(".$data->id_qualitative_appraisers.",".$data->id_recommendation_qualitative.",".$data->id_recommendation_header.")";
                                $button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-success" style="padding:1px 4px 1px 4px;" title="Assessment"><span style="font-size:16px;" class="fas fa-list-alt"></span></button> ';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('employee.employee.reco_qualitative.index');
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
	
	protected function update(Request $request) {	
	//	dd($request->all());
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
									'description_answer' => strip_tags($request->$ansnote),
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
				$update_appraiser = QualitativeAppraiser::where('id_qualitative_appraisers', $request->id_qualitative_appraisers)->where('id_recommendation_qualitative', $request->id_recommendation_qualitative)->where('status', 'A')->update(array(
						'submitted' => 1,
						'subtotal_score' => round($score_appraiser,2),
						'maximum_score' => $paweight['weight_value'],
				));
			//	$get_appraiser = QualitativeAppraiser::where('id_employee_participant', $request->id_employee_participant)->where('transaction_type', 'RECO')->where('status', 'A')->get();			
				$getTotal = EmployeeRecoQualitative::getTotal($request->id_employee_participant,$request->id_recommendation_header);	
				$get_appraiser = collect($getTotal);
				$sub_score = array_sum($get_appraiser->pluck('subtotal_score')->all());				
				$max_score =  array_sum($get_appraiser->pluck('maximum_score')->all());
				
				EmployeeRecoQualitative::where('id_recommendation_qualitative', $request->id_recommendation_qualitative)->where('id_recommendation_header', $request->id_recommendation_header)->update(array(
						'total_score' => round($sub_score,2),
				));
				
				$update_employee = QualitativeParticipant::where('id_employee_participant', $request->id_employee_participant)->where('id_recommendation_header', $request->id_recommendation_header)->where('transaction_type', 'RECO')->where('status', 'A')->update(array(
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
	
	
}
