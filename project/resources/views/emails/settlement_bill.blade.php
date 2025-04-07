@component('mail::message')
<div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px;font-weight:bold;padding-bottom:5px;">
Dear, {{$param['content_title']}}
  </div>
  <div>
    <span>No. Referensi Cash Advance</span>
    <b>{{$param['reference_number']}}</b>
  </div>
 <div style="font-size:14px;">
  Mohon melakukan pengembalian sisa perjalanan dinas sesuai dengan nominal sisa perjalanan dinas pada
  salah satu rekening yang terlampir di Surat Tagihan Perjalanan Dinas anda. Batas waktu untuk melakukan 
 pembayaran adalah {{$param['to_date']}}. 
 <br>
 <b>
  Jika pengembalian dilakukan setelah 14 hari dari tanggal surat tagih, maka pengajuan kasbon perjalanan
dinas baru tidak dapat disetujui untuk proses pembayaran dan pengembalian sisa perjalanan dinas akan
dilakukan melalui pemotongan gaji karyawan pada bulan berikutnya.
 </b>
 </div>
<div style="font-size:14px;">
Anda dapat melihat surat tagih anda di web HRIS.
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