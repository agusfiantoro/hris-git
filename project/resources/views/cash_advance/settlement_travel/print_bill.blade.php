<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Ganti 'fas fa-smile' dengan kelas ikon Font Awesome yang Anda inginkan -->

	<title>Surat Tagih {{$reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"> -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
	<!-- <link rel="stylesheet" href="{{ asset('project/storage/app/public/fonts/css/all.css') }}"> -->

</head>
<style type="text/css">
	#null_table{padding: 7px;}

    td, th {
        border: #000 solid 1px;
    }

    table.borderless>tbody>tr>td, table.borderless>thead>tr>th {
        border-color: rgba(0,0,0,0) !important;
    }

    td.give-gap {
        padding-right:10px !important;
    }
</style>
    <body style="color: black;padding: 0px;font-size: 12px;" class="mx-5 px-1 my-4">
        <div class="text-center col" style="font-family: 'Calibri', 'Helvetica', 'Noto Sans', sans-serif;">
            <span class="row font-weight-bold" style="font-size: 11pt;font-weight:400;">SURAT  TAGIH LAPORAN PERTANGGUNGJAWABAN PERJALANAN DINAS</span>
            <span class="row" style="font-size: 11pt;">{{ $reference_number }}</span>
        </div>
        <div class="mt-4">
            <table class="borderless">
                <tbody>
                    <tr>
                        <td>Tanggal Surat Tagih</td>
                        <td>: {{ \Carbon\Carbon::parse($start_refund_date)->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('j F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Nama/NIK</td>
                        <td>: {{ $name }} / {{ $nik_employee }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>: {{ $position }}</td>
                    </tr>
                    <tr>
                        <td>Ref. No. Surat Tugas</td>
                        <td>: {{ $reference_number }}</td>
                    </tr>
                    <tr>
                        <td>Nominal Sisa Perjalanan Dinas</td>
                        <td>: Rp{{ number_format($total_base_currency_difference_amount, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="my-3">
            <p>Mohon melakukan pengembalian sisa perjalanan dinas sesuai dengan nominal sisa perjalanan dinas pada <b><u>salah satu</u></b> rekening dibawah ini dengan <b><u>berita acara transfer nomor surat tugas perjalanan dinas.</u></b></p>
        </div>
        <div>
            <table class="text-center">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="width: 170px;">Nama Rekening</th>
                        <th style="width: 150px;">Nama Bank</th>
                        <th style="width: 170px;">Nomor Rekening</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bankAccount as $k => $bank)
                    <tr>
                        <td style="width:40px;">{{ $k+1 }}</td>
                        <td style="width: 170px;">{{ $bank->account_name }}</td>
                        <td style="width: 150px;">{{ $bank->bank_code }}</td>
                        <td style="width: 170px;">{{ $bank->account_number }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <p>
                Pengembalian sisa perjalanan dinas <u><b>WAJIB</b></u> dilakukan paling lambat <u><b>14 hari</b></u> setelah tanggal surat tagih perjalanan dinas ini diterbitkan. 
            </p>
        </div>
        <div>
            <p>
                <b>
                    Jika pengembalian dilakukan setelah 14 hari dari tanggal surat tagih, maka pengajuan kasbon perjalanan dinas baru tidak dapat disetujui untuk proses pembayaran dan pengembalian sisa perjalanan dinas akan dilakukan melalui pemotongan gaji karyawan pada bulan berikutnya.
                </b>
            </p>
        </div>
        <div class="mt-5 d-flex flex-column">
            <div>Terima kasih,</div>
            <div class="mt-5 pt-3">{{ implode(" / ", $financeName) }}</div>
        </div>
    </body>
</html>