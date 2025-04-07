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

class InternalMemo extends Model
{
    // use HasFactory;
	public static function get_im_career($request)
	{
		// $id_branch = ElectronicLetter::accessBranch($request);
		$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_career_transaction.id_transaction_type')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_career_transaction.id_employment_status')
		->leftJoin('master_general_data as career_status', 'hr_career_transaction.id_approval_status', 'career_status.id_general_data')
		// old
		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
		->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
		// new
		->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_career_transaction.id_position_detail')
		->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
		->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
		->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
		->select(
			\DB::RAW('hr_career_transaction.id_career_transaction as id_career_transaction'),
			\DB::RAW('hr_career_transaction.reference_number as reference_number'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_general_data.description as type'),
			\DB::RAW('position_routing_old.description dec_position_old'),
			\DB::RAW('branch_old.description as dec_branch_old'),
			\DB::RAW('job_grade_old.description as dec_job_grade_old'),
			\DB::RAW('position_routing_new.description dec_position_new'),
			\DB::RAW('branch_new.description as dec_branch_new'),
			\DB::RAW('job_grade_new.description as dec_job_grade_new'),
			\DB::RAW('status_employment.description as status')
		)
		// ->where('position_detail_old.secondary_position',false)
		// ->where('position_detail_new.secondary_position',false)
		->where('career_status.code', '=', 'Approved')
		->where('master_general_data.code','!=','Terminate')
		->where('master_general_data.code','!=','Join')
		->where('hr_career_transaction.id_employee',$request->employee_id)
		->where('hr_career_transaction.id_company',session('id_company'))
		->where('master_general_data.description','!=','Employment Status Changes');
		$data = $data->orderBy('hr_employee.name','ASC')->get();
		return $data;
	}
	public static function get_im($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		// OLD
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
		// NEW
		->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
		->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
		->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
		->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
		->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
		->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
		// Tanpilkan IM
		->leftJoin('master_general_data as data_im','data_im.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('COALESCE(department_new.description, master_department.description) as dec_dept'),
			\DB::RAW('COALESCE(region_new.description, master_region.description) as dec_region'),
			\DB::RAW('COALESCE(branch_new.description, master_branch.description) as dec_branch'),
			\DB::RAW('COALESCE(position_routing_new.description, master_position_routing.description) as dec_position'),
			\DB::RAW('COALESCE(location_new.description, master_location.description) as dec_location'),
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_location.description as dec_location'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.token as token'),
			\DB::RAW('hr_employee.name as name')
		)
		->where('data_im.code','IM')
		// ->where('master_position_detail.secondary_position','false')
		->where('hr_electronic_letter.id_company',session('id_company'));
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch))
			->whereIn('hr_electronic_letter.id_branch_new',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
		->get();
		// ->orderBy('hr_electronic_letter.id_letter','DESC')
		// ->get();
		return $data;
	}
	public static function changeRegion($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$branch = MasterBranch::select(
			\DB::RAW('description'),
			\DB::RAW('id_branch')
		)
		->where('id_company',session('id_company'))
		->where('id_region',$request->id_region);
		if ($id_branch != NULL) {
			$branch->whereIn('id_branch',explode(',', $id_branch));
		}
		$branch = $branch->orderBy('description','ASC')->get();
		return $branch;
	}
	public static function changeBranch($request)
	{
		$data = MasterLocation::select(
			\DB::RAW('description'),
			\DB::RAW('id_location')
		)
		->where('id_company',session('id_company'))
		->where('id_branch',$request->id_branch);
		$data = $data->orderBy('description','ASC')->get();
		return $data;
	}
	public static function get_edit_im($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// OLD
		->leftJoin('master_region as region_old','region_old.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_department as department_old','department_old.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_location as location_old','location_old.id_location','=','hr_electronic_letter.id_location')
		// // NEW
		->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
		->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
		->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
		->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
		->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
		->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
		// DATA IM
		->leftJoin('master_general_data as data_im','data_im.id_general_data','=','master_general_data.relation_to_id_general_data')
		// ->leftJoin('master_division','master_division.id_division','=','hr_electronic_letter.id_principal_new')
		->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->leftJoin('hr_employee as name_chief','name_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->select(
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_career_transaction.id_career_transaction as id_career_transaction'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_general_data.code as category_code'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('name_chief.name as chief_name'),
			\DB::RAW('position_chief.description as chief_position'),
			// OLD
			\DB::RAW('region_old.description as dec_region_old'),
			\DB::RAW('department_old.description as dec_dept_old'),
			\DB::RAW('position_routing_old.description as dec_position_old'),
			\DB::RAW('branch_old.description as dec_branch_old'),
			\DB::RAW('location_old.description as dec_location_old'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			// // NEW
			\DB::RAW('region_new.description as dec_region_new'),
			\DB::RAW('department_new.description as dec_dept_new'),
			\DB::RAW('position_routing_new.description as dec_position_new'),
			\DB::RAW('branch_new.description as dec_branch_new'),
			\DB::RAW('location_new.description as dec_location_new'),
			\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new')
		)
		->where('data_im.code','IM')
		// ->where('position_detail_old.secondary_position','false')
		// ->where('position_detail_new.secondary_position','false')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->first();
		return $data;
	}

	public static function get_im_pdf($token)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// OLD
		->leftJoin('master_region as region_old','region_old.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_department as department_old','department_old.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_location as location_old','location_old.id_location','=','hr_electronic_letter.id_location')
		// // NEW
		->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
		->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
		->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
		->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
		->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
		->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
		// ->leftJoin('master_division','master_division.id_division','=','hr_electronic_letter.id_principal_new')
		// TAMPIL IM
		->leftJoin('master_general_data as data_im','data_im.id_general_data','=','master_general_data.relation_to_id_general_data')
		// PEMBERI KEPUTUSAN
		->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->select(
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_general_data.code as code_category'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			// OLD
			\DB::RAW('region_old.description as dec_region'),
			\DB::RAW('department_old.description as dec_dept'),
			\DB::RAW('position_routing_old.description as dec_position'),
			\DB::RAW('branch_old.description as dec_branch'),
			\DB::RAW('location_old.description as dec_location'),
			\DB::RAW('hr_electronic_letter.id_principal as dec_divisi'),
			// // NEW
			\DB::RAW('region_new.description as dec_region_new'),
			\DB::RAW('department_new.description as dec_dept_new'),
			\DB::RAW('position_routing_new.description as dec_position_new'),
			\DB::RAW('branch_new.description as dec_branch_new'),
			\DB::RAW('location_new.description as dec_location_new'),
			\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new'),
			// PEMBERI KEPUTUSAN
			\DB::RAW('employee_chief.name as employee_chief_name'),
			\DB::RAW('position_chief.description as employee_chief_position')
		)
		->where('hr_electronic_letter.token',$token)
		->where('data_im.code','IM')
		// ->where('position_detail_old.secondary_position','false')
		// ->where('position_detail_new.secondary_position','false')
		->where('hr_electronic_letter.id_company',session('id_company'))
		->first();
		return $data;
	}
	public static function get_category()
	{
		$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.description as description'),
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.id_general_data as id_general_data')
		)
		->where('master_general_data.code','IM')
		->where('data_category.status','A')
		->where('data_category.code','!=','MUTA')
		->where('data_category.id_company',session('id_company'))
		->orderBy('data_category.description','ASC')
		->get();
		return $category;
	}
	public static function get_employee($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$employee = Employee::leftJoin('master_position_detail',function ($join) {
                    $join->on('master_position_detail.id_employee','=','hr_employee.id_employee');
                    $join->orOn('master_position_detail.id_employee2', '=', 'hr_employee.id_employee');
                })		
		->select(
			\DB::RAW('hr_employee.id_employee as id_employee'),
			\DB::RAW('hr_employee.status as status'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.name as name')
		)
		->where('hr_employee.id_company',session('id_company'))
		->where('hr_employee.status','A')
		->where('master_position_detail.secondary_position','false');
		// ->where(function ($query) {
		// 	$query->whereNotExists(function ($subquery) {
		// 		$subquery->select(\DB::RAW(1))
		// 		->from('hr_electronic_letter')
		// 		->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
		// 		->leftJoin('master_general_data as data_im','data_im.id_general_data','=','data_category.relation_to_id_general_data')
		// 		->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_employee.id_employee')
		// 		->where('data_im.code','IM');
		// 	})->orWhereNull('hr_employee.id_employee');
		// });
		if ($id_branch != NULL) {
			$employee->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
		}
		$employee = $employee->orderBy('hr_employee.name','ASC')->get();
		return $employee;
	}
	public static function change_employee_ta($request)
	{
		$data = Employee::leftJoin('master_position_detail as position_detail_old','position_detail_old.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
		->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
		->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
		->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
		->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
		->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
		->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
		->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
		->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
		->select(
			\DB::RAW('job_grade_old.id_job_grade as id_job_grade_old'),
			\DB::RAW('job_grade_old.description as dec_job_grade_old'),
			\DB::RAW('region_old.description as dec_region_old'),
			\DB::RAW('region_old.id_region as id_region_old'),
			\DB::RAW('branch_old.description as dec_branch_old'),
			\DB::RAW('branch_old.id_branch as id_branch_old'),
			\DB::RAW('position_routing_old.description as dec_position_old'),
			\DB::RAW('position_routing_old.id_routing as id_routing_old'),
			\DB::RAW('position_detail_old.id_position_detail as id_position_detail_old'),
			\DB::RAW('department_old.description as dec_dept_old'),
			\DB::RAW('department_old.id_dept as id_dept_old'),
			\DB::RAW('location_old.description as dec_location_old'),
			\DB::RAW('location_old.id_location as id_location_old'),
			\DB::RAW('principal_old.principal_code as dec_divisi_old')
		)
		->where('position_detail_old.secondary_position','false')
		->where('hr_employee.id_employee',$request->id_employee)
		->where('hr_employee.id_company',session('id_company'))
		->get();
		return $data;
	}
	public static function get_region()
	{
		$data = MasterRegional::select(
			\DB::RAW('description text'),
			\DB::RAW('id_region id')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $data;
	}
	public static function get_position()
	{
		// $position = DB::table('master_position_routing')
		// ->select(
		// 	\DB::RAW('id_routing id'),
		// 	\DB::RAW('description text')
		// )
		// ->where('id_company',session('id_company'))
		// ->where('status','A')
		// ->orderBy('description','ASC')
		// ->get();
		$position = DB::table('master_position_routing')
		->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
		->select(
			\DB::RAW('master_position_routing.id_routing as id_routing'),
			\DB::RAW('master_position_routing.description as dec_position_routing'),
			\DB::RAW('master_job_position.description as dec_job_position'),
		)
		->where('master_position_routing.id_company',session('id_company'))
		->where('master_position_routing.status','A')
		->orderBy('master_position_routing.description','ASC')
		->get();
		return $position;
	}
	public static function get_dept()
	{
		$data = DB::table('master_department')->select(
			\DB::RAW('description text'),
			\DB::RAW('id_dept id')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $data;
	}
	public static function get_division()
	{
		$data = DB::table('master_principal')
		->leftJoin('master_division','master_division.id_division','=','master_principal.id_division')
		->select(
			\DB::RAW('master_principal.principal_code text'),
			\DB::RAW('master_principal.id_principal id')
		)
		// ->groupBy('master_division.description','master_division.id_division')
		->where('master_principal.status','A')
		->where('master_principal.id_company',session('id_company'))
		->orderBy('master_principal.principal_code','ASC')
		->get();
		return $data;
	}
	public static function get_chief_name()
	{
	/*
		$employee = Employee::select(
			\DB::RAW('name'),
			\DB::RAW('id_employee')
		)
		->where('status','A')
		->where('id_company',session('id_company'))
		->orderBy('name','ASC')
		->get();
		return $employee;
	*/	
		$sql = "SELECT he.name, he.id_employee 
				FROM master_position_detail mpd
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee AND he.status = 'A'
				WHERE mpd.id_company = ". session('id_company')."
				ORDER BY he.name ASC";
        $result = DB::select($sql);
        return $result;
	}
	public static function get_position_chief($request)
	{
		$data = DB::table('hr_employee')
		->leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->select(
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_position_routing.id_routing as id_routing')
		)
		->where('master_position_detail.id_employee',$request->employee_id)
		->where('master_position_detail.id_company',session('id_company'))
		->where('hr_employee.status', 'A')
		->orderBy('master_position_routing.description','ASC')
		->first();
		return $data;
	}
	public static function get_principal($string)
	{
		$principal = DB::table('master_principal')
		->leftJoin('master_division','master_division.id_division','=','master_principal.id_division')
		->select(
			\DB::RAW('master_principal.principal_code as dec_division_new')
		)
		// ->groupBy('master_division.description')
		->whereIn('master_principal.id_principal',$string)
		->where('master_principal.id_company',session('id_company'))
		->where('master_principal.status','A')
		->orderBy('master_principal.principal_code','ASC')
		->get();
		return $principal;
	}
	public static function get_principal_print($string)
	{
		$principal = DB::table('master_principal')
		->leftJoin('master_division','master_division.id_division','=','master_principal.id_division')
		->select(
			\DB::RAW('master_principal.principal_code as description')
		)
		// ->groupBy('master_division.description')
		->whereIn('master_principal.id_principal',$string)
		->where('master_principal.id_company',session('id_company'))
		->orderBy('master_principal.principal_code','ASC')
		->get();
		return $principal;
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
		->where('master_general_data.code','IM')
		->where('master_general_data.id_company',session('id_company'))
		->first();
		$principalTextValue = "";
		foreach ($request->id_principal as $key => $value) {
			$principalTextValue .= $value . ",";
		}
		$principalTextValue = rtrim($principalTextValue, ',');

		$company = DB::table('master_company')->where('id_company',session('id_company'))->first();
		$bulan = date('m', strtotime($request->date));
		$tahun = date('Y', strtotime($request->date));
		$id_letter = ElectronicLetter::join('master_company','hr_electronic_letter.id_company','=','master_company.id_company')
		->whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_company.company_code',$company->company_code)
		->where('hr_electronic_letter.id_regional_new',$request->id_region_new)
		->where('hr_electronic_letter.id_letter_type',$id_letter_type->id_general_data)
		->max('hr_electronic_letter.reference_number');
		$no_urut = substr($id_letter, 0,4);
		$no_urut++;
		$kode = sprintf("%04s", abs($no_urut));

		$region = MasterRegional::where('id_region',$request->id_region_new)->first();
		if ($region->description == "Pusat") {
			$code_region = "HQ";
		}else{
			$code_region = $region->region_code;
		}
		$format = $kode."/".$company->company_code."-".$id_letter_type->code."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
		$id_principal_new = $request->input('id_principal_new',[]);
		$id = '{'.implode(',', $id_principal_new).'}';
		$ids = explode(',', trim($id,'{}'));
		return [
			'format'=>$format,
			'id_letter_type'=>$id_letter_type,
			'principalTextValue'=>$principalTextValue,
			'token'=>$token,
			'id'=>$id
		];
	}
}
