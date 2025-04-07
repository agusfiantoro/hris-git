<?php

namespace App\Http\Controllers\Organization\MasterOrganization;

use App\Models\Organization\MasterOrganization\MasterPrincipal;
use App\Models\Organization\MasterOrganization\MasterDivision;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class MasterPrincipalController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterPrincipal::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_principal . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_principal . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('organization.master_organization.master_principal.index');
    }

    protected function save(Request $request) {

        $request->validate([
            'principal_code' => 'required', Rule::unique('master_principal')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'description' => 'required|string',
            'id_division' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'principal_code' => 'Principal Code',
                    'id_division' => 'Division',
                    'description' => 'Description',
                    'status' => 'Status',
        ]);

        $pathPrincipal = 'public/upload/principal/';
        $imgLogo = null;
        $getPrincipal = null;
        if($request->hidden_id){
            $getPrincipal = MasterPrincipal::where('id_principal', $request->hidden_id)->first();
        }

        $storageLogo = storage_path('app/'.$pathPrincipal);
        !file_exists($storageLogo) && mkdir($storageLogo, 0777, true);

        if($request->principal_logo){
            $extension = $request->principal_logo->extension();
            $imageName = Str::random(5).'_'.date('YmdHis').'.'.$extension;
            $imgLogo = $imageName;
            // $this->resizeImage($request->principal_logo, $pathPrincipal.$imageName); 
            Storage::putFileAs($pathPrincipal, $request->principal_logo, $imageName);

            if($request->hidden_id){
                if(Storage::exists($pathPrincipal.@$getPrincipal->principal_logo)) {
                    //jika ganti foto dan foto sebelumnya masih ada maka hapus foto sebelumnya di storage
                    Storage::delete($pathPrincipal.@$getPrincipal->principal_logo);
                }
            }
        }

        $form_data = array(
            'principal_code' => $request->principal_code,
            'description' => $request->description,
            'id_division' => $request->id_division,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'principal_logo' => $request->principal_logo ?? null,
            'is_published_to_web' => @$request->is_published_to_web ? true : false,
			'erp_principal_code' => $request->erp_principal_code,
            'created_by' => session('id_user'),
        );
        if($imgLogo){ $form_data['principal_logo'] = $imgLogo; }
        MasterPrincipal::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Principal Saved Successfully !!']);
    }

    public function destroy($id) {
        $data = MasterPrincipal::findOrFail($id);
        $data->delete();
    }

    public function update(Request $request) {
		 $request->validate([           
            'principal_code' => 'required|string',
            'description' => 'required|string',
            'id_division' => 'required|string',
            'status' => 'required|string',
                ], [],
                [
                    'principal_code' => 'Principal Code',
                    'id_division' => 'Division',
                    'description' => 'Description',
                    'status' => 'Status',
        ]);

        $pathPrincipal = 'public/upload/principal/';
        $imgLogo = null;
        $getPrincipal = null;
        if($request->hidden_id){
            $getPrincipal = MasterPrincipal::where('id_principal', $request->hidden_id)->first();
        }

        $storageLogo = storage_path('app/'.$pathPrincipal);
        !file_exists($storageLogo) && mkdir($storageLogo, 0777, true);

        if($request->principal_logo){
            $extension = $request->principal_logo->extension();
            $imageName = Str::random(5).'_'.date('YmdHis').'.'.$extension;
            $imgLogo = $imageName;
            // $this->resizeImage($request->principal_logo, $pathPrincipal.$imageName); 
            Storage::putFileAs($pathPrincipal, $request->principal_logo, $imageName);

            if($request->hidden_id){
                if(Storage::exists($pathPrincipal.@$getPrincipal->principal_logo)) {
                    //jika ganti foto dan foto sebelumnya masih ada maka hapus foto sebelumnya di storage
                    Storage::delete($pathPrincipal.@$getPrincipal->principal_logo);
                }
            }
        }
        $form_data = array(
            'principal_code' => $request->principal_code,
            'description' => $request->description,
            'id_division' => $request->id_division,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'is_published_to_web' => @$request->is_published_to_web=='on' ? true : false,
			'erp_principal_code' => $request->erp_principal_code,
            'updated_by' => session('id_user'),
        );
        if($imgLogo){ $form_data['principal_logo'] = $imgLogo; }
        MasterPrincipal::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Principal Updated successfully']);
    }

    public function edit($id) {

        if (request()->ajax()) {
            $pathPrincipalStorage = 'public/upload/principal/';
            $logoPath = null;
            $data = MasterPrincipal::findOrFail($id);
            if($data){
                if($data->principal_logo){
                    if(Storage::exists($pathPrincipalStorage.@$data->principal_logo)) {
                        $logoPath = asset('project/storage/app/'.$pathPrincipalStorage.@$data->principal_logo);
                    }
                }
                $data->principal_logo_path = $logoPath;
            }

            return response()->json(['result' => $data]);
        }
    }

    public function get_company() {
        $result = MasterPrincipal::get_company();
        return response()->json($result);
    }
	public function get_division() {
        $result = MasterPrincipal::get_division();
        return response()->json($result);
    }

}
