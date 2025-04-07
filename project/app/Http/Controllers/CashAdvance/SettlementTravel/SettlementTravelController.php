<?php

namespace App\Http\Controllers\CashAdvance\SettlementTravel;

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
use App\Models\CashAdvance\OfficialTravel\HrCashPayment;
use App\Models\CashAdvance\OfficialTravel\HrCashRefund;
use App\Models\CashAdvance\OfficialTravel\HrSettlementExpense;
use App\Models\Organization\MasterOrganization\MasterBranch;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;  
use Exception;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SettlementTravelController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
			$data = HrOfficialTravel::getSettlementOfficialTravelData(session('id_user'));
            // dd($data);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
            ->addColumn('is_approved_by_chief_desc', function($data) {
                if($data->is_approved_by_chief == null) {
                    return 'Pending';
                } else if($data->is_approved_by_chief == true) {
                    return 'Approved';
                } else return 'Rejected';
            })
            ->addColumn('is_approved_by_finance_desc', function($data) {
                if($data->is_approved_by_finance == null) {
                    return 'Pending';
                } else if($data->is_approved_by_finance == true) {
                    return 'Approved';
                } else return 'Rejected';
            })
            ->addColumn('submitted', function($data) {
                return $data->is_validate ? "Yes" : "No";
            })
			->addColumn('action', function($data) {
				$button = "";
                $button .= '<button type="button" action="view" class="button-view btn btn-primary btn-sm" id-official-travel="'.$data->id_official_travel.'" title="View"><span class="fas fa-edit text-white"></span></button>';
                if($data->is_approved_by_chief) {
                    $button .= '&nbsp;<button type="button" action="print" class="button-print btn btn-success btn-sm" id-official-travel="'.Crypt::encrypt($data->id_official_travel).'" title="Print"><span class="fa fa-file-pdf text-white"></span></button>';
                }
				// if ($data->status_approval != 'Rejected' OR $data->status_approval != 'Cancel') {
				// 	$now = new DateTime(date('Y-m-d'));
				// 	$startDate = new DateTime($data->start_date);
				// 	$cancel_date = $startDate->diff($now)->days;
				// 	$realDate = Carbon::parse($data->start_date)->format('Y-m-d');
				// 	if ($data->status_approval == 'New') {
				// 		$button .='<button type="button" name="submit" id="btn-approve-'.$data->id_official_travel.'" more_type="Approve" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" class="btn-action btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
				// 		$button .= '<button type="button" more_type="Edit" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_official_travel.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				// 		$button .= '<button type="button" name="cancel" more_type="Cancel" class="btn-action btn btn-danger btn-sm" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" title="Cancel"><span class="fa fa-close"></span></button>';
				// 	}elseif ($data->status_approval == 'Request_Approval') {
				// 		$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button> ';
				// 		if( $data->travel_status != 'Reschedule'){
				// 			$button .= '<button type="button" name="cancel" more_type="Cancel" class="btn-action btn btn-danger btn-sm" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" title="Cancel"><span class="fa fa-close"></span></button>';
				// 		}
				// 	}elseif ($data->status_approval == 'Approved' && $data->travel_status == 'Cancel') {
				// 		$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
				// 	}elseif ($data->status_approval == 'Approved') {
				// 		$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
				// 		$button .= ' <a href="'.route('print_offtrave',$data->id_official_travel).'" target="_blank" name="print" id="" class="btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></a> ';
				// 		if($cancel_date >= 2 && date('Y-m-d') < $realDate){
				// 			$button .= '<button type="button" name="reschedule" more_id="' . $data->id_official_travel . '" more_type="reschedule" class="reschedule btn btn-info btn-sm" title="Reschedule"><span class="far fa-calendar-alt"></span></button>';
				// 			$button .= '&nbsp;<button type="button" name="req_cancel" more_id="' . $data->id_official_travel . '" more_type="req_cancel" class="btn-action btn btn-request-cancel btn-sm" title="Req Cancel" more_transaction="'.$data->id_approval_transaction.'"><span class="fa fa-window-close fa-lg"></span></button>';
				// 		}
				// 	}elseif ($data->status_approval == 'Revised') {
				// 		$button .= '<button type="button" more_type="Edit" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_official_travel.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				// 		$button .= '<button type="button" name="cancel" more_type="Cancel" class="btn-action btn btn-danger btn-sm" more_id="'.$data->id_official_travel.'" more_transaction="'.$data->id_approval_transaction.'" title="Cancel"><span class="fa fa-close"></span></button> ';
				// 	}elseif ($data->status_approval == 'Cancel' || $data->status_approval == 'Rejected') {
				// 		$button .= '<button type="button" name="view" more_type="View" more_id="' . $data->id_official_travel . '" class="btn-view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button> ';
				// 	}else{
				// 		$button .= '';
				// 	}
				// }
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
        return view('cash_advance.settlement_travel.index');
    }

    public function getData(Request $request) {
        if($request->id_product && $request->id_region && $request->id_branch) {
            return HrSettlementExpense::getBudget($request);
        }

        $request->validate([
            'id_official_travel' => 'required'
        ]);
        
        $findCashAdvance = DB::select("SELECT * FROM hr_cash_advance WHERE id_official_travel = ?", [$request->id_official_travel]);
        // dd($findCashAdvance);
        if(count($findCashAdvance) < 1) {
            $this->generateCashAdvance($request);
        }

        $cashAdvance = HrSettlementExpense::getData($request->id_official_travel);
        $officialTravel = HrOfficialTravel::getSettlementOfficialTravelData(session('id_user'), $request->id_official_travel);
        $products = HrCashAdvance::getProducts();
        $fullProducts = HrCashAdvance::getProducts(true);
        $transportAccommodation = HrSettlementExpense::getTransportAndAccommodation($officialTravel[0]->id_cash_advance);
        $settlement = HrSettlementExpense::getSettlements($officialTravel[0]->id_cash_advance);
        $currencies = HrSettlementExpense::getCurrencies();
        $refunds = HrCashRefund::getCashRefund($officialTravel[0]->id_cash_advance);
        $payments = HrCashPayment::getPayments($officialTravel[0]->id_cash_advance);
        $employeeBank = HrCashRefund::getEmployeeBank();
        $companyBank = HrCashRefund::getCompanyBank();
        $bank = HrCashRefund::getBanks();
        $regions = HrSettlementExpense::getRegions($officialTravel[0]->id_cash_advance);
        return response()->json([
            "data" => $cashAdvance,
            "official_travel" => $officialTravel[0],
            "products" => $products,
            "full_products" => $fullProducts,
            "transport_accommodation" => $transportAccommodation,
            "settlement" => $settlement,
            "refund" => $refunds,
            "payment" => $payments,
            "currencies" => $currencies,
            "bank" => $bank,
            "employee_bank" => $employeeBank,
            "company_bank" => $companyBank,
            "regions" => $regions
        ]);
    }

    protected function validateInput(Request $request) {
        $rules = [
            'id_cash_advance' => 'required',
            'submit_type' => 'nullable',
            'settlement.*.product' => 'required',
            'settlement.*.description' => 'required|string',
            'settlement.*.notes' => 'nullable|string',
            'settlement.*.qty' => 'required',
            'settlement.*.price' => 'required',
            'settlement.*.id_settlement' => 'nullable',
            'settlement.*.attachment' => 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
            'settlement.*.currency' => 'required',
            'settlement.*.currency_rate' => 'required',
            'settlement.*.unit' => 'required',
            'settlement.*.start_end' => 'required_without:settlement.*.id_settlement',
            // 'settlement.*.region' => 'required',
            'settlement.*.branch' => 'required',
            'refund.*.bank_from' => 'required',
            'refund.*.bank_to' => 'required',
            'refund.*.bank_account' => 'required',
            'refund.*.amount' => 'required',
            'refund.*.id_refund' => 'nullable',
            // 'refund.*.description' => 'required',
            'refund.*.notes' => 'nullable|string',
            'refund.*.attachment' => 'required_without:refund.*.id_refund|mimes:jpg,jpeg,png,webp|max:2048',
        ];
        $message = [
            'settlement.*.attachment.required_without' => 'Attachment is required',
            'settlement.*.start_end.required' => 'Settlement date is required',
            'settlement.*.product.required' => 'Settlement product is required',
            'settlement.*.description.required' => 'Settlement description is required',
            'settlement.*.branch.required' => 'Branch is required',
            'settlement.*.currency_rate.required' => 'Settlement currency rate is required',
            'settlement.*.qty.required' => 'Settlement product quantity is required',
            'settlement.*.price.required' => 'Settlement product unit price is required',
            'refund.*.attachment.required_without' => 'Attachment is required',
            'refund.*.amount.required' => 'Refund amount is required',
            'refund.*.bank_account.required' => 'Refund bank account is required',
        ];
        if(!HrCashAdvance::findOrFail($request->id_cash_advance)->attachment_settlement) {
            $rules['attachment_settlement'] = 'required|mimes:pdf|max:5120';
        } else {
            $rules['attachment_settlement'] = 'nullable|mimes:pdf|max:5120';
        }
        $request = SanitizedForm::sanitizeStringInput($request, $rules);
        return $request->validate($rules, $message);
    }

    public function update(Request $request) {
        $officialTravel = "SELECT hot.reference_number FROM hr_cash_advance hca JOIN hr_official_travel hot ON hca.id_official_travel = hot.id_official_travel WHERE hca.id_cash_advance = ?";
        $officialTravel = str_replace("/", "_", DB::selectOne($officialTravel, [$request->id_cash_advance])->reference_number);
        $this->validateInput($request);
        $cashAdvance = HrCashAdvance::findOrFail($request->id_cash_advance);
        $settlementIsValidate = $request->submit_type == "submit";
        try {
            DB::beginTransaction();

            if(!$cashAdvance->attachment_settlement || ($request->attachment_settlement)) {
                $fileName = 'settlement_'.$request->id_cash_advance.'_'.uniqid().'.'.$request->attachment_settlement->getClientOriginalExtension();
                $path = 'upload/cash_advance/settlement/'.$officialTravel."/";
                $cashAdvance->attachment_settlement = $path.$fileName;
                $request->file('attachment_settlement')->storeAs($path, $fileName, 'public');
            }

            if($request->settlement) {
                $cashAdvance->is_validate = $settlementIsValidate;
                foreach ($request->settlement as $settlement) {
                    if(array_key_exists('id_settlement', $settlement) && $settlement['id_settlement']) {
                        //update
                        $data = HrSettlementExpense::findOrFail($settlement['id_settlement']);
                        $data->id_product = $settlement['product'];
                        $data->description = strtoupper($settlement['description']);
                            // 'notes' => $settlement['notes'],
                        $data->qty = $settlement['qty'];
                        $data->id_currency_settlement_expense = $settlement['currency'];
                        $data->currency_rate_settlement_expense = $settlement['currency_rate'];
                        $data->id_uom = $settlement['unit'];
                        $data->id_branch = $settlement['branch'];
                        $data->id_uom = $settlement['unit'];
                        $data->settlement_date = $settlement['start_end'];
                        $data->is_validate = $settlementIsValidate;
                            // 'total_approval_amount' => 0,
                        $data->unit_price = str_replace(".", "", $settlement['price']);
                        if($settlementIsValidate && $cashAdvance->id_employee == $cashAdvance->id_approval_request) {
                            $data->approved_by_chief = $cashAdvance->id_approval_request;
                            $data->is_verified_by_chief = true;
                            $data->approval_price_by_chief = str_replace(".", "", $settlement['price']);
                            $data->save();
                        }
                        if(array_key_exists('attachment', $settlement)) {
                            $fileName = 'settlement_'.$request->id_cash_advance.'_'.uniqid().'.'.$settlement['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/settlement/'.$officialTravel."/";
                            Storage::disk('public')->delete($data->attachment);
                            // $del = Storage::disk('public')->delete('app/public/'.$data->attachment);
                            $settlement['attachment']->storeAs($path, $fileName, 'public');
                            $data->attachment = $path.$fileName;
                        }
                        $data->save();
                    } else {
                        //create
                        if(@$settlement['attachment']) {
                            $fileName = 'settlement_'.$request->id_cash_advance.'_'.uniqid().'.'.$settlement['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/settlement/'.$officialTravel."/";
                            Storage::makeDirectory('public/'.$path, 0777, true, true);
                            Image::make($settlement['attachment'])->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save(storage_path('app/public/'.$path.$fileName));
                        }
                        // $settlement['attachment']->storeAs($path, $fileName, 'public');
                        $data = HrSettlementExpense::create([
                            'id_cash_advance' => $request->id_cash_advance,
                            'id_product' => $settlement['product'],
                            'description' => strtoupper($settlement['description']),
                            // 'notes' => $settlement['notes'],
                            'qty' => $settlement['qty'],
                            'id_currency_settlement_expense' => $settlement['currency'],
                            'currency_rate_settlement_expense' => $settlement['currency_rate'],
                            'id_uom' => $settlement['unit'],
                            'id_branch' => $settlement['branch'],
                            'settlement_date' => $settlement['start_end'],
                            // 'total_approval_amount' => 0,
                            'unit_price' => str_replace(".", "", $settlement['price']),
                            'attachment' => key_exists('attachment', $settlement) ? $path.$fileName : null,
                            'is_validate' => $settlementIsValidate,
                            'status' => 'A',
                            'id_company' => session('id_company'),
                            'created_by' => session('id_user'),
                        ]);
                        if($settlementIsValidate && $cashAdvance->id_employee == $cashAdvance->id_approval_request) {
                            $data->approved_by_chief = $cashAdvance->id_approval_request;
                            $data->is_verified_by_chief = true;
                            $data->approval_price_by_chief = str_replace(".", "", $settlement['price']);
                            $data->save();
                        }
                    }
                }
            }

            if($request->refund) {
                foreach ($request->refund as $refund) {
                    if(array_key_exists('id_refund', $refund) && $refund['id_refund']) {
                        //update
                        $data = HrCashRefund::findOrFail($refund['id_refund']);
                        $companyBank = DB::selectOne('SELECT * FROM accounting.master_bank_account WHERE id_bank_account = ?', [$refund['bank_to']]);
                        if(!$companyBank) {
                            throw new \Exception("Company bank not found!");
                        }
                        if(array_key_exists('attachment', $refund)) {
                            $fileName = 'refund_'.$request->id_cash_advance.'_'.uniqid().'.'.$refund['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/cash_refund/';
                            Storage::disk('public')->delete($data->attachment);
                            $refund['attachment']->storeAs($path, $fileName, 'public');
                            $data->attachment = $path.$fileName;
                        }
                        // $data->description = $refund['description'];
                        // $data->notes = $refund['notes'];
                        $data->id_bank_to = $companyBank->id_bank;
                        $data->bank_to_account = $companyBank->account_number;
                        $data->id_bank_to_account = $companyBank->id_bank_account;
                        $data->id_bank_from = $refund['bank_from'];
                        $data->bank_from_account = $refund['bank_account'];
                        // $data->id_settlement_expense = $refund['id_settlement'];
                        $data->save();
                    } else {
                        //create
                        $fileName = 'refund_'.$request->id_cash_advance.'_'.uniqid().'.'.$refund['attachment']->getClientOriginalExtension();
                        $path = 'upload/cash_advance/cash_refund/';
                        $companyBank = DB::selectOne('SELECT * FROM accounting.master_bank_account WHERE id_bank_account = ?', [$refund['bank_to']]);
                        if(!$companyBank) {
                            throw new \Exception("Company bank not found!");
                        }
                        // try {
                            $id_currency_refund = DB::selectOne('SELECT mc.* FROM master_company mc WHERE mc.id_company = ?', [session('id_company')])->id_currency;
                            Storage::makeDirectory('public/'.$path, 0777, true, true);
                            Image::make($refund['attachment'])->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save(storage_path('app/public/'.$path.$fileName));
                            // $refund['attachment']->storeAs($path, $fileName, 'public');
                            $hrca = HrCashRefund::create([
                                'id_cash_advance' => $request->id_cash_advance,
                                // 'id_settlement_expense' => $refund['id_settlement'],
                                'description' => '',
                                'notes' => '',
                                'id_bank_to' => $companyBank->id_bank,
                                'bank_to_account' => $companyBank->account_number,
                                'id_bank_to_account' => $companyBank->id_bank_account,
                                'id_bank_from' => $refund['bank_from'],
                                'bank_from_account' => $refund['bank_account'],
                                'id_currency_cash_refund' => $id_currency_refund,
                                'currency_rate_refund' => 1,
                                'total_amount' => str_replace(".", "", $refund['amount']),
                                'id_company' => session('id_company'),
                                'created_by' => session('id_user'),
                                'attachment' => $path.$fileName,
                            ]);
                        // } catch(Exception $e) {
                        //     return response()->json($e->getMessage(), $e->getCode());
                        // }
                    }
                }
            }

            $toDelete = explode(",", $request->id_settlement_del);
            foreach($toDelete as $delete) {
                if($delete != "") {
                    $data = HrSettlementExpense::findOrFail($delete);
                    $data->status = "I";
                    $data->save();
                }
            }
            // $toDeleteRefund = explode(",", $request->id_refund_del);
            // foreach($toDeleteRefund as $delete) {
            //     if($delete != "") {
            //         $data = HrCashRefund::findOrFail($delete);
            //         $data->status = "I";
            //         $data->save();
            //     }
            // }
            $cashAdvance->save();
            DB::commit();
            return response()->json([
                "message" => "Data saved successfully!"
            ]);
        } catch(\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function getOfficialTravelWithoutCashAdvance() {
        $data = HrOfficialTravel::getOfficialTravelWithoutCashAdvance(session('id_user'));
        return response()->json([
            "official_travel" => $data
        ]);
    }

    public function generateCashAdvance(Request $request) {
        $request->validate([
            'id_official_travel' => 'required|numeric|exists:hr_official_travel,id_official_travel',
        ]);
        $data = HrOfficialTravel::createCashAdvance($request->id_official_travel);
        return $data;
    }

    public function settlementApproval(Request $request) {
        if ($request->ajax()) {
			$data = HrCashAdvance::getApprovalList(session('id_user'), null, false, $request->approved == "true");
            // HrOfficialTravel::getSettlementOfficialTravelData(session('id_user'));
            // dd($data);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
                $button .= '<button type="button" action="view" class="button-view btn btn-warning btn-sm" id-cash-advance="'.$data->id_cash_advance.'" title="View"><span class="fas fa-eye text-white"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
        return view('cash_advance.settlement_travel.approval.chief');
    }

    public function settlementFinanceApproval(Request $request) {
        if ($request->ajax()) {
            $data = HrCashAdvance::getFinanceApprovalList(session('id_user'), null, $request->status, false, $request->type ?? "finance", "settlement");
			// $data = HrOfficialTravel::getSettlementOfficialTravelData(session('id_user'));
            // dd($data);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
                $button .= '<button type="button" action="view" class="button-view btn btn-warning btn-sm" id-cash-advance="'.$data->id_cash_advance.'" title="View"><span class="fas fa-eye text-white"></span></button>';
                if($data->is_approved_by_chief) $button .= '&nbsp;<button type="button" action="print" class="button-print btn btn-success btn-sm" id-cash-advance="'.Crypt::encrypt($data->id_cash_advance).'" title="Print"><span class="fa fa-file-pdf text-white"></span></button>';
                
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
        return view('cash_advance.settlement_travel.approval.finance', [
            'dataUrl' => route('settlementtravel.approval.finance'),
            'page' => 'settlement_clearing',
            'startDate' => null
        ]);
    }

    public function settlementFinanceCashAdvancePayment(Request $request) {
        $range = $request->range ? explode(' to ', $request->range) : [];
        $startDate = count($range) > 0 ? Carbon::parse($range[0])->startOfDay()->format('Y-m-d H:i:s') : now()->subMonths(3)->startOfDay()->format('Y-m-d H:i:s');
        $endDate = count($range) > 1 ? Carbon::parse($range[1])->startOfDay()->format('Y-m-d H:i:s') : now()->endOfDay()->format('Y-m-d H:i:s');

        if ($request->ajax()) {
            $data = HrCashAdvance::getFinanceApprovalList(session('id_user'), null, $request->status, false, $request->type, "payment-cashadvance", $request->is_paid_by_finance, $startDate, $endDate);
			// $data = HrOfficialTravel::getSettlementOfficialTravelData(session('id_user'));
            // dd($data);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
                $button .= '<button type="button" action="view" class="button-view btn btn-warning btn-sm" id-cash-advance="'.$data->id_cash_advance.'" title="View"><span class="fas fa-eye text-white"></span></button>';
                // $button .= '&nbsp;<button type="button" action="print" class="button-print btn btn-success btn-sm" id-cash-advance="'.$data->id_cash_advance.'" title="Print"><span class="fa fa-file-pdf text-white"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
        return view('cash_advance.settlement_travel.approval.finance', [
            'dataUrl' => route('settlementtravel.payment.finance'),
            'page' => 'expense_payment_finance',
            'startDate' => explode(" ", $startDate)[0]." to ".explode(" ", $endDate)[0],
        ]);
    }

    public function settlementHrPayment(Request $request) {
        if ($request->ajax()) {
            $data = HrCashAdvance::getFinanceApprovalList(session('id_user'));
			// $data = HrOfficialTravel::getSettlementOfficialTravelData(session('id_user'));
            // dd($data);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
                $button .= '<button type="button" action="view" class="button-view btn btn-warning btn-sm" id-cash-advance="'.$data->id_cash_advance.'" title="View"><span class="fas fa-eye text-white"></span></button>';
                if($data->is_approved_by_chief) $button .= '&nbsp;<button type="button" action="print" class="button-print btn btn-success btn-sm" id-cash-advance="'.Crypt::encrypt($data->id_cash_advance).'" title="Print"><span class="fa fa-file-pdf text-white"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
        return view('cash_advance.settlement_travel.approval.hr');
    }

    public function chiefSettlementApproval(Request $request) {
        $request->validate([
            'id_settlement_expense' => 'required',
            'action' => 'required|in:approve,reject,revise',
            'approval_price' => 'required_if:action,approve|min:1',
            'notes' => 'nullable'
        ]);
        DB::beginTransaction();
        try {
            $settlement = HrSettlementExpense::findOrFail($request->id_settlement_expense);
            $cashAdvance = HrCashAdvance::findOrFail($settlement->id_cash_advance);
            $user = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
            if($request->action == "approve") {
                $settlement->is_verified_by_chief = true;
                $settlement->approval_price_by_chief = str_replace(".", "", $request->approval_price);
                $settlement->notes = strtoupper($request->notes);
                $settlement->approved_by_chief = $user->id_employee;
            } else if($request->action == "revise") { 
                $settlement->is_verified_by_chief = null;
                $settlement->notes = strtoupper($request->notes);
                $settlement->is_validate = false;
                $cashAdvance->is_validate = false;
            } else {
                $settlement->is_verified_by_chief = false;
                $settlement->is_verified_by_finance = false;
                $settlement->notes = strtoupper($request->notes);
            }

            if($settlement->save() && $cashAdvance->save()) {
                DB::commit();
                return response()->json([
                    "message" => "Cash advance ".$request->action." by chief success!",
                ]);
            }
            return response()->json([
                "message" => "Cash advance ".$request->action." failed!",
            ], 500);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function financeSettlementApproval(Request $request) {
        $request->validate([
            'id_settlement_expense' => 'required',
            'action' => 'required|in:approve,reject,revise',
            'notes' => 'nullable',
            'approval_price' => 'nullable',
        ]);
        DB::beginTransaction();
        try {
            $settlement = HrSettlementExpense::findOrFail($request->id_settlement_expense);
            $cashAdvance = HrCashAdvance::findOrFail($settlement->id_cash_advance);
            $user = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
            if($request->action == "approve") {
                $settlement->is_verified_by_finance = true;
                $settlement->approval_price_by_finance = str_replace(".", "", $request->approval_price);
                $settlement->notes = strtoupper($request->notes);
                $settlement->approved_by_finance = $user->id_employee;
            } else if($request->action == "revise") { 
                $settlement->is_verified_by_finance = null; 
                $settlement->notes = strtoupper($request->notes);
                $settlement->is_verified_by_chief = null;
                $settlement->approval_price_by_chief = 0;
                $settlement->is_validate = false;
                $cashAdvance->is_validate = false;
            } else {
                $settlement->is_verified_by_chief = false;
                $settlement->is_verified_by_finance = false;
                $settlement->notes = strtoupper($request->notes);
            }

            if($settlement->save() && $cashAdvance->save()) {
                DB::commit();
                return response()->json([
                    "message" => "Cash advance ".$request->action." success!",
                ]);
            }
            return response()->json([
                "message" => "Cash advance ".$request->action." failed!",
            ], 500);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function financeRefundApproval(Request $request) {
        $request->validate([
            'action' => 'required|in:approve,payment_status',
            'payment_status' => 'required_if:action,payment_status|in:Not_Paid,Partially_Paid,Paid',
            'id_refund' => 'required'
        ]);
        $refund = HrCashRefund::findOrFail($request->id_refund);
        if($request->action == "approve") {
            $refund->is_verified = true;
        } else if($request->action == "payment_status") {
            $refund->is_verified = true;
            $refund->payment_status = $request->payment_status;
            // if($refund->payment_status == "Paid") {
            //     $refund->is_paid = true;
            // }
        }

        if($refund->save()) {
            return response()->json([
                "message" => "Data saved successfully!",
            ]);
        }
        return response()->json([
            "message" => "Error saving data!",
        ], 500);
    }

    public function getSettlements(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required',
        ]);
        $settlements = HrSettlementExpense::getSettlements($request->id_cash_advance, true, "finance");
        return DataTables::of($settlements)->addIndexColumn()->toJson();
    }

    public function getApprovalData(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required',
            'type' => 'nullable|in:chief,finance,hr',
            'filter' => 'nullable',
            'ignore_is_validate' => 'nullable'
        ]);
        $filter = $request->filter ?? "Clear";
        $ignoreIsValidate = $request->ignore_is_validate == "1" ? true : false;
        try {
            $expenseRequest = HrSettlementExpense::getData($request->id_cash_advance, false);
            if($request->type && ($request->type == "finance" || $request->type == "hr")) {
                $cashAdvance = HrCashAdvance::getFinanceApprovalList(session('id_user'), $request->id_cash_advance, $filter, $ignoreIsValidate, $request->type);
            } else {
                $approved = null;
                if($request->approved == "true") {
                    $approved = true;
                } else if($request->approved == "false") {
                    $approved = false;
                }
                $cashAdvance = HrCashAdvance::getApprovalList(session('id_user'), $request->id_cash_advance, $ignoreIsValidate, $approved);
            }
            $products = HrCashAdvance::getProducts();
            $fullProducts = HrCashAdvance::getProducts(true);
            $hrProducts = HrCashAdvance::getProducts(true, true);
            $transportAccommodation = HrSettlementExpense::getTransportAndAccommodation($request->id_cash_advance);
            $settlement = HrSettlementExpense::getSettlements($request->id_cash_advance, true, "finance");
            $settlementSummary = HrSettlementExpense::settlementSummary($request->id_cash_advance);
            $currencies = HrSettlementExpense::getCurrencies();
            $refunds = HrCashRefund::getCashRefund($request->id_cash_advance);
            
            $paymentBankTo = HrCashPayment::getPaymentBankTo($request->id_cash_advance);
            $employeeBank = HrCashRefund::getEmployeeBank();
            $companyBank = HrCashRefund::getCompanyBank();
            if($request->fas == "true") {
                $employee = DB::table('hr_employee as he')
                        ->join('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
                        ->where('he.id_user', session('id_user'))
                        ->where('he.status', 'A')
                        ->where('mpd.secondary_position', false)
                        ->select('he.*', 'mpd.id_branch')
                        ->first();
                $companyAllowedBank = HrCashRefund::getCompanyBank(@$employee->id_branch);
            } else {
                $companyAllowedBank = HrCashRefund::getCompanyBank();
            }
            $bank = HrCashRefund::getBanks();
            $regions = DB::select("SELECT id_region AS id, description AS text FROM master_region WHERE id_company = ? AND status = 'A'", [session('id_company')]);
            $masterChartAccount = DB::select("SELECT id_account AS id, concat(account_number, ' - ', account_name) AS text FROM accounting.master_chart_account WHERE status = 'A' AND id_company = ?", [session('id_company')]);
            $returnData = [
                "data" => $expenseRequest,
                "cash_advance" => $cashAdvance[0],
                "products" => $products,
                "full_products" => $fullProducts,
                "transport_accommodation" => $transportAccommodation,
                "settlement" => $settlement,
                "settlement_summary" => $settlementSummary,
                "refund" => $refunds,
                "currencies" => $currencies,
                "bank" => $bank,
                "employee_bank" => $employeeBank,
                "company_bank" => $companyBank,
                "company_allowed_bank" => $companyAllowedBank,
                "regions" => $regions,
                "master_chart_account" => $masterChartAccount,
                'payment_bank_to' => $paymentBankTo,
                "hr_products" => $hrProducts,
            ];
            if($request->type == "hr") {
                $returnData["master_bank"] = HrCashPayment::getHrPayment(session('id_company'));
                $returnData["payment"] = HrCashPayment::getPayments($request->id_cash_advance);
            } else {
                $returnData["payment"] = HrCashPayment::getPayments($request->id_cash_advance);
            }
            return response()->json($returnData);
        } catch(\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveFinancePayment(Request $request) {
        $rules = [
            'id_cash_advance' => 'required',
            'payment.*.id_payment' => 'nullable',
            'payment.*.bank_from_account' => 'required',
            'payment.*.bank_to_account' => 'required_without:payment.*.id_payment',
            'payment.*.notes' => 'required',
            'payment.*.currency' => 'required',
            'payment.*.currency_rate' => 'required',
            'payment.*.total_amount' => 'required',
            'payment.*.total_applied_amount' => 'nullable',
            'payment.*.payment_status' => 'required',
            // 'payment.*.attachment' => 'required_without:payment.*.id_payment|mimes:jpg,jpeg,pdf,png,webp|max:2048',
            'id_clearing_account' => 'nullable',
            'clearing_amount' => 'nullable',
            'accounting_date' => 'nullable',
            'maximum_clearing_date' => 'nullable',
        ];
        SanitizedForm::sanitizeStringInput($request, $rules);
        $request->validate($rules);
        $officialTravel = "SELECT hot.reference_number FROM hr_cash_advance hca JOIN hr_official_travel hot ON hca.id_official_travel = hot.id_official_travel WHERE hca.id_cash_advance = ?";
        $officialTravel = str_replace("/", "_", DB::selectOne($officialTravel, [$request->id_cash_advance])->reference_number);

        $cashAdvance = HrCashAdvance::findOrFail($request->id_cash_advance);
        $cashAdvance->id_clearing_account = $request->id_clearing_account;
        $cashAdvance->total_base_currency_clearing_amount = trim($request->clearing_amount, ' Rp');
        $cashAdvance->accounting_date = $request->accounting_date;
        $cashAdvance->maximum_clearing_date = $request->maximum_clearing_date;
        $cashAdvance->save();

        try {
            DB::beginTransaction();
            if($request->payment) {
                $newPayments = collect($request->payment);
                foreach($newPayments as $payment) {
                    $masterBank = "SELECT * FROM accounting.master_bank_account WHERE id_bank_account = ?";
                    $masterBank = DB::selectOne($masterBank, [$payment['bank_from_account']]);
        
                    if(array_key_exists('id_payment', $payment) && $payment['id_payment']) {
                        $cashPayment = HrCashPayment::findOrFail($payment['id_payment']);
                        $cashPayment->updated_by = session('id_user');
                        if(array_key_exists('attachment', $payment)) {
                            Storage::disk('public')->delete($cashPayment->attachment);
                            $fileName = 'payment_'.$request->id_cash_advance.'_'.uniqid().'.'.$payment['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/cash_payment/'.$officialTravel.'/';
                            $payment['attachment']->storeAs($path, $fileName, 'public');
                            $cashPayment->attachment = $path.$fileName;
                        }
                    } else {
                        $cashPayment = new HrCashPayment();
                        $cashPayment->created_by = session('id_user');
                        if(array_key_exists('attachment', $payment)) {
                            $fileName = 'payment_'.$request->id_cash_advance.'_'.uniqid().'.'.$payment['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/cash_payment/'.$officialTravel.'/';
                            $payment['attachment']->storeAs($path, $fileName, 'public');
                            $cashPayment->attachment = $path.$fileName;
                        }
    
                        if (array_key_exists('bank_to_account', $payment)) {
                            $employeeBank = DB::selectOne('SELECT * FROM hr_bank_employee WHERE id_bank_employee = ?', [$payment['bank_to_account']]);
                        } else {
                            $employeeBank = null;
                        }
    
                        $cashPayment->id_bank_to = $employeeBank ? $employeeBank->id_bank : null;
                        $cashPayment->id_employee_bank_to = array_key_exists('bank_to_account', $payment) ? $payment['bank_to_account'] : null;
                    }

                    $cashPayment->id_cash_advance = $request->id_cash_advance;
                    $cashPayment->id_bank_from = $masterBank->id_bank;
                    $cashPayment->id_bank_from_account = $masterBank->id_bank_account;
                    $cashPayment->bank_from_account = $masterBank->account_number;
                    $cashPayment->description = "Payment";
                    $cashPayment->notes = $payment['notes'];
                    $cashPayment->id_currency_cash_payment = $payment['currency'];
                    $cashPayment->currency_rate_payment = $payment['currency_rate'];
                    $cashPayment->total_amount = str_replace(".", "", $payment['total_amount']);
                    // $cashPayment->total_applied_amount = str_replace(".", "", $payment['total_applied_amount']);
                    $cashPayment->payment_status = $payment['payment_status'];
                    $cashPayment->status = 'A';
                    $cashPayment->id_company = session('id_company');
                    $cashPayment->save();

                    if($cashPayment->payment_status == 'Not_Paid') {
                        HrSettlementExpense::where('id_cash_payment', $cashPayment->id_cash_payment)->update([
                            'id_cash_payment' => null,
                        ]);
                    }
                }
                DB::commit();
            }
            return response()->json([
                "message" => "Data saved successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
        
    }

    public function saveHrPayment(Request $request) {
        $rules = [
            'id_cash_advance' => 'required',
            'transaco.*.id_settlement' => 'required',
            'transaco.*.approval_price' => 'required',
            'payment.*.id_payment' => 'nullable',
            'payment.*.bank_from_account' => 'required',
            'payment.*.bank_to_account' => 'required_without:payment.*.id_payment',
            'payment.*.notes' => 'required|string',
            'payment.*.currency' => 'required',
            'payment.*.currency_rate' => 'required',
            'payment.*.total_amount' => 'required',
            'payment.*.total_applied_amount' => 'nullable',
            'payment.*.payment_status' => 'required',
            // 'payment.*.attachment' => 'required_without:payment.*.id_payment|mimes:jpg,jpeg,pdf,png,webp|max:2048',
        ];
        SanitizedForm::sanitizeStringInput($request, $rules);
        $request->validate($rules);
        
        $officialTravel = "SELECT hot.reference_number FROM hr_cash_advance hca JOIN hr_official_travel hot ON hca.id_official_travel = hot.id_official_travel WHERE hca.id_cash_advance = ?";
        $officialTravel = str_replace("/", "_", DB::selectOne($officialTravel, [$request->id_cash_advance])->reference_number);

        $cashAdvance = HrCashAdvance::findOrFail($request->id_cash_advance);
        $cashAdvance->id_clearing_account = $request->id_clearing_account;
        $cashAdvance->accounting_date = $request->accounting_date;
        $cashAdvance->maximum_clearing_date = $request->maximum_clearing_date;
        $cashAdvance->save();

        try {
            DB::beginTransaction();
            $reqSettlement = collect($request->settlement)->whereNull('id_settlement');
            if($reqSettlement->count() > 0){
                $hca = HrCashAdvance::findOrFail($request->id_cash_advance);
                foreach($reqSettlement as $k => $settlement) {
                    $request->validate([
                        "settlement.$k.start_end" => 'required',
                        "settlement.$k.product" => 'required',
                        "settlement.$k.branch" => 'required',
                        "settlement.$k.description" => 'required',
                        "settlement.$k.qty" => 'required',
                        "settlement.$k.unit" => 'required',
                        "settlement.$k.currency" => 'required',
                        "settlement.$k.price" => 'required',
                    ]);
                    $data = new HrSettlementExpense();
                    $data->id_cash_advance = $request->id_cash_advance;
                    $data->settlement_date = $settlement['start_end'];
                    $data->id_product = $settlement['product'];
                    $data->id_branch = $settlement['branch'];
                    $data->description = $settlement['description'];
                    $data->qty = $settlement['qty'];
                    $data->id_uom = $settlement['unit'];
                    $data->id_currency_settlement_expense = $settlement['currency'];
                    $data->currency_rate_settlement_expense = $hca->currency_rate_cash_advance;
                    $data->unit_price = $settlement['price'];
                    $data->is_validate = true;
                    $data->id_company = $hca->id_company;
                    $data->created_by = session('id_user');
                    $data->save();
                }
            }
            if($request->transaco) {
                foreach($request->transaco as $transaco) {
                    $settlement = HrSettlementExpense::findOrFail($transaco['id_settlement']);
                    $settlement->approval_price_by_finance = str_replace(".", "", $transaco['approval_price']);
                    $settlement->save();
                }
            }
            if($request->payment) {
                foreach($request->payment as $payment) {
                    $masterBank = "SELECT * FROM accounting.master_bank_account WHERE id_bank_account = ?";
                    $masterBank = DB::selectOne($masterBank, [$payment['bank_from_account']]);
        
                    if(array_key_exists('id_payment', $payment) && $payment['id_payment']) {
                        $cashPayment = HrCashPayment::findOrFail($payment['id_payment']);
                        $cashPayment->updated_by = session('id_user');
                        if(array_key_exists('attachment', $payment)) {
                            Storage::disk('public')->delete($cashPayment->attachment);
                            $fileName = 'payment_'.$request->id_cash_advance.'_'.uniqid().'.'.$payment['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/cash_payment/'.$officialTravel.'/';
                            $payment['attachment']->storeAs($path, $fileName, 'public');
                            $cashPayment->attachment = $path.$fileName;
                        }
                    } else {
                        $cashPayment = new HrCashPayment();
                        $cashPayment->created_by = session('id_user');
                        if(array_key_exists('attachment', $payment)) {
                            $fileName = 'payment_'.$request->id_cash_advance.'_'.uniqid().'.'.$payment['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/cash_payment/'.$officialTravel.'/';
                            $payment['attachment']->storeAs($path, $fileName, 'public');
                            $cashPayment->attachment = $path.$fileName;
                        }
    
                        if (array_key_exists('bank_to_account', $payment)) {
                            $partnerBank = DB::selectOne('SELECT * FROM accounting.master_bank_account WHERE id_bank_account = ?', [$payment['bank_to_account']]);
                        } else {
                            $partnerBank = null;
                        }
    
                        $cashPayment->id_bank_to = $partnerBank ? $partnerBank->id_bank : null;
                        $cashPayment->id_bank_to_account = array_key_exists('bank_to_account', $payment) ? $payment['bank_to_account'] : null;
                    }
    
                    $cashPayment->id_cash_advance = $request->id_cash_advance;
                    $cashPayment->id_bank_from = $masterBank->id_bank;
                    $cashPayment->id_bank_from_account = $masterBank->id_bank_account;
                    $cashPayment->bank_from_account = $masterBank->account_number;
                    $cashPayment->description = "Payment";
                    $cashPayment->notes = $payment['notes'];
                    $cashPayment->id_currency_cash_payment = $payment['currency'];
                    $cashPayment->currency_rate_payment = $payment['currency_rate'];
                    $cashPayment->total_amount = str_replace(".", "", $payment['total_amount']);
                    // $cashPayment->total_applied_amount = str_replace(".", "", $payment['total_applied_amount']);
                    $cashPayment->payment_status = $payment['payment_status'];
                    $cashPayment->status = 'A';
                    $cashPayment->id_company = session('id_company');
                    $cashPayment->save();
                }
            }
            DB::commit();
            return response()->json([
                "message" => "Data saved successfully"
            ]);
        } catch(\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                "errors" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function saveHrTransportAccommodation(Request $request) {
        $acceptedRequests = [
            'id_cash_advance' => 'required',
            'id_product' => 'required',
            'id_branch' => 'required',
            'description' => 'required',
            'qty' => 'required',
            'notes' => 'required',
            'settlement_date' => 'required',
            'id_uom' => 'required',
            'id_currency_settlement_expense' => 'required',
            'currency_rate_settlement_expense' => 'required',
            'unit_price' => 'required',
            'approval_price_by_finance' => 'required',
            'is_paid_by_hr' => 'required',
        ];
        $request->validate($acceptedRequests);
        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($acceptedRequests));
            $expenseAccount = DB::selectOne("SELECT mpc.id_expense_account FROM inventory.master_product mp 
                                            JOIN inventory.master_product_categories mpc ON mp.id_product_categories = mpc.id_product_categories 
                                            WHERE mp.id_product = ?",
                                            [$request->id_product]);
            $settlementExpense = HrSettlementExpense::create(array_merge($data, [
                'is_verified_by_chief' => true,
                'is_verified_by_finance' => true,
                'id_expense_account' => $expenseAccount->id_expense_account ?? null,
                'id_company' => session('id_company'),
                'created_by' => session('id_user'),
            ]));
            DB::commit();
            return response()->json([
                "message" => "Data has been saved successfully"
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function getBranch(Request $request) {
        $request->validate([
            'id_region' => 'required'
        ]);
        return MasterBranch::where('id_region', $request->id_region)->get(['id_branch AS id', 'description AS text']);
    }

    public function settlementNotification() {
        $finance = [];
        $financePayment = [];
        $financeLink = "#";
        
        $chief = HrCashAdvance::getApprovalList(session('id_user'));
        foreach(Session::get('app_menu') as $menu) {
            if($menu->address_menu == 'cash_advance/cash_advance/expense_payment_finance') {
                $finance = HrCashAdvance::getFinanceApprovalList(session('id_user'), null,  "Not_Clear", false, "finance", "settlement");//HrCashAdvance::getFinanceApprovalList(session('id_user'), null, "Not_Clear", false, "finance", "settlement");//HrCashAdvance::getFinanceApprovalList(session('id_user'), null, "Not_Clear", false, "finance", "settlement");
                $financeLink = route('settlementtravel.approval.finance');
                $financePayment = HrCashAdvance::getFinanceApprovalList(session('id_user'), null, "Not_Clear", false, "finance", "payment-cashadvance", "FALSE", now()->subMonths(3)->format('Y-m-d H:i:s'), now()->endOfDay()->format('Y-m-d H:i:s'));//HrCashAdvance::getFinanceApprovalList(session('id_user'), null, "Not_Clear", false, "finance", "payment-cashadvance", "FALSE", now()->subMonths(3)->format('Y-m-d'));
                break;
            } else if($menu->address_menu == 'cash_advance/cash_advance/settlement_clearing_by_fas') {
                $finance = HrCashAdvance::getBranchFinanceApprovalList("settlement", false, 'Not_Clear');
                $financeLink = route('settlementtravel.approval.fas');
            }
        }
        return response()->json([
            'chief' => count($chief),
            'finance' => count($finance),
            'finance_payment_cashadvance' => count($financePayment),
            'finance_link' => $financeLink,
        ]);
    }

    public function unpaidSettlement(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required',
            'type' => 'required|in:finance,hr',
        ]);
        if($request->type == "hr") {
            $filterHr = "AND is_paid_by_hr = TRUE";
        } else {
            $filterHr = "AND is_paid_by_hr = FALSE";
        }
        $settlement = DB::select("SELECT 
                        id_settlement_expense AS id, 
                        concat(description, ' (', total_base_currency_amount, ')') AS TEXT, 
                        total_base_currency_amount 
                        FROM hr_settlement_expense
                        WHERE id_cash_advance = ? AND id_cash_payment IS NULL AND is_verified_by_chief = TRUE AND is_verified_by_finance = TRUE ".$filterHr."
                        "
                        , [$request->id_cash_advance]);
        return response()->json($settlement);
    }

    public function applyPayment(Request $request) {
        $request->validate([
            'payment_settlement' => 'nullable',
            'id_cash_payment' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $payment = HrCashPayment::findOrFail($request->id_cash_payment);
            $settlements = HrSettlementExpense::whereIn('id_settlement_expense', $request->payment_settlement ?? [])->get();
            foreach($settlements as $settlement) {
                $settlement->total_payment_amount = $settlement->total_payment_amount + $settlement->total_amount;
                $settlement->id_cash_payment = $request->id_cash_payment;
                $settlement->save();
                $payment->total_applied_amount = $payment->total_applied_amount + $settlement->total_base_currency_amount;
                $payment->save();
            }
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed applying payment'
            ], 500);
        }
    }

    public function print(Request $request) {
        $request->validate([
            'id_official_travel' => 'nullable',
            'id_cash_advance' => 'nullable'
        ]);
        $request->id_official_travel = $request->id_official_travel ? Crypt::decrypt($request->id_official_travel) : null;
        $request->id_cash_advance = $request->id_cash_advance ? Crypt::decrypt($request->id_cash_advance) : null;
        if(!$request->id_cash_advance) {
            $findCashAdvance = DB::select("SELECT * FROM hr_cash_advance WHERE id_official_travel = ?", [$request->id_official_travel]);
            if(count($findCashAdvance) < 1) {
                $this->generateCashAdvance($request);
            }
        } else {
            $getOfficialTravel = DB::table('hr_cash_advance')->where('id_cash_advance', $request->id_cash_advance)->first();
            $employee = DB::table('hr_employee')->where('id_employee', $getOfficialTravel->id_employee)->first();
        }
        // dd($request->id_official_travel ?? $getOfficialTravel->id_official_travel);

        $cashAdvance = HrSettlementExpense::getData($request->id_official_travel ?? $getOfficialTravel->id_official_travel);
        $officialTravel = HrOfficialTravel::getSettlementOfficialTravelData($request->id_official_travel ? session('id_user') : $employee->id_user, $request->id_official_travel ?? $getOfficialTravel->id_official_travel);
        // $products = HrCashAdvance::getProducts();
        $settlement = HrSettlementExpense::getSettlements($officialTravel[0]->id_cash_advance);
        $transportAccommodation = HrSettlementExpense::getTransportAndAccommodation($officialTravel[0]->id_cash_advance);
        // $currencies = HrSettlementExpense::getCurrencies();
        $refunds = HrCashRefund::getCashRefund($officialTravel[0]->id_cash_advance);
        $payments = HrCashPayment::getPayments($officialTravel[0]->id_cash_advance);
        // $employeeBank = HrCashRefund::getEmployeeBank();
        // $companyBank = HrCashRefund::getCompanyBank();
        // $bank = HrCashRefund::getBanks();
        // $regions = DB::select("SELECT id_region AS id, description AS text FROM master_region WHERE id_company = ? AND status = 'A'", [session('id_company')]);
        $settlement = collect($settlement)->where('is_verified_by_chief', TRUE)->whereIn('is_verified_by_finance', [TRUE, NULL]);//->where('is_verified_by_finance', '!=', FALSE);
        $forgetKeys = $settlement->whereNotNull('is_verified_by_finance')->where('is_verified_by_finance', FALSE)->keys();
        
        foreach($forgetKeys as $key) {
            $settlement = $settlement->forget($key);
        }
        
        $settlementGrouped = collect($settlement)->groupBy('product_description')->toArray();
        $transportAccommodation = collect($transportAccommodation)->groupBy('product_description')->toArray();
        // dd($officialTravel[0]);
        $settlementCollect = collect($settlement);
        $finance = $settlementCollect->where('finance_nik', '!=', NULL);

        $sumSettlement = DB::selectOne("select 
                            id_cash_advance, 
                            sum(COALESCE(total_approval_amount*is_verified_by_finance::integer, total_amount*is_verified_by_chief::integer)) as sum
                        from hr_settlement_expense hse
                        where id_cash_advance = ?
                        and is_paid_by_hr = FALSE
                        and approved_by_chief is not null
                        group by id_cash_advance", [$officialTravel[0]->id_cash_advance]);
        if(!$sumSettlement) {
            $sumSettlement = (object)[
                'sum' => 0
            ];
        }

        $qrcode_employee = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($officialTravel[0]->reference_number.' ('.$officialTravel[0]->name_employee.'/'.$officialTravel[0]->nik_employee.')'));
		$qrcode_approver = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($officialTravel[0]->reference_number.' ('.$officialTravel[0]->name_approval_request.' / '.$officialTravel[0]->nik_approval_request.')'));
        $qrcode_finance = count($finance) > 0 && $officialTravel[0]->is_approved_by_finance ? base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($officialTravel[0]->reference_number.' ('.$finance->first()->finance_approval.' / '.$finance->first()->finance_nik.')')) : 'R0lGODlhAQABAAAAACwAAAAAAQABAAA=';

        $data = [
            'official_travel' => $officialTravel,
            'cash_advance' => $cashAdvance,
            'settlement' => $settlementGrouped,
            'transport_accommodation' => $transportAccommodation,
            'refunds' => $refunds,
            'payments' => $payments,
            'company' => DB::selectOne('SELECT * FROM master_company WHERE id_company = ?', [session('id_company')]),
            'qr' => [
                'employee' => $qrcode_employee,
                'approval' => $qrcode_approver,
                'finance' => $qrcode_finance,
            ],
            'sum_settlement' => $sumSettlement,
            'finance' => $officialTravel[0]->is_approved_by_finance,
        ];
        // return response()->json($officialTravel[0]->reference_number.' ('.$officialTravel[0]->name_approval_request.' / '.$officialTravel[0]->nik_approval_request.')');
        // return view('cash_advance.settlement_travel.print', $data);
        
        return PDF::loadView('cash_advance.settlement_travel.print', $data)->setPaper('A4','potrait')->stream();
    }

    public function sendBillingLetter(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required|exists:hr_cash_advance,id_cash_advance',
        ]);
        try {
            DB::beginTransaction();
            $cashAdvance = HrCashAdvance::findOrFail($request->id_cash_advance);
            $cashAdvance->start_refund_date = now()->toIso8601String();
            $cashAdvance->save();
            $notification = new \App\Http\Controllers\EmailController();
            $notification->settlementBillingLetter($cashAdvance->id_cash_advance);
            $notification->settlementBillingLetterManager($cashAdvance->id_cash_advance);
            DB::commit();
            return response()->json([
                'message' => 'Billing letter created successfully!',
            ]);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function billingLetter(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required|exists:hr_cash_advance,id_cash_advance',
        ]);
        $cashAdvance = DB::table('hr_cash_advance as hca')
                        ->leftJoin('hr_employee as he', 'hca.id_employee', 'he.id_employee')
                        ->leftJoin('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
                        ->leftJoin('master_position_routing as mpr', 'mpd.id_position_routing', 'id_routing')
                        ->select('hca.*', 'he.name', 'he.nik_employee', 'mpr.description as position')
                        ->where('id_cash_advance', $request->id_cash_advance)->first();
        if(@$cashAdvance->start_refund_date == null) {
            return '<script>alert("Invalid billing letter");window.history.back();</script>';
        }
        $finance = DB::select("SELECT DISTINCT 
                            hse.id_cash_advance, he.name 
                        FROM hr_settlement_expense hse 
                        JOIN hr_employee he ON
                            hse.approved_by_finance = he.id_employee
                        WHERE hse.id_cash_advance = ?", [$cashAdvance->id_cash_advance]);
        $cashAdvance = collect($cashAdvance)->toArray();
        $cashAdvance["bankAccount"] = DB::select("SELECT * 
                        FROM accounting.master_bank_account mba 
                        JOIN public.master_bank mb ON mba.id_bank = mb.id_bank
                        WHERE mba.bank_type = 'Company' AND mba.is_default = TRUE AND mba.status = 'A'");
        $cashAdvance["financeName"] = [];
        foreach($finance as $fin) {
            $cashAdvance["financeName"][] = $fin->name;
        }
        return PDF::loadView('cash_advance.settlement_travel.print_bill', $cashAdvance)->setPaper('A4','potrait')->stream();
    }

    public function financeManualClearing(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $cashAdvance = HrCashAdvance::findOrFail($request->id_cash_advance);
            $cashAdvance->is_manual_clearing = true;
            $cashAdvance->updated_by = session('id_user');
            $cashAdvance->save();
            DB::commit();
            return response()->json([
                'message' => "Manual clearing success!",
            ], 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function branchFinanceApprovalData(Request $request) {
        if($request->ajax()) {
            $data = HrCashAdvance::getBranchFinanceApprovalList("settlement", false, $request->status);
            return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = "";
                $button .= '<button type="button" action="view" class="button-view btn btn-warning btn-sm" id-cash-advance="'.$data->id_cash_advance.'" title="View"><span class="fas fa-eye text-white"></span></button>';
                if($data->is_approved_by_chief) $button .= '&nbsp;<button type="button" action="print" class="button-print btn btn-success btn-sm" id-cash-advance="'.Crypt::encrypt($data->id_cash_advance).'" title="Print"><span class="fa fa-file-pdf text-white"></span></button>';
                
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
        return view('cash_advance.settlement_travel.approval.finance', [
            'dataUrl' => route('settlementtravel.approval.fas'),
            'page' => 'settlement_clearing',
            'startDate' => null,
            'fas' => true
        ]);
    }

    public function getTransportVariantData(Request $request) {
        $request->validate([
            'id_product' => 'required',
        ]);
        $data = DB::selectOne("SELECT 
            mp.*
        FROM inventory.master_product mp 
        WHERE 
            mp.id_company = ?
            AND mp.id_product = ?",
        [session('id_company'), $request->id_product]);
        return response()->json([
            'data' => explode(",", trim($data->variant, " {}")),
        ]);
    }

    protected function sendNotification($to) {
        $mail = new \App\Http\Controllers\EmailController();
    }
}