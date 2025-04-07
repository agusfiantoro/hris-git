<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;

class AssetReinstatementUpdateRequest extends SanitizedForm
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_reinstate_header' => 'nullable',
            'id_period' => 'required',
            'id_retirement_header_source' => 'required',
            'description' => 'required|string',
            // 'id_approval' => 'required',
            // 'id_document_status' => 'required',
            // 'gl_transfer_flag' => 'required',
            // 'id_je_header' => 'required',
            'is_submit' => 'required',
            'status' => 'required',
            'detail.*.id_reinstate_detail' => 'nullable',
            'detail.*.id_retirement_detail_source' => 'required',
            'detail.*.effective_date' => 'required|date',
            'detail.*.status' => 'required|in:A,I',
        ];
    }
}
