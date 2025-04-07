<?php
namespace App\Http\Controllers\Kpi\Fpr;

use App\Models\Kpi\Fpr\FprHeader;
use App\Models\Kpi\Fpr\FprDetail;
use App\Models\Kpi\Kpi\QuantitativeKpi;
use App\Models\Employee\Employee\Employee;
use App\Models\Kpi\KpiSetting\PaQuestion;
use App\Models\GeneralSetting\CompanySetting\MasterPeriod;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

class FprController extends Controller {
	public function __construct()
	{
        $this->QuantitativeKpiController = new QuantitativeKpiController;
	}
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
/*	
    public function index(Request $request) {
        if ($request->ajax()) {
			$period 				= $request->period ?? null;
            $data = QuantitativeKpi::getdata($period);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_kpi_group . '" class="edit btn btn-primary btn-sm" title="KPI Detail"><span class="fas fa-edit"></span></button> ';
							
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.kpi.pa_quantitative_assesment.index');
    }
*/
	public function index_report(Request $request) {
        if ($request->ajax()) {
			$id_employee 			= $request->id_employee ?? null;
			$period 				= $request->period ?? null;
            $data = FprHeader::getdata_report($id_employee,$period,$this->accessBranch($request));
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_fpr_header . '" class="download btn btn-success btn-sm" style="padding:1px 6px 1px 6px;"  title="Download FPR"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.fpr.fpr_management.index_report');
    }
	
