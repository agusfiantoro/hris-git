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
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use Illuminate\Support\Facades\Storage;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;

class EmploymentCertificate extends Model
{
	public function get_skk($request)
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
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.publish as publish'),
			// \DB::RAW('hr_employee.name as name'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_region.description as dec_region'),
			// 
			// \DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, hr_employee.name) as name'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_department.description) as dec_dept'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_position_routing.description) as dec_position')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		// ->where('master_position_detail.secondary_position','false')
		->where('data_sk.code','SKK');
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
		->get();
		return $data;
	}
	public static function get_employee($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		if ($request->category == "PUB") {
			$data = DB::table('hr_employee')
			->leftjoin('master_position_detail as mpd_hrc', function ($join) {
				$join->whereRaw('(mpd_hrc.id_employee = hr_employee.id_employee OR mpd_hrc.id_employee2 = hr_employee.id_employee)');
			})
		//	->leftJoin('master_position_detail as mpd_hrc','mpd_hrc.id_employee','=','hr_employee.id_employee')
			->select(
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date'),
				\DB::RAW('hr_employee.status as status')
			)
			->where('hr_employee.id_company',session('id_company'))
			->where('hr_employee.status','A')
			->where('mpd_hrc.secondary_position','false');
		}else{
			$data = Employee::leftJoin('hr_career_transaction as hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
			// Terminate
			->leftJoin('master_general_data as status_terminate','status_terminate.id_general_data','=','hr_career_transaction.id_transaction_type')
			// Approved
			->leftJoin('master_general_data as status_approved','status_approved.id_general_data','=','hr_career_transaction.id_approval_status')
			// 
			->leftJoin('master_position_detail as mpd_hrc','mpd_hrc.id_position_detail','=','hr_career_transaction.id_old_position_detail')
			->select(
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date'),
				\DB::RAW('hr_employee.status as status')
			)
			->where('status_terminate.code','Termination')
			->where('status_approved.code','Approved')
			->where('hr_employee.id_company',session('id_company'));
		//	->where('mpd_hrc.secondary_position','false');
		}
		if ($id_branch != NULL) {
			$data->whereIn('mpd_hrc.id_branch',explode(',', $id_branch));
		}
		// $data->where(function ($query) {
		// 	$query->whereNotExists(function ($subquery) {
		// 		$subquery->select(\DB::RAW(1))
		// 		->from('hr_electronic_letter')
		// 		->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
		// 		->leftJoin('master_general_data as data_skk','data_skk.id_general_data','=','data_category.relation_to_id_general_data')
		// 		->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_employee.id_employee')
		// 		->where('data_skk.code','SKK');
		// 	})->orWhereNull('hr_employee.id_employee');
		// });
		$data = $data->orderBy('hr_employee.name','ASC')->get();
		return $data;
	}
	public static function change_employee_skk($request)
	{
		if ($request->employee_status == "A") {
			$data = DB::table('hr_employee')
			->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_employee.id_employment_status')
			->leftjoin('master_position_detail as master_position_detail', function ($join) {
				$join->whereRaw('(master_position_detail.id_employee = hr_employee.id_employee OR master_position_detail.id_employee2 = hr_employee.id_employee)');
			})
		//	->leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
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
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date'),
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('master_region.id_region as id_region'),
				\DB::RAW('status_employment.description as status'),
				\DB::RAW('status_employment.id_general_data as id_employment_status')
			)
			->where('hr_employee.id_company',session('id_company'))
			->where('hr_employee.id_employee',$request->employee_id)
			->where('master_position_detail.secondary_position','false')
			->get();
		}else{
			$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
			->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_career_transaction.id_employment_status')
			// Terminate
			->leftJoin('master_general_data as status_terminate','status_terminate.id_general_data','=','hr_career_transaction.id_transaction_type')
			// Approved
			->leftJoin('master_general_data as status_approved','status_approved.id_general_data','=','hr_career_transaction.id_approval_status')
			// 
			->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
			->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
			->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
			->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
			->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
			->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
			->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
			// 
			->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
			->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
			->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
			->leftJoin('master_company','master_company.id_company','=','hr_career_transaction.id_company')
			->select(
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date'),
				\DB::RAW('master_company.company_name as company_name'),
				\DB::RAW('master_company.id_company as id_company'),
				\DB::RAW('region_old.description as dec_region'),
				\DB::RAW('region_old.id_region as id_region'),
				\DB::RAW('job_grade_old.description as dec_job_grade'),
				\DB::RAW('job_grade_old.id_job_grade as id_job_grade'),
				\DB::RAW('department_old.description as dec_dept'),
				\DB::RAW('department_old.id_dept as id_dept'),
				\DB::RAW('principal_old.principal_code as dec_principal'),
				\DB::RAW('principal_old.id_division as id_principal'),
				\DB::RAW('branch_old.description as dec_branch'),
				\DB::RAW('branch_old.id_branch as id_branch'),
				\DB::RAW('position_routing_old.description as dec_position'),
				\DB::RAW('position_detail_old.id_position_detail as id_routing'),
				\DB::RAW('status_employment.description as status'),
				\DB::RAW('status_employment.id_general_data as id_employment_status')
			)
			->where('status_terminate.code','Termination')
			->where('status_approved.code','Approved')
			->where('hr_career_transaction.id_employee',$request->employee_id)
			->where('hr_career_transaction.id_company',session('id_company'))
		//	->where('position_detail_old.secondary_position','false')
			->get();
		}
		return $data;
	}
	public static function get_edit_skk($id_letter)
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
		// ->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
		->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
		->select(
			\DB::RAW('master_general_data.description as dec_category'),
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.publish as publish'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('hr_electronic_letter.id_region as id_region'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_employee.status as status_employee'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('employee_chief.name as name_chief'),
			\DB::RAW('routing_chief.description as position_chief'),
			// 
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, hr_employee.name) as name'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_branch.description) as dec_branch'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_position_routing.description) as dec_position'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_department.description) as dec_dept'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_job_grade.description) as dec_job_grade'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, status_employment.description) as status')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('hr_electronic_letter.id_letter',$id_letter)
		->where('data_sk.code','SKK')
		// ->where('master_position_detail.secondary_position','false')
		->get();
		// dd(storage_path('app/public/upload',$data[0]->remark_4));
		return $data;
	}
	public static function get_print_skk($token)
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
		// 
		->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
		->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			\DB::RAW('hr_electronic_letter.token as token'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_general_data.code as category_code'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('employee_chief.name as name_chief'),
			\DB::RAW('routing_chief.description as position_chief'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			// 
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, hr_employee.name) as name'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_branch.description) as dec_branch'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_position_routing.description) as dec_position'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_department.description) as dec_dept'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, master_job_grade.description) as dec_job_grade'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_3, status_employment.description) as status')
		)
	//	->where('hr_electronic_letter.id_company',session('id_company'))
		->where('hr_electronic_letter.token',$token)
		// ->where('master_position_detail.secondary_position','false')
		->get();
		return $data;
	}
	// public static function get_employee($request)
	// {
	// 	$id_branch = ElectronicLetter::accessBranch($request);
	// 	$employee = DB::table('hr_employee AS he_all')
	// 	->join(DB::raw('(SELECT identification_number, MAX(id_employee) AS id_em1
	// 		FROM hr_employee
	// 		GROUP BY identification_number
	// 	) AS e1'), function ($join) {
	// 		$join->on('he_all.id_employee', '=', 'e1.id_em1');
	// 	})
	// 	->join('master_position_detail AS mpd', 'mpd.id_employee', '=', 'he_all.id_employee')
	// 	->select(\DB::RAW('he_all.*'))
	// 	->where('he_all.id_company', session('id_company'))
	// 	->where('mpd.secondary_position', 'false')
	// 	->where(function ($query) {
	// 		$query->whereNotExists(function ($subquery) {
	// 			$subquery->select(\DB::RAW(1))
	// 			->from('hr_electronic_letter')
	// 			->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
	// 			->leftJoin('master_general_data as data_skk','data_skk.id_general_data','=','data_category.relation_to_id_general_data')
	// 			->whereColumn('hr_electronic_letter.id_employee', '=', 'he_all.id_employee')
	// 			->where('data_skk.code','SKK');
	// 		})->orWhereNull('he_all.id_employee');
	// 	});
	// 	if ($id_branch != NULL) {
	// 		$employee->whereIn('mpd.id_branch', explode(',', $id_branch));
	// 	}
	// 	$employee = $employee->get();
	// 	return $employee;
	// }
	public static function get_category()
	{
		$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.description as description'),
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.id_general_data as id_general_data')
		)
		->where('master_general_data.code','SKK')
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
	public function get_region()
	{
		$data = MasterRegional::select(
			\DB::RAW('id_region id'),
			\DB::RAW('description text')
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
	// 	->where('master_position_detail.secondary_position','false')
	// 	->first();
	// 	return $branch;
	// }
	public static function format_save($request)
	{
		$employee_name = DB::table('hr_employee')->where('id_employee',$request->id_employee)->first();
		if (isset($request->no_name)) {
			$token = md5($request->name_input).strtotime('now');
		}else{
			$token = md5($employee_name->name).strtotime('now');
		}
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.code','SKK')
		->where('master_general_data.id_company',session('id_company'))
		->where('master_general_data.status','A')
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
		if (isset($request->no_name)) {
			$principalTextValue = $request->principal_input;
		}else{
			$principalTextValue = "";
			foreach ($request->id_principal as $key => $value) {
				$principalTextValue .= $value . ",";
			}
			$principalTextValue = rtrim($principalTextValue, ',');
		}
		return [
			'format'=>$format,
			'id_letter_type'=>$id_letter_type,
			'principalTextValue'=>$principalTextValue,
			'token'=>$token
		];
	}
}
