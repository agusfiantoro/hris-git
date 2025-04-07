<!doctype html>
@foreach($data as $dt)
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Surat Pemberitahuan | {{$dt->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body style="font-family: Times New Roman;color: black;font-size: 14px;">
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
	<!-- <div class="container"> -->
		<div class="row text-center" style="margin-top: 14%;">
			<span style="font-size: 25px;text-decoration: underline;font-weight: bold;">KEPUTUSAN</span>
			<br>
			<span style="font-weight: bold;">No: {{$dt->reference_number}}</span>
		</div>
		<div class="row" style="margin-top: 10%;">
			<table style="width: 100%;">
				<tr>
					<td>MEMPERHATIKAN</td>
					<td>:</td>
					<td>Program Pemagangan Dalam Negeri</td>
				</tr>
				<tr>
					<td>MENIMBANG</td>
					<td>:</td>
					<td>
						<ol>
							<li>Adanya kebutuhan di PT. <span style="text-transform: uppercase;">{{$dt->company_name}}</span> </li>
							<li>
								Optimalisasi alokasi sumber daya manusia
							</li>
							<li>
								Ketertiban administrasi
							</li>
						</ol>
					</td>
				</tr>
				<tr>
					<td>MENGINGAT</td>
					<td>:</td>
					<td>Peraturan Perusahaan dalam hal kepegawaian</td>
				</tr>
			</table>
			<p style="text-align: center;" class="text mt-5"><b>MEMUTUSKAN</b></p>
			<p>
				Melakukan pemagangan kepada Mahasiswa {{$dt->remark_3}} pada periode {{tgl_indo($dt->effective_date)}} – {{tgl_indo($dt->expired_date)}} di bawah ini:
			</p>
			<table style="width: 40%;margin-left: 6%;">
				<tr>
					<td>Nama</td>
					<td>:</td>
					<td>{{$dt->remark_1}}</td>
				</tr>
				<tr>
					<td>Jurusan</td>
					<td>:</td>
					<td>{{$dt->remark_4}}</td>
				</tr>
			</table>
			<p class="text mt-3">
				Demikian keputusan ini dikeluarkan untuk dapat dilaksanakan dengan sebaik-baiknya, apabila ternyata kemudian
				terdapat kekeliruan didalam keputusan ini, maka sewaktu-waktu dapat diadakan perubahan sebagaimana mestinya.
			</p>
		</div>
		<div class="row" style="margin-top: 10%;">
			<table>
				<tr>
					<td>Ditetapkan di</td>
					<td>:</td>
					<td>
						{{$dt->remark_5}}
					</td>
				</tr>
				<tr>
					<td>Pada Tanggal</td>
					<td>:</td>
					<td>{{tgl_indo($dt->date)}}</td>
				</tr>
				<tr>
					<td colspan="3">
						<hr style=" border-top: 1px dashed black;">
					</td>
				</tr>
			</table>
			<span>a/n Pimpinan<br>
				PT. {{$dt->company_name}},
				<br>
				<br>
				<br>
				<br>
				<br>
				<u>{{$dt->name_chief}}</u>
				<br>
				{{$dt->position_chief}}
			</span>
		</div>
		<!-- </div> -->
	</body>
	</html>
	@endforeach
