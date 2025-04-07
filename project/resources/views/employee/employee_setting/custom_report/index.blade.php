@extends('adminlte::page')
@section('title', 'Custom Report')

@section('content')

<style>
    li.select2-results__option strong.select2-results__group:hover {
      background-color: #6a6a6a;
      color:#fff;
      cursor: pointer;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Custom Report</h5>
				<div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Custom Report</button>
                </div>
            </div>

            <div class="card-body">
				<br>	  
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="custom_report_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Menu Report</th>
                            <th>Group Column</th>
                            <th>Detail Column</th>
                            <th style="width:120px;">Action</th>							
                        </tr>
                    </thead>
                </table>
				
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_custom"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="customForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Custom Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
							<div class="row">
                                <label class="col-sm-2 col-form-label">Menu Report</label>
                                <div class="col-sm-6">
									<input name="id_req_report" id="id_req_report" type="hidden">
                                    <select name="name_report" id="name_report" class="form-control form-control-sm select2" style="width:100%;"></select>
									<input type="hidden" name="address_menu" id="address_menu" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Group Column</label>
                                <div class="col-sm-6">
                                    <input type="text" name="group_column" id="group_column" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="group_columnError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<br>
                           <div class="row">
							  <div class="col-12">
								<div class="form-group">
								  <label>Detail Column</label>
								  <select id="duallistbox" name="duallistbox[]" multiple="multiple" style="height: 300px;">
								  </select>
								</div>
								<!-- /.form-group -->
							  </div>
							  <!-- /.col -->
							</div>
                        </div>
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
					<button type="submit" class="edit_custom btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Confirmation</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('css')
<style type="text/css">
.dtfc-fixed-left{
	z-index:10;
}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_req_report 	= "";
let typeDuallistboxNow 		= '';
let accessGroup 			= "<?= $accessGroup ?>";
let filter_column 			= [];
let optionsSelect2 			= [];
let allBranch 				= [] ;
$.each(<?= json_encode($allBranch) ?>, function (index,item) {
	allBranch[index] = item;
})

const group = {
	'Employee_Data' : { // nama harus sama dengan parameter:id
		parameter: {id : 'Employee_Data', text: 'Employee Data', address:'employee/employee/employee'}, 
		selectType: 'duallistbox',
		column: ['NIK','Name','Email','Join Date','ID Number','Home Address','KTP Address','KTP Attachment','KK Attachment','Point of Recruit','Gender','Marital','Religion','Date Of Birth','Place Of Birth','PTKP Status','Number Of Children','NPWP','Mobile Phone','Work Mail','Bank Name','Bank Account','BPJS','BPJS Ketenagakerjaan','Non BPJS','Permanent Date','Emergency Phone','Emergency Contact','Spouse Name','Position','Principal','Department','Regional','Branch','Work Location','Shift Group','Time Zone','Job Grade','Job Status','Direct Supervisor','Immediate Manager','Employment Status', 'Expired Date', 'Sales Code','Vaccine Status','Date of Vaccine','Resign Date','Terminate Reason','Status', 'Surat Pernyataan', 'Last Education', 'SIM', 'NPWP Attachment', 'Buku Rekening', 'Assigned Company']
	},
	'Employee_Request' : { // nama harus sama dengan parameter:id
		parameter: {id: 'Employee_Request', text: 'Employee Request', address:'employee/employee/employee_request_management'},
		selectType: 'duallistbox',
		column: ['Reference Number','Ref Req Cancel','NIK','Employee Name','Created Date','Request Type','Leave Type','Overtime Type','Request Start Date','Request End Date','Days Type','Note Rejected','Note Revised','Note','Approval Status']
	},
	'Employee_Attendance' : { // nama harus sama dengan parameter:id
		parameter: {id: 'Employee_Attendance', text: 'Employee Attendance', address:'employee/employee_setting/workdays'},
		selectType: 'duallistbox',
		column: ['Current Dates','Employee Name','Department','Actual Time In','Actual Time Out','Day Type','Schedule Time In','Late In','Schedule Time Out','Early Out','Current Employee Timezone','Work Hours','Overtime','Current Name In','Current Address In','Current Name Out','Current Address Out','Branch','Location','Note','Maps']
	},
	'Region' : { // nama harus sama dengan parameter:id
		parameter: {id: 'Region', text: 'Region', address:'setting/responsibility_menu/access_right_user'},
		selectType: 'select2',
		column: allBranch 
	},
}

$(document).on('click', '.new', function () {
            global_id_req_report = "";
            $("#customForm")[0].reset();
            $("#customForm .modal-title").html("Form Custom Report");
            $(".invalid-feedback").children("strong").text("");
            $("#customForm input").removeClass("is-invalid");
            $("#customForm textarea").removeClass("is-invalid");
            $('#save_button').attr('class','btn btn-sm btn-success').css("display","block");
            $('#save_button').html('<i class="fas fa-save"></i> Save');
			$("#edit_button").css("display","none");
            $('#modal_form_custom').modal('show');
            get_name_report()
            let menu_name = $('#name_report').find("option:selected").val();
			get_detail_report(menu_name);
        });
		
var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('custom.save_custom') }}";
			$(this).closest(".card").find("customForm").submit();
		  });

		  $(".edit_custom").on("click",function(){
			AjaxUrl = "{{ route('custom.update_custom') }}";
			$(this).closest(".card").find("customForm").submit();
		  });
		
 $('#customForm').submit(function (e) {
            e.preventDefault();
			var formData = new FormData(this);
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $(".invalid-group").children("strong").text("");
            $("#customForm input").removeClass("is-invalid");
            $("#customForm select").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Accept: "application/json"
                    },
					processData: false,  // Important!
					contentType: false,
					cache: false,
				//	url: "{{ route('custom.save_custom') }}",
                    url: AjaxUrl,
					data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
						if (response.status == 'true') {
							 $('#modal_form_custom').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function(){ 
								   location.reload();
								   }
								);
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! '+response.message,
                            });
                        }
                    },
					error: function (response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
                            });
                        }						
						else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
								text: 'Something went wrong! '+response.responseJSON.message,
                            });
                        }
                    },
					complete: function(){
						$('#loader').addClass('hidden');
					},
                });
            
});

