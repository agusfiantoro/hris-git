<?php

namespace App\Models\CashAdvance\TravelRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TravelRequest extends Model
{
    // use HasFactory;
	public function get_travel_request()
	{
		$id_company = session('id_company');
		$data = "SELECT
		hr_official_travel.reference_number as reference_number, hr_official_travel.id_official_travel,
		hr_employee.name as name, hr_employee.mobile_phone as mobile_phone, approval_status.description as dec_approval_status, hr_official_travel.location_to as location_to, hr_official_travel.start_date as start_date, hr_official_travel.end_date as end_date, hr_official_travel.letter_date as letter_date, hr_official_travel.id_official_travel as id_official_travel,
		hr_official_travel.is_verified as is_verified, master_general_data.description AS travel_type, hr_official_travel.travel_status, master_region.description AS emp_region
		-- master_product_categories.description as dec_product_category
		FROM hr_official_travel
		JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
		JOIN master_branch ON master_position_detail.id_branch = master_branch.id_branch
		JOIN master_region ON master_branch.id_region = master_region.id_region AND master_branch.id_company = master_region.id_company
		JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
		JOIN master_company ON master_company.id_company=hr_official_travel.id_company
		JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group
		JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing
		JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
		LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
		LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
		LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
		LEFT JOIN hr_cash_advance ON hr_cash_advance.id_official_travel=hr_official_travel.id_official_travel
		LEFT JOIN hr_expense_request ON hr_expense_request.id_cash_advance=hr_cash_advance.id_cash_advance
		LEFT JOIN inventory.master_product ON inventory.master_product.id_product=hr_expense_request.id_product
		LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories
		-- LEFT JOIN master_product_categories as akomodasi_category ON akomodasi_category.id_product_categories=master_product.id_product_categories
		WHERE hr_official_travel.id_company = '$id_company' AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_company='$id_company' AND inventory.master_product_categories.code != 'CSM' AND approval_status.code='Approved' AND hr_official_travel.is_verified = 'false' AND hr_official_travel.id_approval_request = hr_approval_transaction.id_employee_approval
		GROUP BY hr_official_travel.reference_number, hr_official_travel.id_official_travel,
		hr_employee.name, approval_status.description, hr_official_travel.location_to, hr_official_travel.start_date, hr_official_travel.end_date, hr_official_travel.letter_date, hr_official_travel.id_official_travel, hr_official_travel.is_verified, master_general_data.description, hr_employee.mobile_phone, master_region.description
		ORDER BY hr_official_travel.id_official_travel DESC
		";
		$data = DB::select($data);
		return $data;
	}
	public static function get_view_detail($id_official_travel)
	{
		$data = DB::table('hr_cash_advance')->leftJoin('hr_official_travel','hr_official_travel.id_official_travel','=','hr_cash_advance.id_official_travel')
		->leftJoin('hr_expense_request','hr_expense_request.id_cash_advance','=','hr_cash_advance.id_cash_advance')
		->leftJoin('inventory.master_product','inventory.master_product.id_product','=','hr_expense_request.id_product')
		->leftJoin('inventory.master_product_categories','inventory.master_product_categories.id_product_categories','=','inventory.master_product.id_product_categories')
		->leftJoin('hr_approval_transaction','hr_approval_transaction.id_source_transaction','=','hr_official_travel.id_official_travel')
		->select(
			\DB::RAW('hr_official_travel.id_official_travel as id_official_travel'),
			\DB::RAW('hr_official_travel.start_date as start_date'),
			\DB::RAW('hr_official_travel.end_date as end_date'),
			\DB::RAW('hr_expense_request.is_verified as is_verified'),
			\DB::RAW('hr_expense_request.id_expense_request as id_expense_request'),
			\DB::RAW('hr_expense_request.id_product as id_product'),
			\DB::RAW('hr_expense_request.notes as notes'),
			\DB::RAW('hr_expense_request.description as description'),
			\DB::RAW('hr_expense_request.id_branch as branch'),
			\DB::RAW('hr_expense_request.unit_price'),
			\DB::RAW('hr_expense_request.total_amount'),
			\DB::RAW('hr_expense_request.status as status'),
			\DB::RAW('hr_cash_advance.id_cash_advance as id_cash_advance'),
			\DB::RAW('inventory.master_product_categories.description as dec_category'),
		)
		->where('hr_official_travel.id_company',session('id_company'))
		->where('hr_official_travel.id_official_travel',$id_official_travel)
		->where('hr_approval_transaction.source_transaction_type','Official_Travel')
		->where('hr_approval_transaction.id_source_transaction',$id_official_travel)
		->whereRaw('hr_approval_transaction.id_employee_approval = hr_official_travel.id_approval_request')
		->where('hr_approval_transaction.id_company',session('id_company'));
		// ->where('hr_expense_request.status','A');
		$transport = clone $data;
		$transport = $transport->where('inventory.master_product_categories.code','TST')
		->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		$akomodasi = clone $data;
		$akomodasi = $akomodasi->where('inventory.master_product_categories.code','ACD')
		->orderBy('hr_expense_request.id_expense_request','ASC')->get();
		return ['transport'=>$transport,'akomodasi'=>$akomodasi];
	}
	public function get_view_header($id_official_travel)
	{
		$id_company = session('id_company');
		$data = "SELECT
		master_general_data.description as dec_category, hr_employee.name as name, master_position_detail.description as dec_position, master_region.description as dec_region, master_branch.description as dec_branch, master_division.description as dec_division, master_job_grade.description as dec_job_grade, hr_official_travel.letter_date as letter_date, hr_official_travel.start_date as start_date, hr_official_travel.end_date as end_date,
		hr_official_travel.unlock_gps as unlock_gps, hr_approval_header.description as dec_approval_hirarki, approval_request.name as name_chief, approval_status.description as dec_approval_status, hr_official_travel.is_have_cash_advance as is_have_cash_advance, hr_official_travel.location_to as location_to, hr_official_travel.reason_notes as reason_notes,
		inventory.master_product_categories.description as dec_product_category
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
		LEFT JOIN hr_cash_advance ON hr_cash_advance.id_official_travel=hr_official_travel.id_official_travel
		LEFT JOIN hr_expense_request ON hr_expense_request.id_cash_advance=hr_cash_advance.id_cash_advance
		LEFT JOIN inventory.master_product ON inventory.master_product.id_product=hr_expense_request.id_product
		LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories
		WHERE hr_official_travel.id_company = '$id_company' AND hr_official_travel.id_official_travel='$id_official_travel' AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_source_transaction='$id_official_travel' AND hr_approval_transaction.id_company='$id_company' AND inventory.master_product_categories.description != 'Consumable' AND hr_official_travel.id_approval_request = hr_approval_transaction.id_employee_approval
		-- transport_category.description = 'Transportation' AND akomodasi_category.description = 'Accomodation'
		GROUP BY inventory.master_product_categories.description, hr_official_travel.id_official_travel, master_general_data.description, hr_employee.name, master_position_detail.description, master_region.description, master_branch.description, master_division.description, hr_official_travel.letter_date, hr_official_travel.start_date, hr_official_travel.end_date, hr_official_travel.unlock_gps, hr_approval_header.description, approval_request.name, approval_status.description, hr_official_travel.is_have_cash_advance, hr_official_travel.location_to, hr_official_travel.reason_notes, master_job_grade.description
		";
		$data = DB::select($data);
		return $data;
	}

