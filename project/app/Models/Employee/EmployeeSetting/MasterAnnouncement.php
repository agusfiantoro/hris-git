<?php

namespace App\Models\Employee\EmployeeSetting;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MasterAnnouncement extends Model {
	
	protected $table = 'hr_employee_anouncement';
    protected $primaryKey = 'id_announcement';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_announcement', 'reference_number', 'description', 'id_employee_request', 'start_date', 'end_date', 'id_anouncement_type', 'attachment_type', 'attachment','content_letter' ,'published', 'source_transaction_type', 'id_source_transaction', 'enable_approval' ,'id_approval', 'id_approval_status', 'status', 'id_company', 'created_by', 'updated_by'
    ];

	public static function getkode_announ(){
			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', session('id_company'))->first();
			
		$nomor = $com->company_code.'-ANNC-'.$monthyear.'-';
			
		$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%{$monthyear}%")->max('reference_number'), 17, 22);
		$no = 1;
		if($noUrutAkhir) {
			$kode =  sprintf("%06s",abs($noUrutAkhir + 1));
			$nomorbaru = $nomor.$kode;
			}
		else {
			$kode =  sprintf("%06s",$no);
			$nomorbaru = $nomor.$kode;
		}
		return $nomorbaru;		
	}
	
    public static function getdata() {
		$sql = "select hea.*, he.name as employee_name, mgd.description as announcement_type, 
					   mgd2.code as code_app_status, mgd2.description as desc_app_status 
				from hr_employee_anouncement as hea 
				join hr_employee as he 
				  on hea.id_employee_request = he.id_employee
				join master_general_data as mgd 
				  on hea.id_anouncement_type = mgd.id_general_data 
				left join master_general_data as mgd2 
				  on hea.id_approval_status = mgd2.id_general_data 
				where hea.id_company = ".session('id_company')." and he.id_user = ".session('id_user')."
				order by id_announcement desc";
		$result = DB::select($sql);
        return $result;
    }
	public static function getdata_publish() {
		$sql = "select hea.*, he.name as employee_name, mgd.description as announcement_type, 
					   mgd2.code as code_app_status, mgd2.description as desc_app_status 
				from hr_employee_anouncement as hea 
				join hr_employee as he 
				  on hea.id_employee_request = he.id_employee 
				join master_general_data as mgd 
				  on hea.id_anouncement_type = mgd.id_general_data 
				left join master_general_data as mgd2 
				  on hea.id_approval_status = mgd2.id_general_data 
				where hea.id_company = ".session('id_company')." 
				  and hea.status = 'A' and hea.published = '1' 
				order by id_announcement desc";
		$result = DB::select($sql);
        return $result;
    }
	
	public static function get_employee($idCompany=null, $idUser=null) {
		$idCompany = $idCompany ?? session('id_company');
		$idUser = $idUser ?? session('id_user');
		
        $sql = "SELECT 
                        id_employee id,
                        name text
                FROM  hr_employee where status = 'A' and id_company =" . $idCompany ." and id_user =" . $idUser;
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM  master_company where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	public static function get_announcement_type() {
        $sql = "SELECT 
                        id_general_data id,
                        description text
                FROM  master_general_data where status = 'A' and id_general_type = 13 and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	public static function get_hierachy($data) {
		$code = $data['code'];
        $sql = "SELECT  hah.id_approval id, hah.description text,
						hah.id_approval_doc_type
                FROM  hr_approval_header hah
				join master_general_data mgd
				on hah.id_approval_doc_type = mgd.id_general_data
				where mgd.code='".$code."' 
				and hah.status = 'A' 
				and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	public static function get_approval_status() {
        $sql = "SELECT  id_general_data id, description text, code
				FROM  master_general_data 
				where status = 'A' 
				  and id_general_type = 7 
				  and id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
/*	public static function get_approval($data) {
		$id_approval = $data;
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						mgd.code,
						(select id_general_data from master_general_data where code = 'new' and id_company =".session('id_company').") as id_approval_status,
						ad.id_approval_detail,
						ad.sequence,
						ad.id_employee
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				JOIN  hr_approval_detail ad ON ad.id_approval = hah.id_approval
				WHERE hah.id_approval ='".$id_approval."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
	public static function get_approval_edit($data) {
		$id_approval = $data;
        $sql = "SELECT 
                        hah.id_approval id,
                        hah.description text,
						hah.id_approval,
						hah.id_approval_doc_type,
						mgd.code,
						ad.id_approval_detail,
						ad.sequence,
						ad.id_employee
                FROM  hr_approval_header hah
				JOIN  master_general_data mgd ON mgd.id_general_data = hah.id_approval_doc_type
				JOIN  hr_approval_detail ad ON ad.id_approval = hah.id_approval
				WHERE hah.id_approval ='".$id_approval."' and hah.status = 'A' and hah.id_company =" . session('id_company');
        $result = DB::select($sql);
	//	dd($result);
        return $result;
    }
*/
	public static function get_announcement_new() {
        $sql = "SELECT hea.id_announcement
                FROM hr_employee_anouncement hea
				LEFT JOIN  master_general_data mgd
                ON  hea.id_approval_status = mgd.id_general_data
				WHERE (current_date BETWEEN hea.start_date AND hea.end_date) AND hea.published = true 
				AND hea.status = 'A' AND hea.id_company = ".session('id_company')."
				AND hea.id_announcement not in(
				SELECT rau.id_announcement FROM relation_announcement_users rau
					WHERE rau.id_user = COALESCE(".session('id_user').",rau.id_user) AND rau.id_company = ".session('id_company')."
				)
				ORDER BY hea.id_announcement ASC";
        $result = DB::select($sql);
       // 	dd($result);
        return $result;
    }
	
	public static function get_announcement_show($data) {
		$result = [];
        $sql = "SELECT hea.*, mgd.code AS code_status
                FROM hr_employee_anouncement hea
				LEFT JOIN  master_general_data mgd
                ON  hea.id_approval_status = mgd.id_general_data
                WHERE hea.id_announcement  = ?
				AND (current_date BETWEEN hea.start_date AND hea.end_date) AND hea.published = '1' 
				AND hea.status = 'A' AND hea.id_company =". session('id_company');
        $result = (Array) DB::select($sql, [$data['id_announcement']]);
        if(count($result) > 0){
        	$return = $result[0];
        } else {
        	$return = [];
        }
        return $return;
    }
	public static function get_announcement_edit($data) {
        $result = [];
        $sql = "SELECT hea.*, mgd.code as code_status
                FROM hr_employee_anouncement hea
				LEFT JOIN  master_general_data mgd
                ON  hea.id_approval_status = mgd.id_general_data
				WHERE hea.id_announcement  = ?";
        $result = (Array) DB::select($sql, [$data['id_announcement']]);
        if(count($result) > 0){
        	$thisAttachment = null;
        	if(is_null($result[0]->attachment_type) && !is_null($result[0]->attachment)){
        		$urlPhoto = 'public/upload/announcement/'. $result[0]->attachment;
                if (Storage::exists($urlPhoto)) {
                    $thisAttachment = url('project/storage/app/'.$urlPhoto);
                } 
            } else {
            	if($result[0]->attachment_type == 'image'){
	                $thisAttachment = "data:image;base64,".$result[0]->attachment;
            	} else {
	                $thisAttachment = "data:application/pdf;base64,".$result[0]->attachment;
            	}
            }
            $result[0]->this_attachment = $thisAttachment;
        	$return = $result[0];
        } else {
        	$return = [];
        }
        return $return;
    }
	public static function submit_approve() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Request_Approval' 
				  and mgd.status = 'A' 
				  and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function getdata_approval_status($data) {
		$id_announcement = $data['id_announcement'];
		$id_approval = $data['id_approval'];
        $sql = "select  *
				from  hr_approval_transaction hat
				left join hr_approval_detail had
				  on  hat.id_approval_detail = had.id_approval_detail
				 and  hat.id_company = had.id_company
				left join hr_employee he
				  on  he.id_employee = had.id_employee
				 and  hat.id_company = he.id_company
				join  master_general_data mgd
				  on  hat.id_approval_status = mgd.id_general_data
				where hat.id_company = ?
				  and hat.id_source_transaction = ?
				  and hat.id_approval = coalesce(?,hat.id_approval)";
        $result = DB::select($sql,[session('id_company'),$id_announcement,$id_approval]);

        return $result;
    }
	
	public static function cancel() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Cancel' 
				  and mgd.status = 'A' 
				  and mgd.id_company =". session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	
	public static function approved() {
        $sql = "SELECT mgd.id_general_data
				  FROM master_general_data mgd
				  WHERE mgd.code = 'Approved' and mgd.status = 'A' AND mgd.id_company =".session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }

	
}
