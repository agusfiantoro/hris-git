<!doctype html>
@if(!empty($data))
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Internal Memo | {{$data->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body style="font-family: Times New Roman;font-size: 13px;color: black;">
	<!-- <div class="container"> -->
		<div class="row" style="margin-top: 7%;text-align: right;">
			<span style="font-size: 20px;font-weight: bold;text-decoration: underline;">INTERNAL MEMO</span>
			<br>
			<span style="font-size: 15px;">{{$data->reference_number}}</span>
		</div>
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
		<div class="row">
			<table style="width: 100%;" cellpadding="4" cellspacing="0">
				<tbody>
					<tr style="border: 1px solid;">
						<td>To</td>
						<td>:</td>
						<td>Sdr/i. {{$data->name}}</td>
					</tr>
					<tr style="border: 1px solid;">
						<td>From</td>
						<td>:</td>
						<td>HRD</td>
					</tr>
					<tr style="border: 1px solid;">
						<td>Cc</td>
						<td>:</td>
						<td>{{$data->remark_6}}</td>
					</tr>
					<tr style="border: 1px solid;">
						<td>Date</td>
						<td>:</td>
						<td>{{tgl_indo($data->date)}}</td>
					</tr>
					<tr style="border: 1px solid;">
						<td>Subject</td>
						<td>:</td>
						<td>Pemberitahuan Tidak Lulus Orientas Sdr/i. {{$data->name}}</td>
					</tr>
				</tbody>
			</table>
			<p>
				<br>
				Dengan Hormat,
			</p>
			<p>
				Berdasarkan hasil penilaian orientasi karyawan yang Anda berikan terhadap karyawan di bawah ini, maka bersama ini
				kami sampaikan bahwa :
			</p>
			<table style="width: 100%;">
				<tr>
					<td>Nama / NIK</td>
					<td>:</td>
					<td>{{$data->name}} / {{$data->nik_employee}}</td>
				</tr>
				<tr>
					<td>Orientasi di Jabatan</td>
					<td>:</td>
					<td>{{$data->dec_position}}</td>
				</tr>
				<tr>
					<td>PT</td>
					<td>:</td>
					<td>PT. {{$data->company_name}}</td>
				</tr>
				<tr>
					<td>Divisi</td>
					<td>:</td>
					<td>{{$data->dec_divisi}}</td>
				</tr>
				<tr>
					<td>Cabang/Area</td>
					<td>:</td>
					<td>{{$data->dec_branch}}/{{$data->dec_location}}</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="3">
						Dinyatakan TIDAK LULUS / GAGAL dalam masa orientasi
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="3">Karyawan yang bersangkutan selanjutnya kembali ke posisi berikut:</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Jabatan</td>
					<td>:</td>
					<td>{{$data->dec_position_new}}</td>
				</tr>
				<tr>
					<td>PT</td>
					<td>:</td>
					<td>PT. {{$data->company_name}}</td>
				</tr>
				<tr>
					<td>Divisi</td>
					<td>:</td>
					<td>
						@foreach($principal as $dvs)
						@if ($loop->first)
						{{$dvs->description}}
						@else
						, {{$dvs->description}}
						@endif
						@endforeach
					</td>
				</tr>
				<tr>
					<td>Cabang/Area</td>
					<td>:</td>
					<td>{{$data->dec_branch_new}}/{{$data->dec_location_new}}</td>
				</tr>
				<tr>
					<td>Efektif Per</td>
					<td>:</td>
					<td>
						@if($data->effective_date != NULL)
						{{tgl_indo($data->effective_date)}}
						@endif
					</td>
				</tr>
			</table>
			<p>
				<br>
				Demikian pemberitahuan dari kami. Atas perhatian dan kerjasamanya kami sampaikan terima kasih.
			</p>
			<p>
				<br>
				Menyetujui,
				<br>
				<br>
				<br>
				<br>
				<u>{{$data->employee_chief_name}}</u>
				<br>
				{{$data->employee_chief_position}}
			</p>
		</div>
		<!-- </div> -->
	</body>
	</html>
	@endif
