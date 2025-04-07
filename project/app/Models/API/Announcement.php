<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee\EmployeeSetting\RelationAnnouncementUser;
use Carbon\Carbon;

class Announcement extends Model {
	
	public static function getAnnouncement($employee, $idAnnouncement=null) {
        $idEmployee    = $employee->id_employee;
        $idUser        = $employee->id_user;
        $idCompany     = $employee->id_company;
        $today         = date('Y-m-d');

        if(!$idAnnouncement){
            $announcement = DB::table('hr_employee_anouncement')
                ->select('id_announcement')
                ->where('id_company', $idCompany)
                ->where('status', 'A')
                ->where('published', true)
                ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
                ->get();
            $idAnnouncement = $announcement->pluck('id_announcement')->all();

            $hasBeenRead = DB::table('relation_announcement_users')
                ->select('id_announcement')
                ->where('id_company', $idCompany)
                ->where('id_user', $idUser)
                ->get();
            $idAnnouncementRead = $hasBeenRead->pluck('id_announcement')->all();
            $announcementToRead = collect($idAnnouncement)->diff(collect($idAnnouncementRead));
        } 
        else {
            $announcementToRead = [$idAnnouncement];
        }

        $showAnnouncement = DB::table('hr_employee_anouncement as hea')
            ->select('hea.id_announcement', 'hea.reference_number', 'hea.description', 'hea.start_date', 'hea.end_date', 'mgd.code as type', 'hea.attachment_type', 'hea.attachment', 'hea.content_letter', 'hea.creation_date')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hea.id_anouncement_type')
            ->whereIn('hea.id_announcement', $announcementToRead)
            ->get();

        $showAnnouncement->mapWithKeys(function ($val){
            $thisAttachment = null;
            if(is_null($val->attachment_type) && !is_null($val->attachment)){
                $pathFile = 'public/upload/announcement/'. $val->attachment;
                // if (Storage::exists($pathFile)) {
                    $thisAttachment = url('project/storage/app/'.$pathFile);
                // } 
            } else {
                if(!is_null($val->attachment_type)){
                    if($val->attachment_type == 'image'){
                        $thisAttachment = "data:image;base64,".$val->attachment;
                    } else {
                        $thisAttachment = "data:application/pdf;base64,".$val->attachment;
                    }
                }
            }
            $val->attachment = $thisAttachment;

            $val->content_letter = strip_tags(htmlspecialchars_decode($val->content_letter));
            unset($val->attachment_type);
            return $val;
        });

        return $showAnnouncement;
    }

    public static function getAllAnnouncement($idCompany, $idUser, $start, $end, $idAnnouncement=null, $type=null) {
        $get = DB::table('hr_employee_anouncement as hea')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hea.id_anouncement_type')
            ->leftJoin('relation_announcement_users as rau', function ($join) use ($idUser){
                $join->on('rau.id_announcement', '=', 'hea.id_announcement');
                $join->where('rau.id_user', '=', $idUser);
            })
            ->select('hea.id_announcement', 'hea.reference_number', 'hea.description', 'hea.start_date', 'hea.end_date', 'mgd.code as type_code', 'mgd.description as type_description', 'hea.attachment_type', 'hea.attachment', 'hea.content_letter', 'rau.id_announcement as is_already_read', 'hea.creation_date')
            ->where('hea.id_company', $idCompany)
            ->where('hea.status', 'A')
            ->where('hea.published', true)
            ->whereBetween('hea.start_date', [$start, $end]);

        if($idAnnouncement){
            $get->where('hea.id_announcement', $idAnnouncement);
        }
        if($type){
            $get->where('mgd.code', $type);
        }

        $get->orderBy('hea.creation_date', 'desc');
        $result = $get->get();
        $result->mapWithKeys(function ($val){
            $val->is_already_read = !is_null($val->is_already_read) ? true : false;

            $thisAttachment = null;
            if(is_null($val->attachment_type) && !is_null($val->attachment)){
                $pathFile = 'public/upload/announcement/'. $val->attachment;
                // if (Storage::exists($pathFile)) {
                    $thisAttachment = url('project/storage/app/'.$pathFile);
                // } 
            } else {
                if(!is_null($val->attachment_type)){
                    if($val->attachment_type == 'image'){
                        $thisAttachment = "data:image;base64,".$val->attachment;
                    } else {
                        $thisAttachment = "data:application/pdf;base64,".$val->attachment;
                    }
                }
            }
            $val->attachment = $thisAttachment;

            $val->content_letter = $val->content_letter;
            unset($val->attachment_type);

            return $val;
        });
        return $result;
    }

    public static function readAnnouncement($employee, $idAnnouncement) {
        $idEmployee    = $employee->id_employee;
        $idUser        = $employee->id_user;
        $idCompany     = $employee->id_company;
        $today         = date('Y-m-d');

        $data = [
            'id_announcement'   => $idAnnouncement,
            'id_user'           => $idUser,
            'id_company'        => $idCompany,
            'created_by'        => $idUser,
        ];
        $insert = RelationAnnouncementUser::create($data);
        return $insert;
    }

    public static function getAnnouncementTypes($idCompany) {
        $today         = date('Y-m-d');

        $getMasterTypes = DB::table('master_general_data')
            ->select('id_general_data as id', 'code', 'description')
            ->where('id_general_type', 13)
            ->where('id_company', $idCompany)
            ->where('status', 'A')
            ->get();
        return $getMasterTypes;
    }



}