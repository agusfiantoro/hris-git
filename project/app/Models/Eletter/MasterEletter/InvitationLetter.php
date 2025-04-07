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

class InvitationLetter extends Model
{
	public static function table_view_supa1($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = Employee::leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
		->select(
			\DB::RAW('hr_employee.name as nama_karyawan'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.id_employee as id_employee')
		)
		->where('hr_employee.id_company',session('id_company'))
		->where('master_position_detail.secondary_position','false')
		->where('hr_employee.status','A');
		if ($id_branch != NULL) {
			$data->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_employee.name','ASC')->get();
		return $data;
	}
	public static function select_change_supa1($request)
	{
		$data = DB::table('hr_employee')
		->leftJoin('master_position_detail as name_employee', 'name_employee.id_employee', '=', 'hr_employee.id_employee')
		->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'name_employee.id_position_routing')
		->leftJoin('master_branch', 'master_branch.id_branch', '=', 'name_employee.id_branch')
		->leftJoin('master_region', 'master_region.id_region', '=', 'master_branch.id_region')
		->leftJoin('master_location', 'master_location.id_location', '=', 'name_employee.id_location')
		->leftJoin('master_company', 'hr_employee.id_company', '=', 'master_company.id_company')
		->leftJoin('master_job_grade', 'master_job_grade.id_job_grade', '=', 'master_position_routing.id_job_grade')
		->leftJoin('master_job_position', 'master_job_position.id_position', '=', 'master_position_routing.id_position')
		->leftJoin('master_department', 'master_department.id_dept', '=', 'master_job_position.id_dept')
		->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'name_employee.id_position_detail')
		->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
		->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
		->leftJoin('hr_employee as atasan', 'atasan.id_employee', '=', 'name_employee.parent_id_position_detail')
		->select(
			\DB::RAW('hr_employee.id_employee as id_employee'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.name as nama_karyawan'),
			\DB::RAW('atasan.name as nama_atasan'),
			\DB::RAW('atasan.nik_employee as nik_atasan'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_company.id_company as id_company'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_department.id_dept as id_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_region.id_region as id_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_branch.id_branch as id_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('name_employee.id_position_detail as id_routing')
		)
		->where('hr_employee.id_company', session('id_company'))
		->where('hr_employee.id_employee', $request->employee_id)
		->where('hr_employee.status', 'A')
		->where('name_employee.secondary_position','false')
		->first();
		return $data;
	}
	public static function get_supa($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.token as token'),
			\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter')
		)
		->where('hr_electronic_letter.id_company', session('id_company'))
		->where('data_supa.code','SUPA')
		->where('master_position_detail.secondary_position','false');
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
		->get();
		return $data;
	}
	public static function get_supa2($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_general_data.code as category'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.token as token'),
			\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter')
		)
		->where('hr_electronic_letter.id_company', session('id_company'))
		->where('master_general_data.code', 'SUPA1')
		->where('data_supa.code','SUPA')
		->where('master_position_detail.secondary_position','false');
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
		->get();
		return $data;
	}
	public function select_change_supa2($request)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter')
		)
		->where('hr_electronic_letter.id_company', session('id_company'))
		->where('hr_electronic_letter.id_letter', $request->letter_id)
		->where('data_supa.code','SUPA')
		->where('master_position_detail.secondary_position','false')
		->get();
		return $data;
	}
	public static function get_edit_supa($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
		// PENGIRIM
		->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->leftJoin('hr_employee as name_chief','name_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->select(
			\DB::RAW('hr_employee.id_employee as id_employee'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_general_data.description as category_code'),
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.remark_7 as remark_7'),
			\DB::RAW('hr_electronic_letter.remark_8 as remark_8'),
			\DB::RAW('position_chief.description as jabatan_pengirim'),
			\DB::RAW('name_chief.name as nama_pengirim'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief')
		)
		->where('hr_electronic_letter.id_company', session('id_company'))
		->where('hr_electronic_letter.id_letter', $id_letter)
		->where('data_supa.code', 'SUPA')
		->where('master_position_detail.secondary_position','false')
		->first();
		return $data;
	}
	public static function print_supa($token)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
				// PENGIRIM
		->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->leftJoin('hr_employee as name_chief','name_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->select(
			\DB::RAW('hr_employee.id_employee as id_employee'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_general_data.code as category_code'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.remark_7 as remark_7'),
			\DB::RAW('hr_electronic_letter.remark_8 as remark_8'),
			\DB::RAW('position_chief.description as jabatan_pengirim'),
			\DB::RAW('name_chief.name as nama_pengirim'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief')
		)
		->where('hr_electronic_letter.id_company', session('id_company'))
		->where('hr_electronic_letter.token', $token)
		->where('master_position_detail.secondary_position','false')
		->where('data_supa.code', 'SUPA')
		->first();
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
	// 	->where('master_position_detail.secondary_position','false')
	// 	->first();
	// 	return $branch;
	// }
	public static function get_category()
	{
		$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.id_general_data as id_general_data'),
			\DB::RAW('data_category.description as description')
		)
		->where('data_category.id_company',session('id_company'))
		->where('data_category.status','A')
		->where('master_general_data.code','SUPA')
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
	public static function format_save($request)
	{
		$employee_name = Employee::where('id_employee',$request->id_employee)->first();
		$token = md5($employee_name->name).strtotime('now');

		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.id_company',session('id_company'))
		->where('master_general_data.code','SUPA')
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
		$remark_4 = $request->hari.' '.$request->tanggal_panggil.' '.$request->waktu.' '.$request->zona;
		return ['format'=>$format,'token'=>$token,'id_letter_type'=>$id_letter_type,'remark_4'=>$remark_4];
	}
}
