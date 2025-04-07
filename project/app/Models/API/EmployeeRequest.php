<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeRequest extends Model {
	
    public static function listRequest($idCompany, $idUser, $startDate, $endDate, $requestType=null) {
        $get = DB::table('hr_request_header as hrh')
            ->select('hrh.id_request_header as id_request', 'hrh.reference_number', 'hrd.request_start_to', 'hrd.request_end_to', 'hrh.note', 'hrd.qty_days', 'hrd.day_type as day_type_code', 'hrd.day_type as day_type_description', 'mlt.leave_code as leave_type_code', 'mlt.description as leave_type_description', 'hrh.attachment', 'hrh.attachment_type', 'hrh.note_rejected', 'hrh.note_revised', 'he.name as employee_name', 'mgd.description as request_type_description', 'mgd.code as request_type_code', 'mgd2.description as approval_status_description', 'mgd2.code as approval_status_code', 'hrh.creation_date as request_created_date')
            ->leftJoin('hr_request_detail as hrd', function ($join) {
                $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
            })
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('mlt.id_leave_type', '=', 'hrh.id_leave_type');
            })
            ->join('hr_employee as he', function ($join) {
                $join->on('hrh.id_employee_request', '=', 'he.id_employee');
                $join->whereRaw("(he.status = 'A')");
            })
            ->join('master_general_data as mgd', function ($join) {
                $join->on('hrh.id_request_type', '=', 'mgd.id_general_data');
            })
            ->join('master_general_data as mgd2', function ($join) {
                $join->on('hrh.id_approval_status', '=', 'mgd2.id_general_data');
            });

        if($requestType){
            $get->where('mgd.code', $requestType);
        }
        $get->where('hrh.id_company', $idCompany)
            ->where('he.id_user', $idUser)
            ->whereBetween('hrh.creation_date', [$startDate, $endDate])
            ->orderBy('hrh.id_request_header', 'DESC');
            
        $result = $get->get();

        $result->mapWithKeys(function ($val){
            if(is_null($val->attachment_type) && !is_null($val->attachment)){
                $urlAttachment = null;
                if (Storage::exists('public/upload/employee_request/'. $val->attachment)) {
                    $urlAttachment = url('project/storage/app/public/upload/employee_request').'/'.$val->attachment;
                } else if(Storage::exists('public/thumbnail/'. $val->attachment)){
                    $urlAttachment = url('project/storage/app/public/thumbnail').'/'.$val->attachment;
                }
                $val->attachment = $urlAttachment;
            }

            $val->day_type_description = !is_null($val->day_type_description) ? self::listDayType($val->day_type_code) : null;
            $val->qty_days = ($val->request_type_code=='Attendance_Correction') ? null : $val->qty_days;

            if($val->request_type_code!='Attendance_Correction'){
                $val->request_start_to = Carbon::parse($val->request_start_to)->format('Y-m-d');
                $val->request_end_to = Carbon::parse($val->request_end_to)->format('Y-m-d');
            }

            unset($val->attachment_type);
            return $val;
        });

        $return = $result;
        return $return;
    }

    public static function getRequestType($idCompany, $idRequestType=null, $code=null, $exceptCode=null) {
        if($exceptCode && !is_array($exceptCode)){
            $exceptCode = [$exceptCode];
        }
        $get = DB::table('master_general_data as mgd')
            ->select('id_general_data as id_request_type', 'description as request_type_description', 'code as request_type_code')
            ->where('mgd.id_company', $idCompany)
            ->where('mgd.id_general_type', 10)
            ->where('mgd.status', 'A')
            ->where('mgd.code', '!=', 'Overtime_Request');

        if($idRequestType){
            $get->where('mgd.id_general_data', $idRequestType);
        }
        if($code){
            $get->where('mgd.code', $code);
        }
        if($exceptCode){
            $get->whereNotIn('mgd.code', $exceptCode);
        }
        $result = $get->get();
        return $result;
    }

    public static function getLeaveType($idCompany, $idEmployee) {
        $today = date('Y-m-d');
        $getMinEffectiveDateEdo = DB::table('hr_leave_balance_emp as hlb')
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('hlb.id_leave_type', '=', 'mlt.id_leave_type');
            })
            ->select(DB::raw('MIN(hlb.effective_date) as min_date'))
            ->where('hlb.id_company', $idCompany)
            ->where('hlb.id_employee', $idEmployee)
            ->where('hlb.status', 'A')
            ->where('mlt.leave_code', '=', 'EDO')
            ->whereRaw('(hlb.leave_quota - hlb.used_leave) > 0')
            ->first();

        $get = DB::table('hr_leave_balance_emp as hlb')
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('hlb.id_leave_type', '=', 'mlt.id_leave_type');
            })
            ->select('mlt.id_leave_type as id_leave_type', 'mlt.leave_code as leave_type_code', 'mlt.description as leave_type_description', 'hlb.leave_quota', DB::raw('(hlb.leave_quota - hlb.used_leave) as leave_remaining'),'hlb.effective_date', 'hlb.expired_date', 'mlt.day_limit_submit_request')
            ->where('hlb.id_company', $idCompany)
            ->where('hlb.id_employee', $idEmployee)
            ->where('hlb.status', 'A')
            ->where('mlt.leave_code', '!=', 'EDO')
            // ->whereRaw('(hlb.leave_quota - hlb.used_leave) > 0')
            ->where('hlb.effective_date', '<=', $today);
        $resultExceptEdo = $get->get();

        $get2 = DB::table('hr_leave_balance_emp as hlb')
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('hlb.id_leave_type', '=', 'mlt.id_leave_type');
            })
            ->select('mlt.id_leave_type as id_leave_type', 'mlt.leave_code as leave_type_code', 'mlt.description as leave_type_description', 'hlb.leave_quota', DB::raw('(hlb.leave_quota - hlb.used_leave) as leave_remaining'),'hlb.effective_date', 'hlb.expired_date', 'mlt.day_limit_submit_request')
            ->where('hlb.id_company', $idCompany)
            ->where('hlb.id_employee', $idEmployee)
            ->where('hlb.status', 'A')
            ->where('mlt.leave_code', '=', 'EDO')
            ->whereRaw('(hlb.leave_quota - hlb.used_leave) > 0')
            ->where('hlb.effective_date', @$getMinEffectiveDateEdo->min_date);
        $resultOnlyEdo = $get2->get();

        $result = collect($resultExceptEdo->merge($resultOnlyEdo)->sortBy('id')->values()->all());
        return $result;
    }

    public static function getHierarchyApproval($code, $idCompany, $idLocation) {
        $get = DB::table(DB::raw("sp_funct_list_approval_hierarchy_view (array[".$idLocation."], ".$idCompany.", '".$code."', 'Combine') sflahv"))
                ->select('sflahv.*');
        $return = $get->get();
        return $return;
    }

    public static function checkQuantityWorkdays($idEmployee, $start, $end, $dayTypeCode, $leaveTypeCode, $idCompany) {
        $get = DB::table('hr_work_days as hwd')
                ->whereBetween('current_dates', [$start, $end])
                ->where('id_employee', $idEmployee);

        $masterLeave = self::getMasterLeaveType($idCompany, null, $leaveTypeCode);
        $leaveRestrictBy = @$masterLeave[0]->restrict_by;

        if(is_null($leaveTypeCode)){
            $get->where('day_type', 'OD');
        } 
        else if(!in_array($leaveTypeCode, ['MAT', 'MIS']) && $leaveRestrictBy!='User'){
            $get->where('day_type', 'WD');
        }

        $quantity = $get->get()->count();
        $checkQuantityWorkdays = $quantity;

        if($dayTypeCode != 'Full_Day'){
            if(!is_null($leaveTypeCode) && (!in_array($leaveTypeCode, ['MAT', 'MIS']) && $leaveRestrictBy!='User')){
                $checkQuantityWorkdays = $checkQuantityWorkdays * 0.5;
            }
        }
        $return = $checkQuantityWorkdays;
        return $return;
    }

    public static function getMasterLeaveType($idCompany, $idLeaveType=null, $leaveCode=null) {
        $get = DB::table('master_leave_type as mlt')
                ->where('status', 'A')
                ->where('id_company', $idCompany);
        if($idLeaveType){
            $get->where('id_leave_type', $idLeaveType);
        }
        if($leaveCode){
            $get->where('leave_code', $leaveCode);
        }
        $return = $get->get();
        return $return;
    }

    public static function getLeaveBalance($idLeaveType, $idEmployee, $idCompany) {
        return DB::selectOne("SELECT
                                hlbe .*
                            FROM
                                hr_leave_balance_emp AS hlbe
                            WHERE
                                hlbe.id_employee = ?
                                AND hlbe.id_leave_type = ?
                                AND hlbe.id_company = ?
                                AND (hlbe.expired_date IS NULL
                                        OR hlbe.expired_date >= now()::date
                                    )
                                AND hlbe.status = 'A'
                                AND hlbe.effective_date <= now()::date", 
                                [$idEmployee, $idLeaveType, $idCompany]);
    }

    public static function getMasterApprovalStatus($idCompany, $code=null) {
        $get = DB::table('master_general_data as mgd')
                ->where('status', 'A')
                ->where('id_general_type', 7)
                ->where('id_company', $idCompany);
        if($code){
            $get->where('code', $code);
        }
        $return = $get->get();
        return $return;
    }

    public static function getApproval($idCompany, $idApproval=null) {
        $idApprovalNew = DB::table('master_general_data as mgd')
            ->where('mgd.code', 'New')
            ->where('mgd.id_company', $idCompany)->first()->id_general_data;

        $get = DB::table('hr_approval_header as hah')
            ->join('master_general_data as mgd', function ($join) {
                $join->on('hah.id_approval_doc_type', '=', 'mgd.id_general_data');
            })
            ->select('hah.id_approval as id', 'hah.description as text', 'hah.id_approval', 'hah.id_approval_doc_type', 'hah.hierarchy_type', 'mgd.code')
            ->where('hah.status', 'A')
            ->where('hah.id_approval', $idApproval)
            ->where('hah.id_company', $idCompany);

        $return = $get->get();
        $return->mapWithKeys(function ($val) use ($idApprovalNew) {
            $val->id_approval_status = $idApprovalNew;
            return $val;
        });
        return $return;
    }

    public static function getApprovalOrganizationHierarchy($idEmployee, $idCompany) {
        $get = DB::table(DB::raw("sp_funct_approval_organization_hierarchy_view (".$idEmployee.",".$idCompany.", null, null) sfao"))
                ->select('sfao.*', 'sfao.id_detail_chief as id_position_detail');
        $return = $get->get();
        return $return;
    }

    public static function getApprovalCombineHierarchy($idEmployee, $idCompany, $idApproval) {
        $get = DB::table(DB::raw("sp_funct_approval_combine_hierarchy_view (".$idEmployee.",".$idCompany.",".$idApproval.") sfac"))
                ->select('sfac.*', 'sfac.id_employee as id_employee_approval')
                ->where('sfac.id_employee', '!=', $idEmployee);
        $return = $get->get();
        return $return;
    }

    public static function getApprovalCustomHierarchy($idEmployee, $idCompany, $idApproval) {
        $get = DB::table(DB::raw("sp_funct_approval_custom_hierarchy_view (".$idEmployee.",".$idCompany.",".$idApproval.") sfacus"))
                ->select('sfacus.*', 'sfacus.id_employee as id_employee_approval ')
                ->where('sfacus.id_employee', '!=', $idEmployee);
        $return = $get->get();
        return $return;
    }

    public static function checkActualTime($idEmployee, $currentDate=null) {
        $get = DB::table('hr_work_days as hwd')
                ->where('current_dates', $currentDate)
                ->where('id_employee', $idEmployee);
        $return = $get->first();
        return $return;
    }


    public static function getListApprovalRequest($idCompany, $idEmployee, $idRequestHeader, $idApproval, $requestTypeCode) {
        $getParent = DB::table('hr_approval_transaction as hat2')
            ->select('mpd4.parent_id_position_detail', 'hat2.id_company', 'mpd4.id_position_detail', 'hat2.created_by')
            ->join('master_position_detail as mpd4', function ($join) {
                $join->on('mpd4.id_position_detail', '=', 'hat2.id_position_detail');
            })
            // ->whereRaw('mpd4.id_company = hat2.id_company')
            ->where('hat2.id_source_transaction', $idRequestHeader)
            ->where('hat2.source_transaction_type', $requestTypeCode);
            // ->where('hat2.sequence', '!=', 1);

        $get = DB::table('hr_approval_transaction as hat')
            ->leftJoin('hr_request_header as hrh', function ($join) {
                $join->on('hrh.id_request_header', '=', 'hat.id_source_transaction');
            })
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('hat.id_employee_approval', '=', 'he.id_employee');
            })
            ->leftJoin('master_general_data as mgd', function ($join) {
                $join->on('mgd.id_general_data', '=', 'hat.id_approval_status');
            })
            ->leftJoin(DB::raw("({$getParent->toSql()}) as parent_position"), function ($join) {
                $join->on('hat.id_position_detail', '=', 'parent_position.parent_id_position_detail');
                $join->on('hat.created_by', '=', 'parent_position.created_by');
                $join->on('hat.id_company', '=', 'parent_position.id_company');
            })
            ->join('master_position_detail as mpd', function ($join) {
                $join->on('hat.id_position_detail', '=', 'mpd.id_position_detail');
            })
            ->join('master_position_routing as mpr', function ($join) {
                $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
            })
            ->mergeBindings($getParent)
            ->select('hat.source_transaction_type as transaction_type', 'hat.sequence', 'hat.note_rejected', 'hat.note_revised', 'hat.note_rejected', 'he.name as approval_name', 'mpr.description AS position_routing', 'mgd.code as approval_status_code', 'mgd.description as approval_status_description', 'he.id_employee', 'hat.update_date', DB::raw("CASE WHEN parent_position.id_position_detail is null THEN True WHEN hat.sequence != 1 THEN True ELSE False END as is_primary_approval"))
            // ->where('hat.id_company', $idCompany)
            // ->where('hrh.id_employee_request', $idEmployee)
            ->where('hat.id_source_transaction', $idRequestHeader)
            ->where('hat.source_transaction_type', $requestTypeCode)
            ->where('hat.id_approval', $idApproval)
            ->where('hat.id_company', $idCompany)
            ->orderBy('hat.sequence', 'ASC')
            ->orderBy('hat.update_date', 'ASC');
        $return = $get->distinct()->get();
        // dd($idRequestHeader, $requestTypeCode, $idApproval, $idCompany);
        return $return;
    }

    public static function getListApprovalCareer($idCareer, $requestTypeCode) {
        $getParent = DB::table('hr_approval_transaction as hat2')
            ->select('mpd4.parent_id_position_detail', 'hat2.id_company', 'mpd4.id_position_detail', 'hat2.created_by')
            ->join('master_position_detail as mpd4', function ($join) {
                $join->on('mpd4.id_position_detail', '=', 'hat2.id_position_detail');
            })
            ->whereRaw('hat2.id_company = hat2.id_company')
            ->where('hat2.id_source_transaction', $idCareer)
            ->where('hat2.source_transaction_type', 'Career_Request');

        $get = DB::table('hr_approval_transaction as hat')
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('hat.id_employee_approval', '=', 'he.id_employee');
            })
            ->leftJoin('master_general_data as mgd', function ($join) {
                $join->on('mgd.id_general_data', '=', 'hat.id_approval_status');
            })
            ->leftJoin('master_general_data as mgd2', function ($join) {
                $join->on('hat.source_transaction_type', '=', 'mgd2.code');
                $join->on('hat.id_company', '=', 'mgd2.id_company');
            })
            ->leftJoin('master_general_data as mgd3', function ($join) {
                $join->on('mgd2.id_general_data', '=', 'mgd3.relation_to_id_general_data');
                $join->on('hat.id_company', '=', 'mgd3.id_company');
            })
            ->leftJoin(DB::raw("({$getParent->toSql()}) as parent_position"), function ($join) {
                $join->on('hat.id_position_detail', '=', 'parent_position.parent_id_position_detail');
                $join->on('hat.created_by', '=', 'parent_position.created_by');
                $join->on('hat.id_company', '=', 'parent_position.id_company');
            })
            ->join('master_position_detail as mpd', function ($join) {
                $join->on('he.id_employee', '=', 'mpd.id_employee');
            })
            ->join('master_position_routing as mpr', function ($join) {
                $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
            })
            ->mergeBindings($getParent)
            ->select('hat.source_transaction_type as transaction_type', 'hat.sequence', 'hat.note_rejected', 'hat.note_revised', 'hat.note_rejected', 'he.name as approval_name', 'mgd.code as approval_status_code', 'mgd.description as approval_status_description', 'he.id_employee', 'hat.update_date', 'mpr.description AS position_routing', DB::raw("CASE WHEN parent_position.id_position_detail is null THEN True WHEN hat.sequence != 1 THEN True ELSE False END as is_primary_approval"))
            ->where('hat.id_source_transaction', $idCareer)
            ->where('mgd3.code', $requestTypeCode)
            ->orderBy('hat.sequence', 'ASC')
            ->orderBy('hat.update_date', 'ASC');

        $return = $get->get();
        return $return;
    }

    public static function getListApprovalRequestFpk($idCompany, $idEmployee, $idRequestHeader, $idApproval, $requestTypeCode) {

        $getParent = DB::table('hr_approval_transaction as hat2')
            ->select('mpd4.parent_id_position_detail', 'hat2.id_company', 'mpd4.id_position_detail', 'hat2.created_by')
            ->join('master_position_detail as mpd4', function ($join) {
                $join->on('mpd4.id_position_detail', '=', 'hat2.id_position_detail');
            })
            ->whereRaw('hat2.id_company = hat2.id_company')
            ->where('hat2.id_source_transaction', $idRequestHeader)
            ->where('hat2.source_transaction_type', $requestTypeCode);

        $get = DB::table('hr_approval_transaction as hat')
            ->leftJoin('hr_hiring_request_header as hhrh', function ($join) {
                $join->on('hhrh.id_hiring_request_header', '=', 'hat.id_source_transaction');
            })
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('hat.id_employee_approval', '=', 'he.id_employee');
            })
            ->leftJoin('master_general_data as mgd', function ($join) {
                $join->on('mgd.id_general_data', '=', 'hat.id_approval_status');
            })
            ->leftJoin(DB::raw("({$getParent->toSql()}) as parent_position"), function ($join) {
                $join->on('hat.id_position_detail', '=', 'parent_position.parent_id_position_detail');
                $join->on('hat.created_by', '=', 'parent_position.created_by');
                $join->on('hat.id_company', '=', 'parent_position.id_company');
            })
            ->join('master_position_detail as mpd', function ($join) {
                $join->on('he.id_employee', '=', 'mpd.id_employee');
            })
            ->join('master_position_routing as mpr', function ($join) {
                $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
            })
            ->mergeBindings($getParent)
            ->select('hat.source_transaction_type as transaction_type', 'hat.sequence', 'hat.note_rejected', 'hat.note_revised', 'hat.note_rejected', 'he.name as approval_name','mpr.description AS position_routing', 'mgd.code as approval_status_code', 'mgd.description as approval_status_description', 'he.id_employee', 'hat.update_date', DB::raw("CASE WHEN parent_position.id_position_detail is null THEN True WHEN hat.sequence != 1 THEN True ELSE False END as is_primary_approval"))
            // ->where('hat.id_company', $idCompany)
            // ->where('hrh.id_employee_request', $idEmployee)
            ->where('hat.id_source_transaction', $idRequestHeader)
            ->where('hat.source_transaction_type', $requestTypeCode)
            ->where('hat.id_approval', $idApproval)
            ->orderBy('hat.sequence', 'ASC')
            ->orderBy('hat.update_date', 'ASC');
        $return = $get->get();
        return $return;
    }

    public static function getGeneralData($idCompany, $idRequestType=null, $code=null, $exceptCode=null) {
        if($exceptCode && !is_array($exceptCode)){
            $exceptCode = [$exceptCode];
        }
        $get = DB::table('master_general_data as mgd')
            ->select('id_general_data as id_request_type', 'description as request_type', 'code as request_code')
            ->where('mgd.id_company', $idCompany)
            ->where('mgd.status', 'A');

        if($idRequestType){
            $get->where('mgd.id_general_data', $idRequestType);
        }
        if($code){
            $get->where('mgd.code', $code);
        }
        if($exceptCode){
            $get->whereNotIn('mgd.code', $exceptCode);
        }
        $result = $get->get();
        return $result;
    }

    public static function getApprovalStatus($idCompany, $idRequestHeader, $idApproval) {
        $get = DB::table('hr_approval_transaction as hat')
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('hat.id_employee_approval', '=', 'he.id_employee');
            })
            ->leftJoin('master_general_data as mgd', function ($join) {
                $join->on('hat.id_approval_status', '=', 'mgd.id_general_data');
            })
            ->select('hat.*', 'he.name', 'mgd.description as code')
            ->where('hat.id_company', $idCompany)
            ->where('hat.id_source_transaction', $idRequestHeader)
            ->where('hat.id_approval', $idApproval);

        $result = $get->get();
        return $result;
    }

    public static function getListApprovalToMail($idCompany, $idRequestHeader, $idApproval) {
        $get = DB::table('hr_approval_transaction as hat')
            ->join('hr_request_header as hrh', function ($join) {
                $join->on('hat.id_source_transaction', '=', 'hrh.id_request_header');
                $join->on('hat.id_company', '=', 'hrh.id_company');
            })
            ->join('hr_request_detail as hrd', function ($join) {
                $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                $join->on('hrh.id_company', '=', 'hrd.id_company');
            })
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('hat.id_employee_approval', '=', 'he.id_employee');
                $join->on('hat.id_company', '=', 'hrh.id_company');
            })
            ->join('master_general_data as mgd', function ($join) {
                $join->on('hat.id_approval_status', '=', 'mgd.id_general_data');
                $join->on('hat.id_company', '=', 'mgd.id_company');
            })
            ->join(DB::raw("sp_funct_employee_position_view (null,null) sfepv"), function ($join) {
                $join->on('he.id_employee', '=', 'sfepv.id_employee');
                $join->on('he.id_company', '=', 'sfepv.id_company');
            })
            ->select(DB::raw("case when (sfepv.parent_id_employee in (select hat2.id_employee_approval from hr_approval_transaction hat2 where hat2.id_source_transaction = ".$idRequestHeader." and hat2.id_approval = ".$idApproval." )) then hat.sequence::numeric else concat(hat.sequence ::text, '.2')::numeric end as new_seq"), 'hrh.id_request_header', 'hrh.reference_number', 'hrh.id_employee_request', 'hrh.id_request_type', 'hrh.id_leave_type', 'hrh.note', 'hrh.creation_date as creation_date_request', 'hrd.id_employee', 'hrd.request_start_to', 'hrd.request_end_to', 'hrd.qty_days', 'hrd.day_type', 'hat.*', 'he.name', 'he.private_mail', 'mgd.description as code')
            ->where('hat.id_company', $idCompany)
            ->where('hat.id_source_transaction', $idRequestHeader)
            ->where('hat.id_approval', $idApproval)
            ->orderBy('new_seq', 'asc');

        $result = $get->get();
        return $result;
    }

    public static function checkBackdateRequest($type, $startDate, $idLeaveType=null) {
        $status = true;
        $today = date('Y-m-d');
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        
        if($type == 'Attendance_Correction'){
            $dayLimit = 5;
            $subToday = Carbon::parse($today)->subDays($dayLimit)->format('Y-m-d');
        } else {
            $dayLimit = 7;
            if($type == 'Leave_Request'){
                $mlt = DB::table('master_leave_type')->where('id_leave_type', $idLeaveType)->first();
                $dayLimit = (int)$mlt->day_limit_submit_request;
            }
            $subToday = Carbon::parse($today)->subDays($dayLimit)->format('Y-m-d');
        }

        $strStart = Carbon::parse($startDate)->timestamp;
        $strSubToday = Carbon::parse($subToday)->timestamp;

        if($strStart < $strSubToday){
            $status = false;
        }
        $return['status'] = $status;
        $return['type'] = $type;
        $return['limit'] = $dayLimit;
        return $return;
    }

    public static function listDayType($byCode=null) {
        $list = [
            'Full_Day' => 'Full Day',
            'Half_Day1' => 'Half Day (Awal)',
            'Half_Day2' => 'Half Day (Akhir)',
        ];
        $full = array_keys($list)[0];
        $awal = array_keys($list)[1];
        $akhir = array_keys($list)[2];

        $dayType  = [
            ['day_type_code' => $full, 'day_type_name' => $list[$full]], 
            ['day_type_code' => $awal, 'day_type_name' => $list[$awal]], 
            ['day_type_code' => $akhir, 'day_type_name' => $list[$akhir]]
        ];

        if($byCode){
            return $list[$byCode];
        }
        return $dayType;
    }

    public static function getRevisedRequest($idCompany, $idUser) {
        $start = Carbon::now()->subDays(14)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d').' 23:59:59';

        $getRevise = DB::table('hr_request_header as hrh')
            ->select(DB::raw("COUNT(mgd.description) as total"), 'mgd.description')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hrh.id_approval_status')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hrh.id_employee_request')
            ->where('mgd.code', 'Revised')
            ->where('he.id_company', $idCompany)
            ->where('he.id_user', $idUser)
            ->whereBetween('hrh.update_date', [$start, $end])
            ->groupBy('mgd.description')
            ->get();
        return $getRevise;
    }

    public static function getRejectedRequest($idCompany, $idUser) {
        $start = Carbon::now()->subDays(7)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d').' 23:59:59';

        $getRejected = DB::table('hr_request_header as hrh')
            ->select(DB::raw("COUNT(mgd.description) as total"), 'mgd.description')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hrh.id_approval_status')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hrh.id_employee_request')
            ->where('mgd.code', 'Rejected')
            ->where('he.id_company', $idCompany)
            ->where('he.id_user', $idUser)
            ->whereBetween('hrh.update_date', [$start, $end])
            ->groupBy('mgd.description')
            ->get();
        return $getRejected;
    }

    public static function getRequests($idEmployee, $idCompany) {
        $sql = "SELECT * FROM sp_funct_leave_request_dashboard_view (?,?)";
        return DB::select($sql, [ $idEmployee, $idCompany ]);
    }

    public static function getRequestsByCategory($idEmployee, $idCompany) {
        $sql = "SELECT * FROM sp_funct_category_group_employee_request_view (?,?)";
        return DB::select($sql, [ $idEmployee, $idCompany ]);
    }

}
