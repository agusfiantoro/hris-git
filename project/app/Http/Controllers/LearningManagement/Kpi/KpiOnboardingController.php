<?php

namespace App\Http\Controllers\LearningManagement\Kpi;

use App\Models\LearningManagement\Kpi\HrKpiGroup;
use App\Models\LearningManagement\Kpi\HrKpiHeader;
use App\Models\LearningManagement\Kpi\HrKpiDetail;
use App\Models\LearningManagement\Kpi\MasterKpi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DataTables;
use Validator;

class KpiOnboardingController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = HrKpiGroup::get_data();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        return $data->id_kpi_group;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('learning_management.kpi.onboarding.index');
    }

    protected function validate_form(Request $request) {
        $arr_form_validate = [
            'id_employee'           => 'required|string',
            // 'average_prosentase'    => 'required|string',
            'description'           => 'required|string',
            // 'notes'                 => 'required|string',
            'start_date'            => 'required|date_format:Y-m-d',
            'end_date'              => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'status'                => 'required|string',
            'process_type'          => 'required|string',
        ];
        $arr_msg_form_validate = [
            'id_employee.required'              => 'The Employee field is required',
            'average_prosentase.required'       => 'The Average Prosesentase field is required',
            'description.required'              => 'The Description field is required',
            // 'notes.required'                    => 'The Notes field is required',
            'start_date.required'               => 'The Start Date field is required',
            'end_date.required'                 => 'The End Date field is required',
            'status.required'                   => 'The Status field is required',
        ];
        if($request->summary){
            $arr_form_validate['summary.*.kpi_month']           = 'required|string';
            // $arr_form_validate['summary.*.notes']               = 'required|string';
            // $arr_form_validate['summary.*.subtotal_kpi']        = 'required|string';
            $arr_form_validate['summary.*.status']              = 'required|string';
            $arr_msg_form_validate['summary.*.kpi_month.required']          = 'Kpi Month field is required';
            // $arr_msg_form_validate['summary.*.notes.required']              = 'Notes field is required';
            $arr_msg_form_validate['summary.*.subtotal_kpi.required']       = 'Subtotal field is required';
            $arr_msg_form_validate['summary.*.status.required']             = 'Status field is required';
        }
        if($request->detail){
            $arr_form_validate['detail.*.kpi_month']           = 'required|string';
            $arr_form_validate['detail.*.id_kpi_category']     = 'required|string';
            $arr_form_validate['detail.*.id_kpi_type']         = 'required|string';
            // $arr_form_validate['detail.*.kpi_value']           = 'required|string';
            $arr_form_validate['detail.*.id_course_header']    = 'required|string';
            $arr_form_validate['detail.*.status']              = 'required|string';
            $arr_msg_form_validate['detail.*.kpi_month.required']           = 'Kpi Month field is required';
            $arr_msg_form_validate['detail.*.id_kpi_category.required']     = 'KPI Category field is required';
            $arr_msg_form_validate['detail.*.id_kpi_type.required']         = 'KPI Type field is required';
            $arr_msg_form_validate['detail.*.kpi_value.required']           = 'KPI Value field is required';
            $arr_msg_form_validate['detail.*.id_course_header.required']    = 'Course field is required';
            $arr_msg_form_validate['detail.*.status.required']              = 'Status field is required';
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function validate_summary(Request $request) {
        $arr_form_validate = [
            'process_type' => 'required|string',
            'id_employee' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'status' => 'required|string',
        ];
        $arr_msg_form_validate = [
            'kpi_month.required'            => 'The Kpi Month field is required',
            'notes.required'                => 'The Notes field is required',
            'subtotal_kpi.required'         => 'The Subtotal field is required',
            'status.required'               => 'The Status field is required',
        ];
        if($request->summary){
            $arr_form_validate['summary.*.kpi_month']           = 'required|string';
            // $arr_form_validate['summary.*.notes']               = 'required|string';
            // $arr_form_validate['summary.*.subtotal_kpi']        = 'required|string';
            $arr_form_validate['summary.*.status']              = 'required|string';
            $arr_msg_form_validate['summary.*.kpi_month.required']          = 'Kpi Month field is required';
            $arr_msg_form_validate['summary.*.notes.required']              = 'Notes field is required';
            $arr_msg_form_validate['summary.*.subtotal_kpi.required']       = 'Subtotal field is required';
            $arr_msg_form_validate['summary.*.status.required']             = 'Status field is required';
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        // var_dump($request->all());die;
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'process_type'          => $request->process_type,
                'id_employee'           => $request->id_employee,
                'average_prosentase'    => $request->average_prosentase ?? null,
                'description'           => $request->description ?? null,
                'notes'                 => $request->notes,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'status'                => $request->status,
                'kpi_class'             => 'SALES',
                'id_company'            => session('id_company'),
            ];
            
            if($request->id_kpi_group){
                $idKpiGroup     = $request->id_kpi_group;
                $form_data['updated_by'] = session('id_user');
                HrKpiGroup::findOrFail($idKpiGroup)->update($form_data);
            } else {
                $form_data['created_by'] = session('id_user');
                $insertKpiGroup = HrKpiGroup::create($form_data);
                $idKpiGroup     = $insertKpiGroup->id_kpi_group;
            }
            $averageProsentase      = [];

            if ($request->summary) {
                foreach ($request->summary as $key => $value) {
                    $form_summary = [
                        'id_kpi_group'          => $idKpiGroup,
                        'kpi_month'             => $value['kpi_month'],
                        'notes'                 => $value['notes'],
                        // 'subtotal_kpi'          => $value['subtotal_kpi'],
                        'status'                => $value['status'],
                        'id_company'            => session('id_company'),
                    ];
                    if($value['id_kpi_header'] == ''){
                        $form_summary['created_by'] = session('id_user');
                        HrKpiHeader::create($form_summary);
                    } else {
                        $form_summary['updated_by'] = session('id_user');
                        HrKpiHeader::findOrFail($value['id_kpi_header'])->update($form_summary);
                    }
                }
            }

            if ($request->detail) {
                $valueByKpiHeader = [];
                foreach ($request->detail as $key => $value) {
                    $thisWeight = $value['weight_prosentase'] ?? 100;
                    $thisKpiValue = $value['kpi_value'] ?? 0;
                    $valueAndWeight = ($thisWeight / 100) * $thisKpiValue;
                    $valueByKpiHeader[$value['kpi_month']][] = $valueAndWeight;

                    $form_detail = [
                        'id_kpi_group'              => $idKpiGroup,
                        'id_kpi_header'             => $value['kpi_month'],
                        'id_kpi_category'           => $value['id_kpi_category'],
                        'id_kpi_type'               => $value['id_kpi_type'],
                        'kpi_value'                 => $value['kpi_value'],
                        'weight_prosentase'         => $value['weight_prosentase'],
                        'id_course_header'          => $value['id_course_header'],
                        'status'                    => $value['status'],
                        'id_company'                => session('id_company'),
                    ];
                    if($value['id_kpi_detail'] == ''){
                        $form_detail['created_by'] = session('id_user');
                        HrKpiDetail::create($form_detail);
                    } else {
                        $form_detail['updated_by'] = session('id_user');
                        HrKpiDetail::findOrFail($value['id_kpi_detail'])->update($form_detail);
                    }
                }
                if(count($valueByKpiHeader) > 0){
                    foreach ($valueByKpiHeader as $k => $val) {
                        $subThisMonth = (count($val) > 0) ? (array_sum($val)/count($val)) : null;
                        HrKpiHeader::findOrFail($k)->update(['subtotal_kpi' => $subThisMonth]);

                        $sumByMonth = is_null($subThisMonth) ? 0 : $subThisMonth;
                        $averageProsentase[] = $sumByMonth;
                    }
                }
            }

            if(count($averageProsentase) > 0){
                $dataGroup['average_prosentase'] = array_sum($averageProsentase) / count($averageProsentase);
                HrKpiGroup::findOrFail($idKpiGroup)->update($dataGroup);
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'KPI Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function update(Request $request) {
        // var_dump($request->all());die;
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $idKpiGroup     = $request->id_kpi_group;
            $form_data = [
                'process_type'          => $request->process_type,
                'id_employee'           => $request->id_employee,
                // 'average_prosentase'    => $request->average_prosentase,
                'description'           => $request->description,
                'notes'                 => $request->notes,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'status'                => $request->status,
                'kpi_class'             => 'SALES',
                'id_company'            => session('id_company'),
                'updated_by'            => session('id_user'),
            ];
            HrKpiGroup::findOrFail($idKpiGroup)->update($form_data);

            $listIdKpiHeader        = [];
            $listIdKpiDetail        = [];
            $idKpiHeader            = [];
            $idKpiDetail            = [];
            $averageProsentase      = [];

            if(HrKpiHeader::where('id_kpi_group', $idKpiGroup)->first() != null){
                $getKpiHeader = HrKpiHeader::where('id_kpi_group', $idKpiGroup)->get();
                $listIdKpiHeader = $getKpiHeader->pluck('id_kpi_header')->all();
                $listIdKpiDetail = HrKpiDetail::whereIn('id_kpi_header', $listIdKpiHeader)->get()->pluck('id_kpi_detail')->all();
            }

            if ($request->summary) {
                foreach ($request->summary as $key => $value) {
                    $form_summary = [
                        'id_kpi_group'          => $idKpiGroup,
                        'kpi_month'             => $value['kpi_month'],
                        'notes'                 => $value['notes'],
                        // 'subtotal_kpi'          => $value['subtotal_kpi'],
                        'status'                => $value['status'],
                        'id_company'            => session('id_company'),
                    ];
                    if($value['id_kpi_header'] == ''){
                        $form_summary['created_by'] = session('id_user');
                        HrKpiHeader::create($form_summary);
                    } else {
                        $idKpiHeader[] = $value['id_kpi_header'];
                        $form_summary['updated_by'] = session('id_user');
                        HrKpiHeader::findOrFail($value['id_kpi_header'])->update($form_summary);
                    }
                }
            }

            if ($request->detail) {
                $valueByKpiHeader = [];
                foreach ($request->detail as $key => $value) {
                    $thisWeight = $value['weight_prosentase'] ?? 100;
                    $thisKpiValue = $value['kpi_value'] ?? null;
                    if(!is_null($thisKpiValue)){
                        $valueAndWeight = ($thisWeight / 100) * $thisKpiValue;
                        $valueByKpiHeader[$value['kpi_month']][] = $valueAndWeight;
                    }

                    $form_detail = [
                        'id_kpi_group'              => $idKpiGroup,
                        'id_kpi_header'             => $value['kpi_month'],
                        'id_kpi_category'           => $value['id_kpi_category'],
                        'id_kpi_type'               => $value['id_kpi_type'],
                        'kpi_value'                 => $value['kpi_value'],
                        'weight_prosentase'         => $value['weight_prosentase'],
                        'id_course_header'          => $value['id_course_header'],
                        'status'                    => $value['status'],
                        'id_company'                => session('id_company'),
                    ];

                    if($value['id_kpi_detail'] == ''){
                        $form_detail['created_by'] = session('id_user');
                        HrKpiDetail::create($form_detail);
                    } else {
                        $idKpiDetail[] = $value['id_kpi_detail'];
                        $form_detail['updated_by'] = session('id_user');
                        HrKpiDetail::findOrFail($value['id_kpi_detail'])->update($form_detail);
                    }
                }
                if(count($valueByKpiHeader) > 0){
                    foreach ($valueByKpiHeader as $k => $val) {
                        $subThisMonth = (count($val) > 0) ? array_sum($val) : null;
                        HrKpiHeader::findOrFail($k)->update(['subtotal_kpi' => $subThisMonth]);
                        if(!is_null($subThisMonth)){
                            $sumByMonth = is_null($subThisMonth) ? 0 : $subThisMonth;
                            $averageProsentase[] = $sumByMonth;
                        }
                    }
                }
            }

            if(count($averageProsentase) > 0){
                $dataGroup['average_prosentase'] = array_sum($averageProsentase) / count($averageProsentase);
                HrKpiGroup::findOrFail($idKpiGroup)->update($dataGroup);
            }

            $diffSummary = array_diff($listIdKpiHeader, $idKpiHeader);
            if(count($diffSummary) > 0){
                foreach ($diffSummary as $item) { 
                    HrKpiHeader::where('id_kpi_header', $item)->delete();
                }
            }

            $diffDetail = array_diff($listIdKpiDetail, $idKpiDetail);
            if(count($diffDetail) > 0){
                foreach ($diffDetail as $item) { 
                    HrKpiDetail::where('id_kpi_detail', $item)->delete();
                }
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'KPI Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function save_summary(Request $request) {
        $this->validate_summary($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'process_type'          => $request->process_type,
                'id_employee'           => $request->id_employee,
                'average_prosentase'    => $request->average_prosentase,
                'description'           => $request->description,
                'notes'                 => $request->notes,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'status'                => $request->status,
                'id_company'            => session('id_company'),
                'created_by'            => session('id_user'),
            ];

            if($request->id_kpi_group){
                $idKpiGroup     = $request->id_kpi_group;
            } else {
                $insertKpiGroup = HrKpiGroup::create($form_data);
                $idKpiGroup     = $insertKpiGroup->id_kpi_group;
            }
            $counter        = $request->counter;

            $form_summary = [
                'id_kpi_group'          => $idKpiGroup,
                'kpi_month'             => $request->summary[$counter]['kpi_month'],
                'notes'                 => $request->summary[$counter]['notes'],
                'subtotal_kpi'          => $request->summary[$counter]['subtotal_kpi'],
                'status'                => $request->summary[$counter]['status'],
                'id_company'            => session('id_company'),
            ];
            if($request->id_kpi_header){
                $form_summary['updated_by'] = session('id_user');
                HrKpiHeader::findOrFail($request->id_kpi_header)->update($form_summary);
                $idKpiHeader = $request->id_kpi_header;
            } else {
                $form_summary['created_by'] = session('id_user');
                $insertHeader = HrKpiHeader::create($form_summary);
                $idKpiHeader = $insertHeader->id_kpi_header;
            }
            $resp['global_id_kpi_group']        = $idKpiGroup;
            $resp['id_kpi_header']              = $idKpiHeader;
            $resp['global_select_month']        = HrKpiHeader::get_select_header($idKpiGroup);

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'KPI Summary Saved Successfully !!', 'data' => $resp]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy_summary(Request $request) {
        DB::beginTransaction();
        try {
            $idKpiHeader        = $request->id_kpi_header;
            $idKpiGroup         = $request->id_kpi_group;
            
            if(HrKpiDetail::where('id_kpi_header', $idKpiHeader)->first() != null){
                throw new \Exception('Terdapat data KPI Detail pada Summary ini');
            }
            $summary              = HrKpiHeader::where('id_kpi_header', $idKpiHeader)->delete();
            $resp['global_select_month']      = HrKpiHeader::get_select_header($idKpiGroup);

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'KPI Summary Deleted Successfully !!', 'data' => $resp]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request) {
        DB::beginTransaction();
        try {
            $id_kpi_group   = $request->id_kpi_group;
            if(HrKpiHeader::where('id_kpi_group', $id_kpi_group)->first() != null){
                foreach (HrKpiHeader::where('id_kpi_group', $id_kpi_group)->get() as $key => $item) {
                    HrKpiDetail::where('id_kpi_header', $item->id_kpi_header)->delete();
                }
            }
            HrKpiHeader::where('id_kpi_group', $id_kpi_group)->delete();
            HrKpiGroup::where('id_kpi_group', $id_kpi_group)->delete();
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'KPI Deleted Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function get_kpi_edit(Request $request) {
        $data = [
            'id_kpi_group'   => $request->id_kpi_group,
            'id_company'     => session('id_company'),
        ];
        $result['kpi']          = HrKpiGroup::get_data_kpi($data);
        $result['kpi_month']    = HrKpiHeader::get_select_header($request->id_kpi_group);
        return response()->json($result);
    }

    public function get_employee($id_user=null) {
        $data = DB::table('hr_employee as he')
            ->select('he.id_employee', 'he.name', 'he.nik_employee')
            ->where('he.status', '=', 'A')
            ->where('he.id_company', '=', session('id_company'))
            ->orderBy('he.name');
        if($id_user){
            $data->where('he.id_user', $id_user);
        }
        return response()->json($data->get());
    }

    public function get_employee_attendee(Request $request) {
        $category = $request->category ?? null;

        $data = DB::table('hr_employee as he')
            ->join('hr_event_attendees as hea', function ($join){
                $join->on('hea.booked_by', '=', 'he.id_employee');
                $join->where('hea.status', '!=', 'Cancel');
            });

        if($category){
            if($category=='Onboarding'){
                $data->join('hr_career_transaction as hct', function ($join){
                    $join->on('hct.id_employee', '=', 'he.id_employee');
                    $join->orOn('hct.id_employee2', '=', 'he.id_employee');
                });
                $data->join('master_general_data as mgd', function ($join){
                    $join->on('mgd.id_general_data', '=', 'hct.id_transaction_type');
                    $join->where('mgd.code', '=', 'Join');
                    $join->where('mgd.description', 'LIKE', '%New%');
                });
            } else {
                $data->join('hr_career_transaction as hct', function ($join){
                    $join->on('hct.id_employee', '=', 'he.id_employee');
                    $join->orOn('hct.id_employee2', '=', 'he.id_employee');
                });
                $data->join('master_general_data as mgd', function ($join){
                    $join->on('mgd.id_general_data', '=', 'hct.id_transaction_type');
                    $join->where('mgd.code', '=', 'Acting');
                });
            }
        }

        $data->select('he.id_employee', 'he.name', 'he.nik_employee')
            ->where('he.status', '=', 'A')
            ->where('he.id_company', '=', session('id_company'))
            ->groupBy('he.id_employee', 'he.name', 'he.nik_employee')
            ->orderBy('he.name', 'asc')
            ->orderBy('he.id_employee', 'asc');

        $result = $data->get();
        return response()->json($result);
    }

    public function get_kpi_type() {
        $data = DB::table('master_general_data as mgd')
                ->join('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
                ->select('mgd.id_general_data as id', 'mgd.description as text')
                ->where('mgt.general_type', '=', 'master_kpi_type')
                ->where('mgd.id_company', '=', session('id_company'));
        return response()->json($data->get());
    }
    
    public function get_kpi_category() {
        $result = MasterKpi::get_select_kpi();
        return response()->json($result);
    }

    public function get_kpi_group() {
        $result = HrKpiHeader::get_select_header();
        return response()->json($result);
    }

    public function get_course() {
        $data = DB::table('master_course_header as mch')
                ->select('mch.id_course_header as id', 'mch.course_name as text')
                ->where('mch.id_company', '=', session('id_company'));
        return response()->json($data->get());
    }

    public function get_timezone() {
        $result = HrEventManagement::get_timezone();
        return response()->json($result);
    }

    public function get_checklist() {
        $result = HrEventManagement::get_checklist();
        return response()->json($result);
    }

    public function get_employee_detail($id_employee=null) {
        $result = HrEventManagement::get_employee_detail($id_employee);
        return response()->json($result);
    }

    public function download_attachment($filename) {
        $filePath = 'public/upload/schedule_course/'.$filename;
        if(Storage::disk('local')->exists($filePath)){
            return response()->download(storage_path('app/'.$filePath));
        }
    }

    public function get_user_by_session() {
        $result = HrEventManagement::get_employee(session('id_user'));
        return response()->json($result);
    }

    public function get_course_by_month(Request $request) {
        DB::beginTransaction();
        try {
            $idKpiHeader = $request->id_kpi_header;
            $idEmployee = $request->id_employee;

            $getDataLms = HrKpiHeader::getDataLms($idKpiHeader, $idEmployee);

            DB::commit();
            return response()->json(['status'=>true, 'message'=>'Success', 'data'=>$getDataLms]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' =>false, 'message' => $e->getMessage()]);
        }
    }
}
