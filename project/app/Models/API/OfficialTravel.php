<?php

namespace App\Models\API;

use App\Models\CashAdvance\OfficialTravel\HrCashAdvance;
use App\Models\CashAdvance\OfficialTravel\HrOfficialTravel;
use App\Models\Organization\MasterOrganization\MasterRegional;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfficialTravel extends Model
{
    // use HasFactory;
	protected $table="hr_official_travel";
	protected $primaryKey="id_official_travel";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

    public static function getOfficialTravel($idCompany, $idUser, $startDate, $endDate, $reasonCode = null) {
		$filter = "";
		if($reasonCode) {
			$filter .= "AND master_general_data.code = '".$reasonCode."'";
		}
        $data = "SELECT hr_official_travel.id_official_travel as id_official_travel, hr_official_travel.reference_number as reference_number, hr_official_travel.letter_date as letter_date, hr_employee.name as name, master_position_routing.description as dec_position,
        hr_official_travel.start_date as start_date, hr_official_travel.end_date as end_date, hr_official_travel.location_from as location_from, hr_official_travel.location_to as location_to, master_general_data.description as category, hr_official_travel.id_reason_group as id_reason_group, hr_official_travel.reason_notes as reason_notes, hr_official_travel.unlock_gps as unlock_gps, hr_official_travel.is_have_cash_advance as is_have_cash_advance, approval_status.code as code_app_status, approval_status.code as status_approval, approval_status.description as desc_app_status, hr_official_travel.travel_status,
        hr_employee.name as name, hr_approval_transaction.id_approval_transaction as id_approval_transaction,
        hr_official_travel.note_rejected as note_rejected, hr_official_travel.note_revised as note_revised
        FROM hr_official_travel
        LEFT JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
        LEFT JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
        LEFT JOIN master_company ON master_company.id_company=hr_official_travel.id_company
        LEFT JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group $filter
        LEFT JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing
        LEFT JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
        LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
        LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
        LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
        WHERE hr_official_travel.id_company = ".$idCompany." 
			AND hr_approval_transaction.source_transaction_type='Official_Travel' 
			AND hr_approval_transaction.id_company=".$idCompany." 
			AND hr_employee.id_user=".$idUser." AND hr_official_travel.letter_date >= '".$startDate."' 
			AND hr_official_travel.letter_date <= '".$endDate."'
			$filter
        ORDER BY hr_official_travel.letter_date DESC
        ";
        $data = DB::select($data);
        return $data;
    }

    public static function getData($idOfficialTravel, $idCompany) {
        $data =  DB::select("SELECT 
			hrot.id_official_travel as id_official_travel,
			hrot.reference_number as reference_number,
			hre.name as name,
			hrot.id_position_detail as id_position_detail,
			mpd.description as position_detail,
			hrot.start_date as start_date,
			hrot.end_date as end_date,
			hrot.location_from as location_from,
			hrot.location_to as location_to,
			hrot.id_reason_group as id_reason_group,
			mgd.description as reason_group,
			hrot.reason_notes as reason_notes,
			hrot.unlock_gps as unlock_gps,
			hrot.is_have_cash_advance as is_have_cash_advance,
			hrot.id_approval as id_approval,
			hrah.description as approval,
			hrot.id_approval_request as id_approval_request,
			hrem.name as approval_request,
			hrot.id_approval_status as id_approval_status,
			mgdd.description as approval_status,
			hrot.status as status,
			hrot.note_revised as note_revised,
			hrot.note_rejected as note_rejected,
			hrot.is_verified as is_verified,
			hrot.travel_status as travel_status,
			hrot.letter_date as letter_date
		FROM hr_official_travel hrot
		LEFT JOIN master_position_detail mpd ON hrot.id_position_detail = mpd.id_position_detail
		LEFT JOIN hr_employee hre ON hrot.request_by = hre.id_employee
		LEFT JOIN master_general_data mgd ON hrot.id_reason_group = mgd.id_general_data
		LEFT JOIN hr_approval_header hrah ON hrot.id_approval = hrah.id_approval
		LEFT JOIN hr_employee hrem ON hrot.id_approval_request = hrem.id_employee
		LEFT JOIN master_general_data mgdd ON hrot.id_approval_status = mgdd.id_general_data
		WHERE hrot.id_official_travel = ? and hrot.id_company = ?
		", [ $idOfficialTravel, $idCompany ]);
		return $data;
    }

    public static function getDataTransport($idOfficialTravel, $idCompany) {
        $sql = "SELECT her.id_expense_request, mp.description AS trans_type, SPLIT_PART(her.notes,';',1) AS trans_reco, 
				SPLIT_PART(her.notes,';',2) AS city_from, SPLIT_PART(her.notes,';',3) AS city_to, mb.description AS branch, 
				SPLIT_PART(her.description,';',1) AS date_trans, SPLIT_PART(her.description,';',2) AS time_trans
				FROM hr_expense_request her
				LEFT JOIN inventory.master_product mp 
				ON her.id_product = mp.id_product
				LEFT JOIN inventory.master_product_categories mpc
				ON mp.id_product_categories = mpc.id_product_categories 
				LEFT JOIN hr_cash_advance hca
				ON her.id_cash_advance = hca.id_cash_advance
				LEFT JOIN hr_official_travel hot
				ON hca.id_official_travel = hot.id_official_travel
				LEFT JOIN master_branch mb
				ON her.id_branch = mb.id_branch
				WHERE mpc.code = 'TST' AND hot.id_company = ".$idCompany." AND hot.id_official_travel = ".$idOfficialTravel." AND her.status = 'A'
				ORDER BY her.description ASC";
		$result = DB::select($sql);
		return $result;
    }

    public static function getDataAccommodation($idOfficialTravel, $idCompany) {
        $sql = "SELECT her.id_expense_request, mp.description AS category, SPLIT_PART(her.notes,';',1) AS hotel_name, 
				SPLIT_PART(her.notes,';',2) AS city, mb.description AS branch, SPLIT_PART(her.description,' to ',1) AS date_i,
				SPLIT_PART(SPLIT_PART(her.description,' to ',2),';',1) AS date_o, her.qty
				FROM hr_expense_request her
				JOIN inventory.master_product mp 
				ON her.id_product = mp.id_product
				JOIN inventory.master_product_categories mpc
				ON mp.id_product_categories = mpc.id_product_categories 
				LEFT JOIN hr_cash_advance hca
				ON her.id_cash_advance = hca.id_cash_advance
				LEFT JOIN hr_official_travel hot
				ON hca.id_official_travel = hot.id_official_travel
				LEFT JOIN master_branch mb
				ON her.id_branch = mb.id_branch
				WHERE mpc.code = 'ACD' AND hot.id_company = ".$idCompany." AND hot.id_official_travel = ".$idOfficialTravel." AND her.status = 'A'
				ORDER BY her.description ASC";
		$result = DB::select($sql);
		return $result;
    }

    public static function getCashAdvance($idOfficialTravel, $idCompany) {
        $sql = "SELECT her.id_expense_request, mp.description AS category, SPLIT_PART(her.description,' to ',1) AS from_dates,
				SPLIT_PART(SPLIT_PART(her.description,' to ',2),';',1) AS to_dates, SPLIT_PART(her.notes,';',2) AS region, SPLIT_PART(her.notes,';',3) AS branch, 
				her.qty, her.unit_price AS budget, her.total_amount AS total, SPLIT_PART(her.notes,';',1) AS notes
				FROM hr_expense_request her
				JOIN inventory.master_product mp 
				ON her.id_product = mp.id_product
				JOIN inventory.master_product_categories mpc
				ON mp.id_product_categories = mpc.id_product_categories 
				LEFT JOIN hr_cash_advance hca
				ON her.id_cash_advance = hca.id_cash_advance
				LEFT JOIN hr_official_travel hot
				ON hca.id_official_travel = hot.id_official_travel
				LEFT JOIN master_branch mb
				ON her.id_branch = mb.id_branch
				WHERE mpc.code = 'CSM' AND hot.id_company = ".$idCompany." AND hot.id_official_travel = ".$idOfficialTravel." AND her.status = 'A'
				ORDER BY her.description ASC";
		$result = DB::select($sql);
		return $result;
    }

    public static function getOfficialTravelTransport($idCompany) {
		$data = "SELECT master_product.description as text, master_product.id_product as id, master_product.code as code
		FROM inventory.master_product LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories 
		-- LEFT JOIN master_unit_measures ON master_unit_measures.id_uom=master_product.id_uom
		WHERE inventory.master_product_categories.description='Transportation' AND inventory.master_product.status='A' AND inventory.master_product_categories.status='A' AND inventory.master_product.id_company='$idCompany' ORDER BY inventory.master_product.description ASC
		";
		$data = DB::select($data);
		return $data;
    }

	public static function getTravelTypes($idCompany) {
		$data = "SELECT mgd.id_general_data as id_general_data, mgd.description as description, mgd.code as code_travel 
		FROM master_general_data as mgd
		JOIN master_general_type as mgt 
		ON mgt.id_general_type=mgd.id_general_type
		WHERE mgd.id_general_type='35' AND mgt.general_type='master_group_bussiness_trip' 
		AND mgd.id_company=? AND mgd.status='A'";
		$data = DB::select($data, [$idCompany]);
		return $data;
	}

	public static function maximumOfficialTravelRequest($idCompany) // get max to hr_config_settings
	{
		$data = DB::table('hr_config_settings')
		->select(
			\DB::RAW('maximum_official_travel_request')
		)
		->where('status','A')
		->where('id_company', $idCompany)
		->first();
		return $data;
	}

	public static function getProductTransport($idCompany)
	{
		$data = "SELECT master_product.description as text, master_product.id_product as id, master_product.code as code, master_product.variant
		FROM inventory.master_product LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories 
		-- LEFT JOIN master_unit_measures ON master_unit_measures.id_uom=master_product.id_uom
		WHERE inventory.master_product_categories.description='Transportation' AND inventory.master_product.status='A' AND inventory.master_product_categories.status='A' AND inventory.master_product.id_company='$idCompany' ORDER BY inventory.master_product.description ASC
		";
		$data = DB::select($data);
		for($i = 0; $i<count($data);++$i) {
			$data[$i]->variant = explode(",", str_replace("}", "", str_replace("{", "", $data[$i]->variant)));
			
		}
		return $data;
	}

	public static function getProductAkomodasi($idCompany)
	{
		$data = "SELECT inventory.master_product.description as text, inventory.master_product.id_product as id
		FROM inventory.master_product LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories
		-- LEFT JOIN master_unit_measures ON master_unit_measures.id_uom=inventory.master_product.id_uom
		WHERE inventory.master_product_categories.description='Accomodation' AND inventory.master_product.status='A' AND inventory.master_product_categories.status='A' AND inventory.master_product.id_company='$idCompany' ORDER BY inventory.master_product.description ASC
		";
		$data = DB::select($data);
		return $data;
	}
	public static function getProductCashAdvance($idCompany)
	{
		$data = "SELECT inventory.master_product.description as text, inventory.master_product.code as code_product, inventory.master_product.id_product as id, inventory.master_product.long_description as long_description
		FROM inventory.master_product LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=master_product.id_product_categories
		-- LEFT JOIN inventory.master_unit_measures ON inventory.master_unit_measures.id_uom=master_product.id_uom
		WHERE inventory.master_product_categories.description='Consumable' AND inventory.master_product.status='A' AND inventory.master_product_categories.status='A' AND inventory.master_product.id_company='$idCompany' ORDER BY inventory.master_product.code ASC
		";
		$data = DB::select($data);
		return $data;
	}
	public static function getBranchTransport($idCompany)
	{
		$data = "SELECT id_branch as id, description as text 
				FROM master_branch WHERE status='A' AND id_company='".$idCompany."' ORDER BY description ASC";
		$data = DB::select($data);
		return $data;
	}

	public static function getApprovalStatus($idCompany) {
		$sql = "SELECT 
		id_general_data id,
		description text,
		code
		FROM  master_general_data where status = 'A' and id_general_type = 7 and id_company =" . $idCompany;
		$result = DB::select($sql);
		return $result;
	}

	public static function getApprovalBy($idEmployee, $idUser, $idCompany)
	{
		// $nik = DB::selectOne("SELECT * FROM hr_employee WHERE id_user = ?", [session('id_user')]);;

		$job_grade = HrOfficialTravel::getJobGrade($idUser, 1);
		$sql_approval_hirarki = "SELECT hah.id_approval id, hah.description text
		FROM master_general_data mgd
		JOIN hr_approval_header hah
		ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = '".$idCompany."'
		LEFT JOIN master_general_type mgt
		ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
		LEFT JOIN master_job_grade mjg
		ON hah.id_job_grade = mjg.id_job_grade AND mjg.description = 'Director'
		WHERE mgd.id_company = '".$idCompany."' AND mgd.code='Official_Travel'";
		if($job_grade && $job_grade->job_level == 1) {
			$sql_approval_hirarki .= " AND hah.is_auto_approved = true";
		}
		$sql_approval_hirarki .= " LIMIT 1";

		$sql_approval_hirarki = DB::select($sql_approval_hirarki);
		if(!$job_grade || $job_grade->job_level != 1) {
			$sql_approval_by = "SELECT he2.id_employee id, he2.name text FROM (
				SELECT (
				SELECT hah.id_approval 
				FROM master_general_data mgd
				JOIN hr_approval_header hah
				ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = '".$idCompany."'
				LEFT JOIN master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
				WHERE mgd.id_company = '".$idCompany."' AND mgd.code='Official_Travel'
				), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
				FROM sp_funct_approval_organization_hierarchy_view (? ,'".$idCompany."',null,Array['gm-level','manager-level']) sfao
				left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval
				LIMIT 1
				) as hirar
				join hr_employee he2 
				on hirar.id_employee_approval=he2.id_employee";
				$sql_approval_by = DB::select($sql_approval_by, [$idEmployee]);

			// if(in_array($nik->nik_employee, ['2019081233LU', '20130401SP', '20130401VK'])) {
			// 	$sql_approval_by = DB::select("SELECT he.id_employee id, he.name text, mu.id_user as id_user from hr_employee he 
			// 	join master_position_detail mpd on he.id_employee = mpd.id_employee and mpd.status = 'A'
			// 	join master_position_routing mpr on mpd.id_position_routing = mpr.id_routing and mpr.status = 'A'
			// 	join master_job_grade mjg on mpr.id_job_grade = mjg.id_job_grade 
			// 	join master_users mu on he.id_user = mu.id_user 
			// 	where mjg.id_company = ? and he.id_company = ? and mu.id_user = ?", [session('id_company'), session('id_company'), session('id_user')]);
			// }
			
		} else {
			$sql_approval_by = [HrOfficialTravel::getDirector("he.id_employee id, he.name text, mu.id_user as id_user", $idUser, $idCompany)];
		}
		return ['approval_hierarchy'=>$sql_approval_hirarki,'approval_by'=>$sql_approval_by, 'job_grade'=>$job_grade];
	}

	public static function getUser($idUser, $idCompany)
	{
		$user = "SELECT
		master_position_routing.description as dec_position, master_position_routing.id_routing as id_routing, master_position_detail.id_position_detail,
		master_region.description as dec_region, master_region.id_region as id_region,
		master_branch.description as dec_branch, master_branch.id_branch as id_branch,
		STRING_AGG(master_principal.description,', ') as dec_division, 
		--master_principal.id_principal as id_division,
		hr_employee.id_employee as id_employee, hr_employee.name as name,
		master_department.id_dept as id_dept, master_department.department_code as department_code,
		master_job_grade.id_job_grade as id_job_grade, master_job_grade.description as dec_job_grade, master_job_grade.job_class_group as job_class_group
		FROM hr_employee 
		LEFT JOIN master_position_detail 
		on (master_position_detail.id_employee = hr_employee.id_employee OR hr_employee.id_employee=master_position_detail.id_employee2) AND master_position_detail.secondary_position = false
		LEFT JOIN master_position_routing 
		on master_position_routing.id_routing = master_position_detail.id_position_routing 
		LEFT JOIN master_branch 
		on master_branch.id_branch = master_position_detail.id_branch 
		LEFT JOIN master_region 
		on master_region.id_region = master_branch.id_region 
		LEFT JOIN relation_positiondetail_principal 
		on relation_positiondetail_principal.id_position_detail = master_position_detail.id_position_detail 
		LEFT JOIN master_principal 
		on master_principal.id_principal = relation_positiondetail_principal.id_principal 
		LEFT JOIN master_division 
		on master_division.id_division = master_principal.id_division 
		LEFT JOIN master_job_position 
		ON master_job_position.id_position=master_position_routing.id_position 
		LEFT JOIN master_department 
		ON master_department.id_dept=master_job_position.id_dept 
		LEFT JOIN master_job_grade 
		ON master_job_grade.id_job_grade=master_position_routing.id_job_grade
		WHERE hr_employee.id_user = ".$idUser." AND hr_employee.id_company= ".$idCompany."
		GROUP BY master_position_routing.description, master_position_routing.id_routing, master_position_detail.id_position_detail,
		master_region.description, master_region.id_region, master_branch.description , master_branch.id_branch,
	--	master_principal.id_principal, 
		hr_employee.id_employee, hr_employee.name, master_department.id_dept, master_department.department_code, 
		master_job_grade.id_job_grade, master_job_grade.job_class_group";
		$user = DB::selectOne($user);
		return $user;
	}

	public static function formatSave($start_end, $request, $idCompany)
	{
		$dateRange = $start_end;
		list($start_date, $end_date) = explode(" to ", $dateRange);
		$company = DB::table('master_company')
		->select(
			\DB::RAW('id_company'),
			\DB::RAW('company_code')
		)
		->where('id_company', $idCompany)
		->first();
		$bulan = date('m', strtotime(date('Y-m-d')));
		$tahun = date('Y', strtotime(date('Y-m-d')));
		$department = DB::table('master_department')
		->select(
			\DB::RAW('department_code')
		)
		->where('id_company', $idCompany)
		->where('id_dept',$request->id_dept)
		->first();
		$department_code = explode("_", $department->department_code)[1];
		$id_letter = HrOfficialTravel::join('master_position_detail','master_position_detail.id_position_detail','=','hr_official_travel.id_position_detail')
		->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->join('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
		->join('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		->whereMonth('hr_official_travel.letter_date',$bulan)
		->whereYear('hr_official_travel.letter_date',$tahun)
		->where('master_job_position.id_dept',$request->id_dept)
		->where('master_branch.id_region',$request->id_region)
		->where('hr_official_travel.id_company',$idCompany)
		->where('master_position_detail.id_company',$idCompany)
		->max('hr_official_travel.reference_number');
		$no_urut = substr($id_letter, 0,4);
	//	dd($no_urut);
		$no_urut++;
		$kode = sprintf("%04s", abs($no_urut));
	//	$kode = '0050';
		$region = MasterRegional::where('id_region',$request->id_region)->first();
		if ($region->region_code == "PST") {
			$code_region = "HQ";
		}else{
			$code_region = $region->region_code;
		}
		
		$sql = "SELECT 
		id_general_data id,
		description text,
		code
		FROM  master_general_data where status = 'A' AND code='Request_Approval' and id_general_type = 7 and id_company =" . $idCompany;
		$result = DB::select($sql);
		$id_approval_status_transaction = $result[0]->id;
		//	self::send_mail($request);
		
		$format = $kode."/".$company->company_code."-".'PDN/'.$code_region."-".$department_code."/".$bulan."/".substr($tahun,-2);
		return ['start_date'=>$start_date,'end_date'=>$end_date,'format'=>$format,'id_approval_status_transaction'=>$id_approval_status_transaction];
	}

	public static function formatSaveToTransaction($requestBy, $idCompany)
	{
		$sql = "SELECT mgd2.code, han.* from (
		SELECT (
		SELECT hah.id_approval 
		FROM master_general_data mgd
		JOIN hr_approval_header hah
		ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = '".$idCompany."'
		LEFT JOIN master_general_type mgt
		ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
		WHERE mgd.id_company = '".$idCompany."' AND mgd.code='Official_Travel' AND hah.status = 'A' AND hah.id_job_grade IS NULL 
		AND hah.is_auto_approved = false
		), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
		FROM sp_funct_approval_organization_hierarchy_view (?,'".$idCompany."',null,Array['gm-level','manager-level']) sfao
		left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval 
		LIMIT 1
		) as han 
		left join hr_approval_header hah2 on hah2.id_approval=han.id_approval 
		left join master_general_data mgd2 on mgd2.id_general_data=hah2.id_approval_doc_type and mgd2.id_company = '".$idCompany."'";
		$sql = DB::select($sql, [$requestBy]);
		return $sql;
	}

	public static function saveCashAdvanceToOfficialTravel($request, $idUser, $idCompany)
	{
		$officialTravel = HrOfficialTravel::findOrFail($request->id_official_travel);
		$hr_cashadvance = new HrCashAdvance();
		$hr_cashadvance -> id_official_travel = $request->id_official_travel;
		$hr_cashadvance -> id_employee = $request->request_by;
		$hr_cashadvance -> transaction_date = date('Y-m-d');
		$hr_cashadvance -> id_currency_cash_advance = $request->id_currency_cash_advance;
		$hr_cashadvance -> currency_rate_cash_advance = $request->currency_rate_cash_advance;
		$hr_cashadvance -> status = 'A';
		$hr_cashadvance -> id_company = $idCompany;
		$hr_cashadvance -> created_by = $idUser;
		$hr_cashadvance -> reference_number = $officialTravel->reference_number;
		$hr_cashadvance -> id_approval = $officialTravel->id_approval;
		$hr_cashadvance -> id_approval_request = $officialTravel->id_approval_request;
		$hr_cashadvance -> id_approval_status = $officialTravel->id_approval_status;
		$hr_cashadvance -> save();
		// dd($data->id_official_travel);
		return $hr_cashadvance;
	}
}