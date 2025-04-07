<?php

namespace App\Imports;

use App\Models\Organization\OrganizationStructure\JobPosition;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Illuminate\Support\Facades\DB;



class PositionImport implements ToModel, WithHeadingRow, WithProgressBar
{
	use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
	 
    public function model(array $row)
    {
	//	dd($row);
		$dept = DB::table('master_department')->where('description', $row['id_dept'])->first();
		$parent_position = DB::table('master_job_position')->where('description', $row['parent_id_position'])->first();
		$com = DB::table('master_company')->where('company_name', $row['id_company'])->first();

		$a= $dept->id_dept;
		$b= $row['description'];
		$c= $row['job_description'];
		$d= $row['status'];
		$e= $parent_position->id_position;
		$f= $com->id_company;

        return new JobPosition([
		 'id_dept' => $a,
            'description' => $b,
            'job_description' => $c,
            'status' => $d,
            'parent_id_position' => $e,
            'id_company' => $f,
			'created_by' =>session('id_user'),            
        ]);
		
    }
}
