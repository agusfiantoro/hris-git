<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Models\Employee\EmployeeSetting\MasterAnnouncement;
use App\Models\Employee\Employee\Employee;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MasterAnnouncementController extends Controller {

    public function index(Request $request) {
		$codeid = MasterAnnouncement::getkode_announ();
	//	dd($codeid);
        if ($request->ajax()) {
            $data = MasterAnnouncement::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$button = '<button type="button" name="submit" id="' . $data->id_announcement . '" class="submit_approve btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
                                $button .= '<button type="button" name="edit" id="' . $data->id_announcement . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
								$button .= '<button type="button" name="cancel" id="' . $data->id_announcement . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close"></span></button>';
                                $button .= '&nbsp;<button type="button" name="delete" id="' . $data->id_announcement . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('employee.employee_setting.announcement.index',compact('codeid'));
    }

    protected function save(Request $request) {
		$rules = [
            'reference_number' => 'required|string',
            'description' => 'required|string',
         	//   'enable_approval' => 'required',
            'attachment' => 'mimes:pdf,jpg,jpeg,png|max:1014',
		];
		$request = SanitizedForm::sanitizeStringInput($request, $rules);
        $request->validate($rules, [],
                [
                    'reference_number' => 'Reference Number',
                    'description' => 'Description',
				//	'enable_approval' => 'Enable Approval',
        ]);
		try{
			DB::beginTransaction();
		if($request->attachment != ""){
		//	$image_file = file_get_contents($request->attachment);
		//	$image = base64_encode($image_file);
			$nik_employee = Employee::where('id_employee', $request->id_employee_request)->first();
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/announcement',0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/announcement',$request->attachment,$image);
		}
		else{
			$image = NULL;
		}
		$approved = MasterAnnouncement::approved();
        $form_data = array(
            'reference_number' => $request->reference_number,
            'description' => $request->description,
            'id_employee_request' => $request->id_employee_request,
            'start_date' => $request->start_date ?? date('Y-m-d'),
            'end_date' => $request->end_date ?? date('Y-m-d'),
            'id_anouncement_type' => $request->id_anouncement_type,
            'attachment_type' => NULL,
            'attachment' => $image,
            'content_letter' => $request->content_letter ?? '',
            'published' => isset($request->published) == "on" ? 1 : 0,
            'source_transaction_type' => $request->source_transaction_type,
            'id_source_transaction' => $request->id_source_transaction,
            'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
            'id_approval' => $request->id_approval,
            'id_approval_status' => isset($request->enable_approval) == "on" ? $request->id_approval_status : $approved->id_general_data,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
		
       $masterannouncement = MasterAnnouncement::create($form_data);
		if($request->id_approval != null){
			$app = ApprovalTransaction::get_approval($request->id_approval);
		//	dd($app);
			if($app[0]->hierarchy_type == "Organization"){
				$approve = ApprovalTransaction::get_app_org($request->id_employee_request,session('id_company'));
			}
			else if($app[0]->hierarchy_type == "Combine"){
				$approve = ApprovalTransaction::get_app_combine($request->id_employee_request,session('id_company'),$request->id_approval);
			}
			else if($app[0]->hierarchy_type == "Custom"){
				$approve = ApprovalTransaction::get_app_custom($request->id_employee_request,session('id_company'),$request->id_approval);
			}		
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $masterannouncement->id_announcement;
					$source[$key]['source_transaction_type'] = $app[0]->code;
					$source[$key]['id_approval'] = $app[0]->id_approval;
					$source[$key]['id_approval_detail'] = isset($value->id_approval_detail) ? $value->id_approval_detail: null;
					$source[$key]['id_approval_status'] = $app[0]->id_approval_status;
					$source[$key]['id_approval_mode'] = $value->id_approval_mode;
					$source[$key]['sequence'] = $value->sequence;
					$source[$key]['id_position_detail'] = $value->id_position_detail;
					$source[$key]['id_employee_approval'] = $value->id_employee_approval;
			}	
			foreach ($source as $key => $value) {
				ApprovalTransaction::create(array(
					'id_source_transaction' => $value['id_source_transaction'],
					'source_transaction_type' => $value['source_transaction_type'],
					'id_approval' => $value['id_approval'],
					'id_approval_detail' => $value['id_approval_detail'],
					'sequence' => $value['sequence'],
					'id_employee_approval' => $value['id_employee_approval'],
					'id_approval_status' => $value['id_approval_status'],
					'id_approval_mode' => $value['id_approval_mode'],
					'id_position_detail' => $value['id_position_detail'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				));
			}
		}
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Announcement Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Announcement !! [' . $e->getMessage() . ']']);           
        }
    }

    public function update(Request $request) {
		$rules = [
            'description' => 'required|string',
        	//    'enable_approval' => 'required',
            'attachment' => 'mimes:pdf,jpg,jpeg,png|max:1014',
        ];
		$request = SanitizedForm::sanitizeStringInput($request, $rules);
		$request->validate($rules, [],
		[
			'description' => 'Description',
		//    'enable_approval' => 'Enable Approval',
        ]);
		try{
			DB::beginTransaction();
			
		$form_data = array(
            'reference_number' => $request->reference_number,
            'description' => $request->description,
            'id_employee_request' => $request->id_employee_request,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'id_anouncement_type' => $request->id_anouncement_type,
            'content_letter' => $request->content_letter ?? '',
            'published' => isset($request->published) == "on" ? 1 : 0,
            'source_transaction_type' => $request->source_transaction_type,
            'id_source_transaction' => $request->id_source_transaction,
		//	'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
            'id_approval' => $request->id_approval,
        //    'id_approval_status' => $request->id_approval_status,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
        );
		if($request->attachment != ""){
		//	$image_file = file_get_contents($request->attachment);
		//	$image = base64_encode($image_file);			
			$nik_employee = Employee::where('id_employee', $request->id_employee_request)->first();
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/announcement',0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/announcement',$request->attachment,$image);
			$form_data['attachment_type'] = NULL;
			$form_data['attachment'] = $image;	
		}
	
        $masterannouncement = MasterAnnouncement::findOrFail($request->id_announcement)->update($form_data);
		
		if($request->id_approval != null){
			$app = ApprovalTransaction::get_approval($request->id_approval);
			$apptrans = ApprovalTransaction::where('id_source_transaction', $request->id_announcement)->delete();
		//	dd($app);
			if($app[0]->hierarchy_type == "Organization"){
				$approve = ApprovalTransaction::get_app_org($request->id_employee_request,session('id_company'));
			}
			else if($app[0]->hierarchy_type == "Combine"){
				$approve = ApprovalTransaction::get_app_combine($request->id_employee_request,session('id_company'),$request->id_approval);
			}
			else if($app[0]->hierarchy_type == "Custom"){
				$approve = ApprovalTransaction::get_app_custom($request->id_employee_request,session('id_company'),$request->id_approval);
			}
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] =  $request->id_announcement;
					$source[$key]['source_transaction_type'] = $app[0]->code;
					$source[$key]['id_approval'] = $app[0]->id_approval;
					$source[$key]['id_approval_detail'] = isset($value->id_approval_detail) ? $value->id_approval_detail: null;
					$source[$key]['id_approval_status'] = $app[0]->id_approval_status;
					$source[$key]['id_approval_mode'] = $value->id_approval_mode;
					$source[$key]['sequence'] = $value->sequence;
					$source[$key]['id_position_detail'] = $value->id_position_detail;
					$source[$key]['id_employee_approval'] = $value->id_employee_approval;
			}
			foreach ($source as $key => $value) {
				ApprovalTransaction::create(array(
					'id_source_transaction' => $value['id_source_transaction'],
					'source_transaction_type' => $value['source_transaction_type'],
					'id_approval' => $value['id_approval'],
					'id_approval_detail' => $value['id_approval_detail'],
					'sequence' => $value['sequence'],
					'id_employee_approval' => $value['id_employee_approval'],
					'id_approval_status' => $value['id_approval_status'],
					'id_approval_mode' => $value['id_approval_mode'],
					'id_position_detail' => $value['id_position_detail'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				));
			}
		}
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Announcement Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Updated Announcement !! [' . $e->getMessage() . ']']);           
        }
    }

    public function submit_approve($id) {
		$id_approval = MasterAnnouncement::where('id_announcement', $id)->first();
		$approve = MasterAnnouncement::submit_approve();
			MasterAnnouncement::where('id_announcement', $id)->update(array(
				'id_approval_status' => $approve->id_general_data,
			));		
		$data = [
            'id_announcement' => $id,
            'id_approval' => $id_approval->id_approval
			];
		$data_status = MasterAnnouncement::getdata_approval_status($data);
		foreach ($data_status as $key => $value) {
			if($value->code == "New" || $value->code == "Cancel"){
				 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Announcement_Request')->update(array(
					'id_approval_status' => $approve->id_general_data,
					'updated_by' => session('id_user'),
				));	
			}
		}	
        return response()->json(['status' => 'true', 'message' => 'Announcement Submit Successfully !!']);
    }
	public function cancel($id) {
		$cancel = MasterAnnouncement::cancel();
        MasterAnnouncement::where('id_announcement', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));
		 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Announcement_Request')->update(array(
					'id_approval_status' => $cancel->id_general_data,
					'updated_by' => session('id_user'),
				));	
    }
	public function get_announcement_edit(Request $request) {
        $data = [
            'id_announcement' => $request->id_announcement
        ];
        $result = MasterAnnouncement::get_announcement_edit($data);
        return response()->json($result);
    }
	
	public function destroy($id) {
        $data = MasterAnnouncement::findOrFail($id);
        $findTransaction = ApprovalTransaction::where('id_source_transaction', $id);
        if($findTransaction->first()){
        	$findTransaction->delete();
        }
        $data->delete();
    }
	
	public function get_employee() {
		$userLogin = session('id_user') == 1 ? 938 : session('id_user'); //jika login sbgai admin maka pakai idUser mas radit
		$getOtherUser = DB::table('hr_employee as he')
                ->select('he.id_employee', 'he.id_company', 'he.id_user')
                ->where('he.id_user', $userLogin)
                ->first();
        if($getOtherUser->id_company != session('id_company')){
        	$idCompany = $getOtherUser->id_company;
        	$idUser = $getOtherUser->id_user;
        } else {
        	$idCompany = session('id_company');
        	$idUser = session('id_user');
        }
        $result = MasterAnnouncement::get_employee($idCompany, $idUser);
        return response()->json($result);
    }

    public function get_company() {
        $result = MasterAnnouncement::get_company();
        return response()->json($result);
    }

	public function get_announcement_type() {
        $result = MasterAnnouncement::get_announcement_type();
        return response()->json($result);
    }
/*	public function get_hierachy(Request $request) {
		$data = [
            'code' => $request->code
        ];
        $result = MasterAnnouncement::get_hierachy($data);
        return response()->json($result);
    }
*/
	public function get_hierachy(Request $request) {
		$req = ApprovalTransaction::get_user_req();
		$req_location = $req[0]->id_location;
		$data = [
            'code' => $request->code
        ];
        $result = ApprovalTransaction::get_hierachy_announ($data,$req_location);
        return response()->json($result);
    }
	public function get_approval_status() {		
        $result = MasterAnnouncement::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }
	public function get_status(Request $request) {
		$data = [
            'status' => $request->val
        ];
		$result = MasterAnnouncement::findOrFail($request->id)->update($data);
        return response()->json($result);
    }
}
