<?php

namespace App\Http\Controllers;

use App\Models\HrisModel;
use App\Models\Candidate;
use App\Models\EducationCandidate;
use App\Models\assessment\GroupPsychotest;
use App\Models\assessment\MasterBatch;
use App\Models\assessment\DiscAnswerUser;
use App\Models\assessment\DiscResult;
use App\Models\assessment\DiscResultReport;
use App\Models\assessment\PapiAnswerUser;
use App\Models\assessment\PapiResultReport;
use App\Models\assessment\KraepelinAnswerUser;
use App\Models\assessment\BctAnswerUser;
use App\Models\assessment\BctUser;
use App\Models\assessment\Psychogram;
use App\Models\AppliedCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use DataTables;
use Validator;
use PDF;


class PsikogramController extends Controller
{
    public function papi_result(Request $req)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $idCandidateOrIdEmployee = $req->id_user_assessment;
            $userType = $req->user_type;
            $idBatch = $req->id_batch;

            $getPapi = PapiResultReport::getAllData($idBatch, $idCandidateOrIdEmployee, $userType)->pluck('idx', 'value')->toArray();
            
            DB::commit();
            return ['data' => $getPapi];
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }

    public function disc_result(Request $req)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $idCandidateOrIdEmployee = $req->id_user_assessment;
            $userType = $req->user_type;
            $idBatch = $req->id_batch;

            $getDiscScoring = DiscResult::getAllData($idBatch, $idCandidateOrIdEmployee, $userType)->pluck('value', 'character')->toArray();
            $getDiscProfile = DiscResultReport::getData($idBatch, $idCandidateOrIdEmployee, $userType);
            $d = $getDiscScoring['change_d'];
            $i = $getDiscScoring['change_i'];
            $s = $getDiscScoring['change_s'];
            $c = $getDiscScoring['change_c'];

            $result['scoring'] = ['d'=>$d, 'i'=>$i, 's'=>$s, 'c'=>$c];
            $result['profile'] = $getDiscProfile->character;

            DB::commit();
            return ['data'=>$result];
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }

    public function kraeplin_result(Request $req)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $idCandidateOrIdEmployee = $req->id_user_assessment;
            $userType           = $req->user_type;
            $idBatch            = $req->id_batch;
            $getMax             = 0;
            $getMin             = 0;
            $kraeplinQuestion = DB::table('web.psycho_kraepelin_master_question')->select('coordinate','coordinate_value','answer','column_number')->orderBy('id_kreapelin_question')->get();
            $kraeplinAnswer = KraepelinAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, $userType);
            // $min_pendidikan_lowongan = 'd3';
            $columnResult = [];

            foreach ($kraeplinQuestion as $key => $val) {
                $listQuestions[$val->coordinate] = $val->coordinate_value;
                $listMasterAnswers[$val->coordinate] = $val->answer;
                $columnQuestions[$val->coordinate] = $val->column_number;
            }

            foreach ($kraeplinAnswer as $key => $val) {
                $columnResult[$val->coordinate] = @$val->answer;
            }

            $groupRow = [
                'y_0'=>[], 'y_1'=>[], 'y_2'=>[], 'y_3'=>[], 'y_4'=>[], 'y_5'=>[], 'y_6'=>[], 'y_7'=>[], 'y_8'=>[], 'y_9'=>[],'y_10'=>[],'y_11'=>[],'y_12'=>[],'y_13'=>[],'y_14'=>[],'y_15'=>[],'y_16'=>[],'y_17'=>[],'y_18'=>[],'y_19'=>[],'y_20'=>[],'y_21'=>[],'y_22'=>[],'y_23'=>[],'y_24'=>[],'y_25'=>[],'y_26'=>[],'y_27'=>[],'y_28'=>[],'y_29'=>[],'y_30'=>[],'y_31'=>[],'y_32'=>[],'y_33'=>[],'y_34'=>[],'y_35'=>[]
            ];
            $column = ['aa','ab','ba','bb','ca','cb','da','db','ea','eb','fa','fb','ga','gb','ha','hb','ia','ib','ja','jb','ka','kb','la','lb','ma','mb','na','nb','oa','ob','pa','pb','qa','qb','ra','rb','sa','sb','ta','tb','ua','ub','va','vb','wa','wb','xa','xb','ya','yb'];
            $row = [35,34,33,32,31,30,29,28,27,26,25,24,23,22,21,20,19,18,17,16,15,14,13,12,11,10,9,8,7,6,5,4,3,2,1];
            $countRow = count($row);
            $allAnswerGroupRow = [];
            $notFill = [];
            $wrongAnswer = [];
            $rightAnswer = [];
            $columnLabel = [];
            $chartLabel = [];
            $chartValue = [];
            $allStatusAnswer = [];

            foreach ($column as $k_col => $v_col) {
                $arrAnswer = [];
                $allAnswerThisColumn = [];
                $statusAnswerThisColumn = [];

                foreach ($row as $k_row => $v_row) {
                    $coordinate = $v_col.$v_row;
                    if($v_row > 1){
                        if(@$columnResult[$coordinate]!='' && @$columnResult[$coordinate]==@$listMasterAnswers[$coordinate]){
                            //jika jawaban benar
                            $arrAnswer[] = 1;
                            $rightAnswer[] = 1;
                            $allAnswerThisColumn[] = 1;
                            $statusAnswerThisColumn[] = true;
                        } 
                        else {
                            if(@$columnResult[$coordinate]!='' && @$columnResult[$coordinate]!=@$listMasterAnswers[$coordinate]){
                                //jika jawaban salah
                                $wrongAnswer[] = 1;
                                $allAnswerThisColumn[] = 1;
                                $statusAnswerThisColumn[] = false;
                            } else {
                                //jika jawaban tidak diisi
                                $notFill[] = 1;
                            }
                        } 
                    }
                }
                if(!in_array($k_col, [0,1,2,47,48,49])){
                    //yg dipakai penilaian adalah selain kolom : 1,2,3,48,49,50
                    $allStatusAnswer[] = $statusAnswerThisColumn;
                }
                $chartValue[] = count($allAnswerThisColumn);
                $chartLabel[] = $k_col+1;
                $countAnswer = count($arrAnswer);
                $groupRow['y_'.$countAnswer][] = 1;
            }
            foreach ($row as $k_row => $v_row) {
                $countGroupRow = count($groupRow['y_'.$v_row]);
                if($countGroupRow > 0){
                    $allAnswerGroupRow[] = $v_row;
                }
            }
            //mencari nilai benar tertinggi dan nilai benar terendah dalam semua kolom. (TIDAK PAKAI INI)
            // $getMax = collect($allAnswerGroupRow)->max();
            // $getMin = collect($allAnswerGroupRow)->min();

            $sortStatusCount = collect($allStatusAnswer)->sortByDesc(function($item, $key) {
                return count($item);
            });
            $sortStatusIndex = array_values($sortStatusCount->toArray());
            $trueAnswerByIndex = [];
            foreach ($sortStatusIndex as $k => $val) {
                if(@$val[0] == false){
                    continue;
                } else {
                    // if(count($trueAnswerByIndex) < 2){
                        if(count($trueAnswerByIndex) == 0){
                            $trueAnswerByIndex[] = count($val);
                            break;
                        } 
                    //     else {
                    //         if(count($val) == @$trueAnswerByIndex[0]){
                    //             continue;
                    //         } else {
                    //             $trueAnswerByIndex[] = count($val);
                    //         }
                    //     }
                    // }
                }
            }
            krsort($sortStatusIndex);
            foreach ($sortStatusIndex as $k => $val) {
                if(@$val[0] == false){
                    continue;
                } else {
                    if(count($trueAnswerByIndex) == 1){
                        $trueAnswerByIndex[] = count($val);
                        break;
                    } 
                }
            }

            $getMax = $trueAnswerByIndex[0] ?? 0;
            $getMin = $trueAnswerByIndex[1] ?? 0;

            $result['max'] = $getMax ?? 0;
            $result['min'] = $getMin ?? 0;
            $result['right_answer'] = count($rightAnswer);
            $result['wrong_answer'] = count($wrongAnswer);
            $result['skipped'] = count($notFill);
            $result['chartLabel'] = json_encode($chartLabel);
            $result['chartValue'] = json_encode($chartValue);
            $result['result'] = ($getMax==0 && $getMin==0) ? '50' : abs((int)$getMax - (int)$getMin);
            //Jika getMax && getMin == 0 maka nilai result 50 karena semakin besar angka semakin besar ketimpangan batas atas dan bawah sehingga dianggap tidak stabil
            DB::commit();
            return ['data'=>$result];
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }

    public function bct_result(Request $req)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $calculate = self::score_test($req);

            $req->sub   = 'sub1';
            $req->value = $calculate['data']['sub1']['benar'];
            $normaTest1 = self::norma_test($req)['data']['ss'];

            $req->sub   = 'sub2';
            $req->value = $calculate['data']['sub2']['benar'];
            $normaTest2 = self::norma_test($req)['data']['ss'];

            $req->sub   = 'sub3';
            $req->value = $calculate['data']['sub3']['benar'];
            $normaTest3 = self::norma_test($req)['data']['ss'];

            $req->sub   = 'sub4';
            $req->value = $calculate['data']['sub4']['benar'];
            $normaTest4 = self::norma_test($req)['data']['ss'];

            $req->sub   = 'sub5';
            $req->value = $calculate['data']['sub5']['benar'];
            $normaTest5 = self::norma_test($req)['data']['ss'];

            $req->sub   = 'sub6';
            $req->value = $calculate['data']['sub6']['benar'];
            $normaTest6 = self::norma_test($req)['data']['ss'];

            $ssToPsikogram = [0=>'1', 1=>'1', 2=>'1', 3=>'1', 4=>'1', 5=>'2', 6=>'2', 7=>'2', 8=>'2', 9=>'2', 10=>'3', 11=>'3', 12=>'3', 13=>'3', 14=>'3', 15=>'4', 16=>'4', 17=>'4', 18=>'4', 19=>'5', 20=>'5', 21=>'5'];

            $_kecerdasanUmum = round(($normaTest1 + $normaTest2 + $normaTest3 + $normaTest4 + $normaTest5 + $normaTest6) / 6);
            $_dayaAnalisa = round(($normaTest2 + $normaTest3 + $normaTest4 + $normaTest5) / 4);
            $_abstraksi = round($normaTest6 / 1);
            $_numerik = round(($normaTest2 + $normaTest3) / 2);
            $_verbal = round(($normaTest4 + $normaTest5) / 2);

            $kecerdasanUmum = $ssToPsikogram[$_kecerdasanUmum];
            $dayaAnalisa = $ssToPsikogram[$_dayaAnalisa];
            $abstraksi = $ssToPsikogram[$_abstraksi];
            $numerik = $ssToPsikogram[$_numerik];
            $verbal = $ssToPsikogram[$_verbal];

            $result['kecerdasan_umum'] = $kecerdasanUmum;
            $result['daya_analisa'] = $dayaAnalisa;
            $result['abstraksi'] = $abstraksi;
            $result['numerik'] = $numerik;
            $result['verbal'] = $verbal;
            $result['rawSub1'] = $calculate['data']['rawSub1'];
            $result['rawSub2'] = $calculate['data']['rawSub2'];
            $result['rawSub3'] = $calculate['data']['rawSub3'];
            $result['rawSub4'] = $calculate['data']['rawSub4'];
            $result['rawSub5'] = $calculate['data']['rawSub5'];
            $result['rawSub6'] = $calculate['data']['rawSub6'];
            $result['resSub1'] = $calculate['data']['resSub1'];
            $result['resSub2'] = $calculate['data']['resSub2'];
            $result['resSub3'] = $calculate['data']['resSub3'];
            $result['resSub4'] = $calculate['data']['resSub4'];
            $result['resSub5'] = $calculate['data']['resSub5'];
            $result['resSub6'] = $calculate['data']['resSub6'];
            $result['scoreSub1'] = $calculate['data']['sub1']['benar'];
            $result['scoreSub2'] = $calculate['data']['sub2']['benar'];
            $result['scoreSub3'] = $calculate['data']['sub3']['benar'];
            $result['scoreSub4'] = $calculate['data']['sub4']['benar'];
            $result['scoreSub5'] = $calculate['data']['sub5']['benar'];
            $result['scoreSub6'] = $calculate['data']['sub6']['benar'];
            $result['normaSub1'] = $normaTest1;
            $result['normaSub2'] = $normaTest2;
            $result['normaSub3'] = $normaTest3;
            $result['normaSub4'] = $normaTest4;
            $result['normaSub5'] = $normaTest5;
            $result['normaSub6'] = $normaTest6;
            DB::commit();
            return ['data' => $result];
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }

    public function score_test(Request $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $idCandidateOrIdEmployee = $request->id_user_assessment;
            $userType = $request->user_type;
            $idBatch = $request->id_batch;

            $getMasterKey = DB::table('web.psycho_bct_master_question as pbmq')
                ->leftJoin('web.psycho_bct_master_answer as pbma', 'pbma.id_bct_master_question', '=', 'pbmq.id_bct_master_question')
                ->select('pbmq.subtest_name', 'pbmq.question_sequence', 'pbma.answer_sequence', 'pbma.answer_description', 'pbma.is_corrected_answer')
                ->where('pbma.is_corrected_answer', true)
                ->orderByRaw("pbmq.subtest_name::int")
                ->orderByRaw("pbmq.question_sequence::int")
                ->get();

            foreach ($getMasterKey as $k => $val) {
                if($val->subtest_name == '1'){
                    $masterKeysub1[$val->question_sequence] = $val->answer_sequence;
                }
                if($val->subtest_name == '2'){
                    $masterKeysub2[$val->question_sequence] = $val->answer_sequence;
                }
                if($val->subtest_name == '3'){
                    $masterKeysub3[$val->question_sequence] = $val->answer_sequence;
                }
                if($val->subtest_name == '4'){
                    $masterKeysub4[$val->question_sequence] = $val->answer_description;
                }
                if($val->subtest_name == '5'){
                    $masterKeysub5[$val->question_sequence] = $val->answer_description;
                }
                if($val->subtest_name == '6'){
                    $masterKeysub6[$val->question_sequence] = $val->answer_sequence;
                }
            }

            $resultSub1 = ['benar'=>0, 'salah'=>0];
            $resultSub2 = ['benar'=>0, 'salah'=>0];
            $resultSub3 = ['benar'=>0, 'salah'=>0];
            $resultSub4 = ['benar'=>0, 'salah'=>0];
            $resultSub5 = ['benar'=>0, 'salah'=>0];
            $resultSub6 = ['benar'=>0, 'salah'=>0];
            $rawSub1    = [];
            $rawSub2    = [];
            $rawSub3    = [];
            $rawSub4    = [];
            $rawSub5    = [];
            $rawSub6    = [];
            $resSub1    = [];
            $resSub2    = [];
            $resSub3    = [];
            $resSub4    = [];
            $resSub5    = [];
            $resSub6    = [];

            $getAnswerSub1 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 1, $userType);
            $getAnswerSub2 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 2, $userType);
            $getAnswerSub3 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 3, $userType);
            $getAnswerSub4 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 4, $userType);
            $getAnswerSub5 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 5, $userType);
            $getAnswerSub6 = BctAnswerUser::getAllData($idBatch, $idCandidateOrIdEmployee, 6, $userType);

            if($getAnswerSub1->count() > 0){
                $answerSub1 = $getAnswerSub1->pluck('answer_sequence','question_sequence');
                $benar = [];    $salah = [];
                foreach ($masterKeysub1 as $key => $answerKey) {
                    if(@$answerSub1[$key] == $answerKey){ $benar[] = 1; $rawSub1[$key] = 1;} 
                    else { $salah[] = 1; $rawSub1[$key] = 0;}
                    $resSub1[$key] = @$answerSub1[$key];
                }
                $resultSub1 = ['benar'=>count($benar), 'salah'=>count($salah)];
            }
            if($getAnswerSub2->count() > 0){
                $answerSub2 = $getAnswerSub2->pluck('answer_sequence','question_sequence');
                $benar = [];    $salah = [];
                foreach ($masterKeysub2 as $key => $answerKey) {
                    if(@$answerSub2[$key] == $answerKey){ $benar[] = 1; $rawSub2[$key] = 1;} 
                    else { $salah[] = 1; $rawSub2[$key] = 0;}
                    $resSub2[$key] = @$answerSub2[$key];
                }
                $resultSub2 = ['benar'=>count($benar), 'salah'=>count($salah)];
            }
            if($getAnswerSub3->count() > 0){
                $answerSub3 = $getAnswerSub3->pluck('answer_sequence','question_sequence');
                $benar = [];    $salah = [];
                foreach ($masterKeysub3 as $key => $answerKey) {
                    if(@$answerSub3[$key] == $answerKey){ $benar[] = 1; $rawSub3[$key] = 1;} 
                    else { $salah[] = 1; $rawSub3[$key] = 0;}
                    $resSub3[$key] = @$answerSub3[$key];
                }
                $resultSub3 = ['benar'=>count($benar), 'salah'=>count($salah)];
            }
            if($getAnswerSub4->count() > 0){
                $answerSub4 = $getAnswerSub4->pluck('essay_answer','question_sequence');
                $benar = [];    $salah = [];
                foreach ($masterKeysub4 as $key => $answerKey) {
                    if(@$answerSub4[$key] == $answerKey){ $benar[] = 1; $rawSub4[$key] = 1;} 
                    else { $salah[] = 1; $rawSub4[$key] = 0;}
                    $resSub4[$key] = @$answer->essay_answer;
                }
                $resultSub4 = ['benar'=>count($benar), 'salah'=>count($salah)];
            }
            if($getAnswerSub5->count() > 0){
                $answerSub5 = $getAnswerSub5->pluck('essay_answer','question_sequence');
                $benar = [];    $salah = [];
                foreach ($masterKeysub5 as $key => $answerKey) {
                    if(@$answerSub5[$key] == $answerKey){ $benar[] = 1; $rawSub5[$key] = 1;} 
                    else { $salah[] = 1; $rawSub5[$key] = 0;}
                    $resSub5[$key] = @$answer->essay_answer;
                }
                $resultSub5 = ['benar'=>count($benar), 'salah'=>count($salah)];
            }
            if($getAnswerSub6->count() > 0){
                $answerSub6 = $getAnswerSub6->pluck('answer_sequence','question_sequence');
                $benar = [];    $salah = [];
                foreach ($masterKeysub6 as $key => $answerKey) {
                    if(@$answerSub6[$key] == $answerKey){ $benar[] = 1; $rawSub6[$key] = 1;} 
                    else { $salah[] = 1; $rawSub6[$key] = 0;}
                    $resSub6[$key] = @$answerSub6[$key];
                }
                $resultSub6 = ['benar'=>count($benar), 'salah'=>count($salah)];
            }

            $allResult['sub1'] = $resultSub1;
            $allResult['sub2'] = $resultSub2;
            $allResult['sub3'] = $resultSub3;
            $allResult['sub4'] = $resultSub4;
            $allResult['sub5'] = $resultSub5;
            $allResult['sub6'] = $resultSub6;
            $allResult['rawSub1'] = $rawSub1;
            $allResult['rawSub2'] = $rawSub2;
            $allResult['rawSub3'] = $rawSub3;
            $allResult['rawSub4'] = $rawSub4;
            $allResult['rawSub5'] = $rawSub5;
            $allResult['rawSub6'] = $rawSub6;
            $allResult['resSub1'] = $resSub1;
            $allResult['resSub2'] = $resSub2;
            $allResult['resSub3'] = $resSub3;
            $allResult['resSub4'] = $resSub4;
            $allResult['resSub5'] = $resSub5;
            $allResult['resSub6'] = $resSub6;

            $return = ['data'=>$allResult];
            DB::commit();
            return $return;
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }

    public function norma_test(Request $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $subtest    = $request->sub;
            $value      = $request->value;
            $psikogram  = 0;
            $nilaiSs    = 0;
            $kategori   = [
                1 => 'KURANG SEKALI',
                2 => 'KURANG',
                3 => 'CUKUP',
                4 => 'BAIK SEKALI',
                5 => 'ISTIMEWA',
            ];

            if($subtest == 'sub1'){
                if($value >= 0 && $value <= 5){
                    $psikogram = 1;
                } else if($value >= 6 && $value <= 12){
                    $psikogram = 2;
                } else if($value >= 13 && $value <= 19){
                    $psikogram = 3;
                } else if($value >= 20 && $value <= 25){
                    $psikogram = 4;
                } else if($value >= 26){
                    $psikogram = 5;
                }

                $ss = ['0'=>'0', '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'3', '5'=>'4', '6'=>'5', '7'=>'5', '8'=>'6', '9'=>'7', '10'=>'8', '11'=>'8', '12'=>'9', '13'=>'10', '14'=>'10', '15'=>'11', '16'=>'12', '17'=>'13', '18'=>'13', '19'=>'15', '20'=>'15', '21'=>'15', '22'=>'16', '23'=>'17', '24'=>'18', '25'=>'18', '26'=>'19'];
                $nilaiSs = $ss[$value];
                $namaKategori = $kategori[$psikogram];
            }

            if($subtest == 'sub2'){
                if($value >= 0 && $value <= 4){
                    $psikogram = 1;
                } else if($value >= 5 && $value <= 10){
                    $psikogram = 2;
                } else if($value >= 11 && $value <= 15){
                    $psikogram = 3;
                } else if($value >= 16 && $value <= 19){
                    $psikogram = 4;
                } else if($value >= 20){
                    $psikogram = 5;
                }

                $ss = ['0'=>'0', '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'8', '10'=>'9', '11'=>'10', '12'=>'11', '13'=>'12', '14'=>'13', '15'=>'14', '16'=>'15', '17'=>'16', '18'=>'17', '19'=>'18', '20'=>'19'];
                $nilaiSs = $ss[$value];
                $namaKategori = $kategori[$psikogram];
            }

            if($subtest == 'sub3'){
                if($value >= 0 && $value <= 1){
                    $psikogram = 1;
                } else if($value >= 2 && $value <= 6){
                    $psikogram = 2;
                } else if($value >= 7 && $value <= 12){
                    $psikogram = 3;
                } else if($value >= 13 && $value <= 16){
                    $psikogram = 4;
                } else if($value >= 17){
                    $psikogram = 5;
                }

                $ss = ['0'=>'4', '1'=>'4', '2'=>'5', '3'=>'6', '4'=>'7', '5'=>'8', '6'=>'9', '7'=>'10', '8'=>'11', '9'=>'12', '10'=>'12', '11'=>'13', '12'=>'14', '13'=>'15', '14'=>'16', '15'=>'17', '16'=>'18', '17'=>'19', '18'=>'20', '19'=>'20', '20'=>'21', '21'=>'21', '22'=>'21', '23'=>'21', '24'=>'21', '25'=>'21', '26'=>'21'];
                $nilaiSs = $ss[$value];
                $namaKategori = $kategori[$psikogram];
            }

            if($subtest == 'sub4'){
                if($value >= 0 && $value <= 3){
                    $psikogram = 1;
                } else if($value >= 4 && $value <= 8){
                    $psikogram = 2;
                } else if($value >= 9 && $value <= 14){
                    $psikogram = 3;
                } else if($value >= 15 && $value <= 18){
                    $psikogram = 4;
                } else if($value >= 19){
                    $psikogram = 5;
                }

                $ss = ['0'=>'1', '1'=>'2', '2'=>'3', '3'=>'4', '4'=>'5', '5'=>'6', '6'=>'7', '7'=>'8', '8'=>'9', '9'=>'10', '10'=>'11', '11'=>'11', '12'=>'12', '13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '17'=>'17', '18'=>'18', '19'=>'19', '20'=>'20'];
                $nilaiSs = $ss[$value];
                $namaKategori = $kategori[$psikogram];
            }

            if($subtest == 'sub5'){
                if($value >= 0 && $value <= 3){
                    $psikogram = 1;
                } else if($value >= 4 && $value <= 14){
                    $psikogram = 2;
                } else if($value >= 15 && $value <= 25){
                    $psikogram = 3;
                } else if($value >= 26 && $value <= 30){
                    $psikogram = 4;
                }

                $ss = ['0'=>'3', '1'=>'3', '2'=>'4', '3'=>'4', '4'=>'5', '5'=>'5', '6'=>'6', '7'=>'6', '8'=>'7', '9'=>'7', '10'=>'8', '11'=>'8', '12'=>'9', '13'=>'9', '14'=>'9', '15'=>'10', '16'=>'10', '17'=>'11', '18'=>'11', '19'=>'12', '20'=>'12', '21'=>'13', '22'=>'13', '23'=>'14', '24'=>'14', '25'=>'14', '26'=>'15', '27'=>'15', '28'=>'16', '29'=>'16', '30'=>'17'];
                $nilaiSs = $ss[$value];
                $namaKategori = $kategori[$psikogram];
            }

            if($subtest == 'sub6'){
                if($value >= 0 && $value <= 3){
                    $psikogram = 1;
                } else if($value >= 4 && $value <= 8){
                    $psikogram = 2;
                } else if($value >= 9 && $value <= 14){
                    $psikogram = 3;
                } else if($value >= 15 && $value <= 18){
                    $psikogram = 4;
                } else if($value >= 19){
                    $psikogram = 5;
                }

                $ss = ['0'=>'2', '1'=>'2', '2'=>'3', '3'=>'4', '4'=>'5', '5'=>'6', '6'=>'7', '7'=>'8', '8'=>'9', '9'=>'10', '10'=>'10', '11'=>'11', '12'=>'12', '13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '17'=>'17', '18'=>'18', '19'=>'18', '20'=>'19'];
                $nilaiSs = $ss[$value];
                $namaKategori = $kategori[$psikogram];
            }
            
            $result = ['psikogram' => $psikogram, 'ss' => $nilaiSs, 'kategori'=>$namaKategori];
            $return = ['data'=>$result];
            DB::commit();
            return $return;
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }

    public function psikogram_generator(Request $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $bctResult = self::bct_result($request);
            $discResult = self::disc_result($request);
            $papiResult = self::papi_result($request);
            $kraeplinResult = self::kraeplin_result($request);

            //intelektual
            $kecerdasanUmum     = 1;
            $dayaAnalisa        = 1;
            $dayaAbstraksi      = 1;
            $penalaranVerbal    = 1;
            $kemampuanNumerik   = 1;
            //sikap dan cara kerja
            $orientasiHasil     = 1;
            $stabilitasKerja    = 1;
            $sistematikaKerja   = 1;
            //kepribadian
            $kerjaSama          = 1;
            $keterampilanInterpersonal = 1;
            $penyesuaianDiri    = 1;
            //kepemimpinan
            $kemampuanMemimpin  = 1;
            $pengambilanKeputusan = 1;


            //============================= KEMAMPUAN INTELEKTUAL =================================
            if(count(@$bctResult['data']) > 0){
                $kecerdasanUmum = @$bctResult['data']['kecerdasan_umum'];
                $dayaAnalisa = @$bctResult['data']['daya_analisa'];
                $dayaAbstraksi = @$bctResult['data']['abstraksi'];
                $penalaranVerbal = @$bctResult['data']['verbal'];
                $kemampuanNumerik = @$bctResult['data']['numerik'];
            }

            $summaryKecerdasanUmum = $kecerdasanUmum;
            $summaryDayaAnalisa = $dayaAnalisa;
            $summaryDayaAbstraksi = $dayaAbstraksi;
            $summaryPenalaranVerbal = $penalaranVerbal;
            $summaryKemampuanNumerik = $kemampuanNumerik;

            //============================= SIKAP & CARA KERJA =================================
            $dataPapi_n = 1;
            $dataPapi_a = 1;
            $dataPapi_c = 1;
            $dataPapi_b = 1;
            $dataPapi_s = 1;
            $dataPapi_z = 1;
            $dataPapi_p = 1;
            $dataPapi_l = 1;
            $dataPapi_i = 1;
            $dataFromDisc = '';

            $summaryOrientasiHasil = 1;
            $summarySistematikaKerja = 1;
            $summaryKerjaSama = 1;
            $summaryKeterampilanInterpersonal = 1;
            $summaryPenyesuaianDiri = 1;
            $summaryKemampuanMemimpin = 1;
            $summaryPengambilanKeputusan = 1;

            if(count(@$papiResult['data']) > 0){
                $dataFromPapi = @$papiResult['data'];
                $dataPapi_n = $dataFromPapi['N'];
                $dataPapi_a = $dataFromPapi['A'];
                $dataPapi_c = $dataFromPapi['C'];
                $dataPapi_b = $dataFromPapi['B'];
                $dataPapi_s = $dataFromPapi['S'];
                $dataPapi_z = $dataFromPapi['Z'];
                $dataPapi_p = $dataFromPapi['P'];
                $dataPapi_l = $dataFromPapi['L'];
                $dataPapi_i = $dataFromPapi['I'];
            }

            if(count(@$discResult['data']) > 0){
                $dataFromDisc = $discResult['data']['profile'];

                //====================== ORIENTASI HASIL ===================================
                
                if(in_array($dataFromDisc, ['dc','cd']) && ($dataPapi_n>7 && $dataPapi_a>8) ){
                    $summaryOrientasiHasil = 5;
                } 
                else if(in_array($dataFromDisc, ['dc','cd']) && ($dataPapi_n>7 || $dataPapi_a>8) ){
                    $summaryOrientasiHasil = 4;
                } 
                else if(in_array($dataFromDisc, ['d', 'c', 'dc', 'cd', 'di', 'id', 'ds', 'sd', 'ci', 'ic', 'cs', 'sc', 'dci', 'dcs', 'dic', 'dis', 'dsc', 'dsi', 'cdi', 'cds', 'csd', 'csi', 'cid', 'cis', 'sdi', 'sdc', 'sid', 'sic', 'scd', 'sci', 'ids', 'idc', 'isd', 'isc', 'icd', 'ics']) && ($dataPapi_n>5 && $dataPapi_a>7) ){
                    $summaryOrientasiHasil = 4;
                }
                else if(in_array($dataFromDisc, ['d', 'c', 'dc', 'cd', 'di', 'id', 'ds', 'sd', 'ci', 'ic', 'cs', 'sc', 'dci', 'dcs', 'dic', 'dis', 'dsc', 'dsi', 'cdi', 'cds', 'csd', 'csi', 'cid', 'cis', 'sdi', 'sdc', 'sid', 'sic', 'scd', 'sci', 'ids', 'idc', 'isd', 'isc', 'icd', 'ics']) && ($dataPapi_n>5 || $dataPapi_a>7) ){
                    $summaryOrientasiHasil = 3;
                }
                else if(in_array($dataFromDisc, ['d', 'c', 'dc', 'cd', 'di', 'id', 'ds', 'sd', 'ci', 'ic', 'cs', 'sc', 'dci', 'dcs', 'dic', 'dis', 'dsc', 'dsi', 'cdi', 'cds', 'csd', 'csi', 'cid', 'cis', 'sdi', 'sdc', 'sid', 'sic', 'scd', 'sci', 'ids', 'idc', 'isd', 'isc', 'icd', 'ics']) && ($dataPapi_n>2 && $dataPapi_a>3) ){
                    $summaryOrientasiHasil = 3;
                } 
                else if($dataPapi_n>5 && $dataPapi_a>5){
                    $summaryOrientasiHasil = 3;
                }
                else if(in_array($dataFromDisc, ['d', 'c', 'dc', 'cd', 'di', 'id', 'ds', 'sd', 'ci', 'ic', 'cs', 'sc', 'dci', 'dcs', 'dic', 'dis', 'dsc', 'dsi', 'cdi', 'cds', 'csd', 'csi', 'cid', 'cis', 'sdi', 'sdc', 'sid', 'sic', 'scd', 'sci', 'ids', 'idc', 'isd', 'isc', 'icd', 'ics']) && $dataPapi_a>1) {
                    $summaryOrientasiHasil = 2;
                }
                else {
                    $summaryOrientasiHasil = 1;
                }

                
                //====================== SISTEMATIKA KERJA ===================================

                if(in_array($dataFromDisc, ['sc','cs']) && $dataPapi_c>=7 ){
                    $summarySistematikaKerja = 5;
                }
                else if((strpos($dataFromDisc, 'c')!==false || strpos($dataFromDisc, 's')!==false) && $dataPapi_c>=5){
                    $summarySistematikaKerja = 4;
                }
                else if((strpos($dataFromDisc, 'c')!==false) && $dataPapi_c>=2){
                    $summarySistematikaKerja = 3;
                }
                else if((strpos($dataFromDisc, 's')!==false) && $dataPapi_c>=3){
                    $summarySistematikaKerja = 3;
                }
                else if((strpos($dataFromDisc, 'c')!==false || strpos($dataFromDisc, 's')!==false) && $dataPapi_c>=1){
                    $summarySistematikaKerja = 2;
                }
                else if($dataPapi_c>=2 ){
                    $summarySistematikaKerja = 2;
                } else {
                    $summarySistematikaKerja = 1;
                }


                //====================== KERJA SAMA ===================================

                if(in_array($dataFromDisc, ['is','si']) && $dataPapi_b==8 ){
                    $summaryKerjaSama = 5;
                }
                else if(in_array($dataFromDisc, ['is','si']) && $dataPapi_b==7 ){
                    $summaryKerjaSama = 4;
                }
                else if((strpos($dataFromDisc, 's')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_b>=6 ){
                    $summaryKerjaSama = 3;
                }
                else if((strpos($dataFromDisc, 's')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_b>=1 ){
                    $summaryKerjaSama = 2;
                }
                else if($dataPapi_b>=3 ){
                    $summaryKerjaSama = 2;
                }
                else {
                    $summaryKerjaSama = 1;
                }


                //====================== KETERAMPILAN PERSONAL ===================================

                if(in_array($dataFromDisc, ['is','si']) && $dataPapi_s==8 ){
                    $summaryKeterampilanInterpersonal = 5;
                }
                else if(in_array($dataFromDisc, ['is','si']) && $dataPapi_s==7 ){
                    $summaryKeterampilanInterpersonal = 4;
                }
                else if((strpos($dataFromDisc, 's')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_s>=5 ){
                    $summaryKeterampilanInterpersonal = 3;
                }
                else if((strpos($dataFromDisc, 's')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_s>=1 ){
                    $summaryKeterampilanInterpersonal = 2;
                }
                else if($dataPapi_s>=3 ){
                    $summaryKeterampilanInterpersonal = 2;
                }
                else {
                    $summaryKeterampilanInterpersonal = 1;
                }


                //====================== PENYESUAIAN DIRI ===================================

                if(in_array($dataFromDisc, ['is','si']) && ($dataPapi_z==7 || $dataPapi_z==6)){
                    $summaryPenyesuaianDiri = 5;
                }
                else if(in_array($dataFromDisc, ['is','si']) && ($dataPapi_z==5 || $dataPapi_z==4)){
                    $summaryPenyesuaianDiri = 4;
                }
                else if((strpos($dataFromDisc, 's')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_z>=4 ){
                    $summaryPenyesuaianDiri = 3;
                }
                else if((strpos($dataFromDisc, 's')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_z>=2 ){
                    $summaryPenyesuaianDiri = 2;
                }
                else if($dataPapi_z>=3 ){
                    $summaryPenyesuaianDiri = 2;
                }
                else {
                    $summaryPenyesuaianDiri = 1;
                }


                //====================== KEMAMPUAN MEMIPIN ===================================

                if(in_array($dataFromDisc, ['di','id']) && $dataPapi_l>=7 && $dataPapi_p>=7){
                    $summaryKemampuanMemimpin = 5;
                }
                else if(in_array($dataFromDisc, ['di','id']) && ($dataPapi_l>=7 || $dataPapi_p>=7)){
                    $summaryKemampuanMemimpin = 4;
                }
                else if(strpos($dataFromDisc, 'd')!==false && $dataPapi_l>=6 && $dataPapi_p>=6){
                    $summaryKemampuanMemimpin = 4;
                }
                else if(strpos($dataFromDisc, 'd')!==false && ($dataPapi_l>=6 || $dataPapi_p>=6)){
                    $summaryKemampuanMemimpin = 3;
                }
                else if((strpos($dataFromDisc, 'd')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_p>=5){
                    $summaryKemampuanMemimpin = 3;
                }
                else if((strpos($dataFromDisc, 'd')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_p>=2){
                    $summaryKemampuanMemimpin = 2;
                }
                else if($dataPapi_p>=4){
                    $summaryKemampuanMemimpin = 2;
                }
                else {
                    $summaryKemampuanMemimpin = 1;
                }


                //====================== PENGAMBILAN KEPUTUSAN ===================================

                if($dataFromDisc=='d' && ($dataPapi_i==7 || $dataPapi_i==6)){
                    $summaryPengambilanKeputusan = 5;
                }
                else if((strpos($dataFromDisc, 'd')!==false && $dataPapi_i>=8)){
                    $summaryPengambilanKeputusan = 4;
                }
                else if((strpos($dataFromDisc, 'i')!==false && $dataPapi_i>=9)){
                    $summaryPengambilanKeputusan = 4;
                }
                else if((strpos($dataFromDisc, 'd')!==false || strpos($dataFromDisc, 'i')!==false) && $dataPapi_i>=4){
                    $summaryPengambilanKeputusan = 3;
                }
                else if($dataPapi_i>=2){
                    $summaryPengambilanKeputusan = 2;
                }
                else {
                    $summaryPengambilanKeputusan = 1;
                }

            }


            //============================= STABILITAS KERJA =================================

            if(count(@$kraeplinResult['data']) > 0){
                if(@$kraeplinResult['data']['result'] <= 5){
                    $summaryStabilitasKerja = 5;
                } 
                else if(@$kraeplinResult['data']['result'] == 6){
                    $summaryStabilitasKerja = 4;
                }
                else if(@$kraeplinResult['data']['result'] == 7){
                    $summaryStabilitasKerja = 3;
                }
                else if(@$kraeplinResult['data']['result'] == 8){
                    $summaryStabilitasKerja = 2;
                }
                else if(@$kraeplinResult['data']['result'] >= 9){
                    $summaryStabilitasKerja = 1;
                }
            }

            $result['kecerdasan_umum']              = (int)$summaryKecerdasanUmum;
            $result['daya_analisa']                 = (int)$summaryDayaAnalisa;
            $result['daya_abstraksi']               = (int)$summaryDayaAbstraksi;
            $result['kemampuan_numerik']            = (int)$summaryKemampuanNumerik;
            $result['penalaran_verbal']             = (int)$summaryPenalaranVerbal;
            $result['orientasi_hasil']              = (int)$summaryOrientasiHasil;
            $result['stabilitas_kerja']             = (int)$summaryStabilitasKerja;
            $result['sistematika_kerja']            = (int)$summarySistematikaKerja;
            $result['kerja_sama']                   = (int)$summaryKerjaSama;
            $result['keterampilan_interpersonal']   = (int)$summaryKeterampilanInterpersonal;
            $result['penyesuaian_diri']             = (int)$summaryPenyesuaianDiri;
            $result['kemampuan_memimpin']           = (int)$summaryKemampuanMemimpin;
            $result['pengambilan_keputusan']        = (int)$summaryPengambilanKeputusan;

            $return = ['data'=>$result];
            DB::commit();
            return $return;
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }

    public function summary(Request $req)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $idCandidateOrIdEmployee = (int)$req->id_user_assessment ?? null;
            $userType = $req->user_type ?? null;
            $idDepartment = (int)$req->id_department ?? null;
            $idGrade = (int)$req->id_grade ?? null;
            $idBatch = (int)$req->id_batch ?? null;
            $requestType = $req->request_type ?? 'pdf';
            $update = $req->modify === true || $req->modify === "true";
            $resultFilter = [
                'identity' => [
                    'id_user_assessment' => '',
                    'nik_or_email' => '',
                    'name' => '',
                    'type' => '',
                    'education' => '',
                    'test_date' => '',
                    'batch' => '',
                    'age' => '',
                    'region'  => '',
                    'branch'  => '',
                    'id_company'  => '',
                ],
                'result' => [
                    'status'      => false,
                    'profil' => '',
                    'nilai_aspek' => []
                ],    
            ];
            $parameter = [];

            if(!$idCandidateOrIdEmployee || !$userType || !$idDepartment || !$idGrade || !$idBatch){
                //jika parameter id_user_assessment, user_type, id_department,id_grade,id_batch tidak diisi
                $html = '';
                $idUserAndType = false;
                
                if(!$idDepartment){
                    $parameter[] = 'Departemen ';
                }
                if(!$idGrade){
                    $parameter[] = 'Job Grade ';
                }
                if(!$idBatch){
                    $parameter[] = 'Assessment Batch ';
                }
                if(!$idCandidateOrIdEmployee || !$userType){
                    //Jika salah satu parameter id_user_assessment atau tipe user tidak ada
                    if(!$idCandidateOrIdEmployee){
                        $parameter[] = 'Id User Assessment ';
                    }
                    if(!$userType){
                        $parameter[] = 'Tipe User ';
                    }
                } else {
                    //Jika id_user_assessment dan tipe user ada
                    $getIdentity = self::user_identity($req);
                    $idUserAndType = false;
                    $resultFilter['identity']['id_user_assessment'] = $getIdentity['id_user_assessment'];
                    $resultFilter['identity']['name'] = $getIdentity['name'];
                    $resultFilter['identity']['type'] = $getIdentity['type'];
                    $resultFilter['identity']['education'] = $getIdentity['education'];
                    $resultFilter['identity']['id_company'] = $getIdentity['id_company'];
                }
                $resultFilter['result']['status'] = false;
                $resultFilter['result']['message'] = 'Parameter ('.implode(', ', $parameter).') belum ada';

                if($requestType == 'raw'){
                    return $resultFilter;
                } else if($requestType == 'table'){
                    $result = $resultFilter;
                    echo view('assessment.psikogram_not_yet', compact('result'))->render();die;
                } else {
                    $result = $resultFilter;
                    $html = view('assessment.psikogram_not_yet', compact('result'))->render();
                    $fileName = 'Psikogram '.@$result['nama'].'.pdf';
                    $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');
                    return $pdf->download($fileName);
                }
            } 
            else {
                $getIdentity = self::user_identity($req);
                if(!is_null($getIdentity['name'])){
                    //Jika ada parameter id_user_assessment dan benar terdftr di sistem
                    $getResult = self::result($req);
                } else {
                    //Jika ada parameter id_user_assessment namun angkanya ngawur atau tidak terdftr di sistem
                    $getResult['data'] = $resultFilter;
                    $getResult['status'] = false;
                }
                
                $getBatchUser = GroupPsychotest::getData($idBatch, $idCandidateOrIdEmployee, $userType);
                $idCompany = @$getBatchUser->id_company;

                if($requestType == 'raw'){                    
                    if($getResult['status'] == true){
                        $thisPsychoResult = $getResult['data']['result'];
                        $currentMethodPsycho = $thisPsychoResult['current_method_psychogram'];
                        $psychoReportValue = $thisPsychoResult['psychogram'][$currentMethodPsycho]['result_count'];
                        if($update) {
                            $storeReport = Psychogram::storePsychogramReport($idCandidateOrIdEmployee, $userType, $idBatch, $idDepartment, $idGrade, $psychoReportValue, $idCompany);
                        }
                    }
                    DB::commit();
                    return $getResult['data'];
                } else if($requestType == 'table'){
                    $result = $getResult['data'];
                    if($getResult['status'] == true){
                        $logo = false;
                        $thisGrade = '';
                        $thisDepartment = '';

                        $thisPsychoResult = $getResult['data']['result'];
                        $currentMethodPsycho = $thisPsychoResult['current_method_psychogram'];
                        $psychoReportValue = $thisPsychoResult['psychogram'][$currentMethodPsycho]['result_count'];
                        if($update) {
                            $storeReport = Psychogram::storePsychogramReport($idCandidateOrIdEmployee, $userType, $idBatch, $idDepartment, $idGrade, $psychoReportValue, $idCompany);
                        }
                        $getMasterJobGrade = HrisModel::getMasterJobGrade($idGrade);
                        $getMasterDepartment = HrisModel::getMasterDepartment($idDepartment);
                        if($getMasterJobGrade->count()>0){ $thisGrade = $getMasterJobGrade[0]->description; }
                        if($getMasterDepartment->count()>0){ $thisDepartment = $getMasterDepartment[0]->description; }

                        DB::commit();
                        echo view('assessment.psikogram_table', compact('result','logo', 'thisGrade', 'thisDepartment'))->render();die;
                    } else {
                        echo view('assessment.psikogram_not_yet', compact('result'))->render();die;
                    }
                } else {
                    $result = $getResult['data'];
                    if($getResult['status'] == true){
                        $thisGrade = '';
                        $thisDepartment = '';

                        $thisPsychoResult = $getResult['data']['result'];
                        $currentMethodPsycho = $thisPsychoResult['current_method_psychogram'];
                        $psychoReportValue = $thisPsychoResult['psychogram'][$currentMethodPsycho]['result_count'];
                        if($update) {
                            $storeReport = Psychogram::storePsychogramReport($idCandidateOrIdEmployee, $userType, $idBatch, $idDepartment, $idGrade, $psychoReportValue, $idCompany);
                        }
                        $getMasterJobGrade = HrisModel::getMasterJobGrade($idGrade);
                        $getMasterDepartment = HrisModel::getMasterDepartment($idDepartment);
                        if($getMasterJobGrade->count()>0){ $thisGrade = $getMasterJobGrade[0]->description; }
                        if($getMasterDepartment->count()>0){ $thisDepartment = $getMasterDepartment[0]->description; }

                        if($idCompany == 2){
                            $companyLogo = asset('dist/img').'/orbiz.png';
                        } else {
                            $companyLogo = asset('dist/img').'/borwita-logo.png';
                        }
                        DB::commit();
                        
                        $logo = 'data:image/jpg;base64,'.base64_encode(file_get_contents($companyLogo));
                        $html = view('assessment.psikogram_table', compact('result','logo', 'thisGrade', 'thisDepartment'))->render();
                    } else {
                        $html = view('assessment.psikogram_not_yet', compact('result'))->render();
                    }
                    $fileName = 'Psikogram '.@$result['identity']['name'].'.pdf';
                    $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');
                    return $pdf->download($fileName);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['status'=>false, 'message'=>$e->getMessage(), 'data'=>null];
        }
    }

    public function result(Request $req)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $idCandidateOrIdEmployee = $req->id_user_assessment;
            $userType = $req->user_type;
            $idDepartment = $req->id_department;
            $idGrade = $req->id_grade;
            $idBatch = $req->id_batch;

            $showNotification = [];

            //CEK HASIL BCT
            $getBatchUser = GroupPsychotest::getData($idBatch, $idCandidateOrIdEmployee, $userType);
            $dateEndBatch = Carbon::parse(@$getBatchUser->end_date)->addDays(1)->format('Y-m-d');
            $idCompany = @$getBatchUser->id_company;
            
            $getBctSub1 = BctUser::getData($idCandidateOrIdEmployee, 1, $userType, @$getBatchUser->start_date, $dateEndBatch);
            $getBctSub2 = BctUser::getData($idCandidateOrIdEmployee, 2, $userType, @$getBatchUser->start_date, $dateEndBatch);
            $getBctSub3 = BctUser::getData($idCandidateOrIdEmployee, 3, $userType, @$getBatchUser->start_date, $dateEndBatch);
            $getBctSub4 = BctUser::getData($idCandidateOrIdEmployee, 4, $userType, @$getBatchUser->start_date, $dateEndBatch);
            $getBctSub5 = BctUser::getData($idCandidateOrIdEmployee, 5, $userType, @$getBatchUser->start_date, $dateEndBatch);
            $getBctSub6 = BctUser::getData($idCandidateOrIdEmployee, 6, $userType, @$getBatchUser->start_date, $dateEndBatch);

            //CEK HASIL DISC
            $getDiscProfile = DiscResultReport::getData($idBatch, $idCandidateOrIdEmployee, $userType);

            //CEK HASIL PAPI
            $getPapi = PapiResultReport::getAllData($idBatch, $idCandidateOrIdEmployee, $userType);

            //CEK KRAEPLIN
            $kraeplinAnswer = KraepelinAnswerUser::getData($idBatch, $idCandidateOrIdEmployee, $userType);

            //Identitas
            $getIdentity = self::user_identity($req);
            $result = [
                'identity' => [
                    'id_user_assessment' => $getIdentity['id_user_assessment'],
                    'nik_or_email' => $getIdentity['nik_or_email'],
                    'name' => $getIdentity['name'],
                    'type' => $getIdentity['type'],
                    'education' => $getIdentity['education'],
                    'test_date' => '',
                    'batch' => @$getBatchUser->batch_name.' ('.@$getBatchUser->location.')',
                    'age' => $getIdentity['age'],
                    'region'  => $getIdentity['region'],
                    'branch'  => $getIdentity['branch'],
                    'id_company'  => $getIdentity['id_company'],
                ],
                'result' => [
                    'status'      => false,
                    'profil' => '',
                    'nilai_aspek' => [],
                    'summary_test' => []
                ],    
            ];
            $summaryTest['disc'] = true;
            $summaryTest['papi'] = true;
            $summaryTest['kraepelin'] = true;
            $summaryTest['bct'] = true;

            if(!$getBctSub1 || ((int)@$getBctSub1->time_remaining>0)){ $showNotification[] = 'BCT Subtest 1'; $summaryTest['bct'] = false; }
            if(!$getBctSub2 || ((int)@$getBctSub2->time_remaining>0)){ $showNotification[] = 'BCT Subtest 2'; $summaryTest['bct'] = false; }
            if(!$getBctSub3 || ((int)@$getBctSub3->time_remaining>0)){ $showNotification[] = 'BCT Subtest 3'; $summaryTest['bct'] = false; }
            if(!$getBctSub4 || ((int)@$getBctSub4->time_remaining>0)){ $showNotification[] = 'BCT Subtest 4'; $summaryTest['bct'] = false; }
            if(!$getBctSub5 || ((int)@$getBctSub5->time_remaining>0)){ $showNotification[] = 'BCT Subtest 5'; $summaryTest['bct'] = false; }
            if(!$getBctSub6 || ((int)@$getBctSub6->time_remaining>0)){ $showNotification[] = 'BCT Subtest 6'; $summaryTest['bct'] = false; }
            if(!$getDiscProfile){ $showNotification[] = 'DISC Test'; $summaryTest['disc'] = false; }
            if($getPapi->count() < 1){ $showNotification[] = 'Papikostik Test'; $summaryTest['papi'] = false; }
            if(!$kraeplinAnswer){ $showNotification[] = 'Kraepelin Test'; $summaryTest['kraepelin'] = false; }

            if(count($showNotification) > 0){
                throw new \Exception("Hasil tes berikut belum selesai :<br>".collect($showNotification)->implode("<br>"), 404);
            }

            $psikogramResult = self::psikogram_generator($req);
            $sumOfPotentialValueByWeight = 0;
            $sumOfPotentialValueByHit = 0;
            $currentMethod = 'weight_scale'; //optional ['weight_scale', 'hit_or_miss']
            $result['result']['div_disarankan'] = '';
            $result['result']['div_dipertimbangkan'] = '';
            $result['result']['div_tidak_disarankan'] = '';
            $result['result']['div_nilai_aspek'] = [];

            //Cek Tanggal Test berdasar group psychotest id_batch per user
            $getGroupPsycho = GroupPsychotest::getData($idBatch, $idCandidateOrIdEmployee, $userType);
            $result['identity']['test_date'] = ($getGroupPsycho) ? Carbon::parse(@$getGroupPsycho->test_date)->translatedFormat('d F Y') : '';
            $result['identity']['batch'] = ($getGroupPsycho) ? @$getGroupPsycho->batch_name.' ('.@$getGroupPsycho->location.')' : '';


            $mapping = self::mappping();
            $idDepartmentPotential = array_key_exists($idDepartment, $mapping['data']['department']) ? $mapping['data']['department'][$idDepartment] : $idDepartment;
            $idGradePotential = array_key_exists($idGrade, $mapping['data']['grade']) ? $mapping['data']['grade'][$idGrade] : $idGrade;

            $potentialValue = Psychogram::getPotentialValue($idDepartmentPotential, $idGradePotential);
            $potentialValue = $potentialValue->keyBy(function ($item) {
                return $item->code;
            });

            $parameterSummaryPsychogram = [
                'result_name' => null,
                'result_count' => null,
                'result_matrix' => [
                    'id_psychogram_matrix' => null,
                    'id_conclusion' => null,
                ],
            ];
            $summaryPsychogram['hit_or_miss'] = $parameterSummaryPsychogram;
            $summaryPsychogram['weight_scale'] = $parameterSummaryPsychogram;

            if(count($psikogramResult['data']) > 0){
                foreach ($potentialValue as $k => $val) {
                    $thisPotentialValue = (float)$val->value;
                    $thisPotentialWeight = (float)$val->weight;

                    $sumOfPotentialValueByWeight += ($psikogramResult['data'][$val->code] * $thisPotentialWeight);
                    if($psikogramResult['data'][$val->code] >= $thisPotentialValue){
                        $sumOfPotentialValueByHit += 1;
                    }
                }
                $expectationValue = $potentialValue->pluck('value','code')->toArray();
                $discResult = self::disc_result($req);
                $result['result']['profil'] = strtoupper($discResult['data']['profile']);
                $result['result']['nilai_aspek'] = $psikogramResult['data'];

                $aspek = ['kecerdasan_umum','daya_analisa','daya_abstraksi','kemampuan_numerik','penalaran_verbal','orientasi_hasil','stabilitas_kerja','sistematika_kerja','kerja_sama','keterampilan_interpersonal','penyesuaian_diri','kemampuan_memimpin','pengambilan_keputusan'];

                $resultAspek = [];
                foreach ($aspek as $key => $val) {
                    $skorAspek = [];
                    for ($i=1; $i < 6; $i++) { 
                        if($psikogramResult['data'][$val] == $i){
                            $skorAspek[$val.'_'.$i]['actual'] = '✔'; //SYmbol Cheklist
                        } else {
                            $skorAspek[$val.'_'.$i]['actual'] = '';
                        }

                        if($expectationValue[$val] == $i){
                            $skorAspek[$val.'_'.$i]['expect'] = ''; //for background table 'background-green'
                        } else {
                            $skorAspek[$val.'_'.$i]['expect'] = '';
                        }
                    }
                    $resultAspek[$val] = $skorAspek;
                }
                $result['result']['div_nilai_aspek'] = $resultAspek;

                $psychoMatrixHitOrMiss = Psychogram::getConclusionMatrixHitOrMiss(@$idCompany, $sumOfPotentialValueByHit);
                if(!$psychoMatrixHitOrMiss){
                    $psychoMatrixHitOrMiss = Psychogram::getConclusionMatrixHitOrMiss(1, $sumOfPotentialValueByHit);
                }

                $psychoMatrixWeightScale = Psychogram::getConclusionMatrix($idDepartment, $idGrade, $sumOfPotentialValueByWeight);
                if(!$psychoMatrixWeightScale){
                    //Jika kondisi where pakai id_departmen dn jobgrade yg asli dr asal company karyawan tidak ada di matrix maka pakai kondisi id yg dari bcp dengan dpartemen dn job grade yg sama
                    $psychoMatrixWeightScale = Psychogram::getConclusionMatrix($idDepartmentPotential, $idGradePotential, $sumOfPotentialValueByWeight);
                }

                //======= HIT OR MISS =========
                $summaryPsychogram['hit_or_miss']['result_name'] = Psychogram::statusPsychogram(@$psychoMatrixHitOrMiss->code);
                $summaryPsychogram['hit_or_miss']['result_count'] = $sumOfPotentialValueByHit;
                $summaryPsychogram['hit_or_miss']['result_matrix']['id_conclusion'] = @$psychoMatrixHitOrMiss->id_general_data;


                //======= WEIGHT SCALE =========
                $summaryPsychogram['weight_scale']['result_name'] = Psychogram::statusPsychogram(@$psychoMatrixWeightScale->code);
                $summaryPsychogram['weight_scale']['result_count'] = $sumOfPotentialValueByWeight;
                $summaryPsychogram['weight_scale']['result_matrix']['id_psychogram_matrix'] = @$psychoMatrixWeightScale->id_psychogram_matrix;
                $summaryPsychogram['weight_scale']['result_matrix']['id_conclusion'] = @$psychoMatrixWeightScale->id_conclusion;

                if($currentMethod == 'weight_scale'){
                    $kesimpulan = Psychogram::statusPsychogram(@$psychoMatrixWeightScale->code);
                    $psychoReportValue = $sumOfPotentialValueByWeight;
                } else {
                    $kesimpulan = Psychogram::statusPsychogram(@$psychoMatrixHitOrMiss->code);
                    $psychoReportValue = $sumOfPotentialValueByHit;
                }
                $result['result']['div_'.$kesimpulan] = '✔'; //SYmbol Cheklist
            }
            $result['result']['summary_test'] = $summaryTest;
            $result['result']['status'] = true;
            $result['result']['current_method_psychogram'] = $currentMethod;
            $result['result']['psychogram'] = $summaryPsychogram;

            DB::commit();
            return ['status'=>true, 'message'=>'success', 'data'=>$result];
        } catch (\Exception $e) {
            DB::rollback();
            $result['result']['message'] = $e->getMessage();
            $result['result']['summary_test'] = $summaryTest;
            return ['status'=>false, 'message'=>$e->getMessage(), 'data'=>$result];
        }
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

    public static function mappping()
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $masterDepartment = DB::table('public.master_department')->get();
            $getDepartmentBCP = DB::table('public.master_department')->where(['id_company'=>1, 'status'=>'A'])->get();
            $department = [];
            //departmen yg selain BCP mappingannya menggunakan departemen BCP (utk get departemen dari tabel psycho_master_potential_value)
            foreach ($getDepartmentBCP as $k => $val) {
                $codeBCP = trim($val->department_code);
                $idDeptBCP = $val->id_dept;
                foreach ($masterDepartment as $k_ => $val_) {
                    $code = trim($val_->department_code);
                    $idDept = $val_->id_dept;
                    if($code == $codeBCP){
                        $department[$idDept] = $idDeptBCP;
                    }
                }
            }

            $masterGrade = DB::table('public.master_job_grade')->get();
            $getGradeBCP = DB::table('public.master_job_grade')->where(['id_company'=>1, 'status'=>'A'])->get();
            $jobGrade = [];
            //grade yg selain BCP mappingannya menggunakan grade BCP (utk get grade dari tabel psycho_master_potential_value)
            foreach ($getGradeBCP as $k => $val) {
                $descriptionBCP = trim($val->description);
                $idJobGradeBCP = $val->id_job_grade;
                foreach ($masterGrade as $k_ => $val_) {
                    $description = trim($val_->description);
                    $idJobGrade = $val_->id_job_grade;
                    if($description == $descriptionBCP){
                        $jobGrade[$idJobGrade] = $idJobGradeBCP;
                    }
                }
            }

            $result['department'] = $department;
            $result['grade'] = $jobGrade;

            DB::commit();
            return ['data' => $result];
        } catch (\Exception $e) {
            DB::rollback();
            return ['data'=>[]];
        }
    }
}
