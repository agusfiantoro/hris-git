<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OasysIntegrationDetail extends Model {
    protected $table = "integration.biz_approval_integration_request_detail";
    protected $primaryKey = 'id_integration_request_detail';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'id_integration_request_header',
        'id_branch',
        'id_principal',
        'id_company',
        'created_by',
        'updated_by',
    ];
}