<?php

namespace App\Models\Assets\TransferSettings;

use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardModelScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetTransferHeader extends Model {
    use HasFactory, StandardModelScope;

    protected $table = 'asset.fa_transfer_header';
    protected $primaryKey = 'id_transfer_header';
    const UPDATED_AT = 'update_date';
    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'reference_number',
        'request_date',
        'need_date',
        'description',
        'id_approval',
        'note_revised',
        'note_rejected',
        'id_document_status',
        'status',
        'id_company',
        'created_by',
        'updated_by'
    ];

    public function approval() {
        return $this->hasOne(MasterApprovalAsset::class, 'id_approval', 'id_approval');
    }

    public function scopeApprovalStatus($query, $status = 'Approved') {
        $query->join('master_general_data as xmgd', "$this->table.id_document_status", "xmgd.id_general_data");
        $query->where('xmgd.code', $status);
    }

    public function scopeTransferReceivedBy($query, $nikEmployee, $receivedFlag = NULL) {
        $query->join('asset.fa_transfer_detail as xftd', "$this->table.id_transfer_header", "xftd.id_transfer_header");
        $query->join('asset.fa_asset as xfa', "xftd.id_asset", "xfa.id_asset");
        $query->join('public.hr_employee as xhe', 'xftd.id_employee_destination', 'xhe.id_employee');
        $query->join('public.hr_employee as xhe2', 'xhe.nik_employee', 'xhe2.nik_employee');
        $query->leftJoin('public.hr_employee as xhe3', "$this->table.created_by", 'xhe3.id_user');
        $query->leftJoin('asset.fa_approval_transaction as xfat', "$this->table.id_transfer_header", "xfat.id_source_transaction");
        $query->leftJoin('public.hr_employee as xhe4', 'xfat.id_employee_approval', 'xhe4.id_employee');
        $query->where('xhe2.nik_employee', $nikEmployee);
        $query->where('xhe2.status', 'A');
        $query->where('xhe3.status', 'A');
        $query->where('xfat.source_transaction_type', 'Asset_Transfer');
        $query->where('xftd.status', 'A');
        if($receivedFlag === NULL) {
            $query->whereNull('xftd.received_flag');
        } else if($receivedFlag === TRUE || $receivedFlag === FALSE) {
            $query->where('xftd.received_flag', $receivedFlag);
        }
    }

    public function documentStatus() {
        return $this->belongsTo(MasterGeneralData::class, 'id_document_status', 'id_general_data');
    }

    public function details() {
        return $this->hasMany(AssetTransferDetail::class, 'id_transfer_header', 'id_transfer_header');
    }

    public function scopeJoinCompany($query) {
        $query->join('public.master_company as xmc', "$this->table.id_company", "xmc.id_company");
        // $query->select("$this->table.*", "xmc.description as company_name");
    }
}