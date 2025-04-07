<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

$proxy_url    = getenv('PROXY_URL');
$proxy_schema = getenv('PROXY_SCHEMA');
if (!empty($proxy_url)) {
    \URL::forceRootUrl($proxy_url);
}
if (!empty($proxy_schema)) {
    \URL::forceScheme($proxy_schema);
}
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

// authentication routes
// Route::get('/', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/home_user', [App\Http\Controllers\HomeController::class, 'home']);
Route::get('/read_file', [App\Http\Controllers\HomeController::class, 'read_file']);
Route::get('/delete_attendance_image/{month?}/{id?}', [App\Http\Controllers\HomeController::class, 'delete_attendance_image']);

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/change_session', [\App\Http\Controllers\Auth\LoginController::class, 'change_session']);
Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('us.logout');
Route::get('/forgot_password', [\App\Http\Controllers\Auth\LoginController::class, 'forgotPassword'])->name('forgotPassword');
Route::get('/newPassword/{token}', [\App\Http\Controllers\Auth\LoginController::class, 'newPassword']);
Route::get('/reset/{token}', [\App\Http\Controllers\Auth\LoginController::class, 'reset']);
Route::post('/reset/save', [\App\Http\Controllers\Auth\LoginController::class, 'changePassword']);
Route::get('/getCompany', [\App\Http\Controllers\Auth\LoginController::class, 'getCompany']);
Route::get('/getCompany_user', [\App\Http\Controllers\Auth\LoginController::class, 'getCompany_user']);
Route::get('/getCompany_session', [\App\Http\Controllers\Auth\LoginController::class, 'getCompany_session']);
Route::get('/getNotification', [\App\Http\Controllers\Auth\LoginController::class, 'getNotification'])->name('getNotification');
Route::get('/getNotificationAnnoun', [\App\Http\Controllers\Auth\LoginController::class, 'getNotificationAnnoun'])->name('getNotificationAnnoun');

Route::get('/home', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'index'])->middleware('login-verification');
Route::get('/survey/{token}', [App\Http\Controllers\Employee\EmployeeSurvey\EmployeeSurveyController::class, 'toSurvey']);
Route::get('/checkSession', [App\Http\Controllers\Auth\LoginController::class, 'checkSession']);

Route::get('/assessment/result', [App\Http\Controllers\AssessmentController::class, 'result']);

Route::get('/change-responsibility/{code}', [\App\Http\Controllers\Auth\LoginController::class, 'changeResponsibility'])->middleware('login-verification')->name('change_responsibility');
Route::get('/get-responsibility', [\App\Http\Controllers\Auth\LoginController::class, 'getResponsibilityModules'])->middleware('login-verification')->name('get_modules');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('login-verification');
// Route::post('/home/dashboardinformation', [App\Http\Controllers\HomeController::class, 'dashboardinformation'])->middleware('login-verification');

Route::prefix('dashboard')->middleware('login-verification')->group(function () {
	Route::get('dashboard_user/dashboard', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'index']); // for user
	Route::get('dashboard_administrator/dashboard_administrator', [App\Http\Controllers\Dashboard\DashboardAdministratorController::class, 'index']); // for admin
    Route::get('dashboard_management/dashboard_management', [App\Http\Controllers\Dashboard\DashboardAdministratorController::class, 'index']); // for management
    Route::get('dashboard_management/dashboard_management/cash_advance_reports', [App\Http\Controllers\Dashboard\DashboardAdministratorController::class, 'cashAdvanceReports']);

    Route::post('dashboard_information_user', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'dashboard_information_user']);
    Route::post('dashboard_information_admin', [App\Http\Controllers\Dashboard\DashboardAdministratorController::class, 'dashboard_information_admin'])->name('dashboard.dashboard_information_admin');

    Route::get('birthday', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'birthday']);
    Route::get('contract_expired', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'employee_contract_expired'])->name('dashboard.contract_expired');
    Route::get('getEmployeeByAccessGroup', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'getEmployeeByAccessGroup'])->name('dashboard.getEmployeeByAccessGroup');
    Route::get('leave_information', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'leave_information']);
    Route::get('employee_by_department', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'employee_by_department']);
    Route::get('hierarchy', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'hierarchy']);
    Route::get('employee/{id}', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'employeePositionAll']);
    Route::get('attendanceStatus', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'attendanceStatus']);
    Route::get('news', [App\Http\Controllers\Dashboard\DashboardUserController::class, 'news']);

});

Route::prefix('setting')->middleware('login-verification')->group(function () {
    Route::get('user/reset_password', [App\Http\Controllers\Setting\ResponsibilityUser\ResetPasswordController::class, 'index'])->name('user.index');
    Route::get('user/reset_password/edit', [App\Http\Controllers\Setting\ResponsibilityUser\ResetPasswordController::class, 'edit']);
    Route::post('user/reset_password/change_password', [App\Http\Controllers\Setting\ResponsibilityUser\ResetPasswordController::class, 'changePassword'])->name('user.change_password');
});

Route::prefix('e-letter')->middleware('login-verification')->group(function () {
    // Employee Work Agreement
    Route::get('employee_agreement/employee_work_agreement', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'pkk_index'])->name('pkk.index');
    Route::get('employee_agreement/employee_work_agreement/get_data_new', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'get_data_new']);
    Route::get('employee_agreement/employee_work_agreement/get_position', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'get_position']);
    Route::get('employee_agreement/employee_work_agreement/change_branch', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'change_branch']);
    Route::get('employee_agreement/employee_work_agreement/change_employee', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'change_employee']);
    // Route::get('employee_agreement/employee_work_agreement/get_location', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'get_location']);
    Route::post('employee_work_agreement/create-save', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'save'])->name('pkk.save');
    Route::get('employee_work_agreement/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'get_edit']);
    Route::post('employee_work_agreement/update-save', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'update'])->name('pkk.update');
    Route::get('employee_work_agreement/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'destroy']);

    // Freelance Work Agreement
    Route::get('employee_agreement/freelance_work_agreement', [App\Http\Controllers\Eletter\MasterEletter\FreelanceWorkAgreementController::class, 'pkhl_index'])->name('pkhl.index');
    Route::get('employee_agreement/freelance_work_agreement/get_data_new', [App\Http\Controllers\Eletter\MasterEletter\FreelanceWorkAgreementController::class, 'get_data_new']);
    Route::get('employee_agreement/freelance_work_agreement/get_position', [App\Http\Controllers\Eletter\MasterEletter\FreelanceWorkAgreementController::class, 'get_position']);
    Route::get('employee_agreement/freelance_work_agreement/change_branch', [App\Http\Controllers\Eletter\MasterEletter\EmployeeWorkAgreementController::class, 'change_branch']);
    Route::post('freelance_work_agreement/create-save', [App\Http\Controllers\Eletter\MasterEletter\FreelanceWorkAgreementController::class, 'save'])->name('pkhl.save');
    Route::get('freelance_work_agreement/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\FreelanceWorkAgreementController::class, 'get_edit']);
    Route::post('freelance_work_agreement/update-save', [App\Http\Controllers\Eletter\MasterEletter\FreelanceWorkAgreementController::class, 'update'])->name('pkhl.update');
    Route::get('freelance_work_agreement/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\FreelanceWorkAgreementController::class, 'destroy']);

    // Internal Memo
    Route::get('internal_memo/internal_memo', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'index'])->name('im.index');
    Route::get('internal_memo/get_data_new', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'get_data_new']);
    Route::get('get-chief/employee', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'get_chief_name']);
    Route::get('internal_memo/get_chief', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'get_chief']);
    Route::post('internal_memo/create-save', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'save'])->name('save.im');
    Route::get('internal_memo/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'get_edit']);
    Route::post('internal_memo/update/save', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'edit'])->name('edit.im');
    Route::get('internal_memo/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'destroy']);
	Route::get('internal_memo/internal_memo/get_im_career', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'get_career_im']);
	Route::get('internal_memo/internal_memo/change_region', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'change_region']);
	Route::get('internal_memo/internal_memo/change_branch', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'change_branch']);
	Route::get('internal_memo/internal_memo/changeEmployeeCategoryTA', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'change_employee_ta']);

    // Internal Memo Management
    Route::get('internal_memo/internal_memo_management', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'index'])->name('imm.index');
    Route::get('internal_memo_management/get_data', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'get_data']);
    Route::get('internal_memo_management/get_region', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'get_region']);
    Route::post('internal_memo/internal_memo_management/create-save', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'save'])->name('save.imm');
    Route::get('internal_memo/internal_memo_management/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'get_edit']);
    Route::post('internal_memo/internal_memo_management/update-save', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'edit'])->name('edit.imm');
    Route::get('internal_memo/internal_memo_management/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'destroy']);
    Route::get('internal_memo/internal_memo_management/get_view/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoManagementController::class, 'get_view']);

    // Surat Keterangan Kerja
    Route::get('decree/employment_certificate', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'index'])->name('skk.index');
    Route::get('decree/employment_certificate/get_data', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'get_data']);
    Route::get('decree/employment_certificate/get_employee', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'get_employee']);
    Route::get('decree/employment_certificate/change_employee', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'change_employee']);
    Route::post('decree/employment_certificate/create-save', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'save'])->name('save.skk');
    Route::get('decree/employment_certificate/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'get_edit']);
    Route::post('decree/employment_certificate/update-save', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'edit'])->name('edit.skk');
    Route::get('decree/employment_certificate/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'destroy']);
    Route::get('employment_certificate/get_view-{token}', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'get_view']);
    Route::post('employment_certificate/publish-save', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'publish'])->name('save.publish');
    

    // SP
    Route::get('company_letter/warning_letter', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'index'])->name('sp.index');
    Route::get('company_letter/warning_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'get_data']);
    Route::get('company_letter/warning_letter/get_category', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'get_category']);
    Route::get('company_letter/warning_letter/change_employee', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'change_employee']);
    Route::post('company_letter/warning_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'save'])->name('save.sp');
    Route::get('company_letter/warning_letter/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'get_edit']);
    Route::post('company_letter/warning_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'edit'])->name('edit.sp');
    Route::get('company_letter/warning_letter/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'destroy']);
    Route::get('warning_letter/view-{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'get_view']);


    // SWP
    Route::get('company_letter/stern_warning_letter', [App\Http\Controllers\Eletter\MasterEletter\SternWarningLetterController::class, 'index'])->name('swp.index');
    Route::get('company_letter/stern_warning_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\SternWarningLetterController::class, 'get_data']);
	Route::post('company_letter/stern_warning_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\SternWarningLetterController::class, 'save'])->name('save.sp3');

    // SUPA
    Route::get('company_letter/invitation_letter', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'index'])->name('supa.index');
    Route::get('company_letter/invitation_letter/get_supa2/data', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'get_supa2_data'])->name('supa2.data');
    Route::get('company_letter/invitation_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'get_data']);
    Route::get('company_letter/invitation_letter/get_supa_1', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'get_supa1']);
    Route::get('company_letter/invitation_letter/select_supa_1', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'select_supa1']);
    Route::get('company_letter/invitation_letter/select_supa_2', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'select_supa2']);
    Route::post('company_letter/invitation_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'save'])->name('save.supa');
    Route::get('invitation_letter/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'get_edit']);
    Route::post('company_letter/invitation_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'edit'])->name('edit.supa');
    Route::get('invitation_letter/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'destroy']);

    // PB
    Route::get('employee_agreement/collective_agreement', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'index'])->name('pb.index');
    Route::get('employee_agreement/collective_agreement/get_data', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'get_data']);
    Route::get('employee_agreement/collective_agreement/get_employee', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'get_employee']);
    Route::get('employee_agreement/collective_agreement/change_employee', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'change_employee']);
    // Route::get('employee_agreement/collective_agreement/get_position', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'get_position']);
    Route::get('employee_agreement/collective_agreement/change_branch', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'change_branch']);
    Route::post('employee_agreement/collective_agreement/create-save', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'save'])->name('save.pb');
    Route::get('employee_agreement/collective_agreement/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'get_edit']);
    Route::post('employee_agreement/collective_agreement/update-save', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'edit'])->name('edit.pb');
    Route::get('collective_agreement/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\CollectiveAgreementController::class, 'destroy']);

    // EXT
    Route::get('company_letter/external_letter', [App\Http\Controllers\Eletter\MasterEletter\ExternalLetterController::class, 'index'])->name('ext.index');
    Route::get('company_letter/external_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\ExternalLetterController::class, 'get_data']);
    Route::get('company_letter/external_letter/get_branch', [App\Http\Controllers\Eletter\MasterEletter\ExternalLetterController::class, 'get_branch']);
    Route::post('company_letter/external_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\ExternalLetterController::class, 'save'])->name('save.ext');
    Route::get('company_letter/external_letter/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\ExternalLetterController::class, 'get_edit']);
    Route::post('company_letter/external_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\ExternalLetterController::class, 'edit'])->name('edit.ext');
    Route::get('company_letter/external_letter/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\ExternalLetterController::class, 'destroy']);

    // SK
    Route::get('decree/statement_letter', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'index'])->name('sk.index');
    Route::get('decree/statement_letter/get_career', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'get_career'])->name('sk.career');
    Route::get('decree/statement_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'get_data']);
    Route::get('decree/statement_letter/get_employee', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'get_employee']);
    Route::get('decree/statement_letter/change_career', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'change_career']);
    Route::post('decree/statement_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'save'])->name('save.sk');
    Route::get('decree/statement_letter/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'get_edit']);
    Route::post('decree/statement_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'edit'])->name('edit.sk');
    Route::get('decree/statement_letter/delete/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'destroy']);


    // SKP
    Route::get('decree/termination_letter', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'index'])->name('skp.index');
    Route::get('decree/termination_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'get_data']);
    Route::get('decree/termination_letter/get_career', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'get_career']);
    Route::get('decree/termination_letter/change_career', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'change_career']);
    Route::post('decree/termination_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'save'])->name('save.skp');
    Route::get('decree/termination_letter/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'get_edit']);
    Route::post('decree/termination_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'edit'])->name('edit.skp');
    Route::get('decree/termination_letter/destroy/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\TerminationLetterController::class, 'destroy']);

    // SKI
    Route::get('decree/internship_certificate', [App\Http\Controllers\Eletter\MasterEletter\InternshipCertificateController::class, 'index'])->name('ski.index');
    Route::get('decree/internship_certificate/get_data', [App\Http\Controllers\Eletter\MasterEletter\InternshipCertificateController::class, 'get_data']);
    Route::post('decree/internship_certificate/create-save', [App\Http\Controllers\Eletter\MasterEletter\InternshipCertificateController::class, 'save'])->name('save.ski');
    Route::get('decree/internship_certificate/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternshipCertificateController::class, 'get_edit']);
    Route::post('decree/internship_certificate/update-save', [App\Http\Controllers\Eletter\MasterEletter\InternshipCertificateController::class, 'edit'])->name('edit.ski');
    Route::get('decree/internship_certificate/destroy/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternshipCertificateController::class, 'destroy']);

    Route::get('notif/send-mail/{token}-{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\SendMailController::class, 'send_mail'])->name('send.mail');

    // Master Letter
    Route::get('master_letter/master_letter', [App\Http\Controllers\Eletter\MasterEletter\MasterLetterController::class, 'index'])->name('ml.index');
    Route::get('master_letter/master_letter/get_sequence', [App\Http\Controllers\Eletter\MasterEletter\MasterLetterController::class, 'get_sequence']);
    Route::post('master_letter/master_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\MasterLetterController::class, 'save'])->name('save.ml');
    Route::get('master_letter/master_letter/get_edit/{id_general_data}', [App\Http\Controllers\Eletter\MasterEletter\MasterLetterController::class, 'get_edit']);
    Route::post('master_letter/master_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\MasterLetterController::class, 'edit'])->name('edit.ml');
    Route::get('master_letter/master_letter/destroy/{id_general_data}', [App\Http\Controllers\Eletter\MasterEletter\MasterLetterController::class, 'destroy']);

    // Other Letter
    Route::get('company_letter/other_letter', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'index'])->name('other.index');
    Route::get('company_letter/other_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'get_data']);
    Route::get('company_letter/other_letter/get_branch', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'get_branch']);
    Route::get('company_letter/other_letter/change_employee', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'change_employee']);
    Route::post('company_letter/other_letter/create-save', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'save'])->name('save.other');
    Route::get('company_letter/other_letter/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'get_edit']);
    Route::post('company_letter/other_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'edit'])->name('edit.other');
    Route::get('company_letter/other_letter/destroy/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\OtherLetterController::class, 'destroy']);

    // Perjanjian Internal
    Route::get('employee_agreement/internal_agreement', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'index'])->name('pi.index');
    // Route::get('employee_agreement/internal_agreement/modal-form-detail', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'modal_detail'])->name('modal_detail');
    Route::get('employee_agreement/internal_agreement/get_data', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'get_data']);
    Route::get('employee_agreement/internal_agreement/get_employee', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'get_employee']);
    Route::get('employee_agreement/internal_agreement/change_employee', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'change_employee']);
    Route::post('employee_agreement/internal_agreement/create-save', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'save'])->name('save.pi');
    Route::get('employee_agreement/internal_agreement/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'get_edit'])->name('get_edit');
    Route::post('employee_agreement/internal_agreement/update-save', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'edit'])->name('edit.pi');
    Route::get('employee_agreement/internal_agreement/destroy/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\InternalAgreementController::class, 'destroy']);


    // TAG
    Route::get('employee_agreement/third_aggrement', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'index'])->name('tag.index');
    Route::get('employee_agreement/third_aggrement/modal-form-detail', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'modal_detail'])->name('modal_detail');
    Route::get('employee_agreement/third_aggrement/get_data', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'get_data']);
    Route::get('employee_agreement/third_aggrement/change_region', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'change_region']);
    Route::post('employee_agreement/third_aggrement/create-save', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'save'])->name('save.tag');
    Route::get('employee_agreement/third_aggrement/getEdit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'get_edit'])->name('get_edit');
    Route::post('employee_agreement/third_aggrement/update-save', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'edit'])->name('edit.tag');
    Route::get('employee_agreement/third_aggrement/destroy/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\ThirdAggrementController::class, 'destroy']);
	
	// SPB
	Route::get('company_letter/notif_letter', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'index'])->name('index.notif_letter');
	Route::get('company_letter/notif_letter/get_data', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'get_data']);
	Route::get('company_letter/notif_letter/change_employee', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'change_employee']);
	Route::post('company_letter/notif_letter/save', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'save'])->name('save.notif_letter');
	Route::get('company_letter/notif_letter/get_edit/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'get_edit']);
	Route::post('company_letter/notif_letter/update-save', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'update'])->name('update.notif_letter');
	Route::get('company_letter/notif_letter/destroy/{id_letter}', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'delete']);
	Route::get('company_letter/notif_letter/pkwt-number', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'pkwt_number']);
	
	//P2K
    Route::get('performance_plan/kpk_letter', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'index'])->name('kpk.index');
    Route::post('performance_plan/kpk_letter/save', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'save'])->name('kpk.save');
    Route::post('performance_plan/kpk_letter/update', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'update'])->name('kpk.update');
    Route::get('performance_plan/kpk_letter/modal_detail', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'modal_detail'])->name('kpk.modal_detail');
    Route::get('performance_plan/kpk_letter/get_dept', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_dept']);
    Route::get('performance_plan/kpk_letter/get_division', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_division']);
    Route::get('performance_plan/kpk_letter/get_employee_by', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_employee_by']);
    Route::get('performance_plan/kpk_letter/get_participant', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_participant']);
    Route::get('performance_plan/kpk_letter/get_pos_detail', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_pos_detail'])->name('kpk.get_pos_detail');
    Route::get('performance_plan/kpk_letter/get_edit', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_edit']);
    Route::get('performance_plan/kpk_letter/get_threshold', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_threshold'])->name('kpk.get_threshold');
    Route::post('performance_plan/kpk_letter/upload_review', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'upload_review']);
    Route::get('performance_plan/kpk_letter/download', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'download']);
	
	//P2K Monitoring
	Route::get('performance_plan/performance_monitoring', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'index_monitoring'])->name('kpk.index_monitoring');
	Route::get('performance_plan/performance_monitoring/export_validate', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'export_validate'])->name('kpk.validate');
	Route::get('performance_plan/performance_monitoring/export', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'export'])->name('kpk.export');
	Route::get('performance_plan/performance_monitoring/get_employee_search', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_employee_search'])->name('kpk.get_employee_search');
	Route::get('performance_plan/performance_monitoring/get_dept_search', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_dept_search'])->name('kpk.get_dept_search');
	
	//P2K Review
	Route::get('performance_plan/performance_review', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'index_review'])->name('kpk.index_review');
	Route::get('performance_plan/performance_review/modal_review', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'modal_review'])->name('kpk.modal_review');
	Route::get('performance_plan/performance_review/get_edit_review', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'get_edit_review']);
	Route::post('performance_plan/performance_review/update_review', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'update_review'])->name('kpk.update_review');
	Route::get('performance_plan/performance_review/download', [App\Http\Controllers\Kpi\Kpk\KpkLetterController::class, 'download_review']);
  });


Route::group(['middleware'=>'login-verification'],function()
{
  Route::get('internal_memo/print-im/{token}', [App\Http\Controllers\Eletter\MasterEletter\InternalMemoController::class, 'print_im'])->name('print.im');
  
  Route::get('company_letter/warning_letter/print-sp/{token}', [App\Http\Controllers\Eletter\MasterEletter\WarningLetterController::class, 'print'])->name('print.sp');

  Route::get('company_letter/invitation_letter/print-supa/{token}', [App\Http\Controllers\Eletter\MasterEletter\InvitationLetterController::class, 'print'])->name('print.supa');

  Route::get('decree/statement_letter/print-sk/{token}', [App\Http\Controllers\Eletter\MasterEletter\StatementLetterController::class, 'print'])->name('print.sk');

  Route::get('decree/internship_certificate/print-ski/{token}', [App\Http\Controllers\Eletter\MasterEletter\InternshipCertificateController::class, 'print'])->name('print.ski');
  
  Route::get('company_letter/notif_letter/print-spb/{token}', [App\Http\Controllers\Eletter\MasterEletter\NotifLetterController::class, 'print'])->name('print.spb');

});

Route::get('decree/employment_certificate/print-skk/{token}', [App\Http\Controllers\Eletter\MasterEletter\EmploymentCertificateController::class, 'print'])->name('print.skk');


