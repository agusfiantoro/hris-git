<?php
 
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PsikogramController;
use App\Models\EducationCandidate;
use App\Models\assessment\GroupPsychotest;
use App\Models\assessment\BctAnswerUser;
use App\Models\assessment\BctUser;
use App\Models\Candidate;
use App\Models\assessment\MasterBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use DataTables;
use DateTime;
use Validator;
use PDF;

class AssessmentController extends Controller {

 public function __construct()
    {
        $this->PsikogramController = new PsikogramController;
    }
 public function result(Request $request) {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        DB::beginTransaction();
        try {
            $idBatch = $request->id_batch ?? null;
            $test = $request->test ?? null;
            $idCandidateOrIdEmployee = $request->id_user_assessment ?? null;
            $userType = $request->user_type ?? 'candidate';
            $start = $request->start ?? null;
            $end = $request->end ?? null;
            $getGroupPsycho = GroupPsychotest::getData($idBatch, $idCandidateOrIdEmployee, $userType);
            if(!in_array($test, ['disc','papi','kraepelin','bct'])){
                throw new \Exception("parameter test is not found");
            }

            $getIdentity = self::user_identity($request);
            $result = [
                'identity' => [
                    'id_user_assessment' => @$getIdentity['id_user_assessment'],
                    'name' => @$getIdentity['name'],
                    'nik_or_email' => @$getIdentity['nik_or_email'],
                    'type' => @$getIdentity['type'],
                    'education' => @$getIdentity['education'],
                    'test_date' => Carbon::parse(@$getGroupPsycho->test_date)->translatedFormat('d F Y'),
                    'batch' => @$getGroupPsycho->batch_name.' ('.@$getGroupPsycho->location.')',
                    'age' => @$getIdentity['age'],
                    'region'  => @$getIdentity['region'],
                    'branch'  => @$getIdentity['branch'],
                ],
            ];
            
            $html='';

            if($test == 'bct'){
                $resultBct = self::bctExport($request);die;
            }

            $fileName = @$test_result['name'].' '.$result['identity']['name'].' '.$result['identity']['batch'].'.pdf';
            $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download($fileName);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $pdf = PDF::loadHTML($e->getMessage())->setPaper('a4', 'landscape');
            return $pdf->download();
        }
    }
	
