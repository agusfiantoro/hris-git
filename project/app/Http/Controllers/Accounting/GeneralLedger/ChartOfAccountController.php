<?php
namespace App\Http\Controllers\Accounting\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Models\Assets\ConfigSettings\GlJeHeaders;
use App\Models\Assets\MasterChartAccount;
use App\Traits\Assets\AssetsApproval;
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ChartOfAccountController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $data = MasterChartAccount::currentCompany()->chronological(true)->get();
            // foreach($data as $d) {
            //     $d->source;
            //     $d->category;
            //     $d->period;
            // }
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('accounting.general_ledger.gl_settings.chart_of_account.index');
    }

    public function getData() {
        $accounts = MasterChartAccount::currentCompany()->get(['id_account as id', 'account_name as text', 'account_type']);
        $accountTypes = $accounts->unique('account_type')->map(function ($item) {
            return [
                'id' => $item['account_type'],
                'text' => $item['account_type'],
            ];
        });
        return $this->success([
            'account_types' => $accountTypes,
            'accounts' => $accounts,
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_account' => 'required',
        ]);
        $header = MasterChartAccount::findOrFail($request->id_account);
        return $this->success($header);
    }

    public function save(Request $request) {
        $acceptedRequests = [
            'id_account' => 'nullable',
            'account_number' => 'required',
            'account_name' => 'required',
            'account_type' => 'required',
            'is_transactable' => 'nullable',
            'parent_id_account' => 'nullable',
            'status' => 'required|in:A,I',
        ];
        $request->validate($acceptedRequests);
        $data = $request->only(array_keys($acceptedRequests));

        try {
            if($request->id_account) {
                $mca = MasterChartAccount::findOrFail($request->id_account);
                if($mca->status === 'A' && $data['status'] === 'I') {
                    $data = array_merge($data, [
                        'inactive_date' => now()->format('Y-m-d'),
                    ]);
                } else if($mca->status === 'I' && $data['status'] === 'A') {
                    $data = array_merge($data, [
                        'inactive_date' => null,
                    ]);
                }
                $data = array_merge($data, [
                    'is_transactable' => $request->is_transactable == "true" ? true : false,
                    'updated_by' => session('id_user'),
                ]);
                $mca->update($data);
            } else {
                if($data['status'] === 'I') {
                    $data = array_merge($data, [
                        'inactive_date' => now()->format('Y-m-d'),
                    ]);
                }
                $data = array_merge($data, [
                    'is_transactable' => $request->is_transactable == "true" ? true : false,
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                ]);
                $mca = MasterChartAccount::create($data);
            }
            DB::commit();
            return $this->success($mca, "Chart of account has been saved successfully!");
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }
}