<?php

namespace App\Models\Organization\OrganizationStructure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobPositionDetail extends Model {

    use HasFactory;

    protected $table = 'master_position_detail';
    protected $primaryKey = 'id_position_detail';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_position_detail', 'id_position_routing', 'description', 'status', 'id_branch', 'id_location', 'id_employee', 'parent_id_position_detail', 'remark', 'inactive_date', 'id_company', 'created_by', 'updated_by'
    ];

}
