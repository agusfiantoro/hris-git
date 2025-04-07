<!doctype html>
@if(!empty($data))
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Surat Keterangan | {{$data->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body style="font-family: Bookman Old Style;color: black;font-size: 14px;">
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
		<div class="row text-center" style="margin-top: 7%;">
			<span style="font-size: 25px;text-decoration: underline;">KEPUTUSAN</span>
			<br>
			<span>No: {{$data->reference_number}}</span>
			<p class="text mt-2" style="text-transform: uppercase;">
				<b><u>PIMPINAN PT. {{$data->company_name}}</u></b>
			</p>
		</div>
		<div class="row">
			<table style="width: 100%;">
				<tr>
					<td>MEMPERHATIKAN</td>
					<td>:</td>
					<td>Formulir Rekomendasi/ Penilaian User</td>
				</tr>
				<tr>
					<td>MENIMBANG</td>
					<td>:</td>
					<td>
						<ol>
							<li>Adanya kebutuhan tenaga kerja tetap di fungsi {{$data->dec_dept_new}}</li>
							<li>
								Bahwa <b>Sdr. <span style="text-transform: uppercase;">{{$data->name}}</span></b> yang telah ditentukan dan oleh karenanya dipandang mampu untuk diangkat sebagai karyawan tetap.
							</li>
							<li>
								Ketertiban Administrasi
							</li>
						</ol>
					</td>
				</tr>
				<tr>
					<td>MENGINGAT</td>
					<td>:</td>
					<td>Peraturan Perusahaan dalam hal kepegawaian</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr align="center">
					<td colspan="3"><b>MEMUTUSKAN</b></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>MENETAPKAN</td>
					<td>:</td>
					<td>
						<ol>
							<li>
								Sdr. <span style="text-transform: uppercase;font-weight: bold;">{{$data->name}} ({{$data->nik_employee}})</span><br>terhitung mulai tanggal {{tgl_indo($data->effective_date)}}
								<br>Ditetapkan sebagai :
								<table style="width: 100%;">
									<tr>
										<td>a.</td>
										<td>Jabatan</td>
										<td>:</td>
										<td><b>{{$data->dec_position_new}}</b></td>
									</tr>
									<tr>
										<td>b.</td>
										<td>Status</td>
										<td>:</td>
										<td><b>
											@if($data->status == "Permanent")
											Karyawan tetap
											@else
											{{$data->status}}
											@endif
										</b></td>
									</tr>
									<tr>
										<td>c.</td>
										<td>PT</td>
										<td>:</td>
										<td><b>PT. {{$data->company_name}}</b></td>
									</tr>
									<tr>
										<td>d.</td>
										<td>Cabang</td>
										<td>:</td>
										<td><b style="text-transform: uppercase;">{{$data->dec_branch_new}}</b></td>
									</tr>
									<tr>
										<td>e.</td>
										<td>Area Kerja</td>
										<td>:</td>
										<td><b>{{$data->remark_9}}</b></td>
									</tr>
									<tr>
										<td>f.</td>
										<td>Divisi</td>
										<td>:</td>
										<td>
											@foreach($principal as $key => $pcp)
											@if ($loop->first)
											<b>{{$pcp->dec_division_new}}</b>
											@else
											, <b>{{$pcp->dec_division_new}}</b>
											@endif
											@endforeach
										</td>
									</tr>
									<tr>
										<td>g.</td>
										<td>Fungsi</td>
										<td>:</td>
										<td><b>{{$data->dec_dept_new}}</b></td>
									</tr>
								</table>
							</li>
							<li>
								Hak dan kewajiban lainnya diatur sesuai dengan Peraturan Perusahaan yang berlaku.
							</li>
						</ol>
					</td>
				</tr>
			</table>
			<p>
				Demikian keputusan ini dikeluarkan untuk dapat dilaksanakan dengan sebaik – baiknya, apabila ternyata kemudian terdapat kekeliruan didalam keputusan ini, maka sewaktu – waktu dapat diadakan perubahan sebagaimana mestinya.
			</p>
		</div>
		<div class="row">
			<table>
				<tr>
					<td>Ditetapkan di</td>
					<td>:</td>
					<td>
						{{$data->remark_1}}
					</td>
				</tr>
				<tr>
					<td>Pada Tanggal</td>
					<td>:</td>
					<td>{{tgl_indo($data->date)}}</td>
				</tr>
				<tr>
					<td colspan="3">
						<hr style=" border-top: 1px dashed black;">
					</td>
				</tr>
			</table>
			<span>a/n Pimpinan<br>
				PT. {{$data->company_name}},
				<br>
				<br>
				<br>
				<br>
				<b><u>{{$data->name_chief}}</u></b>
				<br>
				<i>{{$data->position_chief}}</i>
			</span>
		</div>
		<div class="row mt-1" style="font-size: 12px;">
			<br>
			<span><u>Tembusan diberikan kepada, yth :</u></span><br>
			1.	Compensation & Benefit / Payroll / Kepala Keuangan <br>
			2.	(User)<br>
			3.	Arsip
		</div>
	</div>
</body>
</html>
@endif