$(document).on('click', '.edit', function(){
  let id_req_report = $(this).attr('id');
  global_id_req_report = id_req_report;
   $("#customForm")[0].reset();
	$("#customForm .modal-title").html("<span class='fas fa-edit'></span> Edit Custom Report");
	$(".invalid-feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$(".error-tab").html("");
	$("#customForm input").removeClass("is-invalid");
	$("#save_button").css("display","none");
	$("#edit_button").css("display","inline");

		$.ajax({
                url: "<?= url('employee/employee_setting/custom_report/get_custom_edit') ?>",
                method: "GET",
                data: {id_req_report: id_req_report},
                success: function (response) {
					
                    $('#id_req_report').val(response.id_req_report).trigger('change');
                    $('#name_report').val(response.name_report).trigger('change', [true]);
                    $('#address_menu').val(response.address_menu).trigger('change');
                    $('#group_column').val(response.group_column).trigger('change');					
                    $('#duallistbox').val(JSON.parse('['+response.detail_column+']')).trigger('change');  

					if(group[response.name_report].selectType == 'duallistbox'){
						$('#duallistbox').bootstrapDualListbox('refresh', true);
					} else {
						get_detail_report(response.name_report, response.detail_column);
					}
                },
                error: function (xhr) {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                    });
                }
            });
		$('#modal_form_custom').modal('show');
 });


