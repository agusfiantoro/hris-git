<?php

namespace App\Http\Controllers\TimeAttendance\LeaveSetting\MasterHoliday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\TimeAttendance\LeaveSetting\MasterHoliday\Holiday;
use App\Models\TimeAttendance\LeaveSetting\MasterHoliday\LineHoliday;
use App\Models\TimeAttendance\LeaveSetting\MasterHoliday\Attendance;
use DB;
use DataTables;


class HolidayController extends Controller
{
    public function index()
    {
        $line_holidays = $this->getLineHoliday();
        $specific_type = $this->getSpecificType();
        $region = $this->getRegion();
        $branch = $this->getBranch();
        $location = $this->getLocation();
        $company = $this->getCompany();

        return view('time_attendance.leave_setting.master_holiday.holiday', compact('line_holidays', 'specific_type', 'region', 'branch', 'location', 'company'));
    }
    
    public function getdata(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $holiday = DB::table('master_holiday as mh')
                ->leftJoin('master_company as mc', 'mc.id_company', '=', 'mh.id_company')
                ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'mh.id_specific_type')
                ->select('mh.*', 'mc.company_name as company', 'mgd.description as specific_type')
				->where('mh.id_company', session('id_company'))
                ->orderBy('holiday_name')
                ->get();

            foreach ($holiday as $key => $val) {
                if($val->holiday_type == "P"){
                    $holiday_type="Public";
                } else {
                    $holiday_type="Corporate";
                }
                
                if($val->recurring_every_year == "0"){
                    $recurring_every_year="No";
                } else {
                    $recurring_every_year="Yes";
                }
                
                if($val->status == "A"){
                    $status="Active";
                } else {
                    $status="Inactive";
                }

                $data[] = [
                    "id_holiday"=>$val->id_holiday,
                    "id_company"=>$val->id_company,
                    "holiday_name"=>$val->holiday_name,
                    "start_date"=>$val->start_date,
                    "end_date"=>$val->end_date,
                    "holiday_type"=>$holiday_type,
                    "id_specific_type"=>$val->specific_type,
                    "recurring_every_year"=>$recurring_every_year,
                    "company"=>$val->company,
                    "status"=>$status,
                    "actions"=>'<button type="button" class="btn btn-sm btn-primary btn-edit" id="edit_'.$val->id_holiday.'"><i class="fa fa-pencil"></i></button> <button type="button" class="btn btn-sm btn-danger btn-delete" id="delete_'.$val->id_holiday.'"><i class="fa fa-trash"></i></button> '
                ];
            }
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }
    }

    public function getSpecificType() {
        $specific_type = DB::table('master_general_data')->where([['id_general_type', 11],['id_company',session('id_company')]])->get();
        return $specific_type;
    }

    public function getRegion() {
        $region = DB::table('master_region as mr')
            ->select('mr.*', 'mc.company_code')
            ->leftJoin('master_company as mc', 'mr.id_company', '=', 'mc.id_company')
            ->get();
        return $region;
    }

    public function getBranch() {
        $branch = DB::table('master_branch as mb')
            ->select('mb.*', 'mc.company_code')
            ->leftJoin('master_company as mc', 'mb.id_company', '=', 'mc.id_company')
            ->get();
        return $branch;
    }

    public function getLocation() {
        $location = DB::table('master_location as ml')
            ->select('ml.*', 'mc.company_code')
            ->leftJoin('master_company as mc', 'ml.id_company', '=', 'mc.id_company')
            ->get();
        return $location;
    }

    public function getCompany() {
        $company = DB::table('master_company')->where([['id_company',session('id_company')]])->get();
        return $company;
    }

    public function getLineHoliday() {
        $line_holiday = DB::table('master_line_holiday')
        ->leftJoin('master_region', 'master_line_holiday.id_regional', '=', 'master_region.id_region')
        ->leftJoin('master_branch', 'master_line_holiday.id_branch', '=', 'master_branch.id_branch')
        ->leftJoin('master_location', 'master_line_holiday.id_location', '=', 'master_location.id_location')
        ->leftJoin('master_company as mc', 'master_branch.id_company', '=', 'mc.id_company')
        ->select('master_line_holiday.*', 'master_region.description as regional', 'master_branch.description as branch', 'master_location.description as location',
        'master_region.status as regional_status', 'master_branch.status as branch_status', 'master_location.status as location_status', 'mc.company_code')
        ->get();

        $line_holiday->mapWithKeys(function ($val){
            $val->branch = $val->branch.' ('.$val->company_code.')';
            return $val;
        });
        return $line_holiday;
    }

    public function getHoliday() {
        $holidays = DB::table('master_holiday')
        ->leftJoin('master_general_data', 'master_holiday.id_specific_type', '=', 'master_general_data.id_general_type')
        ->select('master_holiday.*', 'master_general_data.description as specific_type')
        ->get();
        return $holidays;
    }

    public function getHolidayById(Request $request) {
        $holiday = DB::table('master_holiday as mh')
                ->leftJoin('master_company as mc', 'mc.id_company', '=', 'mh.id_company')
                ->select('mh.*', 'mc.company_name as company')
                ->where('id_holiday', $request->id_holiday)
                ->orderBy('holiday_name')
                ->get();
        $result['data'] = $holiday;
        return $result;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'holiday_name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'holiday_type' => 'required',
            'recurring_every_year' => 'required',
            'id_company' => 'required',
            'created_by' => 'required',

        ]);

        if($validate->fails())
            return response(['status' => 'Failed', 'message' => $validate->errors()],400);
        
        $holiday = Holiday::create($storeData);

        $getMasterDailyShift = DB::table('master_daily_shift as mds')
            ->select('id_shift', 'day_type', 'id_company')
            ->where('day_type', 'OD')
            ->get();

        $getMasterDailyShift = $getMasterDailyShift->keyBy(function ($item) {
            return $item->id_company;
        });

        $storeLineHoliday = [
            'id_holiday' => $holiday->id_holiday,
            'id_company' => $storeData['id_company'],
            'created_by'=> $storeData['created_by']
        ];

        $fromDate = $storeData['start_date'];
        $toDate = $storeData['end_date'];

        $dataUpdate = [
            'id_shift' => $getMasterDailyShift[$storeData['id_company']]->id_shift,
            'day_type' => $getMasterDailyShift[$storeData['id_company']]->day_type,
            'id_holiday' => $holiday->id_holiday, 
            'note' => $holiday->holiday_name
        ];

        if($storeData['holiday_type'] == "C") {
            foreach($storeData['id_specific_list'] as $id_specific) {
                $storeLineHoliday['id_'. strtolower($storeData['specific_type'])] = $id_specific['id'];
                $line_holiday = LineHoliday::create($storeLineHoliday);
                
                $workdays = Attendance::whereBetween('current_dates', [$fromDate, $toDate]);

                if($storeData['specific_type'] == "regional") {
                    $queryBranch = DB::table('master_branch')->select('id_branch')->where('id_region', $id_specific['id'])->get()->pluck('id_branch')->all();
                    $queryLocation = DB::table('master_location')->select('id_location')->whereIn('id_branch', $queryBranch)->get()->pluck('id_location')->all();
                    $workdays->whereIn('target_id_location', $queryLocation);
                }
                else if($storeData['specific_type'] == "branch") {
                    $queryLocation = DB::table('master_location')->select('id_location')->where('id_branch', $id_specific['id'])->get()->pluck('id_location')->all();
                    $workdays->whereIn('target_id_location', $queryLocation);
                }
                else if($storeData['specific_type'] == "location") {
                    $workdays->where('target_id_location', $id_specific['id']);
                }
                // $workdays->where('id_company', $storeData['id_company']);
                $workdays->update($dataUpdate);
            }
        }
        else {
            $workdays = Attendance::whereBetween('current_dates', [$fromDate, $toDate])
                ->where('id_company', $storeData['id_company'])
                ->update($dataUpdate);
        }

        if($holiday->recurring_every_year == 1) {
            $holiday->recurringHoliday();
        }
        //$holiday->generateHoliday();
        
        return response([
            'status' => 'Success',
            'data' => $holiday,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $holiday = Holiday::find($id);

        if(is_null($holiday)){
            return response([
                'status' => 'Failed',
                'message' => 'Holiday not found',
                'data' => null
            ], 404);
        }

        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'holiday_name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'holiday_type' => 'required',
            'recurring_every_year' => 'required',
            'id_company' => 'required',
            'updated_by' => 'required',
        ]);

        if($validate->fails())
            return response(['status' => 'Failed', 'message' => $validate->errors()],400);


        $getMasterDailyShift = DB::table('master_daily_shift as mds')
            ->select('id_shift', 'day_type', 'id_company')
            ->where('day_type', 'OD')
            ->get();

        $getMasterDailyShift = $getMasterDailyShift->keyBy(function ($item) {
            return $item->id_company;
        });

        $fromDate = $storeData['start_date'];
        $toDate = $storeData['end_date'];

        $old_line_holidays = null;
        
        if($holiday->holiday_type == "C") {
            $old_line_holidays = $holiday->getLineHolidays();
            $holiday_specific_type = $holiday->getSpecificType();

            if($old_line_holidays != null && count($old_line_holidays) > 0 && $holiday_specific_type != null) {
                foreach($old_line_holidays as $line) {
                    LineHoliday::where('id_line_holiday', $line->id_line_holiday)->update(['id_'. strtolower($holiday_specific_type->description) => null]);
                }
            }
            
            $holiday->id_specific_type = null;
        }

        $workdays = Attendance::where('id_holiday', $holiday->id_holiday)->whereNotIn('day_seq', [6, 7])->update(['day_type' => 'WD', 'id_holiday' => null, 'note' => null]);

        $dataUpdate = [
            'id_shift' => $getMasterDailyShift[$storeData['id_company']]->id_shift,
            'day_type' => $getMasterDailyShift[$storeData['id_company']]->day_type,
            'id_holiday' => $holiday->id_holiday, 
            'note' => $holiday->holiday_name,
            'updated_by' => session('id_user')
        ];

        if($storeData['holiday_type'] == "C") {
            $length_max = 0;
            if($old_line_holidays != null && count($old_line_holidays) > 0) {
                if(count($storeData['id_specific_list']) < count($old_line_holidays)) {
                    $length = count($storeData['id_specific_list']);
                    $length_max = count($old_line_holidays);

                    for($i = $length; $i < $length_max; $i++) {
                        LineHoliday::destroy($old_line_holidays[$i]->id_line_holiday);
                    }
                    $old_line_holidays = $holiday->getLineHolidays();
                }
            }
            $length_max = count($storeData['id_specific_list']);
            $works = [];
            for($i = 0; $i < $length_max; $i++) {
                $id_place = $storeData['id_specific_list'][$i]['id'];

                if($old_line_holidays != null && count($old_line_holidays) > 0 && $i < count($old_line_holidays)) {
                    $line_holiday = LineHoliday::find($old_line_holidays[$i]->id_line_holiday);
                    $line_holiday->id_company = $storeData['id_company'];
                    $line_holiday->updated_by = $storeData['updated_by'];
                    $line_holiday->{'id_'. strtolower($storeData['specific_type'])} = $id_place;
                    $line_holiday->save();
                }
                else {
                    $line_holiday = LineHoliday::create(
                        [
                            'id_holiday' => $holiday->id_holiday,
                            'id_company' => $storeData['id_company'],
                            'created_by' => $storeData['updated_by'],
                            'id_'. strtolower($storeData['specific_type']) => $id_place,
                        ]
                    );
                }

                $workdays = Attendance::whereBetween('current_dates', [$fromDate, $toDate]);

                if($storeData['specific_type'] == "regional") {
                    $queryBranch = DB::table('master_branch')->select('id_branch')->where('id_region', $id_place)->get()->pluck('id_branch')->all();
                    $queryLocation = DB::table('master_location')->select('id_location')->whereIn('id_branch', $queryBranch)->get()->pluck('id_location')->all();
                    $workdays->whereIn('target_id_location', $queryLocation);
                }
                else if($storeData['specific_type'] == "branch") {
                    $queryLocation = DB::table('master_location')->select('id_location')->where('id_branch', $id_place)->get()->pluck('id_location')->all();
                    $workdays->whereIn('target_id_location', $queryLocation);
                }
                else if($storeData['specific_type'] == "location") {
                    $workdays->where('target_id_location', $id_place);
                }

                // $workdays->where('id_company', $storeData['id_company']);
                $workdays->update($dataUpdate);
            }
            
            $holiday->id_specific_type = $storeData['id_specific_type'];
        }
        else {
            if($old_line_holidays != null && count($old_line_holidays) > 0) {
                foreach($old_line_holidays as $old_line_holiday) {
                    LineHoliday::destroy($old_line_holiday->id_line_holiday);
                }
            }
            $workdays = Attendance::whereBetween('current_dates', [$fromDate, $toDate])
                ->where('id_company', $storeData['id_company'])
                ->update($dataUpdate);
        }
        
        $holiday->holiday_name = $storeData['holiday_name'];
        $holiday->start_date = $storeData['start_date'];
        $holiday->end_date = $storeData['end_date'];
        $holiday->holiday_type = $storeData['holiday_type'];
        $holiday->recurring_every_year = $storeData['recurring_every_year'];
        $holiday->id_company = $storeData['id_company'];
        $holiday->updated_by = $storeData['updated_by'];
        if($holiday->save()) {
            if($holiday->recurring_every_year == 1) {
                $holiday->recurringHoliday();
            }
            //$holiday->generateHoliday();
            return response([
                'status' => 'Success',
                'data' => $holiday,
            ], 200);
        }
        return response([
            'status' => 'Failed',
            'data' => $holiday
        ], 400);     
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Holiday::find($id);

        if(is_null($holiday)){
            return response([
                'status' => 'Failed',
                'message' => 'Holiday not found',
                'data' => null
            ], 404);
        }

        if($holiday->holiday_type == "C") {
            LineHoliday::where('id_holiday', $holiday->id_holiday)->delete();
        }

        Attendance::where('id_holiday', $holiday->id_holiday)->whereNotIn('day_seq', [6, 7])
                    ->update(['day_type' => 'WD', 'id_holiday' => null, 'note' => null]);

        if($holiday->delete()) {
            return response([
                'status' => 'Success',
                'data' => $holiday,
            ], 200);
        }
        return response([
            'status' => 'Failed',
            'data' => $holiday
        ], 400);
    }
}
