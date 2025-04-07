<style type="text/css">
body{
	font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
}

</style>

<body style="color: black;">
	<div class="row" style="text-align: center;width:1040px;">
		<h4>LAMPIRAN BERITA ACARA<br>PENETAPAN PESERTA<br>PROGRAM PERBAIKAN KINERJA (P2K)<br>KARYAWAN</h4>
	</div>
	<br>	
	<div style="margin-bottom:10px;padding:2px;width:1042px;font-size:12px;">
		<div>
			<table border="0" style="font-size:12px;border:1px solid #ccc;">
				<tr>
				<?php if($result->dept_code == '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){ ?>	
					<th align="left" style="background:#ededed;border:1px solid #ccc;width:160px;vertical-align:middle;padding:5px;">Departemen / Divisi</th>
					<td align="left" style="border:1px solid #ccc;width:520px;vertical-align:middle;padding:5px;"> {{ $result->dept }} / {{ $result->division }}</td>
				<?php } 
					else if($result->dept_code != '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){
				?>	
					<th align="left" style="background:#ededed;border:1px solid #ccc;width:160px;vertical-align:middle;padding:5px;">Departemen</th>
					<td align="left" style="border:1px solid #ccc;width:520px;vertical-align:middle;padding:5px;"> {{ $result->dept }}</td>
				<?php
					}
				?>						
				</tr>
				<tr>
					<th align="left" style="background:#ededed;border:1px solid #ccc;vertical-align:middle;padding:5px;">P2K Bulan</th>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:5px;"> {{ $result->remark_1 }}</td>
				</tr>
				<tr>
					<th align="left" style="background:#ededed;border:1px solid #ccc;vertical-align:middle;padding:5px;">Atas performance (P3M)</th>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:5px;"> {{ $result->remark_2 }}</td>
				</tr>
			</table>
		</div>
		<br>
		<br>
		<table border="0" style="font-size:9px;border:1px solid #ccc;">
			<thead>
				<tr style="background:#ededed;">
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;padding:0 5px 0 5px;">No.</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:100px;vertical-align:middle;padding:0 5px 0 5px;">Name</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;vertical-align:middle;padding:0 5px 0 5px;">NIK</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:80px;vertical-align:middle;padding:0 5px 0 5px;">Group</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:70px;vertical-align:middle;padding:0 5px 0 5px;">Position</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:55px;vertical-align:middle;padding:0 5px 0 5px;">Region</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;padding:0 5px 0 5px;">Branch</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:100px;vertical-align:middle;padding:0 5px 0 5px;">Direct Spv</th>
					<?php if(($result->dept_code != '170_SAL' || $result->dept_code == '170_SAL') &&  $result->remark_6 == 'os'){ ?>
						<th rowspan="2" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;padding:0 5px 0 5px;">Vendor OS</th>
					<?php } ?>
					<?php if($result->dept_code == '170_SAL' &&  ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){ ?>
						<th rowspan="2" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;padding:0 5px 0 5px;">Threshold<br>Offtake 3M (%)</th>
						<th rowspan="2" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;padding:0 5px 0 5px;">IDX sales<br>volume P3M (%)</th>
					<?php } 
					else{
					?>
						<th rowspan="2" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;padding:0 5px 0 5px;">Threshold<br>KPI</th>
					<?php } ?>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;padding:0 5px 0 5px;">AP3M KPI</th>
					<?php if($result->dept_code == '170_SAL' &&  ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){ ?>
						<th colspan="5" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Target Offtake</th>	
					<?php } 
					else{
					?>
						<th colspan="5" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Target KPI</th>
					<?php } ?>					
				</tr>
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->months[0] }}</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->months[1] }}</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->months[2] }}</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->months[3] }}</th>
					<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->months[4] }}</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					$no = 1;	
					foreach($result2 as $key=>$val){	
				?>
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{!! $val->emp_name !!}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{{ $val->nik_employee }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{{ $val->group }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{{ $val->position }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{{ $val->region }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{{ $val->branch }}</td>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{{ $val->name_supervisor }}</td>
					<?php if(($result->dept_code != '170_SAL' || $result->dept_code == '170_SAL') && $result->remark_6 == 'os'){ ?>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;padding:5px;">{{ $val->company_name }}</td>
					<?php } ?>	
					<?php if(count($val->sales_offtake) > 0){ ?>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->minimum_score_kpi_level }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->index_sales_percentage }}</td>
					<?php } 
					else { ?>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->minimum_score_kpi_level }}</td>
					<?php	} ?>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->kpi_average }}</td>
					<?php if(count($val->sales_offtake) > 0){ ?>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ number_format($val->sales_offtake[0]) }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ number_format($val->sales_offtake[1]) }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ number_format($val->sales_offtake[2]) }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ number_format($val->sales_offtake[3]) }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ number_format($val->sales_offtake[4]) }}</td>
					<?php } 
					else { ?>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->minimum_score_kpi_level }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->minimum_score_kpi_level }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->minimum_score_kpi_level }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->minimum_score_kpi_level }}</td>
						<td align="center" style="border:1px solid #ccc;vertical-align:middle;font-size:10px;">{{ $val->minimum_score_kpi_level }}</td>
					<?php	} ?>
				</tr>
				<?php 
					$no++;
					}
				?>	
			</tbody>
		</table>				
		<br>
		<br>
		<div style="width:500px;">
		{{ $result->position }}<br>
		{{ $result->com_name }}<br>
		<div style="padding:10px 0 10px 25px;">
			<img src="data:image/png;base64, {!! $result->acc !!}" width="80">
		</div>
		( {{ $result->emp_name }} )
		</div>
	</div>
	
</body>