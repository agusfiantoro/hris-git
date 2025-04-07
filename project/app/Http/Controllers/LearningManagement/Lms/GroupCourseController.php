<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\MasterGroupCourse;
use App\Models\LearningManagement\Lms\MasterCourseHeader;
use App\Models\LearningManagement\Lms\MasterCourseDetail;
use App\Models\LearningManagement\Lms\MasterContent;
use App\Models\LearningManagement\Lms\HrSurveyQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;

class GroupCourseController extends Controller {

    public function index(Request $request) {

        $getDepartment = MasterGroupCourse::getDepartment();

        if ($request->ajax()) {
            $data = MasterGroupCourse::getGroupCourse();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        return '';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('learning_management.lms.group_course.index', compact('getDepartment'));
    }

    protected function validate_form(Request $request) {
        $arr_form_validate = [
            'group_name'    => 'required|string',
            'status'        => 'required|string',
        ];
        $arr_msg_form_validate = [
            'group_name.required'   => 'The Group Course name is required',
            'status.required'       => 'The Status field is required',
        ];

        if($request->group_course){
            $arr_form_validate['group_course.*.id_course_header']  = 'required|string';
            $arr_form_validate['group_course.*.status']       = 'required|string';
            $arr_msg_form_validate['group_course.*.id_course_header.required']    = 'Course field is required';
            $arr_msg_form_validate['group_course.*.status.required']         = 'Status field is required';
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'id_dept'       => $request->department,
                'id_job_grade'  => $request->job_grade,
                'group_name'    => $request->group_name,
                'description'   => $request->description,
                'status'        => $request->status,
                'id_company'    => session('id_company'),
                'created_by'    => session('id_user'),
                'creation_date' => date('Y-m-d H:i:s'),
            ];
            $insertGroupCourse = MasterGroupCourse::create($form_data);
            $idGroupCourse = $insertGroupCourse->id_group_course;
            if ($request->group_course) {
                foreach ($request->group_course as $key => $value) {
                    $detail = [
                        'id_group_course'       => $idGroupCourse,
                        'id_course_header'      => $value['id_course_header'],
                        'status'                => $value['status'],
                        'id_company'            => session('id_company'),
                        'created_by'            => session('id_user'),
                    ];
                    DB::table('relation_group_course')->insert($detail);
                }
            }
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Group Course Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function update(Request $request) {
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'id_dept'       => $request->department,
                'id_job_grade'  => $request->job_grade,
                'group_name'    => $request->group_name,
                'description'   => $request->description,
                'status'        => $request->status,
                'id_company'    => session('id_company'),
                'created_by'    => session('id_user'),
            ];
            $idGroupCourse      = $request->id_group_course;
            $groupCourse        = MasterGroupCourse::findOrFail($idGroupCourse)->update($form_data);

            $listIdRelationCourse    = [];
            $idRelationCourse        = [];
            if(DB::table('relation_group_course')->where('id_group_course', $idGroupCourse)->first() != null){
                $listIdRelationCourse = DB::table('relation_group_course')->where('id_group_course', $idGroupCourse)->get()->pluck('id_relation_course')->all();
            }
            if ($request->group_course) {
                foreach ($request->group_course as $key => $value) {
                    if($value['id_relation_course'] == ''){
                        $detail = [
                            'id_group_course'       => $idGroupCourse,
                            'id_course_header'      => $value['id_course_header'],
                            'status'                => $value['status'],
                            'id_company'            => session('id_company'),
                            'created_by'            => session('id_user'),
                        ];
                        DB::table('relation_group_course')->insert($detail);
                    } else {
                        $idRelationCourse[] = $value['id_relation_course'];
                        $edit_detail = [
                            'id_group_course'       => $idGroupCourse,
                            'id_course_header'      => $value['id_course_header'],
                            'status'                => $value['status'],
                            'id_company'            => session('id_company'),
                            'updated_by'            => session('id_user'),
                            'update_date'           => date('Y-m-d H:i:s'),
                        ];
                        DB::table('relation_group_course')->where('id_relation_course', $value['id_relation_course'])->update($edit_detail);
                    }
                }
            }
            $diff = array_diff($listIdRelationCourse, $idRelationCourse);
            if(count($diff) > 0){
                foreach ($diff as $key => $value) { 
                    DB::table('relation_group_course')->where('id_relation_course', $value)->delete();
                }
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Content Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request) {
        $id         = $request->id_group_course;
        $group      = MasterGroupCourse::findOrFail($id);
        if(DB::table('relation_group_course')->where('id_group_course', $id)->first() != null){
            DB::table('relation_group_course')->where('id_group_course', $id)->delete();
        }
        $group->delete();
    }

    public function getCourse(Request $request) {
        $result = MasterGroupCourse::getCourse();
        return response()->json($result);
    }

    public function getGroupCourse(Request $request) {
        $id     = $request->id_group_course;
        $group  = MasterGroupCourse::getGroupCourse($id);
        $detail = MasterGroupCourse::getGroupDetail($id);
        $return = (count($group) > 0) ? $group[0] : [];
        $return->detail = (count($detail) > 0) ? $detail : [];

        return response()->json($return);
    }

}
