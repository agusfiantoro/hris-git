<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
	use HasFactory;
	
    protected $table = 'master_company';
	protected $primaryKey = 'id_company';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
     'id_company','company_code','company_name','company_type','description','address','company_email','company_phone','company_registry','company_logo','open_period','administrator_email','inactive_date','id_currency','created_by','updated_by'
    ];
	
	public static function getdata(){
		$data = DB::table('master_company')
		->join('master_currency', 'master_company.id_currency', '=', 'master_currency.id_currency')
		->select('master_company.*', 'master_currency.description as name')
		->get();
		
		return $data;
							
	}
	
	/*
	public static function getdata(){
		$sql = "SELECT
		mco.*, mcu.description as name 
					FROM  master_company mco
					JOIN  master_currency mcu 
					ON mco.id_currency = mcu.id_currency";
        $data = DB::select($sql);
		return $data;				
							
	}
	*/
	
}
