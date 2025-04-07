@component('mail::message')
<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, {{$param['content_title']}}
  </div>
  <div>
    <span>No. Referensi Cash Advance</span>
    <b>{{$param['reference_number']}}</b>
  </div>
 <div style="font-size:14px;">
 Anda memiliki Cash Advance yang berstatus Not Clear. Mohon lakukan settlement untuk melakukan clearing. Batas waktu untuk melakukan 
 clearing adalah {{$param['to_date']}}. 
 <br>
 </div>
<div style="font-size:14px;">
Silahkan masuk ke web HRIS untuk melakukan settlement.
</div>
<br>
<div align="center" style="pdding-top:5px;font-size:16px;">
		 <a href="{{ $param['content_link'] }}" style='border-radius: 5px;background:#ff6f6f;color:#ffffff;font-family:Calibri, Helvetica neue, sans-serif;font-size:14px;font-weight:400;line-height:21px;margin:0;text-decoration:none;text-transform:none;padding:8px;' target='_blank'>
			Open HRIS
		</a>
	</div>
<br>
<div style="font-size:14px;">
Thanks,<br>
HRIS Team
</div>
@endcomponent