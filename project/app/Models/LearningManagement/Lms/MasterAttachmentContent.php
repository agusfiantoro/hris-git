<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterAttachmentContent extends Model {

    use HasFactory;

    protected $table = 'master_attachment_content';
    protected $primaryKey = 'id_attachment_content';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_attachment_content', 'id_content_learning', 'attachment', 'status', 'id_company', 'created_by', 'updated_by'
    ];


}
