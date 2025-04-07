<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Models\API\Employee;
use App\Models\API\OasysIntegrationDetail;
use App\Models\API\OasysIntegrationHeader;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Integration\Bgen\Bgen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class OasysController extends BaseController
{
    public function getData(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $request->validate([
                'nik' => 'required',
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1) {
                throw new Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];

            $branch = DB::table('master_branch')
                ->where('id_company', $employee->id_company)
                ->where('status', 'A')
                ->select('id_branch', 'description', 'branch_code', 'status')
                ->orderBy('description')
                ->get();
            $principals = DB::table('master_principal')
                ->where('id_company', $employee->id_company)
                ->where('status', 'A')
                ->select('id_principal', 'description', 'status')
                ->orderBy('description')
                ->get();
            
            return $this->mobileSuccess("Data has been sent successfully", [
                'branches' => $branch,
                'principals' => $principals
            ]);

            DB::commit();
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

    public function getApproval(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $request->validate([
                'nik' => 'required',
                'id_branch' => 'required',
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1) {
                throw new Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $location = Bgen::oasysGetLocation($employee->id_company, $employee->id_employee)[0]->id_location;
            $bgen = Bgen::oasysGetApproval($location, $employee->id_company, 'Custom');

            return $this->mobileSuccess("Approval data obtained successfully", $bgen);

            DB::commit();
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

    // public function submit(Request $request) {
    //     DB::beginTransaction();
    //     try {
    //         $this->authMobile($request); //required
    //         $request->validate([
    //             'nik' => 'required',
    //             'id_branch' => 'required',
    //             'id_principal' => 'required',
    //             'status' => 'required|in:A,I',
    //             'id_approval' => 'required',
    //         ]);
    //         $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
    //         if(count($employee) < 1) {
    //             throw new Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
    //         }
    //         $employee = $employee[0];
    //         $approvals = DB::table('hr_approval_detail as had')
    //             ->join('master_position_detail as mpd', 'had.id_position_detail', 'mpd.id_position_detail')
    //             ->where('had.id_approval', $request->id_approval)
    //             ->get(['had.*', 'mpd.id_employee as id_employee_approval']);
    //         $approvalStatus = DB::table('master_general_data')->where('code', 'Request_Approval')->where('id_company', $employee->id_company)->where('status', 'A')->first()->id_general_data;
            
    //         $bgen = new Bgen();
    //         $bgen->id_employee = $employee->id_employee;
    //         $bgen->id_branch = $request->id_branch;
    //         $bgen->id_principal = $request->id_principal;
    //         $bgen->is_synchronize_flag = false;
    //         $bgen->id_approval = $approvals[0]->id_approval;
    //         $bgen->id_approval_status = $approvalStatus;
    //         $bgen->status = $request->status;
    //         $bgen->id_company = $employee->id_company;
    //         $bgen->created_by = $employee->id_user;
    //         $bgen->integration_type = 'Biz_Approval';
    //         $bgen->save();

    //         foreach($approvals as $approval) {
    //             $apprTransaction = new ApprovalTransaction();
    //             $apprTransaction->id_source_transaction = $bgen->id_integration_sales_code;
    //             $apprTransaction->source_transaction_type = $bgen->integration_type;
    //             $apprTransaction->id_approval = $approval->id_approval;
    //             $apprTransaction->id_approval_detail = $approval->id_approval_detail;
    //             $apprTransaction->id_approval_mode = $approval->id_approval_mode;
    //             $apprTransaction->sequence = $approval->sequence;
    //             $apprTransaction->id_position_detail = $approval->id_position_detail;
    //             $apprTransaction->id_employee_approval = $approval->id_employee_approval;
    //             $apprTransaction->id_approval_status = $approvalStatus;
    //             $apprTransaction->id_company = $employee->id_company;
    //             $apprTransaction->created_by = $employee->id_user;
    //             $apprTransaction->save();
    //         }

    //         DB::commit();
    //         return $this->mobileSuccess("Berhasil membuat permintaan", $bgen->only('id_employee', 'id_branch', 'id_principal', 'id_approval', 'id_approval_status', 'status', 'id_company', 'integration_type'));
    //     } catch (ValidationException $e){
    //         DB::rollback();
    //         return $this->mobileErrorValidation($e);
    //     } catch (QueryException $e){
    //         DB::rollback();
    //         return $this->mobileErrorQuery($e);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
    //         return $this->mobileError($e);
    //     }
    // }

    public function submit(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $request->validate([
                'nik' => 'required',
                'company_alias_code' => 'required',
                'user_branch_division' => 'required',
                'user_branch_division.*.branch_hris_code' => 'required',
                'user_branch_division.*.division_hris_code' => 'required',
                'status' => 'required|in:A,I', //wip
            ]);
            $idCompany = DB::table('master_company')->where('company_code', $request->company_alias_code)->first();
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $idCompany->id_company);
            if(count($employee) < 1) {
                throw new Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $location = @Bgen::oasysGetLocation($employee->id_company, $employee->id_employee)[0]->id_location;
            if(!$location) {
                throw new Exception("Gagal mengambil lokasi karyawan.");
            }
            $approvals = Bgen::oasysGetApproval($location, $employee->id_company, 'Custom');
            if(count($approvals) < 1) {
                throw new Exception("Gagal mengambil approval.");
            }
            $approvalStatus = DB::table('master_general_data')->where('code', 'Request_Approval')->where('id_company', $employee->id_company)->where('status', 'A')->first()->id_general_data;

            $existingEntryModified = false;
            foreach($request->user_branch_division as $userBranchDivision) {
                foreach($userBranchDivision['division_hris_code'] as $divisionCode) {
                    $integrationDetails = DB::select("SELECT oird.* FROM integration.biz_approval_integration_request_header oirh 
                                                    JOIN integration.biz_approval_integration_request_detail oird ON oirh.id_integration_request_header = oird.id_integration_request_header 
                                                    WHERE oirh.id_employee = ?
                                                    AND oird.id_branch = ?
                                                    AND ? = ANY(oird.id_principal)
                                                    AND oirh.status != ?",
                                                    [$employee->id_employee, (int)$userBranchDivision['branch_hris_code'], (int)$divisionCode, $request->status]);
                    if(count($integrationDetails) > 0) {
                        foreach($integrationDetails as $integrationDetail) {
                            $header = OasysIntegrationHeader::findOrFail($integrationDetail->id_integration_request_header);
                            $header->status = $request->status;
                            $header->save();
                            $existingEntryModified = true;
                        }
                    }
                }
            }
            if($existingEntryModified) {
                DB::commit();
                return $this->mobileSuccess("Berhasil mengubah status integrasi Biz Approval");
            }

            $header = OasysIntegrationHeader::create([
                'id_employee' => $employee->id_employee,
                'transaction_sources' => 'MobileWeb',
                'is_synchronize_flag' => false,
                'id_approval' => $approvals[0]->id_approval,
                'id_approval_status' => $approvalStatus,
                'status' => $request->status,
                'id_company' => $employee->id_company,
                'created_by' => $employee->id_user,
            ]);
            foreach($request->user_branch_division as $branchDivision) {
                $idDivision = [];
                $idBranch = (int)$branchDivision['branch_hris_code'];
                foreach($branchDivision['division_hris_code'] as $division) {
                    $principal = DB::table('public.master_principal')->where('id_principal', $division)->first();
                    if($principal) {
                        $idDivision[] = $principal->id_principal;
                    }
                }
                $detail = OasysIntegrationDetail::create([
                    'id_integration_request_header' => $header->id_integration_request_header,
                    'id_branch' => $idBranch,
                    'id_principal' => "{".implode(",", $idDivision)."}",
                    'id_company' => $header->id_company,
                    'created_by' => $employee->id_user,
                ]);
            }

            foreach($approvals as $approval) {
                $apprTransaction = new ApprovalTransaction();
                $apprTransaction->id_source_transaction = $header->id_integration_request_header;
                $apprTransaction->source_transaction_type = 'Biz_Approval';
                $apprTransaction->id_approval = $approval->id_approval;
                $apprTransaction->id_approval_detail = $approval->id_approval_detail;
                $apprTransaction->id_approval_mode = $approval->id_approval_mode;
                $apprTransaction->sequence = $approval->sequence;
                $apprTransaction->id_position_detail = $approval->id_position_detail;
                $apprTransaction->id_employee_approval = $approval->id_employee_approval;
                $apprTransaction->id_approval_status = $approvalStatus;
                $apprTransaction->id_company = $employee->id_company;
                $apprTransaction->created_by = $employee->id_user;
                $apprTransaction->save();
            }
            DB::commit();
            return $this->mobileSuccess("Berhasil membuat permintaan");
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

    public function getSubmission(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $request->validate([
                'nik' => 'required',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);
            $employee = Employee::getEmployeeDetail($request->nik, null, false, true, $request->id_company);
            if(count($employee) < 1) {
                throw new Exception('Data NIK '.$request->nik.' tidak ditemukan', 404);
            }
            $employee = $employee[0];
            $oasysRequests = OasysIntegrationHeader::where('id_employee', $employee->id_employee)
                            ->join('master_general_data as mgd', 'integration.biz_approval_integration_request_header.id_approval_status', 'mgd.id_general_data');
            if($request->start_date) {
                $oasysRequests->where('biz_approval_integration_request_header.creation_date', '>=', Carbon::parse($request->start_date)->startOfDay());
            }
            if($request->end_date) {
                $oasysRequests->where('biz_approval_integration_request_header.creation_date', '<=', Carbon::parse($request->end_date)->endOfDay());
            }
            $oasysRequests = $oasysRequests
                            ->orderByDesc('biz_approval_integration_request_header.creation_date')
                            ->get([
                                'id_integration_request_header',
                                'id_employee',
                                'transaction_sources',
                                'is_synchronize_flag',
                                'synchronize_message',
                                'mgd.description as document_approval_status',
                                'note_rejected',
                                'note_revised',
                                'biz_approval_integration_request_header.status',
                                'biz_approval_integration_request_header.id_company',
                                'biz_approval_integration_request_header.creation_date'
                            ]);
                            
            foreach($oasysRequests as $oasysRequest) {
                $oasysRequest->approvals;
                $oasysRequest->details;
            }
            DB::commit();
            return $this->mobileSuccess("Data berhasil dikirim", $oasysRequests);
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