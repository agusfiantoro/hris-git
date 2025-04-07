<?php

namespace App\Http\Controllers\CashAdvance\BankAccount;
use DataTables;
use App\Http\Controllers\Controller;
use App\Models\Accounting\MasterBankAccount\MasterBankAccount;
use App\Models\CashAdvance\OfficialTravel\HrCashAdvance;
use App\Models\Employee\EmployeeSetting\MasterBank;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Contracts\DataTable;

class BankAccountController extends Controller
{
    use StandardResponse;
    public function index(Request $request) {
        if($request->ajax()) {
            $emp = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
            $data = MasterBankAccount::active()
                                    ->currentCompany()
                                    ->leftJoin('public.master_branch as mb', 'accounting.master_bank_account.id_branch', 'mb.id_branch')
                                    ->leftJoin('public.master_bank as mb2', 'accounting.master_bank_account.id_bank', 'mb2.id_bank')
                                    ->select('accounting.master_bank_account.*', 'mb.description as branch', 'mb2.bank_code as bank')
                                    ->get();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $button = "<button class='btn btn-sm btn-primary edit' id-bank-account='$row->id_bank_account'><i class='fas fa-edit'></i></button>";
                return $button;
            })
			->make(true);
        }
        return view('cash_advance.bank_account.index');
    }

    public function getData() {
        return $this->success([
            'branches' => MasterBranch::where('id_company', session('id_company'))
                                        ->where('status', 'A')
                                        ->get(['id_branch as id', 'description as text']),
            'banks' => MasterBank::where('id_company', session('id_company'))
                                ->where('status', 'A')
                                ->get(['id_bank as id', 'description as text']),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_bank_account' => 'required'
        ]);
        return $this->success(MasterBankAccount::findOrFail($request->id_bank_account));
    }

    public function save(Request $request) {
        $acceptedRequests = [
            'id_bank' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'branch_name' => 'nullable',
            'id_branch' => 'nullable',
            'bank_type' => 'required',
            'is_default' => 'nullable',
            'status' => 'required',
        ];
        $request->validate($acceptedRequests);

        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($acceptedRequests));
            if($request->id_bank_account) {
                $masterBankAccount = MasterBankAccount::findOrFail($request->id_bank_account)->update(array_merge($data, [
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                    'is_default' => $request->is_default ? true : false,
                ]));
            } else {
                $masterBankAccount = MasterBankAccount::create(array_merge($data, [
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                    'is_default' => $request->is_default ? true : false,
                ]));
            }
            DB::commit();
            return $this->success($masterBankAccount, "Bank account has been saved successfully");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}