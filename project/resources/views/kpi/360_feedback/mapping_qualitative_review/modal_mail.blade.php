<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-1 col-form-label">Penilai :</label>
                    <div class="col-sm-4">
                        <select id="emp_mail" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>
					
					<label class="col-sm-1 col-form-label">Period :</label>
                    <div class="col-sm-3">
                        <select id="period" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>
					
					<label class="col-sm-1 col-form-label">Submit :</label>
                    <div class="col-sm-2">
                        <select id="submit_type" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button onclick="return false;" id="search_mail" class="btn btn-lg btn-success" ><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>
                <div class="div_datatable"> 
                    <button onclick="return false;" class="btn btn-default advanced_mail">Advanced Search</button><br><br>
                    <table id="mail_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>
                </div>
            </div>
        </div>
    </div>
</div>
@section('css')
<style type="text/css">
th, td.dt-center{
	text-align: center;
}
</style>
@stop
<script type="text/javascript">
$(document).ready(function(){
	$(".advanced_mail").hide();

	get_employee_mail();
	get_period();
	get_submit();
	
});

function get_employee_mail() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_employee_mail') ?>', function (data) {
			$('#emp_mail').select2({
                placeholder: "Select Employee",
                allowClear: true,
                data: data
            });
		}).fail(function (data) { // Call failed
            get_employee_mail();
        });	
}

function get_period() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_period') ?>', function (data) {
			$('#period').select2({
                placeholder: "Select Period",
                allowClear: true,
                data: data
            });
		}).fail(function (data) { // Call failed
            get_period();
        });	
}

function get_submit() {
		submit_type = [
			{
				id: 0,
				text: 'NO'
			},
			{
				id: 1,
				text: 'YES'
			},
		];
		$('#submit_type').select2({
			placeholder: "Select Submit",
			allowClear: true,
			data: submit_type
		});	
}

$(document).on('click', '#search_mail', function () {
    $("#mail_table").html("");
    get_datatable()
});

$(document).on("click", ".advanced_mail", function () {
    $('.cf').select2({width:'100%'});
    if($(".mail_table").css('display') == 'none'){
        $(".mail_table").show("slow");
    }
    else {
        $(".mail_table").hide("slow");
    }   
});

const get_datatable = async () => {
    $(".div_datatable").show();
    $(".advanced_mail").show();
	
	
	let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
	
	let statusButton = {
			text: 'Send Mail Selected',
		//	url: '<?= url('kpi/360_feedback/mapping_qualitative_review/mass_mail') ?>',
			url: "{{ route('mail.mass_mail') }}",
			className: 'btn btn-success',
			action: function (e, dt, node, config) {
			  var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
				  return $(entry).data('entry-id')
			  });
				
			if (ids.length === 0) {
				alert('{{ trans('global.datatables.zero_selected') }}')

				return
			  }
		   
			  if (confirm('{{ trans('global.areYouSure') }}')) {
				$.ajax({
					method: 'POST',
					url: config.url,
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					data: { ids: ids},
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
					success:function(res){
						if (res.status == 'true') {
							 swal({
								icon: 'success',
								title: 'Success',
								text: 'Send Mail Successfully'
							});
							$('#mail_table').DataTable().ajax.reload();
						}
						else{
							swal({
								icon: 'error',
								title: 'Oops...',
								dangerMode: true,
								text: 'Send Mail Failed'
							});
						}
					},
					complete: function(){
						$('#loader').addClass('hidden');
					},
				})
			  }
			}
		  }
		
		dtButtons.push(statusButton)
	
    let myData = {
        id_employee: $("#emp_mail").val() == '' ? null : $("#emp_mail").val(),
        period: $("#period").val() == '' ? null : $("#period").val(),
        submit_type: $("#submit_type").val() == '' ? null : $("#submit_type").val(),
    };
    let t = $('#mail_table').DataTable({
		buttons: dtButtons,
        processing: true,
        responsive: true,
        destroy: true,
        serverSide: true,
        ajax: {
            url: "<?= url('kpi/360_feedback/mapping_qualitative_review/list_mail') ?>",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            "data": myData,
        },
		createdRow: function( row, data, dataIndex ) {
			  $(row).attr('data-entry-id', data['id_qualitative_appraisers']);
		  },
        columns: [
            {   // Checkbox select column
                data: '',
                defaultContent: '',
                orderable: false
            },
            {   // Checkbox select column
                data: 'id_qualitative_appraisers',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', title: 'No', orderable: false},
            { data: 'penilai', title: 'Penilai'},
            { data: 'appraisers_hierarchy', title: 'Type'},
            { data: 'dinilai', title: 'Dinilai'},
            { data: 'job_grade', title: 'Job Grade'},
            { data: 'private_mail', title: 'Mail'},
            { data: 'submitted', title: 'Submit', responsivePriority: 2, className: 'dt-center', render: function ( data, type, row ) { 
				if(row.submitted == true){
					return '<div align="center"><span class="badge badge-success">YES</span></div>';
				}
				else{
					return '<div align="center"><span align="center" class="badge badge-danger">NO</span></div>';
				}
			  }
			},
			{ data: 'action', title: 'Send Mail', responsivePriority: 1, orderable: false, width: '100px', className: 'dt-center', render: function ( data, type, row ) {	
					return '<div align="center">'+data+'</div>';
				} 
			},
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, 1000, -1],
            [10, 20, 50, 100, 200, 1000, 'All']
        ],
		 "fnInitComplete": function (oSettings) {
            $('#mail_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
            $('#mail_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
            $('#mail_table_wrapper .column-filter-widget:eq(7)').css('display','none').change();
            $('#mail_table_wrapper .column-filter-widget:eq(8)').css('display','none').change();
            $('#mail_table_wrapper .column-filter-widget:eq(8)').css('display','none').change();
            $('#mail_table_wrapper .dt-center').css('text-align','center').change();
        },
    });
	
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
}  

function send_mail(id_qualitative_appraisers) {
	console.log(id_qualitative_appraisers);
	$.ajax({
            url: '<?= url('kpi/360_feedback/mapping_qualitative_review/send_mail') ?>',
			method: "GET",
			data: {id_qualitative_appraisers: id_qualitative_appraisers},
            success: function (response) {
				if (response.status == 'true') {
					var formData = response.data;
					$.ajax({
						type: 'POST',
						headers: {
							'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
						},
						url: "{{ route('mail.submit_mail') }}",
						data: {source: formData},
						beforeSend: function () {
							$('#loader').removeClass('hidden');
						},
						success: function (res) {
							if (res.status == 'true') {
								 swal({
									icon: 'success',
									title: 'Success',
									text: 'Send Mail Successfully'
								});
								$('#mail_table').DataTable().ajax.reload();
							}
							else{
								swal({
									icon: 'error',
									title: 'Oops...',
									dangerMode: true,
									text: 'Send Mail Failed'
								});
							}
						},
						complete: function(){
							$('#loader').addClass('hidden');
						},
					});	
				} 		
            },
			error: function (response) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
        });
}

</script>
