<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Print Asset Label</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"> -->
	{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> --}}
	{{-- <link rel="stylesheet" href="{{ asset('project/storage/app/public/fonts/css/all.css') }}"> --}}

</head>
<style type="text/css">
    @page {
        margin: 0;
    }
    body {
        margin: 0;
        padding-left: 2px;
    }
    td {
        margin: 0;
        padding: 1;
    }
</style>
<body>
    @foreach($assets as $k => $asset) 
        <div style="padding-top:0.35px;font-size:1.25pt;width:1cm;margin-top:2px;">
            <table>
                <tbody style="border: 0.1pt solid #000;padding:0;">
                    <tr style="padding:0;margin:0;">
                        <td style="border-right: 0.1pt solid #000;">
                            <img src="data:image/png;base64,{!! $asset->qr !!}" width="10cm">
                        </td>
                        <td style="width:6mm">
                            <b>{{ $asset->asset_number }}</b>
                            <div>{{ $asset->description }}</div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
        @if(key_exists($k+1, $assets->toArray()))
            <div style="page-break-after: always;height:0px;"></div>
        @endif
    @endforeach
</body>
</html>