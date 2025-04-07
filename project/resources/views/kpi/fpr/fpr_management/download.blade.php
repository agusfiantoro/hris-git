<style type="text/css">
.row {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  margin-right: -7.5px;
  margin-left: -7.5px;
}
.col-sm-3 {
  -ms-flex: 0 0 25%;
  flex: 0 0 25%;
  width: 25%;
}
.col-sm-6 {
  -ms-flex: 0 0 50%;
  flex: 0 0 50%;
  width: 50%;
}
.col-9 {
  -ms-flex: 0 0 75%;
  flex: 0 0 75%;
  max-width: 75%;
}
.bold {
	font-weight: bold;
}
.italic {
	font-style: italic;
}
.table {
  	width: 100%;
  	margin-bottom: 5px;
  	color: #212529;
  	background-color: transparent;
}
.table thead th {
  	vertical-align: bottom;
  	border-bottom: 1px solid #dee2e6;
}

.border-answer-description {
	border:1px solid;padding:8px;width:98%;margin-left:20px;
}
th, td {
  padding: 5px;
  vertical-align:top;
}
tr {
	padding: 0.75rem;
	vertical-align:top;
}
.kpi.table td{
		padding:0.5rem;
		vertical-align: middle;
	}
.quali.table td{
	padding:0.5rem;
	vertical-align: middle;
}
th.text-middle{
		vertical-align:middle;
		text-align:center;
	}
td.text-middle{
		vertical-align:middle;
		text-align:center;
	}
td.text-name{
		vertical-align:middle;
		text-align:left;
	}

</style>

