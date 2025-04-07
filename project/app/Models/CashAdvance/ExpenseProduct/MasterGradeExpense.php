<?php

namespace App\Models\CashAdvance\ExpenseProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterGradeExpense extends Model
{
    // use HasFactory;
	protected $table="master_grade_expenses";
	protected $primaryKey="id_grade_expense";

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

    public static function get_grade_expenses() {
        $gradeExpenses = DB::select("SELECT
                mge.id_grade_expense AS id_grade_expense,
                mjg.description AS job_grade,
                mp.description AS product,
                mr.description AS region,
                mb.description AS branch,
                mge.description AS description,
                mge.notes AS notes,
                mge.min_price AS min_price,
                mge.max_price AS max_price,
                mge.status AS status,
                mpr.description AS position
            FROM
                master_grade_expenses mge
            JOIN master_job_grade mjg ON
                mge.id_job_grade = mjg.id_job_grade
            JOIN inventory.master_product mp ON
                mge.id_product = mp.id_product
            JOIN master_region mr ON
                mge.id_region = mr.id_region
            JOIN master_branch mb ON
                mge.id_branch = mb.id_branch
            LEFT JOIN master_position_routing mpr ON
                mge.id_position_routing = mpr.id_routing 
            WHERE
                mge.id_company = ?
                --and status = 'A'
            ", 
        [session('id_company')]);
        return $gradeExpenses;
    }

    public static function get_grade_expenses_by_id($id_grade_expense) {
        $gradeExpenses = DB::select("SELECT 
            mge.id_grade_expense as id_grade_expense, 
            mge.id_job_grade as id_job_grade,
            mjg.description as job_grade, 
            mge.id_product as id_product,
            mp.description as product, 
            mge.id_region as id_region, 
            mr.description as region,
            mge.id_branch as id_branch,
            mb.description as branch,
            mge.description as description,
            mge.notes as notes,
            mge.min_price as min_price,
            mge.max_price as max_price,
            mge.status as status,
            mge.id_position_routing
            FROM master_grade_expenses mge 
            join master_job_grade mjg on mge.id_job_grade = mjg.id_job_grade
            join inventory.master_product mp on mge.id_product = mp.id_product
            join master_region mr on mge.id_region = mr.id_region 
            join master_branch mb on mge.id_branch = mb.id_branch 
            WHERE mge.id_grade_expense = ?", 
        [$id_grade_expense]);
        return $gradeExpenses;
    }

    public static function get_region() {
        $regions = DB::select("SELECT id_region as id, description as text FROM master_region WHERE status = 'A' and id_company = ?", [session('id_company')]);
        return $regions;
    }

    public static function get_job_grade() {
        $jobGrades = DB::select("SELECT id_job_grade as id, description as text FROM master_job_grade WHERE status = 'A' and id_company = ?", [session('id_company')]);
        return $jobGrades;
    }

    public static function get_product() {
        $products = DB::select("SELECT id_product as id, description as text FROM inventory.master_product WHERE status = 'A' and expenseable = true and id_company = ?", [session('id_company')]);
        return $products;
    }

    public static function get_branch($region_id) {
        $branches = DB::select("SELECT id_branch as id, description as text FROM master_branch WHERE status = 'A' and id_region = ? and id_company = ?", [$region_id, session('id_company')]);
        return $branches;
    }

    public static function get_row_by_id($id) {
        $row = DB::select("SELECT * FROM master_grade_expenses WHERE id_grade_expense = ?", [$id]);
        return $row;
    }
}
