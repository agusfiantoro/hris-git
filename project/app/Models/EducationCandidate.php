<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class EducationCandidate extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.hr_education_candidate';
    protected $primaryKey = 'id_education_candidate';
    protected $fillable = [
        'id_candidate','major','education_name','id_education_level','start_year','end_year','education_city','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idEducationCandidate=null, $idCandidate=null) {
        $get = DB::table('web.hr_education_candidate as hec')
            ->leftJoin('public.master_general_data as mgd', 'mgd.id_general_data', '=', 'hec.id_education_level')
            ->select('hec.*', 'mgd.description as last_education', 'mgd.code as last_education_code');
        if($idEducationCandidate){
            $idEducationCandidate = is_array($idEducationCandidate) ? $idEducationCandidate : [$idEducationCandidate];
            $get->whereIn('id_education_candidate', $idEducationCandidate);
        }
        if($idCandidate){
            $idCandidate = is_array($idCandidate) ? $idCandidate : [$idCandidate];
            $get->whereIn('id_candidate', $idCandidate);
        }
        $return = $get->first();

        return $return;
    }

    public static function getAllData($idEducationCandidate=null, $idCandidate=null) {
        $get = DB::table('web.hr_education_candidate as hec')
            ->leftJoin('public.master_general_data as mgd', 'mgd.id_general_data', '=', 'hec.id_education_level')
            ->select('hec.*', 'mgd.description as last_education', 'mgd.code as last_education_code');
        if($idEducationCandidate){
            $idEducationCandidate = is_array($idEducationCandidate) ? $idEducationCandidate : [$idEducationCandidate];
            $get->whereIn('id_education_candidate', $idEducationCandidate);
        }
        if($idCandidate){
            $idCandidate = is_array($idCandidate) ? $idCandidate : [$idCandidate];
            $get->whereIn('id_candidate', $idCandidate);
        }
        $return = $get->get();

        return $return;
    }
}

