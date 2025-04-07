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
			<span style="font-size: 25px;text-decoration: underline;font-weight: bold;">KEPUTUSAN</span>
			<br>
			<span style="font-weight: bold;">No: {{$data->reference_number}}</span>
			<p class="text mt-2" style="text-transform: uppercase;">
				<b><u>PIMPINAN PT. {{$data->company_name}}</u></b>
			</p>
		</div>
		<div class="row">
			<table style="width: 100%;">
				<tr>
					<td>MEMPERHATIKAN</td>
					<td>:</td>
					<td>Formulir Persetujuan Relokasi tanggal {{tgl_indo($data->date)}}</td>
				</tr>
				<tr>
					<td>MENIMBANG</td>
					<td>:</td>
					<td>
						<ol>
							<li>Adanya Formasi kerja / jabatan di bagian {{$data->dec_position_new}}</li>
							<li>
								Optimalisasi alokasi sumber daya manusia
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
				<tr align="center"><td colspan="3"><b>MEMUTUSKAN</b></td></tr>
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
								Saudara yang tersebut dibawah ini :
								<table style="width: 100%;">
									<tr>
										<td>a.</td>
										<td>Nama / NIK</td>
										<td>:</td>
										<td>{{$data->name}} / {{$data->nik_employee}}</td>
									</tr>
									<tr>
										<td>b.</td>
										<td>Jabatan</td>
										<td>:</td>
										<td>{{$data->dec_position_old}}</td>
									</tr>
									<tr>
										<td>c.</td>
										<td>Status</td>
										<td>:</td>
										<td>
											@if($data->status == "Permanent")
											Karyawan tetap
											@else
											{{$data->status}}
											@endif
										</td>
									</tr>
									<tr>
										<td>d.</td>
										<td>Fungsi</td>
										<td>:</td>
										<td>{{$data->dec_dept_old}}</td>
									</tr>
									<tr>
										<td>e.</td>
										<td>Divisi</td>
										<td>:</td>
										<td>
											{{$data->id_principal}}
										</td>
									</tr>
									<tr>
										<td>f.</td>
										<td>Cabang</td>
										<td>:</td>
										<td>{{$data->dec_branch_old}}</td>
									</tr>
									<tr>
										<td>g.</td>
										<td>Area</td>
										<td>:</td>
										<td>{{$data->remark_8}}</td>
									</tr>
									<tr>
										<td colspan="4">Terhitung mulai tanggal {{tgl_indo($data->effective_date)}} direlokasi ke :</td>
									</tr>
									<tr>
										<td>a.</td>
										<td>Jabatan</td>
										<td>:</td>
										<td>{{$data->dec_position_new}}</td>
									</tr>
									<tr>
										<td>b.</td>
										<td>Fungsi</td>
										<td>:</td>
										<td>{{$data->dec_dept_new}}</td>
									</tr>
									<tr>
										<td>c.</td>
										<td>Divisi</td>
										<td>:</td>
										<td>
											@foreach($principal as $key => $pcp)
											@if ($loop->first)
											{{$pcp->dec_division_new}}
											@else
											, {{$pcp->dec_division_new}}
											@endif
											@endforeach
										</td>
									</tr>
									<tr>
										<td>d.</td>
										<td>Cabang</td>
										<td>:</td>
										<td>{{$data->dec_branch_new}}</td>
									</tr>
									<tr>
										<td>e.</td>
										<td>Area</td>
										<td>:</td>
										<td>{{$data->remark_9}}</td>
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
				Demikian keputusan ini dikeluarkan untuk dapat dilaksanakan dengan sebaik – baiknya, apabila ternyata kemudian terdapat
				kekeliruan didalam keputusan ini, maka sewaktu – waktu dapat diadakan perubahan sebagaimana mestinya.
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
