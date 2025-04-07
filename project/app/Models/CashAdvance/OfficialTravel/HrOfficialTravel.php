<?php

namespace App\Models\CashAdvance\OfficialTravel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Organization\MasterOrganization\MasterRegional;
use Carbon\Carbon;

class HrOfficialTravel extends Model
{
    // use HasFactory;
	protected $table="hr_official_travel";
	protected $primaryKey="id_official_travel";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

	public static function get_data($id_official_travel)
	{
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
		", [ $id_official_travel, session('id_company') ]);
		return $data;
	}

	public static function get_offtrav() // index
	{
		$id_company = session('id_company');
		$data = "SELECT hr_official_travel.id_official_travel as id_official_travel, hr_official_travel.reference_number as reference_number, hr_official_travel.letter_date as letter_date, hr_employee.name as name, master_position_routing.description as dec_position,
		hr_official_travel.start_date as start_date, hr_official_travel.end_date as end_date, hr_official_travel.location_from as location_from, hr_official_travel.location_to as location_to, master_general_data.description as category, hr_official_travel.id_reason_group as id_reason_group, hr_official_travel.reason_notes as reason_notes, hr_official_travel.unlock_gps as unlock_gps, hr_official_travel.is_have_cash_advance as is_have_cash_advance, approval_status.code as code_app_status, approval_status.code as status_approval, approval_status.description as desc_app_status, hr_official_travel.travel_status,
		hr_employee.name as name, hr_approval_transaction.id_approval_transaction as id_approval_transaction,
		hr_official_travel.note_rejected as note_rejected, hr_official_travel.note_revised as note_revised
		FROM hr_official_travel
		LEFT JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
		LEFT JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
		LEFT JOIN master_company ON master_company.id_company=hr_official_travel.id_company
		LEFT JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group
		LEFT JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing
		LEFT JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
		LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
		LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
		LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
		WHERE hr_official_travel.id_company = ".$id_company." AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_company=".$id_company." AND hr_employee.id_user=".session('id_user')." AND hr_official_travel.id_approval_request = hr_approval_transaction.id_employee_approval
		ORDER BY hr_official_travel.id_official_travel DESC
		";
		$data = DB::select($data);
		return $data;
	}
	
