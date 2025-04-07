<?php

namespace App\Models\Employee\Employee;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class UserSurvey extends Model {

    protected $table = 'hr_survey_answer_user';
    protected $primaryKey = 'id_survey_user';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
       'id_survey_header', 'id_survey_question', 'id_survey_answer', 'description_answer', 'id_employee', 'employee_name', 'status', 'id_company', 'created_by', 'updated_by'
	];
	
	
    public static function getdata() {
        $data = DB::table('hr_survey_header as hsh')
                ->join('hr_employee as he', 'hsh.id_employee_request', '=', 'he.id_employee')
                ->join('master_general_data as mgd', 'hsh.id_question_type', '=', 'mgd.id_general_data')
                ->join('master_general_data as mgd2', 'hsh.id_survey_type', '=', 'mgd2.id_general_data')
                ->select('hsh.*', 'he.name as employee_name', 'mgd.description as question_type', 'mgd2.description as survey_type')
                ->where('hsh.id_company', session('id_company'))
                ->get();
        return $data;
    }
	
    public static function get_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
}
