<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeeLeave extends Model {

    use HasFactory;

    protected $table = 'hr_leave_balance_emp';
    protected $primaryKey = 'id_leave_balance_emp';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_leave_balance_emp','id_employee','id_leave_type', 'leave_quota', 'used_leave', 'note', 'effective_date', 'expired_date', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_all_leave($id_employee, $id_company=null) {
        $id_employee = is_array($id_employee) ? $id_employee : [$id_employee];
        $id_company = $id_company ?? session('id_company');

        $data = DB::table('hr_leave_balance_emp as hlb')
                ->join('master_leave_type as mlt', 'mlt.id_leave_type', '=', 'hlb.id_leave_type', 'left')
                ->selectRaw('mlt.leave_code, mlt.description, hlb.id_leave_type, hlb.id_leave_balance_emp, hlb.leave_quota, hlb.used_leave, hlb.effective_date, hlb.expired_date,  CURRENT_DATE')
                ->whereIn('hlb.id_employee', $id_employee)
                ->where('hlb.id_company', '=', $id_company)
                ->where(function ($q){
                    $q->where(function ($query){
                        $query->where('mlt.leave_code', 'EDO')
                            ->whereIn('hlb.status', ['I','A']);
                    });
                    $q->orWhere(function ($query){
                        $query->where('mlt.leave_code', '!=', 'EDO')
                            ->where('hlb.status', '=', 'A')
                            ->whereRaw('CURRENT_DATE >= hlb.effective_date');
                    });
                })
                ->orderBy('mlt.description');
        return $data->get();
    }

    public static function get_leave_by_gender($id_company=null, $leave_code=null) {
        $id_company = $id_company ?? session('id_company');

        $data = DB::table('master_leave_detail as mld')
                ->join('master_leave_type as mlt', 'mlt.id_leave_type', '=', 'mld.id_leave_type', 'left')
                ->join('master_leave_header as mlh', 'mlh.id_leave_header', '=', 'mld.id_leave_header', 'left')
                ->selectRaw('mlt.leave_code, mlt.description, mlt.id_leave_type, mlh.id_leave_header, mlh.description as group')
                ->where('mlh.id_company', '=', $id_company)
                ->where('mlt.status', '=', 'A')
                ->orderBy('mlt.description');
        if($leave_code){
            $data->where('mlt.leave_code', $leave_code);
        }
        return $data->get();
    }
}
