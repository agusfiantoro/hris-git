<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Ganti 'fas fa-smile' dengan kelas ikon Font Awesome yang Anda inginkan -->

	<title>{{$data->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"> -->
	{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> --}}
	{{-- <link rel="stylesheet" href="{{ asset('project/storage/app/public/fonts/css/all.css') }}"> --}}

</head>
<style type="text/css">
	#null_table{padding: 7px;}

    .title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    td, th, thead {
        border-color:#000 !important;
        border-width: 1px;
        border: solid;
    }

    td.borderless {
        border-color: rgba(0,0,0,0) !important;
    }

    td.give-gap {
        padding-right:10px !important;
    }

    body {
        margin-left: 1rem;
        margin-right: 1rem;
    }
</style>
<body style="font-family:'Trebuchet MS', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="">
        <div style="width:100vw">
            <div class="d-flex justify-content-center mx-auto" style="width:100vw;margin:auto">
                <div class="title fw-bold" align="center"><b>FORM SERAH TERIMA ASSET</b></div>
            </div>
        </div>
        <div style="font-size: 0.75rem;width:50vw;">
            <table class="table table-sm">
                <tbody>
                    <tr width="100vw">
                        <td class="borderless"><b>Reference Number<b></td>
                        <td class="borderless">: {{ $data->reference_number }}</td>
                        <td class="borderless"><b>Request Date<b></td>
                        <td class="borderless">: {{ $data->request_date }}</td>
                    </tr>
                    <tr>
                        <td class="borderless"><b>Request By<b></td>
                        <td class="borderless">: {{ $data->request_by->name }}</td>
                        <td class="borderless"><b>Need Date<b></td>
                        <td class="borderless">: {{ $data->need_date }}</td>
                    </tr>
                    <tr>
                        <td class="borderless"><b>Job Position<b></td>
                        <td class="borderless">: {{ $data->request_by->position }}</td>
                        <td class="borderless"><b>Approval Status<b></td>
                        <td class="borderless">: {{ $data->document_status->description }}</td>
                    </tr>
                    <tr>
                        <td class="borderless"><b>Note<b></td>
                        <td class="borderless">: {{ $data->description }}</td>
                        <td class="borderless"><b>Company<b></td>
                        <td class="borderless">: {{ $data->company_name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <table class="table table-sm" style="font-size:0.75rem;border:solid 1px #000;">
                <thead>
                    <tr>
                        <th>Asset Number</th>
                        <th>Asset Description</th>
                        <th>Qty</th>
                        <th>Employee Name</th>
                        <th>Branch</th>
                        <th>Location</th>
                        <th>Received Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->transfer_details as $detail)
                    <?php 
                        $detail->asset = $detail->asset;
                        $detail->employee = $detail->destinationEmployee; 
                        $detail->branch = $detail->destinationBranch;
                        $detail->location = $detail->destinationLocation;
                        $employeeRecipient = $detail->employee;
                    ?>
                    <tr>
                        <td>{{ $detail->asset->asset_number }}</td>
                        <td>{{ $detail->asset->description }}</td>
                        <td>{{ $detail->unit_assigned }}</td>
                        <td>{{ $detail->employee->name }}</td>
                        <td>{{ $detail->branch->description }}</td>
                        <td>{{ $detail->location->description }}</td>
                        <td>{{ $detail->effective_date }}</td>
                        <td>
                            {{ $detail->received_flag === TRUE ? "Received" : "" }}
                            {{ $detail->received_flag === FALSE ? "Refused" : "" }}
                            {{ $detail->received_flag === NULL ? "Pending" : "" }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <table class="table" style="font-size:0.75rem;">
                <tbody>
                    <tr>
                        <td class="borderless" align="center">Yang Menyerahkan</td>
                        <td class="borderless" align="center">Yang Menerima</td>
                    </tr>
                    <tr>
                        <td class="borderless" align="center">
                            <img src="data:image/png;base64,{!! base64_encode($approvalQr) !!}" width="80">
                        </td>
                        <td class="borderless" align="center">
                            <img src="data:image/png;base64,{!! base64_encode($employeeRecipient->qr) !!}" width="80">
                        </td>
                    </tr>
                    <tr>
                        <td class="borderless" align="center">{{ $data->name_approval }}</td>
                        <td class="borderless" align="center">{{ $employeeRecipient->name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <p style="font-size:0.7rem">
                Note: Jika ada kerusakan dalam asset yang digunakan dibawah 4 tahun dari tahun pembelian dikarenakan kelalaian pengguna asset berdasarkan bukti dan validasi dari atasan, maka biaya perbaikan akan di tanggung oleh penanggung jawab asset
            </p>
        </div>
    </div>
</body>
</html>