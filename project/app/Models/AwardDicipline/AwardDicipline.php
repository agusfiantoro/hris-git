<?php

namespace App\Models\AwardDicipline;

use App\Models\GeneralSetting\CompanySetting\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class AwardDicipline extends Model {

    protected $table = 'hr_award_dicipline_transaction';
    protected $primaryKey = 'id_transaction';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_transaction', 'reference_number', 'id_employee', 'transaction_type', 'effective_date', 'expired_date', 'award_letter_number', 'award_certificate_number', 'dicipline_type','description_name', 'reference_date', 'attachment_type', 'attachment', 'remark', 'status', 'id_company', 'created_by', 'updated_by', 'is_link_with_letter_number','progress_status'
    ];
	
	public static function getkode_awd(){
			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', session('id_company'))->first();
			
		$nomor = $com->company_code.'-AWD-'.$monthyear.'-';
			
		$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('transaction_type', 'A')->where('reference_number', 'LIKE', "%{$monthyear}%")->max('reference_number'), 15, 21);
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
	public static function getkode_dcp(){
			
		$monthyear = date('Y').date('m');
		$com = Company::where('id_company', session('id_company'))->first();
			
		$nomor = $com->company_code.'-DCP-'.$monthyear.'-';
			
		$noUrutAkhir = (int) substr(self::where('id_company', session('id_company'))->where('transaction_type', 'D')->where('reference_number', 'LIKE', "%{$monthyear}%")->max('reference_number'), 15, 21);
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

    public static function getdata_awd($idEmployee=[]) {
        $data = DB::table('hr_award_dicipline_transaction')
                ->join('hr_employee as he', 'hr_award_dicipline_transaction.id_employee', '=', 'he.id_employee')
                ->leftJoin('master_position_detail as mpd', function ($join) {
					$join->where('mpd.secondary_position', 0);
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->leftJoin('master_job_position as mjp', function ($join) {
                    $join->on('mpr.id_position', '=', 'mjp.id_position');
                    $join->on('mpr.id_company', '=', 'mjp.id_company');
                })
                ->leftJoin('master_department as md', function ($join) {
                    $join->on('mjp.id_dept', '=', 'md.id_dept');
                    $join->on('mjp.id_company', '=', 'md.id_company');
                })
                ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                    $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                    $join->on('rpp.id_company', '=', 'mpd.id_company');
                })
                ->leftJoin('master_principal as mp', function ($join) {
                    $join->on('mp.id_principal', '=', 'rpp.id_principal');
                })
                ->leftJoin('master_branch as mb', function ($join) {
                    $join->on('mpd.id_branch', '=', 'mb.id_branch');
                    $join->on('mpd.id_company', '=', 'mb.id_company');
                })
                ->leftJoin('master_region as mr', function ($join) {
                    $join->on('mb.id_region', '=', 'mr.id_region');
                    $join->on('mb.id_company', '=', 'mr.id_company');
                })
                ->select('hr_award_dicipline_transaction.*', 'he.name as employee_name','he.nik_employee','mpr.description as position', 'md.description as department', 'mr.description as region', 'mb.description as branch', \DB::raw("STRING_AGG(mp.description,', ') AS principal"))
                ->where('hr_award_dicipline_transaction.id_company', session('id_company'))
                ->where('hr_award_dicipline_transaction.transaction_type', 'A')
				->groupBy('hr_award_dicipline_transaction.id_transaction', 'he.name','he.nik_employee','mpr.description', 'md.description', 'mr.description', 'mb.description')
                ->orderBy('hr_award_dicipline_transaction.id_transaction', 'DESC');

            if(count($idEmployee) > 0){
                $data->whereIn('hr_award_dicipline_transaction.id_employee', $idEmployee);
            }
            $data = $data->get();
        return $data;
    }
	public static function getdata_dcp($idEmployee=[]) {
     //   $getActive = collect([]);
    //    $getInactive = collect([]);

        $dataActive = DB::table('hr_award_dicipline_transaction')
            ->join('hr_employee as he', 'hr_award_dicipline_transaction.id_employee', '=', 'he.id_employee')
            ->join('hr_employee as he2', 'hr_award_dicipline_transaction.created_by', '=', 'he2.id_user')
            ->join('master_general_data as mgd', 'hr_award_dicipline_transaction.dicipline_type', '=', 'mgd.id_general_data')
            ->leftJoin('master_position_detail as mpd', function ($join) {
                $join->where('mpd.secondary_position', 0);
                $join->on('he.id_employee', '=', 'mpd.id_employee');
                $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
            })
            ->leftJoin('master_position_routing as mpr', function ($join) {
                $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                $join->on('mpd.id_company', '=', 'mpr.id_company');
            })
            ->leftJoin('master_job_position as mjp', function ($join) {
                $join->on('mpr.id_position', '=', 'mjp.id_position');
                $join->on('mpr.id_company', '=', 'mjp.id_company');
            })
            ->leftJoin('master_department as md', function ($join) {
                $join->on('mjp.id_dept', '=', 'md.id_dept');
                $join->on('mjp.id_company', '=', 'md.id_company');
            })
            ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                $join->on('rpp.id_company', '=', 'mpd.id_company');
            })
            ->leftJoin('master_principal as mp', function ($join) {
                $join->on('mp.id_principal', '=', 'rpp.id_principal');
            })
            ->leftJoin('master_branch as mb', function ($join) {
                $join->on('mpd.id_branch', '=', 'mb.id_branch');
                $join->on('mpd.id_company', '=', 'mb.id_company');
            })
            ->leftJoin('master_region as mr', function ($join) {
                $join->on('mb.id_region', '=', 'mr.id_region');
                $join->on('mb.id_company', '=', 'mr.id_company');
            })
            ->select('hr_award_dicipline_transaction.*', 'he.name as employee_name','he.nik_employee','mgd.description as desc_type','he2.name as created_by_user','mpr.description as position', 'md.description as department', \DB::raw("STRING_AGG(mp.description,', ') AS principal"), 'mr.description as region', 'mb.description as branch')
            ->where('hr_award_dicipline_transaction.id_company', session('id_company'))
            ->where('hr_award_dicipline_transaction.transaction_type', 'D')
            ->where('he.status', 'A')
            ->whereRaw("(LENGTH(hr_award_dicipline_transaction.attachment) <=  1000 OR hr_award_dicipline_transaction.attachment IS NULL)")
            ->groupBy('hr_award_dicipline_transaction.id_transaction', 'he.name','he.nik_employee','mgd.description','he2.name','mpr.description', 'md.description', 'mr.description', 'mb.description')
            ->orderBy('hr_award_dicipline_transaction.id_transaction', 'DESC');
    //     dd($dataActive->toSql());   
        if(count($idEmployee) > 0){
            $dataActive->whereIn('hr_award_dicipline_transaction.id_employee', $idEmployee);
        }
        $getActive = collect($dataActive->get());

        $getEmployee = DB::table('hr_career_transaction as hct')
            ->leftJoin('hr_employee as he', 'hct.id_employee', '=', 'he.id_employee')
            ->select(DB::raw("DISTINCT(hct.id_employee) as id_employee"), DB::raw("MAX(hct.id_old_position_detail) as id_old_position_detail"), DB::raw("MAX(hct.id_company) as id_company"))
            ->where('he.status', 'I')
            ->groupBy('hct.id_employee');

        $dataInactive = DB::table('hr_award_dicipline_transaction')
            ->join('hr_employee as he', 'hr_award_dicipline_transaction.id_employee', '=', 'he.id_employee')
            ->join('hr_employee as he2', 'hr_award_dicipline_transaction.created_by', '=', 'he2.id_user')
            ->join('master_general_data as mgd', 'hr_award_dicipline_transaction.dicipline_type', '=', 'mgd.id_general_data')
            ->joinSub($getEmployee, 'hct', function (JoinClause $join) {
                $join->on('hct.id_employee', '=', 'he.id_employee');
            })
            ->join('master_position_detail as mpd', function ($join) {
                $join->where('mpd.secondary_position', 0);
                $join->on('hct.id_old_position_detail', '=', 'mpd.id_position_detail');
                $join->whereRaw('(mpd.id_company = hct.id_company OR mpd.assigned_to_company = hct.id_company)');
            })
            ->leftJoin('master_position_routing as mpr', function ($join) {
                $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                $join->on('mpd.id_company', '=', 'mpr.id_company');
            })
            ->leftJoin('master_job_position as mjp', function ($join) {
                $join->on('mpr.id_position', '=', 'mjp.id_position');
                $join->on('mpr.id_company', '=', 'mjp.id_company');
            })
            ->leftJoin('master_department as md', function ($join) {
                $join->on('mjp.id_dept', '=', 'md.id_dept');
                $join->on('mjp.id_company', '=', 'md.id_company');
            })
            ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                $join->on('rpp.id_company', '=', 'mpd.id_company');
            })
            ->leftJoin('master_principal as mp', function ($join) {
                $join->on('mp.id_principal', '=', 'rpp.id_principal');
            })
            ->leftJoin('master_branch as mb', function ($join) {
                $join->on('mpd.id_branch', '=', 'mb.id_branch');
                $join->on('mpd.id_company', '=', 'mb.id_company');
            })
            ->leftJoin('master_region as mr', function ($join) {
                $join->on('mb.id_region', '=', 'mr.id_region');
                $join->on('mb.id_company', '=', 'mr.id_company');
            })
            ->select('hr_award_dicipline_transaction.*', 'he.name as employee_name','he.nik_employee','mgd.description as desc_type','he2.name as created_by_user','mpr.description as position', 'md.description as department',  \DB::raw("STRING_AGG(mp.description,', ') AS principal"), 'mr.description as region', 'mb.description as branch')
            ->where('hr_award_dicipline_transaction.id_company', session('id_company'))
            ->where('hr_award_dicipline_transaction.transaction_type', 'D')
            ->where('he.status', 'I')
			->whereRaw("(LENGTH(hr_award_dicipline_transaction.attachment) <=  1000 OR hr_award_dicipline_transaction.attachment IS NULL)")
			->groupBy('hr_award_dicipline_transaction.id_transaction', 'he.name','he.nik_employee','mgd.description','he2.name','mpr.description', 'md.description', 'mr.description', 'mb.description')
            ->orderBy('hr_award_dicipline_transaction.id_transaction', 'DESC');            
        if(count($idEmployee) > 0){
            $dataInactive->whereIn('hr_award_dicipline_transaction.id_employee', $idEmployee);
        }
        $getInactive = collect($dataInactive->get());

        $merged = $getActive->merge($getInactive);
        return $merged;
    }
	
	public static function get_employee() {
        $sql = "SELECT 
                        id_employee id,
                         CONCAT(name,' (',nik_employee,')','(',status,')') as text
                FROM  hr_employee WHERE id_company = " . session('id_company')."
				ORDER BY name ASC";
        $result = DB::select($sql);

        return $result;
    }
	public static function get_req_employee() {
        $sql = "SELECT 
                        id_employee id,
                         CONCAT(name,' (',nik_employee,')') as text
                FROM  hr_employee where status = 'A' AND id_user = ". session('id_user')." AND id_company =" . session('id_company');
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
                FROM  master_general_data where status = 'A' AND code = 'Announcement_Award' AND id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	
	public static function get_dicipline_type() {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
                FROM  master_general_data where status = 'A' AND id_general_type = 14 AND id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
	public static function get_dicipline_param($id) {
        $sql = "SELECT 
                        id_general_data id,
                        description text,
						code
                FROM  master_general_data where status = 'A' AND id_general_type = 14 AND id_general_data = COALESCE(".$id.",id_general_data) AND id_company =" . session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function edit_award_dicipline($id) {
        $sql = "SELECT he.nik_employee, hadt.* FROM hr_award_dicipline_transaction hadt
				LEFT JOIN hr_employee he
				ON hadt.id_employee = he.id_employee
				WHERE hadt.id_transaction = ".$id." AND hadt.id_company = " . session('id_company');
        $result = DB::select($sql)[0];
        return $result;
    }
	public static function get_api_type($codeDicipline,$idDicipline=null) {
		if($idDicipline != null){
			$queryDicipline = " AND hel.id_letter = ".$idDicipline;
		}
		else{
			$queryDicipline = "";
		}
        $sql = "SELECT hel.id_letter AS id, hel.date AS tgl_surat, hel.reference_number AS no_surat,
				he.name, he.nik_employee AS nik, mgd2.code AS kategori, hel.effective_date AS tgl_mulai,
				hel.expired_date AS tgl_berakhir
				FROM hr_electronic_letter hel
				JOIN hr_employee he
				ON hel.id_employee = he.id_employee
				JOIN master_general_data mgd
				ON hel.id_letter_type = mgd.id_general_data AND mgd.code IN('SP','SP3')
				JOIN master_general_data mgd2
				ON hel.id_category = mgd2.id_general_data
				JOIN master_general_type mgt
				ON mgd2.id_general_type = mgt.id_general_type
				WHERE mgd.id_company = ".session('id_company')." AND mgt.general_type = 'master_letter_category' 
				AND mgd2.code = '".$codeDicipline."' ".$queryDicipline."
				ORDER BY he.name ASC";
        $result = DB::select($sql);

        return $result;
    }
}
