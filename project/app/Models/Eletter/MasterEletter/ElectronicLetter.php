<?php

namespace App\Models\Eletter\MasterEletter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Employee\Employee\Employee;

class ElectronicLetter extends Model
{
    // use HasFactory;
	protected $table="hr_electronic_letter";
	protected $primaryKey="id_letter";

	const CREATED_AT = 'created_date';
	const UPDATED_AT = 'updated_date';

	protected function accessBranch($request)
	{
		$data_access = Employee::get_access($request->id_url);
		if($data_access != null){
			foreach($data_access as $value){
				$x[] = $value->id_branch;
			}
			$group_branch = implode(",", $x);
		}
		else{
			$group_branch = null;
		}
		return $group_branch;
	}
	// get poisition dan branch untuk semua menu yang memilih opsi position
	public static function get_position_data($request)
	{
		$data = DB::table('master_position_detail')
		->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->join('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		->join('master_region','master_region.id_region','=','master_branch.id_region')
		->join('master_location','master_location.id_location','=','master_position_detail.id_location')
		->join('master_job_grade','master_job_grade.id_job_grade','=','master_position_routing.id_job_grade')
		->join('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
		->join('master_department','master_department.id_dept','=','master_job_position.id_dept')
		->select(
			\DB::RAW('master_position_detail.id_position_routing as id_position_routing'),
			\DB::RAW('master_position_routing.description as dec_position_routing'),
			// 
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			
			\DB::RAW('master_region.id_region as id_region'),
			\DB::RAW('master_branch.id_branch as id_branch'),
			\DB::RAW('master_department.id_dept as id_dept'),
			\DB::RAW('master_job_grade.id_job_grade as id_job_grade')
		)
		->where('master_position_detail.id_position_routing',$request->position_id)
		->where('master_position_detail.id_company',session('id_company'))
		->where('master_position_detail.assigned_to_company',NULL)
		->where('master_position_detail.status','A')
		->distinct()
		->get();
		return $data;
	}
	public static function change_branch_data($request)
	{
		$data = DB::table('master_position_detail')
		->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->join('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		->join('master_region','master_region.id_region','=','master_branch.id_region')
		->join('master_location','master_location.id_location','=','master_position_detail.id_location')
		->select(
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_location.description as dec_location'),
			\DB::RAW('master_region.id_region as id_region'),
			\DB::RAW('master_location.id_location as id_location')
		)
		->where('master_branch.id_branch',$request->id_branch)
		->where('master_position_routing.id_routing',$request->id_position)
		->where('master_branch.id_company',session('id_company'))
		->where('master_branch.status','A')
		->distinct()
		->get();
		return $data;
	}
	// end position dan branch
	
	// public static function getpkk($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
	// 	// ->join('master_position_detail','master_position_detail.id_position_detail','=','master_position_routing.id_routing')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	// 
	// 	->leftJoin('master_general_data as data_pkk','data_pkk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('master_department.description as dc_dept'),
	// 		\DB::RAW('master_region.description as dc_region'),
	// 		\DB::RAW('master_general_data.code as category'),
	// 		\DB::RAW('master_general_data.description as dec_category'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dc_position'),
	// 		\DB::RAW('master_company.company_code as company_cd'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('COALESCE(hr_electronic_letter.remark_1, hr_employee.name) as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_pkk.code','PKK');
	// 	// ->where('master_position_detail.secondary_position','false');
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter', 'DESC')
	// 	->get();
	// 	return $data;
	// }
	// public static function change_employee_pkwt($request)
	// {
	// 	$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
	// 	->leftJoin('master_general_data as career_category','career_category.id_general_data','=','hr_career_transaction.id_transition_category')
	// 	->leftJoin('master_general_data as career_approved','career_approved.id_general_data','=','hr_career_transaction.id_approval_status')
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
	// 	->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
	// 	->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
	// 	->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
	// 	->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
	// 	->select(
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.id_employee as id_employee'),
	// 		\DB::RAW('region_old.description as dec_region'),
	// 		\DB::RAW('region_old.id_region as id_region'),
	// 		\DB::RAW('branch_old.description as dec_branch'),
	// 		\DB::RAW('branch_old.id_branch as id_branch'),
	// 		\DB::RAW('position_detail_old.id_position_routing as id_position_routing'),
	// 		\DB::RAW('location_old.description as dec_location'),
	// 		\DB::RAW('location_old.id_location as id_location')
	// 	)
	// 	->where('position_detail_old.secondary_position','false')
	// 	->where('career_approved.description','Approved')
	// 	->where('career_category.description','Termination')
	// 	->where('hr_career_transaction.id_employee',$request->id_employee)
	// 	->where('hr_employee.id_company',session('id_company'))
	// 	->get();
	// 	return $data;
	// }
	// public static function get_edit_pkk($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	// ->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	// 
	// 	->leftJoin('master_general_data as data_pkk','data_pkk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
	// 		\DB::RAW('COALESCE(hr_electronic_letter.remark_1, hr_employee.name) as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.id_job_grade as id_job_grade'),
	// 		\DB::RAW('hr_electronic_letter.id_region as id_region'),
	// 		\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
	// 		\DB::RAW('hr_electronic_letter.id_position_detail as id_position_detail'),
	// 		\DB::RAW('hr_electronic_letter.id_location as id_location'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_location.description as dec_location'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_pkk.code','PKK')
	// 	// ->where('master_position_detail.secondary_position','false')
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->get();
	// 	return $data;
	// }
	
	// public static function get_im($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	// OLD
	// 	->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
	// 	// NEW
	// 	->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
	// 	->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
	// 	// Tanpilkan IM
	// 	->leftJoin('master_general_data as data_im','data_im.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('COALESCE(department_new.description, master_department.description) as dec_dept'),
	// 		\DB::RAW('COALESCE(region_new.description, master_region.description) as dec_region'),
	// 		\DB::RAW('COALESCE(branch_new.description, master_branch.description) as dec_branch'),
	// 		\DB::RAW('COALESCE(position_routing_new.description, master_position_routing.description) as dec_position'),
	// 		\DB::RAW('COALESCE(location_new.description, master_location.description) as dec_location'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_location.description as dec_location'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_employee.name as name')
	// 	)
	// 	->where('data_im.code','IM')
	// 	// ->where('master_position_detail.secondary_position','false')
	// 	->where('hr_electronic_letter.id_company',session('id_company'));
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch))
	// 		->whereIn('hr_electronic_letter.id_branch_new',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	// ->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	// ->get();
	// 	return $data;
	// }
	// public static function get_edit_im($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	// OLD
	// 	->leftJoin('master_region as region_old','region_old.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_department as department_old','department_old.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_location as location_old','location_old.id_location','=','hr_electronic_letter.id_location')
	// 	// // NEW
	// 	->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
	// 	->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
	// 	->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
	// 	// DATA IM
	// 	->join('master_general_data as data_im','data_im.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	// ->leftJoin('master_division','master_division.id_division','=','hr_electronic_letter.id_principal_new')
	// 	->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->leftJoin('hr_employee as name_chief','name_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->select(
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
	// 		\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('name_chief.name as chief_name'),
	// 		\DB::RAW('position_chief.description as chief_position'),
	// 		// OLD
	// 		\DB::RAW('region_old.description as dec_region_old'),
	// 		\DB::RAW('department_old.description as dec_dept_old'),
	// 		\DB::RAW('position_routing_old.description as dec_position_old'),
	// 		\DB::RAW('branch_old.description as dec_branch_old'),
	// 		\DB::RAW('location_old.description as dec_location_old'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		// // NEW
	// 		\DB::RAW('region_new.description as dec_region_new'),
	// 		\DB::RAW('department_new.description as dec_dept_new'),
	// 		\DB::RAW('position_routing_new.description as dec_position_new'),
	// 		\DB::RAW('branch_new.description as dec_branch_new'),
	// 		\DB::RAW('location_new.description as dec_location_new'),
	// 		\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new')
	// 	)
	// 	->where('data_im.code','IM')
	// 	// ->where('position_detail_old.secondary_position','false')
	// 	// ->where('position_detail_new.secondary_position','false')
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->first();
	// 	return $data;
	// }

	// public static function get_im_pdf($token)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	// OLD
	// 	->leftJoin('master_region as region_old','region_old.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_department as department_old','department_old.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_location as location_old','location_old.id_location','=','hr_electronic_letter.id_location')
	// 	// // NEW
	// 	->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
	// 	->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
	// 	->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
	// 	// ->leftJoin('master_division','master_division.id_division','=','hr_electronic_letter.id_principal_new')
	// 	// TAMPIL IM
	// 	->leftJoin('master_general_data as data_im','data_im.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	// PEMBERI KEPUTUSAN
	// 	->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_general_data.code as code_category'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
	// 		\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		// OLD
	// 		\DB::RAW('region_old.description as dec_region'),
	// 		\DB::RAW('department_old.description as dec_dept'),
	// 		\DB::RAW('position_routing_old.description as dec_position'),
	// 		\DB::RAW('branch_old.description as dec_branch'),
	// 		\DB::RAW('location_old.description as dec_location'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as dec_divisi'),
	// 		// // NEW
	// 		\DB::RAW('region_new.description as dec_region_new'),
	// 		\DB::RAW('department_new.description as dec_dept_new'),
	// 		\DB::RAW('position_routing_new.description as dec_position_new'),
	// 		\DB::RAW('branch_new.description as dec_branch_new'),
	// 		\DB::RAW('location_new.description as dec_location_new'),
	// 		\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new'),
	// 		// PEMBERI KEPUTUSAN
	// 		\DB::RAW('employee_chief.name as employee_chief_name'),
	// 		\DB::RAW('position_chief.description as employee_chief_position')
	// 	)
	// 	->where('hr_electronic_letter.token',$token)
	// 	->where('data_im.code','IM')
	// 	// ->where('position_detail_old.secondary_position','false')
	// 	// ->where('position_detail_new.secondary_position','false')
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->first();
	// 	return $data;
	// }
	

	// SKK
	// public function get_skk($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		// 
	// 		// \DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_position_routing.description as dec_position')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	// ->whereIn('master_general_data.sequence',['17','18','19'])
	// 	// ->whereIn('master_general_data.code',['REGR','UNREGR','PUB'])
	// 	->where('master_position_detail.secondary_position','false')
	// 	->where('data_sk.code','SKK');
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	// ->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	// ->get();
	// 	return $data;
	// }
	// public static function get_employee_skk($request)
	// {
	// 	if ($request->employee_status == "A") {
	// 		$data = DB::table('hr_employee')
	// 		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_employee.id_employment_status')
	// 		->leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 		->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
	// 		->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
	// 	// ->leftJoin('master_location','master_location.id_branch','=','master_branch.id_branch')
	// 		->leftJoin('master_company','hr_employee.id_company','=','master_company.id_company')
	// 		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','master_position_routing.id_job_grade')
	// 		->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
	// 		->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
	// 		->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
	// 		->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
	// 		->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
	// 		->select(
	// 			\DB::RAW('master_position_routing.description as dec_position'),
	// 			\DB::RAW('master_position_detail.id_position_detail as id_routing'),
	// 			\DB::RAW('master_branch.description as dec_branch'),
	// 			\DB::RAW('master_branch.id_branch as id_branch'),
	// 			\DB::RAW('master_company.company_name as company_name'),
	// 			\DB::RAW('master_company.id_company as id_company'),
	// 			\DB::RAW('master_region.description as dec_region'),
	// 			\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 			\DB::RAW('master_job_grade.id_job_grade as id_job_grade'),
	// 			\DB::RAW('master_department.id_dept as id_dept'),
	// 			\DB::RAW('master_department.description as dec_dept'),
	// 			\DB::RAW('master_division.description as dec_principal'),
	// 			\DB::RAW('master_division.id_division as id_principal'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('master_region.id_region as id_region'),
	// 			\DB::RAW('status_employment.description as status'),
	// 			\DB::RAW('status_employment.id_general_data as id_employment_status')
	// 		)
	// 		->where('hr_employee.id_company',session('id_company'))
	// 		->where('hr_employee.id_employee',$request->employee_id)
	// 		->where('master_position_detail.secondary_position','false')
	// 		->get();
	// 	}else{
	// 		$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
	// 		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_career_transaction.id_employment_status')
	// 		// Terminate
	// 		->leftJoin('master_general_data as status_terminate','status_terminate.id_general_data','=','hr_career_transaction.id_transaction_type')
	// 		// Approved
	// 		->leftJoin('master_general_data as status_approved','status_approved.id_general_data','=','hr_career_transaction.id_approval_status')
	// 		// 
	// 		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
	// 		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 		->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
	// 		->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
	// 		->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
	// 		->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
	// 		// 
	// 		->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
	// 		->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
	// 		->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
	// 		->leftJoin('master_company','master_company.id_company','=','hr_career_transaction.id_company')
	// 		->select(
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('master_company.company_name as company_name'),
	// 			\DB::RAW('master_company.id_company as id_company'),
	// 			\DB::RAW('region_old.description as dec_region'),
	// 			\DB::RAW('region_old.id_region as id_region'),
	// 			\DB::RAW('job_grade_old.description as dec_job_grade'),
	// 			\DB::RAW('job_grade_old.id_job_grade as id_job_grade'),
	// 			\DB::RAW('department_old.description as dec_dept'),
	// 			\DB::RAW('department_old.id_dept as id_dept'),
	// 			\DB::RAW('division_old.description as dec_principal'),
	// 			\DB::RAW('division_old.id_division as id_principal'),
	// 			\DB::RAW('branch_old.description as dec_branch'),
	// 			\DB::RAW('branch_old.id_branch as id_branch'),
	// 			\DB::RAW('position_routing_old.description as dec_position'),
	// 			\DB::RAW('position_detail_old.id_position_detail as id_routing'),
	// 			\DB::RAW('status_employment.description as status'),
	// 			\DB::RAW('status_employment.id_general_data as id_employment_status')
	// 		)
	// 		->where('status_terminate.code','Termination')
	// 		->where('status_approved.code','Approved')
	// 		->where('hr_career_transaction.id_employee',$request->employee_id)
	// 		->where('hr_career_transaction.id_company',session('id_company'))
	// 		->where('position_detail_old.secondary_position','false')
	// 		->get();
	// 	}
	// 	return $data;
	// }
	// public static function get_edit_skk($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.status as status_employee'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('status_employment.description as status')
	// 		// \DB::RAW('hr_electronic_letter.id_employment_status as id_employment_status')

	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->where('data_sk.code','SKK')
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_print_skk($token)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	// 
	// 	->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
	// 	->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_general_data.code as category_code'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('employee_chief.name as name_chief'),
	// 		\DB::RAW('routing_chief.description as position_chief'),
	// 		\DB::RAW('status_employment.description as status')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('hr_electronic_letter.token',$token)
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_sp($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	// ->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
	// 	->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.status status'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_position_routing.description as dec_position')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_sp.code','SP')
	// 	->where('master_position_detail.secondary_position','false');
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_sp_employee($request)
	// {
	// 	// $tanggal_sekarang = now()->toDateString();
	// 	// $min_enam = now()->subMonth(6)->toDateString();
	// 	$id_branch = self::accessBranch($request);
	// 	if ($request->category_type == "SP1") {
	// 		$employee = Employee::join('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 		->select(
	// 			\DB::RAW('hr_employee.name as name'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('hr_employee.id_employee as id_employee')
	// 		)
	// 		->where('hr_employee.id_company',session('id_company'))
	// 		->where('master_position_detail.secondary_position','false')
	// 		->where('hr_employee.status','A')
	// 		->where(function ($query) {
	// 			$query->whereNotExists(function ($subquery) {
	// 				$subquery->select(\DB::RAW(1))
	// 				->from('hr_electronic_letter')
	// 				->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
	// 				->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','data_category.relation_to_id_general_data')
	// 				->select(
	// 					\DB::RAW('hr_employee.name as name'),
	// 					\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 					\DB::RAW('hr_employee.id_employee as id_employee')
	// 				)
	// 				->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_employee.id_employee')
	// 				->where('data_sp.code','SP')
	// 				->where('data_category.code','SP1')
	// 				// ->where('hr_electronic_letter.expired_date', '>', date('Y-m-d'));
	// 				->where('hr_electronic_letter.effective_date', '>=', now()->subMonths(6)->toDateString());
	// 			})->orWhereNull('hr_employee.id_employee');
	// 		});
	// 		if ($id_branch != NULL) {
	// 			$employee->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
	// 		}
	// 		$employee = $employee->get();
	// 		// ->get();
	// 	}elseif ($request->category_type == "SP2") {
	// 		$employee = DB::table('hr_electronic_letter')
	// 		->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 		->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 		->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 		->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 		->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 		->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 		->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 		->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 		->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 		->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 		->select(
	// 			\DB::RAW('hr_employee.name as name'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('hr_employee.id_employee as id_employee')
	// 		)
	// 		->where('hr_electronic_letter.id_company',session('id_company'))
	// 		->where('master_general_data.code','SP1')
	// 		->where('data_sp.code','SP')
	// 		->where('hr_electronic_letter.effective_date', '>=', now()->subMonths(6)->toDateString())
	// 		// ->where('hr_electronic_letter.expired_date','>',date('Y-m-d'))
	// 		->where('hr_electronic_letter.status','A')
	// 		->where('master_position_detail.secondary_position','false')
	// 		->whereNotExists(function ($query) {
	// 			$query->select(\DB::raw(1))
	// 			->from('hr_electronic_letter AS el2')
	// 			->whereRaw('el2.id_employee = hr_electronic_letter.id_employee')
	// 			->join('master_general_data AS mgd2', 'mgd2.id_general_data', '=', 'el2.id_category')
	// 			->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','mgd2.relation_to_id_general_data')
	// 			->select(
	// 				\DB::RAW('hr_employee.name as name'),
	// 				\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 				\DB::RAW('hr_employee.id_employee as id_employee')
	// 			)
	// 			->where('mgd2.code', 'SP2')
	// 			->where('data_sp.code', 'SP')
	// 			->where('el2.effective_date', '>=', now()->subMonths(6)->toDateString())
	// 			// ->where('el2.expired_date','>',date('Y-m-d'))
	// 			->where('el2.id_company',session('id_company'))
	// 			->where('el2.status','A');
	// 		});
	// 		if ($id_branch != NULL) {
	// 			$employee->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
	// 		}
	// 		$employee = $employee->get();
	// 		// ->get();
	// 	}elseif ($request->category_type == "SP3") {
	// 		$employee = DB::table('hr_electronic_letter')
	// 		->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 		->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 		->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 		->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 		->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 		->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 		->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 		->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 		->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 		->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 		->select(
	// 			\DB::RAW('hr_employee.name as name'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('hr_employee.id_employee as id_employee')
	// 		)
	// 		->where('hr_electronic_letter.id_company',session('id_company'))
	// 		->where('master_general_data.code','SP2')
	// 		->where('data_sp.code','SP')
	// 		->where('hr_electronic_letter.effective_date', '>=', now()->subMonths(6)->toDateString())
	// 		// ->where('hr_electronic_letter.expired_date','>',date('Y-m-d'))
	// 		->where('hr_electronic_letter.status','A')
	// 		->where('master_position_detail.secondary_position','false')
	// 		->whereNotExists(function ($query) {
	// 			$query->select(\DB::raw(1))
	// 			->from('hr_electronic_letter AS el2')
	// 			->whereRaw('el2.id_employee = hr_electronic_letter.id_employee')
	// 			->join('master_general_data AS mgd2', 'mgd2.id_general_data', '=', 'el2.id_category')
	// 			->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','mgd2.relation_to_id_general_data')
	// 			->select(
	// 				\DB::RAW('hr_employee.name as name'),
	// 				\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 				\DB::RAW('hr_employee.id_employee as id_employee')
	// 			)
	// 			->where('mgd2.code', 'SP3')
	// 			->where('data_sp.code', 'PHK')
	// 			->where('el2.effective_date', '>=', now()->subMonths(6)->toDateString())
	// 			// ->where('el2.expired_date','>',date('Y-m-d'))
	// 			->where('el2.id_company',session('id_company'))
	// 			->where('el2.status','A');
	// 		});
	// 		if ($id_branch != NULL) {
	// 			$employee->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
	// 		}
	// 		$employee = $employee->get();
	// 		// ->get();
	// 	}elseif ($request->category_type == "SPDT") {
	// 		$employee = Employee::join('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 		->select(
	// 			\DB::RAW('hr_employee.name as name'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('hr_employee.id_employee as id_employee')
	// 		)
	// 		->where('hr_employee.id_company',session('id_company'))
	// 		->where('master_position_detail.secondary_position','false')
	// 		->where('hr_employee.status','A')
	// 		->where(function ($query) {
	// 			$query->whereNotExists(function ($subquery) {
	// 				$subquery->select(\DB::RAW(1))
	// 				->from('hr_electronic_letter')
	// 				->join('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
	// 				->leftJoin('master_general_data as data_spdt','data_spdt.id_general_data','=','data_category.relation_to_id_general_data')
	// 				->select(
	// 					\DB::RAW('hr_employee.name as name'),
	// 					\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 					\DB::RAW('hr_employee.id_employee as id_employee')
	// 				)
	// 				// ->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','data_category.relation_to_id_general_data')
	// 				->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_employee.id_employee')
	// 				// ->where('hr_electronic_letter.expired_date', '>', date('Y-m-d'));
	// 				// ->where('data_category.code','SPDT')
	// 				->where('data_category.code','SPDT')
	// 				->where('data_spdt.code','PHK')
	// 				->where('hr_electronic_letter.effective_date', '>=', now()->subMonths(6)->toDateString());
	// 			})->orWhereNull('hr_employee.id_employee');
	// 		});
	// 		if ($id_branch != NULL) {
	// 			$employee->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
	// 		}
	// 		$employee = $employee->get();
	// 		// ->get();
	// 	}
	// 	return $employee;
	// }
	// public static function change_employee_sp($request)
	// {
	// 	$data = DB::table('hr_employee')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_employee.id_employment_status')
	// 	->leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 	->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
	// 	->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
	// 	// ->leftJoin('master_location','master_location.id_branch','=','master_branch.id_branch')
	// 	->leftJoin('master_company','hr_employee.id_company','=','master_company.id_company')
	// 	->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','master_position_routing.id_job_grade')
	// 	->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
	// 	->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
	// 	->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
	// 	->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
	// 	->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
	// 	->select(
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_position_detail.id_position_detail as id_routing'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_branch.id_branch as id_branch'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_company.id_company as id_company'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('master_job_grade.id_job_grade as id_job_grade'),
	// 		\DB::RAW('master_department.id_dept as id_dept'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_division.description as dec_principal'),
	// 		\DB::RAW('master_division.id_division as id_principal'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_region.id_region as id_region'),
	// 		\DB::RAW('status_employment.description as status'),
	// 		\DB::RAW('status_employment.id_general_data as id_employment_status')
	// 	)
	// 	->where('hr_employee.id_company',session('id_company'))
	// 	->where('hr_employee.id_employee',$request->employee_id)
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_edit_sp($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	// ->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','hr_electronic_letter.id_employment_status')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.id_employment_status as id_employment_status'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('status_employment.description as status'),
	// 		\DB::RAW('master_job_grade.description dec_job_grade')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_view_sp($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	// ->join('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	// STATUS
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
	// 	// MENYETUJUI
	// 	->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('master_general_data.code as category'),
	// 		\DB::RAW('status_employment.description as dec_status'),
	// 		\DB::RAW('chief_name.name as nama_menyetujui'),
	// 		\DB::RAW('position_chief.description as jabatan_menyetujui'),
	// 		\DB::RAW('master_position_routing.description as dec_position')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public static function print_sp($token)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	// ->join('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	// 
	// 	->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.id_employee as id_employee'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_general_data.code as code_category'),
	// 		// 
	// 		\DB::RAW('employee_chief.name as name_chief'),
	// 		\DB::RAW('routing_chief.description as position_chief'),
	// 		// 
	// 		\DB::RAW('master_position_routing.description as dec_position')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('hr_electronic_letter.token',$token)
	// 	->where('master_position_detail.secondary_position','false')
	// 	->whereIn('master_general_data.code',['SP1','SP2','SP3','SPDT'])
	// 	->get();
	// 	return $data;
	// }
	// public function cek_exp_sp() // SP (SP)
	// {
	// 	$cek_ex = ElectronicLetter::join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date')
	// 	)
	// 	->where('hr_electronic_letter.status','A')
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_sp.code','SP')
	// 	->whereIn('master_general_data.code',['SP1','SP2'])
	// 	->get();
	// 	foreach ($cek_ex as $cex) {
	// 		if ($cex->effective_date >= now()->subMonths(6)->toDateString()) {
	// 		}else{
	// 			ElectronicLetter::where('id_letter',$cex->id_letter)
	// 			->update([
	// 				'status'=>'I'
	// 			]);
	// 		}
	// 	}
	// }
	// public function cek_exp_spdt() // SP (PHK)
	// {
	// 	$cek_ex = ElectronicLetter::join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->leftJoin('master_general_data as data_sp','data_sp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date')
	// 	)
	// 	->where('hr_electronic_letter.status','A')
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_sp.code','PHK')
	// 	->whereIn('master_general_data.code',['SP3','SPDT'])
	// 	->get();
	// 	foreach ($cek_ex as $cex) {
	// 		if ($cex->effective_date >= now()->subMonths(6)->toDateString()) {
	// 		}else{
	// 			ElectronicLetter::where('id_letter',$cex->id_letter)
	// 			->update([
	// 				'status'=>'I'
	// 			]);
	// 		}
	// 	}
	// }

	// public static function get_swp($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->leftJoin('master_general_data as data_phk','data_phk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.status as status'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_position_routing.description as dec_position')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('master_position_detail.secondary_position','false')
	// 	->where('data_phk.code','PHK')
	// 	->whereIn('master_general_data.code',['SP3','SPDT']);
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	return $data;
	// }

	// SUPA
	// public static function table_view_supa1($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	// $data = DB::table('hr_employee')
	// 	// ->join('master_position_detail as name_employee', 'name_employee.id_employee', '=', 'hr_employee.id_employee')
	// 	// ->join('master_position_routing', 'master_position_routing.id_routing', '=', 'name_employee.id_position_routing')
	// 	// ->join('master_branch', 'master_branch.id_branch', '=', 'name_employee.id_branch')
	// 	// ->join('master_region', 'master_region.id_region', '=', 'master_branch.id_region')
	// 	// ->join('master_location', 'master_location.id_location', '=', 'name_employee.id_location')
	// 	// ->join('master_company', 'hr_employee.id_company', '=', 'master_company.id_company')
	// 	// ->join('master_job_grade', 'master_job_grade.id_job_grade', '=', 'master_position_routing.id_job_grade')
	// 	// ->join('master_job_position', 'master_job_position.id_position', '=', 'master_position_routing.id_position')
	// 	// ->join('master_department', 'master_department.id_dept', '=', 'master_job_position.id_dept')
	// 	// ->join('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'name_employee.id_position_detail')
	// 	// ->join('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
	// 	// ->join('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
	// 	// ->leftJoin('hr_employee as atasan', 'atasan.id_employee', '=', 'name_employee.parent_id_position_detail')
	// 	// ->select(
	// 	// 	\DB::RAW('hr_employee.id_employee as id_employee'),
	// 	// 	\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 	// 	\DB::RAW('hr_employee.name as nama_karyawan'),
 //  //       \DB::RAW('atasan.name as nama_atasan'), // Menggunakan subquery untuk mengambil nama atasan
 //  //       \DB::RAW('atasan.nik_employee as nik_atasan'), // Menggunakan subquery untuk mengambil nama atasan
 //  //       \DB::RAW('master_division.description as dec_divisi'),
 //  //       \DB::RAW('master_department.description as dec_dept'),
 //  //       \DB::RAW('master_region.description as dec_region'),
 //  //       \DB::RAW('master_branch.description as dec_branch'),
 //  //       \DB::RAW('master_job_grade.description as dec_job_grade')
 //  //   )
	// 	// ->where('hr_employee.id_company', session('id_company'))
	// 	// ->where('hr_employee.status', 'A')
	// 	// ->where('name_employee.secondary_position','false')
	// 	// ->get();
	// 	$data = Employee::join('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 	->select(
	// 		\DB::RAW('hr_employee.name as nama_karyawan'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.id_employee as id_employee')
	// 	)
	// 	->where('hr_employee.id_company',session('id_company'))
	// 	->where('master_position_detail.secondary_position','false')
	// 	->where('hr_employee.status','A');
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->get();
	// 	return $data;
	// }
	// public static function select_change_supa1($request)
	// {
	// 	$data = DB::table('hr_employee')
	// 	->join('master_position_detail as name_employee', 'name_employee.id_employee', '=', 'hr_employee.id_employee')
	// 	->join('master_position_routing', 'master_position_routing.id_routing', '=', 'name_employee.id_position_routing')
	// 	->join('master_branch', 'master_branch.id_branch', '=', 'name_employee.id_branch')
	// 	->join('master_region', 'master_region.id_region', '=', 'master_branch.id_region')
	// 	->join('master_location', 'master_location.id_location', '=', 'name_employee.id_location')
	// 	->join('master_company', 'hr_employee.id_company', '=', 'master_company.id_company')
	// 	->join('master_job_grade', 'master_job_grade.id_job_grade', '=', 'master_position_routing.id_job_grade')
	// 	->join('master_job_position', 'master_job_position.id_position', '=', 'master_position_routing.id_position')
	// 	->join('master_department', 'master_department.id_dept', '=', 'master_job_position.id_dept')
	// 	->join('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'name_employee.id_position_detail')
	// 	->join('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
	// 	->join('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
	// 	->leftJoin('hr_employee as atasan', 'atasan.id_employee', '=', 'name_employee.parent_id_position_detail')
	// 	// ->leftJoin('master_position_routing as position_atasan', 'name_employee.id_employee', '=', 'name_employee.parent_id_position_detail')
	// 	->select(
	// 		\DB::RAW('hr_employee.id_employee as id_employee'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.name as nama_karyawan'),
	// 		\DB::RAW('atasan.name as nama_atasan'),
	// 		\DB::RAW('atasan.nik_employee as nik_atasan'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_company.id_company as id_company'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_department.id_dept as id_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_region.id_region as id_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_branch.id_branch as id_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('name_employee.id_position_detail as id_routing')
	// 	)
	// 	// ->groupBy('hr_employee.id_employee','hr_employee.name','hr_employee.nik_employee','master_department.description','master_region.description','master_branch.description','master_position_routing.description','atasan.name','atasan.nik_employee','master_company.company_name','master_company.id_company','master_department.id_dept','master_region.id_region','master_position_routing.id_routing','master_branch.id_branch')
	// 	->where('hr_employee.id_company', session('id_company'))
	// 	->where('hr_employee.id_employee', $request->employee_id)
	// 	->where('hr_employee.status', 'A')
	// 	->where('name_employee.secondary_position','false')
	// 	->first();
	// 	return $data;
	// }
	// public static function get_supa($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	->join('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter')
	// 	)
	// 	->where('hr_electronic_letter.id_company', session('id_company'))
	// 	->where('data_supa.code','SUPA')
	// 	->where('master_position_detail.secondary_position','false');
	// 	// ->whereIn('master_general_data.code', ['SUPA1','SUPA2'])
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_supa2($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	// ->join('master_position_routing', 'master_position_routing.id_routing', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_general_data.code as category'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter')
	// 	)
	// 	->where('hr_electronic_letter.id_company', session('id_company'))
	// 	->where('master_general_data.code', 'SUPA1')
	// 	->where('data_supa.code','SUPA')
	// 	->where('master_position_detail.secondary_position','false');
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	// ->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	// ->get();
	// 	return $data;
	// }
	// public function select_change_supa2($request)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	// ->join('master_position_routing', 'master_position_routing.id_routing', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
	// 		\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter')
	// 	)
	// 	->where('hr_electronic_letter.id_company', session('id_company'))
	// 	->where('hr_electronic_letter.id_letter', $request->letter_id)
	// 	// ->whereIn('master_general_data.code', ['SUPA1','SUPA2'])
	// 	->where('data_supa.code','SUPA')
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_edit_supa($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	// ->join('master_position_routing', 'master_position_routing.id_routing', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	// PENGIRIM
	// 	->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->leftJoin('hr_employee as name_chief','name_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->select(
	// 		\DB::RAW('hr_employee.id_employee as id_employee'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_general_data.description as category_code'),
	// 		\DB::RAW('master_general_data.code as code'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('hr_electronic_letter.remark_7 as remark_7'),
	// 		\DB::RAW('position_chief.description as jabatan_pengirim'),
	// 		\DB::RAW('name_chief.name as nama_pengirim'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief')
	// 	)
	// 	->where('hr_electronic_letter.id_company', session('id_company'))
	// 	->where('hr_electronic_letter.id_letter', $id_letter)
	// 	->where('data_supa.code', 'SUPA')
	// 	->where('master_position_detail.secondary_position','false')
	// 	->first();
	// 	return $data;
	// }
	// public static function print_supa($token)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	// ->join('master_position_routing', 'master_position_routing.id_routing', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_supa','data_supa.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 			// PENGIRIM
	// 	->leftJoin('master_position_routing as position_chief','position_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->leftJoin('hr_employee as name_chief','name_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->select(
	// 		\DB::RAW('hr_employee.id_employee as id_employee'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_general_data.code as category_code'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_6 as remark_6'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('hr_electronic_letter.remark_7 as remark_7'),
	// 		\DB::RAW('position_chief.description as jabatan_pengirim'),
	// 		\DB::RAW('name_chief.name as nama_pengirim'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief')
	// 	)
	// 	->where('hr_electronic_letter.id_company', session('id_company'))
	// 	->where('hr_electronic_letter.token', $token)
	// 	// ->whereIn('master_general_data.code', ['SUPA1','SUPA2'])
	// 	->where('master_position_detail.secondary_position','false')
	// 	->where('data_supa.code', 'SUPA')
	// 	->first();
	// 	return $data;
	// }

	// public static function change_employee_pb($request)
	// {
	// 	$data = DB::table('hr_employee')
	// 	->join('master_position_detail', 'master_position_detail.id_employee', '=', 'hr_employee.id_employee')
	// 	->join('master_position_routing', 'master_position_routing.id_routing', '=', 'master_position_detail.id_position_routing')
	// 	->join('master_branch', 'master_branch.id_branch', '=', 'master_position_detail.id_branch')
	// 	->join('master_region', 'master_region.id_region', '=', 'master_branch.id_region')
	// 	->join('master_company', 'hr_employee.id_company', '=', 'master_company.id_company')
	// 	->join('master_job_position', 'master_job_position.id_position', '=', 'master_position_routing.id_position')
	// 	->join('master_department', 'master_department.id_dept', '=', 'master_job_position.id_dept')
	// 	->select(
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_department.id_dept as id_dept'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_region.id_region as id_region'),
	// 		\DB::RAW('master_branch.id_branch as id_branch'),
	// 		\DB::RAW('master_position_routing.id_routing as id_position_routing'),
	// 		\DB::RAW('master_position_routing.description as dec_position')
	// 	)
	// 	->where('hr_employee.id_company', session('id_company'))
	// 	->where('hr_employee.id_employee', $request->employee_id)
	// 	->where('hr_employee.status', 'A')
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public function get_pb($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->leftJoin('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	// ->leftJoin('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'hr_electronic_letter.id_position_routing')
	// 	->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
	// 	->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_letter','data_letter.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		// \DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter')
	// 	)
	// 	->where('hr_electronic_letter.id_company', session('id_company'))
	// 	->where('master_general_data.code', 'PB');
	// 	// ->where('master_position_detail.secondary_position','false');
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	// ->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	// ->get();
	// 	return $data;
	// }
	// public static function get_edit_pb($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->leftJoin('hr_employee','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	// ->leftJoin('master_position_detail', 'master_position_detail.id_position_detail', '=', 'hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing', 'master_position_routing.id_routing', '=', 'hr_electronic_letter.id_position_routing')
	// 	->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
	// 	->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_letter','data_letter.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as chief_position','chief_position.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.id_region as id_region'),
	// 		\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
	// 		\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
	// 		\DB::RAW('hr_electronic_letter.id_position_routing as id_position_routing'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('chief_name.name as nama_pengirim'),
	// 		\DB::RAW('chief_position.description as jabatan_pengirim'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter')
	// 	)
	// 	->where('hr_electronic_letter.id_company', session('id_company'))
	// 	->where('hr_electronic_letter.id_letter', $id_letter)
	// 	->where('master_general_data.code', 'PB')
	// 	// ->where('master_position_detail.secondary_position','false')
	// 	->first();
	// 	return $data;
	// }
	// public static function get_ext($request)
	// {
	// 	// $ext = DB::table('master_general_data')->select(\DB::RAW('id_general_data'))
	// 	// ->where('code','EXT')
	// 	// ->where('id_company',session('id_company'))
	// 	// ->first();
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('hr_employee','hr_electronic_letter.id_employee_chief','=','hr_employee.id_employee')
	// 	->join('master_position_routing','hr_electronic_letter.id_position_routing_chief','=','master_position_routing.id_routing')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_ext','data_ext.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch')
	// 	)
	// 	// ->where('master_general_data.code','EXT')
	// 	->where('data_ext.code','EXT')
	// 	// ->where('master_general_data.code','!=','CA')
	// 	->where('hr_electronic_letter.id_company',session('id_company'));
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_edit_ext($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('hr_employee','hr_electronic_letter.id_employee_chief','=','hr_employee.id_employee')
	// 	->join('master_position_routing','hr_electronic_letter.id_position_routing_chief','=','master_position_routing.id_routing')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_ext','data_ext.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_company as id_company'),
	// 		\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
	// 		\DB::RAW('hr_electronic_letter.id_region as id_region'),
	// 		\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_position_routing.description as jabatan_pemberi'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('hr_employee.name as nama_pemberi')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_ext.code','EXT')
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->get();
	// 	return $data;
	// }

	// public static function get_sk_career($request)
	// {
	// 	// $id_branch = self::accessBranch($request);
	// 	$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
	// 	->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_career_transaction.id_transaction_type')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_career_transaction.id_employment_status')
	// 	// old
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
	// 	// new
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_career_transaction.id_position_detail')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
	// 	->select(
	// 		\DB::RAW('hr_career_transaction.id_career_transaction as id_career_transaction'),
	// 		\DB::RAW('hr_career_transaction.reference_number as reference_number'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_general_data.description as type'),
	// 		\DB::RAW('position_routing_old.description dec_position_old'),
	// 		\DB::RAW('branch_old.description as dec_branch_old'),
	// 		\DB::RAW('job_grade_old.description as dec_job_grade_old'),
	// 		\DB::RAW('position_routing_new.description dec_position_new'),
	// 		\DB::RAW('branch_new.description as dec_branch_new'),
	// 		\DB::RAW('job_grade_new.description as dec_job_grade_new'),
	// 		\DB::RAW('status_employment.description as status')
	// 	)
	// 	->where('position_detail_old.secondary_position','false')
	// 	->where('position_detail_new.secondary_position','false')
	// 	->where('master_general_data.code','!=','Terminate')
	// 	->where('master_general_data.code','!=','Join')
	// 	->where('hr_career_transaction.id_employee',$request->employee_id)
	// 	->where('hr_career_transaction.id_company',session('id_company'));
	// 	if ($request->category_type == 'Permanent') {
	// 		$data->where('master_general_data.description','Employment Status Changes');
	// 		// $data->where('master_general_data.code','Movement');
	// 	}else{
	// 		// $data->where('master_general_data.code','Movement');
	// 		$data->where('master_general_data.description','!=','Employment Status Changes');
	// 	}
	// 	$data = $data->get();
	// 	return $data;
	// }
	// public static function change_career_sk($request)
	// {
	// 	$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
	// 	->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_career_transaction.id_transaction_type')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_career_transaction.id_employment_status')
	// 	// OLD
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
	// 	->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
	// 	->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
	// 	->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
	// 	->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
	// 	->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
	// 	->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
	// 	->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
	// 	// NEW
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_career_transaction.id_position_detail')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
	// 	->leftJoin('master_region as region_new','region_new.id_region','=','branch_new.id_region')
	// 	->leftJoin('master_job_position as job_position_new','job_position_new.id_position','=','position_routing_new.id_position')
	// 	->leftJoin('master_department as department_new','department_new.id_dept','=','job_position_new.id_dept')
	// 	->leftJoin('master_location as location_new','location_new.id_location','=','position_detail_new.id_location')
	// 	->leftJoin('relation_positiondetail_principal as relation_principal_new','relation_principal_new.id_position_detail','=','position_detail_new.id_position_detail')
	// 	->leftJoin('master_principal as principal_new','principal_new.id_principal','=','relation_principal_new.id_principal')
	// 	->leftJoin('master_division as division_new','division_new.id_division','=','principal_new.id_division')
	// 	// END
	// 	->select(
	// 		\DB::RAW('hr_career_transaction.id_career_transaction as id_career_transaction'),
	// 		\DB::RAW('hr_career_transaction.reference_number as reference_number'),
	// 		\DB::RAW('status_employment.description as status'),
	// 		\DB::RAW('status_employment.id_general_data as id_employment_status'),
	// 		// 
	// 		\DB::RAW('job_grade_old.id_job_grade as id_job_grade_old'),
	// 		\DB::RAW('job_grade_old.description as dec_job_grade_old'),
	// 		\DB::RAW('region_old.description as dec_region_old'),
	// 		\DB::RAW('region_old.id_region as id_region_old'),
	// 		\DB::RAW('branch_old.description as dec_branch_old'),
	// 		\DB::RAW('branch_old.id_branch as id_branch_old'),
	// 		\DB::RAW('position_routing_old.description as dec_position_old'),
	// 		\DB::RAW('position_detail_old.id_position_detail as id_position_detail_old'),
	// 		\DB::RAW('department_old.description as dec_dept_old'),
	// 		\DB::RAW('department_old.id_dept as id_dept_old'),
	// 		\DB::RAW('location_old.description as dec_location_old'),
	// 		\DB::RAW('location_old.id_location as id_location_old'),
	// 		\DB::RAW('division_old.description as dec_divisi_old'),
	// 		// 
	// 		\DB::RAW('job_grade_new.id_job_grade as id_job_grade_new'),
	// 		\DB::RAW('job_grade_new.description as dec_job_grade_new'),
	// 		\DB::RAW('region_new.description as dec_region_new'),
	// 		\DB::RAW('region_new.id_region as id_region_new'),
	// 		\DB::RAW('branch_new.description as dec_branch_new'),
	// 		\DB::RAW('branch_new.id_branch as id_branch_new'),
	// 		\DB::RAW('position_routing_new.description as dec_position_new'),
	// 		\DB::RAW('position_detail_new.id_position_detail as id_position_detail_new'),
	// 		\DB::RAW('department_new.description as dec_dept_new'),
	// 		\DB::RAW('department_new.id_dept as id_dept_new'),
	// 		\DB::RAW('location_new.description as dec_location_new'),
	// 		\DB::RAW('location_new.id_location as id_location_new'),
	// 		\DB::RAW('division_new.description as dec_divisi_new'),
	// 		\DB::RAW('division_new.id_division as id_division_new')

	// 	)
	// 	->where('position_detail_old.secondary_position','false')
	// 	->where('position_detail_new.secondary_position','false')
	// 	->where('hr_career_transaction.id_career_transaction',$request->id_career_transaction)
	// 	->where('hr_career_transaction.id_company',session('id_company'))
	// 	->get();
	// 	return $data;
	// }
	// public static function get_sk($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = Employee::join('hr_electronic_letter','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	// OLD
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
	// 	->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
	// 	->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
	// 	->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
	// 	->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
	// 	->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
	// 	->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
	// 	->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
	// 	// NEW
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
	// 	->leftJoin('master_region as region_new','region_new.id_region','=','branch_new.id_region')
	// 	->leftJoin('master_job_position as job_position_new','job_position_new.id_position','=','position_routing_new.id_position')
	// 	->leftJoin('master_department as department_new','department_new.id_dept','=','job_position_new.id_dept')
	// 	->leftJoin('master_location as location_new','location_new.id_location','=','position_detail_new.id_location')
	// 	->leftJoin('relation_positiondetail_principal as relation_principal_new','relation_principal_new.id_position_detail','=','position_detail_new.id_position_detail')
	// 	->leftJoin('master_principal as principal_new','principal_new.id_principal','=','relation_principal_new.id_principal')
	// 	->leftJoin('master_division as division_new','division_new.id_division','=','principal_new.id_division')
	// 	// TAMPIL SK
	// 	->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	// END
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('COALESCE(region_new.description, region_old.description) as dec_region_old'),
	// 		\DB::RAW('COALESCE(branch_new.description, branch_old.description) as dec_branch_old'),
	// 		\DB::RAW('COALESCE(position_routing_new.description, position_routing_old.description) as dec_position_old'),
	// 		\DB::RAW('COALESCE(department_new.description, department_old.description) as dec_dept_old'),
	// 		\DB::RAW('COALESCE(location_new.description, location_old.description) as dec_location_old')
	// 	)
	// 	->where('data_sk.code','SK')
	// 	->where('hr_electronic_letter.id_company',session('id_company'));
	// 	// ->where('position_detail_old.secondary_position','false')
	// 	// ->where('position_detail_new.secondary_position','false');
	// 	// ->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	// ->get();
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch))
	// 		->whereIn('hr_electronic_letter.id_branch_new',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')->get();
	// 	return $data;
	// }
	// public static function get_edit_sk($id_letter)
	// {
	// 	// $data = Employee::join('hr_electronic_letter','hr_electronic_letter.id_employee','=','hr_employee.id_employee')
	// 	// ->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
	// 	// ->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	// ->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	// // OLD
	// 	// ->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	// ->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 	// ->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	// ->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
	// 	// ->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
	// 	// ->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
	// 	// ->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
	// 	// ->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
	// 	// ->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
	// 	// ->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
	// 	// ->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
	// 	// // NEW
	// 	// ->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
	// 	// ->leftJoin('master_branch as branch_new','branch_new.id_branch','=','position_detail_new.id_branch')
	// 	// ->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	// ->leftJoin('master_job_grade as job_grade_new','job_grade_new.id_job_grade','=','position_routing_new.id_job_grade')
	// 	// ->leftJoin('master_region as region_new','region_new.id_region','=','branch_new.id_region')
	// 	// ->leftJoin('master_job_position as job_position_new','job_position_new.id_position','=','position_routing_new.id_position')
	// 	// ->leftJoin('master_department as department_new','department_new.id_dept','=','job_position_new.id_dept')
	// 	// ->leftJoin('master_location as location_new','location_new.id_location','=','position_detail_new.id_location')
	// 	// ->leftJoin('relation_positiondetail_principal as relation_principal_new','relation_principal_new.id_position_detail','=','position_detail_new.id_position_detail')
	// 	// ->leftJoin('master_principal as principal_new','principal_new.id_principal','=','relation_principal_new.id_principal')
	// 	// ->leftJoin('master_division as division_new','division_new.id_division','=','principal_new.id_division')
	// 	// // END
	// 	// ->select(
	// 	// 	\DB::RAW('hr_career_transaction.reference_number as reference_number_career'),
	// 	// 	\DB::RAW('hr_electronic_letter.id_career_transaction as id_career_transaction'),
	// 	// 	\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 	// 	\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
	// 	// 	\DB::RAW('hr_electronic_letter.date as date'),
	// 	// 	\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 	// 	\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 	// 	// OLD
	// 	// 	\DB::RAW('region_old.description as dec_region_old'),
	// 	// 	\DB::RAW('department_old.description as dec_dept_old'),
	// 	// 	\DB::RAW('position_detail_old.description as dec_position_old'),
	// 	// 	\DB::RAW('branch_old.description as dec_branch_old'),
	// 	// 	\DB::RAW('location_old.description as dec_location_old'),
	// 	// 	\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 	// 	// NEW
	// 	// 	\DB::RAW('region_new.description as dec_region_new')
	// 	// 	// \DB::RAW('department_old.description as dec_dept_old'),
	// 	// 	// \DB::RAW('position_detail_old.description as dec_position_old'),
	// 	// 	// \DB::RAW('branch_old.description as dec_branch_old'),
	// 	// 	// \DB::RAW('location_old.description as dec_location_old'),
	// 	// 	// \DB::RAW('hr_electronic_letter.id_principal as id_principal')
	// 	// )
	// 	// ->where('hr_electronic_letter.id_company',session('id_company'))
	// 	// ->where('hr_electronic_letter.id_letter',$id_letter)
	// 	// ->get();
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
	// 	// OLD
	// 	->leftJoin('master_region as region_old','region_old.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_department as department_old','department_old.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_location as location_old','location_old.id_location','=','hr_electronic_letter.id_location')
	// 	// // NEW
	// 	->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
	// 	->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
	// 	->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
	// 	// ->leftJoin('master_division','master_division.id_division','=','hr_electronic_letter.id_principal_new')
	// 	// TAMPIL SK
	// 	->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	// CHIEF
	// 	->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_career_transaction.reference_number as reference_number_career'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.id_employment_status as id_employment_status'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('status_employment.description as status'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		// \DB::RAW('master_division.description as dec_divisi_new'),
	// 		// OLD
	// 		\DB::RAW('region_old.description as dec_region_old'),
	// 		\DB::RAW('department_old.description as dec_dept_old'),
	// 		\DB::RAW('position_routing_old.description as dec_position_old'),
	// 		\DB::RAW('branch_old.description as dec_branch_old'),
	// 		\DB::RAW('location_old.description as dec_location_old'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		// // NEW
	// 		\DB::RAW('region_new.description as dec_region_new'),
	// 		\DB::RAW('department_new.description as dec_dept_new'),
	// 		\DB::RAW('position_routing_new.description as dec_position_new'),
	// 		\DB::RAW('branch_new.description as dec_branch_new'),
	// 		\DB::RAW('location_new.description as dec_location_new'),
	// 		\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new'),
	// 		// CHIEF
	// 		\DB::RAW('employee_chief.name as name_chief'),
	// 		\DB::RAW('routing_chief.description as position_chief')
	// 	)
	// 	->where('data_sk.code','SK')
	// 	// ->where('position_detail_old.secondary_position','false')
	// 	// ->where('position_detail_new.secondary_position','false')
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->first();
	// 	return $data;
	// }
	// public static function get_print_sk($token)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_career_transaction','hr_career_transaction.id_career_transaction','=','hr_electronic_letter.id_career_transaction')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
	// 	// CHIEF
	// 	->leftJoin('hr_employee as employee_chief','employee_chief.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as routing_chief','routing_chief.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	// OLD
	// 	->leftJoin('master_region as region_old','region_old.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_department as department_old','department_old.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 	->leftJoin('master_branch as branch_old','branch_old.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_location as location_old','location_old.id_location','=','hr_electronic_letter.id_location')
	// 	// // NEW
	// 	->leftJoin('master_region as region_new','region_new.id_region','=','hr_electronic_letter.id_regional_new')
	// 	->leftJoin('master_department as department_new','department_new.id_dept','=','hr_electronic_letter.id_dept_new')
	// 	->leftJoin('master_position_detail as position_detail_new','position_detail_new.id_position_detail','=','hr_electronic_letter.id_position_detail_new')
	// 	->leftJoin('master_position_routing as position_routing_new','position_routing_new.id_routing','=','position_detail_new.id_position_routing')
	// 	->leftJoin('master_branch as branch_new','branch_new.id_branch','=','hr_electronic_letter.id_branch_new')
	// 	->leftJoin('master_location as location_new','location_new.id_location','=','hr_electronic_letter.id_location_new')
	// 	// ->leftJoin('master_division','master_division.id_division','=','hr_electronic_letter.id_principal_new')
	// 	->leftJoin('master_general_data as data_sk','data_sk.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('employee_chief.name as name_chief'),
	// 		\DB::RAW('routing_chief.description as position_chief'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_general_data.code as code_category'),
	// 		// OLD
	// 		\DB::RAW('position_routing_old.description as dec_position_old'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('location_old.description as dec_location_old'),
	// 		// NEW
	// 		\DB::RAW('position_routing_new.description as dec_position_new'),
	// 		\DB::RAW('status_employment.description as status'),
	// 		\DB::RAW('branch_new.description as dec_branch_new'),
	// 		\DB::RAW('hr_electronic_letter.id_principal_new as id_principal_new'),
	// 		\DB::RAW('location_new.description as dec_location_new')
	// 	)
	// 	->where('data_sk.code','SK')
	// 	->where('hr_electronic_letter.token',$token)
	// 	// ->where('position_detail_old.secondary_position','false')
	// 	// ->where('position_detail_new.secondary_position','false')
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->first();
	// 	return $data;
	// }
	// public static function get_career_skp($request)
	// {
	// 	if ($request->status_employee == 'I') {
	// 		$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
	// 		// Terminate
	// 		->leftJoin('master_general_data as status_terminate','status_terminate.id_general_data','=','hr_career_transaction.id_transaction_type')
	// 		// Approved
	// 		->leftJoin('master_general_data as status_approved','status_approved.id_general_data','=','hr_career_transaction.id_approval_status')
	// 		// 
	// 		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
	// 		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 		->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
	// 		->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
	// 		->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
	// 		// 
	// 		->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
	// 		->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
	// 		->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
	// 		->select(
	// 			\DB::RAW('hr_employee.id_employee as id_employee'),
	// 			\DB::RAW('hr_employee.name as name'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('hr_employee.status as status_employee'),
	// 			\DB::RAW('region_old.description as dec_region'),
	// 			\DB::RAW('branch_old.description as dec_branch'),
	// 			\DB::RAW('position_routing_old.description as dec_position'),
	// 			\DB::RAW('department_old.description as dec_dept')
	// 		)
	// 		->where('status_terminate.code','Termination')
	// 		->where('status_approved.code','Approved')
	// 		->where('position_detail_old.secondary_position','false')
	// 		->where('hr_career_transaction.id_employee',$request->id_employee)
	// 		->where('hr_career_transaction.id_company',session('id_company'))
	// 		->get();
	// 	}elseif($request->status_employee == 'A'){
	// 		$data = Employee::leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 		->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
	// 		->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
	// 		->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
	// 		->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
	// 		// 
	// 		->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
	// 		->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
	// 		->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
	// 		->select(
	// 			\DB::RAW('hr_employee.id_employee as id_employee'),
	// 			\DB::RAW('hr_employee.name as name'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('hr_employee.status as status_employee'),
	// 			\DB::RAW('master_region.description as dec_region'),
	// 			\DB::RAW('master_branch.description as dec_branch'),
	// 			\DB::RAW('master_position_routing.description as dec_position'),
	// 			\DB::RAW('master_department.description as dec_dept')
	// 		)
	// 		->where('master_position_detail.id_employee',$request->id_employee)
	// 		->where('master_position_detail.secondary_position','false')
	// 		->where('master_position_detail.id_company',session('id_company'))
	// 		->get();
	// 	}
	// 	return $data;
	// }
	// public static function change_career_skp($request)
	// {
	// 	if ($request->status_employee == 'I') {
	// 		$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
	// 		// Terminate
	// 		->leftJoin('master_general_data as status_terminate','status_terminate.id_general_data','=','hr_career_transaction.id_transaction_type')
	// 		// Approved
	// 		->leftJoin('master_general_data as status_approved','status_approved.id_general_data','=','hr_career_transaction.id_approval_status')
	// 		// 
	// 		->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
	// 		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
	// 		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
	// 		->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
	// 		->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
	// 		->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
	// 		// 
	// 		->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
	// 		->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
	// 		->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
	// 		->leftJoin('master_company','master_company.id_company','=','hr_career_transaction.id_company')
	// 		->select(
	// 			\DB::RAW('hr_career_transaction.id_employee as id_employee'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('master_company.company_name as company_name'),
	// 			\DB::RAW('region_old.description as dec_region'),
	// 			\DB::RAW('region_old.id_region as id_region'),
	// 			\DB::RAW('department_old.description as dec_dept'),
	// 			\DB::RAW('department_old.id_dept as id_dept'),
	// 			\DB::RAW('branch_old.description as dec_branch'),
	// 			\DB::RAW('branch_old.id_branch as id_branch'),
	// 			\DB::RAW('position_routing_old.description as dec_position_routing'),
	// 			\DB::RAW('position_detail_old.id_position_detail as id_position_detail')
	// 		)
	// 		->where('status_terminate.code','Termination')
	// 		->where('status_approved.code','Approved')
	// 		->where('hr_career_transaction.id_employee',$request->id_employee)
	// 		->where('hr_career_transaction.id_company',session('id_company'))
	// 		->where('position_detail_old.secondary_position','false')
	// 		->get();
	// 	}elseif($request->status_employee == 'A'){
	// 		$data = Employee::leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 		->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
	// 		->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
	// 		->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
	// 		->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
	// 		// 
	// 		->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
	// 		->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
	// 		->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
	// 		->leftJoin('master_company','master_company.id_company','=','hr_employee.id_company')
	// 		->select(
	// 			\DB::RAW('hr_employee.id_employee as id_employee'),
	// 			\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 			\DB::RAW('master_company.company_name as company_name'),
	// 			\DB::RAW('master_region.description as dec_region'),
	// 			\DB::RAW('master_region.id_region as id_region'),
	// 			\DB::RAW('master_department.description as dec_dept'),
	// 			\DB::RAW('master_department.id_dept as id_dept'),
	// 			\DB::RAW('master_branch.description as dec_branch'),
	// 			\DB::RAW('master_branch.id_branch as id_branch'),
	// 			\DB::RAW('master_position_routing.description as dec_position_routing'),
	// 			\DB::RAW('master_position_detail.id_position_detail as id_position_detail')
	// 		)
	// 		->where('hr_employee.id_employee',$request->id_employee)
	// 		->where('hr_employee.id_company',session('id_company'))
	// 		->where('master_position_detail.secondary_position','false')
	// 		->get();
	// 	}
	// 	return $data;
	// }

	// public static function get_skp($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_general_data as data_skp','data_skp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes')
	// 	)
	// 	->where('data_skp.code','SKP')
	// 	->where('master_position_detail.secondary_position','false')
	// 	->where('hr_electronic_letter.id_company',session('id_company'));
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')->get();
	// 	// ->get();
	// 	return $data;
	// }
	// public static function get_edit_skp($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->join('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->join('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_general_data as data_skp','data_skp.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as chief_position','chief_position.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('chief_name.name as name_chief'),
	// 		\DB::RAW('chief_position.description as position_chief'),
	// 		\DB::RAW('hr_employee.status as status'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes')
	// 	)
	// 	->where('data_skp.code','SKP')
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }

	// public static function get_ski()
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_ski','data_ski.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.token as token'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_ski.code','SKI')
	// 	->orderBy('hr_electronic_letter.id_letter','DESC')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_edit_ski($id_letter)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_ski','data_ski.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	// CHIEF
	// 	->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('chief_name.name as name_chief'),
	// 		\DB::RAW('master_position_routing.description as position_chief')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_ski.code','SKI')
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->get();
	// 	return $data;
	// }
	// public static function get_print_ski($token)
	// {
	// 	$data = DB::table('hr_electronic_letter')
	// 	->join('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->join('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('master_general_data as data_ski','data_ski.id_general_data','=','master_general_data.relation_to_id_general_data')
	// 	// CHIEF
	// 	->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('master_company.company_name as company_name'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('chief_name.name as name_chief'),
	// 		\DB::RAW('master_position_routing.description as position_chief')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('data_ski.code','SKI')
	// 	->where('hr_electronic_letter.token',$token)
	// 	->get();
	// 	return $data;
	// }

	// Other Letter
	// public static function change_employee_other($request)
	// {
	// 	$data = DB::table('hr_employee')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_employee.id_employment_status')
	// 	->leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
	// 	->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
	// 	->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
	// 	// ->leftJoin('master_location','master_location.id_branch','=','master_branch.id_branch')
	// 	->leftJoin('master_company','hr_employee.id_company','=','master_company.id_company')
	// 	->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','master_position_routing.id_job_grade')
	// 	->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
	// 	->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
	// 	->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
	// 	->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
	// 	->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
	// 	->select(
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_position_detail.id_position_detail as id_position_detail'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_branch.id_branch as id_branch'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('master_job_grade.id_job_grade as id_job_grade'),
	// 		\DB::RAW('master_department.id_dept as id_dept'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_division.description as dec_principal'),
	// 		\DB::RAW('master_division.id_division as id_principal'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('master_region.id_region as id_region'),
	// 		\DB::RAW('status_employment.description as status'),
	// 		\DB::RAW('status_employment.id_general_data as id_employment_status')
	// 	)
	// 	->where('hr_employee.id_company',session('id_company'))
	// 	->where('hr_employee.id_employee',$request->employee_id)
	// 	->where('master_position_detail.secondary_position','false')
	// 	->get();
	// 	return $data;
	// }
	// public function get_other_letter($request)
	// {
	// 	$id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->select(
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_electronic_letter.status as status')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->whereIn('master_general_data.code',['MOU','PKS','PED','SOP','INK','SKD','SPID']);
	// 	if ($id_branch != NULL) {
	// 		$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
	// 	}
	// 	$data = $data->orderBy('hr_electronic_letter.id_letter', 'DESC')
	// 	->get();
	// 	return $data;
	// }
	// public static function get_edit_otherletter($id_letter)
	// {
	// 	// $id_branch = self::accessBranch($request);
	// 	$data = DB::table('hr_electronic_letter')
	// 	->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
	// 	->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
	// 	->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
	// 	->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
	// 	->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
	// 	->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
	// 	->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
	// 	->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
	// 	->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
	// 	->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
	// 	->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
	// 	->leftJoin('master_position_routing as chief_position','chief_position.id_routing','=','hr_electronic_letter.id_position_routing_chief')
	// 	->select(
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.id_category as id_category'),
	// 		\DB::RAW('hr_electronic_letter.date as date'),
	// 		\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
	// 		\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
	// 		\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
	// 		\DB::RAW('hr_electronic_letter.notes as notes'),
	// 		\DB::RAW('hr_electronic_letter.email as email'),
	// 		\DB::RAW('hr_employee.name as name'),
	// 		\DB::RAW('hr_employee.nik_employee as nik_employee'),
	// 		\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
	// 		\DB::RAW('master_department.description as dec_dept'),
	// 		\DB::RAW('master_region.description as dec_region'),
	// 		\DB::RAW('master_branch.description as dec_branch'),
	// 		\DB::RAW('master_position_routing.description as dec_position'),
	// 		\DB::RAW('master_job_grade.description as dec_job_grade'),
	// 		\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
	// 		\DB::RAW('status_employment.description as status'),
	// 		\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
	// 		\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
	// 		\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
	// 		\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
	// 		// 
	// 		\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
	// 		\DB::RAW('master_general_data.description as category'),
	// 		\DB::RAW('chief_name.name as nama_menyetujui'),
	// 		\DB::RAW('chief_position.description as jabatan_menyetujui')
	// 	)
	// 	->where('hr_electronic_letter.id_company',session('id_company'))
	// 	->where('hr_electronic_letter.id_letter',$id_letter)
	// 	->whereIn('master_general_data.code',['MOU','PKS','PED','SOP','INK','SKD','SPID']);
	// 	// if ($id_branch != NULL) {
	// 	// 	$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
	// 	// }
	// 	$data = $data->get();
	// 	return $data;
	// }
}
