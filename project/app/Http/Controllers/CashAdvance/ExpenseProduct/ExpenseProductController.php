<?php

namespace App\Http\Controllers\CashAdvance\ExpenseProduct;
use DataTables;
use App\Http\Controllers\Controller;
use App\Models\CashAdvance\ExpenseProduct\MasterGradeExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseProductController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = MasterGradeExpense::get_grade_expenses();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('', function($data) {
				$a = '';
				return $a;
			})
            ->addColumn('action', function($data) {
                $onclick = "loadedit(".$data->id_grade_expense.")";
                $button = "";
                $button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_grade_expense . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                return $button;
            })
            ->rawColumns(['action'])
			->make(true);
        }
        return view('cash_advance.expense_product.index');
    }

    public function save(Request $request) {
        $request->validate([
            'job_grade' => 'required',
            'id_product' => 'required',
            'id_region' => 'required',
            'id_branch' => 'required',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
            'status' => 'required|in:A,I',
            'description' => 'required',
            'notes' => 'nullable',
            'id_position_routing' => 'nullable'
        ], [
            'id_product.required' => 'The product field is required',
            'id_region.required' => 'The region field is required',
            'id_branch.required' => 'The branch field is required',
        ]);
        try {
            DB::beginTransaction();
            $data = new MasterGradeExpense();
            $data->id_job_grade = $request->job_grade;
            $data->id_product = $request->id_product;
            $data->id_region = $request->id_region;
            $data->id_branch = $request->id_branch;
            $data->description = $request->description;
            $data->min_price = $request->min_price;
            $data->max_price = $request->max_price;
            $data->notes = $request->notes;
            $data->status = $request->status;
            $data->id_position_routing = $request->id_position_routing;
            $data->id_company = session('id_company');
            $data->created_by = session('id_user');
            $data->save();
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Expense Product Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack(); 
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Expense Product !! [' . $e->getMessage() . ']']);           
		}
    }

    public function update(Request $request) {
        $request->validate([
            'id_grade_expense' => 'required',
            'job_grade' => 'required',
            'id_product' => 'required',
            'id_region' => 'required',
            'id_branch' => 'required',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
            'status' => 'required|in:A,I',
            'description' => 'required',
            'notes' => 'nullable',
            'id_position_routing' => 'nullable'
        ], [
            'id_product.required' => 'The product field is required',
            'id_region.required' => 'The region field is required',
            'id_branch.required' => 'The branch field is required',
        ]);

        try {
            DB::beginTransaction();
            $data = MasterGradeExpense::findOrFail($request->id_grade_expense);
            $data->id_job_grade = $request->job_grade;
            $data->id_product = $request->id_product;
            $data->id_region = $request->id_region;
            $data->id_branch = $request->id_branch;
            $data->description = $request->description;
            $data->min_price = $request->min_price;
            $data->max_price = $request->max_price;
            $data->notes = $request->notes;
            $data->status = $request->status;
            $data->id_position_routing = $request->id_position_routing;
            // $data->id_company = session('id_company');
            $data->updated_by = session('id_user');
            $data->save();
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Expense Product Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack(); 
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Expense Product !! [' . $e->getMessage() . ']']);           
		}
    }

    public function modal_detail(Request $request) {
        if($request->cash_expense) {
            $cashExpense = MasterGradeExpense::get_row_by_id($request->cash_expense);
        } else $cashExpense = null;
        return view('cash_advance.expense_product.modal_detail', compact('cashExpense'));
    }

    public function get_initial_field() {
        $regions = MasterGradeExpense::get_region();
        $jobGrades = MasterGradeExpense::get_job_grade();
        $products = MasterGradeExpense::get_product();
        $positionRouting = DB::select("SELECT id_routing AS id, description AS text FROM master_position_routing WHERE id_company = ?", [session('id_company')]);

        return response()->json([
            'regions' => $regions,
            'job_grades' => $jobGrades,
            'products' => $products,
            'position_routing' => $positionRouting
        ]);
    }

    public function get_filled_field(Request $request) {
        $request->validate([
            'id_grade_expense' => 'required',
        ]);
        $expense = MasterGradeExpense::get_grade_expenses_by_id($request->id_grade_expense);
        return response()->json($expense);
    }

    public function get_branch(Request $request) {
        $request->validate([
            'region_id' => 'required'
        ]);
        $branch = MasterGradeExpense::get_branch($request->region_id);
        return response()->json($branch);
    }

    public function delete(Request $request, $id_grade_expense) {
        try {
            DB::beginTransaction();
            $data = MasterGradeExpense::findOrFail($id_grade_expense);
            $data->delete();
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Expense Product Deleted Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack(); 
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Delete Expense Product !! [' . $e->getMessage() . ']']);           
		}
    }

    
}