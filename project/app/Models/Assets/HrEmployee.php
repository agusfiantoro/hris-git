<?php

namespace App\Models\Assets;

use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEmployee extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'public.hr_employee';
    protected $primaryKey = 'id_employee';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'name',
    ];

}