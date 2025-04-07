<!doctype html>
@foreach($data as $dt)
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Surat Keterangan Kerja | {{$dt->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous"> -->
</head>
<body style="font-family: Times New Roman;color: black;padding: 45px;">
	<?php  
	function tgl_indo($tanggal){
		$bulan = array (
			1 =>   'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$pecahkan = explode('-', $tanggal);
		return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
	}
	?>
	@if($dt->remark_3 != NULL)
	@php
	$remark_3 =  explode(';', $dt->remark_3);
	$name = $remark_3[0];
	$dec_branch = $remark_3[1];
	$dec_position = $remark_3[2];
	$dec_dept = $remark_3[3];
	$dec_job_grade = $remark_3[4];
	$status = $remark_3[5];
	@endphp
	@else
	@php
	$name = $dt->name;
	$dec_branch = $dt->dec_branch;
	$dec_position = $dt->dec_position;
	$dec_dept = $dt->dec_dept;
	$dec_job_grade = $dt->dec_job_grade;
	$status = $dt->status;
	@endphp
	@endif
	<!-- <div class="container"> -->
		<div class="row text-center" style="font-weight: bold;margin-top: 8%;">
			<span style="font-size: 20px;text-decoration: underline;">
				@if($dt->category_code == 'PUB')
				SURAT KETERANGAN KERJA
				@else
				SURAT PENGALAMAN KERJA
				@endif
			</span><br>
			<span style="font-size: 15px;">{{$dt->reference_number}}</span>
		</div>
		<div class="row" style="margin-top: 10%;">
			<p>Yang bertanda tangan di bawah ini, kami atas nama Perusahaan <span style="text-transform: uppercase;">PT. {{$dt->company_name}}</span> menerangkan bahwa :</p>
		</div>
		<div class="row ml-3 mt-4">
			<table style="width: 100%;">
				<tr>
					<th>Nama / NIK</th>
					<td>:</td>
					<td>
						@if($dt->remark_3 == NULL)
						{{$dt->name}} / {{$dt->nik_employee}}
						@else
						{{$name}}
						@endif
					</td>
				</tr>
				<tr>
					<th>Status</th>
					<td>:</td>
					<td>
						@if($dt->status == "Contract")
						Karyawan Kontrak
						@elseif($dt->status == "Permanent" || $dt->status == "Acting")
						Karyawan Tetap
						@else
						{{$status}}
						@endif
					</td>
				</tr>
				<tr>
					<th>Jabatan</th>
					<td>:</td>
					<td>{{$dec_position}}</td>
				</tr>
				<tr>
					<th>Divisi</th>
					<td>:</td>
					<td>{{$dt->id_principal}}</td>
				</tr>
				<tr>
					<th>Fungsi</th>
					<td>:</td>
					<td>{{$dec_dept}}</td>
				</tr>
				<tr>
					<th>Cabang</th>
					<td>:</td>
					<td>{{$dec_branch}}</td>
				</tr>
			</table>
		</div>
		<div class="row mt-3">
			<p>
				adalah karyawan kami yang telah bekerja sejak <b>{{tgl_indo($dt->effective_date)}}</b> sampai dengan 
				@if($dt->category_code == "PUB")
				sekarang.
				@else
				<b>{{tgl_indo($dt->expired_date)}}.</b>
				@endif
			</p>
			@if($dt->category_code == 'REGR')
			<br>
			<p>untuk itu kami atas nama perusahaan mengucapkan terima kasih atas dedikasi dan kinerja yang telah diberikan selama ini.</p>
			@endif
			<p>
				<br>
				Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat digunakan
				@if($dt->category_code == 'PUB')
				sebagai {{$dt->notes}}.
				@else
				sebagaimana mestinya.
				@endif
			</p>
			<p>
				<br>
				{{$dt->remark_2}}, {{tgl_indo($dt->date)}}<br>
				Hormat kami,
			</p>
		</div>
		<div class="row">
			<p>
				<br>
				<br>
				<br>
				<u>{{$dt->name_chief}}</u>
				<br>
				{{$dt->position_chief}}
			</p>
			<p style="font-size: 12px;">
				<br>
				cc. File
			</p>
		</div>
		<!-- </div> -->
	</body>
	</html>
	@endforeach
