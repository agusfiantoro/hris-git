<?php
namespace App\Traits\Assets;

use App\Models\Assets\AdditionAsset\MasterPeriod;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\ConfigSettings\GlJeHeaders;
use App\Models\Assets\TransferSettings\AssetTransferHeader;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * FinancialAsset Trait
 * provides essential methods for Financial Asset.
 * @author Faisal
 */
trait FinancialAsset {

    /**
     * @param string $type
     * @param int $idPeriod
     */
    public function findGlJeHeader($type, $idPeriod) {
        $module = "FAM";
        $glJeHeader = GlJeHeaders::getGlJeHeader($module, $type, $idPeriod);
        if(!$glJeHeader) {
            $period = MasterPeriod::findOrFail($idPeriod);
            throw new Exception("Can't find journal header ($module $type $period->description)");
        }
        return $glJeHeader;
    }
}