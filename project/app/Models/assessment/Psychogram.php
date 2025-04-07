<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Psychogram extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.psycho_psychogram_result_report';
    protected $primaryKey = 'id_psychogram_result_report';
    protected $fillable = [
        'id_psychogram_result_report','id_candidate','id_employee','id_batch','id_department','id_job_grade','result_value','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getConclusionMatrix($idDepartment, $idJobGrade, $value) {
        $get = DB::table('web.psycho_master_psychogram_matrix as pmpm')
            ->leftJoin('public.master_general_data as mgd', 'mgd.id_general_data', '=', 'pmpm.id_conclusion')
            ->select('pmpm.*', 'mgd.code')
            ->where(['pmpm.id_department'=>$idDepartment, 'pmpm.id_job_grade'=>$idJobGrade])
            ->where(function ($q) use ($value) {
                $q->where('pmpm.value_min', '<=', $value);
                $q->where('pmpm.value_max', '>=', $value);
            });
        $return = $get->first();
        return $return;
    }

    public static function getPotentialValue($idDepartmentPotential, $idGradePotential) {
        $get = DB::table('web.psycho_master_potential_value as pmpv')
            ->leftJoin('web.psycho_master_potential_aspect as pmpa', 'pmpa.id_master_potential_aspect', '=', 'pmpv.id_master_potential_aspect')
            ->select('pmpv.id_master_potential_aspect', 'pmpa.code', 'pmpv.value', 'pmpv.weight')
            ->where(['pmpv.id_department'=> $idDepartmentPotential, 'pmpv.id_job_grade' => $idGradePotential]);
        $return = $get->get();
        return $return;
    }

    public static function getMasterConclusion($idCompany) {
        $get = DB::table('public.master_general_data as mgd')
            ->select('mgd.*')
            ->where(['mgd.id_company'=>$idCompany, 'id_general_type'=>29]);
        $return = $get->get();
        return $return;
    }

    public static function getConclusionMatrixHitOrMiss($idCompany, $sumOfPotentialValueByHit) {
        $getMasterConclusion = self::getMasterConclusion($idCompany);
        if($getMasterConclusion->count() < 1){
            return null;
        }
        $getMasterConclusion = $getMasterConclusion->keyBy(function ($item) {
            return $item->code;
        });

        if($sumOfPotentialValueByHit >= 11){ // >= 11
            $result = 'disarankan';
        } else if($sumOfPotentialValueByHit >= 8 && $sumOfPotentialValueByHit <= 10){ // 8-10
            $result = 'dipertimbangkan';
        } else {
            $result = 'tidak_disarankan';
        }

        $return = @$getMasterConclusion[self::statusPsychogram($result)];
        return $return;
    }

    public static function statusPsychogram($status) {
        $statusPsychogram = [
            'disarankan'        => 'Recommended',
            'dipertimbangkan'   => 'Considered', 
            'tidak_disarankan'  => 'Not_Recommended',
            'Recommended'       => 'disarankan',
            'Considered'        => 'dipertimbangkan',
            'Not_Recommended'   => 'tidak_disarankan',
        ];
        $return = @$statusPsychogram[$status];
        return $return;
    }

    public static function storePsychogramReport($idCandidateOrIdEmployee, $type='candidate', $idBatch, $idDepartment, $idGrade, $value, $idCompany) {
        $get = DB::table('web.psycho_psychogram_result_report as pprr')
            ->where('id_batch', $idBatch);

        if($type=='candidate'){
            $get->where('id_candidate', $idCandidateOrIdEmployee);
        } else {
            $get->where('id_employee', $idCandidateOrIdEmployee);
        }
        $find = $get->first();
        $store = false;
        $dataStore = [
            'id_batch'  => $idBatch,
            'id_department'  => $idDepartment,
            'id_job_grade'  => $idGrade,
            'result_value'  => $value,
            'id_company'  => $idCompany,
        ];
        if($type=='candidate'){
            $dataStore['id_candidate'] = $idCandidateOrIdEmployee;
        } else {
            $dataStore['id_employee'] = $idCandidateOrIdEmployee;
        }

        if(!$find){
            $dataStore['creation_date'] = date('Y-m-d H:i:s');
            $dataStore['created_by'] = session('id_user') ?? 1;
            $store = self::insert($dataStore);
        } else {
            $dataStore['update_date'] = date('Y-m-d H:i:s');
            $dataStore['updated_by'] = session('id_user') ?? 1;
            $store = self::where('id_psychogram_result_report', $find->id_psychogram_result_report)
                ->update($dataStore);
        }
        return $store;
    }
}

