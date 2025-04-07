<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\HrEventManagement;
use App\Models\LearningManagement\Lms\HrEventProgram;
use App\Models\LearningManagement\Lms\HrEventAttendees;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use App\Models\Employee\Employee\Employee;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUser;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUserHeader;
use App\Models\LearningManagement\Lms\HrSurveyHeader;
use App\Models\LearningManagement\Lms\HrSurveyQuestion;
use App\Models\LearningManagement\Lms\MasterCourseDetail;
use App\Models\LearningManagement\Lms\MasterCourseHeader;
use App\Models\LearningManagement\Lms\RelationEventCourseEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Carbon;
use Validator;

class EventCourseController extends Controller {

    protected function accessBranch($url) {
		$data_access = Employee::get_access($url);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}

			return $group_branch;
	}

    public function index(Request $request) {
        if ($request->ajax()) {
            $getEmployee = DB::table('hr_employee as he')
            ->leftJoin('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
            ->leftJoin('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
            ->select('he.id_employee', 'mpr.description as position')
            ->where('he.id_user', session('id_user'))
            ->where('he.status', 'A')->first();

            $data = HrEventManagement::get_event(@$getEmployee->id_employee);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) use($getEmployee) {
                        $button = "";
                        if($data->event_category != "Training") $button .= '<button type="button" name="edit" id="' . $data->id_event_management . '" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                        if($getEmployee->position === "PEOPLE DEVELOPMENT SUPERVISOR") {
                            $button .= '<button type="button" name="duplicateProgram" id-event-management="' . $data->id_event_management . '" class="duplicate-program btn btn-success btn-sm" title="Duplicate" ><span class="fas fa-copy"></span></button> ';
                        }
                        $button .= '<button type="button" name="uploadEpstp" id-event-management="' . $data->id_event_management . '" class="uploadEpstp btn btn-success btn-sm mt-1" title="Upload EPSTP" ><span class="fas fa-upload"></span></button> ';
                        // $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_event_management . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view('learning_management.lms.event_course.index');
    }

    public function getCreateAccess(Request $request) {
        $request->validate([
            'url' => 'required'
        ]);
        $accessBranch = $this->accessBranch($request->url);
        $emp = DB::table('hr_employee as he')
            ->join('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
            ->join('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
            ->join('master_job_position as mjp', 'mpr.id_position', 'mjp.id_position')
            ->join('master_department as md', 'mjp.id_dept', 'md.id_dept')
            ->join('master_branch as mb', 'mpd.id_branch', 'mb.id_branch')
            ->where('he.id_user', session('id_user'))
            ->where('mpd.secondary_position', FALSE)
            ->where('md.department_code', '150_HR')
            ->where('he.status', 'A')
            ->where('mb.branch_code', 'PST')
            ->first();
        $canCreate = true;
        if($accessBranch != null || $emp == null) {
            $canCreate = false;
        }
        return response()->json([
            'can_create' => $canCreate
        ]);
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
            $arr_form_validate['event.*.id_course_header']  = 'required|string';
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
        /*    $arr_form_validate['attendee.*.booked_by']              = 'required|string';
            $arr_form_validate['attendee.*.attendee_name']          = 'required|string';
            $arr_form_validate['attendee.*.national_identity_card'] = 'required|string';
            $arr_form_validate['attendee.*.invited_by']             = 'required|string';
			
            $arr_msg_form_validate['attendee.*.booked_by.required']             = 'Booked By field is required';
            $arr_msg_form_validate['attendee.*.attendee_name.required']         = 'Attendee Name field is required';
            $arr_msg_form_validate['attendee.*.national_identity_card.required']= 'Identity field is required';
            $arr_msg_form_validate['attendee.*.id_event_program.required']      = 'Program field is required';
            $arr_msg_form_validate['attendee.*.invited_by.required']            = 'Invited By field is required';
		*/	
			foreach ($request->attendee as $key => $valuec) {
				if((!isset($valuec['booked_by']) && !isset($valuec['status'])) || $valuec['national_identity_card'] == null || !isset($valuec['id_event_program'])){
					$arr_form_validate += [
						'attendee.'.$key.'.booked_by' => 'required',
						'attendee.'.$key.'.attendee_name' => 'required',
						'attendee.'.$key.'.national_identity_card' => 'required',
						'attendee.'.$key.'.id_event_program' => 'required',
						'attendee.'.$key.'.invited_by' => 'required',
					];
					$arr_msg_form_validate += [
						'attendee.'.$key.'.booked_by.required' => 'The Booked By field is required.',
						'attendee.'.$key.'.attendee_name.required' => 'The Attendee Name field is required.',
						'attendee.'.$key.'.national_identity_card.required' => 'The Identity Number field is required.',
						'attendee.'.$key.'.id_event_program.required' => 'The Program field is required.',
						'attendee.'.$key.'.invited_by.required' => 'The Invited By field is required.',
					];
				}
				
			}
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
            $arr_form_validate['event.*.id_course_header']  = 'required|string';
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
                'notes'                 => $request->notes,
                'status'                => $request->status,
                'is_mandatory_flag'     => $request->mandatory,
                'long_description'      => @$request->long_description ?? null,
                'event_category'        => 'Learning',
                'id_company'            => session('id_company'),
                'id_region'                => $request->region,
                'id_branch'                => $request->branch,
            ];
            
            if ($request->attachment) {
                $attach = $request->attachment;
                $filePath = 'public/upload/course_event';
                if ($attach->isValid()) {
                    $fileName       = $attach->getClientOriginalName();
                    $newFileName    = Str::random(3).'_'.urlencode($fileName);
                    $dir            = Storage::makeDirectory($filePath, 0775, true, true);
                    $storageimage   = Storage::putFileAs($filePath, $attach, $newFileName);
                    $form_data['attachment'] = $newFileName;
                }
            }

            // if($request->id_event_management){
            //     $idEventMgt     = $request->id_event_management;
            //     $form_data['updated_by'] = session('id_user');
            //     HrEventManagement::findOrFail($idEventMgt)->update($form_data);
            // } else {
            $form_data['created_by'] = session('id_user');
            $insertEventMgt = HrEventManagement::create($form_data);
            $idEventMgt     = $insertEventMgt->id_event_management;
            // }

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
                        'id_course_header'      => $value['id_course_header'],
                        // 'id_checklist'          => $value['id_checklist'],
                        'start_date'            => $value['start_date'],
                        'end_date'              => $value['end_date'],
                        'maximum'               => @$value['maximum'],
                        'pass_scores'           => @$value['pass_scores'],
                        'status'                => $value['status'],
                        'id_company'            => session('id_company'),
                        'sequence'              => @$value['sequence'],
                        'id_trainer'           => @$value['id_trainer'],
                    ];
                    if($value['id_event_program'] == ''){
                        $form_event['created_by'] = session('id_user');
                        $checkDuplicateDesc = HrEventProgram::where('id_event_management', $idEventMgt)
                                                ->where('id_course_header', $value['id_course_header'])
                                                ->where('description', $value['description'])
                                                ->count();
                        if($checkDuplicateDesc > 0) {
                            throw new \Exception("Duplicate course with similar description ".$value['description'].". Please modify the description if you want to add the same course multiple times.");
                        }
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
                'notes'                 => $request->notes,
                'status'                => $request->status,
                'is_mandatory_flag'     => $request->mandatory,
                'long_description'      => @$request->long_description ?? null,
                'event_category'        => 'Learning',
                // 'id_company'            => session('id_company'),
                'updated_by'            => session('id_user'),
                'id_region'             => $request->region,
                'id_branch'             => $request->branch,
            ];
            if ($request->attachment) {
                $attach = $request->attachment;
                $filePath = 'public/upload/course_event';
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
                        'id_course_header'      => $value['id_course_header'],
                        // 'id_checklist'          => $value['id_checklist'],
                        'start_date'            => $value['start_date'],
                        'end_date'              => $value['end_date'],
                        'maximum'               => @$value['maximum'],
                        'pass_scores'           => @$value['pass_scores'],
                        'status'                => $value['status'],
                        'sequence'                => $value['sequence'],
                        'id_trainer'           => @$value['id_trainer'],
                        'id_company'            => session('id_company'),
                    ];
                    if($value['id_event_program'] == ''){
                        $form_event['created_by'] = session('id_user');
                        $checkDuplicateDesc = HrEventProgram::where('id_event_management', $idEventMgt)
                                                ->where('id_course_header', $value['id_course_header'])
                                                ->where('description', $value['description'])
                                                ->count();
                        if($checkDuplicateDesc > 0) {
                            throw new \Exception("Duplicate course with similar description ".$value['description'].". Please modify the description if you want to add the same course multiple times.");
                        }
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
                                // 'booked_by'                 => $value['booked_by'],
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
                                $form_attendee['booked_by'] = $value['booked_by'];
                                HrEventAttendees::create($form_attendee);
                            } else {
                                $idEventAttendees[] = $getAttendee->id_event_attendees;
                                $form_attendee['updated_by'] = session('id_user');
								if(isset($value['booked_by'])){
									$form_attendee['booked_by'] = $value['booked_by'];
									$update = HrEventAttendees::where('booked_by', $value['booked_by'])
										->where('id_event_program', $idEventProgram_)
										->where('id_event_management', $idEventMgt)
										->update($form_attendee);
								}
								else if(!isset($value['booked_by']) && @$value['status']=='on'){
									$update = HrEventAttendees::where('national_identity_card', $value['national_identity_card'])
										->where('id_event_program', $idEventProgram_)
										->where('id_event_management', $idEventMgt)
										->update($form_attendee);
								}
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
            return response()->json(['status' => 'true', 'message' => 'Content Updated Successfully']);
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
                'notes'                 => $request->notes,
                'status'                => $request->status,
                'is_mandatory_flag'     => $request->mandatory,
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
                'id_course_header'      => $request->event[$counter]['id_course_header'],
                // 'id_checklist'          => $request->event[$counter]['id_checklist'],
                'start_date'            => $request->event[$counter]['start_date'],
                'end_date'              => $request->event[$counter]['end_date'],
                'maximum'               => @$request->event[$counter]['maximum'],
                'status'                => $request->event[$counter]['status'],
                'sequence'              => @$request->event[$counter]['sequence'],
                'id_trainer'              => @$request->event[$counter]['id_trainer'],
                'id_company'            => session('id_company'),
                'created_by'            => session('id_user'),
            ];

            if($request->id_event_program){
                unset($form_event['created_by']);
                $form_event['updated_by'] = session('id_user');
                HrEventProgram::findOrFail($request->id_event_program)->update($form_event);
                $idEventProgram = $request->id_event_program;
            } else {
                $checkDuplicateDesc = HrEventProgram::where('id_event_management', $idEventMgt)
                                        ->where('id_course_header', $request->event[$counter]['id_course_header'])
                                        ->where('description', $request->event[$counter]['description'])
                                        ->count();
                if($checkDuplicateDesc > 0) {
                    throw new \Exception("Duplicate course with similar description ".$value['description'].". Please modify the description if you want to add the same course multiple times.");
                }
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

    public function register_course(Request $request) {
        DB::beginTransaction();
        try {
            $idEventManagement = $request->id_event_management;
            $idEmployee = @$request->id_employee;

            $getProgram = HrEventManagement::where('id_event_management', $idEventManagement)->first();
            $getEmployee = DB::table('hr_employee')->where('id_employee', $idEmployee)->first();
            $allCourse = HrEventProgram::where('id_event_management', $idEventManagement)->get();
            
            if($allCourse->count() > 0){
                foreach ($allCourse as $k => $val) {
                    $form_attendee = [
                        'id_event_management'       => $idEventManagement,
                        'id_event_program'          => $val->id_event_program,
                        'booked_by'                 => $getEmployee->id_employee,
                        'national_identity_card'    => $getEmployee->identification_number ?? null,
                        'attendee_name'             => $getEmployee->name ?? null,
                        'attendee_email'            => $getEmployee->work_mail ?? null,
                        'attendee_phone'            => $getEmployee->work_phone ?? null,
                        'invited_by'                => $getProgram->responsible_by ?? null,
                        'id_company'                => session('id_company'),
                        'created_by'                => session('id_user')
                    ];
                    HrEventAttendees::create($form_attendee);
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
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
        $getEmployee = DB::table('hr_employee')->select('id_employee')->where('status', 'A')->where('id_user', session('id_user'))->first();
        $programByEvent = HrEventProgram::get_event_program($request->id_event_management);
        $result['event']    = HrEventManagement::get_survey_edit($data);
        $result['program']  = $programByEvent;
        $result['all_id_program']  = $programByEvent->pluck('id')->all();
        $isMandatory = $result['event']->is_mandatory_flag;
        $result['employees'] = HrEventManagement::get_employee(null, $result['event']->id_region, $result['event']->id_branch);

        $allowAddAttendees = false;
        $allowAddAssignment = false;
        $allowEditHeader = false;
        $allowEditAttendees = false;
        if(@$getEmployee->id_employee == @$result['event']->organized_by) {
            $allowEditHeader = true;
            $allowAddAssignment = true;
            $allowEditAttendees = true;
        }
        if(@$getEmployee->id_employee == @$result['event']->responsible_by || @$getEmployee->id_employee == @$result['event']->organized_by || in_array(@$getEmployee->id_employee, @$result['event']->managed_by)){
            $allowSave = true;
            $allowAddAttendees = true;
            if(in_array(@$getEmployee->id_employee, @$result['event']->managed_by)) {
                $allowEditAttendees = !$isMandatory;
            }
        }
        if(@$getEmployee->id_employee == @$result['event']->responsible_by) {
            // $allowAddAssignment = true;
            $allowEditAttendees = true;
        }
        $result['allow_save']  = $allowSave;
        $result['allow_add_attendees'] = $allowAddAttendees;
        $result['allow_add_assignment'] = $allowAddAssignment;
        $result['allow_edit_header'] = $allowEditHeader;
        $result['allow_edit_attendees'] = $allowEditAttendees;
        return response()->json($result);
    }

    public function get_employee_managed_by(Request $request) {
        $result = HrEventManagement::get_employee_managed_by();
        return response()->json($result);
    }

    public function get_employee(Request $request) {
        $result = HrEventManagement::get_employee(null, $request->id_region, $request->id_branch, $request->name, $request->selected_employee ?? []);
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

    public function get_region() {
        $region = DB::table('master_region')->where('status', 'A')->where('id_company', session('id_company'))->get(['id_region as id', 'description as text']);
        return response()->json($region);
    }

    public function get_branch(Request $request) {
        $request->validate([
            'id_region' => 'required'
        ]);
        $branch = DB::table('master_branch')
                        ->where('id_region', $request->id_region)
                        ->where('status', 'A')
                        ->get(['id_branch as id', 'description as text']);
        return response()->json($branch);
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
        $filePath = 'public/upload/course_event/'.$filename;
        if(Storage::disk('local')->exists($filePath)){
            return response()->download(storage_path('app/'.$filePath));
        }
    }

    public function get_user_by_session() {
        $result = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->get();
        return response()->json($result);
    }

    public function getEmployeeHR(Request $request) {
        $result = HrEventManagement::getEmployeeHR($request->id_region, $request->id_branch);
        return response()->json($result);
    }

    public function duplicateProgram(Request $request) {
        $request->validate([
            'id_event_management' => 'required'
        ]);
        try {
            DB::select('select * from spgenerateduplicateprogram(?,?,?)', [$request->id_event_management, session('id_company'), session('id_user')]);
            return response()->json([
                'message' => 'Duplicate program success!'
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'message' => 'Duplicate program failed! '.$e->getMessage()
            ], 500);
        }
    }

    public function submit_epstp(Request $request) {
        ini_set('max_execution_time', 30);
        $request->validate([
            'attachment' => 'required|file|mimes:xlsx,xls'
        ]);

        DB::beginTransaction();
        try {
            $type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($request->attachment);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            $reader->setLoadSheetsOnly('DATA PSTP');
            $spreadsheet = $reader->load($request->attachment);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            
            $validRows = [];
            $ratingList = ["baik", "cukup", "perlu perbaikan"];
            foreach($sheetData as $rowIndex => $rowData) {
                if($rowIndex == 0 || $rowData[11] == "#N/A" || !$rowData[11]) continue;

                $trainingDate = Carbon::parse($rowData[2]);
                $nik = $rowData[3];
                // $name = $rowData[5];
                $courseName = $rowData[6];
                $rating = $rowData[7];

                $employee = DB::table('hr_employee')->where('nik_employee', $nik)->where('status', 'A')->first();
                if($employee == null || $courseName == "" || $courseName == null || !in_array(strtolower($rating), $ratingList)) {
                    continue;
                }
                array_push($validRows, $rowData);
                $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                $out->writeln(count($validRows)." - R".$rowIndex." ".$trainingDate->format('Y-m-d')." ".$nik.": ".$employee->name." - ".$courseName." - ".$rating);
            }
            return response()->json([
                "count" => count($validRows),
                "rows" => $validRows
            ]);
        } catch(\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ]);
        }
    }

    public function upload_course(Request $request) {
        $request->validate([
            'attachment' => 'required|file'
        ]);

        DB::beginTransaction();
        try {
            $type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($request->attachment);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            // $reader->setLoadSheetsOnly('DATA PSTP');
            $spreadsheet = $reader->load($request->attachment);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            foreach($sheetData as $rowIndex => $rowData) {
                if($rowIndex == 0) continue;

                $courseName = $rowData[0];
                $surveyHeader = $rowData[1];
                $employee = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();

                $mgd = MasterGeneralData::where('id_general_type', 18)->where('code', 'quiz_posttest')->where('id_company', session('id_company'))->first();

                $survey = new HrSurveyHeader;
                $survey->description = $surveyHeader;
                $survey->id_employee_request = $employee->id_employee;
                $survey->id_survey_type = $mgd->id_general_data;
                $survey->survey_category = 'LMS';
                $survey->published = false;
                $survey->status = 'A';
                $survey->id_company = session('id_company');
                $survey->created_by = session('id_user');
                $survey->save();

                $mgd = MasterGeneralData::where('id_general_type', 12)->where('code', 'Essay')->where('id_company', session('id_company'))->first();

                $question = new HrSurveyQuestion();
                $question->id_survey_header = $survey->id_survey_header;
                $question->sequence = 1;
                $question->question = "Rating";
                $question->status = 'A';
                $question->id_company = session('id_company');
                $question->created_by = session('id_user');
                $question->id_question_type = $mgd->id_general_data;
                $question->save();

                $courseHeader = new MasterCourseHeader();
                $courseHeader->course_name = $courseName;
                $courseHeader->status = 'A';
                $courseHeader->id_company = session('id_company');
                $courseHeader->created_by = session('id_user');
                $courseHeader->save();

                $courseDetail = new MasterCourseDetail();
                $courseDetail->id_course_header = $courseHeader->id_course_header;
                $courseDetail->sequence = 1;
                $courseDetail->course_type = "Quiz_Posttest";
                $courseDetail->id_survey_header = $survey->id_survey_header;
                $courseDetail->course_name = $courseName;
                $courseDetail->status = 'A';
                $courseDetail->id_company = session('id_company');
                $courseDetail->created_by = session('id_user');
                $courseDetail->question_view = 1;
                $courseDetail->weight = 1;
                $courseDetail->save();
            }

            DB::commit();
            return response()->json([
                "message" => "Successfully added course!",
            ]);
        } catch(\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function submit_epstp_result(Request $request) {
        ini_set('max_execution_time', 600);
        $request->validate([
            'attachment' => 'required|file',
            'id_event_management' => 'required|exists:hr_event_management,id_event_management'
        ]);
        // if($request->attachment->extension() != 'xlsx' || $request->attachment->extension() != 'xls') {
        //     $request->validate([
        //         'attachment' => 'mimes:xlsx,xls'
        //     ]);
        // }

        DB::beginTransaction();
        try {
            $type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($request->attachment);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            // $reader->setLoadSheetsOnly('DATA PSTP');
            $spreadsheet = $reader->load($request->attachment);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $employeeNotFound = [];
            $courseNotFound = [];
            $duplicates = [];

            foreach($sheetData as $rowIndex => $rowData) {
                if($rowIndex == 0) continue;

                $courseName = $rowData[0];
                $nik = $rowData[1];
                // $name = $rowData[2];
                try {
                    $trainingDate = Carbon::parse($rowData[3]);
                } catch(\Exception $e) {
                    // Jika gagal melakukan parsing pada try, diasumsikan excelnya pakai
                    // format YYYY-DD-MM (cth: 2024-20-01) karena Carbon tidak bisa memparse format itu.
                    // Oleh karena itu, harus dilakukan manual dengan memasukkan format Y-d-m
                    $trainingDate = Carbon::createFromFormat('Y-d-m', $rowData[3]);
                }
                $rating = $rowData[4];
                $hit = $rowData[5];
                // dd($rowData);

                // $employeeCareerHistory = DB::select("SELECT
                //                                     hct.id_employee,
                //                                     hct.id_position_detail,
                //                                     hct.effective_date,
                //                                     hct.expired_date,
                //                                     hct.id_approval,
                //                                     hct.id_approval_status,
                //                                     hct.id_transition_category,
                //                                     mgd.description,
                //                                     he.nik_employee,
                //                                     he.name,
                //                                     he.identification_number,
                //                                     he.private_mail,
                //                                     he.work_mail,
                //                                     he.mobile_phone,
                //                                     he.work_phone,
                //                                     he.status
                //                                 FROM
                //                                     hr_career_transaction hct
                //                                 LEFT JOIN hr_employee he ON
                //                                     hct.id_employee = he.id_employee
                //                                 LEFT JOIN master_general_data mgd ON
                //                                     hct.id_approval_status = mgd.id_general_data
                //                                 LEFT JOIN master_general_data mgd2 ON
                //                                     hct.id_transition_category = mgd2.id_general_data
                //                                     AND mgd.id_company = mgd2.id_company
                //                                 WHERE
                //                                     he.nik_employee = ?
                //                                     AND mgd.code = 'Approved'
                //                                     AND mgd2.code = 'Join'
                //                                     AND mgd.id_company = ?
                //                                 ORDER BY hct.effective_date ASC", 
                // [$nik, session('id_company')]);
                // $employeeCareerHistory = collect($employeeCareerHistory);
                // $employeeCareer = $employeeCareerHistory->where('effective_date', '<=', $trainingDate)->where('expired_date', '>=', $trainingDate)->first();
                // if(!$employeeCareer || count($employeeCareerHistory->where('status', 'A')) < 1) {

                //     array_push($employeeNotFound, $rowIndex);
                //     continue;
                // }
                
                // $employee = $employeeCareer;
                $employee = DB::table('hr_employee')->where('nik_employee', $nik)->where('status', 'A')->first();
                if(!$employee) {
                    array_push($employeeNotFound, $rowIndex);
                    continue;
                }
                $user = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
                $course = DB::table('master_course_header')->where('course_name', $courseName)->where('id_company', session('id_company'))->where('status', 'A')->first();
                if(!$course) {
                    array_push($courseNotFound, $rowIndex);
                    DB::commit();
                    throw new \Exception("Course not found at excel row ".($rowIndex+1)." (".$nik." ".$rowData[2]." ".$courseName.")! Previous rows have been submitted.");
                    continue;
                }
                $courseDetails = DB::select("SELECT
                                    mcd.*,
                                    hsh.*,
                                    hsq.*
                                FROM
                                    master_course_detail mcd
                                LEFT JOIN hr_survey_header hsh 
                                    ON
                                    mcd.id_survey_header = hsh.id_survey_header
                                LEFT JOIN hr_survey_question hsq 
                                    ON hsh.id_survey_header = hsq.id_survey_header
                                WHERE
                                    mcd.id_course_header = ?
                                    AND mcd.status = 'A'
                                    AND hsh.status = 'A'
                                    AND (hsq.question = 'Rating')", 
                                [$course->id_course_header]);

                foreach($courseDetails as $detail) {
                    $eventProgram = HrEventProgram::where('id_event_management', $request->id_event_management)->where('id_course_header', $course->id_course_header)->first();
                    
                    $attendees = HrEventAttendees::where('id_event_management', $request->id_event_management)->where('id_event_program', $eventProgram->id_event_program)->where('booked_by', $employee->id_employee)->first();
                    
                    if(!$attendees) {
                        $attendees = new HrEventAttendees();
                        $attendees->id_event_management = $request->id_event_management;
                        $attendees->id_event_program = $eventProgram->id_event_program;
                        $attendees->booked_by = $employee->id_employee;
                        $attendees->national_identity_card = $employee->identification_number;
                        $attendees->attendee_name = $employee->name;
                        $attendees->attendee_email =  $employee->private_mail;
                        $attendees->attendee_phone = $employee->mobile_phone;
                        $attendees->invited_by = $user->id_employee;
                        $attendees->status = 'Attended';
                        $attendees->id_company = session('id_company');
                        $attendees->created_by = session('id_user');
                        $attendees->start_date = $trainingDate->format('Y-m-d H:i:s');
                        $attendees->end_date = $trainingDate->format('Y-m-d H:i:s');
                        $attendees->save();
                    } else {
                        $findMinMax = HrEventAttendees::getAttendeesMinMaxDate($employee->id_employee, $course->id_course_header, $eventProgram->id_event_program);
                        $attendees->start_date = $findMinMax->min;
                        $attendees->end_date = $findMinMax->max;
                        $attendees->save();
                    }

                    

                    // $checkDuplicate = DB::select("SELECT *
                    //                             FROM hr_survey_answer_user_header hsauh 
                    //                             JOIN hr_survey_answer_user hsau
                    //                                 ON hsauh.id_survey_answer_user_header = hsau.id_survey_answer_user_header 
                    //                             WHERE hsau.creation_date = ? AND hsauh.id_employee = ? AND id_course_detail = ?", 
                    // [$trainingDate->format('Y-m-d H:i:s'), $employee->id_employee, $detail->id_course_detail]);
                    // if(count($checkDuplicate) > 0) {
                    //     array_push($duplicates, $rowIndex);
                    //     continue;
                    // }

                    $rece = new RelationEventCourseEmployee();
                    $rece->id_course_detail = $detail->id_course_detail;
                    $rece->id_event_program = $eventProgram->id_event_program;
                    $rece->id_employee = $employee->id_employee;
                    $rece->is_completed = true;
                    $rece->status = 'A';
                    $rece->id_company = session('id_company');
                    $rece->created_by = session('id_user');
                    $rece->save();


                    $answerHeader = new HrSurveyAnswerUserHeader();
                    $answerHeader->id_survey_header = $detail->id_survey_header;
                    $answerHeader->id_employee = $employee->id_employee;
                    $answerHeader->id_course_detail = $detail->id_course_detail;
                    $answerHeader->status = 'A';
                    $answerHeader->total_score = $hit; // sementara, input manual skor oleh atasan
                    $answerHeader->id_company = session('id_company');
                    $answerHeader->created_by = session('id_user');
                    $answerHeader->creation_date = $trainingDate->format('Y-m-d H:i:s');
                    $answerHeader->id_event_program = $eventProgram->id_event_program;
                    $answerHeader->save();
                    
                    $answer = new HrSurveyAnswerUser();
                    $answer->id_survey_answer_user_header = $answerHeader->id_survey_answer_user_header;
                    $answer->id_survey_question = $detail->id_survey_question;
                    
                    $answer->description_answer = $rating;
                    // $answer->essay_score = $hit;
                    
                    $answer->creation_date = $trainingDate->format('Y-m-d H:i:s');
                    $answer->status = 'A';
                    $answer->id_company = session('id_company');
                    $answer->created_by = session('id_user');
                    $answer->save();
                }
            }

            DB::commit();
            \Log::info('Import EPSTP by '.session('id_user').' completed with '.count($employeeNotFound).' employee not found, '.count($courseNotFound).' course not found, and '.count($duplicates).' duplicates. emp_not_found_rows:'.json_encode($employeeNotFound).' - course_not_found_rows:'.json_encode($courseNotFound).' - dup_rows:'.json_encode($duplicates));
            $message = "Data has been imported successfully! ";
            if(count($employeeNotFound) > 0) {
                $message .= "Skipped rows due to employee not found/inactive: ".json_encode($employeeNotFound).". ";
            }
            if(count($courseNotFound) > 0) {
                $message .= "Skipped rows due to course not found: ".json_encode($courseNotFound).". ";
            }
            if(count($duplicates) > 0) {
                $message .= "Skipped rows due to existing data: ".json_encode($duplicates).". ";
            }
            return response()->json([
                "message" => $message
            ]);

        } catch(\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function batch_add_attendees(Request $request) {
        ini_set('max_execution_time', 30);
        $request->validate([
            'id_event_management' => 'required|exists:hr_event_management,id_event_management',
            'id_event_program' => 'required',
            'attendees_attachment' => 'required|file|mimes:xlsx,xls'
        ]);
        $idEventPrograms = json_decode($request->id_event_program);

        DB::beginTransaction();
        try {
            $type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($request->attendees_attachment);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            // $reader->setLoadSheetsOnly('hr_event_attendees');
            $spreadsheet = $reader->load($request->attendees_attachment);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $employee = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
            if(!$employee) throw new \Exception("User not found!", 401);
            $assignedAttendees = [];
            $eventManagement = HrEventManagement::findOrFail($request->id_event_management);
            foreach($sheetData as $rowIndex => $rowData) {
                if($rowIndex == 0) continue;
                $bookedBy = DB::table('hr_employee')->where('nik_employee', $rowData[0])->where('status', 'A')->first();
                $startDate = Carbon::parse($rowData[1])->format('Y-m-d');
                $endDate = Carbon::parse($rowData[2])->format('Y-m-d');

                $programStart = Carbon::parse($eventManagement->start_date)->format('Y-m-d');
                $programEnd = Carbon::parse($eventManagement->end_date)->format('Y-m-d');

                if($programStart > $startDate || $programEnd < $endDate) {
                    throw new \Exception("Attendee $bookedBy->nik_employee has an invalid start/end date ($startDate - $endDate)! Please make sure the start and end date is between $programStart and $programEnd.");
                }

                if(!$bookedBy) continue;

                $assigned = false;
                foreach($idEventPrograms as $iep) {
                    $hrea = new HrEventAttendees();
                    $hrea->id_event_program = $iep;
                    $hrea->id_company = session('id_company');
                    $hrea->id_event_management = $eventManagement->id_event_management;
                    $hrea->booked_by = $bookedBy->id_employee;
                    $hrea->national_identity_card = @$rowData[4] ?? $bookedBy->identification_number;
                    $hrea->attendee_name = @$rowData[3] ?? $bookedBy->name;
                    $hrea->attendee_email = @$rowData[5] ?? $bookedBy->private_mail;
                    $hrea->attendee_phone = @$rowData[6] ?? $bookedBy->mobile_phone;
                    $hrea->invited_by = $employee->id_employee;
                    $hrea->start_date = $startDate;
                    $hrea->end_date = $endDate;
                    $hrea->status = "Unconfirm";
                    $hrea->created_by = session('id_user');
                    $hrea->save();
                    $assigned = true;
                }
                if($assigned) array_push($assignedAttendees, $bookedBy->id_employee);
            }
            DB::commit();
            return response()->json([
                "message" => count($assignedAttendees)." attendees has been added successfully!",
            ]);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function batch_add_course_program(Request $request) {
        $request->validate([
            'id_event_management' => 'required|exists:hr_event_management,id_event_management',
            'batch_course_attachment' => 'file'
        ]);

        DB::beginTransaction();
        try {
            $type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($request->batch_course_attachment);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            // $reader->setLoadSheetsOnly('DATA PSTP');
            $spreadsheet = $reader->load($request->batch_course_attachment);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $program = HrEventManagement::find($request->id_event_management);
            $i = 0;
            foreach($sheetData as $rowIndex => $rowData) {
                if($rowIndex == 0 || !$rowData[1] || $rowData[1] == '') continue;

                $sequence = $rowData[0];
                $idCourse = $rowData[1];
                $courseDesc = $rowData[2];
                try {
                    if($rowData[3] && $rowData[3] != '') {
                        $start = Carbon::parse($rowData[3])->format('Y-m-d');
                    } else {
                        $start = $program->start_date;
                    }
                    if($rowData[4] && $rowData[4] != '') {
                        $end = Carbon::parse($rowData[4])->format('Y-m-d');
                    } else {
                        $end = $program->end_date;
                    }
                } catch(\Exception $_e) {
                    $start = Carbon::createFromFormat('Y-d-m', $rowData[3])->format('Y-m-d');
                    $end = Carbon::createFromFormat('Y-d-m', $rowData[4])->format('Y-m-d');
                }
                $passScore = $rowData[5];
                $nikTrainer = $rowData[6];
                $maxAttendees = $rowData[7] && $rowData[7] != '' ? $rowData[7] : null;
                $status = $rowData[8];

                $trainer = null;
                if($nikTrainer && $nikTrainer != '') {
                    $trainer = DB::table('hr_employee')->where('nik_employee', $nikTrainer)->where('status', 'A')->first();
                    if(!$trainer) {
                        throw new \Exception('NIK Trainer '.$nikTrainer.' at row '.($i+1).' ('.$courseDesc.') not found!', 404);
                    }
                }
                $hep = new HrEventProgram();
                $hep->id_event_management = $program->id_event_management;
                $hep->description = $courseDesc;
                $hep->id_course_header = $idCourse;
                $hep->start_date = $start;
                $hep->end_date = $end;
                $hep->maximum = $maxAttendees;
                $hep->status = $status;
                $hep->id_company = session('id_company');
                $hep->created_by = session('id_user');
                $hep->pass_scores = $passScore;
                $hep->sequence = $sequence;
                $hep->id_trainer = $trainer->id_employee;
                $hep->save();
                $i++;
            }
            DB::commit();
            return response()->json([
                "message" => $i. ' courses has been added to program '.$program->description.'.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadImportTemplate(Request $request) {
        $request->validate([
            'template' => 'required|string',
        ]);

        $links = [
            'lms.program.import_attendees' => 'LMS - Template Upload Attendees.xlsx',
            'lms.program.import_course_program' => 'LMS - Template Upload Course.xlsx',
        ];

        if(key_exists($request->template, $links) && Storage::exists("public/upload/templates/".$links[$request->template])) {
            return redirect(url('project/storage/app/public/upload/templates/'.$links[$request->template]));
        }

        return response()->json([
            "message" => "Not found",
        ], 404);
    }
}
