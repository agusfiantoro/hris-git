<style type="text/css">
body{
	font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
}

</style>

<body style="color: black;">
	<div class="row" style="text-align: center;width:725px;">
		<h4>BERITA ACARA<br>PENETAPAN PESERTA<br>PROGRAM PERBAIKAN KINERJA (P2K)<br>
		{{ ($result->dept_code != '170_SAL' || $result->dept_code == '170_SAL') && $result->remark_6 == 'corporate' ? "KARYAWAN" : 
		(($result->dept_code != '170_SAL' || $result->dept_code == '170_SAL') &&  $result->remark_6 == 'os' ? "KARYAWAN ALIH DAYA" :"-") }}
		</h4>
	</div>
	<br>	
	<div style="margin-bottom:10px;padding:2px;width:710px;font-size:12px;">
		<div>
			<table border="0" style="font-size:12px;border:1px solid #ccc;">
				<tr>
				<?php if($result->dept_code == '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){ ?>	
					<th align="left" style="background:#ededed;border:1px solid #ccc;width:160px;vertical-align:middle;padding:5px;">Departemen / Divisi</th>
					<td align="left" style="border:1px solid #ccc;width:520px;vertical-align:middle;padding:5px;"> {{ $result->dept }} / {{ $result->division }}</td>
				<?php } 
					else if($result->dept_code != '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){
				?>	
					<th align="left" style="background:#ededed;border:1px solid #ccc;width:160px;vertical-align:middle;padding:5px;">Departemen</th>
					<td align="left" style="border:1px solid #ccc;width:520px;vertical-align:middle;padding:5px;"> {{ $result->dept }}</td>
				<?php
					}
				?>						
				</tr>
				<tr>
					<th align="left" style="background:#ededed;border:1px solid #ccc;vertical-align:middle;padding:5px;">P2K Bulan</th>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:5px;"> {{ $result->remark_1 }}</td>
				</tr>
				<tr>
					<th align="left" style="background:#ededed;border:1px solid #ccc;vertical-align:middle;padding:5px;">Atas performance (P3M)</th>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;padding:5px;"> {{ $result->remark_2 }}</td>
				</tr>
			</table>
		</div>
		<br>
		<p align="justify">
			Pada hari ini, {{ $result->remark_4 }} ({{ date('d-m-Y',strtotime($result->effective_date)) }}) yang bertanda tangan di bawah ini: 		
		</p>
		<div>
			<table border="0" style="font-size:12px;">
				<tr>
					<th align="left" style="width:80px;vertical-align:middle;">Nama</th>
					<th align="center" style="width:10px;vertical-align:middle;">:</th>
					<td align="left" style="width:630px;vertical-align:middle;"> {{ $result->emp_name }}</td>
				</tr>
				<tr>
					<th align="left" style="vertical-align:middle;">NIK</th>
					<th align="center" style="vertical-align:middle;">:</th>
					<td align="left" style="vertical-align:middle;"> {{ $result->nik_employee }}</td>
				</tr>
				<tr>
					<th align="left" style="vertical-align:middle;">Jabatan</th>
					<th align="center" style="vertical-align:middle;">:</th>
					<td align="left" style="vertical-align:middle;"> {{ $result->position }}</td>
				</tr>
				<tr>
					<th align="left" style="vertical-align:middle;">Divisi</th>
					<th align="center" style="vertical-align:middle;">:</th>
					<td align="left" style="vertical-align:middle;"> {{ $result->principal }}</td>
				</tr>
				<tr>
					<th align="left" style="vertical-align:middle;">Region</th>
					<th align="center" style="vertical-align:middle;">:</th>
					<td align="left" style="vertical-align:middle;"> {{ $result->region }}</td>
				</tr>
				
			</table>
		</div>
		<p align="justify">
			{{ ($result->dept_code != '170_SAL' || $result->dept_code == '170_SAL') && $result->remark_6 == 'corporate' ? "mengingat dan mempertimbangkan Peraturan Perusahaan $result->com_name yang berlaku, maka dengan ini menetapkan dalam lampiran  berupa:" : ($result->dept_code == '170_SAL' &&  $result->remark_6 == 'os' ? "maka dengan ini menetapkan dalam lampiran  berupa:" :"-") }}
			<?php if($result->dept_code == '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){ ?>
			<ol style="margin-top:-10px;">
				<li align="justify">
					Daftar nama karyawan yang termasuk dalam Program Perbaikan Kinerja (P2K) departemen {{ $result->dept }}, divisi {{ $result->division }}, Bulan {{ $result->remark_1 }} dengan kriteria sebagai berikut:
					<ol style="list-style-type:lower-alpha;">
						<li>
							AP3M KPI terendah di dalam grup populasinya, dan
						</li>
						<li>
							<i>kumulatif sales offtake</i> 3 bulan: {{ $result->remark_2 }} &lt; 93%
						</li>
					</ol>
				</li>
				<li>
					Target sales offtake yang harus dicapai oleh karyawan yang termasuk dalam Program Perbaikan Kinerja (P2K).
				</li>
			</ol>
			<?php } 
			else if($result->dept_code != '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os')){
			?>
			<ol style="margin-top:-10px;">
				<li align="justify">
					Daftar nama karyawan yang termasuk dalam Program Perbaikan Kinerja (P2K) departemen {{ $result->dept }}, Bulan {{ $result->remark_1 }} dengan kriteria sebagai berikut:
					<ol style="list-style-type:lower-alpha;">
						<li>
							<i>average score KPI</i> (AP3M KPI) terendah di dalam grup populasinya, dan
						</li>
						<li>
							<i>average score KPI</i> (AP3M KPI) kurang dari <i>threshold</i> (batas minimum) yang ditetapkan;
						</li>
					</ol>
				</li>
				<li>
					Target Score KPI yang harus dicapai oleh karyawan yang termasuk dalam Program Perbaikan Kinerja (P2K).
				</li>
			</ol>
			<?php } ?>
		</p>
		<p align="justify">
		<?php if(($result->dept_code != '170_SAL' || $result->dept_code == '170_SAL') && $result->remark_6 == 'corporate'){ ?>
			Bagi karyawan yang tercantum dalam daftar tersebut wajib mencapai {{ $result->dept_code == '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os') ? "target sales offtake" : ($result->dept_code != '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os') ? "target score KPI" :"-") }} yang ditetapkan sebagai kriteria lulus program P2K.<br><br>
			Apabila karyawan mencapai target tersebut, maka P2K berakhir. Apabila karyawan tidak mencapai target tersebut, maka P2K tetap berlanjut sesuai dengan ketentuan yang ditetapkan oleh Perusahaan.<br><br>			
		<?php } 
		else if(($result->dept_code != '170_SAL' || $result->dept_code == '170_SAL') &&  $result->remark_6 == 'os'){
		?>
			Mohon selanjutnya PIC dari rekanan alih daya untuk:
			<ol style="margin-top:-10px;">
				<li>
					membuatkan Berita Acara Penetapan Peserta Program Perbaikan Kinerja (P2K) yang serupa bagi karyawannya.
				</li>
				<li>
					Memberikan informasi kepada karyawan yang tercantum dalam daftar tersebut wajib mencapai target komitmen 100%. Apabila karyawan hit target komitmen, maka P2K berakhir. Apabila karyawan tidak hit target komitmen, maka karyawan tetap melanjutkan program sesuai dengan ketentuan program.
				</li>
			</ol>
		<?php } ?>		
		Demikian Berita Acara ini dibuat untuk dilaksanakan oleh seluruh jajaran departemen {{ $result->dept }}, 
			{{ $result->dept_code == '170_SAL' && ($result->remark_6 == 'corporate' || $result->remark_6 == 'os') ? "divisi $result->division" : ''
			}} sebagaimana mestinya.<br><br>
		Terima kasih.
		</p>
		<br>
		<br>
		<div style="width:500px;">
		{{ $result->position }}<br>
		{{ $result->com_name }}<br>
		<div style="padding:10px 0 10px 25px;">
			<img src="data:image/png;base64, {!! $result->acc !!}" width="80">
		</div>
		( {{ $result->emp_name }} )
		</div>
	</div>
	
</body>