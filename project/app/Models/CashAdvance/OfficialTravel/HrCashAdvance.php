<?php

namespace App\Models\CashAdvance\OfficialTravel;

use App\Models\Organization\MasterOrganization\MasterRegional;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrCashAdvance extends Model
{
    // use HasFactory;
	protected $table="hr_cash_advance";
	protected $primaryKey="id_cash_advance";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

	public static function save_cash_advance_to_official_travel($request)
	{
		$hr_cashadvance = New HrCashAdvance();
		$hr_cashadvance -> id_official_travel = $request->id_official_travel;
		$hr_cashadvance -> id_employee = $request->request_by;
		$hr_cashadvance -> transaction_date = date('Y-m-d');
		$hr_cashadvance -> id_currency_cash_advance = $request->id_currency_cash_advance;
		$hr_cashadvance -> currency_rate_cash_advance = $request->currency_rate_cash_advance;
		$hr_cashadvance -> status = 'A';
		$hr_cashadvance -> id_company = session('id_company');
		$hr_cashadvance -> created_by = session('id_user');
		$hr_cashadvance -> save();
		// dd($data->id_official_travel);
		return $hr_cashadvance;
	}

	public static function getProducts($full = false, $hrOnly = false)
	{
		$codeLimiter = "AND mpc.code = 'CSM'";
		if($full) {
			$codeLimiter = "";
			if($hrOnly) {
				$codeLimiter = "AND mpc.code IN('TST', 'ACD')";
			}
		}
		$sql = "SELECT 
					mp.id_product as id, 
					mp.description as text,
					mp.id_uom,
					mp.code,
					mp.long_description,
					mum.description as desc_uom
				FROM inventory.master_product mp
				JOIN inventory.master_product_categories mpc ON
					mp.id_product_categories = mpc.id_product_categories
					$codeLimiter
				JOIN inventory.master_unit_measures mum ON
					mp.id_uom = mum.id_uom
				WHERE
					mp.id_company = ".session('id_company')."
				";
		return DB::select($sql);
	}

	public static function getApprovalList($requestBy, $idCashAdvance = null, $ignoreIsValidate = false, $isApproved = false) {
		$bindings = [];
		$where = "";
		$requestByQuery = "";
		if($requestBy) {
			$requestByQuery = "JOIN hr_employee he ON hca.id_employee = he.id_employee";
			// array_push($bindings);
		}
		if($idCashAdvance) {
			$where .= " AND hca.id_cash_advance IN(?) ";
			array_push($bindings, $idCashAdvance);
		}
		if(!$ignoreIsValidate) {
			$where .= " AND hca.is_validate = TRUE ";
		}
		if($isApproved === false) {
			$where .= " AND hca.is_approved_by_chief = FALSE ";
		} else if($isApproved === true) {
			$where .= " AND hca.is_approved_by_chief = TRUE ";
		}
		$sql = "SELECT 
			DISTINCT hse.is_validate,
			-- hot.*, 
			hot.id_official_travel,
			hot.reference_number as travel_reference_number,
			-- hot.letter_date,
			-- hot.travel_status,
			-- hot.start_date,
			-- hot.end_date,
			-- hot.location_to,
			-- hot.travel_status,
			mgd.description as desc_app_status, 
			mgd.code as code_app_status, 
			mgd2.description as category, 
			hca.reference_number as reference_number, 
			hca.transaction_date,
			hca.accounting_date,
			hca.total_expense_request_amount as hca_total_expense_request_amount,
			hca.settlement_status as settlement_status,
			hca.total_base_currency_tax_amount as total_base_currency_tax_amount,
			hca.total_tax_amount as total_tax_amount,
			hca.total_expense_request_amount as total_expense_request_amount,
			hca.total_cash_request_amount as total_cash_request_amount,
			hca.total_travel_request_amount as total_travel_request_amount,
			hca.total_base_currency_difference_amount,
			hca.payment_status as payment_status,
			hca.maximum_clearing_date,
			hca.total_clearing_amount,
			he2.name as name_approval_request,
			hca.id_approval_request as id_approval_request,
			hca.id_approval as id_approval,
			hah.description as approval_description,
			mgd3.description as hca_approval_status,
			he.id_employee as id_employee,
			he.name as name_employee,
			hca.reason_notes as reason_notes,
			hca.id_cash_advance,
			hca.is_validate,
			mpd.description as position_detail,
			md.id_dept as id_department,
			md.description as department_description,
			hca.creation_date as hca_creation_date,
			md.description as department_description,
			hca.id_currency_cash_advance,
			hca.total_base_currency_payment_amount,
			hca.total_base_currency_settlement_amount,
			hca.start_refund_date,
			hca.attachment_settlement,
			hcr.creation_date::date AS refund_date,
			hcr.total_base_currency_amount AS refund_amount,
			CASE 
				WHEN (hca.total_base_currency_settlement_amount > 0) THEN 'Settlement'
				ELSE 'Expense Request'
			END AS remark_transaction_type
		FROM hr_cash_advance hca
		JOIN master_general_data mgd ON hca.id_approval_status = mgd.id_general_data  AND mgd.code  = 'Approved' 
		LEFT JOIN hr_official_travel hot ON hca.id_official_travel = hot.id_official_travel AND hot.id_approval_status = mgd.id_general_data 
		JOIN hr_employee he ON hca.id_employee = he.id_employee
		JOIN hr_employee he3 ON he.nik_employee = he3.nik_employee AND he3.status = 'A'
		JOIN master_general_data mgd2 ON mgd2.id_general_data  = hot.id_reason_group AND mgd2.id_company = hot.id_company
		JOIN master_general_data mgd3 ON mgd3.id_general_data  = hca.id_approval_status
		JOIN hr_employee he2 ON hca.id_approval_request = he2.id_employee AND he2.id_user = ".session('id_user')."
		JOIN master_position_detail mpd ON he3.id_employee = mpd.id_employee AND mpd.secondary_position = FALSE
		JOIN hr_approval_header hah ON hca.id_approval = hah.id_approval
		JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing
		JOIN master_job_position mjp ON mpr.id_position = mjp.id_position
		JOIN master_department md ON mjp.id_dept = md.id_dept
		JOIN hr_settlement_expense hse ON
			hca.id_cash_advance = hse.id_cash_advance
			AND hse.is_validate = TRUE
		LEFT JOIN hr_cash_refund hcr ON hca.id_cash_advance = hcr.id_cash_advance
		WHERE hca.status = 'A'
		AND (mpd.secondary_position = FALSE AND mpd.id_company = ".session('id_company').")
		";

		$sql .= $where;
		$sql .= " ORDER BY hca.creation_date DESC";
		// print_r($sql);die();
		// dd($sql);
		return DB::select($sql, $bindings);
	}

	public static function getBranchFinanceApprovalList($financeMode = "settlement", $isPaidByFinance = false, $status = 'Not_Clear') {
		$employeePositionDetail = DB::selectOne("SELECT mpd.*  
			FROM master_position_detail mpd 
			JOIN hr_employee he 
			ON mpd.id_employee = he.id_employee 
			WHERE he.id_user = ? 
			AND he.status = 'A'", [session('id_user')]);
		
		$where = "";
		if($financeMode == "settlement") {
			$where .= "AND (hca.is_validate = TRUE) ";
		} else if($financeMode = "payment-cashadvance") {
			$where .= "AND (hca.total_expense_request_amount > 0) AND hca.is_paid_by_finance = ".($isPaidByFinance ? "TRUE" : "FALSE")." ";
		}
		$statusWhere = "AND LOWER(hca.settlement_status) = LOWER('$status')";
		if($status == "all") {
			$statusWhere = "";
		}
		$sql = "SELECT
			hca.is_validate,
			hot.id_official_travel,
			hot.reference_number as travel_reference_number,
			mgd.description as desc_app_status, 
			mgd.code as code_app_status, 
			mgd2.description as category, 
			hca.reference_number as reference_number, 
			hca.transaction_date,
			hca.accounting_date,
			hca.total_expense_request_amount as hca_total_expense_request_amount,
			hca.settlement_status as settlement_status,
			hca.total_base_currency_tax_amount as total_base_currency_tax_amount,
			hca.total_tax_amount as total_tax_amount,
			hca.total_expense_request_amount as total_expense_request_amount,
			hca.total_cash_request_amount as total_cash_request_amount,
			hca.total_travel_request_amount as total_travel_request_amount,
			hca.total_base_currency_difference_amount,
			hca.payment_status as payment_status,
			hca.maximum_clearing_date,
			hca.total_base_currency_clearing_amount as total_clearing_amount,
			-- hca.is_validate,
			he2.name as name_approval_request,
			hca.id_clearing_account,
			hca.id_approval_request as id_approval_request,
			hca.id_approval as id_approval,
			hah.description as approval_description,
			mgd3.description as hca_approval_status,
			he.id_employee as id_employee,
			he.name as name_employee,
			hca.reason_notes as reason_notes,
			hca.id_cash_advance,
			mpd.description as position_detail,
			md.id_dept as id_department,
			md.description as department_description,
			hca.creation_date as hca_creation_date,
			md.description as department_description,
			hca.id_currency_cash_advance,
			hca.total_base_currency_payment_amount,
			hca.total_base_currency_settlement_amount,
			hca.is_paid_by_hr,
			hca.is_paid_by_finance,
			hca.start_refund_date,
			hca.is_approved_by_chief,
			hca.attachment_settlement,
			hca.is_approved_by_finance,
			hcr.creation_date::date AS refund_date,
			hcr.total_base_currency_amount AS refund_amount,
			CASE 
				WHEN (hca.total_base_currency_settlement_amount > 0) THEN 'Settlement'
				ELSE 'Expense Request'
			END AS remark_transaction_type
		FROM hr_cash_advance hca
		JOIN master_general_data mgd ON hca.id_approval_status = mgd.id_general_data  AND mgd.code  = 'Approved' 
		LEFT JOIN hr_official_travel hot ON hca.id_official_travel = hot.id_official_travel AND hot.id_approval_status = mgd.id_general_data 
		JOIN hr_employee he ON hca.id_employee = he.id_employee
		JOIN master_general_data mgd2 ON mgd2.id_general_data  = hot.id_reason_group AND mgd2.id_company = hot.id_company
		JOIN master_general_data mgd3 ON mgd3.id_general_data  = hca.id_approval_status
		JOIN hr_employee he2 ON hca.id_approval_request = he2.id_employee
		LEFT JOIN master_position_detail mpd ON he.id_employee = mpd.id_employee AND mpd.secondary_position = FALSE
		JOIN hr_approval_header hah ON hca.id_approval = hah.id_approval
		LEFT JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing
		LEFT JOIN master_job_position mjp ON mpr.id_position = mjp.id_position
		LEFT JOIN master_department md ON mjp.id_dept = md.id_dept
		LEFT JOIN hr_career_transaction hct ON hca.id_employee = hct.id_employee 
			AND hct.status = 'A' 
			AND hct.executed = TRUE
			AND hct.creation_date >= current_date - interval '3 month'
		LEFT JOIN master_general_data mgd4 ON hct.id_transition_category = mgd4.id_general_data AND mgd4.code = 'Termination'
		LEFT JOIN master_position_detail mpd2 ON hct.id_old_position_detail = mpd2.id_position_detail 
		LEFT JOIN master_position_routing mpr2 ON mpd2.id_position_routing = mpr2.id_routing 
		LEFT JOIN hr_cash_refund hcr ON hca.id_cash_advance = hcr.id_cash_advance
		WHERE (hca.status = 'A' OR mgd4.code = 'Termination')
		$where
--		AND (hca.is_validate = TRUE)
--		AND (hca.total_expense_request_amount > 0) AND hca.is_paid_by_finance = TRUE
		$statusWhere
		AND hca.id_company = ? 
		AND coalesce(mpd.id_branch, mpd2.id_branch) = ? 
		AND COALESCE(mpr.id_job_grade, mpr2.id_job_grade) IN(
			SELECT id_job_grade 
			FROM master_job_grade mjg 
			WHERE id_company = ? 
			AND job_class_group = 'ant-level'
		)";
		return DB::select($sql, [$employeePositionDetail->id_company, $employeePositionDetail->id_branch, $employeePositionDetail->id_company]);
	}

	public static function getFinanceApprovalList($requestBy, $idCashAdvance = null, $statusFilter = null, $ignoreIsValidate = false, $mode = "finance", $financeMode = "settlement", $paidByFinanceFilter = "FALSE", $startDate = null, $endDate = null) {
		$bindings = [];
		$where = "";
		$requestByQuery = "";
		if($requestBy) {
			$requestByQuery = "JOIN hr_employee he ON hca.id_employee = he.id_employee";
			// array_push($bindings);
		}
		if($idCashAdvance) {
			$where .= "AND hca.id_cash_advance IN(?)";
			array_push($bindings, $idCashAdvance);
		}
		if(!$ignoreIsValidate && $financeMode == "settlement") {
			if($mode == "finance" && $statusFilter == 'not_clear') {
				$where .= "AND (hca.is_validate = TRUE)";
			}
			if($statusFilter && $statusFilter != "all") {
				if($mode == "hr") {
					$flt = strtolower($statusFilter) == 'clear' ? 'TRUE' : 'FALSE';
					$where .= " AND hca.is_paid_by_hr = ".$flt." AND hca.total_travel_request_amount > 0";
				} else $where .= " AND LOWER(hca.settlement_status) = LOWER('".$statusFilter."') ";
			}
		} else if($financeMode == "payment-cashadvance") {
			$where .= " AND (hca.total_expense_request_amount > 0) AND hca.is_paid_by_finance = ".$paidByFinanceFilter;
		}

		if($startDate) {
			$where .= " AND hca.transaction_date >= '".$startDate."'::date";
		}
		if($endDate) {
			$where .= " AND hca.transaction_date <= '".$endDate."'::date";
		}
		// dd($startDate);
		// if($mode == "hr") {
		// 	$where .= " AND hca.is_paid_by_hr = FALSE";
		// }
		

		$sql = "SELECT 
			hca.is_validate,
			hot.id_official_travel,
			hot.reference_number as travel_reference_number,
			mgd.description as desc_app_status, 
			mgd.code as code_app_status, 
			mgd2.description as category, 
			hca.reference_number as reference_number, 
			hca.transaction_date,
			hca.accounting_date,
			hca.total_expense_request_amount as hca_total_expense_request_amount,
			hca.settlement_status as settlement_status,
			hca.total_base_currency_tax_amount as total_base_currency_tax_amount,
			hca.total_tax_amount as total_tax_amount,
			hca.total_expense_request_amount as total_expense_request_amount,
			hca.total_cash_request_amount as total_cash_request_amount,
			hca.total_travel_request_amount as total_travel_request_amount,
			hca.total_base_currency_difference_amount,
			hca.payment_status as payment_status,
			hca.maximum_clearing_date,
			hca.total_base_currency_clearing_amount as total_clearing_amount,
			-- hca.is_validate,
			he2.name as name_approval_request,
			hca.id_clearing_account,
			hca.id_approval_request as id_approval_request,
			hca.id_approval as id_approval,
			hah.description as approval_description,
			mgd3.description as hca_approval_status,
			he.id_employee as id_employee,
			he.name as name_employee,
			hca.reason_notes as reason_notes,
			hca.id_cash_advance,
			mpd.description as position_detail,
			md.id_dept as id_department,
			md.description as department_description,
			hca.creation_date as hca_creation_date,
			md.description as department_description,
			hca.id_currency_cash_advance,
			hca.total_base_currency_payment_amount,
			hca.total_base_currency_settlement_amount,
			hca.is_paid_by_hr,
			hca.is_paid_by_finance,
			hca.start_refund_date,
			hca.is_approved_by_chief,
			hca.is_paid_by_hr,
			hca.is_approved_by_finance,
			hca.attachment_settlement,
			hcr.creation_date::date AS refund_date,
			hcr.total_base_currency_amount AS refund_amount,
			mr.description AS emp_region,
			CASE 
				WHEN (hca.total_base_currency_settlement_amount > 0) THEN 'Settlement'
				ELSE 'Expense Request'
			END AS remark_transaction_type
		FROM hr_cash_advance hca
		JOIN master_general_data mgd ON hca.id_approval_status = mgd.id_general_data  AND mgd.code  = 'Approved' 
		LEFT JOIN hr_official_travel hot ON hca.id_official_travel = hot.id_official_travel AND hot.id_approval_status = mgd.id_general_data 
		JOIN hr_employee he ON hca.id_employee = he.id_employee
		LEFT JOIN hr_employee he3 ON he.nik_employee = he3.nik_employee AND he3.status = 'A'
		JOIN master_general_data mgd2 ON mgd2.id_general_data  = hot.id_reason_group AND mgd2.id_company = hot.id_company
		JOIN master_general_data mgd3 ON mgd3.id_general_data  = hca.id_approval_status
		JOIN hr_employee he2 ON hca.id_approval_request = he2.id_employee
		LEFT JOIN master_position_detail mpd ON he3.id_employee = mpd.id_employee AND mpd.secondary_position = FALSE
		LEFT JOIN master_branch mb ON mpd.id_branch = mb.id_branch
		LEFT JOIN master_region mr ON mb.id_region = mr.id_region
		JOIN hr_approval_header hah ON hca.id_approval = hah.id_approval
		LEFT JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing
		LEFT JOIN master_job_position mjp ON mpr.id_position = mjp.id_position
		LEFT JOIN master_department md ON mjp.id_dept = md.id_dept
		LEFT JOIN hr_cash_refund hcr ON hca.id_cash_advance = hcr.id_cash_advance
		-- LEFT JOIN hr_settlement_expense hse ON
		-- 	hca.id_cash_advance = hse.id_cash_advance
		WHERE hca.status = 'A' AND hca.id_company = ".session('id_company')."
		";

		$sql .= $where;
		$sql .= " ORDER BY hca.creation_date DESC";
		// dd($sql);
		// print_r($sql);die();
		return DB::select($sql, $bindings);
	}

	public static function getApprovalStatus($idEmployee = null, $idCompany = null) {
		if(!$idEmployee) {
			$employee = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
			$idEmployee = @$employee->id_employee;
		}
		if(!$idCompany) {
			$idCompany = session('id_company');
		}
		$sql = "SELECT mgd2.code, han.* from (
				SELECT (
					SELECT hah.id_approval 
					FROM master_general_data mgd
					JOIN hr_approval_header hah
					ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = $idCompany
					LEFT JOIN master_general_type mgt
					ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
					WHERE mgd.id_company = $idCompany AND mgd.code='Expense_Request' AND hah.status = 'A' AND hah.id_job_grade IS NULL 
					AND hah.is_auto_approved = false
				), sfao.id_approval_mode, sfao.sequence, sfao.id_detail_chief, sfao.description_chief, sfao.id_employee_approval, he.name
			FROM sp_funct_approval_organization_hierarchy_view ($idEmployee, $idCompany, NULL, Array['gm-level','manager-level']) sfao
			left JOIN hr_employee he on he.id_employee=sfao.id_employee_approval 
			LIMIT 1
			) as han 
			left join hr_approval_header hah2 on hah2.id_approval=han.id_approval 
			left join master_general_data mgd2 on mgd2.id_general_data=hah2.id_approval_doc_type and mgd2.id_company = $idCompany";
		$approval = DB::selectOne($sql);
		$approval->hierarchy = DB::selectOne("SELECT hah.id_approval id, hah.description text
												FROM master_general_data mgd
												JOIN hr_approval_header hah
												ON mgd.id_general_data = hah.id_approval_doc_type AND hah.id_company = ?
												LEFT JOIN master_general_type mgt
												ON mgd.id_general_type = mgt.id_general_type AND mgt.general_type = 'master_approval_doc_type'
												LEFT JOIN master_job_grade mjg
												ON hah.id_job_grade = mjg.id_job_grade AND mjg.description = 'Director'
												WHERE mgd.id_company = ? AND mgd.code='Expense_Request'"
									, [session('id_company'), session('id_company')]);
		return $approval;
	}

	public static function getApproval($idApprovalHeader, $idCompany = null) {
		$idCompany = $idCompany ?? session('id_company');
        $sql = "SELECT 
                        hah.id_approval AS id,
                        hah.description AS text,
						hah.id_approval,
						hah.id_approval_doc_type,
						hah.hierarchy_type,
						mgd.code,
						(select id_general_data from master_general_data where code = 'Request_Approval' and id_company = ?) as id_approval_status
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				WHERE hah.id_approval = ? and hah.status = 'A' and hah.id_company = ?";
        return DB::select($sql, [$idCompany, $idApprovalHeader, $idCompany]);
    }

	public static function format_save($request)
	{
	//	dd($request->all());
		$dateRange = $request->start_end_date;
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
		$id_letter = \DB::table('hr_cash_advance as hca')
		->join('hr_employee', 'hca.id_employee', 'hr_employee.id_employee')
		->join('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
		->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->join('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
		->join('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		->whereMonth('hca.transaction_date',$bulan)
		->whereYear('hca.transaction_date',$tahun)
		->where('master_job_position.id_dept',$request->id_dept)
		->where('master_branch.id_region',$request->id_region)
		->where('hca.id_company',session('id_company'))
		->where('master_position_detail.id_company',session('id_company'))
	//	->where('hr_official_travel.request_by',$request->request_by)
		->max('hca.reference_number');
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
		$format = $kode."/".$company->company_code."-".'EXP/'.$code_region."-".$department_code."/".$bulan."/".substr($tahun,-2);
		return ['start_date'=>$start_date,'end_date'=>$end_date,'format'=>$format,'id_approval_status_transaction'=>$id_approval_status_transaction];
	}

	/**
	 * Management dashboard data for cash advance per employee
	 * @param int $idCompany
	 * @return mixed
	 */
	public static function cashAdvancePerEmployeeReport($idCompany) {
		$subQuery = DB::raw("(SELECT NULL as id, nik_employee, name, region, branch,department, sum(payment_amount) as payment,
			sum(settlement_amount) as settlement
			from sp_funct_dashboard_cash_advance_view(?,null,null,null,null)
			group by nik_employee, name, region, branch,department) as dcav");
		return DB::table($subQuery)
			->orderBy('settlement', 'desc')
			->orderBy('payment', 'desc')
			->setBindings([$idCompany]);
	}

	/**
	 * Management dashboard data for cash advance per employee per month
	 * @param int $idCompany
	 * @return mixed
	 */
	public static function cashAdvancePerEmployeePerMonthReport($idCompany) {
		$subQuery = DB::raw("(
				SELECT 
					NULL as id,
					to_char(transaction_date,'yyyy-mm') as month,
					nik_employee, name, region, branch,department,
					sum(payment_amount) as payment,
					sum(settlement_amount) as settlement
				from sp_funct_dashboard_cash_advance_view(?,null,null,null,null)
				group by nik_employee, name, region, branch, department, to_char(transaction_date,'yyyy-mm')
			) as dcav");
		return DB::table($subQuery)
			->setBindings([$idCompany])
			->orderBy('month', 'desc')
			->orderBy('settlement', 'desc')
			->orderBy('payment', 'desc')
			->whereRaw("concat(month, '-01')::date <= now()::date");
	}

	/**
	 * Management dashboard data for cash advance per month by department
	 * @param int $idCompany
	 * @param int $interval Interval in the past n-month
	 * @return array
	 */
	public static function cashAdvancePerMonthByDeptReport($idCompany, $interval = 3) {
		$interval = now()->subMonths($interval-1)->startOfMonth();
		return DB::select("SELECT *
			from 
			(select to_char(transaction_date,'yyyy-mm') as month,
					department,
					sum(payment_amount) as total_payment,
					sum(settlement_amount) as total_settlement
			from sp_funct_dashboard_cash_advance_view(?,null,null,null,null)
			group by department,to_char(transaction_date,'yyyy-mm')
			) as a
			WHERE concat(MONTH, '-01')::date >= ? AND concat(MONTH, '-01')::date <= now()
			order by month desc, total_settlement desc, total_payment desc", [$idCompany, $interval]);
	}

	/**
	 * Management dashboard data for cash advance per year by department
	 * @param int $idCompany
	 * @return array
	 */
	public static function cashAdvancePerYearByDeptReport($idCompany, $interval = 5) {
		$interval = now()->subYears($interval-1)->startOfYear();
		return DB::select("SELECT *
			from 
			(select department,to_char(transaction_date,'yyyy') as year,
					sum(payment_amount) as total_payment,
					sum(settlement_amount) as total_settlement
			from sp_funct_dashboard_cash_advance_view(?,null,null,null,null)
			group by department,to_char(transaction_date,'yyyy')) as a
			WHERE concat(year, '-01-01')::date >= ? AND concat(year, '-01-01')::date <= now()
			order by year desc, total_settlement desc, total_payment desc", [$idCompany, $interval]);
	}

	public static function cashAdvancePerYearReport($idCompany, $interval = 3) {
		$interval = now()->subYears($interval-1)->startOfYear();
		return DB::select("SELECT *
			from 
			(select to_char(transaction_date,'yyyy') as year,
					sum(payment_amount) as total_payment,
					sum(settlement_amount) as total_settlement
			from sp_funct_dashboard_cash_advance_view(?,null,null,null,null)
			group by to_char(transaction_date,'yyyy')) as a
			WHERE concat(year, '-01-01')::date >= ? AND concat(year, '-01-01')::date <= now()
			order by year desc, total_settlement desc, total_payment desc", [$idCompany, $interval]);
	}

	public static function getOutstandingReport($idCompany)
	{
		$subQuery = DB::raw("(SELECT NULL as id, nik_employee, name, region, branch,department, sum(payment_amount) as payment,
					sum(settlement_amount) as settlement, 
					case when payment_amount > settlement_amount
						then 'User Must Do Settlement'
						when payment_amount < settlement_amount
						then 'Finance Or HR Needs To Make A Payment'
					end as notes
			from sp_funct_dashboard_cash_advance_view(?,null,null,null,null)
			where payment_amount <> settlement_amount
			group by nik_employee, name, region, branch,department, notes) as dcav");
		return DB::table($subQuery)
			->setBindings([$idCompany])
			->orderBy('notes', 'desc')
			->orderBy('settlement', 'desc')
			->orderBy('payment', 'desc')
			->orderBy('nik_employee', 'asc');
	}
}
