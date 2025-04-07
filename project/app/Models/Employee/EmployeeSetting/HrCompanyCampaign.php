<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrCompanyCampaign extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'hr_company_campaign';
    protected $primaryKey = 'id_company_campaign';

    protected $fillable = [
        'id_company_campaign', 'reference_number', 'description', 'id_employee_request', 'start_date','end_date','image_poster','attachment', 'link', 'id_company', 'creation_date', 'update_date', 'update_date', 'updated_by'
    ];

    public static function getReffNumber(){
        $monthyear = date('Y').date('m');
        $com = DB::table('master_company')->where('id_company', session('id_company'))->first();
        $noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%{$monthyear}%")->max('reference_number'), 17, 22);
        
        if($noUrutAkhir) {
            $newSequence = $noUrutAkhir+1;
        } else {
            $newSequence = 1;
        }
        $nomor = $com->company_code.'-CPGN-'.$monthyear.'-'.str_pad($newSequence, 6, "0", STR_PAD_LEFT);
        return $nomor;      
    }
}