<style type="text/css">
body{
	font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
}

</style>

<body style="color: black;">
	<div class="row" style="text-align: center;width:725px;">
		<h4>BERITA ACARA<br>REVIEW P2K</h4>
	</div>
	<br>	
	<div style="margin-bottom:10px;padding:2px;width:700px;font-size:12px;">
		<p align="justify" style="line-height: 1.5;text-align:justify;">
			Pada hari ini, {{ $result->text_date }} ({{ date('d-m-Y',strtotime($result->review_date)) }}) bertempat di kantor {{ $result->com_name }} Cabang {{ $result->branch }}, dibuat Berita Acara sebagai berikut  :
			<ol style="margin-top:-10px;line-height: 1.5;text-align:justify;">
				<li align="justify">
					Bahwa karyawan an. {{ $result->name }} (NIK: {{ $result->nik_employee }}) masuk dalam Program Perbaikan Kinerja (P2K) berdasarkan Berita Acara Penetapan P2K tanggal {{ \Carbon\Carbon::parse($result->ba_date)->translatedFormat('d F Y') }} yang dibuat oleh {{ $result->name_ba_create }}, selaku {{ $result->pos_ba_create }} tentang Penetapan Peserta Program Perbaikan Kinerja (P2K) Karyawan Departemen {{ $result->ba_dept }} Bulan {{ $result->remark_1 }}.
				</li>
				<li>
					Bahwa pada hari {{ $result->text_date }} ({{ date('d-m-Y',strtotime($result->review_date)) }}), telah dilakukan review P2K atas performance bulan {{ $result->remark_1 }}, kepada karyawan an. {{ $result->name }} (NIK: {{ $result->nik_employee }}). Dimana karyawan tersebut merupakan karyawan {{ $result->emp_status }} {{ $result->com_name }} Cabang {{ $result->branch }}.
				</li>
				<li>
					Bahwa berdasarkan hasil review Program Perbaikan Kinerja (P2K) yang telah dilakukan, didapatkan fakta bahwa karyawan an. {{ $result->name }} 
					<?php if($result->treatment == 'pass'){ ?>
					{{ $result->result_value == $result->target_volume ? "mencapai" : ($result->result_value > $result->target_volume ? "melebihi" : "") }} target kinerja <b>(HIT).</b>
					<?php } 
					else{ ?>
					tidak mencapai target kinerja 100% <b>(TIDAK HIT).</b> 
					<?php } ?>
					Dimana hasil review Program Perbaikan Kinerja (P2K) tercantum sebagai berikut:
					<table border="0" style="font-size:12px;border:1px solid #ccc;width:100%;margin-top:8px;margin-bottom:5px;">
						<thead>
							<tr style="background:#ededed;">
								<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">Measurements</th>
								<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">Target</th>
								<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">Actual</th>
								<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">Index</th>
								<th align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">Decision</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->department_code == '170_SAL' ? "Volume" : "KPI" }}</td>
								<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ number_format($result->target_volume) }}</td>
								<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ number_format($result->result_value) }}</td>
								<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->val_index }}%</td>
								<td align="center" style="border:1px solid #ccc;vertical-align:middle;padding:5px;">{{ $result->decision == 'HIT' ? "HIT" : "TIDAK HIT" }}</td>
							</tr>
						</tbody>
					</table>
				</li>
				<?php if($result->treatment == 'sp2' || $result->treatment == 'sp3'){ ?> 
				<li>
				Bahwa karyawan an. {{ $result->name }} telah mendapatkan Surat Peringatan Ke {{ $result->treatment == 'sp2' ? "1" : ($result->treatment == 'sp3' ? "2" : "") }} yang masih berlaku. 
				</li>
				<?php } 
				else if($result->treatment == 'phk' || $result->treatment == 'phkspdt'){ ?>	
				<li>
				Bahwa karyawan an. {{ $result->name }} telah mendapatkan Surat Peringatan {{ $result->treatment == 'phk' ? "Ke 3" : ($result->treatment == 'phkspdt' ? "Pertama dan Terakhir" : "") }}. 
				</li>
			<?php } ?>
				<li>
					Bahwa atas 
					<?php if($result->treatment == 'bulan1'){ ?>
						tidak tercapainya target kinerja 100% <b>(TIDAK HIT)</b> sebagaimana dijelaskan pada poin no. 3 (tiga) di atas, maka karyawan an. {{ $result->name }} wajib meningkatkan kinerjanya dan melanjutkan Program Perbaikan Kinerja (P2K) untuk jangka waktu yang telah ditetapkan.
					<?php } 					
					else if($result->treatment == 'sp1' || $result->treatment == 'sp2' || $result->treatment == 'sp3' || $result->treatment == 'phk' || $result->treatment == 'phkspdt'){ ?>
						tidak tercapainya target kinerja 100% <b>(TIDAK HIT)</b> sebagaimana dijelaskan pada poin no. 3 (tiga) di atas, maka karyawan an. {{ $result->name }} akan dikenakan sanksi sesuai Peraturan Perusahaan.
					<?php }
					else if($result->treatment == 'pass'){ ?>
						tercapainya target kinerja <b>(HIT)</b> sebagaimana dijelaskan pada poin no. 3 (tiga) di atas, maka karyawan an. {{ $result->name }} dinyatakan telah lolos program P2K.
					<?php } ?>				
					
				</li>
			</ol>
		</p>
		<p align="justify" style="line-height: 1.5;text-align:justify;">
			Demikian Berita Acara ini dibuat dengan sebenarnya, untuk dapat di pergunakan sebagaimana mestinya dan dapat dipertanggungjawabkan secara hukum.
		</p>
		<br>
		<br>
		<table border="0">
			<tr>
				<td align="left" colspan="4" style="vertical-align:middle;padding-bottom:15px;">{{ $result->review_city }}, {{ \Carbon\Carbon::parse($result->review_date)->translatedFormat('d F Y') }}</td>
			</tr>
			<tr>
				<td align="center" style="vertical-align:middle;width:250px;">Dibuat oleh,</td>
				<td style="width:80px;"></td>
				<td style="width:80px;"></td>
				<td align="center" style="vertical-align:middle;width:250px;">Mengetahui,</td>
			</tr>
			<tr>
				<td align="center" style="vertical-align:middle;">
					<img src="data:image/png;base64, {!! $result->acc_direct !!}" width="80">
				</td>
				<td>
				</td>
				<td>
				</td>
				<td align="center" style="vertical-align:middle;">
					<img src="data:image/png;base64, {!! $result->acc_indirect !!}" width="80">
				</td>
			</tr>
			<tr>
				<td align="center" style="width:120px;vertical-align:middle;">( <u>{{ $result->direct_name }}</u> )<br>{{ $result->direct_pos }}</td>
				<td>
				</td>
				<td>
				</td>
				<td align="center" style="width:120px;vertical-align:middle;">( <u>{{ $result->indirect_name }}</u> )<br>{{ $result->indirect_pos }}</td>
			</tr>
		</table>
	</div>
	
</body>