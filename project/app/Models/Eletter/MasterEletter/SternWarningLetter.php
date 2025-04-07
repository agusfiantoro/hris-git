<?php

namespace App\Models\Eletter\MasterEletter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Employee\Employee\Employee;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Eletter\MasterEletter\ElectronicLetter;

class SternWarningLetter extends Model
{
    public function cek_exp_spdt() // SP (PHK)
    {
    	$cek_ex = ElectronicLetter::leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
    	->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','master_general_data.relation_to_id_general_data')
    	->select(
    		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
    		\DB::RAW('hr_electronic_letter.effective_date as effective_date')
    	)
    	->where('hr_electronic_letter.status','A')
    	->where('hr_electronic_letter.id_company',session('id_company'))
        ->where('data_sp.code','SP3')
    	->where('data_sp.id_company',session('id_company'))
    	->whereIn('master_general_data.code',['SP3','SPDT'])
    	->get();
    	foreach ($cek_ex as $cex) {
    		if ($cex->effective_date >= now()->subMonths(6)->toDateString()) {
    		}else{
    			ElectronicLetter::where('id_letter',$cex->id_letter)
    			->update([
    				'status'=>'I'
    			]);
    		}
    	}
    }

    public static function get_swp($request)
    {
    	$id_branch = ElectronicLetter::accessBranch($request);
    	$data = DB::table('hr_electronic_letter')
    	->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
    	->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
    	->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
    	->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
    	->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
    	->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
    	->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
    	->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
    	->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
    	->leftJoin('master_general_data as data_sp3','data_sp3.id_general_data','=','master_general_data.relation_to_id_general_data')
    	->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
    		\DB::RAW('hr_electronic_letter.email as email'),
    		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
    		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
    		\DB::RAW('hr_electronic_letter.token as token'),
    		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
    		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
    		\DB::RAW('hr_electronic_letter.date as date'),
    		\DB::RAW('hr_electronic_letter.status as status'),
    		\DB::RAW('hr_employee.name as name'),
    		\DB::RAW('hr_employee.nik_employee as nik_employee'),
    		\DB::RAW('master_company.company_name as company_name'),
    		\DB::RAW('master_department.description as dec_dept'),
    		\DB::RAW('master_branch.description as dec_branch'),
    		\DB::RAW('master_region.description as dec_region'),
    		\DB::RAW('master_job_grade.description as dec_job_grade'),
    		\DB::RAW('master_general_data.description as category'),
    		\DB::RAW('master_position_routing.description as dec_position')
    	)
    	->where('hr_electronic_letter.id_company',session('id_company'))
    	->where('master_position_detail.secondary_position','false')
    	->where('data_sp3.code','SP3')
    	->whereIn('master_general_data.code',['SP3','SPDT']);
    	if ($id_branch != NULL) {
    		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
    	}
    	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
    	->get();
    	return $data;
    }
    public static function get_category()
    {
    	$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
    	->select(
    		\DB::RAW('data_category.id_general_data'),
    		\DB::RAW('data_category.description'),
    		\DB::RAW('data_category.code')
    	)
        ->where('master_general_data.code','SP3')
        ->where('master_general_data.id_company',session('id_company'))
        ->where('data_category.status','A')
        ->where('data_category.id_company',session('id_company'))
        ->orderBy('data_category.description','ASC')
        ->get();
        return $category;
    }
    public function get_location()
    {
        $data = MasterLocation::select(
            \DB::RAW('description')
        )
        ->where('status','A')
        ->where('id_company',session('id_company'))
        ->orderBy('description','ASC')
        ->get();
        return $data;
    }
    public static function get_employee_chief()
    {
    	$employee = Employee::select(
    		\DB::RAW('id_employee'),
    		\DB::RAW('name')
    	)
    	->where('id_company',session('id_company'))
    	->where('status','A')
        ->orderBy('name','ASC')
    	->get();
    	return $employee;
    }
    public static function format_save($request)
    {
        $employee_name = Employee::where('id_employee',$request->id_employee)->first();
        $token = md5($employee_name->name).strtotime('now');
        $id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
        ->select(
            \DB::RAW('master_general_data.code as code'),
            \DB::RAW('master_general_data.id_general_data as id_general_data')
        )
        ->where('master_general_data.code','SP3')
        ->where('master_general_data.id_company',session('id_company'))
        ->first();
        $company = DB::table('master_company')->where('id_company',session('id_company'))->first();
        $bulan = date('m', strtotime($request->effective_date));
        $tahun = date('Y', strtotime($request->effective_date));
        $id_letter = ElectronicLetter::join('master_company','hr_electronic_letter.id_company','=','master_company.id_company')
        ->whereMonth('hr_electronic_letter.effective_date',$bulan)
        ->whereYear('hr_electronic_letter.effective_date',$tahun)
        ->where('hr_electronic_letter.id_company',session('id_company'))
        ->where('master_company.company_code',$company->company_code)
        ->where('hr_electronic_letter.id_region',$request->id_region)
        ->where('hr_electronic_letter.id_category',$request->id_category)
        ->where('hr_electronic_letter.id_letter_type',$id_letter_type->id_general_data)
        ->max('hr_electronic_letter.reference_number');
        $no_urut = substr($id_letter, 0,4);
        $no_urut++;
        $kode = sprintf("%04s", abs($no_urut));
        $region = MasterRegional::where('id_region',$request->id_region)->first();
        if ($region->description == "Pusat") {
            $code_region = "HQ";
        }else{
            $code_region = $region->region_code;
        }
        $category = MasterGeneralData::where('id_general_data',$request->id_category)
        ->where('id_company',session('id_company'))
        ->first();
        if ($category->code != "SPDT") {
            $kategori = "SP/".substr($category->code, -1);
        }else{
            $kategori = $category->code;
        }
        $principalTextValue = "";
        foreach ($request->id_principal as $key => $value) {
            $principalTextValue .= $value . ",";
        }
        $principalTextValue = rtrim($principalTextValue, ',');
        $format = $kode."/".$company->company_code."-".$kategori."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
        return [
            'format'=>$format,
            'id_letter_type'=>$id_letter_type,
            'token'=>$token,
            'principalTextValue'=>$principalTextValue
        ];
    }
}
