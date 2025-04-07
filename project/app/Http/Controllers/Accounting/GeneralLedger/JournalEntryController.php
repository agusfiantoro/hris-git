<?php
namespace App\Http\Controllers\Accounting\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Http\Requests\JournalEntryUpdateRequest;
use App\Models\Accounting\GeneralLedger\GlJeLines;
use App\Models\Accounting\GeneralLedger\GlPeriod;
use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\ConfigSettings\GlJeCategories;
use App\Models\Assets\ConfigSettings\GlJeHeaders;
use App\Models\Assets\ConfigSettings\GlJeSources;
use App\Models\Assets\MasterChartAccount;
use App\Models\GeneralSetting\CompanySetting\MasterCurrency;
use App\Traits\Assets\AssetsApproval;
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JournalEntryController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $data = GlJeHeaders::currentCompany()->joinCategoryPeriod()->chronological(true)->get();
            foreach($data as $d) {
                $d->source;
                // $d->category;
                // $d->period;
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('accounting.general_ledger.journal_entry.index');
    }

    public function getData() {
        try {
            $journalSources = GlJeSources::currentCompany()->active()->formatSelect2(null, 'source_name')->get();
            $journalCategories = GlJeCategories::currentCompany()->active()->formatSelect2(null, 'category_name')->get();
            $journalEntryHeaders = GlJeHeaders::currentCompany()->active()->formatSelect2(null, 'reference_number')->get();
            $periods = GlPeriod::currentCompany()->where('status', 'O')->formatSelect2()->get();
            $accounts = MasterChartAccount::currentCompany()->active()->formatSelect2(null, 'account_name', ['account_number'])->get();
            $currencies = MasterCurrency::where('status', 'A')
                        ->select('id_currency as id', 'description as text')
                        ->get();
            return $this->success([
                'journal_sources' => $journalSources,
                'journal_categories' => $journalCategories,
                'journal_entry_headers' => $journalEntryHeaders,
                'periods' => $periods,
                'currencies' => $currencies,
                'accounts' => $accounts,
            ]);
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_je_header' => 'required',
        ]);
        $header = GlJeHeaders::findOrFail($request->id_je_header);
        $header->lines;
        return $this->success($header);
    }

    protected function updateDetail(GlJeHeaders $header, array $journalLines) {
        foreach($journalLines as $journalLine) {
            if(key_exists('id_je_lines', $journalLine) && $journalLine['id_je_lines']) {
                // Update journal entry lines
                $line = GlJeLines::findOrFail($journalLine['id_je_lines']);
                $line->update(array_merge($journalLine, [
                    'currency_rate' => $header->currency_rate,
                    'id_je_header' => $header->id_je_header,
                    'updated_by' => session('id_user'),
                ]));
            } else {
                // Create journal entry lines
                GlJeLines::create(array_merge($journalLine, [
                    'id_je_header' => $header->id_je_header,
                    'currency_rate' => $header->currency_rate,
                    'id_company' => $header->id_company,
                    'created_by' => session('id_user'),
                ]));
            }
        }
    }

    public function update(JournalEntryUpdateRequest $request) {
        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($request->rules()));
            $data = array_merge($data, [
                'updated_by' => session('id_user'),
            ]);
            $header = GlJeHeaders::findOrFail($request->id_je_header);
            $header->update($data);
            if($request->detail && count($request->detail) > 0) {
                $this->updateDetail($header, $request->detail);
            }
            DB::commit();
            return $this->success($header, "Journal entry has been updated successfully!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function post(JournalEntryUpdateRequest $request) {
        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($request->rules()));
            $data = array_merge($data, [
                'posted_date' => now(),
                'document_status' => 'Posted',
                'updated_by' => session('id_user'),
            ]);
            $header = GlJeHeaders::findOrFail($request->id_je_header);
            $header->update($data);
            if($request->detail && count($request->detail) > 0) {
                $this->updateDetail($header, $request->detail);
            }
            DB::commit();
            return $this->success($header, "Journal entry has been posted successfully!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function cancel(JournalEntryUpdateRequest $request) {
        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($request->rules()));
            $data = array_merge($data, [
                'posted_date' => null,
                'document_status' => 'Cancel',
                'updated_by' => session('id_user'),
            ]);
            $header = GlJeHeaders::findOrFail($request->id_je_header);
            $header->update($data);
            if($request->detail && count($request->detail) > 0) {
                $this->updateDetail($header, $request->detail);
            }
            DB::commit();
            return $this->success($header, "Journal entry has been cancelled successfully!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}