<?php

namespace App\Http\Controllers\Employee\Announcement;

use App\Models\Employee\EmployeeSetting\MasterAnnouncement;
use App\Models\Employee\EmployeeSetting\RelationAnnouncementUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $data = MasterAnnouncement::getdata_publish();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
							 ->addColumn('action', function($data) {
                                $button = '<button type="button" name="view" id="' . $data->id_announcement . '" class="view btn btn-warning btn-sm" title="View"><span class="far fa-eye" style="color:white;"></span></button> ';                              
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('employee.employee.announcement.index');
    }
	
	public function get_announcement_edit(Request $request) {
        $data = [
            'id_announcement' => $request->id_announcement
        ];
        $result = MasterAnnouncement::get_announcement_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_announcement_new(Request $request) {
        $result = MasterAnnouncement::get_announcement_new();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_announcement_show(Request $request) {
		$data = [
            'id_announcement' => $request->id_announcement
        ];
	//	dd($data);
        $result = MasterAnnouncement::get_announcement_show($data);
	//	dd($result);
        return response()->json($result);
    }
	
	protected function close_read(Request $request) {
        $data = array(
            'id_announcement' => $request->id_announ_relation,
        );
	//	RelationAnnouncementUser::create($form_data);
        return response()->json(['status' => 'true']);
    }
	protected function save_read(Request $request) {
        $form_data = array(
            'id_announcement' => $request->id_announ_relation,
            'id_user' => session('id_user'),
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
		RelationAnnouncementUser::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Announcement is Read']);
    }
}
