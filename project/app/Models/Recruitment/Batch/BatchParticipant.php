<?php

namespace App\Models\Recruitment\Batch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BatchParticipant extends Model
{
	use HasFactory;
	
    protected $table = 'psycho_batch_participant';
	protected $primaryKey = 'id_batch_participant';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_batch_participant', 'id_batch', 'id_candidate', 'id_employee', 'id_position_routing', 'source_type', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
