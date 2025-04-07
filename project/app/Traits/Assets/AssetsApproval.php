<?php
namespace App\Traits\Assets;

use App\Http\Controllers\Assets\FinancialAsset\AssetAdjustmentController;
use App\Models\Assets\Approval\AssetApprovalTransaction;
use App\Models\Assets\FinancialAsset\AdjustmentHeader;
use App\Models\Assets\FinancialAsset\ImpairmentHeader;
use App\Models\Assets\FinancialAsset\RevaluationHeader;
use App\Models\Assets\HrEmployee;
use App\Models\Assets\RetirementDisposal\ReinstateHeader;
use App\Models\Assets\RetirementDisposal\RetirementHeader;
use App\Models\Assets\TransferSettings\AssetTransferHeader;
use App\Models\Assets\TransferSettings\MasterApprovalAsset;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * AssetsApproval Trait
 * provides a single-gate, convenient, and modular method for assets approval/rejection.
 * @author Faisal
 */
trait AssetsApproval {

    /** @var array<string> */
    private $acceptedTypes = ['Approved', 'Revised', 'Rejected', 'Request_Approval'];

    /** @var array<array> */
    private $sourceTypeAndTableNameMap = [
        'Asset_Transfer' => [
            'table' => "asset.fa_transfer_header",
            'alias' => "fth",
            'primary' => 'id_transfer_header',
        ],
        'Adjustment' => [
            'table' => "asset.fa_adjustment_header",
            'alias' => "fah",
            'primary' => "id_adjustment_header"
        ],
        'Revaluation' => [
            'table' => "asset.fa_revaluation_header",
            'alias' => "frh",
            'primary' => "id_revaluation_header"
        ],
        'Impairment' => [
            'table' => "asset.fa_impairment_header",
            'alias' => "fih",
            'primary' => "id_impairment_header"
        ],
        'Reinstate' => [
            'table' => "asset.fa_reinstate_header",
            'alias' => "frh",
            'primary' => "id_reinstate_header",
        ],
        'Retirement' => [
            'table' => "asset.fa_retirement_header",
            'alias' => "frh",
            'primary' => "id_retirement_header",
        ],
    ];
    /**
     * Maps model object instance to its transaction type
     * @var array
     */
    private $instances = [
        AssetTransferHeader::class => 'Asset_Transfer',
        AdjustmentHeader::class => 'Asset_Adjustment',
        RevaluationHeader::class => 'Asset_Revaluation',
        ImpairmentHeader::class => 'Asset_Impairment',
        ReinstateHeader::class => 'Asset_Reinstate',
        RetirementHeader::class => 'Asset_Retirement'
    ];

    /**
     * Returns array containing list of transaction to be approved by the user
     * @param string $sourceTransactionType
     * @param int $idEmployee
     * @param string $status
     * @return array
     */
    public function getApprovalData($sourceTransactionType, int $idEmployee, $status) {
        if($status !== null) {
            $status = "'$status'";
        } else {
            $status = "null";
        }
        if($sourceTransactionType !== null) {
            $sourceTransactionType = "'$sourceTransactionType'";
        } else {
            $sourceTransactionType = "null";
        }
        return DB::select("select * from asset.sp_funct_approval_view ($idEmployee,".session('id_company').",null,$sourceTransactionType,null,null,null,null,$status)");
    }

    /**
     * Deprecated version of `getApprovalData()` using transaction types mapped to its respective 
     * tables and primary keys to obtain the data
     * @param string $sourceTransactionType
     * @param int $idEmployee
     * @param string|null $tableName used if source table is unmapped
     * @param string|null $tableAlias used if source table is unmapped
     * @param array<string> $status
     */
    public function getApprovalDataLegacy($sourceTransactionType, $idEmployee, $tableName = null, $tableAlias = null, $status = ['Request_Approval']) {
        if(!$tableAlias) {
            $tableAlias = "custapprovaltable";
        }
        if(!$tableName) {
            $tableName = $this->sourceTypeAndTableNameMap[$sourceTransactionType]['table'];
            $tableAlias = $this->sourceTypeAndTableNameMap[$sourceTransactionType]['alias'];
            $tablePrimary = $this->sourceTypeAndTableNameMap[$sourceTransactionType]['primary'];
        }
        return DB::select("SELECT fat.*, $tableAlias.*, mgd.description AS approval_status_description 
                            FROM asset.fa_approval_transaction fat 
                            JOIN master_general_data mgd ON fat.id_approval_status = mgd.id_general_data 
                            JOIN $tableName $tableAlias ON fat.id_source_transaction = $tableAlias.$tablePrimary
                            WHERE fat.source_transaction_type = ?
                            AND fat.id_employee_approval = ?
                            AND mgd.code IN(?)
                            ", 
                            [$sourceTransactionType, $idEmployee, implode(",", $status)]);
    }