Route::prefix('employee')->middleware('login-verification')->group(function () {
    /* Master Bank */
    Route::get('employee_setting/master_bank', [App\Http\Controllers\Employee\EmployeeSetting\MasterBankController::class, 'index']);
    Route::get('employee_setting/master_bank/get_data', [App\Http\Controllers\Employee\EmployeeSetting\MasterBankController::class, 'get_data']);
    Route::post('employee_setting/master_bank/save_master_bank', [App\Http\Controllers\Employee\EmployeeSetting\MasterBankController::class, 'save_master_bank']);
    Route::get('employee_setting/master_bank/get_detail_master_bank', [App\Http\Controllers\Employee\EmployeeSetting\MasterBankController::class, 'get_detail_master_bank']);
    Route::post('employee_setting/master_bank/destroy_master_bank', [App\Http\Controllers\Employee\EmployeeSetting\MasterBankController::class, 'destroy_master_bank']);

    /* Master Insurance */
    Route::get('employee_setting/master_insurance', [App\Http\Controllers\Employee\EmployeeSetting\MasterInsuranceController::class, 'index']);
    Route::get('employee_setting/master_insurance/get_data', [App\Http\Controllers\Employee\EmployeeSetting\MasterInsuranceController::class, 'get_data']);
    Route::post('employee_setting/master_insurance/save_master_insurance', [App\Http\Controllers\Employee\EmployeeSetting\MasterInsuranceController::class, 'save_master_insurance']);
    Route::get('employee_setting/master_insurance/get_detail_master_insurance', [App\Http\Controllers\Employee\EmployeeSetting\MasterInsuranceController::class, 'get_detail_master_insurance']);
    Route::post('employee_setting/master_insurance/destroy_master_insurance', [App\Http\Controllers\Employee\EmployeeSetting\MasterInsuranceController::class, 'destroy_master_insurance']);

    /* Master Checklist */
    Route::get('employee_setting/master_checklist', [App\Http\Controllers\Employee\EmployeeSetting\MasterChecklistController::class, 'index']);
    Route::get('employee_setting/master_checklist/get_data', [App\Http\Controllers\Employee\EmployeeSetting\MasterChecklistController::class, 'get_data']);
    Route::post('employee_setting/master_checklist/save', [App\Http\Controllers\Employee\EmployeeSetting\MasterChecklistController::class, 'save'])->name('checklist.save');
    Route::post('employee_setting/master_checklist/update', [App\Http\Controllers\Employee\EmployeeSetting\MasterChecklistController::class, 'update'])->name('checklist.update');
    Route::get('employee_setting/master_checklist/get_detail_master_checklist', [App\Http\Controllers\Employee\EmployeeSetting\MasterChecklistController::class, 'get_detail_master_checklist']);
    Route::post('employee_setting/master_checklist/destroy', [App\Http\Controllers\Employee\EmployeeSetting\MasterChecklistController::class, 'destroy']);

    /* Master Approval Hierarchy */
    Route::get('employee_setting/approval_hierarchy', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'index'])->name('approval_hierarchy.index');
    Route::post('employee_setting/approval_hierarchy/save', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'save'])->name('approval_hierarchy.save');
    Route::post('employee_setting/approval_hierarchy/update', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'update'])->name('approval_hierarchy.update');
    Route::get('employee_setting/approval_hierarchy/edit/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'edit']);
    Route::get('employee_setting/approval_hierarchy/destroy/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'destroy']);
    Route::get('employee_setting/approval_hierarchy/get_position_detail', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'get_position_detail']);
    Route::get('employee_setting/approval_hierarchy/get_company', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'get_company']);
    Route::get('employee_setting/approval_hierarchy/get_approval_edit', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'get_approval_edit']);
    Route::get('employee_setting/approval_hierarchy/get_approval_mode', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'get_approval_mode']);
    Route::get('employee_setting/approval_hierarchy/get_approval_doc', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'get_approval_doc']);
    Route::get('employee_setting/approval_hierarchy/get_grade', [App\Http\Controllers\Employee\EmployeeSetting\MasterApprovalController::class, 'get_grade']);

	/* Master Announcement */
	Route::get('employee_setting/announcement', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'index'])->name('announcement.index');
    Route::post('employee_setting/announcement/save', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'save'])->name('announcement.save');
    Route::post('employee_setting/announcement/update', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'update'])->name('announcement.update');
