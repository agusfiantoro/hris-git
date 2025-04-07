<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Foundation\Http\FormRequest;

class AssetTransferUpdateRequest extends SanitizedForm
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_transfer_header' => 'nullable',
            'need_date' => 'required',
            'description' => 'required',
            'status' => 'required|in:A,I',
            'submission_type' => 'required|in:draft,submit',
            'detail.*.id_employee_destination' => 'nullable',
            'detail.*.id_account_destination' => 'nullable',
            'detail.*.id_branch_destination' => 'nullable',
            'detail.*.id_location_destination' => 'nullable',
            'detail.*.id_asset_location_destination' => 'nullable',
            'detail.*.unit_assigned' => 'nullable',
            'detail.*.effective_date' => 'nullable',
            'detail.*.status' => 'required|in:A,I',
        ];
    }
}
