<?php

namespace App\Models\Assets;

use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterChartAccount extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'accounting.master_chart_account';
    protected $primaryKey = 'id_account';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'account_number',
        'account_name',
        'account_type',
        'status',
        'inactive_date',
        'id_company',
        'created_by',
        'updated_by',
        'parent_id_account',
    ];

    public function scopeIsTransactable($query) {
        $query->where("$this->table.is_transactable", true);
    }
}