	public static function get_offtrav_summary($start_end) // index
	{
		$id_company = session('id_company');
		$filter = "";
		if (!empty($start_end)) {
			list($start, $end) = explode(" to ", $start_end);
			$filter = " AND hr_official_travel.start_date BETWEEN '$start' AND '$end'";
		}
		$data = "SELECT hr_official_travel.id_official_travel as id_official_travel, hr_official_travel.reference_number as reference_number, hr_official_travel.letter_date as letter_date, hr_employee.name as name, master_position_routing.description as dec_position,
		hr_official_travel.start_date as start_date, hr_official_travel.end_date as end_date, hr_official_travel.location_from as location_from, hr_official_travel.location_to as location_to, master_general_data.description as category, hr_official_travel.id_reason_group as id_reason_group, hr_official_travel.reason_notes as reason_notes, hr_official_travel.unlock_gps as unlock_gps, hr_official_travel.is_have_cash_advance as is_have_cash_advance, approval_status.code as code_app_status, approval_status.code as status_approval, approval_status.description as desc_app_status, hr_official_travel.travel_status,
		hr_employee.name as name, hr_approval_transaction.id_approval_transaction as id_approval_transaction,
		hr_official_travel.note_rejected as note_rejected, hr_official_travel.note_revised as note_revised
		FROM hr_official_travel
		LEFT JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
		LEFT JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
		LEFT JOIN master_company ON master_company.id_company=hr_official_travel.id_company
		LEFT JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group
		LEFT JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing
		LEFT JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
		LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
		LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
		LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
		WHERE hr_official_travel.id_company = ".$id_company."  ".$filter." AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_company=".$id_company." AND hr_official_travel.id_approval_request = hr_approval_transaction.id_employee_approval
		ORDER BY hr_official_travel.id_official_travel DESC";
		$data = DB::select($data);
		return $data;
	}
	public static function maximum_official_travel_request() // get max to hr_config_settings
	{
		$data = DB::table('hr_config_settings')
		->select(
			\DB::RAW('maximum_official_travel_request')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->first();
		return $data;
	}
	public static function cek_position_routing() 
	{
		$result = null;
		$sql = "SELECT unnest(hcs.id_position_routing) as id_position_routing
				FROM hr_config_settings hcs 
				WHERE hcs.id_company = ".session('id_company')." AND hcs.status = 'A'";
		$res = DB::select($sql);
		foreach($res as $val){
			$result[] = $val->id_position_routing;
		}
		return $result;
	}
	public static function check_max_date_cashadvance($request) // cek max to hr_config_settings
	{
		$id_employee = $request->request_by;
		$start_date = $request->start_date;
		$id_company = session('id_company');
		$date = date('Y-m-d');
		$data = "SELECT count (distinct hwd.current_dates)::int as total_days
		FROM hr_work_days hwd
		JOIN hr_config_settings hcs
		ON hwd.id_company = hcs.id_company
		WHERE hwd.current_dates > current_date::date AND hwd.current_dates <= '$start_date'
		AND hwd.day_type = 'WD'
		AND hwd.id_holiday IS NULL
		AND hwd.id_company = '$id_company'
		AND hwd.id_employee = '$id_employee'";
		$data = DB::select($data);
		// dd($data);
		return $data;
	}
	public static function get_region_destination($request, $idUser = null, $idCompany = null)
	{
		$id_user = $idUser ?? session('id_user');
		$id_company = $idCompany ?? session('id_company');
		$code_product = $request->code_product;
		$data = "SELECT 
			DISTINCT master_region.id_region as id, 
			master_region.description as text, 
			master_grade_expenses.max_price as max_price, 
			master_region.region_code as code_region,
			master_position_routing.id_routing,
			master_grade_expenses.id_position_routing
		FROM master_grade_expenses
		LEFT JOIN master_job_grade ON master_job_grade.id_job_grade=master_grade_expenses.id_job_grade
		LEFT JOIN inventory.master_product ON inventory.master_product.id_product=master_grade_expenses.id_product AND inventory.master_product.status = 'A'
		LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=master_product.id_product_categories
		LEFT JOIN master_company ON master_company.id_company=master_grade_expenses.id_company
		LEFT JOIN master_position_routing ON master_position_routing.id_job_grade=master_job_grade.id_job_grade
		LEFT JOIN master_position_detail ON master_position_detail.id_position_routing=master_position_routing.id_routing
		LEFT JOIN hr_employee ON (hr_employee.id_employee=master_position_detail.id_employee OR hr_employee.id_employee=master_position_detail.id_employee2)
		LEFT JOIN master_region ON master_region.id_region=master_grade_expenses.id_region
		WHERE hr_employee.id_user='$id_user' AND inventory.master_product.code='$code_product' AND master_position_detail.id_company=$id_company AND master_grade_expenses.status='A' ORDER BY master_region.description ASC
		";
		$data = collect(DB::select($data));
		$idRouting = count($data) > 0 ? $data[0]->id_routing : null;
		
		$returnData = [];
		$returnData[] = $data->where("id_position_routing", $idRouting);
		$returnData[] = $data->where("id_position_routing", null);
		
		foreach($returnData[0] as $k => $dataWithRouting) {
			foreach($returnData[1] as $key => $dataWithoutRouting) {
				if(@$dataWithRouting->code_region == @$dataWithoutRouting->code_region) {
					unset($returnData[1][$key]);
				}
			}
		}
		$returnData = collect(array_merge($returnData[0]->toArray(), $returnData[1]->toArray()))->sortBy('text')->toArray();
		$ret = [];
		foreach($returnData as $r) {
			$ret[] = $r;
		}
		return $ret;
	}
	// cashadvance
	public static function get_branch_destination($request, $idUser = null, $idCompany = null)
	{
		$id_user = $idUser ?? session('id_user');
		$id_company = $idCompany ?? session('id_company');
		$id_region = $request->id_region;
		$code_product = $request->code_product;
		if (!empty($request->id_official_travel)) {
			$cek_status_travel = self::get_edit($request->id_official_travel);
			$status_approval = $cek_status_travel['data'];
			if ($status_approval[0]->status_approval == 'New' OR $status_approval[0]->status_approval == 'Revised') {
				$filter_status = " AND master_grade_expenses.status='A'";
			}else{
				$filter_status = "";
			}
		}else{
			$filter_status = " AND master_grade_expenses.status='A'";
		}
		$data = "SELECT 
			DISTINCT master_grade_expenses.id_grade_expense as id, 
			master_grade_expenses.description as text, 
			master_grade_expenses.max_price as max_price,
			master_position_routing.id_routing, 
			master_grade_expenses.id_position_routing 
		FROM master_grade_expenses
		LEFT JOIN master_job_grade ON master_job_grade.id_job_grade=master_grade_expenses.id_job_grade
		LEFT JOIN inventory.master_product ON inventory.master_product.id_product=master_grade_expenses.id_product AND inventory.master_product.status = 'A'
		LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=master_product.id_product_categories
		LEFT JOIN master_company ON master_company.id_company=master_grade_expenses.id_company
		LEFT JOIN master_position_routing ON master_position_routing.id_job_grade=master_job_grade.id_job_grade
		LEFT JOIN master_position_detail ON master_position_detail.id_position_routing=master_position_routing.id_routing
		LEFT JOIN hr_employee ON (hr_employee.id_employee=master_position_detail.id_employee OR hr_employee.id_employee=master_position_detail.id_employee2)
		LEFT JOIN master_region ON master_region.id_region=master_grade_expenses.id_region
		WHERE hr_employee.id_user='$id_user' AND master_position_detail.id_company=$id_company AND inventory.master_product.code='$code_product' $filter_status AND master_grade_expenses.id_region='$id_region' ORDER BY master_grade_expenses.description ASC
		";
		$data = collect(DB::select($data));
		$idRouting = count($data) > 0 ? $data[0]->id_routing : null;
		
		$returnData = [];
		$returnData[] = $data->where("id_position_routing", $idRouting);
		$returnData[] = $data->where("id_position_routing", null);
		
		foreach($returnData[0] as $k => $dataWithRouting) {
			foreach($returnData[1] as $key => $dataWithoutRouting) {
				if(@$dataWithRouting->code_region == @$dataWithoutRouting->code_region) {
					unset($returnData[1][$key]);
				}
			}
		}
		$returnData = collect(array_merge($returnData[0]->toArray(), $returnData[1]->toArray()))->sortBy('text')->toArray();
		$ret = [];
		foreach($returnData as $r) {
			$ret[] = $r;
		}
		return $ret;
	}
	public static function get_edit($id_official_travel, $summary = false)
	{
		$id_company = session('id_company');
		$data = "SELECT 
		hr_official_travel.id_official_travel as id_official_travel, 
		hr_official_travel.reference_number as reference_number, 
		hr_official_travel.letter_date as letter_date, 
		hr_employee.name as name, 
		master_position_detail.description as position_detail, 
		hr_official_travel.id_approval_status as id_approval_status,
		hr_official_travel.start_date as start_date, 
		hr_official_travel.end_date as end_date, 
		hr_official_travel.location_from as location_from, 
		hr_official_travel.location_to as location_to, 
		master_general_data.description as category, 
		hr_official_travel.id_reason_group as id_reason_group, 
		hr_official_travel.reason_notes as reason_notes, 
		hr_official_travel.unlock_gps as unlock_gps, 
		hr_official_travel.is_have_cash_advance as is_have_cash_advance, 
		hr_official_travel.id_position_detail as id_position_detail, 
		approval_status.code as status_approval, approval_status.description as status_approval_dec, hr_approval_transaction.id_approval_transaction as id_approval_transaction,
		hr_cash_advance.id_cash_advance,
		approval_request.name as name_approval_request,
		hr_official_travel.id_approval_request as id_approval_request
		FROM hr_official_travel
		LEFT JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
		LEFT JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
		LEFT JOIN master_company ON master_company.id_company=hr_official_travel.id_company
		LEFT JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group
		LEFT JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing
		LEFT JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
		LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
		LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
		LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
		LEFT JOIN hr_cash_advance ON hr_official_travel.id_official_travel = hr_cash_advance.id_official_travel
		WHERE hr_official_travel.id_company = '$id_company' AND hr_official_travel.id_official_travel='$id_official_travel' AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_source_transaction='$id_official_travel'
		";
		$data = DB::select($data);
		// $data = self::get_data($id_official_travel);
		$footer_format = DB::table('hr_cash_advance')->leftJoin('hr_official_travel','hr_official_travel.id_official_travel','=','hr_cash_advance.id_official_travel')
		->leftJoin('hr_expense_request','hr_expense_request.id_cash_advance','=','hr_cash_advance.id_cash_advance')
		->leftJoin('inventory.master_product','inventory.master_product.id_product','=','hr_expense_request.id_product')
		->leftJoin('inventory.master_product_categories','inventory.master_product_categories.id_product_categories','=','inventory.master_product.id_product_categories')
		->leftJoin('hr_approval_transaction','hr_approval_transaction.id_source_transaction','=','hr_official_travel.id_official_travel')
		->select(
			\DB::RAW('hr_expense_request.id_expense_request as id_expense_request'),
			\DB::RAW('hr_expense_request.id_product as id_product'),
			\DB::RAW('hr_expense_request.notes as notes'),
			\DB::RAW('hr_expense_request.description as description'),
			\DB::RAW('hr_expense_request.id_branch as branch'),
			\DB::RAW('hr_expense_request.unit_price as unit_price'),
			\DB::RAW('hr_expense_request.total_amount as total_amount'),
			\DB::RAW('hr_expense_request.qty as qty'),
			\DB::RAW('hr_cash_advance.id_cash_advance as id_cash_advance'),
			\DB::RAW('hr_cash_advance.id_currency_cash_advance'),
			\DB::RAW('hr_cash_advance.currency_rate_cash_advance'),
			\DB::RAW('inventory.master_product_categories.description as dec_category'),
			\DB::RAW('hr_expense_request.status as expense_status'),
			\DB::RAW('inventory.master_product.status as inventory_product_status')
		)
		->where('hr_official_travel.id_company',session('id_company'))
		->where('hr_official_travel.id_official_travel',$id_official_travel)
		->where('hr_approval_transaction.source_transaction_type','Official_Travel')
		->where('hr_approval_transaction.id_source_transaction',$id_official_travel)
		->where('hr_approval_transaction.id_company',session('id_company'))
		->whereRaw('hr_approval_transaction.id_employee_approval = hr_official_travel.id_approval_request');
		// ->where('hr_expense_request.status','A')
		// ->where('inventory.master_product.status','A');
		$cashadvance = clone $footer_format;
		$cashadvance = $cashadvance->where('inventory.master_product_categories.description', 'Consumable')->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		$akomodasi = clone $footer_format;
		$akomodasi = $akomodasi->where('inventory.master_product_categories.description','Accomodation')->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		$transport = clone $footer_format;
		$transport = $transport->where('inventory.master_product_categories.description','Transportation')->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		return ['data'=>$data,'transport'=>$transport,'cashadvance'=>$cashadvance,'akomodasi'=>$akomodasi];
	}
	public static function get_user_view($idOfficialTravel)
	{
		$id_company = session('id_company');
		$sql = "SELECT
		master_position_routing.description as dec_position, master_position_routing.id_routing as id_routing, master_position_detail.id_position_detail,
		master_region.description as dec_region, master_region.id_region as id_region,
		master_branch.description as dec_branch, master_branch.id_branch as id_branch,
		STRING_AGG(master_principal.description,', ') as dec_division, 
		--master_principal.id_principal as id_division,
		hr_employee.id_employee as id_employee, hr_employee.name as name,
		master_department.id_dept as id_dept, master_department.department_code as department_code,
		master_job_grade.id_job_grade as id_job_grade, master_job_grade.description as dec_job_grade, master_job_grade.job_class_group as job_class_group
		FROM hr_employee
		JOIN hr_official_travel hot
		ON hr_employee.id_employee = hot.request_by
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
		WHERE hr_employee.id_company= ".$id_company." AND hot.id_official_travel = ".$idOfficialTravel."
		GROUP BY master_position_routing.description, master_position_routing.id_routing, master_position_detail.id_position_detail,
		master_region.description, master_region.id_region, master_branch.description , master_branch.id_branch,
		hr_employee.id_employee, hr_employee.name, master_department.id_dept, master_department.department_code, 
		master_job_grade.id_job_grade, master_job_grade.job_class_group		";
		$result = DB::select($sql);
		return $result;
	}
	
	public static function get_user()
	{
		$id_user = session('id_user');
		$id_company = session('id_company');
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
		WHERE hr_employee.id_user = ".$id_user." AND hr_employee.id_company= ".$id_company."
		GROUP BY master_position_routing.description, master_position_routing.id_routing, master_position_detail.id_position_detail,
		master_region.description, master_region.id_region, master_branch.description , master_branch.id_branch,
	--	master_principal.id_principal, 
		hr_employee.id_employee, hr_employee.name, master_department.id_dept, master_department.department_code, 
		master_job_grade.id_job_grade, master_job_grade.job_class_group";
		$user = DB::select($user);
		return $user;
	}
	
	public static function get_travel_type()
	{
		$id_company = session('id_company');
		$data = "SELECT mgd.id_general_data as id_general_data, mgd.description as description, mgd.code as code_travel 
		FROM master_general_data as mgd
		JOIN master_general_type as mgt 
		ON mgt.id_general_type=mgd.id_general_type
		WHERE mgd.id_general_type='35' AND mgt.general_type='master_group_bussiness_trip' 
		AND mgd.id_company='$id_company' AND mgd.status='A'";
		$data = DB::select($data);
		return $data;
	}
	public static function get_approval_status() {
		$sql = "SELECT 
		id_general_data id,
		description text,
		code
		FROM  master_general_data where status = 'A' and id_general_type = 7 and id_company =" . session('id_company');
		$result = DB::select($sql);
		return $result;
	}
	public static function get_approval_by($request)
	{
		$autoApprove = ["20130401SP"];
		// $nik = DB::selectOne("SELECT * FROM hr_employee WHERE id_user = ?", [session('id_user')]);;

		$job_grade = self::getJobGrade(session('id_user'), 1);
		$sql_approval_hirarki = "SELECT hah.id_approval id, hah.description text
		FROM master_general_data mgd
		JOIN hr_approval_header hah
		ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = '".session('id_company')."'
		LEFT JOIN master_general_type mgt
		ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
		LEFT JOIN master_job_grade mjg
		ON hah.id_job_grade = mjg.id_job_grade AND mjg.description = 'Director'
		WHERE mgd.id_company = '".session('id_company')."' AND mgd.code='Official_Travel'";
		if($job_grade && $job_grade->job_level == 1) {
			$sql_approval_hirarki .= " AND hah.is_auto_approved = true";
		}
		$sql_approval_hirarki = DB::select($sql_approval_hirarki);
		if(!$job_grade || $job_grade->job_level != 1) {
			$exceptionUser = self::getDirector("he.id_employee id, he.name text, mu.id_user as id_user", session('id_user'), null, $autoApprove);
			if($exceptionUser) {
				$sql_approval_by = [$exceptionUser];
			} else {
				$sql_approval_by = "SELECT he2.id_employee id, he2.name text FROM (
					SELECT (
					SELECT hah.id_approval 
					FROM master_general_data mgd
					JOIN hr_approval_header hah
					ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = '".session('id_company')."'
					LEFT JOIN master_general_type mgt
					ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
					WHERE mgd.id_company = '".session('id_company')."' AND mgd.code='Official_Travel'
					), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
					FROM sp_funct_approval_organization_hierarchy_view (? ,'".session('id_company')."',null,Array['gm-level','manager-level']) sfao
					left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval
					LIMIT 1
					) as hirar
					join hr_employee he2 
					on hirar.id_employee_approval=he2.id_employee";
					$sql_approval_by = DB::select($sql_approval_by, [$request->id_employee]);
	
				// if(in_array($nik->nik_employee, ['2019081233LU', '20130401SP', '20130401VK'])) {
				// 	$sql_approval_by = DB::select("SELECT he.id_employee id, he.name text, mu.id_user as id_user from hr_employee he 
				// 	join master_position_detail mpd on he.id_employee = mpd.id_employee and mpd.status = 'A'
				// 	join master_position_routing mpr on mpd.id_position_routing = mpr.id_routing and mpr.status = 'A'
				// 	join master_job_grade mjg on mpr.id_job_grade = mjg.id_job_grade 
				// 	join master_users mu on he.id_user = mu.id_user 
				// 	where mjg.id_company = ? and he.id_company = ? and mu.id_user = ?", [session('id_company'), session('id_company'), session('id_user')]);
				// }
			}
			
			
		} else {
			$sql_approval_by = [self::getDirector("he.id_employee id, he.name text, mu.id_user as id_user", session('id_user'), null, $autoApprove)];
		}
		return ['sql_approval_hirarki'=>$sql_approval_hirarki,'sql_approval_by'=>$sql_approval_by, 'job_grade'=>$job_grade];
	}
	public static function format_save_to_transaction($request)
	{
		$sql = "SELECT mgd2.code, han.* from (
		SELECT (
		SELECT hah.id_approval 
		FROM master_general_data mgd
		JOIN hr_approval_header hah
		ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = '".session('id_company')."'
		LEFT JOIN master_general_type mgt
		ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
		WHERE mgd.id_company = '".session('id_company')."' AND mgd.code='Official_Travel' AND hah.status = 'A' AND hah.id_job_grade IS NULL 
		AND hah.is_auto_approved = false
		), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
		FROM sp_funct_approval_organization_hierarchy_view (?,'".session('id_company')."',null,Array['gm-level','manager-level']) sfao
		left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval 
	--	LIMIT 1
		) as han 
		left join hr_approval_header hah2 on hah2.id_approval=han.id_approval 
		left join master_general_data mgd2 on mgd2.id_general_data=hah2.id_approval_doc_type and mgd2.id_company = '".session('id_company')."'";
		$sql = DB::select($sql, [$request->request_by]);
		return $sql;
	}
	public static function get_product_transport()
	{
		$id_company = session('id_company');
		$data = "SELECT master_product.description as text, master_product.id_product as id, master_product.code as code
		FROM inventory.master_product LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories 
		-- LEFT JOIN master_unit_measures ON master_unit_measures.id_uom=master_product.id_uom
		WHERE inventory.master_product_categories.description='Transportation' AND inventory.master_product.status='A' AND inventory.master_product_categories.status='A' AND inventory.master_product.id_company='$id_company' ORDER BY inventory.master_product.description ASC
		";
		$data = DB::select($data);
		return $data;
	}
	public static function changeTransportType($request)
	{
		$code = $request->code;
		$sql = "SELECT unnest(variant) as variant
		from inventory.master_product
		where code = '$code' AND id_company='".session('id_company')."' AND inventory.master_product.status = 'A'
		ORDER BY variant ASC";
		$sql = DB::select($sql);
		// dd($sql);
		return $sql;
	}
	public static function get_product_akomodasi()
	{
		$id_company = session('id_company');
		$data = "SELECT inventory.master_product.description as text, inventory.master_product.id_product as id
		FROM inventory.master_product LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories
		-- LEFT JOIN master_unit_measures ON master_unit_measures.id_uom=inventory.master_product.id_uom
		WHERE inventory.master_product_categories.description='Accomodation' AND inventory.master_product.status='A' AND inventory.master_product_categories.status='A' AND inventory.master_product.id_company='$id_company' ORDER BY inventory.master_product.description ASC
		";
		$data = DB::select($data);
		return $data;
	}
	public static function get_product_cashadvance()
	{
		$id_company = session('id_company');
		$data = "SELECT inventory.master_product.description as text, inventory.master_product.code as code_product, inventory.master_product.id_product as id, inventory.master_product.long_description as long_description
		FROM inventory.master_product LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=master_product.id_product_categories
		-- LEFT JOIN inventory.master_unit_measures ON inventory.master_unit_measures.id_uom=master_product.id_uom
		WHERE inventory.master_product_categories.description='Consumable' AND inventory.master_product.status='A' AND inventory.master_product_categories.status='A' AND inventory.master_product.id_company='$id_company' ORDER BY inventory.master_product.description ASC
		";
		$data = DB::select($data);
		return $data;
	}
	public static function get_branch_transport()
	{
		$data = "SELECT id_branch as id, description as text 
				FROM master_branch WHERE status='A' AND id_company='".session('id_company')."' ORDER BY description ASC";
		$data = DB::select($data);
		return $data;
	}
	public static function format_save($request)
	{
	//	dd($request->all());
		$dateRange = $request->start_end;
		list($start_date, $end_date) = explode(" to ", $dateRange);
		$company = DB::table('master_company')
		->select(
			\DB::RAW('id_company'),
			\DB::RAW('company_code')
		)
		->where('id_company',session('id_company'))
		->first();
		$bulan = date('m', strtotime(date('Y-m-d')));
		$tahun = date('Y', strtotime(date('Y-m-d')));
		$department = DB::table('master_department')
		->select(
			\DB::RAW('department_code')
		)
		->where('id_company',session('id_company'))
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
		->where('hr_official_travel.id_company',session('id_company'))
		->where('master_position_detail.id_company',session('id_company'))
	//	->where('hr_official_travel.request_by',$request->request_by)
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
		if ($request->clickedButton == 'draft') {
			$id_approval_status_transaction = $request->id_approval_status;
		}else{
			$sql = "SELECT 
			id_general_data id,
			description text,
			code
			FROM  master_general_data where status = 'A' AND code='Request_Approval' and id_general_type = 7 and id_company =" . session('id_company');
			$result = DB::select($sql);
			$id_approval_status_transaction = $result[0]->id;
		//	self::send_mail($request);
		}
		$format = $kode."/".$company->company_code."-".'PDN/'.$code_region."-".$department_code."/".$bulan."/".substr($tahun,-2);
		return ['start_date'=>$start_date,'end_date'=>$end_date,'format'=>$format,'id_approval_status_transaction'=>$id_approval_status_transaction];
	}
	public static function cek_save_product($data, $valuec)
	{
		// 
		$result = DB::table('hr_expense_request')
		->leftJoin('hr_cash_advance','hr_cash_advance.id_cash_advance','=','hr_expense_request.id_cash_advance')
		->leftJoin('hr_official_travel','hr_official_travel.id_official_travel','=','hr_cash_advance.id_official_travel')
		->leftJoin('inventory.master_product','inventory.master_product.id_product','=','hr_expense_request.id_product')
		->leftJoin('inventory.master_product_categories','inventory.master_product_categories.id_product_categories','=','inventory.master_product.id_product_categories')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_official_travel.request_by')
		->where('hr_employee.id_company',session('id_company'))
		->where('hr_employee.id_user',session('id_user'))
		->where('hr_official_travel.id_official_travel',$data->id_official_travel)
		->where('hr_expense_request.id_product',$valuec['nama'])
		->where('inventory.master_product','A')
		->first();
		return $result;
	}
	public static function cek_edit_product($request, $valuec)
	{
		// 
		$data = DB::table('hr_expense_request')
		->leftJoin('hr_cash_advance','hr_cash_advance.id_cash_advance','=','hr_expense_request.id_cash_advance')
		->leftJoin('hr_official_travel','hr_official_travel.id_official_travel','=','hr_cash_advance.id_official_travel')
		->leftJoin('inventory.master_product','inventory.master_product.id_product','=','hr_expense_request.id_product')
		->leftJoin('inventory.master_product_categories','inventory.master_product_categories.id_product_categories','=','inventory.master_product.id_product_categories')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_official_travel.request_by')
		->where('hr_employee.id_company',session('id_company'))
		->where('hr_employee.id_user',session('id_user'))
		->where('inventory.master_product','A');
		$new = clone $data;
		$new = $new->where('hr_official_travel.id_official_travel',$request->id_official_travel)->first();
		// ->where('hr_expense_request.id_product',$valuec['nama'])
		$edit = clone $data;
		$edit = $edit->where('hr_official_travel.id_official_travel',$request->id_official_travel)
		->where('hr_expense_request.id_expense_request',$valuec['id_cash_advance'])->first();
		return ['new'=>$new,'edit'=>$edit];
	}
	public function action_status_approval($request,$travelStatus)
	{
		if ($request->type_action == 'Cancel' && $travelStatus == 'Onschedule') {
			$status = "SELECT 
			id_general_data as id_general_data
			-- description text
			FROM master_general_data where status = 'A' AND code='Cancel' and id_company =" . session('id_company');
		}
		else if ($request->type_action == 'Cancel' && $travelStatus == 'Cancel') {
			$status = "SELECT 
			id_general_data as id_general_data
			-- description text
			FROM master_general_data where status = 'A' AND code='Approved' and id_company =" . session('id_company');
		}else{
			$status = "SELECT 
			id_general_data as id_general_data
			-- description text,
			FROM master_general_data where status = 'A' AND code='Request_Approval' and id_company =" . session('id_company');
		}
		$result = DB::select($status);
		$status_approval = $result[0]->id_general_data;
		return $status_approval;
	}
	public static function get_print($id_official_travel)
	{
		$id_company = session('id_company');
		$data = "SELECT
		hr_employee.name as name, hr_employee.nik_employee as nik_employee, hr_official_travel.reference_number as reference_number, 
		hr_official_travel.letter_date as letter_date, STRING_AGG(master_principal.description,', ') as dec_principal, 
		master_department.description as dec_dept, master_position_routing.description as dec_position, 
		hr_official_travel.start_date as start_date, hr_official_travel.end_date as end_date, hr_official_travel.location_to as location_to, 
		CONCAT(hr_bank_employee.bank_name,' / ',hr_bank_employee.bank_account) as bank_name, hr_bank_employee.account_name as account_name, 
		approval_request.name as name_approval_request, approval_request.nik_employee as nik_employee_approval, hr_employee.id_employee as id_employee, hr_approval_transaction.update_date as update_date, 
		hr_official_travel.is_have_cash_advance as is_have_cash_advance, hr_official_travel.reason_notes as reason_notes
		FROM hr_official_travel
		LEFT JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
		LEFT JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing
		LEFT JOIN master_branch on master_branch.id_branch = master_position_detail.id_branch
		LEFT JOIN master_region on master_region.id_region = master_branch.id_region
		LEFT JOIN relation_positiondetail_principal on relation_positiondetail_principal.id_position_detail = master_position_detail.id_position_detail
		LEFT JOIN master_principal on master_principal.id_principal = relation_positiondetail_principal.id_principal
		LEFT JOIN master_division on master_division.id_division = master_principal.id_division
		LEFT JOIN master_job_position ON master_job_position.id_position=master_position_routing.id_position
		LEFT JOIN master_department ON master_department.id_dept=master_job_position.id_dept
		LEFT JOIN master_job_grade ON master_job_grade.id_job_grade=master_position_routing.id_job_grade
		LEFT JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
		LEFT JOIN master_company ON master_company.id_company=hr_official_travel.id_company
		LEFT JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group
		LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
		LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
		LEFT JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
		LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
		LEFT JOIN hr_bank_employee ON hr_bank_employee.id_employee=hr_employee.id_employee AND hr_bank_employee.default_bank = true
		LEFT JOIN master_bank ON master_bank.id_bank=hr_bank_employee.id_bank
		WHERE hr_official_travel.id_company = ".$id_company." AND hr_official_travel.id_official_travel= ".$id_official_travel."
		AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_source_transaction= ".$id_official_travel."
		AND hr_approval_transaction.id_company= ".$id_company."
		GROUP BY hr_employee.name, hr_employee.nik_employee, hr_official_travel.reference_number, 
		hr_official_travel.letter_date, master_department.description, master_position_routing.description, 
		hr_official_travel.start_date, hr_official_travel.end_date, hr_official_travel.location_to,
		hr_bank_employee.bank_name, hr_bank_employee.account_name, hr_bank_employee.bank_account, 
		approval_request.name, hr_employee.id_employee, hr_approval_transaction.update_date, 
		hr_official_travel.is_have_cash_advance, hr_official_travel.reason_notes, approval_request.nik_employee";
		$data = DB::select($data);
		$footer_format = DB::table('hr_cash_advance')->leftJoin('hr_official_travel','hr_official_travel.id_official_travel','=','hr_cash_advance.id_official_travel')
		->leftJoin('hr_expense_request','hr_expense_request.id_cash_advance','=','hr_cash_advance.id_cash_advance')
		->leftJoin('inventory.master_product','inventory.master_product.id_product','=','hr_expense_request.id_product')
		->leftJoin('inventory.master_product_categories','inventory.master_product_categories.id_product_categories','=','inventory.master_product.id_product_categories')
		->leftJoin('hr_approval_transaction','hr_approval_transaction.id_source_transaction','=','hr_official_travel.id_official_travel')
		->leftJoin('master_branch AS mb','hr_expense_request.id_branch','=','mb.id_branch')
		// ->leftJoin('master_unit_measures','master_unit_measures.id_uom','=','hr_expense_request.id_uom')
		->select(
			\DB::RAW('hr_expense_request.id_expense_request as id_expense_request'),
			\DB::RAW('hr_expense_request.id_product as id_product'),
			\DB::RAW('hr_expense_request.notes as notes'),
			\DB::RAW('hr_expense_request.description as description'),
			\DB::RAW('hr_expense_request.unit_price as unit_price'),
			\DB::RAW('hr_expense_request.qty as qty'),
			\DB::RAW('hr_expense_request.total_amount as total_amount'),
			// \DB::RAW('master_unit_measures.description as dec_uom'),
			\DB::RAW('hr_cash_advance.id_cash_advance as id_cash_advance'),
			\DB::RAW('inventory.master_product_categories.description as dec_category'),
			\DB::RAW('inventory.master_product.description as dec_product'),
			\DB::RAW('mb.description as branch_desc')
		)
		->where('hr_official_travel.id_company',session('id_company'))
		->where('hr_official_travel.id_official_travel',$id_official_travel)
		->where('hr_approval_transaction.source_transaction_type','Official_Travel')
		->where('hr_approval_transaction.id_source_transaction',$id_official_travel)
		->where('hr_approval_transaction.id_company',session('id_company'))
		->whereRaw('hr_approval_transaction.id_employee_approval = hr_official_travel.id_approval_request')
		->where('hr_expense_request.status','A');
		$cashadvance = clone $footer_format;
		$cashadvance = $cashadvance->where('inventory.master_product_categories.code', 'CSM')->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		$akomodasi = clone $footer_format;
		$akomodasi = $akomodasi->where('inventory.master_product_categories.code','ACD')->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		$transport = clone $footer_format;
		$transport = $transport->where('inventory.master_product_categories.code','TST')->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		$end_date = substr($data[0]->end_date, 0, 10);
		// $tanggal_approved = $data[0]->end_date;
		$config_days = "SELECT maximum_reimburse_expense as mre, maximum_cash_advance_request as mor FROM hr_config_settings WHERE id_company='".session('id_company')."' AND status='A'";
		$config_days = DB::select($config_days);
		if (!empty($config_days)) {
			if ($data[0]->is_have_cash_advance == true) {
				$day_plus = $config_days[0]->mre;
			}else{
				$day_plus = $config_days[0]->mor;
			}
		}
		// $add_days = date('Y-m-d', strtotime($tanggal_approved. $day_plus));
		$id_employee = $data[0]->id_employee;
		$last_days = "SELECT hwd.current_dates AS last_date
		FROM hr_work_days hwd
		WHERE hwd.id_employee = '$id_employee' 
		AND hwd.id_company = '".session('id_company')."' 
		AND hwd.day_type = 'WD' 
		AND hwd.current_dates > '$end_date'::date ORDER BY hwd.current_dates ASC
		";
		$last_days = DB::select($last_days);
		return ['data'=>$data,'transport'=>$transport,'cashadvance'=>$cashadvance,'akomodasi'=>$akomodasi,'last_days'=>$last_days,'day_plus'=>$day_plus];
		// 15 hai kerja
	}
	public static function tgl_indo($data){
		$bulan = array (
			1 =>   'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$pecahkan = explode('-', $data);
		return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
	}
	public function send_mail($request)
	{
		$id_company = session('id_company');
		$filter = "";
		if (empty($request->id_official_travel)) {
			// save new
			$id_employee = $request->request_by;
			$filter = " AND hr_official_travel.request_by='$id_employee'";
		}else{
			$id_official_travel = $request->id_official_travel;
			$filter = " AND hr_official_travel.id_official_travel='$id_official_travel'";
		}
		$data = "SELECT
		hr_official_travel.reference_number as reference_number, hr_official_travel.letter_date as letter_date, hr_employee.name as name, hr_employee.nik_employee, master_position_routing.description as dec_position, hr_official_travel.start_date as start_date, hr_official_travel.end_date as end_date, hr_official_travel.reason_notes as reason_notes, hr_official_travel.location_to as location_to, approval_request.name as name_approval_request, approval_request.private_mail as private_mail, hr_official_travel.travel_status, approval_request.id_employee as id_employee_approval, approval_request.mobile_phone as mobile_phone, master_general_data.description as type_travel
		FROM hr_official_travel
		LEFT JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
		LEFT JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing

		LEFT JOIN master_branch on master_branch.id_branch = master_position_detail.id_branch
		LEFT JOIN master_region on master_region.id_region = master_branch.id_region
		LEFT JOIN relation_positiondetail_principal on relation_positiondetail_principal.id_position_detail = master_position_detail.id_position_detail
		LEFT JOIN master_principal on master_principal.id_principal = relation_positiondetail_principal.id_principal
		LEFT JOIN master_division on master_division.id_division = master_principal.id_division
		LEFT JOIN master_job_position ON master_job_position.id_position=master_position_routing.id_position
		LEFT JOIN master_department ON master_department.id_dept=master_job_position.id_dept
		LEFT JOIN master_job_grade ON master_job_grade.id_job_grade=master_position_routing.id_job_grade

		LEFT JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
		LEFT JOIN master_company ON master_company.id_company=hr_official_travel.id_company
		LEFT JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group
		LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
		LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
		LEFT JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
		LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
		LEFT JOIN hr_bank_employee ON hr_bank_employee.id_employee=hr_employee.id_employee
		LEFT JOIN master_bank ON master_bank.id_bank=hr_bank_employee.id_bank
		WHERE hr_official_travel.id_company = '$id_company' AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_official_travel.id_approval_request = hr_approval_transaction.id_employee_approval AND hr_approval_transaction.id_company='$id_company' $filter 
		";
		$data = DB::select($data);	
		if (!empty($data)) {
			$start_date = substr($data[0]->start_date, 0,10);
			$end_date = substr($data[0]->end_date, 0,10);
			$letter_date = substr($data[0]->letter_date, 0,10);
			if($data[0]->travel_status == 'Onschedule'){
				$travel_status = 'On Schedule';
			}
			else if($data[0]->travel_status == 'Cancel'){
				$travel_status = 'Request Cancel';
			}
			else{
				$travel_status = $data[0]->travel_status;
			}
			$param = [
				'type_send' => 'Official_Travel',
				'view_file' => 'cash_advance.official_travel.mail',
				'content_title' => $data[0]->name_approval_request,
				'reference_number' => $data[0]->reference_number,
				'location_to' => $data[0]->location_to,
				'reason_notes' => $data[0]->reason_notes,
				'start_date' => self::tgl_indo($start_date),
				'end_date' => self::tgl_indo($end_date),
				'name_approval_request' => $data[0]->name_approval_request,
				'id_employee_approval' => $data[0]->id_employee_approval,
				'letter_date' => self::tgl_indo($letter_date),
				'name' => $data[0]->name,
				'nik_employee' => $data[0]->nik_employee,
				'dec_position' => $data[0]->dec_position,
				'jenis_permohonan'=> $data[0]->type_travel,
				'content_link' => url('/employee/employee/employee_approval'),
				'mail_subject'=>'[Request ('.$travel_status.') Perjalanan Dinas] HRIS from '.$data[0]->name,
				'travel_status' => $travel_status,
			//	'private_mail' => 'agus.dwi@borwita.co.id',
				'private_mail' => $data[0]->private_mail,
				'mobile_phone' => $data[0]->mobile_phone,
			];
			return $param;
		}
	}
	
	public static function mail_cc($id_company)
	{
		$sql = "SELECT he.id_employee AS id_emp_cc, he.private_mail AS mail_cc
			FROM hr_employee he
			JOIN master_position_detail mpd
			ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
			JOIN (
				SELECT unnest(id_position_detail_email_travel) AS id_position_detail
				FROM hr_config_settings
				WHERE id_company= ".$id_company." 
			) AS list_id
			ON mpd.id_position_detail = list_id.id_position_detail";
		$result = DB::select($sql);
		return $result;
	}
	
	public static function no_with_del($code,$id_cashadvance)
	{
		$sql = "SELECT her.id_expense_request::text
				FROM hr_expense_request her
				LEFT JOIN inventory.master_product mp
				ON her.id_product = mp.id_product
				JOIN inventory.master_product_categories mpc
				ON mp.id_product_categories = mpc.id_product_categories 
				WHERE her.id_company = ".session('id_company')." AND 
				mpc.code = '".$code."' AND her.id_cash_advance = ".$id_cashadvance;
		$result = DB::select($sql);
		// dd($sql);
		return $result;
	}
	
	public static function get_uom($idProduct, $idCompany = null)
	{
		$idCompany = $idCompany ?? session('id_company');
		$sql = "SELECT mp.id_uom
				FROM inventory.master_product mp 
				WHERE mp.id_company = ".$idCompany." AND mp.id_product = ".$idProduct;
		$result = DB::select($sql)[0];
		// dd($sql);
		return $result;
	}
	
	public static function get_trans_view($data)
	{
		$idTravel = $data['id_official_travel'];
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
				WHERE mpc.code = 'TST' AND hot.id_company = ".session('id_company')." AND hot.id_official_travel = ".$idTravel." AND her.status = 'A'
				ORDER BY her.description ASC";
		$result = DB::select($sql);
		return $result;
	}
	
	public static function get_accomodation_view($data)
	{
		$idTravel = $data['id_official_travel'];
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
				WHERE mpc.code = 'ACD' AND hot.id_company = ".session('id_company')." AND hot.id_official_travel = ".$idTravel." AND her.status = 'A'
				ORDER BY her.description ASC";
		$result = DB::select($sql);
		return $result;
	}
	
	public static function get_cash_view($data)
	{
		$idTravel = $data['id_official_travel'];
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
				WHERE mpc.code = 'CSM' AND hot.id_company = ".session('id_company')." AND hot.id_official_travel = ".$idTravel." AND her.status = 'A'
				ORDER BY her.description ASC";
		$result = DB::select($sql);
		return $result;
	}

	public static function getOfficialTravelWithoutCashAdvance($requestBy = null) {
		$bindings = [];
		$where = "";
		$requestByQuery = "";
		if($requestBy) {
			$requestByQuery = "JOIN hr_employee he ON hot.request_by = he.id_employee AND he.id_user IN(?)";
			array_push($bindings, $requestBy);
		}

		$sql = "SELECT
			hot.id_official_travel as id,
			CONCAT(hot.reference_number, ' (', hot.location_to, ')') as text
		FROM hr_official_travel hot
		$requestByQuery
		JOIN master_general_data mgd ON mgd.code  = 'Approved' AND mgd.id_general_data  = hot.id_approval_status AND mgd.id_company = hot.id_company
		WHERE hot.is_have_cash_advance = FALSE AND hot.status = 'A' 
		";
		return DB::select($sql, $bindings);
	}

	public static function getSettlementOfficialTravelData($requestBy = null, $idOfficialTravel = null) {
		$emp = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
		$bindings = [];
		$where = "";
		$requestByQuery = "";
		if($requestBy) {
			$requestByQuery = "JOIN hr_employee he ON hot.request_by = he.id_employee AND (he.id_user IN(?) OR he.nik_employee = '".$emp->nik_employee."')";
			array_push($bindings, $requestBy);
		}
		if($idOfficialTravel) {
			$where .= "AND hot.id_official_travel IN(?)";
			array_push($bindings, $idOfficialTravel);
		}
		$sql = "SELECT 
			hot.*, 
			-- hot.id_official_travel,
			-- hot.reference_number as reference_number,
			-- hot.letter_date,
			-- hot.travel_status,
			-- hot.start_date,
			-- hot.end_date,
			-- hot.location_to,
			-- hot.travel_status,
			mgd.description as desc_app_status, 
			mgd.code as code_app_status, 
			mgd2.description as category, 
			hca.reference_number as cash_advance_reference, 
			hca.transaction_date,
			hca.accounting_date,
			hca.total_expense_request_amount as hca_total_expense_request_amount,
			hca.settlement_status as hca_settlement_status,
			hca.total_base_currency_tax_amount as total_base_currency_tax_amount,
			hca.total_tax_amount as total_tax_amount,
			hca.total_expense_request_amount as total_expense_request_amount,
			hca.total_cash_request_amount as total_cash_request_amount,
			hca.total_travel_request_amount,
			hca.payment_status as payment_status,
			hca.maximum_clearing_date,
			hca.total_base_currency_clearing_amount,
			hca.total_base_currency_difference_amount,
			hca.is_validate as hca_is_validate,
			he2.name as name_approval_request,
			he2.nik_employee as nik_approval_request,
			hca.id_approval_request as id_approval_request,
			hca.id_approval as id_approval,
			hah.description as approval_description,
			mgd3.description as hca_approval_status,
			he.id_employee as id_employee,
			he.name as name_employee,
			he.nik_employee as nik_employee,
			hca.reason_notes as cash_advance_notes,
			hca.id_cash_advance,
			mpd.description as position_detail,
			md.id_dept as id_department,
			md.description as department_description,
			hca.id_currency_cash_advance,
			hca.total_base_currency_payment_amount,
			hca.total_base_currency_settlement_amount,
			hca.start_refund_date,
			hca.is_approved_by_finance,
			hca.is_approved_by_chief,
			hca.attachment_settlement,
			hca.is_validate
			FROM
				hr_official_travel hot
			LEFT JOIN hr_cash_advance hca ON
				hot.id_official_travel = hca.id_official_travel
			JOIN master_general_data mgd ON
				mgd.code = 'Approved'
				AND hot.id_approval_status = mgd.id_general_data
				AND hot.id_company = mgd.id_company
			$requestByQuery
			JOIN master_general_data mgd2 ON
				mgd2.id_general_data = hot.id_reason_group
				AND mgd2.id_company = hot.id_company
			LEFT JOIN master_general_data mgd3 ON
				mgd3.id_general_data = hca.id_approval_status
			LEFT JOIN hr_employee he2 ON
				hca.id_approval_request = he2.id_employee
			LEFT JOIN master_position_detail mpd ON
				he.id_employee = mpd.id_employee
				AND mpd.secondary_position = FALSE
			LEFT JOIN hr_approval_header hah ON
				hca.id_approval = hah.id_approval
			LEFT JOIN master_position_routing mpr ON
				mpd.id_position_routing = mpr.id_routing
			LEFT JOIN master_job_position mjp ON
				mpr.id_position = mjp.id_position
			LEFT JOIN master_department md ON
				mjp.id_dept = md.id_dept
			LEFT JOIN master_currency mc ON
				hca.id_currency_cash_advance = mc.id_currency
			WHERE
				hot.status = 'A' 
		";
		// $sql = "SELECT 
		// 	hot.*, 
		// 	mgd.description as desc_app_status, 
		// 	mgd.code as code_app_status, 
		// 	mgd2.description as category, 
		// 	hca.reference_number as cash_advance_reference, 
		// 	hca.transaction_date,
		// 	hca.accounting_date,
		// 	hca.total_expense_request_amount as hca_total_expense_request_amount,
		// 	hca.settlement_status as hca_settlement_status,
		// 	hca.total_untaxed_amount as total_untaxed_amount,
		// 	hca.total_tax_amount as total_tax_amount,
		// 	hca.total_expense_request_amount as total_expense_request_amount,
		// 	hca.payment_status as payment_status,
		// 	hca.maximum_clearing_date,
		// 	hca.total_clearing_amount,
		// 	he2.name as name_approval_request,
		// 	hca.id_approval_request as id_approval_request,
		// 	mgd3.description as hca_approval_status,
		// 	he.id_employee as id_employee,
		// 	he.name as name_employee,
		// 	hca.reason_notes as cash_advance_notes,
		// 	hca.id_cash_advance,
		// 	mpd.description as position_detail
		// FROM hr_official_travel hot 
		// $requestByQuery
		// JOIN master_general_data mgd ON mgd.code  = 'Approved' AND mgd.id_general_data  = hot.id_approval_status AND mgd.id_company = hot.id_company
		// JOIN master_general_data mgd2 ON mgd2.id_general_data  = hot.id_reason_group AND mgd2.id_company = hot.id_company
		// JOIN hr_cash_advance hca ON hot.id_official_travel = hca.id_official_travel AND hot.id_approval_status = mgd.id_general_data 
		// JOIN master_general_data mgd3 ON mgd3.id_general_data  = hca.id_approval_status
		// JOIN hr_employee he2 ON hca.id_approval_request = he2.id_employee
		// JOIN master_position_detail mpd ON he.id_employee = mpd.id_employee
		// WHERE hot.status = 'A'";
		$sql .= $where;
		$sql .= "ORDER BY hot.creation_date DESC";
		
		// dd($sql);
		// print_r($sql);die();
		return DB::select($sql, $bindings);
	}

	public static function createCashAdvance($idOfficialTravel) {
		$company = DB::selectOne("SELECT * FROM master_company WHERE id_company = ?", [session('id_company')]);
		$officialTravel = HrOfficialTravel::findOrFail($idOfficialTravel);
        $cashAdvance = new HrCashAdvance();
        $cashAdvance->id_official_travel = $idOfficialTravel;
        $cashAdvance->reference_number = $officialTravel->reference_number;
        $cashAdvance->id_employee = $officialTravel->request_by;
        $cashAdvance->transaction_date = $officialTravel->letter_date;
        // $cashAdvance->settlement_status = 'Not_Clear';
        $cashAdvance->id_company = $officialTravel->id_company;
		$cashAdvance->id_currency_cash_advance = $company->id_currency;
		$cashAdvance->currency_rate_cash_advance = 1;
        $cashAdvance->created_by = session('id_user');
        $cashAdvance->status = 'A';
		$cashAdvance->id_approval_request = $officialTravel->id_approval_request;
		$cashAdvance->id_approval_status = $officialTravel->id_approval_status;
		$cashAdvance->id_approval = $officialTravel->id_approval;
		$cashAdvance->reason_notes = $officialTravel->reason_notes;
		$cashAdvance->maximum_clearing_date = Carbon::parse($officialTravel->end_date)->addWeekdays(21)->endOfDay();
        // dd($cashAdvance, $officialTravel);
        if($cashAdvance->save()) {
            // $officialTravel->is_have_cash_advance = true;
            $officialTravel->save();
        }
		return [
			"cashAdvance" => $cashAdvance,
			"officialTravel" => $officialTravel
		];
	}

	public static function getDirector($columns = "he.id_employee id, he.name text, mu.id_user as id_user", $custom_id_user = null, $idCompany = null, $exception = []) {
		$id_company = $idCompany ?? session('id_company');
		$exceptionQuery = "";
		if(count($exception) > 0) {
			$exception = "'".implode("','", $exception)."'";
			$exceptionQuery = "or (he.nik_employee in($exception) and he.id_user = ".session('id_user').")";
		}
		$director = "SELECT ".$columns." from hr_employee he 
		join master_position_detail mpd on he.id_employee = mpd.id_employee and mpd.status = 'A'
		join master_position_routing mpr on mpd.id_position_routing = mpr.id_routing and mpr.status = 'A'
		join master_job_grade mjg on mpr.id_job_grade = mjg.id_job_grade 
		join master_users mu on he.id_user = mu.id_user 
		where mjg.job_level = 1 and mjg.id_company = ? and he.id_company = ?";
		if($custom_id_user) {
			$director .= " and mu.id_user = ".$custom_id_user;
		} 
		$director .= $exceptionQuery;
		return DB::selectOne($director, [$id_company, $id_company]);
	} 

	public static function getDirectorOfficialTravel($columns = "hah.id_approval id, hah.description text, hah.id_job_grade, hah.is_auto_approved", $idCompany = null, $any = false) {
		$idCompany = $idCompany ?? session('id_company');
		if(!$any) {
			return DB::selectOne("SELECT ".$columns." FROM hr_approval_header hah 
			JOIN master_general_data mgd ON hah.id_approval_doc_type = mgd.id_general_data
			-- JOIN hr_approval_detail had ON hah.id_approval = had.id_approval
			JOIN master_job_grade mjg ON hah.id_job_grade = mjg.id_job_grade AND mjg.job_level = 1 AND hah.is_auto_approved = true AND mjg.status = 'A'
			WHERE mgd.code = 'Official_Travel' and hah.id_company = ? AND hah.status = 'A'", [$idCompany]);
		}
		return DB::selectOne("SELECT $columns FROM hr_approval_header hah 
							JOIN master_general_data mgd ON hah.id_approval_doc_type = mgd.id_general_data
							WHERE mgd.code = 'Official_Travel' 
							and hah.id_company = ? AND hah.status = 'A'", [$idCompany]);
	}

	public static function getJobGrade($idUser, $jobLevel) {
		return DB::selectOne("SELECT mjg.id_job_grade, mjg.job_level
		FROM hr_employee he
		JOIN master_position_detail mpd
		ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
		JOIN master_position_routing mpr
		ON mpd.id_position_routing = mpr.id_routing
		JOIN master_job_grade mjg 
		ON mpr.id_job_grade = mjg.id_job_grade
		WHERE he.id_user = ".$idUser." AND he.status = 'A' AND mjg.job_level = ".$jobLevel);
	}

	public static function getCashAdvanceLock($idUser) {
		$sql = "SELECT 
					COUNT(*)
				FROM
					hr_cash_advance hca
				JOIN hr_employee he ON 
					hca.id_employee = he.id_employee
				JOIN hr_official_travel hot ON
					hca.id_official_travel = hot.id_official_travel
				WHERE
					he.id_user = ?
					AND settlement_status = 'Not_Clear'
					AND hca.status = 'A'
					AND he.status = 'A'
					AND maximum_clearing_date >= NOW()
					AND hot.is_have_cash_advance = TRUE";
		$outstanding = DB::selectOne($sql, [$idUser])->count;
		$canSubmit = true;
		if($outstanding >= 2) {
			$canSubmit = false;
		}
		// if($outstanding == 2) {
		// 	$locks = ['Cash_Advance'];
		// } else if($outstanding >= 3) {
		// 	$locks = ['Cash_Advance', 'Official_Travel'];
		// } else $locks = [];
		return $canSubmit;
	}
}