	 public function bctExport(Request $request)
    {   
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        $idBatch = $request->id_batch ?? null;
        $idCandidateOrIdEmployee = $request->id_user_assessment ?? null;
        $userType = $request->user_type ?? 'candidate';
        $start = $request->start ?? null;
        $end = $request->end ?? null;
        $allUser = [];
        $filenameByUser = [];

        if(($start&&$end) && (!$idCandidateOrIdEmployee && !$idBatch)){
            $today  = date('Y-m-d');
            $start  = $request->start ? $request->start : date('Y-m-d', strtotime($today." -2 month"));
            $end    = $request->end ? $request->end : $today;
            $end    = date('Y-m-d', strtotime($end." +1 day"));

            $filename = 'BCT_Report ('.$start.' - '.$end.')';
        } else {
            $filename = 'BCT_Report '.strtoupper($userType);
        }

        $questionSub1 = DB::table('web.psycho_bct_master_question')->orderByRaw("question_sequence::int")->where('subtest_name', 1)->pluck('question_sequence')->all();
        $questionSub2 = DB::table('web.psycho_bct_master_question')->orderByRaw("question_sequence::int")->where('subtest_name', 2)->pluck('question_sequence')->all();
        $questionSub3 = DB::table('web.psycho_bct_master_question')->orderByRaw("question_sequence::int")->where('subtest_name', 3)->pluck('question_sequence')->all();
        $questionSub4 = DB::table('web.psycho_bct_master_question')->orderByRaw("question_sequence::int")->where('subtest_name', 4)->pluck('question_sequence')->all();
        $questionSub5 = DB::table('web.psycho_bct_master_question')->orderByRaw("question_sequence::int")->where('subtest_name', 5)->pluck('question_sequence')->all();
        $questionSub6 = DB::table('web.psycho_bct_master_question')->orderByRaw("question_sequence::int")->where('subtest_name', 6)->pluck('question_sequence')->all();

        $getAnswerSub1 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 1, $userType, $start, $end);
        if($getAnswerSub1->count() > 0){
            $allIdBatch = $getAnswerSub1->pluck('id_batch')->all();
            $getAllBatch = MasterBatch::getAllData($allIdBatch);
            $thisBatch = [];
            foreach ($getAllBatch as $k => $val) {
                $thisBatch[$val->id_batch] = $val->batch_name.' ('.$val->location.')';
            }

            if($userType == 'candidate'){
                $resultAllUser = [];
                $allIdUser = $getAnswerSub1->pluck('id_candidate')->unique();
                $testDate = $getAnswerSub1->pluck('creation_date', 'id_candidate')->toArray();
                $idBatchByCandidate = $getAnswerSub1->pluck('id_batch', 'id_candidate')->toArray();
            } else {
                $resultAllUser = [];
                $allIdUser = $getAnswerSub1->pluck('id_employee')->unique();
                $testDate = $getAnswerSub1->pluck('creation_date', 'id_employee')->toArray();
                $idBatchByCandidate = $getAnswerSub1->pluck('id_batch', 'id_employee')->toArray();
            }

            $getAnswerSub2 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 2, $userType, $start, $end);
            $getAnswerSub3 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 3, $userType, $start, $end);
            $getAnswerSub4 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 4, $userType, $start, $end);
            $getAnswerSub5 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 5, $userType, $start, $end);
            $getAnswerSub6 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 6, $userType, $start, $end);

            foreach ($allIdUser as $k_ => $thisIdCandidateOrEmployee) {
                foreach ($questionSub1 as $k => $sequence) {
                    $resultAllUser[$thisIdCandidateOrEmployee][$idBatchByCandidate[$thisIdCandidateOrEmployee]]['result'][1][$sequence] = null;
                }
                foreach ($questionSub2 as $k => $sequence) {
                    $resultAllUser[$thisIdCandidateOrEmployee][$idBatchByCandidate[$thisIdCandidateOrEmployee]]['result'][2][$sequence] = null;
                }
                foreach ($questionSub3 as $k => $sequence) {
                    $resultAllUser[$thisIdCandidateOrEmployee][$idBatchByCandidate[$thisIdCandidateOrEmployee]]['result'][3][$sequence] = null;
                }
                foreach ($questionSub4 as $k => $sequence) {
                    $resultAllUser[$thisIdCandidateOrEmployee][$idBatchByCandidate[$thisIdCandidateOrEmployee]]['result'][4][$sequence] = null;
                }
                foreach ($questionSub5 as $k => $sequence) {
                    $resultAllUser[$thisIdCandidateOrEmployee][$idBatchByCandidate[$thisIdCandidateOrEmployee]]['result'][5][$sequence] = null;
                }
                foreach ($questionSub6 as $k => $sequence) {
                    $resultAllUser[$thisIdCandidateOrEmployee][$idBatchByCandidate[$thisIdCandidateOrEmployee]]['result'][6][$sequence] = null;
                }
            }

            foreach ($getAnswerSub1 as $k => $val) {
                if($userType == 'candidate'){
                    $thisIdUser = $val->id_candidate;
                } else {
                    $thisIdUser = $val->id_employee;
                }
                $resultAllUser[$thisIdUser][$val->id_batch]['result'][$val->subtest_name][$val->question_sequence] = strtoupper($val->answer_sequence);
            }

            if($getAnswerSub2->count() > 0){
                foreach ($getAnswerSub2 as $k => $val) {
                    if($userType == 'candidate'){
                        $thisIdUser = $val->id_candidate;
                    } else {
                        $thisIdUser = $val->id_employee;
                    }
                    $resultAllUser[$thisIdUser][$val->id_batch]['result'][$val->subtest_name][$val->question_sequence] = strtoupper($val->answer_sequence);
                }
            }
            if($getAnswerSub3->count() > 0){
                foreach ($getAnswerSub3 as $k => $val) {
                    if($userType == 'candidate'){
                        $thisIdUser = $val->id_candidate;
                    } else {
                        $thisIdUser = $val->id_employee;
                    }
                    $resultAllUser[$thisIdUser][$val->id_batch]['result'][$val->subtest_name][$val->question_sequence] = strtoupper($val->answer_sequence);
                }
            }
            if($getAnswerSub4->count() > 0){
                foreach ($getAnswerSub4 as $k => $val) {
                    if($userType == 'candidate'){
                        $thisIdUser = $val->id_candidate;
                    } else {
                        $thisIdUser = $val->id_employee;
                    }
                    $resultAllUser[$thisIdUser][$val->id_batch]['result'][$val->subtest_name][$val->question_sequence] = $val->essay_answer;
                }
            }
            if($getAnswerSub5->count() > 0){
                foreach ($getAnswerSub5 as $k => $val) {
                    if($userType == 'candidate'){
                        $thisIdUser = $val->id_candidate;
                    } else {
                        $thisIdUser = $val->id_employee;
                    }
                    $resultAllUser[$thisIdUser][$val->id_batch]['result'][$val->subtest_name][$val->question_sequence] = $val->essay_answer;
                }
            }
            if($getAnswerSub6->count() > 0){
                foreach ($getAnswerSub6 as $k => $val) {
                    if($userType == 'candidate'){
                        $thisIdUser = $val->id_candidate;
                    } else {
                        $thisIdUser = $val->id_employee;
                    }
                    $resultAllUser[$thisIdUser][$val->id_batch]['result'][$val->subtest_name][$val->question_sequence] = strtoupper($val->answer_sequence);
                }
            }

            foreach ($allIdUser as $k => $val) {
                $request->merge(['id_user_assessment' => $val]);
                $getIdentity = self::user_identity($request);
                $idBatchThisTest = @$idBatchByCandidate[@$getIdentity['id_user_assessment']];
                $batchThisTest = @$thisBatch[$idBatchThisTest];
                $allUser[@$getIdentity['id_user_assessment']][$idBatchThisTest] = [
                    'id_user_assessment' => @$getIdentity['id_user_assessment'],
                    'nik_or_email' => @$getIdentity['nik_or_email'],
                    'name' => @$getIdentity['name'],
                    'type' => @$getIdentity['type'],
                    'education' => @$getIdentity['education'],
                    'age' => @$getIdentity['age'],
                    'test_date' => Carbon::parse(@$testDate[@$getIdentity['id_user_assessment']])->translatedFormat('d F Y'),
                    'batch' => $batchThisTest,
                    'position' => @$getIdentity['position'],
                    'region' => @$getIdentity['region'],
                    'branch' => @$getIdentity['branch'],
                    'result' => $resultAllUser[@$getIdentity['id_user_assessment']][$idBatchThisTest]['result'],
                ];
                $filenameByUser[] = @$getIdentity['name'].' '.$batchThisTest;
            }
        }

        $titleNikOrEmployee = ($userType=='candidate') ? "Email" : "NIK Employee";
        $t = '<table border="1">';
        $t .= '<tr>';
            $t .= '<th>No</th>';
            $t .= '<th>'.$titleNikOrEmployee.'</th>';
            $t .= '<th>Tanggal Tes</th>';
            $t .= '<th>Batch</th>';
            $t .= '<th>Nama</th>';
            $t .= '<th>Pendidikan Terakhir</th>';
            $t .= '<th>Usia (Tahun)</th>';

        if($userType!='candidate'){
            $t .= '<th>Jabatan</th>';
            $t .= '<th>Regional</th>';
            $t .= '<th>Cabang</th>';
        }

            // merah : #b14a4a, kuning : #c5c455, hijau : #7ecc61, tosca : #00FFFF
            foreach ($questionSub1 as $k => $val) {
                $t .= '<th style="background-color:#b14a4a;">Sub-1-'.$val.'</th>';
            }
            foreach ($questionSub2 as $k => $val) {
                $t .= '<th style="background-color:#c5c455;">Sub-2-'.$val.'</th>';
            }
            foreach ($questionSub3 as $k => $val) {
                $t .= '<th style="background-color:#7ecc61;">Sub-3-'.$val.'</th>';
            }
            foreach ($questionSub4 as $k => $val) {
                $t .= '<th style="background-color:#b14a4a;">Sub-4-'.$val.'</th>';
            }
            foreach ($questionSub5 as $k => $val) {
                $t .= '<th style="background-color:#c5c455;">Sub-5-'.$val.'</th>';
            }
            foreach ($questionSub6 as $k => $val) {
                $t .= '<th style="background-color:#7ecc61;">Sub-6-'.$val.'</th>';
            }

            foreach ($questionSub1 as $k => $val) {
                $t .= '<th style="background-color:#b14a4a;">Raw-Sub-1-'.$val.'</th>';
            }
            foreach ($questionSub2 as $k => $val) {
                $t .= '<th style="background-color:#c5c455;">Raw-Sub-2-'.$val.'</th>';
            }
            foreach ($questionSub3 as $k => $val) {
                $t .= '<th style="background-color:#7ecc61;">Raw-Sub-3-'.$val.'</th>';
            }
            foreach ($questionSub4 as $k => $val) {
                $t .= '<th style="background-color:#b14a4a;">Raw-Sub-4-'.$val.'</th>';
            }
            foreach ($questionSub5 as $k => $val) {
                $t .= '<th style="background-color:#c5c455;">Raw-Sub-5-'.$val.'</th>';
            }
            foreach ($questionSub6 as $k => $val) {
                $t .= '<th style="background-color:#7ecc61;">Raw-Sub-6-'.$val.'</th>';
            }

            $t .= '<th style="background-color:#b14a4a;">Skor-Sub-1</th>';
            $t .= '<th style="background-color:#c5c455;">Skor-Sub-2</th>';
            $t .= '<th style="background-color:#7ecc61;">Skor-Sub-3</th>';
            $t .= '<th style="background-color:#b14a4a;">Skor-Sub-4</th>';
            $t .= '<th style="background-color:#c5c455;">Skor-Sub-5</th>';
            $t .= '<th style="background-color:#7ecc61;">Skor-Sub-6</th>';

            $t .= '<th style="background-color:#b14a4a;">Norma-Sub-1</th>';
            $t .= '<th style="background-color:#c5c455;">Norma-Sub-2</th>';
            $t .= '<th style="background-color:#7ecc61;">Norma-Sub-3</th>';
            $t .= '<th style="background-color:#b14a4a;">Norma-Sub-4</th>';
            $t .= '<th style="background-color:#c5c455;">Norma-Sub-5</th>';
            $t .= '<th style="background-color:#7ecc61;">Norma-Sub-6</th>';

            $t .= '<th style="background-color:#619ccc;">Kecerdasan Umum</th>';
            $t .= '<th style="background-color:#619ccc;">Daya Analisa</th>';
            $t .= '<th style="background-color:#619ccc;">Abstraksi</th>';
            $t .= '<th style="background-color:#619ccc;">Numerik</th>';
            $t .= '<th style="background-color:#619ccc;">Verbal</th>';
        $t .= '</tr>'; 

        if(count($allUser) > 0){
            $no = 1;

            foreach ($allUser as $k1 => $idUserAssessment_) {
                foreach ($idUserAssessment_ as $idBatch_ => $val) {
                    $thisIdUserAssessment = $val['id_user_assessment'];
                    $thisIdBatch = $idBatch_;
                    $thisIdUserType = $val['type'];
                    $bctThisUser = [];
                    $t .= '<tr>';
                        $t .= '<td>'.$no.'</td>';
                        $t .= '<td>'.$val['nik_or_email'].'</td>';
                        $t .= '<td>'.$val['test_date'].'</td>';
                        $t .= '<td>'.$val['batch'].'</td>';
                        $t .= '<td>'.$val['name'].'</td>';
                        $t .= '<td>'.$val['education'].'</td>';
                        $t .= '<td>'.$val['age'].'</td>';

                    if($userType!='candidate'){
                        $t .= '<td>'.$val['position'].'</td>';
                        $t .= '<td>'.$val['region'].'</td>';
                        $t .= '<td>'.$val['branch'].'</td>';
                    }

                    if(array_key_exists('1', $val['result'])) {
                        $bctThisUser[] = 1;
                        $this_test_1 = $val['result']['1'];
                        foreach ($this_test_1 as $kk => $vv) {
                            $t .= '<td>'.@$vv.'</td>';
                        }
                    }
                    if(array_key_exists('2', $val['result'])) {
                        $bctThisUser[] = 2;
                        $this_test_2 = $val['result']['2'];
                        foreach ($this_test_2 as $kk => $vv) {
                            $t .= '<td>'.@$vv.'</td>';
                        }
                    }
                    if(array_key_exists('3', $val['result'])) {
                        $bctThisUser[] = 3;
                        $this_test_3 = $val['result']['3'];
                        foreach ($this_test_3 as $kk => $vv) {
                            $t .= '<td>'.@$vv.'</td>';
                        }
                    }
                    if(array_key_exists('4', $val['result'])) {
                        $bctThisUser[] = 4;
                        $this_test_4 = $val['result']['4'];
                        foreach ($this_test_4 as $kk => $vv) {
                            $t .= '<td>'.@$vv.'</td>';
                        }
                    }
                    if(array_key_exists('5', $val['result'])) {
                        $bctThisUser[] = 5;
                        $this_test_5 = $val['result']['5'];
                        foreach ($this_test_5 as $kk => $vv) {
                            $t .= '<td>'.@$vv.'</td>';
                        }
                    }
                    if(array_key_exists('6', $val['result'])) {
                        $bctThisUser[] = 6;
                        $this_test_6 = $val['result']['6'];
                        foreach ($this_test_6 as $kk => $vv) {
                            $t .= '<td>'.@$vv.'</td>';
                        }
                    }

                    $thisReq = new Request();
                    $thisReq->merge(['id_user_assessment' => $thisIdUserAssessment]);
                    $thisReq->merge(['id_batch' => $thisIdBatch]);
                    $thisReq->merge(['user_type' => $thisIdUserType]);

                    $bctResult = $this->PsikogramController->bct_result($thisReq);
                    $statusBctUser = [];

                    if(count($bctResult['data']['rawSub1']) > 0){
                        $statusBctUser[] = 1;
                        foreach ($bctResult['data']['rawSub1'] as $k_ => $val_) {
                            $t .= '<td>'.$val_.'</td>';
                        }
                    } else {
                        foreach ($this_test_1 as $kk => $vv) {
                            $t .= '<td>notyet</td>';
                        }
                    }

                    if(count($bctResult['data']['rawSub2']) > 0){
                        $statusBctUser[] = 2;
                        foreach ($bctResult['data']['rawSub2'] as $k_ => $val_) {
                            $t .= '<td>'.$val_.'</td>';
                        }
                    } else {
                        foreach ($this_test_2 as $kk => $vv) {
                            $t .= '<td>notyet</td>';
                        }
                    }

                    if(count($bctResult['data']['rawSub3']) > 0){
                        $statusBctUser[] = 3;
                        foreach ($bctResult['data']['rawSub3'] as $k_ => $val_) {
                            $t .= '<td>'.$val_.'</td>';
                        }
                    } else {
                        foreach ($this_test_3 as $kk => $vv) {
                            $t .= '<td>notyet</td>';
                        }
                    }

                    if(count($bctResult['data']['rawSub4']) > 0){
                        $statusBctUser[] = 4;
                        foreach ($bctResult['data']['rawSub4'] as $k_ => $val_) {
                            $t .= '<td>'.$val_.'</td>';
                        }
                    } else {
                        foreach ($this_test_4 as $kk => $vv) {
                            $t .= '<td>notyet</td>';
                        }
                    }

                    if(count($bctResult['data']['rawSub5']) > 0){
                        $statusBctUser[] = 5;
                        foreach ($bctResult['data']['rawSub5'] as $k_ => $val_) {
                            $t .= '<td>'.$val_.'</td>';
                        }
                    } else {
                        foreach ($this_test_5 as $kk => $vv) {
                            $t .= '<td>notyet</td>';
                        }
                    }

                    if(count($bctResult['data']['rawSub6']) > 0){
                        $statusBctUser[] = 6;
                        foreach ($bctResult['data']['rawSub6'] as $k_ => $val_) {
                            $t .= '<td>'.$val_.'</td>';
                        }
                    } else {
                        foreach ($this_test_6 as $kk => $vv) {
                            $t .= '<td>notyet</td>';
                        }
                    }

                    if(in_array(1, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['scoreSub1'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(2, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['scoreSub2'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(3, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['scoreSub3'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(4, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['scoreSub4'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(5, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['scoreSub5'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(6, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['scoreSub6'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }

                    if(in_array(1, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['normaSub1'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(2, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['normaSub2'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(3, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['normaSub3'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(4, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['normaSub4'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(5, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['normaSub5'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }
                    if(in_array(6, $statusBctUser)){
                        $t .= '<td>'.$bctResult['data']['normaSub6'].'</td>';
                    } else {
                        $t .= '<td>notyet</td>';
                    }

                    if(count($statusBctUser) >= 6){
                        $t .= '<td style="background-color:#00FFFF;">'.$bctResult['data']['kecerdasan_umum'].'</td>';
                        $t .= '<td style="background-color:#00FFFF;">'.$bctResult['data']['daya_analisa'].'</td>';
                        $t .= '<td style="background-color:#00FFFF;">'.$bctResult['data']['abstraksi'].'</td>';
                        $t .= '<td style="background-color:#00FFFF;">'.$bctResult['data']['numerik'].'</td>';
                        $t .= '<td style="background-color:#00FFFF;">'.$bctResult['data']['verbal'].'</td>';
                    } else {
                        $t .= '<td style="background-color:#00FFFF;"></td>';
                        $t .= '<td style="background-color:#00FFFF;"></td>';
                        $t .= '<td style="background-color:#00FFFF;"></td>';
                        $t .= '<td style="background-color:#00FFFF;"></td>';
                        $t .= '<td style="background-color:#00FFFF;"></td>';
                    }

                    $t .= '</tr>';
                    $no++;
                }
            }
        }

        $t .= '</table>';

        if(!($start&&$end) && count($filenameByUser) == 1){
            $filename = 'BCT_Report '.$filenameByUser[0];
        }
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=".$filename.".xls");
        echo $t;
    }

	
	 public function user_identity(Request $req)
    {
        ini_set('max_execution_time', -1);
        try {
            $idCandidateOrIdEmployee = $req->id_user_assessment ?? null ;
            $userType = $req->user_type ?? 'candidate';
            $education = '';
            $userAssessmentName = '';
            $age = '';
            $position = '';
            $region = '';
            $branch = '';
            $nikOrEmail = '';
            $idCompany = '';

            if($userType == 'candidate'){
                $getCandidate = Candidate::getData($idCandidateOrIdEmployee);
                $userAssessmentName = @$getCandidate->name;
                $getEducation = EducationCandidate::getData(null, $idCandidateOrIdEmployee);
                $education = @$getEducation->last_education_code;
                $nikOrEmail = @$getCandidate->email;
                $age = Carbon::parse(@$getCandidate->date_of_birth)->age;
            } else {
                $getEmployee = DB::table('public.hr_employee as he')
                    ->leftJoin('public.master_position_detail as mpd', function ($join) {
                        $join->where('mpd.secondary_position', 0);
                        $join->on('he.id_employee', '=', 'mpd.id_employee');
                        $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                        $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
                    })
                    ->leftJoin('public.master_position_routing as mpr', function ($join) {
                        $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                        $join->on('mpd.id_company', '=', 'mpr.id_company');
                    })
                    ->leftJoin('public.master_job_position as mjp', function ($join) {
                        $join->on('mpr.id_position', '=', 'mjp.id_position');
                        $join->on('mpr.id_company', '=', 'mjp.id_company');
                    })
                    ->leftJoin('public.master_department as md', function ($join) {
                        $join->on('mjp.id_dept', '=', 'md.id_dept');
                        $join->on('mjp.id_company', '=', 'md.id_company');
                    })
                    ->leftJoin('public.master_branch as mb', function ($join) {
                        $join->on('mpd.id_branch', '=', 'mb.id_branch');
                        $join->on('mpd.id_company', '=', 'mb.id_company');
                    })
                    ->leftJoin('public.master_region as mr', function ($join) {
                        $join->on('mb.id_region', '=', 'mr.id_region');
                        $join->on('mb.id_company', '=', 'mr.id_company');
                    })
                    ->leftJoin('public.hr_education_employee as hee', 'hee.id_employee', '=', 'he.id_employee')
                    ->leftJoin('public.master_general_data as mgd', 'mgd.id_general_data', '=', 'hee.id_education_level')
                    ->select('he.*', 'mgd.code as last_education', 'mpr.description as position', 'mr.description as region', 'mb.description as branch')
                    ->where('he.id_employee', $idCandidateOrIdEmployee)
                    ->first();

                $userAssessmentName = @$getEmployee->name;
                $education = @$getEmployee->last_education;
                $nikOrEmail = @$getEmployee->nik_employee;
                $age = Carbon::parse(@$getEmployee->birthdate)->age;
                $position = @$getEmployee->position;
                $region = @$getEmployee->region;
                $branch = @$getEmployee->branch;
                $idCompany = @$getEmployee->id_company;
            }
            $result = [
                'id_user_assessment' => $idCandidateOrIdEmployee,
                'nik_or_email'  => $nikOrEmail,
                'name'  => $userAssessmentName,
                'type'  => $userType,
                'education'  => $education ?? '',
                'age'  => $age,
                'position'  => $position,
                'region'  => $region ?? '',
                'branch'  => $branch ?? '',
                'id_company'  => $idCompany ?? '',
            ];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }
}
?>