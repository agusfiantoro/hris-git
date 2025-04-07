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

class InternalAgreement extends Model
{
    // use HasFactory;
	public function get_category()
	{
		$data = MasterGeneralData::join('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.id_general_data as id_general_data'),
			\DB::RAW('data_category.description as description')
		)
		->where('data_category.id_company',session('id_company'))
		->where('data_category.status','A')
		->where('master_general_data.code','INT')
		->orderBy('data_category.description','ASC')
		->get();
		return $data;
	}
	public static function get_employee_pi($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		if ($request->status == "Active") {
			$data = DB::table('hr_employee')
			->leftJoin('master_position_detail as mpd_hrc','mpd_hrc.id_employee','=','hr_employee.id_employee')
			->leftJoin('master_position_routing','master_position_routing.id_routing','=','mpd_hrc.id_position_routing')
			->select(
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('hr_employee.status as status'),
				\DB::RAW('master_position_routing.description as dec_position'),
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date')
			)
			->where('hr_employee.id_company',session('id_company'))
			->where('hr_employee.status','A')
			->where('mpd_hrc.secondary_position','false');
		}else{
			$data = Employee::leftJoin('hr_career_transaction as hr_career_transaction','hr_career_transaction.id_employee','=','hr_employee.id_employee')
			->leftJoin('master_general_data as status_terminate','status_terminate.id_general_data','=','hr_career_transaction.id_transaction_type')
			->leftJoin('master_general_data as status_approved','status_approved.id_general_data','=','hr_career_transaction.id_approval_status')
			->leftJoin('master_position_detail as mpd_hrc','mpd_hrc.id_position_detail','=','hr_career_transaction.id_old_position_detail')
			->leftJoin('master_position_routing','master_position_routing.id_routing','=','mpd_hrc.id_position_routing')
			->select(
				\DB::RAW('hr_employee.status as status'),
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('master_position_routing.description as dec_position'),
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date')
			)
			->where('status_terminate.code','Termination')
			->where('status_approved.code','Approved')
			->where('hr_employee.id_company',session('id_company'))
			->where('mpd_hrc.secondary_position','false');
		}
		if ($id_branch != NULL) {
			$data->whereIn('mpd_hrc.id_branch',explode(',', $id_branch));
		}
		// $data->where(function ($query) {
		// 	$query->whereNotExists(function ($subquery) {
		// 		$subquery->select(\DB::RAW(1))
		// 		->from('hr_electronic_letter')
		// 		->leftJoin('master_general_data as data_category', 'data_category.id_general_data', '=', 'hr_electronic_letter.id_category')
		// 		->leftJoin('master_general_data as data_pi','data_pi.id_general_data','=','data_category.relation_to_id_general_data')
		// 		->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_employee.id_employee')
		// 		->where('data_pi.code','INT');
		// 	})->orWhereNull('hr_employee.id_employee');
		// });
		$data = $data->orderBy('hr_employee.name','ASC')->get();
		return $data;
	}
	public static function change_employee_pi($request)
	{
		if ($request->employee_status == "A") {
			$data = Employee::leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
			->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
			->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
			->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
			->leftJoin('master_location','master_location.id_location','=','master_position_detail.id_location')
			->leftJoin('master_company','hr_employee.id_company','=','master_company.id_company')
			->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','master_position_routing.id_job_grade')
			->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
			->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
			->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
			->select(
				\DB::RAW('master_position_routing.description as dec_position'),
				\DB::RAW('master_position_detail.id_position_routing as id_routing'),
				\DB::RAW('master_branch.description as dec_branch'),
				\DB::RAW('master_branch.id_branch as id_branch'),
				\DB::RAW('master_region.id_region as id_region'),
				\DB::RAW('master_region.description as dec_region'),
				\DB::RAW('master_job_grade.description as dec_job_grade'),
				\DB::RAW('master_job_grade.id_job_grade as id_job_grade'),
				\DB::RAW('master_department.id_dept as id_dept'),
				\DB::RAW('master_department.description as dec_dept'),
				\DB::RAW('master_location.description as dec_location'),
				\DB::RAW('master_location.id_location as id_location'),
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date'),
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.id_employee as id_employee')
			)
			->where('hr_employee.id_company',session('id_company'))
			->where('hr_employee.id_employee',$request->employee_id)
			->where('master_position_detail.secondary_position','false')
			->get();
		}else{
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
			->leftJoin('master_job_grade as job_grade_old','job_grade_old.id_job_grade','=','position_routing_old.id_job_grade')
			->leftJoin('master_job_position as job_position_old','job_position_old.id_position','=','position_routing_old.id_position')
			->leftJoin('master_department as department_old','department_old.id_dept','=','job_position_old.id_dept')
			->leftJoin('master_location as location_old','location_old.id_location','=','position_detail_old.id_location')
			->leftJoin('master_company','master_company.id_company','=','hr_career_transaction.id_company')
			->select(
				\DB::RAW('hr_employee.nik_employee as nik_employee'),
				\DB::RAW('hr_employee.name as name'),
				\DB::RAW('hr_employee.id_employee as id_employee'),
				\DB::RAW('hr_employee.join_date as join_date'),
				\DB::RAW('hr_employee.resign_date as resign_date'),
				\DB::RAW('region_old.description as dec_region'),
				\DB::RAW('region_old.id_region as id_region'),
				\DB::RAW('job_grade_old.description as dec_job_grade'),
				\DB::RAW('job_grade_old.id_job_grade as id_job_grade'),
				\DB::RAW('department_old.description as dec_dept'),
				\DB::RAW('department_old.id_dept as id_dept'),
				\DB::RAW('branch_old.description as dec_branch'),
				\DB::RAW('branch_old.id_branch as id_branch'),
				\DB::RAW('position_routing_old.description as dec_position'),
				\DB::RAW('position_detail_old.id_position_routing as id_routing'),
				\DB::RAW('location_old.description as dec_location'),
				\DB::RAW('location_old.id_location as id_location')
			)
			->where('status_terminate.code','Termination')
			->where('status_approved.code','Approved')
			->where('hr_career_transaction.id_employee',$request->employee_id)
			->where('hr_career_transaction.id_company',session('id_company'))
			->where('position_detail_old.secondary_position','false')
			->get();
		}
		return $data;
	}
	public static function format_save($request)
	{
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.code as code'),
			\DB::RAW('master_general_data.id_general_data as id_general_data')
		)
		->where('master_general_data.code','INT')
		->where('master_general_data.id_company',session('id_company'))
		->first();
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
		$region = MasterRegional::where('id_region',$request->id_region)->first();
		if ($region->description == "Pusat") {
			$code_region = "HQ";
		}else{
			$code_region = $region->region_code;
		}
		$format = $kode."/".$company->company_code."-".$category->code."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
		return [
			'format'=>$format,
			'id_letter_type'=>$id_letter_type
		];
	}
	public static function get_pi($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		// 
		->leftJoin('master_general_data as data_pi','data_pi.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_department.description as dc_dept'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			\DB::RAW('master_location.description as dec_location'),
			\DB::RAW('master_region.description as dc_region'),
			\DB::RAW('master_general_data.code as category'),
			\DB::RAW('master_general_data.description as dec_category'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dc_position'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('master_company.company_code as company_cd'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_pi.code','INT');
		// ->where('master_position_detail.secondary_position','false');
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter', 'DESC')
		->get();
		return $data;
	}
	public static function get_edit_pi($id_letter)
	{
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data as data_pi','data_pi.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.id_employee as id_employee'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_location.description as dec_location'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_job_grade.description as dec_job_grade')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_pi.code','INT')
		// ->where('master_position_detail.secondary_position','false')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
}
