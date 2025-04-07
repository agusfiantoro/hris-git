<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Models\Employee\EmployeeSetting\HrCompanyCampaign;
use App\Models\Employee\Employee\Employee;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use DataTables;
use Validator;

class MasterCampaignController extends Controller {

	public function resizeImage($file, $saveTo) {
        $saveImage = Image::make($file)->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save(storage_path('app/'. $saveTo));
        return response()->json(['file' => $saveTo]);
    }

    public function index(Request $request) {
		$reffNumber = HrCompanyCampaign::getReffNumber();
		$findEmployee = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
		$idEmployee = @$findEmployee->id_employee;

        if ($request->ajax()) {
        	$getCampaign = DB::table('hr_company_campaign as hcc')
        		->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hcc.id_employee_request')
				->where('hcc.id_company', session('id_company'))
        		->select('hcc.*', 'he.name as employee_name')
        		->orderByDesc('hcc.id_company_campaign')
                ->get();

            return DataTables::of($getCampaign)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('image_poster_path', function($data) {
                	if($data->image_poster){
                		$pathCampaignStorage = 'public/upload/campaign/';
						if(Storage::exists($pathCampaignStorage.@$data->image_poster)) {
							return asset('project/storage/app/public/upload/campaign/'.@$data->image_poster);
						} else { return ''; }
                	} else { return ''; }
                })
                ->addColumn('attachment_path', function($data) {
                	if($data->attachment){
                		$pathCampaignStorage = 'public/upload/campaign/';
						if(Storage::exists($pathCampaignStorage.@$data->attachment)) {
							return asset('project/storage/app/public/upload/campaign/'.@$data->attachment);
						} else { return ''; }
                	} else { return ''; }
                })
                ->addColumn('action', function($data) {
                    return '';
                })
                ->make(true);
        }
        return view('employee.employee_setting.company_campaign.index', compact('reffNumber', 'idEmployee', 'findEmployee'));
    }

