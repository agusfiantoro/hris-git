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

class StatementLetter extends Model
{
	public static function get_sk_career($request)
	{
		// $id_branch = ElectronicLetter::accessBranch($request);
		$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_career_transaction.id_transaction_type')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_career_transaction.id_employment_status')
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
		->where('position_detail_old.secondary_position','false')
		->where('position_detail_new.secondary_position','false')
		->where('master_general_data.code','!=','Terminate')
		->where('master_general_data.code','!=','Join')
		->where('hr_career_transaction.id_employee',$request->employee_id)
		->where('hr_career_transaction.id_company',session('id_company'));
		if ($request->category_type == 'Permanent') {
			$data->where('master_general_data.description','Employment Status Changes');
			// $data->where('master_general_data.code','Movement');
		}else{
			// $data->where('master_general_data.code','Movement');
			$data->where('master_general_data.description','!=','Employment Status Changes');
		}
		$data = $data->orderBy('hr_employee.name','ASC')->get();
		return $data;
	}
	public static function change_career_sk($request)
	{
		$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_career_transaction.id_transaction_type')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_career_transaction.id_employment_status')
		// OLD
		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
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
		// NEW
		->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_career_transaction.id_position_detail')
		->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
		->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
		->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
		->leftJoin('master_region as region_new','region_new.id_region','=','branch_new.id_region')
		->leftJoin('master_job_position as job_position_new','job_position_new.id_position','=','position_routing_new.id_position')
		->leftJoin('master_department as department_new','department_new.id_dept','=','job_position_new.id_dept')
		->leftJoin('master_location as location_new','location_new.id_location','=','position_detail_new.id_location')
		->leftJoin('relation_positiondetail_principal as relation_principal_new','relation_principal_new.id_position_detail','=','position_detail_new.id_position_detail')
		->leftJoin('master_principal as principal_new','principal_new.id_principal','=','relation_principal_new.id_principal')
		->leftJoin('master_division as division_new','division_new.id_division','=','principal_new.id_division')
		// END
		->select(
			\DB::RAW('hr_career_transaction.id_career_transaction as id_career_transaction'),
			\DB::RAW('hr_career_transaction.reference_number as reference_number'),
			\DB::RAW('status_employment.description as status'),
			\DB::RAW('status_employment.id_general_data as id_employment_status'),
			// 
			\DB::RAW('job_grade_old.id_job_grade as id_job_grade_old'),
			\DB::RAW('job_grade_old.description as dec_job_grade_old'),
			\DB::RAW('region_old.description as dec_region_old'),
			\DB::RAW('region_old.id_region as id_region_old'),
			\DB::RAW('branch_old.description as dec_branch_old'),
			\DB::RAW('branch_old.id_branch as id_branch_old'),
			\DB::RAW('position_routing_old.description as dec_position_old'),
			\DB::RAW('position_detail_old.id_position_detail as id_position_detail_old'),
			\DB::RAW('department_old.description as dec_dept_old'),
			\DB::RAW('department_old.id_dept as id_dept_old'),
			\DB::RAW('location_old.description as dec_location_old'),
			\DB::RAW('location_old.id_location as id_location_old'),
			\DB::RAW('principal_old.description as dec_divisi_old'),
			// 
			\DB::RAW('job_grade_new.id_job_grade as id_job_grade_new'),
			\DB::RAW('job_grade_new.description as dec_job_grade_new'),
			\DB::RAW('region_new.description as dec_region_new'),
			\DB::RAW('region_new.id_region as id_region_new'),
			\DB::RAW('branch_new.description as dec_branch_new'),
			\DB::RAW('branch_new.id_branch as id_branch_new'),
			\DB::RAW('position_routing_new.description as dec_position_new'),
			\DB::RAW('position_detail_new.id_position_detail as id_position_detail_new'),
			\DB::RAW('department_new.description as dec_dept_new'),
			\DB::RAW('department_new.id_dept as id_dept_new'),
			\DB::RAW('location_new.description as dec_location_new'),
			\DB::RAW('location_new.id_location as id_location_new'),
			\DB::RAW('principal_new.description as dec_divisi_new'),
			\DB::RAW('principal_new.id_principal as id_division_new')

		)
		// ->where('position_detail_old.secondary_position','false')
		// ->where('position_detail_new.secondary_position','false')
		->where('hr_career_transaction.id_career_transaction',$request->id_career_transaction)
		->where('hr_career_transaction.id_company',session('id_company'))
		->get();
		return $data;
	}
	public static function get_sk($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = Employee::leftJoin('hr_electronic_letter','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
		->leftJoin('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// OLD
		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
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
		// NEW
		->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
		->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
		->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
		->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
		->leftJoin('master_region as region_new','region_new.id_region','=','branch_new.id_region')
		->leftJoin('master_job_position as job_position_new','job_position_new.id_position','=','position_routing_new.id_position')
		->leftJoin('master_department as department_new','department_new.id_dept','=','job_position_new.id_dept')
		->leftJoin('master_location as location_new','location_new.id_location','=','position_detail_new.id_location')
		->leftJoin('relation_positiondetail_principal as relation_principal_new','relation_principal_new.id_position_detail','=','position_detail_new.id_position_detail')
		->leftJoin('master_principal as principal_new','principal_new.id_principal','=','relation_principal_new.id_principal')
		->leftJoin('master_division as division_new','division_new.id_division','=','principal_new.id_division')
		// TAMPIL SK
		->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
		// END
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.token as token'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.remark_8 as remark_8'),
			\DB::RAW('hr_electronic_letter.remark_9 as remark_9'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('COALESCE(region_new.description, region_old.description) as dec_region_old'),
			\DB::RAW('COALESCE(branch_new.description, branch_old.description) as dec_branch_old'),
			\DB::RAW('COALESCE(position_routing_new.description, position_routing_old.description) as dec_position_old'),
			\DB::RAW('COALESCE(department_new.description, department_old.description) as dec_dept_old'),
			\DB::RAW('COALESCE(location_new.description, location_old.description) as dec_location_old')
		)
		->where('data_sk.code','SK')
		->where('hr_electronic_letter.id_company',session('id_company'));
		// ->where('position_detail_old.secondary_position','false')
		// ->where('position_detail_new.secondary_position','false');
		// ->orderBy('hr_electronic_letter.id_letter','DESC')
		// ->get();
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch))
			->whereIn('hr_electronic_letter.id_branch_new',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')->get();
		return $data;
	}
	public static function get_edit_sk($id_letter)
	{
		// $data = Employee::join('hr_electronic_letter','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
		// ->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
		// ->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		// ->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// // OLD
		// ->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
		// ->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
		// ->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
		// ->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
		// ->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
		// ->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
		// ->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
		// ->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
		// ->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
		// ->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
		// ->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
		// // NEW
		// ->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
		// ->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
		// ->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
		// ->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
		// ->leftJoin('master_region as region_new','region_new.id_region','=','branch_new.id_region')
		// ->leftJoin('master_job_position as job_position_new','job_position_new.id_position','=','position_routing_new.id_position')
		// ->leftJoin('master_department as department_new','department_new.id_dept','=','job_position_new.id_dept')
		// ->leftJoin('master_location as location_new','location_new.id_location','=','position_detail_new.id_location')
		// ->leftJoin('relation_positiondetail_principal as relation_principal_new','relation_principal_new.id_position_detail','=','position_detail_new.id_position_detail')
		// ->leftJoin('master_principal as principal_new','principal_new.id_principal','=','relation_principal_new.id_principal')
		// ->leftJoin('master_division as division_new','division_new.id_division','=','principal_new.id_division')
		// // END
		// ->select(
		// 	\DB::RAW('hr_career_transaction.reference_number as reference_number_career'),
		// 	\DB::RAW('hr_electronic_letter.id_career_transaction as id_career_transaction'),
		// 	\DB::RAW('hr_electronic_letter.id_category as id_category'),
		// 	\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
		// 	\DB::RAW('hr_electronic_letter.date as date'),
		// 	\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
		// 	\DB::RAW('hr_employee.nik_employee as nik_employee'),
		// 	// OLD
		// 	\DB::RAW('region_old.description as dec_region_old'),
		// 	\DB::RAW('department_old.description as dec_dept_old'),
		// 	\DB::RAW('position_detail_old.description as dec_position_old'),
		// 	\DB::RAW('branch_old.description as dec_branch_old'),
		// 	\DB::RAW('location_old.description as dec_location_old'),
		// 	\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
		// 	// NEW
		// 	\DB::RAW('region_new.description as dec_region_new')
		// 	// \DB::RAW('department_old.description as dec_dept_old'),
		// 	// \DB::RAW('position_detail_old.description as dec_position_old'),
		// 	// \DB::RAW('branch_old.description as dec_branch_old'),
		// 	// \DB::RAW('location_old.description as dec_location_old'),
		// 	// \DB::RAW('hr_electronic_letter.id_principal as id_principal')
		// )
		// ->where('hr_electronic_letter.id_company',session('id_company'))
		// ->where('hr_electronic_letter.id_letter',$id_letter)
		// ->get();
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
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
		// TAMPIL SK
		->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
		// CHIEF
		->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->select(
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_career_transaction.reference_number as reference_number_career'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.id_employment_status as id_employment_status'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('status_employment.description as status'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_electronic_letter.remark_8 as remark_8'),
			\DB::RAW('hr_electronic_letter.remark_9 as remark_9'),
			// \DB::RAW('master_division.description as dec_divisi_new'),
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
			\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new'),
			// CHIEF
			\DB::RAW('employee_chief.name as name_chief'),
			\DB::RAW('routing_chief.description as position_chief')
		)
		->where('data_sk.code','SK')
		// ->where('position_detail_old.secondary_position','false')
		// ->where('position_detail_new.secondary_position','false')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->first();
		return $data;
	}
	public static function get_print_sk($token)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
		// CHIEF
		->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
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
		->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			\DB::RAW('employee_chief.name as name_chief'),
			\DB::RAW('routing_chief.description as position_chief'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_general_data.code as code_category'),
			\DB::RAW('hr_electronic_letter.remark_8 as remark_8'),
			\DB::RAW('hr_electronic_letter.remark_9 as remark_9'),
			// OLD
			\DB::RAW('position_routing_old.description as dec_position_old'),
			\DB::RAW('department_old.description as dec_dept_old'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			\DB::RAW('location_old.description as dec_location_old'),
			\DB::RAW('branch_old.description as dec_branch_old'),
			// NEW
			\DB::RAW('position_routing_new.description as dec_position_new'),
			\DB::RAW('department_new.description as dec_dept_new'),
			\DB::RAW('status_employment.description as status'),
			\DB::RAW('branch_new.description as dec_branch_new'),
			\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new'),
			\DB::RAW('location_new.description as dec_location_new')
		)
		->where('data_sk.code','SK')
		->where('hr_electronic_letter.token',$token)
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
			\DB::RAW('data_category.id_general_data as id_general_data')
		)
		->where('master_general_data.code','SK')
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
	public static function get_employee($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$employee = Employee::leftJoin('master_position_detail', function ($join) {
						$join->on('master_position_detail.id_employee', '=', 'hr_employee.id_employee')
						->orOn('master_position_detail.id_employee2', '=', 'hr_employee.id_employee');
					})
		->select(
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.id_employee as id_employee')
		)
		->where('hr_employee.id_company',session('id_company'))
		->where('master_position_detail.secondary_position','false')
		->where('hr_employee.status','A');
		// ->where(function ($query) {
		// 	$query->whereNotExists(function ($subquery) {
		// 		$subquery->select(\DB::RAW(1))
		// 		->from('hr_electronic_letter')
		// 		->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
		// 		->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','data_category.relation_to_id_general_data')
		// 		->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_employee.id_employee')
		// 		->where('data_sk.code','SK');
		// 	})->orWhereNull('hr_employee.id_employee');
		// });
		if ($id_branch != NULL) {
			$employee->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
		}
		$employee = $employee->orderBy('hr_employee.name','ASC')->get();
		return $employee;
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
	public static function get_principal($string)
	{
		$principal = DB::table('master_principal')
		->join('master_division','master_division.id_division','=','master_principal.id_division')
		->select(
			\DB::RAW('master_principal.description as dec_division_new')
		)
		->whereIn('master_principal.id_principal',$string)
		->where('master_principal.id_company',session('id_company'))
		// ->groupBy('master_division.description')
		->orderBy('master_principal.description','ASC')
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
		->where('master_general_data.code','SK')
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
		->where('hr_electronic_letter.id_region',$request->id_region)
		->where('hr_electronic_letter.id_category',$request->id_category)
		->max('hr_electronic_letter.reference_number');
		$no_urut = substr($id_letter, 0,4);
		$no_urut++;
		$kode = sprintf("%04s", abs($no_urut));
		$category = MasterGeneralData::where('id_general_data',$request->id_category)
		->where('id_company',session('id_company'))
		->first();
		$region = MasterRegional::where('id_region',$request->id_region)->first();
		if ($region->description == "Pusat") {
			$code_region = "HQ";
		}else{
			$code_region = $region->region_code;
		}
		$format = $kode."/".$company->company_code."-".$category->code."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
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
