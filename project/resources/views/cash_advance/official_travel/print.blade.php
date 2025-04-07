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
<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Ganti 'fas fa-smile' dengan kelas ikon Font Awesome yang Anda inginkan -->

	<title>{{$dt->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"> -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
	<!-- <link rel="stylesheet" href="{{ asset('project/storage/app/public/fonts/css/all.css') }}"> -->

</head>
<style type="text/css">
	#null_table{padding: 7px;}
</style>
<body style="color: black;padding: 0px;font-size: 12px;">
	
	<?php  
	$index = 0;
	foreach ($last_days as $last) {
		$index++;
		if ($index == $day_plus) {
			$last_date = $last->last_date;
		}
	}
	?>
	<div class="row" style="border: 1px solid;text-align: center;">
		<h3>SURAT TUGAS PERJALANAN DINAS</h3>
	</div>
	<div class="row">
		<table style="width: 100%;">
			<tr>
				<td>Tanggal Surat</td>
				<td>:</td>
				<td>{{tgl_indo($dt->letter_date)}}</td>
			</tr>
			<tr>
				<td>No. Surat (No/fungsi/bulan/th)</td>
				<td>:</td>
				<td>{{$dt->reference_number}}</td>
			</tr>
			<tr>
				<td>Ditugaskan kepada / NIK</td>
				<td>:</td>
				<td>{{$dt->name}} / {{$dt->nik_employee}}</td>
			</tr>
			<tr>
				<td>Divisi / Fungsi</td>
				<td>:</td>
				<td>{{$dt->dec_principal}} / {{$dt->dec_dept}}</td>
			</tr>
			<tr>
				<td>Jabatan</td>
				<td>:</td>
				<td>{{$dt->dec_position}}</td>
			</tr>
			<tr>
				<td>Tanggal tugas (dari...sd...)</td>
				<td>:</td>
				<td>{{tgl_indo(substr($dt->start_date,0,10))}} sd {{tgl_indo(substr($dt->end_date,0,10))}}</td>
			</tr>
			<tr>
				<td>Kota tujuan tugas</td>
				<td>:</td>
				<td>{{$dt->location_to}}</td>
			</tr>
		</table>
	</div>
	<div class="row">
		<b>Sasaran Tugas & Hasil yang Diharapkan :</b>
	</div>
	<div class="row" style="border: 1px solid;padding:10px;">
		<?php
		echo $dt->reason_notes;
		/*
		$array = explode(PHP_EOL, $dt->reason_notes);
		$reason = count($array);
		echo "<ol>";
		foreach($array as $item) {
			echo "<li>". $item . "</li>";
		}
		echo "</ol>";
		*/
		?>
	</div>
	<br>
	<div class="row mt-1">
		<b>1. Transportasi antar kota/negara : </b>
		<table style="width: 100%;padding: 0;margin: 0;" cellpadding="2" cellspacing="0" border="1">
			<tr style="background: #aaa;">
				<th>Jenis Transportasi</th>
				<th>Nama Transportasi</th>
				<th>Dari</th>
				<th>Ke</th>
				<th>Cabang</th>
				<th>Tanggal</th>
				<th>Waktu</th>
				<th>Harga</th>
			</tr>
			@if(count($transport)>0)
			<?php $nul_trans = 0; ?>
			@foreach($transport as $trans)
			<?php  
			$notes_trans = explode(";", $trans->notes);
			$desc_trans = explode(";", $trans->description);
			$subtotal = $nul_trans+=$trans->total_amount;
			?>
			<tr>
				<td>{{strtoupper($trans->dec_product)}}</td>
				<td>{{strtoupper($notes_trans[0])}}</td>
				<td>{{$notes_trans[1]}}</td>
				<td>{{$notes_trans[2]}}</td>
				<td>{{strtoupper($trans->branch_desc)}}</td>
				<td>{{tgl_indo($desc_trans[0])}}</td>
				<td>{{$desc_trans[1]}}</td>
				<td>Rp. {{number_format($trans->total_amount,0,",",".")}}</td>
				<!-- td>Rp. 0</td -->
			</tr>
			@endforeach
			@else
			<tr>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
			</tr>
			@endif
			<tr>
				<td colspan="7" align="right">Total Biaya</td>
				<td>Rp. 
					@if(!empty($subtotal))
					{{number_format($subtotal,0,",",".")}}
					@else
					0	
					@endif
				</td>
				<!-- td>Rp. 0</td -->
			</tr>
		</table>
	</div>
	<div class="row">
		<b>2. Akomodasi</b>
		<table style="width: 100%;padding: 0;margin: 0;" cellpadding="2" cellspacing="0" border="1">
			<tr style="background: #aaa;">
				<th>Kategori</th>
				<th>Nama Hotel/Kos</th>
				<th>Tanggal Awal</th>
				<th>Tanggal Akhir</th>
				<th>Lama Menginap</th>
				<th>Harga</th>
			</tr>
			@if(count($akomodasi)>0)
			<?php $nul_akomo = 0; ?>
			@foreach($akomodasi as $akomo)
			<?php  
			$notes_akomo = explode(";", $akomo->notes);
			$desc_akomo = explode("to", $akomo->description);
			$subtotal_akomo = $nul_akomo+=$akomo->total_amount;
			?>
			<tr>
				<td>{{strtoupper($akomo->dec_product)}}</td>
				<td>{{$notes_akomo[0]}}</td>
				<td>{{tgl_indo($desc_akomo[0])}}</td>
				<td>{{ tgl_indo(implode(" ", explode(";", $desc_akomo[1]))) }}</td>
				<td>{{$akomo->qty}} Night(s)</td>
				<td>Rp. {{number_format($akomo->total_amount,0,",",".")}}</td>
				<!-- td>Rp. 0</td -->
			</tr>
			@endforeach
			@else
			<tr>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
			</tr>
			@endif
			<tr>
				<td colspan="5" align="right">Total Biaya</td>
				<td>Rp. 
					@if(!empty($subtotal_akomo))
					{{number_format($subtotal_akomo,0,",",".")}}
					@else
					0
					@endif
				</td>
				<!-- td>Rp. 0</td -->
			</tr>
		</table>
	</div>
	<div class="row">
		<b>3. Lain-Lain (Uang Makan dll)</b>
		<table style="width: 100%;padding: 0;margin: 0;" cellpadding="2" cellspacing="0" border="1">
			<tr style="background: #aaa;">
				<th>Kategori</th>
				<th>Tanggal Awal</th>
				<th>Tanggal Akhir</th>
				<th>Region Tujuan</th>
				<th>Branch Tujuan</th>
				<th>Qty</th>
				<th>Notes</th>
				<th>Budget</th>
				<th>Total</th>
			</tr>
			@if(count($cashadvance)>0)
			<?php $nul_cash = 0; ?>
			@foreach($cashadvance as $cash)
			<?php  
			$notes_cash = explode(";", $cash->notes);
			$desc_cash = explode("to", $cash->description);
			$subtotal_cash = $nul_cash+=$cash->total_amount;
			?>
			<tr>
				<td>{{strtoupper($cash->dec_product)}}</td>
				<td>{{tgl_indo($desc_cash[0])}}</td>
				<td>{{tgl_indo(implode(" ", explode(";", $desc_cash[1]))) }}</td>
				<td>
					@if(!empty($notes_cash[1]))
					{{strtoupper($notes_cash[1])}}
					@endif
				</td>
				<td>
					@if(!empty($notes_cash[2]))
					{{strtoupper($notes_cash[2])}}
					@endif
				</td>
				<td>{{$cash->qty}}</td>
				<td>{{$notes_cash[0]}}</td>
				<td>Rp. {{number_format($cash->unit_price,0,",",".")}}</td>
				<td>Rp. {{number_format($cash->total_amount,0,",",".")}}</td>
			</tr>
			@endforeach
			@else
			<tr>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
				<td id="null_table"></td>
			</tr>
			@endif
			<tr>
				<td colspan="8" align="right">Total</td>
				<td>Rp. 
					@if(!empty($subtotal_cash))
					{{number_format($subtotal_cash,0,",",".")}}
					@else
					0
					@endif
				</td>
			</tr>
		</table>
	</div>
	<div class="row">
		<table style="width: 100%;margin-bottom: 20px;" class="mb-5">
			<tr>
				<td>Jumlah Kas Bon (Cash Advance)</td>
				<td>:</td>
				<td>Transportasi</td>
				<td>:</td>
				<td>
					Rp. 
					@if(!empty($subtotal))
					{{number_format($subtotal,0,",",".")}}
					@else
					0
					@endif
				</td>
				<!-- td>Rp. 0</td -->
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Akomodasi</td>
				<td>:</td>
				<td>
					Rp. 
					@if(!empty($subtotal_akomo))
					{{number_format($subtotal_akomo,0,",",".")}}
					@else
					0
					@endif
				</td>
				<!-- td>Rp. 0</td -->
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Lain-lain</td>
				<td>:</td>
				<td colspan="2" style="text-decoration: underline;">
					Rp. 
					@if(!empty($subtotal_cash))
					{{number_format($subtotal_cash,0,",",".")}}
					@else
					0
					@endif
					+
				</td>
			</tr>
			<tr>
				<td><b>TOTAL KAS BON (CASH ADVANCE)</b></td>
				<td></td>
				<td></td>
				<td>:</td>
				<td><b>Rp.
					<?php  
					if (!empty($subtotal)) {
						$subtotal = $subtotal;
					}else{
						$subtotal = 0;
					}
					if (!empty($subtotal_akomo)) {
						$subtotal_akomo = $subtotal_akomo;
					}else{
						$subtotal_akomo = 0;
					}
					if (!empty($subtotal_cash)) {
						$subtotal_cash = $subtotal_cash;
					}else{
						$subtotal_cash = 0;
					}
					?>
					{{number_format($subtotal+$subtotal_akomo+$subtotal_cash,0,",",".")}}
				<!-- 	{{number_format($subtotal_cash,0,",",".")}} -->
					</b>
				</td>
			</tr>
			<tr>
				<td>Penyelesaian Kas Bon paling lambat</td>
				<td>:</td>
				<td>
					@if(!empty($last_date))
					{{tgl_indo($last_date)}}
					@endif
				</td>
			</tr>
			<tr>
				<td>Hasil tugas dinas dilaporkan kepada</td>
				<td>:</td>
				<td>{{$dt->name_approval_request}}</td>
			</tr>
			<tr>
				<td>Paling Lambat</td>
				<td>:</td>
				<td>
					@if(!empty($last_date))
					{{tgl_indo($last_date)}}
					@endif
				</td>
			</tr>
			<tr>
				<td>Nama. Rekening Transfer</td>
				<td>:</td>
				<td>{{$dt->account_name}}</td>
			</tr>
			<tr>
				<td>Bank / No. Rekening Transfer</td>
				<td>:</td>
				<td>{{$dt->bank_name}} </td>
			</tr>
		</table>
		<br>
		<br>
		
		<div style="float: left;text-align: center;margin-left:40px;">
			Pembuat Perdin,
			<div style="padding:8px 0 8px 0;">
			<img src="data:image/png;base64, {!! $qrcode_employee !!}" width="80">
			<!-- img src="{{asset('project/storage/app/public/official_travel/approved_logo.jpg')}}" width="90" -->
			</div>
			<u>{{$dt->name}}</u>
		</div>
		<div style="float: right;text-align: center;margin-right:60px;">
			Menyetujui Perdin,
			<div style="padding:8px 0 8px 0;">
			<img src="data:image/png;base64, {!! $qrcode_approver !!}" width="80">
			<!-- img src="{{asset('project/storage/app/public/official_travel/approved_logo.jpg')}}" width="90" -->
			</div>
			<u>{{$dt->name_approval_request}}</u>
		</div>
	</div>
</body>
</html>