//    Route::get('employee_setting/announcement/edit/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'edit']);
    Route::get('employee_setting/announcement/submit_approve/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'submit_approve']);
    Route::get('employee_setting/announcement/cancel/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'cancel']);
    Route::get('employee_setting/announcement/destroy/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'destroy']);
    Route::get('employee_setting/announcement/get_employee', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_employee']);
    Route::get('employee_setting/announcement/get_company', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_company']);
    Route::get('employee_setting/announcement/get_announcement_type', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_announcement_type']);
    Route::get('employee_setting/announcement/get_hierachy', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_hierachy']);
    Route::get('employee_setting/announcement/get_approval', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_approval']);
    Route::get('employee_setting/announcement/get_approval_edit', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_approval_edit']);
    Route::get('employee_setting/announcement/get_approval_status', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_approval_status']);
    Route::get('employee_setting/announcement/get_announcement_edit', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_announcement_edit']);
    Route::get('employee_setting/announcement/get_approval_mode', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_approval_mode']);
    Route::get('employee_setting/announcement/get_approval_doc', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_approval_doc']);
    Route::get('employee_setting/announcement/get_status', [App\Http\Controllers\Employee\EmployeeSetting\MasterAnnouncementController::class, 'get_status']);

	/* Master Employee Survey */
	Route::get('employee_setting/master_employee_survey', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'index'])->name('employee_survey.index');
	Route::get('employee_setting/master_employee_survey/index_answer', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'index_answer'])->name('answer.index_answer');
    Route::post('employee_setting/master_employee_survey/save', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'save'])->name('employee_survey.save');
    Route::post('employee_setting/master_employee_survey/update', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'update'])->name('employee_survey.update');
    Route::post('employee_setting/master_employee_survey/save_survey', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'save_survey']);
    Route::post('employee_setting/master_employee_survey/save_answer', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'save_answer'])->name('answer.save_answer');
	Route::post('employee_setting/master_employee_survey/update_survey', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'update_survey']);
	Route::post('employee_setting/master_employee_survey/save_survey_answer', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'save_survey_answer']);
	Route::post('employee_setting/master_employee_survey/update_survey_answer', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'update_survey_answer']);

    Route::get('employee_setting/master_employee_survey/get_edit_answer', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_edit_answer']);
	Route::post('employee_setting/master_employee_survey/update_answer', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'update_answer'])->name('answer.update_answer');
    Route::get('employee_setting/master_employee_survey/destroy/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'destroy']);
    Route::get('employee_setting/master_employee_survey/destroy_question/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'destroy_question']);
    Route::get('employee_setting/master_employee_survey/destroy_answer/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'destroy_answer']);
    Route::get('employee_setting/master_employee_survey/get_employee', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_employee']);
    Route::get('employee_setting/master_employee_survey/get_company', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_company']);
    Route::get('employee_setting/master_employee_survey/get_question_type', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_question_type']);
    Route::get('employee_setting/master_employee_survey/get_category', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_category']);
    Route::get('employee_setting/master_employee_survey/get_survey_type', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_survey_type']);
    Route::get('employee_setting/master_employee_survey/get_answer', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_answer']);
    Route::get('employee_setting/master_employee_survey/get_survey_edit', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_survey_edit']);
    Route::get('employee_setting/master_employee_survey/checkid/{id}', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'checkid']);
    Route::get('employee_setting/master_employee_survey/get_department', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_department']);
    Route::get('employee_setting/master_employee_survey/get_region', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_region']);
    Route::get('employee_setting/master_employee_survey/get_branch ', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_branch']);
    Route::get('employee_setting/master_employee_survey/get_principal', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'get_principal'])->name('survey.get_principal');
    Route::post('employee_setting/master_employee_survey/duplicate_survey', [App\Http\Controllers\Employee\EmployeeSetting\MasterEmployeeSurveyController::class, 'duplicate_survey'])->name('survey.duplicate_survey');


    Route::get('employee_setting/company_campaign', [App\Http\Controllers\Employee\EmployeeSetting\MasterCampaignController::class, 'index']);
    Route::post('employee_setting/company_campaign/store', [App\Http\Controllers\Employee\EmployeeSetting\MasterCampaignController::class, 'store']);
    Route::post('employee_setting/company_campaign/edit', [App\Http\Controllers\Employee\EmployeeSetting\MasterCampaignController::class, 'edit']);
    Route::post('employee_setting/company_campaign/delete', [App\Http\Controllers\Employee\EmployeeSetting\MasterCampaignController::class, 'delete']);



	/* Contract */
	Route::get('employee/contract', [App\Http\Controllers\Employee\Contract\ContractController::class, 'index'])->name('contract.index');
    Route::post('employee/contract/save', [App\Http\Controllers\Employee\Contract\ContractController::class, 'save'])->name('contract.save');
    Route::post('employee/contract/update', [App\Http\Controllers\Employee\Contract\ContractController::class, 'update'])->name('contract.update');
    Route::get('employee/contract/destroy/{id}', [App\Http\Controllers\Employee\Contract\ContractController::class, 'destroy']);
    Route::get('employee/contract/edit/{id}', [App\Http\Controllers\Employee\Contract\ContractController::class, 'edit']);
    Route::get('employee/contract/get_employee', [App\Http\Controllers\Employee\Contract\ContractController::class, 'get_employee']);
    Route::get('employee/contract/get_employee_edit', [App\Http\Controllers\Employee\Contract\ContractController::class, 'get_employee_edit']);
    Route::get('employee/contract/get_company', [App\Http\Controllers\Employee\Contract\ContractController::class, 'get_company']);
    Route::get('employee/contract/get_contract_category', [App\Http\Controllers\Employee\Contract\ContractController::class, 'get_contract_category']);
    Route::get('employee/contract/get_shift_group', [App\Http\Controllers\Employee\Contract\ContractController::class, 'get_shift_group']);
    Route::get('employee/contract/get_position', [App\Http\Controllers\Employee\Contract\ContractController::class, 'get_position']);
	Route::get('employee/contract/browse', [App\Http\Controllers\Employee\Contract\ContractController::class, 'browse']);
    Route::get('employee/contract/checkid', [App\Http\Controllers\Employee\Contract\ContractController::class, 'checkid']);
    Route::get('employee/contract/attachment', [App\Http\Controllers\Employee\Contract\ContractController::class, 'attachment']);
    Route::get('employee/contract/update_attachment', [App\Http\Controllers\Employee\Contract\ContractController::class, 'update_attachment']);
    Route::get('employee/contract/get_employee_detail', [App\Http\Controllers\Employee\Contract\ContractController::class, 'get_employee_detail']);
	/*     * ** */

	/* Employee Request */
	Route::get('employee/employee_request', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'index'])->name('employee_request.index');
	Route::get('employee/employee_request/index_status', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'index_status'])->name('employee_request.index_status');
    Route::post('employee/employee_request/save', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'save'])->name('employee_request.save');
    Route::post('employee/employee_request/update', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'update'])->name('employee_request.update');
 /*   Route::post('employee/employee_request/submit_approve', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'submit_approve'])->name('employee_request.submit_approve');
 */
    Route::get('employee/employee_request/submit_approve/{id}', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'submit_approve']);
    Route::get('employee/employee_request/cancel/{id}', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'cancel']);
    Route::get('employee/employee_request/destroy/{id}', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'destroy']);
    Route::get('employee/employee_request/edit/{id}', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'edit']);
    Route::get('employee/employee_request/get_employee', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_employee']);
    Route::get('employee/employee_request/get_employee_by', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_employee_by']);
    Route::get('employee/employee_request/get_employee_delegate', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_employee_delegate']);
    Route::get('employee/employee_request/get_request_type', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_request_type']);
    Route::get('employee/employee_request/get_leave_type', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_leave_type']);
    Route::get('employee/employee_request/get_overtime_type', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_overtime_type']);
    Route::get('employee/employee_request/get_hierachy', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_hierachy']);
    Route::get('employee/employee_request/get_approval', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_approval']);
    Route::get('employee/employee_request/get_approval_edit', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_approval_edit']);
    Route::get('employee/employee_request/get_approval_status', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_approval_status']);
    Route::get('employee/employee_request/get_cancel_status', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_cancel_status']);
    Route::get('employee/employee_request/get_company', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_company']);
    Route::get('employee/employee_request/get_request_edit', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_request_edit']);
    Route::get('employee/employee_request/get_emp_leave', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_emp_leave']);
    Route::get('employee/employee_request/get_workdays', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_workdays']);
    Route::get('employee/employee_request/get_count_days', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_count_days']);
    Route::get('employee/employee_request/get_count_days_od', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_count_days_od']);
	Route::post('employee/employee_request/upload', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'upload'])->name('employee_request.upload');
	Route::get('employee/employee_request/get_actual_time', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'get_actual_time']);
	Route::get('employee/employee_request/modal_detail', [App\Http\Controllers\Employee\EmployeeRequest\EmployeeRequestController::class, 'modal_detail'])->name('request.modal_detail');
   
	/* Employee Request Management */
	Route::get('employee/employee_request_management', [App\Http\Controllers\Employee\EmployeeRequestManagement\EmployeeRequestManagementController::class, 'index']);
    Route::get('employee/employee_request_management/attachment', [App\Http\Controllers\Employee\EmployeeRequestManagement\EmployeeRequestManagementController::class, 'attachment']);
    Route::get('employee/employee_request_management/load_attachment', [App\Http\Controllers\Employee\EmployeeRequestManagement\EmployeeRequestManagementController::class, 'loadAttachment']);

	
	/* Employee Data */
	Route::get('employee/employee', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'index']);
	Route::get('employee/employee/report', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'report'])->name('employee.report');
	Route::get('employee/employee/reportptkp', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'reportptkp'])->name('employee.reportptkp');
	Route::get('employee/employee/get_ptkp_report', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_ptkp_report']);
	Route::get('employee/employee/reportdata', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'reportdata'])->name('employee.reportdata');
    Route::post('employee/employee/save', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'save'])->name('employee.save');
    Route::post('employee/employee/update', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'update'])->name('employee.update');
    Route::get('employee/employee/destroy/{id}', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'destroy']);
    Route::get('employee/employee/edit/{id}', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'edit']);
    Route::get('employee/employee/checkid/{id}', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'checkid']);
    Route::get('employee/employee/get_employee_edit', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_employee_edit']);
    Route::get('employee/employee/getcode', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'getcode']);
    Route::get('employee/employee/get_company', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_company']);
    Route::get('employee/employee/get_empstatus', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_empstatus']);
    Route::get('employee/employee/get_shift', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_shift']);
    Route::get('employee/employee/get_leave', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_leave']);
    Route::get('employee/employee/get_user', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_user']);
    Route::get('employee/employee/get_timezone', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_timezone']);
    Route::get('employee/employee/get_vaccine', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_vaccine']);
    Route::get('employee/employee/browse_job', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'browse_job'])->name('employee.browse_job');
    Route::get('employee/employee/get_country', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_country']);
    Route::get('employee/employee/get_religion', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_religion']);
    Route::get('employee/employee/get_education_level', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_education_level']);
	Route::get('employee/employee/get_bank', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_bank']);
    Route::get('employee/employee/get_currency', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_currency']);
    Route::get('employee/employee/get_insurance', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_insurance']);
    Route::get('employee/employee/get_emp_contract', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_emp_contract']);
    Route::get('employee/employee/get_emp_leave', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_emp_leave']);
    Route::get('employee/employee/get_emp_career', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_emp_career']);
    Route::get('employee/employee/get_emp_awdcp', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_emp_awdcp']);
    Route::get('employee/employee/get_emp_history', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_emp_history']);
    Route::get('employee/employee/get_leave_id', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_leave_id']);
    Route::get('employee/employee/generateleave', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'generateleave']);
    Route::get('employee/employee/getCandidate', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'getCandidate']);
    Route::get('employee/employee/getktpcode', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'getktpcode']);
    Route::get('employee/employee/getworkzone', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'getworkzone']);
    Route::get('employee/employee/ktpcheck', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'ktpcheck']);
    Route::get('employee/employee/get_checklist_onboarding', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_checklist_onboarding']);
    Route::get('employee/employee/get_checklist_offboarding', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_checklist_offboarding']);
	Route::post('employee/employee/upload', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'upload'])->name('employee.upload');
    Route::post('employee/employee/update_ptkp', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'update_ptkp'])->name('ptkp.update');
	Route::get('employee/employee/get_ptkp', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_ptkp']);
	Route::get('employee/employee/get_ptkp_edit', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_ptkp_edit']);
	Route::get('employee/employee/get_ex_concurent', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'get_ex_concurent']);
    Route::get('employee/employee/getLeaveCustom', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'getLeaveCustom']);
    Route::post('employee/employee/saveLeaveCustom', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'saveLeaveCustom']);

	/* Announcement Publish*/
	Route::get('employee/announcement', [App\Http\Controllers\Employee\Announcement\AnnouncementController::class, 'index'])->name('announ.index');
    Route::get('employee/announcement/get_announcement_edit', [App\Http\Controllers\Employee\Announcement\AnnouncementController::class, 'get_announcement_edit']);
    Route::get('employee/announcement/get_announcement_new', [App\Http\Controllers\Employee\Announcement\AnnouncementController::class, 'get_announcement_new']);
    Route::get('employee/announcement/get_announcement_show', [App\Http\Controllers\Employee\Announcement\AnnouncementController::class, 'get_announcement_show']);
	Route::post('employee/announcement/save_read', [App\Http\Controllers\Employee\Announcement\AnnouncementController::class, 'save_read'])->name('announ.save_read');
	Route::get('employee/announcement/close_read', [App\Http\Controllers\Employee\Announcement\AnnouncementController::class, 'close_read'])->name('announ.close_read');

	/* Employee Approval*/
	Route::get('employee/employee_approval', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'index'])->name('emp_approval.index');
	Route::get('employee/employee_approval/detail_career/{id}', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'detail_career']);
	Route::get('employee/employee_approval/approve/{id}', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'approve']);
	Route::get('employee/employee_approval/revised/{text}/{id}', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'revised']);
	Route::get('employee/employee_approval/rejected/{text}/{id}', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'rejected']);
	Route::get('employee/employee_approval/get_view_approval', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'get_view_approval']);
	Route::post('employee/employee_approval/general_approve', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'general_approve']);
	Route::post('employee/employee_approval/submit_approve', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'submit_approve']);
	Route::post('employee/employee_approval/partial_approve', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'partial_approve']);
	Route::post('employee/employee_approval/all_approve', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'all_approve']);
	Route::post('employee/employee_approval/revised_approve', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'revised_approve']);
	Route::post('employee/employee_approval/rejected_approve', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'rejected_approve']);
	Route::get('employee/employee_approval/index_status', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'index_status'])->name('emp_approval.index_status');
    Route::post('employee/employee_approval/approve_all', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'approve_all']);
	Route::get('employee/employee_approval/list_req_type', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'list_req_type']);
	Route::get('employee/employee_approval/detail_travel/{id}', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'detail_travel']);
	Route::get('employee/employee_approval/detail_kpi/{id}', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'detail_kpi']);
	
	/* Employee Approval History */
	Route::get('employee/approval_history', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'index_history'])->name('emp_approval_history.index_history');
	Route::get('employee/approval_history/get_view_approval_history', [App\Http\Controllers\Employee\EmployeeApproval\EmployeeApprovalController::class, 'get_view_approval_history']);
	
	/* Employee Survey */
	Route::get('employee/user_survey', [App\Http\Controllers\Employee\EmployeeSetting\UserSurveyController::class, 'index'])->name('user_survey.index');
	Route::get('employee/answer_employee_survey', [App\Http\Controllers\Employee\EmployeeSurvey\EmployeeSurveyController::class, 'index'])->name('employee_survey_answer.index');
    Route::get('employee/employee_survey/{id}', [App\Http\Controllers\Employee\EmployeeSurvey\EmployeeSurveyController::class, 'join'])->name('employee_survey.join');
    Route::post('employee/employee_survey/save', [App\Http\Controllers\Employee\EmployeeSurvey\EmployeeSurveyController::class, 'save'])->name('employee_survey_answer.save');

	/* SUMMARY Employee Survey  */ 
    Route::get('employee/employee_survey_management', [App\Http\Controllers\Employee\EmployeeSurvey\SummaryAnswerController::class, 'index'])->name('employee_survey_management.index');
    Route::post('employee/employee_survey_management/get_survey', [App\Http\Controllers\Employee\EmployeeSurvey\SummaryAnswerController::class, 'get_survey'])->name('employee_survey_management.get_survey');
    Route::post('employee/employee_survey_management', [App\Http\Controllers\Employee\EmployeeSurvey\SummaryAnswerController::class, 'get_result'])->name('employee_survey_management.get_result');
    Route::get('summary_survey', [App\Http\Controllers\Employee\EmployeeSurvey\SummaryAnswerController::class, 'summary_survey_single'])->name('employee_survey_management.summary_survey');
    
	
    /** Master Workdays */// ZAHRAN
    Route::get('employee_setting/workdays', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'index']);
    Route::post('employee_setting/workdays', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'index'])->name('workdays_getdata');
    // Route::post('employee_setting/workdays/getemployeename', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'getemployeename'])->name('workdays_getemployeename');
    Route::post('employee_setting/workdays/edit', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'edit'])->name('workdays_edit');
	/*     * ** */

    Route::post('get_branch', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'getBranch'])->name('workdays_getBranch');
    Route::post('get_location', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'getLocation'])->name('workdays_getLocation');
    Route::get('get_employee_by_status_and_access_group', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'get_employee_by_status_and_access_group']);
    Route::get('get_employee_with_subordinate', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'subordinate']);

    /* Data Verification */ 
    Route::get('employee/data_updates_verification', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'data_verification']);
    Route::post('employee/detail_verification', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'detail_verification']);
    Route::post('employee/detail_verification/save', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'save_detail_verification']);

     /*Employee All */
    Route::get('employee/employee_all', [App\Http\Controllers\Employee\Employee\EmployeeController::class, 'employee_all']);
    Route::get('employee/download_attendance', [App\Http\Controllers\Employee\Employee\EmployeeController::class, '_export_attendance']);
    Route::get('employee/download_headcount', [App\Http\Controllers\Employee\Employee\EmployeeController::class, '_export_employee']);
    Route::get('employee/download_lapkar', [App\Http\Controllers\Employee\Employee\EmployeeController::class, '_export_lapkar']);
    Route::get('employee/download_attendance_summary', [App\Http\Controllers\Employee\Employee\EmployeeController::class, '_export_attendance_summary']);
    Route::get('employee/download_dashboard_attendance', [App\Http\Controllers\Employee\Employee\EmployeeController::class, '_export_dashboard_attendance']);
    Route::get('employee/chart_dashboard_attendance', [App\Http\Controllers\Employee\Employee\EmployeeController::class, '_chart_dashboard_attendance']);
	
	 /*Employee Reco Form */
    Route::get('employee/recommendation_form', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'index'])->name('reco_form.index');
    Route::get('employee/recommendation_form/modal_detail', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'modal_detail'])->name('reco_form.modal_detail');
	Route::post('employee/recommendation_form/save', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'save'])->name('reco_form.save');
    Route::post('employee/recommendation_form/update', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'update'])->name('reco_form.update');
    Route::get('employee/recommendation_form/get_employee', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_employee']);
    Route::get('employee/recommendation_form/get_detail_employee', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_detail_employee']);
    Route::get('employee/recommendation_form/get_edit', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_edit']);
    Route::get('employee/recommendation_form/get_hierachy', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_hierachy']);
    Route::get('employee/recommendation_form/get_approval_status', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_approval_status']);
    Route::get('employee/recommendation_form/get_category', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_category']);
    Route::get('employee/recommendation_form/get_type', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_type']);
    Route::get('employee/recommendation_form/get_new_position', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_new_position']);
    Route::get('employee/recommendation_form/checkpos', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'checkpos']);
    Route::get('employee/recommendation_form/get_new_status', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_new_status']);
    Route::get('employee/recommendation_form/get_appraiser', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_appraiser']);
    Route::get('employee/recommendation_form/get_new_mgr', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_new_mgr']);
    Route::get('employee/recommendation_form/get_decision', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_decision']);
    Route::get('employee/recommendation_form/get_sumQualitative', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_sumQualitative']);
    Route::get('employee/recommendation_form/get_kpi_detail', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'get_kpi_detail']);
    Route::get('employee/recommendation_form/cancel/{id}', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'cancel']);
    Route::get('employee/recommendation_form/download', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'download']);
	
	Route::get('employee/reco_summary', [App\Http\Controllers\Employee\EmployeeReco\EmployeeRecoController::class, 'index_summary'])->name('reco_form.index_summary');
	
	Route::get('employee/reco_qualitative', [App\Http\Controllers\Employee\EmployeeReco\QualitativeRecoController::class, 'index'])->name('quali_form.index');
	Route::get('employee/reco_qualitative/get_appraiser_edit', [App\Http\Controllers\Employee\EmployeeReco\QualitativeRecoController::class, 'get_appraiser_edit']);
	Route::post('employee/reco_qualitative/update', [App\Http\Controllers\Employee\EmployeeReco\QualitativeRecoController::class, 'update'])->name('quali_form.update');
});

Route::prefix('organization')->middleware('login-verification')->group(function () {
    /* Job Position */
    Route::get('organization_structure/job_position', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'index']);
    Route::get('organization_structure/job_position/get_data', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'get_data']);
    Route::get('organization_structure/job_position/get_company', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'get_company']);
    Route::get('organization_structure/job_position/get_department', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'get_department']);
    Route::get('organization_structure/job_position/get_superior_position', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'get_superior_position']);
    Route::get('organization_structure/job_position/get_detail_position', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'get_detail_position']);
    Route::post('organization_structure/job_position/save_position', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'save_position']);
    Route::post('organization_structure/job_position/destroy_position', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'destroy_position']);
    Route::post('organization_structure/job_position/import', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'import']);
    Route::post('organization_structure/job_position/mass_destroy', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'mass_destroy']);
    Route::post('organization_structure/job_position/mass_inactive', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionController::class, 'mass_inactive']);

    /*     * ** */

    /* Job position Route */
    Route::get('organization_structure/job_position_route', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'index']);
    Route::get('organization_structure/job_position_route/get_data', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_data']);
    Route::get('organization_structure/job_position_route/get_job_position', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_job_position']);
    Route::get('organization_structure/job_position_route/get_parent_job', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_parent_job']);
    Route::get('organization_structure/job_position_route/get_grade', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_grade']);
    Route::get('organization_structure/job_position_route/get_job_status', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_job_status']);
    Route::get('organization_structure/job_position_route/get_company', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_company']);
    Route::get('organization_structure/job_position_route/get_assign_company', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_assign_company']);
    Route::get('organization_structure/job_position_route/get_department', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_department']);
    Route::get('organization_structure/job_position_route/get_default_cost_sharing', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_default_cost_sharing']);

    Route::get('organization_structure/job_position_route/get_location', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_location']);
    Route::get('organization_structure/job_position_route/get_branch_operating_unit', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_branch_operating_unit']);
    Route::get('organization_structure/job_position_route/get_principal', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_principal']);
    Route::get('organization_structure/job_position_route/get_work_arround', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_work_arround']);
    Route::get('organization_structure/job_position_route/get_cost_sharing', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_cost_sharing']);
    Route::get('organization_structure/job_position_route/get_employee', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_employee']);
    Route::get('organization_structure/job_position_route/get_superior_position', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_superior_position']);

    Route::get('organization_structure/job_position_route/get_detail_position_route', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'get_detail_position_route']);
    Route::post('organization_structure/job_position_route/save_position_route', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'save_position_route']);
    Route::post('organization_structure/job_position_route/save_update_position_route', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'save_update_position_route']);
    Route::post('organization_structure/job_position_route/destroy_position_route', [App\Http\Controllers\Organization\OrganizationStructure\JobPositionRouteController::class, 'destroy_position_route']);
    /*     * ** */

    /* Regional Route */
    Route::get('master_organization/master_regional', [App\Http\Controllers\Organization\MasterOrganization\MasterRegionalController::class, 'index'])->name('regional.index');
    Route::post('master_organization/master_regional/save', [App\Http\Controllers\Organization\MasterOrganization\MasterRegionalController::class, 'save'])->name('regional.save');
    Route::post('master_organization/master_regional/update', [App\Http\Controllers\Organization\MasterOrganization\MasterRegionalController::class, 'update'])->name('regional.update');
    Route::get('master_organization/master_regional/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterRegionalController::class, 'destroy']);
    Route::get('master_organization/master_regional/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterRegionalController::class, 'edit']);
    Route::get('master_organization/master_regional/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterRegionalController::class, 'get_company']);
    /*     * ** */

    /* Branch Route */
    Route::get('master_organization/master_branch', [App\Http\Controllers\Organization\MasterOrganization\MasterBranchController::class, 'index'])->name('branch.index');
    Route::post('master_organization/master_branch/save', [App\Http\Controllers\Organization\MasterOrganization\MasterBranchController::class, 'save'])->name('branch.save');
    Route::post('master_organization/master_branch/update', [App\Http\Controllers\Organization\MasterOrganization\MasterBranchController::class, 'update'])->name('branch.update');
    Route::get('master_organization/master_branch/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterBranchController::class, 'destroy']);
    Route::get('master_organization/master_branch/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterBranchController::class, 'edit']);
    Route::get('master_organization/master_branch/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterBranchController::class, 'get_company']);
    Route::get('master_organization/master_branch/get_regional', [App\Http\Controllers\Organization\MasterOrganization\MasterBranchController::class, 'get_regional']);
    /*     * ** */

    /* Location Route */
    Route::get('master_organization/job_location', [App\Http\Controllers\Organization\MasterOrganization\MasterLocationController::class, 'index'])->name('location.index');
    Route::post('master_organization/job_location/save', [App\Http\Controllers\Organization\MasterOrganization\MasterLocationController::class, 'save'])->name('location.save');
    Route::post('master_organization/job_location/update', [App\Http\Controllers\Organization\MasterOrganization\MasterLocationController::class, 'update'])->name('location.update');
    Route::get('master_organization/job_location/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterLocationController::class, 'destroy']);
    Route::get('master_organization/job_location/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterLocationController::class, 'edit']);
    Route::get('master_organization/job_location/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterLocationController::class, 'get_company']);
    Route::get('master_organization/job_location/get_branch', [App\Http\Controllers\Organization\MasterOrganization\MasterLocationController::class, 'get_branch'])->name('location.get_branch');
    /*     * ** */

    /* Job Grade Route */
    Route::get('master_organization/job_grade', [App\Http\Controllers\Organization\MasterOrganization\MasterGradeController::class, 'index'])->name('grade.index');
    Route::post('master_organization/job_grade/save', [App\Http\Controllers\Organization\MasterOrganization\MasterGradeController::class, 'save'])->name('grade.save');
    Route::post('master_organization/job_grade/update', [App\Http\Controllers\Organization\MasterOrganization\MasterGradeController::class, 'update'])->name('grade.update');
    Route::get('master_organization/job_grade/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterGradeController::class, 'destroy']);
    Route::get('master_organization/job_grade/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterGradeController::class, 'edit']);
    Route::get('master_organization/job_grade/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterGradeController::class, 'get_company']);
    /*     * ** */

    /* Job Status Route */
    Route::get('master_organization/job_status', [App\Http\Controllers\Organization\MasterOrganization\MasterStatusController::class, 'index'])->name('status.index');
    Route::post('master_organization/job_status/save', [App\Http\Controllers\Organization\MasterOrganization\MasterStatusController::class, 'save'])->name('status.save');
    Route::post('master_organization/job_status/update', [App\Http\Controllers\Organization\MasterOrganization\MasterStatusController::class, 'update'])->name('status.update');
    Route::get('master_organization/job_status/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterStatusController::class, 'destroy']);
    Route::get('master_organization/job_status/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterStatusController::class, 'edit']);
    Route::get('master_organization/job_status/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterStatusController::class, 'get_company']);
    /*     * ** */

    /* OU Route */
    Route::get('organization_structure/organization_unit', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationUnitController::class, 'index'])->name('ou.index');
    Route::post('organization_structure/organization_unit/save', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationUnitController::class, 'save'])->name('ou.save');
    Route::post('organization_structure/organization_unit/update', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationUnitController::class, 'update'])->name('ou.update');
    Route::get('organization_structure/organization_unit/destroy/{id}', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationUnitController::class, 'destroy']);
    Route::get('organization_structure/organization_unit/edit/{id}', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationUnitController::class, 'edit']);
    Route::get('organization_structure/organization_unit/get_company', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationUnitController::class, 'get_company']);
    /*     * ** */

    /* Principal Route */
    Route::get('master_organization/master_principal', [App\Http\Controllers\Organization\MasterOrganization\MasterPrincipalController::class, 'index'])->name('principal.index');
    Route::post('master_organization/master_principal/save', [App\Http\Controllers\Organization\MasterOrganization\MasterPrincipalController::class, 'save'])->name('principal.save');
    Route::post('master_organization/master_principal/update', [App\Http\Controllers\Organization\MasterOrganization\MasterPrincipalController::class, 'update'])->name('principal.update');
    Route::get('master_organization/master_principal/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterPrincipalController::class, 'destroy']);
    Route::get('master_organization/master_principal/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterPrincipalController::class, 'edit']);
    Route::get('master_organization/master_principal/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterPrincipalController::class, 'get_company']);
    Route::get('master_organization/master_principal/get_division', [App\Http\Controllers\Organization\MasterOrganization\MasterPrincipalController::class, 'get_division']);
    /*     * ** */

    /* Hierarchy Routes */
    Route::get('organization_structure/organization_hierarchy', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationHierarchyController::class, 'index'])->name('hierarchy.index');
	Route::get('organization_structure/organization_hierarchy/get_position_filter', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationHierarchyController::class, 'get_position_filter']);
	Route::post('organization_structure/organization_hierarchy/queryChart', [App\Http\Controllers\Organization\OrganizationStructure\OrganizationHierarchyController::class, 'queryChart'])->name('hierarchy.queryChart');
    /*     * ** */

	/* Division Route */
    Route::get('master_organization/master_division', [App\Http\Controllers\Organization\MasterOrganization\MasterDivisionController::class, 'index'])->name('division.index');
    Route::post('master_organization/master_division/save', [App\Http\Controllers\Organization\MasterOrganization\MasterDivisionController::class, 'save'])->name('division.save');
    Route::post('master_organization/master_division/update', [App\Http\Controllers\Organization\MasterOrganization\MasterDivisionController::class, 'update'])->name('division.update');
    Route::get('master_organization/master_division/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterDivisionController::class, 'destroy']);
    Route::get('master_organization/master_division/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterDivisionController::class, 'edit']);
    Route::get('master_organization/master_division/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterDivisionController::class, 'get_company']);
    /*     * ** */

	/* Master Cost Sharing Route */
    Route::get('master_organization/master_cost_sharing', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'index'])->name('cost_sharing.index');
    Route::post('master_organization/master_cost_sharing/save', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'save'])->name('cost_sharing.save');
    Route::post('master_organization/master_cost_sharing/update', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'update'])->name('cost_sharing.update');
    Route::get('master_organization/master_cost_sharing/destroy/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'destroy']);
    Route::get('master_organization/master_cost_sharing/edit/{id}', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'edit']);
    Route::get('master_organization/master_cost_sharing/get_company', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'get_company']);
    Route::get('master_organization/master_cost_sharing/get_branch', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'get_branch']);
    Route::get('master_organization/master_cost_sharing/get_dept', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'get_dept']);
    Route::get('master_organization/master_cost_sharing/get_principal', [App\Http\Controllers\Organization\MasterOrganization\MasterCostSharingController::class, 'get_principal']);
    /*     * ** */
});

Route::prefix('time_attendance')->middleware('login-verification')->group(function () { 
    /* Master Leave Group */
    Route::get('leave_setting/leave_group', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'index']);
    Route::get('leave_setting/leave_group/get_data', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'get_data']);
    Route::post('leave_setting/leave_group/save', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'save'])->name('leave_group.save');
    Route::post('leave_setting/leave_group/update', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'update'])->name('leave_group.update');
    Route::get('leave_setting/leave_group/edit/{id}', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'edit']);
    Route::get('leave_setting/leave_group/destroy/{id}', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'destroy']);
    Route::get('leave_setting/leave_group/get_leave_type', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'get_leave_type']);
    Route::get('leave_setting/leave_group/get_company', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'get_company']);
    Route::get('leave_setting/leave_group/get_leave_edit', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroupController::class, 'get_leave_edit']);

    /* Master Leave Type */
    Route::get('leave_setting/leave_type', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'index']);
    Route::get('leave_setting/leave_type/get_data', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'get_data']);
    Route::post('leave_setting/leave_type/save', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'save'])->name('leave_type.save');
    Route::get('leave_setting/leave_type/browse', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'browse'])->name('leave_type.browse');
    Route::get('leave_setting/leave_type/checkid/{id}', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'checkid']);
    Route::post('leave_setting/leave_type/update', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'update'])->name('leave_type.update');
    Route::get('leave_setting/leave_type/destroy/{id}', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'destroy']);
    Route::get('leave_setting/leave_type/edit/{id}', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'edit']);
    Route::get('leave_setting/leave_type/get_company', [App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveTypeController::class, 'get_company']);


    /* Master Overtime Type */
    Route::get('overtime/overtime_type', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'index']);
    Route::get('overtime/overtime_type/get_data', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'get_data']);
    Route::post('overtime/overtime_type/save', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'save'])->name('overtime_type.save');
    Route::get('overtime/overtime_type/browse', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'browse'])->name('overtime_type.browse');
    Route::get('overtime/overtime_type/checkid/{id}', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'checkid']);
    Route::post('overtime/overtime_type/update', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'update'])->name('overtime_type.update');
    Route::get('overtime/overtime_type/destroy/{id}', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'destroy']);
    Route::get('overtime/overtime_type/edit/{id}', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'edit']);
    Route::get('overtime/overtime_type/get_company', [App\Http\Controllers\TimeAttendance\Overtime\OvertimeType\MasterOvertimeTypeController::class, 'get_company']);

    /* Attendance */
    Route::get('attendance/attendance', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'index']);
    Route::post('attendance/update', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'update']);
    Route::post('attendance/sinkron_absen_localstorage', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'update']);
    Route::get('attendance/attendance_upload', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'attendance_upload']);
    Route::post('attendance_upload_file', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'attendance_upload_file']);


    /** Attendance List */// ZAHRAN
    Route::get('attendance/attendance_list', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'index']);
    Route::post('attendance/attendance_list', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'index'])->name('workdays_getdata');
    Route::post('attendance/attendance_list/getemployeename', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'getemployeename'])->name('workdays_getemployeename_attendance_list');
    Route::post('attendance/attendance_list/edit', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'edit'])->name('workdays_edit');

    /** Master Generate Attendance */// ZAHRAN
    Route::get('attendance/generate_attendance', [App\Http\Controllers\TimeAttendance\Attendance\GenerateAttendanceController::class, 'index']);
    Route::post('attendance/generate_attendance/getemployeename', [App\Http\Controllers\TimeAttendance\Attendance\GenerateAttendanceController::class, 'getemployeename'])->name('getemployeename');
    Route::post('attendance/generate_attendance/generateattendance', [App\Http\Controllers\TimeAttendance\Attendance\GenerateAttendanceController::class, 'generateattendance'])->name('generateattendance');

    /** Master Holiday */// migrasi GABY => ZAHRAN
    Route::get('leave_setting/master_holiday', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterHoliday\HolidayController::class, 'index']);
    Route::post('leave_setting/master_holiday/getdata', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterHoliday\HolidayController::class, 'getdata']);
    Route::post('leave_setting/master_holiday/getHolidayById', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterHoliday\HolidayController::class, 'getHolidayById']);
    Route::post('leave_setting/master_holiday/holiday', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterHoliday\HolidayController::class, 'store'])->name('holiday.store');
    Route::post('leave_setting/master_holiday/holiday-update/{id}', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterHoliday\HolidayController::class, 'update'])->name('holiday.update');
    Route::delete('leave_setting/master_holiday/holiday/{id}', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterHoliday\HolidayController::class, 'destroy'])->name('holiday.delete');

    /** Master Daily Shift */// ZAHRAN
    Route::get('leave_setting/master_shift_daily', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDailyController::class, 'index']);
    Route::post('leave_setting/master_shift_daily/getdata', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDailyController::class, 'getdata'])->name('daily_shift_getdata');
    Route::post('leave_setting/master_shift_daily/create', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDailyController::class, 'create'])->name('daily_shift_create');
    Route::post('leave_setting/master_shift_daily/edit', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDailyController::class, 'edit'])->name('daily_shift_edit');
    Route::post('leave_setting/master_shift_daily/editsave', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDailyController::class, 'editsave'])->name('daily_shift_editsave');
    Route::post('leave_setting/master_shift_daily/delete', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDailyController::class, 'delete'])->name('daily_shift_delete');

    /** Master Shift Group */// ZAHRAN
    Route::get('leave_setting/master_shift_group', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftGroup\ShiftGroupController::class, 'index']);
    Route::post('leave_setting/master_shift_group/getdata', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftGroup\ShiftGroupController::class, 'getdata'])->name('shift_group_getdata');
    Route::post('leave_setting/master_shift_group/create', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftGroup\ShiftGroupController::class, 'create'])->name('shift_group_create');
    Route::post('leave_setting/master_shift_group/edit', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftGroup\ShiftGroupController::class, 'edit'])->name('shift_group_edit');
    Route::post('leave_setting/master_shift_group/editsave', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftGroup\ShiftGroupController::class, 'editsave'])->name('shift_group_editsave');
    Route::post('leave_setting/master_shift_group/delete', [App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftGroup\ShiftGroupController::class, 'delete'])->name('shift_group_delete');

    /** Master Mass Leave Balance */// ZAHRAN
    Route::get('leave_setting/mass_leave', [App\Http\Controllers\TimeAttendance\LeaveSetting\MassLeave\MassLeaveController::class, 'index']);
    Route::post('leave_setting/mass_leave/getemployeename', [App\Http\Controllers\TimeAttendance\LeaveSetting\MassLeave\MassLeaveController::class, 'getemployeename'])->name('mass_leave_getemployeename');
    Route::post('leave_setting/mass_leave/generatemassleave', [App\Http\Controllers\TimeAttendance\LeaveSetting\MassLeave\MassLeaveController::class, 'generatemassleave'])->name('mass_leave_generatemassleave');


    // MASS LEAVE REQUEST
    Route::get('leaves/mass_leave_request', [App\Http\Controllers\TimeAttendance\Leaves\MassLeaveRequestController::class, 'index']);
    Route::post('leaves/mass_leave_request/submit', [App\Http\Controllers\TimeAttendance\Leaves\MassLeaveRequestController::class, 'submit']);
    Route::post('leaves/mass_leave_request/execute', [App\Http\Controllers\TimeAttendance\Leaves\MassLeaveRequestController::class, 'executeMassLeaveRequest'])->name('massleaverequest.execute');


    // GET PLACE TIMEZONE MAPBOX, GET SERVER TIME
    Route::post('getPlaceTimezoneMapbox', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'getPlaceTimezoneMapbox']);
    Route::get('getServerTime', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'getServerTime']);
    Route::post('generateWorkdaysByEmployee', [App\Http\Controllers\TimeAttendance\Attendance\AttendanceController::class, 'generateWorkdaysByEmployee']);
    Route::get('_export', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, '_export_2']);
    Route::get('patchWorkdays', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'patchWorkdays']);
    Route::get('employee_lock_attendance', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'employee_lock_attendance']);
    Route::post('lockEmployeeAttendance', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'lockEmployeeAttendance']);
    Route::get('attendance_photo', [App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController::class, 'attendance_photo']);
});



