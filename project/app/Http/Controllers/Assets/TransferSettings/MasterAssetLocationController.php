<?php
namespace App\Http\Controllers\Assets\TransferSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Exception;
use App\Models\Assets\TransferSettings\MasterAssetLocation;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterAssetLocationController extends Controller {	
    
    public function index(Request $request) {
        if($request->ajax()) {
            $locations = MasterAssetLocation::where('id_company', session('id_company'))->get();
            foreach($locations as $location) {
                $location->location = $location->location;
                $location->branch = $location->branch;
            }
            return DataTables::of($locations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id_asset_location;
                })
                ->make();
        }
        return view('assets.transfer_settings.master_asset_location.index');
    }

    public function getLocations(Request $request) {
        if($request->id_branch) {
            $locations = MasterLocation::where('id_branch', $request->id_branch)->where('id_company', session('id_company'))->where('status', 'A')->orderBy('description')->get(['id_location as id', 'description as text']);
            return response()->json([
                'data' => $locations
            ]);
        } else {
            $branches = MasterBranch::where('id_company', session('id_company'))->where('status', 'A')->orderBy('description')->get(['id_branch as id', 'description as text']);
            return response()->json([
                'data' => $branches
            ]);
        }
    }

    public function getEdit(Request $request) {
        $request->validate([
            'id_asset_location' => 'required',
        ]);
        $assetLocation = MasterAssetLocation::findOrFail($request->id_asset_location);
        return response()->json([
            'data' => $assetLocation,
        ]);
    }

    public function save(Request $request) {
        $acceptedRequests = [
            'id_asset_location' => 'nullable',
            'location_code' => 'required|string',
            'id_location' => 'required',
            'id_branch' => 'required',
            'description' => 'required|string',
            'status' => 'required|in:A,I',
        ];
        $request = SanitizedForm::sanitizeStringInput($request, $acceptedRequests);
        $request->validate($acceptedRequests);

        try {
            $data = $request->only(array_keys($acceptedRequests));
            if($request->id_asset_location) {
                $assetLocation = MasterAssetLocation::findOrFail($request->id_asset_location);
                $data = array_merge($data, [
                    'updated_by' => session('id_user'),
                ]);
                $assetLocation->update($data);
            } else {
                $request->validate([
                    'location_code' => 'unique:'.MasterAssetLocation::class
                ]);
                $data = array_merge($data, [
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ]);
                $assetLocation = MasterAssetLocation::create($data);
            }

            return response()->json([
                'message' => 'Asset Location saved successfully!'
            ]);
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}