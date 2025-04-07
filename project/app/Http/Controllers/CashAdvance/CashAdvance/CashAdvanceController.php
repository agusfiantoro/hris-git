<?php

namespace App\Http\Controllers\CashAdvance\CashAdvance;
use DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use App\Models\CashAdvance\ExpenseProduct\MasterGradeExpense;
use App\Models\CashAdvance\OfficialTravel\HrApprovalTransaction;
use App\Models\CashAdvance\OfficialTravel\HrCashAdvance;
use App\Models\CashAdvance\OfficialTravel\HrCashPayment;
use App\Models\CashAdvance\OfficialTravel\HrCashRefund;
use App\Models\CashAdvance\OfficialTravel\HrExpenseRequest;
use App\Models\CashAdvance\OfficialTravel\HrOfficialTravel;
use App\Models\CashAdvance\OfficialTravel\HrSettlementExpense;
use App\Models\Organization\MasterOrganization\MasterRegional;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Contracts\DataTable;

class CashAdvanceController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
            $emp = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
            $data = HrCashAdvance::where('id_employee', $emp->id_employee)
                                    ->leftJoin('master_general_data as mgd', 'hr_cash_advance.id_approval_status', 'mgd.id_general_data')
                                    ->orderByDesc('id_cash_advance')
                                    ->select('hr_cash_advance.*', 'mgd.description as approval_desc')
                                    ->get();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $button = "<button class='btn btn-sm btn-primary button-view' id-cash-advance='$row->id_cash_advance'><i class='fas fa-edit'></i></button>";
                return $button;
            })
			->make(true);
        }
        return view('cash_advance.cash_advance.index');
    }

    public function getFormData(Request $request) {
        $employee = DB::table('hr_employee as he')
                    ->join('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
                    ->join('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
                    ->join('master_job_position as mjp', 'mpr.id_position', 'mjp.id_position')
                    ->join('master_department as md', 'mjp.id_dept', 'md.id_dept')
                    ->select('he.id_employee', 'he.name', 'mpr.description as position', 'mpr.id_routing as id_position', 'md.description as dept', 'md.id_dept as id_dept')
                    ->where('he.id_user', session('id_user'))->where('he.status', 'A')->first();
        $approval = HrCashAdvance::getApprovalStatus($employee->id_employee);
        $currency = DB::select("SELECT DISTINCT
                                    mc.id_currency AS id,
                                    long_description AS text,
                                    CASE 
                                        WHEN mc.id_currency = mc2.id_currency THEN TRUE
                                        ELSE FALSE
                                    END AS default_currency
                                FROM master_currency mc  
                                LEFT JOIN master_company mc2 
                                    ON mc.id_currency = mc2.id_currency
                                    AND mc2.id_company = ?
                                WHERE mc.id_country IS NOT NULL
                                AND mc.status = 'A'
                                ORDER BY TEXT", [session('id_company')]);
        $defaultCurrency = collect($currency)->where('default_currency', TRUE)->first();
        $categories = HrCashAdvance::getProducts();
        $branch = DB::table('master_branch')
                    ->where('id_company', session('id_company'))
                    ->where('status', 'A')
                    ->get(['id_branch as id', 'description as text', 'id_region']);
        $region = DB::table('master_region as mr')
                    ->where('id_company', session('id_company'))
                    ->where('status', 'A')
                    ->get(['id_region as id', 'description as text']);
        $lockedPrice = DB::select("SELECT DISTINCT id_product FROM master_grade_expenses mge WHERE id_company = ?", [session('id_company')]);
        return response()->json([
            'approval' => $approval,
            'employee' => $employee,
            'currency' => $currency,
            'default_currency_id' => $defaultCurrency->id,
            'categories' => $categories,
            'branch' => $branch,
            'region' => $region,
            'locked_price' => $lockedPrice,
        ]);
    }

    public function getData(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required',
        ]);
        $emp = DB::table('hr_employee as he')
                ->leftJoin('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
                ->leftJoin('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
                ->leftJoin('master_job_position as mjp', 'mpr.id_position', 'mjp.id_position')
                ->leftJoin('master_department as md', 'mjp.id_dept', 'md.id_dept')
                ->select('he.*', 'md.description as desc_dept', 'mpr.description as desc_position')
                ->where('he.id_user', session('id_user'))->where('he.status', 'A')->first();
        $cashAdvance = HrCashAdvance::where('id_cash_advance', $request->id_cash_advance)
                                    ->leftJoin('master_general_data as mgd', 'hr_cash_advance.id_approval_status', 'mgd.id_general_data')
                                    ->orderByDesc('id_cash_advance')
                                    ->select('hr_cash_advance.*', 'mgd.description as approval_desc')
                                    ->first();
        $expenseRequest = HrSettlementExpense::getData($request->id_cash_advance, false);
        // $officialTravel = HrOfficialTravel::getSettlementOfficialTravelData(session('id_user'), $request->id_official_travel);
        $products = HrCashAdvance::getProducts();
        $fullProducts = HrCashAdvance::getProducts(true);
        $transportAccommodation = HrSettlementExpense::getTransportAndAccommodation($cashAdvance->id_cash_advance);
        $settlement = HrSettlementExpense::getSettlements($cashAdvance->id_cash_advance);
        $currencies = HrSettlementExpense::getCurrencies();
        $refunds = HrCashRefund::getCashRefund($cashAdvance->id_cash_advance);
        $payments = HrCashPayment::getPayments($cashAdvance->id_cash_advance);
        $employeeBank = HrCashRefund::getEmployeeBank();
        $companyBank = HrCashRefund::getCompanyBank();
        $bank = HrCashRefund::getBanks();
        $regions = HrSettlementExpense::getRegions($cashAdvance->id_cash_advance);
        $startEnd = @DB::selectOne("SELECT CONCAT(min(start_date::date), ' to ', max(end_date::date)) as startend FROM hr_expense_request her WHERE id_cash_advance = ?", [$request->id_cash_advance])->startend;
        return response()->json([
            "approval" => HrCashAdvance::getApprovalStatus($emp->id_employee),
            "expense_request" => $expenseRequest,
            'cash_advance' => $cashAdvance,
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
            "regions" => $regions,
            "start_end_date" => $startEnd,
            "employee" => $emp,
        ]);
        return response()->json($cashAdvance);
    }

    public function update(Request $request) {
        $rules = [
            'id_cash_advance' => 'nullable',
            'request' => 'nullable',
            'approval_by' => 'required',
            'start_end_date' => 'required',
            'id_dept' => 'required',
            'id_region' => 'required',
            'cash_advance_type' => 'required',
            'request.*.product' => 'required',
            'request.*.branch' => 'required',
            'request.*.start_end_date' => 'required',
            'request.*.notes' => 'nullable|string',
            'request.*.currencyrate' => 'required',
            'request.*.qty' => 'required',
        ];
        $request = SanitizedForm::sanitizeStringInput($request, $rules);
        $request->validate($rules);

        DB::beginTransaction();

        try {

            $employee = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
            if($request->id_cash_advance) {
                $cashAdvance = HrCashAdvance::find($request->id_cash_advance);
                $cashAdvance->updated_by = session('id_user');
            } else {
                $cashAdvance = new HrCashAdvance();
                $cashAdvance->created_by = session('id_user');
                $cashAdvance->id_company = session('id_company');
                $cashAdvance->id_approval_request = $request->approval_by;
                $cashAdvance->transaction_date = now()->toIso8601String();
            }
            if($request->submit_type == 'draft') {
                $format = HrCashAdvance::format_save($request);
                $cashAdvance->reference_number = $format['format'];
                $cashAdvance->id_approval_status = null;
            } else {
                $format = HrCashAdvance::format_save($request);
                $cashAdvance->reference_number = $format['format'];
                $cashAdvance->id_approval_status = $format['id_approval_status_transaction'];
            }
            $cashAdvance->id_employee = $employee->id_employee;
            $cashAdvance->reason_notes = $request->cash_advance_notes;
            $cashAdvance->status = 'A';
            
            $cashAdvance->save();

            // approval
            if($request->submit_type == 'submit') {
                $approval = HrCashAdvance::getApprovalStatus($employee->id_employee);
                $transaction = new HrApprovalTransaction();
                $transaction->id_source_transaction = $cashAdvance->id_cash_advance;
                $transaction->source_transaction_type = $approval->code;
                $transaction->id_approval = $approval->hierarchy->id;
				$transaction->id_approval_mode = $approval->id_approval_mode;
				$transaction->sequence = $approval->sequence;
                $transaction->save();
            }

            // expense request
            $expenseRequest = @$request->only('request')['request'];
            if($expenseRequest) {
                foreach($expenseRequest as $exp) {
                    if(array_key_exists('id_expense_request', $exp)) {
                        $expense = HrExpenseRequest::find($request->id_cash_advance);
                        $expense->updated_by = session('id_user');
                    } else {
                        $expense = new HrExpenseRequest();
                        $expense->id_cash_advance = $cashAdvance->id_cash_advance;
                        $expense->created_by = session('id_user');
                        $expense->id_company = session('id_company');
                    }
                    $expense->id_product = $exp['product'];
                    $expense->id_branch = $exp['branch'];
                    // $expense->description = $exp['description'];
                    $region = DB::table('master_branch as mb')
                    ->join('master_region as mr', 'mb.id_region', 'mr.id_region')
                    ->select('mr.description as region_desc', 'mb.description as branch_desc')
                    ->where('mb.id_branch', $exp['branch'])->first();
                    $desc_cashadvance = $exp['start_end_date'].';';
                    if (empty($exp['branch']) AND empty($region->region_desc)) {
                        $notes = strtoupper($exp['notes']).';';
                    }else{
                        $notes = strtoupper($exp['notes']).';'.$region->region_desc.';'.$region->branch_desc;
                    }
                    $expense->notes = $notes;
                    $expense->description = $desc_cashadvance;
                    $expense->qty = $exp['qty'];
                    $expense->id_uom = HrOfficialTravel::get_uom($exp['product'])->id_uom;
                    $expense->unit_price = str_replace(".", "", $exp['price']);
                    $expense->id_currency = $request->request_currency;
                    $expense->currency_rate = $exp['currencyrate'];
                    
                    $expense->status = 'A';

                    $expStartEnd = explode(" to ", $exp['start_end_date']);
                    $expense->start_date = $expStartEnd[0];
                    $expense->end_date = $expStartEnd[1];                 
    
                    $expense->save();
                }
            }
            if($request->settlement && ($request->id_cash_advance || $request->cash_advance_type == 'reimburse')) {
                $settlementIsValidate = $request->submit_type == "submit";
                $cashAdvance->is_validate = $settlementIsValidate;
                $cashAdvance->save();
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
                        if(array_key_exists('attachment', $settlement)) {
                            $fileName = 'settlement_'.$request->id_cash_advance.'_'.uniqid().'.'.$settlement['attachment']->getClientOriginalExtension();
                            $path = 'upload/cash_advance/settlement/'.$cashAdvance->reference_number."/";
                            Storage::disk('public')->delete($data->attachment);
                            // $del = Storage::disk('public')->delete('app/public/'.$data->attachment);
                            $settlement['attachment']->storeAs($path, $fileName, 'public');
                            $data->attachment = $path.$fileName;
                        }
                        $data->save();
                    } else {
                        //create
                        $fileName = 'settlement_'.$request->id_cash_advance.'_'.uniqid().'.'.$settlement['attachment']->getClientOriginalExtension();
                        $path = 'upload/cash_advance/settlement/'.$cashAdvance->reference_number."/";
                        // try {
                            Storage::makeDirectory('public/'.$path, 0777, true, true);
                            Image::make($settlement['attachment'])->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save(storage_path('app/public/'.$path.$fileName));
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
                                'attachment' => $path.$fileName,
                                'is_validate' => $settlementIsValidate,
                                'status' => 'A',
                                'id_company' => session('id_company'),
                                'created_by' => session('id_user'),
                            ]);
                        // } catch(Exception $e) {
                        //     return response()->json($e->getMessage(), $e->getCode());
                        // }
                    }
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Cash advance saved successfully!'
            ]);
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        
    }

    public function approve(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required',
        ]);

        $cashAdvance = HrCashAdvance::findOrFail($request->id_cash_advance);
    }

    public function reject(Request $request) {
        $request->validate([
            'id_cash_advance' => 'required',
        ]);
    }
}