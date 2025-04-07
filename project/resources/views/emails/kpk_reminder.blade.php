@component('mail::message')

<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, Bpk/Ibu {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
 Dengan ini kami beritahukan bahwa Anda <b>BELUM</b> melakukan Review P2K terhadap anggota tim Anda.
 </div>
 @component('mail::panel')
	Detail Peserta Program Perbaikan Kinerja (P2K) yang harus Anda Review
	<table width="100%" cellpadding="2" cellspacing="2" role="presentation"  style="font-size:14px;border-collapse: collapse;
		margin: 15px 0;
		font-family: sans-serif;
		box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);border: 1px solid #aaa;">
			<tr style="background-color: #ff6f6f;color: #ffffff;text-align:center;border: 1px solid #aaa;padding:3px;">
				<th style="border-right: 1px solid #aaa;padding:3px;">Employee Name</th>
				<th style="border-right: 1px solid #aaa;padding:3px;">NIK</th>
				<th style="border-right: 1px solid #aaa;padding:3px;">Position</th>
				<th style="border-right: 1px solid #aaa;padding:3px;">Branch</th>
				<th style="border-right: 1px solid #aaa;padding:3px;">Month Performance</th>
			</tr>
		@foreach($param['list_child'] as $val_param)
		<tr style="border: 1px solid #aaa;padding:3px;">
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['name_emp'] }}</td>
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['nik_employee'] }}</td>
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['pos_emp'] }}</td>
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['branch'] }}</td>
			<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['period_date'] }}</td>
		</tr>
		@endforeach
	</table>
	@endcomponent
<div style="font-size:14px;">
Jika Anda sebagai Atasan Langsung (Direct Supervisor), Segera lakukan Review dan catat hasil Review kedalam HRIS my.borwita.co.id.
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