    public function edit(Request $request) {
		try{
			DB::beginTransaction();

			$idCompanyCampaign = $request->id_company_campaign ?? null ;
            $pathCampaignStorage = 'public/upload/campaign/';
            $imgPosterPath = null;
            $attachmentPath = null;
        	$getCampaign = HrCompanyCampaign::where('id_company_campaign', $idCompanyCampaign)->first();
        	if(!$getCampaign){
	            throw new \Exception('Data Campaign not found');           
        	}
    		if($getCampaign->image_poster){
				if(Storage::exists($pathCampaignStorage.@$getCampaign->image_poster)) {
					$imgPosterPath = asset('project/storage/app/public/upload/campaign/'.@$getCampaign->image_poster);
				}
        	}
        	if($getCampaign->attachment){
				if(Storage::exists($pathCampaignStorage.@$getCampaign->attachment)) {
					$attachmentPath = asset('project/storage/app/public/upload/campaign/'.@$getCampaign->attachment);
				}
        	}
        	$getCampaign->image_poster_path = $imgPosterPath;
    		$getCampaign->attachment_path = $attachmentPath;

            DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Success', 'data' => $getCampaign]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Failed [' . $e->getMessage() . ']']);           
        }
    }

    public function store(Request $request) {
		try{
			DB::beginTransaction();
			$idCompanyCampaign = $request->id_company_campaign ?? null ;
            $pathCampaignStorage = 'public/upload/campaign/';

			// === Validate ===
			$arr_msg_form_validate = [];
	        $arr_form_validate = [
	            'reference_number' => 'required',
	            'company' => 'required',
	            'description' => 'required|string',
	            'employee_request' => 'required',
	            'start_date' => 'required|date',
	        ];
            if($request->end_date){
                $arr_form_validate['end_date'] = 'date|after_or_equal:start_date';
            }
            
	        $arr_msg_form_validate['employee_request.required'] = 'Employee is required';

	        if($idCompanyCampaign) {
	            $findCampaign = HrCompanyCampaign::where('id_company_campaign', $idCompanyCampaign)->first();
                if(@$findCampaign->image_poster=='' || (@$findCampaign->image_poster!='' && !Storage::exists($pathCampaignStorage.@$findCampaign->image_poster)) || $request->image_poster) {
                	$arr_form_validate['image_poster'] = 'required|mimes:jpg,jpeg,png,svg|max:5000';
        			$arr_msg_form_validate['image_poster.mimes'] = 'Poster type must be : .jpg/.jpeg/.png/.svg';
                }
	        } else {
	            $arr_form_validate['image_poster'] = 'required|mimes:jpg,jpeg,png,svg|max:5000';
	        	$arr_msg_form_validate['image_poster.mimes'] = 'Poster type must be : .jpg/.jpeg/.png/.svg';
	        }

	        if ($request->attachment) {
	            $arr_form_validate['attachment'] = 'mimes:jpg,jpeg,png,svg|max:5000';
	            $arr_msg_form_validate['attachment.mimes'] = 'Image type must be : .jpg/.jpeg/.png/.svg';
	        }
			$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
	        $request->validate($arr_form_validate, $arr_msg_form_validate);
	        // === End Validate ===

			$referenceNumber = $request->reference_number ?? null ;
            $company = $request->company ?? null ;
            $description = $request->description ?? null ;
            $employeeRequest = $request->employee_request ?? null ;
            $start = $request->start_date ?? null ;
            $end = $request->end_date ?? null ;
            $link = $request->link ?? null ;

            $storagePhoto = storage_path('app/'.$pathCampaignStorage);
            !file_exists($storagePhoto) && mkdir($storagePhoto, 0777, true);

            $imgPoster = null;
            $attachment = null;
            $getCampaign = null;
        	if($idCompanyCampaign){
            	$getCampaign = HrCompanyCampaign::where('id_company_campaign', $idCompanyCampaign)->first();
            }

            if($request->image_poster){
                $extension = $request->image_poster->extension();
                $imageName = Str::random(5).'_'.date('YmdHis').'.'.$extension;
                $imgPoster = $imageName;
                // $this->resizeImage($request->image_poster, $pathCampaignStorage.$imageName); 
                Storage::putFileAs($pathCampaignStorage, $request->image_poster, $imageName);

            	if($idCompanyCampaign){
	                if(Storage::exists($pathCampaignStorage.@$getCampaign->image_poster)) {
	                    //jika ganti foto dan foto sebelumnya masih ada maka hapus foto sebelumnya di storage
	                    Storage::delete($pathCampaignStorage.@$getCampaign->image_poster);
	                }
	            }
            }
            if($request->attachment){
                $extension = $request->attachment->extension();
                $imageName2 = Str::random(5).'_'.date('YmdHis').'.'.$extension;
                $attachment = $imageName2;
                // $this->resizeImage($request->attachment, $pathCampaignStorage.$imageName2); 
                Storage::putFileAs($pathCampaignStorage, $request->attachment, $imageName2);

            	if($idCompanyCampaign){
	                if(Storage::exists($pathCampaignStorage.@$getCampaign->attachment)) {
	                    Storage::delete($pathCampaignStorage.@$getCampaign->attachment);
	                }
	            }
            }

            $dataCampaign = [
                'reference_number'    	=> $referenceNumber,
                'description'         	=> $description,
                'id_employee_request' 	=> $employeeRequest,
                'start_date'			=> $start,
                'end_date'            	=> $end,
                'link'                	=> $link,
                'id_company'          	=> $company,
            ];

            if(!$idCompanyCampaign){
                if($imgPoster){ $dataCampaign['image_poster'] = $imgPoster; }
                if($attachment){ $dataCampaign['attachment'] = $attachment; }
                $dataCampaign['created_by'] = session('id_user');

                $storeCampaign = HrCompanyCampaign::insert($dataCampaign);
            } else {
                if($imgPoster){ $dataCampaign['image_poster'] = $imgPoster; }
                if($attachment){ $dataCampaign['attachment'] = $attachment; }
                $dataCampaign['updated_by'] = session('id_user');

                $storeCampaign = HrCompanyCampaign::where('id_company_campaign', $idCompanyCampaign)->update($dataCampaign);
            }

            DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Campaign Saved Successfully']);
        } catch (ValidationException $e){
            DB::rollback();
			return response()->json(['status' => 'false', 'message' => $e->validator->errors()]);           
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Campaign [' . $e->getMessage() . ']']);           
        }
    }

	public function delete(Request $request) {
		try{
			DB::beginTransaction();

			$idCompanyCampaign = $request->id_company_campaign ?? null ;
            $pathCampaignStorage = 'public/upload/campaign/';
        	$getCampaign = HrCompanyCampaign::where('id_company_campaign', $idCompanyCampaign)->first();
        	if(!$getCampaign){
	            throw new \Exception('Data Campaign not found');           
        	}
    		if($getCampaign->image_poster){
				if(Storage::exists($pathCampaignStorage.@$getCampaign->image_poster)) {
	                Storage::delete($pathCampaignStorage.@$getCampaign->image_poster);
				}
        	}
        	if($getCampaign->attachment){
				if(Storage::exists($pathCampaignStorage.@$getCampaign->attachment)) {
	                Storage::delete($pathCampaignStorage.@$getCampaign->attachment);
				}
        	}
        	$delCampaign = HrCompanyCampaign::where('id_company_campaign', $idCompanyCampaign)->delete();

            DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Delete Success', 'data' => $delCampaign]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Failed [' . $e->getMessage() . ']']);      
        }
    }
}
