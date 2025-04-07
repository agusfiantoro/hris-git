<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Ganti 'fas fa-smile' dengan kelas ikon Font Awesome yang Anda inginkan -->

	<title>Interview Question</title>
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
    *, .sans {
        font-family: Arial, 'Helvetica', 'Tahoma', sans-serif;
    }
</style>
    <body style="color: black;padding: 0px;font-size: 12px;">
        <div>
            <span class="sans" style="font-size: 20pt;">Interview Question</span>

            <table>
                <thead>
                </thead>
                <tbody style="width:100%">
                    <tr>
                        <td style="width: 100px;">Name</td>
                        <td>: {{$name}}</td>
                    </tr>
                    <tr>
                        <td style="width: 100px;">Email</td>
                        <td>: {{$email}}</td>
                    </tr>
                    <tr>
                        <td style="width: 100px;">Mobile Phone</td>
                        <td>: {{$mobile_phone}}</td>
                    </tr>
                    <tr>
                        <td style="width: 100px;">Interest Position</td>
                        <td>: {{$dept}}</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <thead>
                </thead>
                <tbody style="width:100%">
                    {{-- Appl Job --}}
                    <tr style="">
                        <td><b>Applied Job</b></td>
                        <td></td>
                    </tr>
                    @foreach($applied as $appl) 
                        <tr>
                            <td style="width: 100px;">Job Position</td>
                            <td>: {{$appl['pos_req']}}</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Ref. Number</td>
                            <td>: {{$appl['reference_number']}}</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Branch</td>
                            <td>: {{$appl['branch']}}</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Date Applied</td>
                            <td>: {{$appl['applied_date']}}</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Recruitment Stage</td>
                            <td>: {{$appl['code_status']}}</td>
                        </tr>
                        <tr>
                            <td style="width: 100px;">Status</td>
                            <td>: {{$appl['status']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <b>Interview</b>
            <table class="">
                <thead>
                </thead>
                <tbody style="width:100%">
                    @foreach($hr_answer as $interview)
                    <tr class="">
                        <td style="width:210px" class="px-2 py-1">Interview Date</td>
                        <td style="width:470px" class="rounded px-2 py-1"><i>{{$interview['interview_date']}}</i></td>
                    </tr>
                    @endforeach
                    @foreach($questions as $group => $groupVal) 
                        <tr>
                            <td><b>{{ $group }}</b></td>
                        </tr>
                        @foreach($groupVal as $compKey => $compVal)
                            <b>{{ $compKey }}</b>
                            @foreach($compVal as $question)
                                <tr class="">
                                    <td style="width:210px" class="border px-2 py-1">{{$question['soal']}}</td>
                                    <td style="width:470px" class="border rounded px-2 py-1"><i>{!! $question['essay_answer'] ?? '<span class="text-danger">No answer</span>' !!}</i></td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>