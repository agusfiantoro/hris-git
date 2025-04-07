<?php

namespace App\Models\Assets;

use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJobGrade extends Model {
    use HasFactory;

    protected $table = 'public.master_job_grade';
    protected $primaryKey = 'id_job_grade';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'description',
        'job_class_group',
        'status',
        'inactive_date',
        'id_company',
        'created_by',
        'updated_by',
        'max_hiring_days',
        'job_level',
    ];

    /**
     * Scope a query to only show master approval data from current company (by active session)
     */
    public function scopeCurrentCompany($query) {
        $query->where('id_company', session('id_company'));
    }

    /**
     * Scope a query to only show master approval data from current company (by active session)
     */
    public function scopeActive($query) {
        $query->where('status', 'A');
    }
}