<?php
namespace App\Http\Controllers\Assets\ConfigSettings;

use App\Http\Controllers\Controller;
use App\Models\Assets\ConfigSettings\AssetConfigSettings;
use App\Models\Assets\ConfigSettings\GlJeCategories;
use App\Models\Assets\ConfigSettings\GlJeSources;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\MasterJobGrade;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\Assets\TransferSettings\MasterApprovalAssetDetail;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssetConfigSettingsController extends Controller {	
    use StandardResponse;
    
    public function index(Request $request) {
        if($request->ajax()) {
            $configs = AssetConfigSettings::currentCompany()->chronological(true)->get();
            foreach($configs as $config) {
                $config->je_source = $config->jeSource;
                $config->asset_journal_category = $config->assetJournalCategory;
                $config->depreciation_journal_category = $config->depreciationJournalCategory;
                $config->adjustment_journal_category = $config->adjustmentJournalCategory;
                $config->retirement_journal_category = $config->retirementJournalCategory;
            }
            // dd($configs);
            return DataTables::of($configs)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) {
                        return $row->id_config_setting;
                    })
                    ->make();
        }
        return view('assets.config_settings.index');
    }

    public function getData() {
        return $this->success([
            'je_sources' => GlJeSources::currentCompany()->active()->get(['id_je_source as id', 'source_name as text']),
            'je_categories' => GlJeCategories::currentCompany()->active()->get(['id_je_category as id', 'category_name as text']),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_config_setting' => 'required',
        ]);

        $config = AssetConfigSettings::findOrFail($request->id_config_setting);
        return $this->success($config);
    }

    public function save(Request $request) {
        $acceptedRequests = [
            'id_config_setting' => 'nullable',
            'revaluation_flag' => 'nullable',
            'impairment_flag' => 'nullable',
            'id_je_source' => 'required',
            'id_asset_journal_category' => 'required',
            'id_depreciation_journal_category' => 'required',
            'id_adjustment_journal_category' => 'required',
            'id_retirement_journal_category' => 'required',
            'id_revaluation_journal_category' => 'required',
            'id_impairment_journal_category' => 'required',
            'id_reinstate_journal_category' => 'required',
            'next_month_depreciation_start' => 'nullable|numeric|min:1|max:31',
            'status' => 'required|in:A,I',
        ];
        $request->validate($acceptedRequests);
        $data = $request->only(array_keys($acceptedRequests));

        try {
            if($request->id_config_setting) {
                $config = AssetConfigSettings::findOrFail($request->id_config_setting);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                    'revaluation_flag' => $request->revaluation_flag != null,
                    'impairment_flag' => $request->impairment_flag != null,
                ]);
                $config->update($data);
            } else {
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                    'revaluation_flag' => $request->revaluation_flag != null,
                    'impairment_flag' => $request->impairment_flag != null,
                ]);
                $config = AssetConfigSettings::create($data);
            }
            return $this->success($config, 'Asset config settings saved successfully!');
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }
}