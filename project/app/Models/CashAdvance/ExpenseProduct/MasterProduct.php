<?php

namespace App\Models\CashAdvance\ExpenseProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterProduct extends Model
{
    // use HasFactory;
	protected $table="inventory.master_product";
	protected $primaryKey="id_product";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

    public static function getData() {
        $sql = "SELECT 
                    mp.*,
                    mum.description AS unit_description,
                    mpc.description AS category
                FROM inventory.master_product mp
                JOIN inventory.master_unit_measures mum 
                ON mp.id_uom = mum.id_uom
                JOIN inventory.master_product_categories mpc
                ON mp.id_product_categories = mpc.id_product_categories
                WHERE mp.id_company = ?
                ";
        return DB::select($sql, [session('id_company')]);
    }
}