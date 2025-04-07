<?php

namespace App\Models\Employee\EmployeeReco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmployeeRecoQuantitative extends Model
{
	use HasFactory;
	
    protected $table = 'public.hr_recommendation_quantitative';
	protected $primaryKey = 'id_recommendation_quantitative';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
}
