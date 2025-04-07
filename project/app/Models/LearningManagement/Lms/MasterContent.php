<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterContent extends Model {

    use HasFactory;

    protected $table = 'master_content_learning';
    protected $primaryKey = 'id_content_learning';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_content_learning', 'content_attachment_type', 'description', 'link', 'content_name', 'notes', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_content() {
        $sql = "SELECT
					mcl.id_content_learning, 
					mcl.content_attachment_type, 
					mcl.link, 
                    mcl.content_name, 
					mcl.status, 
					mc.company_name AS company
					FROM master_content_learning mcl
					LEFT JOIN master_company mc
					ON mcl.id_company = mc.id_company
                    WHERE mcl.id_company = ? 
                    ORDER BY id_content_learning desc";
        $data = DB::select($sql, [session('id_company')]);
        return $data;
    }

    public static function get_content_type() {
        $data = DB::table('master_general_data as mgd')
                ->join('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
                ->select('mgd.code as id', 'mgd.description as text')
                ->where('mgt.general_type', '=', 'master_content_type')
                ->where('mgd.id_company', '=', session('id_company'))
                ->where('mgd.status', '=', 'A')
                ->get();
        return $data;
    }

    public static function get_content_learning($data) {
        $result = [];
        $sql = "SELECT *           
                FROM master_content_learning
                WHERE id_company = ? AND id_content_learning = ? ";
        $result = (array)DB::select($sql, [$data['id_company'], $data['id_content_learning']])[0];

        $q  = "SELECT *           
                FROM master_attachment_content
                WHERE id_company = ? AND id_content_learning = ? ";
        $result_attachment = DB::select($q, [$data['id_company'], $data['id_content_learning']]);
        $result['attachment'] = $result_attachment;
        return $result;
    }

}
