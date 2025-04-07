<?php

namespace App\Http\Controllers\AwardDicipline\Award;

use App\Models\AwardDicipline\AwardDicipline;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeSetting\MasterAnnouncement;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
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

class AwardController extends Controller {

    public function index(Request $request) {
	//	dd($codeid);
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

            $data = AwardDicipline::getdata_awd($id_employee);
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
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('career_administration.award_dicipline.awards.index');
    }

    protected function save(Request $request) {
        $request->validate([
            'effective_date' => 'required',
            'attachment' => 'mimes:pdf,jpg,jpeg,png|max:1024',
                ], [],
                [
                    'effective_date' => 'Effective Date',
        ]);
	try{
		DB::beginTransaction();	
		if($request->attachment != ""){
		//	$image_file = file_get_contents($request->attachment);
		//	$image = base64_encode($image_file);
			$nik_employee = Employee::where('id_employee', $request->id_employee)->first();
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = "Award-".$rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/announcement',0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/announcement',$request->attachment,$image);
		}
		else{
			$image = NULL;
		}
		$codeid = AwardDicipline::getkode_awd();
        $form_data = array(
            'reference_number' => $codeid,
            'id_employee' => $request->id_employee,
            'transaction_type' => 'A',
            'effective_date' => $request->effective_date,
            'expired_date' => $request->expired_date,
            'award_letter_number' => $request->award_letter_number,
            'award_certificate_number' => $request->award_certificate_number,
            'description_name' => $request->description_name,
            'reference_date' => $request->reference_date,
            'attachment_type' => NULL,
            'attachment' => $image,
            'remark' => $request->remark,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
	//	dd($form_data);
        $result = AwardDicipline::create($form_data);
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Award Saved Successfully !!','result'=> $result]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Award !! [' . $e->getMessage() . ']']);           
        }
    }

	protected function save_announ(Request $request) {
		try{
		DB::beginTransaction();	
		 $x[] =  array_column($request->award, 'value', 'name');
		 $y[] =  array_column($request->announ, 'value', 'name');
		$approve = MasterAnnouncement::submit_approve();
	//	dd($x);
			$form_data = array(
            'reference_number' => $x[0]['reference_number'],
            'id_employee' => $x[0]['id_employee'],
            'transaction_type' => 'A',
            'effective_date' => $x[0]['effective_date'],
            'expired_date' => $x[0]['expired_date'],
            'award_letter_number' => $x[0]['award_letter_number'],
            'award_certificate_number' => $x[0]['award_certificate_number'],
            'description_name' => $x[0]['description_name'],
            'reference_date' => $x[0]['reference_date'],
            'remark' => $x[0]['remark'],
            'status' => $x[0]['status'],
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
        );
        AwardDicipline::findOrFail($x[0]['id_transaction'])->update($form_data);
	
	if(isset($y[0]['enable_approval']) == "on"){
		$app_status = $approve->id_general_data;
	}
	else{
		$app_status = null;
	}
        $result = MasterAnnouncement::create([
            'id_company' => session('id_company'),
			'reference_number' => $x[0]['reference_number'],
			'description' => "ANNOUNCEMENT AWARD (".$x[0]['reference_number'].")",
			'id_employee_request' => $y[0]['id_employee_request'],
			'id_anouncement_type' => $y[0]['id_anouncement_type'],
			'published' => isset($y[0]['published']) == "on" ? 1 : 0,
			'enable_approval' => isset($y[0]['enable_approval']) == "on" ? 1 : 0,
			'id_approval' => isset($y[0]['id_approval']) ? $y[0]['id_approval'] : null,
			'id_approval_status' => $app_status,
			'id_transaction' => $x[0]['id_transaction'],
			'attachment' => $x[0]['attachment_name'],
			'content_letter' => $x[0]['description_name'],
			'start_date' => $x[0]['effective_date'],
			'end_date' => $x[0]['expired_date'],
			'created_by' => session('id_user'),
        ]);
	DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Publish to Announcement Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Publish !! [' . $e->getMessage() . ']']);           
        }
    }


    public function destroy($id) {
        $data = AwardDicipline::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
	try{
		DB::beginTransaction();	
		$form_data = array(
            'id_employee' => $request->id_employee,
            'transaction_type' => 'A',
            'effective_date' => $request->effective_date,
            'expired_date' => $request->expired_date,
            'award_letter_number' => $request->award_letter_number,
            'award_certificate_number' => $request->award_certificate_number,
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
			$image = "Award-".$rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/announcement',0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/announcement',$request->attachment,$image);
			$form_data['attachment_type'] = NULL;
			$form_data['attachment'] = $image;	
		}
		
        AwardDicipline::findOrFail($request->id_transaction)->update($form_data);
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Award Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Award !! [' . $e->getMessage() . ']']);           
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
	public function get_req_employee() {
        $result = AwardDicipline::get_req_employee();
        return response()->json($result);
    }

    public function get_company() {
        $result = AwardDicipline::get_company();
        return response()->json($result);
    }
	
	public function get_announcement_type() {
        $result = AwardDicipline::get_announcement_type();
        return response()->json($result);
    }

}
