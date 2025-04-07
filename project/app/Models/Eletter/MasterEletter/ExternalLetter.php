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

class ExternalLetter extends Model
{
    // use HasFactory;
	public static function get_ext($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		// ->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		// ->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		// ->leftJoin('hr_employee','hr_electronic_letter.id_employee_chief','=','hr_employee.id_employee')
		// ->leftJoin('master_position_routing','hr_electronic_letter.id_position_routing_chief','=','master_position_routing.id_routing')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_ext','data_ext.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_company.company_name as company_name')
			// \DB::RAW('master_department.description as dec_dept'),
			// \DB::RAW('master_region.description as dec_region'),
			// \DB::RAW('master_branch.description as dec_branch')
		)
		->where('data_ext.code','EXT')
		->where('hr_electronic_letter.id_company',session('id_company'));
		$access_group = session('access_group');
		if ($id_branch == NULL AND $access_group == NULL) {
			
		}elseif ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));			
		}else{

		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
		->get();
		return $data;
	}
	// public static function get_branch($request)
	// {
	// 	$id_branch = ElectronicLetter::accessBranch($request);
	// 	$data = MasterBranch::select(
	// 		\DB::RAW('description'),
	// 		\DB::RAW('id_branch')
	// 	)
	// 	->where('id_company',session('id_company'))
	// 	->where('status','A');
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->get();
	// 	// ->get();
	// 	return $data;
	// }
	public static function get_edit_ext($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		// ->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		// ->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		// ->leftJoin('hr_employee','hr_electronic_letter.id_employee_chief','=','hr_employee.id_employee')
		// ->leftJoin('master_position_routing','hr_electronic_letter.id_position_routing_chief','=','master_position_routing.id_routing')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_ext','data_ext.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_company as id_company'),
			\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
			\DB::RAW('hr_electronic_letter.id_region as id_region'),
			\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
			// \DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('master_general_data.description as category'),
			// \DB::RAW('master_position_routing.description as jabatan_pemberi'),
			\DB::RAW('master_company.company_name as company_name'),
			// \DB::RAW('master_department.description as dec_dept'),
			// \DB::RAW('master_department.id_dept as id_dept'),
			// \DB::RAW('master_region.description as dec_region'),
			// \DB::RAW('master_region.id_region as id_region'),
			\DB::RAW('master_branch.description as dec_branch')
			// \DB::RAW('hr_employee.name as nama_pemberi')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_ext.code','EXT')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
	public static function get_category()
	{
		$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.description as description'),
			\DB::RAW('data_category.id_general_data as id_general_data')
		)
		->where('data_category.id_company',session('id_company'))
		->where('data_category.status','A')
		->where('data_category.code','!=','CA')
		->where('master_general_data.code','EXT')
		->orderBy('data_category.description','ASC')
		->get();
		return $category;
	}
	// public static function get_dept()
	// {
	// 	$department = DB::table('master_department')
	// 	->select(\DB::RAW('id_dept'),\DB::RAW('description'))
	// 	->where('id_company',session('id_company'))
	// 	->get();
	// 	return $department;
	// }
	// public static function get_region($request)
	// {
	// 	$id_branch = ElectronicLetter::accessBranch($request);
	// 	if ($id_branch == NULL) {
	// 		$region = MasterRegional::select(
	// 			\DB::RAW('id_region'),
	// 			\DB::RAW('description')
	// 		)
	// 		->where('id_company',session('id_company'))
	// 		->get();
	// 	}else{
	// 		$region = MasterRegional::join('master_branch','master_branch.id_region','=','master_region.id_region')
	// 		->select(
	// 			\DB::RAW('master_region.id_region as id_region'),
	// 			\DB::RAW('master_region.description as description')
	// 		)
	// 		->where('master_region.id_company',session('id_company'))
	// 		->whereIn('master_branch.id_branch',explode(',', $id_branch))
	// 		->distinct()
	// 		->get();
	// 	}
	// 	return $region;
	// }
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
	public static function get_branch($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$branch = MasterBranch::select(
			\DB::RAW('id_branch'),
			\DB::RAW('description')
		)
		// ->where('id_region',$request->region_id)
		->where('id_company',session('id_company'))
		->where('status','A');
		if ($id_branch != NULL) {
			$branch->whereIn('id_branch',explode(',', $id_branch));
		}
		$branch = $branch->orderBy('description','ASC')->get();
		return $branch;
	}
	public static function format_save($request)
	{
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.id_company',session('id_company'))
		->where('master_general_data.code','=','EXT')
		->first();
		$department = DB::table('master_department')
		->select(
			\DB::RAW('department_code')
		)
		->where('id_company',session('id_company'))
		->where('id_dept',$request->id_dept)
		->first();
		// $department_code = explode("_", $department->department_code)[1];
			// dd($department_code);
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
		->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_company.company_code',$company->company_code)
		->where('hr_electronic_letter.id_dept',$request->id_dept)
		// ->where('hr_electronic_letter.id_region',$request->id_region)
		// ->where('hr_electronic_letter.id_category',$request->id_category)
		->where('master_general_data.code',$category->code)
		->where('hr_electronic_letter.id_letter_type',$id_letter_type->id_general_data)
		->max('hr_electronic_letter.reference_number');
		$no_urut = substr($id_letter, 0,4);
		$no_urut++;
		$kode = sprintf("%04s", abs($no_urut));
		// $region = MasterRegional::where('id_region',$request->id_region)->first();
		// if ($region->description == "Pusat") {
		// 	$code_region = "HQ";
		// }else{
		// 	$code_region = $region->region_code;
		// }
		$format = $kode."/".$company->company_code."-".$category->code."/".$bulan."/".substr($tahun,-2);
		return ['format'=>$format,'id_letter_type'=>$id_letter_type];
	}
}
