<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Models\API\Employee;
use App\Models\API\Attendance;
use App\Models\Curl;
use App\Models\Home;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;
use stdClass;

class AttendanceController extends BaseController
{
    public function __construct()
    {
        $this->AttendanceWeb = new \App\Models\TimeAttendance\Attendance\Attendance;
    }

    public function resizeImage($file, $saveTo) {
        $saveImage = Image::make($file)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save(storage_path('app/'. $saveTo));
        return response()->json(['file' => $saveTo]);
    }

    public function convertZone($zone) {
        $zone_ = strtolower($zone);
        $data = [
            'wib'           => 'Asia/Jakarta',
            'wita'          => 'Asia/Makassar',
            'wit'           => 'Asia/Jayapura',
            'asia/jakarta'  => 'Asia/Jakarta',
            'asia/makassar' => 'Asia/Makassar',
            'asia/jayapura' => 'Asia/Jayapura',
        ];
        return $data[$zone_];
    }

    public function listAttendance(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required'
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $result = Employee::getEmployeeDetail($nik);
            if(count($result) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee       = $result[0];
            $attendance     = Attendance::getAttendance($employee);
            $attendanceStatus = Attendance::getAttendanceStatus($employee);
            $attendanceLastMonthByFormatDate = $attendanceStatus['last_month']['by_status_in_format_date'];
            $attendanceCurrentMonthByFormatDate = $attendanceStatus['current_month']['by_status_in_format_date'];

            $absLastMonth = array_key_exists('ABS', $attendanceLastMonthByFormatDate) ? $attendanceLastMonthByFormatDate['ABS'] : [];
            $absCurrentMonth = array_key_exists('ABS', $attendanceCurrentMonthByFormatDate) ? $attendanceCurrentMonthByFormatDate['ABS'] : [];
            $mergeAttendanceStatus = collect($absLastMonth)->merge($absCurrentMonth);
            $absentDays = (count($mergeAttendanceStatus) > 0) ? $mergeAttendanceStatus : null;
            
            $serverTime = [
                'year'      => date('Y'),
                'month'     => date('m'),
                'day'       => date('d'),
                'hour'      => date('H'),
                'minute'    => date('i'),
                'second'    => date('s'),
            ];

            $result = [
                'server_time'       => $serverTime,
                'absent_days'       => $absentDays,
                'workdays'          => $attendance,
            ];
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function getMapToken(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            /*$parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required'
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }*/

            $mapboxToken    = $this->AttendanceWeb->getTokenMapbox();

            /*$nik = $request->nik ?? null;
            $result = Employee::getEmployeeDetail($nik);
            if(count($result) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }

            $employee       = $result[0];
            $attendance     = Attendance::getAttendance($employee);
            $today          = date('Y-m-d');
            $timeNow        = date('H:i:s');
            $schTimeIn      = @$attendance[0]->schedule_time_in;
            $schTimeOut     = @$attendance[0]->schedule_time_out;
            $strToday       = Carbon::parse($today)->timestamp;
            $strNow         = Carbon::parse($today.$timeNow)->timestamp;
            $strSchTimeIn   = Carbon::parse($schTimeIn)->timestamp;
            $strSchTimeOut  = Carbon::parse($schTimeOut)->timestamp;
            $schTimeInOnlyDay       = Carbon::parse($schTimeIn)->format('Y-m-d');
            $strSchTimeInOnlyDay    = Carbon::parse($schTimeInOnlyDay)->timestamp;
            $attendanceState        = 'insert';

            if($today == $schTimeInOnlyDay){
                $actualTimeOutNow       = @$attendance[0]->actual_time_out;
                $actualTimeInNow        = @$attendance[0]->actual_time_in;
                $shiftCodeBefore        = @$attendance[1]->shift_code;
                $actualTimeOutBefore    = @$attendance[1]->actual_time_out;
                $scheduleTimeOutBefore  = @$attendance[1]->schedule_time_out;
                $strDayNow              = Carbon::parse($today.date('H:i'))->timestamp;
                $strScheduleTimeOutBeforeAdd4Hours = Carbon::parse($scheduleTimeOutBefore)->addHours(4)->timestamp;
                $strScheduleTimeInAdd4Hours = Carbon::parse($schTimeIn)->addHours(4)->timestamp;
                $strScheduleTimeOutAdd4Hours = Carbon::parse($schTimeOut)->addHours(4)->timestamp;

                if($shiftCodeBefore == 'SHIFT_3' && !is_null($actualTimeOutBefore) && $strDayNow <= $strScheduleTimeOutAdd4Hours){
                    //Jika absen sebelumnya Shift 3 dan actual_time_outnya terisi dan jam skr masih dalam waktu 4 jam dari jadwal checkout skr maka yg ditampilkan update absen
                    $attendanceState = 'update';
                } else {
                    //Jika actual-in terisi & jam-now > schedule-in & jam-now < schedule-in-add4hour
                    //Jika actual-out terisi & jam-now > schedule-out & jam-now < schedule-out-add4hour
                    if((!is_null($actualTimeInNow) && $strNow <= $strSchTimeIn && $strNow < $strScheduleTimeInAdd4Hours) || 
                        (!is_null($actualTimeOutNow) && $strNow > $strSchTimeOut && $strNow < $strScheduleTimeOutAdd4Hours)){
                        $attendanceState = 'update';
                    }
                }
            }
            $attendance[0]->id_user = $employee->id_user; */

            $serverTime = [
                'year'      => date('Y'),
                'month'     => date('m'),
                'day'       => date('d'),
                'hour'      => date('H'),
                'minute'    => date('i'),
                'second'    => date('s'),
            ];

            $result = [
                'server_time'       => $serverTime,
                'map_token'         => $mapboxToken,
                // 'attendance_state'  => $attendanceState,
            ];
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function confirmLocation(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik'               => 'required',
                'longitude'         => 'required',
                'latitude'          => 'required',
                'timezone'          => 'required',
                'address'           => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required',
                'longitude.required' => 'longitude is required',
                'latitude.required' => 'latitude is required',
                'timezone.required' => 'timezone is required',
                'address.required' => 'address is required',
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $result = Employee::getEmployeeDetail($nik);
            if(count($result) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $result[0];
            $attendance         = Attendance::getAttendance($employee);
            $longitude          = $request->longitude;
            $latitude           = $request->latitude;
            $workdaysZone       = $this->convertZone($request->timezone);
            $workdaysLocation   = $request->address;
            $mapboxToken        = $this->AttendanceWeb->getTokenMapbox();
            $mapboxGetPlace     = Curl::findApi('mapbox_getplace');
            $mapboxGetTimezone  = Curl::findApi('mapbox_gettimezone');
            $urlGetPlace        = $mapboxGetPlace->url;
            $urlGetTimezone     = $mapboxGetTimezone->url;
            $getTimeZone        = Http::get($urlGetTimezone.$longitude.','.$latitude.'.json?access_token='.$mapboxToken);
            $getPlace           = Http::get($urlGetPlace.$longitude.','.$latitude.'.json?access_token='.$mapboxToken);

            if($getTimeZone){
                $attendanceTimeZone = count($getTimeZone['features']) > 0 ? $getTimeZone['features'][0]['properties']['TZID'] : $workdaysZone;
            } else {
                $attendanceTimeZone = $workdaysZone;
            }

            if($getPlace){
                $attendanceAddress = [
                    'text' => $getPlace['features'][1]['text'], 
                    'place_name' => $getPlace['features'][1]['place_name']
                ];
            } else {
                $attendanceAddress = $workdaysLocation;
            }

            $today                  = date('Y-m-d');
            $timeNow                = date('H:i:s');
            $startTime              = !is_null(@$attendance[0]->start_time) ? @$attendance[0]->start_time : "00:00:00";
            $strStartTime           = Carbon::parse($startTime)->timestamp;
            $strTimeNow             = Carbon::parse($timeNow)->timestamp;
            $strToday               = Carbon::parse($today)->timestamp;
            $strTodayTime           = Carbon::parse($today.$timeNow)->timestamp;

            $idWorkday                      = @$attendance[0]->id_workdays;
            $shiftCode                      = @$attendance[0]->shift_code;
            $schTimeIn                      = @$attendance[0]->schedule_time_in;
            $schTimeOut                     = @$attendance[0]->schedule_time_out;
            $actualTimeIn                   = @$attendance[0]->actual_time_in;
            $actualTimeOut                  = @$attendance[0]->actual_time_out;

            $shiftCodeBefore                = @$attendance[1]->shift_code;
            $schTimeInBefore                = @$attendance[1]->schedule_time_in;
            $schTimeOutBefore               = @$attendance[1]->schedule_time_out;
            $actualTimeInBefore             = @$attendance[1]->actual_time_in;
            $actualTimeOutBefore            = @$attendance[1]->actual_time_out;
            $allowNextDayBefore             = @$attendance[1]->allow_checkout_nextdays;

            $strSchTimeOutBefore            = Carbon::parse($schTimeOutBefore)->timestamp;
            $strSchTimeOutAdd1Hours         = Carbon::parse($schTimeOut)->addHours(1)->timestamp;
            $strSchTimeInAdd4Hours          = Carbon::parse($schTimeIn)->addHours(4)->timestamp;
            $strSchTimeOutAdd4Hours         = Carbon::parse($schTimeOut)->addHours(4)->timestamp;
            $strSchTimeInBeforeAdd4Hours    = Carbon::parse($schTimeInBefore)->addHours(4)->timestamp;
            $strSchTimeOutBeforeAdd1Hours   = Carbon::parse($schTimeOutBefore)->addHours(1)->timestamp;
            $strSchTimeOutBeforeAdd4Hours   = Carbon::parse($schTimeOutBefore)->addHours(4)->timestamp;
            $strStartTimeAdd4Hours          = Carbon::parse($startTime)->addHours(4)->timestamp;
            $attendanceType                 = 'start';

            if($shiftCode=='Regular' || $shiftCode=='Half Regular' || $shiftCode=='SHIFT_1' || $shiftCode=='SHIFT_2'){
                if($shiftCodeBefore == 'SHIFT_3'){
                    if(!is_null($actualTimeOutBefore) && $strTodayTime <= $strScheduleTimeOutAdd1Hours){
                        // kondisi sudah absen cekout utk shift 3, lalu melakukan cekout lg
                        $attendanceType = "end";
                    } else if($strTodayTime >= $strSchTimeInBeforeAdd4Hours && $strTodayTime <= $strSchTimeOutBeforeAdd1Hours ){ 
                        // kondisi absen cekout utk shift 3, dan kondisi sudah cekout SHift3 lalu cekout lagi (max. 1 jam dr schedule time_out)
                        $attendanceType = "end";
                    } else if($strTodayTime < $strSchTimeInAdd4Hours || $strTodayTime < $strSchTimeInBeforeAdd4Hours){
                        // kondisi absen pertama utk shift 3 jika lupa blm cekin di jam awal
                        if($strTodayTime >= $strSchTimeOutBefore && ((!is_null($actualTimeInBefore) && is_null($actualTimeOutBefore)) || (is_null($actualTimeInBefore) && is_null($actualTimeOutBefore) && $strTodayTime > $strSchTimeInAdd4Hours)) ){
                            $attendanceType = "end";
                        } else {
                            $attendanceType = "start";
                        }
                    } 
                } else {
                    if($shiftCodeBefore == 'FLEX_SHIFT'){
                        if($allowNextDayBefore == true && is_null($actualTimeOutBefore)){
                            $attendanceType = "end";
                        } else {
                            if(!is_null($actualTimeIn) && $strTimeNow >= $strStartTime){
                                $attendanceType = "end";
                            } else {
                                $attendanceType = "start";
                            }
                        }
                    } else {
                        if($strTimeNow < $strStartTimeAdd4Hours){
                            if($strTimeNow > $strStartTime && !is_null($actualTimeIn)){
                                $attendanceType = "end";
                            } else {
                                $attendanceType = "start";
                            }
                        } else {
                            $attendanceType = "end";
                        }
                    }
                }
            } else {
                if($shiftCodeBefore == 'SHIFT_3' || $shiftCode == 'SHIFT_3' ){
                    if($shiftCode == 'OFF'){
                        if($strTodayTime >= $strSchTimeOutBefore && ((!is_null($actualTimeInBefore) && is_null($actualTimeOutBefore)) || (is_null($actualTimeInBefore) && is_null($actualTimeOutBefore) && $strTodayTime > $strSchTimeInAdd4Hours)) ){
                            $attendanceType = "end";
                        } else {
                            if($strTodayTime >= $strSchTimeOutBeforeAdd4Hours && is_null($actualTimeIn)){
                                $attendanceType = "start";
                            } else {
                                $attendanceType = "end";
                            }
                        } 
                    } else {
                        if(!is_null($actualTimeOutBefore) && $strTodayTime <= $strScheduleTimeOutAdd1Hours){
                            // kondisi sudah absen cekout utk shift 3, lalu melakukan cekout lg
                            $attendanceType = "end";
                        } else if($shiftCodeBefore == 'SHIFT_3' && $strTodayTime >= $strSchTimeInBeforeAdd4Hours && $strTodayTime <= $strScheduleTimeOutAdd1Hours ){ 
                            // kondisi absen cekout utk shift 3, dan kondisi sudah cekout SHift3 lalu cekout lagi (max. 1 jam dr schedule time_out)
                            $attendanceType = "end";
                        } else if($strTodayTime < $strSchTimeInAdd4Hours || $strTodayTime < $strSchTimeInBeforeAdd4Hours){
                            // kondisi absen cekin utk shift 3
                            if($strTodayTime >= $strSchTimeOutBefore && ((!is_null($actualTimeInBefore) && is_null($actualTimeOutBefore)) || (is_null($actualTimeInBefore) && is_null($actualTimeOutBefore) && $strTodayTime > $strSchTimeInAdd4Hours)) ){
                                $attendanceType = "end";
                            } else {
                                $attendanceType = "start";
                            } 
                        }
                    }
                } else { // masuk kondisi FLEX_SHIFT ATAU OFF
                    if($shiftCodeBefore == 'FLEX_SHIFT'){
                        if(is_null($actualTimeInBefore) || is_null($actualTimeOutBefore)){ // jika hari kemarin tidak ada absen sama sekali
                            if(!is_null($actualTimeIn) && $strTimeNow >= $strStartTime){
                                $attendanceType = "end";
                            } else {
                                $attendanceType = "start";
                            }
                        } else {
                            if($allowNextDayBefore== true && is_null($actualTimeOutBefore)){
                                $attendanceType = "end";
                            } else {
                                if(!is_null($actualTimeIn) && $strTimeNow >= $strStartTime){
                                    $attendanceType = "end";
                                } else {
                                    $attendanceType = "start";
                                }
                            }
                        }
                    } else {
                        if(!is_null($actualTimeIn)){
                            $attendanceType = "end";
                        } else {
                            $attendanceType = "start";
                        }
                    }
                }
            }

            $attendance = [
                // 'id_user'       => $employee->id_user,
                // 'id_employee'   => $employee->id_employee,
                // 'id_company'    => $employee->id_company,
                // 'id_workdays'   => $idWorkday,
                'latitude'          => $latitude,
                'longitude'         => $longitude,
                'attendance_type'   => $attendanceType,
                'address'           => $attendanceAddress,
                'timezone'          => $attendanceTimeZone,
            ];
            $serverTime = [
                'year'      => date('Y'),
                'month'     => date('m'),
                'day'       => date('d'),
                'hour'      => date('H'),
                'minute'    => date('i'),
                'second'    => date('s'),
            ];

            $result = [
                'server_time'       => $serverTime,
                'attendance'        => $attendance,
            ];
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function submitAttendance(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required

            $parameterValidation = [
                'nik'               => 'required|string',
                'id_workdays'       => 'required',
                'attendance_type'   => 'required|string',
                'actual_timezone'   => 'required|string',
                'actual_latitude'   => 'required',
                'actual_longitude'  => 'required',
                'attendance_photo'  => 'required|string',
                'actual_address'    => 'required|string',
                // 'actual_name'       => 'required|string',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required',
                'id_workdays.required' => 'id_workdays is required',
                'attendance_type.required' => 'attendance_type is required',
                'actual_timezone.required' => 'actual_timezone is required',
                'actual_longitude.required' => 'actual_longitude is required',
                'actual_latitude.required' => 'actual_latitude is required',
                'attendance_photo.required' => 'attendance_photo is required',
                'actual_address.required' => 'actual_address is required',
                // 'actual_name.required' => 'actual_name is required',
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik        = $request->nik ?? null;
            $idUser     = @$request->id_user ;
            $idEmployee = @$request->id_employee;
            $idCompany  = @$request->id_company;

            if(!$idEmployee || !$idCompany || !$idUser){
                $result = Employee::getEmployeeDetail($nik);
                if(count($result) < 1){
                    throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
                }
                $employee   = $result[0];
                $idUser     = @$employee->id_user ;
                $idEmployee = @$employee->id_employee;
                $idCompany  = @$employee->id_company;
            }

            if (!base64_decode($request->attendance_photo, true)) {
                $validator->getMessageBag()->add('attendance_photo', 'attendance_photo format is not valid base64');
                throw new ValidationException($validator);
            }

            $idWorkday  = $request->id_workdays;
            $longitude  = $request->actual_longitude;
            $latitude   = $request->actual_latitude;
            $type       = $request->attendance_type;
            $city       = @$request->actual_name ?? '';
            $address    = $request->actual_address;
            $timezone   = $this->convertZone($request->actual_timezone);
            $image      = str_replace(' ', '+', str_replace('\/', '/', $request->attendance_photo));
            $time       = $request->actual_time ?? date('Y-m-d H:i:s');

            $findAttendance = $this->AttendanceWeb->find($idWorkday);
            if(!$findAttendance){
                throw new \Exception("id_workdays tidak ditemukan");
            }

            $allowedMimeType = ['image/png', 'image/jpeg'];
            $finfo_open = finfo_open();
            $imageMimeType = finfo_buffer($finfo_open, base64_decode($image), FILEINFO_MIME_TYPE);
            if(!in_array($imageMimeType, $allowedMimeType)){
                $validator->getMessageBag()->add('attendance_photo', 'attendance_photo is not valid image mime type');
                throw new ValidationException($validator);
            }

            $filePath       = 'public/images/';
            $imageName      = $idEmployee.'-'.date('YmdHis').'-'.Str::random(3).'.jpeg';
            $makeDirectory  = Storage::makeDirectory($filePath, 0775, true, true);
            $imageBase64    = $image;

            $check_allow_checkout_nextdays = $this->AttendanceWeb->check_allow_checkout_nextdays($idEmployee);
            $check_empty_actual_timeout_before = $this->AttendanceWeb->check_empty_actual_timeout_before($idEmployee);
            $mapAddress['text']         = $city;
            $mapAddress['place_name']   = $address;

            if($latitude == '0'){
                if(!is_null(@$findAttendance->current_latitude_in) || @$findAttendance->current_latitude_in != '0'){ // jika lat_in ada isi maka pakai itu
                    $latitude = @$findAttendance->current_latitude_in;
                    @$mapAddress['text'] = @$findAttendance->current_name_in;
                    @$mapAddress['place_name'] = @$findAttendance->current_address_in;
                } else if(!is_null(@$check_empty_actual_timeout_before['data'][0]->current_latitude_out) || @$check_empty_actual_timeout_before['data'][0]->current_latitude_out != '0'){ // jika lat_out hari sebelumnya ada isi maka pakai itu
                    $latitude = @$check_empty_actual_timeout_before['data'][0]->current_latitude_out;
                    @$mapAddress['text'] = @$check_empty_actual_timeout_before['data'][0]->current_name_out;
                    @$mapAddress['place_name'] = @$check_empty_actual_timeout_before['data'][0]->current_address_out;
                } else { // jika tidak pakai lat in hari sebelumnya
                    $latitude = @$check_empty_actual_timeout_before['data'][0]->current_latitude_in;
                    @$mapAddress['text'] = @$check_empty_actual_timeout_before['data'][0]->current_name_in;
                    @$mapAddress['place_name'] = @$check_empty_actual_timeout_before['data'][0]->current_address_in;
                }
            }
            if($longitude == '0'){
                if(!is_null(@$findAttendance->current_longitude_in) || @$findAttendance->current_longitude_in != '0'){ // jika lng_in ada isi maka pakai itu
                    $longitude = @$findAttendance->current_longitude_in;
                } else if(!is_null(@$check_empty_actual_timeout_before['data'][0]->current_longitude_out) || @$check_empty_actual_timeout_before['data'][0]->current_longitude_out != '0'){ // jika lng_out hari sebelumnya ada isi maka pakai itu
                    $longitude = @$check_empty_actual_timeout_before['data'][0]->current_longitude_out;
                } else { // jika tidak pakai lng in hari sebelumnya
                    $longitude = @$check_empty_actual_timeout_before['data'][0]->current_longitude_in;
                }
            }

            if(@$check_allow_checkout_nextdays['data']->allow_checkout_nextdays=="1" || @$check_empty_actual_timeout_before['data'][0]->id_request!=NULL){ //Jika jadwal checkin kemarin tapi checkout di esok hari (utk yg masuk shift) atau jika di workdays terdapat request leave atau cuti, dll. atau jika settingan work hour pd karyawan tsb memiliki allow checkout next days
                
                if(is_null(@$check_empty_actual_timeout_before['data'][1]->actual_time_out) && !is_null(@$check_empty_actual_timeout_before['data'][1]->actual_time_in)){
                    $findAttendance = $this->AttendanceWeb->find($check_empty_actual_timeout_before['data'][1]->id_workdays);
                    if (Storage::exists($filePath.$findAttendance->image_attachment_out)) {
                        Storage::delete($filePath.$findAttendance->image_attachment_out);
                    }
                    $this->resizeImage($imageBase64, $filePath.$imageName);

                    $dataAttendance = [
                        'actual_time_out'           => $time,
                        'current_latitude_out'      => $latitude,
                        'current_longitude_out'     => $longitude,
                        'image_attachment_out'      => $imageName,
                        'current_name_out'          => $mapAddress['text'],
                        'current_address_out'       => $mapAddress['place_name'],
                        'current_employee_timezone' => $timezone,
                    ];

                    $workHours = date_diff(date_create($dataAttendance['actual_time_out']),date_create($findAttendance->actual_time_in));
                    $workHours_h = $workHours->format("%h")<10?"0".$workHours->format("%h"):$workHours->format("%h");
                    $workHours_i = $workHours->format("%i")<10?"0".$workHours->format("%i"):$workHours->format("%i");
                    $workHours_s = $workHours->format("%s")<10?"0".$workHours->format("%s"):$workHours->format("%s");
                    $dataAttendance['work_hours'] = $workHours_h.":".$workHours_i.":".$workHours_s;
                    
                    if(strtotime($dataAttendance['actual_time_out'])>strtotime($findAttendance->schedule_time_out)){
                        $overtime = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                        $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                        $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                        $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                        $dataAttendance['overtime'] = $overtime_h.":".$overtime_i.":".$overtime_s;
                    }
                    else if(strtotime($dataAttendance['actual_time_out'])<strtotime($findAttendance->schedule_time_out))
                    {
                        $earlyOut = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                        $earlyOut_h = $earlyOut->format("%h")<10?"0".$earlyOut->format("%h"):$earlyOut->format("%h");
                        $earlyOut_i = $earlyOut->format("%i")<10?"0".$earlyOut->format("%i"):$earlyOut->format("%i");
                        $earlyOut_s = $earlyOut->format("%s")<10?"0".$earlyOut->format("%s"):$earlyOut->format("%s");
                        $dataAttendance['early_out'] = $earlyOut_h.":".$earlyOut_i.":".$earlyOut_s;
                    }

                    if(@$check_empty_actual_timeout_before['data'][0]->id_request!=NULL){
                        $idWorkday = $check_empty_actual_timeout_before['data'][0]->id_workdays;
                    } else {
                        $idWorkday = $check_empty_actual_timeout_before['data'][1]->id_workdays;
                    }
                } else {
                    if($type == 'first_half') {
                        $str_now = strtotime($time);
                        $str_schedule_in = strtotime($findAttendance->schedule_time_in);

                        if(is_null(@$check_empty_actual_timeout_before['data'][0]->actual_time_in)){
                            if (Storage::exists($filePath.$findAttendance->image_attachment_in)) {
                                Storage::delete($filePath.$findAttendance->image_attachment_in);
                            }
                            $this->resizeImage($imageBase64, $filePath.$imageName);

                            $dataAttendance = [
                                'actual_time_in'           => $time,
                                'current_latitude_in'      => $latitude,
                                'current_longitude_in'     => $longitude,
                                'image_attachment_in'      => $imageName,
                                'current_name_in'          => $mapAddress['text'],
                                'current_address_in'       => $mapAddress['place_name'],
                                'current_employee_timezone' => $timezone,
                            ];
                            
                            $lateIn = date_diff(date_create($findAttendance->schedule_time_in),date_create($dataAttendance['actual_time_in']));
                            $lateIn_h = $lateIn->format("%h")<10?"0".$lateIn->format("%h"):$lateIn->format("%h");
                            $lateIn_i = $lateIn->format("%i")<10?"0".$lateIn->format("%i"):$lateIn->format("%i");
                            $lateIn_s = $lateIn->format("%s")<10?"0".$lateIn->format("%s"):$lateIn->format("%s");

                            if(strtotime($dataAttendance['actual_time_in']) > strtotime($findAttendance->schedule_time_in)){
                                $dataAttendance['late_in'] = $lateIn_h.":".$lateIn_i.":".$lateIn_s;
                            }
                        } 
                        else if($str_now <= $str_schedule_in){
                            // jika sudah cekin, namun melakukan cekin lagi pada jam sebelum schedule in maka tidak ada perubahan
                        } else {
                            //jika setelah shift 3 sudah cekout lalu lepas dari range shift 3 ke workdays selanjutnya
                            if (Storage::exists($filePath.$findAttendance->image_attachment_in)) {
                                Storage::delete($filePath.$findAttendance->image_attachment_in);
                            }
                            $this->resizeImage($imageBase64, $filePath.$imageName);

                            $dataAttendance = [
                                'actual_time_in'           => $time,
                                'current_latitude_in'      => $latitude,
                                'current_longitude_in'     => $longitude,
                                'image_attachment_in'      => $imageName,
                                'current_name_in'          => $mapAddress['text'],
                                'current_address_in'       => $mapAddress['place_name'],
                                'current_employee_timezone' => $timezone,
                            ];

                            $lateIn = date_diff(date_create($findAttendance->schedule_time_in),date_create($dataAttendance['actual_time_in']));
                            $lateIn_h = $lateIn->format("%h")<10?"0".$lateIn->format("%h"):$lateIn->format("%h");
                            $lateIn_i = $lateIn->format("%i")<10?"0".$lateIn->format("%i"):$lateIn->format("%i");
                            $lateIn_s = $lateIn->format("%s")<10?"0".$lateIn->format("%s"):$lateIn->format("%s");

                            if(strtotime($dataAttendance['actual_time_in']) > strtotime($findAttendance->schedule_time_in)){
                                $dataAttendance['late_in'] = $lateIn_h.":".$lateIn_i.":".$lateIn_s;
                            }
                        }
                        $idWorkday = $idWorkday;
                    }
                    else {
                        $findAttendance_b = $this->AttendanceWeb->find($check_empty_actual_timeout_before['data'][0]->id_workdays);
                        $diff   = abs(strtotime($findAttendance_b->schedule_time_out) - strtotime($time));
                        $diff_2 = abs(strtotime($findAttendance->schedule_time_out) - strtotime($time)); 
                        if($diff < $diff_2 || $findAttendance_b->actual_time_out == null){ 

                            if($findAttendance->actual_time_in != null){
                                if (Storage::exists($filePath.$findAttendance->image_attachment_out)) {
                                    Storage::delete($filePath.$findAttendance->image_attachment_out);
                                }
                                $this->resizeImage($imageBase64, $filePath.$imageName);

                                $dataAttendance = [
                                    'actual_time_out'           => $time,
                                    'current_latitude_out'      => $latitude,
                                    'current_longitude_out'     => $longitude,
                                    'image_attachment_out'      => $imageName,
                                    'current_name_out'          => $mapAddress['text'],
                                    'current_address_out'       => $mapAddress['place_name'],
                                    'current_employee_timezone' => $timezone,
                                ];

                                $workHours = date_diff(date_create($dataAttendance['actual_time_out']),date_create($findAttendance->actual_time_in));
                                $workHours_h = $workHours->format("%h")<10?"0".$workHours->format("%h"):$workHours->format("%h");
                                $workHours_i = $workHours->format("%i")<10?"0".$workHours->format("%i"):$workHours->format("%i");
                                $workHours_s = $workHours->format("%s")<10?"0".$workHours->format("%s"):$workHours->format("%s");
                                $dataAttendance['work_hours'] = $workHours_h.":".$workHours_i.":".$workHours_s;
                                
                                if(strtotime($dataAttendance['actual_time_out']) > strtotime($findAttendance->schedule_time_out)){
                                    $overtime = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                                    $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                                    $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                                    $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                                    $dataAttendance['overtime'] = $overtime_h.":".$overtime_i.":".$overtime_s;
                                }
                                else if(strtotime($dataAttendance['actual_time_out'])<strtotime($findAttendance->schedule_time_out)){
                                    $earlyOut = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                                    $earlyOut_h = $earlyOut->format("%h")<10?"0".$earlyOut->format("%h"):$earlyOut->format("%h");
                                    $earlyOut_i = $earlyOut->format("%i")<10?"0".$earlyOut->format("%i"):$earlyOut->format("%i");
                                    $earlyOut_s = $earlyOut->format("%s")<10?"0".$earlyOut->format("%s"):$earlyOut->format("%s");
                                    $dataAttendance['early_out'] = $earlyOut_h.":".$earlyOut_i.":".$earlyOut_s;
                                }
                                $idWorkday = $idWorkday;
                            } else {
                                // kondisi jika setelah shift 3, sudah cekout lalu melakukan cekout lagi
                                if (Storage::exists($filePath.$findAttendance_b->image_attachment_out)) {
                                    Storage::delete($filePath.$findAttendance_b->image_attachment_out);
                                }
                                $this->resizeImage($imageBase64, $filePath.$imageName);

                                $dataAttendance = [
                                    'actual_time_out'           => $time,
                                    'current_latitude_out'      => $latitude,
                                    'current_longitude_out'     => $longitude,
                                    'image_attachment_out'      => $imageName,
                                    'current_name_out'          => $mapAddress['text'],
                                    'current_address_out'       => $mapAddress['place_name'],
                                    'current_employee_timezone' => $timezone,
                                ];

                                $workHours = date_diff(date_create($findAttendance_b->actual_time_out),date_create($findAttendance_b->actual_time_in));
                                $workHours_h = $workHours->format("%h")<10?"0".$workHours->format("%h"):$workHours->format("%h");
                                $workHours_i = $workHours->format("%i")<10?"0".$workHours->format("%i"):$workHours->format("%i");
                                $workHours_s = $workHours->format("%s")<10?"0".$workHours->format("%s"):$workHours->format("%s");
                                $dataAttendance['work_hours'] = $workHours_h.":".$workHours_i.":".$workHours_s;

                                $idWorkday = $check_empty_actual_timeout_before['data'][0]->id_workdays;
                            }
                        } else {
                            // kondisi jika checkout belum dilakukan
                            if (Storage::exists($filePath.$findAttendance->image_attachment_out)) {
                                Storage::delete($filePath.$findAttendance->image_attachment_out);
                            }
                            $this->resizeImage($imageBase64, $filePath.$imageName);

                            $dataAttendance = [
                                'actual_time_out'           => $time,
                                'current_latitude_out'      => $latitude,
                                'current_longitude_out'     => $longitude,
                                'image_attachment_out'      => $imageName,
                                'current_name_out'          => $mapAddress['text'],
                                'current_address_out'       => $mapAddress['place_name'],
                                'current_employee_timezone' => $timezone,
                            ];

                            $workHours = date_diff(date_create($dataAttendance['actual_time_out']),date_create($findAttendance->actual_time_in));
                            $workHours_h = $workHours->format("%h")<10?"0".$workHours->format("%h"):$workHours->format("%h");
                            $workHours_i = $workHours->format("%i")<10?"0".$workHours->format("%i"):$workHours->format("%i");
                            $workHours_s = $workHours->format("%s")<10?"0".$workHours->format("%s"):$workHours->format("%s");
                            $dataAttendance['work_hours'] = $workHours_h.":".$workHours_i.":".$workHours_s;

                            if(strtotime($dataAttendance['actual_time_out'])>strtotime($findAttendance->schedule_time_out)){
                                $overtime = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                                $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                                $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                                $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                                $dataAttendance['overtime'] = $overtime_h.":".$overtime_i.":".$overtime_s;
                            }
                            else if(strtotime($dataAttendance['actual_time_out'])<strtotime($findAttendance->schedule_time_out)){
                                $earlyOut = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                                $earlyOut_h = $earlyOut->format("%h")<10?"0".$earlyOut->format("%h"):$earlyOut->format("%h");
                                $earlyOut_i = $earlyOut->format("%i")<10?"0".$earlyOut->format("%i"):$earlyOut->format("%i");
                                $earlyOut_s = $earlyOut->format("%s")<10?"0".$earlyOut->format("%s"):$earlyOut->format("%s");
                                $dataAttendance['early_out'] = $earlyOut_h.":".$earlyOut_i.":".$earlyOut_s;
                            }
                            $idWorkday = $idWorkday;
                        }
                    }
                }
            } else { // JIKA GROUP SHIFT TANPA ALLOW NEXT DAYS 
                if($type == 'first_half' && is_null(@$findAttendance->actual_time_in)) { // Jika absen IN
                    if (Storage::exists($filePath.$findAttendance->image_attachment_in)) {
                        Storage::delete($filePath.$findAttendance->image_attachment_in);
                    }
                    //Storage::put($filePath.$imageName, $imageBase64);
                    $this->resizeImage($imageBase64, $filePath.$imageName);

                    $dataAttendance = [
                        'actual_time_in'           => $time,
                        'current_latitude_in'      => $latitude,
                        'current_longitude_in'     => $longitude,
                        'image_attachment_in'      => $imageName,
                        'current_name_in'          => $mapAddress['text'],
                        'current_address_in'       => $mapAddress['place_name'],
                        'current_employee_timezone' => $timezone,
                    ];

                    $lateIn = date_diff(date_create($findAttendance->schedule_time_in),date_create($dataAttendance['actual_time_in']));
                    $lateIn_h = $lateIn->format("%h")<10?"0".$lateIn->format("%h"):$lateIn->format("%h");
                    $lateIn_i = $lateIn->format("%i")<10?"0".$lateIn->format("%i"):$lateIn->format("%i");
                    $lateIn_s = $lateIn->format("%s")<10?"0".$lateIn->format("%s"):$lateIn->format("%s");

                    if(strtotime($dataAttendance['actual_time_in']) > strtotime($findAttendance->schedule_time_in)){
                        $dataAttendance['late_in'] = $lateIn_h.":".$lateIn_i.":".$lateIn_s;
                    }
                    $idWorkday = $idWorkday;
                }
                else if($type != 'first_half') { // Jika absen OUT 
                    if (Storage::exists($filePath.$findAttendance->image_attachment_out)) {
                        Storage::delete($filePath.$findAttendance->image_attachment_out);
                    }
                    //Storage::put($filePath.$imageName, $imageBase64);
                    $this->resizeImage($imageBase64, $filePath.$imageName);

                    $dataAttendance = [
                        'actual_time_out'           => $time,
                        'current_latitude_out'      => $latitude,
                        'current_longitude_out'     => $longitude,
                        'image_attachment_out'      => $imageName,
                        'current_name_out'          => $mapAddress['text'],
                        'current_address_out'       => $mapAddress['place_name'],
                        'current_employee_timezone' => $timezone,
                    ];

                    $workHours = date_diff(date_create($dataAttendance['actual_time_out']),date_create($findAttendance->actual_time_in));
                    $workHours_h = $workHours->format("%h")<10?"0".$workHours->format("%h"):$workHours->format("%h");
                    $workHours_i = $workHours->format("%i")<10?"0".$workHours->format("%i"):$workHours->format("%i");
                    $workHours_s = $workHours->format("%s")<10?"0".$workHours->format("%s"):$workHours->format("%s");
                    $dataAttendance['work_hours'] = $workHours_h.":".$workHours_i.":".$workHours_s;
                    
                    if(strtotime($dataAttendance['actual_time_out'])>strtotime($findAttendance->schedule_time_out)){
                        $overtime = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                        $overtime_h = $overtime->format("%h")<10?"0".$overtime->format("%h"):$overtime->format("%h");
                        $overtime_i = $overtime->format("%i")<10?"0".$overtime->format("%i"):$overtime->format("%i");
                        $overtime_s = $overtime->format("%s")<10?"0".$overtime->format("%s"):$overtime->format("%s");
                        $dataAttendance['overtime'] = $overtime_h.":".$overtime_i.":".$overtime_s;
                    }
                    else if(strtotime($dataAttendance['actual_time_out'])<strtotime($findAttendance->schedule_time_out)){
                        $earlyOut = date_diff(date_create($findAttendance->schedule_time_out),date_create($dataAttendance['actual_time_out']));
                        $earlyOut_h = $earlyOut->format("%h")<10?"0".$earlyOut->format("%h"):$earlyOut->format("%h");
                        $earlyOut_i = $earlyOut->format("%i")<10?"0".$earlyOut->format("%i"):$earlyOut->format("%i");
                        $earlyOut_s = $earlyOut->format("%s")<10?"0".$earlyOut->format("%s"):$earlyOut->format("%s");
                        $dataAttendance['early_out'] = $earlyOut_h.":".$earlyOut_i.":".$earlyOut_s;
                    }
                    $idWorkday = $idWorkday;
                }
            }

            $dataAttendance['updated_by'] = $idUser;
            $dataAttendance['update_date'] = date('Y-m-d H:i:s');
            $dataAttendance['is_mobile_attendance'] = true;

            $saveAttendance = $this->AttendanceWeb->where('id_workdays', $idWorkday)->update($dataAttendance);
            if(!$saveAttendance){
                throw new \Exception("Attendance failed to save", 422);
            }

            $thisEmployee = (object)[
                'id_company' => $idCompany,
                'id_employee' => $idEmployee,
            ];

            // $listAttendance     = Attendance::getAttendance($thisEmployee);
            $serverTime = [
                'year'      => date('Y'),
                'month'     => date('m'),
                'day'       => date('d'),
                'hour'      => date('H'),
                'minute'    => date('i'),
                'second'    => date('s'),
            ];

            $result = [
                // 'server_time'       => $serverTime,
                'id_workdays'   => $idWorkday,
            ];
            $message    = 'Data was successfully insert';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function getAttendanceStatusCount(Request $request)
    {
        /*
        * Trello Issue 149.004 API Get Attendance Status Count
        */
        try {
            $this->authMobile($request);
        } catch(Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }

        $request->validate([
            'nik' => 'required',
        ]);
        
        $employee = DB::table('hr_employee as he')
                    ->select('he.name', 'he.id_employee', 'he.id_company')
                    ->where('he.nik_employee', $request->nik)
                    ->where('he.status', 'A')
                    ->first();
                    // dd($employee);
        if(!$employee) {
            return $this->sendError(404, "Employee Not Found!");
        }
        $attendance = [
            "month" => Home::attendance_per_month($employee->id_employee, $employee->id_company),
            "year" => Home::attendance_per_year($employee->id_employee, $employee->id_company),
        ];
        unset($employee->id_employee);
        unset($employee->id_company);

        $payload = [
            "employee" => $employee,
            "attendance" => $attendance
        ];
        return $this->sendSuccess("Data was successfully sent", $payload, false);
        
    }
}
