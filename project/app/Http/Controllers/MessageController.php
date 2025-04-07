<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Curl;
use DB;

class MessageController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    //contoh call : $this->MessageController->sendWhatsapp('Employee Data Saved Successfully', '089679228227');
    
    public function messageTemplate($type=null, $param=null){
        $message = "";

        if($type == 'new_account'){
            $message = "*".@$param->mail_subject."*\n\n".@$param->content_title."\n".@$param->content_username."\n\nClick this link to create new password :\n\n".@$param->content_link ;
        }
        else {// golongan request
            if(@$param->status_direct == 'direct'){
                $titlePersetujuan = 'Permohonan Persetujuan';
                $messageToLink = "\n\nSilahkan masuk pada link berikut :\n\n".@$param->content_link;
            } else {
                $titlePersetujuan = 'Informasi Persetujuan';
                $messageToLink = "\n\nJika atasan langsung dari pemohon berhalangan melakukan approval, mohon melakukan approval pada link berikut :\n\n".@$param->content_link;
            }

            if($type != 'Attendance_Correction'){
                $message = "*".$titlePersetujuan."*\n\nNo. Permohonan : ".@$param->reference_number."\nJenis Permohonan : ".@$param->permohonan."\nKaryawan : ".@$param->karyawan."\nTanggal Dibuat : ".@$param->creation_date."\nDibuat Oleh : ".@$param->request_by."\n\nLeave Name : ".@$param->leave_name."\nReq. Start Date : ".@$param->from_date."\nReq. End Date : ".@$param->to_date."\nNo. of Day (s) : ".@$param->qty_days."\nNote : ".@$param->note.$messageToLink ;
            } 
            else if($type == 'Attendance_Correction'){
                $message = "*".$titlePersetujuan."*\n\nNo. Permohonan : ".@$param->reference_number."\nJenis Permohonan : ".@$param->permohonan."\nKaryawan : ".@$param->karyawan."\nTanggal Dibuat : ".@$param->creation_date."\nDibuat Oleh : ".@$param->request_by."\nReq. Check in : ".@$param->from_date."\nReq. Check out : ".@$param->to_date."\nNote : ".@$param->note.$messageToLink ;
            }
        }
        return $message;
    }

    public function messageTemplateFpk($param=null){
        $message = "";
        $titlePersetujuan = 'Permohonan Persetujuan';
        $messageToLink = "\n\nSilahkan masuk pada link berikut :\n\n".@$param->content_link;

        $message = "*".$titlePersetujuan."*\n\nNo. Permohonan : ".@$param->reference_number."\nJenis Permohonan : ".@$param->permohonan."\nKaryawan : ".@$param->karyawan."\nTanggal Dibuat : ".@$param->creation_date."\nDibuat Oleh : ".@$param->request_by."\n\nRequest Position : ".@$param->pos_req."\nRequest Total : ".@$param->count_req."\nNote : ".@$param->note.$messageToLink ;
        
        return $message;
    }
	public function messageTemplateReco($param=null){
        $message = "";
        $titlePersetujuan = 'Permohonan Persetujuan';
        $messageToLink = "\n\nSilahkan masuk pada link berikut :\n\n".@$param->content_link;

        $message = "*".$titlePersetujuan."*\n\n
		No. Permohonan : ".@$param->reference_number."\n
		Jenis Permohonan : ".@$param->permohonan."\n
		Tanggal Dibuat : ".@$param->creation_date."\n
		Dibuat Oleh : ".@$param->request_by."\n\n
		Karyawan : ".@$param->karyawan."\n
		Position : ".@$param->position."\n
		Note : ".@$param->note.$messageToLink ;
        
        return $message;
    }
	
	public function messageTemplateKpk($param=null){
        $message = "";
        $titlePersetujuan = 'BA Penetapan P2K';
        $messageToLink = "\n\nSilahkan masuk pada link berikut :\n\n".@$param->content_link;

        $message = "*".$titlePersetujuan."*\n\n
		No. Pemberitahuan : ".@$param->reference_number."\n
		Tanggal Dibuat : ".@$param->creation_date."\n
		Dibuat Oleh : ".@$param->request_by."\n
		Bulan P2K : ".@$param->month."\n
		Performance (P3M) : ".@$param->month_performance."\n
		Karyawan : ".@$param->text_emp."\n
		Note : Jika Anda sebagai Atasan Langsung (Direct Supervisor), maka Anda wajib melakukan Review dan mencatat hasil Review didalam HRIS my.borwita.co.id\n".$messageToLink;
        
        return $message;
    }
	
	public function messageTemplateQuali($param=null){
        $message = "";
        $titlePersetujuan = 'Permohonan Penilaian 360 Feedback';
        $messageToLink = "\n\nDear, Bpk/Ibu ".@$param->content_title."\n*Mohon lakukan penilaian Qualitative (360 Feedback).*\nSilahkan masuk pada link berikut :\n".@$param->content_link;

        $message = "*".$titlePersetujuan."*\n\n
		No. Permohonan : ".@$param->reference_number."\n
		Jenis Permohonan : ".@$param->permohonan."\n
		Tanggal Dibuat : ".@$param->creation_date."\n
		Dibuat Oleh : ".@$param->request_by."\n\n
		Karyawan : ".@$param->karyawan."\n
		Position : ".@$param->position."\n
		Note : ".@$param->note.$messageToLink ;
        
        return $message;
    }
	
	public function messageTemplateTravel($param=null){
        $message = "";
        $titlePersetujuan = 'Permohonan Persetujuan';
        $messageToLink = "\n\nSilahkan masuk pada link berikut :\n\n".@$param->content_link;

		$message = "*".$titlePersetujuan."*\n\nNo. Permohonan : ".@$param->reference_number."\nJenis Permohonan : ".@$param->jenis_permohonan."\nKaryawan : ".@$param->name."\nNIK : ".@$param->nik_employee."\nPosition : ".@$param->dec_position."\nTanggal Dibuat : ".@$param->letter_date."\nDibuat Oleh : ".@$param->name."\n\nTanggal Perjalanan : ".@$param->start_date." - ".@$param->end_date."\nNote : ".@$param->location_to." (".@$param->reason_notes.")\nStatus : ".@$param->travel_status.$messageToLink ;

        return $message;
    }

    public function whatsapp(Request $request){
        $message    = $request->message ?? '-';
        $phone      = $request->phone ?? null;
        $type       = $request->type ?? 'message';
        $dataToSend = $request->dataToSend ?? null;

        $this->sendWhatsapp($message, $phone, $type, $dataToSend);
    }

    public function sendWhatsapp($message='', $phone, $type='message', $dataToSend=null){
        ini_set('max_execution_time', -1);
        try{
            if(is_null($phone) || $phone == ''){
                throw new \Exception("No phone number");
            }
            if(strlen($phone) < 6){
                throw new \Exception("(".$phone.") format phone is not eligible");
            }
            $phoneNumber = curl::formatPhone($phone);
            $data = ['phone' => $phoneNumber];

            if($type=='message'){
                $wablas    = curl::findApi('wablas_message');
                $data['message'] = $message;
            }
            else if($type=='image'){
                $wablas    = curl::findApi('wablas_image');
                $data['image'] = $dataToSend;
                $data['caption'] = $message;
            }
            else if($type=='document'){
                $wablas    = curl::findApi('wablas_document');
                $data['document'] = $dataToSend;
                $data['caption'] = $message;
            }
            else if($type=='video'){
                $wablas    = curl::findApi('wablas_video');
                $data['video'] = $dataToSend;
                $data['caption'] = $message;
            }

            if(!$wablas){
                throw new \Exception("API not found");
            }
            $token      = trim($wablas->token);
            $url        = $wablas->url;

            $headers = [
                'Authorization: ' .$token
            ];

            $send = curl::_send_($url, $data, $headers, 'POST', false, true);
            $log_message = $phoneNumber.' : '.$message;

            if(!$send){
                throw new \Exception("Failed. ".$log_message);
            } else if ($send->status != true) {
                throw new \Exception("Failed. ".$log_message.' | '. @$send->message);
            }
            \Log::channel('whatsapp')->info('Success Whatsapp. '.$log_message);
            return true;
        } catch (\Exception $e) {
            \Log::channel('whatsapp')->error($e->getMessage());
            return false;
        }
    }

}
