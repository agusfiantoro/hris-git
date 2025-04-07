<?php

namespace App\Models\CareerAdministration\CareerHistory;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CareerHistory extends Model {

    protected $table = 'hr_career_transaction';
    protected $primaryKey = 'id_career_transaction';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_career_transaction', 'id_employee', 'id_employee2', 'transaction_number', 'reference_number', 'id_transition_category', 'id_transaction_type', 'id_old_employment_status', 'id_employment_status', 'id_old_position_detail', 'id_position_detail','id_position_routing', 'id_job_grade', 'id_job_status', 'id_location', 'id_contract_employee', 'effective_date', 'expired_date', 'remark', 'attachment_type', 'attachment', 'attachment_letter', 'enable_approval', 'id_approval', 'id_approval_status', 'id_company_destination', 'status', 'id_company', 'executed', 'id_terminate_reason', 'resign_category', 'created_by', 'updated_by', 'id_recommendation_header'
    ];
	
	public static function getdata($group_branch, $dateType, $startDate, $endDate) {
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND (
			(mpd.id_position_detail IS NOT NULL AND mpd.id_branch in(".$group_branch.")) 
			OR (mpd.id_position_detail IS NULL AND mpd2.id_branch in(".$group_branch."))
			)";
		}
		$dateCondition = "";
		if($startDate && $endDate) {
			try {
				$startDate = Carbon::parse($startDate)->toDateString();
				$endDate = Carbon::parse($endDate)->toDateString();
				
				if(in_array($dateType, ['creation_date', 'effective_date'])) {
					$dateCondition = "AND cast(hct.".$dateType." AS date) BETWEEN '$startDate' AND '$endDate'";
				}
			} catch(\Exception $e) {
			}
		}

		$sql = "SELECT hct.id_career_transaction, hct.reference_number, 
				CASE 
					WHEN hct.transaction_number IS NOT NULL THEN hct.transaction_number
					ELSE hel.reference_number
				END AS transaction_number,
				j_c.id_employee as id_employee_old, hct.id_employee, he.nik_employee, he.name, mgd.description as transition_category, REPLACE( mgd2.description, ' ', '_' ) as code_transaction_type, mgd2.description as transaction_type, 
					mgd3.description as employment_status, mc.company_name as company_destination, 
					coalesce(mpd.description,mpd2.description) as position_detail, coalesce(mpr.description,mpr2.description) as position_routing, 
					coalesce(mjg.description,mjg2.description) as job_grade, coalesce(mjs.description,mjs2.description) as job_status, 
					coalesce(ml.description,ml2.description) as location, hct.effective_date, hct.expired_date, hct.attachment, hct.attachment_letter, 
					mgd4.code as code_app_status, mgd4.description as desc_app_status, hct.request_resign_date, hct.resign_category, 
					mgd5.description as terminate_reason, hct.remark, to_char(hct.creation_date, 'YYYY-MM-DD') as creation_date, hct.executed, hct.id_recommendation_header, hrh.reference_number AS ref_reco
				FROM hr_career_transaction hct
				LEFT JOIN hr_electronic_letter hel
				ON hct.id_career_transaction = hel.id_career_transaction
				JOIN hr_employee he
				  ON hct.id_employee = he.id_employee
				JOIN master_general_data mgd
				  ON hct.id_transition_category = mgd.id_general_data
				JOIN master_general_data mgd2
				  ON hct.id_transaction_type = mgd2.id_general_data
				JOIN master_general_data mgd3
				  ON hct.id_employment_status = mgd3.id_general_data
				LEFT JOIN master_general_data mgd4
				  ON hct.id_approval_status = mgd4.id_general_data
				LEFT JOIN master_general_data mgd5
				  ON hct.id_terminate_reason = mgd5.id_general_data
				LEFT JOIN master_position_detail mpd
				  ON hct.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_routing mpr
				  ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_location ml
				  ON mpd.id_location = ml.id_location
				LEFT JOIN master_job_grade mjg
				  ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_status mjs
				  ON mpr.id_job_status = mjs.id_job_status
				LEFT JOIN master_position_detail mpd2
				  ON hct.id_old_position_detail = mpd2.id_position_detail
				LEFT JOIN master_position_routing mpr2
				  ON mpd2.id_position_routing = mpr2.id_routing
				LEFT JOIN master_location ml2
				  ON mpd2.id_location = ml2.id_location
				LEFT JOIN master_job_grade mjg2
				  ON mpr2.id_job_grade = mjg2.id_job_grade
				LEFT JOIN master_job_status mjs2
				  ON mpr2.id_job_status = mjs2.id_job_status
				LEFT JOIN master_company mc
				  ON hct.id_company_destination = mc.id_company
				LEFT JOIN hr_recommendation_header hrh
				  ON hct.id_recommendation_header = hrh.id_recommendation_header  
				LEFT JOIN (
					select
						hct2.id_employee,
						he2.nik_employee
					from
						hr_career_transaction hct2
					join hr_employee he2
								on
						hct2.id_employee = he2.id_employee
					join master_general_data mgd6
								on
						hct2.id_transaction_type = mgd6.id_general_data
					left join master_general_data mgd7
								on
						hct2.id_approval_status = mgd7.id_general_data
					where
						hct2.id_company = ?
						and mgd6.description = 'Terminate'
						and mgd7.code = 'Approved'
							) as j_c
							on
					he.id_employee = j_c.id_employee
				WHERE hct.id_company = ? AND mgd4.code = 'Approved' ".$branch." ".$dateCondition."
					ORDER BY hct.id_career_transaction DESC";
        $result = DB::select($sql,[session('id_company'), session('id_company')]);
		// dd($result);
        return $result;
    }
	
	public static function browse($value) {
			$data = [
				'career' => $value
			];	
			if($data['career'] == "Orientation" || $data['career'] == "Failed_Orientation" || $data['career'] == "Temporary_Assignment" || $data['career'] == "Pass_RPK"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/memo/apimemo';
			}
			else if($data['career'] == "Employment_Status_Changes"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontract?contract=pkwtt';
			}
			else if($data['career'] == "New_Employee"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontract?contract=pkwt';
			}
			else if($data['career'] == "Promotion" || $data['career'] == "Pass_Orientation" || $data['career'] == "Mutation" || $data['career'] == "Demotion"|| $data['career'] == "Rotation" || $data['career'] == "Relocation"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/career/apicareer?career='.$data['career'];
			}
			else{
				$url = 'https://hris.borwita.co.id/nosurat/index.php/career/apinocareer';				
			}
			$response = file_get_contents($url);
			$decode = json_decode($response);
		//	$result = collect($decode);
			
		return $decode;
        //    echo $result;
    }
	
	public static function get_career_edit($data) {
        $result = [];
        $sql = "SELECT hct.id_career_transaction, hct.transaction_number, hct.effective_date, hct.expired_date, hct.attachment, hct.attachment_letter,
hct.enable_approval, hct.status, hct.id_company, he.id_employee, he.name, he.nik_employee, mgd2.description as transition_category, 
mgd3.description as transaction_type, mgd4.description as old_employment_status, 
mgd5.description as employment_status, mpd2.description as old_position_detail, 
mpd.description as position_detail, hah.description as approval, 
mc2.company_name as old_company, mc.company_name as company_destination,
md2.description as old_dept, md.description as dept, mpr2.description as old_routing, mpr.description as routing, 
ml2.description as old_location, mjg2.description as old_job_grade, mjs2.description as old_job_status, mgd.code as code_status,
ml.description as location, mjg.description as job_grade, mjs.description as job_status, mgd.code as code_status, hct.resign_category, mgd6.description as terminate_reason, hct.remark, CONCAT(hrh.reference_number,' (',mgd8.description,')') AS ref_number_reco, mgd2.code AS code_cat
                FROM hr_career_transaction hct
				LEFT JOIN hr_employee he
				ON hct.id_employee = he.id_employee
				LEFT JOIN  master_general_data mgd
                ON  hct.id_approval_status = mgd.id_general_data
				LEFT JOIN  master_general_data mgd2
                ON  hct.id_transition_category = mgd2.id_general_data
				LEFT JOIN  master_general_data mgd3
                ON  hct.id_transaction_type = mgd3.id_general_data
				LEFT JOIN  master_general_data mgd4
                ON  hct.id_old_employment_status = mgd4.id_general_data
				LEFT JOIN  master_general_data mgd5
                ON  hct.id_employment_status = mgd5.id_general_data
				LEFT JOIN master_general_data mgd6
				ON hct.id_terminate_reason = mgd6.id_general_data
				LEFT JOIN master_position_detail mpd
				ON hct.id_position_detail = mpd.id_position_detail
				LEFT JOIN master_position_detail mpd2
				ON hct.id_old_position_detail = mpd2.id_position_detail
				LEFT JOIN hr_approval_header hah
				ON hct.id_approval = hah.id_approval
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_position_routing mpr2
				ON mpd2.id_position_routing = mpr2.id_routing
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				LEFT JOIN master_location ml2
				ON mpd2.id_location = ml2.id_location
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_job_grade mjg2
				ON mpr2.id_job_grade = mjg2.id_job_grade
				LEFT JOIN master_job_status mjs
				ON mpr.id_job_status = mjs.id_job_status
				LEFT JOIN master_job_status mjs2
				ON mpr2.id_job_status = mjs2.id_job_status
				LEFT JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				LEFT JOIN master_job_position mjp2
				ON mpr2.id_position = mjp2.id_position
				LEFT JOIN master_department md
				ON mjp.id_dept = md.id_dept
				LEFT JOIN master_department md2
				ON mjp2.id_dept = md2.id_dept
				LEFT JOIN master_company mc
				ON hct.id_company_destination = mc.id_company
				LEFT JOIN master_company mc2
				ON mpd2.id_company = mc2.id_company
				LEFT JOIN hr_recommendation_header hrh
             	ON hct.id_recommendation_header = hrh.id_recommendation_header
             	LEFT JOIN master_general_data mgd7
             	ON hrh.id_transition_category = mgd7.id_general_data
				LEFT JOIN master_general_data mgd8
             	ON hrh.id_transition_type = mgd8.id_general_data
				WHERE hct.id_career_transaction = ?";
        $result = (Array) DB::select($sql, [$data['id_career_transaction']])[0];
        return $result;
    }
	
	public static function cancel_career($id) {
    //    $sql = "SELECT * FROM  sp_action_cancel_career(?,?,?)";
        $sql = "SELECT hct.*, mgd.code AS category, mgd2.description AS type FROM hr_career_transaction hct
					JOIN master_general_data mgd
					ON hct.id_transition_category = mgd.id_general_data		
					LEFT JOIN master_general_data mgd2
					ON hct.id_transaction_type = mgd2.id_general_data
					WHERE hct.id_career_transaction = ? AND hct.id_company = ?";
		
        $result = DB::select($sql, [$id, session('id_company')])[0];
     //   $result = DB::select($sql, [session('id_company'),$id,session('id_user')]);
        return $result;
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Cancel' AND mgd.status = 'A' AND mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function cancel_reject() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code in('Cancel','Rejected') AND mgd.status = 'A' AND mgd.id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
}
