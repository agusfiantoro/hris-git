<?php

namespace App\Http\Controllers\AwardDicipline\Dicipline;

use App\Models\AwardDicipline\AwardDicipline;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DiciplineController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
        	$id_employee = [];
            if(session('access_group') != 'Default_Administrator'){
                $path_menu      = 'employee/employee_setting/workdays';
                $id_branch      = [];
                $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu);
                if(count($get_ur) > 0){
                    $get_ur = $get_ur[0];
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->id_user_responsibility);
                    if(count($get_branch) > 0){
                        $id_branch        = $get_branch->pluck('id_branch')->all();
                    }
                }
                if(count($id_branch) > 0){
                    $id_employee = JobPositionDetail::whereIn('id_branch', $id_branch)->pluck('id_employee')->all();
                }
            }
            
            $data = AwardDicipline::getdata_dcp($id_employee);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_transaction . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_transaction . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('employee_name', function($row) {
                                return $row->employee_name;
                            })->addColumn('created_by_user', function($row) {
                                return $row->created_by_user;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('career_administration.award_dicipline.dicipline.index');
    }

	protected function validateDicipline(Request $request) {
		$diparams = AwardDicipline::get_dicipline_param($request->dicipline_type);
		if($diparams->code == 'verbal'){
			$request->validate([
				'id_employee' => 'required',
				'effective_date' => 'required',
				'expired_date' => 'required',
				'attachment' => 'mimes:pdf,jpg,jpeg,png|max:1024',
					], [],
					[
						'effective_date' => 'Effective Date',
			]);
		}
		else{
			$param = [
				'id_employee' => 'required',
				'effective_date' => 'required',
				// 'reference_number' => 'required',
				'attachment' => 'mimes:pdf,jpg,jpeg,png|max:1024',
			];
			$descriptionParam = [
				// 'reference_number' => 'Reference Number',
				'effective_date' => 'Effective Date',
			];
			if(in_array($diparams->code, ['SP1','SP2','SP3'])){
			//	$param['using_letter_number'] = 'required';
			//	$param['award_letter_number'] = 'required';
				$param['expired_date'] = 'required';
			//	$descriptionParam['using_letter_number'] = 'Using Letter Number';
			//	$descriptionParam['award_letter_number'] = 'Letter Number';
				$descriptionParam['expired_date'] = 'Expired Date';
			}
			else if($diparams->code == 'KPK'){
				$param['kpk_status'] = 'required';
				$descriptionParam['kpk_status'] = 'KPK Status';
			}
			$request->validate($param, [], $descriptionParam);
		}
	}	
    protected function save(Request $request) {
		$this->validateDicipline($request);
		try{
			DB::beginTransaction();			
			if($request->attachment != ""){
			//	$image_file = file_get_contents($request->attachment);
			//	$image = base64_encode($image_file);
				$nik_employee = Employee::where('id_employee', $request->id_employee)->first();
				$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
				$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
				$dir = Storage::makeDirectory('public/upload/dicipline/'.$nik_employee['nik_employee'],0775, true, true);
				$storageimage = Storage::putFileAs('public/upload/dicipline/'.$nik_employee['nik_employee'],$request->attachment,$image);
			}
			else{
				$image = NULL;
			}
			$codeid = AwardDicipline::getkode_dcp();
			$diparams = AwardDicipline::get_dicipline_param($request->dicipline_type);
	        $form_data = array(
	        //    'reference_number' => $codeid,
	            'id_employee' => $request->id_employee,
	            'transaction_type' => 'D',
	            'effective_date' => $request->effective_date,
	            'expired_date' => $request->expired_date,
	            'dicipline_type' => $request->dicipline_type,
				'description_name' => $request->description_name,
	            'reference_date' => $request->reference_date,
	            'reference_number' => $codeid,
	            'attachment_type' => NULL,
	            'attachment' => $image,
	            'remark' => $request->remark,
	            'status' => $request->status,
	            'id_company' => session('id_company'),
	            'created_by' => session('id_user'),
	        );

	        if(@$request->using_letter_number=='on'){
				if($diparams->code != 'verbal'){
					$form_data['award_letter_number'] = $request->award_letter_number;
					$form_data['is_link_with_letter_number'] = true;
				}
	        }
			if($diparams->code == 'KPK'){				
				$form_data['progress_status'] = $request->kpk_status;
			}
			$save_dicipline = AwardDicipline::create($form_data);
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Dicipline Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Dicipline !! [' . $e->getMessage() . ']']);           
        }
    }

    public function destroy($id) {
        $data = AwardDicipline::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
		$this->validateDicipline($request);
	try{
		DB::beginTransaction();	
		$codeid = AwardDicipline::getkode_dcp();
		$diparams = AwardDicipline::get_dicipline_param($request->dicipline_type);
		$form_data = array(
            'id_employee' => $request->id_employee,
            'transaction_type' => 'D',
            'effective_date' => $request->effective_date,
            'expired_date' => $request->expired_date,
            'dicipline_type' => $request->dicipline_type,
            'description_name' => $request->description_name,
            'reference_date' => $request->reference_date,
            'remark' => $request->remark,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
        );
		if($request->attachment != ""){
		//	$image_file = file_get_contents($request->attachment);
		//	$image = base64_encode($image_file);
			$nik_employee = Employee::where('id_employee', $request->id_employee)->first();
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/dicipline/'.$nik_employee['nik_employee'],0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/dicipline/'.$nik_employee['nik_employee'],$request->attachment,$image);
			$form_data['attachment_type'] = NULL;
			$form_data['attachment'] = $image;			
		}

		if(@$request->using_letter_number=='on'){
			if(in_array($diparams->code, ['SP1','SP2','SP3'])){
				$form_data['award_letter_number'] = $request->award_letter_number;
				$form_data['is_link_with_letter_number'] = true;
			}
        }

	    if($diparams->code == 'verbal'){
			$cek_dicipline = AwardDicipline::where('id_transaction',$request->hidden_id)->first();
			if($request->reference_number != $cek_dicipline['reference_number'] && $request->reference_number != ""){
				$form_data['reference_number'] = $codeid;
			}
		}
		else if($diparams->code == 'KPK'){				
			$form_data['progress_status'] = $request->kpk_status;
		}

        $update_dicipline = AwardDicipline::findOrFail($request->hidden_id)->update($form_data);
	   
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Dicipline Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Dicipline !! [' . $e->getMessage() . ']']);           
        }
    }

    public function edit($id) {
        if (request()->ajax()) {
            $data = AwardDicipline::edit_award_dicipline($id);
            return response()->json(['result' => $data]);
        }
    }
	
	public function get_employee() {
        $result = AwardDicipline::get_employee();
        return response()->json($result);
    }
	
	public function get_dicipline_type() {
        $result = AwardDicipline::get_dicipline_type();
        return response()->json($result);
    }

    public function get_company() {
        $result = AwardDicipline::get_company();
        return response()->json($result);
    }
	
	public function browse(Request $request) {
        if ($request->ajax()) {
			$data = [
				'dicipline' => $request->dicipline
			];	
		/*	$url = 'https://hris.borwita.co.id/nosurat/index.php/dicipline/apidicipline?dicipline='.$data['dicipline'];
			$response = file_get_contents($url);
			$decode = json_decode($response);
			$result = collect($decode);
		*/
			$decode = AwardDicipline::get_api_type($data['dicipline']);
			$result = collect($decode);
		//	dd($result);
            echo $result;
        }
    }

    public function checkid(Request $request) {
		if ($request->ajax()) {
        $data = [
				'dicipline' => $request->dicipline,
				'id' => $request->id
			];
		/*	$url = 'https://hris.borwita.co.id/nosurat/index.php/dicipline/apidiciplineid?dicipline='.$data['dicipline'].'&id='.$data['id'];
			$response = file_get_contents($url);			
			$result = json_decode($response);
		*/
			$result = AwardDicipline::get_api_type($data['dicipline'],$data['id']);
		//	$result = collect($decode);
		//	dd($result);
		 }
        return response()->json(['result' => $result]);
    }

}
