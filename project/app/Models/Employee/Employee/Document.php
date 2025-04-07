<?php

namespace App\Models\Employee\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Document extends Model {

    use HasFactory;

    protected $table = 'hr_document_employee';
    protected $primaryKey = 'id_document_employee';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_document_employee','id_employee','document_name', 'document_number', 'effective_date', 'expired_date', 'attachment', 'id_company', 'created_by', 'updated_by'
    ];

}
