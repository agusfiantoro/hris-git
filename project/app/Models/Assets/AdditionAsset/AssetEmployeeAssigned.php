<?php

namespace App\Models\Assets\AdditionAsset;

use App\Models\Assets\HrEmployee;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetEmployeeAssigned extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_employee_assigned';
    protected $primaryKey = 'id_employee_assigned';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_asset',
        'transaction_date',
        'id_employee',
        'unit_assigned',
        'id_account',
        'id_branch',
        'id_location',
        'id_asset_location',
        'status',
        'id_company',
        'created_by',
        'updated_by',
    ];

    public function employee() {
        return $this->hasOne(HrEmployee::class, 'id_employee', 'id_employee');
    }

    public function location() {
        return $this->hasOne(MasterLocation::class, 'id_location', 'id_location');
    }

    public function branch() {
        return $this->hasOne(MasterBranch::class, 'id_branch', 'id_branch');
    }

    public function assetLocation() {
        return $this->hasOne(AssetLocation::class, 'id_asset_location', 'id_asset_location');
    }
}