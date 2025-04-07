<?php

namespace App\Models\LearningManagement\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrKpiGroup extends Model {

    use HasFactory;

    protected $table = 'hr_kpi_group';
    protected $primaryKey = 'id_kpi_group';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_kpi_group', 'id_employee', 'average_prosentase', 'description', 'notes', 'start_date', 'end_date', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_data() {
        $data = DB::table('hr_kpi_group as hkg')
                ->join('hr_employee as he', 'he.id_employee', '=', 'hkg.id_employee', 'left')
                ->select('hkg.*', 'he.name')
                ->where('hkg.id_company', '=', session('id_company'))
                ->where('hkg.kpi_class', '!=', 'PA')
                ->orderByDesc('hkg.start_date')
                ->orderBy('he.name')
                ->get();

        return $data;
    }

    public static function get_data_kpi($param) {
        $result = (object)[];
        $listIdKpiHeader = [];
        $data = DB::table('hr_kpi_group as hkg')
                ->where('hkg.id_company', '=', $param['id_company'])
                ->where('hkg.id_kpi_group', '=', $param['id_kpi_group'])
                ->first();

        $header = DB::table('hr_kpi_header as hkh')
                ->where('hkh.id_company', '=', $param['id_company'])
                ->where('hkh.id_kpi_group', '=', $param['id_kpi_group'])
                ->get();
 
        $listIdKpiHeader = $header->pluck('id_kpi_header')->all();
        $detail = DB::table('hr_kpi_detail as hkd')
                ->where('hkd.id_company', '=', $param['id_company'])
                ->whereIn('hkd.id_kpi_header', $listIdKpiHeader)
                ->get();

        $result             = $data;
        $result->header     = $header;
        $result->detail     = $detail;

        return $result;
    }
}
