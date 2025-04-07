<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class PapiAnswerUser extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.psycho_papicostick_answer_user';
    protected $primaryKey = 'id_papikostick_answer_user';
    protected $fillable = [
        'id_papikostick_question','id_candidate','id_employee','id_batch','value','status','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate') {
        $get = DB::table('web.psycho_papicostick_answer_user as ppau');
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
        $get = DB::table('web.psycho_papicostick_answer_user as ppau');
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
            $get->whereBetween('ppau.creation_date', [$start, $end]);
        }
        $return = $get->get();

        return $return;
    }

    public static function getDataLast($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate') {
        $get = DB::table('web.psycho_papicostick_answer_user as ppau')
            ->leftJoin('public.psycho_master_batch as pmb', 'pmb.id_batch', '=', 'ppau.id_batch')
            ->select('ppau.*', 'pmb.batch_name as batch');
            
        if($idBatch){
            $idBatch = is_array($idBatch) ? $idBatch : [$idBatch];
            $get->whereIn('ppau.id_batch', $idBatch);
        }
        if($idCandidateOrIdEmployee){
            $idCandidateOrIdEmployee = is_array($idCandidateOrIdEmployee) ? $idCandidateOrIdEmployee : [$idCandidateOrIdEmployee];
            if($type=='candidate'){
                $get->whereIn('ppau.id_candidate', $idCandidateOrIdEmployee);
            } else {
                $get->whereIn('ppau.id_employee', $idCandidateOrIdEmployee);
            }
        } else {
            if($type=='candidate'){
                $get->whereNull('ppau.id_employee');
            } else {
                $get->whereNull('ppau.id_candidate');
            }
        }
        $get->orderByDesc('id_papikostick_answer_user');
        $return = $get->first();

        return $return;
    }
}

