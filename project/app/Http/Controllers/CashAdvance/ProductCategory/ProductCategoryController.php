<?php

namespace App\Http\Controllers\CashAdvance\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MessageController;
use App\Models\CashAdvance\ExpenseProduct\MasterProduct;
use App\Models\CashAdvance\ExpenseProduct\MasterProductCategory;
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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    public function index(Request $request) {
        if($request->id_product_categories) {
            $categories = DB::table('inventory.master_product_categories')->where('id_product_categories', $request->id_product_categories)->first();
            return response()->json($categories);
        }
        if($request->ajax()) {
            $categories = DB::table('inventory.master_product_categories')->where('id_company', session('id_company'))->get();
            return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('', function($data) {
				$a = '';
				return $a;
			})
            ->addColumn('action', function($data) {
                $onclick = "loadEdit(".$data->id_product_categories.")";
                $button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id-product="' . $data->id_product_categories . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                return $button;
            })
            ->rawColumns(['action'])
			->make(true);
        }
        return view('cash_advance.product_categories.index');
    }

    public function getMasterChartAccount(Request $request) {
        $mca = DB::table('accounting.master_chart_account')
                ->where('status', 'A')
                ->where('id_company', session('id_company'))
                ->get(['id_account as id', "account_name as text"]);
        return response()->json($mca);
    }

    public function save(Request $request) {
        $request->validate([
            'id_product_categories' => 'nullable',
            'code' => 'required',
            'description' => 'required',
            'id_valuation_account' => 'nullable',
            'id_income_account' => 'nullable',
            'id_revenue_account' => 'nullable',
            'id_expense_account' => 'nullable',
            'id_inventory_account' => 'nullable',
            'costing_method' => 'required',
            'status' => 'required'
        ]);

        if($request->id_product_categories) {
            $category = MasterProductCategory::findOrFail($request->id_product_categories);
            $category->updated_by = session('id_user');
        } else {
            $category = new MasterProductCategory();
            $category->created_by = session('id_user');
        }
        $category->code = $request->code;
        $category->description = $request->description;
        $category->id_valuation_account = $request->id_valuation_account;
        $category->id_income_account = $request->id_income_account;
        $category->id_revenue_account = $request->id_revenue_account;
        $category->id_expense_account = $request->id_expense_account;
        $category->id_inventory_account = $request->id_inventory_account;
        $category->costing_method = $request->costing_method;
        $category->status = $request->status;
        $category->id_company = session('id_company');
        $category->created_by = session('id_user');
        $category->save();
    }
}