<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\EmailController;
use App\Jobs\SyncUpdateBizApproval;
use App\Models\API\Employee as APIEmployee;
use App\Models\Employee\Employee\Employee;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Models\Curl;
use App\Models\GeneralSetting\CompanySetting\HrConfigSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use stdClass;

class EmployeeLoginController extends BaseController
{
    public function __construct()
    {
        $this->EmailController = new EmailController;
    }

    public function login(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
				'username' => 'required|string',
				'password' => 'required|string',
			//	'id_company' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }
			
			$username       = $request->username;
			$password       = $request->password;
			
			$user = DB::table('master_users as mu')
					->select('mu.*')
					->where('mu.user_name', $username)
					->first();
					
			// $id_company     = $request->id_company ?? $user->default_company;
			
			if(is_null($user)){
				throw new \Exception('Username '.$username.' tidak valid', 404);
			}
            
            $id_company     = $user->default_company;

            if($user->status == 'A'){
                $checkLogin = DB::select("SELECT cl.status,
                cl.id_user,
                cl.user_name,
                cl.password,
                cl.id_company,
                -- mc.company_name,
                -- cl.messages,
                mc.company_name 
                FROM CheckLogin ('" . $username . "' ," . $id_company . ") AS cl
                JOIN master_company mc ON mc.id_company = cl.id_company");
                if(count($checkLogin) > 0){
                    $login = $checkLogin[0];
                    $erpIntegrationFlag = @HrConfigSettings::where('id_company', $login->id_company)->first()->erp_integration;
                    $login->erp_integration_flag = $erpIntegrationFlag ?? false;
                    if ($login->status == 1) {
                        if (Hash::check($password, $login->password)) {
                            $emp = Employee::where('id_user',$login->id_user)->where('id_company',$login->id_company)->where('status','A')->first();
                            $status     = true;
                            $message    = 'Successfully Login';
                            $result       = @$login;
                            if(is_null($emp)){
                                $result->nik_employee = null;
                            }
                            else{
                                $result->mobile_serial_number = $user->mobile_serial_number;
                                $result->nik_employee	= $emp->nik_employee;	
                                $result->message = $message;								
                            }
                            
                        } else {
                            throw new \Exception('Password Salah', 422);
                        }
                    } else {
                        throw new \Exception('Username '.$username.' tidak ditemukan di Company yang dipilih', 422);
                    }
                } else {
                    throw new \Exception('Username '.$username.' tidak ditemukan', 404);
                }
            } else {
                throw new \Exception('Username Tidak Aktif', 422);
            }
			
			DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }
	
	public function createSerial(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
			$parameterValidation = [
                'username' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }
           
			$username       = $request->username;
			$serialDevice 	= $request->serial_device ?? null;
			$sql = "SELECT mu.id_user, mu.user_name, avm.version_number, mu.mobile_serial_number, mu.default_company 
				FROM master_users mu
				LEFT JOIN app_version_management avm
				ON mu.id_user = avm.id_user AND avm.status = 'A'
				WHERE mu.status = 'A' AND mu.user_name = '".$username."'";
			$result = DB::select($sql);
			if(is_null($serialDevice)){
				throw new \Exception('Device Number tidak ditemukan', 404);
			}
			if(count($result) == 0){
				throw new \Exception('Username '.$username.' tidak ditemukan', 404);
			}
			else{
				$getCheck = collect($result)->where('mobile_serial_number',$serialDevice)->first();
				if(!is_null($getCheck)){
					$mess    = 'Device Serial Number is Match';
					return $this->mobileSuccess($mess, collect($result)->first());
				}
				else{
					$update = DB::table('master_users')->where('user_name', $username)->where('status', 'A')->update(array('mobile_serial_number'=>$serialDevice));
				}			
			}
		
            $message    = 'Data was successfully Check';
            DB::commit();
            return $this->mobileSuccess($message, collect($result)->first());
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function replaceSerialNumber(Request $request)
    {
        /*
        * Trello Issue 149.004 API Post Serial Number
        */
        ini_set('max_execution_time', 300);
        $validationParam = [
            'username' => 'required',
            'serial_number' => 'required',
            'firebase_token' => 'nullable',
            'brand_manufacture' => 'nullable',
            'brand_type' => 'nullable',
            'os_version' => 'nullable',
            'ram_capacity' => 'nullable',
            'mode' => 'nullable',
        ];
        $mode = $request->mode ?? "login";
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), $validationParam);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $this->authMobile($request);

            if(!$request->serial_number) {
                throw new \Exception("Serial Number harus diisi!", 422);
            }
            $user = MasterUser::where('user_name', $request->username)
                            ->where('status', 'A')
                            ->first(["id_user", "email", "user_name", "mobile_serial_number", "default_company", "mobile_firebase_token"]);
            if(!$user) {
                throw new \Exception("Username tidak ditemukan!", 404);
            }
            
            if($mode == "login") {
                $user->mobile_serial_number = $request->serial_number;
                if($request->firebase_token) $user->mobile_firebase_token = $request->firebase_token;
                if($request->brand_manufacture) $user->mobile_brand_manufacture = $request->brand_manufacture;
                if($request->brand_type) $user->mobile_brand_type = $request->brand_type;
                if($request->os_version) $user->mobile_os_version = $request->os_version;
                if($request->ram_capacity) $user->mobile_ram_cappacity = $request->ram_capacity;
                if($user->save()) {
                    DB::commit();
                    if($user->wasChanged('mobile_firebase_token')) {
                        $erpIntegration = DB::selectOne("SELECT erp_integration, rcu.nik_employee
                                                        FROM hr_config_settings hcs
                                                        JOIN (
                                                                SELECT nik_employee, id_employee, array_agg(rcu.id_company) as id_company, he.id_user
                                                                FROM hr_employee he
                                                                JOIN relation_company_users rcu
                                                                ON he.id_user =rcu.id_user
                                                                GROUP by id_employee
                                                        ) rcu
                                                        ON hcs.id_company = any(array[rcu.id_company])
                                                        WHERE rcu.id_user = ?
                                                        AND hcs.erp_integration = TRUE", [$user->id_user]);
                        Log::channel('mobile')->info("User $user->user_name (ID: $user->id_user) has updated firebase token");
                        if($erpIntegration) {
                            $sync = new \App\Http\Controllers\Integration\Bgen\OasysController();
                            $result = $sync->syncUpdateToBgen($erpIntegration->nik_employee, false, false);
                            Log::channel('mobile')->info("Biz Approval Firebase update [$erpIntegration->nik_employee]: ".json_encode($result));
                        }
                    }

                    return $this->sendSuccess("Serial Number was successfully replaced!", $user, false);
                } else throw new \Exception("Gagal menyimpan Serial Number");
            } else if($mode == "logout") {
                if($user->mobile_serial_number != $request->serial_number) {
                    throw new \Exception("Gagal melakukan logout. Serial Number tidak ditemukan.", 422);
                }
                $user->mobile_serial_number = null;
                $user->mobile_firebase_token = null;
                $user->mobile_brand_manufacture = null;
                $user->mobile_brand_type = null;
                $user->mobile_os_version = null;
                $user->mobile_ram_cappacity = null;
                if($user->save()) {
                    DB::commit();
                    return $this->sendSuccess("User has been logged out!", $user, false);
                } else throw new \Exception("Gagal melakukan logout.");
            }
        }catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch(\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }
	
	public function getCompany(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
			$result = DB::table('master_company as mc')
					->select('mc.id_company','mc.company_code','mc.company_name')
					->where('mc.status', 'A')
					->orderBy('mc.id_company', 'ASC')
					->get();
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }
	
	public function getCompanyUser(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
			$parameterValidation = [
                'username' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }          
			$username       = $request->username;
			// $sql = "SELECT mc.id_company, mc.company_name, mu.user_name, he.nik_employee
            //         FROM relation_company_users rcu
            //         JOIN master_users mu
            //         ON rcu.id_user = mu.id_user AND mu.status = 'A'
            //         JOIN master_company mc
            //         ON rcu.id_company = mc.id_company
            //         JOIN hr_employee he
            //         ON mu.id_user = he.id_user
            //         where mu.user_name = '".$username."'";
			// $result = DB::select($sql);
            $result = APIEmployee::getCompanies($username);
			if(count($result) == 0){
				throw new \Exception('Username '.$username.' tidak ditemukan', 404);
			}					
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }
	
	public function getVersion(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
			$parameterValidation = [
                'username' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }
			$username       = $request->username;
			$sql = "SELECT avm.version_number,avm.id_user,avm.status,avm.id_company,av.description as change_log
                    from app_version_management avm
                    join app_versions av
                    on avm.version_number = av.version_number
                    left join master_users mu
                    on avm.id_user = mu.id_user
                    and avm.status = mu.status
                    where avm.status = 'A'
                    AND coalesce(mu.user_name,'$username') = '$username'
                    order by 1 desc
                    limit 1";
			$result = DB::select($sql);	
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, collect($result)->first());
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function changePassword(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $minPassLength = 6;
			$parameterValidation = [
                'username' => 'required|exists:master_users,user_name',
                'old_password' => 'required',
                'new_password' => 'required|min:'.$minPassLength,
            ];
            $validator = Validator::make($request->all(), $parameterValidation, [
                'new_password.min' => 'Panjang password minimal '.$minPassLength.'.',
                'new_password.required' => 'Password baru harus diisi.',
                'old_password.required' => 'Password lama harus diisi.'
            ]);
            if($validator->fails()){
                throw new ValidationException($validator);
            }
            if($request->old_password == $request->new_password) {
                throw new \Exception('Password baru tidak boleh sama dengan password lama!', 422);
            }
            $user = MasterUser::where('user_name', $request->username)->first();
            if(!Hash::check($request->old_password, $user->password)) {
                throw new \Exception('Pasword lama yang Anda masukkan salah.', 401);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            DB::commit();

            $message = 'Password berhasil diubah!';
            return $this->mobileSuccess($message);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404,401])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function resetPassword(Request $request) {
        ini_set('max_execution_time', 120);
        DB::beginTransaction();
        try {
            $parameterValidation = [
                'nik' => 'required'
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }
            // $email = $request->email;
            // $get_user = self::getUserByEmail($email);
            $get_user = DB::table('hr_employee as he')
                        ->join('master_users as mu', 'he.id_user', 'mu.id_user')
                        ->select('mu.*', 'he.name')
                        ->where('he.nik_employee', $request->nik)
                        ->where('he.status', 'A')
                        ->first();
            if(!$get_user){
                throw new \Exception('NIK '.$request->nik.' tidak ditemukan', 404);
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
                throw new \Exception('Email pada user anda sudah tidak valid, silahkan hubungi HR untuk update email pada user dengan email yang masih aktif', 422);
            }
            DB::commit();
            return $this->mobileSuccess('Link reset password berhasil dikirim ke email anda.');
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    protected static function getUserByEmail($email=null) {
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

}