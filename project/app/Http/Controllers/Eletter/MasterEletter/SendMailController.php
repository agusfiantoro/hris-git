<?php

namespace App\Http\Controllers\Eletter\MasterEletter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Eletter\MasterEletter\ElectronicLetter;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Log;
use App\Models\Employee\Employee\Employee;

class SendMailController extends Controller
{
	public function send_mail(Request $request,$token,$id_letter)
	{
		try {
			DB::beginTransaction();
			if ($request->type == "SKI") {
				$data = ElectronicLetter::leftJoin('master_general_data as data_category','data_category.id_general_data','=','hr_electronic_letter.id_category')
				->leftJoin('master_general_data as data_surat','data_surat.id_general_data','=','hr_electronic_letter.id_letter_type')
				->select(
					\DB::RAW('data_surat.description as nama_surat'),
					\DB::RAW('data_surat.code as singkatan_surat'),
					\DB::RAW('data_category.description as category_surat'),
					\DB::RAW('hr_electronic_letter.remark_1 as name'),
					\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
					\DB::RAW('hr_electronic_letter.token as token'),
					\DB::RAW('hr_electronic_letter.email as email')
				)
				->where('hr_electronic_letter.token',$token)
				->where('hr_electronic_letter.id_letter',$id_letter)
				->first();
			}elseif ($request->type == "Other Letter") {
				$data = ElectronicLetter::leftJoin('master_general_data as data_category','data_category.id_general_data','=','hr_electronic_letter.id_category')
				->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
				->select(
					\DB::RAW('data_category.description as nama_surat'),
					\DB::RAW('data_category.code as singkatan_surat'),
					\DB::RAW('data_category.description as category_surat'),
					\DB::RAW('hr_employee.name as name'),
					\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
					\DB::RAW('hr_electronic_letter.token as token'),
					\DB::RAW('hr_electronic_letter.email as email')
				)
				->where('hr_electronic_letter.id_letter',$id_letter)
				->first();
			}
			else{
				$data = ElectronicLetter::leftJoin('master_general_data as data_category','data_category.id_general_data','=','hr_electronic_letter.id_category')
				->leftJoin('master_general_data as data_surat','data_surat.id_general_data','=','hr_electronic_letter.id_letter_type')
				->leftJoin('hr_employee','hr_employee.id_employee','=','hr_electronic_letter.id_employee')
				->select(
					\DB::RAW('data_surat.description as nama_surat'),
					\DB::RAW('data_surat.code as singkatan_surat'),
					\DB::RAW('data_category.description as category_surat'),
					\DB::RAW('data_category.code as category_code'),
					// \DB::RAW('COALESCE(hr_electronic_letter.remark_3, hr_employee.name) as name'),
					\DB::RAW('hr_electronic_letter.remark_3 as remark_3'),
					\DB::RAW('hr_employee.name as name'),
					\DB::RAW('hr_electronic_letter.id_letter as id_letter'),
					\DB::RAW('hr_electronic_letter.token as token'),
					\DB::RAW('hr_electronic_letter.email as email')
				)
				->where('hr_electronic_letter.token',$token)
				->where('hr_electronic_letter.id_letter',$id_letter)
				->first();
			}
			if (!empty($data)) {
				if ($data->singkatan_surat == "SKK") {
					if ($data->remark_3 == NULL) {
						$name = $data->name;
					}else{
						$remark_3 =  explode(';', $data->remark_3);
						$name = $remark_3[0];
					}
				}else{
					$name = $data->name;
				}
				if ($data->email != NULL) {
					if ($request->type == "Other Letter") {
						$url = '-';
					}else{
						$singkatan_url_print = strtolower($data->singkatan_surat);
						if ($data->singkatan_surat == 'SP3') {
							$singkatan_url_print = 'sp';
						}else{
							$singkatan_url_print = strtolower($data->singkatan_surat);
						}
						$url = route('print.'.$singkatan_url_print,$data->token);
					}
					$details = [
						'name'=>$name,
						'detail_surat'=>$data->nama_surat,
						'code_surat'=>$data->singkatan_surat,
						'category'=>$data->category_surat,
						'category_code'=>$data->category_code,
						'url'=>$url,
						'type_surat'=>$request->type,
						'subject'=>'#E-letter '.$data->nama_surat.' / '.$data->singkatan_surat.' '.$data->category_surat.' - '.$name
					];
					\Mail::to($data->email)->send(new \App\Mail\SendMail($details));
					return response()->json(['status'=>'success']);
				}else{
					return response()->json(['status'=>'email_null','message'=>'Email address is empty.']);
				}
			}else{
				return response()->json(['status'=>'error']);
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Send Mail !! [' . $e->getMessage() . ']']);
		}
	}
}
