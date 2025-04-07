<?php
namespace App\Http\Controllers\Assets\DepreciationSettings;

use App\Http\Controllers\Controller;
use App\Models\Assets\DepreciationSettings\DepreciationMethod;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DepreciationMethodController extends Controller {	
    use StandardResponse;

    public function index(Request $request) {
        if($request->ajax()) {
            $data = DepreciationMethod::currentCompany()->chronological(true)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_depreciation_method;
                })
                ->make();
        }
        return view('assets.depreciation_settings.depreciation_method.index');
    }

    public function save(Request $request) {
        $request->validate([
            'id_depreciation_method' => 'nullable',
            'depreciation_code' => 'required',
            'depreciation_rule' => 'required',
            'description' => 'required',
            'status' => 'required|in:A,I',
        ]);

        try {
            $data = $request->all();
            if($request->id_depreciation_method) {
                $method = DepreciationMethod::findOrFail($request->id_depreciation_method);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                ]);
                $method->update($data);
            } else {
                $data = array_merge($data, [
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ]);
                $method = DepreciationMethod::create($data);
            }
            return $this->success(null, 'Depreciation Method saved successfully!');
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage());
        }
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_depreciation_method' => 'required'
        ]);
        $data = DepreciationMethod::findOrFail($request->id_depreciation_method);
        return $this->success($data);
    }

    public function delete(Request $request) {
        $request->validate([
            'id_depreciation_method' => 'required'
        ]);
        $data = DepreciationMethod::findOrFail($request->id_depreciation_method);
        $data->status = 'I';
        if($data->save()) {
            return $this->success(null, 'Depreciation Method has been set as inactive!');
        }
        return $this->error(null, 'Failed deleting data!');
    }
}