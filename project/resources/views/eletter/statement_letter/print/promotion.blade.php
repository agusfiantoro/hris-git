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
					<td style="width:150px;">MEMPERHATIKAN</td>
					<td>:</td>
					<td>&nbsp;Memo persetujuan dari pimpinan Sales perihal promosi karyawan</td>
				</tr>
				<tr>
					<td>MENIMBANG</td>
					<td>:</td>
					<td>&nbsp;1. Adanya formasi kerja / jabatan {{$data->dec_position_new}}
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td>&nbsp;2. Bahwa {{$data->name}} telah melampaui masa percobaan & orientasi di jabatan {{$data->dec_position_new}} dengan baik dan telah memenuhi persyaratan kerja yang telah
						ditentukan dan oleh karenanya dipandang mampu untuk menduduki jabatan tersebut</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td>&nbsp;3. Ketertiban administrasi.</td>
				</tr>
				<tr>
					<td>MENGINGAT</td>
					<td>:</td>
					<td>&nbsp;Peraturan Perusahaan dalam hal kepegawaian</td>
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
					<td style="vertical-align: top;">MENETAPKAN</td>
					<td style="vertical-align: top;">:</td>
					<td>
								&nbsp;1. Saudara yang tersebut dibawah ini :
								<table style="width: 90%; margin-left:24px;">
									<tr>
										<td style="vertical-align: top;">a.</td>
										<td style="vertical-align: top; width:100px;">Nama / NIK</td>
										<td style="vertical-align: top;">:</td>
										<td style="vertical-align: top;">{{$data->name}} / {{$data->nik_employee}}</td>
									</tr>
									<tr>
										<td>b.</td>
										<td>Jabatan</td>
										<td>:</td>
										<td>{{$data->dec_position_old}}</td>
									</tr>
									<tr>
										<td>c.</td>
										<td>Fungsi</td>
										<td>:</td>
										<td>{{$data->dec_dept_old}}</td>
									</tr>
									<tr>
										<td>d.</td>
										<td>Divisi</td>
										<td>:</td>
										<td>
											{{$data->id_principal}}
										</td>
									</tr>
									<tr>
										<td>e.</td>
										<td>Cabang</td>
										<td>:</td>
										<td>{{$data->dec_branch_old}}</td>
									</tr>
									<tr>
										<td>f.</td>
										<td>Area</td>
										<td>:</td>
										<td>{{$data->remark_8}}</td>
									</tr>
									<tr>
										<td colspan="4">Terhitung mulai tanggal {{tgl_indo($data->effective_date)}} dipromosikan ke :</td>
									</tr>
									<tr>
										<td>a.</td>
										<td>Jabatan</td>
										<td>:</td>
										<td>{{$data->dec_position_new}}</td>
									</tr>
									<tr>
										<td>b.</td>
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
										<td>c.</td>
										<td>Cabang</td>
										<td>:</td>
										<td>{{$data->dec_branch_new}}</td>
									</tr>
									<tr>
										<td>d.</td>
										<td>Area</td>
										<td>:</td>
										<td>{{$data->remark_9}}</td>
									</tr>
								</table>

					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td>&nbsp;2. Hak dan kewajiban lainnya diatur sesuai dengan Peraturan Perusahaan yang berlaku.</td>
				</tr>
			</table>
			<p style="margin-top:10px;">
				Demikian keputusan ini dikeluarkan untuk dapat dilaksanakan dengan sebaik-baiknya, apabila ternyata kemudian terdapat kekeliruan didalam keputusan ini, maka sewaktu- waktu dapat diadakan perubahan sebagaimana mestinya.
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
		<div class="row" style="font-size: 10px;margin-top:5px;">
			<br>
			<span><u>Tembusan diberikan kepada, yth :</u></span><br>
			1.	Compensation Dept.<br>
			2. Payroll <br>
			3.	(User)<br>
			4.	Arsip
		</div>
	</div>
</body>
</html>
@endif