Route::prefix('general_setting')->middleware('login-verification')->group(function () {
    /* Company Route */
    Route::get('company_setting/company', [App\Http\Controllers\GeneralSetting\CompanySetting\CompanyController::class, 'index'])->name('company.index');
    Route::post('company_setting/company/save', [App\Http\Controllers\GeneralSetting\CompanySetting\CompanyController::class, 'save'])->name('company.save');
    Route::get('company_setting/company/browse', [App\Http\Controllers\GeneralSetting\CompanySetting\CompanyController::class, 'browse'])->name('company.browse');
    Route::get('company_setting/company/checkid/{id}', [App\Http\Controllers\GeneralSetting\CompanySetting\CompanyController::class, 'checkid']);
    Route::post('company_setting/company/update', [App\Http\Controllers\GeneralSetting\CompanySetting\CompanyController::class, 'update'])->name('company.update');
    Route::get('company_setting/company/destroy/{id}', [App\Http\Controllers\GeneralSetting\CompanySetting\CompanyController::class, 'destroy']);
    Route::get('company_setting/company/edit/{id}', [App\Http\Controllers\GeneralSetting\CompanySetting\CompanyController::class, 'edit']);

    /* Master General Data */
    Route::get('company_setting/master_general_data', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterGeneralDataController::class, 'index']);
    Route::get('company_setting/master_general_data/get_data', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterGeneralDataController::class, 'get_data']);
    Route::post('company_setting/master_general_data/save_master_general_data', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterGeneralDataController::class, 'save_master_general_data']);
    Route::get('company_setting/master_general_data/get_detail_master_general_data', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterGeneralDataController::class, 'get_detail_master_general_data']);
    Route::get('company_setting/master_general_data/generate_data_to_all_companies', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterGeneralDataController::class, 'generate_data_to_all_companies'])->name('mgd.generate');


    /* Master API key */
    Route::get('company_setting/master_api_key', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterApiController::class, 'index']);
    Route::get('company_setting/master_api_key/get_data', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterApiController::class, 'get_data']);
    Route::post('company_setting/master_api_key/save_master_api_key', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterApiController::class, 'save_master_api_key']);
    Route::get('company_setting/master_api_key/get_detail_master_api_key', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterApiController::class, 'get_detail_master_api_key']);
	
	 /* Master Period */
	Route::get('company_setting/master_period', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterPeriodController::class, 'index'])->name('master_period.index');
	Route::post('company_setting/master_period/save', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterPeriodController::class, 'save'])->name('master_period.save');
	Route::post('company_setting/master_period/update', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterPeriodController::class, 'update'])->name('master_period.update');
    Route::get('company_setting/master_period/destroy/{id}', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterPeriodController::class, 'destroy']);
    Route::get('company_setting/master_period/edit/{id}', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterPeriodController::class, 'edit']);
    Route::get('company_setting/master_period/get_period_type', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterPeriodController::class, 'get_period_type']);
    Route::get('company_setting/master_period/get_period_edit', [App\Http\Controllers\GeneralSetting\CompanySetting\MasterPeriodController::class, 'get_period_edit']);
	
	// Hr COnfig Setting
	Route::get('company_setting/hr_config_settings', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'index'])->name('index.hr_config');
	Route::get('company_setting/hr_config_settings/get_data', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'get_data']);
	Route::post('company_setting/hr_config_settings/create-save', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'save'])->name('save.hrconfig');
	Route::get('company_setting/hr_config_settings/getEdit/{id_hr_config}', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'get_edit']);
	Route::post('company_setting/hr_config_settings/update-save', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'edit'])->name('edit.hrconfig');
	Route::get('company_setting/hr_config_settings/destroy/{id_hr_config}', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'destroy']);
    Route::get('company_setting/hr_config_settings/getSurveyPeriod', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'getSurveyPeriod']);
    Route::get('company_setting/hr_config_settings/getSurveyQuestion', [App\Http\Controllers\GeneralSetting\CompanySetting\HrConfigSettingsController::class, 'getSurveyQuestion']);


});


Route::prefix('setting')->middleware('login-verification')->group(function () {
    /* Responsibility Menu */
    Route::get('responsibility/responsibility_menu', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'index'])->name('responsibility.index');
    Route::post('responsibility/responsibility_menu/save', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'save'])->name('responsibility.save');
    Route::get('responsibility/responsibility_menu/browse', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'browse'])->name('responsibility.browse');
    Route::get('responsibility/responsibility_menu/checkid/{id}', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'checkid']);
    Route::post('responsibility/responsibility_menu/update', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'update'])->name('responsibility.update');
    Route::get('responsibility/responsibility_menu/destroy/{id}', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'destroy']);
    Route::get('responsibility/responsibility_menu/edit/{id}', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'edit']);
    Route::get('responsibility/responsibility_menu/get_address_menu', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'get_address_menu']);
    Route::get('responsibility/responsibility_menu/get_company', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'get_company']);
    Route::get('responsibility/responsibility_menu/get_responsibility', [App\Http\Controllers\Setting\Responsibility\ResponsibilityController::class, 'get_responsibility']);
    /*     * ** */

    /* Responsibility Group Route */
    Route::get('responsibility/responsibility_group', [App\Http\Controllers\Setting\Responsibility\ResponsibilityMenuController::class, 'index'])->name('responsibility_group.index');
    Route::post('responsibility/responsibility_group/save', [App\Http\Controllers\Setting\Responsibility\ResponsibilityMenuController::class, 'save'])->name('responsibility_group.save');
    Route::post('responsibility/responsibility_group/update', [App\Http\Controllers\Setting\Responsibility\ResponsibilityMenuController::class, 'update'])->name('responsibility_group.update');
    Route::get('responsibility/responsibility_group/destroy/{id}', [App\Http\Controllers\Setting\Responsibility\ResponsibilityMenuController::class, 'destroy']);
    Route::get('responsibility/responsibility_group/edit/{id}', [App\Http\Controllers\Setting\Responsibility\ResponsibilityMenuController::class, 'edit']);
    Route::get('responsibility/responsibility_group/get_company', [App\Http\Controllers\Setting\Responsibility\ResponsibilityMenuController::class, 'get_company']);
    /*     * ** */

    /* Access Right User */
    Route::get('responsibility_menu/access_right_user', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'index'])->name('responsibility_user.index');
    Route::post('responsibility_menu/access_right_user/save', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'save'])->name('responsibility_user.save');
    Route::post('responsibility_menu/access_right_user/update', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'update'])->name('responsibility_user.update');
    Route::get('responsibility_menu/access_right_user/destroy/{id}', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'destroy']);
    Route::get('responsibility_menu/access_right_user/edit/{id}', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'edit']);
    Route::get('responsibility_menu/access_right_user/get_default_company', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_default_company']);
    Route::get('responsibility_menu/access_right_user/get_assigned_company', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_assigned_company']);
    Route::get('responsibility_menu/access_right_user/get_menu_desc', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_menu_desc']);
    Route::get('responsibility_menu/access_right_user/get_menu_name', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_menu_name']);
    Route::get('responsibility_menu/access_right_user/get_menu', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_menu']);
    Route::get('responsibility_menu/access_right_user/get_grade', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_grade']);
    Route::get('responsibility_menu/access_right_user/get_region', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_region']);
    Route::get('responsibility_menu/access_right_user/get_region_branch', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_region_branch']);
    Route::get('responsibility_menu/access_right_user/get_branch', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_branch']);
    Route::get('responsibility_menu/access_right_user/getUserByCompany', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'getUserByCompany']);
    Route::get('responsibility_menu/access_right_user/get_user_responsibility', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_user_responsibility']);
    Route::post('responsibility_menu/access_right_user/role', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'role']);
    Route::get('responsibility_menu/access_right_user/get_default_access', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_default_access']);
    Route::get('responsibility_menu/access_right_user/get_default_access_edit', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'get_default_access_edit']);
    /*     * ** */

    // SET MENU BY DEFAULT ACCESS GROUP
    Route::get('set_menu/{id_company}', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'set_menu']);
    Route::post('responsibility_menu/assignMenu', [App\Http\Controllers\Setting\ResponsibilityUser\MasterUserController::class, 'assignMenu']);
    
});


Route::prefix('career_administration')->middleware('login-verification')->group(function () {
	/* Award */
	Route::get('award_dicipline/awards', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'index'])->name('award.index');
    Route::post('award_dicipline/awards/save', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'save'])->name('award.save');
    Route::post('award_dicipline/awards/save_announ', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'save_announ'])->name('award.save_announ');
    Route::post('award_dicipline/awards/update', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'update'])->name('award.update');
    Route::get('award_dicipline/awards/destroy/{id}', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'destroy']);
    Route::get('award_dicipline/awards/edit/{id}', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'edit']);
    Route::get('award_dicipline/awards/get_employee', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'get_employee']);
	Route::get('award_dicipline/awards/get_req_employee', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'get_req_employee']);
    Route::get('award_dicipline/awards/get_company', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'get_company']);
    Route::get('award_dicipline/awards/get_announcement_type', [App\Http\Controllers\AwardDicipline\Award\AwardController::class, 'get_announcement_type']);
	/*     * ** */

	/* Dicipline */
	Route::get('award_dicipline/dicipline', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'index'])->name('dicipline.index');
    Route::post('award_dicipline/dicipline/save', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'save'])->name('dicipline.save');
    Route::post('award_dicipline/dicipline/update', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'update'])->name('dicipline.update');
    Route::get('award_dicipline/dicipline/destroy/{id}', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'destroy']);
    Route::get('award_dicipline/dicipline/edit/{id}', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'edit']);
    Route::get('award_dicipline/dicipline/get_employee', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'get_employee']);
    Route::get('award_dicipline/dicipline/get_employee_edit', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'get_employee_edit']);
    Route::get('award_dicipline/dicipline/get_company', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'get_company']);
    Route::get('award_dicipline/dicipline/get_dicipline_type', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'get_dicipline_type']);
	Route::get('award_dicipline/dicipline/browse', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'browse']);
    Route::get('award_dicipline/dicipline/checkid', [App\Http\Controllers\AwardDicipline\Dicipline\DiciplineController::class, 'checkid']);
	/*     * ** */

	/* Career Transition */
	Route::get('career_transition/career_transition_request', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'index'])->name('career.index');
    Route::post('career_transition/career_transition_request/save', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'save'])->name('career.save');
    Route::post('career_transition/career_transition_request/update', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'update'])->name('career.update');
	Route::get('career_transition/career_transition_request/transition', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'transition']);
    Route::get('career_transition/career_transition_request/submit_approve/{id}', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'submit_approve']);
    Route::get('career_transition/career_transition_request/cancel/{id}', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'cancel']);
    Route::get('career_transition/career_transition_request/destroy/{id}', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'destroy']);
    Route::get('career_transition/career_transition_request/edit/{id}', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'edit']);
    Route::get('career_transition/career_transition_request/get_career_edit', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_career_edit']);
    Route::get('career_transition/career_transition_request/get_employee', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_employee']);
    Route::get('career_transition/career_transition_request/get_company_session', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_company_session']);
    Route::get('career_transition/career_transition_request/get_company_all', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_company_all']);
    Route::get('career_transition/career_transition_request/get_company', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_company']);
    Route::get('career_transition/career_transition_request/get_career_category', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_career_category']);
    Route::get('career_transition/career_transition_request/get_career_type', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_career_type']);
    Route::get('career_transition/career_transition_request/get_position', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_position']);
    Route::get('career_transition/career_transition_request/get_position_detail', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_position_detail']);
    Route::get('career_transition/career_transition_request/get_hierachy', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_hierachy']);
    Route::get('career_transition/career_transition_request/get_approval_status', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_approval_status']);
    Route::get('career_transition/career_transition_request/get_employment_status', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_employment_status']);
    Route::get('career_transition/career_transition_request/get_approval', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_approval']);
    Route::get('career_transition/career_transition_request/get_new_dept', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_new_dept']);
	Route::get('career_transition/career_transition_request/get_new_route', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_new_route']);
	Route::get('career_transition/career_transition_request/get_new_positon', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_new_positon']);
	Route::get('career_transition/career_transition_request/get_new_positon_detail', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_new_positon_detail']);
	Route::get('career_transition/career_transition_request/browse', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'browse']);
    Route::get('career_transition/career_transition_request/checkid', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'checkid']);
    Route::get('career_transition/career_transition_request/browse_job', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'browse_job']);
	Route::get('career_transition/career_transition_request/browse_reco', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'browse_reco']);
    Route::get('career_transition/career_transition_request/checkpos', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'checkpos']);
	Route::get('career_transition/career_transition_request/checkreco', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'checkreco']);
	Route::get('career_transition/career_transition_request/get_terminate_reason', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_terminate_reason']);
	Route::get('career_transition/career_transition_request/get_shift', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_shift']);
	Route::get('career_transition/career_transition_request/get_timezone', [App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController::class, 'get_timezone']);
	/*     * ** */

	/* Career History */	
	Route::get('career_transition/career_transition_history', [App\Http\Controllers\CareerAdministration\CareerHistory\CareerHistoryController::class, 'index'])->name('career_history.index');
	Route::post('career_transition/career_transition_history/update', [App\Http\Controllers\CareerAdministration\CareerHistory\CareerHistoryController::class, 'update'])->name('career_history.update');
    Route::get('career_transition/career_transition_history/get_career_edit', [App\Http\Controllers\CareerAdministration\CareerHistory\CareerHistoryController::class, 'get_career_edit']);
    Route::get('career_transition/career_transition_history/api_surat', [App\Http\Controllers\CareerAdministration\CareerHistory\CareerHistoryController::class, 'api_surat']);
	Route::get('career_transition/career_transition_history/cancel_career/{id}', [App\Http\Controllers\CareerAdministration\CareerHistory\CareerHistoryController::class, 'cancel_career']);

	/*     * ** */
	
	/* Termination */	
	Route::get('career_transition/termination', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'index'])->name('termination.index');
    Route::post('career_transition/termination/save', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'save'])->name('termination.save');
    Route::post('career_transition/termination/update', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'update'])->name('termination.update');
	Route::get('career_transition/termination/transition', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'transition']);
    Route::get('career_transition/termination/submit_approve/{id}', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'submit_approve']);
    Route::get('career_transition/termination/cancel/{id}', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'cancel']);
    Route::get('career_transition/termination/destroy/{id}', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'destroy']);
    Route::get('career_transition/termination/edit/{id}', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'edit']);
    Route::get('career_transition/termination/get_career_edit', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_career_edit']);
    Route::get('career_transition/termination/get_employee', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_employee']);
    Route::get('career_transition/termination/get_company_session', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_company_session']);
    Route::get('career_transition/termination/get_company', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_company']);
    Route::get('career_transition/termination/get_career_category', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_career_category']);
    Route::get('career_transition/termination/get_career_type', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_career_type']);
    Route::get('career_transition/termination/get_position', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_position']);
    Route::get('career_transition/termination/get_position_detail', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_position_detail']);
    Route::get('career_transition/termination/get_hierachy', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_hierachy']);
    Route::get('career_transition/termination/get_approval_status', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_approval_status']);
    Route::get('career_transition/termination/get_employment_status', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_employment_status']);
    Route::get('career_transition/termination/get_approval', [App\Http\Controllers\CareerAdministration\Termination\TerminationController::class, 'get_approval']);
	/*     * ** */
	
	/* Offboarding */
    Route::get('employee_checklist/offboarding', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'index']);
    Route::get('employee_checklist/offboarding/get_data', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'get_data']);
    Route::post('employee_checklist/offboarding/save', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'save'])->name('offboarding.save');
    Route::post('employee_checklist/offboarding/update', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'update'])->name('offboarding.update');
    Route::get('employee_checklist/offboarding/get_detail_offboarding', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'get_detail_offboarding']);
    Route::get('employee_checklist/offboarding/get_type', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'get_type']);
    Route::get('employee_checklist/offboarding/get_employee', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'get_employee']);
    Route::get('employee_checklist/offboarding/get_hr', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'get_hr']);
    Route::get('employee_checklist/offboarding/report', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'report'])->name('offboarding.report');
    Route::get('employee_checklist/offboarding/get_report', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'get_report']);
    Route::get('employee_checklist/offboarding/check_clearence', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'check_clearence']);
//    Route::post('employee_checklist/offboarding/destroy', [App\Http\Controllers\CareerAdministration\EmployeeChecklist\EmployeeChecklistController::class, 'destroy']);

});

// Route::get('learning_management/lms/content/', [App\Http\Controllers\LearningManagement\Lms\ContentController::class, 'video']);

Route::prefix('learning_management')->middleware('login-verification')->group(function () {

    /* CONTENT */ 
    Route::get('lms/content', [App\Http\Controllers\LearningManagement\Lms\ContentController::class, 'index'])->name('content.index');
    Route::post('lms/content/save', [App\Http\Controllers\LearningManagement\Lms\ContentController::class, 'save'])->name('content_attachment.save');
    Route::post('lms/content/update', [App\Http\Controllers\LearningManagement\Lms\ContentController::class, 'update'])->name('content_attachment.update');
    Route::get('lms/content/get_content_type', [App\Http\Controllers\LearningManagement\Lms\ContentController::class, 'get_content_type'])->name('get_content_type');
    Route::get('lms/content/get_content_learning', [App\Http\Controllers\LearningManagement\Lms\ContentController::class, 'get_content_learning'])->name('content_attachment.get_content_learning');
    Route::post('lms/content/destroy', [App\Http\Controllers\LearningManagement\Lms\ContentController::class, 'destroy'])->name('content_attachment.destroy');
    Route::get('content_file/{folder}/{filename}', [App\Http\Controllers\LearningManagement\Lms\ContentController::class,'content_file'])->name('content_attachment.content_file');


    /* QUIZ */ 
    Route::get('lms/quiz', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'index'])->name('quiz.index');
    Route::get('lms/quiz/index_answer', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'index_answer'])->name('quiz.index_answer');
    Route::post('lms/quiz/save', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'save'])->name('quiz.save');
    Route::post('lms/quiz/update', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'update'])->name('quiz.update');
    Route::post('lms/quiz/save_survey', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'save_survey']);
    Route::post('lms/quiz/save_answer', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'save_answer'])->name('quiz.save_answer');
    Route::post('lms/quiz/update_survey', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'update_survey']);
    Route::post('lms/quiz/save_survey_answer', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'save_survey_answer'])->name('quiz.save_survey_answer');
    Route::post('lms/quiz/update_survey_answer', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'update_survey_answer'])->name('quiz.update_survey_answer');
    Route::get('lms/quiz/get_edit_answer', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_edit_answer']);
    Route::post('lms/quiz/update_answer', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'update_answer'])->name('quiz.update_answer');
    Route::post('lms/quiz/destroy', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'destroy'])->name('quiz.destroy');
    Route::get('lms/quiz/destroy_question/{id}', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'destroy_question']);
    Route::get('lms/quiz/destroy_answer/{id}', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'destroy_answer']);
    Route::get('lms/quiz/get_employee', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_employee']);
    Route::get('lms/quiz/get_company', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_company']);
    Route::get('lms/quiz/get_question_type', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_question_type']);
    Route::get('lms/quiz/get_survey_type', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_survey_type']);
    Route::get('lms/quiz/get_answer', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_answer']);
    Route::get('lms/quiz/get_survey_edit', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_survey_edit']);
    Route::get('lms/quiz/get_answer/{id_question?}', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_answer'])->name('quiz.get_answer');
    Route::get('lms/quiz/get_quiz', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_quiz'])->name('quiz.get_quiz');
    Route::get('lms/quiz/get_quiz_reff', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_quiz_reff'])->name('quiz.get_quiz_reff');
    Route::get('lms/quiz/get_user_by_session', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_user_by_session'])->name('quiz.get_user_by_session');
    Route::get('lms/quiz/duplicate', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'duplicateQuiz'])->name('quiz.duplicate');
    Route::get('lms/quiz/get_company_all', [App\Http\Controllers\LearningManagement\Lms\QuizController::class, 'get_company_all'])->name('quiz.get_company_all');

    /* COURSE */ 
    Route::get('lms/course', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'index'])->name('course.index');
    Route::post('lms/course/save', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'save'])->name('course.save');
    Route::post('lms/course/update', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'update'])->name('course.update');
    Route::get('lms/course/get_course', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'get_course'])->name('course.get_course');
    Route::get('lms/course/get_course_detail', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'get_course_detail'])->name('course.get_course_detail');
    Route::get('lms/course/get_course_materi', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'get_course_materi'])->name('course.get_course_materi');
    Route::get('lms/course/get_course_quiz', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'get_course_quiz'])->name('course.get_course_quiz');
    Route::post('lms/course/destroy', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'destroy'])->name('course.destroy');
    Route::post('lms/course/duplicate', [App\Http\Controllers\LearningManagement\Lms\CourseController::class, 'duplicateCourse'])->name('course.duplicate');

    /* TRAINING SCORE */
    Route::get('lms/training_scores', [App\Http\Controllers\LearningManagement\Lms\TrainingScoreController::class, 'index'])->name('training_scores.index');
    Route::get('lms/training_scores/get_data', [App\Http\Controllers\LearningManagement\Lms\TrainingScoreController::class, 'get_data'])->name('training_scores.get_data');
    Route::post('lms/training_scores/save_score', [App\Http\Controllers\LearningManagement\Lms\TrainingScoreController::class, 'save_score'])->name('training_scores.save_score');

    /* GROUP COURSE */ 
    // Route::get('lms/group_course', [App\Http\Controllers\LearningManagement\Lms\GroupCourseController::class, 'index']);
    Route::post('lms/group_course/save', [App\Http\Controllers\LearningManagement\Lms\GroupCourseController::class, 'save']);
    Route::post('lms/group_course/update', [App\Http\Controllers\LearningManagement\Lms\GroupCourseController::class, 'update']);
    Route::get('lms/group_course/getCourse', [App\Http\Controllers\LearningManagement\Lms\GroupCourseController::class, 'getCourse']);
    Route::get('lms/group_course/getGroupCourse', [App\Http\Controllers\LearningManagement\Lms\GroupCourseController::class, 'getGroupCourse']);
    Route::post('lms/group_course/destroy', [App\Http\Controllers\LearningManagement\Lms\GroupCourseController::class, 'destroy']);



    /* EVENT COURSE || SCHEDULE COURSE */ 
    Route::get('lms/schedule_course', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'index']);
    Route::get('lms/schedule_course/get_create_access', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'getCreateAccess'])->name('event_course.get_create_access');
    Route::get('lms/schedule_room', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'index'])->name('event_course.index');
    Route::get('lms/event_course/get_employee', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_employee'])->name('event_course.get_employee');
    Route::get('lms/event_course/get_employee_managed_by', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_employee_managed_by'])->name('event_course.get_employee_managed_by');
    Route::get('lms/event_course/get_timezone', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_timezone']);
    Route::get('lms/event_course/get_event_type', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_event_type']);
    Route::get('lms/event_course/get_timezone', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_timezone']);
    Route::get('lms/event_course/get_course', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_course'])->name('event_course.get_course');
    Route::get('lms/event_course/get_region', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_region'])->name('event_course.get_region');
    Route::get('lms/event_course/get_branch', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_branch'])->name('event_course.get_branch');
    Route::get('lms/event_course/get_checklist', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_checklist'])->name('event_course.get_checklist');
    Route::get('lms/event_course/getContent', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'getContent']);
    Route::get('lms/event_course/get_event_edit', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_event_edit'])->name('event_course.get_event_edit');
    Route::post('lms/event_course/save_event', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'save_event'])->name('event_course.save_event');
    Route::post('lms/event_course/update', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'update'])->name('event_course.update');
    Route::post('lms/event_course/save', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'save'])->name('event_course.save');
    Route::post('lms/event_course/destroy_event', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'destroy_event'])->name('event_course.destroy_event');
    Route::post('lms/event_course/destroy', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'destroy'])->name('event_course.destroy');
    Route::get('download_attachment/{filename}', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class,'download_attachment'])->name('event_course.download_attachment');
    Route::get('lms/event_course/get_employee_detail/{id}', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_employee_detail'])->name('event_course.get_employee_detail');
    Route::get('lms/event_course/get_user_by_session', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'get_user_by_session'])->name('event_course.get_user_by_session');
    Route::get('lms/event_course/getEmployeeHR', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'getEmployeeHR']);
    Route::post('lms/event_course/register', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'register_course']);
    // Route::post('lms/e-pstp/submit', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'submit_epstp'])->name('epstp.submit');
    Route::post('lms/event_course/course/submit-batch', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'upload_course'])->name('event_course.submit');
    Route::post('lms/epstp/submit', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'submit_epstp_result'])->name('epstp_result.submit');
    Route::post('lms/event_course/attendees/submit-batch', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'batch_add_attendees'])->name('event_attendees.submit');
    Route::post('lms/event_course/course_program/submit-batch', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'batch_add_course_program'])->name('event_program.submit');
    Route::post('lms/event_course/duplicate', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'duplicateProgram'])->name('event_course.duplicate');
    Route::get('lms/event_course/download_template', [App\Http\Controllers\LearningManagement\Lms\EventCourseController::class, 'downloadImportTemplate'])->name('event_course.download_template');
    


    /* EVENT CLASSROOM */ 
    Route::get('lms/class_room', [App\Http\Controllers\LearningManagement\Lms\ClassroomController::class, 'index'])->name('classroom.index');
    Route::get('lms/class_room/join/{id}', [App\Http\Controllers\LearningManagement\Lms\ClassroomController::class, 'join'])->name('classroom.join');
    Route::get('lms/class_room/class/', [App\Http\Controllers\LearningManagement\Lms\ClassroomController::class, 'class']);
    Route::post('lms/class_room/save', [App\Http\Controllers\LearningManagement\Lms\ClassroomController::class, 'save'])->name('classroom.save');
    Route::post('lms/class_room/save-course', [App\Http\Controllers\LearningManagement\Lms\ClassroomController::class, 'saveCourse']);
    Route::post('lms/class_room/finish-course', [App\Http\Controllers\LearningManagement\Lms\ClassroomController::class, 'finishCourse']);


    /* SUMMARY EVENT CLASSROOM */ 
    Route::get('lms/summary_event', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'index'])->name('summary_event.index');
    Route::get('lms/summary_event/get_event_result', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'get_event_result'])->name('summary_event.get_event_result');
    Route::post('lms/summary_event/get_course_result', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'get_course_result'])->name('summary_event.get_course_result');
    Route::get('lms/summary_event/get_course', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'get_course']);
    Route::get('lms/summary_event/download', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'download']);
    Route::get('lms/summary_event/download_multiple_programs', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'download_multiple_programs'])->name('summary_event.download_multiple');
    Route::get('lms/summary_event/download_course_report', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'downloadReportByCourse'])->name('summary_event.download_course_report');
    Route::get('lms/summary_event/download_detail', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'download_detail']);
    Route::get('lms/summary_event/getProgram', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'getPrograms'])->name('summary_event.filter_program');
    Route::get('lms/summary_event/getCourse', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'getCourse'])->name('summary_event.filter_course');
    Route::get('lms/summary_event/getEmployee', [App\Http\Controllers\LearningManagement\Lms\SummaryEventController::class, 'getEmployee'])->name('summary_event.filter_employee');


    /* MASTER CATEGORY KPI */ 
    Route::get('kpi_onboarding/master_kpi', [App\Http\Controllers\LearningManagement\Kpi\MasterKpiController::class, 'index'])->name('master_kpi.index');
    Route::get('kpi_onboarding/master_kpi/get_master_kpi', [App\Http\Controllers\LearningManagement\Kpi\MasterKpiController::class, 'get_master_kpi'])->name('master_kpi.get_master_kpi');
    Route::post('kpi_onboarding/master_kpi/save', [App\Http\Controllers\LearningManagement\Kpi\MasterKpiController::class, 'save'])->name('master_kpi.save');
    Route::post('kpi_onboarding/master_kpi/update', [App\Http\Controllers\LearningManagement\Kpi\MasterKpiController::class, 'update'])->name('master_kpi.update');
    Route::post('kpi_onboarding/master_kpi/destroy', [App\Http\Controllers\LearningManagement\Kpi\MasterKpiController::class, 'destroy'])->name('master_kpi.destroy');


    /* KPI ONBOARDING */ 
    Route::get('kpi_onboarding/kpi_onboarding', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'index'])->name('kpi_onboarding.index');
    Route::post('kpi_onboarding/kpi_onboarding/save_summary', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'save_summary'])->name('kpi_onboarding.save_summary');
    Route::post('kpi_onboarding/kpi_onboarding/destroy_summary', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'destroy_summary'])->name('kpi_onboarding.destroy_summary');
    Route::get('kpi_onboarding/kpi_onboarding/get_employee', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_employee'])->name('kpi_onboarding.get_employee');
    Route::get('kpi_onboarding/kpi_onboarding/get_employee_attendee', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_employee_attendee']);
    Route::get('kpi_onboarding/kpi_onboarding/get_kpi_category', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_kpi_category'])->name('kpi_onboarding.get_kpi_category');
    Route::get('kpi_onboarding/kpi_onboarding/get_kpi_type', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_kpi_type'])->name('kpi_onboarding.get_kpi_type');
    Route::get('kpi_onboarding/kpi_onboarding/get_course', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_course'])->name('kpi_onboarding.get_course');
    Route::get('kpi_onboarding/kpi_onboarding/get_kpi_group', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_kpi_group'])->name('kpi_onboarding.get_kpi_group');
    Route::get('kpi_onboarding/kpi_onboarding/get_kpi_edit', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_kpi_edit'])->name('kpi_onboarding.get_kpi_edit');
    Route::post('kpi_onboarding/kpi_onboarding/save', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'save'])->name('kpi_onboarding.save');
    Route::post('kpi_onboarding/kpi_onboarding/update', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'update'])->name('kpi_onboarding.update');
    Route::post('kpi_onboarding/kpi_onboarding/destroy', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'destroy'])->name('kpi_onboarding.destroy');
    Route::get('kpi_onboarding/kpi_onboarding/get_course_by_month', [App\Http\Controllers\LearningManagement\Kpi\KpiOnboardingController::class, 'get_course_by_month']);
    
});


Route::prefix('mail')->group(function () {
    Route::get('borwita', [App\Http\Controllers\EmailController::class, 'index'])->name('mail.borwita');
    Route::get('new_account/{nik}', [App\Http\Controllers\EmailController::class, 'new_account'])->name('mail.new_account');
    Route::get('end_employee', [App\Http\Controllers\EmailController::class, 'end_employee'])->name('mail.end_employee');
    Route::get('fpk_reminder', [App\Http\Controllers\EmailController::class, 'fpk_reminder'])->name('mail.fpk_reminder');
    Route::get('kpk_reminder', [App\Http\Controllers\EmailController::class, 'kpk_reminder'])->name('mail.kpk_reminder');
    Route::post('new_request', [App\Http\Controllers\EmailController::class, 'new_request'])->name('mail.new_request');
    Route::post('new_approval', [App\Http\Controllers\EmailController::class, 'new_approval'])->name('mail.new_approval');
    Route::post('new_revise', [App\Http\Controllers\EmailController::class, 'new_revise'])->name('mail.new_revise');
    Route::post('new_reject', [App\Http\Controllers\EmailController::class, 'new_reject'])->name('mail.new_reject');
    Route::post('submit_mail', [App\Http\Controllers\EmailController::class, 'submit_mail'])->name('mail.submit_mail');
    Route::post('mass_mail', [App\Http\Controllers\EmailController::class, 'mass_mail'])->name('mail.mass_mail');
    Route::post('new_fpk', [App\Http\Controllers\EmailController::class, 'new_fpk'])->name('mail.new_fpk');
    Route::post('new_reco', [App\Http\Controllers\EmailController::class, 'new_reco'])->name('mail.new_reco');
    Route::post('new_kpk', [App\Http\Controllers\EmailController::class, 'new_kpk'])->name('mail.new_kpk');
	Route::post('forgot_password', [\App\Http\Controllers\EmailController::class, 'forgot_password'])->name('mail.forgot_password');
//	Route::post('new_hiring', [\App\Http\Controllers\EmailController::class, 'new_hiring'])->name('mail.new_hiring');

});


Route::prefix('event_management')->middleware('login-verification')->group(function () {

    Route::get('event/event_management', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'index']);
    Route::get('event/event_management/get_employee', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_employee']);
    Route::get('event/event_management/get_timezone', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_timezone']);
    Route::get('event/event_management/get_event_type', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_event_type']);
    Route::get('event/event_management/get_timezone', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_timezone']);
    Route::get('event/event_management/get_course', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_course']);
    Route::get('event/event_management/get_checklist', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_checklist']);
    Route::get('event/event_management/getContent', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'getContent']);
    Route::get('event/event_management/get_event_edit', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_event_edit']);
    Route::post('event/event_management/save_event', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'save_event']);
    Route::post('event/event_management/update', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'update']);
    Route::post('event/event_management/save', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'save']);
    Route::post('event/event_management/destroy_event', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'destroy_event']);
    Route::post('event/event_management/destroy', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'destroy']);
    Route::get('download_attachment/{filename}', [App\Http\Controllers\EventManagement\Event\EventManagementController::class,'download_attachment']);
    Route::get('event/event_management/get_employee_detail/{id}', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_employee_detail']);
    Route::get('event/event_management/get_user_by_session', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'get_user_by_session']);
    Route::get('event/event_management/getEmployeeHR', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'getEmployeeHR']);
    Route::post('event/event_management/register', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'register_course']);
    Route::get('event/event_management/get_room', [App\Http\Controllers\EventManagement\Event\EventManagementController::class, 'getRoom']);

    Route::get('event/master_room', [App\Http\Controllers\EventManagement\Room\MasterRoomController::class, 'index'])->name('event_management.master_room');
    Route::get('event/master_room/get_data', [App\Http\Controllers\EventManagement\Room\MasterRoomController::class, 'getData'])->name('event_management.master_room.get_data');
    Route::get('event/master_room/get_edit', [App\Http\Controllers\EventManagement\Room\MasterRoomController::class, 'getEdit'])->name('event_management.master_room.get_edit');
    Route::post('event/master_room/save', [App\Http\Controllers\EventManagement\Room\MasterRoomController::class, 'save'])->name('event_management.master_room.save');

    Route::get('event/booking_room', [App\Http\Controllers\EventManagement\Room\BookingRoomController::class, 'index'])->name('event_management.booking_room');
    Route::get('event/booking_room/get_data', [App\Http\Controllers\EventManagement\Room\BookingRoomController::class, 'getData'])->name('event_management.booking_room.get_data');
    Route::get('event/booking_room/get_edit', [App\Http\Controllers\EventManagement\Room\BookingRoomController::class, 'getEdit'])->name('event_management.booking_room.get_edit');
    Route::post('event/booking_room/save', [App\Http\Controllers\EventManagement\Room\BookingRoomController::class, 'save'])->name('event_management.booking_room.save');

    Route::get('event/event_room_history', [App\Http\Controllers\EventManagement\Room\BookingRoomController::class, 'history'])->name('event_management.booking_room.history');
});

