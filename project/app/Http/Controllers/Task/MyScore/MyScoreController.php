<?php

namespace App\Http\Controllers\Task\MyScore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Task\Missions\Missions;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\API\BaseController;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class MyScoreController extends Controller
{
	public function index(Request $request) {
        if ($request->ajax()) {
            $data = Missions::getMyScore($request->myData);
			foreach($data as $key=>$val){
				$startTime = date('H:i',strtotime($val->start_date));
				$endTime = date('H:i',strtotime($val->end_date));
				$completeTime = $val->completion_date != null ? date('H:i',strtotime($val->completion_date)) : null;
				$startDateTime = Carbon::parse($val->start_date)->translatedFormat('d M Y')." ".$startTime;
				$endDateTime = Carbon::parse($val->end_date)->translatedFormat('d M Y')." ".$endTime;
				$completeDateTime = $val->completion_date != null ? Carbon::parse($val->completion_date)->translatedFormat('d M Y')." ".$completeTime : null;
				$data[$key]->start_date = $startDateTime;
				$data[$key]->end_date = $endDateTime;
				$data[$key]->completion_date = $completeDateTime;
				$data[$key]->score_answer = $val->completion_date != null ? $val->score_answer : 0;
			}
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->make(true);
        }
        return view('task.missions.my_score.index');
    }
	
	public function get_task(Request $request) {
		$result = Missions::get_task();
	//	dd($result);
		return response()->json($result);
	}
	
	public function get_tot_score(Request $request) {
		$data = Missions::getMyScore($request->myData);
		$sum = 0;
		$all = 0;
		$result = [];
		foreach($data as $key=>$val){			
			$sum += $val->score_answer;
			$all += $val->maximum_score;
		}
		if($all != 0){
			$tot = ($sum/$all)*100;
		}
		else{
			$tot = 0;
		}		
		$result[0] = $sum;
		$result[1] = round($tot, 2);
		return response()->json($result);
	}
	
}