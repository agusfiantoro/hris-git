<?php

namespace App\Models\Task\MasterTask;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterActivity extends Model
{
	use HasFactory;
	
    protected $table = 'master_activity';
	protected $primaryKey = 'id_activity';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
}
