<?php
namespace App\Http\Controllers\GeneralSetting\CompanySetting;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\GeneralSetting\CompanySetting\MasterPeriod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MasterPeriodController extends Controller {

     public function index(Request $request) {
        if ($request->ajax()) {
            $data = MasterPeriod::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_period . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';

                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_period . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('general_setting.company_setting.master_period.index');
    }
	
	protected function save(Request $request) {       
            $request->validate([
                'year' => 'required',
                'start_date' => 'required|string',
                'end_date' => 'required|string',
                    ], [],
                    [
                        'year' => 'Year',
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',                   
            ]);
		try{
            DB::beginTransaction();
            $form_data = array(
                'description' => $request->description,
                'period_code' => $request->period_code,
				'year' => $request->year,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'id_function_type'  => $request->function_type,
				'status' => $request->status,
				'id_company' => session('id_company'),
                'created_by' => session('id_user'),
            );
           MasterPeriod::create($form_data);                  

            DB::commit();   
            return response()->json(['status' => 'true', 'message' => 'Master Period Saved Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	protected function update(Request $request) {	
	 	$request->validate([
                'year' => 'required',
                'start_date' => 'required|string',
                'end_date' => 'required|string',
                    ], [],
                    [
                        'year' => 'Year',
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',                   
            ]);
		try{
			DB::beginTransaction();
			 $form_data = array(
                'description' => $request->description,
                'period_code' => $request->period_code,
				'year' => $request->year,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'id_function_type'  => $request->function_type,
				'status' => $request->status,
				'id_company' => session('id_company'),
                'updated_by' => session('id_user'),
            );
			
			MasterPeriod::findOrFail($request->id_period)->update($form_data);
			
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Master Period Updated Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Updated Master Period [' . $e->getMessage() . ']']);           
        }
	}
	
	public function destroy($id) {
        $data = MasterPeriod::findOrFail($id);
        $data->delete();
    }
	
	public function get_period_edit(Request $request) {
        $data = [
            'id_period' => $request->id_period
        ];
        $result = MasterPeriod::get_period_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_period_type() {		
        $result = MasterPeriod::get_period_type();
        return response()->json($result);
    }
}
