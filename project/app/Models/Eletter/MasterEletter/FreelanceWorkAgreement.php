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

class FreelanceWorkAgreement extends Model
{
	public static function get_position()
	{
		$position = DB::table('master_position_routing')
		->select(
			\DB::RAW('id_routing'),
			\DB::RAW('description as description')
		)
		->where('id_company',session('id_company'))
		->where('status','A')
		->orderBy('description','ASC')
		->get();
		return $position;
	}
	public static function get_freelance($request)
	{
		$id_branch = ElectronicLetter::accessBranch($request);
		$data = DB::table('hr_electronic_letter')->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		// ->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// 
		->leftJoin('master_general_data as data_pkhl','data_pkhl.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('master_department.description as dc_dept'),
			\DB::RAW('master_region.description as dc_region'),
			\DB::RAW('master_general_data.description as dec_category'),
			\DB::RAW('master_company.company_name as cp_name'),
			// \DB::RAW('master_position_detail.description as dc_position'),
			\DB::RAW('master_position_routing.description as dc_position'),
			\DB::RAW('master_company.company_code as company_cd'),
			\DB::RAW('master_branch.description as dec_branch'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
			\DB::RAW('hr_electronic_letter.date as date'),
			\DB::RAW('hr_electronic_letter.reference_number as reference_number'),
			\DB::RAW('hr_electronic_letter.effective_date as effective_date'),
			\DB::RAW('hr_electronic_letter.expired_date as expired_date'),
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.notes as notes')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_general_data.code','PKHL');
		// ->where('master_position_detail.secondary_position','false');
		// ->orderBy('hr_electronic_letter.id_letter','DESC')
		// ->get();
		if ($id_branch != NULL) {
			$data->whereIn('hr_electronic_letter.id_branch', explode(',', $id_branch));
		}
		$data = $data->orderBy('hr_electronic_letter.id_letter','DESC')->get();
		return $data;
	}
	public static function get_edit_freelance($id_letter)
	{
		$data = DB::table('hr_electronic_letter')->leftJoin('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_letter_type')
		->leftJoin('master_department','master_department.id_dept','=','hr_electronic_letter.id_dept')
		->leftJoin('master_branch','master_branch.id_branch','=','hr_electronic_letter.id_branch')
		->leftJoin('master_region','master_region.id_region','=','hr_electronic_letter.id_region')
		// ->join('master_position_detail','master_position_detail.id_position_detail','=','hr_electronic_letter.id_position_detail')
		->leftJoin('master_position_routing','master_position_routing.id_routing','=','hr_electronic_letter.id_position_routing')
		->leftJoin('master_job_grade','master_job_grade.id_job_grade','=','hr_electronic_letter.id_job_grade')
		->leftJoin('master_location','master_location.id_location','=','hr_electronic_letter.id_location')
		->leftJoin('master_company','master_company.id_company','=','hr_electronic_letter.id_company')
		// 
		->leftJoin('master_general_data as data_pkhl','data_pkhl.id_general_data','=','master_general_data.relation_to_id_general_data')
		->select(
			\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
			\DB::RAW('hr_electronic_letter.id_category as id_category'),
			\DB::RAW('hr_electronic_letter.id_dept as id_dept'),
			\DB::RAW('hr_electronic_letter.remark_1 as remark_1'),
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
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('master_department.description as dec_dept'),
			\DB::RAW('master_job_grade.description as dec_job_grade')
		)
		->where('hr_electronic_letter.id_company',session('id_company'))
		->where('master_general_data.code','PKHL')
		// ->where('master_position_detail.secondary_position','false')
		->where('hr_electronic_letter.id_letter',$id_letter)
		->get();
		return $data;
	}
	public static function id_letter_type_save()
	{
		$id_letter_type = MasterGeneralData::leftJoin('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('master_general_data.id_general_data as id_general_data'),
			\DB::RAW('master_general_data.code as code')
		)
		->where('master_general_data.code','PKHL')
		->where('master_general_data.id_company',session('id_company'))
		->first();
		return $id_letter_type;
	}
	public static function format_save($request)
	{
		$id_letter_type = self::id_letter_type_save();
		$dateRange = $request->effective_date;
		list($start, $end) = explode(" - ", $dateRange);
		$tgl1 = strtotime($start); 
		$tgl2 = strtotime($end); 
		$jarak = $tgl2 - $tgl1;
		$hari = $jarak / 60 / 60 / 24;
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
		$category = MasterGeneralData::join('master_general_data as data_category','data_category.relation_to_id_general_data','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('data_category.id_general_data as id_category')
		)
		->where('master_general_data.code','PKHL')
		->where('data_category.code','PKHL')
		->where('master_general_data.status','A')
		->where('master_general_data.id_company',session('id_company'))
		->first();
		if (!empty($category)) {
			$id_category = $category->id_category;
		}else{
			$id_category = NULL;
		}
		$format = $kode."/".$company->company_code."-".$id_letter_type->code."/HRD-".$code_region."/".$bulan."/".substr($tahun,-2);
		return ['format'=>$format,'start'=>$start,'end'=>$end,'hari'=>$hari,'id_category'=>$id_category];
	}
}
