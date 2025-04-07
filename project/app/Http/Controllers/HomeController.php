<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Home;
use App\Models\TimeAttendance\Attendance\Attendance;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->Home = new Home;
        $this->LoginController = new LoginController;
	}

    public function index(Request $request)
    {	
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

        if($request->cookie('remember_hris')){
            $user = DB::table('master_users as mu')
                ->join('hr_employee as he', 'he.id_user', '=', 'mu.id_user', 'left')
                ->join('master_company as mc', 'mc.id_company', '=', 'he.id_company', 'left')
                ->select('mu.id_user', 'mu.user_name', 'mu.password', 'mu.access_group', 'mu.status', 'mu.remember_token', 'he.id_company', 'he.id_employee', 'mc.company_name')
                ->where('mu.remember_token', $request->cookie('remember_hris'))
                ->where('mu.status', 'A')
                ->first();

            if(!$user){
                return redirect('/login')->with('error', 'Please login first');
            } else {
                $access = $user->access_group;
            }

            if(!@$user->id_company){
                return redirect('logout');
            }

            $data = [
                'id_user'       => $user->id_user,
                'access_group'  => $user->access_group,
                'id_company'    => $user->id_company,
                'company_name'  => $user->company_name,
                'username'      => $user->user_name,
                'password'      => $user->password,
            ];
            $this->LoginController->set_session($request, $data);

            return redirect($redirect[$access]);
        } else {
            return redirect('/login');
        }
    }

    public function home()
    {   
        $redirect = [
            null                    => 'home',
            'Default_User'          => 'dashboard/dashboard_user/dashboard',
            'Default_Manager'       => 'dashboard/dashboard_management/dashboard_management',
            'Default_Administrator' => 'dashboard/dashboard_administrator/dashboard_administrator',
        ];
        $access_group = session('access_group');
        return redirect($redirect[$access_group]);
    }

    public function read_file()
    {   
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $filename = '1.csv';
            $filePath = 'public/'.$filename;
            $dataReturn = [];

            if(Storage::disk('local')->exists($filePath)){
                $reader = new Csv(); 
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load(storage_path('app/'.$filePath));
                $sheetData   = $spreadsheet->getActiveSheet()->toArray();
                foreach ($sheetData as $k => $row) {
                    if($k > 0){
                        $up = [
                            'longitude' => $row[1],
                            'latitude'  => $row[2],
                        ];
                        $dataReturn[] = $row[0];
                        $execute = DB::table('master_location')
                                ->where('description', $row[0])
                                ->update($up);
                    }
                }
            }
            DB::commit();   
            return response(['status' => 'Success', 'message' => 'Update Success', 'data' => $dataReturn]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'Failed', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function delete_attendance_image($month=null, $id_company=null)
    {   
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $path       = 'public/images/';
            $del        = [];
            $photos     = [];
            $range      = null;
            $fromDate   = Carbon::now()->subDays(5);
            if($month){
                $range  = Carbon::createFromFormat('Y-m-d', $fromDate->format('Y-m-d'))->subMonths((int)$month);
            }

            $data = DB::table('hr_work_days as hwd')
                    ->select('hwd.id_workdays', 'hwd.image_attachment_in', 'hwd.image_attachment_out','hwd.current_dates')
                    ->whereDate('hwd.current_dates', '<', $fromDate);
            if($range){
                $data->whereDate('hwd.current_dates', '>=', $range);
            }
            if($id_company){
                $data->where('hwd.id_company', $id_company);
            }
            $get = $data->orderBy('hwd.creation_date', 'desc')->get();

            foreach ($get as $key => $val) {
                $imgIn  = $val->image_attachment_in;
                $imgOut = $val->image_attachment_out;
                $up = [];

                if(!is_null($imgIn)){
                    $filePathIn     = $path.$imgIn;
                    if(Storage::disk('local')->exists($filePathIn)){
                        if(Storage::delete(storage_path('app/'.$filePathIn))){
                            $photos[] = 1;
                            $up['image_attachment_in'] = null;
                        }
                    }
                }
                if(!is_null($imgOut)){
                    $filePathOut     = $path.$imgOut;
                    if(Storage::disk('local')->exists($filePathOut)){
                        if(Storage::delete(storage_path('app/'.$filePathOut))){
                            $photos[] = 1;
                            $up['image_attachment_out'] = null;
                        }
                    }
                }
                if(count($up) > 0){
                    $del[] = 1;
                    // $update = DB::table('hr_work_days as hwd')->where('hwd.id_workdays', $val->id_workdays)->update($up);
                }
            }
            if($range){
                $notif = 'delete '.count($photos).' photos from '.$range->format('Y-m-d').' to '.$fromDate->format('Y-m-d').' success';
            } else {
                $notif = 'delete '.count($photos).' photos until '.$fromDate->format('Y-m-d').' success';
            }
            DB::commit();   
            return response(['status' => 'Success', 'message' => $notif]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'Failed', 'message' => $e->getMessage()]);
        }
    }

}
