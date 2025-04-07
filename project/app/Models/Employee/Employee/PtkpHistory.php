<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PtkpHistory extends Model {

    use HasFactory;

    protected $table = 'hr_ptkp_history';
    protected $primaryKey = 'id_ptkp_history';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_ptkp_history','id_employee','ptkp_status', 'date_change_ptkp', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
