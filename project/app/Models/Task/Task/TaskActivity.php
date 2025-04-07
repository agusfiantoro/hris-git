<?php

namespace App\Models\Task\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
//use App\Models\Employee\Employee\Employee;

class TaskActivity extends Model
{
	use HasFactory;
	
    protected $table = 'hr_task_activity';
	protected $primaryKey = 'id_task_activity';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
}
