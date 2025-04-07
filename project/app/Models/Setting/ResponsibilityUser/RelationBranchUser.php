<?php

namespace App\Models\Setting\ResponsibilityUser;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationBranchUser extends Model
{
	use HasFactory;
	
    protected $table = 'relation_branch_users';
	protected $primaryKey = 'id';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
     'id','id_user_responsibility','id_branch','id_company','created_by','updated_by'
    ];
	
	public static function get_branch_by_user_responsibility($id_user_resp, $id_company=null) {
		$id_company = $id_company ?? session('id_company');
		$id_user_resp = is_array($id_user_resp) ? $id_user_resp : [$id_user_resp];

        $data = DB::table('relation_branch_users as rbu')
                ->select('rbu.id_branch')
                ->where('rbu.id_company', '=', $id_company)
                ->whereIn('rbu.id_user_responsibility',  $id_user_resp);
  
        return $data->get();
    }

}
