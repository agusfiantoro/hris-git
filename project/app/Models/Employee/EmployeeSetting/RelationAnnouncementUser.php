<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationAnnouncementUser extends Model {

    use HasFactory;

    protected $table = 'relation_announcement_users';
    protected $primaryKey = 'id';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id', 'id_user', 'id_announcement', 'id_company','created_by', 'updated_by'
    ];

}