Route::prefix('api/v1')->group(function () { //for mobile MYBORWITA
    Route::get('employee', [App\Http\Controllers\API\v1\EmployeeController::class, 'employeeDetail'])->name('api.v1.employee');
    Route::get('employee-all', [App\Http\Controllers\API\v1\EmployeeController::class, 'employeeDetailAll']);
    Route::get('employee/companies', [App\Http\Controllers\API\v1\EmployeeController::class, 'getEmployeeCompanies'])->name('api.v1.get-employee-companies');

    Route::get('check-version', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'getVersion'])->name('api.v1.get-version');
    // Route::post('mobile-serial-number', [App\Http\Controllers\API\v1\AppController::class, 'replaceSerialNumber'])->name('api.v1.replace-serial');
    

    //ATTENDANCE
    Route::get('attendance/get-list', [App\Http\Controllers\API\v1\AttendanceController::class, 'listAttendance']);
    Route::get('attendance/get-mapbox-token', [App\Http\Controllers\API\v1\AttendanceController::class, 'getMapToken']);
    Route::post('attendance/confirm-location', [App\Http\Controllers\API\v1\AttendanceController::class, 'confirmLocation']);
    Route::post('attendance/submit', [App\Http\Controllers\API\v1\AttendanceController::class, 'submitAttendance']);
    Route::get('attendance/status/count', [App\Http\Controllers\API\v1\AttendanceController::class, 'getAttendanceStatusCount']);

    //ANNOUNCEMENT
    Route::get('announcement/get-announcement-types', [App\Http\Controllers\API\v1\AnnouncementController::class, 'getAnnouncementTypes']);
    Route::get('announcement/get-announcements', [App\Http\Controllers\API\v1\AnnouncementController::class, 'allAnnouncement']);
    Route::post('announcement/mark-as-read', [App\Http\Controllers\API\v1\AnnouncementController::class, 'markAsRead']);

    //SURVEY
    Route::get('survey/get-list', [App\Http\Controllers\API\v1\SurveyController::class, 'getActiveSurveyToWeb']);
    Route::get('survey/get-active-surveys', [App\Http\Controllers\API\v1\SurveyController::class, 'getActiveSurveys']);
    Route::post('survey/submit', [App\Http\Controllers\API\v1\SurveyController::class, 'submit']);
    Route::get('survey/get-all-surveys', [App\Http\Controllers\API\v1\SurveyController::class, 'allSurveys']);

    //EMPLOYEE REQUESTS
    Route::get('employee-request/get-list-history', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'listRequest']);
    Route::get('employee-request/get-form-data', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'addRequest']);
    Route::get('employee-request/get-list-leave-balance', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'getLeaveBalance']);
    Route::get('employee-request/get-list-approval', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'getListApproval']);
    Route::get('employee-request/check-quantity-workdays', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'checkQuantityWorkdays']);
    Route::post('employee-request/check-actual-time', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'checkActualTime']);
    Route::post('employee-request/submit', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'saveRequest']);
    Route::post('employee-request/update', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'updateRequest']);
    Route::post('employee-request/submit-to-approval', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'submitToApproval']);
    Route::get('employee-request/count', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'getRequestCount']);
    Route::get('employee-request/count-by-category', [App\Http\Controllers\API\v1\EmployeeRequestController::class, 'getEmployeeRequestCountByCategory']);

    //EMPLOYEE APPROVAL
    Route::get('employee-approval/get-form-data', [App\Http\Controllers\API\v1\EmployeeApprovalController::class, 'formData']);
    Route::get('employee-approval/get-list-outstanding', [App\Http\Controllers\API\v1\EmployeeApprovalController::class, 'listApproval']);
    Route::get('employee-approval/get-list-history', [App\Http\Controllers\API\v1\EmployeeApprovalController::class, 'listApprovalHistory']);
    Route::get('employee-approval/get-outstanding-approval-count', [App\Http\Controllers\API\v1\EmployeeApprovalController::class, 'approvalCount']);
    Route::post('employee-approval/reject', [App\Http\Controllers\API\v1\EmployeeApprovalController::class, 'reject']);
    Route::post('employee-approval/approve', [App\Http\Controllers\API\v1\EmployeeApprovalController::class, 'approve']);
    Route::post('employee-approval/revise', [App\Http\Controllers\API\v1\EmployeeApprovalController::class, 'revise']);

    Route::get('official-travel/get-list', [App\Http\Controllers\API\v1\OfficialTravelController::class, 'index']);
    Route::get('official-travel/get-detail', [App\Http\Controllers\API\v1\OfficialTravelController::class, 'detail']);
    Route::get('official-travel/get-form-data', [App\Http\Controllers\API\v1\OfficialTravelController::class, 'getFormData']);
    Route::get('official-travel/get-regions', [App\Http\Controllers\API\v1\OfficialTravelController::class, 'getRegions']);
    Route::get('official-travel/get-branches', [App\Http\Controllers\API\v1\OfficialTravelController::class, 'getBranches']);
    Route::get('official-travel/get-project-approval', [App\Http\Controllers\API\v1\OfficialTravelController::class, 'getProjectApproval']);
    Route::post('official-travel/submit', [App\Http\Controllers\API\v1\OfficialTravelController::class, 'submit']);

    //CAMPAIGN
    Route::get('campaign/get-list', [App\Http\Controllers\API\v1\CampaignController::class, 'listCampaign']);
    Route::post('campaign/mark-as-read', [App\Http\Controllers\API\v1\CampaignController::class, 'markAsRead']);
	
	//LOGIN
	Route::post('employee-login/check-login', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'login']);
	Route::post('employee-login/submit-serial', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'replaceSerialNumber']);
	Route::get('employee-login/get-list-companies', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'getCompany']);
	Route::get('employee-login/get-company-by-user', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'getCompanyUser']);
	Route::get('employee-login/get-version', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'getVersion']);
    Route::post('employee-login/change-password', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'changePassword']);
    Route::post('employee-login/reset-password', [App\Http\Controllers\API\v1\EmployeeLoginController::class, 'resetPassword']);

    Route::get('approval-transaction/get-data-mapping-branch-divisi-non-hris', [App\Http\Controllers\API\v1\OasysController::class, 'getData']);
    Route::get('approval-transaction/get-approval-mapping-branch-divisi-non-hris', [App\Http\Controllers\API\v1\OasysController::class, 'getApproval']);
    Route::get('approval-transaction/get-submission-mapping-branch-divisi-non-hris', [App\Http\Controllers\API\v1\OasysController::class, 'getSubmission']);
    Route::post('approval-transaction/submit-mapping-branch-divisi-non-hris', [App\Http\Controllers\API\v1\OasysController::class, 'submit']);

    Route::get('assets/get-data', [App\Http\Controllers\API\v1\AssetsController::class, 'getData']);
    Route::post('assets/submit', [App\Http\Controllers\API\v1\AssetsController::class, 'submit']);
});

Route::prefix('api/hris')->group(function () { //for https://hris.borwita.co.id/
    Route::get('employee', [App\Http\Controllers\API\HrisController::class, 'employeeDetailHris'])->name('api.hris.employee');
});

Route::prefix('employee')->middleware('login-verification')->group(function () {
	Route::get('employee_setting/custom_report', [App\Http\Controllers\Employee\EmployeeSetting\CustomReportController::class, 'index'])->name('custom.index');
	Route::get('employee_setting/custom_report/destroy/{id}', [App\Http\Controllers\Employee\EmployeeSetting\CustomReportController::class, 'destroy']);
    Route::post('employee_setting/custom_report/save_custom', [App\Http\Controllers\Employee\EmployeeSetting\CustomReportController::class, 'save_custom'])->name('custom.save_custom');
    Route::post('employee_setting/custom_report/update_custom', [App\Http\Controllers\Employee\EmployeeSetting\CustomReportController::class, 'update_custom'])->name('custom.update_custom');
    Route::get('employee_setting/custom_report/get_custom_edit', [App\Http\Controllers\Employee\EmployeeSetting\CustomReportController::class, 'get_custom_edit']);
	Route::get('employee_setting/custom_report/get_data_custom', [App\Http\Controllers\Employee\EmployeeSetting\CustomReportController::class, 'get_data_custom']);
    Route::get('employee_setting/custom_report/get_region_branch', [App\Http\Controllers\Employee\EmployeeSetting\CustomReportController::class, 'get_region_branch']);

});

Route::prefix('message')->group(function () {
    Route::post('whatsapp', [App\Http\Controllers\MessageController::class, 'whatsapp'])->name('message.whatsapp');

});

Route::prefix('kpi')->middleware('login-verification')->group(function () {
	/* Master PA Question */
	Route::get('kpi_settings/master_kpi_question', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'index'])->name('pa_question.index');
	Route::post('kpi_settings/master_kpi_question/save', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'save'])->name('pa_question.save');
	Route::post('kpi_settings/master_kpi_question/update', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'update'])->name('pa_question.update');
    Route::get('kpi_settings/master_kpi_question/destroy/{id}', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'destroy']);
    Route::get('kpi_settings/master_kpi_question/edit/{id}', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'edit']);
    Route::get('kpi_settings/master_kpi_question/get_question_edit', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'get_question_edit']);
    Route::get('kpi_settings/master_kpi_question/get_question_group', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'get_question_group']);
    Route::get('kpi_settings/master_kpi_question/get_question_type', [App\Http\Controllers\Kpi\KpiSetting\PaQuestionController::class, 'get_question_type']);
	
	/* Master PA Grade */
	Route::get('kpi_settings/master_grade_question', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'index'])->name('pa_grade.index');
	Route::post('kpi_settings/master_grade_question/save', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'save'])->name('pa_grade.save');
	Route::post('kpi_settings/master_grade_question/update', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'update'])->name('pa_grade.update');
    Route::get('kpi_settings/master_grade_question/destroy/{id}', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'destroy']);
    Route::get('kpi_settings/master_grade_question/edit/{id}', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'edit']);
    Route::get('kpi_settings/master_grade_question/get_grade_edit', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'get_grade_edit']);
    Route::get('kpi_settings/master_grade_question/get_question', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'get_question']);
    Route::get('kpi_settings/master_grade_question/get_level', [App\Http\Controllers\Kpi\KpiSetting\PaGradeController::class, 'get_level']);
	
	/* Qualitative Mapping */
	Route::get('360_feedback/mapping_qualitative_review', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'index']);
	Route::post('360_feedback/mapping_qualitative_review/save', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'save'])->name('qualitative.save');
	Route::post('360_feedback/mapping_qualitative_review/update', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'update'])->name('qualitative.update');
    Route::get('360_feedback/mapping_qualitative_review/reset_appraiser/{id}', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'reset_appraiser']);
    Route::get('360_feedback/mapping_qualitative_review/edit/{id}', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'edit']);
    Route::get('360_feedback/mapping_qualitative_review/get_qualitative_edit', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_qualitative_edit']);
    Route::get('360_feedback/mapping_qualitative_review/get_employee', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_employee']);
    Route::get('360_feedback/mapping_qualitative_review/get_grade', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_grade']);
    Route::get('360_feedback/mapping_qualitative_review/get_employee_filter', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_employee_filter']);
    Route::post('360_feedback/mapping_qualitative_review/upload_review', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'upload_review']);
    Route::get('360_feedback/mapping_qualitative_review/get_cross_company', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_cross_company']);
    Route::get('360_feedback/mapping_qualitative_review/get_cross_company_emp', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_cross_company_emp']);
    Route::get('360_feedback/mapping_qualitative_review/get_period', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_period']);
	Route::post('360_feedback/mapping_qualitative_review/generate', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'generate'])->name('qualitative.generate');
	Route::get('360_feedback/mapping_qualitative_review/modal_mail', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'modal_mail'])->name('qualitative.modal_mail');
	Route::get('360_feedback/mapping_qualitative_review/list_mail', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'list_mail']);
	Route::get('360_feedback/mapping_qualitative_review/get_employee_mail', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'get_employee_mail']);
//	Route::post('360_feedback/mapping_qualitative_review/mass_mail', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'mass_mail']);
	Route::get('360_feedback/mapping_qualitative_review/send_mail', [App\Http\Controllers\Kpi\Kpi\MappingQualitativeController::class, 'send_mail']);
	
	/* Qualitative Appraiser */
	Route::get('360_feedback/pa_qualitative', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'index'])->name('appraisal.index');
	Route::get('360_feedback/export_validate', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'export_validate'])->name('appraisal.export_validate');
	Route::post('360_feedback/pa_qualitative/save', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'save'])->name('appraisal.save');
	Route::post('360_feedback/pa_qualitative/update', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'update'])->name('appraisal.update');
	Route::get('360_feedback/pa_qualitative/review', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'review'])->name('appraisal.review');
    Route::get('360_feedback/pa_qualitative/destroy/{id}', [App\Http\Controllers\Kpi\AppraiserQualitativeController::class, 'destroy']);
    Route::get('360_feedback/pa_qualitative/edit/{id}', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'edit']);
    Route::get('360_feedback/pa_qualitative/get_appraiser_edit', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'get_appraiser_edit']);
    Route::get('360_feedback/pa_qualitative/get_employee', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'get_employee']);
    Route::get('360_feedback/pa_qualitative/get_period', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'get_period']);
    Route::get('360_feedback/export_detail', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'export_detail']);
    Route::get('360_feedback/pa_qualitative/list_review', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'list_review']);
    Route::get('360_feedback/pa_qualitative/get_detail_review', [App\Http\Controllers\Kpi\Kpi\AppraiserQualitativeController::class, 'get_detail_review']);
	
	/* Report 360 */
	Route::get('report_pa/report_360', [App\Http\Controllers\Kpi\Kpi\ReportQualitativeController::class, 'index_report'])->name('report_qualitative.index_report');
    Route::get('report_360/export_validate', [App\Http\Controllers\Kpi\Kpi\ReportQualitativeController::class, 'export_validate'])->name('report_360.export_validate');
    Route::get('report_360/export', [App\Http\Controllers\Kpi\Kpi\ReportQualitativeController::class, 'export']);
    Route::get('report_360/export_detail', [App\Http\Controllers\Kpi\Kpi\ReportQualitativeController::class, 'export_detail']);
    Route::get('report_pa/report_360/get_period_report', [App\Http\Controllers\Kpi\Kpi\ReportQualitativeController::class, 'get_period_report']);
	
	/* Report KPI */
	Route::get('report_pa/report_kpi', [App\Http\Controllers\Kpi\Kpi\ReportKpiController::class, 'index_report'])->name('report_kpi.index_report');
    Route::get('report_kpi/export_validate', [App\Http\Controllers\Kpi\Kpi\ReportKpiController::class, 'export_validate'])->name('report_kpi.export_validate');
    Route::get('report_kpi/export', [App\Http\Controllers\Kpi\Kpi\ReportKpiController::class, 'export'])->name('report_kpi.export');
    Route::get('report_pa/report_kpi/get_period_report', [App\Http\Controllers\Kpi\Kpi\ReportKpiController::class, 'get_period_report']);
    Route::get('report_pa/report_kpi/get_employee_filter', [App\Http\Controllers\Kpi\Kpi\ReportKpiController::class, 'get_employee_filter']);

	/* Quantitative Assessment */
	Route::get('kpi/pa_quantitative_assesment', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'index'])->name('quantitative.index');
