<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BctUser extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.psycho_subtest_timer';
    protected $primaryKey = 'id_subtest_timer';
    protected $fillable = [
        'subtest_name','time_remaining','id_candidate','id_employee','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idCandidateOrIdEmployee=null, $subtest='1', $type='candidate', $start, $end) {
        $get = DB::table('web.psycho_subtest_timer as pst');
        if($idCandidateOrIdEmployee){
            $idCandidateOrIdEmployee = is_array($idCandidateOrIdEmployee) ? $idCandidateOrIdEmployee : [$idCandidateOrIdEmployee];
            if($type=='candidate'){
                $get->whereIn('id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->whereIn('id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('id_employee');
            } else {
                $get->whereNull('id_candidate');
            }
        }
        $get->where('subtest_name', $subtest);
        $get->where(function ($where) use($start, $end){
            $where->whereBetween('creation_date', [$start, $end]);
            $where->orWhereBetween('update_date', [$start, $end]);
        });
        $return = $get->first();

        return $return;
    }

    public static function getAllData($idCandidateOrIdEmployee=null, $subtest='1', $type='candidate', $start, $end) {
        $get = DB::table('web.psycho_subtest_timer as pst');
        if($idCandidateOrIdEmployee){
            $idCandidateOrIdEmployee = is_array($idCandidateOrIdEmployee) ? $idCandidateOrIdEmployee : [$idCandidateOrIdEmployee];
            if($type=='candidate'){
                $get->whereIn('pst.id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->whereIn('pst.id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('pst.id_employee');
            } else {
                $get->whereNull('pst.id_candidate');
            }
        }
        $get->where('pst.subtest_name', $subtest);
        $get->where(function ($where) use($start, $end){
            $where->whereBetween('creation_date', [$start, $end]);
            $where->orWhereBetween('update_date', [$start, $end]);
        });
        $return = $get->get();

        return $return;
    }

    public static function getDataLast($idBatch=null, $idCandidateOrIdEmployee=null, $subtest='1', $type='candidate') {
        $get = DB::table('web.psycho_bct_answer_user as pbau')
            ->leftJoin('public.psycho_master_batch as pmb', 'pmb.id_batch', '=', 'pbau.id_batch')
            ->select('pbau.*', 'pmb.batch_name as batch');

        if($idBatch){
            $idBatch = is_array($idBatch) ? $idBatch : [$idBatch];
            $get->whereIn('pbau.id_batch', $idBatch);
        }
        if($idCandidateOrIdEmployee){
            $idCandidateOrIdEmployee = is_array($idCandidateOrIdEmployee) ? $idCandidateOrIdEmployee : [$idCandidateOrIdEmployee];
            if($type=='candidate'){
                $get->whereIn('id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->whereIn('id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('id_employee');
            } else {
                $get->whereNull('id_candidate');
            }
        }
        $get->where('subtest_name', $subtest);
        $get->orderByDesc('id_bct_answer_user');
        $return = $get->first();

        return $return;
    }

    public static function getMasterQuestion($subtest='1') {
        $getQuestion = DB::table('web.psycho_bct_master_question as pbmq')
            ->leftJoin('public.master_general_data as mgd', 'mgd.id_general_data', '=', 'pbmq.question_type')
            ->select('pbmq.*', 'mgd.code as question_type')
            ->where(['pbmq.subtest_name' => $subtest]);

        $return = $getQuestion->get();
        return $return;
    }

    public static function getMasterAnswer($subtest='1') {
        $getMasterAnswer = DB::table('web.psycho_bct_master_answer as pbma')
            ->leftJoin('web.psycho_bct_master_question as pbmq', 'pbmq.id_bct_master_question', '=', 'pbma.id_bct_master_question')
            ->select('pbma.*', 'pbmq.question_sequence')
            ->where(['pbma.subtest_name' => $subtest]);

        $return = $getMasterAnswer->get();
        return $return;
    }
}

