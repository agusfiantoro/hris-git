<?php

namespace App\Http\Controllers\CashAdvance\OfficialTravel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MessageController;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Employee\Employee\Employee;
use App\Models\CashAdvance\OfficialTravel\HrOfficialTravel;
use App\Models\CashAdvance\OfficialTravel\HrExpenseRequest;
use App\Models\CashAdvance\OfficialTravel\HrCashAdvance;
use Illuminate\Support\Facades\Log;
use App\Models\CashAdvance\OfficialTravel\HrApprovalTransaction;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;   
use Exception;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class OfficialTravelController extends Controller
{
	public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->MessageController  = new MessageController;
	}
	
	public function index(Request $request)
	{
		if ($request->ajax()) {
			
			$data = HrOfficialTravel::get_offtrav();
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
				if ($data->status_approval != 'Rejected' OR $data->status_approval != 'Cancel') {
					$now = new DateTime(date('Y-m-d'));
					$startDate = new DateTime($data->start_date);
					$cancel_date = $startDate->diff($now)->days;
					$realDate = Carbon::parse($data->start_date)->format('Y-m-d');
					$idEncrypt = Crypt::encrypt($data->id_official_travel);
					if ($data->status_approval == 'New') {
						$button .='<button type="button" name="submit" id="btn-approve-'.$data->id_official_travel.'" more_type="Approve" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" class="btn-action btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
						$button .= '<button type="button" more_type="Edit" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_official_travel.'" title="Edit"><span class="fas fa-edit"></span></button> ';
						$button .= '<button type="button" name="cancel" more_type="Cancel" class="btn-action btn btn-danger btn-sm" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" title="Cancel"><span class="fa fa-close"></span></button>';
					}elseif ($data->status_approval == 'Request_Approval') {
						$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button> ';
						if( $data->travel_status != 'Reschedule'){
							$button .= '<button type="button" name="cancel" more_type="Cancel" class="btn-action btn btn-danger btn-sm" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" title="Cancel"><span class="fa fa-close"></span></button>';
						}
					}elseif ($data->status_approval == 'Approved' && $data->travel_status == 'Cancel') {
						$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
					}elseif ($data->status_approval == 'Approved') {
						$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
						$button .= ' <a href="'.route('print_offtrave',$idEncrypt).'" target="_blank" name="print" id="" class="btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></a> ';
						if($cancel_date >= 2 && date('Y-m-d') < $realDate){
							$button .= '<button type="button" name="reschedule" more_id="' . $data->id_official_travel . '" more_type="reschedule" class="reschedule btn btn-info btn-sm" title="Reschedule"><span class="far fa-calendar-alt"></span></button>';
							$button .= '&nbsp;<button type="button" name="req_cancel" more_id="' . $data->id_official_travel . '" more_type="req_cancel" class="btn-action btn btn-request-cancel btn-sm" title="Req Cancel" more_transaction="'.$data->id_approval_transaction.'"><span class="fa fa-window-close fa-lg"></span></button>';
						}
					}elseif ($data->status_approval == 'Revised') {
						$button .= '<button type="button" more_type="Edit" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_official_travel.'" title="Edit"><span class="fas fa-edit"></span></button> ';
						$button .= '<button type="button" name="cancel" more_type="Cancel" class="btn-action btn btn-danger btn-sm" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" title="Cancel"><span class="fa fa-close"></span></button> ';
					}elseif ($data->status_approval == 'Cancel' || $data->status_approval == 'Rejected') {
						$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button> ';
					}else{
						$button .= '';
					}
				}
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		$canSubmit = HrOfficialTravel::getCashAdvanceLock(session('id_user'));
		$bankAccount = DB::selectOne("SELECT hbe.* FROM hr_employee he 
						JOIN hr_bank_employee hbe ON he.id_employee = hbe.id_employee 
						WHERE he.id_user = ?
						AND he.status = 'A'
						AND hbe.default_bank = TRUE", [session('id_user')]);
		return view('cash_advance.official_travel.index', [
			"can_create" => $canSubmit,
			"bankAccount" => $bankAccount,
		]);
	}
	
	public function index_summary(Request $request)
	{
		if ($request->ajax()) {
			$data = HrOfficialTravel::get_offtrav_summary($request->start_end);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
				$idEncrypt = Crypt::encrypt($data->id_official_travel);
				if ($data->status_approval == 'Approved') {
					$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button> ';
					$button .= ' <a href="'.route('print_offtrave',$idEncrypt).'" target="_blank" name="print" id="" class="btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></a> ';
				}
				else{
					$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button> ';
				}
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('cash_advance.official_travel.index_summary');
	}
	public function get_data_view(Request $request)
	{
		$user = HrOfficialTravel::get_user_view($request->id_official_travel);
		$product_transport = HrOfficialTravel::get_product_transport();
		$product_akomodasi = HrOfficialTravel::get_product_akomodasi();
		$product_cashadvance = HrOfficialTravel::get_product_cashadvance();
		$travel_type = HrOfficialTravel::get_travel_type();
		$maximum_official_travel_request = HrOfficialTravel::maximum_official_travel_request();
		$cek_position_routing = HrOfficialTravel::cek_position_routing();
	//	dd($cek_position_routing);
		// $region_destination = HrOfficialTravel::get_region_destination();
		$approval_status = HrOfficialTravel::get_approval_status();
		$branch_transport = HrOfficialTravel::get_branch_transport();
		// dd(['product_transport'=>$product_transport,'product_akomodasi'=>$product_akomodasi,'product_cashadvance'=>$product_cashadvance,'travel_type'=>$travel_type,'maximum_official_travel_request'=>$maximum_official_travel_request,'cek_position_routing'=>$cek_position_routing,'approval_status'=>$approval_status,'branch_transport'=>$branch_transport]);
		return response()->json(['user'=>$user,'product_transport'=>$product_transport,'product_akomodasi'=>$product_akomodasi,'product_cashadvance'=>$product_cashadvance,'travel_type'=>$travel_type,'maximum_official_travel_request'=>$maximum_official_travel_request,'cek_position_routing'=>$cek_position_routing,'approval_status'=>$approval_status,'branch_transport'=>$branch_transport]);
	}
	
	public function get_data(Request $request)
	{
		$user = HrOfficialTravel::get_user();
		$product_transport = HrOfficialTravel::get_product_transport();
		$product_akomodasi = HrOfficialTravel::get_product_akomodasi();
		$product_cashadvance = HrOfficialTravel::get_product_cashadvance();
		$travel_type = HrOfficialTravel::get_travel_type();
		$maximum_official_travel_request = HrOfficialTravel::maximum_official_travel_request();
		$cek_position_routing = HrOfficialTravel::cek_position_routing();
	//	dd($cek_position_routing);
		// $region_destination = HrOfficialTravel::get_region_destination();
		$approval_status = HrOfficialTravel::get_approval_status();
		$branch_transport = HrOfficialTravel::get_branch_transport();
		$currency = DB::select("SELECT id_currency AS id, concat(currency_code, ' - ', long_description) AS text FROM master_currency WHERE id_country IS NOT NULL AND status = 'A'");
		$company = DB::selectOne("SELECT * FROM master_company WHERE id_company = ?", [session('id_company')]);
		return response()->json(['user'=>$user,'product_transport'=>$product_transport,'product_akomodasi'=>$product_akomodasi,'product_cashadvance'=>$product_cashadvance,'travel_type'=>$travel_type,'maximum_official_travel_request'=>$maximum_official_travel_request,'cek_position_routing'=>$cek_position_routing,'approval_status'=>$approval_status,'branch_transport'=>$branch_transport,'currency'=>$currency,'company'=>$company]);
	}

	public function get_edit_data(Request $request)
	{
		$request->validate([
			'id_official_travel' => 'required'
		]);
		$data = HrOfficialTravel::get_data($request->id_official_travel);
		return response()->json($data);
	}

	public function get_approval_by(Request $request)
	{
		$class_group = HrOfficialTravel::get_user();
		$job_class_group = $class_group[0]->job_class_group;
		$sql_header = HrOfficialTravel::get_approval_by($request);
		$approval_hirarki = $sql_header['sql_approval_hirarki'];
		$approval_by = $sql_header['sql_approval_by'];
		$count_by = count($approval_by);
		return response()->json(['approval_hirarki'=>$approval_hirarki,'approval_by'=>$approval_by,'count_by'=>$count_by,'job_class_group'=>$job_class_group]);
		// if (count($approval_by) > 0) {
		// 	dd($approval_by);
		// 	// $approval_hirarki = $sql_hirarki;
		// 	// $approval_by = $sql_by;
		// }else{
		// 	if ($job_class_group == 'gm-level') {
		// 		dd('hirarki ada, by ada');
		// 	}else{
		// 		dd('hirarki ada, by koson');
		// 	}
		// }
		// else{
		// 	if ($job_class_group == 'gm-level') {
		// 		$approval_hirarki = $sql_hirarki;
		// 	}
		// }
	}
	public function check_max_date_cashadvance(Request $request)
	{
		$check_max_date = HrOfficialTravel::check_max_date_cashadvance($request);
		foreach($check_max_date as $k => $max_date) {
			$check_max_date[$k]->clearance_cash_advance_allowed = HrOfficialTravel::getCashAdvanceLock(session('id_user'));
		}
		return response()->json($check_max_date);
	}
	public function change_type_transport(Request $request)
	{
		$data = HrOfficialTravel::changeTransportType($request);
		return response()->json($data);
	}
	public function save(Request $request)
	{
		$validationRules = [];
		$validationMessages = [];
		$validationRules += [
			'request_by' => 'required',
			'id_position_detail' => 'required',
			'start_end' => 'required',
			'location_to' => 'required|string|max:255',
			'id_reason_group' => 'required',
			'reason_notes' => 'required|string',
			'id_approval' => 'required',
			'id_approval_request' => 'required',
			'currencyrate_cashadvance' => 'required',
			'currency_cashadvance' => 'required',
			'letter_date' => 'date|date_equals:'.date('Y-m-d')
		];
		$validationMessages += [
			'request_by.required' => 'The Request By field is required.',
			'id_position_detail.required' => 'The Position field is required.',
			'start_end.required' => 'The Start & End Date field is required.',
			'location_to.required' => 'The Destination To field is required.',
			'location_to.max' => 'The Destination to must not be greater than 255 characters.',
			'id_reason_group.required' => 'The Travel Type field is required.',
			'reason_notes.required' => 'The Reason to Travel field is required.',
			'id_approval.required' => 'The Approval Hierarchy field is required.',
			'id_approval_request.required' => 'The Approved By field is required',
			'currencyrate_cashadvance' => 'The Currency Rate field is required',
			'currency_cashadvance' => 'The Currency field is required',
			'letter_date.date_equals' => "Letter date must be today (".date('Y-m-d').")"
		];

		$autoApproveByNik = ["20130401SP"];
		$isDirector = HrOfficialTravel::getDirector("he.id_employee id, he.name text, mu.id_user as id_user, mpr.id_job_grade, mpd.id_position_detail", session('id_user'), null, $autoApproveByNik);
		$autoApproveTarget = HrOfficialTravel::getDirectorOfficialTravel();
		$autoApprove = ($isDirector && $autoApproveTarget && $isDirector->id_job_grade == $autoApproveTarget->id_job_grade) || @$isDirector->id_user == session('id_user');
		if($autoApprove && $autoApproveTarget == null) {
			$autoApproveTarget = HrOfficialTravel::getDirectorOfficialTravel("hah.id_approval id, hah.description text, hah.id_job_grade, hah.is_auto_approved", null, true);
		}

		if (isset($request->with_caseadvance)) {
			$validationRules += [
				'cashadvance.*.nama' => 'required',
				'cashadvance.*.notes_cashadvance' => 'required|string',
				'cashadvance.*.tanggal' => 'required',
				'cashadvance.*.max_budget' => 'required',
				// 'cashadvance.*.total' => 'required',
			];
			$validationMessages += [
				'cashadvance.*.nama.required' => 'The Name field is required.',
				'cashadvance.*.notes_cashadvance.required' => 'The Notes field is required.',
				'cashadvance.*.tanggal.required' => 'The Date field is required.',
				'cashadvance.*.max_budget.required' => 'The Budget field is required.',
				// 'cashadvance.*.total.required' => 'The Total field is required.',
			];
			if (!empty($request->cashadvance)) {
				foreach ($request->cashadvance as $key => $valuec) {
					if(isset($valuec['nama'])){
						$cek_cashadvance = "SELECT code FROM inventory.master_product as mp WHERE id_product='".$valuec['nama']."'";
						$cek_cashadvance = DB::select($cek_cashadvance);
						if ($cek_cashadvance[0]->code == 'CSM0000002' OR $cek_cashadvance[0]->code == 'CSM0000006') {
							$validationRules += [
								'cashadvance.*.total' => 'required'
							];
							$validationMessages += [
								'cashadvance.*.total.required' => 'The Total field is required.'
							];
						}
					}
				}
			}
		}
		if (isset($request->with_trans)) {
			$validationRules += [
				'transport.*.jenis_transportasi' => 'required',
				'transport.*.transport_name' => 'required',
				'transport.*.from' => 'required|string',
				'transport.*.to' => 'required|string',
				'transport.*.date_transport' => 'required',
				'transport.*.time_transport' => 'required',
				'transport.*.branch' => 'required',
			];
			$validationMessages += [
				'transport.*.jenis_transportasi.required' => 'The Type of Transportation field is required.',
				'transport.*.transport_name.required' => 'The Transport Recommendation field is required.',
				'transport.*.from.required' => 'The From field is required.',
				'transport.*.to.required' => 'The To field is required.',
				'transport.*.date_transport.required' => 'The Date field is required.',
				'transport.*.time_transport.required' => 'The Time field is required.',
				'transport.*.branch.required' => 'The Destination Branch field is required.',
			];
		}
		if (isset($request->with_accom)) {
			$validationRules += [
				'akomodasi.*.product_akomodasi' => 'required',
				'akomodasi.*.nama_hotel' => 'required|string',
				'akomodasi.*.city' => 'required|string',
				'akomodasi.*.branch' => 'required',
				'akomodasi.*.start_end_akomodasi' => 'required',
				'akomodasi.*.lama_menginap' => 'string|not_in:0 Night(s)',
			];
			$validationMessages += [
				'akomodasi.*.nama_hotel.required' => 'The Name of Hotel field is required.',
				'akomodasi.*.city.required' => 'The City field is required.',
				'akomodasi.*.product_akomodasi.required' => 'The Product field is required.',
				'akomodasi.*.start_end_akomodasi.required' => 'The Start End Date field is required.',
				'akomodasi.*.branch.required' => 'The Destination Branch field is required.',
				'akomodasi.*.lama_menginap.not_in' => 'The Length of stay field is not 0 Night(s).',
			];
		}
		$request = SanitizedForm::sanitizeStringInput($request, $validationRules);
		$request->validate($validationRules, $validationMessages);

		$canSubmitCashAdvance = HrOfficialTravel::getCashAdvanceLock(session('id_user'));

		try {
			DB::beginTransaction();
			$result = HrOfficialTravel::format_save($request);
			$start_date = $result['start_date'];
			$end_date = $result['end_date'];
			$format = $result['format'];
			if (isset($request->unlock_gps)) {
				$unlock_gps = true;
			}else{
				$unlock_gps = false;
			}
			if (isset($request->with_caseadvance)) {
				$with_caseadvance = true;
			}else{
				$with_caseadvance = false;
			}
			if ($autoApprove) {
				$cek_status = DB::table('master_general_data')
				->select(
					DB::RAW('id_general_data')
				)
				->where('id_company',session('id_company'))
				->where('status','A')
				->where('description','Approved')
				->first();
				$id_approval_status = $cek_status->id_general_data;
			}else{
				$id_approval_status = $result['id_approval_status_transaction'];;
			}
			$data = New HrOfficialTravel();
			$data -> reference_number = $format;
			$data -> request_by = $request->request_by;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> start_date = $start_date.' '.date('H:i:s');
			$data -> end_date = $end_date.' '.date('H:i:s');
			$data -> location_to = strtoupper($request->location_to);
			$data -> id_reason_group = $request->id_reason_group;
			$data -> reason_notes = strtoupper($request->reason_notes);
			$data -> unlock_gps = $unlock_gps;
			$data -> is_have_cash_advance = $with_caseadvance;
			$data -> id_approval = $request->id_approval;
			$data -> id_approval_request = $request->id_approval_request;
			$data -> id_approval_status = $id_approval_status;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> letter_date = date('Y-m-d');
			$data -> save();

			$sql = "SELECT id_general_data, code 
					FROM master_general_data
					WHERE code = 'Project' AND id_company = ?";
			$reason = DB::selectOne($sql, [session('id_company')]);
			
			$jobGrade = DB::selectOne("SELECT mjg.id_job_grade, mjg.job_level
			FROM hr_employee he
			JOIN master_position_detail mpd
			ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
			JOIN master_position_routing mpr
			ON mpd.id_position_routing = mpr.id_routing
			JOIN master_job_grade mjg 
			ON mpr.id_job_grade = mjg.id_job_grade
			WHERE he.id_user = ".session('id_user')." AND he.status = 'A' AND (mjg.job_level = 1 OR he.nik_employee IN('".implode(',',$autoApproveByNik)."'))");
			$transaction_function = HrOfficialTravel::format_save_to_transaction($request);
			$transaction = New HrApprovalTransaction();		
			if($jobGrade == null) {				
				if($reason && $data->id_reason_group == $reason->id_general_data) {
					// If reason group == Project
					$transaction -> id_source_transaction = $data->id_official_travel;
					$transaction -> source_transaction_type = $transaction_function[0]->code;
					$transaction -> id_approval = $transaction_function[0]->id_approval;
					$transaction -> id_approval_mode = $transaction_function[0]->id_approval_mode;
					$transaction -> sequence = $transaction_function[0]->sequence;
					$transaction -> id_position_detail = $data->id_position_detail;
					$transaction -> id_employee_approval = $data->id_approval_request;
					$transaction -> id_approval_status = $id_approval_status;
					$transaction -> id_company = session('id_company');
					$transaction -> created_by = session('id_user');
					$transaction -> save();
				} else {
					foreach($transaction_function as $key=>$val){
						$appTrans = New HrApprovalTransaction();
						$appTrans -> id_source_transaction = $data->id_official_travel;
						$appTrans -> source_transaction_type = $val->code;
						$appTrans -> id_approval = $val->id_approval;
						$appTrans -> id_approval_mode = $val->id_approval_mode;
						$appTrans -> sequence = $val->sequence;
						$appTrans -> id_position_detail = $val->id_detail_chief;
						$appTrans -> id_employee_approval = $val->id_employee_approval;
						$appTrans -> id_approval_status = $id_approval_status;
						$appTrans -> id_company = session('id_company');
						$appTrans -> created_by = session('id_user');
						$appTrans -> save();
					}
				}
			} else {
				// DIRECTOR
				$id_approval_mode = DB::selectOne("SELECT mgd.id_general_data FROM master_general_type mgt 
				JOIN master_general_data mgd ON mgt.id_general_type = mgd.id_general_type and mgd.id_company = ".session('id_company')."
				WHERE mgt.general_type = 'master_approval_mode' and mgd.code = 'AND'");
				$transaction -> id_source_transaction = $data->id_official_travel;
				$transaction -> source_transaction_type = 'Official_Travel';
				$transaction -> id_approval = $autoApproveTarget->id;
				$transaction -> id_approval_mode = $id_approval_mode->id_general_data;
				$transaction -> sequence = 1;
				
				$transaction -> id_position_detail = $data->id_position_detail;
				$transaction -> id_employee_approval = $request->request_by;
				$transaction -> id_approval_status = $id_approval_status;
				$transaction -> id_company = session('id_company');
				$transaction -> created_by = session('id_user');
				$transaction -> save();			
			}			
			$company = DB::selectOne('SELECT * FROM master_company WHERE id_company = ?', [session('id_company')]);
			if(isset($request->with_caseadvance) || isset($request->with_trans) || isset($request->with_accom)) {
				// $data->id_currency_cash_advance = $company->id_currency;
				// $data->currency_rate_cash_advance = 1;
				$hr_cashadvance = HrCashAdvance::save_cash_advance_to_official_travel($data);
			}
			$company = DB::selectOne('SELECT * FROM master_company WHERE id_company = ?', [session('id_company')]);
			if (isset($request->with_caseadvance) && $canSubmitCashAdvance) {
				if (isset($request->cashadvance)) {
				//	$hr_cashadvance = HrCashAdvance::save_cash_advance_to_official_travel($data);
					foreach ($request->cashadvance as $key => $valuec) {
						$idUom = HrOfficialTravel::get_uom($valuec['nama']);
						/*
						$cek_product = HrOfficialTravel::cek_save_product($data,$valuec);
						
						if ($cek_product) {
							$validationRules += [
								'cashadvance.'.$key.'.nama' => 'required|unique:hr_expense_request,id_product'
							];
							$validationMessages += [
								'cashadvance.'.$key.'.nama.unique' => 'The Category has already been taken.'
							];
							$request->validate($validationRules, $validationMessages);
						}
						*/
						$dec_cashadvance = $valuec['tanggal'].';';
						if (empty($valuec['branch_cashadvance']) AND empty($valuec['region_cashadvance'])) {
							$notes_cashadvance = strtoupper($valuec['notes_cashadvance']).';';
						}else{
							$notes_cashadvance = strtoupper($valuec['notes_cashadvance']).';'.$valuec['region_cashadvance'].';'.$valuec['branch_cashadvance'];
						}
						if ($valuec['max_budget'] != '') {
							$unit_price = preg_replace("/[^aZ0-9]/", "", $valuec['max_budget']);
						}else{
							$unit_price = NULL;
						}
						if ($valuec['total'] != "") {
							$total_amount = preg_replace("/[^aZ0-9]/", "", $valuec['total']);
						}else{
							$total_amount = NULL;
						}
						$expense = New HrExpenseRequest();
						$expense -> id_cash_advance = $hr_cashadvance->id_cash_advance;
						$expense -> id_product = $valuec['nama'];
						$expense -> id_uom = $idUom->id_uom;
						$expense -> description = $dec_cashadvance;
						$expense -> notes = $notes_cashadvance;
						if ($valuec['qty_cashadvance'] != '') {
							$expense -> qty = $valuec['qty_cashadvance'];
						}
						$expense -> unit_price = $unit_price;
					//	$expense -> total_amount = $total_amount;
						$expense -> id_currency = $company->id_currency;
						$expense -> status = 'A';
						$expense -> id_company = session('id_company');
						$expense -> created_by = session('id_user');
						$expense -> save();
						// $hr_cashadvance->id_currency_cash_advance = $expense->id_currency;
						// $hr_cashadvance->currency_rate_cash_advance = 1;
						$hr_cashadvance->save();
					}
				}else{
					return response()->json(['status'=>'null','message'=>'Add at least 1 Cash Advance Detail.','title'=>'Cash Advance Detail']);
				}
			}
			if (isset($request->with_trans)) {
				if (isset($request->transport)) {
				//	$hr_cashadvance = HrCashAdvance::save_cash_advance_to_official_travel($data);
					foreach ($request->transport as $key => $value) {
						$idUom = HrOfficialTravel::get_uom($value['jenis_transportasi']);
						$dec_transport = $value['date_transport'].';'.$value['time_transport'].';';
						$notes_transport = $value['transport_name'].';'.strtoupper($value['from']).';'.strtoupper($value['to']).';';
					/*	if (empty($value['branch'])) {
							$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';';
						}else{
							$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';'.$value['branch'].';';
						}
					*/
						$expense = New HrExpenseRequest();
						$expense -> id_cash_advance = $hr_cashadvance->id_cash_advance;
						$expense -> id_product = $value['jenis_transportasi'];
						$expense -> id_uom = $idUom->id_uom;
						$expense -> id_branch = $value['branch'];
						$expense -> description = $dec_transport;
						$expense -> notes = $notes_transport;
						$expense -> qty = 1;
						$expense -> unit_price = 0;
						$expense -> id_currency = $company->id_currency;
						$expense -> status = 'A';
						$expense -> id_company = session('id_company');
						$expense -> created_by = session('id_user');
						$expense -> save();
						// $hr_cashadvance->id_currency_cash_advance = $expense->id_currency;
						// $hr_cashadvance->currency_rate_cash_advance = 1;
						$hr_cashadvance->save();
					}
				}else{
					return response()->json(['status'=>'null','message'=>'Add at least 1 Transport Detail.','title'=>'Transport Detail']);
				}
			}
			if (isset($request->with_accom)) {
				if (isset($request->akomodasi)) {
				//	$hr_cashadvance = HrCashAdvance::save_cash_advance_to_official_travel($data);
					foreach ($request->akomodasi as $key => $values) {
						$idUom = HrOfficialTravel::get_uom($values['product_akomodasi']);
						$dec_akomodasi = $values['start_end_akomodasi'].';';
						$notes_akomodasi = strtoupper($values['nama_hotel']).';'.strtoupper($values['city']).';';
						// $qty = $values['lama_menginap'];
						$qty = str_replace(" Night(s)", "", $values['lama_menginap']);
						$expense = New HrExpenseRequest();
						$expense -> id_cash_advance = $hr_cashadvance->id_cash_advance;
						$expense -> id_product = $values['product_akomodasi'];
						$expense -> id_uom = $idUom->id_uom;
						$expense -> description = $dec_akomodasi;
						$expense -> notes = $notes_akomodasi;
						$expense -> id_branch = $values['branch'];
						$expense -> qty = $qty;
						$expense -> unit_price = 0;
						$expense -> id_currency = $company->id_currency;
						$expense -> status = 'A';
						$expense -> id_company = session('id_company');
						$expense -> created_by = session('id_user');
						$expense -> save();
					}
				}else{
					return response()->json(['status'=>'null','message'=>'Add at least 1 Accomodation Detail.','title'=>'Accomodation Detail']);
				}
			}
			DB::commit();
			return response()->json(['status'=>'true','data'=>$data,'message'=>'Official Travel Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Official Travel !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit(Request $request, $id_official_travel)
	{
		$result = HrOfficialTravel::get_edit($id_official_travel);
		$data = $result['data'];
		$cashadvance = $result['cashadvance'];
		$transport = $result['transport'];
		$akomodasi = $result['akomodasi'];
		return response()->json([
			'data'=>$data,
			'cashadvance'=>$cashadvance,
			'transport'=>$transport,
			'akomodasi'=>$akomodasi
		]);
	}
	public function get_region_destination(Request $request)
	{
		$data = HrOfficialTravel::get_region_destination($request);
		return response()->json($data);
	}
	public function change_region_cashadvance(Request $request)
	{
		$data = HrOfficialTravel::get_branch_destination($request);
		return response()->json($data);
	}
	public function update_offtrave(Request $request)
	{
		$validationRules = [];
		$validationMessages = [];
	//	dd($request->all());
		if($request->buttonType != 'reschedule'){
			$validationRules += [
				'request_by' => 'required',
				'id_position_detail' => 'required',
				'location_to' => 'required|max:255',
				'id_reason_group' => 'required',
				'reason_notes' => 'required',
				'id_approval' => 'required',
			];
			$validationMessages += [
				'request_by.required' => 'The Request By field is required.',
				'id_position_detail.required' => 'The Position field is required.',
				'location_to.required' => 'The Destination To field is required.',
				'location_to.max' => 'The Destination to must not be greater than 255 characters.',
				'id_reason_group.required' => 'The Travel Type field is required.',
				'reason_notes.required' => 'The Reason to Travel field is required.',
				'id_approval.required' => 'The Approval Hierarchy field is required.',
			];
		}
		if (isset($request->with_caseadvance)) {
			if (isset($request->cashadvance)) {
				foreach ($request->cashadvance as $key => $valuec) {
					if($valuec['re_disabled'] != 're_disabled'){
						$validationRules += ['cashadvance.'.$key.'.nama' => 'required',
											 'cashadvance.'.$key.'.tanggal' => 'required',
											 'cashadvance.'.$key.'.total' => 'required',
											 'cashadvance.'.$key.'.notes_cashadvance' => 'required',
						];
						$validationMessages += ['cashadvance.'.$key.'.nama.required' => 'The Category field is required.',
											 'cashadvance.'.$key.'.tanggal.required' => 'The Date field is required.',
											 'cashadvance.'.$key.'.total.required' => 'The Total field is required',
											 'cashadvance.'.$key.'.notes_cashadvance.required' => 'The Notes field is required',
						];
					}
					else if($valuec['re_disabled'] == 're_disabled'){
						$dateRange = $request->start_end;
						$dateRangeDetail = $valuec['tanggal'];
						$dateFix = explode(" to ", $dateRange);
						$dateFixDetail = explode(" to ", $dateRangeDetail);
						$dateAfter = date('Y-m-d',strtotime($dateFix[0] . "-1 days"));
						$dateBefore = date('Y-m-d',strtotime($dateFix[1] . "+1 days"));
						$dateStart = date('Y-m-d',strtotime($dateFixDetail[0]));
						$dateEnd = date('Y-m-d',strtotime($dateFixDetail[1]));
						if(!isset($valuec['check_cashadvance'])){
							if(($dateStart > $dateAfter && $dateStart < $dateBefore) && ($dateEnd > $dateAfter && $dateEnd < $dateBefore)){
								$validationRules += [];
								$validationMessages += [];
							}
							else{
								$validationRules += [
													 'cashadvance.'.$key.'.tanggal' => 'after:'.$dateAfter.'|before:'.$dateBefore,
								];
								$validationMessages += [
													 'cashadvance.'.$key.'.tanggal.after' => 'The Date field does not match',
								];
							}
						}
					}
				}
			}

			// if (!empty($request->cashadvance)) {
			// 	foreach ($request->cashadvance as $key => $valuec) {
			// 		if(isset($valuec['nama'])){
			// 			if($valuec['re_disabled'] == 're_disabled'){
			// 				if(!isset($valuec['check_cashadvance'])){
			// 					$cek_cashadvance = "SELECT code FROM inventory.master_product as mp WHERE id_product='".$valuec['nama']."'";
			// 					$cek_cashadvance = DB::select($cek_cashadvance);
			// 					if ($cek_cashadvance[0]->code == 'CSM0000002' OR $cek_cashadvance[0]->code == 'CSM0000006') {
			// 						$validationRules += [
			// 							'cashadvance.'.$key.'.total' => 'required'
			// 						];
			// 						$validationMessages += [
			// 							'cashadvance.'.$key.'.total.required' => 'The Total field is required.'
			// 						];
			// 					}
			// 				}
			// 				else{
			// 					$validationRules += [];
			// 					$validationMessages += [];
			// 				}
			// 			}
			// 			else{
			// 				$cek_cashadvance = "SELECT code FROM inventory.master_product as mp WHERE id_product='".$valuec['nama']."'";
			// 				$cek_cashadvance = DB::select($cek_cashadvance);
			// 				if ($cek_cashadvance[0]->code == 'CSM0000002' OR $cek_cashadvance[0]->code == 'CSM0000006') {
			// 					$validationRules += [
			// 						'cashadvance.'.$key.'.total' => 'required'
			// 					];
			// 					$validationMessages += [
			// 						'cashadvance.'.$key.'.total.required' => 'The Total field is required.'
			// 					];
			// 				}
			// 			}
			// 		}
			// 	}
			// }
		}
		if (isset($request->with_trans)) {
			if (isset($request->transport)) {
				foreach ($request->transport as $key => $valuec) {
					if($valuec['trans_disabled'] != 're_disabled'){
						$validationRules += ['transport.'.$key.'.jenis_transportasi' => 'required',
											 'transport.'.$key.'.transport_name' => 'required',
											 'transport.'.$key.'.from' => 'required',
											 'transport.'.$key.'.to' => 'required',
											 'transport.'.$key.'.branch' => 'required',
											 'transport.'.$key.'.date_transport' => 'required',
											 'transport.'.$key.'.time_transport' => 'required',
						];
						$validationMessages += ['transport.'.$key.'.jenis_transportasi.required' => 'The Type of Transportation field is required.',
											 'transport.'.$key.'.transport_name.required' => 'The Transport Name field is required.',
											 'transport.'.$key.'.from.required' => 'The Date From is required.',
											 'transport.'.$key.'.to.required' => 'The To field is required',
											 'transport.'.$key.'.branch.required' => 'The Branch field is required',
											 'transport.'.$key.'.date_transport.required' => 'The Date field is required',
											 'transport.'.$key.'.time_transport.required' => 'The Time field is required',
						];
					}
					else if($valuec['trans_disabled'] == 're_disabled'){
						$dateRange = $request->start_end;
						$dateFix = explode(" to ", $dateRange);
						$dateAfter = date('Y-m-d',strtotime($dateFix[0] . "-1 days"));
						$dateBefore = date('Y-m-d',strtotime($dateFix[1] . "+1 days"));
						if(!isset($valuec['check_trans'])){
							$validationRules += [
												 'transport.'.$key.'.date_transport' => 'after:'.$dateAfter.'|before:'.$dateBefore,
							];
							$validationMessages += [
												 'transport.'.$key.'.date_transport.after' => 'The Date field does not match',
							];
						}
					}
				}
			}
		}
		if (isset($request->with_accom)) {
			if (isset($request->akomodasi)) {
				foreach ($request->akomodasi as $key => $valuec) {
					if($valuec['akomodasi_disabled'] != 're_disabled'){
						$validationRules += ['akomodasi.'.$key.'.product_akomodasi' => 'required',
											 'akomodasi.'.$key.'.nama_hotel' => 'required',
											 'akomodasi.'.$key.'.city' => 'required',
											 'akomodasi.'.$key.'.branch' => 'required',
											 'akomodasi.'.$key.'.start_end_akomodasi' => 'required',
						];
						$validationMessages += ['akomodasi.'.$key.'.product_akomodasi.required' => 'The Name of Hotel field is required.',
											 'akomodasi.'.$key.'.nama_hotel.required' => 'The Product field is required.',
											 'akomodasi.'.$key.'.city.required' => 'The City field is required.',
											 'akomodasi.'.$key.'.branch.required' => 'The Branch field is required.',
											 'akomodasi.'.$key.'.start_end_akomodasi.required' => 'The Start End Date field is required.',
						];
					}
					else if($valuec['akomodasi_disabled'] == 're_disabled'){
						$dateRange = $request->start_end;
						$dateRangeDetail = $valuec['start_end_akomodasi'];
						$dateFix = explode(" to ", $dateRange);
						$dateFixDetail = explode(" to ", $dateRangeDetail);
						$dateAfter = date('Y-m-d',strtotime($dateFix[0] . "-1 days"));
						$dateBefore = date('Y-m-d',strtotime($dateFix[1] . "+1 days"));
						$dateStart = date('Y-m-d',strtotime($dateFixDetail[0]));
						$dateEnd = date('Y-m-d',strtotime($dateFixDetail[1]));
						if(!isset($valuec['check_akomodasi'])){
							if(($dateStart > $dateAfter && $dateStart < $dateBefore) && ($dateEnd > $dateAfter && $dateEnd < $dateBefore)){
								$validationRules += [];
								$validationMessages += [];
							}
							else{
								$validationRules += [
													 'akomodasi.'.$key.'.start_end_akomodasi' => 'after:'.$dateAfter.'|before:'.$dateBefore,
								];
								$validationMessages += [
													 'akomodasi.'.$key.'.start_end_akomodasi.after' => 'The Date field does not match',
								];
							}
						}
					}
				}
			}
		}
		$request = SanitizedForm::sanitizeStringInput($request, $validationRules);
		$request->validate($validationRules, $validationMessages);

		try {
			DB::beginTransaction();
			$dateRange = $request->start_end;
			list($start_date, $end_date) = explode(" to ", $dateRange);
			
			if ($request->clickedButton == 'draft') {
					$id_approval_status = $request->id_approval_status;
			}else{
				$sql = "SELECT 
				id_general_data id,
				description text,
				code
				FROM  master_general_data where status = 'A' AND code='Request_Approval' and id_general_type = 7 and id_company =" . session('id_company');
				$result = DB::select($sql);
				$id_approval_status = $result[0]->id;
			}
			
			if($request->buttonType == 'reschedule'){
				$data = HrOfficialTravel::where('id_official_travel',$request->id_official_travel)->first();
				$data -> start_date = $start_date.' '.date('H:i:s');
				$data -> end_date = $end_date.' '.date('H:i:s');
				$data -> travel_status = 'Reschedule';
				$data -> reason_notes = $request->reason_notes ? strtoupper($request->reason_notes) : $data->reason_notes;
				$data -> id_approval_status = $id_approval_status;
				$data -> updated_by = session('id_user');
			}
			else{
				if (isset($request->unlock_gps)) {
					$unlock_gps = true;
				}else{
					$unlock_gps = false;
				}
				if (isset($request->with_caseadvance)) {
					$with_caseadvance = true;
				}else{
					$with_caseadvance = false;
				}
				
				$data = HrOfficialTravel::where('id_official_travel',$request->id_official_travel)->first();
				$data -> request_by = $request->request_by;
				$data -> id_position_detail = $request->id_position_detail;
				$data -> location_to = strtoupper($request->location_to);
				$data -> id_reason_group = $request->id_reason_group;
				$data -> reason_notes = strtoupper($request->reason_notes);
				$data -> start_date = $start_date.' '.date('H:i:s');
				$data -> end_date = $end_date.' '.date('H:i:s');
				$data -> unlock_gps = $unlock_gps;
				if ($request->clickedButton == 'submit') {
					$data -> id_approval_status = $id_approval_status;
				}
				$data -> status = 'A';
				$data -> is_have_cash_advance = $with_caseadvance;
				$data -> id_company = session('id_company');
				$data -> updated_by = session('id_user');
			}			
			$data -> is_verified = false;
			$data -> save();
			if ($request->clickedButton == 'submit') {
			//	HrOfficialTravel::send_mail($request);
				$transaction = HrApprovalTransaction::where('id_source_transaction',$request->id_official_travel)
				->where('source_transaction_type','Official_Travel')
				->get();
				foreach($transaction as $key=>$val){
					$val -> id_approval_status = $id_approval_status;
					$val -> id_company = session('id_company');
					if($request->buttonType == 'reschedule'){
						$val -> updated_by = null;
					}
					$val -> save();
				}				
			}
					
		//	dd($request->all());
			if(!$request->id_cashadvance_cash && ($request->with_caseadvance ||  $request->with_trans || $request->with_accom)) {
				$cashAdv = HrCashAdvance::save_cash_advance_to_official_travel($request);
				$request->id_cashadvance_cash = $cashAdv->id_cash_advance;
			}
			if (isset($request->with_caseadvance)) {
				if (isset($request->cashadvance)) {
					if($request->buttonType != 'reschedule'){
						$zc = [];
						foreach ($request->cashadvance as $key => $valCash) {
							if($valCash['id_cash_advance'] != null){
								array_push($zc,$valCash['id_cash_advance']);
							}
						}
						$data_csm = [
							'status' => 'I',
							'update_date' => date('Y-m-d H:i:s'),
							'updated_by' => session('id_user'),
						];
						if(count($zc) == 0){
							$noWith = HrOfficialTravel::no_with_del('CSM',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$xc[] = $val->id_expense_request;					
								}
								
								$hapus_expense_cash = HrExpenseRequest::whereIn('id_expense_request',$xc)->update($data_csm);
							//	$hapus_expense_request->status = "I";
							//	$hapus_expense_request->save();
							}
						}
						else{
							$noWith = HrOfficialTravel::no_with_del('CSM',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$xc[] = $val->id_expense_request;					
								}
							}
							$hapus_expense_cash = HrExpenseRequest::whereIn('id_expense_request',$xc)->whereNotIn('id_expense_request',$zc)->update($data_csm);
						}
					}
		
					// dump($valuec);
					foreach ($request->cashadvance as $key => $valuec) {
						if($valuec['re_disabled'] == null){
							$idUom = HrOfficialTravel::get_uom($valuec['nama']);
							if ($valuec['id_cash_advance'] == '' || $valuec['id_cash_advance'] == null) {
								$expense = New HrExpenseRequest();
							}else{
								$expense = HrExpenseRequest::where('id_expense_request',$valuec['id_cash_advance'])->first();
							}
							if (empty($valuec['branch_cashadvance']) AND empty($valuec['region_cashadvance'])) {
								$notes_cashadvance = strtoupper($valuec['notes_cashadvance']).';';
							}else{
								$notes_cashadvance = strtoupper($valuec['notes_cashadvance']).';'.$valuec['region_cashadvance'].';'.$valuec['branch_cashadvance'];
							}
							$dec_cashadvance = $valuec['tanggal'].';';
							if ($valuec['max_budget'] != '') {
								$unit_price = preg_replace("/[^aZ0-9]/", "", $valuec['max_budget']);
							}else{
								$unit_price = NULL;
							}
							if ($valuec['total'] != "") {
								$total_amount = preg_replace("/[^aZ0-9]/", "", $valuec['total']);
							}else{
								$total_amount = NULL;
							}
							$expense -> id_cash_advance = $request->id_cashadvance_cash;
							$expense -> id_product = $valuec['nama'];
							$expense -> id_uom = $idUom->id_uom;
							$expense -> description = $dec_cashadvance;
							$expense -> notes = $notes_cashadvance;
							if ($valuec['qty_cashadvance'] != '') {
								$expense -> qty = $valuec['qty_cashadvance'];
							}
							$expense -> unit_price = $unit_price;
						//	$expense -> total_amount = $total_amount;
							$expense -> id_company = session('id_company');
							$expense -> created_by = session('id_user');
							$expense -> updated_by = session('id_user');
							$expense -> save();
						
						}
					}						
				}				
				else{
					if($request->buttonType != 'reschedule'){
						$noWith = HrOfficialTravel::no_with_del('CSM',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$x[] = $val->id_expense_request;					
								}
								$data_csm = [
									'status' => 'I',
									'update_date' => date('Y-m-d H:i:s'),
									'updated_by' => session('id_user'),
								];
							$hapus_expense_request = HrExpenseRequest::whereIn('id_expense_request',$x)->update($data_csm);
						}
					//	return response()->json(['status'=>'null','message'=>'Add at least 1 Cash Advance Detail.','title'=>'Cash Advance Detail']);
					}
				}
			}
			else{
				if($request->id_cashadvance_cash != null){
					if($request->buttonType != 'reschedule'){
						$noWith = HrOfficialTravel::no_with_del('CSM',$request->id_cashadvance_cash);
						if(count($noWith) > 0){
							foreach($noWith as $key=>$val){
								$x[] = $val->id_expense_request;					
							}
							$hapus_expense_request_transport = HrExpenseRequest::whereIn('id_expense_request',$x)->first();
							$hapus_expense_request_transport->status = "I";
							$hapus_expense_request_transport->save();	
						}
					}
				}
			}
		
			if (!empty($request->id_cash_advance_del)) {
				$id_cash_advance_del = explode(",", $request->id_cash_advance_del);
				$hapus_expense_request_cashadvance = HrExpenseRequest::whereIn('id_expense_request',$id_cash_advance_del)->first();
				$hapus_expense_request_cashadvance->status = "I";
				$hapus_expense_request_cashadvance->save();
			}
			// Transport and Akomodasi
			if (isset($request->with_trans)) {
				if (isset($request->transport)) {
					if($request->buttonType != 'reschedule'){
						$zt = [];
						foreach ($request->transport as $key => $valCash) {
							if($valCash['id_transport'] != null){
								array_push($zt,$valCash['id_transport']);
							}
						}
						$data_tst = [
							'status' => 'I',
							'update_date' => date('Y-m-d H:i:s'),
							'updated_by' => session('id_user'),
						];
						if(count($zt) == 0){
							$noWith = HrOfficialTravel::no_with_del('TST',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$xt[] = $val->id_expense_request;					
								}
								
								$hapus_expense_request = HrExpenseRequest::whereIn('id_expense_request',$xt)->update($data_tst);
							}
						}
						else{
							$noWith = HrOfficialTravel::no_with_del('TST',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$xt[] = $val->id_expense_request;					
								}
							}
							$hapus_expense_request = HrExpenseRequest::whereIn('id_expense_request',$xt)->whereNotIn('id_expense_request',$zt)->update($data_tst);
						}
					}
					foreach ($request->transport as $key => $value) {					
						if($value['trans_disabled'] == null){
							$idUom = HrOfficialTravel::get_uom($value['jenis_transportasi']);
							$dec_transport = $value['date_transport'].';'.$value['time_transport'].';';
							$notes_transport = $value['transport_name'].';'.strtoupper($value['from']).';'.strtoupper($value['to']).';';
						/*	if (empty($value['branch'])) {
								$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';';
							}else{
								$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';'.$value['branch'].';';
							}
						*/
							if ($value['id_transport'] == '' || $value['id_transport'] == null) {
								$expense = New HrExpenseRequest();
							}else{
								$expense = HrExpenseRequest::where('id_expense_request',$value['id_transport'])->first();
							}
							$expense -> id_cash_advance = $request->id_cashadvance_cash;
							$expense -> id_product = $value['jenis_transportasi'];
							$expense -> id_uom = $idUom->id_uom;
							$expense -> id_branch = $value['branch'];
							$expense -> description = $dec_transport;
							$expense -> notes = $notes_transport;
							$expense -> qty = 1;
							$expense -> unit_price = 0;
							$expense -> status = 'A';
							$expense -> id_company = session('id_company');
							$expense -> created_by = session('id_user');
							$expense -> updated_by = session('id_user');
							$expense -> save();
						}
					}
				}else{
					if($request->buttonType != 'reschedule'){
						$noWith = HrOfficialTravel::no_with_del('TST',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$x[] = $val->id_expense_request;					
								}
								$data_tst = [
									'status' => 'I',
									'update_date' => date('Y-m-d H:i:s'),
									'updated_by' => session('id_user'),
								];
								$hapus_expense_request = HrExpenseRequest::whereIn('id_expense_request',$x)->update($data_tst);
							//	$hapus_expense_request->status = "I";
							//	$hapus_expense_request->save();
							}
					//	return response()->json(['status'=>'null','message'=>'Add at least 1 Transport Detail.','title'=>'Transport Detail']);
					}
				}
			}
			else{
				if($request->id_cashadvance_cash != null){
					if($request->buttonType != 'reschedule'){
						$noWith = HrOfficialTravel::no_with_del('TST',$request->id_cashadvance_cash);
						if(count($noWith) > 0){
							foreach($noWith as $key=>$val){
								$x[] = $val->id_expense_request;					
							}
							$data_tst = [
									'status' => 'I',
									'update_date' => date('Y-m-d H:i:s'),
									'updated_by' => session('id_user'),
								];
							$hapus_expense_request_transport = HrExpenseRequest::whereIn('id_expense_request',$x)->update($data_tst);
						//	$hapus_expense_request_transport->status = "I";
						//	$hapus_expense_request_transport->save();
						}
					}
				}
			}
		
			if (!empty($request->id_transport_del)) {
				$id_transport_del = explode(",", $request->id_transport_del);
				
				$hapus_expense_request_transport = HrExpenseRequest::whereIn('id_expense_request',$id_transport_del)->first();
				$hapus_expense_request_transport->status = "I";
				$hapus_expense_request_transport->save();
			}
			if (isset($request->with_accom)) {
				if (isset($request->akomodasi)) {
					if($request->buttonType != 'reschedule'){
						$z = [];
						foreach ($request->akomodasi as $key => $valCash) {
							if($valCash['id_akomodasi'] != null){
								array_push($z,$valCash['id_akomodasi']);
							}
						}
						$data_acd = [
							'status' => 'I',
							'update_date' => date('Y-m-d H:i:s'),
							'updated_by' => session('id_user'),
						];
						if(count($z) == 0){
							$noWith = HrOfficialTravel::no_with_del('ACD',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$x[] = $val->id_expense_request;					
								}
								
								$hapus_expense_request = HrExpenseRequest::whereIn('id_expense_request',$x)->update($data_acd);
							//	$hapus_expense_request->status = "I";
							//	$hapus_expense_request->save();
							}
						}
						else{
							$noWith = HrOfficialTravel::no_with_del('ACD',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$x[] = $val->id_expense_request;					
								}
							}
							$hapus_expense_request = HrExpenseRequest::whereIn('id_expense_request',$x)->whereNotIn('id_expense_request',$z)->update($data_acd);
						}
					}
					foreach ($request->akomodasi as $key => $values) {
						if($values['akomodasi_disabled'] == null){
							$idUom = HrOfficialTravel::get_uom($values['product_akomodasi']);
							$dec_akomodasi = $values['start_end_akomodasi'].';';
							$notes_akomodasi = strtoupper($values['nama_hotel']).';'.strtoupper($values['city']).';';
							$qty = str_replace(" Night(s)", "", $values['lama_menginap']);

							if ($values['id_akomodasi'] == '' || $values['id_akomodasi'] == null) {
								$expense = New HrExpenseRequest();
							}else{
								$expense = HrExpenseRequest::where('id_expense_request',$values['id_akomodasi'])->first();
							}
							$expense -> id_cash_advance = $request->id_cashadvance_cash;
							$expense -> id_product = $values['product_akomodasi'];
							$expense -> id_uom = $idUom->id_uom;
							$expense -> description = $dec_akomodasi;
							$expense -> notes = $notes_akomodasi;
							$expense -> id_branch = $values['branch'];
							$expense -> qty = $qty;
							$expense -> unit_price = 0;
							$expense -> status = 'A';
							$expense -> id_company = session('id_company');
							$expense -> created_by = session('id_user');
							$expense -> updated_by = session('id_user');
							$expense -> save();
						}
					}
				}else{
					if($request->buttonType != 'reschedule'){						
							$noWith = HrOfficialTravel::no_with_del('ACD',$request->id_cashadvance_cash);
							if(count($noWith) > 0){
								foreach($noWith as $key=>$val){
									$x[] = $val->id_expense_request;					
								}
								$data_acd = [
									'status' => 'I',
									'update_date' => date('Y-m-d H:i:s'),
									'updated_by' => session('id_user'),
								];
								$hapus_expense_request = HrExpenseRequest::whereIn('id_expense_request',$x)->update($data_acd);
							//	$hapus_expense_request->status = "I";
							//	$hapus_expense_request->save();
							}
					//	return response()->json(['status'=>'null','message'=>'Add at least 1 Accomodation Detail.','title'=>'Accomodation Detail']);
					}
				}
			}
			else{
				if($request->id_cashadvance_cash != null){
					if($request->buttonType != 'reschedule'){
						$noWith = HrOfficialTravel::no_with_del('ACD',$request->id_cashadvance_cash);
						if(count($noWith) > 0){
							foreach($noWith as $key=>$val){
								$x[] = $val->id_expense_request;					
							}
							$hapus_expense_request_transport = HrExpenseRequest::whereIn('id_expense_request',$x)->first();
							$hapus_expense_request_transport->status = "I";
							$hapus_expense_request_transport->save();	
						}
					}
				}
			}
			if (!empty($request->id_akomodasi_del)) {
				$id_akomodasi_del = explode(",", $request->id_akomodasi_del);
				$hapus_expense_request_akomodasi = HrExpenseRequest::whereIn('id_expense_request',$id_akomodasi_del)->first();
				$hapus_expense_request_akomodasi->status = "I";
				$hapus_expense_request_akomodasi->save();
			}
			DB::commit();
			return response()->json(['status'=>'true','data'=>$data,'message'=>'Official Travel Edit Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Official Travel !! [' . $e->getMessage() . ']']);
		}
	}
	public function action_offtrave(Request $request)
	{	
		try {
			DB::beginTransaction();			
			$travel = HrOfficialTravel::where('id_official_travel',$request->id_official_travel)->first();
			$transaction = HrApprovalTransaction::where('id_source_transaction',$request->id_official_travel)
			->where('id_approval_transaction',$request->id_approval_transaction)
			->where('source_transaction_type','Official_Travel')
			->first();
			$employee = Employee::where('id_employee',$transaction['id_employee_approval'])->first();
			$jobGrade = DB::selectOne("SELECT mjg.id_job_grade, mjg.job_level
						FROM hr_employee he
						JOIN master_position_detail mpd
						ON he.id_employee = mpd.id_employee AND mpd.secondary_position = false
						JOIN master_position_routing mpr
						ON mpd.id_position_routing = mpr.id_routing
						JOIN master_job_grade mjg 
						ON mpr.id_job_grade = mjg.id_job_grade
						WHERE he.id_user = ".session('id_user')." AND he.status = 'A' AND mjg.job_level = 1"
			);
			if($jobGrade == null) {
				$status_approval = HrOfficialTravel::action_status_approval($request,$travel['travel_status']);
			} else {
				$status_approval = DB::selectOne("SELECT 
							id_general_data as id_general_data
							-- description text
							FROM master_general_data where status = 'A' AND code='Approved' and id_company =" . session('id_company'))->id_general_data;
			}
			$travel -> id_approval_status = $status_approval;
			// dd($request->type_action, $travel['travel_status'], $status_approval, $travel);
			$getTrans = HrApprovalTransaction::where('id_source_transaction',$request->id_official_travel)
			->where('source_transaction_type','Official_Travel')
			->get();
			if($request->type_action == 'req_cancel'){
				$travel -> travel_status = 'Cancel';
				$travel -> is_verified = false;
				foreach($getTrans as $key=>$valTrans){
					$valTrans -> updated_by = null;
					if($jobGrade && $jobGrade->job_level == 1) {
						$travel -> is_verified = true;
						$valTrans -> updated_by = session('id_user');
					//	dd($travel);
					}
					$valTrans -> id_approval_status = $status_approval;
					$valTrans -> save();
				}
				
			}
			else if($request->type_action == 'Cancel' && $travel['travel_status'] == 'Cancel'){
				foreach($getTrans as $key=>$valTrans){
					$travel -> travel_status = 'Onschedule';
					$valTrans -> updated_by = $employee['id_user'];
					$valTrans -> id_approval_status = $status_approval;
					$valTrans -> save();
				}
			}
			else{
				foreach($getTrans as $key=>$valTrans){
					$valTrans -> updated_by = $employee['id_user'];
					$valTrans -> id_approval_status = $status_approval;
					$valTrans -> save();
				}
			}
			$travel -> save();			
			
			// send mail
			if ($request->type_action == 'Approve') {
				$result = HrOfficialTravel::send_mail($request);
				if(!is_null(@$result['mobile_phone']) || !empty(@$result['mobile_phone'])){
					$param = (object)$result;
					$message = $this->MessageController->messageTemplateTravel($param);
					$sendMessage = $this->MessageController->sendWhatsapp($message, @$result['mobile_phone']);
				}
				\Mail::to($result['private_mail'])->send(new \App\Mail\SendMail($result));
			}
			DB::commit();
			return response()->json(['status'=>'true', 'data'=>$travel, 'message'=>'Official Travel Action Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Action Official Travel !! [' . $e->getMessage() . ']']);
		}
	}
	public function print_offtrave($id_official_travel)
	{
		$idDecrypt = Crypt::decrypt($id_official_travel);
		$result = HrOfficialTravel::get_print($idDecrypt);
	/*	$dt = $result['data'][0];
		$transport = $result['transport'];
		$akomodasi = $result['akomodasi'];
		$cashadvance = $result['cashadvance'];
		$last_days = $result['last_days'];
		$day_plus = $result['day_plus'];	
	*/
		$qrcode_employee = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result['data'][0]->reference_number.' ('.$result['data'][0]->name.' / '.$result['data'][0]->nik_employee.')'));
		$qrcode_approver = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result['data'][0]->reference_number.' ('.$result['data'][0]->name_approval_request.' / '.$result['data'][0]->nik_employee_approval.')'));
		$data = [
            'dt' => $result['data'][0],
			'transport' => $result['transport'],
			'akomodasi' => $result['akomodasi'],
			'cashadvance' => $result['cashadvance'],
			'last_days' => $result['last_days'],
			'day_plus' => $result['day_plus'],
            'qrcode_employee' => $qrcode_employee,
            'qrcode_approver' => $qrcode_approver,
        ];
	//	$pdf=PDF::loadview('cash_advance.official_travel.print',compact('dt','transport','akomodasi','cashadvance','last_days','day_plus','qrcode'))->setPaper('A4','potrait');
		$pdf = PDF::loadView('cash_advance.official_travel.print', $data)->setPaper('A4','potrait');
		return $pdf->stream();
	}
	
	public function mail_success(Request $request)
	{
		if(env('APP_ENV') != "production") {
			return response()->json();
		}
		$req_array = json_decode(json_encode($request['source']), FALSE);
		$result = HrOfficialTravel::send_mail($req_array);
			if(!is_null(@$result['mobile_phone']) || !empty(@$result['mobile_phone'])){
				$param = (object)$result;
				$message = $this->MessageController->messageTemplateTravel($param);
				$sendMessage = $this->MessageController->sendWhatsapp($message, @$result['mobile_phone']);
			}
			\Mail::to($result['private_mail'])->send(new \App\Mail\SendMail($result));
		return response()->json($result);
	}
	
	public function get_trans_view(Request $request) {
		$data = [
            'id_official_travel' => $request->id_official_travel
        ];	
        $result = HrOfficialTravel::get_trans_view($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_accomodation_view(Request $request) {
		$data = [
            'id_official_travel' => $request->id_official_travel
        ];	
        $result = HrOfficialTravel::get_accomodation_view($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_cash_view(Request $request) {
		$data = [
            'id_official_travel' => $request->id_official_travel
        ];	
        $result = HrOfficialTravel::get_cash_view($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }

	public function getProjectApproval(Request $request) {
		$request->validate([
			'action' => 'required'
		]);
		$result = null;
		if($request->action == 'company') {
			$result = DB::table('master_company')->where('company_type', 'corporate')->where('id_company', session('id_company'))->orderBy('id_company')->get(['id_company as id', 'company_name as text']);
		} elseif($request->action == 'department') {
			$result = DB::table('master_department')->where('id_company', $request->id_company ?? session('id_company'))->get(['id_dept as id', 'description as text']);
		} elseif($request->action == 'position_routing' && $request->id_department) {
			$sql = "SELECT
						mpr.id_routing as id,
						mpr.description as text,
						mpr.job_description_detail 
					FROM master_job_position mjp
					JOIN master_position_routing mpr ON mjp.id_position = mpr.id_position
					WHERE mjp.id_dept = ? AND mpr.status = 'A'
					";
			$result = DB::select($sql, [$request->id_department]);
		} elseif($request->action == "employee" && $request->id_position_routing) {
			$sql = "SELECT 
						hre.id_employee as id,
						hre.name as text
					FROM master_position_detail mpd 
					JOIN hr_employee hre ON mpd.id_employee = hre.id_employee
					WHERE mpd.id_position_routing = ? 
					AND mpd.status = 'A' 
					AND hre.status = 'A' 
					AND hre.id_user != ?
					";
			$result = DB::select($sql, [$request->id_position_routing, session('id_user')]);
		} elseif($request->action == "approved_by" && $request->id_official_travel) {
			$sql = "SELECT 
						hrot.id_approval_request as id,
						hre.name as text
					FROM hr_official_travel hrot
					JOIN hr_employee hre ON hrot.id_approval_request = hre.id_employee
					WHERE id_official_travel = ?
					";
			$result = DB::select($sql, [$request->id_official_travel]);
		}
		return response()->json($result);
	}
}
