<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeApproval extends Model {
	
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

    public static function listApproval($idCompany, $idEmployee, $idUser, $idApprovalTrans=null, $requestType=null, $returnCount=false) {
        $get = DB::table(DB::raw("sp_funct_approval_view (".$idEmployee.",".$idCompany.",null,null,null,".$idUser.",null,null) sfav"))
                ->leftJoin('hr_request_header as hrh', function ($join) {
                    $join->on('hrh.id_request_header', '=', 'sfav.id_source_transaction');
                    $join->whereNotIn('sfav.source_transaction_type', ['Career_Request', 'Termination_Request', 'FPK_Request','Official_Travel', 'Sales_Code']);
                })
                ->leftJoin('hr_request_detail as hrd', function ($join) {
                    $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                    $join->where(function ($where){
                        $where->where('sfav.source_transaction_type', '!=', 'Attendance_Correction');
                        $where->whereNotNull('hrd.request_start_to');
                        $where->whereNotNull('hrd.request_end_to');
                    });
                })
                ->leftJoin('hr_hiring_request_header as hhrh', function ($join) {
                    $join->on('hhrh.id_hiring_request_header', '=', 'sfav.id_source_transaction');
                    $join->whereIn('sfav.source_transaction_type', ['FPK_Request']);
                })
                ->leftJoin('hr_official_travel as hot', function ($join) {
                    $join->on('hot.id_official_travel', '=', 'sfav.id_source_transaction');
                    $join->whereIn('sfav.source_transaction_type', ['Official_Travel']);
                })
                ->leftJoin('master_leave_type as mlt', function ($join) {
                    $join->on('mlt.id_leave_type', '=', 'hrh.id_leave_type');
                })
                ->leftJoin('integration.bgen_integration_sales_code as bisc', function ($join) {
                    $join->on('bisc.id_integration_sales_code', '=', 'sfav.id_source_transaction');
                    $join->whereIn('sfav.source_transaction_type', ['Sales_Code']);
                })
                ->leftJoin('hr_employee as he', function ($join) {
                    $join->on('he.id_employee', '=', 'sfav.id_employee_request');
                })
                ->leftJoin('master_position_detail as mpd', function ($join) {
                    $join->where('mpd.secondary_position', 0);
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                    $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->select('sfav.*', 'hrd.qty_days', 'hrd.day_type as day_type_code', 'hrd.day_type as day_type_description', 'mlt.leave_code as leave_type_code', 'mlt.description as leave_type_description', 'sfav.document_status_code', 'sfav.document_status as document_status_description', 'sfav.approval_status_code', 'sfav.approval_status as approval_status_description', 'mpr.description as position_routing');

        if($idApprovalTrans){
            $get->where('sfav.id_approval_transaction', $idApprovalTrans);
        }
        if($requestType){
            $get->where('sfav.request_group_code', $requestType);
        }
        
        // $get->whereNotIn('sfav.source_transaction_type', ['Career_Request', 'Announcement_Request']);
        $get->where(function ($where){
            $where->where('sfav.source_transaction_type', '!=', 'Attendance_Correction');
            $where->whereNotNull('sfav.request_start_date');
            $where->whereNotNull('sfav.request_end_date');
        });
        $get->orWhere(function ($where){
            $where->where('sfav.source_transaction_type', 'Attendance_Correction');
            $where->whereNull('hrd.request_start_to');
            $where->orWhereNull('hrd.request_end_to');
        });
        $result = $get->get();

        if($returnCount){
            return $result->count();
        }
        
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
            $val->qty_days = (in_array($val->source_transaction_type, ['Attendance_Correction', 'Career_Request', 'Termination_Request'])) ? null : $val->qty_days;

            if($val->source_transaction_type!='Attendance_Correction'){
                $val->request_start_date = Carbon::parse($val->request_start_date)->format('Y-m-d');
                $val->request_end_date = Carbon::parse($val->request_end_date)->format('Y-m-d');
            }
            return $val;
        });

        $return = collect([]);
        if($result->count() > 0){
            foreach ($result as $key => $val) {
                $return[] = (object)[
                    'id_approval_transaction' => @$val->id_approval_transaction,
                    'reference_number' => @$val->reference_number,
                    'id_source_transaction' => @$val->id_source_transaction,
                    'source_transaction_type' => @$val->source_transaction_type,
                    'request_type_description' => @$val->request_group,
                    'request_type_code' => @$val->request_group_code,
                    'id_employee_request' => @$val->id_employee_request,
                    'nik_employee' => @$val->nik_employee,
                    'employee_name' => @$val->name,
                    'position_routing' => @$val->position_routing,
                    'note' => @$val->note,
                    'qty_days' => @$val->qty_days,
                    'day_type_code' => @$val->day_type_code,
                    'day_type_description' => @$val->day_type_description,
                    'leave_type_code' => @$val->leave_type_code,
                    'leave_type_description' => @$val->leave_type_description,
                    'approval_name' => @$val->approval_name,
                    'position_route_old' => @$val->position_route_old,
                    'position_route_new' => @$val->position_route_new,
                    'employment_status_old' => @$val->employment_status_old,
                    'employment_status_new' => @$val->employment_status_new,
                    'location_old' => @$val->location_old,
                    'location_new' => @$val->location_new,
                    'job_grade_old' => @$val->job_grade_old,
                    'job_grade_new' => @$val->job_grade_new,
                    'principal_old' => @$val->principal_old,
                    'principal_new' => @$val->principal_new,
                    'document_status_code' => @$val->document_status_code,
                    'document_status_description' => @$val->document_status_description,
                    'approval_status_code' => @$val->approval_status_code,
                    'approval_status_description' => @$val->approval_status_description,
                    'attachment' => @$val->attachment,
                    'request_start_date' => @$val->request_start_date,
                    'request_end_date' => @$val->request_end_date,
                    'request_submitted_date' => @$val->creation_date,
                    'submitted_by' => @$val->submitted_by,
                    'is_primary_approval' => @$val->is_primary_approval,
                ];
            }
        }
        
        return $return;
    }

    public static function listApprovalHistory($idCompany, $idEmployee, $idUser, $start=null, $end=null, $requestType=null) {
        $get = DB::table(DB::raw("sp_funct_approved_view (".$idEmployee.",".$idCompany.",null,null,null,".$idUser.",'".$start."','".$end."') sfav"))
            ->leftJoin('hr_request_header as hrh', function ($join) {
                $join->on('hrh.id_request_header', '=', 'sfav.id_source_transaction');
                $join->whereNotIn('sfav.source_transaction_type', ['Career_Request', 'Termination_Request','FPK_Request','Official_Travel', 'Sales_Code']);
            })
            ->leftJoin('hr_request_detail as hrd', function ($join) {
                $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                $join->where(function ($where){
                    $where->where('sfav.source_transaction_type', '!=', 'Attendance_Correction');
                    $where->whereNotNull('hrd.request_start_to');
                    $where->whereNotNull('hrd.request_end_to');
                });
            })
            ->leftJoin('hr_hiring_request_header as hhrh', function ($join) {
                $join->on('hhrh.id_hiring_request_header', '=', 'sfav.id_source_transaction');
                $join->whereIn('sfav.source_transaction_type', ['FPK_Request']);
            })
            ->leftJoin('hr_official_travel as hot', function ($join) {
                $join->on('hot.id_official_travel', '=', 'sfav.id_source_transaction');
                $join->whereIn('sfav.source_transaction_type', ['Official_Travel']);
            })
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('mlt.id_leave_type', '=', 'hrh.id_leave_type');
            })
            ->leftJoin('integration.bgen_integration_sales_code as bisc', function ($join) {
                $join->on('bisc.id_integration_sales_code', '=', 'sfav.id_source_transaction');
                $join->whereIn('sfav.source_transaction_type', ['Sales_Code']);
            })
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('he.id_employee', '=', 'sfav.id_employee_request');
            })
            ->leftJoin('master_position_detail as mpd', function ($join) {
                $join->where('mpd.secondary_position', 0);
                $join->on('he.id_employee', '=', 'mpd.id_employee');
                $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
            })
            ->leftJoin('master_position_routing as mpr', function ($join) {
                $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                $join->on('mpd.id_company', '=', 'mpr.id_company');
            })
            ->select('sfav.*', 'hrd.qty_days', 'hrd.day_type as day_type_code', 'hrd.day_type as day_type_description', 'mlt.leave_code as leave_type_code', 'mlt.description as leave_type_description', 'sfav.document_status_code', 'sfav.document_status as document_status_description', 'sfav.approval_status_code', 'sfav.approval_status as approval_status_description', 'mpr.description as position_routing');

        if($requestType){
            $get->where('sfav.request_group_code', $requestType);
        }
        // $get->whereNotIn('sfav.source_transaction_type', ['Career_Request', 'Announcement_Request']);
        // $get->where(function($getWhere) {
        //     $getWhere->where(function ($where){
        //         $where->where('sfav.source_transaction_type', '!=', 'Attendance_Correction');
        //         $where->whereNotNull('sfav.request_start_date');
        //         $where->whereNotNull('sfav.request_end_date');
        //     });
        //     $getWhere->orWhere(function ($where){
        //         $where->where('sfav.source_transaction_type', 'Attendance_Correction');
        //         $where->whereNull('hrd.request_start_to');
        //         $where->orWhereNull('hrd.request_end_to');
        //     });
        // });
        $get->orderBy('sfav.creation_date', 'desc');
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
            $val->qty_days = (in_array($val->source_transaction_type, ['Attendance_Correction', 'Career_Request', 'Termination_Request'])) ? null : $val->qty_days;

            if($val->source_transaction_type!='Attendance_Correction'){
                $val->request_start_date = Carbon::parse($val->request_start_date)->format('Y-m-d');
                $val->request_end_date = Carbon::parse($val->request_end_date)->format('Y-m-d');
            }
            return $val;
        });

        $return = collect([]);
        if($result->count() > 0){
            foreach ($result as $key => $val) {
                $return[] = (object)[
                    'id_approval_transaction' => @$val->id_approval_transaction,
                    'reference_number' => @$val->reference_number,
                    'id_source_transaction' => @$val->id_source_transaction,
                    'source_transaction_type' => @$val->source_transaction_type,
                    'request_type_description' => @$val->request_group,
                    'request_type_code' => @$val->request_group_code,
                    'id_employee_request' => @$val->id_employee_request,
                    'nik_employee' => @$val->nik_employee,
                    'employee_name' => @$val->name,
                    'position_routing' => @$val->position_routing,
                    'note' => @$val->note,
                    'qty_days' => @$val->qty_days,
                    'day_type_code' => @$val->day_type_code,
                    'day_type_description' => @$val->day_type_description,
                    'leave_type_code' => @$val->leave_type_code,
                    'leave_type_description' => @$val->leave_type_description,
                    'approval_name' => @$val->approval_name,
                    'approval_name' => @$val->approval_name,
                    'position_route_old' => @$val->position_route_old,
                    'position_route_new' => @$val->position_route_new,
                    'employment_status_old' => @$val->employment_status_old,
                    'employment_status_new' => @$val->employment_status_new,
                    'location_old' => @$val->location_old,
                    'location_new' => @$val->location_new,
                    'job_grade_old' => @$val->job_grade_old,
                    'job_grade_new' => @$val->job_grade_new,
                    'principal_old' => @$val->principal_old,
                    'principal_new' => @$val->principal_new,
                    'document_status_code' => @$val->document_status_code,
                    'document_status_description' => @$val->document_status_description,
                    'approval_status_code' => @$val->approval_status_code,
                    'approval_status_description' => @$val->approval_status_description,
                    'attachment' => @$val->attachment,
                    'request_start_date' => @$val->request_start_date,
                    'request_end_date' => @$val->request_end_date,
                    'request_submitted_date' => @$val->creation_date,
                    'submitted_by' => @$val->submitted_by,
                    'is_primary_approval' => @$val->is_primary_approval,
                ];
            }
        }
        
        return $return;
    }

    public static function approvalCount($idCompany, $idUser) {
        $get = DB::table('hr_approval_transaction as hat')
            ->leftJoin('master_general_data as mgd', function ($join) {
                $join->on('hat.id_approval_status', '=', 'mgd.id_general_data');
            })
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('he.id_employee', '=', 'hat.id_employee_approval');
            })
            ->select(DB::raw("COUNT(hat.source_transaction_type) as total"))
            ->where('he.id_company', $idCompany)
            ->where('he.id_user', $idUser)
            ->where('mgd.code', 'Request_Approval')
            ->groupBy('he.id_user');

        $result = $get->get();
        return $result;
    }

    public static function getMailEmployeeFromEmployeeRequest($idRequest) {
        $get = DB::table('hr_request_header as hrh')
            ->join('hr_request_detail as hrd', function ($join) {
                $join->on('hrd.id_request_header', '=', 'hrh.id_request_header');
            })
            ->join('hr_employee as he', function ($join) {
                $join->on('he.id_employee', '=', 'hrd.id_employee');
            })
            ->join('master_general_data as mgd', function ($join) {
                $join->on('hrh.id_request_type', '=', 'mgd.id_general_data');
            })
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
            })
            ->select('hrh.reference_number', 'hrd.request_start_to', 'hrd.request_end_to', 'hrd.qty_days', 'hrh.note', 'he.name', 'he.private_mail', 'mgd.code as request_code', 'mgd.description as desc_request', 'mlt.description as leave_name')
            ->where('hrd.id_request_header', $idRequest);

        $result = $get->get();
        return $result;
    }

    public static function getSequenceEmployeeApproval($idSourceTransaction, $sourceTransactionType) {
        $get = DB::table('hr_approval_transaction as hat')
            ->join('master_general_data as mgd', function ($join) {
                $join->on('hat.id_approval_status', '=', 'mgd.id_general_data');
            })
            ->select(DB::raw("DISTINCT(hat.sequence) as sequence"), 'mgd.description as status_app')
            ->where('hat.id_source_transaction', $idSourceTransaction)
            ->where('hat.source_transaction_type', $sourceTransactionType)
            ->where('mgd.code', 'Request_Approval');

        $result = $get->get();
        return $result;
    }

    public static function approveAll($company, $idApprovalTrans) {
        $get = DB::table(DB::raw("compute_approval_status (".$company.",".$idApprovalTrans.") cas"))
                ->select('cas.*');

        $result = $get->get();
        return $result;
    }

    public static function getApprovalDocType($idCompany, $idRequestType=null, $code=null, $exceptCode=null) {
        if($exceptCode && !is_array($exceptCode)){
            $exceptCode = [$exceptCode];
        }
        if($code && !is_array($code)){
            $code = [$code];
        }
        $get = DB::table('master_general_data as mgd')
            ->select('id_general_data', 'description', 'code')
            ->where('mgd.id_company', $idCompany)
            ->where('mgd.id_general_type', 9)
            ->where('mgd.status', 'A');

        if($idRequestType){
            $get->where('mgd.id_general_data', $idRequestType);
        }
        if($code){
            $get->whereIn('mgd.code', $code);
        }
        if($exceptCode){
            $get->whereNotIn('mgd.code', $exceptCode);
        }
        $result = $get->get();
        return $result;
    }

    public static function getApprovalHierarchy($idCompany=null, $idApproval=null, $hierarchyType=null, $idApprovalDocType=null, $idPositionDetail=null) {
        if($hierarchyType && !is_array($hierarchyType)){
            $hierarchyType = [$hierarchyType];
        }
        if($idApprovalDocType && !is_array($idApprovalDocType)){
            $idApprovalDocType = [$idApprovalDocType];
        }
        if($idPositionDetail && !is_array($idPositionDetail)){
            $idPositionDetail = [$idPositionDetail];
        }
        $get = DB::table('hr_approval_header as hah')
            ->leftJoin('hr_approval_detail as had', function ($join) {
                $join->on('hah.id_approval', '=', 'had.id_approval');
            })
            ->leftJoin('master_general_data as mgd', function ($join) {
                $join->on('hah.id_approval_doc_type', '=', 'mgd.id_general_data');
            })
            ->leftJoin('master_position_detail as mpd', function ($join) {
                $join->on('had.id_position_detail', '=', 'mpd.id_position_detail');
            })
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('mpd.id_employee', '=', 'he.id_employee');
            })
            ->select('hah.id_approval', 'hah.description as hierarchy_description', 'hah.hierarchy_type', 'hah.approval_mode as hierarchy_approval_mode', 'mgd.code as hierarchy_doc_type', 'had.sequence', 'had.id_position_detail', 'he.id_employee', 'he.id_user', 'he.name as employee')
            ->where('mgd.id_general_type', 9)
            ->where('hah.status', 'A');

        if($idCompany){
            $get->where('hah.id_company', $idCompany);
        }
        if($idApproval){
            $get->where('hah.id_approval', $idApproval);
        }
        if($hierarchyType){
            $get->whereIn('hah.hierarchy_type', $hierarchyType);
        }
        if($idApprovalDocType){
            $get->whereIn('hah.id_approval_doc_type', $idApprovalDocType);
        }
        if($idPositionDetail){
            $get->whereIn('mpd.id_position_detail', $idPositionDetail);
        }
        $result = $get->get();
        return $result;
    }

    public static function getMasterTransitionCategory($idCompany, $idCategory=null, $code=null, $exceptCode=null) {
        if($exceptCode && !is_array($exceptCode)){
            $exceptCode = [$exceptCode];
        }
        if($code && !is_array($code)){
            $code = [$code];
        }
        $get = DB::table('master_general_data as mgd')
            ->select('id_general_data', 'description', 'code')
            ->where('mgd.id_company', $idCompany)
            ->where('mgd.id_general_type', 5)
            ->where('mgd.status', 'A');

        if($idCategory){
            $get->where('mgd.id_general_data', $idCategory);
        }
        if($code){
            $get->whereIn('mgd.code', $code);
        }
        if($exceptCode){
            $get->whereNotIn('mgd.code', $exceptCode);
        }
        $result = $get->get();
        return $result;
    }

    public static function getCustomTypeApprovalCareer($idCompany, $idPositionDetail=null) {
        $get = DB::table('master_general_data as mgd')
            ->join('master_general_data as mgd2', function ($join) {
                $join->on('mgd.relation_to_id_general_data', '=', 'mgd2.id_general_data');
            })
            ->join('hr_approval_header as hah', function ($join) {
                $join->on('mgd2.id_general_data', '=', 'hah.id_approval_doc_type');
            })
            ->join('hr_approval_detail as had', function ($join) {
                $join->on('hah.id_approval', '=', 'had.id_approval');
            })
            ->selectRaw('distinct mgd.code, mgd2.id_general_data, mgd.description')
            ->where('mgd.id_company', $idCompany)
            ->where('mgd.status', 'A')
            ->where('mgd.id_general_type', 5);

        if($idPositionDetail){
            $get->where('had.id_position_detail', $idPositionDetail);
        }

        $result = $get->get();
        return $result;
    }

    public static function getCustomTypeApprovalFpk($idCompany, $idPositionDetail=null) {
        $get = DB::table('master_general_data as mgd')
            ->join('hr_approval_header as hah', function ($join) {
                $join->on('mgd.id_general_data', '=', 'hah.id_approval_doc_type');
            })
            ->join('hr_approval_detail as had', function ($join) {
                $join->on('hah.id_approval', '=', 'had.id_approval');
            })
            ->selectRaw('distinct mgd.code, mgd.id_general_data, mgd.description')
            ->where('mgd.id_company', $idCompany)
            ->where('mgd.status', 'A')
            ->where('mgd.code', 'FPK_Request')
            ->where('mgd.id_general_type', 9);

        if($idPositionDetail){
            $get->where('had.id_position_detail', $idPositionDetail);
        }

        $result = $get->get();
        return $result;
    }

    public static function getCustomTypeApprovalTravel($idCompany, $idPositionDetail=null) {
        $get = DB::table('master_general_data as mgd')
            ->join('hr_approval_header as hah', function ($join) {
                $join->on('mgd.id_general_data', '=', 'hah.id_approval_doc_type');
            })
            ->selectRaw('distinct mgd.code, mgd.id_general_data, mgd.description')
            ->where('mgd.id_company', $idCompany)
            ->where('mgd.status', 'A')
            ->where('mgd.code', 'Official_Travel')
            ->where('mgd.id_general_type', 9);

        if($idPositionDetail){
            $get->where('had.id_position_detail', $idPositionDetail);
        }

        $result = $get->get();
        return $result;
    }
	
	public static function get_mail_approval_travel($data) {
        $sql = "SELECT hot.reference_number, he.name, he.private_mail,hot.start_date::date AS request_start_to, 
				hot.end_date::date AS request_end_to, CONCAT(hot.location_to,' (',hot.reason_notes,')') AS note,  
				hat.source_transaction_type AS request_code, mgd.description as desc_request, hot.travel_status
				FROM hr_official_travel hot
				JOIN hr_employee he
				ON hot.request_by = he.id_employee
				JOIN hr_approval_transaction hat
				ON hot.id_official_travel = hat.id_source_transaction 
				AND hat.source_transaction_type = 'Official_Travel'
				JOIN master_general_data mgd
				ON hot.id_reason_group = mgd.id_general_data
				WHERE hot.id_official_travel = ".$data;
        $result = DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
}
