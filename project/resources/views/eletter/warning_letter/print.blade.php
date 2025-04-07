<!doctype html>
@foreach($data as $dt)
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Surat Peringatan | {{$dt->reference_number}}</title>
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
	if ($dt->code_category == "SP2") {
		$sp_old = App\Models\Eletter\MasterEletter\ElectronicLetter::join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->where('hr_electronic_letter.id_employee',$dt->id_employee)
		->where('master_general_data.code','SP1')
		->where('hr_electronic_letter.status','A')
		->where('hr_electronic_letter.id_company',session('id_company'))
		->orderBy('hr_electronic_letter.id_letter','DESC')
		->first();
	}elseif($dt->code_category == "SP3"){
		$sp_old = App\Models\Eletter\MasterEletter\ElectronicLetter::join('master_general_data','master_general_data.id_general_data','=','hr_electronic_letter.id_category')
		->where('hr_electronic_letter.id_employee',$dt->id_employee)
		->where('master_general_data.code','SP2')
		->where('hr_electronic_letter.status','A')
		->where('hr_electronic_letter.id_company',session('id_company'))
		->orderBy('hr_electronic_letter.id_letter','DESC')
		->first();
	}
	?>
	<div class="container">
		<div class="row" style="margin-top: 14%;">
			<table style="width: 100%;">
				<tr>
					<td>Nomor</td>
					<td>:</td>
					<td>{{$dt->reference_number}}</td>
				</tr>
				<tr>
					<td>Perihal</td>
					<td>:</td>
					<td><b>
						@if($dt->code_category == "SP1")
						Surat Peringatan Pertama
						@elseif($dt->code_category == "SP2")
						Surat Peringatan Kedua
						@elseif($dt->code_category == "SP3")
						Surat Peringatan Ketiga
						@elseif($dt->code_category == "SPDT")
						SURAT PERINGATAN PERTAMA DAN TERAKHIR
						@endif
					</b></td>
				</tr>
			</table>
		</div>
		<div class="row mt-5">
			Kepada Yth,
			<table style="width: 100%;">
				<tr>
					<td>Sdr/i.</td>
					<td>:</td>
					<td>{{$dt->name}} ({{$dt->nik_employee}})</td>
				</tr>
				<tr>
					<td>Bagian</td>
					<td>:</td>
					<td>{{$dt->dec_position}}</td>
				</tr>
				<tr>
					<td>Cabang</td>
					<td>:</td>
					<td>{{$dt->dec_branch}}</td>
				</tr>
			</table>
		</div>
		<div class="row mt-5">
			<span>Dengan Hormat,<br>
				Sehubungan dengan kesalahan/pelanggaran disiplin kerja yang telah Saudara/i lakukan sebagai berikut :
			</span>
			<p class="text mt-4" align="justify">
				<?php
				$array = explode(PHP_EOL, $dt->remark_3);
				$total = count($array);
				foreach($array as $item) {
					echo "<span>".$item."<span><br>";
				}
				?>
			</p>
			<p class="text mt-4" align="justify">
				@if($dt->code_category == "SP1")
				<?php
				$array = explode(PHP_EOL, $dt->remark_4);
				$total = count($array);
				foreach($array as $item) {
					echo "<span>".$item."<span><br>";
				}
				?>
				@elseif($dt->code_category == "SP2" OR $dt->code_category == "SP3" OR $dt->code_category == "SPDT")
				Berdasarkan {{$dt->remark_4}}
				@endif
				@if($dt->code_category == "SP2")
				<br>
				<span>
					Saudara sebelumnya telah mendapatkan Surat Peringatan Pertama pada tanggal {{tgl_indo($sp_old->effective_date)}}
				</span>
				@elseif($dt->code_category == "SP3")
				<br>
				<span>
					Saudara sebelumnya telah mendapatkan Surat Peringatan Kedua pada tanggal {{tgl_indo($sp_old->effective_date)}}
				</span>
				@endif
			</p>
			<p class="text mt-4" align="justify">
				Maka Saudara diberi sanksi berupa :
				<ul>
					@if($dt->code_category == "SP1")
					<li>Pemberian Surat Peringatan Pertama</li>
					@elseif($dt->code_category == "SP2")
					<li>Pemberian Surat Peringatan Kedua</li>
					@elseif($dt->code_category == "SP3")
					<li>Pemberian Surat Peringatan Ketiga</li>
					@elseif($dt->code_category == "SPDT")
					<li>Surat Peringatan Pertama dan Terakhir</li>
					@endif
					<li>Terhitung dari {{tgl_indo($dt->effective_date)}} sampai dengan {{tgl_indo($dt->expired_date)}}</li>
				</ul>
			</p>
			<p class="text mt-4" align="justify">
				@if($dt->code_category == "SP1" OR $dt->code_category == "SP2")
				Dengan adanya surat peringatan ini diharapkan Saudara menyadari kesalahan Saudara serta berusaha
				memperbaikinya serta tidak akan mengulangi/membuat kesalahan lainnya. Apabila Saudara melakukan pelanggaran
				kembali dalam tenggang waktu masa berlakunya surat peringatan ini maka perusahaan akan mengeluarkan sanksi
				tingkat lanjutan.
				@elseif($dt->code_category == "SP3")
				Dengan adanya surat peringatan ini diharapkan Saudara menyadari kesalahan Saudara serta berusaha
				memperbaikinya serta tidak akan mengulangi/membuat kesalahan lainnya. Apabila Saudara melakukan pelanggaran
				kembali dalam tenggang waktu masa berlakunya surat peringatan ini maka perusahaan akan melakukan Pemutusan
				Hubungan Kerja
				@endif
			</p>
		</div>
		<div class="row mt-3">
			<p>
				{{$dt->remark_5}}, {{tgl_indo($dt->date)}}<br>
				{{$dt->company_name}}
			</p>
			<p>
				<br>
				<br>
				<br>
				<u>{{$dt->name_chief}}</u>
				<br>
				{{$dt->position_chief}}
			</p>
		</div>
	</div>
</body>
</html>
@endforeach
