<?php

namespace App\Models\EventManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrEventAttendees extends Model {

    use HasFactory;

    protected $table = 'hr_event_attendees';
    protected $primaryKey = 'id_event_attendees';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_event_attendees', 'id_event_management', 'id_event_program', 'booked_by', 'national_identity_card', 'attendee_name', 'attendee_email', 'attendee_phone', 'start_date', 'end_date', 'attachment', 'notes', 'invited_by', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
