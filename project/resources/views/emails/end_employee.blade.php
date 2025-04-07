@component('mail::message')
<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, {{$param['content_title']}}
  </div>
 <div style="font-size:14px;">
 Berikut adalah daftar karyawan yang akan segera ataupun telah berakhir masa 
@if($param['employment_type'] == 'Acting')
	actingnya
@elseif($param['employment_type'] == 'Probation')
	probationnya
@else
	kepegawaiannya
@endif
: <br>
 </div>
 @component('mail::panel')
	{{ $param['permohonan'] }}
	<div style="width:850px;">
		<table width="100%" cellpadding="2" cellspacing="2" role="presentation"  style="font-size:14px;border-collapse: collapse;
		margin: 15px 0;
		font-family: sans-serif;
		box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);border: 1px solid #aaa;">
				<tr style="background-color: #ff6f6f;color: #ffffff;text-align: center;border: 1px solid #aaa;padding:3px;">
					<th style="border-right: 1px solid #aaa;padding:3px;">Employee Name</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">NIK</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Employment Status</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Join Date</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">End Date</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Position</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Principal</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Department</th>
					<th style="border-right: 1px solid #aaa;padding:3px;">Regional</th>
					<th>Branch</th>
				</tr>
			@foreach($param['list_child'] as $val_param)
			<tr style="border: 1px solid #aaa;padding:3px;">
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['emp_name'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['nik_employee'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['emp_status'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['join_date'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['expired_date'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['position'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['principal'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['department'] }}</td>
				<td style="border-right: 1px solid #aaa;padding:3px;">{{ $val_param['regional'] }}</td>
				<td>{{ $val_param['branch'] }}</td>
			</tr>
			@endforeach
		</table>
	</div>
	@endcomponent
<div style="font-size:14px;">
Mohon segera melakukan performance review terhadap karyawan tersebut. 
Dan, silakan melakukan koordinasi secara langsung ke tim HR di area kerja Anda untuk tindak lanjut berikutnya.
</div>
<br>
<br>
<div style="font-size:14px;">
Thanks,<br>
Corporate HR Dept.
</div>
@endcomponent