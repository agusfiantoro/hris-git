<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Login;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Http\Controllers\API\BaseController;

class LoginVerification {

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next) {

        if (empty(Session('id_user'))) {
            if($request->cookie('remember_hris')){
                $redirect = '/';
            } else {
                $redirect = '/login';
            }
			try {
                $mobile = new BaseController();
                $mobile->authMobile($request);
                return $next($request);
            } catch(\Exception $e) {
            }
            return redirect($redirect);
        } else {
            $findUser = DB::table('master_users as mu')->where('mu.id_user', session('id_user'))->where('status', 'A')->count(\DB::raw(1));
            if(!$findUser){
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login')->with('error', 'Please login first');
            }

            if(!$request->ajax()){
                $setAddressMenuUser = MasterUserResponsibility::get_address_menu();
                $setAccessAddressMenuUser = MasterUserResponsibility::getAccessByAddressMenu();
                $access_menu  = $setAddressMenuUser;
                $additionalMenu = ['home', 'setting/user/reset_password', 'forgot_password'];

                if(in_array('learning_management/lms/class_room', $access_menu)){
                    $additionalMenu[] = 'learning_management/lms/class_room/class'; 
                }

                foreach ($additionalMenu as $k => $val) {
                    $access_menu[count($access_menu)] = $val; 
                    $setAccessAddressMenuUser[$val][session('id_company')]['can_create'] = false;
                    $setAccessAddressMenuUser[$val][session('id_company')]['can_update'] = false;
                    $setAccessAddressMenuUser[$val][session('id_company')]['can_delete'] = false;
                    $setAccessAddressMenuUser[$val][session('id_company')]['can_print'] = false;
                }
                $request->session()->put('address_menu', $access_menu);
                $request->session()->put('address_menu_access', $setAccessAddressMenuUser);

                if(in_array('employee/employee_setting/workdays', $access_menu) || in_array('time_attendance/attendance/attendance_list', $access_menu)){
                    $access_menu[count($access_menu)] = 'time_attendance/_export'; 
                }
                if(in_array('employee/employee/employee_survey_management', $access_menu)){
                    $access_menu[count($access_menu)] = 'employee/summary_survey'; 
                }
                if(in_array('kpi/report_pa/report_360', $access_menu)){
                    $access_menu[count($access_menu)] = 'kpi/report_360/export'; 
                    $access_menu[count($access_menu)] = 'kpi/report_360/export_detail'; 
				}
				if(in_array('kpi/360_feedback/pa_qualitative', $access_menu)){
                    $access_menu[count($access_menu)] = 'kpi/360_feedback/export_detail'; 
                }
                if(in_array('kpi/report_pa/report_kpi', $access_menu)){
                    $access_menu[count($access_menu)] = 'kpi/report_kpi/export'; 
                }
                if(in_array('kpi/fpr/fpr_management', $access_menu)){
                    $access_menu[count($access_menu)] = 'kpi/fpr/fpr_management/download'; 
                }
                if(in_array('kpi/fpr/fpr_subordinate', $access_menu)){
                    $access_menu[count($access_menu)] = 'kpi/fpr/fpr_subordinate/download'; 
                }
				if(in_array('kpi/fpr/fpr_self', $access_menu)){
                    $access_menu[count($access_menu)] = 'kpi/fpr/fpr_self/download'; 
                }
				if(in_array('recruitment/recruitment/candidate', $access_menu)){
                    $access_menu[count($access_menu)] = 'recruitment/recruitment/candidate/download'; 
                    
                }
                if(in_array('recruitment/personality_assessment/interview_question', $access_menu)) {
                    $access_menu[count($access_menu)] = 'recruitment/personality_assessment/interview_question/print';
                }
                if(in_array('recruitment/personality_assessment/interview_question_summary', $access_menu)) {
                    $access_menu[count($access_menu)] = 'recruitment/personality_assessment/interview_question_summary/print_bei';
                }
                if(in_array('recruitment/psychotest/psychogram', $access_menu)){
                    $access_menu[count($access_menu)] = 'recruitment/psychotest/psychogram/download'; 
                    $access_menu[count($access_menu)] = 'recruitment/psychotest/master_batch/download_zip';
                }
                if(in_array('learning_management/lms/summary_event', $access_menu)){
                    $access_menu[count($access_menu)] = 'learning_management/lms/summary_event/download';
                    $access_menu[count($access_menu)] = 'learning_management/lms/summary_event/download_detail';
                    $access_menu[count($access_menu)] = 'learning_management/lms/summary_event/download_multiple_programs';
                }
                if(in_array('employee/employee/employee_all', $access_menu)){
                    $access_menu[count($access_menu)] = 'employee/employee/download_attendance'; 
                    $access_menu[count($access_menu)] = 'employee/employee/download_headcount'; 
                    $access_menu[count($access_menu)] = 'employee/employee/download_lapkar'; 
                    $access_menu[count($access_menu)] = 'employee/employee/download_attendance_summary'; 
                }
                if(in_array('talent_management/talent_development/talent_recomendation', $access_menu)) {
                    $access_menu[count($access_menu)] = 'talent_management/talent_development/talent_recomendation/export_talent'; 
                }
				
				if(in_array('talent_management/talent_development/talent_profile', $access_menu)){
                    $access_menu[count($access_menu)] = 'talent_management/talent_development/talent_profile/download';
                    $access_menu[count($access_menu)] = 'talent_management/talent_development/talent_profile/export_zip';
                }
				
				if(in_array('integration/bgen/sales_code_bgen', $access_menu)) {
                    $access_menu[count($access_menu)] = 'integration/bgen/sales_code_bgen/export'; 
                }
				if(in_array('employee/employee/recommendation_form', $access_menu)) {
                    $access_menu[count($access_menu)] = 'employee/employee/recommendation_form/download'; 
                }	
				if(in_array('e-letter/performance_plan/kpk_letter', $access_menu)) {
                    $access_menu[count($access_menu)] = 'e-letter/performance_plan/kpk_letter/download'; 
                }
				if(in_array('e-letter/performance_plan/performance_review', $access_menu)) {
                    $access_menu[count($access_menu)] = 'e-letter/performance_plan/performance_review/download'; 
                }	
				if(in_array('e-letter/performance_plan/performance_monitoring', $access_menu)) {
                    $access_menu[count($access_menu)] = 'e-letter/performance_plan/performance_monitoring/export'; 
                }		
                if(in_array('transfer_asset/transfer_transaction/receiving_transfer_asset', $access_menu)) {
                    $access_menu[count($access_menu)] = 'transfer_asset/transfer_transaction/receiving_transfer_asset/print'; 
                }
                if(in_array('addition_asset/quick_additions/asset_history', $access_menu)) {
                    $access_menu[count($access_menu)] = 'addition_asset/quick_additions/asset_history/print_label'; 
                }	
                if(in_array('learning_management/lms/summary_event', $access_menu)) {
                    $access_menu[count($access_menu)] = 'learning_management/lms/summary_event/download_course_report';
                }
                if(!in_array($request->path(), $access_menu)){
                    echo "<script>alert('Access Not Allowed'); window.location.href = '".url('home')."'; </script>";  
                }
            }
            return $next($request);
        }
    }

}
