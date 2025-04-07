<?php

namespace App\Models;

use App\Models\Employee\EmployeeApproval\EmployeeApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Login extends Model {
	
	public static function checkLogin($data, $user) {
        $username   = $data['username'];
        $password   = $data['password'];
        $id_company = $data['id_company'];
        $status     = false;
        $attribute  = '';
        $message    = '';
        $data       = [];

        if(is_null($user)){
            $attribute  = 'username';
            $message    = 'User Name not found';
        } else {
            if($user->status == 'A'){
                $checkLogin = DB::select("select * from CheckLogin ('" . $username . "' ," . $id_company . ")");
                if(count($checkLogin) > 0){
                    $login = $checkLogin[0];
                    if ($login->status == 1) {
                        if (Hash::check($password, $login->password)) {
                            $status     = true;
                            $data       = @$login;
                        } else {
                            $attribute  = 'password';
                            $message    = 'Password is incorrect';
                            $data       = @$login;
                        }
                    } else {
                        $attribute  = 'id_company';
                        $message    = 'User Name or Company is invalid';
                        $data       = @$login;
                    }
                } else {
                    $attribute  = 'username';
                    $message    = 'User Name not found';
                }
            } else {
                $attribute  = 'username';
                $message    = 'User Name Inactive';
                $data       = @$login;
            }
        }

        $result = [
            'status' => $status,
            'attribute' => $attribute,
            'message' => $message,
            'data' => $data
        ];
        return $result;
    }

    public static function generateMenu($data) {
        $id_user = $data['id_user'];
        $id_company = $data['id_company'];
        $module_code = $data['module_code'] ?? 'HRS';
        $sql_menu = "select  * from  AuthResponsibility(?,?,?)";
        $list_menu = DB::select($sql_menu, [$id_user, $id_company, $module_code]);
        return $list_menu;
    }

    public static function userRight($data) {
        $id_user = $data['id_user'];
        $id_company = $data['id_company'];
        $sql = "SELECT 
		mu.id_user,mu.user_name,mu.password,mu.email,mu.status,mu.description_name,mu.default_company, 
		mur.id_menu,mur.sequence,mur.start_date,mur.end_date,mur.description_name as description,mur.can_create,mur.can_update,mur.can_delete,mur.can_print,
		mm.address_menu,
		mur.id_company as assigned_company
		FROM master_users mu
		INNER JOIN master_user_responsibility mur
		ON mur.id_user = mu.id_user
		INNER JOIN master_menu mm
		ON mur.id_menu = mm.id_menu
		INNER JOIN master_company mc
		ON mur.id_company = mc.id_company
		WHERE mu.id_user = ? AND mur.id_company = ?";
        $result = DB::select($sql, [$id_user, $id_company]);
        return $result;
    }

    public static function companyLogo($data) {
        $id_company = $data['id_company'];
        $sql = "SELECT company_logo, company_name, company_type          
                FROM master_company
				WHERE id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function getCompany() {
        $sql = "SELECT 
                        id_company id,
                        company_name text,
						company_logo
                FROM master_company
				ORDER BY id_company ASC";
        $result = DB::select($sql);

        return $result;
    }
	public static function getCompany_user() {
        $sql = "SELECT mc.id_company id,
				   mc.company_name text
				   FROM relation_company_users rcu
			INNER JOIN master_company mc
			ON rcu.id_company = mc.id_company
			where rcu.id_user = ?";
        $result = (Array) DB::select($sql, [session('id_user')]);
	//	dd($result);
        return $result;
    }
	public static function getCompany_session($data) {
		$result=[];
        $sql = "SELECT 
                        id_company id,
                        company_name text,
						company_logo
                FROM master_company
				WHERE id_company = ?";
        $result = (Array) DB::select($sql, [$data['id_company']])[0];
	//	dd($result);
        return $result;
    }
	
	public static function getNotificationEmployeeApproval() {
	//	\Log::channel('scheduler')->info("Company = ".session('id_company')." User = ".session('id_user'));
		$arrEmp = EmployeeApproval::get_session_emp();			
		if(session('id_company') == null ||  session('id_company') == "" ){
			return redirect('/logout');
		}
		if($arrEmp == null){
			$idEmp = 1;
		}
		else{
			$idEmp = $arrEmp['id_employee'];
		}
		$idCompany = session('id_company');
        $idUser = session('id_user');
        $result=[];		
		$sql = "SELECT COUNT(c_t.source_transaction_type) as total, c_t.source_transaction_type
				FROM (
					SELECT source_transaction_type FROM
						sp_funct_approval_view (".$idEmp.",".$idCompany.",null,null,null,".$idUser.",null,null)
					) AS c_t
			 GROUP BY c_t.source_transaction_type";
		
        $result = (Array) DB::select($sql);
        return $result;
    }
	
	public static function getNotificationAnnoun() {
        $result=[];
        $sql = "SELECT COUNT(g_announ.id_user) AS total_announ
				FROM hr_employee_anouncement hea
				JOIN(
					SELECT hea.id_announcement, ".session('id_user')." AS id_user
					FROM hr_employee_anouncement hea
					LEFT JOIN  master_general_data mgd
					ON  hea.id_approval_status = mgd.id_general_data
					WHERE (current_date BETWEEN hea.start_date AND hea.end_date) AND hea.published = true 
					AND hea.status = 'A' AND hea.id_company = ".session('id_company')."
					AND hea.id_announcement not in(
					SELECT rau.id_announcement FROM relation_announcement_users rau
						WHERE rau.id_user = COALESCE(".session('id_user').",rau.id_user) AND rau.id_company = ".session('id_company')."
					)					
				) AS g_announ
				ON hea.id_announcement = g_announ.id_announcement
				GROUP BY g_announ.id_user";
        $result = (Array) DB::select($sql);
        return $result;
    }

    public static function remember_me($id, $data) {
        $data = DB::table('master_users as mu')
                ->where('mu.id_user', $id)
                ->update($data);
        return $data;
    }

    public static function getNotificationRevisedRequest() {
        $idCompany = session('id_company');
        $idUser = session('id_user');
        $start = Carbon::now()->subDays(14)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d').' 23:59:59';

        $getRevise = DB::table('hr_request_header as hrh')
            ->select(DB::raw("COUNT(mgd.description) as total"), 'mgd.description')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hrh.id_approval_status')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hrh.id_employee_request')
            ->where('mgd.code', 'Revised')
            ->where('he.id_company', $idCompany)
            ->where('he.id_user', $idUser)
            ->whereBetween('hrh.update_date', [$start, $end])
            ->groupBy('mgd.description')
            ->get()->toArray();
        return $getRevise;
    }

    public static function getNotificationRejectedRequest() {
        $idCompany = session('id_company');
        $idUser = session('id_user');
        $start = Carbon::now()->subDays(7)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d').' 23:59:59';

        $getRejected = DB::table('hr_request_header as hrh')
            ->select(DB::raw("COUNT(mgd.description) as total"), 'mgd.description', 'hrh.id_request_header')
            ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hrh.id_approval_status')
            ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hrh.id_employee_request')
            ->where('mgd.code', 'Rejected')
            ->where('he.id_company', $idCompany)
            ->where('he.id_user', $idUser)
            ->whereBetween('hrh.update_date', [$start, $end])
            ->groupBy('mgd.description', 'hrh.id_request_header')
            ->get()->toArray();
        return $getRejected;
    }
	
	public static function getNotifcom($idCom) {
		$id_emp = EmployeeApproval::get_session_emp();
		if($id_emp == null){
			$idEmp = 1;
		}
		else{
			$idEmp = $id_emp['id_employee'];
		}
		$result=[];
        $sql = "SELECT SUM(t_t.total) as total
			FROM(
					SELECT COUNT(c_t.source_transaction_type) as total
						FROM (
							SELECT * FROM
								sp_funct_approval_view (".$idEmp.",".$idCom.",null,null,null,".session('id_user').",null,null)
							) AS c_t
					 GROUP BY c_t.source_transaction_type		
			) AS t_t";
        $result = (Array) DB::select($sql)[0];
	//	dd($result);
        return $result;
    }
}