//	Route::post('kpi/pa_quantitative_assesment/save', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'save'])->name('itemkpi.save');
	Route::post('kpi/pa_quantitative_assesment/update', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'update'])->name('itemkpi.update');
    Route::get('kpi/pa_quantitative_assesment/edit/{id}', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'edit']);
    Route::get('kpi/pa_quantitative_assesment/get_kpi_edit', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_kpi_edit']);
    Route::get('kpi/pa_quantitative_assesment/get_kpi_view', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_kpi_view']);
    Route::get('kpi/pa_quantitative_assesment/get_kpi_total', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_kpi_total']);
    Route::get('kpi/pa_quantitative_assesment/get_item_kpi', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_item_kpi']);
    Route::get('kpi/pa_quantitative_assesment/get_employee', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_employee']);
    Route::get('kpi/pa_quantitative_assesment/get_period', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_period']);
    Route::get('kpi/pa_quantitative_assesment/get_category', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_category']);
    Route::get('kpi/pa_quantitative_assesment/get_type', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_type']);
    Route::get('kpi/pa_quantitative_assesment/generate_kpi', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'generate_kpi']);
    Route::get('kpi/pa_quantitative_assesment/calyearly', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'calyearly']);
    Route::get('kpi/pa_quantitative_assesment/calmonthly', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'calmonthly']);
    Route::get('kpi/pa_quantitative_assesment/get_monthly', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_monthly']);
	
	/* Upload KPI */
    Route::get('kpi/kpi_upload/', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'index_upload']);
	Route::post('kpi/kpi_upload/upload_review', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'upload_review']);
	Route::get('kpi/kpi_upload/get_inactive', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_inactive']);
	Route::get('kpi/kpi_upload/get_emp_inactive', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'get_emp_inactive']);
	Route::post('kpi/kpi_upload/update_inactive', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'update_inactive'])->name('inactive.update_inactive');

//    Route::get('kpi/kpi_upload/inactive/{id}', [App\Http\Controllers\Kpi\Kpi\QuantitativeKpiController::class, 'inactive']);
	
	
	/* FPR Management*/
    Route::get('fpr/fpr_management/', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'index_report'])->name('fpr_management.index_report');
    Route::get('fpr/fpr_management/generate_fpr', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'generate_fpr']);
    Route::get('fpr/fpr_management/get_period', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'get_period']);
    Route::get('fpr/fpr_management/download', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'download']);

	/* FPR*/
    Route::get('fpr/fpr_subordinate/', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'index'])->name('fpr.index');
    Route::get('fpr/fpr_self/', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'index'])->name('fpr.index');
    Route::get('fpr/fpr/get_fpr_edit', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'get_fpr_edit']);
    Route::post('fpr/fpr/update', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'update'])->name('fpr.update');
    Route::post('fpr/fpr/draft', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'draft'])->name('fpr.draft');
    Route::get('fpr/fpr_subordinate/download', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'download']);
    Route::get('fpr/fpr_self/download', [App\Http\Controllers\Kpi\Fpr\FprController::class, 'download']);
	
    /* Propose Rating */
    Route::get('pa_rating/pa_employee_result/', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'index'])->name('rating.index');
    Route::get('pa_rating/pa_employee_result/get_rating_edit', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'get_rating_edit']);
	Route::post('pa_rating/pa_employee_result/update', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'update'])->name('rating.update');
    Route::get('pa_rating/pa_employee_result/calpa', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'calpa']);
    Route::get('pa_rating/pa_employee_result/calpa_id_emp', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'calpa']);
    Route::get('pa_rating/pa_employee_result/filter', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'filter']);
    Route::get('pa_rating/pa_employee_result/get_period', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'get_period']);
    Route::get('pa_rating/pa_employee_result/get_rating', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'get_rating']);
	
	 /* Report All Rating */
    Route::get('report_pa/report_all/', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'index_report'])->name('report_rating.index_report');
    Route::post('report_pa/report_all/upload_final_rating', [App\Http\Controllers\Kpi\Rating\RatingController::class, 'upload_final_rating']);

});


Route::prefix('recruitment')->middleware('login-verification')->group(function () {
	/* Hiring Request */
    Route::get('recruitment/hiring_request/', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'index'])->name('hiring.index');
	Route::post('recruitment/hiring_request/save', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'save'])->name('hiring.save');
	Route::post('recruitment/hiring_request/update', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'update'])->name('hiring.update');
    Route::get('recruitment/hiring_request/get_hiring_edit', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_hiring_edit']);
    Route::get('recruitment/hiring_request/get_employee_by', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_employee_by']);
    Route::get('recruitment/hiring_request/get_pos_by', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_pos_by']);
    Route::get('recruitment/hiring_request/get_sla', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_sla']);
    Route::get('recruitment/hiring_request/get_branch', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_branch']);
    Route::get('recruitment/hiring_request/get_pos_detail', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_pos_detail']);
    Route::get('recruitment/hiring_request/get_emp_reco', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_emp_reco']);
    Route::get('recruitment/hiring_request/get_hr_email', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_hr_email']);
    Route::get('recruitment/hiring_request/get_approval_status', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_approval_status']);
	Route::get('recruitment/hiring_request/get_emp_edit', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_emp_edit']);
    Route::get('recruitment/hiring_request/get_hierachy_fpk', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_hierachy_fpk']);
    Route::get('recruitment/hiring_request/get_hierachy_approver', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_hierachy_approver']);
    Route::get('recruitment/hiring_request/submit_approve/{id}', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'submit_approve']);
    Route::get('recruitment/hiring_request/cancel/{id}', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'cancel']);
    Route::get('recruitment/hiring_request/get_info', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_info'])->name('hiring.info');
	
	/* Hiring Summary */
    Route::get('recruitment/hiring_summary/', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'index_summary'])->name('summary.index');
	Route::post('recruitment/hiring_summary/update_summary', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'update_summary'])->name('summary.update_summary');
	Route::get('recruitment/hiring_summary/get_summary_edit', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_summary_edit']);
	
	/* Hiring Detail Summary */
    Route::get('recruitment/hiring_detail_summary/', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'index_detail_summary'])->name('detail_summary.index');
	Route::post('recruitment/hiring_detail_summary/update_detail_summary', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'update_detail_summary'])->name('detail_summary.update_detail');
	Route::get('recruitment/hiring_detail_summary/get_detail_summary_edit', [App\Http\Controllers\Recruitment\HiringRequest\HiringRequestController::class, 'get_detail_summary_edit']);
	
	/* Batch */
    Route::get('psychotest/master_batch/', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'index'])->name('batch.index');
    Route::get('psychotest/master_batch/review', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'review'])->name('batch.review');
	Route::post('psychotest/master_batch/save', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'save'])->name('batch.save');
	Route::post('psychotest/master_batch/update', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'update'])->name('batch.update');
    Route::get('psychotest/master_batch/get_participant', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'get_participant']);
    Route::get('psychotest/master_batch/get_participant_edit', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'get_participant_edit']);
	Route::get('psychotest/master_batch/get_batch_edit', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'get_batch_edit']);
	Route::get('psychotest/master_batch/get_region_batch', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'get_region_batch']);
	Route::get('psychotest/master_batch/get_region_batch_edit', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'get_region_batch_edit']);
	Route::get('psychotest/master_batch/get_branch_batch', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'get_branch_batch']);
	Route::get('psychotest/master_batch/destroy/{id}', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'destroy']);
    Route::post('psychotest/master_batch/upload_review', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'upload_review']);
    Route::get('psychotest/master_batch/list_review', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'list_review'])->name('batch.list_review');
    Route::get('psychotest/master_batch/download_zip', [App\Http\Controllers\Recruitment\Batch\MasterBatchController::class, 'downloadZip'])->name('master_batch.download_zip');
	
	/* Recruitment Tracking */
    Route::get('recruitment/recruitment_tracking/', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'index'])->name('candidate.index');
    Route::get('recruitment/recruitment_tracking/get_status_tracking', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'get_status_tracking']);
    Route::get('recruitment/recruitment_tracking/filter_candidate', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'filter_candidate']);
    Route::get('recruitment/recruitment_tracking/filter_job', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'filter_job']);
    Route::get('recruitment/recruitment_tracking/get_tracking', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'get_tracking']);
    Route::get('recruitment/recruitment_tracking/get_total', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'get_total']);
	Route::post('recruitment/recruitment_tracking/update', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'update'])->name('candidate.update');
	Route::post('recruitment/recruitment_tracking/update_status', [App\Http\Controllers\Recruitment\Candidate\CandidateController::class, 'update_status'])->name('candidate.update_status');
	
	/* Candidate Data */
    Route::get('recruitment/candidate/', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'index']);
    Route::get('recruitment/candidate/index_group', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'index_group'])->name('candidate_data.index_group');
	Route::get('recruitment/candidate/get_detail_group', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_detail_group'])->name('candidate_data.get_detail_group');
    Route::get('recruitment/candidate/index_can', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'index_can'])->name('candidate_data.index_can');
    Route::get('recruitment/candidate/modal_detail', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'modal_detail'])->name('candidate_data.modal_detail');
	Route::get('recruitment/candidate/get_edit_detail', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_edit_detail']);
	Route::get('recruitment/candidate/get_pos', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_pos']);
	Route::get('recruitment/candidate/get_pos_done', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_pos_done']);
	Route::get('recruitment/candidate/get_pos_change', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_pos_change']);
	Route::get('recruitment/candidate/get_stage', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_stage']);
	Route::get('recruitment/candidate/get_mass_stage', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_mass_stage']);
//	Route::get('recruitment/candidate/get_conclusion', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_conclusion']);
	Route::get('recruitment/candidate/get_family', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_family']);
	Route::get('recruitment/candidate/get_ex', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_ex']);
	Route::get('recruitment/candidate/get_skill', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_skill']);
	Route::get('recruitment/candidate/get_cert', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_cert']);
//	Route::get('recruitment/candidate/get_question', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_question']);
	Route::post('recruitment/candidate/update', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'update'])->name('candidate_data.update');
	Route::get('recruitment/candidate/cancel_join/{id}', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'cancel_join']);
	Route::get('recruitment/candidate/check_stage', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'check_stage']);
	Route::get('recruitment/candidate/get_interview', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_interview']);
	Route::get('recruitment/candidate/get_psychotest', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_psychotest']);
	Route::get('recruitment/candidate/download', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'download']);
	Route::post('recruitment/candidate/mass_submit', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'mass_submit']);
	Route::get('recruitment/candidate/get_can_name', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'get_can_name']);
    Route::get('recruitment/candidate/get_candidate_batch', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'getCandidateBatch'])->name('candidate_data.get_batch');
    Route::post('recruitment/candidate/assign_candidate_batch', [App\Http\Controllers\Recruitment\Candidate\CandidateDataController::class, 'assignCandidateToBatch'])->name('candidate_data.assign_batch');
	
	/* Interview Data */
    Route::get('personality_assessment/master_interview_question/', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'index'])->name('rec_group.index');
	Route::get('personality_assessment/master_interview_question/get_stage', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'get_stage']);
	Route::get('personality_assessment/master_interview_question/get_answer', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'get_answer']);
	Route::get('personality_assessment/master_interview_question/index_answer', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'index_answer'])->name('rec_answer.index_answer');
	Route::get('personality_assessment/master_interview_question/destroy/{id}', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'destroy']);
	Route::get('personality_assessment/master_interview_question/destroy_answer/{id}', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'destroy_answer']);
	Route::post('personality_assessment/master_interview_question/save', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'save'])->name('rec_group.save');
	Route::post('personality_assessment/master_interview_question/update', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'update'])->name('rec_group.update');
	Route::post('personality_assessment/master_interview_question/save_answer', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'save_answer'])->name('answer_interview.save_answer');
	Route::post('personality_assessment/master_interview_question/update_answer', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'update_answer'])->name('answer_interview.update_answer');
	Route::get('personality_assessment/master_interview_question/get_edit_answer', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'get_edit_answer']);
	Route::get('personality_assessment/master_interview_question/get_question_type', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'get_question_type']);
	Route::post('personality_assessment/master_interview_question/save_interview', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'save_interview']);
	Route::post('personality_assessment/master_interview_question/update_interview', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'update_interview']);
	Route::post('personality_assessment/master_interview_question/save_interview_answer', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'save_interview_answer']);
	Route::post('personality_assessment/master_interview_question/update_interview_answer', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'update_interview_answer']);
	Route::get('personality_assessment/master_interview_question/get_edit_interview', [App\Http\Controllers\Recruitment\Interview\InterviewController::class, 'get_edit_interview']);
	
	
	/* Interview Question */
	Route::get('personality_assessment/interview_question/', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'index'])->name('interview_ques.index');
	Route::get('personality_assessment/interview_question/get_edit_question', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'get_edit_question']);
	Route::post('personality_assessment/interview_question/update', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'update'])->name('interview_ques.update');
	Route::get('personality_assessment/interview_question/get_question', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'get_question']);
 	Route::get('personality_assessment/interview_question/get_conclusion', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'get_conclusion']);
    Route::get('personality_assessment/interview_question/print', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'print_interview']);
    Route::get('personality_assessment/interview_question_summary/print_bei', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'print_bei']);
	
	/* Interview Question Summary*/
	Route::get('personality_assessment/interview_question_summary/', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'index_summary'])->name('interview_summary.index'); 
	Route::get('personality_assessment/interview_question/get_edit_question_summary', [App\Http\Controllers\Recruitment\Interview\InterviewQuestionController::class, 'get_edit_question_summary']);
	
	/* Psychotest Summary*/
	Route::get('psychotest/disc_summary/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_disc']);
	Route::get('psychotest/disc_summary/index_disc_can', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_disc_can'])->name('disc.index_disc_can');
	Route::get('psychotest/disc_summary/index_disc_emp', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_disc_emp'])->name('disc.index_disc_emp');Route::get('psychotest/papikostick_summary/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_papi']);
	Route::get('psychotest/papikostick_summary/index_papi_can', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_papi_can'])->name('papi.index_papi_can');
	Route::get('psychotest/papikostick_summary/index_papi_emp', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_papi_emp'])->name('papi.index_papi_emp');
	Route::get('psychotest/kreapelin_summary/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_kraepelin']);
	Route::get('psychotest/kreapelin_summary/index_kraepelin_can', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_kraepelin_can'])->name('kraepelin.index_kraepelin_can');
	Route::get('psychotest/kreapelin_summary/index_kraepelin_emp', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_kraepelin_emp'])->name('kraepelin.index_kraepelin_emp');
	Route::get('psychotest/bct_summary/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_bct']);
	Route::get('psychotest/bct_summary/index_bct_can', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_bct_can'])->name('bct.index_bct_can');
	Route::get('psychotest/bct_summary/index_bct_emp', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_bct_emp'])->name('bct.index_bct_emp');
	Route::get('psychotest/psychogram/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_psychogram']);
    Route::get('psychotest/psychogram/download', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'psychogramExport']);
	Route::get('psychotest/psychogram/index_psychogram_can', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_psychogram_can'])->name('psychogram.index_psychogram_can');
	Route::get('psychotest/psychogram/index_psychogram_emp', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'index_psychogram_emp'])->name('psychogram.index_psychogram_emp');
    Route::get('psychotest/psychogram/get_grade', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'get_grade']);
    Route::get('psychotest/psychogram/get_department', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'get_department']);
    Route::post('psychotest/psychogram/get_psychogram', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'get_psychogram']);
    Route::get('psychotest/psychogram/get_batch/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'get_batch']);
    Route::get('psychotest/psychogram/get_position/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'get_position']);
    Route::get('psychotest/kreapelin_summary/get_kraepelin_result/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'get_kraepelin_result']);
    Route::post('psychotest/kreapelin_summary/reset_kraepelin_result/', [App\Http\Controllers\Recruitment\Psychotest\PsychotestController::class, 'reset_kraepelin_result']);

});

Route::prefix('talent_management')->middleware('login-verification')->group(function () {
    /* Master Talent */
    Route::get('master_talent_setting/master_talent_rating/', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'index'])->name('talent.index');
    Route::get('master_talent_setting/master_talent_rating/modal_detail', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'modal_detail'])->name('talent.modal_detail');
    Route::get('master_talent_setting/master_talent_rating/get_grade', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'get_grade']);
    Route::get('master_talent_setting/master_talent_rating/get_rating', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'get_rating']);
    Route::get('master_talent_setting/master_talent_rating/get_group_matrix', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'get_group_matrix']);
    Route::get('master_talent_setting/master_talent_rating/get_conclusion', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'get_conclusion']);
    Route::post('master_talent_setting/master_talent_rating/save', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'save'])->name('talent.save');
    Route::post('master_talent_setting/master_talent_rating/update', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'update'])->name('talent.update');
    Route::get('master_talent_setting/master_talent_rating/destroy/{id}', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'destroy']);
    Route::get('master_talent_setting/master_talent_rating/get_talent_edit', [App\Http\Controllers\TalentManagement\MasterTalent\MasterTalentController::class, 'get_talent_edit']);

	/* Talent Reco */
	Route::get('talent_development/talent_recomendation/', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'index'])->name('talent_reco.index');
	Route::post('talent_development/talent_recomendation/save', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'save'])->name('talent_reco.save');
	Route::post('talent_development/talent_recomendation/save_batch', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'save_batch'])->name('talent_reco.save_batch');
	Route::post('talent_development/talent_recomendation/save_bei', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'save_bei'])->name('talent_reco.save_bei');
	Route::post('talent_development/talent_recomendation/update', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'update'])->name('talent_reco.update');
	Route::get('talent_development/talent_recomendation/modal_detail', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'modal_detail'])->name('talent_reco.modal_detail');
	Route::get('talent_development/talent_recomendation/modal_list', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'modal_list'])->name('talent_reco.modal_list');
	Route::get('talent_development/talent_recomendation/modal_batch', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'modal_batch'])->name('talent_reco.modal_batch');
	Route::get('talent_development/talent_recomendation/modal_bei', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'modal_bei'])->name('talent_reco.modal_bei');
    Route::get('talent_development/talent_recomendation/modal_psychogram', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'modal_psychogram'])->name('talent_reco.modal_psychogram');
	Route::get('talent_development/talent_recomendation/get_employee_by', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_employee_by']);
	Route::get('talent_development/talent_recomendation/get_grade', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_grade']);
	Route::get('talent_development/talent_recomendation/get_region', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_region']);
	Route::get('talent_development/talent_recomendation/get_branch', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_branch']);
	Route::get('talent_development/talent_recomendation/get_source_pos', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_source_pos']);
	Route::get('talent_development/talent_recomendation/get_trigger_pos', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_trigger_pos']);
	Route::get('talent_development/talent_recomendation/get_projected', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_projected']);
	Route::get('talent_development/talent_recomendation/get_fpk', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_fpk']);
	Route::get('talent_development/talent_recomendation/get_survey', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_survey']);
	Route::get('talent_development/talent_recomendation/get_validate', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_validate']);
	Route::get('talent_development/talent_recomendation/list_talent', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'list_talent'])->name('talent_reco.list_talent');
	Route::get('talent_development/talent_recomendation/reco_talent', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'reco_talent']);
	Route::get('talent_development/talent_recomendation/get_talent_edit', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_talent_edit']);
	Route::get('talent_development/talent_recomendation/get_list_batch', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_list_batch'])->name('talent_reco.list_batch');
	Route::get('talent_development/talent_recomendation/get_type_bei', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_type_bei']);
	Route::get('talent_development/talent_recomendation/get_val_psychogram', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_val_psychogram'])->name('talent_reco.get_val_psychogram');
    Route::get('talent_development/talent_recomendation/export_talent', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'exportTalent'])->name('talent_reco.export');
    Route::post('talent_development/talent_recomendation/import_talent', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'importTalent'])->name('talent_reco.import');
    Route::get('talent_development/talent_recomendation/get_employee_kpi', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRecoController::class, 'get_employee_kpi'])->name('talent_reco.get_employee_kpi');
	
	/* Talent Request */
	Route::get('talent_development/assessment_request/', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'index'])->name('ass.index');
	Route::get('talent_development/assessment_request/modal_detail', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'modal_detail'])->name('ass.modal_detail');
	Route::post('talent_development/assessment_request/save', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'save'])->name('ass.save');
	Route::post('talent_development/assessment_request/update', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'update'])->name('ass.update');
	Route::get('talent_development/assessment_request/get_talent_edit', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'get_talent_edit']);
	Route::get('talent_development/assessment_request/get_employee_by', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'get_employee_by']);
	Route::get('talent_development/assessment_request/get_hr_email', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'get_hr_email']);
	Route::get('talent_development/assessment_request/get_hierachy_talent', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'get_hierachy_talent']);
	Route::get('talent_development/assessment_request/get_all_hierachy', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'get_all_hierachy']);
	Route::get('talent_development/assessment_request/get_approval_status', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'get_approval_status']);
	Route::get('talent_development/assessment_request/submit_approve/{id}', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'submit_approve']);
	Route::get('talent_development/assessment_request/cancel/{id}', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentRequestController::class, 'cancel']);

	/* Talent Matrix */
	Route::get('talent_development/talent_ranking_summary/', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'index'])->name('matrix.index');
	Route::get('talent_development/talent_ranking_summary/modal_detail', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'modal_detail'])->name('matrix.modal_detail');
	Route::get('talent_development/talent_ranking_summary/index_modal', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'index_modal'])->name('matrix.index_modal');
	Route::get('talent_development/talent_ranking_summary/get_box', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'get_box']);
	Route::get('talent_development/talent_ranking_summary/filter_type', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'filter_type']);
	Route::get('talent_development/talent_ranking_summary/filter_period', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'filter_period']);
	Route::get('talent_development/talent_ranking_summary/filter_projected', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'filter_projected']);
	Route::get('talent_development/talent_ranking_summary/filter_region', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'filter_region']);
	Route::get('talent_development/talent_ranking_summary/filter_grade', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'filter_grade']);
	Route::get('talent_development/talent_ranking_summary/filter_dept', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'filter_dept']);
	Route::get('talent_development/talent_ranking_summary/filter_principal', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'filter_principal']);
	Route::get('talent_development/talent_ranking_summary/get_reco_name', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'get_reco_name']);
	Route::get('talent_development/talent_ranking_summary/gen_box_talent', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'gen_box_talent']);
	Route::get('talent_development/talent_ranking_summary/modal_talent_profile', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentMatrixController::class, 'modal_talent_profile'])->name('matrix.modal_talent_profile');
	
	/* Talent Reco Progress Summary */
	Route::get('talent_development/talent_progress_review/', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProgressController::class, 'index'])->name('progress.index');
	Route::get('talent_development/talent_progress_review/modal_batch', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProgressController::class, 'modal_batch'])->name('progress.modal_batch');
	Route::get('talent_development/talent_progress_review/modal_bei', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProgressController::class, 'modal_bei'])->name('progress.modal_bei');
	Route::post('talent_development/talent_progress_review/save_batch', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProgressController::class, 'save_batch'])->name('progress.save_batch');
	Route::post('talent_development/talent_progress_review/save_bei', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProgressController::class, 'save_bei'])->name('progress.save_bei');
	
	/* Talent Functional Interview */
    Route::get('talent_development/functional_interview/', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentFunctionalController::class, 'index'])->name('funct_int.index');
	Route::get('talent_development/functional_interview_summary/', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentFunctionalController::class, 'index_summary'])->name('funct_int.index_summary');
    Route::get('talent_development/functional_interview/get_edit_question', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentFunctionalController::class, 'get_edit_question']);
    Route::get('talent_development/functional_interview/get_question', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentFunctionalController::class, 'get_question']);
	Route::post('talent_development/functional_interview/update', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentFunctionalController::class, 'update'])->name('funct_int.update');
	
	/* Talent Profile */
	Route::get('talent_development/talent_profile/', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'index'])->name('talent_profile.index');
	Route::get('talent_development/talent_profile/modal_talent_profile', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'modal_talent_profile'])->name('talent_profile.modal_talent_profile');
	Route::get('talent_development/talent_profile/save', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'save']);
	Route::get('talent_development/talent_profile/update', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'update']);
	Route::post('talent_development/talent_profile/submit', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'submit'])->name('committee.submit');
	Route::get('talent_development/talent_profile/deleted', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'deleted'])->name('committee.deleted');
    Route::get('talent_development/talent_profile/get_editProfile', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_editProfile']);
    Route::get('talent_development/talent_profile/get_cert_profile', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_cert_profile']);
    Route::get('talent_development/talent_profile/get_working', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_working']);
    Route::get('talent_development/talent_profile/get_emp_career_history', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_emp_career_history']);
    Route::get('talent_development/talent_profile/get_pro_position', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_pro_position']);
    Route::get('talent_development/talent_profile/get_history_talent', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_history_talent']);
    Route::get('talent_development/talent_profile/get_last_rating', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_last_rating']);
    Route::get('talent_development/talent_profile/get_old_rating', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_old_rating']);
    Route::get('talent_development/talent_profile/get_training', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_training']);
    Route::get('talent_development/talent_profile/get_kpi_list', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_kpi_list']);
    Route::get('talent_development/talent_profile/get_edu', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_edu']);
    Route::get('talent_development/talent_profile/get_award', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_award']);
    Route::get('talent_development/talent_profile/get_sp', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_sp']);
    Route::get('talent_development/talent_profile/get_employee', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_employee']);
    Route::get('talent_development/talent_profile/get_reco', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_reco']);
    Route::get('talent_development/talent_profile/get_aspiration', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_aspiration']);
    Route::get('talent_development/talent_profile/get_risk', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_risk']);
    Route::get('talent_development/talent_profile/get_plan', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_plan']);
    Route::get('talent_development/talent_profile/get_activity', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_activity']);
    Route::get('talent_development/talent_profile/download', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'download']);
    Route::get('talent_development/talent_profile/get_id', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'get_id']);
    Route::get('talent_development/talent_profile/export_zip', [App\Http\Controllers\TalentManagement\TalentDevelopment\TalentProfileController::class, 'exportZip'])->name('talent_profile.download_zip');;
});

Route::prefix('cash_advance')->middleware('login-verification')->group(function () {
	/* Official Travel Request */
	  Route::get('official_travel/official_travel', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'index'])->name('index.offtrave');
	  Route::get('official_travel/official_travel/get_data', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_data']);
	  Route::get('official_travel/official_travel/get_data_view', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_data_view']);
	  Route::get('official_travel/official_travel/get_approval_by', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_approval_by']);
	  Route::get('official_travel/official_travel/check/maxDateCashAdvance', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'check_max_date_cashadvance']);
	  Route::get('official_travel/official_travel/change-product/type_transport', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'change_type_transport'])->name('change_type_transport');
	  Route::get('official_travel/official_travel/get_region_destination', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_region_destination'])->name('get_region_destination');
	  Route::get('official_travel/official_travel/change-region/cash-advance', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'change_region_cashadvance'])->name('change_region_cashadvance');
	  Route::post('official_travel/official_travel/create-save', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'save'])->name('save.offtrave');
	  Route::get('official_travel/official_travel/getEdit/{id_official_travel}', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_edit']);
	  Route::post('official_travel/official_travel/update-save', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'update_offtrave'])->name('edit.offtrave');
	  Route::get('official_travel/official_travel/destroy/{id_official_travel}', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'delete_offtrave']);
	  Route::get('official_travel/official_travel/action-official-travel', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'action_offtrave']);
	  Route::get('official_travel/official_travel/send-to-mail', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'send_mail']);
	  Route::post('official_travel/official_travel/mail_success', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'mail_success'])->name('mail.mail_success');
	  Route::get('official_travel/official_travel/get_trans_view', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_trans_view'])->name('trans.index');
	  Route::get('official_travel/official_travel/get_accomodation_view', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_accomodation_view'])->name('aco.index');
	  Route::get('official_travel/official_travel/get_cash_view', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'get_cash_view'])->name('cash.index');
      Route::get('official_travel/official_travel/get_project_approval', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'getProjectApproval'])->name('offtrave.get_project_approval');
   
	/* Official Travel Summary */
	Route::get('official_travel/official_travel_summary', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'index_summary'])->name('index_summary.offtrave');

    /* Expense Product */
    Route::get('cash_advance_settings/budget_product_expense', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'index'])->name('expense_product.index');
    Route::get('cash_advance_settings/budget_product_expense/modal_detail', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'modal_detail'])->name('expense_product.modal_detail');
    Route::get('cash_advance_settings/budget_product_expense/initial_field', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'get_initial_field'])->name('expense_product.initial');
    Route::get('cash_advance_settings/budget_product_expense/filled_field', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'get_filled_field'])->name('expense_product.filled');
    Route::get('cash_advance_settings/budget_product_expense/get_branch', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'get_branch'])->name('expense_product.branch');
    Route::post('cash_advance_settings/budget_product_expense', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'save'])->name('expense_product.save');
    Route::post('cash_advance_settings/budget_product_expense/update', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'update'])->name('expense_product.update');
    Route::get('cash_advance_settings/budget_product_expense/destroy/{id_grade_expense}', [App\Http\Controllers\CashAdvance\ExpenseProduct\ExpenseProductController::class, 'delete'])->name('expense_product.delete');

    /* Master Product */
    Route::get('cash_advance_settings/expense_product', [App\Http\Controllers\CashAdvance\MasterProduct\MasterProductController::class, 'index'])->name('master_product.index');
    Route::get('cash_advance_settings/expense_product/get_categories', [App\Http\Controllers\CashAdvance\MasterProduct\MasterProductController::class, 'getProductCategories'])->name('master_product.get_categories');
    Route::get('cash_advance_settings/expense_product/get_uom', [App\Http\Controllers\CashAdvance\MasterProduct\MasterProductController::class, 'getUnitOfMeasures'])->name('master_product.get_uom');
    Route::post('cash_advance_settings/expense_product/save', [App\Http\Controllers\CashAdvance\MasterProduct\MasterProductController::class, 'save'])->name('master_product.save');
    Route::post('cash_advance_settings/expense_product/delete', [App\Http\Controllers\CashAdvance\MasterProduct\MasterProductController::class, 'delete'])->name('master_product.delete');

    
    Route::get('cash_advance_settings/product_category', [App\Http\Controllers\CashAdvance\ProductCategory\ProductCategoryController::class, 'index'])->name('product_categories.index');
    Route::get('cash_advance_settings/product_category/get_master_chart_account', [App\Http\Controllers\CashAdvance\ProductCategory\ProductCategoryController::class, 'getMasterChartAccount'])->name('product_categories.get_mca');
    Route::post('cash_advance_settings/product_category/save', [App\Http\Controllers\CashAdvance\ProductCategory\ProductCategoryController::class, 'save'])->name('product_categories.save');

    Route::get('cash_advance_settings/bank_account', [App\Http\Controllers\CashAdvance\BankAccount\BankAccountController::class, 'index'])->name('master_bank_account');
    Route::get('cash_advance_settings/bank_account/get_data', [App\Http\Controllers\CashAdvance\BankAccount\BankAccountController::class, 'getData'])->name('master_bank_account.get_data');
    Route::get('cash_advance_settings/bank_account/get_edit', [App\Http\Controllers\CashAdvance\BankAccount\BankAccountController::class, 'getEdit'])->name('master_bank_account.get_edit');
    Route::post('cash_advance_settings/bank_account/save', [App\Http\Controllers\CashAdvance\BankAccount\BankAccountController::class, 'save'])->name('master_bank_account.save');

    
    Route::get('cash_advance/expense_request', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'index'])->name('cash_advance');
    Route::get('cash_advance/expense_request/get_form_data', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'getFormData'])->name('cash_advance.get_form_data');
    Route::get('cash_advance/expense_request/get_data', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'getData'])->name('cash_advance.get_data');
    Route::post('cash_advance/expense_request/update', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'update'])->name('cash_advance.update');

    /* Settlement Approval */
    Route::get('cash_advance/settlement_approval_data', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'getApprovalData'])->name('settlementtravel.approval.data');
    Route::post('cash_advance/settlement_approval/approval', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'chiefSettlementApproval'])->name("settlementtravel.approval.chief-action");
    Route::get('cash_advance/settlement_approval', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'settlementApproval'])->name('settlementtravel.approval.chief');
    Route::get('cash_advance/expense_payment_finance', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'settlementFinanceCashAdvancePayment'])->name('settlementtravel.payment.finance');
    Route::get('cash_advance/settlement_clearing', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'settlementFinanceApproval'])->name('settlementtravel.approval.finance');
    Route::get('cash_advance/expense_payment_hr', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'settlementHrPayment'])->name('settlementtravel.payment.hr');
    Route::post('cash_advance/payment/save', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'saveFinancePayment'])->name('settlementtravel.payment.update');
    Route::post('cash_advance/payment_hr/save', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'saveHrPayment'])->name('settlementtravel.paymenthr.update');
    Route::post('cash_advance/transport_accommodation_hr/save', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'saveHrTransportAccommodation'])->name('settlementtravel.transportaccommodation.save');
    Route::post('cash_advance/settlement_approval/finance_approval', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'financeSettlementApproval'])->name('settlementtravel.approval.finance-action');
    Route::post('cash_advance/refund_approval/approval', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'financeRefundApproval'])->name('settlementtravel.approval.refund.finance-action');
    Route::get('cash_advance/settlement/get_branch', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'getBranch'])->name('settlementtravel.get_branch');
    Route::get('cash_advance/settlement/unpaid', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'unpaidSettlement'])->name('settlementtravel.unpaid');
    Route::post('cash_advance/settlement/apply-payment', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'applyPayment'])->name('settlementtravel.apply-payment');
    Route::get('cash_advance/settlement/notification', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'settlementNotification'])->name('settlementtravel.notification');
    Route::post('cash_advance/settlement/send-billing-letter', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'sendBillingLetter'])->name('settlementtravel.send_bill');
    Route::post('cash_advance/settlement_approval/manual-clearing', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'financeManualClearing'])->name('settlementtravel.manual_clearing');
    Route::get('cash_advance/settlement_clearing_by_fas', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'branchFinanceApprovalData'])->name('settlementtravel.approval.fas');
    Route::get('cash_advance/settlement/get_product_variant', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'getTransportVariantData'])->name('settlementtravel.get-product-variant');
    Route::get('cash_advance/settlement/get_settlements', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'getSettlements'])->name('settlementtravel.get-settlements');
    Route::get('cash_advance/expense_request', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'index'])->name('cash_advance');
    Route::post('cash_advance/expense_request/update', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'update'])->name('cash_advance.update');
    Route::get('cash_advance/expense_request/get-data', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'getData'])->name('cash_advance.get_data');
    Route::get('cash_advance/expense_request/get-form-data', [App\Http\Controllers\CashAdvance\CashAdvance\CashAdvanceController::class, 'getFormData'])->name('cash_advance.get_form_data');
});
Route::get('cash_advance/settlement/print', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'print'])->name('settlementtravel.print');
Route::get('cash_advance/settlement/print_bill', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'billingLetter'])->name('settlementtravel.print_bill');
Route::get('cash_advance/expense_request/bgen/export/excel', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'export_excel'])->name('export_excel');

 // Export Travel PDF
