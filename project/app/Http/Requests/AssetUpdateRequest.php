<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Foundation\Http\FormRequest;

class AssetUpdateRequest extends SanitizedForm
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->sanitize();
        $this->merge([
            'in_used_flag' => $this->in_used_flag ? true : false,
            'depreciation_flag' => $this->depreciation_flag ?  true : false,
            'original_cost' => str_replace('.', '', $this->original_cost),
            'salvage_value' => str_replace('.', '', $this->salvage_value),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_asset' => 'nullable',
            'id_mass_addition' => 'nullable',
            'id_asset_category' => 'required',
            'asset_type' => 'required',
            'id_asset_group' => 'required',
            'asset_number' => 'nullable|string',
            'manufacture_name' => 'nullable|string',
            'model_number' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'tag_number'  => 'nullable|string',
            'warranty_number' => 'nullable|string',
            'warranty_date' => 'nullable',
            'receiving_date' => 'nullable',
            'tax_expired_date' => 'nullable',
            'id_parent_asset' => 'nullable',
            'in_used_flag' => 'nullable',
            'depreciation_flag' => 'nullable',
            'depreciation_start_date' => 'nullable',
            'description' => 'required|string',
            'original_cost' => 'required',
            'adjusted_cost' => 'nullable',
            'depreciation_cost' => 'nullable',
            'current_cost' => 'nullable',
            'salvage_type' => 'required_if:depreciation_flag,true',
            'salvage_value' => 'required_if:depreciation_flag,true',
            'property_type' => 'required',
            'ownership' => 'required',
            'bought' => 'required',
            'leased_number' => 'nullable',
            'lease_effective_date' => 'nullable',
            'lease_expired_date' => 'nullable',
            'lease_contract_expired_date' => 'nullable',
            'partner_name' => 'nullable|string',
            'receiving_number' => 'nullable|string',
            'purchase_invoice_number' => 'nullable|string',
            'project_number' => 'nullable|string',
            'batch_number' => 'nullable|string',
            'current_units' => 'required|numeric|min:1|max:1',
            'id_depreciation_method' => 'required_if:depreciation_flag,true',
            'depreciation_start_date' => 'required_if:depreciation_flag,true',
            'life_in_month' => 'required_if:depreciation_flag,true|numeric|min:0',
            'queue_process_status' => 'required',
            'status' => 'required|in:A,I',
            'reference_number' => 'nullable',
            'notes_1' => 'nullable|string',
            'notes_2' => 'nullable|string',
            'notes_3' => 'nullable|string',
            'notes_4' => 'nullable|string',
            'notes_5' => 'nullable|string',
            'notes_6' => 'nullable|string',
            'notes_7' => 'nullable|string',
            'notes_8' => 'nullable|string',
            'purchase_date' => 'nullable',
            'detail.*.transaction_date' => 'required',
            'detail.*.id_employee' => 'nullable',
            'detail.*.unit_assigned' => 'required|numeric|min:1|max:'.$this->current_units,
            'detail.*.id_branch' => 'nullable',
            'detail.*.id_location' => 'nullable',
            'detail.*.id_asset_location' => 'nullable',
            'detail.*.status' => 'required',
            'image.*.id_image_asset' => 'nullable',
            'image.*.attachment' => 'required|file|image',
            'image.*.note' => 'required|string',
            'image.*.status' => 'required|in:A,I',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            'original_cost.required' => 'Asset acquisition cost is required',
            'detail.*.transaction_date.required' => 'Transaction date is required',
            'detail.*.id_employee.required' => 'Employee is required',
            'detail.*.unit_assigned.required' => 'Unit assigned is required',
            'detail.*.unit_assigned.max' => 'Unit assigned cannot be more than total asset unit',
            'detail.*.id_branch.required' => 'Branch is required',
            'detail.*.id_location.required' => 'Location is required',
            'detail.*.id_asset_location.required' => 'Room is required',
            'detail.*.status.required' => 'Status is required',
        ];
    }
}
