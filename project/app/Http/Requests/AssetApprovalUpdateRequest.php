<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Foundation\Http\FormRequest;

class AssetApprovalUpdateRequest extends SanitizedForm
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_approval' => 'nullable',
            'description' => 'required',
            'hierarchy_type' => 'required',
            'approval_mode' => 'required',
            'id_approval_doc_type' => 'required',
            'id_job_grade' => 'nullable',
            'is_auto_approved' => 'nullable',
            'note' => 'nullable',
            'status' => 'required|in:A,I',
            'detail.*.id_approval_detail' => 'nullable',
            'detail.*.sequence' => 'required',
            // 'detail.*.id_position_detail' => 'required',
            'detail.*.id_employee' => 'required',
            'detail.*.id_approval_mode' => 'required',
            'detail.*.limit' => 'nullable',
            'detail.*.note' => 'nullable',
            'detail.*.status' => 'required|in:A,I',
        ];
    }
}