	public function get_travel_request_bigen($request)
	{
		$id_company = session('id_company');
		$filter = "";
		if (!empty($request->start_end)) {
			$dateRange = $request->start_end;
			list($start, $end) = explode(" to ", $dateRange);
			$filter = " AND hr_official_travel.start_date BETWEEN '$start' AND '$end'";
		}
		$data = "SELECT
		hr_official_travel.reference_number as reference_number, hr_official_travel.id_official_travel as id_official_travel, hr_expense_request.id_expense_request as id_expense_request, hr_expense_request.status as status,
		hr_employee.name as name, hr_employee.nik_employee as nik_employee, master_position_routing.description dec_position, master_branch.description as dec_branch, master_division.description as dec_division, hr_expense_request.description as start_date, hr_expense_request.unit_price as unit_price, master_principal.principal_code as principal_code, master_department.description as dec_dept, inventory.master_product_categories.code as code_product_category, inventory.master_product_categories.description as dec_product_category, hr_official_travel.location_to as location_to, hr_official_travel.reason_notes as reason_notes

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
		LEFT JOIN hr_cash_advance ON hr_cash_advance.id_official_travel=hr_official_travel.id_official_travel
		LEFT JOIN hr_expense_request ON hr_expense_request.id_cash_advance=hr_cash_advance.id_cash_advance
		LEFT JOIN inventory.master_product ON inventory.master_product.id_product=hr_expense_request.id_product
		LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories

		WHERE hr_official_travel.id_company = '$id_company' AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_company='$id_company' AND inventory.master_product_categories.description != 'Consumable' AND hr_official_travel.id_approval_request = hr_approval_transaction.id_employee_approval
		AND hr_official_travel.is_verified = 'true' $filter ORDER BY hr_official_travel.id_official_travel DESC, dec_product_category ASC
		";
		$data = DB::select($data);
		return $data;
	}

