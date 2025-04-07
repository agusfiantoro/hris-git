<?php

namespace App\Models\Organization\MasterOrganization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterCountry extends Model {

    use HasFactory;

    protected $table = 'master_country';
    protected $primaryKey = 'id_country';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_country','country_code', 'description', 'status', 'inactive_date', 'created_by', 'updated_by'
    ];

}
