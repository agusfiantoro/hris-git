<?php

namespace App\Models\Assets\AdditionAsset;

use App\Models\Assets\DepreciationSettings\AssetDepreciation;
use App\Models\Assets\FinancialAsset\FinancialAsset;
use App\Traits\StandardModelScope;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Asset extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_asset';
    protected $primaryKey = 'id_asset';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_mass_addition',
        'id_asset_category',
        'asset_type',
        'id_asset_group',
        'asset_number',
        'manufacture_name',
        'model_number',
        'serial_number',
        'tag_number',
        'warranty_number',
        'warranty_date',
        'tax_expired_date',
        'id_parent_asset',
        'in_used_flag',
        'depreciation_flag',
        'depreciation_start_date',
        'description',
        'original_cost',
        'adjusted_cost',
        'depreciation_cost',
        'current_cost',
        'salvage_type',
        'salvage_value',
        'property_type',
        'ownership',
        'bought',
        'leased_number',
        'lease_effective_date',
        'lease_expired_date',
        'lease_contract_expired_date',
        'partner_name',
        'receiving_number',
        'purchase_invoice_number',
        'project_number',
        'batch_number',
        'current_units',
        'id_partner',
        'id_purchase_receiving_header',
        'id_purchase_invoice_detail',
        'id_project',
        'id_batch',
        'id_depreciation_method',
        'depreciation_start_date',
        'life_in_month',
        'queue_process_status',
        'status',
        'id_company',
        'created_by',
        'updated_by',
        'reference_number',
        'notes_1',
        'notes_2',
        'notes_3',
        'notes_4',
        'notes_5',
        'notes_6',
        'notes_7',
        'notes_8',
        'purchase_date',
        'receiving_date',
    ];

    /**
     * Scope a query to only show asset which have their warranty currently active or not
     */
    public function scopeInWarranty($query, bool $active = true) {
        $comparator = '>=';
        if(!$active) {
            $comparator = '<';
        }
        $query->where('fa_asset.warranty_date', $comparator, 'NOW()::date');
    }

    public function scopeHasAssignedEmployee($query) {
        $query->join('asset.fa_employee_assigned as fea', 'fa_asset.id_asset', 'fea.id_asset');
        $query->where('fea.status', 'A');
    }

    public function scopeWithAssignedEmployeeAndDetails($query, $idEmployee = null, $includeNullAssignee = false) {
        $query->leftJoin('asset.fa_employee_assigned as fea', 'fa_asset.id_asset', 'fea.id_asset');
        $query->leftJoin('public.master_branch as mb', 'fea.id_branch', 'mb.id_branch');
        $query->leftJoin('public.master_location as ml', 'fea.id_location', 'ml.id_location');
        $query->leftJoin('asset.fa_asset_location as fal', 'fea.id_asset_location', 'fal.id_asset_location');
        $query->leftJoin('public.hr_employee as he', 'fea.id_employee', 'he.id_employee');
        $query->leftJoin('asset.fa_asset_category as fac', 'fa_asset.id_asset_category', 'fac.id_asset_category');
        $query->where('fea.status', 'A');
        $query->where(function($q) use($idEmployee, $includeNullAssignee) {
            if($idEmployee) {
                $q->where('fea.id_employee', $idEmployee);
                if($includeNullAssignee) {
                    $q->orWhereNull('fea.id_employee');
                }
            }
        });
    }

    public function scopeInUse($query, $inUse = true) {
        $query->where("$this->table.in_used_flag", $inUse);
    }

    public function scopePosted($query, bool $isPosted = true) {
        $sign = $isPosted ? "=" : "!=";
        $query->where("$this->table.queue_process_status", $sign, "Post");
    }

    public function scopeQueueProcessStatus($query, string $sign = "=", array $queueProcessStatus) {
        if($sign == "=") {
            $query->whereIn("$this->table.queue_process_status", $queueProcessStatus);
        } else if($sign == "!=") {
            $query->whereNotIn("$this->table.queue_process_status", $queueProcessStatus);
        } else throw new Exception("Invalid sign '$sign'");
    }

    public function assetCategory() {
        return $this->hasOne(MasterAssetCategory::class, 'id_asset_category', 'id_asset_category');
    }

    public function assetGroup() {
        return $this->hasOne(MasterAssetGroup::class, 'id_asset_group', 'id_asset_group');
    }

    public function massAddition() {
        return $this->belongsTo(MassAddition::class, 'id_mass_addition', 'id_mass_adition');
    }

    public function parentAsset() {
        return $this->belongsTo(Asset::class, 'id_parent_asset', 'id_asset');
    }

    public function assignedEmployee(): HasMany {
        return $this->hasMany(AssetEmployeeAssigned::class, 'id_asset', 'id_asset');
    }

    public function depreciation(): HasMany {
        return $this->hasMany(AssetDepreciation::class, 'id_asset', 'id_asset')->orderByDesc('depreciation_date');
    }

    public function costHistory(): HasMany {
        return $this->hasMany(FinancialAsset::class, 'id_asset', 'id_asset')->orderByDesc('accounting_date');
    }

    public function image(): HasMany {
        return $this->hasMany(AssetImage::class, 'id_asset', 'id_asset')->active();
    }

    public static function assigneeHistory($idAsset) {
        $assignees = DB::select("SELECT 'past' AS type, ftd.id_transfer_detail AS id, ftd.effective_date AS start_date, sfath.* FROM asset.sp_funct_asset_transfer_history (
                  null,
                  $idAsset,
                  null,
                  null,
                  null,
                  null
                ) sfath
                JOIN asset.fa_transfer_detail ftd ON sfath.id_employee = ftd.id_employee_source AND sfath.id_asset = ftd.id_asset
                UNION 
                SELECT 
                	'current' AS type,
                	fea.id_employee_assigned AS id,
                	COALESCE(fea.update_date, fea.creation_date)::date AS start_date,
                	fea.id_asset,
                	fa.asset_number,
                	fa.description AS asset_name,
                	fea.id_employee,
                	he.nik_employee,
                	he.name AS employee_name,
                	fea.id_branch,
                	mb.description AS branch_name,
                	fea.id_location,
                	ml.description AS location_name,
                	fea.id_asset_location,
                	fal.description AS room_name
                FROM asset.fa_employee_assigned fea 
                JOIN asset.fa_asset fa ON fea.id_asset = fa.id_asset
                JOIN hr_employee he ON fea.id_employee = he.id_employee
                JOIN master_branch mb ON fea.id_branch = mb.id_branch
                JOIN master_location ml ON fea.id_location = ml.id_location
                JOIN asset.fa_asset_location fal ON fea.id_asset_location = fal.id_asset_location
                WHERE fea.id_asset = ?
                ORDER BY type ASC, start_date DESC, id DESC",
                [$idAsset]);
        $assignees = collect($assignees);
        foreach($assignees as $key => $assignee) {
            $assignees[$key]->period_start = $assignees[$key]->start_date;
            $assignees[$key]->period_end = 'Now';
            if($key > 0) {
                $assignees[$key]->period_start = $assignees[$key]->start_date;
                $assignees[$key]->period_end = $assignees[$key-1]->start_date;
            }
        }
        return $assignees;
    }

    public static function listAsset(
        $idAsset = 'null', 
        $idCompany = 'null', 
        $idEmployee = 'null', 
        $nikEmployee = 'null', 
        $assetNumber = 'null',
        $serialNumber = 'null',
        $idAssetGroup = 'null',
        $idAssetCategory = 'null',
        $idBranch = 'null',
        $idLocation = 'null',
        $idAssetLocation = 'null',
        $assetOwnership = 'null'
    ) {
        if($nikEmployee && $nikEmployee != 'null') {
            $nikEmployee = "'$nikEmployee'";
        }
        if($assetNumber && $assetNumber != 'null') {
            $assetNumber = "'$assetNumber'";
        }
        if($serialNumber && $serialNumber != 'null') {
            $serialNumber = "'$serialNumber'";
        }
        if($assetOwnership && $assetOwnership != 'null') {
            $assetOwnership = "'$assetOwnership'";
        }
        return DB::select("SELECT 
                id_asset,
                asset_type,
                asset_number, 
                asset_name, 
                asset_category, 
                asset_group, 
                ownership,
                serial_number, 
	            reference_number,
                nik_employee, 
                employee_name, 
                branch, 
                location, 
                asset_location, 
                status
            FROM asset.sp_funct_asset_view (
                $idAsset,
                $idCompany,
                $idEmployee,
                $nikEmployee,
                $assetNumber,
                $serialNumber,
                $assetOwnership,
                $idAssetGroup,
                $idAssetCategory,
                $idBranch,
                $idLocation,
                $idAssetLocation
            ) sfav
            WHERE sfav.queue_process_status NOT IN('New', 'Confirm')
        ");
    }

    public static function retiredAssets($idCompany, $select = "fa.*") {
        $retiredAssets = DB::select("SELECT $select FROM asset.fa_retirement_header frh
                                    JOIN asset.fa_retirement_detail frd ON frh.id_retirement_header = frd.id_retirement_header
                                    JOIN asset.fa_asset fa ON frd.id_asset = fa.id_asset 
                                    JOIN public.master_general_data mgd ON frh.id_document_status = mgd.id_general_data 
                                    WHERE mgd.code = 'Approved'
                                    AND fa.id_company = ?
                                    GROUP BY fa.id_asset 
                                    ORDER BY fa.id_asset",
                                    [$idCompany]);
        return collect($retiredAssets);
    }
}