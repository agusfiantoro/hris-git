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

class OtherLetter extends Model
{
    // use HasFactory;
	public static function change_employee_other($request)
	{
		$data = DB::table('hr_employee')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_employee.id_employment_status')
		->leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->leftJoin('master_branch','master_branch.id_branch','=','master_position_detail.id_branch')
		->leftJoin('master_region','master_region.id_region','=','master_branch.id_region')
		// ->leftJoin('master_location','master_location.id_branch','=','master_branch.id_branch')
		->leftJoin('master_company','hr_employee.id_company','=','master_company.id_company')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','master_position_routing.id_job_grade')
		->leftJoin('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
		->leftJoin('master_department','master_department.id_dept','=','master_job_position.id_dept')
		->leftJoin('relation_positiondetail_principal', 'relation_positiondetail_principal.id_position_detail', '=', 'master_position_detail.id_position_detail')
		->leftJoin('master_principal', 'master_principal.id_principal', '=', 'relation_positiondetail_principal.id_principal')
		->leftJoin('master_division', 'master_division.id_division', '=', 'master_principal.id_division')
		->select(
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_position_detail.id_position_detail as id_position_detail'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_branch.id_branch as id_branch'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			\DB::RAW('master_job_grade.id_job_grade as id_job_grade'),
			\DB::RAW('master_department.id_dept as id_dept'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_division.description as dec_principal'),
			\DB::RAW('master_division.id_division as id_principal'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('master_region.id_region as id_region'),
			\DB::RAW('status_employment.description as status'),
			\DB::RAW('status_employment.id_general_data as id_employment_status')
		)
		->where('hr_employee.id_company',session('id_company'))
		->where('hr_employee.id_employee',$request->employee_id)
		->where('master_position_detail.secondary_position','false')
		->get();
		return $data;
	}
	public function get_other_letter($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_general_data as data_other','data_other.id_general_data','=','master_general_data.relation_to_id_general_data')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->select(
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_electronic_letter.status as status')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('data_other.code','OTH');
		// ->whereIn('master_general_data.code',['MOU','PKS','PED','SOP','INK','SKD','SPID']);
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter', 'DESC')
		->get();
		return $data;
	}
	public static function get_edit_otherletter($id_letter)
	{
		// $id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')
		->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		->leftJoin('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
		->leftJoin('master_general_data as status_employment','status_employment.id_general_data','=','hr_electronic_letter.id_employment_status')
		->leftJoin('hr_employee as chief_name','chief_name.id_employee','=','hr_electronic_letter.id_employee_chief')
		->leftJoin('master_position_routing as chief_position','chief_position.id_routing','=','hr_electronic_letter.id_position_routing_chief')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.notes as notes'),
			\DB::RAW('hr_electronic_letter.email as email'),
			\DB::RAW('hr_employee.name as name'),
			\DB::RAW('hr_employee.nik_employee as nik_employee'),
			\DB::RAW('hr_electronic_letter.id_employee_chief as id_employee_chief'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_region.description as dec_region'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('master_position_routing.description as dec_position'),
			\DB::RAW('master_job_grade.description as dec_job_grade'),
			\DB::RAW('hr_electronic_letter.id_principal as id_principal'),
			\DB::RAW('status_employment.description as status'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.remark_2 as remark_2'),
			\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
			\DB::RAW('hr_electronic_letter.remark_4 as remark_4'),
			\DB::RAW('hr_electronic_letter.remark_5 as remark_5'),
			// 
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('master_general_data.description as category'),
			\DB::RAW('chief_name.name as nama_menyetujui'),
			\DB::RAW('chief_position.description as jabatan_menyetujui')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('hr_electronic_letter.id_letter',$id_letter);
		// ->whereIn('master_general_data.code',['MOU','PKS','PED','SOP','INK','SKD','SPID']);
		// if ($id_branch != NULL) {
		// 	$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
		// }
		$data = $data->get();
		return $data;
	}
	public static function get_category()
	{
		$category = MasterGeneralData::join('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.id_general_data as id_general_data'),
			\DB::RAW('data_category.code as code'),
			\DB::RAW('data_category.description as description')
		)
		->where('master_general_data.code','OTH')
		->where('data_category.id_company',session('id_company'))
		// ->where('data_category.restrict_by','User')
		->where('data_category.status','A')
		// ->whereIn('code',['MOU','PKS','PED','SOP','INK','SKD','SPID'])
		->orderBy('data_category.description','ASC')
		->get();
		return $category;
	}
	public static function get_employee($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$employee = Employee::leftJoin('master_position_detail','master_position_detail.id_employee','=','hr_employee.id_employee')
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
		// 		->leftJoin('master_general_data as data_other','data_other.id_general_data','=','data_category.relation_to_id_general_data')
		// 		->whereColumn('hr_electronic_letter.id_employee', '=', 'hr_employee.id_employee')
		// 		->where('data_other.code','OTH');
		// 		// ->whereIn('data_category.code',['MOU','PKS','PED','SOP','INK','SKD','SPID']);
		// 	})->orWhereNull('hr_employee.id_employee');
		// });
		if ($id_branch != NULL) {
			$employee->whereIn('master_position_detail.id_branch',explode(',', $id_branch));
		}
		$employee = $employee->orderBy('hr_employee.name','ASC')->get();
		return $employee;
	}
	
	public static function get_employee_chief() {
        $sql = "SELECT DISTINCT he.id_employee, he.name
				FROM master_position_detail mpd
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee
				WHERE mpd.id_company = ? AND he.status = 'A'
				ORDER BY he.name ASC";
        $employee_chief = DB::select($sql,[session('id_company')]);
        return $employee_chief;
    }
	
	public static function get_dept()
	{
		$department = DB::table('master_department')
		->select(\DB::RAW('id_dept'),\DB::RAW('description'))
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $department;
	}
	public static function get_region()
	{
		$region = MasterRegional::select(
			\DB::RAW('id_region'),
			\DB::RAW('description')
		)
		->where('id_company',session('id_company'))
		->orderBy('description','ASC')
		->get();
		return $region;
	}
	public static function get_branch(Request $request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$branch = MasterBranch::select(
			\DB::RAW('id_branch'),
			\DB::RAW('description')
		)
		->where('id_region',$request->region_id)
		->where('id_company',session('id_company'));
		if ($id_branch != NULL) {
			$branch->whereIn('id_branch',explode(',', $id_branch));
		}
		$branch = $branch->orderBy('description','ASC')->get();
		return $branch;
	}
	public static function format_save($request)
	{
		if (!empty($request->remark_4) AND !empty($request->remark_3)) {
			$request->validate([
				'id_employee' => 'required',
				'id_category' => 'required',
				'date' => 'required',
				'id_region' => 'required',
				'id_branch' => 'required',
				'id_position_detail' => 'required',
				'id_job_grade' => 'required',
				'id_principal' => 'required',
				'id_employment_status' => 'required'
			],[
				'id_employee.required' => 'The Name field is required.',
				'id_category.required' => 'The Category field is required.',
				'date.required' => 'The Letter Date field is required.',
				'id_region.required' => 'The Region field is required.',
				'id_branch.required' => 'The Branch field is required.',
				'id_position_detail.required' => 'The Position field is required.',
				'id_job_grade.required' => 'The Job Grade field is required.',
				'id_principal.required' => 'The Principal field is required.',
				'id_employment_status.required' => 'The Status field is required.'
			]);
			$department_code = "HRD";
		}
		elseif(!empty($request->remark_4) AND empty($request->remark_3)){
			$request->validate([
				'id_category' => 'required',
				'date' => 'required',
				'id_region' => 'required',
				'id_branch' => 'required'
			],[
				'id_category.required' => 'The Category field is required.',
				'date.required' => 'The Letter Date field is required.',
				'id_region.required' => 'The Region field is required.',
				'id_branch.required' => 'The Branch field is required.'
			]);
			$department_code = "HRD";
		}
		elseif (empty($request->remark_4) AND !empty($request->remark_3)) {
			$request->validate([
				'id_employee' => 'required',
				'id_category' => 'required',
				'date' => 'required',
				'id_region' => 'required',
				'id_branch' => 'required',
				'id_dept' => 'required',
				'id_position_detail' => 'required',
				'id_job_grade' => 'required',
				'id_principal' => 'required',
				'id_employment_status' => 'required'
			],[
				'id_employee.required' => 'The Name field is required.',
				'id_category.required' => 'The Category field is required.',
				'date.required' => 'The Letter Date field is required.',
				'id_region.required' => 'The Region field is required.',
				'id_branch.required' => 'The Branch field is required.',
				'id_dept.required' => 'The Department field is required.',
				'id_position_detail.required' => 'The Position field is required.',
				'id_job_grade.required' => 'The Job Grade field is required.',
				'id_principal.required' => 'The Principal field is required.',
				'id_employment_status.required' => 'The Status field is required.'
			]);
			$department = DB::table('master_department')
			->select(
				\DB::RAW('department_code')
			)
			->where('id_company',session('id_company'))
			->where('id_dept',$request->id_dept)
			->first();
			$department_code = explode("_", $department->department_code)[1];
		}
		elseif(empty($request->remark_3) AND empty($request->remark_4)){
			$request->validate([
				'id_category' => 'required',
				'date' => 'required',
				'id_region' => 'required',
				'id_branch' => 'required',
				'id_dept' => 'required'
			],[
				'id_category.required' => 'The Category field is required.',
				'date.required' => 'The Letter Date field is required.',
				'id_region.required' => 'The Region field is required.',
				'id_branch.required' => 'The Branch field is required.',
				'id_dept.required' => 'The Department field is required.'
			]);
			$department = DB::table('master_department')
			->select(
				\DB::RAW('department_code')
			)
			->where('id_company',session('id_company'))
			->where('id_dept',$request->id_dept)
			->first();
			$department_code = explode("_", $department->department_code)[1];
		}
		if (!empty($request->id_employee)) {
			$employee = Employee::where('id_employee',$request->id_employee)->first();
			$employee_name = $employee->name;
		}else{
			$employee_name = '-';
		}
		$token = md5($employee_name).strtotime('now');
		$id_letter_type =  MasterGeneralData::join('master_general_data as data_other','data_other.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_general_data.id_general_data as id_general_data'),
			\DB::RAW('master_general_data.code as code')
		)
		->where('master_general_data.id_company',session('id_company'))
		->where('master_general_data.id_general_data',$request->id_category)
		// ->where('data_other.code','OTH')
		// ->whereIn('master_general_data.code',['MOU','PKS','PED','SOP','INK','SKD','SPID'])
		->first();
		// dd($id_letter_type->id_general_data);
		$company = DB::table('master_company')->where('id_company',session('id_company'))->first();
		$bulan = date('m', strtotime($request->date));
		$tahun = date('Y', strtotime($request->date));
		// dd($request->id_dept);
		$id_letter = ElectronicLetter::join('master_company','hr_electronic_letter.id_company','=','master_company.id_company')
		->whereMonth('hr_electronic_letter.date',$bulan)
		->whereYear('hr_electronic_letter.date',$tahun)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_company.company_code',$company->company_code)
		->where('hr_electronic_letter.id_category',$request->id_category)
		// ->where('hr_electronic_letter.remark_5',$request->remark_5)
		->where('hr_electronic_letter.id_letter_type',$id_letter_type->id_general_data);
		if (!empty($request->remark_4)) {
			$id_letter->where('hr_electronic_letter.id_region',$request->id_region);
		}
		elseif (!empty($request->remark_5)) {
			$id_letter->where('hr_electronic_letter.id_region',$request->id_region)
			->where('hr_electronic_letter.id_dept',$request->id_dept);
		}
		$id_letter = $id_letter->max('hr_electronic_letter.reference_number');
		$no_urut = substr($id_letter, 0,4);
		$no_urut++;
		$kode = sprintf("%04s", abs($no_urut));
		$region = MasterRegional::where('id_region',$request->id_region)->first();
		if ($region->description == "Pusat") {
			$code_region = "HQ";
		}else{
			$code_region = $region->region_code;
		}
		$category = MasterGeneralData::where('id_general_data',$request->id_category)
		->where('id_company',session('id_company'))
		->first();
		if (!empty($request->id_principal)) {
			$principalTextValue = "";
			foreach ($request->id_principal as $key => $value) {
				$principalTextValue .= $value . ",";
			}
			$principalTextValue = rtrim($principalTextValue, ',');
		}else{
			$principalTextValue = null;
		}
		if (!empty($request->remark_5)) {
			$format = $kode."/".$company->company_code."-".$category->code."/".$department_code."-".$code_region."/".$bulan."/".substr($tahun,-2);
		}else{
			$format = $kode."/".$company->company_code."-".$category->code."/".$bulan."/".substr($tahun,-2);
		}
		return ['format'=>$format,'id_letter_type'=>$id_letter_type,'principalTextValue'=>$principalTextValue,'token'=>$token];
	}
}
