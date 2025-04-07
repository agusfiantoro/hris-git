<?php

namespace App\Models\Eletter\MasterEletter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGeneralLetter extends Model
{
    // use HasFactory;
	protected $table="master_general_data";
	protected $primaryKey="id_general_data";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}
