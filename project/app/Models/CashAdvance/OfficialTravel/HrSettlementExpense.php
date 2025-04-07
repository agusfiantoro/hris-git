<?php

namespace App\Models\CashAdvance\OfficialTravel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Organization\MasterOrganization\MasterRegional;
use Carbon\Carbon;

class HrSettlementExpense extends Model
{
    protected $table = "hr_settlement_expense";
	protected $primaryKey="id_settlement_expense";

    const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_cash_advance', 
        'id_cash_payment',
        'id_product', 
        'description', 
        'notes', 
        'qty', 
        'id_currency_settlement_expense', 
        'currency_rate_settlement_expense', 
        'unit_price', 
        'status', 
        'id_company', 
        'created_by', 
        'id_uom', 
        'total_approval_amount', 
        'attachment', 
        'is_verified_by_chief', 
        'is_verified_by_finance', 
        'id_branch', 
        'settlement_date', 
        'is_validate', 
        'approval_price_by_chief', 
        'approval_price_by_finance', 
        'is_paid_by_hr',
        'id_expense_account',
    ];

    public static function getData($id, $useOfficialTravel = true) {
        $sql = "SELECT her.*, mp.description as desc_product FROM hr_expense_request her 
        JOIN hr_cash_advance hca ON her.id_cash_advance = hca.id_cash_advance ";

        if($useOfficialTravel) {
            $sql .= "JOIN hr_official_travel hot ON hca.id_official_travel = hot.id_official_travel 
            JOIN inventory.master_product mp ON her.id_product = mp.id_product 
            WHERE hot.id_official_travel = ?";
            return DB::select($sql, [$id]);
        } else {
            $sql .= "JOIN inventory.master_product mp ON her.id_product = mp.id_product
                    WHERE hca.id_cash_advance = ?";
            return DB::select($sql, [$id]);
        }
        
    }

    public static function getTransportAndAccommodation($idCashAdvance) {
        $sql = "SELECT
                    hse.*,
                    mp.description AS product_description,
                    mge.max_price AS product_max_price,
                    coalesce(mge.id_region,mr.id_region) as id_region,
                    mum2.description AS desc_uom,
                    mp.id_uom AS mp_id_uom,
                    mum.code AS mum_code_uom,
                    mum.description AS mum_desc_uom,
                    mb.description as desc_branch
                FROM
                    public.hr_settlement_expense hse
                JOIN hr_cash_advance hca ON
                    hse.id_cash_advance = hca.id_cash_advance
                LEFT JOIN hr_official_travel hot ON
                    hca.id_official_travel = hot.id_official_travel
                JOIN master_position_detail mpd ON
                    hca.id_employee = mpd.id_employee
                    AND mpd.secondary_position = FALSE
                JOIN master_position_routing mpr ON
                    mpd.id_position_routing = mpr.id_routing
                LEFT JOIN master_branch mb ON
                    hse.id_branch = mb.id_branch
                LEFT JOIN master_region mr
                    ON mb.id_region = mr.id_region
                LEFT JOIN inventory.master_product mp ON
                    hse.id_product = mp.id_product
                LEFT JOIN master_grade_expenses mge ON
                    mp.id_product = mge.id_product
                    AND mge.id_branch = mb.id_branch
                    AND mge.id_job_grade = mpr.id_job_grade
                JOIN inventory.master_unit_measures mum ON
                    mp.id_uom = mum.id_uom
                JOIN inventory.master_unit_measures mum2 ON
                    hse.id_uom = mum2.id_uom
                WHERE
                    hse.id_cash_advance = ?
                    AND hse.status = 'A'
                    AND hse.is_paid_by_hr = TRUE
                ORDER BY
                    hse.settlement_date
                ";
        return DB::select($sql, [$idCashAdvance]);
    }

    public static function getSettlements($idCashAdvance, $validatedOnly = false, $mode = null) {
        $isValidate = "";
        $filterHr = "";
        if($mode == "hr") {
            $filterHr = "AND hse.is_paid_by_hr = TRUE";
        } else if($mode == "finance") {
            $isValidate = $validatedOnly == TRUE ? "AND hse.is_validate = TRUE" : "";
            $filterHr = "AND hse.is_paid_by_hr = FALSE";
        } else {
            $filterHr = "AND hse.is_paid_by_hr = FALSE";
        }
        return DB::select("SELECT 
                * 
                FROM sp_funct_detail_settlement_expense(?,?) AS hse--dse
                -- JOIN hr_settlement_expense hse ON dse.id_settlement_expense = hse.id_settlement_expense
                WHERE 1=1
                $isValidate
                $filterHr"
                , [session('id_company'), $idCashAdvance]);
        // $sql = "SELECT
        //             hse.*,
        //             mp.description AS product_description,
        //             mge.max_price AS product_max_price,
        //             mge.id_position_routing AS grade_expense_position_routing,
        //             coalesce(mge.id_region,mr.id_region) as id_region,
        //             mum2.description AS desc_uom,
        //             mp.id_uom AS mp_id_uom,
        //             mum.code AS mum_code_uom,
        //             mum.description AS mum_desc_uom,
        //             mb.description as desc_branch,
        //             he.name AS finance_approval,
        //             he.nik_employee AS finance_nik,
        //             coalesce(hse.is_verified_by_finance::integer * hse.approval_price_by_finance, hse.is_verified_by_chief::integer * hse.approval_price_by_chief) as print_settlement_price,
        //             hca.id_employee
        //         FROM
        //             public.hr_settlement_expense hse
        //         JOIN hr_cash_advance hca ON
        //             hse.id_cash_advance = hca.id_cash_advance
        //         LEFT JOIN hr_official_travel hot ON
        //             hca.id_official_travel = hot.id_official_travel
        //         JOIN master_position_detail mpd ON
        //             hca.id_employee = mpd.id_employee
        //             AND mpd.secondary_position = FALSE
        //         JOIN master_position_routing mpr ON
        //             mpd.id_position_routing = mpr.id_routing
        //         LEFT JOIN master_branch mb ON
        //             hse.id_branch = mb.id_branch
        //         LEFT JOIN master_region mr
        //             ON mb.id_region = mr.id_region
        //         LEFT JOIN inventory.master_product mp ON
        //             hse.id_product = mp.id_product
        //         LEFT JOIN master_grade_expenses mge ON
        //             mp.id_product = mge.id_product
        //             AND mge.id_branch = mb.id_branch
        //             AND mge.id_job_grade = mpr.id_job_grade
        //         JOIN inventory.master_unit_measures mum ON
        //             mp.id_uom = mum.id_uom
        //         JOIN inventory.master_unit_measures mum2 ON
        //             hse.id_uom = mum2.id_uom
        //         LEFT JOIN hr_employee he ON
        //             hse.approved_by_finance = he.id_employee
        //         WHERE
        //             hse.id_cash_advance = ?
        //             AND hse.status = 'A'
        //             $isValidate
        //             $filterHr
        //         ORDER BY
        //             hse.id_product,
        //             hse.settlement_date
        //         ";
        // $result = DB::select($sql, [$idCashAdvance]);
        // $result = collect($result);
        // $idEmployee = $result[0]->id_employee;
        // $empRouting = DB::selectOne("SELECT mpr.* FROM hr_employee he 
        //                     JOIN master_position_detail mpd ON he.id_employee = mpd.id_employee 
        //                     JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing
        //                     WHERE he.id_employee = ?
        //                     AND he.status = 'A'
        //                     AND mpd.id_company = ?
        //                     AND mpd.secondary_position = FALSE", [$idEmployee, session('id_company')]);
        // $returnData = [];
		// $returnData[] = $result->where("grade_expense_position_routing", $empRouting->id_routing);
		// $returnData[] = $result->where("grade_expense_position_routing", null);
        // foreach($returnData[0] as $k => $dataWithRouting) {
		// 	foreach($returnData[1] as $key => $dataWithoutRouting) {
		// 		if(@$dataWithRouting->id_settlement_expense == @$dataWithoutRouting->id_settlement_expense) {
		// 			unset($returnData[1][$key]);
		// 		}
		// 	}
		// }
		// $returnData = collect(array_merge($returnData[0]->toArray(), $returnData[1]->toArray()))
        //             ->sortBy(['id_product', 'creation_date'])
        //             ->toArray();
		// $ret = [];
		// foreach($returnData as $r) {
		// 	$ret[] = $r;
		// }
        // return $ret;
    }
    public static function getCurrencies() {
        $sql = "SELECT 
                    id_currency as id,
                    concat(currency_code, ' - ', long_description) as text
                FROM master_currency WHERE status = 'A'";
        $sql2 = "SELECT 
                    mcurr.id_currency as id,
                    concat(mcurr.currency_code, ' - ', mcurr.long_description) as text,
                    mcurr.currency_symbol
                FROM master_company mcomp
                JOIN master_currency mcurr ON mcomp.id_currency = mcurr.id_currency
                WHERE mcomp.status = 'A' AND mcomp.id_company = ?";
        return [
            "all" => DB::select($sql),
            "company" => DB::selectOne($sql2, [session('id_company')])
        ];
    }

    public static function getRegions($idCashAdvance) {
        $regions = DB::select("SELECT
                        -- her.id_branch,
                        -- mb.description AS desc_branch,
                        mr.id_region AS id,
                        mr.description AS text
                    FROM
                        hr_expense_request her
                    LEFT JOIN master_branch mb
                        ON her.id_branch = mb.id_branch
                    LEFT JOIN master_region mr 
                        ON mb.id_region = mr.id_region
                    WHERE
                        her.id_cash_advance = ?
                        AND her.id_branch IS NOT NULL
                    GROUP BY her.id_branch, mb.description, mr.id_region", 
        [$idCashAdvance]);
        if(count($regions) < 1) {
            $regions = DB::select("SELECT id_region AS id, description AS text FROM master_region WHERE id_company = ? AND status = 'A'", [session('id_company')]);
        }
        return $regions;
    }

    public static function settlementSummary($idCashAdvance) {
        $result = DB::select("select 
                                sse.settlement_date,
                                sse.product_name AS description,
                                sse.budget AS budget_price,
                                sse.total_settlement_amount AS total_settlement_expense,
                                sse.approval_price_by_chief AS approval_by_chief,
                                sse.approval_price_by_finance AS final_approval,
                                sse.approval_price_by_finance AS approval_by_finance
                            from sp_funct_summary_settlement_expense(?, ?) as sse", [session('id_company'), $idCashAdvance]);
        $result = collect($result);
        return $result;
    }

    public static function getBudget($request) {
        $position = DB::table('hr_employee as he')
                        ->join('master_position_detail as mpd', 'he.id_employee', 'mpd.id_employee')
                        ->join('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
                        ->where('mpd.secondary_position', false)
                        ->where('he.status', 'A')
                        ->where('he.id_user', session('id_user'))
                        ->select('mpr.*')
                        ->first();
        $query = DB::selectOne("SELECT 
                            mge3.*
                        FROM inventory.master_product mp 
                        LEFT JOIN master_grade_expenses mge 
                            ON mp.id_product = mge.id_product
                            AND mge.id_position_routing = $position->id_routing
                            AND mge.id_product = $request->id_product
                            AND mge.id_job_grade = $position->id_job_grade
                            AND mge.id_region = $request->id_region
                            AND mge.id_branch = $request->id_branch
                        LEFT JOIN master_grade_expenses mge2 
                            ON mp.id_product = mge2.id_product
                            AND mge2.id_position_routing IS NULL
                        JOIN master_grade_expenses mge3 
                            ON COALESCE(mge.id_grade_expense, mge2.id_grade_expense) = mge3.id_grade_expense
                        WHERE 
                            mge2.id_product = $request->id_product
                            AND mge2.id_job_grade = $position->id_job_grade
                            AND mge2.id_region = $request->id_region
                            AND mge2.id_branch = $request->id_branch");
        return $query;
    }
}