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

class TerminationLetter extends Model
{
	public static function change_career_skp($request)
	{
		if ($request->status_employee == 'I') {
			$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
			// Terminate
			->leftJoin('master_general_data as status_terminate','status_terminate.id_general_data','=','hr_career_transaction.id_transaction_type')
			// Approved
			->leftJoin('master_general_data as status_approved','status_approved.id_general_data','=','hr_career_transaction.id_approval_status')
			// 
			->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
			->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
			->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
			->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
			->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
			->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
			// 
			->leftJoin('relation_positiondetail_principal as relation_principal_old','relation_principal_old.id_position_detail','=','position_detail_old.id_position_detail')
			->leftJoin('master_principal as principal_old','principal_old.id_principal','=','relation_principal_old.id_principal')
			->leftJoin('master_division as division_old','division_old.id_division','=','principal_old.id_division')
			->leftJoin('master_company','master_company.id_company','=','hr_career_transaction.id_company')
			->select(
				\DB::RAW('hr_career_transaction.id_employee as id_employee'),
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('master_company.company_name as company_name'),
				\DB::RAW('region_old.description as dec_region'),
				\DB::RAW('region_old.id_region as id_region'),
				\DB::RAW('department_old.description as dec_dept'),
				\DB::RAW('department_old.id_dept as id_dept'),
				\DB::RAW('branch_old.description as dec_branch'),
				\DB::RAW('branch_old.id_branch as id_branch'),
				\DB::RAW('position_routing_old.description as dec_position_routing'),
				\DB::RAW('position_detail_old.id_position_detail as id_position_detail')
			)
			->where('status_terminate.code','Termination')
			->where('status_approved.code','Approved')
			->where('hr_career_transaction.id_employee',$request->id_employee)
			->where('hr_career_transaction.id_company',session('id_company'))
			->where('position_detail_old.secondary_position','false')
			->get();
		}elseif($request->status_employee == 'A'){
			$data = Employee::leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
			->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
			->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
			->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
			->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
			->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
			// 
			->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
			->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
			->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
			->leftJoin('master_company','master_company.id_company','=','hr_employee.id_company')
			->select(
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('master_company.company_name as company_name'),
				\DB::RAW('master_region.description as dec_region'),
				\DB::RAW('master_region.id_region as id_region'),
				\DB::RAW('master_department.description as dec_dept'),
				\DB::RAW('master_department.id_dept as id_dept'),
				\DB::RAW('master_branch.description as dec_branch'),
				\DB::RAW('master_branch.id_branch as id_branch'),
				\DB::RAW('master_position_routing.description as dec_position_routing'),
				\DB::RAW('master_position_detail.id_position_detail as id_position_detail')
			)
			->where('hr_employee.id_employee',$request->id_employee)
			->where('hr_employee.id_company',session('id_company'))
			->where('master_position_detail.secondary_position','false')
			->get();
		}
		return $data;
	}

	public static function get_skp($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_general_data as data_skp','data_skp.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('data_skp.code','SKP')
		->where('master_position_detail.secondary_position','false')
		->where('hr_electronic_letter.id_company',session('id_company'));
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')->get();
		// ->get();
		return $data;
	}
	public static function get_edit_skp($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_general_data as data_skp','data_skp.id_general_data','=','master_general_data.relation_to_id_general_data')
		// ->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
		// ->leftJoin('master_position_routing as chief_position','chief_position.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->select(
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('hr_electronic_letter.email as email'),
			// \DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('master_general_data.description as category'),
			// \DB::RAW('chief_name.name as name_chief'),
			// \DB::RAW('chief_position.description as position_chief'),
			\DB::RAW('hr_employee.status as status'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('data_skp.code','SKP')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_position_detail.secondary_position','false')
		->get();
		return $data;
	}
	public static function get_category()
	{
		$category = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.id_general_data as id_general_data'),
			\DB::RAW('data_category.description as description')
		)
		->where('data_category.id_company',session('id_company'))
		->where('master_general_data.code','SKP')
		->where('data_category.status','A')
		->orderBy('data_category.description','ASC')
		->get();
		return $category;
	}
	public static function get_employee($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$employee = DB::table('hr_employee AS he_all')
		->join(DB::raw('(SELECT identification_number, MAX(id_employee) AS id_em1 FROM hr_employee GROUP BY identification_number, status) AS e1'), function ($join) {
			$join->on('he_all.id_employee', '=', 'e1.id_em1');
		})
	//	->join('master_position_detail AS mpd', 'mpd.id_employee', '=', 'he_all.id_employee')
		->select(\DB::RAW('he_all.*'))
		->where('he_all.id_company', session('id_company'))
	//	->where('mpd.secondary_position', 'false')
		->where(function ($query) {
			$query->whereNotExists(function ($subquery) {
				$subquery->select(\DB::RAW(1))
				->from('hr_electronic_letter')
				->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
				->leftJoin('master_general_data as data_skp','data_skp.id_general_data','=','data_category.relation_to_id_general_data')
				->whereColumn('hr_electronic_letter.id_employee', '=', 'he_all.id_employee')
				->where('data_skp.code','SKP');
			})->orWhereNull('he_all.id_employee');
		});
		if ($id_branch != NULL) {
			$employee->join('master_position_detail AS mpd', 'mpd.id_employee', '=', 'he_all.id_employee');
			// $id_branch_ex = explode(',', $id_branch);
			// $sql_employee .= "AND mpd.id_branch IN ($id_branch)";
			$employee->whereIn('mpd.id_branch',explode(',', $id_branch));
			// $sql_employee = "SELECT *
			// FROM hr_employee AS he_all
			// JOIN (
			// SELECT identification_number, MAX(id_employee) AS id_em1
			// FROM hr_employee
			// GROUP BY identification_number
			// ) AS e1
			// ON he_all.id_employee = e1.id_em1
			// JOIN master_position_detail AS mpd
			// ON mpd.id_employee = he_all.id_employee
			// WHERE he_all.id_company='$session AND mpd.id_branch IN ($id_branch)'
			// ";
		}
		// else{
		// 	// $sql_employee = "SELECT *
		// 	// FROM hr_employee AS he_all
		// 	// JOIN (
		// 	// SELECT identification_number, MAX(id_employee) AS id_em1
		// 	// FROM hr_employee
		// 	// GROUP BY identification_number
		// 	// ) AS e1
		// 	// ON he_all.id_employee = e1.id_em1
		// 	// JOIN master_position_detail AS mpd
		// 	// ON mpd.id_employee = he_all.id_employee
		// 	// WHERE he_all.id_company='$session'
		// 	// ";
		// }
		$employee = $employee->orderBy('he_all.name','ASC')->get();
		return $employee;
	}
	public static function get_chief()
	{
		$chief = Employee::select(
			\DB::RAW('name'),
			\DB::RAW('id_employee')
		)
		->where('id_company',session('id_company'))
		->where('status','A')
		->orderBy('name','ASC')
		->get();
		return $chief;
	}
	public static function format_save($request)
	{
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.id_general_data as id_general_data'),
			\DB::RAW('master_general_data.code as code')
		)
		->where('master_general_data.id_company',session('id_company'))
		->where('master_general_data.code','SKP')
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
		return [
			'format'=>$format,
			'id_letter_type'=>$id_letter_type,
		];
	}
}
