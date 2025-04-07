<?php

namespace App\Models\CareerAdministration\MasterBoarding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationChecklist extends Model {

    protected $table = 'relation_checklist_employee';
    protected $primaryKey = 'id_relation_checklist_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_relation_checklist_employee', 'id_checklist_employee', 'id_position_detail_assigned', 'id_company', 'created_by', 'updated_by'
    ];
	
}
