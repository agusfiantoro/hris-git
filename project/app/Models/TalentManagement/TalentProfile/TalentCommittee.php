<?php

namespace App\Models\TalentManagement\TalentProfile;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TalentCommittee extends Model{
	
	protected $table = 'hr_talent_commite_note';
    protected $primaryKey = 'id_talent_profile_note';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_talent_profile_note', 'id_position_routing', 'id_employee', 'id_category_profile', 'id_activity', 'talent_commite_date', 'notes','is_submitted_flag', 'status', 'id_company', 'created_by', 'updated_by'
	];
	
	
	public static function get_reco() {
        $sql = "SELECT htrh.id_talent_recommendation_header id, htrh.notes text 
				FROM hr_talent_recommendation_header htrh
				WHERE htrh.status = 'A' AND htrh.id_company = ".session('id_company')."
				ORDER BY text ASC";
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	

}
