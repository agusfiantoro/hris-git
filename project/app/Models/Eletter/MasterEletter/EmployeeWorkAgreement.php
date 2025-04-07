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
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Models\Organization\MasterOrganization\MasterLocation;

class EmployeeWorkAgreement extends Model
{
	public static function get_category()
	{
		$data = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.description as description'),
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.id_general_data as id_general_data')
		)
		->where('master_general_data.code','PKK')
		->where('data_category.status','A')
		->where('data_category.id_company',session('id_company'))
		->orderBy('data_category.description','ASC')
		->get();
		return $data;
	}
	public static function get_position()
	{
		// $id_branch = ElectronicLetter::accessBranch($request);
		// if ($id_branch != NULL) {
		// 	$data = DB::table('master_position_routing')
		// 	->join('master_position_detail','master_position_detail.id_position_routing','=','master_position_routing.id_routing')
		// 	->join('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		// 	->select(
		// 		\DB::RAW('master_position_routing.id_routing as id_routing'),
		// 		\DB::RAW('master_position_routing.description as description'),
		// 		\DB::RAW('master_position_detail.id_branch as id_branch')
		// 	)
		// 	->where('master_position_routing.id_company',session('id_company'))
		// 	->where('master_position_routing.status','A')
		// 	->whereIn('master_position_detail.id_branch',explode(',', $id_branch))
		// 	->distinct()
		// 	->get();
		// }else{
		$data = DB::table('master_position_routing')
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
		// }
		return $data;
	}
	public static function get_employee($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_career_transaction as hr_cr')
		->join('master_general_data as mgd_category', 'mgd_category.id_general_data', '=', 'hr_cr.id_transition_category')
		->leftJoin('master_general_data as mgd_approved', 'mgd_approved.id_general_data', '=', 'hr_cr.id_approval_status')
		->join('hr_employee as hr_emp', 'hr_emp.id_employee', '=', 'hr_cr.id_employee')
		->join('master_position_detail as mpd',function ($join) {
			$join->on('mpd.id_position_detail','=','hr_cr.id_old_position_detail');
			$join->orOn('mpd.id_position_detail', '=','hr_cr.id_position_detail');
		})
		->join(DB::raw('(
			SELECT identification_number, MAX(id_employee) AS id_em1
			FROM hr_employee
			GROUP BY identification_number
		) as e1'), function ($join) {
			$join->on('hr_emp.id_employee', '=', DB::raw('e1.id_em1'));
		})
		->where(function ($query) {
				$query->where('mgd_category.code', 'Termination');
				$query->orWhere('mgd_category.code', 'Join');
			})
		->where('mgd_approved.code', 'Approved')
		->where('hr_cr.id_company', session('id_company'))
		->where('mpd.secondary_position', 'false')
	/*	->where(function ($query) {
			$query->whereNotExists(function ($subquery) {
				$subquery->select(\DB::RAW(1))
				->from('hr_electronic_letter')
				->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
				->leftJoin('master_general_data as data_pkk','data_pkk.id_general_data','=','data_category.relation_to_id_general_data')
				->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_emp.id_employee')
				->where('data_pkk.code','PKK');
			})->orWhereNull('hr_emp.id_employee');
		})
	*/
		->groupBy('hr_emp.id_employee', 'hr_emp.name', 'hr_emp.nik_employee')
		->select(
			\DB::RAW('hr_emp.id_employee as id_employee'),
			\DB::RAW('hr_emp.name as name'),
			\DB::RAW('hr_emp.nik_employee as nik_employee'),
			\DB::RAW('MAX(hr_cr.id_career_transaction) as id_career_transaction')
		);
		if ($id_branch != NULL) {
			$data->whereIn('mpd.id_branch',explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_emp.name','ASC')->get();
		return $data;
	}
	public static function getpkk($request)
	{
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
			\DB::RAW('master_department.description as dc_dept'),
			\DB::RAW('master_region.description as dc_region'),
			\DB::RAW('master_general_data.code as category'),
			\DB::RAW('master_general_data.description as dec_category'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dc_position'),
			\DB::RAW('master_company.company_code as company_cd'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_1, hr_employee.name) as remark_1'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_pkk.code','PKK');
		// ->where('master_position_detail.secondary_position','false');
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter', 'DESC')
		->get();
		return $data;
	}
	public static function change_employee_pkwt($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = Employee::leftJoin('hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_general_data as career_category','career_category.id_general_data','=','hr_career_transaction.id_transition_category')
		->leftJoin('master_general_data as career_approved','career_approved.id_general_data','=','hr_career_transaction.id_approval_status')
		->leftjoin('master_position_detail as position_detail_old',function ($join) {
			$join->on('position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail');
			$join->orOn('position_detail_old.id_position_detail', '=','hr_career_transaction.id_position_detail');
		})
	//	->leftJoin('master_position_detail as position_detail_old','position_detail_old.id_position_detail','=','hr_career_transaction.id_old_position_detail')
		->leftJoin('master_branch as branch_old','branch_old.id_branch','=','position_detail_old.id_branch')
		->leftJoin('master_position_routing as position_routing_old','position_routing_old.id_routing','=','position_detail_old.id_position_routing')
		->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
		->leftJoin('master_region as region_old','region_old.id_region','=','branch_old.id_region')
		->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
		->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
		->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
		->select(
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.id_employee as id_employee'),
			\DB::RAW('region_old.description as dec_region'),
			\DB::RAW('region_old.id_region as id_region'),
			\DB::RAW('branch_old.description as dec_branch'),
			\DB::RAW('branch_old.id_branch as id_branch'),
			\DB::RAW('position_detail_old.id_position_routing as id_position_routing'),
			\DB::RAW('location_old.description as dec_location'),
			\DB::RAW('location_old.id_location as id_location')
		)
		->where('position_detail_old.secondary_position','false')
		->where('career_approved.code','Approved')
		->where(function ($query) {
				$query->where('career_category.code', 'Termination');
				$query->orWhere('career_category.code', 'Join');
			})
		->where('hr_career_transaction.id_employee',$request->id_employee)
		->where('hr_employee.id_company',session('id_company'))
		->orderBy('hr_employee.name','ASC')
		->get();
		// ->get();
		return $data;
	}
	public static function get_edit_pkk($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		// ->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		// 
		->leftJoin('master_general_data as data_pkk','data_pkk.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
			\DB::RAW('COALESCE(hr_electronic_letter.remark_1, hr_employee.name) as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.id_job_grade as id_job_grade'),
			\DB::RAW('hr_electronic_letter.id_region as id_region'),
			\DB::RAW('hr_electronic_letter.id_branch as id_branch'),
			\DB::RAW('hr_electronic_letter.id_position_detail as id_position_detail'),
			\DB::RAW('hr_electronic_letter.id_location as id_location'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_location.description as dec_location'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_job_grade.description as dec_job_grade')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_pkk.code','PKK')
		// ->where('master_position_detail.secondary_position','false')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
	public static function company_save()
	{
		$company = DB::table('master_company')->where('id_company',session('id_company'))->first();
		return $company;
	}
	public static function id_letter_type_save()
	{
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.code','PKK')
		->where('master_general_data.id_company',session('id_company'))
		->first();
		return $id_letter_type;
	}
	public static function format_surat($request)
	{
		$company = self::company_save();
		$bulan = date('m', strtotime($request->date));
		$tahun = date('Y', strtotime($request->date));
		$category = MasterGeneralData::where('id_general_data',$request->id_category)
		->where('id_company',session('id_company'))
		->first();
		$id_letter = ElectronicLetter::leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('hr_electronic_letter.id_region',$request->id_region)
		->where('master_general_data.code',$category->code)
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
		$format = $kode."/".$company->company_code."-".$category->code."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
		if ($category->code != "PKWTT") {
			$request->validate([
				'expired_date' => 'required'
			]);
		}
		return $format;
	}
	
}
