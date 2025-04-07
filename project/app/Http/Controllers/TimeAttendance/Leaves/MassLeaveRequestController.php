<?php

namespace App\Http\Controllers\TimeAttendance\Leaves;

use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeeSetting\WorkDays;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Validator;
use DB;
use DataTables;


class MassLeaveRequestController extends Controller
{
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
	}
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $getMassLeaveRequest = DB::table('hr_mass_leave_request_history as hmlrh')
                ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hmlrh.id_employee')
                ->leftJoin('master_shiftgroup_header as msh', 'msh.id_shiftgroup', '=', 'hmlrh.id_shiftgroup')
                ->select('hmlrh.id_mass_leave_request_history', 'hmlrh.id_employee', 'he.name', 'he.nik_employee', 'msh.description as shift_group', 'hmlrh.start_date', 'hmlrh.end_date', 'hmlrh.qty_days', 'hmlrh.note', 'hmlrh.executed')
                ->where('hmlrh.id_company', session('id_company'))
                ->get();
            
            return DataTables::of($getMassLeaveRequest)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('action', function($data) {
                    $button = "";
                    if(!$data->executed && now() >= Carbon::parse($data->start_date)) {
                        $button .= "<button class='execute-button btn btn-success' mass-leave-request-id='".$data->id_mass_leave_request_history."'><i class='fas fa-play'></i></button>";
                    }
                    return $button;
                })
                ->make(true);
        }

        $allCompany = DB::table('master_company')->where('status', 'A')->where('id_company', session('id_company'))->orderBy('company_name')->get();
        return view('time_attendance.leaves.index', compact('allCompany'));
    }

    public function submit(Request $req)
    {
        ini_set('max_execution_time', -1);
        try{
            $company            = $req->company ?? null;
            $start              = $req->start ?? null;
            $end                = $req->end ?? null;
            $note               = $req->note ?? null;

            $employee = DB::table('hr_employee as he')
                    ->where('he.id_user', session('id_user'))
                    ->first();

            $checkDateMassLeave = DB::table('hr_mass_leave_request_history as hmlrh')
                ->where('id_company', session('id_company'))
                ->where(function ($query) use ($end, $start) {
                    $query->where(function ($query2) use ($start) {
                        $query2->whereDate('hmlrh.start_date', $start)->orWhereDate('hmlrh.end_date', '>=', $start);
                    });
                    $query->orWhere(function ($query2) use ($end) {
                        $query2->whereDate('hmlrh.start_date', $end)->orWhereDate('hmlrh.end_date', '>=', $end);
                    });
                })
                ->get();
            if($checkDateMassLeave->count() > 0){
                throw new \Exception('There are same date in selected company with data record');
            }

            // $generateWorkdays = WorkDays::patchWorkdays($company, $start, $end); //antisipasi jika ada company yg blm tergenerate workdaysnya.
            $bulky = DB::table(DB::raw("generateworkdaysbulky(".session('id_company').",'".$start."','".$end."',".session('id_user').")"))->select('*')->get();
            $workDay = DB::table('hr_work_days')->select('day_seq')
                ->whereBetween('current_dates', [$start, $end])
                ->whereIn('id_company', $company)
                ->get();

            $daySequence = $workDay->pluck('day_seq')->unique();
            $shiftGroup = DB::table('master_shiftgroup_detail as msd')
                ->leftjoin('master_daily_shift as mds', 'msd.id_shift', '=', 'mds.id_shift')
                ->select('msd.id_shiftgroup', 'msd.sequence', 'msd.id_company', 'mds.productive_work_time')
                ->whereIn('msd.id_company', $company)
                ->whereIn('msd.sequence', $daySequence)
                ->orderBy('msd.id_shiftgroup')->get();
            $dataByIdShiftGroup = [];
            $qtyDaysByShiftGroup = [];

            $shiftGroup->mapWithKeys(function ($val){
                $workHour = is_null($val->productive_work_time) ? 0 : (float)Carbon::parse($val->productive_work_time)->format('H');
                if($workHour > 0 && $workHour >= 8){
                    $val->hour = 1;
                } else if($workHour > 0 && $workHour < 8){
                    $val->hour = 0.5;
                } else {
                    $val->hour = 0;
                }
                return $val;
            });
            foreach ($shiftGroup as $k => $val) {
                $dataByIdShiftGroup[$val->id_shiftgroup] = [
                    'id_company' => $val->id_company,
                    'start' => $start,
                    'end' => $end,
                    'id_shiftgroup' => $val->id_shiftgroup,
                    'qty_days' => 0,
                    'note' => $note
                ];
                $hourByIdShiftGroup[$val->id_shiftgroup][] = $val->hour;
            }
            foreach ($hourByIdShiftGroup as $k => $val) {
                $qtyDaysByShiftGroup[$k] = array_sum($val);
            }
            foreach ($qtyDaysByShiftGroup as $k => $val) {
                $_idCompany = $dataByIdShiftGroup[$k]['id_company'];
                $_idUser = session('id_user');
                $_start = $dataByIdShiftGroup[$k]['start'];
                $_end = $dataByIdShiftGroup[$k]['end'];
                $_idShiftGroup = $dataByIdShiftGroup[$k]['id_shiftgroup'];
                $_qtyDays = $val;
                $_note = $dataByIdShiftGroup[$k]['note'];

                $dataInsert = [
                    'id_employee' => $employee->id_employee,
                    'id_shiftgroup' => $_idShiftGroup,
                    'start_date' => $_start,
                    'end_date' => $_end,
                    'qty_days' => $_qtyDays,
                    'note' => $_note,
                    'id_company' => $_idCompany,
                    'creation_date' => date('Y-m-d H:i:s'),
                    'created_by' => $_idUser,
                ];
                $insertHistory = DB::table('hr_mass_leave_request_history')->insert($dataInsert);
            }

            if($start == date('Y-m-d')){
                self::executeMassLeaveRequest();
            }
            
            DB::commit();   
            return response(['status' => 'true', 'message' => 'Mass leave request saved successfully', 'data' => null]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function executeMassLeaveRequest(Request $request)
    {
        ini_set('max_execution_time', -1);
        try{
            DB::beginTransaction();
            \Log::channel('scheduler')->info('Start Schedule : Mass Leave Request');

            $today = date('Y-m-d');
            $massLeaveRequest = DB::table('hr_mass_leave_request_history')->where('executed', '!=', true);
            if($request->execute_all != "true") {
                $massLeaveRequest->where('start_date', '<=', $today);
            }
            if($request->id_mass_leave) {
                $massLeaveRequest->where('id_mass_leave_request_history', $request->id_mass_leave);
            }
            $massLeaveRequest = $massLeaveRequest->get();

            if($massLeaveRequest->count() > 0){
                foreach ($massLeaveRequest as $k => $val) {
                    $_idCompany = $val->id_company;
                    $_idUser = $val->created_by;
                    $_idEmployee = $val->id_employee;
                    $_start = $val->start_date;
                    $_end = $val->end_date;
                    $_idShiftGroup = $val->id_shiftgroup;
                    $_qtyDays = $val->qty_days;
                    $_note = $val->note;

                    $sql = "generatemassleaverequest(".$_idCompany.",".$_idUser.",".$_idEmployee.",'".$_start."','".$_end."',".$_idShiftGroup.",".$_qtyDays.",'".$_note."') gmlr";
                    $generate = DB::table(DB::raw($sql));
                    $result = $generate->get();

                    $dataUpdate = [
                        'executed' => true,
                        'update_date' => date('Y-m-d H:i:s'),
                        'updated_by' => 1,
                    ];
                    $updateExecute = DB::table('hr_mass_leave_request_history')
                        ->where('id_mass_leave_request_history', $val->id_mass_leave_request_history)
                        ->update($dataUpdate);

                    \Log::channel('scheduler')->info($sql.'. Result: '.$result.'. Executed: true');
                }
            }
            DB::commit();   
            \Log::channel('scheduler')->info('Stop Schedule : Mass Leave Request');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::channel('scheduler')->info('Stop Schedule : Mass Leave Request. '.$e->getMessage());
        }
    }
}
