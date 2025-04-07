<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Foundation\Http\FormRequest;

class AssetRevaluationUpdateRequest extends SanitizedForm
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
                if(key_exists('revaluation_amount', $object)) {
                    $object['revaluation_amount'] = str_replace('.', '', $object['revaluation_amount']);
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
            'id_revaluation_header' => 'nullable',
            'id_period' => 'required',
            'description' => 'required',
            'status' => 'required',
            'detail.*.id_asset_category' => 'required_without:detail.*.id_asset',
            'detail.*.id_asset' => 'required_without:detail.*.id_asset_category',
            'detail.*.revaluation_amount' => 'required_with:detail.*.id_asset|numeric|min:0',
            'detail.*.revaluation_percentage' => 'required_with:detail.*.id_asset_category|numeric|min:0|max:100',
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
            'detail.*.id_asset_category.required_without' => 'Asset category is required',
            'detail.*.id_asset.required_without' => 'Asset is required',
            'detail.*.revaluation_amount.required_with' => 'Revaluation amount is required',
            'detail.*.revaluation_percentage.required_with' => 'Revaluation percentage is required',
            'detail.*.id_account.required' => 'Account is required',
            'detail.*.id_counterpart_account.required' => 'Counterpart Account is required',
            'detail.*.status.required' => 'Status is required',
            'detail.*.revaluation_amount.min' => 'Impairment amount cannot be a negative number',
            'detail.*.revaluation_percentage.min' => 'Impairment percentage must be between 0 and 100',
            'detail.*.revaluation_percentage.max' => 'Impairment percentage must be between 0 and 100',
        ];
    }
}
