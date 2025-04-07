<?php

namespace App\Models\Task\Missions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
//use App\Models\Employee\Employee\Employee;

class AttachAnswer extends Model
{
	use HasFactory;
	
    protected $table = 'hr_task_attachment_answer';
	protected $primaryKey = 'id_task_attachment_answer';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
}
