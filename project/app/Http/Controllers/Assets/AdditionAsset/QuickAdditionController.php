<?php
namespace App\Http\Controllers\Assets\AdditionAsset;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetUpdateRequest;
use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\AdditionAsset\AssetEmployeeAssigned;
use App\Models\Assets\AdditionAsset\AssetImage;
use App\Models\Assets\AdditionAsset\AssetLocation;
use App\Models\Assets\AdditionAsset\MassAddition;
use Exception;
use App\Models\Assets\AdditionAsset\MasterAssetCategory;
use App\Models\Assets\AdditionAsset\MasterAssetGroup;
use App\Models\Assets\ConfigSettings\AssetConfigSettings;
use App\Models\Assets\DepreciationSettings\AssetDepreciation;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\TransferSettings\MasterAssetLocation;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Intervention\Image\Facades\Image;
use OutOfBoundsException;

class QuickAdditionController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $assets = Asset::currentCompany()
                ->with(['assetCategory', 'assetGroup'])
                ->queueProcessStatus('=', ['New', 'Confirm'])
                ->chronological(true);
            
            return DataTables::of($assets)
                    ->addIndexColumn()
                    ->toJson();
        }
        return view('assets.addition_asset.quick_addition.index');
    }

    public function getData(Request $request) {
        if($request->id_branch) {
            return $this->success([
                'locations' => MasterLocation::where('id_branch', $request->id_branch)->where('status', 'A')->get(['id_location as id', 'description as text']),
            ]);
        }
        if($request->id_location) {
            return $this->success([
                'asset_locations' => AssetLocation::currentCompany()->active()->where('id_location', $request->id_location)->get(['id_asset_location as id', 'description as text']),
            ]);
        }
        if($request->receiving_date) {
            $config = AssetConfigSettings::currentCompany()->active()->first();
            $receivingDate = Carbon::parse($request->receiving_date);
            $depreciationStart = $request->receiving_date;
            if($config->next_month_depreciation_start) {
                if($config->next_month_depreciation_start <= (int)$receivingDate->format('d')) {
                    $depreciationStart = $receivingDate->addMonth()->startOfMonth()->format('Y-m-d');
                }
            }
            return $this->success([
                'depreciation_start_date' => $depreciationStart,
            ]);
        }
        return $this->success([
            'asset_categories' => MasterAssetCategory::currentCompany()->active()->get(['id_asset_category as id', 'description as text', 'category_type']),
            'asset_groups' => MasterAssetGroup::currentCompany()->active()->get(['id_asset_group as id', 'description as text', '*']),
            'depreciation_methods' => DepreciationMethod::currentCompany()->active()->get(['id_depreciation_method as id', 'description as text']),
            'assets' => Asset::currentCompany()->active()->get(['id_asset as id', 'description as text']),
            'employees' => HrEmployee::selectRaw("id_employee as id, CONCAT(name, ' (', nik_employee, ')') as text")->currentCompany()->active()->orderBy('name')->get(),
            'branches' => MasterBranch::where('id_company', session('id_company'))->where('status', 'A')->get(['id_branch as id', 'description as text']),
        ]);
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_asset' => 'required',
        ]);
        $asset = Asset::with([
                'assignedEmployee.employee', 
                'assignedEmployee.branch',
                'assignedEmployee.location',
                'assignedEmployee.assetLocation',
                'image'
            ])->findOrFail($request->id_asset);

        foreach($asset->assignedEmployee as $assignedEmployee) {
            $assignedEmployee->human_transaction_date = Carbon::parse($assignedEmployee->transaction_date)->format('d M Y');
        }

        return $this->success($asset);
    }

    /**
     * Generates asset number based on last asset number in the database.
     * @param   int     $idCompany      Valid Company ID from master_company table for asset number prefix
     * @param   int     $idAssetGroup   Valid Asset Group ID from fa_asset_group table for asset number infix
     * @return  string
     */
    public function generateAssetNumber(int $idCompany, int $idAssetGroup, Carbon $date = null) {
        if(!$date) {
            $date = now();
        }
        $company = DB::table('master_company')->where('id_company', $idCompany)->where('status', 'A')->first();
        $assetGroup = MasterAssetGroup::findOrFail($idAssetGroup);
        $time = $date->format('Y');
        $assetNumber = "$company->company_code/$assetGroup->asset_group_code/$time/";
        $maxNumber = DB::selectOne("SELECT max(right(asset_number, 4)) AS max FROM asset.fa_asset fa WHERE asset_number LIKE ?", [$assetNumber."%"]);
        $maxNumber = $maxNumber && $maxNumber->max ? sprintf("%04s", abs(substr($maxNumber->max, -4)+1)) : "0001";
        $assetNumber .= $date->format('m/').$maxNumber;
        return $assetNumber;
    }

    public function save(AssetUpdateRequest $request) {
        if($request->detail && count($request->detail) > 0) {
            $sum = 0;
            foreach($request->detail as $detail) {
                $sum += $detail['unit_assigned'];
                if($sum > $request->current_units) {
                    return $this->error(null, 'Total assigned units to employee exceeds asset total units!');
                }
            }
        }
        $data = $request->only(array_keys($request->rules()));
        DB::beginTransaction();
        try {
            if($request->id_asset) {
                $asset = Asset::findOrFail($request->id_asset);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                    
                ]);
                $asset->update($data);
            } else {
                $data = array_merge($data, [
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                    'asset_number' => $this->generateAssetNumber(session('id_company'), $request->id_asset_group),
                ]);
                $asset = Asset::create($data);
            }
            if($request->detail && count($request->detail) > 0) {
                foreach($request->detail as $detail) {
                    if(array_key_exists('id_employee_assigned', $detail) && $detail['id_employee_assigned']) {
                        $detailData = array_merge($detail, [
                            'updated_by' => session('id_user'),
                        ]);
                        AssetEmployeeAssigned::findOrFail($detail['id_employee_assigned'])->update($detailData);
                    } else {
                        $detailData = array_merge($detail, [
                            'id_asset' => $asset->id_asset,
                            'id_company' => $asset->id_company,
                            'created_by' => session('id_user'),
                        ]);
                        AssetEmployeeAssigned::create($detailData);
                    }
                }
            }
            if($request->image && count($request->image) > 0) {
                $pathSafeAssetNumber = str_replace("/", "-", $asset->asset_number);
                foreach($request->image as $image) {
                    $saveImage = Image::make($image['attachment'])->resize(1280, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $fileName = uniqid($pathSafeAssetNumber).".".$image['attachment']->getClientOriginalExtension();
                    $path = "upload/assets/addition/$pathSafeAssetNumber";
                    Storage::makeDirectory("public/$path");
                    $saveImage->save(storage_path("app/public/$path/$fileName"));
                    AssetImage::create([
                        'id_asset' => $asset->id_asset,
                        'attachment' => "$path/$fileName",
                        'note' => $image['note'],
                        'status' => $image['status'],
                        'created_by' => session('id_user'),
                        'id_company' => $asset->id_company,
                    ]);
                }
            }
            DB::commit();
            return $this->success($asset, 'Asset has been saved successfully!');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function post(AssetUpdateRequest $request) {
        DB::beginTransaction();
        try {
            $data = $request->only(array_keys($request->rules()));
            $asset = Asset::findOrFail($request->id_asset);
            $asset->update(array_merge($data, [
                'updated_by' => session('id_user'),
                'queue_process_status' => 'Post',
            ]));
            $now = now()->format('Y-m-d');
            DB::select("SELECT * FROM asset.sp_funct_generate_journal_asset($asset->id_asset, $asset->id_company, '$now', 'Addition')");
            DB::commit();
            return $this->success($asset, "Asset has been set to Post successfully!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }

    public function batchAdd(Request $request) {
        ini_set('max_execution_time', 3600);
        $request->validate([
            'import_attachment' => 'required|file'
        ]);

        DB::beginTransaction();
        try {
            $asset = [];
            $type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($request->import_attachment);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load($request->import_attachment);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $assetFailedQueues = [];

            foreach($sheetData as $rowIndex => $row) {
                if($rowIndex == 0) continue;
                try {
                    $assetCategory = MasterAssetCategory::where('asset_category_code', $row[0])->active()->currentCompany()->first();
                    $assetGroup = MasterAssetGroup::where('asset_group_code', $row[1])->active()->currentCompany()->first();
                    $description = $row[2];
                    $assetType = $row[3];
                    $ownership = $row[4];
                    $propertyType = $row[5];
                    $bought = $row[6];
                    $inUsedFlag = $row[7] == 'TRUE';
                    $depreciationFlag = $row[8] == 'TRUE';
                    $depreciationMethod = DepreciationMethod::where('depreciation_code', $row[9])->active()->currentCompany()->first();
                    $salvageType = $row[10];
                    $salvageValue = $row[11];
                    $lifeInMonth = $row[12];
                    $depreciationStart = $row[13];
                    $originalCost = $row[14];
                    $employee = HrEmployee::where('nik_employee', $row[15])->active()->first();
                    $branchEmployee = MasterBranch::where('branch_code', $row[16])
                        ->where('id_company', session('id_company'))
                        ->where('status', 'A')
                        ->first();
                    $locationEmployee = MasterLocation::where('location_code', $row[17])
                        ->where('id_company', session('id_company'))
                        ->where('status', 'A')
                        ->where('id_branch', $branchEmployee->id_branch ?? null)
                        ->first();
                    $assetLocation = MasterAssetLocation::currentCompany()
                        ->active()
                        ->where('id_location', $locationEmployee->id_location ?? null)
                        ->where('location_code', $row[18])
                        ->first();
                    $purchaseDate = $row[19];
                    $receivingDate = $row[20];
                    $referenceNumber = $row[21];
                    $depreciationCost = $row[22];
                } catch(Exception $e) {
                    throw new Exception($e->getMessage().". Please make sure to use the template.");
                }

                if(!$assetGroup) {
                    throw new Exception("Asset group ".$row[1]." not found.");
                }

                if($receivingDate && (!$depreciationStart || is_null($depreciationStart))) {
                    $config = AssetConfigSettings::currentCompany()->active()->first();
                    $receivingDate = Carbon::parse($receivingDate);
                    $depreciationStart = clone $receivingDate;
                    if($config->next_month_depreciation_start) {
                        if($config->next_month_depreciation_start <= (int)$receivingDate->format('d')) {
                            $depreciationStart->addMonth()->startOfMonth()->format('Y-m-d');
                        }
                    }
                }

                $assetNumberingDate = Carbon::parse($receivingDate ?? $purchaseDate);
                $assetNumber = $this->generateAssetNumber(session('id_company'), $assetGroup->id_asset_group, $assetNumberingDate);


                $assetPayload = [
                    'id_asset_category' => $assetCategory->id_asset_category,
                    'id_asset_group' => $assetGroup->id_asset_group,
                    'property_type' => $propertyType,
                    'asset_type' => $assetType,
                    'ownership' => $ownership,
                    'bought' => $bought,
                    'asset_number' => $assetNumber,
                    'description' => $description,
                    'life_in_month' => $lifeInMonth ?? $assetGroup->life_in_month,
                    'in_used_flag' => $inUsedFlag,
                    'depreciation_flag' => $depreciationFlag,
                    'id_depreciation_method' => $depreciationMethod->id_depreciation_method ?? NULL,
                    'depreciation_start_date' => $depreciationStart,
                    'depreciation_cost' => $depreciationCost,
                    'salvage_type' => $salvageType ?? $assetGroup->salvage_type,
                    'salvage_value' => $salvageValue ?? $assetGroup->salvage_value,
                    'original_cost' => $originalCost,
                    'receiving_date' => $receivingDate,
                    'purchase_date' => $purchaseDate,
                    'reference_number' => $referenceNumber,
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ];
                $createdAsset = Asset::create($assetPayload);

                $createdAsset->assigned_employee = AssetEmployeeAssigned::create([
                    'id_asset' => $createdAsset->id_asset,
                    'transaction_date' => now()->format('Y-m-d'),
                    'id_employee' => $employee->id_employee ?? null,
                    'unit_assigned' => 1,
                    'id_account' => null,
                    'id_branch' => $branchEmployee->id_branch,
                    'id_location' => $locationEmployee->id_location ?? null,
                    'id_asset_location' => $assetLocation->id_asset_location ?? null,
                    'status' => 'A',
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ]);
                $asset[] = $createdAsset;
            }

            DB::commit();
            return $this->success($asset, count($asset)." assets has been imported successfully!");
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage());
        }
    }
}