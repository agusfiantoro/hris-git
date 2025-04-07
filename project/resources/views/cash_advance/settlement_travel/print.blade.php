<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Ganti 'fas fa-smile' dengan kelas ikon Font Awesome yang Anda inginkan -->

	<title>{{$official_travel[0]->reference_number}}</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"> -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
	<!-- <link rel="stylesheet" href="{{ asset('project/storage/app/public/fonts/css/all.css') }}"> -->

</head>
<style type="text/css">
	#null_table{padding: 7px;}

    td, th {
        border-color:#000 !important;
    }

    td.borderless {
        border-color: rgba(0,0,0,0) !important;
    }

    td.give-gap {
        padding-right:10px !important;
    }
</style>
    <body style="color: black;padding: 0px;font-size: 12px;">
        <div>
            <div class="p-1" style="border:1px solid #000;font-family:'Trebuchet MS', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <div>
                    <b>{{ $company->description }}</b>
                    <p style="padding:0px;margin:0px;" class="font-weight-bold">Laporan Pertanggungjawaban Perjalanan Dinas</p>
                    
                    <div class="text-sm">
                        <table class="">
                            <tbody>
                                <tr>
                                    <td class="borderless give-gap">NIK / Nama</td>
                                    <td class="borderless">:</td>
                                    <td class="borderless">{{ $official_travel[0]->name_employee }}</td>
                                </tr>
                                <tr>
                                    <td class="borderless give-gap">Tanggal</td>
                                    <td class="borderless">:</td>
                                    <td class="borderless">
                                        {{ Carbon\Carbon::parse($official_travel[0]->start_date)->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('j F Y') }}
                                        -
                                        {{ Carbon\Carbon::parse($official_travel[0]->end_date)->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('j F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="borderless give-gap">Tempat</td>
                                    <td class="borderless">:</td>
                                    <td class="borderless">{{ $official_travel[0]->location_to }}</td>
                                </tr>
                                <tr>
                                    <td class="borderless give-gap">Keperluan</td>
                                    <td class="borderless">:</td>
                                    <td class="borderless">{{ $official_travel[0]->reason_notes }}</td>
                                </tr>
                                <tr>
                                    <td class="borderless give-gap">Ref. No. Surat Tugas</td>
                                    <td class="borderless">:</td>
                                    <td class="borderless">{{ $official_travel[0]->reference_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div style="border:1px solid #000;width:50%;padding:3px;">
                No. Referensi: {{ $official_travel[0]->reference_number }}
            </div>
            <div>
                Diajukan oleh {{ $official_travel[0]->name_employee }}
            </div>
            <div>
                Disetujui oleh {{ $official_travel[0]->name_approval_request }}
            </div> --}}
        </div>
        <div>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr style="text-align: center;">
                        <th rowspan="2">Tanggal</th>
                        <th colspan="3" scope="colgroup">Keterangan</th>
                        <th rowspan="2">Rp</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr style="text-align: center">
                        <th scope="col">Uraian</th>
                        <th scope="col">Area</th>
                        <th scope="col">#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transport_accommodation as $key => $val) 
                        <tr style="background-color:#CCC">
                            <td></td>
                            <td colspan="5">
                                <b>{{ $key }}</b>
                            </td>
                        </tr>
                        @foreach($val as $sett)
                            <tr>
                                <td>{{ Carbon\Carbon::parse($sett->settlement_date)->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('j F Y') }}</td>
                                <td>{{ $sett->description }}</td>
                                <td>{{ $sett->desc_branch }}</td>
                                <td>{{ $sett->qty }}</td>
                                <td>{{ number_format($sett->approval_price_by_finance*$sett->currency_rate_settlement_expense, 0, ",", ".") }}</td>
                                <td>{{ number_format($sett->total_base_currency_amount, 0, ",", ".") }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    <?php $finance = ''; $sum = 0; ?>
                    
                    @foreach($settlement as $key => $val) 
                    <?php $sum += collect($val)->sum('print_settlement_price'); ?>
                        <tr style="background-color:#CCC">
                            <td></td>
                            <td colspan="5">
                                <b>{{ $key }}</b>
                            </td>
                        </tr>
                        @foreach($val as $sett)
                            <?php $finance = $sett->finance_approval; ?>
                            <tr>
                                <td>{{ Carbon\Carbon::parse($sett->settlement_date)->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('j F Y') }}</td>
                                <td>{{ $sett->description }}</td>
                                <td>{{ $sett->desc_branch }}</td>
                                <td>{{ $sett->qty }}</td>
                                <td>{{ number_format($sett->print_settlement_price, 0, ",", ".") }}</td>
                                <td>{{ number_format($sett->print_settlement_price*$sett->qty, 0, ",", ".") }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td></td>
                        <td colspan="4">TOTAL BIAYA PERJALANAN DINAS</td>
                        <?php 
                            $numfmt = new NumberFormatter('id', NumberFormatter::CURRENCY); 
                            $numfmt = str_replace("-", "&ndash; ", $numfmt->formatCurrency($sum_settlement->sum, 'IDR'));
                        ?>
                        <td>
                            <b>{!! $numfmt !!}</b>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4">BIAYA DIBAYAR OLEH PERUSAHAAN</td>
                        <?php 
                            $numfmt = new NumberFormatter('id', NumberFormatter::CURRENCY); 
                            $numfmt = str_replace("-", "&ndash; ", $numfmt->formatCurrency($official_travel[0]->total_travel_request_amount, 'IDR'));
                        ?>
                        <td><b>{!! $numfmt !!} </b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4">BIAYA DIBAYAR DENGAN CASH ADVANCE</td>
                        <?php 
                            $numfmt = new NumberFormatter('id', NumberFormatter::CURRENCY); 
                            $numfmt = str_replace("-", "&ndash; ", $numfmt->formatCurrency(abs($official_travel[0]->total_base_currency_payment_amount)*(-1), 'IDR'));
                        ?>
                        <td><b>{!! $numfmt !!}</b></td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td colspan="4">SELISIH LEBIH/(KURANG) BIAYA PERJALANAN DINAS</td>
                        <?php 
                            $numfmt = new NumberFormatter('id', NumberFormatter::CURRENCY); 
                            $numfmt = str_replace("-", "&ndash; ", $numfmt->formatCurrency($official_travel[0]->total_base_currency_difference_amount, 'IDR'));
                        ?>
                        <td><b>{!! $numfmt !!}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div>

        </div>

        <div>
            <span>SURABAYA, {{ strtoupper(now()->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('j F Y')) }}</span>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:center">Dibuat Oleh,</th>
                        <th style="text-align:center">Dicek Oleh,</th>
                        <th style="text-align:center">Disetujui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:center"><img src="data:image/png;base64,{!! $qr['employee'] !!}" width="80"></td>
                        <td style="text-align:center">@if($finance)<img src="data:image/png;base64,{!! $qr['finance'] !!}" width="80">@endif</td>
                        <td style="text-align:center"><img src="data:image/png;base64,{!! $qr['approval'] !!}" width="80"></td>
                    </tr>
                    <tr>
                        <td style="text-align:center">{{ $official_travel[0]->name_employee }}</td>
                        <td style="text-align:center">{{ $finance ?? '' }}</td>
                        <td style="text-align:center">{{ $official_travel[0]->name_approval_request }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:center"></td>
                        <td style="text-align:center">{{ $finance ? 'Kasir' : '' }}</td>
                        <td style="text-align:center">(Atasan Langsung)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <br>
        <div>
            <span>Catatan:</span>
            <p>Jika sudah dibayarkan dengan Kartu Kredit Korporasi maka pada kolom Uraian diberikan catatan sudah dibayarkan melalui CC Corporate</p>
        </div>
    </body>
</html>