    /**
     * Returns a new instance of model object according to its respective transaction type.
     * Returns null if transaction type is not found.
     * @param string $sourceTransactionType
     * @return AssetTransferHeader|AdjustmentHeader|RevaluationHeader|ImpairmentHeader|ReinstateHeader|RetirementHeader|null
     */
    public function getHeaderInstance($sourceTransactionType) {
        foreach($this->instances as $instance => $code) {
            if($code === $sourceTransactionType) {
                return new $instance();
            }
        }
        return null;
    }

    /**
     * **Get Approval List**
     * 
     * Returns approval name according to transaction type inferred from `$headerInstance` object.
     * @param MasterApprovalHeader $headerInstance   an instance of MasterApprovalHeader model
     * @return array
     */
    public function getApprovalList($headerInstance, $idEmployee, $idCompany, $idAssetCategory = 'null') {
        if(!$headerInstance) {
            return [];
        }
        $approvals = null;
        if($headerInstance->hierarchy_type == 'Organization') {
            $employeePosition = DB::selectOne("SELECT mpd.id_position_detail, mjg.job_class_group FROM master_position_detail mpd 
                                            JOIN master_position_routing mpr ON mpd.id_position_routing = mpr.id_routing 
                                            JOIN master_job_grade mjg ON mpr.id_job_grade = mjg.id_job_grade 
                                            WHERE ((mpd.id_employee = ? OR mpd.id_employee2 = ?) AND mpd.secondary_position = FALSE)
                                            AND mpd.status = 'A'",
                                            [$idEmployee, $idEmployee]);
            if(!$employeePosition) {
                throw new Exception("Cannot find employee position/job grade.");
            }
            // dd("SELECT * FROM asset.sp_funct_approval_organization_hierarchy_view($idEmployee, $idCompany, $employeePosition->id_position_detail, null");
            $approvals = DB::select("SELECT 
                                        sfav.*, he.name 
                                    FROM asset.sp_funct_approval_organization_hierarchy_view(?, ?, ?, null) sfav
                                    JOIN hr_employee he ON sfav.id_employee_approval = he.id_employee",
                    [$idEmployee, $idCompany, $employeePosition->id_position_detail]);
        } else if($headerInstance->hierarchy_type == 'Combine') {
            $approvals = DB::select("SELECT 
                                        sfav.*, he.name 
                                    FROM asset.sp_funct_approval_combine_hierarchy_view(?, ?, ?, null) sfav
                                    JOIN hr_employee he ON sfav.id_employee = he.id_employee",
                    [$idEmployee, $idCompany, $headerInstance->id_approval]);
        } else if($headerInstance->hierarchy_type == 'Custom') {
            $approvals = DB::select("SELECT sfav.*, he.name 
                                    FROM asset.sp_funct_approval_custom_hierarchy_view(?, ?, ?, null) sfav
                                    JOIN hr_employee he ON sfav.id_employee = he.id_employee",
                    [$idEmployee, $idCompany, $headerInstance->id_approval]);
        } else {
            throw new Exception("Hierarchy type $headerInstance->hierarchy_type is invalid.");
        }
        if(count($approvals) < 1) {
            throw new Exception("Cannot find employee approval with hierarchy type $headerInstance->hierarchy_type");
        }
        return $approvals;
    }

    /**
     * **Transfer Approval**
     * @param AssetTransferHeader|ImpairmentHeader|RevaluationHeader|AdjustmentHeader $headerInstance   an instance of AssetTransferHeader model
     * @param int|string $idEmployeeApprover The approver's id employee
     * @param string $type Approved|Rejected|Revised
     */
    public function assetApproval(
        $headerInstance,
        $idEmployeeApprover,
        $type = 'Approved'
    ) {
        $mgdCode = "";
        foreach($this->instances as $instance => $code) {
            if($headerInstance instanceof $instance) {
                $mgdCode = $code;
            }
        }
        if(!in_array($type, $this->acceptedTypes)) {
            throw new Exception("Invalid argument for parameter type: $type. Argument must be in these options: ".implode(", ", $this->acceptedTypes));
        }
        $status = MasterGeneralData::where('code', $type)->where('id_company', session('id_company'))->first();
        if(!$status) {
            throw new Exception("ID General Data for code $type not found!");
        }
        $approvals = AssetApprovalTransaction::transactionType($mgdCode)->idSourceTransaction($headerInstance[$headerInstance->getKeyName()])->get();
        if($approvals->where('id_employee_approval', $idEmployeeApprover)->count() < 1) {
            throw new Exception("You are not allowed to modify this transaction!", 403);
        }
        foreach($approvals as $approval) {
            if($approval->id_employee_approval == $idEmployeeApprover) {
                $approval->id_approval_status = $status->id_general_data;
                $approval->save();

                if($status->code == 'Approved') {
                    $approvalMode = MasterGeneralData::findOrFail($approval->id_approval_mode);
                    if($approvalMode->code == 'OR') {
                        AssetApprovalTransaction::transactionType($mgdCode)
                                            ->idSourceTransaction($headerInstance[$headerInstance->getKeyName()])
                                            ->update([
                                                'id_approval_status' => $status->id_general_data,
                                            ]);
                        $headerInstance->id_document_status = $status->id_general_data;
                        $headerInstance->save();
                    }
                }
            }
        }

        if($status->code == 'Approved') {
            if($headerInstance->id_document_status != $status->id_general_data) {
                if(count($approvals->where('id_approval_status', $status->id_general_data)) == count($approvals)) {
                    $headerInstance->id_document_status = $status->id_general_data;
                } else {
                    $partialApproved = MasterGeneralData::where('code', 'Partial_Approved')->where('id_company', session('id_company'))->first();
                    $headerInstance->id_document_status = $partialApproved->id_general_data;
                }
            }
        } else if($status->code == 'Rejected') {
            $headerInstance->id_document_status = $status->id_general_data;
        }

        $headerInstance->save();
        return $headerInstance;
    }

