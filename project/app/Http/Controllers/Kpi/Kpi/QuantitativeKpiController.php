<?php
namespace App\Http\Controllers\Kpi\Kpi;

use App\Models\Kpi\Kpi\QuantitativeKpi;
use App\Models\Kpi\Kpi\KpiHeader;
use App\Models\Kpi\Kpi\KpiDetail;
use App\Models\Kpi\KpiSetting\PaWeight;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
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

class QuantitativeKpiController extends Controller {
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
							/*	
								$button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_employee_participant . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
							*/	
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.kpi.pa_quantitative_assesment.index');
    }
	public function index_upload(Request $request) {
        if ($request->ajax()) {
			$id_employee 			= $request->id_employee ?? null;
			$period 				= $request->period ?? null;
            $data = QuantitativeKpi::getdata_upload($id_employee,$period,$this->accessBranch($request));
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_kpi_group . '" class="edit btn btn-primary btn-sm" title="KPI Detail"><span class="fas fa-edit"></span></button> ';
							
							/*	$button .= '&nbsp;&nbsp;<button type="button" name="inactive" id="' . $data->id_kpi_group . '" class="inactive btn btn-danger btn-sm" title="Inactive"><span class="fa fa-sign-out fa-lg"></span></button>';
							*/	
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('kpi.kpi.kpi_upload.index_upload');
    }
	
	public function generate_kpi(Request $request) {
		if(@$request->path == 'kpi/kpi/kpi_upload'){
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
        $result = QuantitativeKpi::generate_kpi($data);
        return response()->json($result);
    }
	
	public function get_kpi_edit(Request $request) {		
		$qu = QuantitativeKpi::where('id_kpi_group', $request->id_kpi_group)->first();
		if($qu['submitted'] == false){	
			$year_period = date('Y',strtotime($qu['start_date']));
			$emp = Employee::where('id_employee', $qu['id_employee'])->first();
			$nik = Employee::where('nik_employee', $emp['nik_employee'])->get();
			$date_min = [];
			foreach($nik as $val){
				$year_re[] = date('Y',strtotime($val->join_date));
				if($year_period == $year_re){
					$date_min[] = $val->join_date;
				}				
			}
			if(count($date_min) > 0){
				$start_date = min($date_min);
			}			
			else{
				$start_date = $qu['start_date'];
			}
			$data = [
				'id_kpi_group' => $request->id_kpi_group,
				'start_date' => $start_date,
				'id_company' => session('id_company'),
				'id_user' => session('id_user'),
			];
			$gen = QuantitativeKpi::generate_detail($data);
			foreach ($gen as $key => $val) {
				$form_data = array(
						'id_kpi_group' => $val->id_kpi_group,
						'kpi_month' => $val->kpi_month,
						'notes' => $val->notes,
						'subtotal_kpi' => $val->subtotal_kpi,
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
				$res =	KpiHeader::create($form_data);  
			}
		//	dd($gen);
			$result = QuantitativeKpi::get_kpi_edit($data['id_kpi_group']);
		//	$response['result'] = $result;
		//	$response['data_monthly'] = $result['monthly'];
			return response()->json(['status' => 'true', 'result'=>$result, 'data_monthly'=>$result['monthly']]);
		}
		else{			
			return response()->json(['status' => 'false']);           
		}
    }
	
	public function get_monthly(Request $request) {
		if ($request->ajax()) {
		return DataTables::of($request->mon)
					->addIndexColumn()
					->addColumn('', function($result) {
						$a = '';
						return $a;
					})
					->addColumn('action', function($data) {
						$button = '<button type="button" id="' . $data['id_kpi_header'] . '" class="additem btn btn-xs btn-success" title="Item KPI"><span class="far fa-list-alt"></span></button> ';	
						return $button;
					})
					->rawColumns(['action'])
					->make(true);		
		}
	}
	
	public function get_item_kpi(Request $request) {					
			$result = QuantitativeKpi::get_item_kpi($request->id_kpi_header);
			return response()->json($result);
		   
    }
	
	public function get_kpi_view(Request $request) {
		if ($request->ajax()) {
			$data = [
				'id_kpi_group' => $request->id_kpi_group,
				'id_company' => session('id_company'),
			];
			$result = QuantitativeKpi::get_kpi_view($data);
		//	dd($result);
			return DataTables::of($result)
				->addIndexColumn()
				->addColumn('', '')
				->addColumn('jan', function($data) {
					return $this->get_calculate($data->type_kpi,$data->jan_value,$data->jan_target,$data->jan_weight);
				})
				->addColumn('feb', function($data) {
					return $this->get_calculate($data->type_kpi,$data->feb_value,$data->feb_target,$data->feb_weight);
				})
				->addColumn('mar', function($data) {
					return $this->get_calculate($data->type_kpi,$data->mar_value,$data->mar_target,$data->mar_weight);
				})
				->addColumn('apr', function($data) {
					return $this->get_calculate($data->type_kpi,$data->apr_value,$data->apr_target,$data->apr_weight);
				})
				->addColumn('mei', function($data) {
					return $this->get_calculate($data->type_kpi,$data->may_value,$data->may_target,$data->may_weight);
				})
				->addColumn('jun', function($data) {
					return $this->get_calculate($data->type_kpi,$data->jun_value,$data->jun_target,$data->jun_weight);
				})
				->addColumn('jul', function($data) {
					return $this->get_calculate($data->type_kpi,$data->jul_value,$data->jul_target,$data->jul_weight);
				})
				->addColumn('ags', function($data) {
					return $this->get_calculate($data->type_kpi,$data->aug_value,$data->aug_target,$data->aug_weight);
				})
				->addColumn('sep', function($data) {
					return $this->get_calculate($data->type_kpi,$data->sep_value,$data->sep_target,$data->sep_weight);
				})
				->addColumn('okt', function($data) {
					return $this->get_calculate($data->type_kpi,$data->okt_value,$data->okt_target,$data->okt_weight);
				})
				->addColumn('nov', function($data) {
					return $this->get_calculate($data->type_kpi,$data->nov_value,$data->nov_target,$data->nov_weight);
				})
				->addColumn('des', function($data) {
					return $this->get_calculate($data->type_kpi,$data->des_value,$data->des_target,$data->des_weight);
				})
				->make(true);	
		}
    }
	
	public function get_kpi_total(Request $request) {
		if ($request->ajax()) {
			$data = [
				'id_kpi_group' => $request->id_kpi_group,
				'id_company' => session('id_company'),
			];
			$result = QuantitativeKpi::get_kpi_total($data);
		//	dd($result);
			return DataTables::of($result)
				->addIndexColumn()
				->make(true);	
		}
    }
	
	public function get_calculate($type_kpi,$kpi_value,$kpi_target,$kpi_weight) {
        if($type_kpi == 'Lurus' || $type_kpi == 'Progressif'){
			if($kpi_value == null || $kpi_target == null){
					$kpi_total = null;
			}
			else if($kpi_target == 0){
				$kpi_total = 0;
			}
			else{
				$tot_kpi = @($kpi_value / $kpi_target) * 100;
				if($tot_kpi > 100){
					$tot = (100 * $kpi_weight)/100;
					$kpi_total = round($tot,2);
				}
				else{	
					$tot = ($tot_kpi * $kpi_weight)/100;
					$kpi_total = round($tot,2);
				}					
			}
		}
		else if($type_kpi == 'Terbalik'){
			$tot_kpi = @(1-($kpi_value / $kpi_target))*100 ;
			if($tot_kpi < 0){
				$kpi_total = 0;
			}
			else if($kpi_value == null || $kpi_target == null){
				$kpi_total = null;
			}
			else if($tot_kpi >= 0){
				$tot = ($tot_kpi * $kpi_weight)/100;
				$kpi_total = round($tot,2);
			}
		}
		else if($type_kpi == 'Hit_Miss_Lurus'){
				if($kpi_value == null || $kpi_target == null){
					$kpi_total = null;
				}
				else if($kpi_value >= $kpi_target){
					$kpi_total = $kpi_weight;
				}
				else{
					$kpi_total = 0;
				}
			}
		else if($type_kpi == 'Hit_Miss_Terbalik'){
			if($kpi_value == null || $kpi_target == null){
				$kpi_total = null;
			}
			else if($kpi_value < $kpi_target){
				$kpi_total = $kpi_weight;
			}
			else{
				$kpi_total = 0;
			}
		}
		else if($type_kpi == 'Terbalik_2'){
			if($kpi_target == 0){
				if($kpi_target < $kpi_value){
					$kpi_total = 0;
				}
				else if($kpi_target == 0 && $kpi_value == 0){
					$tot = (100 * $kpi_weight)/100;
					$kpi_total = round($tot,2);
				}
				else{
					$kpi_total = null;
				}						
			}
			else if($kpi_target >= $kpi_value){
				$tot = (100 * $kpi_weight)/100;
				$kpi_total = round($tot,2);
			}						
			else{
				$tot_kpi = ($kpi_target / $kpi_value)*100 ;
				$tot = ($tot_kpi * $kpi_weight)/100;
				$kpi_total = round($tot,2);
			}
		}
        return $kpi_total;
    }
	
	public function get_employee() {
        $result = QuantitativeKpi::get_employee();
        return response()->json($result);
    }
	
	public function get_period() {
        $result = QuantitativeKpi::get_period();
        return response()->json($result);
    }
	
	public function get_category() {
        $result = QuantitativeKpi::get_category();
        return response()->json($result);
    }
	
	public function get_type() {
        $result = QuantitativeKpi::get_type();
        return response()->json($result);
    }
	
	protected function validateItem(Request $request) {
		if ($request->itemkpi) {
			$arr_form_validate = [];
			$arr_msg_form_validate = [];
			$i = 1;
			foreach($request->post('itemkpi') as $key=>$val){
				$validate_emprequest = ['itemkpi.'.$key.'.weight' => 'required',
									 'itemkpi.'.$key.'.target' => 'required',
									 'itemkpi.'.$key.'.kpi_value' => 'required',
				];
				$validate_msg_emprequest = ['itemkpi.'.$key.'.weight.required' => 'Bobot field is required',
										  'itemkpi.'.$key.'.target.required' => 'Target field is required',
										  'itemkpi.'.$key.'.kpi_value.required' => 'Aktual field is required'
										  ];
				$i++;					  
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
			}
			$request->validate($arr_form_validate, $arr_msg_form_validate);
		}
	}
	
	protected function update(Request $request) {
        $this->validateItem($request);
		try{
            DB::beginTransaction();
			
	//	dd($request->all());
		$listIdItem    = [];
        $idItem      = [];
		if(KpiDetail::where('id_kpi_header', $request->id_kpi_header)->first() != null){
			$listIdItem = KpiDetail::where('id_kpi_header', $request->id_kpi_header)->where('id_company', session('id_company'))->get()->pluck('id_kpi_detail')->all();
		}
		if ($request->itemkpi) {
			foreach ($request->itemkpi as $key => $value) {
				if ($value['id_kpi_detail'] == "") {
					KpiDetail::create(array(
						'id_kpi_header' => $request->id_kpi_header,
						'id_kpi_category' =>  $value['id_kpi_category'],
						'description' =>  $value['kpi_desc'],
						'id_kpi_type' =>  $value['id_kpi_type'],
						'weight_prosentase' =>  $value['weight'],
						'kpi_target_value' =>  $value['target'],
						'kpi_value' =>  $value['kpi_value'],						
						'status' =>  'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					$idItem[] = $value['id_kpi_detail'];
					KpiDetail::where('id_kpi_detail', $value['id_kpi_detail'])->update(array(
						'id_kpi_category' =>  $value['id_kpi_category'],
						'description' =>  $value['kpi_desc'],
						'id_kpi_type' =>  $value['id_kpi_type'],
						'weight_prosentase' =>  $value['weight'],
						'kpi_target_value' =>  $value['target'],
						'kpi_value' =>  $value['kpi_value'],						
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		$diff = array_diff($listIdItem, $idItem);
		if(count($diff) > 0){
			foreach ($diff as $key => $value) { 
				KpiDetail::where('id_kpi_detail', $value)->delete();
			}
		}

		$total=null;
		$prog=[];
		if ($request->itemkpi) {
			foreach ($request->itemkpi as $key => $val_total) {
				$type_kpi = QuantitativeKpi::get_name_type($val_total['id_kpi_type']);
					if($type_kpi[0]->code == 'Lurus' || $type_kpi[0]->code == 'Progressif'){
						if($type_kpi[0]->code == 'Progressif'){
							$prog[] = 'Progressif';
						}
						if($val_total['kpi_value'] == null || $val_total['target'] == null){
							$kpi_total = null;
						}
						else if($val_total['target'] == 0){
							$kpi_total = 0;
						}
						else{
							$kpi_subtotal = @($val_total['kpi_value'] / $val_total['target']) * 100;
							if($kpi_subtotal > 100){
								$kpi_total = (100 * $val_total['weight'])/100;
							}
							else{	
								$kpi_total = ($kpi_subtotal * $val_total['weight']) / 100;
							}					
						}		
						$total +=  round($kpi_total,2);
					}
					else if($type_kpi[0]->code == 'Terbalik'){
						$kpi_subtotal = @(1-($val_total['kpi_value'] / $val_total['target']))*100 ;
								if($kpi_subtotal < 0){
									$kpi_total = 0;
								}
								else if($val_total['kpi_value'] == null || $val_total['target'] == null){
									$kpi_total = null;
								}
								else if($kpi_subtotal >= 0){
									$kpi_total = ($kpi_subtotal * $val_total['weight'])/100;
								}		
						$total +=  round($kpi_total,2);
					}
					else if($type_kpi[0]->code == 'Hit_Miss_Lurus'){
						if($val_total['kpi_value'] == null || $val_total['target'] == null){
							$kpi_total = null;
						}
						else if($val_total['kpi_value'] >= $val_total['target']){
							$kpi_total = $val_total['weight'];
						}
						else{
							$kpi_total = 0;
						}
						$total +=  round($kpi_total,2);	
					}
					else if($type_kpi[0]->code == 'Hit_Miss_Terbalik'){
						if($val_total['kpi_value'] == null || $val_total['target'] == null){
							$kpi_total = null;
						}
						else if($val_total['kpi_value'] < $val_total['target']){
							$kpi_total = $val_total['weight'];
						}
						else{
							$kpi_total = 0;
						}
						$total +=  round($kpi_total,2);
					}
					else if($type_kpi[0]->code == 'Terbalik_2'){
						if($val_total['target'] == 0){
							if($val_total['target'] < $val_total['kpi_value']){
								$kpi_total = 0;
							}
							else if($val_total['target'] == 0 && $val_total['kpi_value'] == 0){
								$tot = (100 * $val_total['weight'])/100;
								$kpi_total = round($tot,2);
							}
							else{
								$kpi_total = null;
							}						
						}
						else if($val_total['target'] >= $val_total['kpi_value']){
							$tot = (100 * $val_total['weight'])/100;
							$kpi_total = round($tot,2);
						}						
						else{
							$tot_kpi = ($val_total['target'] / $val_total['kpi_value'])*100 ;
							$tot = ($tot_kpi * $val_total['weight'])/100;
							$kpi_total = round($tot,2);
						}
						$total +=  round($kpi_total,2);
					}										
			
				$sum[] = $val_total['weight'];
			}
			$total_sum = array_sum($sum);
			if($total_sum > 100){
				$showerr = 'Bobot must not be greater than 100';
				throw new \Exception($showerr);
			}
		}
		
		if(count($prog) > 0){
			$get_small = KpiHeader::where('id_kpi_group',$request->id_kpi_group)->get();
			$s = $get_small->pluck('kpi_month')->all();
			$sx = explode("-",min($s));
			$number_s = (int) date('n',strtotime(min($s)));
			$prog_first = KpiHeader::where('id_kpi_group',$request->id_kpi_group)->where('id_kpi_header',$request->id_kpi_header)->first();
			$number_month = (int) date('n',strtotime($prog_first['kpi_month']));
			$main_header = QuantitativeKpi::get_prog_detail($request->id_kpi_header);
		//	$v[] = $main_header;
			for($i=$number_month;$i>=$number_s;$i--){
				if($i < 10){
					$j = '0'.$i;
				}
				else{
					$j = $i;
				}
				
				$x =	KpiHeader::where('id_kpi_group',$request->id_kpi_group)->where('kpi_month',$sx[0].'-'.$j.'-01')->first();
				$y =	QuantitativeKpi::get_prog_detail($x->id_kpi_header);
				
				if(count($y) > 0){	
						KpiDetail::where('id_kpi_detail', $y[0]->id_kpi_detail)->update(array(
							'id_kpi_category' =>  $main_header[0]->id_kpi_category,
							'description' =>  $main_header[0]->description,
							'id_kpi_type' =>  $main_header[0]->id_kpi_type,
							'weight_prosentase' =>  $main_header[0]->weight_prosentase,
							'kpi_target_value' =>  $main_header[0]->kpi_target_value,
							'kpi_value' =>  $main_header[0]->kpi_value,						
							'status' =>  'A',
							'id_company' => session('id_company'),
							'updated_by' => session('id_user'),
						));
				}
				else{
					KpiDetail::create(array(
						'id_kpi_header' => $x->id_kpi_header,
						'id_kpi_category' =>  $main_header[0]->id_kpi_category,
						'description' =>  $main_header[0]->description,
						'id_kpi_type' =>  $main_header[0]->id_kpi_type,
						'weight_prosentase' =>  $main_header[0]->weight_prosentase,
						'kpi_target_value' =>  $main_header[0]->kpi_target_value,
						'kpi_value' =>  $main_header[0]->kpi_value,						
						'status' =>  'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				}
			}
			
			$new = new Request();
			$new->id_kpi_group = $request->id_kpi_group;
			$this->calmonthly($new);
			$this->calyearly($new);
		}
		
		KpiHeader::where('id_kpi_header', $request->id_kpi_header)->update(array(
			'subtotal_kpi' => $total,
			'updated_by' => session('id_user'),
		));
	//	dd($v);
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Item KPI Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Save Item KPI !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function calyearly(Request $request) {
		DB::beginTransaction();
		try {
        $result = QuantitativeKpi::calyearly($request->id_kpi_group);
		$count=[];
		$total=0;
		foreach($result as $key=>$cal){
			if($cal->subtotal_kpi != null){
				$count[] = $cal;
				$total +=  $cal->subtotal_kpi;
			}
		}
		if(count($count) > 0){
			$cal_total = round($total / count($count), 2);
		}
		else{
			$cal_total = null;
		}
		QuantitativeKpi::where('id_kpi_group', $request->id_kpi_group)->update(array(
			'average_prosentase' => $cal_total,
			'updated_by' => session('id_user'),
		));
		 DB::commit();   
            return response(['status' => 'true', 'message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function calmonthly(Request $request) {
		DB::beginTransaction();
		try {
        $result = QuantitativeKpi::calyearly($request->id_kpi_group);
		foreach($result as $key=>$year){
			$res = QuantitativeKpi::calmonthly($year->id_kpi_header);
			$total=null;
			foreach($res as $key=>$val_total){
				$type_kpi = QuantitativeKpi::get_name_type($val_total['id_kpi_type']);
			if($type_kpi[0]->code == 'Lurus' || $type_kpi[0]->code == 'Progressif'){						
						if(is_null($val_total['kpi_value'])|| is_null($val_total['target'])){
							continue;
						}
						else if($val_total['target'] == 0){
							$kpi_total = 0;
						}
						else{
							$kpi_subtotal = @($val_total['kpi_value'] / $val_total['target']) * 100;
							if($kpi_subtotal > 100){
								$kpi_total = (100 * $val_total['weight'])/100;
							}
							else{	
								$kpi_total = ($kpi_subtotal * $val_total['weight']) / 100;
							}					
						}		
						$total +=  round($kpi_total,2);
					}				
					else if($type_kpi[0]->code == 'Terbalik'){
						$kpi_subtotal = @(1-($val_total['kpi_value'] / $val_total['target']))*100 ;
								if($kpi_subtotal < 0){
									$kpi_total = 0;
								}
								else if(is_null($val_total['kpi_value']) || is_null($val_total['target'])){
									continue;
								}
								else if($kpi_subtotal >= 0){
									$kpi_total = ($kpi_subtotal * $val_total['weight'])/100;
								}		
						$total +=  round($kpi_total,2);
					}
					else if($type_kpi[0]->code == 'Hit_Miss_Lurus'){
						if(is_null($val_total['kpi_value']) || is_null($val_total['target'])){
							continue;
						}
						else if($val_total['kpi_value'] >= $val_total['target']){
							$kpi_total = $val_total['weight'];
						}
						else{
							$kpi_total = 0;
						}
						$total +=  round($kpi_total,2);	
					}
					else if($type_kpi[0]->code == 'Hit_Miss_Terbalik'){
						if(is_null($val_total['kpi_value']) || is_null($val_total['target'])){
							continue;
						}
						else if($val_total['kpi_value'] < $val_total['target']){
							$kpi_total = $val_total['weight'];
						}
						else{
							$kpi_total = 0;
						}
						$total +=  round($kpi_total,2);
					}
					else if($type_kpi[0]->code == 'Terbalik_2'){
						if(is_null($val_total['kpi_value']) || is_null($val_total['target'])){
							continue;
						}
						else if($val_total['target'] == 0){
							if($val_total['target'] < $val_total['kpi_value']){
								$kpi_total = 0;
							}
							else if($val_total['target'] == 0 && $val_total['kpi_value'] == 0){
								$tot = (100 * $val_total['weight'])/100;
								$kpi_total = round($tot,2);
							}
							else{
								continue;
							}						
						}
						else if($val_total['target'] >= $val_total['kpi_value']){
							$tot = (100 * $val_total['weight'])/100;
							$kpi_total = round($tot,2);
						}						
						else{
							$tot_kpi = ($val_total['target'] / $val_total['kpi_value'])*100 ;
							$tot = ($tot_kpi * $val_total['weight'])/100;
							$kpi_total = round($tot,2);
						}
						$total +=  round($kpi_total,2);
					}				

			}
			KpiHeader::where('id_kpi_header', $year->id_kpi_header)->update(array(
				'subtotal_kpi' => $total,
				'updated_by' => session('id_user'),
			));	
		}
		 DB::commit();   
            return response(['status' => 'true', 'message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
/*	public function inactive($id) {
		$id_group = QuantitativeKpi::where('id_kpi_group',$id)->first();
		$update_status = array(
            'status' => 'I',
        );
        QuantitativeKpi::where('id_kpi_group',$id)->update($update_status);
        KpiHeader::where('id_kpi_group',$id_group->id_kpi_group)->update($update_status);
        KpiDetail::where('id_kpi_group',$id_group->id_kpi_group)->update($update_status);
    }
*/

	public function get_inactive(Request $request) {
        $result = QuantitativeKpi::where('id_kpi_group',$request->id_kpi_group)->first();
		$emp = Employee::where('id_employee',$result->id_employee_appraisers)->first();
		$result['name'] = $emp->name;
        return response()->json($result);
    }
	
	public function get_emp_inactive(Request $request) {
		$data = [
				'id_employee' => $request->id_employee,
				'id_kpi_group' => $request->id_kpi_group,
			];
        $result = QuantitativeKpi::get_emp_inactive($data);
        return response()->json($result);
    }
	
	protected function update_inactive(Request $request) {
		try{
			DB::beginTransaction();
			if($request->id_employee_group != null){
				$cek_in = QuantitativeKpi::where('id_kpi_group', $request->id_kpi_group_inactive)->first();
				if($request->status == 'I' && $cek_in->status == 'A'){
					QuantitativeKpi::where('id_kpi_group', $request->id_kpi_group_inactive)->update(array(
						'status' => $request->status,
						'updated_by' => session('id_user'),
					));
					$cek_header = KpiHeader::where('id_kpi_group', $request->id_employee_group)->where('status', 'A')->get();
					if($cek_header->count() > 0 ){
						KpiHeader::where('id_kpi_group', $request->id_employee_group)->where('status', 'A')->update(array(
							'status' => 'I',
							'updated_by' => session('id_user'),
						));
					}				
					KpiHeader::where('id_kpi_group', $request->id_kpi_group_inactive)->update(array(
						'id_kpi_group' => $request->id_employee_group,
						'updated_by' => session('id_user'),
					));
				}
			}
			else{
				throw new \Exception();
			}
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Status Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Update !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function upload_review(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        $allFile    = [];
        $filePath   = 'public/';
        try {
            $dataReturn = [];
            $mappingNotAvailable = [];
            $bawahanNotAvailable = [];
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
                    $storageFile   = Storage::putFileAs($filePath, $request->attachment, $newFileName);
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
 						
 						$allNikAtasan		= [];
 						$allNikKaryawan		= [];
 						$masterTypeKpi 		= [];
 						$masterCategoryKpi 	= [];
 						$periodByAtasan 	= [];
 						$periodInExcel 		= [];
 						$dataKpiKaryawanByPeriodAndCategoryAndType 	= [];

                        foreach ($sheetData as $k => $row) {
                    	   if($k > 0){
                            	$nikAtasan 			= $row[0] ?? null;
                            	$namaAtasan 		= $row[1] ?? null;
                            	$periodeCode 	    = $row[2] ?? null;
                            	$typeKpi			= $row[3] ? strtolower(trim($row[3])) : null;
                            	$nikKaryawan 		= $row[4] ?? null;
                            	$namaKaryawan 		= $row[5] ?? null;
                            	$categoryKpi 		= $row[6] ? strtolower(trim($row[6])) : null;
                            	$descKpi 			= $row[7] ?? null;

                            	$bobotJan 			= $row[8] ?? null;
                            	$targetJan 			= $row[9] ?? null;
                            	$actualJan 			= $row[10] ?? null;

                            	$bobotFeb 			= $row[11] ?? null;
                            	$targetFeb 			= $row[12] ?? null;
                            	$actualFeb 			= $row[13] ?? null;

                            	$bobotMar 			= $row[14] ?? null;
                            	$targetMar 			= $row[15] ?? null;
                            	$actualMar 			= $row[16] ?? null;

                            	$bobotApr 			= $row[17] ?? null;
                            	$targetApr 			= $row[18] ?? null;
                            	$actualApr 			= $row[19] ?? null;

                            	$bobotMei 			= $row[20] ?? null;
                            	$targetMei 			= $row[21] ?? null;
                            	$actualMei 			= $row[22] ?? null;

                            	$bobotJun 			= $row[23] ?? null;
                            	$targetJun 			= $row[24] ?? null;
                            	$actualJun 			= $row[25] ?? null;

                            	$bobotJul 			= $row[26] ?? null;
                            	$targetJul 			= $row[27] ?? null;
                            	$actualJul 			= $row[28] ?? null;

                            	$bobotAgs 			= $row[29] ?? null;
                            	$targetAgs 			= $row[30] ?? null;
                            	$actualAgs 			= $row[31] ?? null;

                            	$bobotSep 			= $row[32] ?? null;
                            	$targetSep 			= $row[33] ?? null;
                            	$actualSep 			= $row[34] ?? null;

                            	$bobotOkt 			= $row[35] ?? null;
                            	$targetOkt 			= $row[36] ?? null;
                            	$actualOkt 			= $row[37] ?? null;

                            	$bobotNov 			= $row[38] ?? null;
                            	$targetNov 			= $row[39] ?? null;
                            	$actualNov 			= $row[40] ?? null;

                            	$bobotDes 			= $row[41] ?? null;
                            	$targetDes 			= $row[42] ?? null;
                            	$actualDes 			= $row[43] ?? null;

                            	if(!in_array($periodeCode, $periodInExcel)){
                            		$periodInExcel[] = $periodeCode;
                            	}
                                if(!is_null($nikAtasan) && !in_array($nikAtasan, $allNikAtasan)){
                                	$allNikAtasan[] = $nikAtasan;
                                	$periodByAtasan[(string)$nikAtasan] = [];
                                }

                                if(!is_null($nikKaryawan) && !in_array($nikKaryawan, $allNikKaryawan)){
                                	$allNikKaryawan[] = $nikKaryawan;
                                }

                                if(!is_null($nikKaryawan)){
                                	//utk menampung sementara data dari excel ke dalam array

                                	if(!in_array($periodeCode, $periodByAtasan)){
                                		$periodByAtasan[(string)$nikAtasan][] = $periodeCode;
                                	}
                                	
                                	$dataKpiKaryawanByPeriodAndCategoryAndType[$nikKaryawan][$periodeCode][] = [
                                		'period'		=> $periodeCode,
                                		'period_start'	=> null,
                                		'period_end'	=> null,
                                    	'nik_karyawan'  => (string)$nikKaryawan,
                                    	'nik_atasan'  	=> (string)$nikAtasan,
                                    	'category'  	=> $categoryKpi,
                                    	'type'  		=> $typeKpi,
                                    	'description'	=> $descKpi,
                                    	'detail'		=> [
                                    		['month' => '01', 'kpi_value' => $actualJan, 'weight_prosentase' => $bobotJan, 'kpi_target_value' => $targetJan],
                                    		['month' => '02', 'kpi_value' => $actualFeb, 'weight_prosentase' => $bobotFeb, 'kpi_target_value' => $targetFeb],
                                    		['month' => '03', 'kpi_value' => $actualMar, 'weight_prosentase' => $bobotMar, 'kpi_target_value' => $targetMar],
                                    		['month' => '04', 'kpi_value' => $actualApr, 'weight_prosentase' => $bobotApr, 'kpi_target_value' => $targetApr],
                                    		['month' => '05', 'kpi_value' => $actualMei, 'weight_prosentase' => $bobotMei, 'kpi_target_value' => $targetMei],
                                    		['month' => '06', 'kpi_value' => $actualJun, 'weight_prosentase' => $bobotJun, 'kpi_target_value' => $targetJun],
                                    		['month' => '07', 'kpi_value' => $actualJul, 'weight_prosentase' => $bobotJul, 'kpi_target_value' => $targetJul],
                                    		['month' => '08', 'kpi_value' => $actualAgs, 'weight_prosentase' => $bobotAgs, 'kpi_target_value' => $targetAgs],
                                    		['month' => '09', 'kpi_value' => $actualSep, 'weight_prosentase' => $bobotSep, 'kpi_target_value' => $targetSep],
                                    		['month' => '10', 'kpi_value' => $actualOkt, 'weight_prosentase' => $bobotOkt, 'kpi_target_value' => $targetOkt],
                                    		['month' => '11', 'kpi_value' => $actualNov, 'weight_prosentase' => $bobotNov, 'kpi_target_value' => $targetNov],
                                    		['month' => '12', 'kpi_value' => $actualDes, 'weight_prosentase' => $bobotDes, 'kpi_target_value' => $targetDes],
                                    	],
                                    ];
                                }
                            }
                        }

                        $idCategoryOther = [];

                        // Get master Type KPI
        				$getTypeKpi = DB::table('master_general_data')
        					->select('id_general_data', 'code', 'id_company')
        				//	->where('id_general_type', 16)
        					->where(function ($query){
			                    $query->where(DB::raw('lower(code)'), '=', 'lurus');
			                    $query->orWhere(DB::raw('lower(code)'), '=', 'terbalik');
			                    $query->orWhere(DB::raw('lower(code)'), '=', 'hit_miss_lurus');
			                    $query->orWhere(DB::raw('lower(code)'), '=', 'hit_miss_terbalik');
			                    $query->orWhere(DB::raw('lower(code)'), '=', 'terbalik_2');
			                    $query->orWhere(DB::raw('lower(code)'), '=', 'progressif');
			                })
        					->get();
        				foreach ($getTypeKpi as $key => $val) {
        					$codeTypeKpi = strtolower($val->code);
        					//menampung get data dari db ke dalam array agar tidak banyak proses get ke db tiap record
        					$masterTypeKpi[$codeTypeKpi][$val->id_company] = ['id' => $val->id_general_data, 'code' => $codeTypeKpi, 'id_company' => $val->id_company];
        				}
        				// Get master Category KPI
        				$getCategoryKpi = DB::table('master_kpi_category')
        					->select('id_kpi_category', 'description', 'id_company')
        					->get();
        				foreach ($getCategoryKpi as $key => $val) {
        					$descCategoryKpi = strtolower($val->description);
        					//menampung get data dari db ke dalam array agar tidak banyak proses get ke db tiap record
        					if($descCategoryKpi == 'other'){
        						$idCategoryOther[] = $val->id_kpi_category;
        					}
        					$masterCategoryKpi[$descCategoryKpi][$val->id_company] = ['id' => $val->id_kpi_category, 'description' => $descCategoryKpi, 'id_company' => $val->id_company];
        				}
        				// Get master Period KPI
        				$getPeriodKpi = DB::table('master_period')
        					->select('id_period', 'period_code', 'id_company', 'start_date', 'end_date')
        					->get();
        				$periodeTerpilih = '';
        				foreach ($getPeriodKpi as $key => $val) {
        					$code = $val->period_code;
        					if($periodInExcel[0] == $code && $val->id_company == session('id_company')){
        						$periodeTerpilih = $val->id_period;
        					}
        					//menampung get data dari db ke dalam array agar tidak banyak proses get ke db tiap record
        					$masterPeriodKpi[$code][$val->id_company] = ['id'=>$val->id_period, 'code'=>$code, 'id_company'=>$val->id_company, 'start'=>$val->start_date, 'end'=>$val->end_date];
        				}
        				// Get Atasan & Karyawan
        				$getKaryawanDanAtasan = DB::table('hr_employee')
        					->select('id_employee', 'name', 'nik_employee', 'id_company', 'join_date')
        					->where('status', 'A')
        					->where(function($query) use ($allNikAtasan,$allNikKaryawan){
        						$query->whereIn('nik_employee', $allNikAtasan);
        						$query->orWhereIn('nik_employee', $allNikKaryawan);
				            })
        					->get();

        				$allNikAfterGet = $getKaryawanDanAtasan->pluck('nik_employee')->all();
        				$diffNikAtasan = collect($allNikAtasan)->diff($allNikAfterGet);
        				$diffNikKaryawan = collect($allNikKaryawan)->diff($allNikAfterGet);

        				$notifError = '';
                        if(count($diffNikAtasan) > 0){
                            $notifError .= "NIK Atasan Not Found : ".$diffNikAtasan->implode(', ')." \n";
                        }
                        if(count($diffNikKaryawan) > 0){
                            $notifError .= "NIK Karyawan Not Found : ".$diffNikKaryawan->implode(', ')." \n";
                        }
                        if($notifError != ''){
                            DB::rollBack();
                            Storage::disk('local')->delete($filePath.$file);
                            return response(['status' => 'false_other', 'message' => $notifError, 'data' => null]);
                        }


        				//ubah key array yang awalnya berupa sequence 0,1,2,3 menjadi key berupa NIK nya
        				//$getKaryawanDanAtasan[BCP000001] = [....]
        				$getKaryawanDanAtasan = $getKaryawanDanAtasan->keyBy(function ($item) {
						    return $item->nik_employee;
						});
   						
        				//Ubah Mappingan Atasan dari hasil fungsi berikut :
        				$mappingAtasan = [];
        				$getMappingAtasan = DB::table(DB::raw("sp_funct_pa_mapping (".$periodeTerpilih.", ".session('id_company').") sfpm"))->get();
        				foreach ($getMappingAtasan as $k => $val) {
        					$mappingAtasan[$val->id_employee][$periodeTerpilih] = [
        						'id_kpi_group' => $val->id_kpi_group,
        						'id_employee_appraisers' => $val->id_employee_appraisers,
        						'atasan' => $val->atasan,
        						'bawahan' => $val->bawahan,
        					];
        				}

   						foreach ($allNikAtasan as $k => $thisNikAtasan) {
   							//generate KPI dulu agar pasangan atasan dan bawahan sesuai hierarchy
   							$periodCodeByAtasan = $periodByAtasan[$thisNikAtasan];
   							$idCompanyAtasan_ = $getKaryawanDanAtasan[$thisNikAtasan]->id_company;
   							$idEmployeeAtasan_ = $getKaryawanDanAtasan[$thisNikAtasan]->id_employee;

   							if($idCompanyAtasan_ == session('id_company')){
   								foreach ($periodCodeByAtasan as $key => $codePeriod) {
									$idPeriod_ = $masterPeriodKpi[$codePeriod][$idCompanyAtasan_]['id'];
									$paramGenerate = [
										'id_employee' => $idEmployeeAtasan_,
										'period' => $idPeriod_,
										'id_company' => $idCompanyAtasan_,
										'id_user' => session('id_user'),
									];
						        	$result = QuantitativeKpi::generate_kpi($paramGenerate);
	   							}
   							}
   						}

        				foreach ($dataKpiKaryawanByPeriodAndCategoryAndType as $nik => $contentPeriod) {
        					foreach ($contentPeriod as $period => $arrData) {
        						foreach ($arrData as $k => $val) {
        							$idCompanyAtasan = $getKaryawanDanAtasan[$val['nik_atasan']]->id_company;
        							$idCompanyKaryawan = $getKaryawanDanAtasan[$val['nik_karyawan']]->id_company;
        							$idEmployeeAtasan = $getKaryawanDanAtasan[$val['nik_atasan']]->id_employee;
        							$idEmployeeKaryawan = $getKaryawanDanAtasan[$val['nik_karyawan']]->id_employee;
        							$joinDateKaryawan = $getKaryawanDanAtasan[$val['nik_karyawan']]->join_date;

        							$idCategory = $masterCategoryKpi[$val['category']][$idCompanyAtasan]['id'];
        							$idType = $masterTypeKpi[$val['type']][$idCompanyAtasan]['id'];
        							$idPeriod = $masterPeriodKpi[$val['period']][$idCompanyAtasan]['id'];
        							$periodStart = $masterPeriodKpi[$val['period']][$idCompanyAtasan]['start'];
        							$periodEnd = $masterPeriodKpi[$val['period']][$idCompanyAtasan]['end'];

        							//penambahan parameter setara dgn type,category,period,detail sesuai NIK karywn
	        						$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['id_atasan'] = $idEmployeeAtasan;
	        						$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['id_karyawan'] = $idEmployeeKaryawan;

	        						$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['id_category'] = $idCategory;
	        						$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['id_type'] = $idType;

	        						$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['id_period'] = $idPeriod;
	        						$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['period_start'] = $periodStart;
	        						$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['period_end'] = $periodEnd;

	        						if($idCompanyAtasan != session('id_company')){
	        							continue;
	        						}

	        						if(!array_key_exists($idEmployeeKaryawan, $mappingAtasan)){
										$bawahanNotAvailable[] = [
        									'atasan' => $val['nik_atasan'], 'bawahan' => $val['nik_karyawan']
        								];
        								continue;
	        						}
	        						$idKpiGroupByMappingFunction = $mappingAtasan[$idEmployeeKaryawan][$periodeTerpilih]['id_kpi_group'];

	        						$getKpiGroup = QuantitativeKpi::where('id_kpi_group', $idKpiGroupByMappingFunction)
	        							->where('id_employee', $idEmployeeKaryawan)
	        							->first();

	        						// $getKpiGroup = QuantitativeKpi::where('id_employee', $idEmployeeKaryawan)
	        						// 	->where('id_employee_appraisers', $idEmployeeAtasan)
	        						// 	->where('id_period', $idPeriod)
	        						// 	->first();

        							if(!$getKpiGroup){
        								//Jika pasangan Atasan dan Bawahan dari excel tidak ada di generate sistem atau hasil dari hierarchy.
        								$mappingNotAvailable[] = [
        									'atasan' => $val['nik_atasan'], 'bawahan' => $val['nik_karyawan']
        								];
        								continue;
        								// $dataGroupKpi = [
        								// 	'id_employee' => $idEmployeeKaryawan,
        								// 	'start_date' => $periodStart,
        								// 	'end_date' => $periodEnd,
        								// 	'status' => 'A',
        								// 	'id_company' => $idCompanyAtasan,
        								// 	'creation_date' => date('Y-m-d H:i:s'),
        								// 	'created_by' => session('id_user'),
        								// 	'id_period' => $idPeriod,
        								// 	'kpi_class' => 'PA',
        								// 	'id_employee_appraisers' => $idEmployeeAtasan
        								// ];
        								// $createGroupKpi = QuantitativeKpi::create($dataGroupKpi);

										// $yearPeriod = date('Y',strtotime($periodStart));
										// $yearJoin = date('Y',strtotime($joinDateKaryawan));
										// $startGenerate = ($yearPeriod == $yearJoin) ? $joinDateKaryawan : $periodStart;
										// $idKpiGroup = $createGroupKpi->id_kpi_group;

										// $dataGenerate = [
										// 	'id_kpi_group' => $idKpiGroup,
										// 	'start_date' => $startGenerate,
										// 	'id_company' => $idCompanyAtasan,
										// 	'id_user' => session('id_user'),
										// ];
										// $generateDetail = QuantitativeKpi::generate_detail($dataGenerate);
										// foreach ($generateDetail as $key => $valGenerate) {
										// 	$dataKpiHeader = [
										// 		'id_kpi_group' => $valGenerate->id_kpi_group,
										// 		'kpi_month' => $valGenerate->kpi_month,
										// 		'notes' => $valGenerate->notes,
										// 		'subtotal_kpi' => $valGenerate->subtotal_kpi,
										// 		'id_company' => $idCompanyAtasan,
										// 		'created_by' => session('id_user'),
										// 	];
										// 	$createKpiHeader =	KpiHeader::create($dataKpiHeader);  
										// }
        							} else {
        								if($getKpiGroup->submitted == true){
	        								continue;
        								}
										$idKpiGroup = $getKpiGroup->id_kpi_group;
        								$getKpiHeader = KpiHeader::where('id_kpi_group', $idKpiGroup)->where('id_company', $idCompanyAtasan)->get();
																			
        							//	if($getKpiHeader->count() < 1){
											$yearPeriod = date('Y',strtotime($periodStart));
											$nik_date = Employee::where('nik_employee', $val['nik_karyawan'])->get();
											$date_min = [];
											foreach($nik_date as $val_date){
												$year_re = date('Y',strtotime($val_date->join_date));
												if($yearPeriod == $year_re){
													$date_min[] = $val_date->join_date;
												}				
											}    
										//	$yearJoin = date('Y',strtotime(min($date_min)));
											if(count($date_min) > 0){
												$startGenerate =  min($date_min);
											}
											else{
												$startGenerate =  $periodStart;
											}
										//	$startGenerate = ($yearPeriod == $yearJoin) ? min($date_min) : $periodStart;
											$dataGenerate = [
												'id_kpi_group' => $idKpiGroup,
												'start_date' => $startGenerate,
												'id_company' => $idCompanyAtasan,
												'id_user' => session('id_user'),
											];
											$generateDetail = QuantitativeKpi::generate_detail($dataGenerate);
											if(count($generateDetail) > 0){
												foreach ($generateDetail as $key => $valGenerate) {
													$dataKpiHeader = [
														'id_kpi_group' => $valGenerate->id_kpi_group,
														'kpi_month' => $valGenerate->kpi_month,
														'notes' => $valGenerate->notes,
														'subtotal_kpi' => $valGenerate->subtotal_kpi,
														'id_company' => $idCompanyAtasan,
														'created_by' => session('id_user'),
													];
													$createKpiHeader =	KpiHeader::create($dataKpiHeader);  
												}
											}
        							//	}
        							}
        							foreach ($val['detail'] as $k_detail => $val_detail) {
	        							$monthDetail = $val_detail['month'];
	        							$yearPeriod = explode('-', $periodStart)[0];
	        							$newMonthDetail = $yearPeriod.'-'.$monthDetail.'-01';
	        							$dataKpiKaryawanByPeriodAndCategoryAndType[$nik][$period][$k]['detail'][$k_detail]['month'] = $newMonthDetail;

        								$getKpiHeader = KpiHeader::where('kpi_month', $newMonthDetail)->where('id_kpi_group', $idKpiGroup)->first();
        								if($getKpiHeader){
        									$getKpiDetail = KpiDetail::where('id_kpi_category', $idCategory)->where('id_kpi_type', $idType)->where('id_kpi_header', $getKpiHeader->id_kpi_header)->first();

        									$descOther = strtolower(trim($val['description']));
        									$getKpiDetailOther = KpiDetail::where('id_kpi_category', $idCategory)->where('id_kpi_type', $idType)->where('id_kpi_header', $getKpiHeader->id_kpi_header)->where(DB::raw('lower(description)'), '=', $descOther)->first();
        									
    										$dataKpiDetail = [
    											'id_kpi_header' => $getKpiHeader->id_kpi_header,
    											'id_kpi_category' => $idCategory,
    											'id_kpi_type' => $idType,
    											'description' => $val['description'],
    											'id_company' => $idCompanyAtasan,
    										];

    										if(in_array($idCategory,$idCategoryOther)){
    											if($getKpiDetailOther){
	        										if(!is_null($val_detail['kpi_value'])){
	        											$dataKpiDetail['kpi_value'] = $val_detail['kpi_value'];
	        										}
	        										if(!is_null($val_detail['weight_prosentase'])){
	        											$dataKpiDetail['weight_prosentase'] = $val_detail['weight_prosentase'];
	        										}
	        										if(!is_null($val_detail['kpi_target_value'])){
	        											$dataKpiDetail['kpi_target_value'] = $val_detail['kpi_target_value'];
	        										}
	        										$dataKpiDetail['updated_by'] = session('id_user');
	        										$updateKpiDetail = KpiDetail::where('id_kpi_category', $idCategory)->where('id_kpi_type', $idType)->where('id_kpi_header', $getKpiHeader->id_kpi_header)->where(DB::raw('lower(description)'), '=', $descOther)->update($dataKpiDetail);
	        									}
	        									else {
	        										$dataKpiDetail['kpi_value'] = $val_detail['kpi_value'];
	        										$dataKpiDetail['weight_prosentase'] = $val_detail['weight_prosentase'];
	        										$dataKpiDetail['kpi_target_value'] = $val_detail['kpi_target_value'];
	        										$dataKpiDetail['created_by'] = session('id_user');
	        										if(!is_null($val_detail['weight_prosentase']) && !is_null($val_detail['kpi_target_value'])){
	        											$insertKpiDetail = KpiDetail::insert($dataKpiDetail);
	        										}
	        									}
    										} else {
	        									if($getKpiDetail){
	        										if(!is_null($val_detail['kpi_value'])){
	        											$dataKpiDetail['kpi_value'] = $val_detail['kpi_value'];
	        										}
	        										if(!is_null($val_detail['weight_prosentase'])){
	        											$dataKpiDetail['weight_prosentase'] = $val_detail['weight_prosentase'];
	        										}
	        										if(!is_null($val_detail['kpi_target_value'])){
	        											$dataKpiDetail['kpi_target_value'] = $val_detail['kpi_target_value'];
	        										}
	        										$dataKpiDetail['updated_by'] = session('id_user');
	        										$updateKpiDetail = KpiDetail::where('id_kpi_category', $idCategory)->where('id_kpi_type', $idType)->where('id_kpi_header', $getKpiHeader->id_kpi_header)->update($dataKpiDetail);
	        									}
	        									else {
	        										$dataKpiDetail['kpi_value'] = $val_detail['kpi_value'];
	        										$dataKpiDetail['weight_prosentase'] = $val_detail['weight_prosentase'];
	        										$dataKpiDetail['kpi_target_value'] = $val_detail['kpi_target_value'];
	        										$dataKpiDetail['created_by'] = session('id_user');
	        										if(!is_null($val_detail['weight_prosentase']) && !is_null($val_detail['kpi_target_value'])){
	        											$insertKpiDetail = KpiDetail::insert($dataKpiDetail);
	        										}
	        									}
    										}
        								}
	        						}
        						}
        					}
        				}
                        Storage::disk('local')->delete($filePath.$file);
                    }
                }
            }
            if(count($mappingNotAvailable) > 0){
            	$showError = "Terdapat mapping yang belum tergenerate. \n (Atasan) - (Bawahan) \n";
            	foreach ($mappingNotAvailable as $k => $val) {
            		$showError.= $val['atasan']." - ".$val['bawahan']."\n";
            	}
            	return response(['status' => 'false_other', 'message' => $showError, 'data' => null]);
            }

            if(count($bawahanNotAvailable) > 0){
            	$showError = "Terdapat mapping bawahan yang tidak sesuai dengan atasan. \n (Atasan) - (Bawahan) \n";
            	foreach ($bawahanNotAvailable as $k => $val) {
            		$showError.= $val['atasan']." - ".$val['bawahan']."\n";
            	}
            	return response(['status' => 'false_other', 'message' => $showError, 'data' => null]);
            }
            DB::commit();   
            return response(['status' => 'true', 'message' => 'Upload Success', 'data' => $dataReturn]);
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

}
