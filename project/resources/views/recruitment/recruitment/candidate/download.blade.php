<style type="text/css">
.bold {
	font-weight: bold;
}
.italic {
	font-style: italic;
}
.table {
  	color: #212529;
  	background-color: transparent;
}
.table thead th {
  	vertical-align: middle;
}

th, td {
  padding: 5px;
  vertical-align:top;
}
tr {
	vertical-align:middle;
}
</style>

<page pagegroup="new" backleft="3%" backright="5%" backtop="2%" backbottom="4%">
	<h3 align="center"><u>DATA CALON KARYAWAN</u></h3><br><br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">PERSONAL INFORMATION</th>
		</tr>
	</table>
	<table border="1">
		<tr>
			<th style="width:150px;border:none;">Name</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['name'] }}</td>
			<td align="center" rowspan="10" style="width:180px;vertical-align:middle;">@if($result['photo_candidate'])<img src="{{ $result['photo_candidate'] }}" style="height:140px;width:130px;" alt="User Avatar">@endif</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Email</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['email'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Mobile Phone</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['mobile_phone'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Gender</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['gender'] == 'M' ? 'Male' : 'Female'}}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Place / Date of Birth</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['place_of_birth'] }} / {{ date('d F Y',strtotime($result['date_of_birth'])) }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Religion</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['religion'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Current Address</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['address_home'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">KTP Address</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['idcard_address'] }}</td>
		</tr>		
		<tr>
			<th style="width:150px;border:none;">ID Number/KTP</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['identification_number'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Driving License/No. SIM</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['driving_license_number'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Emergency Contact</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['emergency_contact'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Emergency Phone</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['emergency_phone'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Marital Status</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['marital'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Tax Number/No. NPWP</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['taxpayer_identification_number'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Nationality</th>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $result['country'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;"></th>
			<td style="border:none;"></td>
			<td style="width:260px;border:none;"></td>
		</tr>
		<tr>
			<th style="width:150px;font-size:13px;border:none;">About Me</th>
			<td style="border:none;">:</td>
			<td align="justify" style="width:517.5px;border:none;" colspan="2">{{ $result['about_me'] }}</td>
		</tr>
		<tr>
			<th style="width:150px;font-size:13px;border:none;">Curriculum Vitae</th>
			<td style="border:none;">:</td>
			<td align="justify" style="width:517.5px;border:none;" colspan="2">
				<a href="{{ $result['cv_link'] }}" style="text-decoration:none;">Download</a>
			</td>
		</tr>
	</table>
	
	<br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">MEDIA SOCIAL</th>
		</tr>
	</table>
	<table border="1">
		<tr>
			<th style="width:100px;border:none;">Instagram</th>
			<td style="border:none;">:</td>
			<td style="width:150px;border:none;">{{ $result['link_instagram'] }}</td>
			<td style="width:51px;border:none;"></td>
			<th style="width:100px;border:none;">LinkedIn</th>
			<td style="border:none;">:</td>
			<td style="width:185px;border:none;">{{ $result['link_linkedin'] }}</td>
		</tr>
		<tr>
			<th style="width:100px;border:none;">Twitter</th>
			<td style="border:none;">:</td>
			<td style="width:150px;border:none;">{{ $result['link_twitter'] }}</td>
			<td style="width:51px;border:none;"></td>
			<th style="width:100px;border:none;">Facebook</th>
			<td style="border:none;">:</td>
			<td style="width:185px;border:none;">{{ $result['link_facebook'] }}</td>
		</tr>
	</table>
	
	<br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">LAST EDUCATION</th>
		</tr>	
	</table>
	<table border="1">
		<tr>
			<th style="width:150px;border:none;">Level</th>
			<td style="border:none;">:</td>
			<td style="width:226px;border:none;">{{ count($result['edu']) > 0 ? $result['edu'][0]['edu_level'] : ''}}</td>
			<td style="width:0px;border:none;"></td>
			<th style="width:65px;border:none;">From Date</th>
			<td style="border:none;">:</td>
			<td style="width:155px;border:none;">{{ count($result['edu']) > 0 ? date('d F Y',strtotime($result['edu'][0]['start_year'])) : '' }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">University/School Name</th>
			<td style="border:none;">:</td>
			<td style="width:226px;border:none;">{{ count($result['edu']) > 0 ? $result['edu'][0]['education_name'] : ''}}</td>
			<td style="width:0px;border:none;"></td>
			<th style="width:65px;border:none;">To Date</th>
			<td style="border:none;">:</td>
			<td style="width:155px;border:none;">{{ count($result['edu']) > 0 ? date('d F Y',strtotime($result['edu'][0]['end_year'])) : '' }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">Major</th>
			<td style="border:none;">:</td>
			<td style="width:226px;border:none;">{{ count($result['edu']) > 0 ? $result['edu'][0]['major'] : '' }}</td>
			<td style="width:0px;border:none;"></td>
			<th style="width:65px;border:none;">IPK/Scores</th>
			<td style="border:none;">:</td>
			<td style="width:155px;border:none;">{{ count($result['edu']) > 0 ? $result['edu'][0]['grade_point_average'] : '' }}</td>
		</tr>
		<tr>
			<th style="width:150px;border:none;">City</th>
			<td style="border:none;">:</td>
			<td style="width:226px;border:none;">{{ count($result['edu']) > 0 ? $result['edu'][0]['education_city'] : '' }}</td>
			<td style="width:0px;border:none;"></td>
			<th style="width:65px;border:none;"></th>
			<td style="border:none;"></td>
			<td style="width:120px;border:none;"></td>
		</tr>
	</table>
	
	<br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">FAMILY INFORMATION</th>
		</tr>
	</table>
	<?php if(count($result['family']) > 0) {	?>	
	<table border="1" style="font-size:11px;">
		<tr style="background:#eee;border:1px solid #000;">
			<th align="center" style="vertical-align:middle;">Relationship</th>
			<th align="center" style="width:80px;vertical-align:middle;">Name</th>
			<th align="center" style="vertical-align:middle;">Gender</th>
			<th align="center" style="vertical-align:middle;">Birthdate</th>
			<th align="center" style="width:70px;vertical-align:middle;">Mobile<br>Phone</th>
			<th align="center" style="width:100px;vertical-align:middle;">Address</th>
			<th align="center" style="width:50px;vertical-align:middle;">Last<br>Education</th>
			<th align="center" style="vertical-align:middle;">Working</th>
		</tr>
	<?php 
		foreach($result['family'] as $key=>$val){	
	?>	
		<tr>
			<td style="width:50px;">{{ $val->relationship }}</td>
			<td style="width:80px;">{{ $val->family_name }}</td>
			<td style="width:30px;">{{ $val->gender == 'M' ? 'Male' : 'Female'}}</td>
			<td style="width:85px;">{{ date('d F Y',strtotime($val->birthdate)) }}</td>
			<td style="width:70px;">{{ $val->mobile_phone }}</td>
			<td style="width:100px;">{{ $val->address_home }}</td>
			<td style="width:50px;">{{ $val->last_edu }}</td>
			<td style="width:50px;">{{ $val->current_works }}</td>
		</tr>
	<?php 
		}
	?>	
	</table>
	<?php	} ?> 
	
	<br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">WORK EXPERIENCE</th>
		</tr>
	</table>
	<?php if(count($result['ex']) > 0) {	?>	
	<table border="1" style="font-size:11px;">
		<tr style="background:#eee;border:1px solid #000;">
			<th align="center" style="width:100px;vertical-align:middle;">Job Position</th>
			<th align="center" style="width:86px;vertical-align:middle;">Company</th>
			<th align="center" style="width:80px;vertical-align:middle;">From Date</th>
			<th align="center" style="width:80px;vertical-align:middle;">To Date</th>
			<th align="center" style="width:135px;vertical-align:middle;">Reason Out</th>
			<th align="center" style="width:90px;vertical-align:middle;">Salary</th>
		</tr>
	<?php 
		foreach($result['ex'] as $key=>$val){	
	?>	
		<tr>
			<td style="width:100px;">{{ $val->position_name }}</td>
			<td style="width:86px;">{{ $val->company_name }}</td>
			<td style="width:80px;">{{ date('F Y',strtotime($val->start_year)) }}</td>
			<td style="width:80px;">{{ $val->end_year != null ? date('F Y',strtotime($val->end_year)) : 'Now' }}</td>
			<td style="width:135px;">{{ $val->reason_out }}</td>
			<td style="width:90px;">{{ $val->salary }},- {{ $val->salary_type == 'N' ? 'Nett' : 'Gross' }}</td>
		</tr>
	<?php 
		}
	?>	
	</table>
	<?php	} ?>
	
	<br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">SKILLS</th>
		</tr>
	</table>
	<?php if(count($result['skill']) > 0) {	?>	
	<table border="1" style="font-size:11px;">
		<tr style="background:#eee;border:1px solid #000;">
			<th align="center" style="width:470px;vertical-align:middle;">Skill Name</th>
			<th align="center" style="width:205px;vertical-align:middle;">Level</th>
		</tr>
	<?php 
		foreach($result['skill'] as $key=>$val){	
	?>	
		<tr>
			<td align="center" style="width:470px;">{{ $val->skill_name }}</td>
			<td align="center" style="width:205px;">
			<?php if($val->skill_level == 5){
					echo '5 (Expert)';
				}
				else if($val->skill_level == 4){
					echo '4 (Proficient)';
				}
				else if($val->skill_level == 3){
					echo '3 (Intermediate)';
				}
				else if($val->skill_level == 2){
					echo '2 (Advanced Beginner)';
				}
				else {
					echo '1 (Basic)';
				}
			?>
			</td>
		</tr>
	<?php 
		}
	?>	
	</table>
	<?php	} ?> 
	
	<br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">Training & Certification</th>
		</tr>
	</table>
	<?php if(count($result['cert']) > 0) {	?>	
	<table border="1" style="font-size:11px;">
		<tr style="background:#eee;border:1px solid #000;">
			<th align="center" style="width:268px;vertical-align:middle;">Certification Name</th>
			<th align="center" style="width:175px;vertical-align:middle;">Certified By</th>
			<th align="center" style="width:90px;vertical-align:middle;">Issued Year</th>
			<th align="center" style="width:90px;vertical-align:middle;">Validity Period</th>
		</tr>
	<?php 
		foreach($result['cert'] as $key=>$val){	
	?>	
		<tr>
			<td style="width:268px;">{{ $val->certification_name }}</td>
			<td align="center" style="width:175px;">{{ $val->certified_by }}</td>
			<td align="center" style="width:90px;">{{ $val->years_issued }}</td>
			<td align="center" style="width:90px;">{{ $val->validity_period }}</td>		
		</tr>
	<?php 
		}
	?>	
	</table>
	<?php	} ?> 
	
	<br>
	<table border="1" style="background:#fcdbd9;">
		<tr>
			<th align="center" style="font-size:13px;border:none;width:705px;">Other Information</th>
		</tr>
	</table>
	<table border="1">
		<tr>
			<td style="border:none;">1. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Berikan contoh sebuah masalah yang pernah Anda selesaikan. Jelaskan bagaimana reaksi Anda, Keputusan apa yang telah Anda buat, dan bagaimana cara Anda menyelesaikan masalah tersebut ?
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_1'] }}</i></div>		
			</td>
		</tr>
		<tr>
			<td style="border:none;">2. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Berikan contoh sebuah ide Anda yang telah terwujud serta jelaskan kesulitan apa yang Anda alami dan hasil apa yang telah Anda capai terkait ide tersebut !
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_2'] }}</i></div>
			</td>
		</tr>
		<tr>
			<td style="border:none;">3. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Apakah Anda bersedia ditempatkan di seluruh unit kerja (di luar tempat yang Anda tinggali saat ini) ?
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_3'] }}</i></div>
			</td>
		</tr>
		<tr>
			<td style="border:none;">4. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Apakah Anda pernah menderita sakit keras ? Jika pernah, sakit keras apa dan kapan ?
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_4'] }}</i></div>
			</td>
		</tr>
		<tr>
			<td style="border:none;">5. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Apakah Anda pernah melamar dalam kurun waktu 1 Tahun ini ? Jika pernah, mohon sebutkan waktunya, posisi yang dilamar dan tahapan seleksi terakhir !
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_5'] }}</i></div>
			</td>
		</tr>
		<tr>
			<td style="border:none;">6. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Darimana Anda mendapatkan informasi lowongan kerja ?
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_6'] }}</i></div>
			</td>
		</tr>
		<tr>
			<td style="border:none;">7. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Kapan Anda bisa mulai bekerja apabila diterima ?
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_7'] }}</i></div>
			</td>
		</tr>
		<tr>
			<td style="border:none;">8. </td>
			<td align="justify" style="border:none;width:682px;">
				<div>Berapa gaji yang anda harapkan ?
				</div>
				<br>
				<div style="border:1px solid #000;padding:8px;margin-top:-12px;"><i>{{ $result['info_8'] }}</i></div>
			</td>
		</tr>
	</table>
	<br>
	<br>
	<div align="justify" style="width:710px;font-size:14px;">
		<b>Saya menyatakan bahwa semua informasi yang saya sampaikan dalam dokumen ini adalah benar dan saya memahami sepenuhnya bahwa
	informasi yang salah atau tidak akurat akan mengakibatkan sanksi sesuai dengan Peraturan Perusahaan yang berlaku.
		</b>	
		<br>
		<br>
		<br>
		<?php 
			if($result['branch']->branch != 'Pusat'){
				echo $result['branch']->branch.", ".date('d/m/Y', strtotime($result['creation_date'])); 
			}
			else if($result['branch']->branch == 'Pusat' && $result['branch']->company_code != 'BCP'){
				echo "Jakarta, ".date('d/m/Y', strtotime($result['creation_date'])); 
			}
			else{
				echo "Sidoarjo, ".date('d/m/Y', strtotime($result['creation_date'])); 
			}
		?>
		<br>
		<br>
		<div style="width:150px;">
			<div align="center" style="padding-bottom:5px;">TTD,</div>
			<div align="center" style="width:150px;">
				<qrcode value="<?php echo $result['name']."\n".$result['identification_number']; ?>" style="border: none; width: 25mm;"></qrcode>
			</div>
		</div>
		<div style="padding-top:5px;margin-left:25px;">( <u><?php echo $result['name']; ?> </u> )	</div>
	</div>
	<page_footer style="text-align: center;">
	Copyright@BORWITA
    </page_footer>

</page>