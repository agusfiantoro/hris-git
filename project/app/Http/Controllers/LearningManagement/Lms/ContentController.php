<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\MasterContent;
use App\Models\LearningManagement\Lms\MasterAttachmentContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;

class ContentController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterContent::get_content();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        $button = '<button type="button" name="edit" id="' . $data->id_content_learning . '" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_content_learning . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('learning_management.lms.content.index');
    }

    protected function validateContent(Request $request) {
        $arr_form_validate = [
            'content_name'  => 'required|string',
            'notes'         => 'string',
            'content_type'  => 'required|string',
            'status'        => 'required|string',
        ];
        $arr_msg_form_validate = [
            'content_name.required' => 'The Content Name field is required',
            'notes.required'        => 'The Notes field is required',
            'content_type.required' => 'The Content Type field is required',
            'status.required'       => 'The Status field is required',
        ];

        if($request->content_type){
            if($request->content_type == 'Description'){
                $arr_form_validate['description']               = 'required';
                $arr_msg_form_validate['description.required']  = 'File Description field is required';
            } else if($request->content_type == 'Presentation'){
                if(@$request->content_attachment){
                    foreach (@$request->content_attachment as $k => $val) {
                        $arr_form_validate['content_attachment.'.$k.'.status_attachment'] = 'required';
                        $arr_msg_form_validate['content_attachment.'.$k.'.status_attachment.required'] = 'The field is required';
                        if(is_null($val['id_attachment_content'])){
                            $arr_form_validate['content_attachment.'.$k.'.attachment'] = 'required|mimes:pdf|max:6500';
                            $arr_msg_form_validate['content_attachment.'.$k.'.attachment.required'] = 'The field is required';
                            $arr_msg_form_validate['content_attachment.'.$k.'.attachment.mimes'] = 'Only (.pdf) extension type is allowed';
                        }
                    }
                }
            } else {
                $arr_form_validate['link']                  = 'required';
                $arr_msg_form_validate['link.required']     = 'The Link field is required';
            }
        }
        $request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        $this->validateContent($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'content_name'              => $request->content_name,
                'notes'                     => $request->notes,
                'link'                      => $request->link,
                'content_attachment_type'   => $request->content_type,
                'status'                    => $request->status,
                'description'               => $request->description ? SanitizedForm::stripUnsafeTagsAndAttrs($request->description) : null,
                'id_company'                => session('id_company'),
                'created_by'                => session('id_user'),
            ];
            $checkDuplicate = MasterContent::where('id_company', session('id_company'))
                                            ->where('content_name', $request->content_name)
                                            ->where('link', $request->link)
                                            ->where('notes', $request->notes)
                                            ->first();
            if($checkDuplicate) {
                throw new \Exception("Existing content with similar content name, link, and notes already exists!");
            }
            $insertContent = MasterContent::create($form_data);
            $idContent = $insertContent->id_content_learning;
            if ($request->content_attachment) {
                $filePath = 'public/upload/content_learning/'.$idContent;
                foreach ($request->content_attachment as $key => $value) {
                    if ($value['attachment']->isValid()) {
                        $fileName       = $value['attachment']->getClientOriginalName();
                        $fileName       = str_replace(' ', '-', $fileName);
                        $newFileName    = Str::random(3).'_'.$fileName;
                        $dir            = Storage::makeDirectory($filePath, 0775, true, true);
                        $storageimage   = Storage::putFileAs($filePath, $value['attachment'], $newFileName);

                        $attach = [
                            'id_content_learning'   => $idContent,
                            'attachment'            => $newFileName,
                            'status'                => $value['status_attachment'],
                            'id_company'            => session('id_company'),
                            'created_by'            => session('id_user'),
                        ];
                        MasterAttachmentContent::create($attach);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Content Saved Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function update(Request $request) {
        $this->validateContent($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'content_name'              => $request->content_name,
                'notes'                     => $request->notes,
                'link'                      => $request->link,
                'content_attachment_type'   => $request->content_type,
                'status'                    => $request->status,
                'description'               => $request->description,
                'id_company'                => session('id_company'),
                'updated_by'                => session('id_user'),
            ];

            $idContent  = $request->id_content_learning;
            $content    = MasterContent::findOrFail($idContent)->update($form_data);
            $filePath   = 'public/upload/content_learning/'.$idContent;

            if($request->content_type == 'Presentation'){
                $listIdAttachmentContent = [];
                $idAttachmentContent = [];
                if(MasterAttachmentContent::where('id_content_learning', $idContent)->first() != null){
                    $listIdAttachmentContent = MasterAttachmentContent::where('id_content_learning', $idContent)->get()->pluck('id_attachment_content')->all();
                }

                if ($request->content_attachment) {
                    foreach ($request->content_attachment as $key => $value) {
                        if(is_null($value['id_attachment_content']) && $value['attachment']->isValid()){
                            $fileName       = $value['attachment']->getClientOriginalName();
                            $fileName       = str_replace(' ', '-', $fileName);
                            $newFileName    = Str::random(3).'_'.$fileName;
                            $dir            = Storage::makeDirectory($filePath, 0775, true, true);
                            $storageimage   = Storage::putFileAs($filePath, $value['attachment'], $newFileName);

                            $form_attach = [
                                'id_content_learning'   => $idContent,
                                'attachment'            => $newFileName,
                                'status'                => $value['status_attachment'],
                                'id_company'            => session('id_company'),
                                'created_by'            => session('id_user'),
                            ];
                            MasterAttachmentContent::create($form_attach);
                        } else if($value['id_attachment_content'] != ''){
                            $idAttachmentContent[] = $value['id_attachment_content'];
                            $edit_attach = [
                                'status'                => $value['status_attachment'],
                                'id_company'            => session('id_company'),
                                'updated_by'            => session('id_user'),
                            ];
                            MasterAttachmentContent::findOrFail($value['id_attachment_content'])->update($edit_attach);
                        }
                    }
                }
                $diff = array_diff($listIdAttachmentContent, $idAttachmentContent);
                if(count($diff) > 0){
                    foreach ($diff as $key => $value) { 
                        $getAttach = MasterAttachmentContent::where('id_attachment_content', $value);
                        Storage::disk('local')->delete($filePath.'/'.$getAttach->first()->attachment);
                        $getAttach->delete();
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Content Saved Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request) {
        $id         = $request->id_content_learning;
        $data       = MasterContent::findOrFail($id);
        $attachment = MasterAttachmentContent::where('id_content_learning', $id);
        if($attachment->first() != null){
            $filePath = 'public/upload/content_learning/'.$id;
            foreach($attachment->get() as $value){
                Storage::disk('local')->delete($filePath.'/'.$value['attachment']);
            }
            $attachment->delete();
        }
        $data->delete();
    }

    public function get_content_type() {
        $result = MasterContent::get_content_type();
        return response()->json($result);
    }

    public function get_content_learning(Request $request) {
        $data = [
            'id_content_learning' => $request->id_content_learning,
            'id_company' => session('id_company')
        ];
        $result = MasterContent::get_content_learning($data);
        return response()->json($result);
    }

    public function content_file($folder, $filename) {
        $filePath = 'public/upload/content_learning/'.$folder.'/'.$filename;
        if(Storage::disk('local')->exists($filePath)){
            return response()->download(storage_path('app/'.$filePath));
        }
    }

}
