@component('mail::message')

<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
	Dengan ini kami beritahukan bahwa proses penilaian 360 Feedback sedang berlangsung, mohon melakukan penilaian terhadap :<br>
 </div>
 @component('mail::panel')
	<table width="100%" cellpadding="0" cellspacing="0" role="presentation"  style="font-size:14px;">
		<tr>
			<td style="width:150px;display:inline-block;"><b>Nama</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['karyawan'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;display:inline-block;"><b>NIK</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['content_username'] }}</td>
		</tr>
	</table>
	@endcomponent
<div style="font-size:14px;">
Silahkan cek di aplikasi HRIS dan lakukan penilaian 360 Feedback.
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