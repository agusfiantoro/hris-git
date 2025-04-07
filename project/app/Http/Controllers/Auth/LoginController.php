<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Models\Assets\HrEmployee;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Login;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Traits\Assets\AssetsApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LoginController extends Controller {
    use AssetsApproval;
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm() {
        if (request()->session()->exists('username')) {
            return redirect('home');
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $this->validateLogin($request);
        $username       = $request->username;
        $password       = $request->password;
        $id_company     = $request->id_company;
        $company_name   = $request->company_name;
        $remember       = ($request->remember && $request->remember == 'on') ? true : false;
        //    $company_logo = $request->company_logo;
        $user = DB::table('master_users as mu')
                ->select('mu.*')
                ->where('mu.user_name', $username)
                ->first();

        $checkLogin = Login::checkLogin([
                    'username' => $username,
                    'password' => $password,
                    'id_company' => $id_company,
                    'company_name' => $company_name,
                        //    'company_logo' => $company_logo,
        ], $user);

        if ($checkLogin['status']) {
            $data = [
                'id_user'       => $checkLogin['data']->id_user,
                'access_group'  => $user->access_group,
                'id_company'    => $id_company,
                'company_name'  => $company_name,
                'username'      => $username,
                'password'      => $password,
                'module_code'   => @$checkLogin['data']->module_code,
            ];
            $this->set_session($request, $data);
            
            if($remember){
                $remember_token = Str::random(40);
                $expired = 43800; // expired for 1 month if no click logout
                $remember  = ['remember_token' => $remember_token];
                Login::remember_me(session('id_user'), $remember);
                Cookie::queue($token_remember = Cookie::make('remember_hris', $remember_token, $expired));
            }

            $redirect = [
                // null                    => 'home',
                // 'Default_User'          => 'dashboard/dashboard_user/dashboard',
                // 'Default_Manager'       => 'dashboard/dashboard_management/dashboard_management',
                // 'Default_Administrator' => 'dashboard/dashboard_administrator/dashboard_administrator',

                null                    => 'time_attendance/attendance/attendance',
                'Default_User'          => 'time_attendance/attendance/attendance',
                'Default_Manager'       => 'time_attendance/attendance/attendance',
                'Default_Administrator' => 'time_attendance/attendance/attendance',
            ];
            
            $menuPermissions = MasterUserResponsibility::get_address_menu();
            if(!in_array($redirect[$user->access_group], $menuPermissions)) {
                return redirect('home');
            }
            return redirect($redirect[$user->access_group]);
        } else {
            $this->sendFailedLoginResponse($request, $checkLogin['attribute'], $checkLogin['message']);
        }
    }

    public function set_session($request, $data) {
        $avatar = asset('public/global/img/avatar.jpg');
        $employee = DB::table('hr_employee as he')
                    ->select('he.*')
                    ->where('he.id_user', $data['id_user'])
                    ->where('he.id_company', $data['id_company'])
                    ->where('he.status', 'A')
                    ->first();
        if(@$employee->image_attachment != null){
            if(strlen(@$employee->image_attachment) > 1000){
                $avatar = "data:image;base64,".@$employee->image_attachment;
            } else {
                $urlPhoto = 'public/upload/photo/'. @$employee->image_attachment;
                if (Storage::exists($urlPhoto)) {
                    $avatar = url('project/storage/app/'.$urlPhoto);
                } 
            }
        }

        $getCompany = Login::companyLogo(['id_company' => $data['id_company']]);
        if($getCompany){
            $company_logo = $getCompany[0]->company_logo;
            $company_type = $getCompany[0]->company_type;
        } else {
            $company_logo = '';
            $company_type = '';
        }

        $request->session()->put('id_user', $data['id_user']);
        $request->session()->put('access_group', $data['access_group']);
        $request->session()->put('username', $data['username']);
        // $request->session()->put('password', $data['password']);
        $request->session()->put('id_company', $data['id_company']);
        $request->session()->put('company_name', $data['company_name']);
        // $request->session()->put('module_code', $data['module_code']);
        $request->session()->put('company_logo', $company_logo);
        $request->session()->put('company_type', $company_type);
        $request->session()->put('profile_picture', $avatar);
        $request->session()->put('app_menu', Login::generateMenu([
                    'id_user' => $data['id_user'],
                    'id_company' => $data['id_company'],
                    // 'module_code' => $data['module_code'],
        ]));
        $request->session()->put('user_right', Login::userRight([
                    'id_user' => $data['id_user'],
                    'id_company' => $data['id_company']
        ]));

        $setAddressMenuUser = MasterUserResponsibility::get_address_menu();
        $setAccessAddressMenuUser = MasterUserResponsibility::getAccessByAddressMenu();
        $access_menu  = $setAddressMenuUser;
        $additionalMenu = ['home', 'setting/user/reset_password'];

        foreach ($additionalMenu as $k => $val) {
            $access_menu[count($access_menu)] = $val; 
            $setAccessAddressMenuUser[$val][session('id_company')]['can_create'] = false;
            $setAccessAddressMenuUser[$val][session('id_company')]['can_update'] = false;
            $setAccessAddressMenuUser[$val][session('id_company')]['can_delete'] = false;
            $setAccessAddressMenuUser[$val][session('id_company')]['can_print'] = false;
        }
        $request->session()->put('address_menu', $access_menu);
        $request->session()->put('address_menu_access', $setAccessAddressMenuUser);
    }

    protected function validateLogin(Request $request) {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'id_company' => 'required',
        ]);
    }

    protected function sendFailedLoginResponse(Request $request, $attribute, $message) {
        throw ValidationException::withMessages([
            $attribute => $message,
        ]);
    }
	
	public function forgotPassword() {
        return view('auth.forgot_password');
    }

    public function newPassword($token='') {
        try {
            if($token == ''){
                throw new \Exception("Token not found");
            }

            $get = DB::table('master_users as mu')
                    ->select('mu.*', 'he.name')
                    ->join('hr_employee as he', 'he.id_user', '=', 'mu.id_user', 'left')
                    ->where('mu.id_token', $token)
                    ->first();
            if(!$get){
                throw new \Exception("Data not found");
            }
            return view('auth.reset_password', ['data' => $get]);
        } catch (\Exception $e) {
            return redirect('forgot_password')->with('error', $e->getMessage());
        }
    }

    public function reset($token='') {
        try {
            if($token == ''){
                throw new \Exception("Token not found");
            }

            $time       = strtotime(date("Y-m-d H:i:s"));
            try{
                $time_token = Crypt::decryptString($token);
            } catch (\Exception $e) {
                throw new \Exception("Reset link is invalid");
            }

            if($time > $time_token){
                throw new \Exception("Reset link is expired");
            }

            $get = DB::table('master_users as mu')
                    ->select('mu.*', 'he.name')
                    ->join('hr_employee as he', 'he.id_user', '=', 'mu.id_user', 'left')
                    ->where('mu.id_token', $token)
                    ->first();
            if(!$get){
                throw new \Exception("Data not found");
            }
            return view('auth.reset_password', ['data' => $get]);
        } catch (\Exception $e) {
            return redirect('forgot_password')->with('error', $e->getMessage());
        }
    }

    public function changePassword(Request $request) {
        try {
            $password = $request->password;
            if($password==''){
                throw new \Exception("Password can't empty");
            }
            if(strlen($password) < 6){
                throw new \Exception("Password min. 6 character");
            }
            
            $new_password = Hash::make($password);
            $master_user['password'] = $new_password;
            $master_user['id_token'] = null;
            $master_user['remember_token'] = null;
            $update = DB::table('master_users')
                        ->where('id_user', $request->id_user)
                        ->update($master_user);

            return redirect('/login')->with('success', 'Password succesfully changed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getCompany() {
        $result = Login::getCompany();
        return response()->json($result);
    }
	public function getCompany_user() {
        $res = Login::getCompany_user();
		$result = [];
		foreach($res as $key=>$val){
			$tot = Login::getNotifcom($val->id);
			if($tot['total'] != null){
				$val->total = $tot['total'];
			}
			else{
				$val->total = "";
			}
			$result[] = $val;
		}
        return response()->json($result);
    }
	public function getCompany_session(Request $request) {
        $data = [
            'id_company' => $request->id_company
        ];
        $result = Login::getCompany_session($data);
        return response()->json($result);
    }
	public function change_session(Request $request) {
        $id_company =  $request->id_company;
   //     $company_name =  $request->company_name;
        $getCompany = Login::companyLogo(['id_company' => $id_company]);
        if($getCompany){
            $company_logo = $getCompany[0]->company_logo;
            $company_name = $getCompany[0]->company_name;
            $company_type = $getCompany[0]->company_type;
        } else {
            $company_logo = '';
            $company_name = '';
            $company_type = '';
        }
		$request->session()->put('id_company', $id_company);
		$request->session()->put('company_name', $company_name);
        $request->session()->put('company_logo', $company_logo);
        $request->session()->put('company_type', $company_type);
		$request->session()->put('app_menu', Login::generateMenu([
            'id_user' => session('id_user'),
            'id_company' => $id_company,
            'module_code' => session('module_code'),
        ]));
    }
	
	public function getNotification() {
        $employee = HrEmployee::active()->where('id_user', session('id_user'))->first();
        $result['employeeApproval'] = Login::getNotificationEmployeeApproval();
        $result['revisedEmployeeRequest'] = Login::getNotificationRevisedRequest();
        $result['rejectedEmployeeRequest'] = Login::getNotificationRejectedRequest();
        if($employee) {
            $result['assetsApproval'] = $this->getApprovalData(null, $employee->id_employee, 'Request_Approval');
        }
        return response()->json($result);
    }
	public function getNotificationAnnoun() {
        $getAnnouncement = [];
        if(session('id_company')){
            $getAnnouncement = Login::getNotificationAnnoun();
        }
        $result['employeeAnnoun'] = $getAnnouncement;
        return response()->json($result);
    }

    public function checkSession(Request $request) {
        $selectedCompany = $request->selected_company;
        $path = $request->path;
        $result['status'] = true;
        if($selectedCompany != session('id_company')){
            $result['status'] = false;
        }
        return response()->json($result);
    }

    public function logout(Request $request) {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if(Cookie::get('remember_hris')){
            Cookie::queue(Cookie::forget('remember_hris'));
        }
        return redirect('/');
    }

    public function changeResponsibility(Request $request, $code) {
        $request->session()->put('app_menu', Login::generateMenu([
            'id_user' => session('id_user'),
            'id_company' => session('id_company'),
            'module_code' => $code
        ]));
        $request->session()->put('module_code', $code);
        return redirect("/");
    }

    public function getResponsibilityModules() {
        return DB::select('SELECT mm.module_code, mm.module_icon, mm.module_name FROM master_module mm WHERE status = ?', ['A']);
    }

}
