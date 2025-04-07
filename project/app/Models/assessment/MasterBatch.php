<?php

namespace App\Models\assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class MasterBatch extends Model
{
    use HasFactory;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $table = 'public.psycho_master_batch';
    protected $primaryKey = 'id_batch';
    protected $fillable = [
        'batch_name','location','start_date','end_date','status','id_company','creation_date','update_date','created_by','updated_by'
    ];

    public static function getData($idBatch=null, $idCompany=null) {
        $get = DB::table('public.psycho_master_batch as pmb');
        if($idBatch){
            $idBatch = is_array($idBatch) ? $idBatch : [$idBatch];
            $get->whereIn('id_batch', $idBatch);
        }
        if($idCompany){
            $idCompany = is_array($idCompany) ? $idCompany : [$idCompany];
            $get->whereIn('id_company', $idCompany);
        }
        $return = $get->first();

        return $return;
    }

    public static function getAllData($idBatch=null, $idCompany=null) {
        $get = DB::table('public.psycho_master_batch as pmb');
        if($idBatch){
            $idBatch = is_array($idBatch) ? $idBatch : [$idBatch];
            $get->whereIn('id_batch', $idBatch);
        }
        if($idCompany){
            $idCompany = is_array($idCompany) ? $idCompany : [$idCompany];
            $get->whereIn('id_company', $idCompany);
        }
        $return = $get->get();

        return $return;
    }
}