	public static function get_travel_request_bigen_data($id_expense_request)
	{
		$id_company = session('id_company');
		$data = "SELECT
		hr_official_travel.reference_number as reference_number, hr_official_travel.id_official_travel as id_official_travel, hr_expense_request.status as status, hr_expense_request.id_expense_request as id_expense_request,
		hr_employee.name as name, hr_employee.nik_employee as nik_employee, master_position_routing.description dec_position, master_branch.description as dec_branch, master_division.description as dec_division, hr_expense_request.description as start_date, hr_expense_request.unit_price as unit_price, master_principal.principal_code as principal_code, master_department.description as dec_dept, inventory.master_product_categories.code as code_product_category, inventory.master_product_categories.description as dec_product_category, hr_official_travel.location_to as location_to, hr_official_travel.reason_notes as reason_notes

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
		LEFT JOIN hr_cash_advance ON hr_cash_advance.id_official_travel=hr_official_travel.id_official_travel
		LEFT JOIN hr_expense_request ON hr_expense_request.id_cash_advance=hr_cash_advance.id_cash_advance
		LEFT JOIN inventory.master_product ON inventory.master_product.id_product=hr_expense_request.id_product
		LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories

		WHERE hr_official_travel.id_company = '$id_company' AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_company='$id_company' AND inventory.master_product_categories.description != 'Consumable' AND hr_official_travel.id_approval_request = hr_approval_transaction.id_employee_approval
		AND hr_official_travel.is_verified = 'true' AND hr_expense_request.id_expense_request = $id_expense_request ORDER BY hr_official_travel.id_official_travel DESC
		";
		$data = DB::select($data);
		// dd($data);
		return $data;
	}
	
	public static function get_product($idTravel,$code,$idExpense)
	{
	//	dd($idTravel." ".$code." ".$idExpense);
		$sql = "SELECT her.id_expense_request 
				FROM public.hr_expense_request her
				JOIN public.hr_cash_advance hca
				ON her.id_cash_advance = hca.id_cash_advance 
				JOIN inventory.master_product mp
				ON her.id_product = mp.id_product
				JOIN inventory.master_product_categories mpc
				ON mp.id_product_categories = mpc.id_product_categories
				WHERE hca.id_official_travel =  ".$idTravel." AND her.id_company = ".session('id_company')."  AND mpc.code = '".$code."'
				AND her.id_expense_request = ".$idExpense;
		$result = DB::select($sql);
	//	dd($result);
		return $result;
	}
}
