<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MessageController;
use App\Models\Employee\EmployeeReco\EmployeeRecoQualitative;
use App\Models\Mail\Employee;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\TestMail;
use App\Mail\BorwitaMail;
use Carbon\Carbon;

class EmailController extends Controller {

	public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->MessageController  = new MessageController;
	}

	private function employee($id_employee=null){
        $employee = DB::table('hr_employee as he')
                    ->select('he.*')
                    ->where('he.id_employee', $id_employee)
                    ->first();
        return $employee;
    }

	private function emp_user($id_user=null){
        $employee = DB::table('hr_employee as he')
                    ->select('he.*')
                    ->where('he.id_user', $id_user)
                    ->where('he.status', 'A')
                    ->first();
        return $employee;
    }

    public function index(Request $request) {
        ini_set('max_execution_time', -1);

        try {
			$env_mail       = env('MAIL_FROM_ADDRESS');
            $to             = [];
            $cc             = [];
            $bcc            = [];
            $file           = [];
            $source_logo    = "data:image/png;base64,".base64_encode(file_get_contents(asset('project/public/icon/mail-logo.png')));
			
            $params = [
				'view_file'             => @$request->view_file ?? 'emails.borwita',
				'mail_from'             => $env_mail,
                'mail_alias'            => @$request->mail_alias ?? $env_mail,
                'mail_subject'          => @$request->mail_subject ?? '',
                'content_title'         => @$request->content_title ?? '',
                'content_image'         => @$request->content_image ?? '',
                'content_username'   	=> @$request->content_username ?? '',
                'content_password'   	=> @$request->content_password ?? '',
                'reference_number'   	=> @$request->reference_number ?? '',
                'karyawan'   			=> @$request->karyawan ?? '',
                'creation_date'   		=> @$request->creation_date ?? '',
                'permohonan'   			=> @$request->permohonan ?? '',
                'request_by'   			=> @$request->request_by ?? '',
                'leave_name'   			=> @$request->leave_name ?? '',
                'from_date'   			=> @$request->from_date ?? '',
                'to_date'   			=> @$request->to_date ?? '',
                'qty_days'   			=> @$request->qty_days ?? '',
                'note'   				=> @$request->note ?? '',
                'code'   				=> @$request->code ?? '',			
				'content_link'          => @$request->content_link ?? '',				
				'list_child'        	=> @$request->list_child ?? '',
				'text_emp'        		=> @$request->text_emp ?? '',
				'position'        		=> @$request->position ?? '',
				'new_position'        	=> @$request->new_position ?? '',
				'pos_req'        		=> @$request->pos_req ?? '',
				'count_req'        		=> @$request->count_req ?? '',
				'effective_date'        => @$request->effective_date ?? '',
				'expired_date'        	=> @$request->expired_date ?? '',
				'travel_status'         => @$request->travel_status ?? '',
				'employment_type'		=> @$request->employment_type ?? '',
				'old_region'			=> @$request->old_region ?? '',
				'old_branch'			=> @$request->old_branch ?? '',
				'old_principal'			=> @$request->old_principal ?? '',
				'new_region'			=> @$request->new_region ?? '',
				'new_branch'			=> @$request->new_branch ?? '',
				'new_principal'			=> @$request->new_principal ?? '',
				'sales_data'			=> @$request->sales_data ?? [],
				'month'					=> @$request->month ?? '',
				'month_performance'		=> @$request->month_performance ?? '',
				'dept_code'				=> @$request->dept_code ?? '',
				'dept'					=> @$request->dept ?? '',
				'obj_kpi'				=> @$request->obj_kpi ?? '',
				'act_idx'				=> @$request->act_idx ?? '',
				'act_kpi'				=> @$request->act_kpi ?? '',
				
            ];

			$to = [ @$request->to ];
			if(@$request->cc){
                if(!is_array(@$request->cc)){
                    $cc = [ @$request->cc ];
                }
				else{
					 $cc = @$request->cc;
				}
            }
		//	$bcc = [ @$request->bcc ];
		
            if(@$request->file && is_array(@$request->file)){
                if(!is_array(@$request->file)){
                    $file = [ @$request->file ];
                }
            }

            if(count($file) > 0){
                $params['files'] = $file;
            }
            if(count($to) > 0){
                foreach ($to as $key => $sendTo) {
                    $params['logo'] = $source_logo;
                   
                    $mail = Mail::to($sendTo);

                   if(count($cc) > 0){
                        $mail->cc($cc);
                    }
                    if(count($bcc) > 0){
                        $mail->bcc($bcc);
                    }
				
                   $mail->send(new BorwitaMail($params));
                }
            } else {
                throw new \Exception('No Email Recipient');
            }

            return true;
        } catch (\Exception $e) {
			\Log::channel('email')->info($e->getMessage());
            return false;
        }
    }
	
	public function submit_mail(Request $request) {
		// dd($request->source);
        try {		
				$req = new Request();
				$req->view_file             = 'emails.pa_feedback';
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    =  $request->source['private_mail'];
				$req->mail_subject          = '[360 Feedback]HRIS';
				$req->content_title         = $request->source['penilai'];
			
				$req->karyawan     		= $request->source['dinilai'];
				$req->content_username     		= $request->source['nik_dinilai'];
				
				$req->content_link          = url('/kpi/360_feedback/pa_qualitative');
		
				$send = self::index($req);
				if(!$send){
					throw new \Exception('Sending Failed');
				}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function mass_mail(Request $request) {
		// dd(request('ids'));
		 try {
			foreach (request('ids') as $key => $value) {
				$emp_send_mail = [];
				$emp_appraiser = DB::table('hr_qualitative_appraisers as hqa')
                    ->select('hqa.id_employee_appraisers','hqa.id_employee_participant')
                    ->where('hqa.id_qualitative_appraisers', $value)
                    ->first();
				
				
				$emp_mail_appraiser = DB::table('hr_employee as he')
                    ->select('he.private_mail','he.name')
                    ->where('he.id_employee', $emp_appraiser->id_employee_appraisers)
                    ->first();
							
				$emp_dinilai = DB::table('hr_employee as he')
                    ->select('he.name','he.nik_employee')
                    ->where('he.id_employee', $emp_appraiser->id_employee_participant)
                    ->first();
													
					$req = new Request();
					$req->view_file             = 'emails.pa_feedback';
					$req->mail_alias            = 'HRIS Borwita';
					$req->to                    =  $emp_mail_appraiser->private_mail;
					$req->mail_subject          = '[360 Feedback]HRIS';
					$req->content_title         = $emp_mail_appraiser->name;
				
					$req->karyawan     			= $emp_dinilai->name;
					$req->content_username     	= $emp_dinilai->nik_employee;
					
					$req->content_link          = url('/kpi/360_feedback/pa_qualitative');
			
					$send = self::index($req);
					if(!$send){
						throw new \Exception('Sending Failed');
					}
			}
		 return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }		
	}

    public function new_account($nik='') {
        try {
            if($nik==''){
                throw new \Exception('NIK is required');
            }
            $get_employee = $this->get_employee($nik);
            if(!$get_employee){
                throw new \Exception('Data Not Found');
            }
	
			// $time = strtotime(date("Y-m-d H:i:s", strtotime("+120 minutes")));
            $token = Str::random(40);
            $update = DB::table('master_users')
                        ->where('id_user', $get_employee->id_user)
                        ->update(['id_token' => $token]);
						
            $source_image = asset('project/public/icon/join-logo.png');

            $req = new Request();
            $req->view_file             = 'emails.borwita';
			$req->mail_alias            = 'HRIS Borwita';
            $req->to                    = $get_employee->private_mail;
            $req->mail_subject          = '[Welcome] to Borwita';
            $req->content_title         = 'Hai, '.$get_employee->name;
            $req->content_username      = "Username : ".$nik;
			$req->content_link          = url('/newPassword').'/'.$token;
       //     $req->content_password      = "Password : ".$nik;

            if(strpos($req->to, '@gmail') !== false) {
                $req->content_image = $source_image;
            } else {
                $source_base64 = 'data:image/png;base64,'.base64_encode(file_get_contents($source_image));
                $req->content_image = $source_base64;
            }
			if(!is_null(@$get_employee->mobile_phone) || !empty(@$get_employee->mobile_phone)){
				$message = $this->MessageController->messageTemplate('new_account', $req);
				//$sendMessage = $this->MessageController->sendWhatsapp($message, @$get_employee->mobile_phone);
			}
            $send = self::index($req);
            
            if(!$send){
                throw new \Exception('Sending Failed');
            }
            // return true;
            return response()->json(['status' => 'true', 'message' => 'Email New Account Sent Successfully !!']);
        } catch (\Exception $e) {
            // return false;
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function new_request(Request $request) {
        try {
        //    $source_image = 'http://career.borwita.co.id/image/request.png';
			foreach($request->source['transaction'] as $trans_mail){
				$req = new Request();
				if($request->source['req']['code'] == 'Attendance_Correction'){
					$req->view_file         = 'emails.request_attendance';
				}
				else if($request->source['req']['code'] == 'Change_Day_off'){
					$req->view_file        	= 'emails.request_cdo';
				}
				else{
					$req->view_file         = 'emails.request_leave';
				}

				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $trans_mail['private_mail'];
		//		$req->to                    = 'agus.dwi@borwita.co.id';
		//		$req->cc                    = ['agus.fiantoro@gmail.com','nabil.firas@borwita.co.id'];
				$req->mail_subject          = '[Request]HRIS From '.$request->source['reqdetail']['name'];
		//		$req->content_title         = 'Agus';
				$req->content_title         = $trans_mail['name'];
				$req->reference_number      = $request->source['req']['reference_number'];
				$req->karyawan      		= $request->source['reqdetail']['name'];
				$req->creation_date      	= $request->source['req']['creation_date'];
				$req->permohonan      		= $request->source['req']['type'];
				$req->request_by      		= $request->source['req']['name'];
				
				if($request->source['req']['code'] == 'Leave_Request'){
					$req->leave_name      	= $request->source['req']['leave_type'];
					$req->qty_days      	= $request->source['reqdetail']['qty_days'];
				}
				else if($request->source['req']['code'] == 'Change_Day_off' ){
					$req->qty_days      	= $request->source['reqdetail']['qty_days'];
				}

				$req->from_date      		= $request->source['reqdetail']['request_start_to'];
				$req->to_date      			= $request->source['reqdetail']['request_end_to'];
				$req->note      			= $request->source['req']['note'];
				$req->code      			= $request->source['req']['code'];
				$req->content_link          = url('/employee/employee/employee_approval');

			//	$req->content_image = $source_image;
				
				if($trans_mail['sequence'] == '1'){ // HANYA ATASAN YANG DIKIRIM NOTIF WA
					$get_employee 	= $this->employee($trans_mail['id_employee_approval']);
					if(!is_null(@$get_employee->mobile_phone) || !empty(@$get_employee->mobile_phone)){
						$req->status_direct = 'direct';
						if(strpos($trans_mail['new_sequence'], '.') !== false) {
							$req->status_direct = 'indirect';
						}
						$message = $this->MessageController->messageTemplate($request->source['req']['code'], $req);
						$sendMessage = $this->MessageController->sendWhatsapp($message, @$get_employee->mobile_phone);
					}
					$send = self::index($req);
					if(!$send){
						throw new \Exception('Sending Failed');
					}
				}

			}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function new_fpk(Request $request) {
	//	dd($request->source);
        try {
			foreach($request->source['transaction'] as $trans_mail){
				$req = new Request();
				$req->view_file   	        = 'emails.request_fpk';				
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $trans_mail['private_mail'];
			//	$req->to                    = 'agus.dwi@borwita.co.id';
				$req->cc                    = $request->source['req']['cc_email'];
			//	$req->cc                    = ['agus.dwi@borwita.co.id','nabil.firas@borwita.co.id'];
				$req->mail_subject          = '[FPK]HRIS From '.$request->source['req']['emp_name'];
		//		$req->content_title         = 'Agus';
				$req->content_title         = $trans_mail['name'];
				$req->reference_number      = $request->source['req']['reference_number'];
				$req->creation_date      	= $request->source['req']['creation_date'];
				$req->permohonan      		= $trans_mail['permohonan'];
				$req->request_by      		= $request->source['req']['emp_name'];
								
				$req->pos_req      			= $request->source['req']['pos_req'];
				$req->count_req      			= $request->source['req']['count_req'];
				$req->effective_date      			= $request->source['req']['effective_date'];
				$req->note      			= $request->source['req']['reason_notes'];
				
				$req->content_link          = url('/employee/employee/employee_approval');
				
				if($trans_mail['sequence'] == '1'){ // HANYA SEQUENCE 1 YANG DIKIRIM NOTIF WA
					$get_employee 	= $this->employee($trans_mail['id_employee_approval']);
					if(!is_null(@$get_employee->mobile_phone) || !empty(@$get_employee->mobile_phone)){
						$message = $this->MessageController->messageTemplateFpk($req);
        				if (env('APP_ENV')=='production'){
        					$sendMessage = $this->MessageController->sendWhatsapp($message, @$get_employee->mobile_phone);
        				} else {
							\Log::channel('email')->info('Trial send notification Whatsapp for FPK. '.$message);
        				}
					}
				}

				$logSuccess = '[FPK] Email to '.@$req->to.' success. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';
				$logFailed = '[FPK] Email to '.@$req->to.' failed. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';

				$send = self::index($req);
				if($send){
        			\Log::channel('email')->info($logSuccess);
				} else {
        			\Log::channel('email')->info($logFailed);
        		//	throw new \Exception('Sending Failed');
				}
			}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function new_reco(Request $request) {
		try {
			if($request->type_submit == "save_and_submit"){
				if($request->source['id_new_chief_employee'] != null){
					$get_mgr_notice 	= $this->employee($request->source['id_new_chief_employee']);
					$req = new Request();
					$req->view_file   	        = 'emails.request_reco_notice';				
					$req->mail_alias            = 'HRIS Borwita';
					$req->to                    = $get_mgr_notice->private_mail;
				//	$req->to                    = 'agus.dwi@borwita.co.id';
					$req->mail_subject          = '[RECO Inform]HRIS From '.$request->source['emp_name'];
					$req->content_title         = $get_mgr_notice->name;
					$req->reference_number      = $request->source['reference_number'];
					$req->creation_date      	= Carbon::parse($request->source['creation_date'])->format('d M Y');;
					$req->permohonan      		= 'Recommendation Form (Inform)';
					$req->karyawan      		= $request->source['emp_name'];
					$req->position      		= $request->source['position'];
					$req->old_branch      		= $request->source['old_branch'];
					$req->old_region      		= $request->source['old_region'];
					$req->old_principal      	= $request->source['old_principal'];
					$req->request_by      		= $request->source['request_by'];									
					$req->note      			= $request->source['category']." (".$request->source['type'].")";
					if($request->source['new_position'] != null){
						$req->new_position      	= $request->source['new_position'];
						$req->new_branch      		= $request->source['new_branch'];
						$req->new_region      		= $request->source['new_region'];
						$req->new_principal      	= $request->source['new_principal'];
					}
					else{
						$req->new_position      	= '-';
						$req->new_branch      		= '-';
						$req->new_region      		= '-';
						$req->new_principal      	= '-';
					}
					$req->effective_date      		= Carbon::parse($request->source['effective_date'])->format('d M Y');
					if($request->source['expired_date'] != null){
						$req->expired_date      		= Carbon::parse($request->source['expired_date'])->format('d M Y');
					}
					else{
						$req->expired_date      		= '-';
					}
					
					$logSuccess = '[RECO Inform] Email to '.@$req->to.' success. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';
					$logFailed = '[RECO Inform] Email to '.@$req->to.' failed. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';
					$send = self::index($req);
					if($send){
						\Log::channel('email')->info($logSuccess);
					} else {
						\Log::channel('email')->info($logFailed);
						throw new \Exception('Sending Failed');
					}
				}
				
				foreach($request->trans as $trans_mail){
					$get_emp_approval 	= $this->employee($trans_mail['id_employee_approval']);
					$req = new Request();
					$req->view_file   	        = 'emails.request_reco';				
					$req->mail_alias            = 'HRIS Borwita';
					if($trans_mail['sequence'] == 1){
						$req->to                    = $get_emp_approval->private_mail;
					//	$req->to                    = 'agus.dwi@borwita.co.id';
					}
					else if($trans_mail['sequence'] == 2){
						$req->cc                    = $get_emp_approval->private_mail;
					//	$req->cc                    = ['agus.fiantoro@gmail.com'];
					}			
					$req->mail_subject          = '[RECO]HRIS From '.$request->source['emp_name'];
					$req->content_title         = $get_emp_approval->name;
					$req->reference_number      = $request->source['reference_number'];
					$req->creation_date      	= Carbon::parse($request->source['creation_date'])->format('d M Y');;
					$req->permohonan      		= 'Recommendation Form';
					$req->karyawan      		= $request->source['emp_name'];
					$req->position      		= $request->source['position'];
					$req->old_branch      		= $request->source['old_branch'];
					$req->old_region      		= $request->source['old_region'];
					$req->old_principal      	= $request->source['old_principal'];
					$req->request_by      		= $request->source['request_by'];									
					$req->note      			= $request->source['category']." (".$request->source['type'].")";
					if($request->source['new_position'] != null){
						$req->new_position      	= $request->source['new_position'];
						$req->new_branch      		= $request->source['new_branch'];
						$req->new_region      		= $request->source['new_region'];
						$req->new_principal      	= $request->source['new_principal'];
					}
					else{
						$req->new_position      	= '-';
						$req->new_branch      		= '-';
						$req->new_region      		= '-';
						$req->new_principal      	= '-';
					}
					$req->effective_date      		= Carbon::parse($request->source['effective_date'])->format('d M Y');
					if($request->source['expired_date'] != null){
						$req->expired_date      		= Carbon::parse($request->source['expired_date'])->format('d M Y');
					}
					else{
						$req->expired_date      		= '-';
					}
					$req->content_link          = url('/employee/employee/employee_approval');
					
					if($trans_mail['sequence'] == '1'){ // HANYA SEQUENCE 1 YANG DIKIRIM NOTIF WA
						$get_employee 	= $this->employee($trans_mail['id_employee_approval']);
						if(!is_null(@$get_employee->mobile_phone) || !empty(@$get_employee->mobile_phone)){
							$message = $this->MessageController->messageTemplateReco($req);
						//	$message = null;
							if (env('APP_ENV')=='production'){
								$sendMessage = $this->MessageController->sendWhatsapp($message, @$get_employee->mobile_phone);
							} else {
								\Log::channel('email')->info('Trial send notification Whatsapp for RECO. '.$message);
							}
						}
					}

					$logSuccess = '[RECO] Email to '.@$req->to.' success. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';
					$logFailed = '[RECO] Email to '.@$req->to.' failed. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';

					$send = self::index($req);
					if($send){
						\Log::channel('email')->info($logSuccess);
					} else {
						\Log::channel('email')->info($logFailed);
						throw new \Exception('Sending Failed');
					}
				}
			}	
			else if($request->type_submit == "save_and_draft") {
				$get_employee = [];
				foreach($request->source as $trans_mail){					
						$get_emp_approval 	= $this->employee($trans_mail['id_employee_appraisers']);
						$req = new Request();
						$req->view_file   	        = 'emails.request_reco_quali';				
						$req->mail_alias            = 'HRIS Borwita';
						$req->to                    = $get_emp_approval->private_mail;
					//	$req->cc                    = 'agus.dwi@borwita.co.id';
						$req->mail_subject          = '[360 Feedback]HRIS From '.$trans_mail['emp_participant'];
						$req->content_title         = $get_emp_approval->name;
						$req->reference_number      = $trans_mail['reference_number'];
						$req->creation_date      	= Carbon::parse($trans_mail['creation_date'])->format('d M Y');;
						$req->permohonan      		= 'Penilaian 360 Feedback';
						$req->karyawan      		= $trans_mail['emp_participant'];
						$req->position      		= $trans_mail['position'];
						$req->request_by      		= $trans_mail['request_by'];
						
						if($trans_mail['appraisers_hierarchy'] == "direct"){
							$type = "Direct Line (Atasan Langsung)";
						}
						else if($trans_mail['appraisers_hierarchy'] == "subordinat"){
							$type = "Subordinat (Anggota Tim)";
						}
						else if($trans_mail['appraisers_hierarchy'] == "peers"){
							$type = "Peers (Rekan Kerja)";
						}
						else{
							$type = "-";
						}
						$req->note      			= $type;
						$req->code      			= $trans_mail['code'];
						$req->content_link          = url('/employee/employee/reco_qualitative');
					
					if($trans_mail['submitted'] == 'false'){
						$get_employee 	= $this->employee($trans_mail['id_employee_appraisers']);						
						if(!is_null(@$get_employee->mobile_phone) || !empty(@$get_employee->mobile_phone)){
							$message = $this->MessageController->messageTemplateQuali($req);
							if (env('APP_ENV')=='production'){
								$sendMessage = $this->MessageController->sendWhatsapp($message, @$get_employee->mobile_phone);
							} else {
								\Log::channel('email')->info('Trial send notification Whatsapp for 360. '.$message);
							}
						}						
					}	
						$logSuccess = '[360 Feedback Reco] Email to '.@$req->to.' success. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';
						$logFailed = '[360 Feedback Reco] Email to '.@$req->to.' failed. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';
						
					if($trans_mail['notes_send'] == null){
						$send = self::index($req);
						if($send){
							$dataQuali = EmployeeRecoQualitative::where('id_recommendation_qualitative', $trans_mail['id_recommendation_qualitative'])->first();
							$dataQuali -> notes = 'Email Terkirim';
							$dataQuali -> save();
							\Log::channel('email')->info($logSuccess);
						} else {
							\Log::channel('email')->info($logFailed);
							throw new \Exception('Sending Failed');
						}					
					}
				}
			}	
			return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
		} catch (\Exception $e) {
			return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
		}
    }
	
	public function new_kpk(Request $request) {
		try {
		//	dd($request->source);
			foreach($request->source['send_mail'] as $trans_mail){
				foreach($trans_mail['child'] as $key=>$val_child){
					$req = new Request();
					$req->view_file   	        = 'emails.kpk_submit_employee';				
					$req->mail_alias            = 'HRIS Borwita';
					$req->to              		= $val_child['private_mail_emp'];
				//	$req->to                	= 'agus.fiantoro@gmail.com';
					$req->mail_subject          = '[P2K Employee]Month ('.$request->source['remark_1'].') - Dept ('.$request->source['dept'].')';
			//		$req->content_title         = 'Agus';
					$req->content_title         = $val_child['name_emp'];
					$req->reference_number      = $request->source['reference_number'];
					$req->creation_date      	= Carbon::parse($request->source['date'])->translatedFormat('d F Y');
					$req->request_by      		= $request->source['emp_name'];							
					$req->pos_req      			= $request->source['position'];
					$req->month      			= $request->source['remark_1'];
					$req->month_performance     = $request->source['remark_2'];
					$req->dept_code   			= $request->source['dept_code'];
					$req->dept   				= $request->source['dept'];
					$req->obj_kpi   			= $val_child['obj_kpi'];
					$req->act_idx   			= $val_child['act_idx'];
					$req->act_kpi   			= $val_child['act_kpi'];
					
					$logSuccess = '[P2K] Employee Email to '.@$req->to.' success. ('.@$req->request_by.', '.@$req->reference_number.')';
					$logFailed = '[P2K] Employee Email to '.@$req->to.' failed. ('.@$req->request_by.', '.@$req->reference_number.')';
					if(isset($val_child['private_mail_emp'])){
						$send = self::index($req);
						if($send){
							\Log::channel('email')->info($logSuccess);
						} else {
							\Log::channel('email')->info($logFailed);
						}
					}
				}
								
			}
			foreach($request->source['send_mail'] as $trans_mail){
				$req = new Request();
				$req->view_file   	        = 'emails.kpk_submit_direct';				
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $trans_mail['private_mail'];
			//	$req->to                    = 'agus.dwi@borwita.co.id';
				$req->cc                    = $trans_mail['private_mail_indirect'];
			//	$req->cc                    = ['agus.fiantoro@gmail.com'];
				$req->mail_subject          = '[P2K]Month ('.$request->source['remark_1'].') - Dept ('.$request->source['dept'].')';
		//		$req->content_title         = 'Agus';
				$req->content_title         = $trans_mail['name_direct'];
				$req->reference_number      = $request->source['reference_number'];
				$req->creation_date      	= Carbon::parse($request->source['date'])->translatedFormat('d F Y');
				$req->request_by      		= $request->source['emp_name'];							
				$req->pos_req      			= $request->source['position'];
				$req->month      			= $request->source['remark_1'];
				$req->month_performance     = $request->source['remark_2'];
				$req->dept_code   			= $request->source['dept_code'];
				$req->dept   				= $request->source['dept'];
								
			//	if($request->source['dept_code'] == '170_SAL'){
			//		$req->idx_sales      	= $trans_mail['act_idx'];			
			//	}				
				$req->content_link      = url('/e-letter/performance_plan/performance_review');
				$list_child = [];
				foreach($trans_mail['child'] as $key=>$val_child){
					$list_child[] = [
						'id_employee'       => $val_child['id_employee'],
						'name_emp'         	=> $val_child['name_emp'],
						'nik_employee'      => $val_child['nik_employee'],
						'pos_emp'       	=> $val_child['pos_emp'],
						'obj_kpi'       	=> $val_child['obj_kpi'],
						'act_idx'       	=> $val_child['act_idx'],
						'act_kpi'       	=> $val_child['act_kpi'],
					];
					$text_emp[] = $val_child['name_emp'];
				}
				$req->list_child = $list_child;
				$req->text_emp = implode(", ",$text_emp);
				
				 // HANYA DIRECT YANG DIKIRIM NOTIF WA			 
				if(isset($trans_mail['id_direct'])){
					$get_employee 	= $this->employee($trans_mail['id_direct']);
					if(!is_null(@$get_employee->mobile_phone) || !empty(@$get_employee->mobile_phone)){
						$message = $this->MessageController->messageTemplateKpk($req);
        				if (env('APP_ENV')=='production'){
        					$sendMessage = $this->MessageController->sendWhatsapp($message, @$get_employee->mobile_phone);
        				} else {
							\Log::channel('email')->info('Trial send notification Whatsapp for P2K. '.$message);
        				}
					}
					$logSuccess = '[P2K] Direct Email to '.@$req->to.' success. ('.@$req->request_by.', '.@$req->reference_number.')';
					$logFailed = '[P2K] Direct Email to '.@$req->to.' failed. ('.@$req->request_by.', '.@$req->reference_number.')';

					$send = self::index($req);
					if($send){
						\Log::channel('email')->info($logSuccess);
					} else {
						\Log::channel('email')->info($logFailed);
					//	throw new \Exception('Sending Failed');
					}
				}
			}
			return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
		} catch (\Exception $e) {
			return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
		}
    }
	
	public function new_approval(Request $request) {
        try {		
			//	dd($request->all());
				$req = new Request();
				if($request->source['request_code'] != 'Attendance_Correction'){
					if($request->source['request_code'] == 'Official_Travel'){
						$req->view_file         = 'emails.approval_travel';
					}
					else{
						$req->view_file         = 'emails.approval_leave';
					}
				}
				else{
					$req->view_file         = 'emails.approval_attendance';
				}
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    =  $request->source['private_mail'];
		//		$req->to                    = 'agus.fiantoro@gmail.com';
		//		$req->cc                    = ['agus.fiantoro@gmail.com','nabil.firas@borwita.co.id'];
				$req->mail_subject          = '[Approval]HRIS From Approver';
		//		$req->content_title         = 'Agus';
				$req->content_title         = $request->source['name'];
				
				if($request->source['request_code'] == 'Official_Travel'){
					$req->travel_status		= $request->source['travel_status'];
					$req->cc				= $request->source['cc_mail'];
				}
				else if($request->source['request_code'] != 'Attendance_Correction'){
					$req->leave_name      	= $request->source['leave_name'];
					$req->qty_days      	= $request->source['qty_days'];
				}
				$req->reference_number      = $request->source['reference_number'];				
				$req->permohonan     		= $request->source['desc_request'];
				
				$req->from_date      		= $request->source['request_start_to'];
				$req->to_date      			= $request->source['request_end_to'];
				$req->note      			= $request->source['note'];
				
				$req->code      			= $request->source['request_code'];
				if($request->source['request_code'] == 'Official_Travel'){
					$req->content_link          = url('/cash_advance/official_travel/official_travel');
				}
				else{
					$req->content_link          = url('/employee/employee/employee_request');
				}

			//	$req->content_image = $source_image;
		
				$send = self::index($req);
				
				if(!$send){
					throw new \Exception('Sending Failed');
				}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function new_revise(Request $request) {
        try {		
				$req = new Request();
				$req->view_file             = 'emails.approval_revise';
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    =  $request->source['private_mail'];
				$req->mail_subject          = '[Revise]HRIS From Approver';
				$req->content_title         = $request->source['name'];
			
				$req->reference_number      = $request->source['reference_number'];				
				$req->permohonan     		= $request->source['desc_request'];
				
				$req->note      			= $request->source['note_revise'];
				
				if($request->source['request_code'] == 'Official_Travel'){
					$req->content_link          = url('/cash_advance/official_travel/official_travel');
				}
				else{
					$req->content_link          = url('/employee/employee/employee_request');
				}

			//	$req->content_image = $source_image;
				$send = self::index($req);
				
				if(!$send){
					throw new \Exception('Sending Failed');
				}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function new_reject(Request $request) {
        try {		
				$req = new Request();
				$req->view_file             = 'emails.approval_reject';
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    =  $request->source['private_mail'];
				$req->mail_subject          = '[Reject]HRIS From Approver';
				$req->content_title         = $request->source['name'];
			
				$req->reference_number      = $request->source['reference_number'];				
				$req->permohonan     		= $request->source['desc_request'];
				
				$req->note      			= $request->source['note_rejected'];
				
				if($request->source['request_code'] == 'Official_Travel'){
					$req->content_link          = url('/cash_advance/official_travel/official_travel');
				}
				else{
					$req->content_link          = url('/employee/employee/employee_request');
				}

			//	$req->content_image = $source_image;
		
				$send = self::index($req);
				
				if(!$send){
					throw new \Exception('Sending Failed');
				}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function end_employee() {
		// dd("Tes");
		$employmentType = "Contract";
		$emp = "SELECT he.id_employee, he.name, he.nik_employee, mgd.description as employment_status, he.join_date, he.expired_date, mpr.description AS position,
			mp.principal_code as principal, md.description as department, mr.description as regional, mb.description as branch, 
			mpd2.id_employee as id_employee_spv , he2.name as name_spv, he2.private_mail
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				JOIN  master_position_routing mpr
             	ON  mpd.id_position_routing = mpr.id_routing
				JOIN  master_job_position mjp
             	 ON  mpr.id_position = mjp.id_position
				JOIN  master_branch mb
		          ON  mpd.id_branch = mb.id_branch
		        JOIN  master_department md
		          ON  mjp.id_dept = md.id_dept
		        JOIN  master_region mr
		          ON  mb.id_region = mr.id_region
				LEFT JOIN  relation_positiondetail_principal rpp
			  	  ON  mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN  master_principal mp
				  ON  rpp.id_principal = mp.id_principal
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail  = mpd2.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd2.id_employee  = he2.id_employee
				JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data				
				WHERE (mgd.code = 'Contract' AND he.status = 'A') AND (he.expired_date BETWEEN current_date AND (current_date::date + interval '30 days')::date OR he.expired_date <= current_date)";
		$result_emp = DB::select($emp);
		$spv1=[];
		foreach($result_emp as $val_spv){
			if($val_spv->id_employee_spv == null){
				$spv1[] = $val_spv->id_employee;
			}
		}
		$result_emp1 = $result_emp;
		if(count($spv1) > 0){
			$im_spv = implode(',',$spv1);
			$emp_spv = "SELECT he.id_employee, he.name, he.nik_employee, mgd.description as employment_status, he.join_date, he.expired_date, mpr.description AS position,
			mp.principal_code as principal, md.description as department, mr.description as regional, mb.description as branch, mpd3.id_employee as id_employee_spv, he2.name as name_spv, he2.private_mail
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				JOIN  master_position_routing mpr
             	ON  mpd.id_position_routing = mpr.id_routing
				JOIN  master_job_position mjp
             	 ON  mpr.id_position = mjp.id_position
				JOIN  master_branch mb
		          ON  mpd.id_branch = mb.id_branch
		        JOIN  master_department md
		          ON  mjp.id_dept = md.id_dept
		        JOIN  master_region mr
		          ON  mb.id_region = mr.id_region
				LEFT JOIN  relation_positiondetail_principal rpp
			  	  ON  mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN  master_principal mp
				  ON  rpp.id_principal = mp.id_principal
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail  = mpd2.id_position_detail
				LEFT JOIN master_position_detail mpd3
				ON mpd2.parent_id_position_detail  = mpd3.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd3.id_employee  = he2.id_employee
				JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data
				WHERE (he.status = 'A' AND he.expired_date BETWEEN current_date AND (current_date::date + interval '30 days')::date
				OR he.expired_date <= current_date) AND he.id_employee IN(".$im_spv.")";
		$result_spv = DB::select($emp_spv);
		
			foreach($result_emp as $val_spv){
				if($val_spv->id_employee_spv != null){
					$spv2[] = $val_spv;
					}
				}
			foreach($result_spv as $val2_spv){
					$spv2[] = $val2_spv;
				}
			
			$result_emp1 = 	$spv2;
			}
	
		$id_approval = [];
			$arr_atasan = [];
			$penampung_approval = [];
			foreach ($result_emp1 as $key => $value) {
				if(!in_array($value->id_employee_spv, $penampung_approval)){
					$penampung_approval[] = $value->id_employee_spv;
					$id_approval[$value->id_employee_spv][] = array(
						"id_employee" => $value->id_employee, 
						"name"=>$value->name,
						"nik_employee"=>$value->nik_employee,
						"employment_status"=>$value->employment_status,
						"position"=>$value->position,
						"principal"=>$value->principal,
						"department"=>$value->department,
						"regional"=>$value->regional,
						"branch"=>$value->branch,
						"join_date"=>$value->join_date,
						"expired_date"=>$value->expired_date,
					);
					$arr_atasan[] = $result_emp1[$key];
				} else {
					$id_approval[$value->id_employee_spv][] = array(
						"id_employee" => $value->id_employee, 
						"name"=>$value->name,
						"nik_employee"=>$value->nik_employee,
						"employment_status"=>$value->employment_status,
						"position"=>$value->position,
						"principal"=>$value->principal,
						"department"=>$value->department,
						"regional"=>$value->regional,
						"branch"=>$value->branch,
						"join_date"=>$value->join_date,
						"expired_date"=>$value->expired_date,
					);
				}
			}
			foreach ($arr_atasan as $key => $value) {
				unset($value->id_employee);unset($value->name);unset($value->nik_employee);
				unset($value->employment_status);unset($value->position);unset($value->principal);unset($value->department);
				unset($value->regional);unset($value->branch);unset($value->join_date);unset($value->expired_date);
				
				$arr_atasan[$key]->child = $id_approval[$value->id_employee_spv];
			}

        try {		
			foreach($arr_atasan as $val_mail){
				$req = new Request();			
				$req->view_file             = 'emails.end_employee';				
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $val_mail->private_mail;
				$req->content_title         = $val_mail->name_spv;
			//	$req->to                    = 'agus.dwi@borwita.co.id';
			//	$req->cc                    = ['agus.fiantoro@gmail.com','nabil.firas@borwita.co.id'];
				$req->mail_subject          = '[End Employment Reminder]HRIS';
				$req->permohonan     		= 'End Employment Reminder';
			//	$req->content_title         = 'Agus';
				$list_child = [];
				foreach($val_mail->child as $key=>$val_child){
					$list_child[] = [
					'emp_name'         	=> $val_child['name'],
					'nik_employee'      => $val_child['nik_employee'],
					'emp_status'        => $val_child['employment_status'],
					'position'       	=> $val_child['position'],
					'principal'         => $val_child['principal'],
					'department'        => $val_child['department'],
					'regional'         	=> $val_child['regional'],
					'branch'         	=> $val_child['branch'],
					'join_date'      	=> Carbon::parse($val_child['join_date'])->format('d M Y'),
					'expired_date'      => Carbon::parse($val_child['expired_date'])->format('d M Y'),
					];
				}
				$req->list_child = $list_child;
				$req->employment_type = $employmentType;
				
				if($val_mail->private_mail != null && env('APP_ENV')=='production'){
					$send = self::index($req);
					if($send){
            			\Log::info('Success. Employment Reminder to : '.$val_mail->private_mail.' | Name : '.$val_mail->name_spv);
					} else {
            			\Log::info('Failed. Employment Reminder to : '.$val_mail->private_mail.' | Name : '.$val_mail->name_spv);
					}
				}
				
			}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

	public function end_employee_acting($employmentType = "Acting") {
		// dd("Tes");
		$emp = "SELECT he.id_employee, he.name, he.nik_employee, mgd.description as employment_status, he.join_date, he.expired_date, mpr.description AS position,
			mp.principal_code as principal, md.description as department, mr.description as regional, mb.description as branch, 
			mpd2.id_employee as id_employee_spv , he2.name as name_spv, he2.private_mail
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				JOIN  master_position_routing mpr
             	ON  mpd.id_position_routing = mpr.id_routing
				JOIN  master_job_position mjp
             	 ON  mpr.id_position = mjp.id_position
				JOIN  master_branch mb
		          ON  mpd.id_branch = mb.id_branch
		        JOIN  master_department md
		          ON  mjp.id_dept = md.id_dept
		        JOIN  master_region mr
		          ON  mb.id_region = mr.id_region
				LEFT JOIN  relation_positiondetail_principal rpp
			  	  ON  mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN  master_principal mp
				  ON  rpp.id_principal = mp.id_principal
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail  = mpd2.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd2.id_employee  = he2.id_employee
				JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data				
				WHERE (mgd.code = ? AND he.status = 'A') AND (he.expired_date BETWEEN current_date AND (current_date::date + interval '30 days')::date OR he.expired_date <= current_date)";
		$result_emp = DB::select($emp, [$employmentType]);
		$spv1=[];
		foreach($result_emp as $val_spv){
			if($val_spv->id_employee_spv == null){
				$spv1[] = $val_spv->id_employee;
			}
		}
		$result_emp1 = $result_emp;
		if(count($spv1) > 0){
			$im_spv = implode(',',$spv1);
			$emp_spv = "SELECT he.id_employee, he.name, he.nik_employee, mgd.description as employment_status, he.join_date, he.expired_date, mpr.description AS position,
			mp.principal_code as principal, md.description as department, mr.description as regional, mb.description as branch, mpd3.id_employee as id_employee_spv, he2.name as name_spv, he2.private_mail
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				JOIN  master_position_routing mpr
             	ON  mpd.id_position_routing = mpr.id_routing
				JOIN  master_job_position mjp
             	 ON  mpr.id_position = mjp.id_position
				JOIN  master_branch mb
		          ON  mpd.id_branch = mb.id_branch
		        JOIN  master_department md
		          ON  mjp.id_dept = md.id_dept
		        JOIN  master_region mr
		          ON  mb.id_region = mr.id_region
				LEFT JOIN  relation_positiondetail_principal rpp
			  	  ON  mpd.id_position_detail = rpp.id_position_detail
				LEFT JOIN  master_principal mp
				  ON  rpp.id_principal = mp.id_principal
				LEFT JOIN master_position_detail mpd2
				ON mpd.parent_id_position_detail  = mpd2.id_position_detail
				LEFT JOIN master_position_detail mpd3
				ON mpd2.parent_id_position_detail  = mpd3.id_position_detail
				LEFT JOIN hr_employee he2
				ON mpd3.id_employee  = he2.id_employee
				JOIN master_general_data mgd
				ON he.id_employment_status = mgd.id_general_data
				WHERE (he.status = 'A' AND he.expired_date BETWEEN current_date AND (current_date::date + interval '30 days')::date
				OR he.expired_date <= current_date) AND he.id_employee IN(".$im_spv.")";
		$result_spv = DB::select($emp_spv);
		
			foreach($result_emp as $val_spv){
				if($val_spv->id_employee_spv != null){
					$spv2[] = $val_spv;
					}
				}
			foreach($result_spv as $val2_spv){
					$spv2[] = $val2_spv;
				}
			
			$result_emp1 = 	$spv2;
			}
	
		$id_approval = [];
			$arr_atasan = [];
			$penampung_approval = [];
			foreach ($result_emp1 as $key => $value) {
				if(!in_array($value->id_employee_spv, $penampung_approval)){
					$penampung_approval[] = $value->id_employee_spv;
					$id_approval[$value->id_employee_spv][] = array(
						"id_employee" => $value->id_employee, 
						"name"=>$value->name,
						"nik_employee"=>$value->nik_employee,
						"employment_status"=>$value->employment_status,
						"position"=>$value->position,
						"principal"=>$value->principal,
						"department"=>$value->department,
						"regional"=>$value->regional,
						"branch"=>$value->branch,
						"join_date"=>$value->join_date,
						"expired_date"=>$value->expired_date,
					);
					$arr_atasan[] = $result_emp1[$key];
				} else {
					$id_approval[$value->id_employee_spv][] = array(
						"id_employee" => $value->id_employee, 
						"name"=>$value->name,
						"nik_employee"=>$value->nik_employee,
						"employment_status"=>$value->employment_status,
						"position"=>$value->position,
						"principal"=>$value->principal,
						"department"=>$value->department,
						"regional"=>$value->regional,
						"branch"=>$value->branch,
						"join_date"=>$value->join_date,
						"expired_date"=>$value->expired_date,
					);
				}
			}
			foreach ($arr_atasan as $key => $value) {
				unset($value->id_employee);unset($value->name);unset($value->nik_employee);
				unset($value->employment_status);unset($value->position);unset($value->principal);unset($value->department);
				unset($value->regional);unset($value->branch);unset($value->join_date);unset($value->expired_date);
				
				$arr_atasan[$key]->child = $id_approval[$value->id_employee_spv];
			}

        try {		
			foreach($arr_atasan as $val_mail){
				$req = new Request();			
				$req->view_file             = 'emails.end_employee';				
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $val_mail->private_mail;
				$req->content_title         = $val_mail->name_spv;
			//	$req->cc                    = ['agus.fiantoro@gmail.com','nabil.firas@borwita.co.id'];
				$req->mail_subject          = '[End '.$employmentType.' Reminder]HRIS';
				$req->permohonan     		= 'End '.$employmentType.' Reminder';
			//	$req->content_title         = 'Agus';
				$list_child = [];
				$req->employment_type = $employmentType;
				// dd($val_mail, $req);
				foreach($val_mail->child as $key=>$val_child){
					$list_child[] = [
					'emp_name'         	=> $val_child['name'],
					'nik_employee'      => $val_child['nik_employee'],
					'emp_status'        => $val_child['employment_status'],
					'position'       	=> $val_child['position'],
					'principal'         => $val_child['principal'],
					'department'        => $val_child['department'],
					'regional'         	=> $val_child['regional'],
					'branch'         	=> $val_child['branch'],
					'join_date'      	=> Carbon::parse($val_child['join_date'])->format('d M Y'),
					'expired_date'      => Carbon::parse($val_child['expired_date'])->format('d M Y'),
					];
				}
				$req->list_child = $list_child;
				if($val_mail->private_mail != null && env('APP_ENV')=='production'){
					$send = self::index($req);
					if($send){
            			\Log::info('Success. Acting Reminder to : '.$val_mail->private_mail.' | Name : '.$val_mail->name_spv);
					} else {
            			\Log::info('Failed. Acting Reminder to : '.$val_mail->private_mail.' | Name : '.$val_mail->name_spv);
					}
				}
				
			}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function kpk_reminder() {
	//	$current_date = "current_date::date";
		$current_date = "current_date";
		$emp = "SELECT * FROM (
						SELECT DISTINCT 
						to_char((date_trunc('month', ".$current_date."::date) + interval '1 month - 1 day')::date, 'DD')::int AS end_month,
						CONCAT(to_char((mix.period_date::date + interval '1 month')::date,'YYYY-MM'),'-',to_char(".$current_date."::date,'DD')) AS check_date, mix.*,
						he.name AS direct_name, he2.name AS indirect_name, he.private_mail AS mail_direct, he2.private_mail AS mail_indirect  
					FROM (
						SELECT hpe.id_performance_evaluation, hel.reference_number, hel.effective_date, he.id_employee, 
						he.nik_employee, he.name, mpr.description AS position, mb.description AS branch, CONCAT(mtg.group_name,' (',mtg.subgroup_name,')') AS group_name, 
						mtg.minimum_score_kpi_level as th, hel.remark_1 AS month_ba, hpe.evaluation_date, hpe.kpi_average, 
						hpe.sales_offtake, he2.name AS created_name, hpe.evaluation_status,
						CASE
							WHEN mpd2.id_employee IS NOT NULL THEN he3.id_employee
							WHEN mpd2.id_employee IS NULL THEN he4.id_employee
							WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN he5.id_employee
						END AS id_direct,
						CASE
							WHEN mpd2.id_employee IS NOT NULL THEN he4.id_employee
							WHEN mpd2.id_employee IS NULL THEN he5.id_employee
							WHEN mpd2.id_employee IS NULL AND mpd3.id_employee IS NULL  THEN he5.id_employee
						END AS id_indirect, j_all.period_date
						FROM hr_performance_evaluation hpe
						LEFT JOIN hr_electronic_letter hel
						ON hpe.id_letter = hel.id_letter
						LEFT JOIN hr_employee he
						ON hpe.id_employee = he.id_employee
						LEFT JOIN master_threshold_group mtg
						ON hpe.id_threshold_group = mtg.id_threshold_group
						LEFT JOIN hr_employee he2
						ON hel.created_by = he2.id_user
						LEFT JOIN master_position_detail mpd
						ON hpe.id_employee = mpd.id_employee
						LEFT JOIN master_position_routing mpr
						ON mpd.id_position_routing = mpr.id_routing
						LEFT JOIN master_branch mb
						ON mpd.id_branch = mb.id_branch
						LEFT JOIN master_position_detail mpd2
						ON mpd.parent_id_position_detail = mpd2.id_position_detail
						LEFT JOIN hr_employee he3
						ON mpd2.id_employee = he3.id_employee
						LEFT JOIN master_position_detail mpd3
						ON mpd2.parent_id_position_detail = mpd3.id_position_detail
						LEFT JOIN hr_employee he4
						ON mpd3.id_employee = he4.id_employee
						LEFT JOIN master_position_detail mpd4
						ON mpd3.parent_id_position_detail = mpd4.id_position_detail
						LEFT JOIN hr_employee he5
						ON mpd4.id_employee = he5.id_employee
						LEFT JOIN (
							SELECT hpr.id_performance_review, hpr.id_performance_evaluation, 
							hpr.period_date, hpr.treatment, hpr.review_date, j_fix.month_eval 
							FROM hr_performance_review hpr
							JOIN (
								SELECT j_review.id_performance_evaluation, min(j_review.id_performance_review) AS id_performance_review, 
								count(j_review.id_performance_evaluation) AS month_eval
								FROM (							
									SELECT hpr.id_performance_evaluation, hpr.id_performance_review
									FROM hr_performance_review hpr
									WHERE hpr.review_date IS NULL
									GROUP BY hpr.id_performance_evaluation, hpr.id_performance_review
								) AS j_review
								GROUP BY j_review.id_performance_evaluation
							) AS j_fix
							ON hpr.id_performance_review = j_fix.id_performance_review 
							AND hpr.id_performance_evaluation = j_fix.id_performance_evaluation
						) AS j_all
						ON (hpe.id_performance_evaluation = j_all.id_performance_evaluation)
						JOIN (
							SELECT DISTINCT hpr.id_performance_evaluation
							FROM hr_performance_review hpr
						) AS group_all
						ON hpe.id_performance_evaluation = group_all.id_performance_evaluation
						WHERE hel.remark_5 = 'submit' AND hel.status = 'A'
						ORDER BY hpe.id_performance_evaluation DESC		
					) AS mix
					LEFT JOIN hr_employee he
					ON mix.id_direct = he.id_employee AND he.status = 'A'
					LEFT JOIN hr_employee he2
					ON mix.id_indirect = he2.id_employee AND he2.status = 'A'
					WHERE mix.evaluation_status = 'Continue'
					) AS DATA	
					WHERE (check_date::date BETWEEN to_char(".$current_date."::date, 'YYYY-MM-10')::date 
					AND to_char(".$current_date."::date, 'YYYY-MM-'||end_month)::date)
					OR check_date::date < ".$current_date."::date";
		$result = DB::select($emp);

			$id_approval = [];
			$arr_atasan = [];
			$penampung_approval = [];
			foreach ($result as $key => $value) {
				if(!in_array($value->id_direct, $penampung_approval)){
					$penampung_approval[] = $value->id_direct;
					$id_approval[$value->id_direct][] = array(
						"id_employee" => $value->id_employee, 
						"nik_employee" => $value->nik_employee, 
						"name"=>$value->name,
						"position"=>$value->position,
						"branch"=>$value->branch,
						"period_date"=>$value->period_date,
					);
					$arr_atasan[] = $result[$key];
				} else {
					$id_approval[$value->id_direct][] = array(
						"id_employee" => $value->id_employee, 
						"nik_employee" => $value->nik_employee, 
						"name"=>$value->name,
						"position"=>$value->position,
						"branch"=>$value->branch,
						"period_date"=>$value->period_date,
					);
				}
			}
			foreach ($arr_atasan as $key => $value) {	
				$arr_atasan[$key]->child = $id_approval[$value->id_direct];
				unset($value->id_employee);unset($value->nik_employee);unset($value->name);
				unset($value->position);unset($value->branch);unset($value->period_date);
			}
		try {		
			foreach($arr_atasan as $val_mail){
				$req = new Request();			
				$req->view_file             = 'emails.kpk_reminder';				
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $val_mail->mail_direct;
				$req->cc                    = $val_mail->mail_indirect;
				$req->content_title         = $val_mail->direct_name;
			//	$req->to                    = 'agus.dwi@borwita.co.id';
			//	$req->cc                    = ['agus.fiantoro@gmail.com'];
				$req->mail_subject          = '[Review Program Perbaikan Kinerja (P2K) Reminder]HRIS';
			//	$req->content_title         = 'Agus';
				$req->content_link          = url('/e-letter/performance_plan/performance_review');
				$list_child = [];
				foreach($val_mail->child as $key=>$val_child){
					$list_child[] = [
						'name_emp'         		=> $val_child['name'],
						'nik_employee'         	=> $val_child['nik_employee'],
						'pos_emp'    	    	=> $val_child['position'],
						'branch'       			=> $val_child['branch'],
						'period_date'     	 	=> Carbon::parse($val_child['period_date'])->translatedFormat('F Y'),
					];
				}
				$req->list_child = $list_child;
			
				if($val_mail->mail_direct != null && env('APP_ENV')=='production'){
					$send = self::index($req);
					if($send){
            			\Log::info('Success. P2K Reminder to : '.$val_mail->mail_direct.' | Name : '.$val_mail->direct_name);
					} else {
            			\Log::info('Failed. P2K Reminder : '.$val_mail->mail_direct.' | Name : '.$val_mail->direct_name);
					}
				}
			}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
	}
	
	public function fpk_reminder() {
		$emp = "SELECT j_detail.count_req, mpr.description AS pos_req, mb.description AS branch, hhrh.effective_date, 
					hhrh.reason_notes, hhrh.recruitment_source, he.id_employee AS id_employee_spv, he.name AS name_spv, he.private_mail, 
					he2.id_employee, he2.name FROM public.hr_approval_transaction hat
					LEFT JOIN public.hr_employee he
					ON hat.id_employee_approval = he.id_employee
					LEFT JOIN public.hr_hiring_request_header hhrh
					ON hat.id_source_transaction = hhrh.id_hiring_request_header
					LEFT JOIN public.master_position_routing mpr
					ON hhrh.id_position_routing_request = mpr.id_routing
					LEFT JOIN public.master_branch mb
					ON hhrh.id_branch = mb.id_branch 
					LEFT JOIN public.hr_employee he2
					ON hhrh.id_employee_request = he2.id_employee
					LEFT JOIN public.master_general_data mgd
					ON hat.id_approval_status = mgd.id_general_data
					LEFT JOIN (
						SELECT hhrd.id_hiring_request_header, count(hhrd.id_hiring_request_header) AS count_req
						FROM hr_hiring_request_detail hhrd 
						GROUP BY id_hiring_request_header
					) AS j_detail
					ON hhrh.id_hiring_request_header = j_detail.id_hiring_request_header
					WHERE hat.source_transaction_type = 'FPK_Request' 
					AND mgd.code = 'Request_Approval'";
		$result = DB::select($emp);
		
		$id_approval = [];
			$arr_atasan = [];
			$penampung_approval = [];
			foreach ($result as $key => $value) {
				if(!in_array($value->id_employee_spv, $penampung_approval)){
					$penampung_approval[] = $value->id_employee_spv;
					$id_approval[$value->id_employee_spv][] = array(
						"id_employee" => $value->id_employee, 
						"name"=>$value->name,
						"pos_req"=>$value->pos_req,
						"branch"=>$value->branch,
						"count_req"=>$value->count_req,
						"effective_date"=>$value->effective_date,
						"reason_notes"=>$value->reason_notes,
						"recruitment_source"=>$value->recruitment_source,
					);
					$arr_atasan[] = $result[$key];
				} else {
					$id_approval[$value->id_employee_spv][] = array(
						"id_employee" => $value->id_employee, 
						"name"=>$value->name,
						"pos_req"=>$value->pos_req,
						"branch"=>$value->branch,
						"count_req"=>$value->count_req,
						"effective_date"=>$value->effective_date,
						"reason_notes"=>$value->reason_notes,
						"recruitment_source"=>$value->recruitment_source,
					);
				}
			}
			foreach ($arr_atasan as $key => $value) {	
				$arr_atasan[$key]->child = $id_approval[$value->id_employee_spv];
				unset($value->id_employee);unset($value->name);unset($value->pos_req);
				unset($value->branch);unset($value->count_req);unset($value->effective_date);
				unset($value->reason_notes);unset($value->recruitment_source);
			}
			
			//	dd($arr_atasan);
		try {		
			foreach($arr_atasan as $val_mail){
				$req = new Request();			
				$req->view_file             = 'emails.fpk_reminder';				
				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $val_mail->private_mail;
				$req->content_title         = $val_mail->name_spv;
			//	$req->to                    = 'agus.dwi@borwita.co.id';
			//	$req->cc                    = ['agus.dwi@borwita.co.id'];
				$req->mail_subject          = '[Approval Form Permintaan Karyawan (FPK) Reminder]HRIS';
				$req->permohonan     		= 'Form Permintaan Karyawan (FPK)';
			//	$req->content_title         = 'Agus';
				$req->content_link          = url('/employee/employee/employee_approval');
				$list_child = [];
				foreach($val_mail->child as $key=>$val_child){
					$list_child[] = [
						'emp_name'         		=> $val_child['name'],
						'pos_req'    	    	=> $val_child['pos_req'],
						'branch'       			=> $val_child['branch'],
						'count_req'         	=> $val_child['count_req'],
						'created_date'      	=> Carbon::parse($val_child['effective_date'])->format('d M Y'),
						'reason_notes'      	=> $val_child['reason_notes'],
						'recruitment_source'    => $val_child['recruitment_source'],
					];
				}
				$req->list_child = $list_child;
			/*	$send = self::index($req);
				if(!$send){
					throw new \Exception('Sending Failed');
				}
			*/
				if($val_mail->private_mail != null && env('APP_ENV')=='production'){
					$send = self::index($req);
					if($send){
            			\Log::info('Success. FPK Approval Reminder to : '.$val_mail->private_mail.' | Name : '.$val_mail->name_spv);
					} else {
            			\Log::info('Failed. FPK Approval Reminder to : '.$val_mail->private_mail.' | Name : '.$val_mail->name_spv);
					}
				}
				
			}
            return response()->json(['status' => 'true', 'message' => 'Email Request Sent Successfully !!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
	}
	
	public static function forgot_password(Request $request) {
        try {
            $email = $request->email;
            if($email==''){
                throw new \Exception('Email is required');
            }
            $get_user = self::get_user_by_mail($email);
            if(!$get_user){
                throw new \Exception('Email Not Found');
            }
			$time = strtotime(date("Y-m-d H:i:s", strtotime("+120 minutes")));
            $token = Crypt::encryptString($time);
            $update = DB::table('master_users')
                        ->where('id_user', $get_user->id_user)
                        ->update(['id_token' => $token]);

            $source_image = asset('project/public/icon/reset-password.png');

            $req = new Request();
            $req->view_file             = 'emails.forgot_password';
			$req->mail_alias            = 'HRIS Borwita';
            $req->to                    = $get_user->email;
            $req->mail_subject          = '[Reset] Password HRIS';
            $req->content_title         = 'Hai, '.$get_user->name;
            $req->content_username      = "Username : ".$get_user->user_name;
            $req->content_link          = url('/reset').'/'.$token;

            if(strpos($req->to, '@gmail') !== false) {
                $req->content_image = $source_image;
            } else {
                $source_base64 = 'data:image/png;base64,'.base64_encode(file_get_contents($source_image));
                $req->content_image = $source_base64;
            }
		
            $email_control = new EmailController();
            $send = $email_control->index($req);
            if(!$send){
                throw new \Exception('Failed to send email');
            }
            return redirect()->back()->with('success', 'Reset link sent succesfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    protected static function get_employee($nik=null) {
        $data = DB::table('hr_employee as he')
                ->select('he.*')
                ->where('he.status', '=', 'A')
                ->where('he.nik_employee', $nik)
                ->orderBy('he.name')
                ->first();
        if(is_null($data)){
            return false;
        }
        return $data;
    }
	
	 protected static function get_user_by_mail($email=null) {
        $data = DB::table('master_users as mu')
                ->join('hr_employee as he', 'he.id_user', '=', 'mu.id_user', 'left')
                ->select('mu.*', 'he.name')
                ->where('mu.status', '=', 'A')
                ->where('mu.email', $email)
                ->first();
        if(is_null($data)){
            return false;
        }
        return $data;
    }

    public function employeeRequestMobile(Request $request) {
        try {
			foreach($request->source['transaction'] as $trans_mail){
				$req = new Request();
				if($request->source['req']['code'] == 'Attendance_Correction'){
					$req->view_file         = 'emails.request_attendance';
				}
				else if($request->source['req']['code'] == 'Change_Day_off'){
					$req->view_file        	= 'emails.request_cdo';
				}
				else{
					$req->view_file         = 'emails.request_leave';
				}

				$req->mail_alias            = 'HRIS Borwita';
				$req->to                    = $trans_mail['private_mail'];
				$req->mail_subject          = '[Request]HRIS From '.$request->source['reqdetail']['name'];
				$req->content_title         = $trans_mail['name'];
				$req->reference_number      = $request->source['req']['reference_number'];
				$req->karyawan      		= $request->source['reqdetail']['name'];
				$req->creation_date      	= $request->source['req']['creation_date'];
				$req->permohonan      		= $request->source['req']['type'];
				$req->request_by      		= $request->source['req']['name'];
				
				if($request->source['req']['code'] == 'Leave_Request'){
					$req->leave_name      	= $request->source['req']['leave_type'];
					$req->qty_days      	= $request->source['reqdetail']['qty_days'];
				}
				else if($request->source['req']['code'] == 'Change_Day_off' ){
					$req->qty_days      	= $request->source['reqdetail']['qty_days'];
				}

				$req->from_date      		= $request->source['reqdetail']['request_start_to'];
				$req->to_date      			= $request->source['reqdetail']['request_end_to'];
				$req->note      			= $request->source['req']['note'];
				$req->code      			= $request->source['req']['code'];
				$req->content_link          = url('/employee/employee/employee_approval');

				if($trans_mail['sequence'] == '1'){ // HANYA ATASAN YANG DIKIRIM NOTIF WA
					$get_employee 	= $this->employee($trans_mail['id_employee_approval']);
					if(!is_null(@$get_employee->mobile_phone) || !empty(@$get_employee->mobile_phone)){
						$req->status_direct = 'direct';
						if(strpos($trans_mail['new_sequence'], '.') !== false) {
							$req->status_direct = 'indirect';
						}
						$message = $this->MessageController->messageTemplate($request->source['req']['code'], $req);

        				if (env('APP_ENV')=='production'){
        					$sendMessage = $this->MessageController->sendWhatsapp($message, @$get_employee->mobile_phone);
        				} else {
							\Log::channel('mobile_request')->info('Trial submit employee request. '.$message);
        				}
					}

					$logRequestSuccess = 'Employee Request. Email to '.@$req->to.' success. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';
					$logRequestFailed = 'Employee Request. Email to '.@$req->to.' failed. ('.@$req->permohonan.' : '.@$req->request_by.', '.@$req->reference_number.')';

					if (env('APP_ENV')=='production'){
						$send = self::index($req);
						if($send){
							\Log::channel('mobile_request')->info($logRequestSuccess);
						} else {
							\Log::channel('mobile_request')->info($logRequestFailed);
						}
					} else {
						\Log::channel('mobile_request')->info('Trial submit employee request. '.$logRequestSuccess);
					}
				}

			}
            return true;
        } catch (\Exception $e) {
			\Log::channel('mobile_request')->error('Error Send Email.');
            return true;
        }
    }

    public function employeeApprovalRejectMobile(Request $request) {
    	try {		
			$req = new Request();
			$req->view_file             = 'emails.approval_reject';
			$req->mail_alias            = 'HRIS Borwita';
			$req->to                    =  $request->source['private_mail'];
			$req->mail_subject          = '[Reject]HRIS From Approver';
			$req->content_title         = $request->source['name'];
			$req->reference_number      = $request->source['reference_number'];				
			$req->permohonan     		= $request->source['desc_request'];
			$req->note      			= $request->source['note_rejected'];
			
			if($request->source['request_code'] == 'Official_Travel'){
				$req->content_link          = url('/cash_advance/official_travel/official_travel');
			}
			else{
				$req->content_link          = url('/employee/employee/employee_request');
			}
			
			$logRequestSuccess = 'Employee Approval(Reject). Email to '.@$req->to.' success. ('.@$req->permohonan.' : '.@$req->reference_number.')';
			$logRequestFailed = 'Employee Approval(Reject). Email to '.@$req->to.' failed. ('.@$req->permohonan.' : '.@$req->reference_number.')';

			if (env('APP_ENV')=='production'){
				$send = self::index($req);
				if($send){
        			\Log::channel('mobile_approval')->info($logRequestSuccess);
				} else {
        			\Log::channel('mobile_approval')->info($logRequestFailed);
				}
			} else {
				\Log::channel('mobile_approval')->info('Trial reject employee approval. '.$logRequestSuccess);
			}
            return true;
        } catch (\Exception $e) {
			\Log::channel('mobile_approval')->error('Error Send Email.');
            return true;
        }
    }

    public function employeeApprovalApproveMobile(Request $request) {
    	try {		
			$req = new Request();
			if($request->source['request_code'] != 'Attendance_Correction'){
				if($request->source['request_code'] == 'Official_Travel'){
					$req->view_file         = 'emails.approval_travel';
					$req->travel_status		= $request->source['travel_status'];
					$req->cc				= $request->source['cc_mail'];
				}
				else{
					$req->view_file         = 'emails.approval_leave';
					$req->leave_name      	= $request->source['leave_name'];
					$req->qty_days      	= $request->source['qty_days'];
				}
			}
			else{
				$req->view_file         = 'emails.approval_attendance';
			}
			
			$req->mail_alias            = 'HRIS Borwita';
			$req->to                    =  $request->source['private_mail'];
			$req->mail_subject          = '[Approval]HRIS From Approver';
			$req->content_title         = $request->source['name'];
			$req->reference_number      = $request->source['reference_number'];				
			$req->permohonan     		= $request->source['desc_request'];
			$req->from_date      		= $request->source['request_start_to'];
			$req->to_date      			= $request->source['request_end_to'];
			$req->note      			= $request->source['note'];
			$req->code      			= $request->source['request_code'];

			if($request->source['request_code'] == 'Official_Travel'){
				$req->content_link          = url('/cash_advance/official_travel/official_travel');
			}
			else{
				$req->content_link          = url('/employee/employee/employee_request');
			}
			
			$logRequestSuccess = 'Employee Approval(Approve). Email to '.@$req->to.' success. ('.@$req->permohonan.' : '.@$req->reference_number.')';
			$logRequestFailed = 'Employee Approval(Approve). Email to '.@$req->to.' failed. ('.@$req->permohonan.' : '.@$req->reference_number.')';
			
			if (env('APP_ENV')=='production'){
				$send = self::index($req);
				if($send){
        			\Log::channel('mobile_approval')->info($logRequestSuccess);
				} else {
        			\Log::channel('mobile_approval')->info($logRequestFailed);
				}
			} else {
				\Log::channel('mobile_approval')->info('Trial approve employee approval. '.$logRequestSuccess);
			}
            return true;
        } catch (\Exception $e) {
			\Log::channel('mobile_approval')->error('Error Send Email.');
            return true;
        }
    }

	public function settlementReminder() {
		setLocale(LC_TIME, 'id-ID');
		$add10Days = now()->addDays(10);
		$data = DB::select("SELECT DISTINCT 
					hca.id_cash_advance,
					hca.id_employee,
					hca.maximum_clearing_date,
					hca.settlement_status,
					hca.reference_number
				FROM hr_cash_advance hca 
				LEFT JOIN hr_settlement_expense hse ON hca.id_cash_advance = hse.id_cash_advance
				WHERE hca.maximum_clearing_date >= now()::date
					AND hca.maximum_clearing_date <= ?
					AND hca.settlement_status = 'Not_Clear'
					AND hse.id_settlement_expense IS NULL
				ORDER BY maximum_clearing_date ASC", 
				[$add10Days->format('Y-m-d')]);
		
		foreach($data as $cashAdvance) {
			$employee = DB::table('hr_employee')->where('id_employee', $cashAdvance->id_employee)->where('status', 'A')->first();
			
			$whatsapp = new MessageController();
			$messageTitle = 'Cash Advance Settlement Reminder';
			$clearing = Carbon::parse($cashAdvance->maximum_clearing_date)->locale('id-ID')->isoFormat('dddd, d MMMM Y');
			
			$req = new Request();

			$req->mail_alias            = 'HRIS Borwita';
			$req->to                    =  $employee->private_mail;
			$req->mail_subject          = '[Reminder]Cash Advance Settlement Reminder';
			$req->content_title         = $employee->name;
			$req->reference_number		= $cashAdvance->reference_number;
			$req->content_link			= url('/official_travel/travels/settlement_travels');
			$req->to_date				= $clearing;
			$req->view_file        		= 'emails.settlement_reminder';
			// $req->reference_number      = $request->source['reference_number'];				
			// $req->permohonan     		= $request->source['desc_request'];
			// $req->from_date      		= $request->source['request_start_to'];
			// $req->to_date      			= $request->source['request_end_to'];
			// $req->note      			= $request->source['note'];
			// $req->code      			= $request->source['request_code'];

			if(env('APP_ENV')=='production'){
				if(!self::index($req)) {
					\Log::channel('scheduler')->error('Settlement Reminder: Failed sending email to '.$req->to);
				}
				$whatsapp->sendWhatsapp("*".$messageTitle."* \n\nNo. Referensi Cash Advance: ".$cashAdvance->reference_number."\n\nAnda belum melakukan settlement terhadap pengajuan anda. Mohon lakukan clearing sebelum ".$clearing.".", $employee->mobile_phone);
			}
		}
	}

	public function settlementBillingLetter($idCashAdvance) {
		$cashAdvance = DB::selectOne("SELECT 
							hca.id_cash_advance,
							hca.id_official_travel,
							hca.id_employee,
							hca.total_base_currency_difference_amount,
							hca.start_refund_date,
							hca.reference_number,
							he.nik_employee,
							he.name,
							he.private_mail,
							he.mobile_phone
						FROM hr_cash_advance hca
						JOIN hr_employee he
							ON hca.id_employee = he.id_employee 
						WHERE hca.id_cash_advance = ?", [$idCashAdvance]);
		if(!$idCashAdvance) {
			return false;
		}
		$whatsapp = new MessageController();
		$messageTitle = 'Cash Advance Billing Letter';
		$dueDate = Carbon::parse($cashAdvance->start_refund_date)->addDays(14)->locale('id-ID')->isoFormat('dddd, d MMMM Y');
		$req = new Request();

		$req->mail_alias            = 'HRIS Borwita';
		$req->to                    =  $cashAdvance->private_mail;
		$req->mail_subject          = '[Reminder] Cash Advance Billing Letter';
		$req->content_title         = $cashAdvance->name;
		$req->reference_number		= $cashAdvance->reference_number;
		$req->content_link			= route('settlementtravel.print_bill')."?id_cash_advance=".$cashAdvance->id_cash_advance;
		$req->to_date				= $dueDate;
		$req->view_file        		= 'emails.settlement_bill';
		// $req->reference_number      = $request->source['reference_number'];				
		// $req->permohonan     		= $request->source['desc_request'];
		// $req->from_date      		= $request->source['request_start_to'];
		// $req->to_date      			= $request->source['request_end_to'];
		// $req->note      			= $request->source['note'];
		// $req->code      			= $request->source['request_code'];

		if(env('APP_ENV')=='production'){
			if(!self::index($req)) {
				\Log::channel('scheduler')->error('Settlement Reminder: Failed sending email to '.$req->to);
			}
			$whatsapp->sendWhatsapp("*".$messageTitle."* \n\nNo. Referensi Cash Advance: ".$cashAdvance->reference_number."\n\nSurat tagih atas sisa dana cash advance anda telah keluar. Mohon lakukan pengembalian dana sebelum ".$dueDate.".\n\n*Jika pengembalian dilakukan setelah 14 hari dari tanggal surat tagih, maka pengajuan kasbon perjalanan dinas baru tidak dapat disetujui untuk proses pembayaran dan pengembalian sisa perjalanan dinas akan dilakukan melalui pemotongan gaji karyawan pada bulan berikutnya.*\n\nLink Surat Tagih: ".route('settlementtravel.print_bill')."?id_cash_advance=".$cashAdvance->id_cash_advance."", $cashAdvance->mobile_phone);
		}
	}

	public function settlementBillingLetterManager($idCashAdvance) {
		$cashAdvance = DB::selectOne("SELECT 
							hca.id_cash_advance,
							hca.id_official_travel,
							hca.id_employee,
							hca.total_base_currency_difference_amount,
							hca.start_refund_date,
							hca.reference_number,
							he.nik_employee,
							he.name,
							he.private_mail,
							he.mobile_phone,
							he2.name as mgr_name,
							he2.private_mail as mgr_mail,
							he2.mobile_phone as mgr_phone
						FROM hr_cash_advance hca
						JOIN hr_employee he
							ON hca.id_employee = he.id_employee 
						JOIN hr_employee he2
							ON hca.id_approval_request = he2.id_employee
						WHERE hca.id_cash_advance = ?", [$idCashAdvance]);
		if(!$idCashAdvance) {
			return false;
		}
		$whatsapp = new MessageController();
		$messageTitle = 'Cash Advance Billing Letter';
		$dueDate = Carbon::parse($cashAdvance->start_refund_date)->addDays(14)->locale('id-ID')->isoFormat('dddd, d MMMM Y');
		$req = new Request();

		$req->mail_alias            = 'HRIS Borwita';
		$req->to                    =  $cashAdvance->mgr_mail;
		$req->mail_subject          = '[Reminder] Cash Advance Billing Letter';
		$req->content_title         = $cashAdvance->mgr_name;
		$req->reference_number		= $cashAdvance->reference_number;
		$req->content_link			= route('settlementtravel.print_bill')."?id_cash_advance=".$cashAdvance->id_cash_advance;
		$req->to_date				= $dueDate;
		$req->view_file        		= 'emails.settlement_bill';
		// $req->reference_number      = $request->source['reference_number'];				
		// $req->permohonan     		= $request->source['desc_request'];
		// $req->from_date      		= $request->source['request_start_to'];
		// $req->to_date      			= $request->source['request_end_to'];
		// $req->note      			= $request->source['note'];
		// $req->code      			= $request->source['request_code'];

		if(env('APP_ENV')=='production'){
			if(!self::index($req)) {
				\Log::channel('scheduler')->error('Settlement Reminder: Failed sending email to '.$req->to);
			}
			$whatsapp->sendWhatsapp("*".$messageTitle."* \n\nNo. Referensi Cash Advance: ".$cashAdvance->reference_number."\n\nCash advance atas nama $cashAdvance->name perlu melakukan pengembalian dana. Mohon diinformasikan untuk melakukan pengembalian dana sebelum ".$dueDate.".\n\n*Jika pengembalian dilakukan setelah 14 hari dari tanggal surat tagih, maka pengajuan kasbon perjalanan dinas baru tidak dapat dilakukan.*\n\nLink Surat Tagih: ".route('settlementtravel.print_bill')."?id_cash_advance=".$cashAdvance->id_cash_advance."", $cashAdvance->mgr_phone);
		}
	}

	public static function bgenApproval($spv, $employee, $success, $bgenData) {
		$req = new Request();

		$req->mail_alias            		= 'HRIS Borwita';
		$req->to                    		=  $spv->private_mail;
		$req->mail_subject          		= '[Approval]Sales Code Approval';
		$req->content_title         		= $spv->name;
		// $req->reference_number		= $cashAdvance->reference_number;
		$req->content_link					= 'https://my.borwita.co.id';
		$salesData['sales_name']			= $employee->name;		
		$salesData['sales_branch']			= $employee->desc_branch;
		$salesData['sales_principal']		= $employee->desc_principal;
		$salesData['sales_code']			= $bgenData->sales_code;
		$req->sales_data 					= $salesData;
		// $req->to_date				= $dueDate;
		if($success == true) {
			$req->view_file        				= 'emails.approval_bgen';
		} else {
			$req->view_file						= 'emails.approval_fail_sync_bgen';
		}
		
		if(env('APP_ENV')=='production'){
			if(!self::index($req)) {
				\Log::channel('bgen')->error('BGEN Approval Notif: Failed sending email to '.$req->to);
			}
			$whatsapp = new MessageController();
			if($success == true) {
				if(!$bgenData->sales_code) {
					\Log::channel('bgen')->info("[BGEN] Sync success but sales code notification returns null: ".json_encode($bgenData));
				}
				$whatsapp->sendWhatsapp("Dear, $spv->name\n\nDengan ini kami beritahukan bahwa Request Sales Code anda telah selesai dilakukan Final Approval oleh Approver.\n\n*Name: $employee->name*\n*Branch: $employee->desc_branch*\n*Principal: $employee->desc_principal*\n*Sales Code: $bgenData->sales_code*", $spv->mobile_phone);
			} else {
				$msgInfo = "";
				$nikMsg = "";
				if($bgenData->synchronize_message) {
					$msgInfo = " dengan pesan sebagai berikut: $bgenData->synchronize_message";
				}
				if($employee->nik_employee) {
					$nikMsg = "\n*NIK: $employee->nik_employee";
				}
				$whatsapp->sendWhatsapp("Dear, $spv->name\n\nDengan ini kami beritahukan bahwa Request Sales Code anda telah selesai dilakukan Final Approval oleh Approver tetapi *sistem gagal untuk membuat sales code*$msgInfo.\n\n*Name: $employee->name*$nikMsg\n*Branch: $employee->desc_branch*\n*Principal: $employee->desc_principal*", $spv->mobile_phone);
			}
		}
	}
}
