<?php

namespace App\Http\Controllers\Setting\Responsibility;

use App\Models\Setting\Responsibility\ResponsibilityMenu;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;

class ResponsibilityMenuController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = ResponsibilityMenu::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_responsibility_menu . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_responsibility_menu . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('company_name', function($row) {
                                return $row->company_name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('setting.responsibility.responsibility_group.index');
    }

    protected function save(Request $request) {
        $request->validate([
            'responsibility_name' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'responsibility_name' => 'Responsibility Name',
                    'status' => 'Status',
        ]);

        $form_data = array(
            'responsibility_name' => $request->responsibility_name,
            'status' => $request->status,
            'sequence' => $request->sequence,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'icon' => $request->icon,
            'created_by' => session('id_user'),
        );
        ResponsibilityMenu::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Responsibility Group Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = ResponsibilityMenu::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
        $form_data = array(
            'responsibility_name' => $request->responsibility_name,
            'status' => $request->status,
            'sequence' => $request->sequence,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'icon' => $request->icon,
            'created_by' => session('id_user'),
        );

        ResponsibilityMenu::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Responsibility Group Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = ResponsibilityMenu::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = ResponsibilityMenu::get_company();
        return response()->json($result);
    }

}
