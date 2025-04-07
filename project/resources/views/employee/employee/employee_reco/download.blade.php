<!DOCTYPE html>
<html lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">

th, td {
  padding: 5px;
  vertical-align:top;
}
body{
	font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
}
.badge {
  display: inline-block;
  padding:5px 15px 5px 15px;
  font-size:12px;
  font-weight: 700;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: .25rem;
  transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
  color:white;
}
.badge-danger {
  background-color: #dc3545;
}
.badge-success {
  background-color: #28a745;
}
.page-avoid{
  page-break-inside: avoid;
}

.small-box {
  border-radius: .25rem;
  box-shadow: 0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);
  display: block;
  margin-bottom: 0px;
  position: relative;
}
.small-box > .small-box-footer {
  background: rgba(0,0,0,.1);
  color: rgba(255,255,255,.8);
  display: block;
  padding: 3px 0;
  position: relative;
  text-align: center;
  text-decoration: none;
}

@page { margin:30px 10px 15px 35px; }
</style>
<body style="color: black;padding: 0px;">
	<div class="row" style="border:1px solid #ccc;border-radius:5px;text-align: center;width:725px;">
		<h3>EMPLOYEE RECOMMENDATION</h3>
	</div>
	<br>
	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;margin-bottom:10px;padding:2px;width:720px;">
		<div style="padding:12px 5px 2px 8px;">
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr>
					<th align="left" style="background:#ededed;border:1px solid #ccc;width:100px;vertical-align:middle;">Reco Number</th>
					<td align="left" colspan="5" style="border:1px solid #ccc;width:650px;vertical-align:middle;">{{ $result['reference_number'] }}</td>
				</tr>
				<tr>
					<th align="left" style="background:#ededed;border:1px solid #ccc;width:100px;vertical-align:middle;">Name</th>
					<td align="left" colspan="5" style="border:1px solid #ccc;width:650px;vertical-align:middle;">{{ $result['name'] }}</td>
				</tr>
				<tr>
					<th align="left" style="background:#ededed;border:1px solid #ccc;width:100px;vertical-align:middle;">NIK</th>
					<td align="left" colspan="5" style="border:1px solid #ccc;width:150px;vertical-align:middle;">{{ $result['nik_employee'] }}</td>
				</tr>
				<tr>
					<th colspan="6" align="center" style="border:1px solid #ccc;vertical-align:middle;background:#fffcd5;">Current Position</th>
				</tr>
				<tr style="background:#ededed;">			
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Position</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Department</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Grade</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Principal</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Branch</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Location</th>
				</tr>
				<tr>			
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['position'] }}</td>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['dept'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['grade'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['principal'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['branch'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['location'] }}</td>
				</tr>
				<tr>
					<th colspan="6" align="center" style="border:1px solid #ccc;vertical-align:middle;background:#fffcd5;">New Position</th>
				</tr>
				<tr style="background:#ededed;">			
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Position</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Department</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Grade</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Principal</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Branch</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Location</th>
				</tr>
				<tr>			
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['new_position'] }}</td>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['new_dept'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['new_grade'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['new_principal'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['new_branch'] }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['new_location'] }}</td>
				</tr>
				<tr style="background:#ededed;">			
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Effective Date</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Expired Date</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Duration</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Category</th>
					<th align="center" colspan="2" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">Type</th>
				</tr>
				<tr>			
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['effective_date'] ? date('d F Y',strtotime($result['effective_date'])) : "-" }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['expired_date'] ? date('d F Y',strtotime($result['expired_date'])) : "-" }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['expired_date'] ? $result['duration'] : "-" }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['category'] }}</td>
					<td align="center" colspan="2" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $result['type'] }}</td>
				</tr>
			</table>
		</div>
	</div>
	
	<?php if($result['reco_flag'] == true){ ?>
		<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;padding:2px;margin-bottom:10px;width:720px;">
			<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
				<b>Quantitative Review</b>
			</div>
			<div style="padding:5px 5px 2px 8px;">
				<?php if(count($result['quanti']) > 0) {	?>
				<table border="0" style="font-size:10px;border:1px solid #ccc;">
					<thead>
						<tr style="background:#ededed;">
							<th rowspan="2" align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
							<th rowspan="2" align="center" style="border:1px solid #ccc;width:153px;vertical-align:middle;">Measurement</th>
							<th colspan="4" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">{{ $result['month'][5] }}</th>
							<th colspan="4" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">{{ $result['month'][4] }}</th>
							<th colspan="4" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">{{ $result['month'][3] }}</th>
						</tr>
						<tr style="background:#ededed;">
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Bobot</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Obj.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Ach.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Point</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Bobot</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Obj.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Ach.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Point</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Bobot</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Obj.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Ach.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Point</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no = 1;	
							foreach($result['quanti'] as $key=>$val){	
						?>
						<tr>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
						<td align="left" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['desc_kpi'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['weight_1'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['obj_1'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['ach_1'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['idx_1'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['weight_2'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['obj_2'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['ach_2'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['idx_2'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['weight_3'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['obj_3'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['ach_3'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['idx_3'] }}</td>
					</tr>
						<?php 
							$no++;
							}
						?>	
					</tbody>
					<tfoot>
						<tr align="center" style="background:#fffcd5;">
							<th colspan="2" style="border:1px solid #ccc;vertical-align:middle;">Total Point</th>
							<th style="border:1px solid #ccc;vertical-align:middle;">{{ $result['b_1'] }}</th>
							<th colspan="3" style="border:1px solid #ccc;vertical-align:middle;">{{ $result['average_kpi_1_month_ago'] }}</th>
							<th style="border:1px solid #ccc;vertical-align:middle;">{{ $result['b_2'] }}</th>
							<th colspan="3" style="border:1px solid #ccc;vertical-align:middle;">{{ $result['average_kpi_2_month_ago'] }}</th>
							<th style="border:1px solid #ccc;vertical-align:middle;">{{ $result['b_3'] }}</th>
							<th colspan="3" style="border:1px solid #ccc;vertical-align:middle;">{{ $result['average_kpi_3_month_ago'] }}</th>
						</tr>
					</tfoot>									
				</table>	
				<?php	} 
				else{
					echo '<i style="font-size:12px;">No Data</i>';
				}
				?>
			</div>
			<div style="padding:0px 5px 2px 8px;">
				<?php if(count($result['quanti']) > 0) {	?>
				<table border="0" style="font-size:10px;border:1px solid #ccc;">
					<thead>
						<tr style="background:#ededed;">
							<th rowspan="2" align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
							<th rowspan="2" align="center" style="border:1px solid #ccc;width:153px;vertical-align:middle;">Measurement</th>
							<th colspan="4" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">{{ $result['month'][2] }}</th>
							<th colspan="4" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">{{ $result['month'][1] }}</th>
							<th colspan="4" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">{{ $result['month'][0] }}</th>
						</tr>
						<tr style="background:#ededed;">
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Bobot</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Obj.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Ach.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Point</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Bobot</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Obj.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Ach.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Point</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Bobot</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Obj.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Ach.</th>
							<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Point</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no = 1;	
							foreach($result['quanti'] as $key=>$val){	
						?>
						<tr>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
						<td align="left" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['desc_kpi'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['weight_4'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['obj_4'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['ach_4'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['idx_4'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['weight_5'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['obj_5'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['ach_5'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['idx_5'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['weight_6'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['obj_6'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['ach_6'] }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val['idx_6'] }}</td>
					</tr>
						<?php 
							$no++;
							}
						?>	
					</tbody>
					<tfoot>
						<tr align="center" style="background:#fffcd5;">
							<th colspan="2" style="border:1px solid #ccc;vertical-align:middle;">Total Point</th>
							<th style="border:1px solid #ccc;vertical-align:middle;">{{ $result['b_4'] }}</th>
							<th colspan="3" style="border:1px solid #ccc;vertical-align:middle;">{{ $result['average_kpi_4_month_ago'] }}</th>
							<th style="border:1px solid #ccc;vertical-align:middle;">{{ $result['b_5'] }}</th>
							<th colspan="3" style="border:1px solid #ccc;vertical-align:middle;">{{ $result['average_kpi_5_month_ago'] }}</th>
							<th style="border:1px solid #ccc;vertical-align:middle;">{{ $result['b_6'] }}</th>
							<th colspan="3" style="border:1px solid #ccc;vertical-align:middle;">{{ $result['average_kpi_6_month_ago'] }}</th>
						</tr>
					</tfoot>									
				</table>
				<?php	} 
				else{
					echo '<i style="font-size:12px;">No Data</i>';
				}
				?>
			</div>
			<div style="padding:0px 5px 2px 8px;">
				<table border="0" style="font-size:10px;border:1px solid #ccc;">
					<tr style="background:#e9f3f9;font-size:12px;font-weight:bold;">
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;width:180px;">Average Score KPI (%)</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;width:40px;">{{ $result['kpi_average_ap6m'] }}</td>				
							@if($result['kpi_status'] == 'Pass')  
								<td align="center" class="badge-success" style="color:white;border:1px solid #ccc;vertical-align:middle;width:442px;">Pass</td>
							@else
								<td align="center" class="badge-danger" style="color:white;border:1px solid #ccc;vertical-align:middle;width:442px;">Not Pass</td>
							@endif
						</td>
					</tr>
				</table>
				<i style="font-size:8px;">Syarat Kelulusan secara Quantitative: Minimal Average Score KPI = 85%</i>
			</div>
		</div>
		<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;padding:2px;margin-bottom:10px;width:720px;">
			<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
				<b>Qualitative Review</b>
			</div>
			<div style="padding:5px 5px 2px 8px;">
				<?php if(count($result['quali']) > 0) {	?>
				<table border="0" style="font-size:9px;border:1px solid #ccc;">
					<thead>
						<tr style="background:#ededed;">
							<th rowspan="2" align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
							<th rowspan="2" align="center" style="border:1px solid #ccc;width:375px;vertical-align:middle;">Appraisal Area</th>
							<th colspan="5" align="center" style="border:1px solid #ccc;vertical-align:middle;">Number of Appraisers (People)</th>
							<th rowspan="2" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Average (Level)</th>
						</tr>
						<tr style="background:#ededed;">
							<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Level 5</th>
							<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Level 4</th>
							<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Level 3</th>
							<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Level 2</th>
							<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Level 1</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no = 1;	
							foreach($result['quali'] as $key=>$val){	
						?>
						<tr>
							<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
							<td align="justify" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{!! $val->question !!}</td>
							<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->level_5 }}</td>
							<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->level_4 }}</td>
							<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->level_3 }}</td>
							<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->level_2 }}</td>
							<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->level_1 }}</td>
							<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->avg }}</td>
						</tr>
						<?php 
							$no++;
							}
						?>	
					</tbody>
				</table>
				<i style="font-size:8px;">Level 5 = Baik Sekali, Level 4 = Baik, Level 3 = Sedang, Level 2 = Kurang, Level 1 = Kurang Sekali</i>
				<?php	} 
				else{
					echo '<i style="font-size:12px;">No Data</i>';
				}
				?>
			</div>
		</div>
		
		<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;padding:2px;margin-bottom:10px;width:720px;">
			<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
				<b>Summary Review</b>
			</div>
			<div style="padding:5px 5px 2px 8px;font-size:10px;">
				<div style="margin-bottom:8px;padding:6px;border:1px solid #ccc;text-align: justify;font-size:11px;">
					{{ $result['notes'] }}			
				</div>
				<div style="margin-bottom:3px;">Berdasarkan Quantitative dan Qualitative Result maka karyawan tersebut dinyatakan : </div>
				<table border="0" style="font-size:10px;border:1px solid #ccc;">
					<tr>
						<th align="left" style="background:#ededed;border:1px solid #ccc;vertical-align:middle;width:100px;">Decision</th>
						<td align="left" colspan="3" style="border:1px solid #ccc;vertical-align:middle;">{{ $result['decision'] }}</td>
					</tr>
					<tr>
						<th align="left" style="background:#ededed;border:1px solid #ccc;vertical-align:middle;width:100px;">Effective Date</th>
						<td align="left" style="border:1px solid #ccc;vertical-align:middle;width:223px;">{{ $result['effective_date'] ? date('d F Y',strtotime($result['effective_date'])) : "-" }} </td>
						<th align="left" style="background:#ededed;border:1px solid #ccc;vertical-align:middle;width:100px;">Expired Date</th>
						<td align="left" style="border:1px solid #ccc;vertical-align:middle;width:224px;">{{ $result['expired_date'] ? date('d F Y',strtotime($result['expired_date'])) : "-" }}</td>
					</tr>
				</table>
			</div>
		</div>
		
		<br>
		<br>	
		<?php 
		if($result['code_status'] == "Approved"){
			if($result['new_mgr'] == null ){ ?>
			<table border="0" style="font-size:9px;width:725px;">
				<tr>
					<td align="left" colspan="4" style="vertical-align:middle;font-size:10px;">{{ $result['created_date'] }}</td>
				</tr>
				<tr>
					<td align="center" style="vertical-align:middle;font-size:10px;border:1px solid #ccc;">Yang Mengajukan,</td>
					<td align="left" style="width:80px;vertical-align:middle;"></td>
					<td align="center" colspan="2" style="vertical-align:middle;font-size:10px;border:1px solid #ccc;">Menyetujui,</td>
				</tr>
				<tr>
					<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
						<img src="data:image/png;base64, {!! $result['approval']['direct'] !!}" width="60">
					</td>
					<td align="center" style="vertical-align:middle;"></td>
					<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
					<?php
						if($result['manager_approval'] != null){
					?>
						<img src="data:image/png;base64, {!! $result['approval']['manager'] !!}" width="60">
					<?php 
						}
					?>	
					</td>
					<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
					<?php
						if($result['hr_manager'] != null){
					?>
						<img src="data:image/png;base64, {!! $result['approval']['hr'] !!}" width="60">
					<?php 
						}
					?>	
					</td>
				</tr>
				<tr>
					<td align="center" style="width:190px;vertical-align:middle;border:1px solid #ccc;"><u>{{ $result['direct_name'] }}</u><br>Atasan Langsung</td>
					<td align="center" style="vertical-align:middle;"></td>
					<td align="center" style="width:190px;vertical-align:middle;border:1px solid #ccc;"><u>{{ $result['manager_approval'] }}</u><br>Manager Department</td>
					<td align="center" style="width:190px;vertical-align:middle;border:1px solid #ccc;"><u>{{ $result['hr_manager'] }}</u><br>HR Manager</td>
				</tr>
			</table>
		<?php } 
			else {
			?>
				<table border="0" style="font-size:9px;width:725px;">
					<tr>
						<td align="left" colspan="4" style="vertical-align:middle;font-size:10px;">{{ $result['created_date'] }}</td>
					</tr>
					<tr>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Yang Mengajukan,</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Disetujui oleh Pemimpin Unit Kerja Asal,</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Disetujui oleh Pemimpin Unit Kerja Tujuan,</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Disetujui HR,</td>
					</tr>
					<tr>
						<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
							<img src="data:image/png;base64, {!! $result['approval']['direct'] !!}" width="60">
						</td>
						<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
							<?php
								if($result['manager_approval'] != null){
							?>
								<img src="data:image/png;base64, {!! $result['approval']['manager'] !!}" width="60">
							<?php 
								}
							?>	
						</td>
						<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
							<?php
								if($result['new_mgr'] != null){
							?>
								<img src="data:image/png;base64, {!! $result['approval']['new_manager'] !!}" width="60">
							<?php 
								}
							?>						
						</td>
						<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
							<?php
								if($result['hr_manager'] != null){
							?>
								<img src="data:image/png;base64, {!! $result['approval']['hr'] !!}" width="60">
							<?php 
								}
							?>	
						</td>
					</tr>
					<tr>
						<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['direct_name'] }}</u><br>Atasan Langsung</td>
						<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['manager_approval'] }}</u><br>Manager Department</td>
						<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['new_mgr'] }}</u><br>Manager Department</td>
						<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['hr_manager'] }}</u><br>HR Manager</td>
					</tr>
				</table>		
		<?php } 
		} 
	} 
	else{		
		if($result['code_status'] == "Approved"){
	?>
		<br>
		<br>	
		<table border="0" style="font-size:9px;width:725px;">
			<tr>
				<td align="left" colspan="4" style="vertical-align:middle;font-size:10px;">{{ $result['created_date'] }}</td>
			</tr>
			<tr>
				<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Yang Mengajukan,</td>
				<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Disetujui oleh Pemimpin Unit Kerja Asal,</td>
				<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Disetujui oleh Pemimpin Unit Kerja Tujuan,</td>
				<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">Diketahui oleh,</td>
			</tr>
			<tr>
				<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
					<img src="data:image/png;base64, {!! $result['approval']['direct'] !!}" width="60">
				</td>
				<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
					<?php
						if($result['manager_approval'] != null){
					?>
						<img src="data:image/png;base64, {!! $result['approval']['manager'] !!}" width="60">
					<?php 
						}
					?>	
				</td>
				<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
					<?php
						if($result['new_mgr'] != null){
					?>
						<img src="data:image/png;base64, {!! $result['approval']['new_manager'] !!}" width="60">
					<?php 
						}
					?>						
				</td>
				<td align="center" style="vertical-align:middle;border:1px solid #ccc;">
					<?php
						if($result['hr_manager'] != null){
					?>
						<img src="data:image/png;base64, {!! $result['approval']['hr'] !!}" width="60">
					<?php 
						}
					?>	
				</td>
			</tr>
			<tr>
				<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['direct_name'] }}</u><br>Atasan Langsung</td>
				<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['manager_approval'] }}</u><br>Manager Department</td>
				<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['new_mgr'] }}</u><br>Manager Department</td>
				<td align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;"><u>{{ $result['hr_manager'] }}</u><br>HR Manager</td>
			</tr>
		</table>
	<?php }
		}
	?> 
	
</body>
</html>