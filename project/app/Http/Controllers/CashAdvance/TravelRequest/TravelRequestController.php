<?php

namespace App\Http\Controllers\CashAdvance\TravelRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\CashAdvance\OfficialTravel\HrOfficialTravel;
use App\Models\CashAdvance\OfficialTravel\HrExpenseRequest;
use App\Models\CashAdvance\OfficialTravel\HrCashAdvance;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\CashAdvance\TravelRequest\TravelRequest;
use App\Exports\BgenExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class TravelRequestController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = TravelRequest::get_travel_request();
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
				$button .= '<button type="button" name="save" more_type="Save" more_id="'.$data->id_official_travel.'" id="btn-save-'.$data->id_official_travel.'" disabled="" class="btn-save btn btn-success btn-sm" title="Save"><span class="fas fa-save" style="color:white;"></span></button> ';
				$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
				if ($data->is_verified == NULL) {
					$button .= ' <button type="button" name="lock" more_type="Lock" more_id="' . $data->id_official_travel . '" class="btn-lock btn btn-success btn-sm" title="Submit"><span class="fas fa-check" style="color:white;"></span></button>';
				}
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('cash_advance.travel_request.index');
	}
	public function detail_view($id_official_travel)
	{
		$data = TravelRequest::get_view_detail($id_official_travel);
		$dataHeader = HrOfficialTravel::where('id_official_travel',$id_official_travel)->first();		
		$header = $dataHeader['travel_status'];
		$transport = $data['transport'];
		$akomodasi = $data['akomodasi'];
		return response()->json(['header'=>$header,'transport'=>$transport,'akomodasi'=>$akomodasi]);
	}
	public function edit(Request $request)
	{		
	//	dd($request->all());		
		$validationRules = [];
		$validationMessage = [];
		if (isset($request->transport)) {
			foreach ($request->transport as $key => $value) {
				$getProduct = TravelRequest::get_product($request['id_travel'],'TST',$value['id_transport']);
				if(count($getProduct) > 0){
					if (isset($value['pilih_transport'])) {
						if ($value['pilih_transport'] == 'on') {
							$validationRules += [
								'transport.'.$key.'.price_transport' => 'required'
							];
							$validationMessage += [
								'transport.'.$key.'.price_transport.required' => 'The Price field is required.'
							];
						}
					}
				}
			}
			$validationRules += [
				'transport.*.jenis_transportasi' => 'required',
				'transport.*.transport_name' => 'required',
				'transport.*.from' => 'required',
				'transport.*.to' => 'required',
				'transport.*.date_transport' => 'required',
				'transport.*.time_transport' => 'required'
			];
			$validationMessage += [
				'transport.*.jenis_transportasi.required' => 'The Transport Type field is required.',
				'transport.*.transport_name.required' => 'The Transport Name field is required.',
				'transport.*.from.required' => 'The From field is required.',
				'transport.*.to.required' => 'The To field is required.',
				'transport.*.date_transport.required' => 'The Date field is required.',
				'transport.*.time_transport.required' => 'The Time field is required.'
			];
		}
		if (isset($request->akomodasi)) {
				foreach ($request->akomodasi as $key => $values) {
					$getProduct = TravelRequest::get_product($request['id_travel'],'ACD',$values['id_akomodasi']);
					if(count($getProduct) > 0){
						if (isset($values['pilih_akomodasi'])) {
							if ($values['pilih_akomodasi'] == 'on') {
								$validationRules += [
									'akomodasi.'.$key.'.price_akomodasi' => 'required'
								];
								$validationMessage += [
									'akomodasi.'.$key.'.price_akomodasi.required' => 'The Price field is required.'
								];
							}
						}
					}
				}
				$validationRules += [
					'akomodasi.*.product_akomodasi' => 'required',
					'akomodasi.*.nama_hotel' => 'required',
					'akomodasi.*.city' => 'required',
					'akomodasi.*.start_end_akomodasi' => 'required'
				];
				$validationMessage += [
					'akomodasi.*.product_akomodasi.required' => 'The Name of Hotel field is required.',
					'akomodasi.*.nama_hotel.required' => 'The Product field is required.',
					'akomodasi.*.city.required' => 'The City field is required.',
					'akomodasi.*.start_end_akomodasi.required' => 'The Start End Date field is required.'
				];				
		}
		$request->validate($validationRules, $validationMessage);

		try {
			DB::beginTransaction();
			if (isset($request->transport)) {
				foreach ($request->transport as $key => $value) {
					$getProduct = TravelRequest::get_product($request['id_travel'],'TST',$value['id_transport']);
					if(count($getProduct) > 0){
						if (isset($value['pilih_transport'])) {
							if($value['pilih_transport'] == 'on'){
								$dec_transport = $value['date_transport'].';'.$value['time_transport'].';';
								$notes_transport = $value['transport_name'].';'.strtoupper($value['from']).';'.strtoupper($value['to']).';';
								/*
								if (empty($value['branch'])) {
									$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';';
								}else{
									$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';'.$value['branch'].';';
								}
								*/
								if (!empty($value['price_transport'])) {
									$unit_price = preg_replace("/[^aZ0-9]/", "", $value['price_transport']);
								}else{
									$unit_price = 0;
								}
								$expense = HrExpenseRequest::where('id_expense_request',$value['id_transport'])->first();
								$expense -> id_product = $value['jenis_transportasi'];
								$expense -> description = $dec_transport;
								$expense -> id_branch = $value['branch'];
								$expense -> notes = $notes_transport;
								$expense -> id_company = session('id_company');
								$expense -> unit_price = $unit_price;
								if (isset($value['pilih_transport'])) {
									$expense -> is_verified = true;
								}else{
									$expense -> is_verified = false;
								}
								$expense -> save();
							}
						}
					}
				}
			}
			if (isset($request->akomodasi)) {
				foreach ($request->akomodasi as $key => $values) {
					$getProduct = TravelRequest::get_product($request['id_travel'],'ACD',$values['id_akomodasi']);
					if(count($getProduct) > 0){
						if (isset($values['pilih_akomodasi'])) {
							if ($values['pilih_akomodasi'] == 'on') {
								$dec_akomodasi = $values['start_end_akomodasi'].';';
								$notes_akomodasi = strtoupper($values['nama_hotel']).';'.strtoupper($values['city']).';';
								$qty = str_ireplace('Night(s)', '', $values['lama_menginap']);
								if (!empty($values['price_akomodasi'])) {
									$unit_price = preg_replace("/[^aZ0-9]/", "", $values['price_akomodasi']);
								}else{
									$unit_price = 0;
								}
								$expense = HrExpenseRequest::where('id_expense_request',$values['id_akomodasi'])->first();
								$expense -> id_product = $values['product_akomodasi'];
								$expense -> description = $dec_akomodasi;
								$expense -> notes = $notes_akomodasi;
								$expense -> id_branch = $values['branch'];
								$expense -> qty = $qty;
								$expense -> status = 'A';
								$expense -> id_company = session('id_company');
								$expense -> unit_price = $unit_price;
								if (isset($values['pilih_akomodasi'])) {
									$expense -> is_verified = true;
								}else{
									$expense -> is_verified = false;
								}
								$expense -> save();
							}
						}
					}
				}
			}
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Travel Request Edit Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Travel Request !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_view($id_official_travel)
	{
		$data = TravelRequest::get_view_header($id_official_travel);
		return response()->json($data);
	}
	public function lock_submit($id_official_travel)
	{
		try {
			DB::beginTransaction();
			$cek = HrOfficialTravel::leftJoin('hr_cash_advance','hr_cash_advance.id_official_travel','=','hr_official_travel.id_official_travel')
			->leftJoin('hr_expense_request','hr_expense_request.id_cash_advance','=','hr_cash_advance.id_cash_advance')
			->leftJoin('inventory.master_product','inventory.master_product.id_product','=','hr_expense_request.id_product')
			->leftJoin('inventory.master_product_categories','inventory.master_product_categories.id_product_categories','=','inventory.master_product.id_product_categories')
			->select(
				\DB::RAW('hr_expense_request.is_verified as is_verified')
			)
			->where('hr_official_travel.id_official_travel',$id_official_travel)
			->where('hr_cash_advance.id_official_travel',$id_official_travel)
			->where('hr_expense_request.is_verified',false)
			->where('inventory.master_product_categories.code','!=','CSM')
			->where('hr_expense_request.status', 'A')
			->get();
			if (count($cek) > 0) {
				return response()->json(['status'=>'null','message'=>'Transport and Accommodation prices must be complete.']);
			}else{
				$data = HrOfficialTravel::where('id_official_travel',$id_official_travel)->first();
				$data -> is_verified = true;
				$data -> save();
			}
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Official Travel Submit Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Submit Official Travel !! [' . $e->getMessage() . ']']);
		}
	}

	public function index_bigen(Request $request)
	{
		if ($request->ajax()) {
			$data = TravelRequest::get_travel_request_bigen($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('action', function ($data) {
				$button = '<button type="button" name="edit" more_type="Edit" more_id="' . $data->id_official_travel . '" expense-request-id="'.$data->id_expense_request.'" class="btn-edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit" style="color:white;"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('cash_advance.bigen_travel.index');
	}
	public function export_excel()
	{
		return Excel::download(new BgenExport, 'BgenExport.xlsx');
	}

	public function get_expense_data(Request $request) {
		$request->validate([
			'expense_request_id' => 'required'
		]);

		if ($request->ajax()) {
			$data = TravelRequest::get_travel_request_bigen_data($request->expense_request_id);
			return response()->json($data);
		}
	}

	public function edit_expense_data_price(Request $request) {
		$request->validate([
			'expense_request_id' => 'required',
			'unit_price' => 'required|numeric|min:0'
		]);

		if ($request->ajax()) {
			try {
				DB::beginTransaction();
				// $data = DB::table('hr_expense_request')->where('id_expense_request', $request->expense_request_id)->first();
				$data = DB::table('hr_expense_request')
				->where('id_expense_request', $request->expense_request_id)
				->limit(1)
				->update([
					'unit_price' => $request->unit_price,
					'status' => $request->status
				]);
				DB::commit();
				return response()->json($data);
			} catch(\Exception $e) {
				DB::rollBack();
				return response()->json($e, 500);
			}
		}
	}
}