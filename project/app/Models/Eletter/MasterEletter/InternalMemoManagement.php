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

class InternalMemoManagement extends Model
{
	public static function get_imm($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// TAMPIL IMM
		->leftJoin('master_general_data as data_imm','data_imm.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		// ->groupBy('master_department.description','master_region.description','master_branch.description','master_company.company_name','hr_electronic_letter.reference_number','hr_electronic_letter.id_letter','hr_electronic_letter.date','hr_electronic_letter.email','hr_electronic_letter.notes')
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_general_data.code','SED');
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
		}
		$data->orderBy('hr_electronic_letter.id_letter','DESC')
		->get();
		return $data;
	}
	public static function get_edit_imm($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_imm','data_imm.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.id_company as id_company'),
			\DB::RAW('hr_electronic_letter.id_region as id_region'),
			\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
			\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
			// \DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_general_data.code','SED')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
	public static function get_imm_view($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// ->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee_chief')
		// ->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->leftJoin('master_general_data as data_imm','data_imm.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_company.company_name as company_name'),
			// \DB::RAW('master_position_routing.description as dec_position'),
			// \DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('master_general_data.code','SED')
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
	public static function get_region()
	{
		$region = MasterRegional::select(
			\DB::RAW('description'),
			\DB::RAW('id_region')
		)
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $region;
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
		->where('id_company',session('id_company'))
		->where('status','A')
		->orderBy('name','ASC')
		->get();
		return $employee;
	}
	public static function get_branch($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$branch = MasterBranch::select(
			\DB::RAW('description'),
			\DB::RAW('id_branch')
		)
		->where('id_company',session('id_company'))
		->where('id_region',$request->region_id);
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
		->where('master_general_data.code','SED')
		->where('master_general_data.id_company',session('id_company'))
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
		$format = $kode."/".$company->company_code."-".$id_letter_type->code."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
		return ['format'=>$format,'id_letter_type'=>$id_letter_type];
	}
}
