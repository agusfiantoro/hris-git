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

class InternshipCertificates extends Model
{
	public static function get_ski()
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_ski','data_ski.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.token as token'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_ski.code','SKI')
		->orderBy('hr_electronic_letter.id_letter','DESC')
		->get();
		return $data;
	}
	public static function get_edit_ski($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_ski','data_ski.id_general_data','=','master_general_data.relation_to_id_general_data')
		// CHIEF
		->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('chief_name.name as name_chief'),
			\DB::RAW('master_position_routing.description as position_chief')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_ski.code','SKI')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
	public static function get_print_ski($token)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_ski','data_ski.id_general_data','=','master_general_data.relation_to_id_general_data')
		// CHIEF
		->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('chief_name.name as name_chief'),
			\DB::RAW('master_position_routing.description as position_chief')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_ski.code','SKI')
		->where('hr_electronic_letter.token',$token)
		->get();
		return $data;
	}
	public static function get_dept()
	{
		$dept = DB::table('master_department')
		->select(
			\DB::RAW('description'),
			\DB::RAW('id_dept')
		)
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $dept;
	}
	public static function get_employee()
	{
		$employee = Employee::select(
			\DB::RAW('name'),
			\DB::RAW('id_employee')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->orderBy('name','ASC')
		->get();
		return $employee;
	}
	public static function get_category()
	{
		$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.description as description'),
			\DB::RAW('data_category.id_general_data as id_general_data')
		)
		->where('master_general_data.code','SKI')
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
	// public static function branch_print()
	// {
	// 	$branch = Employee::join('master_users','master_users.id_user','=','hr_employee.id_user')
	// 	->join('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 	->join('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
	// 	->select(\DB::RAW('master_branch.description as branch_name'))
	// 	->groupBy('master_branch.description')
	// 	->where('master_users.id_user',session('id_user'))
	// 	->first();
	// 	return $branch;
	// }
	public static function format_save($request)
	{
		$token = md5($request->remark_1).strtotime('now');
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_letter','data_letter.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.description as description'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.code','SKI')
		->where('master_general_data.id_company',session('id_company'))
		->first();
		$company = DB::table('master_company')->where('id_company',session('id_company'))->first();
		$region = Employee::join('master_users','master_users.id_user','=','hr_employee.id_user')
		->join('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
		->join('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		->join('master_region','master_region.id_region','=','master_branch.id_region')
		->select(
			\DB::RAW('master_region.description as description'),
			\DB::RAW('master_region.id_region as id_region')
		)
		->where('master_users.id_user',session('id_user'))
		->first();
		$category = MasterGeneralData::select(
			\DB::RAW('description'),
			\DB::RAW('code'),
			\DB::RAW('id_general_data')
		)
		->where('id_general_data',$request->id_category)
		->where('id_company',session('id_company'))
		->first();
		if ($region->description == "Pusat") {
			$code_region = "HQ";
		}else{
			$code_region = $region->region_code;
		}
		$bulan = date('m', strtotime($request->date));
		$tahun = date('Y', strtotime($request->date));
		$id_letter = ElectronicLetter::join('master_company','hr_electronic_letter.id_company','=','master_company.id_company')
		->whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_company.company_code',$company->company_code)
		->where('hr_electronic_letter.id_region',$region->id_region)
		->where('hr_electronic_letter.id_category',$request->id_category)
		->max('hr_electronic_letter.reference_number');
		$no_urut = substr($id_letter, 0,4);
		$no_urut++;
		$kode = sprintf("%04s", abs($no_urut));
		$format = $kode."/".$company->company_code."-".$category->code."/HR-".$code_region."/".$bulan."/".substr($tahun,-2);
		return [
			'format'=>$format,
			'token'=>$token,
			'id_letter_type'=>$id_letter_type,
			'region'=>$region
		];
	}

}
