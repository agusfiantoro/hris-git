<?php

namespace App\Models\Eletter\MasterEletter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Organization\MasterOrganization\MasterRegional;

class NotifLetter extends Model
{
    // use HasFactory;
	public static function get_notif_etter($request)
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
		->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			\DB::RAW('hr_electronic_letter.token as token'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_general_data.code as category_code'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_region.description as dec_region'),
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_sk.code','SPB');
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
			\DB::RAW('data_category.id_general_data id'),
			\DB::RAW('data_category.description text'),
			\DB::RAW('data_category.code as code')
		)
		->where('master_general_data.code','SPB')
		->where('data_category.status','A')
		->where('data_category.id_company',session('id_company'))
		->orderBy('data_category.description','ASC')
		->get();
		return $category;
	}
	public static function get_user($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$user = DB::table('hr_employee')
		->leftJoin('master_position_detail as mpd_hrc','mpd_hrc.id_employee','=','hr_employee.id_employee')
		->select(
			\DB::RAW('hr_employee.id_employee id'),
			\DB::RAW('hr_employee.name text')
		)
		->where('hr_employee.id_company',session('id_company'))
		->where('hr_employee.status','A')
		->where('mpd_hrc.secondary_position','false');
		if ($id_branch != NULL) {
			$user->whereIn('mpd_hrc.id_branch',explode(',', $id_branch));
		}
		$user = $user->orderBy('hr_employee.name','ASC')->get();
		return $user;
	}
	public static function get_location()
	{
		$location = MasterLocation::select(
			\DB::RAW('description id'),
			\DB::RAW('description text')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $location;
	}
	public static function change_employee_notifletter($request)
	{
		$data = DB::table('hr_employee')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_employee.id_employment_status')
		->leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
		// ->join('master_location','master_location.id_branch','=','master_branch.id_branch')
		->leftJoin('master_company','hr_employee.id_company','=','master_company.id_company')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','master_position_routing.id_job_grade')
		->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
		->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
		->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
		->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
		->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
		->select(
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_position_detail.id_position_detail as id_routing'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_branch.id_branch as id_branch'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_company.id_company as id_company'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			\DB::RAW('master_job_grade.id_job_grade as id_job_grade'),
			\DB::RAW('master_department.id_dept as id_dept'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_principal.principal_code as dec_principal'),
			\DB::RAW('master_principal.id_principal as id_principal'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			// \DB::RAW('hr_employee.join_date as join_date'),
			\DB::RAW('hr_employee.resign_date as resign_date'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.id_employee as id_employee'),
			\DB::RAW('master_region.id_region as id_region'),
			\DB::RAW('status_employment.description as status'),
			\DB::RAW('status_employment.id_general_data as id_employment_status')
		)
		->where('hr_employee.id_company',session('id_company'))
		->where('hr_employee.id_employee',$request->id_employee)
		->where('master_position_detail.secondary_position','false')
		->get();
		return $data;
	}
	public static function format_save($request)
	{
		$employee_name = DB::table('hr_employee')->where('id_employee',$request->id_employee)->first();
		$token = md5($employee_name->name).strtotime('now');
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.code','SPB')
		->where('master_general_data.id_company',session('id_company'))
		->where('master_general_data.status','A')
		->first();
		$company = DB::table('master_company')->where('id_company',session('id_company'))->first();
		$bulan = date('m', strtotime($request->date));
		$tahun = date('Y', strtotime($request->date));
		$id_letter = ElectronicLetter::whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
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
		$principalTextValue = "";
		foreach ($request->id_principal as $key => $value) {
			$principalTextValue .= $value . ",";
		}
		$principalTextValue = rtrim($principalTextValue, ',');
		return [
			'format'=>$format,
			'id_letter_type'=>$id_letter_type,
			'principalTextValue'=>$principalTextValue,
			'token'=>$token
		];
	}
	public static function get_edit_notifletter($id_letter)
	{
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
		->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
		->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			// view
			\DB::RAW('employee_chief.name as name_chief'),
			\DB::RAW('routing_chief.description as position_chief'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('master_general_data.description as dec_category'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			\DB::RAW('status_employment.description as status')
		)
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
	public static function get_print_notifletter($token)
	{
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
		->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
		->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->select(
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('employee_chief.name as name_chief'),
			\DB::RAW('routing_chief.description as position_chief'),
			\DB::RAW('master_company.company_name as company_name')
		)
		->where('hr_electronic_letter.token',$token)
		->get();
		return $data;
	}
	public static function get_pkwt_number($request)
	{
		$emp = DB::table('hr_employee')->select(\DB::RAW('name'))->where('id_employee',$request->id_employee)->where('status','A')->first();
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
		// ->join('master_position_detail','master_position_detail.id_position_detail','=','master_position_routing.id_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		// 
		->leftJoin('master_general_data as data_pkk','data_pkk.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_pkk.code','PKK')
		->where('master_general_data.code','PKWT')
		->whereRaw("(hr_electronic_letter.id_employee = ".$request->id_employee." OR hr_electronic_letter.remark_1 = '".$emp->name."')");
	//	->where('hr_electronic_letter.id_employee',$request->id_employee);
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.date', 'ASC')
		->get();
		return $data;
	}
}
