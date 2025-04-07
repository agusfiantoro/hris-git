<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\assessment\DiscResultReport;
use App\Models\assessment\PapiResultReport;
use App\Models\assessment\KraepelinAnswerUser;
use App\Models\assessment\BctAnswerUser;
use App\Models\assessment\BctUser;
use Carbon\Carbon;
use DB;

class GroupPsychotest extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.psycho_group_psychotest';
    protected $primaryKey = 'id_group_psychotest';
    protected $fillable = [
        'id_batch','id_applied_candidate','id_candidate','id_employee','batch_code','test_date','test_status','expired_date','status','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate') {
        if(!$idCandidateOrIdEmployee){
            return true;
        }
        $get = DB::table('web.psycho_group_psychotest as pgp')
            ->leftJoin('public.psycho_master_batch as pmb', 'pmb.id_batch', '=', 'pgp.id_batch')
            ->select('pgp.*', 'pmb.batch_name', 'pmb.batch_code', 'pmb.location', 'pmb.start_date', 'pmb.end_date')
            ->where(['pgp.id_batch'=>$idBatch]);
            
        if($idCandidateOrIdEmployee){
            if($type=='candidate'){
                $get->where('id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->where('id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('id_employee');
            } else {
                $get->whereNull('id_candidate');
            }
        }
        $getGroup = $get->first();
dd($getGroup);
        return $getGroup;
    }

    public static function checkGroupPsycho($idBatch=null, $idAppliedCandidate=null, $idCandidateOrIdEmployee=null, $idCompany=null, $type='candidate') {

        if(!$idCandidateOrIdEmployee){
            return true;
        }
        $get = DB::table('web.psycho_group_psychotest as pgp')
            ->where('id_batch', $idBatch);

        if($idCandidateOrIdEmployee){
            if($type=='candidate'){
                $get->where('id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->where('id_employee', $idCandidateOrIdEmployee);
            }
        }
        $getGroupPsycho = $get->first();

        if(!$getGroupPsycho){
            $thisIdAppliedCandidate = $idAppliedCandidate;
            if($idAppliedCandidate){
                $getApplied = DB::table('web.hr_applied_candidate')->where('id_applied_candidate', $idAppliedCandidate)->first();
                if($getApplied->id_company != $idCompany){
                    $thisIdAppliedCandidate = null;
                }
            }

            $today = date('Y-m-d');
            $expired = Carbon::parse($today)->addMonths(6)->format('Y-m-d');
            $dataGroup = [
                'id_batch'              => $idBatch,
                'id_applied_candidate'  => $thisIdAppliedCandidate,
                'test_date'             => $today,
                'expired_date'          => $expired,
                'id_company'            => $idCompany,
                'created_by'            => session('id_user_career'),
            ];
            if($idCandidateOrIdEmployee){
                if($type=='candidate'){
                    $dataGroup['id_candidate'] = $idCandidateOrIdEmployee;
                } else {
                    $dataGroup['id_employee'] = $idCandidateOrIdEmployee;
                }
            }
            self::insert($dataGroup);
        } 
        else {
            // Digunakan utk update test_status group psycotest menjadi Completed berdasar user dan batch harus mengecek per masing2 test dulu.
            $showNotification = [];
            //CEK HASIL BCT
            $getBatchUser = self::getData($idBatch, $idCandidateOrIdEmployee, $type);
            $getBctSub1 = BctUser::getData($idCandidateOrIdEmployee, 1, $type, @$getBatchUser->start_date, @$getBatchUser->end_date);
            $getBctSub2 = BctUser::getData($idCandidateOrIdEmployee, 2, $type, @$getBatchUser->start_date, @$getBatchUser->end_date);
            $getBctSub3 = BctUser::getData($idCandidateOrIdEmployee, 3, $type, @$getBatchUser->start_date, @$getBatchUser->end_date);
            $getBctSub4 = BctUser::getData($idCandidateOrIdEmployee, 4, $type, @$getBatchUser->start_date, @$getBatchUser->end_date);
            $getBctSub5 = BctUser::getData($idCandidateOrIdEmployee, 5, $type, @$getBatchUser->start_date, @$getBatchUser->end_date);
            $getBctSub6 = BctUser::getData($idCandidateOrIdEmployee, 6, $type, @$getBatchUser->start_date, @$getBatchUser->end_date);

            //CEK HASIL DISC
            $getDiscProfile = DiscResultReport::getData($idBatch, $idCandidateOrIdEmployee, $type);

            //CEK HASIL PAPI
            $getPapi = PapiResultReport::getAllData($idBatch, $idCandidateOrIdEmployee, $type);

            //CEK KRAEPLIN
            $kraeplinAnswer = KraepelinAnswerUser::getData($idBatch, $idCandidateOrIdEmployee, $type);

            if(!$getBctSub1 || ((int)@$getBctSub1->time_remaining>0)){ $showNotification[] = 'BCT Subtest 1'; }
            if(!$getBctSub2 || ((int)@$getBctSub2->time_remaining>0)){ $showNotification[] = 'BCT Subtest 2'; }
            if(!$getBctSub3 || ((int)@$getBctSub3->time_remaining>0)){ $showNotification[] = 'BCT Subtest 3'; }
            if(!$getBctSub4 || ((int)@$getBctSub4->time_remaining>0)){ $showNotification[] = 'BCT Subtest 4'; }
            if(!$getBctSub5 || ((int)@$getBctSub5->time_remaining>0)){ $showNotification[] = 'BCT Subtest 5'; }
            if(!$getBctSub6 || ((int)@$getBctSub6->time_remaining>0)){ $showNotification[] = 'BCT Subtest 6'; }
            if(!$getDiscProfile){ $showNotification[] = 'DISC Test'; }
            if($getPapi->count() < 1){ $showNotification[] = 'Papikostik Test'; }
            if(!$kraeplinAnswer){ $showNotification[] = 'Kraepelin Test'; }

            if(count($showNotification) < 1){
                $dataUpdate = ['test_status' => 'Completed', 'updated_by'=>session('id_user_career')];
                if($type=='candidate'){
                    self::where('id_batch', $idBatch)->where('id_candidate', $idCandidateOrIdEmployee)->update($dataUpdate);
                } else {
                    self::where('id_batch', $idBatch)->where('id_employee', $idCandidateOrIdEmployee)->update($dataUpdate);
                }
            }
        }
        return true;
    }

    public static function getBatchUserByPeriod($idCandidateOrIdEmployee=null, $type='candidate') {
        if(!$idCandidateOrIdEmployee){
            return true;
        }
        $get = DB::table('public.psycho_batch_participant as pbp')
            ->leftJoin('public.psycho_master_batch as pmb', 'pmb.id_batch', '=', 'pbp.id_batch')
            ->whereRaw('? between pmb.start_date and pmb.end_date', [date('Y-m-d')])
            ->where(['pbp.status'=>'A', 'pmb.status'=>'A']);
            
        if($idCandidateOrIdEmployee){
            if($type=='candidate'){
                $get->where('id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->where('id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('id_employee');
            } else {
                $get->whereNull('id_candidate');
            }
        }
        $get->orderBy('id_batch_participant');
        $getBatchPariticipant = $get->get();

        if($getBatchPariticipant->count() < 1) { return null; }
        else if($getBatchPariticipant->count() == 1) { return $getBatchPariticipant[0]; }
        else {
            $selectThisIdBatch = $getBatchPariticipant[0]->id_batch;
            foreach ($getBatchPariticipant as $k => $val) {
                $getBatchUser = self::getData($val->id_batch, $idCandidateOrIdEmployee, $type);
                if($getBatchUser->test_status != 'Completed'){
                    $selectThisIdBatch = $val->id_batch;
                    break;
                }
            }
            $getSelectedBatch = DB::table('public.psycho_batch_participant as pbp')
                ->leftJoin('public.psycho_master_batch as pmb', 'pmb.id_batch', '=', 'pbp.id_batch')
                ->where('pmb.id_batch', $selectThisIdBatch)
                ->first();
            return $getSelectedBatch;
        }
    }
}

