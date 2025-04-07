<?php

namespace App\Http\Controllers\TalentManagement\TalentDevelopment;

use App\Models\TalentManagement\TalentProgress\RecoProgress;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TalentProgressController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = RecoProgress::getdata();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        $button = '&nbsp;&nbsp;<button type="button" name="batch" id="' . $data->id_talent_recommendation_summary . '" emp_name = "'.$data->emp_name.'" class="batch btn btn-success btn-sm" title="Generate Batch"><span class="fas fa-tasks fa-lg"></span></button>';
						$button .= '&nbsp;&nbsp;<button type="button" name="bei" id="' . $data->id_talent_recommendation_summary . '" emp_name = "'.$data->emp_name.'" class="bei btn btn-info btn-sm" title="Generate BEI"><span class="fas fa-list-ol fa-lg"></span></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('talent_management.talent_development.talent_progress_review.index');
    }
	
	public function modal_batch(Request $request) {
        return view('talent_management.talent_development.talent_progress_review.modal_batch');
    }
	
	public function modal_bei(Request $request) {
        return view('talent_management.talent_development.talent_progress_review.modal_bei');
    }
	
	protected function save_batch(Request $request) {
		 $request->validate([
            'batch_name' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'location' => 'required|string',
            'id_branch' => 'required|string',
                ], [],
                [
                    'batch_name' => 'Batch Name',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'location' => 'Location',
                    'id_branch' => 'Access Branch',
        ]);
		try{
			DB::beginTransaction();
			$form_data = array(
				'batch_name' => $request->batch_name,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'location' => $request->location,
				'id_branch' => $request->id_branch,
				'id_summary_batch' => $request->id_summary_batch,
				'id_company' => session('id_company'),
			);
		//	dd($form_data);
			RecoProgress::gen_batch($form_data);
			
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Batch Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Batch !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function save_bei(Request $request) {
		$request->validate([
            'int_type' => 'required|string',
            'int_date' => 'required|string',
                ], [],
                [
                    'int_type' => 'Interview Type',
                    'int_date' => 'Interview Date',
        ]);
		try{
			DB::beginTransaction();
			$form_data = array(
				'id_summary_bei' => $request->id_summary_bei,
				'int_type' => $request->int_type,
				'int_date' => $request->int_date,
				'id_company' => session('id_company'),
			);
			RecoProgress::gen_bei($form_data);
			
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'BEI Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save BEI !! [' . $e->getMessage() . ']']);           
		}	
	}
	
}
