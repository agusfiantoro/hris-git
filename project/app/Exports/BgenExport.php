<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\CashAdvance\TravelRequest\TravelRequest;
use App\Models\CashAdvance\OfficialTravel\HrOfficialTravel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BgenExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$id_company = session('id_company');
    	$sql = "SELECT
    	hr_official_travel.reference_number as reference_number,
    	hr_employee.name as name, hr_employee.nik_employee as nik_employee, master_position_routing.description dec_position, master_branch.description as dec_branch, master_principal.principal_code as principal_code,master_department.description as dec_dept, master_product_categories.description dec_product_category, hr_official_travel.start_date as start_date, hr_expense_request.unit_price as unit_price, hr_official_travel.location_to as location_to, hr_official_travel.reason_notes as reason_notes

    	FROM hr_official_travel
    	LEFT JOIN master_position_detail ON hr_official_travel.id_position_detail=master_position_detail.id_position_detail
    	LEFT JOIN master_position_routing ON master_position_routing.id_routing=master_position_detail.id_position_routing

    	LEFT JOIN master_branch on master_branch.id_branch = master_position_detail.id_branch
    	LEFT JOIN master_region on master_region.id_region = master_branch.id_region
    	LEFT JOIN relation_positiondetail_principal on relation_positiondetail_principal.id_position_detail = master_position_detail.id_position_detail
    	LEFT JOIN master_principal on master_principal.id_principal = relation_positiondetail_principal.id_principal
    	LEFT JOIN master_division on master_division.id_division = master_principal.id_division
    	LEFT JOIN master_job_position ON master_job_position.id_position=master_position_routing.id_position
    	LEFT JOIN master_department ON master_department.id_dept=master_job_position.id_dept
    	LEFT JOIN master_job_grade ON master_job_grade.id_job_grade=master_position_routing.id_job_grade

    	LEFT JOIN hr_employee ON hr_employee.id_employee=hr_official_travel.request_by
    	LEFT JOIN master_company ON master_company.id_company=hr_official_travel.id_company
    	LEFT JOIN master_general_data ON master_general_data.id_general_data=hr_official_travel.id_reason_group
    	LEFT JOIN hr_approval_header ON hr_approval_header.id_approval=hr_official_travel.id_approval
    	LEFT JOIN hr_employee as approval_request ON approval_request.id_employee=hr_official_travel.id_approval_request
    	LEFT JOIN master_general_data as approval_status ON approval_status.id_general_data=hr_official_travel.id_approval_status
    	LEFT JOIN hr_approval_transaction ON hr_approval_transaction.id_source_transaction=hr_official_travel.id_official_travel
    	LEFT JOIN hr_cash_advance ON hr_cash_advance.id_official_travel=hr_official_travel.id_official_travel
    	LEFT JOIN hr_expense_request ON hr_expense_request.id_cash_advance=hr_cash_advance.id_cash_advance
    	LEFT JOIN inventory.master_product ON inventory.master_product.id_product=hr_expense_request.id_product
    	LEFT JOIN inventory.master_product_categories ON inventory.master_product_categories.id_product_categories=inventory.master_product.id_product_categories

    	WHERE hr_official_travel.id_company = '$id_company' AND hr_approval_transaction.source_transaction_type='Official_Travel' AND hr_approval_transaction.id_company='$id_company' AND inventory.master_product_categories.description != 'Consumable'
    	AND hr_official_travel.is_verified = 'true' ORDER BY hr_official_travel.id_official_travel DESC
    	";
    	$data = DB::select($sql);
    	$formattedData = collect($data)->map(function ($item) {
    		if ($item->dec_product_category == 'Transportation') {
    			$dec_product_category = 'PERJ DINAS-TIKET KE '.$item->location_to.' UNTUK '.$item->reason_notes;
    		}else{
    			$dec_product_category = 'PERJ DINAS-HOTEL KE '.$item->location_to.' UNTUK '.$item->reason_notes;
    		}
    		$unit_price = 'Rp. '.number_format($item->unit_price,0,",",".");
    		$carbonDate = Carbon::parse($item->start_date);
    		return [
    			'nik_employee' => $item->nik_employee,
    			'name' => $item->name,
    			'dec_position' => $item->dec_position,
    			'reference_number' => $item->reference_number,
    			'dec_branch' => $item->dec_branch,
    			'principal_code' => $item->principal_code,
    			'dec_dept' => $item->dec_dept,
    			'dec_product_category' => $dec_product_category,
    			'start_date' => $carbonDate->isoFormat('D MMMM YYYY', 'id'),
    			'bank' => '',
    			'unit_price' => $unit_price,
    		];
    	});
    	return $formattedData;
    	// return $dataCollection;
    }
    public function headings(): array
    {
    	return [
    		'NIK Karyawan',
    		'NAMA KARYAWAN/NAMA VENDOR',
    		'Position',
    		'Reference Number',
    		'Branch',
    		'Division',
    		'Department',
    		'Remark (Harus komplit keperluan dan tujuan nya apa)',
    		'Periode',
    		'Bank Name',
    		'Trans. Amount',
    	];
    }
}
