<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class KraepelinAnswerUser extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.psycho_kraepelin_answer_user';
    protected $primaryKey = 'psycho_kraepelin_answer_user';
    protected $fillable = [
        'id_candidate','id_employee','id_batch','answer','coordinate','column_number','status','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate') {
        $get = DB::table('web.psycho_kraepelin_answer_user as pkau');
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
                $get->whereNull('id_employee');
            } else {
                $get->whereNull('id_candidate');
            }
        }
        $return = $get->first();

        return $return;
    }

    public static function getAllData($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate', $start=null, $end=null) {
        $get = DB::table('web.psycho_kraepelin_answer_user as pkau');
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
                $get->whereNull('id_employee');
            } else {
                $get->whereNull('id_candidate');
            }
        }
        if($start && $end){
            $get->whereBetween('pkau.creation_date', [$start, $end]);
        }
        $return = $get->get();

        return $return;
    }

    public static function getAllDataGroup($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate', $start=null, $end=null) {
        $get = DB::table('web.psycho_kraepelin_answer_user as pkau');

        if($type=='candidate'){
            $get->select('id_candidate','id_batch','id_company');
        } else {
            $get->select('id_employee','id_batch','id_company');
        }

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
                $get->whereNull('id_employee');
            } else {
                $get->whereNull('id_candidate');
            }
        }
        if($start && $end){
            $get->whereBetween('pkau.creation_date', [$start, $end]);
        }

        if($type=='candidate'){
            $get->groupBy('pkau.id_candidate');
            $get->groupBy('pkau.id_batch');
            $get->groupBy('pkau.id_company');
        } else {
            $get->groupBy('pkau.id_employee');
            $get->groupBy('pkau.id_batch');
            $get->groupBy('pkau.id_company');
        }
        $return = $get->get();
        return $return;
    }

    public static function getDataLast($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate') {
        $get = DB::table('web.psycho_kraepelin_answer_user as pkau')
            ->leftJoin('public.psycho_master_batch as pmb', 'pmb.id_batch', '=', 'pkau.id_batch')
            ->select('pkau.*', 'pmb.batch_name as batch');
            
        if($idBatch){
            $idBatch = is_array($idBatch) ? $idBatch : [$idBatch];
            $get->whereIn('pkau.id_batch', $idBatch);
        }
        if($idCandidateOrIdEmployee){
            $idCandidateOrIdEmployee = is_array($idCandidateOrIdEmployee) ? $idCandidateOrIdEmployee : [$idCandidateOrIdEmployee];
            if($type=='candidate'){
                $get->whereIn('pkau.id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->whereIn('pkau.id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('pkau.id_employee');
            } else {
                $get->whereNull('pkau.id_candidate');
            }
        }
        $get->orderByDesc('pkau.id_kreapelin_answer_user');
        $return = $get->first();

        return $return;
    }
}

