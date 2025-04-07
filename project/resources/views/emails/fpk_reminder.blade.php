@component('mail::message')
<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
 Berikut adalah daftar Karyawan yang telah mengajukan Form Permintaan Karyawan (FPK) : <br>
 </div>
 @component('mail::panel')
	{{ $param['permohonan'] }}
	<div style="width:850px;">
		<table width="100%" cellpadding="2" cellspacing="2" role="presentation"  style="font-size:14px;border-collapse: collapse;
		margin: 15px 0;
		font-family: sans-serif;
		box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);border: 1px solid #aaa;">
				<tr style="background-color: #ff6f6f;color: #ffffff;text-align: center;border: 1px solid #aaa;padding:3px;">
					<th style="border-right: 1px solid #aaa;padding:3px;">FPK Request Name</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Position Request</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Branch</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Total Request</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Created Date</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Notes</th>
					<th>Rec. Source</th>
				</tr>
			@foreach($param['list_child'] as $val_param)
			<tr style="border: 1px solid #aaa;padding:3px;">
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['emp_name'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;font-weight:bold;">{{ $val_param['pos_req'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['branch'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;font-weight:bold;text-align: center">{{ $val_param['count_req'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['created_date'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['reason_notes'] }}</td>
				<td>{{ $val_param['recruitment_source'] }}</td>
			</tr>
			@endforeach
		</table>
	</div>
	@endcomponent
<div style="font-size:14px;">
Silahkan masuk ke aplikasi MyBorwita untuk proses Approval.
</div>
<br>
<div align="center" style="pdding-top:5px;font-size:16px;">
		 <a href="{{ $param['content_link'] }}" style='border-radius: 5px;background:#ff6f6f;color:#ffffff;font-family:Calibri, Helvetica neue, sans-serif;font-size:14px;font-weight:400;line-height:21px;margin:0;text-decoration:none;text-transform:none;padding:8px;' target='_blank'>
			Click to Apps
		</a>
	</div>
<br>
<div style="font-size:14px;">
Thanks,<br>
HRIS Team
</div>
@endcomponent