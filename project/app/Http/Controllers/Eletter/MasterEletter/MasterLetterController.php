<?php

namespace App\Http\Controllers\Eletter\MasterEletter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Eletter\MasterEletter\MasterGeneralLetter;
use DataTables;

class MasterLetterController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$id_general_type = DB::table('master_general_type')
			->select(
				\DB::RAW('id_general_type')
			)
			->where('description','Master Letter Category')
			->where('id_general_type','33')
			->first();
			$data = MasterGeneralLetter::join('master_general_data as data_others','data_others.id_general_data','=','master_general_data.relation_to_id_general_data')
			->select(
				\DB::RAW('master_general_data.id_general_data'),
				\DB::RAW('master_general_data.sequence'),
				\DB::RAW('master_general_data.code'),
				\DB::RAW('master_general_data.description'),
				\DB::RAW('master_general_data.status')
			)
			->where('master_general_data.id_company',session('id_company'))
			->where('master_general_data.restrict_by','User')
			->where('data_others.code','OTH')
			->where('master_general_data.id_general_type',$id_general_type->id_general_type)
			// ->where('description','Master Letter Category')
			->orderBy('master_general_data.id_general_data','DESC')
			->get();
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_general_data.'" title="Edit"><span class="fas fa-edit"></span></button>';
				// $button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm" more_id="'.$data->id_general_data.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('eletter.master_letter.index');
	}
	public function get_sequence()
	{
		$id_general_type = DB::table('master_general_type')
		->select(
			\DB::RAW('id_general_type')
		)
		->where('description','Master Letter Category')
		->where('id_general_type','33')
		->first();
		$sequence_akhir = MasterGeneralLetter::join('master_general_data as data_others','data_others.id_general_data','=','master_general_data.relation_to_id_general_data')
		->where('master_general_data.id_company',session('id_company'))
		->where('data_others.code','OTH')
		// ->where('master_general_data.restrict_by','User')
		->where('master_general_data.id_general_type',$id_general_type->id_general_type)
		->max('master_general_data.sequence');
		if ($sequence_akhir == NULL) {
			$sequence_akhir = 1;
		}else{
			$sequence_akhir++;
		}
		return response()->json($sequence_akhir);
	}
	public function save(Request $request)
	{
		$request->validate([
			'sequence' => 'required|integer|min:1',
			'code' => 'required|unique:master_general_data,code',
			'description' => 'required',
			'status' => 'required'
		]);
		try {
			DB::beginTransaction();
			$id_general_type = DB::table('master_general_type')
			->select(
				\DB::RAW('id_general_type')
			)
			->where('description','Master Letter Category')
			->where('id_general_type','33')
			->first();
			$relation = MasterGeneralLetter::join('master_general_data as data_others','data_others.id_general_data','=','master_general_data.relation_to_id_general_data')
			->select(
				\DB::RAW('data_others.id_general_data as id_general_data_relation')
			)
			->where('data_others.id_company',session('id_company'))
			->where('data_others.code','OTH')
			->first();
			$sequence_akhir = MasterGeneralLetter::join('master_general_data as data_others','data_others.id_general_data','=','master_general_data.relation_to_id_general_data')
			->where('master_general_data.id_company',session('id_company'))
			->where('data_others.code','OTH')
			// ->where('master_general_data.restrict_by','User')
			->where('master_general_data.id_general_type',$id_general_type->id_general_type)
			->max('master_general_data.sequence');
			if ($sequence_akhir == NULL) {
				$sequence_akhir = 1;
			}else{
				$sequence_akhir++;
			}
			$cek_sequence = MasterGeneralLetter::join('master_general_data as data_others','data_others.id_general_data','=','master_general_data.relation_to_id_general_data')
			->where('master_general_data.id_company',session('id_company'))
			->where('data_others.code','OTH')
			// ->where('master_general_data.restrict_by','User')
			->where('master_general_data.id_general_type',$id_general_type->id_general_type)
			->where('master_general_data.sequence',$request->sequence)
			->count();
			if ($cek_sequence>0) {
				$request->validate([
					'sequence' => 'required|integer|min:1|unique:master_general_data,sequence',
				]);
				return response()->json([
					'status'=>'sama',
					'message'=>'This sequence has already been used.'
				]);
			}else{
				$data = New MasterGeneralLetter();
				$data -> id_general_type = $id_general_type->id_general_type;
				$data -> sequence = $sequence_akhir;
				$data -> code = $request->code;
				$data -> description = $request->description;
				$data -> status = $request->status;
				$data -> restrict_by = 'User';
				$data -> id_company = session('id_company');
				$data -> created_by = session('id_user');
				$data -> relation_to_id_general_data = $relation->id_general_data_relation;
				$data -> save();
				DB::commit();
				return response()->json(['status'=>'true','message'=>'Master Letter Successfully !!']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Master Letter !! [' . $e->getMessage() . ']']);
		}
	}
	public function get_edit($id_general_data)
	{
		$data = MasterGeneralLetter::select(
			\DB::RAW('id_general_data'),
			\DB::RAW('sequence'),
			\DB::RAW('code'),
			\DB::RAW('description'),
			\DB::RAW('status')
		)
		->where('id_general_data',$id_general_data)
		->where('id_company',session('id_company'))
		->get();
		return response()->json($data);
	}
	public function edit(Request $request)
	{
		$request->validate([
			'sequence' => 'required|integer|min:1',
			'code' => 'required',
			'description' => 'required',
			'status' => 'required'
		]);
		try {
			DB::beginTransaction();
			$data = MasterGeneralLetter::where('id_general_data',$request->id_general_data)->first();
			if ($data->code != $request->code) {
				$existingDataWithSameCode = MasterGeneralLetter::join('master_general_data as data_others','data_others.id_general_data','=','master_general_data.relation_to_id_general_data')
				->where('master_general_data.code', $request->code)
				->where('master_general_data.id_general_type',$data->id_general_type)
				->where('master_general_data.id_company',session('id_company'))
				->first();
				if ($existingDataWithSameCode) {
					$request->validate([
						'code' => 'required|unique:master_general_data,code'
					]);
					return response()->json([
						'status'=>'sama',
						'message'=>'This code has already been used. Please choose a different code.'
					]);
				}
			}elseif ($data->sequence != $request->sequence) {
				$cek_sequence = MasterGeneralLetter::join('master_general_data as data_others','data_others.id_general_data','=','master_general_data.relation_to_id_general_data')
				->where('data_others.code','OTH')
				->where('master_general_data.id_company',session('id_company'))
				->where('master_general_data.id_general_type',$data->id_general_type)
				->where('master_general_data.sequence',$request->sequence)
				->first();
				if ($cek_sequence) {
					$request->validate([
						'sequence' => 'required|integer|min:1|unique:master_general_data,sequence',
					]);
					return response()->json([
						'status'=>'sama',
						'message'=>'This sequence has already been used.'
					]);
				}
			}
			$data -> sequence = $request->sequence;
			$data -> code = $request->code;
			$data -> description = $request->description;
			$data -> status = $request->status;
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');
			$data -> save();
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Master Letter Edit Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Master Letter !! [' . $e->getMessage() . ']']);
		}
	}
	public function destroy($id_general_data)
	{
		try {
			DB::beginTransaction();
			$data = MasterGeneralLetter::where('id_general_data',$id_general_data)->first();
			if ($data) {
				$data -> delete();
				DB::commit();
				return response()->json(['status'=>'true','message'=>'Master Letter Delete Successfully !!']);
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false']);
		}
	}
}
