<?php
namespace App\Http\Controllers\Assets\AdditionAsset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Exception;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterAssetGroup;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Models\Assets\MasterChartAccount;
use App\Traits\StandardResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterAssetPeriodController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $periods = MasterPeriod::currentCompany()->chronological(true)->get();
            return DataTables::of($periods)
                ->addIndexColumn()
                ->addColumn('action', function($data) {
                    return $data->id_period;
                })
                ->make();
        }
        return view('assets.addition_asset.asset_period.index');
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_period' => 'required',
        ]);
        $period = MasterPeriod::findOrFail($request->id_period);
        return $this->success($period);
    }

    public function save(Request $request) {
        $acceptedRequests = [
            'id_period' => 'nullable',
            'period_code' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:O,C',
        ];
        $request = SanitizedForm::sanitizeStringInput($request, $acceptedRequests);
        $request->validate($acceptedRequests);
        $data = $request->only(array_keys($acceptedRequests));
        try {
            if($request->id_period) {
                $period = MasterPeriod::findOrFail($request->id_period);
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                ]);   
                $period->update($data);
            } else {
                $data = array_merge($data, [
                    'id_company' => session('id_company'),
                    'updated_by' => session('id_user'),
                ]);
                $period = MasterPeriod::create($data);
            }
            return $this->success($period, "Period has been saved successfully!");
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }

}