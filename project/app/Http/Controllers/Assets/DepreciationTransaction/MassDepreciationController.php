<?php
namespace App\Http\Controllers\Assets\DepreciationTransaction;

use App\Http\Controllers\Controller;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MassDepreciationController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $data = DepreciationMethod::currentCompany()->chronological(true)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_depreciation_method;
                })
                ->make();
        }
        return view('assets.depreciation_transaction.depreciation_history.index', [
            'param_source' => 'mass_depreciation'
        ]);
    }

    public function getData() {
        try {
            return $this->success([
                'periods' => MasterPeriod::active('O')->currentCompany()->formatSelect2()->get(),
                'company' => DB::table('public.master_company')->where('id_company', session('id_company'))->get(['id_company as id', 'company_name as text'])
            ]);
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }

    public function generate(Request $request) {
        $request->validate([
            'id_period' => 'required',
            'id_company' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $period = MasterPeriod::findOrFail($request->id_period);
            $generate = DB::select("SELECT * FROM asset.sp_funct_generate_mass_journal_depreciation ($period->id_period, $request->id_company)");
            DB::commit();
            return $this->success(null, "Mass depreciation has been generated successfully!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}