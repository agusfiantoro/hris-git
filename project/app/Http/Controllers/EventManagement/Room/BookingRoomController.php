<?php

namespace App\Http\Controllers\EventManagement\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\HrEventRoomBookingUpdateRequest;
use App\Models\Assets\HrEmployee;
use App\Models\EventManagement\HrEventManagement;
use App\Models\EventManagement\MasterEventRoom;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BookingRoomController extends Controller {
    use StandardResponse;

    public function index(Request $request) 
    {
        if($request->ajax()) {
            $date = Carbon::parse($request->date);
            $employeeLocation = self::getEmployeeLocation(session('id_user'));
            $schedule = HrEventManagement::active()
                                        ->currentCompany()
                                        ->join((new MasterEventRoom())->getTable()." as mer", "hr_event_management.id_event_room", "mer.id_event_room")
                                        ->leftJoin((new HrEmployee())->getTable()." as he", "hr_event_management.organized_by", "he.id_employee")
                                        ->where('event_category', 'Event')
                                        ->where('start_date', '>=', $date->startOfDay()->format('Y-m-d H:i:s'))
                                        ->where('end_date', '<=', $date->endOfDay()->format('Y-m-d H:i:s'))
                                        ->where('mer.status', 'A')
                                        ->whereIn('mer.id_location', $employeeLocation)
                                        ->select("hr_event_management.*", "mer.description as event_room", "he.name", "he.nik_employee as nik")
                                        ->get();
            $rooms = MasterEventRoom::active()->currentCompany()->whereIn('id_location', $employeeLocation)->get()->pluck('description');
            return $this->success([
                'schedule' => $schedule,
                'rooms' => $rooms,
                'date' => $date->format('l, d F Y'),
            ]);
        }
        return view('event_management.booking_room.index', [
            'type' => 'booking',
            'employee' => HrEmployee::where('id_user', session('id_user'))->where('status', 'A')->first(),
        ]);
    }

    public function history(Request $request)
    {
        if($request->ajax()) {
            $date = Carbon::parse($request->date);
            $employeeLocation = self::getEmployeeLocation(session('id_user'));
            $schedules = HrEventManagement::currentCompany()
                ->join((new MasterEventRoom())->getTable()." as mer", "hr_event_management.id_event_room", "mer.id_event_room")
                ->leftJoin((new HrEmployee())->getTable()." as he", "hr_event_management.organized_by", "he.id_employee")
                ->where('event_category', 'Event')
                ->where('start_date', '>=', $date->startOfDay()->format('Y-m-d H:i:s'))
                ->where('end_date', '<=', $date->endOfDay()->format('Y-m-d H:i:s'))
                ->whereIn('mer.id_location', $employeeLocation)
                ->where('mer.status', 'A')
                ->select("hr_event_management.*", "mer.description as event_room", "he.name", "he.nik_employee as nik");
            return DataTables::eloquent($schedules)
                ->addIndexColumn()
                ->toJson();
        }
        return view('event_management.booking_room.index', [
            'type' => 'history',
            'employee' => HrEmployee::where('id_user', session('id_user'))->where('status', 'A')->first(),
        ]);
    }

    public function getData()
    {
        $employeeLocations = self::getEmployeeLocation(session('id_user'));
        $rooms = MasterEventRoom::active()->currentCompany()->whereIn('id_location', $employeeLocations)->formatSelect2()->get();
        $eventTypes = MasterGeneralData::join('public.master_general_type as mgt', 'master_general_data.id_general_type', 'mgt.id_general_type')
                                        ->where('master_general_data.id_company', session('id_company'))
                                        ->where('mgt.general_type', 'master_event_type')
                                        ->get(['master_general_data.id_general_data as id', 'master_general_data.description as text']);
        $responsiblesAvailable = HrEmployee::currentCompany()
                                ->join('master_position_detail as mpd', function($join) {
                                    $join->on('hr_employee.id_employee', 'mpd.id_employee')
                                        ->orOn('hr_employee.id_employee', 'mpd.id_employee2');
                                    $join->where('mpd.secondary_position', FALSE);
                                })
                                ->whereIn('mpd.id_location', $employeeLocations)
                                ->active()
                                ->orderBy('hr_employee.name')
                                ->get(['hr_employee.id_employee as id', DB::raw("hr_employee.name || ' (' || hr_employee.nik_employee || ')' as text")]);
        return $this->success([
            'rooms' => $rooms,
            'event_types' => $eventTypes,
            'responsibles_available' => $responsiblesAvailable,
        ]);
    }

    public function getEdit(Request $request)
    {
        $request->validate([
            'id_event_management' => 'required',
        ]);
        $event = HrEventManagement::where('id_event_management', $request->id_event_management)
                            ->leftJoin((new HrEmployee())->getTable()." as he", "hr_event_management.organized_by", "he.id_employee")
                            ->select("hr_event_management.*", "he.name as name_organized_by")
                            ->first();
        $event->responsible = DB::table('public.relation_responsible_event as rre')
                            ->where('id_event_management', $event->id_event_management)
                            ->get()
                            ->pluck('id_employee')
                            ->toArray();
        $event->viewer_id_employee = HrEmployee::where('id_user', session('id_user'))->where('status', 'A')->first()->id_employee;
        return $this->success($event);
    }

    public function save(HrEventRoomBookingUpdateRequest $request)
    {
        $data = $request->only(array_keys($request->rules()));
        DB::beginTransaction();
        try {
            self::checkEventRoomAvailability($request->id_event_management, $data['id_event_room'], Carbon::parse($request->time->start), Carbon::parse($request->time->end));
            $employee = HrEmployee::where('id_user', session('id_user'))->where('status', 'A')->first();
            if($request->id_event_management) {
                $event = HrEventManagement::findOrFail($request->id_event_management);
                $event->update(array_merge($data, [
                    'start_date' => $data['time']->start,
                    'end_date' => $data['time']->end,
                    'updated_by' => session('id_user'),
                ]));
                if(key_exists('responsible', $data)) {
                    $responsibles = DB::table('public.relation_responsible_event')
                                    ->where('id_event_management', $event->id_event_management)
                                    ->get();
                    foreach($data['responsible'] as $responsibleEmployee) {
                        $dbIdEmployees = $responsibles->pluck('id_employee')->toArray();
                        if(!in_array($responsibleEmployee, $dbIdEmployees)) {
                            // Exists on input, doesnt exist on database. Insert into database.
                            DB::table('public.relation_responsible_event')->insert([
                                'id_event_management' => $event->id_event_management,
                                'id_employee' => $responsibleEmployee,
                                'id_company' => $event->id_company,
                                'created_by' => $employee->id_user,
                            ]);
                        }
                    }
                    DB::table('public.relation_responsible_event')->where('id_event_management', $event->id_event_management)->whereNotIn('id_employee', $data['responsible'])->delete();
                }
            } else {
                $event = HrEventManagement::create(array_merge($data, [
                    'start_date' => $data['time']->start,
                    'end_date' => $data['time']->end,
                    'id_employee_request' => $employee->id_employee,
                    'organized_by' => $employee->id_employee,
                    'event_category' => 'Event',
                    'created_by' => $employee->id_user,
                    'id_company' => session('id_company'),
                ]));
                if(key_exists('responsible', $data)) {
                    foreach($data['responsible'] as $responsibleEmployee) {
                        DB::table('public.relation_responsible_event')->insert([
                            'id_event_management' => $event->id_event_management,
                            'id_employee' => $responsibleEmployee,
                            'id_company' => $event->id_company,
                            'created_by' => $employee->id_user,
                        ]);
                    }
                }
            }
            DB::commit();
            return $this->success(null, "Room booking has been saved successfully");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), $e->getCode());
        }
    }

    public static function checkEventRoomAvailability($eventManagementId, int $eventRoomId, Carbon $start, Carbon $end): void
    {
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        $events = DB::table('hr_event_management as hem')
                    ->where('hem.id_event_room', $eventRoomId)
                    ->where('hem.status', 'A');
        if($eventManagementId) {
            $events->where('id_event_management', '!=', $eventManagementId);
        }
        $events = $events->select(
                    'hem.id_event_management',
                    'hem.description',
                    'hem.start_date',
                    'hem.end_date',
                    DB::raw("(hem.start_date, hem.end_date) OVERLAPS ('$start'::timestamp, '$end'::timestamp) AS overlaps")
                )
                ->get()
                ->where('overlaps', true);
        if(count($events) > 0) {
            $event = $events->first();
            throw new Exception("Event schedule overlaps with $event->description which already scheduled at $event->start_date until $event->end_date", 400);
        }
    }

    protected static function getEmployeeLocation($idUser)
    {
        return DB::table('hr_employee as he')
                ->join('master_position_detail as mpd', function($join) {
                    $join->on('he.id_employee', 'mpd.id_employee')
                        ->orOn('he.id_employee', 'mpd.id_employee2');
                    $join->where('mpd.secondary_position', FALSE);
                })
                ->where('he.status', 'A')
                ->where('he.id_user', $idUser)
                ->get(['mpd.id_location'])
                ->pluck('id_location');
    }
}