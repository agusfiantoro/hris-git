<?php

namespace App\Models\CashAdvance\OfficialTravel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Organization\MasterOrganization\MasterRegional;
use Carbon\Carbon;

class HrCashPayment extends Model
{
    protected $table = "hr_cash_payment";
	protected $primaryKey="id_cash_payment";

    const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

    public static function getPayments($idCashAdvance) {
        $sql = "SELECT
                    hcp.*,
                    mb.description as bank_from,
                    mba.account_name as mba_bank_from_account,
                    mb2.description as bank_to,
                    mba2.account_name as mba_bank_to_account,
                    hca.reference_number,
                    hbe.bank_name as employee_bank_name,
                    hbe.bank_account as employee_bank_account,
                    hbe.account_name as employee_bank_account_name,
                    CASE
                        WHEN hcp.created_by = ? THEN TRUE
                        ELSE FALSE
                    END AS can_apply_payment
                FROM hr_cash_payment hcp
                JOIN master_bank mb ON hcp.id_bank_from = mb.id_bank
                JOIN accounting.master_bank_account mba ON hcp.id_bank_from_account = mba.id_bank_account
                JOIN master_bank mb2 ON hcp.id_bank_to = mb2.id_bank
                LEFT JOIN accounting.master_bank_account mba2 ON hcp.id_bank_to_account = mba2.id_bank_account
                JOIN hr_cash_advance hca ON hcp.id_cash_advance = hca.id_cash_advance
                LEFT JOIN hr_employee he ON hcp.id_employee_bank_to = he.id_employee
                LEFT JOIN hr_bank_employee hbe ON hcp.id_employee_bank_to = hbe.id_bank_employee
                WHERE hcp.id_cash_advance = ? AND hcp.status = 'A'
                ";
        return DB::select($sql, [session('id_user'), $idCashAdvance]);
    }

    public static function getPaymentBankTo($idCashAdvance) {
        $sql = "SELECT 
                    hca.id_cash_advance,
                    hca.id_employee,
                    hbe.id_bank_employee,
                    hbe.id_bank,
                    hbe.bank_name,
                    hbe.bank_account,
                    hbe.account_name,
                    hbe.bank_currency
                FROM hr_cash_advance hca
                LEFT JOIN hr_bank_employee hbe
                    ON hca.id_employee = hbe.id_employee AND hbe.status = 'A' AND hbe.default_bank = TRUE
                WHERE hca.id_cash_advance = ?
                ";
        return DB::selectOne($sql, [$idCashAdvance]);
    }

    public static function getHrPayment($idCompany) {
        return DB::select("SELECT 
                            id_bank_account AS id, 
                            CONCAT(mba.account_name, ' - ', mb.bank_code, ' - ', mba.account_number, ' (', mba.branch_name, ')') AS text
                            FROM accounting.master_bank_account mba
                            JOIN public.master_bank mb ON mba.id_bank = mb.id_bank
                            WHERE mba.id_company = ? AND mba.status = 'A'", 
                            [$idCompany]);
    }
}