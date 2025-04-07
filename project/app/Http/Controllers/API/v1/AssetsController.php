<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Models\API\Employee;
use App\Models\API\Attendance;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\AssetEmployeeAssigned;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterAssetGroup;
use App\Models\Assets\ConfigSettings\AssetConfigSettings;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Models\Assets\MasterChartAccount;
use App\Models\Assets\TransferSettings\MasterAssetLocation;
use App\Models\Curl;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class AssetsController extends BaseController
{

    /**
     * Generates asset number with the following format: `CCC/GGG/YYYY/MM/IIII`
     * 
     * - CCC: Company code
     * - GGG: Asset Group code
     * - YYYY: Current year
     * - MM: Current month
     * - IIII: Increments of assets registered on year YYYY and asset group code GGG
     * @param int $idCompany
     * @param int $idAssetGroup
     * @return string
     */
    protected function generateAssetNumber(int $idCompany, int $idAssetGroup) {
        $company = DB::table('master_company')->where('id_company', $idCompany)->where('status', 'A')->first();
        $assetGroup = MasterAssetGroup::findOrFail($idAssetGroup);
        $time = now()->format('Y');
        $assetNumber = "$company->company_code/$assetGroup->asset_group_code/$time/";
        $maxNumber = DB::selectOne("SELECT max(right(asset_number, 4)) AS max FROM asset.fa_asset fa WHERE asset_number LIKE ?", [$assetNumber."%"]);
        $maxNumber = $maxNumber && $maxNumber->max ? sprintf("%04s", abs(substr($maxNumber->max, -4)+1)) : "0001";
        $assetNumber .= now()->format('m/').$maxNumber;
        return $assetNumber;
    }

    public function getData(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $getEmployeeDetail = Employee::getEmployeeDetail($request->nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }

            $employee = $getEmployeeDetail[0];
            $idCompany = $request->id_company ?? $employee->id_company;
            $data = [
                "asset_categories" => MasterAssetCategory::currentCompany($idCompany)->active()->formatSelect2()->get(),
                "depreciation_methods" => DepreciationMethod::currentCompany($idCompany)->active()->formatSelect2()->get(),
                "asset_groups" => MasterAssetGroup::currentCompany($idCompany)->active()->formatSelect2()->get(),
                "chart_accounts" => MasterChartAccount::currentCompany($idCompany)->active()->isTransactable()->formatSelect2(null, 'account_name')->get(),
                "branches" => MasterBranch::where('id_company', $idCompany)->where('status', 'A')->get(['id_branch as id', 'description as text']),
            ];

            if($request->id_branch) {
                $data['locations'] = MasterLocation::where('id_branch', $request->id_branch)->where('id_company', $idCompany)->where('status', 'A')->get(['id_location as id', 'description as text']);
            }
            if($request->id_location) {
                $data['asset_locations'] = MasterAssetLocation::currentCompany($idCompany)->active()->where('id_location', $request->id_location)->formatSelect2()->get();
            }

            DB::commit();
            return $this->mobileSuccess("Data has been sent successfully.", $data);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch(Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function submit(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'assets' => 'required',
                'assets.*.id_asset_category' => 'required',
                'assets.*.asset_type' => 'required|in:Capitalized,CIP,Expense',
                'assets.*.id_asset_group' => 'required',
                'assets.*.description' => 'required',
                // 'assets.*.current_units' => 'required|numeric|max:1',
                'assets.*.manufacture_name' => 'nullable',
                'assets.*.model_number' => 'nullable',
                'assets.*.serial_number' => 'nullable',
                'assets.*.tag_number' => 'nullable',
                'assets.*.warranty_number' => 'nullable',
                'assets.*.warranty_date' => 'nullable|date',
                'assets.*.tax_expired_date' => 'nullable|date',
                'assets.*.receiving_date' => 'nullable|date',
                'assets.*.id_parent_asset' => 'nullable',
                // 'assets.*.in_used_flag' => 'required|boolean',
                'assets.*.depreciation_flag' => 'required|boolean',
                'assets.*.id_depreciation_method' => 'required_if:assets.*.depreciation_flag,true',
                'assets.*.depreciation_start_date' => 'nullable|date',
                'assets.*.life_in_month' => 'nullable|numeric|min:0',
                'assets.*.original_cost' => 'required|numeric|min:0',
                'assets.*.adjusted_cost' => 'required|numeric|min:0',
                'assets.*.depreciation_cost' => 'required|numeric|min:0',
                'assets.*.salvage_type' => 'required|in:Amount,Percent',
                'assets.*.salvage_value' => 'required|numeric|min:0',
                'assets.*.property_type' => 'required|in:Private,Commercial',
                'assets.*.ownership' => 'required|in:Owned,Leased',
                'assets.*.bought' => 'required|in:New,Used',
                'assets.*.leased_number' => 'nullable',
                'assets.*.lease_effective_date' => 'nullable|date',
                'assets.*.lease_expired_date' => 'nullable|date',
                'assets.*.lease_contract_expired_date' => 'nullable|date',
                'assets.*.partner_name' => 'nullable',
                'assets.*.receiving_number' => 'nullable',
                'assets.*.purchase_invoice_number' => 'nullable',
                'assets.*.project_number' => 'nullable',
                'assets.*.batch_number' => 'nullable',
                // 'assets.*.queue_process_status' => 'required|in:New,Confirm,Post',
                'assets.*.status' => 'required|in:A,I,R,D',
                'assets.*.reference_number' => 'nullable',
                'assets.*.notes_1' => 'nullable',
                'assets.*.notes_2' => 'nullable',
                'assets.*.notes_3' => 'nullable',
                'assets.*.notes_4' => 'nullable',
                'assets.*.notes_5' => 'nullable',
                'assets.*.notes_6' => 'nullable',
                'assets.*.notes_7' => 'nullable',
                'assets.*.notes_8' => 'nullable',
                'assets.*.purchase_date' => 'nullable|date',
                // 'assets.*.id_employee' => 'nullable',
                // 'assets.*.id_account' => 'nullable',
                'assets.*.id_branch' => 'nullable',
                'assets.*.id_location' => 'nullable',
                'assets.*.id_asset_location' => 'nullable',
                'assets.*.assignment_date' => 'nullable',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $getEmployeeDetail = Employee::getEmployeeDetail($request->nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }

            $employee = $getEmployeeDetail[0];
            $idCompany = $request->id_company ?? $employee->id_company;
            $count = 0;
            foreach($request->assets as $asset) {
                // if asset input has receiving date and depreciation start date is not set, 
                // calculate depreciation start date according to fa_config_settings
                if(key_exists('receiving_date', $asset) && (!key_exists('depreciation_start_date', $asset) || is_null($asset['depreciation_start_date']))) {
                    $config = AssetConfigSettings::currentCompany()->active()->first();
                    $receivingDate = Carbon::parse($asset['receiving_date']);
                    $depreciationStart = $asset['receiving_date'];
                    if($config->next_month_depreciation_start) {
                        if($config->next_month_depreciation_start <= (int)$receivingDate->format('d')) {
                            $depreciationStart = $receivingDate->addMonth()->startOfMonth()->format('Y-m-d');
                        }
                    }
                    $asset['depreciation_start_date'] = $depreciationStart;
                }
                $createdAsset = Asset::create(array_merge($asset, [
                    'created_by' => $employee->id_user,
                    'id_company' => $idCompany,
                    'asset_number' => $this->generateAssetNumber($idCompany, $asset['id_asset_group']),
                    'current_units' => 1,
                    'queue_process_status' => 'New',
                    'in_used_flag' => true,
                    'purchase_date' => $asset['purchase_date'] ?? date('Y-m-d'),
                ]));
                AssetEmployeeAssigned::create([
                    'id_employee' => $employee->id_employee,
                    'unit_assigned' => 1,
                    'id_branch' => $asset['id_branch'] ?? $employee->id_branch,
                    'id_location' => $asset['id_location'],
                    'id_asset_location' => $asset['id_asset_location'],
                    'transaction_date' => $asset['assignment_date'],
                    'id_asset' => $createdAsset->id_asset,
                    'status' => 'A',
                    'id_company' => $createdAsset->id_company,
                    'created_by' => $employee->id_user,
                ]);
                $count++;
            }

            DB::commit();
            return $this->mobileSuccess("$count assets has been created.", null);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return response()->json($e->getMessage());
            return $this->mobileErrorQuery($e);
        } catch(Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }
}