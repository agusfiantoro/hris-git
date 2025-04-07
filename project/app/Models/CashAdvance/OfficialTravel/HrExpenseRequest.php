<?php

namespace App\Models\CashAdvance\OfficialTravel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrExpenseRequest extends Model
{
    // use HasFactory;
	protected $table="hr_expense_request";
	protected $primaryKey="id_expense_request";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}
