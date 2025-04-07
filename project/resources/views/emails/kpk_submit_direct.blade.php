@component('mail::message')

<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, Bpk/Ibu {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
 Dengan ini kami beritahukan bahwa terdapat anggota tim Anda yang masuk sebagai PESERTA PROGRAM PERBAIKAN KINERJA (P2K) dan tercantum dalam BA PENETAPAN PESERTA PROGRAM PERBAIKAN KINERJA (P2K).<br>Berikut BA yang dimaksud:  
 </div>
	<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding-top:10px;font-size:14px;">
		<tr>
			<td style="width:150px;display:inline-block;"><b>No. BA Penetapan</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['reference_number'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Tanggal Dibuat</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['creation_date'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Dibuat Oleh</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['request_by'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Jabatan</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['pos_req'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Bulan P2K</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['month'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Performance (P3M)</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['month_performance'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Department</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['dept'] }}</td>
		</tr>
	</table>
 @component('mail::panel')
	DETAIL PESERTA PROGRAM PERBAIKAN KINERJA (P2K)
	<table width="100%" cellpadding="2" cellspacing="2" role="presentation"  style="font-size:14px;border-collapse: collapse;
		margin: 15px 0;
		font-family: sans-serif;
		box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);border: 1px solid #aaa;">
			<tr style="background-color: #ff6f6f;color: #ffffff;text-align:center;border: 1px solid #aaa;padding:3px;">
				<th style="border-right: 1px solid #aaa;padding:3px;">Employee Name</th>
				<th style="border-right: 1px solid #aaa;padding:3px;">NIK</th>
				<th style="border-right: 1px solid #aaa;padding:3px;">Position</th>
				<th style="border-right: 1px solid #aaa;padding:3px;">{{ $param['dept_code'] == '170_SAL' ? 'Threshold Offtake' : 'Threshold KPI'  }} </th>
				<?php if($param['dept_code'] == '170_SAL'){ ?>
					<th style="border-right: 1px solid #aaa;padding:3px;">IDX sales volume</th>
				<?php } ?>
				<th style="border-right: 1px solid #aaa;padding:3px;">AP3M KPI</th>
			</tr>
		@foreach($param['list_child'] as $val_param)
		<tr style="border: 1px solid #aaa;padding:3px;">
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['name_emp'] }}</td>
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['nik_employee'] }}</td>
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['pos_emp'] }}</td>
			<td style="border-right: 1px solid #aaa;padding:3px;text-align:center;">{{ $val_param['obj_kpi'] }}</td>
			<?php if($param['dept_code'] == '170_SAL'){ ?>
					<td style="border-right: 1px solid #aaa;padding:3px;text-align:center;">{{ $val_param['act_idx'] }}</td>
			<?php } ?>
			<td style="border-right: 1px solid #aaa;padding:3px;text-align:center;">{{ $val_param['act_kpi'] }}</td>
		</tr>
		@endforeach
	</table>
	@endcomponent
<div style="font-size:14px;">
Jika Anda sebagai Atasan Langsung (Direct Supervisor), maka Anda wajib melakukan Review dan mencatat hasil Review didalam HRIS my.borwita.co.id.
</div>
<br>
<div align="center" style="pdding-top:5px;font-size:16px;">
	 <a href="{{ $param['content_link'] }}" style='border-radius: 5px;background:#ff6f6f;color:#ffffff;font-family:Calibri, Helvetica neue, sans-serif;font-size:14px;font-weight:400;line-height:21px;margin:0;text-decoration:none;text-transform:none;padding:8px;' target='_blank'>
		Click to Apps
	</a>
</div>

<br>	
<br>	
<div style="font-size:14px;">
Thanks,<br>
HRIS Team
</div>
@endcomponent