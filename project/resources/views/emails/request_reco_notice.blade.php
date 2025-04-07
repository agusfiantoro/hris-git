@component('mail::message')

<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, Bpk/Ibu {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
 Dengan ini kami informasikan bahwa ada sebuah permohonan <b>Recommendation Form (RECO)</b> yang dibuat di dalam aplikasi HRIS. <br>
 Berikut permohonan yang dimaksud: 
 </div>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding-top:10px;font-size:14px;">
		<tr>
			<td style="width:150px;display:inline-block;"><b>No. Permohonan</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['reference_number'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Tanggal Dibuat</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['creation_date'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Jenis Permohonan</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['permohonan'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Dibuat Oleh</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['request_by'] }}</td>
		</tr>
	</table>
 @component('mail::panel')
	DETAIL
	<table width="100%" cellpadding="0" cellspacing="0" role="presentation"  style="font-size:14px;">
		<tr>
			<td style="width:150px;display:inline-block;"><b>Employee Name</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['karyawan'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Current Position</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['position'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Current Branch</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['old_branch'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Current Region</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['old_region'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Current Division</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['old_principal'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Notes</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['note'] }}</td>
		</tr>
		<?php
			if($param['new_position'] != "-"){				
		?>
		<tr>
			<td style="width:150px;display:inline-block;"><b>New Position</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['new_position'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>New Branch</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['new_branch'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>New Region</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['new_region'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>New Division</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['new_principal'] }}</td>
		</tr>
			<?php } ?>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Effective Date</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['effective_date'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Expired Date</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['expired_date'] }}</td>
		</tr>
	</table>
	@endcomponent
<br>	
<br>	
<div style="font-size:14px;">
Thanks,<br>
HRIS Team
</div>
@endcomponent