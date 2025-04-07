<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterBank extends Model {
    protected $table = 'public.master_bank';

    public static function getdata() {
        $sql = "SELECT mb.id_bank
                        ,mb.bank_code
                        ,mb.transfer_code
                        ,mb.description
                        ,mb.status
                        ,mb.inactive_date
                        ,mb.id_company
                        ,mb.creation_date
                        ,mb.update_date
                        ,mb.created_by
                        ,mb.updated_by
                    FROM master_bank mb
                    WHERE mb.id_company = ? AND mb.status = 'A'";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }

    public static function save_master_bank($data) {
        $id_user = session()->get('id_user');
        if ($data['id_bank'] == "") {
            $data_insert = [
                'bank_code' => $data['bank_code'],
                'description' => $data['bank_name'],
                'transfer_code' => $data['transfer_code'],
                'status' => $data['status']
            ];
            try {
                DB::insert('INSERT INTO master_bank
                                (bank_code
                                ,transfer_code
                                ,description
                                ,status
                                ,inactive_date
                                ,id_company
                                ,creation_date
                                ,update_date
                                ,created_by
                                ,updated_by)
                            VALUES
                                (
                                    ? -- <bank_code, nvarchar(10),>
                                    ,? -- <transfer_code, nvarchar(5),>
                                    ,? -- <description, nvarchar(50),>
                                    ,? -- <status, nvarchar(255),>
                                    ,NULL -- <inactive_date, date,>
                                    ,? -- <id_company, int,>
                                    ,NOW() -- <creation_date, datetime,>
                                    ,NULL -- <update_date, datetime,>
                                    ,? -- <created_by, int,>
                                    ,NULL -- <updated_by, int,>
                            )', [
                    $data_insert['bank_code'],
                    $data_insert['transfer_code'],
                    $data_insert['description'],
                    $data_insert['status'],
                    session('id_company'),
                    $id_user
                ]);
                return [
                    'status' => 'true',
                    'message' => 'Master Bank saved successfully !!'
                ];
            } catch (\Exception $e) {
                Log::error($e);
                return [
                    'status' => 'false',
                    'message' => 'Cannot save master bank !! [' . $e->getMessage() . ']'
                ];
            }
        } else {
            $data_update = [
                'id_bank' => $data['id_bank'],
                'bank_code' => $data['bank_code'],
                'description' => $data['bank_name'],
                'transfer_code' => $data['transfer_code'],
                'status' => $data['status']
            ];
            try {
                DB::update('
                    UPDATE master_bank
                        SET bank_code = ? -- <bank_code, nvarchar(10),>
                           ,transfer_code = ? -- <transfer_code, nvarchar(5),>
                           ,description = ? -- <description, nvarchar(50),>
                           ,status = ? -- <status, nvarchar(255),>
                           ,update_date = NOW() -- <update_date, datetime,>
                           ,updated_by = ? -- <updated_by, int,>
                      WHERE id_bank = ?', [
                    $data_update['bank_code'],
                    $data_update['transfer_code'],
                    $data_update['description'],
                    $data_update['status'],
                    $id_user,
                    $data_update['id_bank'],
                ]);
                return [
                    'status' => 'true',
                    'message' => 'Master Bank update successfully !!'
                ];
            } catch (\Exception $e) {
                Log::error($e);
                return [
                    'status' => 'false',
                    'message' => 'Cannot update master bank !! [' . $e->getMessage() . ']'
                ];
            }
        }
    }

    public static function get_detail_master_bank($data) {
        $sql = "SELECT mb.id_bank
                        ,mb.bank_code
                        ,mb.transfer_code
                        ,mb.description bank_name
                        ,mb.status
                        ,mb.inactive_date
                        ,mb.id_company
                        ,mb.creation_date
                        ,mb.update_date
                        ,mb.created_by
                        ,mb.updated_by
                    FROM master_bank mb
                    WHERE mb.status = 'A' and mb.id_bank = ?";
        $result = DB::select($sql, [$data['id_bank']])[0];
        return $result;
    }

    public static function destroy_master_bank($data) {
        try {
            DB::delete('DELETE FROM  master_bank WHERE id_bank = ?', [$data['id_bank']]);
            return [
                'status' => 'true',
                'message' => 'Master bank deleted successfully !!'
            ];
        } catch (\Exception $e) {
            Log::error($e);
            return [
                'status' => 'false',
                'message' => 'Cannot delete master bank !! [' . $e->getMessage() . ']'
            ];
        }
    }

}