$(document).on('click', '.delete', function (event) {
	id_req_report = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
       buttons: true,
		dangerMode: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, delete it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"custom_report/destroy/"+id_req_report,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#custom_report_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Deleted!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						location.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});

function get_name_report() {
	const parameter = Object.keys(group); // mengambil nama Region, Employee_Request, dll.
	let name_report = [];
	$.each(parameter, function (index, item) {
		if(accessGroup != 'Default_Administrator' && item == 'Region'){
			return;
		} else {
			name_report.push(group[item].parameter) 
		}
	})	
	$('#name_report').select2({
		data: name_report,
	}).trigger('change', [true]);	

	return name_report;
}

$(document).on('change', '#name_report', function (e, isTriggered) {
	$('#duallistbox').empty();
	$('#address_menu').val('');
	$('#address_menu').val($(this).select2('data')[0].address);
	if ($(this).val() != 'Region' || ($(this).val() == 'Region' && !isTriggered)){ // change by human
		get_detail_report($(this).val());
    } 
});

function get_detail_report(name, id_option=''){
	let arrData 		= group[name].column;
	let type 			= group[name].selectType;
	let custom_column 	= [];
	let value 			= '';

	$('#duallistbox').html('');
	if(type == 'duallistbox'){
		if(typeDuallistboxNow == 'select2'){
			$('#duallistbox').select2('destroy');
			$('#duallistbox').removeClass('select2').css('display','none');
			$('.bootstrap-duallistbox-container').show();
		} 
		$('#duallistbox').bootstrapDualListbox();
		$.each(arrData, function (index,item) {
			custom_column.push({
				id : index + 2,
				text : item
			}) 
		})	
		$.each(custom_column, function (index,item) {
			$('#duallistbox').append( '<option value="'+item.id+'">'+item.text+'</option>' );
	    })
		$('#duallistbox').bootstrapDualListbox('refresh', true);
		typeDuallistboxNow = type;
	} 
	else {
		if(typeDuallistboxNow == 'duallistbox'){
			$('.bootstrap-duallistbox-container').hide();
		}
		$('#duallistbox').addClass('select2').css('width','100%');

        getOptSelect2(name).then(listOpt => { // get data ke server dulu lalu hasilnya ditaruh select2
            $('#duallistbox').select2({
                placeholder: "Select Option",
                data: listOpt,
                allowClear: true,
            });
            if(id_option != ''){ // utk pendefinisian parameter saat proses edit
            	let arrOption = id_option.split(',');
                $('#duallistbox').val(arrOption).trigger('change');
            }
        });
		typeDuallistboxNow = type;

		$('#duallistbox').on('select2:open', function(e) { //saat memilih nama region akan select seluruh branch
	        $('#select2-duallistbox-results').on('click', function(event) {
	            event.stopPropagation();
	            var data = $(event.target).html();
	            var selectedOptionGroup = data.toString().trim();
	            var groupchildren = [];
	            for (var i = 0; i < optionsSelect2.length; i++) {
	                if (selectedOptionGroup.toString() === optionsSelect2[i].text.toString()) {
	                    for (var j = 0; j < optionsSelect2[i].children.length; j++) {
	                        groupchildren.push(optionsSelect2[i].children[j].id);
	                    }
	                }
	            }
	            let options = [];
	            options = $('#duallistbox').val();
	            if (options === null || options === '') {
	                options = [];
	            }
	            for (var i = 0; i < groupchildren.length; i++) {
	                var count = 0;
	                for (var j = 0; j < options.length; j++) {
	                    if (options[j].toString() === groupchildren[i].toString()) {
	                        count++;
	                        break;
	                    }
	                }
	                if (count === 0) {
	                    options.push(groupchildren[i].toString());
	                }
	            }
	            $('#duallistbox').val(options).trigger('change').select2('close');
	        });
	    });
	}
}

async function getOptSelect2(param) {
	try {
		if(param == 'Region'){//didefiniskan agar kedepannya jika memang dibutuhkan IF IF parameter2 lain ketika gnti name report
	        let urlRegion 	= '<?= url('employee/employee_setting/custom_report/get_region_branch') ?>';
		    let option 		= [];
		    try {
		        await $.getJSON(urlRegion, function (res) { 
	        		optionsSelect2 = res;
		        });
		        return optionsSelect2;
		    } catch (error) {
		        getOptSelect2(param);
		    }
	    }
    } catch (error) {
        getOptSelect2(param);
    }
}

$(document).ready(function(){
	get_name_report()
	
	$('#custom_report_table').DataTable({
        processing: true,
    //    serverSide: true,
		responsive: true,		
        ajax: {
		   url: "{{ route('custom.index') }}",
		   error: function (jqXHR, textStatus, errorThrown) {
				$('#custom_report_table').DataTable().ajax.reload();
            }
		  },
        columns: [
		{
                defaultContent: '',
				orderable: false,
				},
		{   // Checkbox select column
                data: 'id_req_report',
                defaultContent: '',
                orderable: false
            },
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{ data: 'name_report', name: 'name_report',render: function ( data, type, row ) {
					return data.replace(/_/g,' ');
				} 
			},
			{ data: 'group_column', name: 'group_column' },
			{ data: 'detail_column', name: 'detail_column',render: function ( data, type, row ) {
				var sp = data.split(',');
				var x = "";

				$.each(sp, function (index,item) {
					if(group[row['name_report']].selectType == 'duallistbox'){
						if(parseInt(item) > 1){
							x += group[row['name_report']].column[parseInt(item)-2]+', ';
						}
					} else {
						if(parseInt(item) > 0){
							x += group[row['name_report']].column[parseInt(item)]+', ';
						}
					}
				})	
				return x.slice(0, -2);
			}},
			
			{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {
					if(access_create == 0){
						$('.new').css('display', 'none');
					}	
					if(access_edit == 0){
						$('.edit').css('display', 'none');
					}		
					if(access_delete == 0){
						$('.delete').css('display', 'none');
					}
					if(access_print == 0){
						$('.print').css('display', 'none');
					}										
					return data;
				} 
			},
        ]
    });
		
	
	$('#advanced').click(function(){
		$('.cf').select2({width:'100%'});
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
		}
		else {
			$("#cf").hide("slow");
		}		
	});
	
});
</script>
@endsection