<?php

namespace App\Http\Controllers\Setting\ResponsibilityUser;

use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;

class ResetPasswordController extends Controller {

    protected $redirectTo = '/setting/user/reset_password';

    public function index() {
        return view('setting.user.reset_password.index');
    }

    public function changePassword(Request $request) {
        $user = MasterUser::where('id_user', session('id_user'))->first();
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!\Hash::check($value, $user->password)) {
                        return $fail(__('The Current Password is incorrect.'));
                    }
                }],
            'new_password' => 'required|min:6|confirmed',
                ], [],
                [
                    'current_password' => 'Current Password',
                    'new_password' => 'New Password',
        ]);

        if (Hash::check($request->get('current_password'), $user->password)) {
            $user->password = Hash::make($request->get('new_password'));
            $user->remember_token = null;
            $user->save();
            return response()->json(['status' => 'true', 'message' => 'Password changed successfully!']);
        }
    }

    /**
     * Get a validator for an incoming change password request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function edit() {
        if (request()->ajax()) {
			$data = DB::table('master_users AS mu')
						 ->select('he.id_employee', 'mu.user_name', 'mu.email', 'mu.description_name', 'sr.*')
						->join('hr_employee AS he', 'mu.id_user', '=', 'he.id_user')
						->join(DB::raw("sp_funct_get_employee_report_all ('".session('id_company')."') sr"), 'he.id_employee', '=', 'sr.id_employee')
						->where('mu.id_user', session('id_user'))
						->where('he.status','A')
						->get()->toArray()[0];
			$data->image_attachment = session('profile_picture');
       //     $data = MasterUser::where('id_user', session('id_user'));			
		//	dd($data);
            return response()->json(['result' => $data]);
        }
    }

}
