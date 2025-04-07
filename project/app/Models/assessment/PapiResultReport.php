<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class PapiResultReport extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.psycho_papicostick_result_report';
    protected $primaryKey = 'id_papikostick_result_report';
    protected $fillable = [
        'id_candidate','id_employee','id_batch','value','idx','description','status','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idBatch=null, $idCandidateOrIdEmployee=null, $type='candidate') {
        $get = DB::table('web.psycho_papicostick_result_report as pprr');
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
        $get = DB::table('web.psycho_papicostick_result_report as pprr');
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
            $get->whereBetween('pprr.creation_date', [$start, $end]);
        }
        $get->orderBy('id_papikostick_result_report');
        $return = $get->get();

        return $return;
    }
}

