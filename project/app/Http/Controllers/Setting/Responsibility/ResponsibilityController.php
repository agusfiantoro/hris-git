<?php

namespace App\Http\Controllers\Setting\Responsibility;

use App\Models\Setting\Responsibility\Responsibility;
use App\Models\Setting\Responsibility\ResponsibilityMenu;
use App\Models\Setting\Responsibility\MasterMenu;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Validator;

class ResponsibilityController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = Responsibility::getdata();
            //	dd($data);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_responsibility . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_responsibility . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('responsibility_menu', function($row) {
                                return $row->responsibility_menu;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('setting.responsibility.responsibility_menu.index');
    }

    public function browse(Request $request) {
        if ($request->ajax()) {
            $data = ResponsibilityMenu::getdata();
            echo json_encode($data);
        }
    }

    public function checkid($id) {
        $data = ResponsibilityMenu::findOrFail($id);
        $com = Company::findOrFail($data->id_company);
        return response()->json(['result' => $data, 'com' => $com]);
    }

    protected function validateResponsibility(Request $request) {

        $arr_form_validate = [
            'id_responsibility_menu' => 'required|string',
            'responsibility_name' => 'required|string',
            'responsibility.*.menu_name' => 'required|string',
            'responsibility.*.address_menu' => 'required|string',
        ];
        $arr_msg_form_validate = [
            'id_responsibility_menu.required' => 'The Responsibility Menu field is required',
            'responsibility_name.required' => 'The Responsibility Name field is required',
            'responsibility.*.menu_name.required' => 'The Menu Name field is required',
            'responsibility.*.address_menu.required' => 'The Address Name field is required',
        ];
        if ($request->post('responsibility') == null) {
            $validate_responsibility = ['table_menu_detail' => 'required|string'];
            $validate_msg_responsibility = ['table_menu_detail.required' => 'Menu cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_responsibility);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_responsibility);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        $this->validateResponsibility($request);
        $form_data = array(
            'id_responsibility_menu' => $request->id_responsibility_menu,
            'responsibility_name' => $request->responsibility_name,
			'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
//				'responsibility'		=>  $request->responsibility,
        );
        $responsibility = Responsibility::create($form_data);
        foreach ($request->responsibility as $key => $value) {
            MasterMenu::create(array(
                'id_responsibility' => $responsibility->id_responsibility,
                'menu_name' => $value['menu_name'],
                'address_menu' => $value['address_menu'],
                'default_user' => isset($value['default_user']) == "on" ? 1 : 0,
                'default_manager' => isset($value['default_manager']) == "on" ? 1 : 0,
                'default_administrator' => isset($value['default_administrator']) == "on" ? 1 : 0,
                'status' => $value['status'],
                'created_by' => session('id_user'),
            ));
        }
        return response()->json(['status' => 'true', 'message' => 'Responsibility Saved Successfully !!']);
    }

    public function update(Request $request) {
        $this->validateResponsibility($request);
        $form_data = array(
            'id_responsibility' => $request->id_responsibility,
            'id_responsibility_menu' => $request->id_responsibility_menu,
            'responsibility_name' => $request->responsibility_name,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
            'responsibility' => $request->responsibility,
        );
        $responsibility = Responsibility::findOrFail($request->id_responsibility)->update($form_data);
        $collect_responsibility = collect($form_data['responsibility'])->groupBy('id_menu')->toArray();
        $list_responsibility = array_filter(array_keys($collect_responsibility));

        DB::delete("DELETE FROM  master_menu mm
                        WHERE mm.id_responsibility = ? AND mm.id_menu NOT IN (" . implode(",", $list_responsibility) . ")", [$request->id_responsibility]);

        foreach ($request->responsibility as $key => $value) {
            if ($value['id_menu'] == "") {
                MasterMenu::create(array(
                    'id_responsibility' => $request->id_responsibility,
                    'menu_name' => $value['menu_name'],
                    'address_menu' => $value['address_menu'],
					'default_user' => isset($value['default_user']) == "on" ? 1 : 0,
					'default_manager' => isset($value['default_manager']) == "on" ? 1 : 0,
					'default_administrator' => isset($value['default_administrator']) == "on" ? 1 : 0,
                    'status' => $value['status'],
                    'created_by' => session('id_user'),
                ));
            } else {
                MasterMenu::where('id_menu', $value['id_menu'])->update(array(
                    //	'id_responsibility' => $request->id_responsibility,
                    'menu_name' => $value['menu_name'],
                    'address_menu' => $value['address_menu'],
					'default_user' => isset($value['default_user']) == "on" ? 1 : 0,
					'default_manager' => isset($value['default_manager']) == "on" ? 1 : 0,
					'default_administrator' => isset($value['default_administrator']) == "on" ? 1 : 0,
                    'status' => $value['status'],
                    'updated_by' => session('id_user'),
                ));
            }
        }
        return response()->json(['status' => 'true', 'message' => 'Responsibility Updated Successfully !!']);
    }

     public function destroy($id) {
        $data = Responsibility::findOrFail($id);
        MasterMenu::where('id_responsibility', $id)->delete();
        $data->delete();
    }

	public function edit($id) {

        if (request()->ajax()) {
            $data = Responsibility::findOrFail($id);
            $master = ResponsibilityMenu::findOrFail($data->id_responsibility_menu);
            return response()->json(['result' => $data, 'hasil' => $master]);
        }
    }

    public function get_address_menu() {
        $result = Responsibility::get_address_menu();
        return response()->json($result);
    }
	
	public function get_company() {
        $result = Responsibility::get_company();
        return response()->json($result);
    }
    public function get_responsibility(Request $request) {
        $data = [
            'id_responsibility' => $request->id_responsibility
        ];
        $result = Responsibility::get_responsibility($data);
        return response()->json($result);
    }

}
