<?php

namespace App\Http\Controllers\GeneralSetting\CompanySetting;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\GeneralSetting\CompanySetting\MasterCurrency;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $data = Company::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_company . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';

                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_company . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('name', function($row) {
                                return $row->name;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('general_setting.company_setting.company.index');
    }

    public function browse(Request $request) {
        if ($request->ajax()) {
            $data = MasterCurrency::all();
            echo json_encode($data);
        }
    }

    public function checkid($id) {
        $data = MasterCurrency::findOrFail($id);
        return response()->json(['result' => $data]);
    }

    protected function save(Request $request) {
        ini_set('max_execution_time', -1);
        try{
            DB::beginTransaction();
            $request->validate([
                'company_code' => 'required|min:2|max:3|unique:master_company',
                'company_name' => 'required|string',
                'address' => 'required|string',
                'administrator_email' => 'required|unique:master_company',
                'id_currency' => 'required|string',
                'company_logo' => 'image|max:300',
                    ], [],
                    [
                        'company_code' => 'Company Code',
                        'company_name' => 'Company Name',
                        'address' => 'Address',
                        'administrator_email' => 'Administrator Email',
                        'id_currency' => 'Currency Code',
                        'company_logo' => 'Company Logo',
            ]);

            $image = null;
            if($request->company_logo){
                $image_file = file_get_contents($request->company_logo);
                $image = base64_encode($image_file);
            }

            $form_data = array(
                'company_code' => $request->company_code,
                'company_name' => $request->company_name,
                'company_type' => $request->company_type,
                'description' => $request->description,
                'address' => $request->address,
                'company_phone' => $request->company_phone,
                'company_email' => $request->company_email,
                'company_registry' => $request->company_registry,
                'administrator_email' => $request->administrator_email,
                'open_period' => $request->open_period,
                'inactive_date' => $request->inactive_date,
                'company_logo' => $image,
                'created_by' => session('id_user'),
                'id_currency' => $request->id_currency,
            );
            $id_company = Company::create($form_data)->id_company;
            
            $CreateMasterGeneralData = DB::table(DB::raw("CreateMasterGeneralData(".$id_company.")"))->select('*')->get();
            $CreateMasterJobStatus = DB::table(DB::raw("CreateMasterJobStatus(".$id_company.")"))->select('*')->get();
            $CreateMasterJobGrade = DB::table(DB::raw("CreateMasterJobGrade(".$id_company.")"))->select('*')->get();
            $CreateMasterInsurance = DB::table(DB::raw("CreateMasterInsurance(".$id_company.")"))->select('*')->get();
            $CreateMasterBank = DB::table(DB::raw("CreateMasterBank(".$id_company.")"))->select('*')->get();
            $CreateMasterLeaveType = DB::table(DB::raw("CreateMasterLeaveType(".$id_company.")"))->select('*')->get();
            $CreateMasterShiftDaily = DB::table(DB::raw("CreateMasterShiftDaily(".$id_company.")"))->select('*')->get();
            $CreateMasterHoliday = DB::table(DB::raw("CreateMasterHoliday(".$id_company.")"))->select('*')->get();

            DB::commit();   
            return response()->json(['status' => 'true', 'message' => 'Company Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id) {
        $data = Company::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
		if($request->company_logo != ""){
			$image_file = file_get_contents($request->company_logo);
			$image = base64_encode($image_file);
			
			$form_data = array(
            'company_code' => $request->company_code,
            'company_name' => $request->company_name,
			'company_type' => $request->company_type,
            'description' => $request->description,
            'address' => $request->address,
            'company_phone' => $request->company_phone,
            'company_registry' => $request->company_registry,
            'administrator_email' => $request->administrator_email,
            'company_email' => $request->company_email,
            'open_period' => $request->open_period,
            'inactive_date' => $request->inactive_date,
            'company_logo' => $image,
            'id_currency' => $request->id_currency,
            'updated_by' => session('id_user'),
			);
		}
		else{
			$form_data = array(
            'company_code' => $request->company_code,
            'company_name' => $request->company_name,
			'company_type' => $request->company_type,
            'description' => $request->description,
            'address' => $request->address,
            'company_phone' => $request->company_phone,
            'company_registry' => $request->company_registry,
            'administrator_email' => $request->administrator_email,
            'company_email' => $request->company_email,
            'open_period' => $request->open_period,
            'inactive_date' => $request->inactive_date,
            'id_currency' => $request->id_currency,
            'updated_by' => session('id_user'),
			);
		}
       

        Company::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Company Updated Successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = Company::findOrFail($id);
            $master = MasterCurrency::findOrFail($data->id_currency)->toArray();
			$master['currency_desc'] = $master['description'];
			unset($master['description']);
            return response()->json(['result' => $data, 'hasil' => $master]);
        }
    }

}
