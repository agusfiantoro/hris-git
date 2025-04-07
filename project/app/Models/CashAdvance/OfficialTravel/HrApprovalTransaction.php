<?php

namespace App\Models\CashAdvance\OfficialTravel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrApprovalTransaction extends Model
{
    // use HasFactory;
	protected $table="hr_approval_transaction";
	protected $primaryKey="id_approval_transaction";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}