    public function getApprovalTransaction($headerInstance, $idSource = null) {
        if(!$idSource) {
            $idSource = $headerInstance[$headerInstance->getKeyName()];
        }
        $mgdCode = "";
        foreach($this->instances as $instance => $code) {
            if($headerInstance instanceof $instance) {
                $mgdCode = $code;
            }
        }
        $key = $mgdCode;
        if($mgdCode != 'Asset_Transfer') {
            $key = str_replace("Asset_", "", $mgdCode);
        }
        $tableName = $this->sourceTypeAndTableNameMap[$key]['table'];
        $tableAlias = $this->sourceTypeAndTableNameMap[$key]['alias'];
        $tablePrimary = $this->sourceTypeAndTableNameMap[$key]['primary'];
        return DB::select("SELECT 
						fat.id_approval_transaction, 
						fat.sequence,
						mgd.description AS approval_mode, 
						mgd2.description AS approval_status, 
                        mgd2.code AS approval_code,
						he.name AS approval_name,
                        CASE
                            WHEN mgd2.code IN ('New', 'Request_Approval') THEN NULL
                            -- WHEN mgd2.code IN ('Rejected') THEN FALSE
                            ELSE TRUE
                        END AS approval_execute
					FROM asset.fa_approval_transaction fat 
					JOIN $tableName $tableAlias ON $tableAlias.$tablePrimary = fat.id_source_transaction 
					JOIN master_general_data mgd ON fat.id_approval_mode = mgd.id_general_data
					JOIN master_general_data mgd2 ON fat.id_approval_status = mgd2.id_general_data
					JOIN hr_employee he ON fat.id_employee_approval = he.id_employee
					WHERE fat.id_source_transaction = ?
					AND fat.source_transaction_type = ?", [$idSource, $mgdCode]);
    }

    public function generateJournal(
        $headerInstance,
        $idCompany,
        string $date = null
    ) {
        $id = $headerInstance[$headerInstance->getKeyName()];
        $financialType = '';
        foreach($this->instances as $instance => $code) {
            if($headerInstance instanceof $instance) {
                $financialType = $code === "Asset_Transfer" ? $code : str_replace('Asset_', '', $code);
            }
        }
        if(!$date) {
            $date = now()->format('Y-m-d');
        }
        return DB::select("SELECT * FROM asset.sp_funct_generate_journal_asset($id, $idCompany, '$date', '$financialType')");
    }

    /**
     * Inserts entry(-ies) on fa_approval_transaction with data according to the header instance.
     * @param Model $headerInstance The transaction header model instance
     * @param int $userId the actor user ID
     * @return array
     */
    public function generateApprovalTransaction(Model $headerInstance, int $userId): array {

        $sourceType = null;
        foreach($this->instances as $instance => $code) {
            if($headerInstance instanceof $instance) {
                $sourceType = $code;
            }
        }

        $employee = HrEmployee::where('id_user', $userId)->active()->first();
        $header = MasterApprovalAsset::currentCompany($headerInstance->id_company)->active()->findByCode($sourceType)->first();
        if(!$header) {
            throw new Exception("Approval code $sourceType not found.");
        }
        $approvalDetails = $this->getApprovalList($header, $employee->id_employee, $headerInstance->id_company, $header->id_asset_category);

        $result = [];
        foreach($approvalDetails as $detail) {
            $result[] = AssetApprovalTransaction::create([
                'id_source_transaction' => $headerInstance->getKey(),
                'source_transaction_type' => $sourceType,
                'id_approval' => $header->id_approval,
                'id_approval_detail' => $detail->id_approval_detail ?? null,
                'id_approval_mode' => $detail->id_approval_mode,
                'sequence' => $detail->sequence,
                'id_position_detail' => $detail->id_position_detail ?? $detail->id_detail_chief,
                'id_employee_approval' => $detail->id_employee ?? $detail->id_employee_approval,
                'id_approval_status' => $headerInstance->id_document_status,
                'id_company' => $headerInstance->id_company,
                'created_by' => $userId,
            ]);
        }
        return $result;
    }
}