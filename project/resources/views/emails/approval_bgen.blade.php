{{-- @component('mail::message') --}}

<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
 Dengan ini kami beritahukan bahwa <i><b>Request Sales Code</b></i> anda 
 telah selesai dilakukan <i>Final Approval</i> oleh <i>Approver</i>.<br>
 </div>
 {{-- @component('mail::panel') --}}
	{{ $param['permohonan'] }}
	<table width="100%" cellpadding="0" cellspacing="0" role="presentation"  style="font-size:14px;">
		<tr>
			<td style="width:150px;display:inline-block;"><b>Name</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['sales_data']['sales_name'] }}</td>
		</tr>
        <tr>
			<td style="width:150px;display:inline-block;"><b>Branch</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['sales_data']['sales_branch'] }}</td>
		</tr>
        <tr>
			<td style="width:150px;display:inline-block;"><b>Principal</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['sales_data']['sales_principal'] }}</td>
		</tr>
        <tr>
			<td style="width:150px;display:inline-block;"><b>Sales Code</b></td>
			<td style="display:inline-block;"><b>:</b></td>
			<td style="display:inline-block;">{{ $param['sales_data']['sales_code'] }}</td>
		</tr>
	</table>
	{{-- @endcomponent --}}
<div style="font-size:14px;">
Silahkan cek di aplikasi HRIS.
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
{{-- @endcomponent --}}