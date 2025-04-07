<?php

namespace App\Models\TimeAttendance\LeaveSetting\MasterHoliday;

use Illuminate\Database\Eloquent\Model;

class LineHoliday extends Model
{
    protected $table = 'master_line_holiday';
    protected $fillable = ['id_line_holiday', 'id_holiday', 'id_regional', 'id_branch',
    'id_location', 'id_company', 'created_by', 'updated_by'];

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    protected $primaryKey = 'id_line_holiday';

}
