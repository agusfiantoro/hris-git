<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterGeneralData extends Model {

    protected $table="master_general_data";
    protected $primaryKey="id_general_data";

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    public static function getdata() {
        $sql = "SELECT id_general_type
        ,general_type
        ,description
        ,status
                        --,[id_company]
                        ,creation_date
                        ,update_date
                        ,created_by
                        ,updated_by
                        FROM master_general_type mgt
                        WHERE  mgt.status = 'A'";
                        $result = DB::select($sql);
                        return $result;
                    }

                    public static function save_master_general_data($data) {
                        $id_user = session()->get('id_user');
                        $id_company = session()->get('id_company');
                        $data_update = [
                            'id_general_type' => $data['id_general_type'],
                            'general_data_detail' => $data['general_data_detail'],
                        ];
                        $collect_general_data_detail = collect($data_update['general_data_detail'])->groupBy('id_general_data')->toArray();
                        $list_id_general_data = array_filter(array_keys($collect_general_data_detail));

                        try {
                            DB::beginTransaction();
                            $date_now = DB::select('SELECT current_date date_now')[0]->date_now;

                            $id_general_type = $data_update['id_general_type'];
                            DB::delete("DELETE mgd FROM master_general_data mgd
                                WHERE mgd.id_general_type = ? AND mgd.id_general_data NOT IN (" . implode(",", $list_id_general_data) . ")", [$id_general_type]);

                            foreach (array_keys($data_update['general_data_detail']) as $key => $value) {
                                if ($data_update['general_data_detail'][$value]['id_general_data'] == "" || $data_update['general_data_detail'][$value]['id_general_data'] == null) {
                                    DB::insert('
                                        INSERT INTO master_general_data
                                        (id_general_type
                                        ,sequence
                                        ,code
                                        ,description
                                        ,status
                                        ,id_company
                                        ,creation_date
                                        ,update_date
                                        ,created_by
                                        ,updated_by)
                                        VALUES
                                (? --<id_general_type, int,>
                                ,? --<sequence, int,>
                                ,? --<code, nvarchar(50),>
                                ,? --<description, nvarchar(100),>
                                ,? --<status, nvarchar(255),>
                                ,? --<id_company, int,>
                                ,? --<creation_date, datetime,>
                                ,? --<update_date, datetime,>
                                ,? --<created_by, int,>
                                ,? --<updated_by, int,>
                                )
                                ',
                                [
                                $id_general_type //<id_general_type, int,>
                                ,$data_update['general_data_detail'][$value]['sequence'] //<sequence, int,>
                                ,$data_update['general_data_detail'][$value]['code'] //<code, nvarchar(50),>
                                ,$data_update['general_data_detail'][$value]['description'] //<description, nvarchar(100),>
                                ,isset($data_update['general_data_detail'][$value]['status']) ? 'I' : 'A' //<status, nvarchar(255),>
                                ,$id_company //<id_company, int,>
                                ,$date_now //<creation_date, datetime,>
                                ,$date_now //<update_date, datetime,>
                                ,$id_user //<created_by, int,>
                                ,$id_user //<updated_by, int,>
                            ]
                        );
                                }
                            }

                            DB::commit();
                            return [
                                'status' => 'true',
                                'message' => 'Master general data updated successfully !!'
                            ];
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return [
                                'status' => 'false',
                                'message' => 'Cannot update master general data !! [' . $e->getMessage() . ']'
                            ];
                        }
                    }

                    public static function get_detail_master_general_data($data) {
                        $id_user = session()->get('id_user');
                        $id_company = session()->get('id_company');
                        $sql_master = "SELECT mgt.*, 
                                        CASE when mgt.relation_feature = 'I' THEN 'Independent'
                                        WHEN mgt.relation_feature = 'D' THEN concat('Dependent on ', mgt2.general_type)
                                        END relation_description
                        FROM master_general_type mgt
                        JOIN master_general_type mgt2 ON coalesce(mgt.relation_to_id_general_type, 1) = coalesce(mgt2.id_general_type, 1)
                        WHERE mgt.id_general_type = ?";

                        $sql_detail = "SELECT id_general_data
                        ,id_general_type
                        ,sequence
                        ,code
                        ,description
                        ,status
                        ,id_company
                        ,creation_date
                        ,update_date
                        ,created_by
                        ,updated_by
                        ,restrict_by
                        ,relation_to_id_general_data
                        FROM master_general_data mgd 
                        WHERE mgd.id_general_type = ?
                        AND mgd.id_company = ?";
                        $result['master'] = DB::select($sql_master, [$data['id_general_type']])[0];
                        $result['detail'] = collect(DB::select($sql_detail, [$data['id_general_type'],$id_company]))->sortBy('sequence')->values()->all();
                        return $result;
                    }

                    public static function get_parent_options($parent_id) {
                        $sql = "SELECT 
                                    -- mgt.id_general_type, 
                                    -- mgt.general_type,
                                    mgd.id_general_data as id,
                                    CONCAT(mgd.description, ' (', mgd.code, ')') as text
                                FROM master_general_type mgt
                                JOIN master_general_data mgd ON mgt.id_general_type = mgd.id_general_type
                                WHERE mgd.status = 'A' and mgt.id_general_type  = ? and mgd.id_company = ?
                                ";
                            // return $sql;
                        return DB::select($sql, [$parent_id, session('id_company')]);
                    }

                }