<page pagegroup="new" backleft="2%" backright="10%" backtop="2%" backbottom="4%">
<div style="line-height: 1.5;font-size:13px;">
	<h3 align="center">PERFORMANCE REVIEW {{ strtoupper($notes) }}</h3><br><br>
	<table>
		<tr>
			<td style="width:150px;border:none;">NIK KARYAWAN</td>
			<td style="border:none;">:</td>
			<td style="width:280px;border:none;">{{ $emp['nik_employee'] }}</td>
			<td style="width:200px;border:none;">JABATAN / DEPARTEMEN</td>
			<td style="border:none;">:</td>
			<td style="width:260px;border:none;">{{ $emp['jabatan'] }} / {{ $emp['dept'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;border:none;">NAMA KARYAWAN</td>
			<td style="border:none;">:</td>
			<td style="width:280px;border:none;">{{ $emp['name'] }}</td>
			<td style="width:200px;border:none;">CABANG / REGIONAL</td>
			<td style="border:none;">:</td>
			<td style="border:none;">{{ $emp['branch'] }} / {{ $emp['region'] }}</td>
		</tr>
		<tr>
			<td style="width:150px;border:none;">ATASAN LANGSUNG</td>
			<td style="border:none;">:</td>
			<td style="width:280px;border:none;">{{ $emp['atasan'] }}</td>
			<td style="width:200px;border:none;">TANGGAL REVIEW</td>
			<td style="border:none;">:</td>
			<td style="border:none;">{{ $emp['review_date'] }}</td>
		</tr>
		
		<?php 
			if($notes == '(Periodically)'){
		?>
		<tr>
			<td style="width:150px;border:none;">PERIODE REVIEW</td>
			<td style="border:none;">:</td>
			<td style="width:280px;border:none;">{{ $period }}</td>
			<td style="width:200px;border:none;"></td>
			<td style="border:none;"></td>
			<td style="border:none;"></td>
		</tr>
		<?php 
			}
		?>
		</table>

	<br><hr><br>

	<?php
		$view = '';
		if($notes == '(Yearly)'){
		foreach ($emp['group_soal'] as $k => $soal) {			
			$idFprDetail = '';
			$idPaQuestion = '';
			$idPaAnswer = [];
			$descAnswer = '';

			if(count($emp['group_jawaban']) > 0){
				$idFprDetail = @$emp['group_jawaban'][$k]['id_fpr_detail'];
				$idPaQuestion = @$emp['group_jawaban'][$k]['id_pa_question'];
				$idPaAnswer = @$emp['group_jawaban'][$k]['id_pa_answer'];	
				$descAnswerResult = @$emp['group_jawaban'][$k]['desc_answer'];
				if($descAnswerResult != null){
					$descAnswer = @$emp['group_jawaban'][$k]['desc_answer'];
				}
			}
		
			$view .= '<div align="justify" style="margin-bottom:10px;">'.@$soal['fpr_soal'];
				$view .= '<div class="row">';
				if($soal['question_type'] == 'Essay'){				
					if($soal['sequence_soal'] == 2){
					//	echo 'seq 2';
						
						$view .= '<div class="col-9" style="padding-left:30px;">';	
							if(count($emp['quantitative']) > 0){
								$view .= '<table style="width:100%;" cellspacing="0" border="1">
										<thead>
											 <tr style="background: #E7E7E7;" align="center">
												<th rowspan="2" style="vertical-align: middle;">Item KPI</th>
												<th rowspan="2" style="vertical-align: middle;">Description</th>
												<th rowspan="2" style="vertical-align: middle;">Type</th>
												<th colspan="12" style="text-align: center;border-bottom: none;">Score KPI Monthly(%)</th>
											  </tr>
											<tr style="background: #E7E7E7;">				   																
												<th>Jan</th>
												<th>Feb</th>
												<th>Mar</th>
												<th>Apr</th>
												<th>Mei</th>
												<th>Jun</th>
												<th>Jul</th>
												<th>Ags</th>
												<th>Sep</th>
												<th>Okt</th>
												<th>Nov</th>
												<th>Des</th>
											</tr>
										</thead>
										<tbody>';
												foreach ($emp['quantitative'] as $k_2 => $item_quanti) {
													$view .= '<tr align="left">
														<td class="text-name" style="width:180px;">'.$item_quanti->item_kpi.'</td>
														<td class="text-name" style="width:150px;">'.$item_quanti->kpi_desc.'</td>
														<td class="text-middle" style="width:50px;">'.$item_quanti->type_kpi.'</td>										
														<td class="text-middle">'.$item_quanti->jan.'</td>
														<td class="text-middle">'.$item_quanti->feb.'</td>
														<td class="text-middle">'.$item_quanti->mar.'</td>
														<td class="text-middle">'.$item_quanti->apr.'</td>
														<td class="text-middle">'.$item_quanti->may.'</td>
														<td class="text-middle">'.$item_quanti->jun.'</td>
														<td class="text-middle">'.$item_quanti->jul.'</td>
														<td class="text-middle">'.$item_quanti->ags.'</td>
														<td class="text-middle">'.$item_quanti->sep.'</td>
														<td class="text-middle">'.$item_quanti->okt.'</td>
														<td class="text-middle">'.$item_quanti->nov.'</td>
														<td class="text-middle">'.$item_quanti->des.'</td>													
													</tr>';												
												}
											$view .='</tbody>
									</table>';
								}
								else{
									$view .= '<i>No Data</i>';
								}	
							$view .= '</div>';															
					}
					else if($soal['sequence_soal'] == 5){
					//	echo 'seq 5';
						$view .= '<div class="col-9" style="padding-left:25px;">';
								if(count($emp['qualitative']) > 0){
									if($name_url == 'Self'){
										$view .= '<table  cellspacing="0" border="1" style="width:100%;">
												<thead>
													<tr align="center" style="background: #E7E7E7;">			   
														<th class="text-middle">Final Score (%)</th>															
													</tr>
												</thead>
												<tbody>';
														$view .= '<tr>';																
														$view .= '<td class="text-middle">'.$emp['qualitative'][0]->final_score.'</td>';
														$view .= '</tr>';																									
												$view .='</tbody>
											</table>';
									}
									else{
										$view .= '<table  cellspacing="0" border="1" style="width:100%;">
												<thead>
													<tr align="center" style="background: #E7E7E7;">			   
														<th class="text-middle">Penilai</th>
														<th class="text-middle">Type</th>
														<th class="text-middle">Dinilai</th>
														<th class="text-middle">Score</th>
														<th class="text-middle">Total<br>Score</th>
														<th class="text-middle">Final<br>Score<br>(%)</th>															
													</tr>
												</thead>
												<tbody>';
													foreach ($emp['qualitative'] as $k_1 => $item_quali) {
														$view .= '<tr align="left">
															<td style="width:300px;">'.$item_quali->penilai.'</td>
															<td style="width:100px;">'.$item_quali->appraisers_hierarchy.'</td>
															<td style="width:300px;">'.$item_quali->dinilai.'</td>										
															<td class="text-middle">'.$item_quali->subtotal_score.'</td>';
															if($k_1 == 0){
																$rows = 'rowspan='.count($emp['qualitative']);
																$view .= '<td class="text-middle" '.$rows.'>'.$item_quali->total_score.'</td>';
																$view .= '<td class="text-middle" '.$rows.'>'.$item_quali->final_score.'</td>';
															}													
														$view .= '</tr>';												
													}
												$view .='</tbody>
											</table>';									
									}
								}
								else{
									$view .= '<i>No Data</i>';
								}				
						$view .=	'</div>';
					} 
					else {
						$view.= '<div class="col-9 border-answer-description">'.$descAnswer.'</div>';
					}
				}

				if($soal['question_type'] == 'Multiple_Answer'){	
					$allSelectedAnswer = '';
					foreach ($soal['answer'] as $k_answer => $m_answer) {
						$checked = '';
						$display = 'display:none;';
						$subPilihan = '';
						$deskripsiLainnya = '';

						foreach ($idPaAnswer as $k_answer_2 => $m_answer_2) {
							if($m_answer_2 == @$m_answer['id_pa_answer']){
								$checked = 'checked';
								if(@$m_answer['sequence'] == 'Lainnya'){
									$display = 'display:inline;';
								}
							}
						}

						if($display == 'display:inline;'){
							$deskripsiLainnya = $descAnswer;
						}
						if($checked == 'checked'){
							$subPilihan = @$m_answer['desc_answer'];

							if($k_answer == (count($soal['answer'])-1)){
								$allSelectedAnswer.= '<span class="style="color:#212529;">'.$subPilihan.': '.$deskripsiLainnya.'</span>';
							} else {
								$allSelectedAnswer.= '<span class="style="color:#212529;">'.$subPilihan.', </span>';
							}
						}
					}
					$view.= '<div class="col-9 border-answer-description">'.$allSelectedAnswer.'</div>';
				}	

				if($soal['question_type'] == 'Single_Answer'){
					$checkedAnswer = '';
				//	$x[] = $idPaAnswer;
					foreach ($soal['answer'] as $k_answer => $m_answer) {
						
					//	foreach ($idPaAnswer as $k_answer_3 => $m_answer_3) {
							if(@$idPaAnswer[0] == $m_answer['id_pa_answer']){
								$checkedAnswer.= '<span class="style="color:#212529;">'.$m_answer['desc_answer'].'</span>';
							}
					//	}
					}
					
					$view.=	'<div class="col-9 border-answer-description">'.$checkedAnswer.'</div>';
				}

			$view .=	'</div></div>';
		}
	
		}
		else if($notes == '(Periodically)'){
		//	dd('Periodically');
			foreach ($emp['group_soal'] as $k => $soal) {
				$idFprDetail = '';
				$idPaQuestion = '';
				$descAnswer = '';

				if(count($emp['group_jawaban']) > 0){
					$idFprDetail = @$emp['group_jawaban'][$k]['id_fpr_detail'];
					$idPaQuestion = @$emp['group_jawaban'][$k]['id_pa_question'];
					$descAnswer = @$emp['group_jawaban'][$k]['desc_answer'];
				}
					
					if($soal['question_type'] == 'Essay'){		
						if($soal['sequence_soal'] == 1){
						$view .= '<b>1. Pencapaian KPI Periodik</b><br>
								<div style="padding-left:15px;">Detail pencapaian kerja (KPI), point-point penting untuk pencapaian selama periodik (bulanan / 3 bulanan / 6 bulanan)</div>';
							$view .= '<div class="col-9" style="padding-left:20px;margin-bottom:20px;">';	
								if(count($emp['quantitative']) > 0){
									$view .= '<table style="width:100%;" cellspacing="0" border="1">
											<thead>
												 <tr style="background: #E7E7E7;" align="center">
													<th rowspan="2" style="vertical-align: middle;">Item KPI</th>
													<th rowspan="2" style="vertical-align: middle;">Description</th>
													<th rowspan="2" style="vertical-align: middle;">Type</th>
													<th colspan="12" style="text-align: center;border-bottom: none;">Detail Score KPI Monthly(%)</th>
												  </tr>
												<tr style="background: #E7E7E7;">				   																
													<th>Jan</th>
													<th>Feb</th>
													<th>Mar</th>
													<th>Apr</th>
													<th>Mei</th>
													<th>Jun</th>
													<th>Jul</th>
													<th>Ags</th>
													<th>Sep</th>
													<th>Okt</th>
													<th>Nov</th>
													<th>Des</th>
												</tr>
											</thead>
											<tbody>';
													foreach ($emp['quantitative'] as $k_2 => $item_quanti) {
														$view .= '<tr align="left">
															<td class="text-name" style="width:180px;">'.$item_quanti->item_kpi.'</td>
															<td class="text-name" style="width:180px;">'.$item_quanti->kpi_desc.'</td>
															<td class="text-middle" style="width:50px;">'.$item_quanti->type_kpi.'</td>										
															<td class="text-middle">'.$item_quanti->jan.'</td>
															<td class="text-middle">'.$item_quanti->feb.'</td>
															<td class="text-middle">'.$item_quanti->mar.'</td>
															<td class="text-middle">'.$item_quanti->apr.'</td>
															<td class="text-middle">'.$item_quanti->may.'</td>
															<td class="text-middle">'.$item_quanti->jun.'</td>
															<td class="text-middle">'.$item_quanti->jul.'</td>
															<td class="text-middle">'.$item_quanti->ags.'</td>
															<td class="text-middle">'.$item_quanti->sep.'</td>
															<td class="text-middle">'.$item_quanti->okt.'</td>
															<td class="text-middle">'.$item_quanti->nov.'</td>
															<td class="text-middle">'.$item_quanti->des.'</td>													
														</tr>';												
													}
												$view .='</tbody>
										</table>';
									}
									else{
										$view .= '<i>No Data</i>';
									}	
								$view .= '</div>';
								
							$view .= '<div style="padding-left:15px;">Total pencapaian kerja (KPI)/Bulan</div>';
							$view .= '<div class="col-9" style="padding-left:20px;margin-bottom:20px;">';	
								if(count($emp['quantitative_total']) > 0){
									$view .= '<table style="width:100%;" cellspacing="0" border="1">
											<thead>
												 <tr style="background: #E7E7E7;" align="center">												
													<th colspan="12" style="text-align: center;border-bottom: none;">Total Score KPI Monthly(%)</th>
												  </tr>
												<tr style="background: #E7E7E7;">				   																
													<th class="text-middle" style="width:55px;">Jan</th>
													<th class="text-middle" style="width:55px;">Feb</th>
													<th class="text-middle" style="width:55px;">Mar</th>
													<th class="text-middle" style="width:55px;">Apr</th>
													<th class="text-middle" style="width:55px;">Mei</th>
													<th class="text-middle" style="width:55px;">Jun</th>
													<th class="text-middle" style="width:55px;">Jul</th>
													<th class="text-middle" style="width:55px;">Ags</th>
													<th class="text-middle" style="width:55px;">Sep</th>
													<th class="text-middle" style="width:55px;">Okt</th>
													<th class="text-middle" style="width:55px;">Nov</th>
													<th class="text-middle" style="width:55px;">Des</th>
												</tr>
											</thead>
											<tbody>';
													foreach ($emp['quantitative_total'] as $k_2 => $item_quanti) {
														$view .= '<tr align="left">									
															<td class="text-middle">'.$item_quanti->jan.'</td>
															<td class="text-middle">'.$item_quanti->feb.'</td>
															<td class="text-middle">'.$item_quanti->mar.'</td>
															<td class="text-middle">'.$item_quanti->apr.'</td>
															<td class="text-middle">'.$item_quanti->mei.'</td>
															<td class="text-middle">'.$item_quanti->jun.'</td>
															<td class="text-middle">'.$item_quanti->jul.'</td>
															<td class="text-middle">'.$item_quanti->ags.'</td>
															<td class="text-middle">'.$item_quanti->sep.'</td>
															<td class="text-middle">'.$item_quanti->okt.'</td>
															<td class="text-middle">'.$item_quanti->nov.'</td>
															<td class="text-middle">'.$item_quanti->des.'</td>													
														</tr>';												
													}
												$view .='</tbody>
										</table>';
									}
									else{
										$view .= '<i>No Data</i>';
									}	
								$view .= '</div>';															
						}
						if($soal['sequence_soal'] == 2){
							$view .= '<b>2. Detail Review Kinerja Periodik</b><br>';
							$view .= '<div style="width:1000px;font-size:12px;margin:0 0 0px 5px;">';
							$view .= '<div style="padding-top:10px;border:1px solid #cfd0d0;margin:0 0 10px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" ><div align="center">TARGET YANG TELAH TERCAPAI (ACHIEVEMENTS)</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view .= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div>';
						}
						if($soal['sequence_soal'] == 3){
							$view .= '<div style="padding-top:10px;border:1px solid #cfd0d0;margin:0 0 10px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" ><div align="center">HAL YANG MENJADI KEKUATAN / SUDAH BAIK & PERLU DIPERTAHANKAN (AREA OF EXCELLENCES)</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view .= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div>';
						}
						if($soal['sequence_soal'] == 4){
							$view .= '<div style="padding-top:10px;border:1px solid #cfd0d0;margin:0 0 10px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" ><div align="center">HAL YANG PERLU DITINGKATKAN DI PERIODE BERIKUTNYA (AREA OF IMPROVEMENTS)</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view .= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div>';
							$view .= '</div>';
						}
						if($soal['sequence_soal'] == 5){
							$view .= '<div style="width:940px;padding-top:10px;border:1px solid #cfd0d0;margin:0 0 20px 20px;border-radius: 5px;font-size:12px;background:#f2f2f2;" ><div align="center">TARGET DI PERIODE BERIKUTNYA (BULANAN / 3 BULANAN / 6 BULANAN*)</div>
							<table>
							<tr>';							
							$view .= '<td style="width:400px;"><div style="padding-top:10px;border:1px solid #cfd0d0;"><div align="center">TARGET YANG AKAN DICAPAI</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view .= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div></td>';							
						}
						if($soal['sequence_soal'] == 6){
							$view .= '<td style="width:260px;"><div style="padding-top:10px;border:1px solid #cfd0d0;"><div align="center">TARGET WAKTU</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view .= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div></td>';
							
						}
						if($soal['sequence_soal'] == 7){
							$view .= '<td style="width:260px;"><div style="padding-top:10px;border:1px solid #cfd0d0;"><div align="center">BANTUAN YG DIBUTUHKAN & PIC</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view .= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div></td>';						
							$view .= '</tr></table></div>';
						}
						if($soal['sequence_soal'] == 8){
							$view .= '<b>3. Komentar, Saran, dan Persetujuan</b><br>';
							$view .= '<div style="width:980px;font-size:12px;margin:0 0 0px 10px;">';
							$view .= '<table><tr>';
							$view .= '<td style="width:490px;"><div style="padding-top:10px;border:1px solid #cfd0d0;background:#f2f2f2;"><div align="center">KOMENTAR / SARAN ATASAN</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view.= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div></td>';
						}
						if($soal['sequence_soal'] == 9){
							$view .= '<td style="width:470px;"><div style="padding-top:10px;border:1px solid #cfd0d0;background:#f2f2f2;"><div align="center">KOMENTAR / SARAN KARYAWAN</div>';
							$view .= '<div style="padding:-5px 5px 5px 5px;border:1px solid #cfd0d0;border-radius: 5px;font-size:12px;background:#ffffff;">';
								$view.= '<p align="justify">'.$descAnswer.'</p>';
							$view .= '</div>';
							$view .= '</div></td>';						
							$view .= '</tr></table>';
							$view .= '</div>';
						}
																			
					}
			}
		}
	//	dd($x);
		$view .='<br><br>
			<div>Pusat, '.$emp['sign_date'].'</div><br><br>
			<table class="table" style="vertical-align: middle;" align="center">
				<tr style="">
					<td class="bold" style="vertical-align: middle; text-align:center;">
						<label style="font-size:12px;">Atasan Menyetujui</label><br><br>';						
						if($emp['approve_atasan'] != null){
							$view .= '<qrcode value="'.@$emp['nik_atasan'].'" style="border:none;width:20mm;"></qrcode><br><br>'; 
						}
						else{
							$view .= '<br><br>';
						}
					$view.=	'<label style="font-size:12px;">( '.@$emp['atasan'].' )</label><br>
					</td>
					<td style="padding-right:100px;padding-left:100px;"></td>
					<td class="bold" style="vertical-align: middle; text-align:center;">
						<label style="font-size:12px;">Karyawan Menyetujui</label><br><br>';
						if($emp['approve_emp'] != null){
							$view .= '<qrcode value="'.@$emp['nik_employee'].'" style="border:none;width:20mm;"></qrcode><br><br>'; 
						}
						else{
							$view .= '<br><br>';
						}						
					$view.=	'<label style="font-size:12px;">( '.@$emp['name'].' )</label><br>
					</td>
				</tr>
			</table>';		
		echo $view;
	?>
</div>
</page>