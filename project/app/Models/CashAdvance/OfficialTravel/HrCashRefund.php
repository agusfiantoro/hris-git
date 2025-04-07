<?php

namespace App\Models\CashAdvance\OfficialTravel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Organization\MasterOrganization\MasterRegional;
use Carbon\Carbon;

class HrCashRefund extends Model
{
    protected $table = "hr_cash_refund";
	protected $primaryKey="id_cash_refund";

    const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

    protected $fillable = ['id_cash_advance', 'id_settlement_expense', 'id_bank_from', 'id_employee_bank_from', 'id_account', 'bank_from_account', 'description', 'notes', 'id_bank_to', 'bank_to_account', 'id_bank_to_account', 'id_currency_cash_refund', 'currency_rate_refund', 'total_amount', 'total_base_currency_amount', 'attachment', 'is_verified', 'is_paid', 'payment_status', 'status', 'id_company', 'created_by', 'updated_by'];

    public static function getCashRefund($idCashAdvance) {
        $sql = "SELECT *
                FROM hr_cash_refund
                WHERE id_cash_advance = ?
                ";
        return DB::select($sql, [$idCashAdvance]);
    }

    public static function getEmployeeBank() {
        $sql = "SELECT 
                    id_bank_employee as id,
                    concat(hbe.bank_name, ' - ', hbe.bank_account) as text,
                    id_bank,
                    account_name,
                    bank_currency,
                    hbe.default_bank,
                    hbe.bank_account
                FROM hr_employee he
                JOIN hr_bank_employee hbe ON he.id_employee = hbe.id_employee AND hbe.id_company = ?
                WHERE he.id_user = ? AND hbe.status = 'A'
                ";
        return DB::select($sql, [session('id_company'), session('id_user')]);
    }

    public static function getCompanyBank($idBranch = null) {
        $where = "";
        if($idBranch) {
            $where .= " AND mba.id_branch = $idBranch";
        }
        $sql = "SELECT
                    mba.id_bank_account as id,
                    concat(mb.bank_code, ' - ', mba.account_name, ' - ', mba.account_number, ' (', mba.branch_name, ')') as text
                FROM accounting.master_bank_account mba
                JOIN public.master_bank mb ON mba.id_bank = mb.id_bank
                WHERE mba.id_company = ? 
                AND mba.status = 'A'  
                AND mba.bank_type = 'Company' 
                AND mba.is_default = TRUE
                $where";
        return DB::select($sql, [session('id_company')]);
    }

    public static function getBanks() {
        $sql = "SELECT
                    id_bank as id,
                    description as text,
                    bank_code,
                    transfer_code
                FROM master_bank
                WHERE status = 'A' AND id_company = ?
                ";
        return DB::select($sql, [session('id_company')]);
    }
}