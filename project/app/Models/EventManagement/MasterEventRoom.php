<?php

namespace App\Models\EventManagement;

use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterEventRoom extends Model {
    use StandardModelScope;
    use HasFactory;

    protected $table = 'public.master_event_room';
    protected $primaryKey = 'id_event_room';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_location',
        'description',
        'status',
        'id_company',
        'update_date',
        'created_by',
        'updated_by',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(MasterLocation::class, 'id_location', 'id_location');
    }

    public function scopeWithLocation($query)
    {
        $query->leftJoin((new MasterLocation())->getTable()." as _ml", "$this->table.id_location", "_ml.id_location");
        $query->select("$this->table.*", "_ml.description as location");
    }
}