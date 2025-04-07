<!doctype html>
@if(!empty($data))
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Surat Panggilan | {{$data->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body style="font-family: Times New Roman;color: black;font-size: 13px;">
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
	<div class="container-fluid">
		<div class="row" style="margin-top: 15%;">
			<table style="width: 100%;">
				<tr>
					<td>No</td>
					<td>:</td>
					<td>{{$data->reference_number}}</td>
				</tr>
				<tr>
					<td>Perihal</td>
					<td>:</td>
					<td>
						@if($data->category_code == 'SUPA1')
						Surat Panggilan Pertama
						@elseif($data->category_code == 'SUPA2')
						Surat Panggilan Kedua
						@endif
					</td>
				</tr>
			</table>
		</div>
		<div class="row mt-5">
			Kepada Yth,
			<table style="width: 100%;">
				<tr>
					<td>Sdr/i. <span style="text-transform: uppercase;">{{$data->name}}</span></td>
				</tr>
				<tr>
					<td>
						{{$data->remark_3}}<br>{{$data->remark_6}}
					</td>
				</tr>
			</table>
		</div>
		<div class="row mt-5">
			<span>Dengan Hormat,<br><br>
				Sehubungan dengan ketidakhadiran / mangkir tanpa keterangan secara tertulis yang dilengkapi dengan bukti yang sah sejak
				tanggal {{tgl_indo($data->effective_date)}} sampai dengan {{tgl_indo($data->expired_date)}}, maka Anda Kami panggil untuk masuk bekerja pada :
			</span>
			<br>
			<br>
			<table style="width: 100%;margin-left: 5%;">
				<tr>
					<td>Hari / Tanggal</td>
					<td>:</td>
					<td>{{$hari}} / {{tgl_indo($tanggal_panggil)}}</td>
				</tr>
				<tr>
					<td>Tempat</td>
					<td>:</td>
					<td>{{ $tempat }}</td>
				</tr>
				<tr>
					<td>Waktu</td>
					<td>:</td>
					<td>{{$waktu}} {{$zona}}</td>
				</tr>
				<tr>
					<td>Agenda</td>
					<td>:</td>
					<td>Menghadap HRD dan masuk bekerja</td>
				</tr>
			</table>
			<br>
			Demikian surat panggilan ini disampaikan, atas perhatiannya Kami ucapkan terimakasih.
		</div>
		<div class="row mt-5">
			{{$data->remark_8}}, {{tgl_indo($data->date)}}<br>
			<span style="text-transform: uppercase;">PT. {{$data->company_name}}</span>
		</div>
		<div class="row">
			<br>
			<br>
			<br>
			<br>
			<span style="text-transform: uppercase;">
				<u>{{$data->nama_pengirim}}</u><br>{{$data->jabatan_pengirim}}
			</span>
			<p class="text mt-5">
				Cc. arsip.
			</p>
		</div>
	</div>
</body>
</html>
@endif
