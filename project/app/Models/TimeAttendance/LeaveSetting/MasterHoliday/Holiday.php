<?php

namespace App\Models\TimeAttendance\LeaveSetting\MasterHoliday;

use Illuminate\Database\Eloquent\Model;
use DB;

class Holiday extends Model
{
    protected $table = 'master_holiday';
    protected $fillable = ['id_holiday', 'holiday_name', 'start_date', 'end_date',
    'holiday_type', 'id_specific_type', 'recurring_every_year', 'id_company', 'created_by', 'updated_by'];

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    protected $primaryKey = 'id_holiday';

    function getLineHolidays() {
        if($this->holiday_type == "C") {
            $line_holiday = DB::table('master_line_holiday')
                                ->select('master_line_holiday.*')
                                ->where('master_line_holiday.id_holiday', $this->id_holiday)
                                ->get();
            return $line_holiday;
        }
        else {
            return null;
        }
    }

    function getSpecificType() {
        if($this->holiday_type == "C") {
            $specific_type = DB::table('master_general_data')
                                ->where('id_general_data', $this->id_specific_type)
                                ->first();
            return $specific_type;
        }
        else {
            return null;
        }
    }

    function generateHoliday() {
        $data = DB::table(DB::raw('GenerateHoliday ('.session('id_company').', '.session('id_user').')'));
        return true;
    }

    function recurringHoliday() {
        $data = DB::table(DB::raw('RecurringHoliday ('.session('id_company').', '.session('id_user').')'));
        return true;
    }
}
