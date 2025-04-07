<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterInsurance extends Model {

    public static function getdata() {
        $sql = "SELECT  mi.id_insurance
                        ,mi.insurance_code
                        ,mi.description
                        ,mi.insurance_class
                        ,mi.bill_amount
                        ,mi.insurance_address
                        ,mi.id_country
                        ,mi.status
                        ,mi.inactive_date
                        ,mi.id_company
                        ,mi.creation_date
                        ,mi.update_date
                        ,mi.created_by
                        ,mi.updated_by
                    FROM master_insurance mi 
                    JOIN master_company mc ON mc.id_company = mi.id_company
                    WHERE mi.id_company = ? AND mi.status = 'A'";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
    
    public static function save_master_insurance($data){
        $id_user = session()->get('id_user');
        if ($data['id_master_insurance'] == "") {
            $data_insert = [
                'insurance_code' => $data['insurance_code'],
                'description' => $data['insurance_name'],
                'insurance_class' => $data['insurance_class'],
                'bill_amount' => $data['bill_amount'],
                'insurance_address' => $data['address'],
                'status' => $data['status'],
            ];
            try {
                DB::insert('INSERT INTO [master_insurance]
                                ([insurance_code]
                                ,[description]
                                ,[insurance_class]
                                ,[bill_amount]
                                ,[insurance_address]
                                ,[id_country]
                                ,[status]
                                ,[inactive_date]
                                ,[id_company]
                                ,[creation_date]
                                ,[update_date]
                                ,[created_by]
                                ,[updated_by])
                            VALUES
                                (? -- <insurance_code, nvarchar(50),>
                                ,? -- <description, nvarchar(100),>
                                ,? -- <insurance_class, nvarchar(25),>
                                ,? -- <bill_amount, float,>
                                ,? -- <insurance_address, nvarchar(100),>
                                ,NULL -- <id_country, int,>
                                ,? -- <status, nvarchar(255),>
                                ,NULL -- <inactive_date, date,>
                                ,? -- <id_company, int,>
                                ,GETDATE() -- <creation_date, datetime,>
                                ,NULL -- <update_date, datetime,>
                                ,? -- <created_by, int,>
                                ,NULL -- <updated_by, int,>
                            )',[
                                    $data_insert['insurance_code'],
                                    $data_insert['description'],
                                    $data_insert['insurance_class'],
                                    $data_insert['bill_amount'],
                                    $data_insert['insurance_address'],
                                    $data_insert['status'],
                                    session('id_company'),
                                    $id_user
                                ]);
                return [
                    'status' => 'true',
                    'message' => 'Master Insurance saved successfully !!'
                ];
            } catch (\Exception $e) {
                Log::error($e);
                return [
                    'status' => 'false',
                    'message' => 'Cannot save master insurance !! [' . $e->getMessage() . ']'
                ];
            }
        } else {
            $data_update = [
                'id_master_insurance' => $data['id_master_insurance'],
                'insurance_code' => $data['insurance_code'],
                'description' => $data['insurance_name'],
                'insurance_class' => $data['insurance_class'],
                'bill_amount' => $data['bill_amount'],
                'insurance_address' => $data['address'],
                'status' => $data['status'],
            ];
            try {
                DB::update('UPDATE [master_insurance]
                            SET [insurance_code] = ? -- <insurance_code, nvarchar(50),>
                               ,[description] = ? -- <description, nvarchar(100),>
                               ,[insurance_class] = ? -- <insurance_class, nvarchar(25),>
                               ,[bill_amount] = ? -- <bill_amount, float,>
                               ,[insurance_address] = ? -- <insurance_address, nvarchar(100),>
                               ,[status] = ? -- <status, nvarchar(255),>
                               ,[update_date] = GETDATE() -- <update_date, datetime,>
                               ,[updated_by] = ? -- <updated_by, int,>
                          WHERE id_insurance = ?',[
                                $data_update['insurance_code'],
                                $data_update['description'],
                                $data_update['insurance_class'],
                                $data_update['bill_amount'],
                                $data_update['insurance_address'],
                                $data_update['status'],
                                $id_user,
                                $data_update['id_master_insurance'],
                          ]);
                return [
                    'status' => 'true',
                    'message' => 'Master Insurance update successfully !!'
                ];
            } catch (\Exception $e) {
                Log::error($e);
                return [
                    'status' => 'false',
                    'message' => 'Cannot update master insurance !! [' . $e->getMessage() . ']'
                ];
            }
        }
        
    }
    
    public static function get_detail_master_insurance($data){
        $sql = "SELECT  mi.id_insurance
                        ,mi.insurance_code
                        ,mi.description insurance_name
                        ,mi.insurance_class
                        ,mi.bill_amount
                        ,mi.insurance_address address
                        ,mi.id_country
                        ,mi.status
                        ,mi.inactive_date
                        ,mi.id_company
                        ,mi.creation_date
                        ,mi.update_date
                        ,mi.created_by
                        ,mi.updated_by
                    FROM master_insurance mi 
                    JOIN master_company mc ON mc.id_company = mi.id_company
                    WHERE mi.status = 'A' and mi.id_insurance = ?";
        $result = DB::select($sql, [$data['id_master_insurance']])[0];
        return $result;
    }
    
    public static function destroy_master_insurance($data){
        try {
            DB::delete('DELETE FROM  master_insurance WHERE id_insurance = ?', [$data['id_master_insurance']]);
            return [
                'status' => 'true',
                'message' => 'Master insurance deleted successfully !!'
            ];
        } catch (\Exception $e) {
            Log::error($e);
            return [
                'status' => 'false',
                'message' => 'Cannot delete master insurance !! [' . $e->getMessage() . ']'
            ];
        }
    }

}
