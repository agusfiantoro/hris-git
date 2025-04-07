<?php

namespace App\Models\Employee\EmployeeReco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmployeeRecoQualitative extends Model
{
	use HasFactory;
	
    protected $table = 'public.hr_recommendation_qualitative';
	protected $primaryKey = 'id_recommendation_qualitative';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	public static function getdata() {
        $sql = "SELECT hqa.id_qualitative_appraisers, hqa.id_employee_appraisers, he.name AS penilai, hqa.id_period,
		hqa.id_employee_participant, he2.nik_employee AS nik_dinilai, he2.name AS name_dinilai, hqa.appraisers_hierarchy, 
		hqa.submitted, mjg.description AS grade, mc.company_name AS company_dinilai, hqa.subtotal_score,
		hqa.id_recommendation_qualitative, hrq.id_recommendation_header 
				FROM hr_qualitative_appraisers hqa
				JOIN hr_employee he
				ON hqa.id_employee_appraisers = he.id_employee AND he.status = 'A'
				JOIN hr_employee he2
				ON hqa.id_employee_participant = he2.id_employee AND he2.status = 'A'
				LEFT JOIN master_position_detail mpd
				ON (he2.id_employee = mpd.id_employee OR he2.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_company mc
				ON hqa.id_company = mc.id_company
				LEFT JOIN hr_recommendation_qualitative hrq
				ON hqa.id_recommendation_qualitative = hrq.id_recommendation_qualitative
				WHERE he.id_user = ".session('id_user')." AND hqa.status = 'A' AND hqa.id_period IS NULL AND hqa.transaction_type = 'RECO'
				ORDER BY hqa.id_qualitative_appraisers DESC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getTotal($id_employee_participant,$id_recommendation_header) {
        $sql = "SELECT hqa.*,hrh.id_recommendation_header 
				FROM hr_qualitative_appraisers hqa
				LEFT JOIN hr_recommendation_qualitative hrq
				ON hqa.id_recommendation_qualitative = hrq.id_recommendation_qualitative 
				LEFT JOIN hr_recommendation_header hrh
				ON hrq.id_recommendation_header = hrh.id_recommendation_header 
				WHERE hqa.transaction_type = 'RECO' AND hqa.id_period IS NULL AND hqa.id_employee_participant = ".$id_employee_participant." 
				AND hrh.id_recommendation_header = ".$id_recommendation_header;
        $result = DB::select($sql);
        return $result;
    }
		
}
