<?php

namespace App\Models\Organization\OrganizationStructure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobPosition extends Model {

    use HasFactory;

    protected $table = 'master_job_position';
    protected $primaryKey = 'id_position';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_position', 'id_dept', 'description', 'job_description', 'status', 'parent_id_position', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_data() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                    mjp.id_position,
                    mjp.description,
					md.description as department,
					mc.company_name as company_name,
					mjp2.description as superior_position,
                    mjp.status
                FROM  master_job_position mjp
				left join  master_department md
				on	mjp.id_dept =  md.id_dept
				left join  master_company mc
				on	mjp.id_company = mc.id_company
				left join  master_job_position mjp2
				on mjp.parent_id_position = mjp2.id_position
				WHERE mjp.id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_company() {
        $id_company = session()->get('id_company');
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company WHERE id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_department() {
        $id_company = session()->get('id_company');
        $sql = "SELECT
                        id_dept id,
                        description text
                FROM  master_department WHERE status = 'A' AND id_company = ?";
        $result = DB::select($sql, [$id_company]);

        return $result;
    }

    public static function get_superior_position() {
        $sql = "SELECT 
                        id_position id,
                        description text
                FROM  master_job_position WHERE status = 'A'";
        $result = DB::select($sql);

        return $result;
    }

    public static function save_position($data) {
        $id_user = session()->get('id_user');
        if ($data['id_position'] == "") {
            $data_insert = [
                'id_dept' => $data['department'],
                'description' => $data['job_position'],
                'job_description' => $data['job_description'],
                'status' => $data['status'],
                'parent_id_position' => $data['superior_position'],
                'id_company' => $data['company'],
            ];
            try {
                DB::insert("INSERT INTO  master_job_position (
                                id_dept,
                                description,
                                job_description,
                                status,
                                parent_id_position,
                                id_company,
                                creation_date,
                                update_date,
                                created_by,
                                updated_by) values (?,?,?,?,?,?,'".date('Y-m-d H:i:s')."',null,?,null)",
                        [
                            $data_insert['id_dept'],
                            $data_insert['description'],
                            $data_insert['job_description'],
                            $data_insert['status'],
                            $data_insert['parent_id_position'],
                            $data_insert['id_company'],
                            $id_user,
                ]);
                return [
                    'status' => 'true',
                    'message' => 'Job position saved successfully !!'
                ];
            } catch (\Exception $e) {
                Log::error($e);
                return [
                    'status' => 'false',
                    'message' => 'Cannot save job position !! [' . $e->getMessage() . ']'
                ];
            }
        } else {
            $data_update = [
                'id_position' => $data['id_position'],
                'id_dept' => $data['department'],
                'description' => $data['job_position'],
                'job_description' => $data['job_description'],
                'status' => $data['status'],
                'parent_id_position' => $data['superior_position'],
                'id_company' => $data['company'],
            ];
            try {
                DB::update("UPDATE  master_job_position SET 
                                id_dept = ? ,
                                description = ?,
                                job_description = ?,
                                status = ?,
                                parent_id_position = ?,
                                id_company = ?,
                                update_date = '".date('Y-m-d H:i:s')."',
                                updated_by = ? WHERE id_position = ?",
                        [
                            $data_update['id_dept'],
                            $data_update['description'],
                            $data_update['job_description'],
                            $data_update['status'],
                            $data_update['parent_id_position'],
                            $data_update['id_company'],
                            $id_user,
                            $data_update['id_position'],
                ]);
                return [
                    'status' => 'true',
                    'message' => 'Job position update successfully !!'
                ];
            } catch (\Exception $e) {
                Log::error($e);
                return [
                    'status' => 'false',
                    'message' => 'Cannot update job position !! [' . $e->getMessage() . ']'
                ];
            }
        }
    }

    public static function get_detail_position($data) {
        $sql = "SELECT 
                    mjp.id_position,
                    mjp.id_dept,
                    md.description dept_name,
                    mjp.description,
                    mjp.job_description,
                    mjp.status,
                    mjp.parent_id_position,
                    mjp2.description parent_name,
                    mjp.id_company,
                    mc.company_name
                FROM  master_job_position mjp
                LEFT JOIN  master_company mc ON mc.id_company = mjp.id_company
                LEFT JOIN  master_department md ON md.id_dept = mjp.id_dept
                LEFT JOIN  master_job_position mjp2 ON mjp2.id_position = mjp.parent_id_position
                WHERE mjp.id_position = ?";
        $result = DB::select($sql, [$data['id_position']])[0];

        return $result;
    }

    public static function destroy_position($data) {
        try {
            DB::delete('DELETE FROM  master_job_position WHERE id_position = ?', [$data['id_position']]);
            return [
                'status' => 'true',
                'message' => 'Job position deleted successfully !!'
            ];
        } catch (\Exception $e) {
            Log::error($e);
            return [
                'status' => 'false',
                'message' => 'Cannot delete job position !! [' . $e->getMessage() . ']'
            ];
        }
    }

}
