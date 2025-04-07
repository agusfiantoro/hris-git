<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BctAnswerUser extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.psycho_bct_answer_user';
    protected $primaryKey = 'id_bct_answer_user';
    protected $fillable = [
        'subtest_name','id_bct_master_question','id_bct_master_answer','id_candidate','id_employee','id_batch','essay_answer','corrected_answer','status','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idBatch=null, $idCandidateOrIdEmployee=null, $subtest='1', $type='candidate') {
        $get = DB::table('web.psycho_bct_answer_user as pbau');
        if($idBatch){
            $idBatch = is_array($idBatch) ? $idBatch : [$idBatch];
            $get->whereIn('id_batch', $idBatch);
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
                $get->whereNull('pbau.id_employee');
            } else {
                $get->whereNull('pbau.id_candidate');
            }
        }
        $get->where('subtest_name', $subtest);
        $return = $get->first();

        return $return;
    }

    public static function getAllData($idBatch=null, $idCandidateOrIdEmployee=null, $subtest='1', $type='candidate', $start=null, $end=null) {
        $get = DB::table('web.psycho_bct_answer_user as pbau')
            ->leftJoin('web.psycho_bct_master_question as pbmq', 'pbmq.id_bct_master_question', '=', 'pbau.id_bct_master_question')
            ->leftJoin('web.psycho_bct_master_answer as pbma', 'pbma.id_bct_master_answer', '=', 'pbau.id_bct_master_answer')
            ->select('pbau.*', 'pbma.is_corrected_answer', 'pbma.answer_description', 'pbma.answer_sequence','pbmq.question_sequence');

        if($idBatch){
            $idBatch = is_array($idBatch) ? $idBatch : [$idBatch];
            $get->whereIn('pbau.id_batch', $idBatch);
        }
        if($idCandidateOrIdEmployee){
            $idCandidateOrIdEmployee = is_array($idCandidateOrIdEmployee) ? $idCandidateOrIdEmployee : [$idCandidateOrIdEmployee];
            if($type=='candidate'){
                $get->whereIn('pbau.id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->whereIn('pbau.id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('pbau.id_employee');
            } else {
                $get->whereNull('pbau.id_candidate');
            }
        }
        if($start && $end){
            $get->whereBetween('pbau.creation_date', [$start, $end]);
        }
        $get->where('pbau.subtest_name', $subtest);
        $return = $get->get();

        return $return;
    }
}