Route::prefix('official_travel')->middleware('login-verification')->group(function () {
	Route::get('official_travel/print-official-travel/{id_official_travel}', [App\Http\Controllers\CashAdvance\OfficialTravel\OfficialTravelController::class, 'print_offtrave'])->name('print_offtrave');
	
	/* Travel Transport & accomodation HR */
	  Route::get('travels/transport_and_accommodation', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'index'])->name('index.travreq');
	  Route::get('travels/transport_and_accommodation/detail_view/{id_official_travel}', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'detail_view']);
	  Route::post('travels/transport_and_accommodation/update-save', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'edit'])->name('edit.settravel');
	  Route::get('travels/transport_and_accommodation/view/{id_official_travel}', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'get_view']);
	  Route::get('travels/transport_and_accommodation/lock-submit/{id_official_travel}', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'lock_submit']);
	
	// Travel History HR
	Route::get('travels/official_travel_history', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'index_bigen'])->name('index_bigen');
    Route::post('travels/official_travel_history/price', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'edit_expense_data_price'])->name('travel_history_expense_data.price');
    Route::get('travels/official_travel_history/expense_data', [App\Http\Controllers\CashAdvance\TravelRequest\TravelRequestController::class, 'get_expense_data'])->name('travel_history_expense_data');

    // Settlement Travel
    Route::get('travels/settlement_travels', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'index'])->name('settlementtravel.index');
    Route::get('travels/settlement_travel/get-data', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'getData'])->name('settlementtravel.get_data');
    Route::post('travels/settlement_travel/update', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'update'])->name('settlementtravel.update');
    Route::get('travels/settlement/official-travel-no-cash-advance-list', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'getOfficialTravelWithoutCashAdvance'])->name('settlementtravel.offtrav_nocashadvance');
    Route::post('travels/settlement/add-cash-advance', [App\Http\Controllers\CashAdvance\SettlementTravel\SettlementTravelController::class, 'generateCashAdvance'])->name('settlementtravel.add_cash_advance');
});    

