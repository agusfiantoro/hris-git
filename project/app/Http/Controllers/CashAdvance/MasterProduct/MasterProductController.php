<?php

namespace App\Http\Controllers\CashAdvance\MasterProduct;
use DataTables;
use App\Http\Controllers\Controller;
use App\Models\CashAdvance\ExpenseProduct\MasterGradeExpense;
use App\Models\CashAdvance\ExpenseProduct\MasterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterProductController extends Controller
{
    public function index(Request $request) {
        if($request->id_product) {
            $product = MasterProduct::findOrFail($request->id_product);
            return response()->json($product);
        }
        if($request->ajax()) {
            $data = MasterProduct::getData();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('', function($data) {
				$a = '';
				return $a;
			})
            ->addColumn('action', function($data) {
                $onclick = "loadEdit(".$data->id_product.")";
                $button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id-product="' . $data->id_product . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                return $button;
            })
            ->rawColumns(['action'])
			->make(true);
        }
        return view('cash_advance.master_product.index');
    }

    public function getProductCategories(Request $request) {
        $categories = DB::table('inventory.master_product_categories')->where('id_company', session('id_company'))->where('status', 'A')->get(['id_product_categories as id', 'description as text']);
        return response()->json($categories);
    }

    public function getUnitOfMeasures(Request $request) {
        $uom = DB::table('inventory.master_unit_measures')->where('is_base_units', true)->where('id_company', session('id_company'))->where('status', 'A')->get(['id_uom as id', 'description as text']);
        return response()->json($uom);
    }

    public function save(Request $request) {
        $request->validate([
            'id_product' => 'nullable',
            'code' => 'required',
            'description' => 'required',
            'inventory_type' => 'required',
            'price' => 'required',
            'status' => 'required',
            'saleable' => 'nullable',
            'purchasable' => 'nullable',
            'expenseable' => 'nullable',
            'id_uom' => 'required',
            'accommodation' => 'nullable',
            'transportation' => 'nullable'
        ]);
        DB::beginTransaction();
        try {
            if(!$request->id_product) {
                $product = new MasterProduct();
            } else {
                $product = MasterProduct::findOrFail($request->id_product);
            }
            $product->id_uom = $request->id_uom;
            $product->code = $request->code;
            $product->description = $request->description;
            $product->long_description = $request->long_description;
            $product->inventory_type = $request->inventory_type;
            $product->id_product_categories = $request->id_product_categories;
            $product->price = (int)trim(str_replace(".", "", str_replace("Rp.", "", $request->price)), " ");
            $product->purchasable = $request->purchasable && $request->purchasable == "true" ? true : false;
            $product->expenseable = $request->expenseable && $request->expenseable == "true" ? true : false;
            $product->saleable = $request->saleable && $request->saleable == "true" ? true : false;
            $product->accomodation = $request->accommodation && $request->accommodation == "true" ? true : false;
            $product->transportation = $request->transportation && $request->transportation == "true" ? true : false;
            $product->status = $request->status;
            $product->id_company = session('id_company');
            $product->created_by = session('id_user');
            $product->save();
            DB::commit();

            return response()->json([
                "message" => "Product ".$product->description." has been saved successfully!"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request) {
        $request->validate([
            'id_product' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $product = MasterProduct::findOrFail($request->id_product);
            $product->status = "I";
            $product->save();
            DB::commit();
            return response()->json([
                "message" => "Product has been deleted successfully!"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }
}