<!doctype html>
@foreach($data as $dt)
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Surat Pemberitahuan Berakhir | {{$dt->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body style="font-family: sans-serif;color: black;padding: 30px;font-size: 14px;">
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
	<div class="row" style="margin-top: 7%;">
		<span>{{$dt->remark_2}}, {{tgl_indo($dt->date)}}</span>
	</div>
	<div class="row ml-3 mt-4">
		<table style="width: 100%;">
			<tr>
				<td>No. </td>
				<td>:</td>
				<td>
					{{$dt->reference_number}}
				</td>
			</tr>
			<tr>
				<td>Perihal</td>
				<td>:</td>
				<td>
					{{$dt->category}}
				</td>
			</tr>
			<tr>
				<td>Lampiran</td>
				<td>:</td>
				<td>-</td>
			</tr>
		</table>
	</div>
	<div class="row mt-5">
		<p>
			Kepada Yth. <br>
			Sdr. {{$dt->name}} <br>
			{{$dt->remark_4}}
		</p>
	</div>
	<div class="row mt-4">
		<p class="text-justify">
			Dengan Hormat, <br>
			<span style="padding-left: 7%;">Sehubungan dengan berakhirnya Perjanjian Kerja Untuk Waktu Tertentu (PKWT) No.<b>{{$dt->remark_1}}</b> Tanggal 
				@if($dt->remark_3 != NULL)
				<b>{{tgl_indo($dt->remark_3)}}</b>, 
				@endif
				maka kami memberitahukan bahwa Perjanjian Kerja Untuk Waktu Tertentu akan berakhir jangka waktunya pada tanggal 
				@if($dt->expired_date != NULL) {{tgl_indo($dt->expired_date)}}@endif. Dengan berakhirnya PKWT pada tanggal tersebut, maka hubungan kerja antara saudara <b>{{$dt->name}}</b> Dengan PT. {{$dt->company_name}} <b><u>telah berakhir</u>.</b></span>
				<br>
				<span style="padding-left: 7%;">
					Demikian pemberitahuan ini kami sampaikan, atas kerjasamanya yang telah terjalin kami ucapkan terima kasih.
				</span>
			</p>
		</div>
		<div class="row mt-5">
			<p>
				<br>
				Hormat kami,
				<br>
				<br>
				<br>
				<br>
				<u><b>{{$dt->name_chief}}</b></u>
				<br>
				{{$dt->position_chief}}
			</p>
		</div>
	</body>
	</html>
	@endforeach
