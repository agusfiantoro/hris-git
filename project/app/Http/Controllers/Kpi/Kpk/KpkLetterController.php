<?php

namespace App\Http\Controllers\Kpi\Kpk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Employee\Employee\Employee;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\AwardDicipline\AwardDicipline;
use App\Models\Kpi\Kpk\HrPerformanceEvaluation;
use App\Models\Kpi\Kpk\HrPerformanceReview;
use App\Models\Kpi\Kpk\KpkLetter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Spipu\Html2Pdf\Html2Pdf;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\Crypt;

class KpkLetterController extends Controller
{
	protected function accessBranch(Request $request) {
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
	
	public function index(Request $request) {
        if ($request->ajax()) {
            $data = KpkLetter::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {	
								$idEncrypt = Crypt::encrypt($data->id_letter);
							//	$onclickPdf = "get_pdf(".$data->id_letter.")";
								$onclickPdf = "get_pdf('".$idEncrypt."')";
                                $buttonEdit = '&nbsp;<button type="button" name="edit" id="' . $data->id_letter . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> '; 
								$buttonView = '&nbsp;<button type="button" name="view" id="' . $data->id_letter . '" class="edit btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
								$buttonPdf= '&nbsp;<button type="button" target="_blank" name="print" onclick="'.$onclickPdf.'" class="print btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></button> ';
								if($data->draft_submit == 'Submit'){
									$returnButton = $buttonView.$buttonPdf;
								}
								else{
									$returnButton = $buttonView.$buttonEdit;
								}                           										
								return $returnButton;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('eletter.kpk_letter.index');
    }
	
	public function modal_detail(Request $request) {
		$global_kpk = $request->global_kpk;
        return view('eletter.kpk_letter.modal_detail', compact('global_kpk'));
    }
		
	public function index_monitoring(Request $request) {
        if ($request->ajax()) {
            $data = KpkLetter::getdata_monitoring($this->accessBranch($request));
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {								
                                $buttonEdit = '&nbsp;<button type="button" name="edit" id="' . $data->id_performance_evaluation . '" date="'.$data->effective_date.'" id_position_detail="'.$data->id_position_detail.'" id_employee="'.$data->id_employee.'" class="edit btn btn-warning btn-sm" title="Monitoring"><span class="fas fa-eye" style="color:white;"></span></button> '; 
								
                            	$returnButton = $buttonEdit;
									
								return $returnButton;
                            })
                            ->rawColumns(['kpi'])
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('eletter.kpk_letter.index_monitoring');
    }
	
	public function index_review(Request $request) {
        if ($request->ajax()) {
            $data = KpkLetter::getdata_review();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {								
                                $buttonEdit = '&nbsp;<button type="button" name="edit" id="' . $data->id_performance_evaluation . '" date="'.$data->effective_date.'" id_position_detail="'.$data->id_position_detail.'" id_employee="'.$data->id_employee.'" class="edit btn btn-success btn-sm" title="Review"><span class="fas fa-list-alt"></span></button> '; 
								
                            	$returnButton = $buttonEdit;
									
								return $returnButton;
                            })
						/*	
							->addColumn('kpi', function($data) {
                                $kpi = $data->kpi_average ?? $data->sales_offtake;
                                return $kpi;
                            })
                            ->rawColumns(['kpi'])
						*/
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('eletter.kpk_letter.index_review');
    }
	
	public function modal_review(Request $request) {
		$global_kpk = $request->global_kpk;
		$global_date = $request->global_date;
		$global_position = $request->global_position;
		$global_employee = $request->global_employee;
        return view('eletter.kpk_letter.modal_review', compact('global_kpk','global_date','global_position','global_employee'));
    }
	
	
	protected function validateReq(Request $request) {
	//	dd($request->all());
        $arr_form_validate = [
			'reference_number' => 'unique:hr_electronic_letter', Rule::unique('hr_electronic_letter')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'date' => 'required',
            'performance_date' => 'required',
            'effective_date' => 'required',
            'date_text' => 'required',
            'com_type' => 'required',
            'dept' => 'required',
            'kpk.*.id_employee' => 'required',
            'kpk.*.id_group' => 'required',
            'kpk.*.act_kpi' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'date.required' => 'The Letter Date field is required',
            'performance_date.required' => 'The Performance Date field is required',
            'effective_date.required' => 'The Effective Date field is required',
            'date_text.required' => 'The Date Text field is required',
            'com_type.required' => 'The Company Type field is required',
            'dept.required' => 'The Department field is required',
            'kpk.*.id_employee.required' => 'The Participant Name field is required',
            'kpk.*.id_group.required' => 'The Group field is required',
            'kpk.*.act_kpi.required' => 'The KPI field is required',
        ];
		if($request->dept_code == '170_SAL'){
			$arr_form_validate['division'] = 'required';			
			$arr_form_validate['kpk.*.act_idx'] = 'required';
			
			$arr_form_validate['kpk.*.month_1'] = 'required';
			$arr_form_validate['kpk.*.month_2'] = 'required';
			$arr_form_validate['kpk.*.month_3'] = 'required';
			$arr_form_validate['kpk.*.month_4'] = 'required';
			$arr_form_validate['kpk.*.month_5'] = 'required';
			 
			$arr_msg_form_validate['division.required'] = 'The Division field is required';
			$arr_msg_form_validate['kpk.*.act_idx.required'] = 'The IDX Sales field is required';
			
			$arr_msg_form_validate['kpk.*.month_1.required'] = 'The Month 1 field is required';
			$arr_msg_form_validate['kpk.*.month_2.required'] = 'The Month 2 field is required';
			$arr_msg_form_validate['kpk.*.month_3.required'] = 'The Month 3 field is required';
			$arr_msg_form_validate['kpk.*.month_4.required'] = 'The Month 4 field is required';
			$arr_msg_form_validate['kpk.*.month_5.required'] = 'The Month 5 field is required';
		}
        if ($request->post('kpk') == null) {
            $validate_emprequest = ['table_rec_detail' => 'required|string'];
            $validate_msg_emprequest = ['table_rec_detail.required' => 'Participant cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
		
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function validateReview(Request $request) {
		$arr_form_validate = [];
		$arr_msg_form_validate = [];

		$validate_review = [];
		$validate_msg_review = [];
		foreach($request->post('review') as $key=>$val){
			if($val['decision'] == 'HIT'){
				$arr_form_validate['pass_date'] = 'required';
				$arr_msg_form_validate['pass_date.required'] = 'The Pass Date field is required';
			}
			if($val['review_date'] != null || $val['decision'] != null){
				$validate_review = [
					'review.'.$key.'.review_date' => 'required',
					'review.'.$key.'.id_target' => 'required',
					'review.'.$key.'.act' => 'required',
					'review.'.$key.'.decision' => 'required',
					'review.'.$key.'.treatment' => 'required',
				];
				
				$validate_msg_review = [
					'review.'.$key.'.review_date.required' => 'The Review Date field is required',
					'review.'.$key.'.id_target.required' => 'The Target field is required',
					'review.'.$key.'.act.required' => 'The Actual field is required',
					'review.'.$key.'.decision.required' => 'The Decision field is required',
					'review.'.$key.'.treatment.required' => 'The Treatment field is required',
				];		
				$arr_form_validate = array_merge($arr_form_validate, $validate_review);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_review);
			}
		}  
	
        if ($request->post('review') == null) {
            $validate_emprequest = ['table_rec_detail' => 'required|string'];
            $validate_msg_emprequest = ['table_rec_detail.required' => 'Month Performance cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$result = KpkLetter::format_save($request);
			$format = $result['format'];
			$id_letter_type = $result['id_letter_type'];
			$data = New KpkLetter();
			$data -> reference_number = $format;
			$data -> date = $request->date;
			$data -> id_letter_type = $id_letter_type->id_general_data;
            $data -> id_category = DB::table('master_general_data')->where('id_company', session('id_company'))->where('code', 'P2K')->first()->id_general_data;
			$data -> effective_date = Carbon::parseFromLocale($request->effective_date, "id")->format("Y-m-d");
			$data -> remark_1 = $request->performance_date;
			$data -> remark_2 = $request->on_performance;
			$data -> remark_3 = $request->dept;
			$data -> remark_4 = $request->date_text;
			$data -> remark_6 = $request->com_type;
			$data -> remark_7 = $request->division;
			$data -> id_employee_chief = $request->id_employee_request;
			$data -> id_position_routing_chief = $request->id_routing;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_region = $request->id_region;
			$data -> status = $request->status;
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			if($request->form == 'save_and_submit'){
				$data -> remark_5 = 'submit';
			}
			else{
				$data -> remark_5 = 'draft';
			}
			$data -> save();
			foreach ($request->kpk as $key => $value) {
				$data_form = new HrPerformanceEvaluation();
				$data_form -> id_letter = $data->id_letter;
				$data_form -> id_employee = $value['id_employee'];
				$data_form -> id_position_detail = $value['id_pos_detail'];
				$data_form -> id_threshold_group = $value['id_group'];
				$data_form -> evaluation_date = $data->date;
				$data_form -> id_company = session('id_company');
				$data_form -> created_by = session('id_user');                        
                $data_form -> kpi_average = $value['act_kpi'];
				if($request->dept_code == '170_SAL'){
					$data_form -> index_sales_percentage = $value['act_idx'];
					$data_form -> sales_offtake = "{".str_replace(',','',$value['month_1']).",".str_replace(',','',$value['month_2']).",".str_replace(',','',$value['month_3']).",".str_replace(',','',$value['month_4']).",".str_replace(',','',$value['month_5'])."}";
				}
				$data_form -> save();
				
				$res[] = $value;
			}
			foreach($res as $k=>$res_val){
				$get_emp = Employee::where('id_employee',$res_val['id_employee'])->where('status','A')->first();
				$res[$k]['private_mail_emp'] = $get_emp->private_mail;
			}
			$all_direct = [];
			if($request->form == 'save_and_submit'){
				foreach($res as $key=>$val){
					$ori = KpkLetter::get_emp_ori($val['id_employee'],session('id_company'));				
					if(count($ori) > 0){
					$res[$key] = array_merge($res[$key],(array)$ori[0]);				
						$direct = KpkLetter::get_indirect($val['id_employee'],session('id_company'));
						$arr_direct = [];
						$ar_indirect = [];
						foreach($direct as $k=>$val_dir){
							if($val_dir->id_indirect != null){
								$name_direct = KpkLetter::get_name_mail($val_dir->id_indirect);
								$arr_direct = (array)$name_direct[0];
								$indirect = KpkLetter::get_indirect($val_dir->id_indirect,session('id_company'));
								foreach($indirect as $k=>$val_indir){
									if($val_indir->id_indirect != null){
										$name_indirect = KpkLetter::get_name_mail($val_indir->id_indirect);
										foreach($name_indirect as $k=>$val_in){
											$x[$k]['id_indirect'] = $val_in->id_direct;
											$x[$k]['name_indirect'] = $val_in->name_direct;
											$x[$k]['nik_indirect'] = $val_in->nik_direct;
											$x[$k]['private_mail_indirect'] = $val_in->private_mail;
											$x[$k]['mobile_phone_indirect'] = $val_in->mobile_phone;
										}
										$ar_indirect = $x[0];
									}									
								}
							}									
						}
					}					
					$all_direct[] = array_merge($res[$key],$arr_direct,$ar_indirect);	
				}
			}
			$collection = collect($all_direct);
			$listNik = $collection->pluck('id_direct')->unique();
			$arr_atasan = [];
			foreach($listNik as $nik) {
				$childKeys = ['id_employee','name_emp', 'nik_employee','obj_kpi','act_idx','act_kpi','pos_emp','id_company','private_mail_emp'];
				$parentKeys = ['id_direct','name_direct','nik_direct','private_mail','mobile_phone','id_indirect','name_indirect','nik_indirect','private_mail_indirect','mobile_phone_indirect'];
				$content = collect($collection->where('id_direct', $nik)->first())->except($childKeys)->only($parentKeys);
				$content['child'] = collect($collection->where('id_direct', $nik)->values())->map(function($child) use($childKeys) {
					return collect($child)->only($childKeys);
				});
				$arr_atasan[] = $content->toArray();
			}

			$param = array(
				'id_letter' => $data->id_letter,
			);
			$all_data = (array)KpkLetter::get_pdf_potrait($param);
			$all_data['send_mail'] = $arr_atasan;
		DB::commit();
			return response()->json(['status' => 'true', 'data'=>$all_data, 'message' => 'P2K Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Save P2K !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update(Request $request) {
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$data =  KpkLetter::where('id_letter',$request->id_letter)->first();;
			$data -> date = $request->date;
			$data -> effective_date = Carbon::parseFromLocale($request->effective_date, "id")->format("Y-m-d");
			$data -> remark_1 = $request->performance_date;
			$data -> remark_2 = $request->on_performance;
			$data -> remark_3 = $request->dept;
			$data -> remark_4 = $request->date_text;
			$data -> remark_6 = $request->com_type;
			$data -> remark_7 = $request->division;
			$data -> id_employee_chief = $request->id_employee_request;
			$data -> id_position_routing_chief = $request->id_routing;
			$data -> id_dept = $request->id_dept;
			$data -> id_branch = $request->id_branch;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_region = $request->id_region;
			$data -> status = $request->status;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			if($request->form == 'save_and_submit'){
				$data -> remark_5 = 'submit';
			}
			else{
				$data -> remark_5 = 'draft';
			}
			$data -> save();
			
			$listId    = [];
			$idDetail     = [];
			
			if(HrPerformanceEvaluation::where('id_letter', $request->id_letter)->first() != null){
				$listId = HrPerformanceEvaluation::where('id_letter', $request->id_letter)->where('id_company', session('id_company'))->get()->pluck('id_performance_evaluation')->all();
			}
			foreach ($request->kpk as $key => $value) {
				if ($value['id_performance_evaluation'] == "") {
					$detail = New HrPerformanceEvaluation();
					$detail -> id_letter = $request->id_letter;
					$detail -> id_employee = $value['id_employee'];
					$detail -> id_position_detail = $value['id_pos_detail'];
					$detail -> id_threshold_group = $value['id_group'];
					$detail -> evaluation_date = $request->date;
					$detail -> created_by = session('id_user');
					$detail -> id_company = session('id_company');
					$detail -> kpi_average = $value['act_kpi'];					
					if($request->dept_code == '170_SAL'){
						$detail -> index_sales_percentage = $value['act_idx'];
						$detail -> sales_offtake = "{".str_replace(',','',$value['month_1']).",".str_replace(',','',$value['month_2']).",".str_replace(',','',$value['month_3']).",".str_replace(',','',$value['month_4']).",".str_replace(',','',$value['month_5'])."}";
					}
					$detail -> save();
				}
				
				else {
					$idDetail[] = $value['id_performance_evaluation'];
					$detail = HrPerformanceEvaluation::where('id_performance_evaluation', $value['id_performance_evaluation'])->first();
					$detail -> id_employee = $value['id_employee'];
					$detail -> id_position_detail = $value['id_pos_detail'];
					$detail -> id_threshold_group = $value['id_group'];
					$detail -> evaluation_date = $request->date;
					$detail -> update_date = date('Y-m-d H:i:s');
					$detail -> updated_by = session('id_user');
					$detail -> kpi_average = $value['act_kpi'];
					if($request->dept_code == '170_SAL'){
						$detail -> index_sales_percentage = $value['act_idx'];
						$detail -> sales_offtake = "{".str_replace(',','',$value['month_1']).",".str_replace(',','',$value['month_2']).",".str_replace(',','',$value['month_3']).",".str_replace(',','',$value['month_4']).",".str_replace(',','',$value['month_5'])."}";
					}
					$detail -> save();
				}
				
				$res[] = $value;
			}
			
			$diff = array_diff($listId, $idDetail);
			if(count($diff) > 0){
				foreach ($diff as $key => $value) { 
					HrPerformanceEvaluation::where('id_performance_evaluation', $value)->delete();
				}
			}
			
			foreach($res as $k=>$res_val){
				$get_emp = Employee::where('id_employee',$res_val['id_employee'])->where('status','A')->first();
				$res[$k]['private_mail_emp'] = $get_emp->private_mail;
			}
			$all_direct = [];
			if($request->form == 'save_and_submit'){
				foreach($res as $key=>$val){
					$ori = KpkLetter::get_emp_ori($val['id_employee'],session('id_company'));				
					if(count($ori) > 0){
					$res[$key] = array_merge($res[$key],(array)$ori[0]);				
						$direct = KpkLetter::get_indirect($val['id_employee'],session('id_company'));
						$arr_direct = [];
						$ar_indirect = [];
						foreach($direct as $k=>$val_dir){
							if($val_dir->id_indirect != null){
								$name_direct = KpkLetter::get_name_mail($val_dir->id_indirect);
								$arr_direct = (array)$name_direct[0];
								$indirect = KpkLetter::get_indirect($val_dir->id_indirect,session('id_company'));
								foreach($indirect as $k=>$val_indir){
									if($val_indir->id_indirect != null){
										$name_indirect = KpkLetter::get_name_mail($val_indir->id_indirect);
										foreach($name_indirect as $k=>$val_in){
											$x[$k]['id_indirect'] = $val_in->id_direct;
											$x[$k]['name_indirect'] = $val_in->name_direct;
											$x[$k]['nik_indirect'] = $val_in->nik_direct;
											$x[$k]['private_mail_indirect'] = $val_in->private_mail;
											$x[$k]['mobile_phone_indirect'] = $val_in->mobile_phone;
										}
										$ar_indirect = $x[0];
									}									
								}
							}									
						}
					}					
					$all_direct[] = array_merge($res[$key],$arr_direct,$ar_indirect);	
				}
			}
			
			$collection = collect($all_direct);
			$listNik = $collection->pluck('id_direct')->unique();
			$arr_atasan = [];
			foreach($listNik as $nik) {
				$childKeys = ['id_employee','name_emp', 'nik_employee','obj_kpi','act_idx','act_kpi','pos_emp','id_company','private_mail_emp'];
				$parentKeys = ['id_direct','name_direct','nik_direct','private_mail','mobile_phone','id_indirect','name_indirect','nik_indirect','private_mail_indirect','mobile_phone_indirect'];
				$content = collect($collection->where('id_direct', $nik)->first())->except($childKeys)->only($parentKeys);
				$content['child'] = collect($collection->where('id_direct', $nik)->values())->map(function($child) use($childKeys) {
					return collect($child)->only($childKeys);
				});
				$arr_atasan[] = $content->toArray();
			}

			$param = array(
				'id_letter' => $data->id_letter,
			);
			$all_data = (array)KpkLetter::get_pdf_potrait($param);
			$all_data['send_mail'] = $arr_atasan;
		
		    DB::commit();
			return response()->json(['status' => 'true', 'data'=>$all_data, 'message' => 'P2K Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Update P2K !! [' . $e->getMessage() . ']']);           
		}	
	}
		
	protected function update_review(Request $request) {
		$this->validateReview($request);
		try{
			DB::beginTransaction();
			foreach($request->review as $k=>$val){
				$x_array[] = $val['treatment'];
			}
			if($request->pass_date != null){
				$update = HrPerformanceEvaluation::where('id_performance_evaluation', $request->id_performance_evaluation)->first();
				$update -> pass_date = $request->pass_date;
				if(in_array("phk", $x_array) || in_array("phkspdt", $x_array) || in_array("resign", $x_array) || in_array("demosi", $x_array)){
					$update -> evaluation_status = 'Failed';
				}
				else if(in_array("pass", $x_array)){
					$update -> evaluation_status = 'Pass';
				}			
				$update -> save();
			}
					
		/*	$listId    = [];
			$idDetail     = [];
			
			if(HrPerformanceReview::where('id_performance_evaluation', $request->id_performance_evaluation)->first() != null){
				$listId = HrPerformanceReview::where('id_performance_evaluation', $request->id_performance_evaluation)->where('id_company', session('id_company'))->get()->pluck('id_performance_review')->all();
			}
		*/
			$emp_direct = KpkLetter::get_emp_direct();
			$emp_indirect = KpkLetter::get_indirect($emp_direct[0]->id_employee,$emp_direct[0]->id_company);
			foreach ($request->review as $key => $value) {				
				if ($value['id_performance_review'] == "") {
					$detail = New HrPerformanceReview();
					$detail -> id_performance_evaluation = $request->id_performance_evaluation;
			//		$detail -> period_date = Carbon::parseFromLocale($value['period'], "id")->format("Y-m-d");
					$detail -> review_date = $value['review_date'];
					$detail -> target_volume = str_replace(',','',$value['id_target']);
					$detail -> result_value = str_replace(',','',$value['act']);
					$detail -> decision = $value['decision'];
					$detail -> treatment = $value['treatment'];
					$detail -> created_by = session('id_user');
					$detail -> id_company = session('id_company');
					$detail -> id_employee_reviewer = $emp_direct[0]->id_employee;
					$detail -> id_position_detail_reviewer = $emp_direct[0]->id_position_detail;
					$detail -> id_employee_acknowledge = $emp_indirect[0]->id_indirect;
					$detail -> id_position_detail_acknowledge = $emp_indirect[0]->id_pos_indirect;
					$detail -> save();
				}
				
				else {					
				//	$idDetail[] = $value['id_performance_review'];
					$detail = HrPerformanceReview::where('id_performance_review', $value['id_performance_review'])->first();
					if($value['review_date'] == null){
						continue;
					}
					if($detail->review_date == null){
					//	$detail -> period_date = Carbon::parseFromLocale($value['period'], "id")->format("Y-m-d");
						$detail -> review_date = $value['review_date'];
						$detail -> target_volume = str_replace(',','',$value['id_target']);
						$detail -> result_value = str_replace(',','',$value['act']);
						$detail -> decision = $value['decision'];
						$detail -> treatment = $value['treatment'];
						$detail -> updated_by = session('id_user');	
						$detail -> id_employee_reviewer = $emp_direct[0]->id_employee;
						$detail -> id_position_detail_reviewer = $emp_direct[0]->id_position_detail;
						$detail -> id_employee_acknowledge = $emp_indirect[0]->id_indirect;
						$detail -> id_position_detail_acknowledge = $emp_indirect[0]->id_pos_indirect;
						$detail -> save();
					//	break;
					}
				}           
			}
		/*	$diff = array_diff($listId, $idDetail);
			if(count($diff) > 0){
				foreach ($diff as $key => $value) { 
					HrPerformanceReview::where('id_performance_review', $value)->delete();
				}
			}
		*/
		    DB::commit();
			return response()->json(['status' => 'true', 'message' => 'P2K Review Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Save P2K Review !! [' . $e->getMessage() . ']']);           
		}	
	}
		
	
	public function get_employee_by() {
        $result = KpkLetter::get_employee_by();
        return response()->json($result);
    }
	
	public function get_dept(Request $request) {
        $results = collect(KpkLetter::get_dept($request->id_com));
        $logisticDelivery = $results->where('department_code', '140D_LOGD')->first();
        $return = [];
        foreach($results as $key => $result) {
            if($result->department_code == '140W_LOGW' && $logisticDelivery) {
                unset($results[$key]);
                continue;
            } 
            if($result->department_code == $logisticDelivery->department_code) {
                $results[$key]->text = 'Logistik';
            }
            $return[] = $result;
        }
        return response()->json($return);
    }
	
	public function get_division() {
        $result = KpkLetter::get_division();
        return response()->json($result);
    }
	
	public function get_participant(Request $request) {
        $id_com_type = $request->id_com_type;
        $id_dept = is_array($request->id_dept) ? $request->id_dept : [$request->id_dept];
		$month = $request->period_month;
		$year = $request->period_year;
		$new_edit = $request->new_edit;
        $result = KpkLetter::get_participant($id_com_type,$id_dept,$month,$year,$new_edit);
        return response()->json($result);
    }

	public function get_pos_detail(Request $request) {
        $result = KpkLetter::get_pos_detail($request->id_pos_detail);
        return response()->json($result);
    }
	
	public function get_threshold(Request $request) {
        $request->validate([
            'id_employee' => 'required',
        ]);
		$job_emp = JobPositionDetail::where('id_employee',$request->id_employee)->where('secondary_position',false)->first();
		if($job_emp){
			$pos = $job_emp->id_position_routing;
		}
		else{
			$job_pos = JobPositionDetail::where('id_position_detail',$request->id_pos_detail)->where('secondary_position',false)->first();
			$pos = $job_pos->id_position_routing;
		}
        $threshold = KpkLetter::get_threshold_group($request->id_employee,$pos,$request->edit);
		$x=[];
		$y=[];
		foreach($threshold as $key=>$val){
			if($val->pos_routing != NULL){
				$x[$key] = $val;
			}
			else{
				$y[$key] = $val;
			}
		}
		if(count($x) > 0){
			$threshold = $x;
		}
		else{
			$threshold = $y;
		}
        return response()->json($threshold);
    }
	
	public function get_edit(Request $request) {
        $data = [
            'id_kpk' => $request->id_kpk
        ];
		$result = KpkLetter::get_edit($data);
		foreach($result['kpk'] as $key => $val){
			$string = explode(',', trim($val['sales_offtake'],'{}'));
			$result['kpk'][$key] = $val;
			$result['kpk'][$key]['sales_offtake'] = $string;
		}
        return response()->json($result);
    }
	
	public function get_edit_review(Request $request) {
        $data = [
            'id_kpk' => $request->id_kpk,
            'id_position_detail' => $request->id_position_detail,
            'id_employee' => $request->id_employee,
        ];
		$result = KpkLetter::get_edit_review($data);
		$result['check'] = false;
		if(count($result['review']) > 0){
			$array = [];
			$firstElement = current($result['review']);
			$endElement = end($result['review']);
			array_push($array,$firstElement,$endElement);
			$dateFirst = Carbon::parse($array[0]['period_date'])->startOfMonth()->format('Y-m-d');
			$dateEnd = Carbon::parse($array[1]['period_date'])->endOfMonth()->format('Y-m-d');
			$param = [
				'id_employee' => $result['id_employee'],
				'start_date' => $dateFirst,
				'end_date' => $dateEnd,
				'id_company' => $result['id_company'],
			];
			$check = KpkLetter::get_check($param);
			if(count($check) > 0){
				$result['check'] = true;
			}
		}
        return response()->json($result);
    }
	
	public function upload_review(Request $request) {
	//	dd($request->emp);
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        $allFile    = [];
        $filePath   = 'public/';
        try {
            $dataReturn = [];
            $today      = date('Y-m-d');
            $validator 	= Validator::make($request->all(), [
                'attachment' => 'required|mimes:xls,xlsx'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            if($request->attachment) {
                if ($request->attachment->isValid()) {
                    $fileName       = $request->attachment->getClientOriginalName();
                    $newFileName    = Str::random(3).'_'.$fileName;
                    $storageFile    = Storage::putFileAs($filePath, $request->attachment, $newFileName);
                    $allFile[]      = $newFileName;
                }
            }
            if(count($allFile) > 0){
                foreach ($allFile as $key => $file) {
                    if(Storage::disk('local')->exists($filePath.$file)){
                        $thisExt = pathinfo(storage_path('app/'.$filePath.$file))['extension'];
                        if($thisExt == 'csv'){
                            $reader = new Csv(); 
                        } else if($thisExt == 'xlsx'){
                            $reader = new Xlsx(); 
                        } else {
                            $reader = new Xls(); 
                        }
                        $reader->setReadDataOnly(true);
                        $spreadsheet    	= $reader->load(storage_path('app/'.$filePath.$file));
                        $sheetData      	= $spreadsheet->getActiveSheet()->toArray();
 						
 						$allNikKaryawan		= [];
 					//	$listType			= [];
 						$dataType			= [];

                        foreach ($sheetData as $k => $row) {
                    	   if($k > 0){
                            	$nikKaryawan 		= $row[0] ?? null;
                            	$namaKaryawan 		= $row[1] ?? null;
								$act_idx 			= $row[2] ?? null;
								$act_kpi 			= $row[3] ?? null;
								$m_1 				= $row[4] ?? null;
								$m_2 				= $row[5] ?? null;
								$m_3 				= $row[6] ?? null;
								$m_4 				= $row[7] ?? null;
								$m_5 				= $row[8] ?? null;

                                if(!is_null($nikKaryawan) && !in_array($nikKaryawan, $allNikKaryawan)){
                                	$allNikKaryawan[] = $nikKaryawan;
                                //	$listType[] = $type;
                                }
								
								 if(!is_null($nikKaryawan)){
                                	//utk menampung sementara data dari excel ke dalam array
                                	$dataType[$k-1]['nik_employee'] = $nikKaryawan;
                                	$dataType[$k-1]['act_idx'] = $act_idx;
                                	$dataType[$k-1]['act_kpi'] = $act_kpi;
                                	$dataType[$k-1]['m_1'] = $m_1;
                                	$dataType[$k-1]['m_2'] = $m_2;
                                	$dataType[$k-1]['m_3'] = $m_3;
                                	$dataType[$k-1]['m_4'] = $m_4;
                                	$dataType[$k-1]['m_5'] = $m_5;
                                }
                            }
                        }
						foreach ($dataType as $nik => $val) {
							foreach ($request->emp as $key => $nik_emp) {
								if($val['nik_employee'] == $nik_emp['nik_employee']){
									$job_emp = JobPositionDetail::where('id_employee',$nik_emp['id_employee'])->where('secondary_position',false)->first();
									$get_threshold = KpkLetter::get_threshold_group($nik_emp['id_employee'],$job_emp->id_position_routing);
									$id_threshold = null;
									$minimum_score_kpi_level = null;
									if(count($get_threshold) > 0){
										$id_threshold = $get_threshold[0]->id;
										$minimum_score_kpi_level = $get_threshold[0]->minimum_score_kpi_level;
									}
									$dataReturn[$nik]['id_employee'] = $nik_emp['id_employee'];
									$dataReturn[$nik]['id_position_detail'] = $job_emp->id_position_detail;
									$dataReturn[$nik]['act_idx'] = $val['act_idx'];
									$dataReturn[$nik]['act_kpi'] = $val['act_kpi'];
									$dataReturn[$nik]['id_threshold'] = $id_threshold;
									$dataReturn[$nik]['minimum_score_kpi_level'] = $minimum_score_kpi_level;
									$dataReturn[$nik]['m_1'] = $val['m_1'];
									$dataReturn[$nik]['m_2'] = $val['m_2'];
									$dataReturn[$nik]['m_3'] = $val['m_3'];
									$dataReturn[$nik]['m_4'] = $val['m_4'];
									$dataReturn[$nik]['m_5'] = $val['m_5'];
									
								}							
							}
        				}
						$result = array_values($dataReturn);
                        Storage::disk('local')->delete($filePath.$file);
                    }
                }
			//	dd($result);
            }
            DB::commit();   
            return response(['status' => 'true', 'message' => 'Upload Success', 'data' => $result]);
        } catch (\Exception $e) {
            DB::rollBack();
            if(count($allFile) > 0){
                foreach ($allFile as $key => $file) {
                    if(Storage::disk('local')->exists($filePath.$file)){
            			Storage::disk('local')->delete($filePath.$file);
                    }
                }
            }
            return response(['status' => 'false', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

	public function download(Request $request) {
        ini_set('max_execution_time', -1);
		$idDecrypt = Crypt::decrypt($request->id_letter);
		$data = array(
			'id_letter' => $idDecrypt,
		);
        $result = KpkLetter::get_pdf_potrait($data);
		$qrcode = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result->reference_number.' ('.$result->emp_name.'-'.$result->nik_employee.')'));
		$result->acc = $qrcode;
	//	dd($result);
		$m = new Merger();
		$html = view('eletter.kpk_letter.download_portrait', compact('result'))->render();
		$pdf = PDF::loadHTML($html)->setPaper('A4','potrait');
        $m->addRaw($pdf->stream());	
		
		$result2 = KpkLetter::get_pdf_landscape($data);
		$date = Carbon::parse($result->effective_date);
		for ($i = 1; $i <= 6; $i++) {
			$z[] = $date->addMonths(1)->translatedFormat('M y');
		}
		$result->months = $z;	
		foreach($result2 as $key=>$val){
			if($val->sales_offtake != null){
				$replace = str_replace(['{','}'],'',$val->sales_offtake);
				$x = explode(",",$replace);
				$result2[$key]->sales_offtake = $x;
			}
			else{
				$x = [];
				$result2[$key]->sales_offtake = $x;
			}
		}
	//	dd($result2);
		$html2 = view('eletter.kpk_letter.download_landscape', compact('result','result2'))->render();
		$pdf2 = PDF::loadHTML($html2)->setPaper('legal','landscape');
        $m->addRaw($pdf2->stream());
 
		$nama_file = 'BA_Penetapan_'.date('dmY').'.pdf';
		
        return response($m->merge())
                ->withHeaders([
                    'Content-Type' => 'application/pdf',
                    'Cache-Control' => 'no-store, no-cache',
                    'Content-Disposition' => 'inline; filename="'.$nama_file,
                ]);
		
    }
	
	public function download_review(Request $request) {
        ini_set('max_execution_time', -1);
		$text_date = str_replace("-"," ",$request->text_date);
		$period_month = str_replace("-"," ",$request->period_month);
		$city_date = str_replace("-"," ",$request->city_date);
		$data = array(
			'id_review' => $request->id_review,
		);
        $result = KpkLetter::get_pdf_review($data);
		$tot_kpi = round((($result->result_value / $result->target_volume) * 100),2);
		$result->val_index = $tot_kpi;
		$result->text_date = $text_date;
		$result->period_month = $period_month;
		$result->city_date = $city_date;

		$qrcode_direct = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result->direct_name.'-'.$result->direct_nik));
		$qrcode_indirect = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result->indirect_name.'-'.$result->indirect_nik));
		$result->acc_direct = $qrcode_direct;
		$result->acc_indirect = $qrcode_indirect;
		$result->first_sp = null;
		$result->end_sp = null;
		if($result->treatment == 'sp2' || $result->treatment == 'sp3'){
			if($result->treatment == 'sp2'){
				$get_review = HrPerformanceReview::where('id_performance_evaluation',$result->id_performance_evaluation)->where('treatment','sp1')->first();
			}
			else if($result->treatment == 'sp3'){
				$get_review = HrPerformanceReview::where('id_performance_evaluation',$result->id_performance_evaluation)->where('treatment','sp2')->first();
			}
			
			$first_sp = Carbon::parse($get_review->review_date)->translatedFormat('d F Y');
			$end_sp = Carbon::parse($get_review->review_date)->addMonths(6)->translatedFormat('d F Y');
			$result->first_sp = $first_sp;
			$result->end_sp = $end_sp;
		}
	//	dd($result);
		$html = view('eletter.kpk_letter.download_review', compact('result'))->render();
		$pdf = PDF::loadHTML($html)->setPaper('A4','potrait');
		
		return $pdf->stream('BA_Review_'.date('dmY').'.pdf');
		
    }
	
	public function export_validate(Request $request) {
		$valid = array(
			'id_url' => $request->id_url,
			'status' => $request->status,
			'employee' => $request->nik,
			'dept_search' => $request->id_dept,
		);
        return response()->json($valid);
    }
	
	public function export(Request $request) {
		$nik = $request->employee ?? NULL;
		$idNik = null;
		if($nik != 'null'){
			$arrNik = explode(",",$nik);
			foreach($arrNik as $val){
				$idNik[] = "'".$val."'";
			}
		}
		$status = $request->status;
		$codeDept = $request->dept_search;
        $res = KpkLetter::getExport($this->accessBranch($request),$idNik,$codeDept,$status);
	//	dd($res);
		$treat = ['bulan1','sp1','sp2','sp3'];
		$treat_pass = ['pass'];
		$treat_failed = ['phk','phkspdt','resign','demosi'];
		foreach($res as $i=>$val_res){
			$res[$i]->status = 'Belum Lulus';
			$res[$i]->last_treat = '';
			$res[$i]->progress = 'Belum Review';
			$res[$i]->last_month = '';
			$res[$i]->status_review = 'Need Review';
			for($x=1; $x<=8;$x++){
				if(in_array($val_res->{"treat_month_".$x}, $treat)){
					if($val_res->{"treat_month_".$x} == 'bulan1'){
						$res[$i]->{"treat_month_".$x} = 'P2K bulan ke-1';
						$res[$i]->last_treat = 'P2K bulan ke-1';
					}
					else if($val_res->{"treat_month_".$x} == 'sp1'){
						$res[$i]->{"treat_month_".$x} = 'SP 1';
						$res[$i]->last_treat = 'SP 1';
					}
					else if($val_res->{"treat_month_".$x} == 'sp2'){
						$res[$i]->{"treat_month_".$x} = 'SP 2';
						$res[$i]->last_treat = 'SP 2';
					}
					else if($val_res->{"treat_month_".$x} == 'sp3'){
						$res[$i]->{"treat_month_".$x} = 'SP 3';
						$res[$i]->last_treat = 'SP 3';
					}else if($val_res->{"treat_month_".$x} == 'sp3'){
						$res[$i]->{"treat_month_".$x} = 'SP 3';
						$res[$i]->last_treat = 'SP 3';
					}
					$res[$i]->status = 'Belum Lulus';
					$res[$i]->progress = 'Sudah Review Bulan ke-'.$x;
					$res[$i]->last_month = Carbon::parse($val_res->{"review_date_".$x})->translatedFormat('M y');
					$res[$i]->status_review = 'Need Review';
				}
				if(in_array($val_res->{"treat_month_".$x}, $treat_pass)){
					$res[$i]->{"treat_month_".$x} = 'Lulus';
					$res[$i]->last_treat = 'Lulus';
					$res[$i]->status = 'Lulus';
					$res[$i]->progress = 'Done';
					$res[$i]->last_month = '';
					$res[$i]->status_review = 'Done Review';
				}
				if(in_array($val_res->{"treat_month_".$x}, $treat_failed)){
					if($val_res->{"treat_month_".$x} == 'phk'){
						$res[$i]->{"treat_month_".$x} = 'PHK';
						$res[$i]->last_treat = 'PHK';
					}
					else if($val_res->{"treat_month_".$x} == 'phkspdt'){
						$res[$i]->{"treat_month_".$x} = 'PHK SPDT';
						$res[$i]->last_treat = 'PHK SPDT';
					}
					else if($val_res->{"treat_month_".$x} == 'resign'){
						$res[$i]->{"treat_month_".$x} = 'Resign';
						$res[$i]->last_treat = 'Resign';
					}
					else if($val_res->{"treat_month_".$x} == 'demosi'){
						$res[$i]->{"treat_month_".$x} = 'Demosi';
						$res[$i]->last_treat = 'Demosi';
					}
					$res[$i]->status = 'Tidak Lulus';
					$res[$i]->progress = 'Done';
					$res[$i]->last_month = '';
					$res[$i]->status_review = 'Done Review';
				}			
			}
		}
	//	dd($res);
		$codeDept = "";
		if(count($res) > 0){
			$codeDept = $res[0]->dept_code;
		}
		$dataSheet1 = [];
		$dataSheet1[] = ['No', 'NIK', 'Name', 'Position', 'Division', 'Region', 'Branch', 'Direct Supervisor', 'Company', 'Grade', 'Department', 'Month BA',
		'Perf. Month-1','Hasil Month-1','Treatment Month-1',
		'Perf. Month-2','Hasil Month-2','Treatment Month-2',
		'Perf. Month-3','Hasil Month-3','Treatment Month-3',
		'Perf. Month-4','Hasil Month-4','Treatment Month-4',
		'Perf. Month-5','Hasil Month-5','Treatment Month-5',
		'Perf. Month-6','Hasil Month-6','Treatment Month-6',
		'Perf. Month-7','Hasil Month-7','Treatment Month-7',
		'Perf. Month-8','Hasil Month-8','Treatment Month-8',
		'Status','Treatment Akhir','Progress P2K','Last Month Review','Status Review'];
		
		foreach($dataSheet1 as $key=>$val){
			if($codeDept == '170_SAL'){
				$dataSheet[$key][] = 'Vol Idx Month-1';
				$dataSheet[$key][] = 'Vol Idx Month-2';
				$dataSheet[$key][] = 'Vol Idx Month-3';
				$dataSheet[$key][] = 'Vol Idx Month-4';
				$dataSheet[$key][] = 'Vol Idx Month-5';
				$dataSheet[$key][] = 'Vol Idx Month-6';
				$dataSheet[$key][] = 'Vol Idx Month-7';
				$dataSheet[$key][] = 'Vol Idx Month-8';
			}
			else{
				$dataSheet[$key][] = 'KPI Month-1';
				$dataSheet[$key][] = 'KPI Month-2';
				$dataSheet[$key][] = 'KPI Month-3';
				$dataSheet[$key][] = 'KPI Month-4';
				$dataSheet[$key][] = 'KPI Month-5';
				$dataSheet[$key][] = 'KPI Month-6';
				$dataSheet[$key][] = 'KPI Month-7';
				$dataSheet[$key][] = 'KPI Month-8';
			}
		}
		array_splice($dataSheet1[0],14,0,$dataSheet[0][0]);
		array_splice($dataSheet1[0],18,0,$dataSheet[0][1]);
		array_splice($dataSheet1[0],22,0,$dataSheet[0][2]);
		array_splice($dataSheet1[0],26,0,$dataSheet[0][3]);
		array_splice($dataSheet1[0],30,0,$dataSheet[0][4]);
		array_splice($dataSheet1[0],34,0,$dataSheet[0][5]);
		array_splice($dataSheet1[0],38,0,$dataSheet[0][6]);
		array_splice($dataSheet1[0],42,0,$dataSheet[0][7]);

		foreach($res as $k => $emp) {
			$dataSheet1[] = [
				$k+1,
				$emp->nik_employee,
				$emp->name,
				$emp->emp_pos,
				$emp->division ? $emp->division : "-",
				$emp->region,
				$emp->branch,
				$emp->direct_name,
				$emp->company_code,
				$emp->grade,
				$emp->dept,
				$emp->ba_month,
				$emp->perf_month_1,
				$emp->vol_kpi_month_1 ? $emp->vol_kpi_month_1."%" : "",
				$emp->res_month_1,
				$emp->treat_month_1,
				$emp->perf_month_2,
				$emp->vol_kpi_month_2 ? $emp->vol_kpi_month_2."%" : "",
				$emp->res_month_2,
				$emp->treat_month_2,
				$emp->perf_month_3,
				$emp->vol_kpi_month_3 ? $emp->vol_kpi_month_3."%" : "",
				$emp->res_month_3,
				$emp->treat_month_3,
				$emp->perf_month_4,
				$emp->vol_kpi_month_4 ? $emp->vol_kpi_month_4."%" : "",
				$emp->res_month_4,
				$emp->treat_month_4,
				$emp->perf_month_5,
				$emp->vol_kpi_month_5 ? $emp->vol_kpi_month_5."%" : "",
				$emp->res_month_5,
				$emp->treat_month_5,
				$emp->perf_month_6,
				$emp->vol_kpi_month_6 ? $emp->vol_kpi_month_6."%" : "",
				$emp->res_month_6,
				$emp->treat_month_6,
				$emp->perf_month_7,
				$emp->vol_kpi_month_7 ? $emp->vol_kpi_month_7."%" : "",
				$emp->res_month_7,
				$emp->treat_month_7,
				$emp->perf_month_8,
				$emp->vol_kpi_month_8 ? $emp->vol_kpi_month_8."%" : "",
				$emp->res_month_8,
				$emp->treat_month_8,
				$emp->status,
				$emp->last_treat,
				$emp->progress,
				$emp->last_month,
				$emp->status_review,
			];
		}

		$filename = 'Report P2K_('.date('Ymd').')';
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0);
		

		$nameSheet1 = 'Report P2K';
        $indexSheet1 = 0;
        $workSheet1 = new Worksheet($spreadsheet, $nameSheet1);
        $spreadsheet->addSheet($workSheet1, $indexSheet1);
		$spreadsheet->setActiveSheetIndexByName($nameSheet1); // utk set sheet yg aktif
        $workSheet1->fromArray($dataSheet1);
		
		$activeSheet = $spreadsheet->getActiveSheet();
		foreach ($workSheet1->getColumnIterator() as $rowIndex=>$column){
			$workSheet1->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
		}
		foreach ($workSheet1->getRowIterator() as $key=>$row){
			if($key > 1){
				$ASRow = $activeSheet->getCell('AS'.$key)->getValue();
				$AURow = $activeSheet->getCell('AU'.$key)->getValue();
				$AWRow = $activeSheet->getCell('AW'.$key)->getValue();
				if($ASRow == "Lulus"){
					$activeSheet->getStyle('AS'.$key.':AS'.$key)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('affca8');
				}
				else if($ASRow == "Belum Lulus"){
					$activeSheet->getStyle('AS'.$key.':AS'.$key)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('eafb7b');
				}
				else if($ASRow == "Tidak Lulus"){
					$activeSheet->getStyle('AS'.$key.':AS'.$key)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffb0aa');
				}
				if($AURow == "Done"){
					$activeSheet->getStyle('AU'.$key.':AU'.$key)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('affca8');
				}
				if($AWRow == "Done Review"){
					$activeSheet->getStyle('AW'.$key.':AW'.$key)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('affca8');
				}
				else if($AWRow == "Need Review"){
					$activeSheet->getStyle('AW'.$key.':AW'.$key)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffb0aa');
				}
			}
		}
		$activeSheet->getStyle('A1:AW1')->getFont()->setBold(true);
		$activeSheet->getStyle('A1:AW1')->getAlignment()->setHorizontal('center');
		$activeSheet->getStyle('A1:AW1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fdff3b');
		
		$lastColumn         = $activeSheet->getHighestColumn();
        $lastRow            = $activeSheet->getHighestRow();
        $columnAllData      = $lastColumn.$lastRow;
		
		$activeSheet->getStyle('M2:AR'.$lastRow)->getAlignment()->setHorizontal('center');
		$activeSheet->getStyle('A1:A'.$lastRow)->getAlignment()->setHorizontal('center');
		$activeSheet->getStyle('A1:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
		
	//	dd($activeSheet->getCell('C2')->getValue());
		
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->setIncludeCharts(true);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		$writer->save('php://output');
	}

	public function get_dept_search() {
		$result = KpkLetter::get_dept_search();
        return response()->json($result);
    }
	
	public function get_employee_search(Request $request) {
		$status = $request->status ?? 'A';
		$result = KpkLetter::get_employee_search($this->accessBranch($request), $request->code_dept, $status);
        return response()->json($result);
    }
}