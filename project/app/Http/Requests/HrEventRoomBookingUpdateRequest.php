<?php

namespace App\Http\Requests;

use App\Http\Requests\Support\SanitizedForm;
use App\Models\EventManagement\HrEventManagement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class HrEventRoomBookingUpdateRequest extends SanitizedForm
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if($this->id_event_management) {
            $event = HrEventManagement::findOrFail($this->id_event_management);
            $eventResponsibles = DB::table('public.relation_responsible_event as rre')
                                    ->join('hr_employee as he', 'rre.id_employee', 'he.id_employee')
                                    ->where('rre.id_event_management', $event->id_event_management)
                                    ->get(['he.id_user']);
            $allowList = array_merge([$event->created_by], $eventResponsibles->pluck('id_user')->toArray());
            if(!in_array(session('id_user'), $allowList)) {
                return false;
            }
        }
        
        return true;
    }
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_event_management' => 'nullable',
            'id_event_room' => 'required',
            'id_event_type' => 'required',
            'date' => 'required|before_or_equal:'.now()->addDays(5)->format('Y-m-d'),
            'time' => 'required',
            'description' => 'required|string',
            'status' => 'required|in:A,I',
            'responsible' => 'nullable|array',
            'long_description' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id_event_room.required' => 'Event room field is required',
            'id_event_type.required' => 'Event type field is required',
            'time.required' => 'Time field is required',
            'description.required' => 'Meeting description field is required',
            'long_description.string' => 'Participant field must be string',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $time = explode(' to ', $this->time);
        if(count($time) < 2) {
            throw new \Exception("Invalid time");
        }
        $time = (object)[
            'start' => $this->date." ".$time[0],
            'end' => $this->date." ".$time[1],
        ];
        $this->merge([
            'time' => $time,
            'responsible' => $this->responsible ? array_filter($this->responsible, function($responsible) {
              return $responsible != '';
            }) : [],
        ]);
    }
}
