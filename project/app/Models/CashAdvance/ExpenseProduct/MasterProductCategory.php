<?php

namespace App\Models\CashAdvance\ExpenseProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterProductCategory extends Model
{
    // use HasFactory;
	protected $table="inventory.master_product_categories";
	protected $primaryKey="id_product_categories";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}