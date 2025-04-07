<?php

namespace App\Models\LearningManagement\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterKpi extends Model {

    use HasFactory;

    protected $table = 'master_kpi_category';
    protected $primaryKey = 'id_kpi_category';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_kpi_category', 'description', 'weight_kpi', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_data() {
        $data = DB::table('master_kpi_category as mkc')
                ->select('mkc.*')
                ->where('mkc.id_company', '=', session('id_company'))
                ->get();
        return $data;
    }

    public static function get_select_kpi($id_kpi_category=null) {
        $data = DB::table('master_kpi_category as mkc')
                ->select('mkc.id_kpi_category as id', 'mkc.description as text')
                ->where('mkc.id_company', '=', session('id_company'))
                ->orderBy('mkc.description');
        if($id_kpi_category){
            $data->where('mkc.id_kpi_category', $id_kpi_category);
        }
        return $data->get();
    }

}
