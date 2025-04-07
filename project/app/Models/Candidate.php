<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use DB;

class Candidate extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'web.hr_candidate';
    protected $primaryKey = 'id_candidate';
    protected $fillable = [
        'id_candidate','name','photo_candidate','identification_number','address_home','idcard_address','id_country','gender','id_religion','marital','place_of_birth','id_country_of_birth','ptkp_status','id_candidate_user','status','mobile_phone','emergency_phone','emergency_contact','email','about_me','duration_work','estimation_join','hired_date','join_date','additional_note','cv_upload','app_letter_upload','info_1','info_2','info_3','info_4','info_5','info_6','info_7','info_8','info_9','info_10','link_facebook','link_instagram','link_twitter','link_linkedin', 'date_of_birth', 'driving_license_number', 'taxpayer_identification_number','salary_type','creation_date','update_date','created_by','updated_by','is_profil_completed'
    ];

    public static function getData($idCandidate=null, $idCandidateUser=null) {
        $get = DB::table('web.hr_candidate as hc');
        if($idCandidate){
            $idCandidate = is_array($idCandidate) ? $idCandidate : [$idCandidate];
            $get->whereIn('id_candidate', $idCandidate);
        }
        if($idCandidateUser){
            $idCandidateUser = is_array($idCandidateUser) ? $idCandidateUser : [$idCandidateUser];
            $get->whereIn('id_candidate_user', $idCandidateUser);
        }
        $return = $get->first();

        if($return){
            $photoPath = 'public/candidate_photo/';
            $cvPath = 'public/curriculum_vitae/';
            $appLetterPath = 'public/application_letter/';

            if(Storage::exists($photoPath.@$return->photo_candidate)) {
                @$return->photo_candidate_path = asset('storage/candidate_photo/'.@$return->photo_candidate);
            } else {
                @$return->photo_candidate_path = asset('dist/img/ava.png');
            }

            if(!is_null(@$return->cv_upload) && Storage::exists($cvPath.@$return->cv_upload)) {
                @$return->cv_upload_path = asset('storage/curriculum_vitae/'.@$return->cv_upload);
            } else {
                @$return->cv_upload_path = null;
            }

            if(!is_null(@$return->app_letter_upload) && Storage::exists($appLetterPath.@$return->app_letter_upload)) {
                @$return->app_letter_upload_path = asset('storage/application_letter/'.@$return->app_letter_upload);
            } else {
                @$return->app_letter_upload_path = null;
            }
        }
        return $return;
    }

    public static function getAllData($idCandidate=null, $idCandidateUser=null) {
        $get = DB::table('web.hr_candidate as hc');
        if($idCandidate){
            $idCandidate = is_array($idCandidate) ? $idCandidate : [$idCandidate];
            $get->whereIn('id_candidate', $idCandidate);
        }
        if($idCandidateUser){
            $idCandidateUser = is_array($idCandidateUser) ? $idCandidateUser : [$idCandidateUser];
            $get->whereIn('id_candidate_user', $idCandidateUser);
        }
        $return = $get->get();

        if($return->count() > 0){
            foreach ($return as $k => $val) {
                $photoPath = 'public/candidate_photo/';
                $cvPath = 'public/curriculum_vitae/';
                $appLetterPath = 'public/application_letter/';

                if(Storage::exists($photoPath.@$val->photo_candidate)) {
                    @$return[$k]->photo_candidate_path = asset('storage/candidate_photo/'.@$val->photo_candidate);
                } else {
                    @$return[$k]->photo_candidate_path = asset('dist/img/ava.png');
                }

                if(Storage::exists($photoPath.@$val->cv_upload)) {
                    @$return[$k]->cv_upload_path = asset('storage/curriculum_vitae/'.@$val->cv_upload);
                } else {
                    @$return[$k]->cv_upload_path = null;
                }

                if(Storage::exists($photoPath.@$val->app_letter_upload)) {
                    @$return[$k]->app_letter_upload_path = asset('storage/application_letter/'.@$val->app_letter_upload);
                } else {
                    @$return[$k]->app_letter_upload_path = null;
                }
            }
        }
        return $return;
    }
}

