 <div class="row">
	<div class="col-md-12">
		<ul class="nav nav-tabs" id="tab_official_detail" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="link_tab_trans-details" data-toggle="pill" href="#trans-details" role="tab" aria-controls="link_tab_trans-details" aria-selected="true">Transport</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="link_tab_akomodasi-details" data-toggle="pill" href="#akomodasi-detail" role="tab" aria-controls="link_tab_akomodasi-details" aria-selected="true">Accommodation</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="link_tab_cash-details" data-toggle="pill" href="#cash-detail" role="tab" aria-controls="link_tab_cash-details" aria-selected="true">Cash advance</a>
			</li>	 
		</ul>
		<div class="tab-content" id="tab_official_detail_content" style="font-size:14px">
			<div class="tab-pane fade show active" id="trans-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
				<br/>
				<div class="row">
					<div class="col-12">
						<table style="width:100%;" id="table_trans_detail" class="responsive table table-striped table-bordered table-hover datatable">
							<thead>
								<tr align="center">
									<th style="white-space:nowrap;">No.</th>
									<th>Transport Type</th>
									<th>Transport Recommendation</th>
									<th>From</th>
									<th>To</th>
									<th>Branch Destination</th>
									<th>Date</th>
									<th>Time</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="akomodasi-detail" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
				<br/>
				<div class="row">                                      
					<div class="col-12">
						<table style="width:100%;" id="table_akomodasi_detail" class="responsive table table-striped table-bordered table-hover datatable">
							<thead>
								<tr>
									<th style="white-space:nowrap;">No.</th>
									<th>Category</th>
									<th>Hotel/Kos Name</th>
									<th>City</th>
									<th>Branch Destination</th>
									<th>Check In Date</th>
									<th>Check Out Date</th>
									<th>Length of stay</th>
								</tr>
							</thead>   
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="cash-detail" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
				<br/>
				<div class="row">                                      
					<div class="col-12">
						<table style="width:100%;" id="table_cash_detail" class="responsive table table-striped table-bordered table-hover datatable">
							<thead>
								<tr>
									<th style="white-space:nowrap;">No.</th>
									<th>Category</th>
									<th>From Date</th>
									<th>To Date</th>
									<th>Region Destination</th>
									<th>Branch Destination</th>
									<th>Qty</th>
									<th>Budget</th>
									<th>Total</th>
									<th>Notes</th>
								</tr>
							</thead>   
						</table>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>

<script type="text/javascript">
moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  
 function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D/MM/YYYY');
    return formattedDate;
  }
  
   function formatRupiah(amount) {
    if (!amount) {
      return '';
    }
    return amount.toLocaleString('id-ID');
  }
  
$(document).ready(function(){
	let id_travel_transaction = '<?= $id ?>';
	get_trans_view(id_travel_transaction);
	get_accomodation_view(id_travel_transaction);
	get_cash_view(id_travel_transaction);
});

function get_trans_view(id_travel_transaction) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_trans_detail').DataTable({	
		destroy:true,
		ajax: {
			url: "{{ route('trans.index') }}",
		//	url: "<?= url('employee/employee/employee/get_emp_contract') . '?id_employee=' ?>" + global_id_employee,
			data: {id_official_travel: id_travel_transaction},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_trans_detail').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'trans_type', name: 'trans_type'},
			{data: 'trans_reco', name: 'trans_reco'},
			{data: 'city_from', name: 'city_from'},
			{data: 'city_to', name: 'city_to'},
			{data: 'branch', name: 'branch'},
			{data: 'date_trans', name: 'date_trans',render: function (data, type, row) {
				return TanggalIndonesia(data);
				}  
			},
			{data: 'time_trans', name: 'time_trans'},						
		]
	});
}

function get_accomodation_view(id_travel_transaction) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_akomodasi_detail').DataTable({	
		destroy:true,
		ajax: {
			url: "{{ route('aco.index') }}",
			data: {id_official_travel: id_travel_transaction},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_akomodasi_detail').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'category', name: 'category'},
			{data: 'hotel_name', name: 'hotel_name'},
			{data: 'city', name: 'city'},
			{data: 'branch', name: 'branch'},
			{data: 'date_i', name: 'date_i',render: function (data, type, row) {
				return TanggalIndonesia(data);
				}  
			},
			{data: 'date_o', name: 'date_o',render: function (data, type, row) {
				return TanggalIndonesia(data);
				}  
			},
			{data: 'qty', name: 'qty',render: function (data, type, row) {
				return data+" Night(s)";
				}  
			},
		]
	});
}

function get_cash_view(id_travel_transaction) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_cash_detail').DataTable({	
		destroy:true,
		ajax: {
			url: "{{ route('cash.index') }}",
			data: {id_official_travel: id_travel_transaction},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_cash_detail').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'category', name: 'category'},
			{data: 'from_dates', name: 'from_dates',render: function (data, type, row) {
				return TanggalIndonesia(data);
				}  
			},
			{data: 'to_dates', name: 'to_dates',render: function (data, type, row) {
				return TanggalIndonesia(data);
				}  
			},
			{data: 'region', name: 'region'},
			{data: 'branch', name: 'branch'},
			{data: 'qty', name: 'qty',render: function (data, type, row) {
				return data;
				}  
			},
			{data: 'budget', name: 'budget', render: $.fn.dataTable.render.number('.')
			},
			{data: 'total', name: 'total', render: $.fn.dataTable.render.number('.')  
			},
			{data: 'notes', name: 'notes',render: function (data, type, row) {
				return data;
				}  
			},
		]
	});
}
</script>	