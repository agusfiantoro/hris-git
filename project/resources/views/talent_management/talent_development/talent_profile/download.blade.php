<!DOCTYPE html>
<html lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">

th, td {
  padding: 5px;
  vertical-align:top;
}
body{
	font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
}
.badge {
  display: inline-block;
  padding: 6px;
  font-size: 10px;
  font-weight: 700;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: .25rem;
  transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
  color:white;
}
.badge-danger {
  background-color: #dc3545;
}
.badge-success {
  background-color: #28a745;
}
.page-avoid{
  page-break-inside: avoid;
}

.small-box {
  border-radius: .25rem;
  box-shadow: 0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);
  display: block;
  margin-bottom: 0px;
  position: relative;
}
.small-box > .small-box-footer {
  background: rgba(0,0,0,.1);
  color: rgba(255,255,255,.8);
  display: block;
  padding: 3px 0;
  position: relative;
  text-align: center;
  text-decoration: none;
}

.green {
	background-color : #54b556;
}
.yellow {
	background-color : #c4c106;
}
.blue {
	background-color : #007ca2;
}
.grey {
	background-color : #797978;;
}
.red {
	background-color : #ef5454;
}
@page { margin:30px 10px 15px 35px; }
</style>
<body style="color: black;padding: 0px;">

	<h3 align="center"><u>TALENT PROFILE CARD</u></h3><br>
	
	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;padding:2px;margin-bottom:10px;width:720px;">
		<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
			<b>GENERAL PROFILE</b>
		</div>
		<table border="0" style="font-size:11px;">
			<tr align="left" >
				<td rowspan="6"  align="center" style="width:150px;border:none;">@if($result['profile']->image_attachment)<img id="test" src="{{ $result['profile']->filePath }}/{{ $result['profile']->image_attachment }}" style="width:120px;border-radius:5px;" alt="User Avatar">@endif</td>
				<th style="width:70px;border:none;">Name / NIK</th>
				<td style="border:none;">:</td>
				<td style="width:170px;border:none;">{{ $result['profile']->name }}</td>
				<td style="border:none;"></td>
				<th style="width:70px;border:none;">Region</th>
				<td style="border:none;">:</td>
				<td style="width:150px;border:none;">{{ strtoupper($result['profile']->regional) }}</td>
			</tr>
			<tr align="left" >
				<th style="border:none;">Department</th>
				<td style="border:none;">:</td>
				<td style="border:none;">{{ strtoupper($result['profile']->department) }}</td>
				<td style="border:none;"></td>
				<th style="width:70px;border:none;">Branch</th>
				<td style="border:none;">:</td>
				<td style="width:120px;border:none;">{{ strtoupper($result['profile']->branch) }}</td>
			</tr>
			<tr align="left" >
				<th style="border:none;">Position</th>
				<td style="border:none;">:</td>
				<td style="border:none;">{{ strtoupper($result['profile']->position_routing) }}</td>
				<td style="border:none;"></td>
				<th style="width:70px;border:none;">Grade</th>
				<td style="border:none;">:</td>
				<td style="width:120px;border:none;">{{ strtoupper($result['profile']->job_grade) }}</td>
			</tr>
			<tr align="left" >
				<th style="border:none;">Employment Status</th>
				<td style="border:none;">:</td>
				<td style="border:none;">{{ strtoupper($result['profile']->employment_status) }}</td>
				<td style="border:none;"></td>
				<th style="width:70px;border:none;">Division</th>
				<td style="border:none;">:</td>
				<td style="width:120px;border:none;">{{ strtoupper($result['profile']->principal) }}</td>
			</tr>
			<tr align="left" >
				<th style="border:none;">Join Date</th>
				<td style="border:none;">:</td>
				<td style="border:none;">{{ strtoupper(date('d M Y',strtotime($result['profile']->join_date))) }}</td>
				<td style="border:none;"></td>
				<th style="width:70px;border:none;">Direct Spv</th>
				<td style="border:none;">:</td>
				<td style="width:120px;border:none;white-space:normal;">{{ $result['profile']->parent_emp_name }}</td>
			</tr>
			<tr align="left" >
				<th style="border:none;">Length of Service</th>
				<td style="border:none;">:</td>
				<td style="width:100px;border:none;">{{ $result['profile']->duration }}</td>
				<td style="border:none;"></td>
				<th style="width:70px;border:none;">Immediate Mgr</th>
				<td style="border:none;">:</td>
				<td style="width:120px;border:none;">{{ $result['profile']->indirect_emp_name }}</td>
			</tr>
		</table>
	</div>	
	
	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;padding:2px;margin-bottom:10px;width:720px;">
		<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
			<b>DEMOGRAPHICS</b>
		</div>
		<table border="0" style="font-size:10px;margin-bottom:5px;border-radius:5px 5px 0 0;">
			<tr align="center" >
				<td style="width:120px;border:none;">				
					<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;">
						<div style="background:#ededed;padding:5px 0 5px 0;"><b>Date of Birth</b></div>
						<div style="padding:10px 0 10px 0;">{{ strtoupper(date('d M Y',strtotime($result['profile']->birthdate))) }}</div>
					</div>
				</td>
				<td style="width:80px;border:none;">				
					<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;">
						<div style="background:#ededed;padding:5px 0 5px 0;"><b>Age</b></div>
						<div style="padding:10px 0 10px 0;">{{ $result['profile']->age }}</div>
					</div>
				</td>
				<td style="width:142px;border:none;">				
					<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;">
						<div style="background:#ededed;padding:5px 0 5px 0;"><b>Place of Birth</b></div>
						<div style="padding:10px 0 10px 0;">{{ strtoupper($result['profile']->place_of_birth) }}</div>
					</div>
				</td>
				<td style="width:100px;border:none;">				
					<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;">
						<div style="background:#ededed;padding:5px 0 5px 0;"><b>Marital Status</b></div>
						<div style="padding:10px 0 10px 0;">{{ strtoupper($result['profile']->marital) }}</div>
					</div>
				</td>
				<td style="width:100px;border:none;">				
					<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;">
						<div style="background:#ededed;padding:5px 0 5px 0;"><b>PTKP Status</b></div>
						<div style="padding:10px 0 10px 0;">{{ $result['profile']->ptkp_status }}</div>
					</div>
				</td>
				<td style="width:105px;border:none;">				
					<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;">
						<div style="background:#ededed;padding:5px 0 5px 0;"><b>Last Education</b></div>
						@if($result['profile']->edu_level != null)  
							<div style="padding:10px 0 10px 0;">{{ $result['profile']->edu_level }}</div>
						@else
							  <div style="padding:10px 0 10px 0;">-</div>      
						@endif
					</div>
				</td>
			</tr>
		</table>
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:0px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Education</b>
			</div>
			<?php if(count($result['edu']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Major</th>
					<th align="center" style="border:1px solid #ccc;width:150px;vertical-align:middle;">University/School</th>
					<th align="center" style="border:1px solid #ccc;width:121px;vertical-align:middle;">Level</th>
					<th align="center" style="border:1px solid #ccc;width:100px;vertical-align:middle;">City</th>
					<th align="center" style="border:1px solid #ccc;width:82px;vertical-align:middle;">Start Year</th>
					<th align="center" style="border:1px solid #ccc;width:82px;vertical-align:middle;">End Year</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['edu'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->major }}</td>
					<td style="border:1px solid #ccc;vertical-align:middle;">{{ $val->education_name }}</td>
					<td style="border:1px solid #ccc;vertical-align:middle;">{{ $val->level }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->education_city }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->start_year ? date('F Y',strtotime($val->start_year)) : "-" }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->end_year ? date('F Y',strtotime($val->end_year)) : "-" }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>	
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Achievement (Award)</b>
			</div>
			<?php if(count($result['award']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:128px;vertical-align:middle;">Award Name</th>
					<th align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;">Award Number</th>
					<th align="center" style="border:1px solid #ccc;width:112px;vertical-align:middle;">Certificate Number</th>
					<th align="center" style="border:1px solid #ccc;width:75px;vertical-align:middle;">Reference Date</th>
					<th align="center" style="border:1px solid #ccc;width:75px;vertical-align:middle;">Effective Date</th>
					<th align="center" style="border:1px solid #ccc;width:75px;vertical-align:middle;">Expired Date</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['award'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->remark }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->award_letter_number }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->award_certificate_number }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->reference_date != null)  
						{{ date('d M Y',strtotime($val->reference_date)) }}
					@else
						-
					@endif
					</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->effective_date != null)  
						{{ date('d M Y',strtotime($val->effective_date)) }}
					@else
						-
					@endif
					</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->expired_date != null)  
						{{ date('d M Y',strtotime($val->expired_date)) }}
					@else
						-
					@endif
					</td>
				</tr>
				<?php 
					$no++;
					}
				?>			
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
		
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Disciplinary Sanction</b>
			</div>
			<?php if(count($result['sp']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:200px;vertical-align:middle;">SP Number</th>
					<th align="center" style="border:1px solid #ccc;width:175px;vertical-align:middle;">SP Name</th>
					<th align="center" style="border:1px solid #ccc;width:119px;vertical-align:middle;">Effective Date</th>
					<th align="center" style="border:1px solid #ccc;width:119px;vertical-align:middle;">Expired Date</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['sp'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->sp_number }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->sp_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ date('d M Y',strtotime($val->effective_date)) }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ date('d M Y',strtotime($val->expired_date)) }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>	
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
	</div>

	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;padding:2px;margin-bottom:10px;width:720px;">
		<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
			<b>CAREER HISTORY</b>
		</div>
		<div style="padding:0 5px 2px 8px;">
			<div align="left" style="font-size:13px;padding:10px 0 5px 0;">
				<b>Career</b>
			</div>
			<?php if(count($result['career']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">				
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:5px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:71px;vertical-align:middle;">Career Number</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Career Transition</th>
					<th align="center" style="border:1px solid #ccc;width:60px;vertical-align:middle;">Transaction Type</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Employee Status</th>
					<th align="center" style="border:1px solid #ccc;width:76px;vertical-align:middle;">Position</th>
					<th align="center" style="border:1px solid #ccc;width:35px;vertical-align:middle;">Grade</th>
					<th align="center" style="border:1px solid #ccc;width:45px;vertical-align:middle;">Effective Date</th>
					<th align="center" style="border:1px solid #ccc;width:45px;vertical-align:middle;">Expired Date</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Duration</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Company</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['career'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->reference_number }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->transition_category }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->transaction_type }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->employment_status }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->position_routing }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->job_grade }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ date('d M Y',strtotime($val->effective_date)) }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->expired_date != null)  
						{{ date('d M Y',strtotime($val->expired_date)) }}
					@else
						-
					@endif
					</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->duration == '00:00:00')  
							-
						@else
							{{ $val->duration }}
						@endif				
					</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->company_name }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>		
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 				
		</div>
		
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Working Experience</b>
			</div>
			<?php if(count($result['working']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:199px;vertical-align:middle;">Company Name</th>
					<th align="center" style="border:1px solid #ccc;width:174px;vertical-align:middle;">Position</th>
					<th align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;">Period</th>
					<th align="center" style="border:1px solid #ccc;width:120px;vertical-align:middle;">City</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['working'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->company_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->position_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->period }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->company_city }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>	
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
	</div>

	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;margin-bottom:10px;padding:2px;width:720px;">
		<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#e8f8e3;border-radius:5px 5px 0 0;padding:5px;">
			<b>LEARNING HISTORY</b>
		</div>
		<div style="padding:0 5px 2px 8px;">
			<div align="left" style="font-size:13px;padding:10px 0 5px 0;">
				<b>Certification</b>
			</div>
			<?php if(count($result['cert']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">				
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:330px;vertical-align:middle;">Certification Name</th>
					<th align="center" style="border:1px solid #ccc;width:311px;vertical-align:middle;">Certified By</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['cert'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->certification_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->certified_by }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>		
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 				
		</div>
		
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Courses</b>
			</div>
			<?php if(count($result['training']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:171px;vertical-align:middle;">Program</th>
					<th align="center" style="border:1px solid #ccc;width:150px;vertical-align:middle;">Courses</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Score</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Status</th>
					<th align="center" style="border:1px solid #ccc;width:20px;vertical-align:middle;">Hit/Miss</th>
					<th align="center" style="border:1px solid #ccc;width:40px;vertical-align:middle;">Rating</th>
					<th align="center" style="border:1px solid #ccc;width:80px;vertical-align:middle;">Training Date</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['training'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->program_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->course_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->user_score }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->status == 'Pass')  
						<span class="badge badge-success">{{ $val->status }}</span>
					@else
						<span class="badge badge-danger">{{ $val->status }}</span>
					@endif
					
					</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->hit_miss }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->rating }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ date('d M Y H:i:s',strtotime($val->creation_date)) }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>	
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
	</div>
	
	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;margin-bottom:10px;padding:2px;width:720px;">
		<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
			<b>CAREER ASPIRATION</b>
		</div>
		<div style="padding:12px 5px 2px 8px;">
			<?php if(count($result['aspiration']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">				
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:325px;vertical-align:middle;">Question</th>
					<th align="center" style="border:1px solid #ccc;width:350px;vertical-align:middle;">Answer</th>
				</tr>
				<?php 
					$no = 1;
					$col = collect($result['aspiration'])->groupBy('period');
					foreach($col as $key=>$val){					
						foreach($val as $key_a=>$val_a){
					?>
					<?php
						if($key_a == 0){
					?>
					<tr>
						<th colspan="2" align="center" style="border:1px solid #ccc;vertical-align:middle;background:#fffcd5;">{{ $key }}</th>
					</tr>
					<?php	
						}
					?>
					<tr>			
						<td align="justify" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $val_a->question }}</td>
						<td align="justify" style="border:1px solid #ccc;vertical-align:middle;padding:8px;">{{ $val_a->description_answer }}</td>
					</tr>
					<?php 
						}
					?>					
				<?php 
					$no++;
					}
				?>		
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 				
		</div>
		
		<!-- div style="padding:12px 5px 2px 8px;">
			<?php if(count($result['aspiration']) > 0) {	
				$no = 1;
				$col = collect($result['aspiration'])->groupBy('period');
				foreach($col as $key=>$val){
			?>	
				<table border="0" style="font-size:12px;border:1px solid #ccc;" class="page-avoid">				
					<tr style="background:#ededed;">
						<th align="center" style="border:1px solid #ccc;width:80px;vertical-align:middle;">Period</th>
						<th align="center" style="border:1px solid #ccc;width:280px;vertical-align:middle;">Question</th>
						<th align="center" style="border:1px solid #ccc;width:300px;vertical-align:middle;">Answer</th>
					</tr>
				<?php
					foreach($val as $key_a=>$val_a){
				?>
					<tr>	
						<?php
						if($key_a == 0){
							?>
							<td rowspan="{{ count($val) }}" align="center" style="border:1px solid #ccc;vertical-align:middle;padding:10px;">{{ $key }}</td>
						<?php	
							}
						?>
						<td align="justify" style="border:1px solid #ccc;vertical-align:middle;padding:10px;">{{ $val_a->question }}</td>
						<td align="justify" style="border:1px solid #ccc;vertical-align:middle;padding:10px;">{{ $val_a->description_answer }}</td>
					</tr>
				<?php	
					}					
				?>
				
				</table>
			<?php
			}
			?>	
				
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 				
		</div -->
		
	</div>
	
	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;margin-bottom:10px;padding:2px;width:720px;">
		<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#d6f3cd;border-radius:5px 5px 0 0;padding:5px;">
			<b>TALENT INSIGHT</b>
		</div>
		<div style="padding:0 5px 2px 8px;">
			<div align="left" style="font-size:13px;padding:10px 0 5px 0;">
				<b>Talent History (Talent)</b>
			</div>
			<?php if(count($result['talent']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">				
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:100px;vertical-align:middle;">Projected Position</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Matrix Box</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Matrix Name</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Period</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">KPI Avg (%)</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Rating</th>
					<th align="center" style="border:1px solid #ccc;width:99px;vertical-align:middle;">Potencies</th>
					<th align="center" style="border:1px solid #ccc;width:99px;vertical-align:middle;">Competencies</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Eligibility Status</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['talent'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->pro_position }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->matrix_box }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->matrix_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->period }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->kpi_average }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->rating }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->potencies }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->competencies }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->eligible_status }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>		
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 				
		</div>
		
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Talent History (Successor)</b>
			</div>
			<?php if(count($result['successor']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="left" style="border:1px solid #ccc;width:100px;vertical-align:middle;">Projected Position</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Matrix Box</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Matrix Name</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Period</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">KPI Avg (%)</th>
					<th align="center" style="border:1px solid #ccc;width:30px;vertical-align:middle;">Rating</th>
					<th align="center" style="border:1px solid #ccc;width:99px;vertical-align:middle;">Potencies</th>
					<th align="center" style="border:1px solid #ccc;width:99px;vertical-align:middle;">Competencies</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Eligibility Status</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['successor'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->pro_position }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->matrix_box }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->matrix_name }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->period }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->kpi_average }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->rating }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->potencies }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->competencies }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->eligible_status }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>	
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
		
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<table border="0">
				<tr align="center" >
					<td style="width:350px;border:none;">				
						<div class="
						@if($result['profile']->box != null)
							@if($result['profile']->box == 'BOX 1')
								green
							@elseif($result['profile']->box == 'BOX 2' || $result['profile']->box == 'BOX 3' || $result['profile']->box == 'BOX 5')
								yellow
							@elseif($result['profile']->box == 'BOX 4' || $result['profile']->box == 'BOX 6' || $result['profile']->box == 'BOX 7')
								blue
							@elseif($result['profile']->box == 'BOX 8' || $result['profile']->box == 'BOX 9')
								red
							@else
								grey
							@endif
						@else
							grey
						@endif	
						small-box" style="color:white !important;">
							<span href="#" class="small-box-footer" style="font-size:16px;padding:5px;"><b>Current HAV Matrix</b></span>
							<div class="justify-content-center text-center" style="padding:15px;">
								@if($result['profile']->box != null)  
									<b style="font-size:16px;">{{ $result['profile']->box }}</b>
									<b style="font-size:14px;">({{ $result['profile']->desc_box }})</b>
								@else
									<b style="font-size:16px;">-</b>
								@endif					
							</div>
						</div>
					</td>
					<td style="width:330px;border:none;">
						<div class="small-box grey" style="color:white !important;">
							<span href="#" class="small-box-footer" style="font-size:16px;padding:5px;"><b>Readiness</b></span>
							<div class="justify-content-center text-center" style="padding:15px;">
								@if($result['profile']->readyness != null)  
									<b align="center" style="font-size:14px;">{{ $result['profile']->readyness }}</b>
								@else
									<b align="center" style="font-size:16px;">-</b>
								@endif	
							</div>
						</div>
					</td>
				</tr>
			</table>	
		</div>
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Performance</b>
			</div>
			<table border="0">
				<tr align="center" >
					<td style="width:222px;border:none;">				
						<div class="small-box green" style="color:white !important;">
							<span href="#" class="small-box-footer" style="font-size:14px;padding:5px;"><b>Annual Rating ({{ $result['rating'][0]->year }})</b></span>
							<div class="inner justify-content-center text-center" style="padding:5px;">
								<b align="center" style="font-size:16px;">
								{{ $result['rating'][0]->final_rating }}
								</b>
							</div>
						</div>
					</td>
					<td style="width:222px;border:none;">
						<div class="small-box grey" style="color:white !important;">
							<span href="#" class="small-box-footer" style="font-size:14px;padding:5px;"><b>Annual Rating ({{ $result['old_rating'][0]->year }})</b></span>
							<div class="inner justify-content-center text-center" style="padding:5px;">
								<b align="center" style="font-size:16px;">
								{{ $result['old_rating'][0]->final_rating }}
								</b>
							</div>
						</div>
					</td>
					<td style="width:222px;border:none;">
						<div class="small-box grey" style="color:white !important;">
							<span href="#" class="small-box-footer" style="font-size:14px;padding:5px;"><b>Annual Rating ({{ $result['old_rating'][1]->year }})</b></span>
							<div class="inner justify-content-center text-center" style="padding:5px;">
								<b align="center" style="font-size:16px;">
								{{ $result['old_rating'][1]->final_rating }}
								</b>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<?php if(count($result['kpi']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">
				<tr style="background:#ededed;">
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:90px;vertical-align:middle;">Period</th>
					<th rowspan="2" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">KPI Average (%)</th>
					<th colspan="12" align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Months (%)</th>
				</tr>
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Jan</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Feb</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Mar</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Apr</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Mei</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Jun</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Jul</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Ags</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Sep</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Okt</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Nov</th>
					<th align="center" style="border:1px solid #ccc;width:28px;vertical-align:middle;">Des</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['kpi'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->period }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->average_prosentase }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->jan }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->feb }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->mar }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->apr }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->mei }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->jun }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->jul }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->ags }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->sep }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->okt }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->nov }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->des }}</td>
				</tr>
				<?php 
					$no++;
					}
				?>	
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
		
	</div>
	
	<div style="border:1px solid #ccc;border-radius:5px 5px 0 0;margin-bottom:10px;padding:2px;width:720px;">
		<div align="left" style="font-size:14px;border:1px solid #ceeac5;background:#e8f8e3;border-radius:5px 5px 0 0;padding:5px;">
			<b>COMMITTEE NOTE</b>
		</div>
		<div style="padding:0 5px 2px 8px;">
			<div align="left" style="font-size:13px;padding:10px 0 5px 0;">
				<b>Flight Risk</b>
			</div>
			<?php if(count($result['flight_risk']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">				
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:135px;vertical-align:middle;">Projected Position</th>
					<th align="center" style="border:1px solid #ccc;width:80px;vertical-align:middle;">Category</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Committee Date</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Activity</th>
					<th align="center" style="border:1px solid #ccc;width:200px;vertical-align:middle;">Committee Note</th>
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">Submitted</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['flight_risk'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->pos_route }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->category }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ date('d M Y',strtotime($val->talent_commite_date)) }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->activity }}</td>
					<td align="justify" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->notes }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->is_submitted_flag == 1)  
						<span class="badge badge-success">Yes</span>
					@else
						<span class="badge badge-danger">No</span>
					@endif
					</td>
				</tr>
				<?php 
					$no++;
					}
				?>		
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 				
		</div>
		
		<div style="padding:0 5px 2px 8px;">
			<div style="border: 0.5px solid #ccc;margin:10px 0 10px 0;"></div>
			<div align="left" style="font-size:13px;padding-bottom:5px;">
				<b>Development Plan</b>
			</div>
			<?php if(count($result['talent_note']) > 0) {	?>	
			<table border="0" style="font-size:10px;border:1px solid #ccc;">				
				<tr style="background:#ededed;">
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">No.</th>
					<th align="center" style="border:1px solid #ccc;width:135px;vertical-align:middle;">Projected Position</th>
					<th align="center" style="border:1px solid #ccc;width:71px;vertical-align:middle;">Category</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Committee Date</th>
					<th align="center" style="border:1px solid #ccc;width:50px;vertical-align:middle;">Activity</th>
					<th align="center" style="border:1px solid #ccc;width:192px;vertical-align:middle;">Committee Note</th>
					<th align="center" style="border:1px solid #ccc;width:10px;vertical-align:middle;">Submitted</th>
				</tr>
				<?php 
					$no = 1;	
					foreach($result['talent_note'] as $key=>$val){	
				?>	
				<tr>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $no }}</td>
					<td align="left" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->pos_route }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->category }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ date('d M Y',strtotime($val->talent_commite_date)) }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->activity }}</td>
					<td align="justify" style="border:1px solid #ccc;vertical-align:middle;">{{ $val->notes }}</td>
					<td align="center" style="border:1px solid #ccc;vertical-align:middle;">
					@if($val->is_submitted_flag == 1)  
						<span class="badge badge-success">Yes</span>
					@else
						<span class="badge badge-danger">No</span>
					@endif
					</td>
				</tr>
				<?php 
					$no++;
					}
				?>		
			</table>
			<?php	} 
			else{
				echo '<i style="font-size:12px;">No Data</i>';
			}
			?> 	
		</div>
	</div>
	
</body>
</html>