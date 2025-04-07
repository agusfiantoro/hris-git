<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterCurrency extends Model
{	
    protected $table = 'master_currency';	
	protected $primaryKey = 'id_currency';	
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
     'id_currency','currency_code','description'
    ];
	
}
