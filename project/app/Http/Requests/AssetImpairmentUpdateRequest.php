<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Foundation\Http\FormRequest;

class AssetImpairmentUpdateRequest extends SanitizedForm
{
   
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $parameters = $this->all();

        if(key_exists('detail', $parameters) && count($parameters['detail']) > 0) {
            array_walk($parameters['detail'], function (&$object) {
                if(key_exists('impairment_amount', $object)) {
                    $object['impairment_amount'] = str_replace('.', '', $object['impairment_amount']);
                }
            });
        }
        $this->merge($parameters);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_impairment_header' => 'nullable',
            'id_period' => 'required',
            'description' => 'required',
            // 'id_approval' => 'required',
            // 'id_document_status' => 'required',
            // 'gl_transfer_flag' => 'required',
            // 'id_je_header' => 'required',
            'status' => 'required',
            'detail.*.id_asset_category' => 'required_without:detail.*.id_asset',
            'detail.*.id_asset' => 'required_without:detail.*.id_asset_category',
            'detail.*.impairment_amount' => 'required_with:detail.*.id_asset|numeric|min:0',
            'detail.*.impairment_percentage' => 'required_with:detail.*.id_asset_category|numeric|max:100|min:0',
            'detail.*.id_account' => 'required',
            'detail.*.id_counterpart_account' => 'required',
            'detail.*.status' => 'required',
        ];
    }

    /**
     * Get the validation messages that apply if the validation condition is not met.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'detail.*.id_asset_category.required' => 'Asset category is required',
            'detail.*.id_asset.required' => 'Asset is required',
            'detail.*.impairment_amount.required_with' => 'Impairment amount is required',
            'detail.*.impairment_percentage.required_with' => 'Impairment percentage is required',
            'detail.*.id_account.required' => 'Account is required',
            'detail.*.id_counterpart_account.required' => 'Counterpart Account is required',
            'detail.*.status.required' => 'Status is required',
            'detail.*.impairment_amount.min' => 'Impairment amount cannot be a negative number',
            'detail.*.impairment_percentage.min' => 'Impairment percentage must be between 0 and 100',
            'detail.*.impairment_percentage.max' => 'Impairment percentage must be between 0 and 100',
        ];
    }
}
