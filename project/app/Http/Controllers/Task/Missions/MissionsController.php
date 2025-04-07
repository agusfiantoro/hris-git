<?php

namespace App\Http\Controllers\Task\Missions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Task\Missions\Missions;
use App\Models\Task\Missions\AttachAnswer;
use App\Models\Task\Task\TaskAssignment;
use App\Models\Task\Task\TaskActivity;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Employee\Employee\Employee;
use App\Models\API\Employee as Employee_api;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\MasterOrganization\MasterGrade;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Models\TimeAttendance\Attendance\Attendance;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\API\BaseController;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class MissionsController extends Controller
{
	public function get_list(Request $request) {		
        if ($request->ajax()) {		
			$nik = Employee::where('id_user', session('id_user'))->where('status','A')->first();
            $data = Missions::getdata($nik->nik_employee);
			foreach($data as $key=>$val){
				$startTime = date('H:i',strtotime($val->start_date));
				$endTime = date('H:i',strtotime($val->end_date));
				$startDateTime = Carbon::parse($val->start_date)->translatedFormat('d M Y')." ".$startTime;
				$endDateTime = Carbon::parse($val->end_date)->translatedFormat('d M Y')." ".$endTime;
				$data[$key]->start_date = $startDateTime;
				$data[$key]->end_date = $endDateTime;
			}
            return DataTables::of($data)
				->addIndexColumn()
				->addColumn('', function($data) {
					$a = '';
					return $a;
				})
				->addColumn('action', function($data) {
					$multi_attach = $data->multiple_attachment_flag ? "true" : "false";
					$id_object_activity = $data->id_object_activity ?? 'null';
					$onclick = "loadedit(".$data->id_task_activity_answer.",'".$data->evidence_type."',".$id_object_activity.",'".$multi_attach."')";
					$onSubmit = "onSubmit(".$data->id_task_activity_answer.")";
				//	$button = '<button type="button" class="btn-edit btn btn-primary" style="padding:4px 5px 2px 5px;" title="Edit"><span style="font-size:16px;" class="fas fa-edit" style="color:white;"></span></button>';
					$button = '&nbsp;<button type="button" onclick="'.$onclick.'" class="btn-answer btn btn-success" style="padding:4px 5px 2px 5px;" title="Answer"><span style="font-size:18px;" class="fas fa-list-alt"></span></button> ';
					$button .= '<button type="button" onclick="'.$onSubmit.'" class="btn-submit btn btn-success" style="padding:4px 5px 2px 5px;" title="Submit"><span style="font-size:16px;" class="fas fa-check" style="color:white;"></span></button>';
					
					
					$soon = '<button type="button" class="btn btn-warning" style="padding:4px 5px 2px 5px;cursor: not-allowed;" title="Coming Soon"><span style="font-size:18px;color:white" class="fas fa-clock"></span></button>';
					if($data->flag == 1){
						return $button;
					}
					else{
						return $soon;
					}			
				})
				->rawColumns(['action'])
				->make(true);
        }
		else{
			 DB::beginTransaction();
			try {
				$mobile = new BaseController();
				$mobile->authMobile($request);
				$request->validate([
					'nik' => 'required|exists:hr_employee,nik_employee',
					'id_company' => 'nullable|exists:master_company,id_company',
				]);			
				$employee = Employee_api::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
				if(count($employee) < 1){
					throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
				}
				$data = Missions::getdata($request->nik);
				foreach($data as $key=>$val){
					$startTime = date('H:i',strtotime($val->start_date));
					$endTime = date('H:i',strtotime($val->end_date));
					$startDateTime = Carbon::parse($val->start_date)->translatedFormat('d M Y')." ".$startTime;
					$endDateTime = Carbon::parse($val->end_date)->translatedFormat('d M Y')." ".$endTime;
					$data[$key]->start_date = $startDateTime;
					$data[$key]->end_date = $endDateTime;
				}
				return $mobile->mobileSuccess('Data was sent successfully!', $data);
			} catch (ValidationException $e){
				DB::rollback();
				return $mobile->mobileErrorValidation($e);
			} catch (QueryException $e){
				DB::rollback();
				return $mobile->mobileErrorQuery($e);
			} catch (\Exception $e) {
				DB::rollback();
				if(in_array($e->getCode(), [422,404])){ return $mobile->mobileErrorCustom($e); }
				return $mobile->mobileError($e);
			}
		}       
    }
	
	public function index(Request $request) {
		return view('task.missions.missions.index');
	}
	
	public function modal_upload(Request $request) {
		$data = Missions::where('id_task_activity_answer',$request->id_task_activity_answer)->first();
		if($data->submitted_flag == false || ($data->submitted_flag == true && $request->next == 1)){
			$employee = DB::table('hr_employee as he')
						->select('he.*')
						->where('he.id_user', session('id_user'))
						->where('he.status', 'A')
						->first();
						
			$id_task_activity_answer = $request->id_task_activity_answer;
			$type = $request->type;
			$id_object_activity = $request->id_object_activity;
			$attendance = Attendance::getAttendance([
				'id_employee' => @$employee->id_employee
			]);
			$mapboxToken    = self::getTokenMapbox();
			if($type == 'Document'){
				$view = view('task.missions.missions.modal_upload', compact('id_task_activity_answer','type','id_object_activity'))->render();
			}
			else if($type == 'Photo'){
				$view = view('task.missions.missions.modal_photo', compact('id_task_activity_answer','type','id_object_activity','mapboxToken','attendance'))->render();
			}
			else if($type == 'Essay'){
				$view = view('task.missions.missions.modal_essay', compact('id_task_activity_answer','type','id_object_activity'))->render();
			}
			else if($type == 'GPS'){
				$view = view('task.missions.missions.modal_gps', compact('id_task_activity_answer','type','id_object_activity','mapboxToken','attendance'))->render();
			}
			else if($type == 'Video'){
				$view = view('task.missions.missions.modal_video', compact('id_task_activity_answer','type','id_object_activity'))->render();
			}     	
			return response()->json(['status' => 'true', 'view' => $view]);
		}
		else{
			return response()->json(['status' => 'false', 'message' => 'Mission Accomplished']);
		}	
    }
	
	public static function getTokenMapbox() {
        $mapboxToken = Attendance::getTokenMapbox();
        return $mapboxToken;
    }
	
	public function resizeImage($file, $imageName, $nik_employee) {
		$dir = Storage::makeDirectory('public/task/photo/'.$nik_employee,0775, true, true);
        $saveImage = Image::make($file)->save(storage_path('app/public/task/photo/'.$nik_employee.'/'.$imageName));
        return response()->json(['file_name' => $imageName]);
    }
	
	protected function validateReq(Request $request) {
	//	dd($request->all());
		if(session('id_user') != null){
			$arr_form_validate = [
				'desc' => 'required',
			];
		}     
		else{
			$arr_form_validate = [
				'nik' => 'required|exists:hr_employee,nik_employee',
				'id_company' => 'nullable|exists:master_company,id_company',
				'id_task_activity_answer' => 'required|exists:hr_task_activity_answer,id_task_activity_answer',
				'desc' => 'required',
				'type' => 'required|in:Document,Photo,GPS,Essay',
				'multiple_attachment' => 'required',
			];
		}
		if($request->type == 'Document'){
			if($request->attachment == '' || is_null($request->attachment)){
				$arr_form_validate['attachment'] = 'required';
			} else {
				if(@$request->attachment && !is_string($request->attachment)){
					$arr_form_validate['attachment'] = 'required|mimes:xls,xlsx,pdf|max:2048';
				}
			}
		}
		else if($request->type == 'GPS' || $request->type == 'Photo'){
			if(session('id_user') != null){
				if($request->longlat == null){
					$arr_form_validate['longlat'] = 'required';
				}
				else{
					$arr_form_validate['image_photo'] = 'required';
				}
			}
			else{
				if($request->lng == null && $request->lat == null){
					$arr_form_validate['lng'] = 'required';
					$arr_form_validate['lat'] = 'required';
					$arr_form_validate['map_place'] = 'required';
				}
				else{
					$arr_form_validate['image_photo'] = 'required';
				}
				$arr_form_validate['lock_gps_location'] = 'required';
			}
		}
		else if($request->type == 'Video'){
			$arr_form_validate['video'] = 'required';
		}
		
        $arr_msg_form_validate = [
            'desc.required' => 'The Description field is required',
            'image_photo.required' => 'The Photo is required',
            'longlat.required' => 'The Location is required',
            'video.required' => 'The Video Record is required',
        ];
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {		
		DB::beginTransaction();
		if(session('id_user') != null){
			$this->validateReq($request);
			try{
				$nik_employee = Employee::where('id_user', session('id_user'))->where('status','A')->first();
				$result = $this->detail_save($request,$nik_employee->nik_employee,session('id_company'),session('id_user'));
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Data Saved Successfully !!']);
			}catch (\Exception $e) {
				DB::rollBack();
			//    Log::error($e);
				if($request->type == 'Photo' || $request->type == 'GPS'){
					$image_parts        = explode(";base64,", $request->image_photo);
					$image_type_aux     = explode("image/", $image_parts[0]);
					$image_type         = $image_type_aux[1];
					$image_base64       = base64_decode($image_parts[1]);
					$imageName          = Str::random(10).'_'.time().'.' . $image_type;
					Storage::delete('public/task/photo/'.$nik_employee['nik_employee'].'/'.$imageName);
				}
				else if($request->type == 'Document'){
					$image = Str::random(10).'_'.time().'.' . $request->attachment->getClientOriginalExtension();
					Storage::delete('public/task/document/'.$nik_employee['nik_employee'].'/'.$image);
				}
				else if($request->type == 'Video'){
					$filename = Str::random(10).'_'.time().'.webm';
					Storage::delete('public/task/video/'.$nik_employee['nik_employee'].'/'.$filename);
				}
				return response()->json(['status' => 'false', 'message' => 'Cannot Save Data !! [' . $e->getMessage() . ']']);           
			}
		}
		else{		
			try{
				$mobile = new BaseController();
				$mobile->authMobile($request);
				$this->validateReq($request);			
				$employee = Employee_api::getEmployeeDetail($request->nik);
				if(count($employee) < 1){
					throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
				}
				$nik_employee = (array)$employee[0];
				$idCompany = $nik_employee['id_company'];
				$idUser = $nik_employee['id_user'];
				$this->detail_save($request,$request->nik,$idCompany,$idUser);
				
				DB::commit();
				return $mobile->mobileSuccess('Data was sent successfully!', null);
			}catch (ValidationException $e){
				DB::rollback();
				return $mobile->mobileErrorValidation($e);
			} catch (QueryException $e){
				dd($e->getMessage());
				DB::rollback();
				return $mobile->mobileErrorQuery($e);
			} catch (\Exception $e) {
				DB::rollback();
				dd($e->getMessage());
				if(in_array($e->getCode(), [422,404])){ return $mobile->mobileErrorCustom($e); }
				return $mobile->mobileError($e);
			}
		}    
    }
	
	protected function detail_save(Request $request,$nik_employee,$idCompany,$idUser) {
		$data = Missions::where('id_task_activity_answer',$request->id_task_activity_answer)->first();
		$dataAttach = New AttachAnswer();
		if($request->type == 'Photo' || $request->type == 'GPS'){
			$image_parts        = explode(";base64,", $request->image_photo);
			$image_type_aux     = explode("image/", $image_parts[0]);
			$image_type         = $image_type_aux[1];
			$image_base64       = base64_decode($image_parts[1]);
			$imageName          = Str::random(10).'_'.time().'.' . $image_type;	
			
			$this->resizeImage($image_base64, $imageName, $nik_employee);
			
			$dataAttach -> id_task_activity_answer = $request->id_task_activity_answer;
			$dataAttach -> longitude = $request->lng;
			$dataAttach -> latitude = $request->lat;
			$dataAttach -> attachment = $imageName;
			$dataAttach -> address_description = $request->map_place;
			$dataAttach -> notes = $request->desc;
			$dataAttach -> id_company = $idCompany;
			$dataAttach -> created_by = $idUser;
			$dataAttach -> save();
		}
		else if($request->type == 'Document'){
			if($request->attachment != ""){
				$image = Str::random(10).'_'.time().'.' . $request->attachment->getClientOriginalExtension();
				$dir = Storage::makeDirectory('public/task/document/'.$nik_employee, 0775, true, true);
				$storageimage = Storage::putFileAs('public/task/document/'.$nik_employee, $request->attachment,$image);
			}
			$dataAttach -> id_task_activity_answer = $request->id_task_activity_answer;
			$dataAttach -> attachment = $image;
			$dataAttach -> notes = $request->desc;
			$dataAttach -> id_company = $idCompany;
			$dataAttach -> created_by = $idUser;
			$dataAttach -> save();
		}	
		else if($request->type == 'Essay'){
			$data -> essay_answer = $request->desc;
		}
		else if($request->type == 'Video'){ // save video only
			$fileName = Str::random(10).'_'.time().'.webm';
			$dir = Storage::makeDirectory('public/task/video/'.$nik_employee, 0775, true, true);
			$storageimage = Storage::putFileAs('public/task/video/'.$nik_employee,$request->video,$fileName);
			$dataAttach -> id_task_activity_answer = $request->id_task_activity_answer;
			$dataAttach -> attachment = $fileName;
			$dataAttach -> notes = $request->desc;
			$dataAttach -> id_company = $idCompany;
			$dataAttach -> created_by = $idUser;
			$dataAttach -> save();
		}
		$data -> completion_date = date('Y-m-d H:i:s');
		$data -> id_company = $idCompany;
		$data -> updated_by = $idUser;
		$data -> save();
	}
	
	public function submit_check(Request $request){
		DB::beginTransaction();
		if(session('id_user') != null){
			try{
				$data = Missions::where('id_task_activity_answer',$request->id_task_activity_answer)->first();
				$data -> submitted_flag = true;
				$data -> save();
				DB::commit();
				return response()->json(['status'=>'true', 'message'=>'Submit Successfully !!']);
			} catch (Exception $e) {
				DB::rollBack();
				Log::error($e);
				return response()->json(['status' => 'false', 'message' => 'Cannot Submit !! [' . $e->getMessage() . ']']);
			}
		}
		else{
			try{
				$mobile = new BaseController();
				$mobile->authMobile($request);
				$request->validate([
					'nik' => 'required|exists:hr_employee,nik_employee',
					'id_company' => 'nullable|exists:master_company,id_company',
					'id_task_activity_answer' => 'required|exists:hr_task_activity_answer,id_task_activity_answer',
				]);
				$employee = Employee_api::getEmployeeDetail($request->nik);
				if(count($employee) < 1){
					throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
				}
				$data = Missions::where('id_task_activity_answer',$request->id_task_activity_answer)->first();
				if($data->completion_date == null){
					throw new \Exception('Anda tidak bisa Submit Mission, karena Mission belum dilakukan.', 422);
				}
				$data -> submitted_flag = true;
				$data -> save();
				
				DB::commit();
				return $mobile->mobileSuccess('Data was sent successfully!', null);
			}catch (ValidationException $e){
				DB::rollback();
				return $mobile->mobileErrorValidation($e);
			} catch (QueryException $e){
				DB::rollback();
				return $mobile->mobileErrorQuery($e);
			} catch (\Exception $e) {
				DB::rollback();
				if(in_array($e->getCode(), [422,404])){ return $mobile->mobileErrorCustom($e); }
				return $mobile->mobileError($e);
			}
		}
		
	}
	
}