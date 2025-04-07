@component('mail::message')

<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
 Dengan ini kami beritahukan bahwa ada sebuah permohonan yang dibuat di dalam aplikasi HRIS menunggu Approval dari Anda. <br>
 Berikut permohonan yang dimaksud: 
 </div>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding-top:10px;font-size:14px;">
		<tr>
			<td style="width:150px;display:inline-block;"><b>No. Permohonan</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['reference_number'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Karyawan</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['karyawan'] }}</td>
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
				<td style="width:150px;display:inline-block;"><b>Leave Name</b></td>
				<td style="display:inline-block;"><b>:</b></td>
				<td style="display:inline-block;">{{ $param['leave_name'] }}</td>
			</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>From Date</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['from_date'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>To Date</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['to_date'] }}</td>
		</tr>
			<tr>
				<td style="width:150px;display:inline-block;"><b>No. of Day (s)</b></td>
				<td style="display:inline-block;"><b>:</b></td>
				<td style="display:inline-block;">{{ $param['qty_days'] }}</td>
			</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>Note</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['note'] }}</td>
		</tr>
	</table>
	@endcomponent
<div style="font-size:14px;">
Silahkan masuk ke aplikasi HRIS untuk memasukkan keputusan Anda.
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