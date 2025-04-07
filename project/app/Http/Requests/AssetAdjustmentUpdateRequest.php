<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Foundation\Http\FormRequest;

class AssetAdjustmentUpdateRequest extends SanitizedForm
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
                $object['current_cost'] = trim(str_replace(".", "", $object['current_cost']), " ");
                $object['adjusted_cost'] = trim(str_replace(".", "", $object['adjusted_cost']), " ");
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
            'id_adjustment_header' => 'nullable',
            'id_period' => 'required',
            'description' => 'required',
            'status' => 'required',
            'is_submit' => 'required',
        ];
    }
}
