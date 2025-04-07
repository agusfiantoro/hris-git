<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\EmailController;
use App\Models\Employee\EmployeeRequest\RequestHeader;
use App\Models\Employee\EmployeeRequest\RequestDetail;
use App\Models\Employee\EmployeeRequest\ApprovalDelegation;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\Recruitment\HiringRequest\HiringRequest;
use App\Models\API\Employee;
use App\Models\API\OfficialTravel;
use App\Models\CashAdvance\OfficialTravel\HrApprovalTransaction;
use App\Models\CashAdvance\OfficialTravel\HrExpenseRequest;
use App\Models\CashAdvance\OfficialTravel\HrOfficialTravel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;
use stdClass;

class OfficialTravelController extends BaseController
{
    protected function getTravelRules($type) {
        $rules = new stdClass;
        $rules->manual_approval = false;
        $rules->min_cash_advance = 7;
        if($type == "Travel"){
            $rules->min_cash_advance = 7;
        } elseif($type == "Mutation") {
            $rules->min_cash_advance = 2;
        } elseif($type == "Project") {
            $rules->min_cash_advance = 7;
            $rules->manual_approval = true;
        }
        return $rules;
    }

    public function index(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $request->validate([
                'nik' => 'required|exists:hr_employee,nik_employee',
                'id_company' => 'nullable|exists:master_company,id_company',
                'start_date' => 'nullable',
                'end_date' => 'nullable',
                'reason_code' => 'nullable',
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $idCompany = $employee->id_company;
            $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s') ?? now()->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s') ?? now()->addDays(7)->endOfDay()->format('Y-m-d H:i:s');
            $data = OfficialTravel::getOfficialTravel($idCompany, $employee->id_user, $startDate, $endDate, $request->reason_code);
            return $this->mobileSuccess('Data was sent successfully!', $data);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function detail(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $request->validate([
                'nik' => 'required|exists:hr_employee,nik_employee',
                'id_company' => 'nullable|exists:master_company,id_company',
                'id_official_travel' => 'required|exists:hr_official_travel,id_official_travel'
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $idCompany = $employee->id_company;
            $data = [
                "official_travel" => OfficialTravel::getData($request->id_official_travel, $idCompany)[0],
                "transport" => OfficialTravel::getDataTransport($request->id_official_travel, $idCompany),
                "accomodation" => OfficialTravel::getDataAccommodation($request->id_official_travel, $idCompany),
                "cash_advance" => OfficialTravel::getCashAdvance($request->id_official_travel, $idCompany)
            ];
            return $this->mobileSuccess('Data was sent successfully!', $data);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function getFormData(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $request->validate([
                'nik' => 'required',
                'id_company' => 'nullable',
                'type' => 'nullable'
            ]);
            $type = $request->type;
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $idUser = $employee->id_user;
            $idEmployee = $employee->id_employee;
            $idCompany = $employee->id_company;
            
            $canSubmit = HrOfficialTravel::getCashAdvanceLock($idUser);
            if(!$canSubmit) {
                throw new \Exception('Official Travel tidak tersedia karena anda memiliki settlement yang belum clear.', 422);
            }

            $types = OfficialTravel::getTravelTypes($idCompany);
            foreach($types as $type) {
                $type->validation_rules = $this->getTravelRules($type->code_travel);
            }
            $data = [
                "travel_types" => $types,
                "max_official_travel_request" => OfficialTravel::maximumOfficialTravelRequest($idCompany)->maximum_official_travel_request,
                "transport_products" => OfficialTravel::getProductTransport($idCompany),
                "accommodation_products" => OfficialTravel::getProductAkomodasi($idCompany),
                "transport_branches" => OfficialTravel::getBranchTransport($idCompany),
                "approval" => OfficialTravel::getApprovalBy($idEmployee, $idUser, $idCompany),
                "cash_advance_products" => OfficialTravel::getProductCashAdvance($idCompany),
            ];
            return $this->mobileSuccess('Data was sent successfully!', $data);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function getRegions(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $request->validate([
                'nik' => 'required',
                'id_company' => 'nullable',
                'code_product' => 'required'
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $idUser = $employee->id_user;
            $idCompany = $employee->id_company;

            $regions = HrOfficialTravel::get_region_destination($request, $idUser, $idCompany);
            return $this->mobileSuccess('Data was sent successfully!', $regions);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function getBranches(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $request->validate([
                'nik' => 'required',
                'id_company' => 'nullable',
                'id_region' => 'nullable',
                'code_product' => 'nullable'
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $idUser = $employee->id_user;
            $idCompany = $employee->id_company;
            // dd($idUser, $idEmployee);

            if($request->code_product) {
                if($request->id_region) {
                    $branches = HrOfficialTravel::get_branch_destination($request, $idUser, $idCompany);
                } else {
                    throw new \Exception('ID Region harus ada saat code_product diisi!', 422);
                }
            } else {
                $branches = DB::table('master_branch')->where('id_company', $idCompany)->where('status', 'A');
                if($request->id_region) {
                    $branches->where('id_region', $request->id_region);
                }
                $branches = $branches->orderBy('id_region')->orderBy('description')->get(['id_branch as id', 'description as text']);
            }
            return $this->mobileSuccess('Data was sent successfully!', $branches);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function getProjectApproval(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $request->validate([
                'nik' => 'required',
                'id_company' => 'nullable',
                'action' => 'required|in:company,department,position_routing,employee,approved_by',
                'id_department' => 'nullable',
                'id_position_routing' => 'nullable',
                'approved_by' => 'nullable',
                'id_official_travel' => 'nullable'
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $idUser = $employee->id_user;
            $idCompany = $employee->id_company;

            $result = null;
            if($request->action == 'company') {
                $result = DB::table('master_company')->where('company_type', 'corporate')->where('id_company', $idCompany)->orderBy('id_company')->get(['id_company as id', 'company_name as text']);
            } elseif($request->action == 'department') {
                $result = DB::table('master_department')->where('id_company', $request->id_company ?? $idCompany)->get(['id_dept as id', 'description as text']);
            } elseif($request->action == 'position_routing' && $request->id_department) {
                $sql = "SELECT
                            mpr.id_routing as id,
                            mpr.description as text,
                            mpr.job_description_detail 
                        FROM master_job_position mjp
                        JOIN master_position_routing mpr ON mjp.id_position = mpr.id_position
                        WHERE mjp.id_dept = ? AND mpr.status = 'A'
                        ";
                $result = DB::select($sql, [$request->id_department]);
            } elseif($request->action == "employee" && $request->id_position_routing) {
                $sql = "SELECT 
                            hre.id_employee as id,
                            hre.name as text
                        FROM master_position_detail mpd 
                        JOIN hr_employee hre ON mpd.id_employee = hre.id_employee
                        WHERE mpd.id_position_routing = ? 
                        AND mpd.status = 'A' 
                        AND hre.status = 'A' 
                        AND hre.id_user != ?
                        ";
                $result = DB::select($sql, [$request->id_position_routing, $idUser]);
            } elseif($request->action == "approved_by" && $request->id_official_travel) {
                $sql = "SELECT 
                            hrot.id_approval_request as id,
                            hre.name as text
                        FROM hr_official_travel hrot
                        JOIN hr_employee hre ON hrot.id_approval_request = hre.id_employee
                        WHERE id_official_travel = ?
                        ";
                $result = DB::select($sql, [$request->id_official_travel]);
            }
        
            return $this->mobileSuccess('Data was sent successfully!', $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }

    }

    public function submit(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            // dd($request->all());
            $validationRules = [
                'nik' => 'required|exists:hr_employee,nik_employee',
                'id_company' => 'nullable|exists:master_company,id_company',
                // 'id_position_detail' => 'required',
                'start_end' => 'required',
                'location_to' => 'required|max:255',
                'id_reason_group' => 'required',
                'reason_notes' => 'required',
                'id_approval' => 'required',
                'id_approval_request' => 'required',
                'with_cashadvance' => 'required|boolean',
                'with_accommodation' => 'required|boolean',
                'with_transport' => 'required|boolean',
                'unlock_gps' => 'required|boolean',
            ];

            if ($request->with_cashadvance == true) {
                $validationRules += [
                    'cashadvance.*.nama' => 'required',
                    'cashadvance.*.notes_cashadvance' => 'required',
                    'cashadvance.*.tanggal' => 'required',
                    'cashadvance.*.unit_price' => 'required',
                    'cashadvance.*.qty_cashadvance' => 'required',
                    // 'cashadvance.*.total' => 'required',
                ];
                // if (!empty($request->cashadvance)) {
                //     foreach ($request->cashadvance as $key => $valuec) {
                //         if(isset($valuec['nama'])){
                //             $cek_cashadvance = "SELECT code FROM inventory.master_product as mp WHERE id_product='".$valuec['nama']."'";
                //             $cek_cashadvance = DB::select($cek_cashadvance);
                //             if ($cek_cashadvance[0]->code == 'CSM0000002' OR $cek_cashadvance[0]->code == 'CSM0000006') {
                //                 $validationRules += [
                //                     'cashadvance.*.total' => 'required'
                //                 ];
                //             }
                //         }
                //     }
                // }
            }

            if ($request->with_transport == true) {
                $validationRules += [
                    'transport.*.jenis_transportasi' => 'required',
                    'transport.*.transport_name' => 'required',
                    'transport.*.from' => 'required',
                    'transport.*.to' => 'required',
                    'transport.*.date_transport' => 'required',
                    'transport.*.time_transport' => 'required',
                    'transport.*.branch' => 'required',
                ];
            }
            if ($request->with_accommodation == true) {
                $validationRules += [
                    'akomodasi.*.product_akomodasi' => 'required',
                    'akomodasi.*.nama_hotel' => 'required',
                    'akomodasi.*.city' => 'required',
                    'akomodasi.*.branch' => 'required',
                    'akomodasi.*.start_end_akomodasi' => 'required'
                ];
            }
            $request->validate($validationRules);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1){
                throw new \Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $idUser = $employee->id_user;
            $idEmployee = $employee->id_employee;
            $idCompany = $employee->id_company;

            // $canSubmit = HrOfficialTravel::getCashAdvanceLock($idUser);
            // if(!$canSubmit) {
            //     throw new \Exception('Official Travel tidak tersedia karena anda memiliki settlement yang belum clear.', 422);
            // }

            $isDirector = HrOfficialTravel::getDirector("he.id_employee id, he.name text, mu.id_user as id_user, mpr.id_job_grade, mpd.id_position_detail", $idUser);
		    $autoApproveTarget = HrOfficialTravel::getDirectorOfficialTravel("hah.id_approval id, hah.description text, hah.id_job_grade, hah.is_auto_approved", $idCompany);
		    $autoApprove = $isDirector && $autoApproveTarget && $isDirector->id_job_grade == $autoApproveTarget->id_job_grade;

            $userPosition = OfficialTravel::getUser($idUser, $idCompany);

            $company = DB::selectOne('SELECT * FROM master_company WHERE id_company = ?', [$idCompany]);

            $result = OfficialTravel::formatSave($request->start_end, $userPosition, $idCompany);

            $start_date = $result['start_date'];
			$end_date = $result['end_date'];
			$format = $result['format'];
            $unlock_gps = $request->unlock_gps;
            $with_caseadvance = $request->with_cashadvance;
			if ($autoApprove || $idEmployee == $request->id_approval_request) {
				$cek_status = DB::table('master_general_data')
				->select(
					DB::RAW('id_general_data')
				)
				->where('id_company',$idCompany)
				->where('status','A')
				->where('description','Approved')
				->first();
				$id_approval_status = $cek_status->id_general_data;
			}else{
				$id_approval_status = $result['id_approval_status_transaction'];;
			}

            $data = new OfficialTravel();
			$data -> reference_number = $format;
			$data -> request_by = $idEmployee;
			$data -> id_position_detail = $userPosition->id_position_detail;
			$data -> start_date = $start_date.' '.date('H:i:s');
			$data -> end_date = $end_date.' '.date('H:i:s');
			$data -> location_to = strtoupper($request->location_to);
			$data -> id_reason_group = $request->id_reason_group;
			$data -> reason_notes = strtoupper($request->reason_notes);
			$data -> unlock_gps = $unlock_gps;
			$data -> is_have_cash_advance = $with_caseadvance;
			$data -> id_approval = $request->id_approval;
			$data -> id_approval_request = $request->id_approval_request;
			$data -> id_approval_status = $id_approval_status;
			$data -> status = 'A';
			$data -> id_company = $idCompany;
			$data -> created_by = $idUser;
			$data -> letter_date = date('Y-m-d');
			$data -> save();

            $sql = "SELECT id_general_data, code 
					FROM master_general_data
					WHERE code = 'Project' AND id_company = ?";
			$reason = DB::selectOne($sql, [$idCompany]);

            $jobGrade = HrOfficialTravel::getJobGrade($idUser, 1);

            $transaction_function = OfficialTravel::formatSaveToTransaction($idEmployee, $idCompany);
			$transaction = new HrApprovalTransaction();
			$transaction -> id_source_transaction = $data->id_official_travel;
            if($jobGrade == null && $idEmployee != $request->id_approval_request) {
				$transaction -> source_transaction_type = $transaction_function[0]->code;
				$transaction -> id_approval = $transaction_function[0]->id_approval;
				$transaction -> id_approval_mode = $transaction_function[0]->id_approval_mode;
				$transaction -> sequence = $transaction_function[0]->sequence;
				if($reason && $data->id_reason_group == $reason->id_general_data) {
					// If reason group == Project
					$transaction -> id_position_detail = $data->id_position_detail;
					$transaction -> id_employee_approval = $data->id_approval_request;
				} else {
					$transaction -> id_position_detail = $transaction_function[0]->id_detail_chief;
					$transaction -> id_employee_approval = $transaction_function[0]->id_employee_approval;
				}
			} else {
				// DIRECTOR
				$id_approval_mode = DB::selectOne("SELECT mgd.id_general_data FROM master_general_type mgt 
				JOIN master_general_data mgd ON mgt.id_general_type = mgd.id_general_type and mgd.id_company = ".$idCompany."
				WHERE mgt.id_company = ".$idCompany." AND mgt.general_type = 'master_approval_mode' and mgd.code = 'AND'");
				$transaction -> source_transaction_type = 'Official_Travel';
				$transaction -> id_approval = $autoApproveTarget->id;
				$transaction -> id_approval_mode = $id_approval_mode->id_general_data;
				$transaction -> sequence = 1;
				
				$transaction -> id_position_detail = $data->id_position_detail;
				$transaction -> id_employee_approval = $idEmployee;
				
			}
			$transaction -> id_approval_status = $id_approval_status;
			$transaction -> id_company = $idCompany;
			$transaction -> created_by = $idUser;
			$transaction -> save();

            $data->id_currency_cash_advance = $company->id_currency;
			$data->currency_rate_cash_advance = 1;

			if(isset($request->with_cashadvance) || isset($request->with_transport) || isset($request->with_accommodation)) {
				// $data->id_currency_cash_advance = $company->id_currency;
				// $data->currency_rate_cash_advance = 1;
				$hr_cashadvance = OfficialTravel::saveCashAdvanceToOfficialTravel($data, $idUser, $idCompany);
			}

			if ($request->with_cashadvance) {
				if (isset($request->cashadvance)) {
				//	$hr_cashadvance = HrCashAdvance::save_cash_advance_to_official_travel($data);
					foreach ($request->cashadvance as $key => $valuec) {
						$idUom = HrOfficialTravel::get_uom($valuec['nama'], $idCompany);
						/*
						$cek_product = HrOfficialTravel::cek_save_product($data,$valuec);
						
						if ($cek_product) {
							$validationRules += [
								'cashadvance.'.$key.'.nama' => 'required|unique:hr_expense_request,id_product'
							];
							$validationMessages += [
								'cashadvance.'.$key.'.nama.unique' => 'The Category has already been taken.'
							];
							$request->validate($validationRules, $validationMessages);
						}
						*/
						$dec_cashadvance = $valuec['tanggal'].';';
						if (empty($valuec['branch_cashadvance']) AND empty($valuec['region_cashadvance'])) {
							$notes_cashadvance = strtoupper($valuec['notes_cashadvance']).';';
						}else{
							$notes_cashadvance = strtoupper($valuec['notes_cashadvance']).';'.$valuec['region_cashadvance'].';'.$valuec['branch_cashadvance'];
						}
						if ($valuec['unit_price'] != '') {
							$unit_price = preg_replace("/[^aZ0-9]/", "", $valuec['unit_price']);
						}else{
							$unit_price = NULL;
						}
						// if ($valuec['total'] != "") {
						// 	$total_amount = preg_replace("/[^aZ0-9]/", "", $valuec['total']);
						// }else{
						// 	$total_amount = NULL;
						// }
						$expense = New HrExpenseRequest();
						$expense -> id_cash_advance = $hr_cashadvance->id_cash_advance;
						$expense -> id_product = $valuec['nama'];
						$expense -> id_uom = $idUom->id_uom;
						$expense -> description = $dec_cashadvance;
						$expense -> notes = $notes_cashadvance;
						if ($valuec['qty_cashadvance'] != '') {
							$expense -> qty = $valuec['qty_cashadvance'];
						}
						$expense -> unit_price = $unit_price;
					//	$expense -> total_amount = $total_amount;
						$expense -> id_currency = $company->id_currency;
                        $expense -> currency_rate = 1;
						$expense -> status = 'A';
						$expense -> id_company = $idCompany;
						$expense -> created_by = $idUser;
						$expense -> save();
						// $hr_cashadvance->id_currency_cash_advance = $expense->id_currency;
						// $hr_cashadvance->currency_rate_cash_advance = 1;
						// $hr_cashadvance->save();
					}
				}else{
                    throw new \Exception('Tambahkan setidaknya 1 detail cash advance.', 422);
				}
			}
            if ($request->with_transport) {
				if (isset($request->transport)) {
				//	$hr_cashadvance = HrCashAdvance::save_cash_advance_to_official_travel($data);
					foreach ($request->transport as $key => $value) {
						$idUom = HrOfficialTravel::get_uom($value['jenis_transportasi'], $idCompany);
						$dec_transport = $value['date_transport'].';'.$value['time_transport'].';';
						$notes_transport = $value['transport_name'].';'.strtoupper($value['from']).';'.strtoupper($value['to']).';';
					/*	if (empty($value['branch'])) {
							$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';';
						}else{
							$notes_transport = $value['transport_name'].';'.$value['from'].';'.$value['to'].';'.$value['branch'].';';
						}
					*/
						$expense = New HrExpenseRequest();
						$expense -> id_cash_advance = $hr_cashadvance->id_cash_advance;
						$expense -> id_product = $value['jenis_transportasi'];
						$expense -> id_uom = $idUom->id_uom;
						$expense -> id_branch = $value['branch'];
						$expense -> description = $dec_transport;
						$expense -> notes = $notes_transport;
						$expense -> qty = 1;
						$expense -> unit_price = 0;
						$expense -> id_currency = $company->id_currency;
                        $expense -> currency_rate = 1;
						$expense -> status = 'A';
						$expense -> id_company = $idCompany;
						$expense -> created_by = $idUser;
						$expense -> save();
						// $hr_cashadvance->id_currency_cash_advance = $expense->id_currency;
						// $hr_cashadvance->currency_rate_cash_advance = 1;
						// $hr_cashadvance->save();
					}
				}else{
                    throw new \Exception('Tambahkan setidaknya 1 detail transport.', 422);
				}
			}
            if ($request->with_accommodation) {
				if (isset($request->akomodasi)) {
				//	$hr_cashadvance = HrCashAdvance::save_cash_advance_to_official_travel($data);
					foreach ($request->akomodasi as $key => $values) {
						$idUom = HrOfficialTravel::get_uom($values['product_akomodasi'], $idCompany);
						$dec_akomodasi = strtolower($values['start_end_akomodasi']).';';
						$notes_akomodasi = strtoupper($values['nama_hotel']).';'.strtoupper($values['city']).';';
						// $qty = $values['lama_menginap'];
						$qty = $values['lama_menginap'];
						$expense = New HrExpenseRequest();
						$expense -> id_cash_advance = $hr_cashadvance->id_cash_advance;
						$expense -> id_product = $values['product_akomodasi'];
						$expense -> id_uom = $idUom->id_uom;
						$expense -> description = $dec_akomodasi;
						$expense -> notes = $notes_akomodasi;
						$expense -> id_branch = $values['branch'];
						$expense -> qty = $qty;
						$expense -> unit_price = 0;
						$expense -> id_currency = $company->id_currency;
                        $expense -> currency_rate = 1;
						$expense -> status = 'A';
						$expense -> id_company = $idCompany;
						$expense -> created_by = $idUser;
						$expense -> save();
					}
				}else{
                    throw new \Exception('Tambahkan setidaknya 1 detail akomodasi.', 422);
				}
			}
            DB::commit();
            return $this->mobileSuccess('Data was sent successfully!', $data);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }
}