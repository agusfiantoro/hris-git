<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Foundation\Http\FormRequest;

class JournalEntryUpdateRequest extends SanitizedForm
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->sanitize();
        $parameters = $this->all();

        if(key_exists('detail', $parameters) && count($parameters['detail']) > 0) {
            array_walk($parameters['detail'], function (&$object) {
                $object['entered_debit_amount'] = str_replace('.', '', $object['entered_debit_amount']);
                $object['entered_credit_amount'] = str_replace('.', '', $object['entered_credit_amount']);
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
            'id_je_header' => 'required',
            'description' => 'required|string',
            'id_period' => 'required',
            'accounting_date' => 'required',
            'id_je_source' => 'required',
            'id_je_category' => 'required',
            'id_currency' => 'required',
            'currency_rate' => 'required',
            'status' => 'required|in:A,I',
            'detail.*.id_je_line' => 'nullable',
            'detail.*.id_account' => 'required',
            'detail.*.description' => 'required|min:1|string',
            'detail.*.entered_debit_amount' => 'required',
            'detail.*.entered_credit_amount' => 'required',
            'detail.*.status' => 'required|in:A,I',
        ];
    }
}
