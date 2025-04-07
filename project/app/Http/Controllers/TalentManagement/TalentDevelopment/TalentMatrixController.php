<?php

namespace App\Http\Controllers\TalentManagement\TalentDevelopment;

use App\Models\TalentManagement\MasterTalent\MasterTalent;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TalentMatrixController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $data = MasterTalent::getdataMatrix($request->fil_name,$request->fil_type,$request->fil_period,$request->fil_pro,$request->fil_reg,$request->fil_grade,$request->fil_dept,$request->fil_principal);
			return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$onclickEdit = "get_profile(".$data->id_employee.",'".$data->nik_employee."')";
								$onclickPdf = "get_pdf(".$data->id_employee.",'".$data->nik_employee."','".$data->identification_number."')";
								$button = '&nbsp;&nbsp;<button type="button" name="profile" id="' . $data->id_employee . '"  onclick="'.$onclickEdit.'" class="btn btn-success btn-xs" title="Profile"><span class="far fa-id-card" style="font-size:18px;margin:3px;"></span></button>';
								$button .= '&nbsp;&nbsp;<button type="button" name="pdf" onclick="'.$onclickPdf.'" class="btn btn-success btn-xs" title="Export PDF"><span class="fas fa-file-pdf" style="font-size:18px;margin:3px;"></span></button>';
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('talent_management.talent_development.talent_ranking_summary.index');
    }
	
	public function modal_detail(Request $request) {
		$global_box = $request->global_box;
		$fil_name = $request->fil_name;
		$fil_type = $request->fil_type;
		$fil_period = $request->fil_period;
		$fil_pro = $request->fil_pro;
		$fil_reg = $request->fil_reg;
		$fil_grade = $request->fil_grade;
		$fil_dept = $request->fil_dept;
		$fil_principal = $request->fil_principal;
        return view('talent_management.talent_development.talent_ranking_summary.modal_detail', compact('global_box','fil_name','fil_type','fil_period','fil_pro','fil_reg','fil_grade','fil_dept','fil_principal'));
    }
	
	public function modal_talent_profile(Request $request) {
		$global_emp = $request->global_emp;
		$global_nik = $request->global_nik;
        return view('talent_management.talent_development.talent_ranking_summary.modal_profile', compact('global_emp','global_nik'));
    }
	
	public function index_modal(Request $request) {
		if ($request->ajax()) {
		$result = MasterTalent::getBoxMatrix($request->code_box,$request->fil_name,$request->fil_type,$request->fil_period,$request->fil_pro,$request->fil_reg,$request->fil_grade,$request->fil_dept,$request->fil_principal);	
		return DataTables::of($result)
					->addIndexColumn()					
					->make(true);		
		}
	}
	
	public function get_box(Request $request) {
	//	dd($request->all());
		$fil_name = $request->fil_name;
		$fil_type = $request->fil_type;
		$fil_period = $request->fil_period;
		$fil_pro = $request->fil_pro;
		$fil_reg = $request->fil_reg;
		$fil_grade = $request->fil_grade;
		$fil_dept = $request->fil_dept;
		$fil_principal = $request->fil_principal;
		
        $result = MasterTalent::get_box($fil_name,$fil_type,$fil_period,$fil_pro,$fil_reg,$fil_grade,$fil_dept,$fil_principal);
	//	dd($result);
        return response()->json($result);
    }
	
	public function filter_type() {
        $result = MasterTalent::filter_type();
	//	dd($result);
        return response()->json($result);
    }
	
	public function filter_period(Request $request) {
		$idReco = $request->idReco;
		if(!is_null($idReco)){
			$arr_reco = implode(",",$idReco);
		}
		else{
			$arr_reco = null;
		}
        $z = MasterTalent::filter_period($arr_reco);
		$result = null;
		if(count($z) > 0){
			foreach($z as $key=>$val){
				$x = explode(" - ",$val->text);
				$result[$key]['id'] = $val->id;
				$result[$key]['text'] =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));
			}
		}
		
        return response()->json($result);
    }
	
	public function filter_projected() {
        $result = MasterTalent::filter_projected();
	//	dd($result);
        return response()->json($result);
    }
	
	public function filter_region() {
        $result = MasterTalent::filter_region();
	//	dd($result);
        return response()->json($result);
    }
	public function filter_grade() {
        $result = MasterTalent::filter_grade();
	//	dd($result);
        return response()->json($result);
    }
	public function filter_dept() {
        $result = MasterTalent::filter_dept();
	//	dd($result);
        return response()->json($result);
    }
	public function filter_principal() {
        $result = MasterTalent::filter_principal();
	//	dd($result);
        return response()->json($result);
    }
	public function get_reco_name() {
        $result = MasterTalent::get_reco_name();
	//	dd($result);
        return response()->json($result);
    }
	
	
	public function gen_box_talent(Request $request) {
	//	dd($request->all());
		$request->validate([
            'reco_name' => 'required',
            'reco_date' => 'required',
                ], [],
                [
                    'reco_name' => 'Talent Reco Name',
                    'reco_date' => 'Period Date',
        ]);
		try{
				DB::beginTransaction();	
			$fil_id_reco_name = $request->reco_name;
			$fil_reco_date = $request->reco_date;
			$getRecoDetail = MasterTalent::get_reco_detail($fil_id_reco_name);
			foreach($getRecoDetail AS $key=>$val){
				$x[] = $val->id_talent_recommendation_detail;
			}
			$listIdDetail = implode(",",$x);
			$result = MasterTalent::gen_box_talent($listIdDetail,$fil_reco_date);
		//	dd($result);
			DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Generate Success']);
			} catch (\Exception $e) {
				DB::rollBack();
				Log::error($e);
				return response()->json(['status' => 'false', 'message' => 'Generate Failed [' . $e->getMessage() . ']']); 
			}	
		}
}