Route::prefix('integration')->middleware('login-verification')->group(function () {
	Route::get('bgen/sales_code_bgen', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'index'])->name('bgen.index');
	Route::get('bgen/sales_code_bgen/modal_detail', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'modal_detail'])->name('bgen.modal_detail');
	Route::post('bgen/sales_code_bgen/save', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'save'])->name('bgen.save');
	Route::post('bgen/sales_code_bgen/update', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'update'])->name('bgen.update');
	Route::get('bgen/sales_code_bgen/get_employee', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_employee']);
    Route::get('bgen/sales_code_bgen/get_department', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_department'])->name('bgen.get_dept');
	Route::get('bgen/sales_code_bgen/get_status', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_status']);
	Route::get('bgen/sales_code_bgen/get_category', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_category']);
	Route::get('bgen/sales_code_bgen/get_branch', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_branch']);
	Route::get('bgen/sales_code_bgen/get_principal', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_principal']);
	Route::get('bgen/sales_code_bgen/get_edit', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_edit']);
	Route::get('bgen/sales_code_bgen/get_approval_by', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_approval_by']);
	Route::get('bgen/sales_code_bgen/get_direct_spv', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'get_direct_spv']);
	Route::get('bgen/sales_code_bgen/cancel/{id}', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'cancel']);
    Route::post('bgen/sales_code_bgen/sync', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'sync'])->name('bgen.sync');
    Route::get('bgen/sales_code_bgen/export_validate', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'export_validate'])->name('bgen.validate');
    Route::get('bgen/sales_code_bgen/export', [App\Http\Controllers\Integration\Bgen\BgenController::class, 'export'])->name('bgen.export');

    Route::get('bgen/approval_bisnis_bgen', [App\Http\Controllers\Integration\Bgen\OasysController::class, 'index'])->name('oasys.index');
    Route::get('bgen/approval_bisnis_bgen/modal_detail', [App\Http\Controllers\Integration\Bgen\OasysController::class, 'modal_detail'])->name('oasys.modal_detail');
    Route::get('bgen/approval_bisnis_bgen/get_edit', [App\Http\Controllers\Integration\Bgen\OasysController::class, 'get_edit'])->name('oasys.get_edit');
    Route::get('bgen/approval_bisnis_bgen/get_approval_by', [App\Http\Controllers\Integration\Bgen\OasysController::class, 'get_approval_by'])->name('oasys.get_approval_by');
    Route::post('bgen/approval_bisnis_bgen/save', [App\Http\Controllers\Integration\Bgen\OasysController::class, 'save'])->name('oasys.save');
	Route::post('bgen/approval_bisnis_bgen/update', [App\Http\Controllers\Integration\Bgen\OasysController::class, 'update'])->name('oasys.update');
    Route::post('bgen/approval_bisnis_bgen/sync', [App\Http\Controllers\Integration\Bgen\OasysController::class, 'sync'])->name('oasys.sync');
});

// ------------------ ASSETS ------------------- //

Route::prefix('dashboard')->middleware('login-verification')->group(function () {
    Route::get('dashboard_asset/dashboard_asset', [App\Http\Controllers\Assets\Dashboard\Asset\AssetDashboardController::class, 'index'])->name('assets.dashboard.asset');
    Route::get('dashboard_asset/dashboard_asset/get_stats', [App\Http\Controllers\Assets\Dashboard\Asset\AssetDashboardController::class, 'getStats'])->name('assets.dashboard.asset.get_stats');
    Route::get('dashboard_financial/dashboard_financial', [App\Http\Controllers\Assets\Dashboard\Financial\FinancialDashboardController::class, 'index'])->name('assets.dashboard.financial');
    Route::get('dashboard_financial/dashboard_financial/get_stats', [App\Http\Controllers\Assets\Dashboard\Financial\FinancialDashboardController::class, 'getStats'])->name('assets.dashboard.financial.get_stats');
});

Route::prefix('depreciation_asset')->middleware('login-verification')->group(function () {
    Route::get('depreciation_settings/depreciation_method', [App\Http\Controllers\Assets\DepreciationSettings\DepreciationMethodController::class, 'index'])->name('assets.master_depreciation_method');
    Route::get('depreciation_settings/depreciation_method/get_edit', [App\Http\Controllers\Assets\DepreciationSettings\DepreciationMethodController::class, 'getEdit'])->name('assets.master_depreciation_method.get_edit');
    Route::post('depreciation_settings/depreciation_method/save', [App\Http\Controllers\Assets\DepreciationSettings\DepreciationMethodController::class, 'save'])->name('assets.master_depreciation_method.save');

    Route::get('depreciation_transaction/mass_depreciations', [App\Http\Controllers\Assets\DepreciationTransaction\MassDepreciationController::class, 'index'])->name('assets.mass_depreciation');
    Route::get('depreciation_transaction/mass_depreciations/get_data', [App\Http\Controllers\Assets\DepreciationTransaction\MassDepreciationController::class, 'getData'])->name('assets.mass_depreciation.get_data');
    Route::post('depreciation_transaction/mass_depreciations/generate', [App\Http\Controllers\Assets\DepreciationTransaction\MassDepreciationController::class, 'generate'])->name('assets.mass_depreciation.generate');

    Route::get('depreciation_transaction/depreciation_history', [App\Http\Controllers\Assets\DepreciationTransaction\DepreciationHistoryController::class, 'index'])->name('assets.depreciation_history');
    Route::get('depreciation_transaction/depreciation_history/get_data', [App\Http\Controllers\Assets\DepreciationTransaction\DepreciationHistoryController::class, 'getData'])->name('assets.depreciation_history.get_data');
    Route::get('depreciation_transaction/depreciation_history/get_edit', [App\Http\Controllers\Assets\DepreciationTransaction\DepreciationHistoryController::class, 'getEdit'])->name('assets.depreciation_history.get_edit');
});

Route::prefix('transfer_asset')->middleware('login-verification')->group(function() {
    Route::get('transfer_transaction/transfer_request', [App\Http\Controllers\Assets\TransferSettings\AssetTransferController::class, 'index'])->name('assets.transfer');
    Route::get('transfer_transaction/transfer_request/get_data', [App\Http\Controllers\Assets\TransferSettings\AssetTransferController::class, 'getData'])->name('assets.transfer.get_data');
    Route::get('transfer_transaction/transfer_request/get_edit', [App\Http\Controllers\Assets\TransferSettings\AssetTransferController::class, 'getEdit'])->name('assets.transfer.get_edit');
    Route::post('transfer_transaction/transfer_request/save', [App\Http\Controllers\Assets\TransferSettings\AssetTransferController::class, 'save'])->name('assets.transfer.save');
    Route::post('transfer_transaction/transfer_request/generate_journal', [App\Http\Controllers\Assets\TransferSettings\AssetTransferController::class, 'generateJournalTransfer'])->name('assets.transfer.generate_journal');

    Route::get('transfer_transaction/transfer_request_summary', [App\Http\Controllers\Assets\TransferSettings\AssetTransferController::class, 'summaryIndex'])->name('assets.transfer_summary');

    Route::get('transfer_transaction/transfer_approval', [App\Http\Controllers\Assets\TransferSettings\AssetTransferApprovalController::class, 'index'])->name('assets.transfer_approval');
    Route::get('transfer_transaction/transfer_approval/get_modal', [App\Http\Controllers\Assets\TransferSettings\AssetTransferApprovalController::class, 'getModal'])->name('assets.transfer_approval.get_modal');
    Route::post('transfer_transaction/transfer_approval/approve', [App\Http\Controllers\Assets\TransferSettings\AssetTransferApprovalController::class, 'approve'])->name('assets.transfer_approval.approve');
    Route::post('transfer_transaction/transfer_approval/reject', [App\Http\Controllers\Assets\TransferSettings\AssetTransferApprovalController::class, 'reject'])->name('assets.transfer_approval.reject');

    Route::get('transfer_transaction/transfer_approval_history', [App\Http\Controllers\Assets\TransferSettings\AssetTransferApprovalHistoryController::class, 'index'])->name('assets.transfer_approval_history');

    Route::get('transfer_transaction/receiving_transfer_asset', [App\Http\Controllers\Assets\TransferSettings\ReceiveTransferAssetController::class, 'index'])->name('assets.transfer_receive');
    Route::post('transfer_transaction/receiving_transfer_asset/approve', [App\Http\Controllers\Assets\TransferSettings\ReceiveTransferAssetController::class, 'receive'])->name('assets.transfer_receive.receive');
    Route::post('transfer_transaction/receiving_transfer_asset/reject', [App\Http\Controllers\Assets\TransferSettings\ReceiveTransferAssetController::class, 'refuse'])->name('assets.transfer_receive.refuse');
    Route::get('transfer_transaction/receiving_transfer_asset/print', [App\Http\Controllers\Assets\TransferSettings\ReceiveTransferAssetController::class, 'print'])->name('assets.transfer_receive.print');

    Route::get('transfer_settings/master_asset_location', [App\Http\Controllers\Assets\TransferSettings\MasterAssetLocationController::class, 'index'])->name('assets.master_asset_location');
    Route::get('transfer_settings/master_asset_location/get_edit', [App\Http\Controllers\Assets\TransferSettings\MasterAssetLocationController::class, 'getEdit'])->name('assets.master_asset_location.get_edit');
    Route::get('transfer_settings/master_asset_location/get_locations', [App\Http\Controllers\Assets\TransferSettings\MasterAssetLocationController::class, 'getLocations'])->name('assets.master_asset_location.get_locations');
    Route::post('transfer_settings/master_asset_location/save', [App\Http\Controllers\Assets\TransferSettings\MasterAssetLocationController::class, 'save'])->name('assets.master_asset_location.save');

    Route::get('transfer_transaction/master_approval', [App\Http\Controllers\Assets\TransferSettings\MasterApprovalController::class, 'index'])->name('assets.master_approval');
    Route::get('transfer_transaction/master_approval/get_data', [App\Http\Controllers\Assets\TransferSettings\MasterApprovalController::class, 'getData'])->name('assets.master_approval.get_data');
    Route::get('transfer_transaction/master_approval/get_edit', [App\Http\Controllers\Assets\TransferSettings\MasterApprovalController::class, 'getEdit'])->name('assets.master_approval.get_edit');
    Route::post('transfer_transaction/master_approval/save', [App\Http\Controllers\Assets\TransferSettings\MasterApprovalController::class, 'save'])->name('assets.master_approval.save');
});

Route::prefix('addition_asset')->middleware('login-verification')->group(function() {
    Route::get('asset_settings/asset_categories', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetCategoryController::class, 'index'])->name('assets.master_asset_category');
    Route::get('asset_settings/asset_categories/get_accounts', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetCategoryController::class, 'getAccounts'])->name('assets.master_asset_category.get_accounts');
    Route::get('asset_settings/asset_categories/get_edit', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetCategoryController::class, 'getEdit'])->name('assets.master_asset_category.get_edit');
    Route::post('asset_settings/asset_categories/save', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetCategoryController::class, 'save'])->name('assets.master_asset_category.save');

    Route::get('asset_settings/asset_group', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetGroupController::class, 'index'])->name('assets.master_asset_group');
    Route::get('asset_settings/asset_group/get_data', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetGroupController::class, 'getData'])->name('assets.master_asset_group.get_data');
    Route::get('asset_settings/asset_group/get_edit', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetGroupController::class, 'getEdit'])->name('assets.master_asset_group.get_edit');
    Route::post('asset_settings/asset_group/save', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetGroupController::class, 'save'])->name('assets.master_asset_group.save');

    Route::get('asset_settings/master_period', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetPeriodController::class, 'index'])->name('assets.master_period');
    Route::get('asset_settings/master_period/get_edit', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetPeriodController::class, 'getEdit'])->name('assets.master_period.get_edit');
    Route::post('asset_settings/master_period/save', [App\Http\Controllers\Assets\AdditionAsset\MasterAssetPeriodController::class, 'save'])->name('assets.master_period.save');

    Route::get('mass_additions/mass_additions', [App\Http\Controllers\Assets\AdditionAsset\MassAdditionController::class, 'index'])->name('assets.mass_addition');
    Route::get('mass_additions/mass_additions/get_data', [App\Http\Controllers\Assets\AdditionAsset\MassAdditionController::class, 'getData'])->name('assets.mass_addition.get_data');
    Route::get('mass_additions/mass_additions/get_edit', [App\Http\Controllers\Assets\AdditionAsset\MassAdditionController::class, 'getEdit'])->name('assets.mass_addition.get_edit');
    // Route::post('mass_additions/mass_additions/save', [App\Http\Controllers\Assets\AdditionAsset\MassAdditionController::class, 'save'])->name('assets.mass_addition.save');

    Route::get('quick_additions/addition_asset', [App\Http\Controllers\Assets\AdditionAsset\QuickAdditionController::class, 'index'])->name('assets.quick_addition');
    Route::get('quick_additions/addition_asset/get_data', [App\Http\Controllers\Assets\AdditionAsset\QuickAdditionController::class, 'getData'])->name('assets.quick_addition.get_data');
    Route::get('quick_additions/addition_asset/get_edit', [App\Http\Controllers\Assets\AdditionAsset\QuickAdditionController::class, 'getEdit'])->name('assets.quick_addition.get_edit');
    Route::post('quick_additions/addition_asset/save', [App\Http\Controllers\Assets\AdditionAsset\QuickAdditionController::class, 'save'])->name('assets.quick_addition.save');
    Route::post('quick_additions/addition_asset/post', [App\Http\Controllers\Assets\AdditionAsset\QuickAdditionController::class, 'post'])->name('assets.quick_addition.post');
    Route::post('quick_additions/addition_asset/import', [App\Http\Controllers\Assets\AdditionAsset\QuickAdditionController::class, 'batchAdd'])->name('assets.quick_addition.import');

    Route::get('quick_additions/asset_history', [App\Http\Controllers\Assets\AdditionAsset\AssetHistoryController::class, 'index'])->name('assets.asset_history');
    Route::get('quick_additions/asset_history/get_edit', [App\Http\Controllers\Assets\AdditionAsset\AssetHistoryController::class, 'getEdit'])->name('assets.asset_history.get_edit');
    Route::get('quick_additions/asset_history/get_data', [App\Http\Controllers\Assets\AdditionAsset\AssetHistoryController::class, 'getData'])->name('assets.asset_history.get_data');
    Route::get('quick_additions/asset_history/assigned_employee', [App\Http\Controllers\Assets\AdditionAsset\AssetHistoryController::class, 'assetAssigneeHistory'])->name('assets.asset_history.assigned_employee');
    Route::get('quick_additions/asset_history/print_label', [App\Http\Controllers\Assets\AdditionAsset\AssetHistoryController::class, 'printLabel'])->name('assets.asset_history.print_label');

    Route::get('quick_additions/view_asset', [App\Http\Controllers\Assets\AdditionAsset\AssetHistoryController::class, 'viewAsset'])->name('assets.view_assets');
});

Route::prefix('retirement_disposal')->middleware('login-verification')->group(function() {
    Route::get('retirement/retirement_request', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementController::class, 'index'])->name('assets.retirement');
    Route::get('retirement/retirement_request/get_edit', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementController::class, 'getEdit'])->name('assets.retirement.get_edit');
    Route::get('retirement/retirement_request/get_data', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementController::class, 'getData'])->name('assets.retirement.get_data');
    Route::post('retirement/retirement_request/save', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementController::class, 'save'])->name('assets.retirement.save');
    Route::post('retirement/retirement_request/generate_journal', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementController::class, 'generateJournalTransfer'])->name('assets.retirement.generate_journal');

    Route::get('retirement/retirement_approval', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementApprovalController::class, 'index'])->name('assets.retirement_approval');
    Route::post('retirement/retirement_approval/approve', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementApprovalController::class, 'approve'])->name('assets.retirement_approval.approve');
    Route::post('retirement/retirement_approval/reject', [App\Http\Controllers\Assets\RetirementDisposal\AssetRetirementApprovalController::class, 'reject'])->name('assets.retirement_approval.reject');

    Route::get('reinstate/reinstate_request', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementController::class, 'index'])->name('assets.reinstatement');
    Route::get('reinstate/reinstate_request/get_data', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementController::class, 'getData'])->name('assets.reinstatement.get_data');
    Route::get('reinstate/reinstate_request/get_edit', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementController::class, 'getEdit'])->name('assets.reinstatement.get_edit');
    Route::post('reinstate/reinstate_request/save', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementController::class, 'save'])->name('assets.reinstatement.save');
    Route::post('reinstate/reinstate_request/generate_journal', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementController::class, 'generateJournalTransfer'])->name('assets.reinstatement.generate_journal');

    Route::get('reinstate/reinstate_approval', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementApprovalController::class, 'index'])->name('assets.reinstatement_approval');
    Route::post('reinstate/reinstate_approval/approve', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementApprovalController::class, 'approve'])->name('assets.reinstatement_approval.approve');
    Route::post('reinstate/reinstate_approval/reject', [App\Http\Controllers\Assets\RetirementDisposal\AssetReinstatementApprovalController::class, 'reject'])->name('assets.reinstatement_approval.reject');
});

Route::prefix('financial_asset')->middleware('login-verification')->group(function() {
    Route::get('adjustments/adjustment_request', [App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentController::class, 'index'])->name('assets.adjustment');
    Route::get('adjustments/adjustment_request/get_data', [App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentController::class, 'getData'])->name('assets.adjustment.get_data');
    Route::get('adjustments/adjustment_request/get_edit', [App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentController::class, 'getEdit'])->name('assets.adjustment.get_edit');
    Route::post('adjustments/adjustment_request/save', [App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentController::class, 'save'])->name('assets.adjustment.save');

    Route::get('adjustments/adjustment_approval', [App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentApprovalController::class, 'index'])->name('assets.adjustment_approval');
    Route::post('adjustments/adjustment_approval/approve', [App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentApprovalController::class, 'approve'])->name('assets.adjustment_approval.approve');
    Route::post('adjustments/adjustment_approval/reject', [App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentApprovalController::class, 'reject'])->name('assets.adjustment_approval.reject');

    Route::get('revaluations/revaluation_request', [App\Http\Controllers\Assets\FinancialAsset\AssetRevaluationController::class, 'index'])->name('assets.revaluations');
    Route::get('revaluations/revaluation_request/get_data', [App\Http\Controllers\Assets\FinancialAsset\AssetRevaluationController::class, 'getData'])->name('assets.revaluations.get_data');
    Route::get('revaluations/revaluation_request/get_edit', [App\Http\Controllers\Assets\FinancialAsset\AssetRevaluationController::class, 'getEdit'])->name('assets.revaluations.get_edit');
    Route::post('revaluations/revaluation_request/save', [App\Http\Controllers\Assets\FinancialAsset\AssetRevaluationController::class, 'save'])->name('assets.revaluations.save');

    Route::get('revaluations/revaluation_approval', [App\Http\Controllers\Assets\FinancialAsset\AssetRevaluationApprovalController::class, 'index'])->name('assets.revaluation_approval');
    Route::post('revaluations/revaluation_approval/approve', [App\Http\Controllers\Assets\FinancialAsset\AssetRevaluationApprovalController::class, 'approve'])->name('assets.revaluation_approval.approve');
    Route::post('revaluations/revaluation_approval/reject', [App\Http\Controllers\Assets\FinancialAsset\AssetRevaluationApprovalController::class, 'reject'])->name('assets.revaluation_approval.reject');

    Route::get('impairments/impairment_request', [App\Http\Controllers\Assets\FinancialAsset\AssetImpairmentController::class, 'index'])->name('assets.impairments');
    Route::get('impairments/impairment_request/get_data', [App\Http\Controllers\Assets\FinancialAsset\AssetImpairmentController::class, 'getData'])->name('assets.impairments.get_data');
    Route::get('impairments/impairment_request/get_edit', [App\Http\Controllers\Assets\FinancialAsset\AssetImpairmentController::class, 'getEdit'])->name('assets.impairments.get_edit');
    Route::post('impairments/impairment_request/save', [App\Http\Controllers\Assets\FinancialAsset\AssetImpairmentController::class, 'save'])->name('assets.impairments.save');

    Route::get('impairments/impairment_approval', [App\Http\Controllers\Assets\FinancialAsset\AssetImpairmentApprovalController::class, 'index'])->name('assets.impairment_approval');
    Route::post('impairments/impairment_approval/approve', [App\Http\Controllers\Assets\FinancialAsset\AssetImpairmentApprovalController::class, 'approve'])->name('assets.impairment_approval.approve');
    Route::post('impairments/impairment_approval/reject', [App\Http\Controllers\Assets\FinancialAsset\AssetImpairmentApprovalController::class, 'reject'])->name('assets.impairment_approval.reject');
});

Route::prefix('general_setting')->middleware('login-verification')->group(function() {
    Route::get('fa_config_setting/asset_config_settings', [App\Http\Controllers\Assets\ConfigSettings\AssetConfigSettingsController::class, 'index'])->name('assets.config_settings');
    Route::get('fa_config_setting/asset_config_settings/get_data', [App\Http\Controllers\Assets\ConfigSettings\AssetConfigSettingsController::class, 'getData'])->name('assets.config_settings.get_data');
    Route::get('fa_config_setting/asset_config_settings/get_edit', [App\Http\Controllers\Assets\ConfigSettings\AssetConfigSettingsController::class, 'getEdit'])->name('assets.config_settings.get_edit');
    Route::post('fa_config_setting/asset_config_settings/save', [App\Http\Controllers\Assets\ConfigSettings\AssetConfigSettingsController::class, 'save'])->name('assets.config_settings.save');
});

Route::prefix('general_ledger')->middleware('login-verification')->group(function() {
    Route::get('journal_entries/journal_entry', [App\Http\Controllers\Accounting\GeneralLedger\JournalEntryController::class, 'index'])->name('accounting.gl_je');
    Route::get('journal_entries/journal_entry/get_data', [App\Http\Controllers\Accounting\GeneralLedger\JournalEntryController::class, 'getData'])->name('accounting.gl_je.get_data');
    Route::get('journal_entries/journal_entry/get_edit', [App\Http\Controllers\Accounting\GeneralLedger\JournalEntryController::class, 'getEdit'])->name('accounting.gl_je.get_edit');
    Route::post('journal_entries/journal_entry/post', [App\Http\Controllers\Accounting\GeneralLedger\JournalEntryController::class, 'post'])->name('accounting.gl_je.post');
    Route::post('journal_entries/journal_entry/cancel', [App\Http\Controllers\Accounting\GeneralLedger\JournalEntryController::class, 'cancel'])->name('accounting.gl_je.cancel');
    Route::post('journal_entries/journal_entry/update', [App\Http\Controllers\Accounting\GeneralLedger\JournalEntryController::class, 'update'])->name('accounting.gl_je.update');

    Route::get('gl_settings/chart_of_account', [App\Http\Controllers\Accounting\GeneralLedger\ChartOfAccountController::class, 'index'])->name('accounting.chart_of_account');
    Route::get('gl_settings/chart_of_account/get_data', [App\Http\Controllers\Accounting\GeneralLedger\ChartOfAccountController::class, 'getData'])->name('accounting.chart_of_account.get_data');
    Route::get('gl_settings/chart_of_account/get_edit', [App\Http\Controllers\Accounting\GeneralLedger\ChartOfAccountController::class, 'getEdit'])->name('accounting.chart_of_account.get_edit');
    Route::post('gl_settings/chart_of_account/save', [App\Http\Controllers\Accounting\GeneralLedger\ChartOfAccountController::class, 'save'])->name('accounting.chart_of_account.save');
});

Route::prefix('task_management')->middleware('login-verification')->group(function() {
	/* Master Task */
    Route::get('missions/master_task', [App\Http\Controllers\Task\MasterTask\MasterTaskController::class, 'index'])->name('master_task.index');
	Route::get('missions/master_task/modal_detail', [App\Http\Controllers\Task\MasterTask\MasterTaskController::class, 'modal_detail'])->name('master_task.modal_detail');
	Route::get('missions/master_task/get_dept', [App\Http\Controllers\Task\MasterTask\MasterTaskController::class, 'get_dept']);
	Route::get('missions/master_task/get_grade', [App\Http\Controllers\Task\MasterTask\MasterTaskController::class, 'get_grade']);
	Route::get('missions/master_task/get_edit', [App\Http\Controllers\Task\MasterTask\MasterTaskController::class, 'get_edit']);
	Route::post('missions/master_task/save', [App\Http\Controllers\Task\MasterTask\MasterTaskController::class, 'save'])->name('master_task.save');
	Route::post('missions/master_task/update', [App\Http\Controllers\Task\MasterTask\MasterTaskController::class, 'update'])->name('master_task.update');
	
	/* Task Assignment */
    Route::get('missions/task_assignment', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'index'])->name('task.index');
	Route::get('missions/task_assignment/modal_detail', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'modal_detail'])->name('task.modal_detail');
	Route::get('missions/task_assignment/modal_list', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'modal_list'])->name('task.modal_list');
	Route::get('missions/task_assignment/list_task', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'list_task'])->name('task.list_task');
	Route::get('missions/task_assignment/task_assign', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'task_assign']);
	Route::get('missions/task_assignment/get_dept', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_dept']);
	Route::get('missions/task_assignment/get_grade', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_grade']);
	Route::get('missions/task_assignment/get_position', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_position']);
	Route::get('missions/task_assignment/get_respon', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_respon']);
	Route::get('missions/task_assignment/get_employee_by', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_employee_by']);
//	Route::get('missions/task_assignment/get_task', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_task']);
//	Route::get('missions/task_assignment/get_activity', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_activity']);
//	Route::get('missions/task_assignment/get_detail_act', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_detail_act']);
	Route::get('missions/task_assignment/get_validate', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_validate']);
	Route::get('missions/task_assignment/get_edit', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'get_edit']);
	Route::get('missions/task_assignment/gen_task', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'gen_task']);
	Route::post('missions/task_assignment/save', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'save'])->name('task.save');
	Route::post('missions/task_assignment/update', [App\Http\Controllers\Task\TaskAssignment\TaskAssignmentController::class, 'update'])->name('task.update');
	
	/* Missions */
    Route::get('missions/missions', [App\Http\Controllers\Task\Missions\MissionsController::class, 'index']);
    Route::get('missions/missions/get_list', [App\Http\Controllers\Task\Missions\MissionsController::class, 'get_list'])->name('missions.get_list');
    Route::get('missions/missions/modal_upload', [App\Http\Controllers\Task\Missions\MissionsController::class, 'modal_upload'])->name('missions.modal_upload');
    Route::get('missions/missions/submit_check', [App\Http\Controllers\Task\Missions\MissionsController::class, 'submit_check'])->name('missions.submit_check');
	Route::post('missions/missions/save', [App\Http\Controllers\Task\Missions\MissionsController::class, 'save'])->name('missions.save');
	
	/* Missions Review */
    Route::get('missions/missions_review', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'index'])->name('missions_review.index');
	Route::get('missions/missions_review/modal_upload', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'modal_upload'])->name('missions_review.modal_upload');
	Route::get('missions/missions_review/get_score', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'get_score']);
	Route::get('missions/missions_review/get_photo', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'get_photo']);
	Route::get('missions/missions_review/get_essay', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'get_essay']);
	Route::get('missions/missions_review/get_doc', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'get_doc']);
	Route::get('missions/missions_review/get_search_emp', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'get_search_emp']);
	Route::post('missions/missions_review/save', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'save'])->name('missions_review.save');
	
	/* Leader Board*/
    Route::get('missions/leaderboard', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'getLeaderboard'])->name('leaderboard.index');
    Route::get('missions/leaderboard/get_month', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'get_month']);
	
	/* Task Summary*/
    Route::get('missions/task_summary', [App\Http\Controllers\Task\MissionsReview\MissionsReviewController::class, 'getSummary'])->name('task_summary.index');
	
	/* My Score*/
    Route::get('missions/my_score', [App\Http\Controllers\Task\MyScore\MyScoreController::class, 'index'])->name('myscore.index');
    Route::get('missions/my_score/get_task', [App\Http\Controllers\Task\MyScore\MyScoreController::class, 'get_task']);
    Route::get('missions/my_score/get_tot_score', [App\Http\Controllers\Task\MyScore\MyScoreController::class, 'get_tot_score']);
});

Route::prefix('api/v1')->group(function () { //for mobile MYBORWITA
	Route::post('missions/missions/save', [App\Http\Controllers\Task\Missions\MissionsController::class, 'save']);
	Route::post('missions/missions/submit_check', [App\Http\Controllers\Task\Missions\MissionsController::class, 'submit_check']);
});