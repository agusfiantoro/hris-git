<?php

namespace App\Models\Assets\TransferSettings;

use App\Models\Assets\AdditionAsset\Asset;
use App\Models\Assets\HrEmployee;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetTransferDetail extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_transfer_detail';
    protected $primaryKey = 'id_transfer_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_transfer_header',
        'id_asset',
        'effective_date',
        'id_account_source',
        'id_branch_source',
        'id_location_source',
        'id_asset_location_source',
        'id_employee_source',
        'id_account_destination',
        'id_branch_destination',
        'id_location_destination',
        'id_asset_location_destination',
        'id_employee_destination',
        'unit_assigned',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];

    public function asset() {
        return $this->belongsTo(Asset::class, 'id_asset', 'id_asset');
    }

    public function sourceBranch() {
        return $this->belongsTo(MasterBranch::class, 'id_branch_source', 'id_branch');
    }

    public function sourceLocation() {
        return $this->belongsTo(MasterLocation::class, 'id_location_source', 'id_location');
    }

    public function sourceAssetLocation() {
        return $this->belongsTo(MasterAssetLocation::class, 'id_asset_location_source', 'id_asset_location');
    }

    public function sourceEmployee() {
        return $this->belongsTo(HrEmployee::class, 'id_employee_source', 'id_employee');
    }

    public function destinationEmployee() {
        return $this->belongsTo(HrEmployee::class, 'id_employee_destination', 'id_employee');
    }

    public function destinationBranch() {
        return $this->belongsTo(MasterBranch::class, 'id_branch_destination', 'id_branch');
    }

    public function destinationLocation() {
        return $this->belongsTo(MasterLocation::class, 'id_location_destination', 'id_location');
    }
}