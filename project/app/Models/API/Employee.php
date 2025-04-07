<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Employee extends Model {
	
	public static function get_employee($nik, $idCompany = "NULL") {
        $q1 = DB::table('hr_employee as he')
                ->select('he.id_employee')
                ->where('he.nik_employee', $nik)
                ->first();
        if($q1){
            $get = DB::table(DB::raw("sp_funct_api_employee_view ('".$nik."', ".$idCompany.") sfaev"))
                    ->select('sfaev.*');
            $return = $get->get();
            if($return->count() > 0){
                $avatar = asset('public/global/img/avatar.jpg');
                foreach ($return as $key => $val) {
                    if(@$return[$key]->image_attachment != null){
                        if(strlen(@$return[$key]->image_attachment) > 1000){
                            @$return[$key]->image_attachment = "data:image;base64,".@$return[$key]->image_attachment;
                        } else {
                            $urlPhoto = 'public/upload/photo/'. @$return[$key]->image_attachment;
                            if (Storage::exists($urlPhoto)) {
                                @$return[$key]->image_attachment = url('project/storage/app/'.$urlPhoto);
                            } else {
                                @$return[$key]->image_attachment = $avatar;
                            }
                        }
                    } else {
                        @$return[$key]->image_attachment = $avatar;
                    }
                }
            }
        } else {
            $return = [];
        }
        return $return;
    }

    public static function get_employee_all($nik) {
        $q1 = DB::table('hr_employee as he')
                ->select('he.id_employee')
                ->where('he.nik_employee', $nik)
                ->first();
        if($q1){
            $get = DB::table(DB::raw("sp_funct_api_employee_all_status_view ('".$nik."') sfaeasv"))
                    ->select('sfaeasv.*');
            $return = $get->get();
            if($return->count() > 0){
                $avatar = asset('public/global/img/avatar.jpg');
                foreach ($return as $key => $val) {
                    if(@$return[$key]->image_attachment != null){
                        if(strlen(@$return[$key]->image_attachment) > 1000){
                            @$return[$key]->image_attachment = "data:image;base64,".@$return[$key]->image_attachment;
                        } else {
                            $urlPhoto = 'public/upload/photo/'. @$return[$key]->image_attachment;
                            if (Storage::exists($urlPhoto)) {
                                @$return[$key]->image_attachment = url('project/storage/app/'.$urlPhoto);
                            } else {
                                @$return[$key]->image_attachment = $avatar;
                            }
                        }
                    } else {
                        @$return[$key]->image_attachment = $avatar;
                    }
                }
            }
        } else {
            $return = [];
        }
        return $return;
    }

    public static function getEmployeeDetail($nik=null, $idEmployee=null, $full=false, $isActive=true, $idCompany = null) {
        $return = [];
        if($nik){
            if(!is_array($nik)){
                $nik = [$nik];
            }
        }
        if($idEmployee){
            if(!is_array($idEmployee)){
                $idEmployee = [$idEmployee];
            }
        }
        $q1 = DB::table('hr_employee as he')
                ->join('public.relation_company_users as rcu', function ($join) use($idCompany) {
                    $join->on('he.id_user', '=', 'rcu.id_user');
                    if(!$idCompany) {
                        $join->on('he.id_company', '=', 'rcu.id_company');
                    }
                })
                ->leftJoin('master_position_detail as mpd', function ($join) use($idCompany) {
                    if(!$idCompany) {
                        $join->where('mpd.secondary_position', false);
                    }
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                    $join->whereRaw('(mpd.id_company = rcu.id_company OR mpd.assigned_to_company = rcu.id_company)');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->leftJoin('master_job_position as mjp', function ($join) {
                    $join->on('mpr.id_position', '=', 'mjp.id_position');
                    $join->on('mpr.id_company', '=', 'mjp.id_company');
                })
                ->leftJoin('master_department as md', function ($join) {
                    $join->on('mjp.id_dept', '=', 'md.id_dept');
                    $join->on('mjp.id_company', '=', 'md.id_company');
                })
                ->leftJoin('master_branch as mb', function ($join) {
                    $join->on('mpd.id_branch', '=', 'mb.id_branch');
                    $join->on('mpd.id_company', '=', 'mb.id_company');
                })
                ->leftJoin('master_region as mr', function ($join) {
                    $join->on('mb.id_region', '=', 'mr.id_region');
                    $join->on('mb.id_company', '=', 'mr.id_company');
                })
                ->leftJoin('master_location as ml', function ($join) {
                    $join->on('ml.id_location', '=', 'mpd.id_location');
                    $join->on('ml.id_company', '=', 'mpd.id_company');
                });

        if($full){
            $q1->leftJoin('master_position_detail as mpd2', function ($join) {
                    $join->on('mpd.parent_id_position_detail', '=', 'mpd2.id_position_detail');
                    $join->on('mpd.id_company', '=', 'mpd2.id_company');
                    $join->where('mpd2.status', 'A');
                })
                ->leftJoin('hr_employee as he2', function ($join) {
                    $join->whereRaw('(mpd2.id_employee = he2.id_employee OR mpd2.id_employee2 = he2.id_employee)');
                    $join->whereRaw('(mpd2.id_company = he2.id_company OR mpd2.assigned_to_company = he2.id_company)');
                    $join->where('he2.status', 'A');
                })
                ->leftJoin('master_position_detail as mpd3', function ($join) {
                    $join->on('mpd2.parent_id_position_detail', '=', 'mpd3.id_position_detail');
                    $join->on('mpd2.id_company', '=', 'mpd3.id_company');
                    $join->where('mpd3.status', 'A');
                })
                ->leftJoin('hr_employee as he3', function ($join) {
                    $join->whereRaw('(mpd3.id_employee = he3.id_employee)');
                    $join->whereRaw('(mpd3.id_company = he3.id_company OR mpd3.assigned_to_company = he3.id_company)');
                    $join->where('he3.status', 'A');
                })
                ->leftJoin('master_job_grade as mjg', function ($join) {
                    $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
                    $join->on('mpr.id_company', '=', 'mjg.id_company');
                })
                ->leftJoin('master_job_status as mjs', function ($join) {
                    $join->on('mpr.id_job_status', '=', 'mjs.id_job_status');
                    $join->on('mpr.id_company', '=', 'mjs.id_company');
                })
                ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                    $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                    $join->on('rpp.id_company', '=', 'mpd.id_company');
                })
                ->leftJoin('master_principal as mp', function ($join) {
                    $join->on('mp.id_principal', '=', 'rpp.id_principal');
                })
                ->leftJoin('master_general_data as mgd2', 'mgd2.id_general_data', '=', 'he.id_timezone');
        }
                
        $q1->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'he.id_employment_status')
                ->leftJoin('master_company as mc', 'mc.id_company', '=', 'rcu.id_company')
                ->leftJoin('master_company as mc2', 'mc2.id_company', '=', 'mpd.assigned_to_company')
                ->leftJoin('master_shiftgroup_header as msh', 'msh.id_shiftgroup', '=', 'he.id_shift_group');

        if($full){
            $q1->select('he.id_employee', 'he.id_user', 'he.id_company', 'mc.company_code', 'mc.company_code', 'mc.company_type', 'mc.description as company', 'he.nik_employee', 'he.name', 'he.gender', 'he.marital', 'he.status', 'he.mobile_phone', 'he.private_mail', 'he.work_phone', 'he.work_mail', 'he.join_date', 'he.expired_date', 'he.permanent_date', 'he.resign_date', 'he.image_attachment', 'mgd.description as employment_status', 'mc2.company_code as assigned_to_company', 'md.id_dept as id_department', 'md.department_code', 'md.description as department', 'mpr.id_routing', 'mpr.description as position_routing', 'mpd.id_position_detail', 'mpd.description as position_detail', 'mpd.parent_id_position_detail', 'mpd.secondary_position', 'mr.id_region', 'mr.region_code', 'mr.description as region', 'mb.id_branch', 'mb.branch_code', 'mb.description as branch', 'ml.id_location', 'ml.location_code', 'ml.description as location', 'msh.id_shiftgroup', 'msh.shiftgroup_code', 'msh.description as shift_group', 'msh.automatic_absence', 'msh.allow_checkout_nextdays','he.lock_gps_location', 'mgd2.code as timezone_code', 'mgd2.description as timezone', 'mjg.id_job_grade', 'mjg.description as job_grade', 'mjg.job_class_group', 'mjs.description as job_status', 'mp.description as principal', 'he2.id_employee as direct_id_employee', 'he2.name as direct_employee_name', 'he3.id_employee as indirect_id_employee', 'he3.name as indirect_employee_name', 'rpp.id_principal');
        } else {
            $q1->select('he.id_employee', 'he.id_user', 'rcu.id_company', 'mc.company_code', 'mc.company_code', 'mc.company_type', 'mc.description as company', 'he.name', 'he.gender', 'he.marital', 'he.status', 'he.mobile_phone', 'he.private_mail', 'he.join_date', 'he.expired_date', 'he.permanent_date', 'he.resign_date', 'he.image_attachment', 'mgd.description as employment_status', 'mc2.company_code as assigned_to_company', 'md.id_dept as id_department', 'md.department_code', 'md.description as department', 'mpr.id_routing', 'mpr.description as position_routing', 'mpd.id_position_detail', 'mpd.description as position_detail', 'mpd.secondary_position', 'mr.id_region', 'mr.region_code', 'mr.description as region', 'mb.id_branch', 'mb.branch_code', 'mb.description as branch', 'ml.id_location', 'ml.location_code', 'ml.description as location', 'msh.id_shiftgroup', 'msh.shiftgroup_code', 'msh.description as shift_group', 'msh.automatic_absence', 'msh.allow_checkout_nextdays', 'he.lock_gps_location');
        }

        if($isActive){
            $q1->where('he.status', 'A');
        }
        if($idEmployee){
            $q1->whereIn('he.id_employee', $idEmployee);
        }
        if($nik){
            $q1->whereIn('he.nik_employee', $nik);
        }
        if($idCompany) {
            $q1->whereRaw("
                (
                    (mpd.id_company = ? AND mc.id_company = ? AND he.id_company = mpd.id_company)
                    OR (mpd.id_company = ?
                        AND rcu.id_company = mpd.id_company
                        AND mc.company_type = 'corporate'
                        AND mpd.secondary_position = TRUE
                    )
                    OR (mpd.assigned_to_company = ?
                        AND rcu.id_company = mpd.id_company
                        AND mc.company_type = 'corporate'
                        AND mpd.secondary_position = FALSE
                    )
                    OR (rcu.id_company = mpd.assigned_to_company
                        AND mc.company_type = 'os')
                )", [
                $idCompany,
                $idCompany,
                $idCompany,
                $idCompany
            ]);
        }

        $result = $q1->get();

        if($result->count() > 0){
            foreach ($result as $key => $val) {
                if(@$result[$key]->image_attachment != null){
                    if(strlen(@$result[$key]->image_attachment) > 1000){
                        @$result[$key]->image_attachment = "data:image;base64,".@$result[$key]->image_attachment;
                    } else {
                        $urlPhoto = 'public/upload/photo/'. @$result[$key]->image_attachment;
                        if (Storage::exists($urlPhoto)) {
                            @$result[$key]->image_attachment = url('project/storage/app/'.$urlPhoto);
                        } else {
                            @$result[$key]->image_attachment = asset('public/global/img/avatar.jpg');
                        }
                    }
                } else {
                    @$result[$key]->image_attachment = asset('public/global/img/avatar.jpg');
                }
            }
            $return = $result;
        }
        return $return;
    }

    public static function getCompanies($username)
    {
        $sql = "SELECT DISTINCT mpd.id_company, mc.company_name, mu.user_name, he.nik_employee
                FROM relation_company_users rcu
                JOIN master_users mu
                ON rcu.id_user = mu.id_user 
                AND mu.status = 'A'
                JOIN hr_employee he
                ON mu.id_user = he.id_user
                JOIN master_position_detail mpd
                ON he.id_employee = mpd.id_employee
                AND rcu.id_company = mpd.id_company
                JOIN master_company mc
                ON mpd.id_company = mc.id_company
                where mu.user_name = ?;
                ";
        return DB::select($sql, [ $username ]);
    }
}
