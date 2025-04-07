<?php

namespace App\Http\Controllers\TimeAttendance\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Pool;
use App\Models\TimeAttendance\Attendance\Attendance;
use App\Models\TimeAttendance\Attendance\GenerateAttendance;
use App\Models\Curl;
use App\Models\Home;
use App\Models\Employee\EmployeeSetting\WorkDays;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Validator;

class AttendanceController extends Controller {

    public function __construct()
    {
        $this->WorkDays = new WorkDays;
        $this->Home     = new Home;
    }

    public function index(Request $request) {
        DB::beginTransaction();
        try {
            $id = session('id_user');
            $avatar = asset('public/global/img/avatar.jpg');
            $employee = DB::table('hr_employee as he')
                    ->leftJoin('master_position_detail as mpd', function($join) {
                        $join->on('he.id_employee', 'mpd.id_employee');
                        $join->where('mpd.secondary_position', false);
                    })
                    ->select('he.*')
                    ->where('he.id_user', $id)
                    ->where('he.id_company', session('id_company'))
                    ->where('he.status', 'A')
                    ->first();
            $attendance = Attendance::getAttendance([
                'id_employee' => @$employee->id_employee
            ]);
            $mapboxToken    = self::getTokenMapbox();

            if(@$employee->image_attachment != null){
                if(strlen(@$employee->image_attachment) > 1000){
                    $avatar = "data:image;base64,".@$employee->image_attachment;
                } else {
                    $urlPhoto = 'public/upload/photo/'. @$employee->image_attachment;
                    if (Storage::exists($urlPhoto)) {
                        $avatar = url('project/storage/app/'.$urlPhoto);
                    } 
                }
            }

            if($attendance == null){
                $attendance = [];
            }

            //GET ABS BULAN INI DAN YANG SEBELUMNYA 
            $currentMonth = Carbon::now();
            $yearOfCurrentMonth = $currentMonth->format('Y');
            $startDateCurrentMonth = $currentMonth->startOfMonth()->toDateString();
            $endDateCurrentMonth = Carbon::now()->subDays(1)->toDateString(); //dikurangi sehari
            if(date('d') == '01'){
                $endDateCurrentMonth = $startDateCurrentMonth;
            }
            $nameOfCurrentMonth = $currentMonth->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('F');

            if(date('d') == '31'){
                $startDate = date('Y-m-d',strtotime('first day of last month'));
                $endDate = date('Y-m-d',strtotime('last day of last month'));
                $yearOfLastMonth = date('Y',strtotime('first day of last month'));
                $nameOfLastMonth = date('M',strtotime('first day of last month'));
            } else {
                $lastMonth = Carbon::now()->subMonth();
                $startDate = $lastMonth->startOfMonth()->toDateString();
                $endDate = $lastMonth->endOfMonth()->toDateString();
                $yearOfLastMonth = $lastMonth->format('Y');
                $nameOfLastMonth = $lastMonth->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('F');
            }
			if($employee != null){
				if($employee->join_date >= $startDate){
					$startDate = $employee->join_date;
				}
				
				if($employee->join_date >= $startDateCurrentMonth){
					$startDateCurrentMonth = $employee->join_date;
				}
			}
			
            $attendanceThisAndLastMonth = collect(WorkDays::getAttendanceStatus(@$employee->id_employee, $startDate, $endDateCurrentMonth, null, true));
            $getAttendanceStatusLastMonth  = Workdays::parseGetAttendanceStatus($attendanceThisAndLastMonth->whereBetween('current_dates', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]));
            $getAttendanceStatusCurrentMonth  = WorkDays::parseGetAttendanceStatus($attendanceThisAndLastMonth->whereBetween('current_dates', [now()->startOfMonth(), now()->endOfMonth()]));
            
            $absCurrentMonth = ['month' => $nameOfCurrentMonth.' '.$yearOfCurrentMonth, 'date' => ''];
            $absLastMonth = ['month' => $nameOfLastMonth.' '.$yearOfLastMonth, 'date' => ''];

            if(array_key_exists('ABS', $getAttendanceStatusCurrentMonth['by_status_in_date'])){
                $absCurrentMonth['date'] = collect($getAttendanceStatusCurrentMonth['by_status_in_date']['ABS'])->implode(', ');
            }
            if(array_key_exists('ABS', $getAttendanceStatusLastMonth['by_status_in_date'])){
                $absLastMonth['date'] = collect($getAttendanceStatusLastMonth['by_status_in_date']['ABS'])->implode(', ');
            }
            
            $redirect = [
                null                    => 'home',
                'Default_User'          => 'dashboard/dashboard_user/dashboard',
                'Default_Manager'       => 'dashboard/dashboard_management/dashboard_management',
                'Default_Administrator' => 'dashboard/dashboard_administrator/dashboard_administrator',
            ];
            $url_home = $redirect[session('access_group')];

            DB::commit();
            return view('time_attendance.attendance.index', compact('attendance','mapboxToken','avatar','url_home', 'absLastMonth', 'absCurrentMonth', 'employee'));
        } catch (\Exception $e) {
            DB::rollback();
            $url = url('logout');
            echo "<script>alert('".$e->getMessage()."'); document.location.href = '".$url."';</script>";
        }
    }
    
    public function attendance_upload(Request $request) {
        return view('time_attendance.attendance.attendance_upload');
    }

    public function attendance_upload_file(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $allFile    = [];
            $filePath   = 'public/';
            $dataReturn = [];
            $today      = date('Y-m-d');

            $validator = Validator::make($request->all(), [
                'attachment' => 'required|mimes:xlsx,xls,csv,txt' //utk csv pakai txt, kalau pake csv saja dianggap fail
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            if($request->attachment) {
                if ($request->attachment->isValid()) {
                    $fileName       = $request->attachment->getClientOriginalName();
                    $newFileName    = Str::random(3).'_'.$fileName;
                    $storageFile   = Storage::putFileAs($filePath, $request->attachment, $newFileName);
                    $allFile[]      = $newFileName;
                }
            }

            if(count($allFile) > 0){
                foreach ($allFile as $key => $file) {
                    if(Storage::disk('local')->exists($filePath.$file)){
                        $thisExt = pathinfo(storage_path('app/'.$filePath.$file))['extension'];
                        if($thisExt == 'csv'){
                            $reader = new Csv(); 
                        } else if($thisExt == 'xlsx'){
                            $reader = new Xlsx(); 
                        } else {
                            $reader = new Xls(); 
                        }
                        $reader->setReadDataOnly(true);
                        $spreadsheet    = $reader->load(storage_path('app/'.$filePath.$file));
                        $sheetData      = $spreadsheet->getActiveSheet()->toArray();
                        $dayByNik       = [];
                        $allNik         = [];
                        $patchWorkdays  = [];
                        $dateUpdate     = [];
                        $fill           = [];
                        foreach ($sheetData as $k => $row) {
                            if($k > 0){
                                $nik = $row[0];
                                if($nik != ''){
                                    $thisDate = Date::excelToDateTimeObject($row[2]);
                                    $currentDates = Carbon::parse($thisDate)->format('Y-m-d');
                                    if(!in_array($nik, $allNik)){
                                        $allNik[] = $nik;
                                    }
                                    $dayByNik[$nik][] = $currentDates;
                                    $dateUpdate[] = [
                                        'nik' => $nik,
                                        'date' => $currentDates,
                                        'day_type' => $row[3],
                                        'shift_type' => $row[4],
                                    ];
                                }
                            }
                        }
                        if(count($dayByNik) > 0){
                            foreach ($dayByNik as $key => $value) {
                                $patchWorkdays[$key] = ['start' => min($value), 'end' => max($value)];
                            }
                        }

                        $selectedEmployee = [];
                        $employee = DB::table('hr_employee as he')
                            ->select('he.id_employee', 'he.id_company', 'he.nik_employee', 'id_shift_group')
                            // ->where('he.status', 'A')
                            ->whereIn('he.nik_employee', $allNik)->get();
                        if($employee){ 
                        // proses patchworkdays dulu, antisipasi jika tanggal yg didalam file upload msh blm tergenerate 
                            if(count($patchWorkdays) > 0){
                                foreach ($employee as $key => $val) {
                                    $employee[$key]->start = $patchWorkdays[$val->nik_employee]['start'];
                                    $employee[$key]->end = $patchWorkdays[$val->nik_employee]['end'];
                                    $dataReturn[] = $val->nik_employee;

                                    // $patch = $this->WorkDays->patchWorkdays([$val->id_company], $val->start, $val->end, $val->id_employee);
                                    $selectedEmployee[$val->nik_employee][$val->id_employee] = [
                                        'id_employee' => $val->id_employee,
                                        'id_company' => $val->id_company,
                                        'id_shift_group' => $val->id_shift_group,
                                        'nik_employee' => $val->nik_employee,
                                    ];
                                }
                            }
                            if(count($dateUpdate) > 0){
                                foreach ($dateUpdate as $key => $item) {
                                    $thisNik = $item['nik'];
                                    foreach ($selectedEmployee[$thisNik] as $idEmployee_ => $valSelected) {
                                        if($thisNik == $valSelected['nik_employee']){
                                            //menambahi parameter di hasil dateUpdate sesuai isi dari nik di excel
                                            $dateUpdate[$key]['id_employee'] = $selectedEmployee[$thisNik][$idEmployee_]['id_employee'];
                                            $dateUpdate[$key]['id_company'] = $selectedEmployee[$thisNik][$idEmployee_]['id_company'];
                                            $dateUpdate[$key]['id_shift_group'] = $selectedEmployee[$thisNik][$idEmployee_]['id_shift_group'];
                                        }
                                        $thisIdCompany = $dateUpdate[$key]['id_company'];
                                        $thisIdEmployee = $dateUpdate[$key]['id_employee'];
                                        $thisDate = $item['date'];

                                        if($item['day_type'] != 'DEL'){
                                            // mencari id_shift, schedule time, day type utk diupdate di work days
                                            $getShift = DB::table('master_daily_shift as mds')
                                                        ->select('mds.id_shift', 'mds.day_type', 'mds.start_time', 'mds.end_time', 'mds.overlap_days')
                                                        ->where('mds.id_company', $thisIdCompany);
                                            if($item['day_type'] == 'OD'){
                                                $getShift->where('day_type', $item['day_type']);
                                            } else {
                                                $strShiftType = strtolower($item['shift_type']);
                                                if($strShiftType == 'regular'){
                                                    $getShift->whereRaw("LOWER(mds.shift_code) = ?",[$strShiftType]);
                                                } else if ($strShiftType == 'half'){
                                                    $getShift->whereRaw("LOWER(mds.shift_code) LIKE ?",[$strShiftType.'%']);
                                                } else {
                                                    $getShift->whereRaw("LOWER(mds.shift_code) LIKE ?",[$strShiftType.'%']);
                                                }
                                            }
                                            $shift = $getShift->first(); 
                                            
                                            $arrWorkDays = [
                                                'id_shift' => @$shift->id_shift,
                                                'day_type' => @$shift->day_type,
                                                'schedule_time_in' => $thisDate.' '.@$shift->start_time,
                                                'schedule_time_out' => $thisDate.' '.@$shift->end_time,
                                                'update_date' => date('Y-m-d H:i:s'),
                                                'updated_by' => session('id_user'),
                                            ];
                                            //update workdays sesuai tgl dan shift type di excel
                                            $update = DB::table('hr_work_days as hwd')
                                                        ->where('hwd.id_employee', $thisIdEmployee)
                                                        ->where('hwd.id_company', $thisIdCompany)
                                                        // ->whereNull('hwd.actual_time_in')
                                                        // ->whereNull('hwd.actual_time_out')
                                                        ->whereNull('hwd.id_holiday')
                                                        ->where(function($query) use ($today, $thisDate){
                                                            $query->whereDate('hwd.current_dates', $thisDate);
                                                                // ->whereDate('hwd.current_dates', '>=', $today);
                                                        })
                                                        ->update($arrWorkDays);
                                        } else {
                                            $getThisWorkDay = DB::table('hr_work_days as hwd')
                                                ->where('hwd.id_employee', $thisIdEmployee)
                                                ->where('hwd.id_company', $thisIdCompany)
                                                ->where(function($query) use ($today, $thisDate){
                                                    $query->whereDate('hwd.current_dates', $thisDate);
                                                        // ->whereDate('hwd.current_dates', '>=', $today);
                                                });
                                            $thisWD = $getThisWorkDay->first();
                                            if (@$thisWD->image_attachment_in && Storage::exists($filePath.'images/'.@$thisWD->image_attachment_in)) {
                                                Storage::delete($filePath.'images/'.@$thisWD->image_attachment_in);
                                            }
                                            if (@$thisWD->image_attachment_out && Storage::exists($filePath.'images/'.@$thisWD->image_attachment_out)) {
                                                Storage::delete($filePath.'images/'.@$thisWD->image_attachment_out);
                                            }
                                            $arrWorkDays = [
                                                'actual_time_in' => null,
                                                'actual_time_out' => null,
                                                'current_name_in' => null,
                                                'current_name_out' => null,
                                                'current_address_in' => null,
                                                'current_address_out' => null,
                                                'current_latitude_in' => null,
                                                'current_latitude_out' => null,
                                                'current_longitude_in' => null,
                                                'current_longitude_out' => null,
                                                'image_attachment_in' => null,
                                                'image_attachment_out' => null,
                                                'update_date' => date('Y-m-d H:i:s'),
                                                'updated_by' => session('id_user'),
                                            ];
                                            $update = $getThisWorkDay->update($arrWorkDays);
                                        }
                                    }
                                }
                            }
                        }
                        Storage::delete($filePath.$file);
                    }
                }
            }
            DB::commit();   
            return response(['status' => 'true', 'message' => 'Upload Success', 'data' => $dataReturn]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function allAttendance() {
        $data = Attendance::get();
        return response([
            'status' => 'Success',
            'data' => $data,
        ], 200);
    }

    public static function getTokenMapbox() {
        $mapboxToken = Attendance::getTokenMapbox();
        return $mapboxToken;
    }

    public function getPlaceTimezoneMapbox(Request $request) {
        $lng            = $request->lng;
        $lat            = $request->lat;
        $zone_workdays  = $request->zone_workdays;
        $t['properties']['TZID'] = $zone_workdays;

        if($lng==0 && $lat = 0){
            return response()->json(['result' => false]);
        }
        $newmapToken        = self::getTokenMapbox();
        $mapboxGetPlace     = Curl::findApi('mapbox_getplace');
        $mapboxGetTimezone  = Curl::findApi('mapbox_gettimezone');
        $urlGetPlace        = $mapboxGetPlace->url;
        $urlGetTimezone     = $mapboxGetTimezone->url;

        $urls = collect([
            'urlGetTimezone' => $urlGetTimezone.$lng.','.$lat.'.json?access_token='.$newmapToken,
            'urlGetPlace' => $urlGetPlace.$lng.','.$lat.'.json?access_token='.$newmapToken
        ]);

        $httpPool = function (\Illuminate\Http\Client\Pool $pool) use ($urls) {
            foreach ($urls as $aKey => $aVal) {
                $arrayPools[] = $pool->async()->get($aVal);
            }
            return $arrayPools;
        };
        $responses = \Illuminate\Support\Facades\Http::pool($httpPool);

        $resTimezone = json_decode($responses[0]->getBody()->getContents());
        $getTimeZone = (count($resTimezone->features) > 0) ? $resTimezone->features[0] : $t;
        $return['timezone'] = $getTimeZone;

        $resPlace = json_decode($responses[1]->getBody()->getContents());
        $getPlace = (count($resPlace->features) > 0) ? $resPlace->features[1] : $getPlace;
        $return['place'] = $getPlace;

        //================================= OLD ===========================
        // $getTimeZone        = Http::get($urlGetTimezone.$lng.','.$lat.'.json?access_token='.$newmapToken)['features'];
        // $return['place']    = Http::get($urlGetPlace.$lng.','.$lat.'.json?access_token='.$newmapToken)['features'][1];
        // $return['timezone'] = count($getTimeZone) > 0 ? $getTimeZone[0] : $t;
        
        return response()->json($return);
    }

    public function getServerTime(Request $request) {
        $return['Y']        = date('Y');
        $return['M']        = date('m');
        $return['D']        = date('d');
        $return['hour']     = date('H');
        $return['minute']   = date('i');
        return response()->json($return);
    }

    public function resizeImage($file, $saveTo) {
        $saveImage = Image::make($file)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save(storage_path('app/'. $saveTo));
        return response()->json(['file' => $saveTo]);
    }
    
    public function update(Request $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $id = $request->get('id');
            $attendance = Attendance::find($id);

            if(is_null($attendance)){
                throw new \Exception("Attendance tidak ditemukan", 404);
            }

            $updateData = $request->all();
            $validate = Validator::make($updateData, [
                'current_latitude' => 'required',
                'current_longitude' => 'required',
                'image_attachment' => 'required',
                'shift' => 'required',
            ]);

            if($validate->fails()){
                throw new \Exception($validate->errors(), 400);
            }

            // $currentDate = Carbon::now();
            $filepath           = 'public/images/';
            $starttimeabsen     = $updateData['starttimeabsen'];
            $endtimeabsen       = $updateData['endtimeabsen'];
            $map_place          = $updateData['map_place'];
            $map_timezone       = (is_array(@$updateData['map_timezone'])) ? @$updateData['map_timezone']['properties']['TZID'] : $updateData['map_timezone'];

            $image_parts        = explode(";base64,", $updateData['image_attachment']);
            $image_type_aux     = explode("image/", $image_parts[0]);
            $image_type         = $image_type_aux[1];
            $image_base64       = base64_decode($image_parts[1]);
            $imageName          = Str::random(10).'_'.time().'.' . $image_type;
            

            $check_allow_checkout_nextdays = Attendance::check_allow_checkout_nextdays($attendance->id_employee);
            $check_empty_actual_timeout_before = Attendance::check_empty_actual_timeout_before($attendance->id_employee);

            if($updateData['current_latitude'] == '0'){
                if(!is_null($attendance->current_latitude_in) || $attendance->current_latitude_in != '0'){ // jika lat_in ada isi maka pakai itu
                    $updateData['current_latitude'] = @$attendance->current_latitude_in;
                    @$map_place['text'] = @$attendance->current_name_in;
                    @$map_place['place_name'] = @$attendance->current_address_in;
                } else if(!is_null(@$check_empty_actual_timeout_before['data'][0]->current_latitude_out) || @$check_empty_actual_timeout_before['data'][0]->current_latitude_out != '0'){ // jika lat_out hari sebelumnya ada isi maka pakai itu
                    $updateData['current_latitude'] = @$check_empty_actual_timeout_before['data'][0]->current_latitude_out;
                    @$map_place['text'] = @$check_empty_actual_timeout_before['data'][0]->current_name_out;
                    @$map_place['place_name'] = @$check_empty_actual_timeout_before['data'][0]->current_address_out;
                } else { // jika tidak pakai lat in hari sebelumnya
                    $updateData['current_latitude'] = @$check_empty_actual_timeout_before['data'][0]->current_latitude_in;
                    @$map_place['text'] = @$check_empty_actual_timeout_before['data'][0]->current_name_in;
                    @$map_place['place_name'] = @$check_empty_actual_timeout_before['data'][0]->current_address_in;
                }
            }
            if($updateData['current_longitude'] == '0'){
                if(!is_null(@$attendance->current_longitude_in) || @$attendance->current_longitude_in != '0'){ // jika lng_in ada isi maka pakai itu
                    $updateData['current_longitude'] = @$attendance->current_longitude_in;
                } else if(!is_null(@$check_empty_actual_timeout_before['data'][0]->current_longitude_out) || @$check_empty_actual_timeout_before['data'][0]->current_longitude_out != '0'){ // jika lng_out hari sebelumnya ada isi maka pakai itu
                    $updateData['current_longitude'] = @$check_empty_actual_timeout_before['data'][0]->current_longitude_out;
                } else { // jika tidak pakai lng in hari sebelumnya
                    $updateData['current_longitude'] = @$check_empty_actual_timeout_before['data'][0]->current_longitude_in;
                }
            }

            if(@$check_allow_checkout_nextdays['data']->allow_checkout_nextdays=="1" || @$check_empty_actual_timeout_before['data'][0]->id_request!=NULL){ //Jika jadwal checkin kemarin tapi checkout di esok hari (utk yg masuk shift) atau jika di workdays terdapat request leave atau cuti, dll. atau jika settingan work hour pd karyawan tsb memiliki allow checkout next days

                if($starttimeabsen!=NULL){
                    $currentDate = $starttimeabsen;
                } else if($endtimeabsen!=NULL){
                    $currentDate = $endtimeabsen;
                }
                
                if(@$check_empty_actual_timeout_before['data'][1]->actual_time_out==NULL && @$check_empty_actual_timeout_before['data'][1]->actual_time_in!=NULL){

                    if(@$check_empty_actual_timeout_before['data'][0]->id_request!=NULL){
                        $attendance = Attendance::find($check_empty_actual_timeout_before['data'][0]->id_workdays);
                        //Storage::put($filepath.$imageName, $image_base64);
                        $this->resizeImage($image_base64, $filepath.$imageName);
                        if(@$check_empty_actual_timeout_before['data'][0]->actual_time_in == NULL) {
                            if (Storage::exists($filepath.$attendance->image_attachment_in)) {
                                Storage::delete($filepath.$attendance->image_attachment_in);
                            }
                            
                            $attendance->actual_time_in             = $currentDate;
                            $attendance->current_latitude_in        = $updateData['current_latitude'];
                            $attendance->current_longitude_in       = $updateData['current_longitude'];
                            $attendance->image_attachment_in        = $imageName;
                            
                            $attendance->current_name_in            = @$map_place['text'];
                            $attendance->current_address_in         = @$map_place['place_name'];
                            $attendance->current_employee_timezone  = $map_timezone;
                            
                            $late_in = date_diff(date_create($attendance->schedule_time_in),date_create($attendance->actual_time_in));
                            $late_in_h = $late_in->format("%h")<10?"0".$late_in->format("%h"):$late_in->format("%h");
                            $late_in_i = $late_in->format("%i")<10?"0".$late_in->format("%i"):$late_in->format("%i");
                            $late_in_s = $late_in->format("%s")<10?"0".$late_in->format("%s"):$late_in->format("%s");
    
                            if(strtotime($attendance->actual_time_in) > strtotime($attendance->schedule_time_in)){
                                $attendance->late_in = $late_in_h.":".$late_in_i.":".$late_in_s;
                            }
                        } else {
                            if (Storage::exists($filepath.$attendance->image_attachment_out)) {
                                Storage::delete($filepath.$attendance->image_attachment_out);
                            }
        
                            $attendance->actual_time_out            = $currentDate;
                            $attendance->current_latitude_out       = $updateData['current_latitude'];
                            $attendance->current_longitude_out      = $updateData['current_longitude'];
                            $attendance->image_attachment_out       = $imageName;
                            
                            $attendance->current_name_out           = @$map_place['text'];
                            $attendance->current_address_out        = @$map_place['place_name'];
                            $attendance->current_employee_timezone  = $map_timezone;
                            
                            $work_hours = date_diff(date_create($attendance->actual_time_out),date_create($attendance->actual_time_in));
                            $work_hours_h = $work_hours->format("%h")<10?"0".$work_hours->format("%h"):$work_hours->format("%h");
                            $work_hours_i = $work_hours->format("%i")<10?"0".$work_hours->format("%i"):$work_hours->format("%i");
                            $work_hours_s = $work_hours->format("%s")<10?"0".$work_hours->format("%s"):$work_hours->format("%s");
                            $attendance->work_hours = $work_hours_h.":".$work_hours_i.":".$work_hours_s;
                            
                            if(strtotime($attendance->actual_time_out)>strtotime($attendance->schedule_time_out)){
                                $overtime = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                                $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                                $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                                $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                                $attendance->overtime = $overtime_h.":".$overtime_i.":".$overtime_s;
                            }
                            else if(strtotime($attendance->actual_time_out)<strtotime($attendance->schedule_time_out))
                            {
                                $early_out = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                                $early_out_h = $early_out->format("%h")<10?"0".$early_out->format("%h"):$early_out->format("%h");
                                $early_out_i = $early_out->format("%i")<10?"0".$early_out->format("%i"):$early_out->format("%i");
                                $early_out_s = $early_out->format("%s")<10?"0".$early_out->format("%s"):$early_out->format("%s");
                                $attendance->early_out = $early_out_h.":".$early_out_i.":".$early_out_s;
                            }
                        }
                    } else {
                        $attendance = Attendance::find($check_empty_actual_timeout_before['data'][1]->id_workdays);
                        if (Storage::exists($filepath.$attendance->image_attachment_out)) {
                            Storage::delete($filepath.$attendance->image_attachment_out);
                        }
                        //Storage::put($filepath.$imageName, $image_base64);
                        $this->resizeImage($image_base64, $filepath.$imageName);
    
                        $attendance->actual_time_out            = $currentDate;
                        $attendance->current_latitude_out       = $updateData['current_latitude'];
                        $attendance->current_longitude_out      = $updateData['current_longitude'];
                        $attendance->image_attachment_out       = $imageName;
                        
                        $attendance->current_name_out           = @$map_place['text'];
                        $attendance->current_address_out        = @$map_place['place_name'];
                        $attendance->current_employee_timezone  = $map_timezone;
                        
                        $work_hours = date_diff(date_create($attendance->actual_time_out),date_create($attendance->actual_time_in));
                        $work_hours_h = $work_hours->format("%h")<10?"0".$work_hours->format("%h"):$work_hours->format("%h");
                        $work_hours_i = $work_hours->format("%i")<10?"0".$work_hours->format("%i"):$work_hours->format("%i");
                        $work_hours_s = $work_hours->format("%s")<10?"0".$work_hours->format("%s"):$work_hours->format("%s");
                        $attendance->work_hours = $work_hours_h.":".$work_hours_i.":".$work_hours_s;
                        
                        if(strtotime($attendance->actual_time_out)>strtotime($attendance->schedule_time_out)){
                            $overtime = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                            $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                            $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                            $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                            $attendance->overtime = $overtime_h.":".$overtime_i.":".$overtime_s;
                        }
                        else if(strtotime($attendance->actual_time_out)<strtotime($attendance->schedule_time_out))
                        {
                            $early_out = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                            $early_out_h = $early_out->format("%h")<10?"0".$early_out->format("%h"):$early_out->format("%h");
                            $early_out_i = $early_out->format("%i")<10?"0".$early_out->format("%i"):$early_out->format("%i");
                            $early_out_s = $early_out->format("%s")<10?"0".$early_out->format("%s"):$early_out->format("%s");
                            $attendance->early_out = $early_out_h.":".$early_out_i.":".$early_out_s;
                        }
                    }
                    
                } else {
                    if(@$updateData['shift'] == 'start') {
                        $str_now = strtotime($currentDate);
                        $str_schedule_in = strtotime($attendance->schedule_time_in);

                        if(@$check_empty_actual_timeout_before['data'][0]->actual_time_in==NULL){
                            if (Storage::exists($filepath.$attendance->image_attachment_in)) {
                                Storage::delete($filepath.$attendance->image_attachment_in);
                            }
                            //Storage::put($filepath.$imageName, $image_base64);
                            $this->resizeImage($image_base64, $filepath.$imageName);

                            $attendance->actual_time_in             = $currentDate;
                            $attendance->current_latitude_in        = $updateData['current_latitude'];
                            $attendance->current_longitude_in       = $updateData['current_longitude'];
                            $attendance->image_attachment_in        = $imageName;
                            
                            $attendance->current_name_in            = @$map_place['text'];
                            $attendance->current_address_in         = @$map_place['place_name'];
                            $attendance->current_employee_timezone  = $map_timezone;
                            
                            $late_in = date_diff(date_create($attendance->schedule_time_in),date_create($attendance->actual_time_in));
                            $late_in_h = $late_in->format("%h")<10?"0".$late_in->format("%h"):$late_in->format("%h");
                            $late_in_i = $late_in->format("%i")<10?"0".$late_in->format("%i"):$late_in->format("%i");
                            $late_in_s = $late_in->format("%s")<10?"0".$late_in->format("%s"):$late_in->format("%s");

                            if(strtotime($attendance->actual_time_in) > strtotime($attendance->schedule_time_in)){
                                $attendance->late_in = $late_in_h.":".$late_in_i.":".$late_in_s;
                            }
                        } 
                        else if($str_now <= $str_schedule_in){
                            // jika sudah cekin, namun melakukan cekin lagi pada jam sebelum schedule in maka tidak ada perubahan
                        } else {
                            //jika setelah shift 3 sudah cekout lalu lepas dari range shift 3 ke workdays selanjutnya
                            if (Storage::exists($filepath.$attendance->image_attachment_in)) {
                                Storage::delete($filepath.$attendance->image_attachment_in);
                            }
                            //Storage::put($filepath.$imageName, $image_base64);
                            $this->resizeImage($image_base64, $filepath.$imageName);

                            $attendance->actual_time_in             = $currentDate;
                            $attendance->current_latitude_in        = $updateData['current_latitude'];
                            $attendance->current_longitude_in       = $updateData['current_longitude'];
                            $attendance->image_attachment_in        = $imageName;
                            
                            $attendance->current_name_in            = @$map_place['text'];
                            $attendance->current_address_in         = @$map_place['place_name'];
                            $attendance->current_employee_timezone  = $map_timezone;
                            
                            $late_in = date_diff(date_create($attendance->schedule_time_in),date_create($attendance->actual_time_in));
                            $late_in_h = $late_in->format("%h")<10?"0".$late_in->format("%h"):$late_in->format("%h");
                            $late_in_i = $late_in->format("%i")<10?"0".$late_in->format("%i"):$late_in->format("%i");
                            $late_in_s = $late_in->format("%s")<10?"0".$late_in->format("%s"):$late_in->format("%s");

                            if(strtotime($attendance->actual_time_in) > strtotime($attendance->schedule_time_in)){
                                $attendance->late_in = $late_in_h.":".$late_in_i.":".$late_in_s;
                            }
                        }
                    }
                    else {
                        $attendance_b = Attendance::find($check_empty_actual_timeout_before['data'][0]->id_workdays);
                        $diff   = abs(strtotime($attendance_b->schedule_time_out) - strtotime($currentDate));
                        $diff_2 = abs(strtotime($attendance->schedule_time_out) - strtotime($currentDate)); 
                        if($diff < $diff_2 || $attendance_b->actual_time_out == null){ 

                            if($attendance->actual_time_in != null){
                                if (Storage::exists($filepath.$attendance->image_attachment_out)) {
                                    Storage::delete($filepath.$attendance->image_attachment_out);
                                }
                                //Storage::put($filepath.$imageName, $image_base64);
                                $this->resizeImage($image_base64, $filepath.$imageName);

                                $attendance->actual_time_out            = $endtimeabsen;
                                $attendance->current_latitude_out       = $updateData['current_latitude'];
                                $attendance->current_longitude_out      = $updateData['current_longitude'];
                                $attendance->image_attachment_out       = $imageName;
                                
                                $attendance->current_name_out           = @$map_place['text'];
                                $attendance->current_address_out        = @$map_place['place_name'];
                                $attendance->current_employee_timezone  = $map_timezone;
                                
                                $work_hours = date_diff(date_create($attendance->actual_time_out),date_create($attendance->actual_time_in));
                                $work_hours_h = $work_hours->format("%h")<10?"0".$work_hours->format("%h"):$work_hours->format("%h");
                                $work_hours_i = $work_hours->format("%i")<10?"0".$work_hours->format("%i"):$work_hours->format("%i");
                                $work_hours_s = $work_hours->format("%s")<10?"0".$work_hours->format("%s"):$work_hours->format("%s");
                                $attendance->work_hours = $work_hours_h.":".$work_hours_i.":".$work_hours_s;
                                
                                if(strtotime($attendance->actual_time_out)>strtotime($attendance->schedule_time_out)){
                                    $overtime = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                                    $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                                    $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                                    $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                                    $attendance->overtime = $overtime_h.":".$overtime_i.":".$overtime_s;
                                }
                                else if(strtotime($attendance->actual_time_out)<strtotime($attendance->schedule_time_out)){
                                    $early_out = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                                    $early_out_h = $early_out->format("%h")<10?"0".$early_out->format("%h"):$early_out->format("%h");
                                    $early_out_i = $early_out->format("%i")<10?"0".$early_out->format("%i"):$early_out->format("%i");
                                    $early_out_s = $early_out->format("%s")<10?"0".$early_out->format("%s"):$early_out->format("%s");
                                    $attendance->early_out = $early_out_h.":".$early_out_i.":".$early_out_s;
                                }
                            } else {
                                // kondisi jika setelah shift 3, sudah cekout lalu melakukan cekout lagi
                                if (Storage::exists($filepath.$attendance_b->image_attachment_out)) {
                                    Storage::delete($filepath.$attendance_b->image_attachment_out);
                                }
                                //Storage::put($filepath.$imageName, $image_base64);
                                $this->resizeImage($image_base64, $filepath.$imageName);

                                $attendance_b->actual_time_out            = $currentDate;
                                $attendance_b->current_latitude_out       = $updateData['current_latitude'];
                                $attendance_b->current_longitude_out      = $updateData['current_longitude'];
                                $attendance_b->image_attachment_out       = $imageName;
                                
                                $attendance_b->current_name_out           = @$map_place['text'];
                                $attendance_b->current_address_out        = @$map_place['place_name'];
                                $attendance_b->current_employee_timezone  = $map_timezone;

                                $work_hours = date_diff(date_create($attendance_b->actual_time_out),date_create($attendance_b->actual_time_in));
                                $work_hours_h = $work_hours->format("%h")<10?"0".$work_hours->format("%h"):$work_hours->format("%h");
                                $work_hours_i = $work_hours->format("%i")<10?"0".$work_hours->format("%i"):$work_hours->format("%i");
                                $work_hours_s = $work_hours->format("%s")<10?"0".$work_hours->format("%s"):$work_hours->format("%s");
                                $attendance_b->work_hours = $work_hours_h.":".$work_hours_i.":".$work_hours_s;
                                if(!$attendance_b->save()){
                                    throw new \Exception("Attendance Failed to save");
                                }
                            }
                        } else {
                            // kondisi jika checkout belum dilakukan
                            if (Storage::exists($filepath.$attendance->image_attachment_out)) {
                                Storage::delete($filepath.$attendance->image_attachment_out);
                            }
                            //Storage::put($filepath.$imageName, $image_base64);
                            $this->resizeImage($image_base64, $filepath.$imageName);

                            $attendance->actual_time_out            = $currentDate;
                            $attendance->current_latitude_out       = $updateData['current_latitude'];
                            $attendance->current_longitude_out      = $updateData['current_longitude'];
                            $attendance->image_attachment_out       = $imageName;
                            
                            $attendance->current_name_out           = @$map_place['text'];
                            $attendance->current_address_out        = @$map_place['place_name'];
                            $attendance->current_employee_timezone  = $map_timezone;
                            
                            $work_hours = date_diff(date_create($attendance->actual_time_out),date_create($attendance->actual_time_in));
                            $work_hours_h = $work_hours->format("%h")<10?"0".$work_hours->format("%h"):$work_hours->format("%h");
                            $work_hours_i = $work_hours->format("%i")<10?"0".$work_hours->format("%i"):$work_hours->format("%i");
                            $work_hours_s = $work_hours->format("%s")<10?"0".$work_hours->format("%s"):$work_hours->format("%s");
                            $attendance->work_hours = $work_hours_h.":".$work_hours_i.":".$work_hours_s;
                            
                            if(strtotime($attendance->actual_time_out)>strtotime($attendance->schedule_time_out)){
                                $overtime = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                                $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                                $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                                $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                                $attendance->overtime = $overtime_h.":".$overtime_i.":".$overtime_s;
                            }
                            else if(strtotime($attendance->actual_time_out)<strtotime($attendance->schedule_time_out)){
                                $early_out = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                                $early_out_h = $early_out->format("%h")<10?"0".$early_out->format("%h"):$early_out->format("%h");
                                $early_out_i = $early_out->format("%i")<10?"0".$early_out->format("%i"):$early_out->format("%i");
                                $early_out_s = $early_out->format("%s")<10?"0".$early_out->format("%s"):$early_out->format("%s");
                                $attendance->early_out = $early_out_h.":".$early_out_i.":".$early_out_s;
                            }
                        }
                    }
                }
            } else { // JIKA GROUP SHIFT TANPA ALLOW NEXT DAYS 

                if($starttimeabsen!=NULL && @$attendance->actual_time_in==NULL) { // Jika absen IN type selain shift
                    if (Storage::exists($filepath.$attendance->image_attachment_in)) {
                        Storage::delete($filepath.$attendance->image_attachment_in);
                    }
                    //Storage::put($filepath.$imageName, $image_base64);
                    $this->resizeImage($image_base64, $filepath.$imageName);

                    $attendance->actual_time_in             = $starttimeabsen;
                    $attendance->current_latitude_in        = $updateData['current_latitude'];
                    $attendance->current_longitude_in       = $updateData['current_longitude'];
                    $attendance->image_attachment_in        = $imageName;
                    
                    $attendance->current_name_in            = @$map_place['text'];
                    $attendance->current_address_in         = @$map_place['place_name'];
                    $attendance->current_employee_timezone  = $map_timezone;
                    
                    $late_in = date_diff(date_create($attendance->schedule_time_in),date_create($attendance->actual_time_in));
                    $late_in_h = $late_in->format("%h")<10?"0".$late_in->format("%h"):$late_in->format("%h");
                    $late_in_i = $late_in->format("%i")<10?"0".$late_in->format("%i"):$late_in->format("%i");
                    $late_in_s = $late_in->format("%s")<10?"0".$late_in->format("%s"):$late_in->format("%s");

                    if(strtotime($attendance->actual_time_in) > strtotime($attendance->schedule_time_in)){
                        $attendance->late_in = $late_in_h.":".$late_in_i.":".$late_in_s;
                    }
                }
                else if($endtimeabsen!=NULL) { // Jika absen OUT type selain shift
                    
                    if (Storage::exists($filepath.$attendance->image_attachment_out)) {
                        Storage::delete($filepath.$attendance->image_attachment_out);
                    }
                    //Storage::put($filepath.$imageName, $image_base64);
                    $this->resizeImage($image_base64, $filepath.$imageName);

                    $attendance->actual_time_out            = $endtimeabsen;
                    $attendance->current_latitude_out       = $updateData['current_latitude'];
                    $attendance->current_longitude_out      = $updateData['current_longitude'];
                    $attendance->image_attachment_out       = $imageName;
                    
                    $attendance->current_name_out           = @$map_place['text'];
                    $attendance->current_address_out        = @$map_place['place_name'];
                    $attendance->current_employee_timezone  = $map_timezone;
                    
                    $work_hours = date_diff(date_create($attendance->actual_time_out),date_create($attendance->actual_time_in));
                    $work_hours_h = $work_hours->format("%h")<10?"0".$work_hours->format("%h"):$work_hours->format("%h");
                    $work_hours_i = $work_hours->format("%i")<10?"0".$work_hours->format("%i"):$work_hours->format("%i");
                    $work_hours_s = $work_hours->format("%s")<10?"0".$work_hours->format("%s"):$work_hours->format("%s");
                    $attendance->work_hours = $work_hours_h.":".$work_hours_i.":".$work_hours_s;
                    
                    if(strtotime($attendance->actual_time_out)>strtotime($attendance->schedule_time_out)){
                        $overtime = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                        $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                        $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                        $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                        $attendance->overtime = $overtime_h.":".$overtime_i.":".$overtime_s;
                    }
                    else if(strtotime($attendance->actual_time_out)<strtotime($attendance->schedule_time_out)){
                        $early_out = date_diff(date_create($attendance->schedule_time_out),date_create($attendance->actual_time_out));
                        $early_out_h = $early_out->format("%h")<10?"0".$early_out->format("%h"):$early_out->format("%h");
                        $early_out_i = $early_out->format("%i")<10?"0".$early_out->format("%i"):$early_out->format("%i");
                        $early_out_s = $early_out->format("%s")<10?"0".$early_out->format("%s"):$early_out->format("%s");
                        $attendance->early_out = $early_out_h.":".$early_out_i.":".$early_out_s;
                    }
                }
            }

            if($request->shift == "start") {
                if($attendance->current_latitude_in == null || $attendance->current_longitude_in == null) {
                    throw new \Exception("Failed saving attendance");
                }
            } else {
                if($attendance->current_latitude_out == null || $attendance->current_longitude_out == null) {
                    throw new \Exception("Failed saving attendance");
                }
            }
            if(!$attendance->save()){
                throw new \Exception("Attendance Failed to save");
            }
            $message = 'Attendance berhasil direkam';
            DB::commit();
            return response(['status' => 'Success', 'message' => $message, 'data' => $attendance], 200);
        } catch (\Exception $e) {
            DB::rollback();
            $httpCode   = $e->getCode();
            $code       = $httpCode == 0 ? 404 : (strlen($httpCode) > 3 ? 500 : $httpCode) ;
            $message    = $e->getMessage() ? $e->getMessage() : 'Error!' ;
            return response(['status' => 'Failed', 'message' => $message, 'data' => null], $code);
        }
    }

    public function generateWorkdays()
    {
        ini_set('max_execution_time', -1);
        try{
            DB::beginTransaction();
            $today      = date('Y-m-d');
            $company    = DB::table('master_company as mc')->get();
            foreach ($company as $key => $item) {
                $id_company = $item->id_company;
                $sql_daily = "select * from GenerateWorkdaysDaily(?, ?, ?, ?)";
                $generateworksdaydaily = DB::select($sql_daily, [$id_company, $today, null, 1]);
            }
            DB::commit();   
            \Log::channel('scheduler')->info('Generate Workdays Success');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::channel('scheduler')->error('Generate Workdays Failed. '.$e);
        }
    }

     public function generateWorkdaysByEmployee(Request $request)
    {
        ini_set('max_execution_time', -1);
        try{
            $id_company = $request->id_company;
            $nik = $request->nik;
            $id_employee = $request->id_employee ?? NULL;

            DB::beginTransaction();
        /*    if(!$id_company || !$nik ){
                return true;
            }
		*/
			if($id_employee){
				$employee = DB::table('hr_employee as he')->select('he.id_employee', 'he.join_date')->where('he.id_employee', $id_employee)->first();
			}
			else{
				$employee = DB::table('hr_employee as he')->select('he.id_employee', 'he.join_date')->where('he.nik_employee', $nik)->first();
			}
            if(!$employee){
                return true;
            }
            $today      = date('Y-m-d');
            $str_join   = strtotime($employee->join_date."+1 month");
            $str_today  = strtotime($today);
            $start      = ($str_join < $str_today) ? $today : $employee->join_date;
            $end        = date('Y-m-d', strtotime($start."+1 month"));

            $generateworksdaydaily = $this->WorkDays->patchWorkdays([$id_company], $start, $end, $employee->id_employee);

            // $generateworksdaydaily = DB::table(DB::raw("generateworkdaysnewemployee(".$employee->id_employee.",".$id_company.",'".$start."','".$end."',".session('id_user').") g"))->select('g.*')->get();

            if(!$generateworksdaydaily){
                throw new \Exception("Failed generate workdays");
            }
            DB::commit();   
            Log::info('Generate Workdays Success');
            return response(['status' => 'Success', 'message' => 'Generate Workdays Success', 'data' => $start]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Generate Workdays Failed. '.$e->getMessage());
            return response(['status' => 'Failed', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public static function generateWorkdaysByDate($startDate = null, $endDate = null) {
        $t = now();
        $maxDate = $endDate ? Carbon::parse($endDate) : now()->addDays(45);
        $currentDate = $startDate ? Carbon::parse($startDate) : now();
        // \Log::channel('scheduler')->info('Attempting to generate workdays from '.$currentDate->format('Y-m-d').' to ');
        $company    = DB::table('master_company as mc')->orderBy('id_company')->get();
        foreach($company as $key => $item) {
            $maxQuery = DB::selectOne("SELECT max(hwd.current_dates) FROM hr_work_days hwd WHERE id_company = ?", [$item->id_company])->max;
            $maxDate = $endDate ? Carbon::parse($endDate) : Carbon::parse($maxQuery)->format('Y-m-d');
            $i = 0;
            while($currentDate <= $maxDate && $i < 45) {
                try {
                    // dump('['.gmdate('H:i:s', now()->diffInSeconds($t)).'] '.$maxQuery.' ['.$currentDate->format('Y-m-d').'] '.$item->company_name);
                    $id_company = $item->id_company;
                    $sql_daily = "select * from GenerateWorkdaysDaily(?, ?, ?, ?)";
                    $generateworksdaydaily = DB::select($sql_daily, [$id_company, $currentDate->format('Y-m-d'), $currentDate->format('Y-m-d'), 1]);
                } catch(\Exception $e) {
                    \Log::channel('scheduler')->error('Error generating workdays ('.$item->company_name.' '.$currentDate->format('Y-m-d').'): '.$e->getMessage());
                }
                $i++;
                $currentDate->addDay();
            }
            $currentDate = $startDate ? Carbon::parse($startDate) : now()->subDay();
        }
        
        \Log::channel('scheduler')->info('Stop Schedule : Generate Workdays. Elapsed time: '.gmdate('H:i:s', now()->diffInSeconds($t)));
    }
}