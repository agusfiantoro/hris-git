<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;

class AssetRetirementUpdateRequest extends SanitizedForm
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_retirement_header' => 'nullable',
            'transaction_type' => 'required',
            'id_period' => 'required',
            'description' => 'required|string',
            // 'id_approval' => 'required',
            // 'id_document_status' => 'required',
            // 'gl_transfer_flag' => 'required',
            // 'id_je_header' => 'required',
            'is_submit' => 'required',
            'status' => 'required',
            'detail.*.id_retirement_detail' => 'nullable',
            'detail.*.id_asset' => 'required',
            'detail.*.id_branch_destination' => 'nullable',
            'detail.*.id_location_destination' => 'nullable',
            'detail.*.id_asset_location_destination' => 'nullable',
            'detail.*.unit_assigned' => 'required|numeric',
            'detail.*.effective_date' => 'required|date',
            'detail.*.id_account' => 'required',
            'detail.*.id_counterpart_account' => 'required',
            'detail.*.status' => 'required|in:A,I',
        ];
    }
}
