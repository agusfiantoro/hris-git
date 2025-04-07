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

class ThirdAggrement extends Model
{
    // use HasFactory;
	public static function get_category()
	{
		$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.id_general_data as id_general_data'),
			// \DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.description as description')
		)
		->where('data_category.status','A')
		->where('data_category.id_company',session('id_company'))
		->where('master_general_data.status','A')
		->where('master_general_data.code','PK3')
		->where('master_general_data.id_company',session('id_company'))
		->orderBy('data_category.description','ASC')
		->get();
		return $category;
	}
	public static function get_region()
	{
		$data = MasterRegional::select(
			\DB::RAW('id_region'),
			\DB::RAW('description')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $data;
	}
	public static function get_dept()
	{
		$data = DB::table('master_department')
		->select(
			\DB::RAW('id_dept'),
			\DB::RAW('description')
		)
		->Where('status','A')
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $data;
	}
	public static function change_region($request)
	{
		$data = MasterBranch::select(
			\DB::RAW('id_branch'),
			\DB::RAW('description')
		)
		->where('status','A')
		->where('id_region',$request->id_region)
		->orderBy('description','ASC')
		->get();
		return $data;
	}
	public static function format_save($request)
	{
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.code','PK3')
		->where('master_general_data.id_company',session('id_company'))
		->first();
		$department = DB::table('master_department')
		->select(
			\DB::RAW('department_code')
		)
		->where('id_company',session('id_company'))
		->where('id_dept',$request->id_dept)
		->first();
		$department_code = explode("_", $department->department_code)[1];
		$category = MasterGeneralData::select(
			\DB::RAW('code')
		)
		->where('id_general_data',$request->id_category)
		->where('id_company',session('id_company'))
		->first();
		$company = DB::table('master_company')->where('id_company',session('id_company'))->first();
		$bulan = date('m', strtotime($request->date));
		$tahun = date('Y', strtotime($request->date));
		$id_letter = ElectronicLetter::join('master_company','hr_electronic_letter.id_company','=','master_company.id_company')
		->whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_company.company_code',$company->company_code)
		->where('hr_electronic_letter.id_region',$request->id_region)
		->where('hr_electronic_letter.id_category',$request->id_category)
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
		$format = $kode."/".$company->company_code."-".$category->code."/".$department_code."-".$code_region."/".$bulan."/".substr($tahun,-2);
		return [
			'format'=>$format,
			'id_letter_type'=>$id_letter_type
		];
	}
	public static function get_tag($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// 
		->leftJoin('master_general_data as data_tag','data_tag.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_department.description as dc_dept'),
			\DB::RAW('master_region.description as dc_region'),
			\DB::RAW('master_general_data.code as category'),
			\DB::RAW('master_general_data.description as dec_category'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_company.company_code as company_cd'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_tag.code','PK3');
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter', 'DESC')
		->get();
		return $data;
	}
	public static function get_edit($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// 
		->leftJoin('master_general_data as data_tag','data_tag.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_department.description as dc_dept'),
			\DB::RAW('master_region.description as dc_region'),
			\DB::RAW('master_general_data.code as category'),
			\DB::RAW('master_general_data.description as dec_category'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_company.company_code as company_cd'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_region as id_region'),
			\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_tag.code','PK3')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
}
