<?php

namespace App\Http\Controllers\EventManagement\Event;

use App\Models\EventManagement\HrEventManagement;
use App\Models\EventManagement\HrEventProgram;
use App\Models\EventManagement\HrEventAttendees;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EventManagement\Room\BookingRoomController;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Carbon;
use Validator;

class EventManagementController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $getEmployee = DB::table('hr_employee')->select('id_employee')->where('id_user', session('id_user'))->first();

            $data = HrEventManagement::get_event(@$getEmployee->id_employee);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        $button = '<button type="button" name="edit" id="' . $data->id_event_management . '" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('event_management.event.event_management.index');
    }

    protected function validate_form(Request $request) {
        $arr_form_validate = [
            'description'           => 'required|string',
            'start_date'            => 'required|string',
            'end_date'              => 'required|string',
            'id_timezone'           => 'required|string',
            // 'end_date_registration' => 'required|string',
            'id_event_type'         => 'required|string',
            'organized_by'          => 'required|string',
            'responsible_by'        => 'required|string',
            'venue'                 => 'required|string',
            'status'                => 'required|string',
        ];
        $arr_msg_form_validate = [
            'description.required'           => 'The Event Name field is required',
            'start_date.required'            => 'The Start Date field is required',
            'end_date.required'              => 'The End Date field is required',
            'id_timezone.required'           => 'The Timezone field is required',
            'end_date_registration.required' => 'The End Date Registration field is required',
            'id_event_type.required'         => 'The Type field is required',
            'organized_by.required'          => 'The Organized By field is required',
            'responsible_by.required'        => 'The Responsible field is required',
            'venue.required'                 => 'The Venue field is required',
            'status.required'                => 'The Status field is required',
        ];

        if(@$request->public){
            $arr_form_validate['end_date_registration']  = 'required';
            $arr_msg_form_validate['end_date_registration.required'] = 'End Date Registration field is required';
        }

        if($request->event){
            // $arr_form_validate['event.*.id_course_header']  = 'required|string';
            // $arr_form_validate['event.*.id_checklist']      = 'required|string';
            $arr_form_validate['event.*.description']       = 'required|string';
            $arr_form_validate['event.*.start_date']        = 'required|string';
            $arr_form_validate['event.*.end_date']          = 'required|string';
            $arr_form_validate['event.*.status']            = 'required|string';
            $arr_msg_form_validate['event.*.id_course_header.required'] = 'Course Name field is required';
            $arr_msg_form_validate['event.*.description.required']      = 'Description field is required';
            $arr_msg_form_validate['event.*.start_date.required']       = 'Start Date field is required';
            $arr_msg_form_validate['event.*.end_date.required']         = 'End Date field is required';
            $arr_msg_form_validate['event.*.status.required']           = 'Status field is required';
        }
        if($request->attendee){
            $arr_form_validate['attendee.*.booked_by']              = 'required|string';
            $arr_form_validate['attendee.*.attendee_name']          = 'required|string';
            $arr_form_validate['attendee.*.national_identity_card'] = 'required|string';
            // $arr_form_validate['attendee.*.id_event_program']       = 'required|string';
            $arr_form_validate['attendee.*.invited_by']             = 'required|string';
            $arr_msg_form_validate['attendee.*.booked_by.required']             = 'Booked By field is required';
            $arr_msg_form_validate['attendee.*.attendee_name.required']         = 'Attendee Name field is required';
            $arr_msg_form_validate['attendee.*.national_identity_card.required']= 'Identity field is required';
            $arr_msg_form_validate['attendee.*.id_event_program.required']      = 'Program field is required';
            $arr_msg_form_validate['attendee.*.invited_by.required']            = 'Invited By field is required';
        }
        $request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function validate_event(Request $request) {
        $arr_form_validate = [
            'description'           => 'required|string',
            'start_date'            => 'required|string',
            'end_date'              => 'required|string',
            'id_timezone'           => 'required|string',
            // 'end_date_registration' => 'required|string',
            'id_event_type'         => 'required|string',
            'organized_by'          => 'required|string',
            'responsible_by'        => 'required|string',
            'venue'                 => 'required|string',
            'status'                => 'required|string',
        ];
        $arr_msg_form_validate = [
            'description.required'           => 'The Event Name field is required',
            'start_date.required'            => 'The Start Date field is required',
            'end_date.required'              => 'The End Date field is required',
            'id_timezone.required'           => 'The Timezone field is required',
            'end_date_registration.required' => 'The End Date Registration field is required',
            'id_event_type.required'         => 'The Type field is required',
            'organized_by.required'          => 'The Organized By field is required',
            'responsible_by.required'        => 'The Responsible field is required',
            'venue.required'                 => 'The Venue field is required',
            'status.required'                => 'The Status field is required',
        ];

        if(@$request->public){
            $arr_form_validate['end_date_registration']  = 'required';
            $arr_msg_form_validate['end_date_registration.required'] = 'End Date Registration field is required';
        }
        if($request->event){
            // $arr_form_validate['event.*.id_course_header']  = 'required|string';
            // $arr_form_validate['event.*.id_checklist']      = 'required|string';
            $arr_form_validate['event.*.description']       = 'required|string';
            $arr_form_validate['event.*.start_date']        = 'required|string';
            $arr_form_validate['event.*.end_date']          = 'required|string';
            $arr_form_validate['event.*.status']            = 'required|string';
            $arr_msg_form_validate['event.*.id_course_header.required'] = 'Course Name field is required';
            // $arr_msg_form_validate['event.*.id_checklist.required']     = 'Checklist field is required';
            $arr_msg_form_validate['event.*.description.required']      = 'Description field is required';
            $arr_msg_form_validate['event.*.start_date.required']       = 'Start Date field is required';
            $arr_msg_form_validate['event.*.end_date.required']         = 'End Date field is required';
            $arr_msg_form_validate['event.*.status.required']           = 'Status field is required';
        }
        $request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'description'           => $request->description,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'id_timezone'           => $request->id_timezone,
                'limit_registration'    => $request->limit_registration ? 1 : null,
                'public'                => $request->public ? 1 : null,
                'end_date_registration' => $request->public ? @$request->end_date_registration : null,
                'id_event_type'         => $request->id_event_type,
                'organized_by'          => $request->organized_by,
                'responsible_by'        => $request->responsible_by,
                'venue'                 => $request->venue,
                'id_event_room'         => $request->event_room,
                'notes'                 => $request->notes,
                'status'                => $request->status,
                'long_description'      => @$request->long_description ?? null,
                'event_category'        => 'Event',
                'id_company'            => session('id_company'),
            ];
            
            if ($request->attachment) {
                $attach = $request->attachment;
                $filePath = 'public/upload/event_management';
                if ($attach->isValid()) {
                    $fileName       = $attach->getClientOriginalName();
                    $newFileName    = Str::random(3).'_'.urlencode($fileName);
                    $dir            = Storage::makeDirectory($filePath, 0775, true, true);
                    $storageimage   = Storage::putFileAs($filePath, $attach, $newFileName);
                    $form_data['attachment'] = $newFileName;
                }
            }
            if($request->event_room) {
                BookingRoomController::checkEventRoomAvailability($request->id_event_management, $request->event_room, Carbon::parse($request->start_date), Carbon::parse($request->end_date));
            }

            if($request->id_event_management){
                $idEventMgt     = $request->id_event_management;
                $form_data['updated_by'] = session('id_user');
                HrEventManagement::findOrFail($idEventMgt)->update($form_data);
            } else {
                $form_data['created_by'] = session('id_user');
                $insertEventMgt = HrEventManagement::create($form_data);
                $idEventMgt     = $insertEventMgt->id_event_management;
            }

            $idEventProgram = [];

            if($request->managed_by) {
                foreach ($request->managed_by as $key => $val) {
                    $dataManaged = [
                        'id_event_management' => $idEventMgt,
                        'id_employee' => $val,
                        'id_company' => session('id_company'),
                        'creation_date' => date('Y-m-d H:i:s'),
                        'created_by' => session('id_user'),
                    ];
                    $insertManaged = DB::table('relation_responsible_event')->insert($dataManaged);
                }
            }

            if ($request->event) {
                foreach ($request->event as $key => $value) {
                    $form_event = [
                        'id_event_management'   => $idEventMgt,
                        'description'           => $value['description'],
                        // 'id_course_header'      => $value['id_course_header'],
                        // 'id_checklist'          => $value['id_checklist'],
                        'start_date'            => $value['start_date'],
                        'end_date'              => $value['end_date'],
                        'maximum'               => @$value['maximum'],
                        // 'pass_scores'           => @$value['pass_scores'],
                        'status'                => $value['status'],
                        'id_company'            => session('id_company'),
                    ];
                    if($value['id_event_program'] == ''){
                        $form_event['created_by'] = session('id_user');
                        $insertProgram = HrEventProgram::create($form_event);
                        $idEventProgram[] = $insertProgram->id_event_program;
                    } else {
                        $form_event['updated_by'] = session('id_user');
                        HrEventProgram::findOrFail($value['id_event_program'])->update($form_event);
                        $idEventProgram[] = $value['id_event_program'];
                    }
                }
            }

            if ($request->attendee) {
                // if(count($idEventProgram) > 0){
                    foreach ($request->attendee as $key => $value) {
                        foreach ($value['id_event_program'] as $k => $idEventProgram_) {
                            $form_attendee = [
                                'id_event_management'       => $idEventMgt,
                                'id_event_program'          => $idEventProgram_,
                                // 'id_event_program'          => $value['id_event_program'],
                                'booked_by'                 => $value['booked_by'],
                                'national_identity_card'    => $value['national_identity_card'],
                                'attendee_name'             => $value['attendee_name'],
                                'attendee_email'            => $value['attendee_email'],
                                'attendee_phone'            => $value['attendee_phone'],
                                'start_date'                => $value['start_date'] ?? null,
                                'end_date'                  => $value['end_date'] ?? null,
                                'invited_by'                => $value['invited_by'],
                                'id_company'                => session('id_company'),
                            ];

                            if(@$value['status']=='on'){
                                $form_attendee['status'] = 'Cancel';
                            }

                            if($value['id_event_attendees'] == ''){
                                $form_attendee['created_by'] = session('id_user');
                                HrEventAttendees::create($form_attendee);
                            } else {
                                $form_attendee['updated_by'] = session('id_user');
                                HrEventAttendees::findOrFail($value['id_event_attendees'])->update($form_attendee);
                            }
                        }
                    }
                // }
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Saved Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function update(Request $request) {
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $idEventMgt     = $request->id_event_management;
            $form_data = [
                'description'           => $request->description,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'id_timezone'           => $request->id_timezone,
                'limit_registration'    => $request->limit_registration ? 1 : null,
                'public'                => $request->public ? 1 : null,
                'end_date_registration' => $request->public ? @$request->end_date_registration : null,
                'id_event_type'         => $request->id_event_type,
                'organized_by'          => $request->organized_by,
                'responsible_by'        => $request->responsible_by,
                'venue'                 => $request->venue,
                'id_event_room'         => $request->event_room,
                'notes'                 => $request->notes,
                'status'                => $request->status,
                'long_description'      => @$request->long_description ?? null,
                'event_category'        => 'Event',
                'id_company'            => session('id_company'),
                'updated_by'            => session('id_user'),
            ];
            if($request->event_room) {
                BookingRoomController::checkEventRoomAvailability($request->id_event_management, $request->event_room, Carbon::parse($request->start_date), Carbon::parse($request->end_date));
            }
            if ($request->attachment) {
                $attach = $request->attachment;
                $filePath = 'public/upload/event_management';
                if ($attach->isValid()) {
                    $getAttach      = HrEventManagement::where('id_event_management', $idEventMgt);
                    if(!is_null($getAttach->first()->attachment)){
                        Storage::disk('local')->delete($filePath.'/'.$getAttach->first()->attachment);
                        $getAttach->delete();
                    }
                    $fileName       = $attach->getClientOriginalName();
                    $newFileName    = Str::random(3).'_'.$fileName;
                    $dir            = Storage::makeDirectory($filePath, 0775, true, true);
                    $storageimage   = Storage::putFileAs($filePath, $attach, $newFileName);
                    $form_data['attachment'] = $newFileName;
                }
            }
            HrEventManagement::findOrFail($idEventMgt)->update($form_data);

            $listIdEventAttendees   = [];
            $listIdEventProgram     = [];
            $idEventAttendees       = [];
            $idEventProgram         = [];
            $idEventProgramInAttendee = [];
            $attendeeCannotDelete   = [];

            if(HrEventAttendees::where('id_event_management', $idEventMgt)->first() != null){
                $listIdEventAttendees = HrEventAttendees::where('id_event_management', $idEventMgt)->get()->pluck('id_event_attendees')->all();
            }

            if(HrEventProgram::where('id_event_management', $idEventMgt)->first() != null){
                $listIdEventProgram = HrEventProgram::where('id_event_management', $idEventMgt)->get()->pluck('id_event_program')->all();
            }

            if($request->managed_by) {
                $getExistingManaged = DB::table('relation_responsible_event')->where('id_event_management',$idEventMgt)->pluck('id_employee')->all();

                foreach ($request->managed_by as $key => $val) {
                    if(!in_array($val, $getExistingManaged)){
                        $dataManaged = [
                            'id_event_management' => $idEventMgt,
                            'id_employee' => $val,
                            'id_company' => session('id_company'),
                            'creation_date' => date('Y-m-d H:i:s'),
                            'created_by' => session('id_user'),
                        ];
                        $insertManaged = DB::table('relation_responsible_event')->insert($dataManaged);
                    }
                }
                if(count($getExistingManaged) > 0){
                    $diffManaged = collect($getExistingManaged)->diff($request->managed_by);
                    if($diffManaged->count() > 0){
                        $deleteManaged = DB::table('relation_responsible_event')
                            ->where('id_event_management', $idEventMgt)
                            ->whereIn('id_employee', $diffManaged)
                            ->delete();
                    }
                }
            }

            if ($request->event) {
                foreach ($request->event as $key => $value) {
                    $form_event = [
                        'id_event_management'   => $idEventMgt,
                        'description'           => $value['description'],
                        // 'id_course_header'      => $value['id_course_header'],
                        // 'id_checklist'          => $value['id_checklist'],
                        'start_date'            => $value['start_date'],
                        'end_date'              => $value['end_date'],
                        'maximum'               => @$value['maximum'],
                        // 'pass_scores'           => @$value['pass_scores'],
                        'status'                => $value['status'],
                        'id_company'            => session('id_company'),
                    ];
                    if($value['id_event_program'] == ''){
                        $form_event['created_by'] = session('id_user');
                        $insertProgram = HrEventProgram::create($form_event);
                        $idEventProgram[] = $insertProgram->id_event_program;
                        $idEventProgramInAttendee[] = $insertProgram->id_event_program;
                    } else {
                        $idEventProgram[] = $value['id_event_program'];
                        $idEventProgramInAttendee[] = $value['id_event_program'];
                        $form_event['updated_by'] = session('id_user');
                        HrEventProgram::findOrFail($value['id_event_program'])->update($form_event);
                    }
                }
            }

            if ($request->attendee) {
                // if(count($idEventProgram) > 0){
                    foreach ($request->attendee as $key => $value) {
                        foreach ($value['id_event_program'] as $k => $idEventProgram_) {
                            $form_attendee = [
                                'id_event_management'       => $idEventMgt,
                                'id_event_program'          => $idEventProgram_,
                                // 'id_event_program'          => $value['id_event_program'],
                                'booked_by'                 => $value['booked_by'],
                                'national_identity_card'    => $value['national_identity_card'],
                                'attendee_name'             => $value['attendee_name'],
                                'attendee_email'            => $value['attendee_email'],
                                'attendee_phone'            => $value['attendee_phone'],
                                'start_date'                => $value['start_date'] ?? null,
                                'end_date'                  => $value['end_date'] ?? null,
                                'invited_by'                => $value['invited_by'],
                                'id_company'                => session('id_company'),
                            ];

                            if(@$value['status']=='on'){
                                $form_attendee['status'] = 'Cancel';
                            } else {
                                $checkRelation = DB::table('relation_event_course_employee')
                                    ->where([
                                        'id_event_program'=>$idEventProgram_, 
                                        'id_employee'=> $value['booked_by']
                                    ])->first();
                                if($checkRelation){
                                    $form_attendee['status'] = 'Attended';
                                } else {
                                    $form_attendee['status'] = 'Unconfirm';
                                }
                            }

                            $getAttendee = HrEventAttendees::where('booked_by', $value['booked_by'])
                                ->where('id_event_program', $idEventProgram_)
                                ->where('id_event_management', $idEventMgt)
                                ->first();

                            if(!$getAttendee){
                                $form_attendee['created_by'] = session('id_user');
                                HrEventAttendees::create($form_attendee);
                            } else {
                                $idEventAttendees[] = $getAttendee->id_event_attendees;
                                $form_attendee['updated_by'] = session('id_user');
                                $update = HrEventAttendees::where('booked_by', $value['booked_by'])
                                    ->where('id_event_program', $idEventProgram_)
                                    ->where('id_event_management', $idEventMgt)
                                    ->update($form_attendee);
                            }
                        }
                    }
                // }
            }

            if(count($idEventProgramInAttendee) > 0){
                $getProgram_ = HrEventProgram::whereIn('id_event_program',$idEventProgramInAttendee)->pluck('description','id_event_program')->all();
            }

            $diffEventAttendees = array_diff($listIdEventAttendees, $idEventAttendees);
            if(count($diffEventAttendees) > 0){
                foreach ($diffEventAttendees as $item) { 
                    $attendeeInfo = HrEventAttendees::where('id_event_attendees', $item)->first();
                    $checkRelation = DB::table('relation_event_course_employee')
                        ->where([
                            'id_event_program'=>@$attendeeInfo->id_event_program, 
                            'id_employee'=> @$attendeeInfo->booked_by
                        ])->first();

                    if($checkRelation){
                        $getEmployee = DB::table('hr_employee')->select('nik_employee')->where('id_employee',@$attendeeInfo->booked_by)->first();
                        $attendeeCannotDelete[$getEmployee->nik_employee][] = @$getProgram_[@$attendeeInfo->id_event_program];
                    } else {
                        HrEventAttendees::where('id_event_attendees', $item)->delete();
                    }
                }
            }

            if(count($attendeeCannotDelete) > 0){
                $showMessage = "Cannot delete course in this Attendee\n";
                foreach ($attendeeCannotDelete as $nik => $thisCourse) {
                    $showMessage.= $nik." (".implode(', ',$thisCourse).")\n";
                }
                throw new \Exception($showMessage);
            }

            $diffEventProgram = array_diff($listIdEventProgram, $idEventProgram);
            if(count($diffEventProgram) > 0){
                foreach ($diffEventProgram as $item) { 
                    HrEventProgram::where('id_event_program', $item)->delete();
                }
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Updated Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function save_event(Request $request) {
        $this->validate_event($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'description'           => $request->description,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'id_timezone'           => $request->id_timezone,
                'end_date_registration' => $request->end_date_registration,
                'id_event_type'         => $request->id_event_type,
                'organized_by'          => $request->organized_by,
                'responsible_by'        => $request->responsible_by,
                'venue'                 => $request->venue,
                'id_event_room'         => $request->event_room,
                'notes'                 => $request->notes,
                'status'                => $request->status,
                'event_category'        => 'Learning',
                'id_company'            => session('id_company'),
                'created_by'            => session('id_user'),
            ];
            
            if($request->id_event_management){
                $idEventMgt     = $request->id_event_management;
            } else {
                $insertEventMgt = HrEventManagement::create($form_data);
                $idEventMgt     = $insertEventMgt->id_event_management;
            }
            $counter        = $request->counter;

            $form_event = [
                'id_event_management'   => $idEventMgt,
                'description'           => $request->event[$counter]['description'],
                // 'id_course_header'      => $request->event[$counter]['id_course_header'],
                // 'id_checklist'          => $request->event[$counter]['id_checklist'],
                'start_date'            => $request->event[$counter]['start_date'],
                'end_date'              => $request->event[$counter]['end_date'],
                'maximum'               => @$request->event[$counter]['maximum'],
                'status'                => $request->event[$counter]['status'],
                'id_company'            => session('id_company'),
                'created_by'            => session('id_user'),
            ];

            if($request->id_event_program){
                unset($form_event['created_by']);
                $form_event['updated_by'] = session('id_user');
                HrEventProgram::findOrFail($request->id_event_program)->update($form_event);
                $idEventProgram = $request->id_event_program;
            } else {
                $insertProgram  = HrEventProgram::create($form_event);
                $idEventProgram = $insertProgram->id_event_program;
            }
                
            $getAllProgram = HrEventProgram::where('id_event_management',$idEventMgt)->get()->pluck('id_event_program')->all();
            $resp['global_id_event_management'] = $idEventMgt;
            $resp['id_event_program']           = $idEventProgram;
            $resp['all_id_event_program']       = $getAllProgram;
            $resp['global_select_program']      = HrEventProgram::get_event_program($idEventMgt);

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Saved Successfully', 'data' => $resp]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy_event(Request $request) {
        DB::beginTransaction();
        try {
            $id_event_program   = $request->id_event_program;
            $idEventMgt         = $request->id_event_management;
            
            if(HrEventAttendees::where('id_event_program', $id_event_program)->first() != null){
                throw new \Exception('Terdapat data Attendees yg sudah tersimpan pada program ini');
            }
            $event         = HrEventProgram::where('id_event_program', $id_event_program)->delete();
            $getAllProgram = HrEventProgram::where('id_event_management',$idEventMgt)->get()->pluck('id_event_program')->all();
            $resp['all_id_event_program']       = $getAllProgram;
            $resp['global_select_program']      = HrEventProgram::get_event_program($idEventMgt);

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Deleted Successfully', 'data' => $resp]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request) {
        DB::beginTransaction();
        try {
            $id_event_management   = $request->id_event_management;
            HrEventAttendees::where('id_event_management', $id_event_management)->delete();
            HrEventProgram::where('id_event_management', $id_event_management)->delete();
            HrEventManagement::where('id_event_management', $id_event_management)->delete();
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Deleted Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function get_event_edit(Request $request) {
        $data = [
            'id_event_management'   => $request->id_event_management,
            'id_company'            => session('id_company'),
        ];
        $allowSave = false;
        $getEmployee = DB::table('hr_employee')->select('id_employee')->where('id_user', session('id_user'))->first();
        $programByEvent = HrEventProgram::get_event_program($request->id_event_management);
        $result['event']    = HrEventManagement::get_survey_edit($data);
        $result['program']  = $programByEvent;
        $result['all_id_program']  = $programByEvent->pluck('id')->all();

        if(@$getEmployee->id_employee == @$result['event']->responsible_by || @$getEmployee->id_employee == @$result['event']->organized_by){
            $allowSave = true;
        }
        $result['allow_save']  = $allowSave;
        return response()->json($result);
    }

    public function get_employee() {
        $result = HrEventManagement::get_employee();
        return response()->json($result);
    }

    public function get_event_type() {
        $result = HrEventManagement::get_event_type();
        return response()->json($result);
    }

    public function get_timezone() {
        $result = HrEventManagement::get_timezone();
        return response()->json($result);
    }

    public function get_checklist() {
        $result = HrEventManagement::get_checklist();
        return response()->json($result);
    }

    public function get_course() {
        $result = HrEventManagement::get_course();
        return response()->json($result);
    }
    
    public function get_employee_detail($id_employee=null) {
        $result = HrEventManagement::get_employee_detail($id_employee);
        return response()->json($result);
    }

    public function download_attachment($filename) {
        $filePath = 'public/upload/event_management/'.$filename;
        if(Storage::disk('local')->exists($filePath)){
            return response()->download(storage_path('app/'.$filePath));
        }
    }

    public function get_user_by_session() {
        $result = HrEventManagement::get_employee(session('id_user'));
        return response()->json($result);
    }

    public function getEmployeeHR() {
        $result = HrEventManagement::getEmployeeHR();
        return response()->json($result);
    }

    public function getRoom() {
        $result = HrEventManagement::getRoom();
        return response()->json($result);
    }
}
