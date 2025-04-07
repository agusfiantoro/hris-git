<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<!-- <img src="https://borwita.co.id/wp-content/uploads/logo-2019-PNG.png" style="width: 100%;height: 130px;"> -->
	<b>BORWITA</b>

	<p>Dear Bpk / Ibu,</p>

	<p>
		Berikut e-letter {{$details['detail_surat']}} / {{$details['code_surat']}} {{$details['category']}} - <span style="text-transform: uppercase;">{{$details['name']}}</span>.
		
		@if($details['name'] != NULL)
		Harap segera dicetak dan diberikan kepada karyawan yang bersangkutan. Sebagai bukti bahwa karyawan sudah menerima, karyawan diminta tanda tangan pada copy surat yang telah dicetak. <br>
		Surat yang sudah ditandatangani harap di-upload ke <a href="bit.ly/e-letter-borwita">bit.ly/e-letter-borwita</a> maksimal 7 hari dari email ini diterima.
		@endif

	</p>
	@if($details['type_surat'] != "Other Letter")
	Download Attachment e-letter : <a href="{{$details['url']}}">Click Here to Download e-letter</a><br>
	@endif
	<p>
		<br>
		Terima kasih <br>
		Human Resources Department <br>
		PT Borwita
	</p>
</body>
</html>