<?php

namespace App\Models\Setting\Responsibility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterMenu extends Model {

    use HasFactory;

    protected $table = 'master_menu';
    protected $primaryKey = 'id_menu';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_menu', 'id_responsibility', 'address_menu', 'menu_name', 'default_user', 'default_manager', 'default_administrator', 'status', 'inactive_date', 'created_by', 'updated_by'
    ];

}