	public function index(Request $request) {
			$path_url = $request->path();
			if($path_url == 'kpi/fpr/fpr_subordinate'){
				$name_url = 'Subordinate';
			}
			else if($path_url == 'kpi/fpr/fpr_self'){
				$name_url = 'Self';
			}
			else {
				$name_url = '';
			}
        if ($request->ajax()) {
			$period 				= $request->period ?? null;
			$path_param 			= $request->path;
            $data = FprHeader::getdata($period,$path_param);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_fpr_header . '" class="edit btn btn-success" style="padding:1px 4px 1px 4px;" title="Isi FPR"><span class="fas fa-list-alt" style="font-size:16px;"></span></button> ';
								
								 $button .= '<button type="button" name="pdf" id="' . $data->id_fpr_header . '" class="pdf btn btn-success download" style="margin-left:5px;padding:1px 6px 1px 6px;" title="Download FPR"><span class="fas fa-file-pdf" style="font-size:16px;"></span></button> ';
								 
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.fpr.fpr.index',compact('path_url','name_url'));
    }
	
	public function generate_fpr(Request $request) {
		if(@$request->path == 'kpi/fpr/fpr_management'){
			$data['id_employee'] = $request->id_employee != null ? (int)$request->id_employee : null;
			$data['period'] = $request->period;
			$data['id_company'] = session('id_company');
			$data['id_user'] = session('id_user');
		}
		else{
			$emp = Employee::where('id_user',session('id_user'))->where('status','A')->first();
			$data = [
					'id_employee' => $emp['id_employee'],
					'period' => $request->period,
					'id_company' => session('id_company'),
					'id_user' => session('id_user'),
				];
		}
	//	dd($data);
        $result = FprHeader::generate_fpr($data);
        return response()->json($result);
    }
	
	protected function update(Request $request) {	
		try{
			$path_url = $request->path();	
		//	dd($request->all());
			DB::beginTransaction();
			if($request->soal || $request->soal_periodic){
				$seq = [];
				$fprHeader = FprHeader::where('id_fpr_header',$request->id_fpr_header)->first();
				$mapping = QuantitativeKpi::get_mapping($fprHeader->id_kpi_group);
				if(count($mapping) > 0){
					$kpi_group = $mapping[0];
				}		
			//	$kpiGroup = QuantitativeKpi::where('id_kpi_group',$fprHeader->id_kpi_group)->first();
				$form_data = [];
			//	dd($request->id_fpr_header);
				if($request->fpr_type == '(Yearly)'){
					foreach ($request->soal as $key => $id_question) {
						$ansnote = 'ans_'.$id_question;
						$answers = 'answers_'.$id_question;
						$ques = PaQuestion::where('id_pa_question',$id_question)->first();
						$char = 'Jumlah Karakter Kurang Dari 30';
						
						if(!$request->$answers){
							if($ques->sequence == 12){
								$seq[] = 'Soal No. 5a (Bidang) Belum Dijawab';
							}
							if($ques->sequence == 13){
								$seq[] = 'Soal No. 5b (homebase) Belum Dijawab';
							}
						}
						if(!$request->$ansnote){
							if($ques->sequence == 1){
								$seq[] = 'Soal No. 1 Belum Dijawab';
							}
							if($ques->sequence == 3){
								$seq[] = 'Soal No. 2b Belum Dijawab';
							}
							if($ques->sequence == 4){
								$seq[] = 'Soal No. 2c Belum Dijawab';
							}
							if($ques->sequence == 6){
								$seq[] = 'Soal No. 4a Belum Dijawab';
							}
							if($ques->sequence == 7){
								$seq[] = 'Soal No. 4b Belum Dijawab';
							}
							if($ques->sequence == 8){
								$seq[] = 'Soal No. 4c (Deskripsi Program Pengembangan) Belum Dijawab';
							}
							if($ques->sequence == 9){
								$seq[] = 'Soal No. 4c (Target Program) Belum Dijawab';
							}
							if($ques->sequence == 10){
								$seq[] = 'Soal No. 4c (Bantuan yang dibutuhkan) Belum Dijawab';
							}
							if($ques->sequence == 11){
								$seq[] = 'Soal No. 4c (PIC yang Terlibat) Belum Dijawab';
							}
							if($ques->sequence == 14){
								$seq[] = 'Soal No. 5c (Area Kerja) Belum Dijawab';
							}
							if($ques->sequence == 15){
								$seq[] = 'Soal No. 5d (Karir / Jabatan) Belum Dijawab';
							}
							if($ques->sequence == 16){
								$seq[] = 'Soal No. 6 Belum Dijawab';
							}
						}
						else{
							if($ques->sequence == 1 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 1 ('.$char.')';
							}
							if($ques->sequence == 3 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 2b ('.$char.')';
							}
							if($ques->sequence == 4 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 2c ('.$char.')';
							}
							if($ques->sequence == 6 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 4a ('.$char.')';
							}
							if($ques->sequence == 7 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 4b ('.$char.')';
							}
							if($ques->sequence == 8 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 4c (Deskripsi Program Pengembangan - '.$char.')';
							}
							if($ques->sequence == 9 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 4c (Target Program - '.$char.')';
							}
							if($ques->sequence == 10 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 4c (Bantuan yang dibutuhkan - '.$char.')';
							}
							if($ques->sequence == 11 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 4c (PIC yang Terlibat - '.$char.')';
							}
							if($ques->sequence == 14 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 5c (Area Kerja - '.$char.')';
							}
							if($ques->sequence == 15 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 5d (Karir / Jabatan - '.$char.')';
							}
							if($ques->sequence == 16 && strlen($request->$ansnote) < 30){
								$seq[] = 'Soal No. 6 ('.$char.')';
							}
						}
						
						$detail = FprDetail::where('id_fpr_header',$request->id_fpr_header)->where('id_pa_question',$id_question)->get();
							$form_data = array(
								'id_fpr_header' => $request->id_fpr_header,
								'id_period' => $fprHeader->id_period,
								'id_pa_question' => $id_question,
								'status' => $fprHeader->status,
								'id_company' => $fprHeader->id_company,
							);
						if($detail->count() == 0){
							$form_data['created_by'] = session('id_user');
							if(is_array($request->$answers)){							
								$a = [];	
								$lain = null;	
								foreach ($request->$answers as $i => $id_answer) {
									$aws = 'aws_'.$id_answer;
									$a[] = $id_answer;
									if(@$request->$aws[0]){
										$lain = @$request->$aws[0];
									}						
								}
								$form_data['id_pa_answer'] = '{' . substr(json_encode($a), 1, -1) . '}';
								
								$form_data['description_answer'] = $lain;
							}
							else{
								$form_data['description_answer'] = $request->$ansnote;						
							}
							FprDetail::create($form_data);
						}					
						else{
							$form_data['updated_by'] = session('id_user');
							if(is_array($request->$answers)){							
								$a = [];	
								$lain = null;	
								foreach ($request->$answers as $i => $id_answer) {
									$aws = 'aws_'.$id_answer;
									$a[] = $id_answer;
									if(@$request->$aws[0]){
										$lain = @$request->$aws[0];
									}
								
								}
								$form_data['id_pa_answer'] = '{' . substr(json_encode($a), 1, -1) . '}';
								
								$form_data['description_answer'] = $lain;
							}
							else{
								$form_data['description_answer'] = $request->$ansnote;						
							}
							FprDetail::where('id_fpr_header',$request->id_fpr_header)->where('id_pa_question',$id_question)->update($form_data); 
						}			
					}
				}
				else if($request->fpr_type == '(Periodically)'){
					foreach ($request->soal_periodic as $key => $id_question) {
						$periodic = 'per_'.$id_question;
						$ques = PaQuestion::where('id_pa_question',$id_question)->first();
						$char = 'Jumlah Karakter Kurang Dari 30';
						if(!$request->$periodic){							
							if($ques->sequence == 2){
								$seq[] = 'Soal No. 2 (TARGET YANG TELAH TERCAPAI) Belum Dijawab';
							}
							if($ques->sequence == 3){
								$seq[] = 'Soal No. 2 (HAL YANG MENJADI KEKUATAN) Belum Dijawab';
							}
							if($ques->sequence == 4){
								$seq[] = 'Soal No. 2 (HAL YANG PERLU DITINGKATKAN) Belum Dijawab';
							}
							if($ques->sequence == 5){
								$seq[] = 'Soal No. 2 (TARGET YANG AKAN DICAPAI) Belum Dijawab';
							}
							if($ques->sequence == 6){
								$seq[] = 'Soal No. 2 (TARGET WAKTU) Belum Dijawab';
							}
							if($ques->sequence == 7){
								$seq[] = 'Soal No. 2 (BANTUAN YG DIBUTUHKAN) Belum Dijawab';
							}
							if($ques->sequence == 8){
								$seq[] = 'Soal No. 3 (KOMENTAR / SARAN ATASAN) Belum Dijawab';
							}
							if($ques->sequence == 9){
								$seq[] = 'Soal No. 3 (KOMENTAR / SARAN KARYAWAN) Belum Dijawab';
							}							
						}
						else{
							if($ques->sequence == 2 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 2 (TARGET YANG TELAH TERCAPAI - '.$char.')';
							}
							if($ques->sequence == 3 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 2 (HAL YANG MENJADI KEKUATAN - '.$char.')';
							}
							if($ques->sequence == 4 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 2 (HAL YANG PERLU DITINGKATKAN - '.$char.')';
							}
							if($ques->sequence == 5 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 2 (TARGET YANG AKAN DICAPAI - '.$char.')';
							}
							if($ques->sequence == 6 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 2 (TARGET WAKTU - '.$char.')';
							}
							if($ques->sequence == 7 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 2 (BANTUAN YG DIBUTUHKAN - '.$char.')';
							}
							if($ques->sequence == 8 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 3 (KOMENTAR / SARAN ATASAN - '.$char.')';
							}
							if($ques->sequence == 9 && strlen($request->$periodic) < 30){
								$seq[] = 'Soal No. 3 (KOMENTAR / SARAN KARYAWAN - '.$char.')';
							}
							
						}
												
						$detail = FprDetail::where('id_fpr_header',$request->id_fpr_header)->where('id_pa_question',$id_question)->get();
						$form_periodic = array(
								'id_fpr_header' => $request->id_fpr_header,
								'id_period' => $fprHeader->id_period,
								'id_pa_question' => $id_question,
								'description_answer' => $request->$periodic ,
								'status' => $fprHeader->status,
								'id_company' => $fprHeader->id_company,
							);	
						if($detail->count() == 0){
							$form_periodic['created_by'] = session('id_user');
							FprDetail::create($form_periodic);
						}
						else{
							$form_periodic['updated_by'] = session('id_user');
							FprDetail::where('id_fpr_header',$request->id_fpr_header)->where('id_pa_question',$id_question)->update($form_periodic);
						}
					}
				}
				
				$update_header = array(
					'review_date' => $request->review_date,
					'updated_by' => session('id_user'),
				);
					
				if($request->id_button == 'save_button'){
					if(@$request->emp_approve == 'on'){
						$update_header['id_approval_participant'] = $kpi_group->id_employee;
					}
					else{
						$update_header['id_approval_participant'] = null;
					}
					FprHeader::where('id_fpr_header',$request->id_fpr_header)->update($update_header);
					$message = 'Performance Review disimpan sebagai Draft';
				}
				else if($request->id_button == 'submit_button'){
					if(!$request->atasan_approve){
						$seq[] = 'Atasan Menyetujui belum dicentang';
					}
					else if(@$request->atasan_approve == 'on'){
						$update_header['id_approval_appraisers'] = $kpi_group->id_employee_appraisers;
					}
			
					if(!$request->emp_approve){
						$seq[] = 'Karyawan Menyetujui belum dicentang';
					}
					else if(@$request->emp_approve == 'on'){
						$update_header['id_approval_participant'] = $kpi_group->id_employee;
					}
					else{
						$update_header['id_approval_participant'] = null;
					}
				
					if(count($seq)){
						$showerr = collect($seq)->implode("\n");
						throw new \Exception($showerr);
					}			
					$update_header['submitted'] = 1;
					FprHeader::where('id_fpr_header',$request->id_fpr_header)->update($update_header);
					$message = 'Performance Review telah sukses disubmit';
				}
			}
			DB::commit();
            return response()->json(['status' => 'true', 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }
	
	public function get_fpr_edit(Request $request) {
		$appraiser = FprHeader::where('id_fpr_header', $request->id_fpr_header)->first();
		$getperiod = MasterPeriod::where('id_period',$appraiser['id_period'])->first();
		$note = explode(' ',$appraiser['notes']);
		$notes = "";
		if(in_array("(Periodically)", $note)){
			$notes = "(Periodically)";
		}
		else if(in_array("(Yearly)", $note)){
			$notes = "(Yearly)";
		}
		$mapping = QuantitativeKpi::get_mapping($appraiser['id_kpi_group']);
		$kpi_group = null;
		if(count($mapping) > 0){
			$kpi_group = $mapping[0];
		}
		$getQualiPart = FprHeader::get_quali_part($getperiod->year,$kpi_group->id_employee);
		$QualiPartId = null;
		if(count($getQualiPart) > 0){
			$QualiPartId = $getQualiPart[0]->id_qualitative_participant;
			FprHeader::where('id_fpr_header', $request->id_fpr_header)->update(array(
				'id_qualitative_participant' => $QualiPartId,			
			));
		}
	//	$kpi = QuantitativeKpi::where('id_kpi_group', $kpi_group)->first();
		$resfpr = FprDetail::where('id_fpr_header', $request->id_fpr_header)->get();
		$getquanti = $appraiser['id_kpi_group'];
		$getquali = FprHeader::get_qualitative($QualiPartId);
		
		$per = array_slice(explode(' ',$getperiod['description']),0,-1);
		if($appraiser['submitted'] != true ){
			$atasan = Employee::where('id_employee', $kpi_group->id_employee_appraisers)->first();
			$emp = FprHeader::get_title($kpi_group->id_employee);
			$data = [
				'id_fpr_header' => $request->id_fpr_header
			];
			$emp['atasan'] = $atasan['name'];
			$emp['review_date'] = date('Y-m-d');
			
			
			$emp['period'] = implode(' ',$per);
			
			if($notes == '(Yearly)'){
				$master = FprHeader::get_fpr_new($data);									
			}
			else if($notes == '(Periodically)'){
				$master = FprHeader::get_periodic_new($data);		
			}
			
			if($resfpr->count() == 0){
				$answer = [];
			}
			else{
				$answer = FprHeader::get_fpr_edit($data);
			}		
						
			$emp['group_soal'] = $master;			
			$emp['answers'] = $answer;
			$emp['approve'] = $appraiser;
			$emp['quantitative'] = $getquanti;
			
			if(count($getquali) == 0){
				$emp['qualitative'] = [];
			}	
			else{
				$emp['qualitative'] = $getquali;
			}
										
			foreach($emp['answers'] as $key=>$val){
				if($val['id_pa_answer'] != null){
					$x = json_decode('[' . substr($val['id_pa_answer'], 1, -1) . ']'); 
					$emp['answers'][$key]['id_pa_answer']= $x;
				}						
			}
		
			return response()->json(['status' => 'true', 'result' => $emp, 'notes' => $notes]);
		}
		else{
			return response()->json(['status' => 'false', 'message' => 'Penilaian telah dilakukan']);
		}       
    }
	public function get_employee() {
        $result = FprHeader::get_employee();
        return response()->json($result);
    }
	
	public function get_period() {
        $result = FprHeader::get_period();
        return response()->json($result);
    }

    public function download(Request $request) {
        ini_set('max_execution_time', -1);

		$name_url = $request->name_url;
		$fprHeader 			= FprHeader::where('id_fpr_header', $request->id_fpr_header)->first();
		$notes_fpr			= explode(' ',$fprHeader['notes']);
		$notes = "";
		if(in_array("(Periodically)", $notes_fpr)){
			$notes = "(Periodically)";
		}
		else if(in_array("(Yearly)", $notes_fpr)){
			$notes = "(Yearly)";
		}
	//	$notes				= $notes_fpr[2];
		$getperiod = MasterPeriod::where('id_period',$fprHeader['id_period'])->first();
		$per = array_slice(explode(' ',$getperiod['description']),0,-1);
		$period = implode(' ',$per);
		$fprDetail 			= FprDetail::where('id_fpr_header', $request->id_fpr_header)->get();
		$mapping = QuantitativeKpi::get_mapping_all($fprHeader['id_kpi_group']);
		if(count($mapping) > 0){
			$kpi_group = $mapping[0];
		}
	//	$quantitativeKpi 	= QuantitativeKpi::where('id_kpi_group', $fprHeader['id_kpi_group'])->first();
		$data = [
				'id_kpi_group' => $fprHeader['id_kpi_group'],
				'id_company' => $fprHeader['id_company'],
			];
		$getquanti = QuantitativeKpi::get_kpi_view($data);
		$getquantitotal = QuantitativeKpi::get_kpi_total($data);
	//	dd($getquanti);
		$x_quanti = [];
		foreach($getquanti as $k => $val_kpi){
			$getquanti[$k]->jan = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->jan_value,$val_kpi->jan_target,$val_kpi->jan_weight);
			$getquanti[$k]->feb = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->feb_value,$val_kpi->feb_target,$val_kpi->feb_weight);
			$getquanti[$k]->mar = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->mar_value,$val_kpi->mar_target,$val_kpi->mar_weight);
			$getquanti[$k]->apr = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->apr_value,$val_kpi->apr_target,$val_kpi->apr_weight);
			$getquanti[$k]->may = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->may_value,$val_kpi->may_target,$val_kpi->may_weight);
			$getquanti[$k]->jun = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->jun_value,$val_kpi->jun_target,$val_kpi->jun_weight);
			$getquanti[$k]->jul = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->jul_value,$val_kpi->jul_target,$val_kpi->jul_weight);
			$getquanti[$k]->ags = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->aug_value,$val_kpi->aug_target,$val_kpi->aug_weight);
			$getquanti[$k]->sep = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->sep_value,$val_kpi->sep_target,$val_kpi->sep_weight);
			$getquanti[$k]->okt = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->okt_value,$val_kpi->okt_target,$val_kpi->okt_weight);
			$getquanti[$k]->nov = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->nov_value,$val_kpi->nov_target,$val_kpi->nov_weight);
			$getquanti[$k]->des = $this->QuantitativeKpiController->get_calculate($val_kpi->type_kpi,$val_kpi->des_value,$val_kpi->des_target,$val_kpi->des_weight);
			$x_quanti[] = $val_kpi;
		}
		$getquali 			= FprHeader::get_qualitative($fprHeader['id_qualitative_participant']);
		$emp 				= FprHeader::get_title($kpi_group->id_employee);
		$atasan 			= Employee::where('id_employee', $kpi_group->id_employee_appraisers)->first();
		$emp['atasan'] 		= $atasan['name'];
		$emp['nik_atasan'] 	= $atasan['nik_employee'];
		$emp['approve_emp'] 	= $fprHeader['id_approval_participant'];
		$emp['approve_atasan'] 	= $fprHeader['id_approval_appraisers'];
		$emp['review_date'] = Carbon::parse($fprHeader->review_date)->translatedFormat('d F Y');
		$emp['sign_date'] = Carbon::parse($fprHeader->review_date)->translatedFormat('d/m/Y');

		$data 				= ['id_fpr_header' => $request->id_fpr_header];
		if($notes == '(Yearly)'){
				$master = FprHeader::get_fpr_new($data);									
		}
		else if($notes == '(Periodically)'){
			$master = FprHeader::get_periodic_new($data);		
		}
	//	$master 			= FprHeader::get_fpr_new($data);
		$answer 			= [];
		
		
			
		if($fprDetail->count() > 0){
			$answer = FprHeader::get_fpr_edit($data);
		}
		
		if(count($x_quanti) == 0){
				$emp['quantitative'] = [];
				$emp['quantitative_total'] = [];
		}	
		else{
			$emp['quantitative'] = $x_quanti;
			$emp['quantitative_total'] = $getquantitotal;
		}
		
		if(count($getquali) == 0){
				$emp['qualitative'] = [];
		}	
		else{
			$emp['qualitative'] = $getquali;
		}
		
		$emp['group_soal'] = $master;			
		$emp['group_jawaban'] = $answer;
									
		foreach($emp['group_jawaban'] as $key => $val){
			if($val['id_pa_answer'] != null){
				$stringAnswer = substr($val['id_pa_answer'], 1, -1); 
				$emp['group_jawaban'][$key]['id_pa_answer'] = explode(',', $stringAnswer);
			}						
		}
	//	dd($emp);
		$html = view('kpi.fpr.fpr_management.download', compact('emp','notes','period','name_url'))->render();

		$html2pdf = new Html2Pdf('L', 'A4', 'en'); //Landscape-A4-English
        $fileName = $fprHeader['notes'].'.pdf';
        $html2pdf->writeHTML($html);
        $html2pdf->output($fileName);
    }

}
