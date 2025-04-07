<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-body">
                <div class="div_datatable"> 
                    <button onclick="return false;" class="btn btn-default advanced_review">Advanced Search</button><br><br>
                    <table id="review_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_review"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="mod_title">Review 360 Subordinat (Based On Appraiser)</h5>
                    <button type="button" class="close" onclick="second_close()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
					<div class="detail_datatable"> 
						<button onclick="return false;" class="btn btn-default advanced_detail">Advanced Search</button><br><br>
						<table id="detail_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>
					</div>
				</div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="second_close()">Close</button>
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
let period_code = '{{ $period_code }}';
$(document).ready(function(){
	 get_datatable();
});


$(document).on("click", ".advanced_review", function () {
    $('.cf').select2({width:'100%'});
    if($(".review_table").css('display') == 'none'){
        $(".review_table").show("slow");
    }
    else {
        $(".review_table").hide("slow");
    }   
});

$(document).on("click", ".advanced_detail", function () {
    $('.cf').select2({width:'100%'});
    if($(".detail_table").css('display') == 'none'){
        $(".detail_table").show("slow");
    }
    else {
        $(".detail_table").hide("slow");
    }   
});

const get_datatable = async () => {
    $(".div_datatable").show();
    $(".advanced_review").show();
	
	
    let t = $('#review_table').DataTable({
        processing: true,
        responsive: true,
        destroy: true,
        ajax: {
            url: "<?= url('kpi/360_feedback/pa_qualitative/list_review') ?>",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
			"data": {period_code:period_code},
        },
	
        columns: [
            {   // Checkbox select column
                data: '',
                defaultContent: '',
				width: '10px',
                orderable: false
            },
            {   // Checkbox select column
                data: 'id_employee_participant',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', title: 'No', orderable: false},
            { data: 'nik_employee', title: 'NIK'},
            { data: 'name', title: 'Name'},
            { data: 'total_percent', title: 'Score Total<br>360 (%)', className: 'dt-center',},
			{ data: 'action', title: 'Review', responsivePriority: 1, orderable: false, className: 'dt-center', render: function ( data, type, row ) {	
					return '<div align="center">'+data+'</div>';
				} 
			},
        ],
		"fnInitComplete": function (oSettings) {
            $('#review_table_wrapper .dt-center').css('text-align','center').change();
        },
    });
	
}
  
function second_close(){
	$('#modal_form_review').modal('hide');
	$("#modal_second").removeClass("modal-backdrop fade show");
}
$(document).on('click', '.view_level', function () {
		$("#modal_second").addClass("modal-backdrop fade show");
		global_id_employee_participant = $(this).attr('id');
		global_name = $(this).attr('name');
		$("#mod_title").html("Review 360 "+global_name+" (Based On Appraiser)");
		get_datatable_detail(global_id_employee_participant);
		
		$('#modal_form_review').modal('show');
		$("#detail_table_processing").css("background","white");
		$("#detail_table_processing").css("color","black");
   });	

const get_datatable_detail = async () => {
    $(".detail_datatable").show();
    $(".advanced_detail").show();
		
    let t = $('#detail_table').DataTable({
        processing: true,
        responsive: true,
        destroy: true,
        ajax: {
            url: "<?= url('kpi/360_feedback/pa_qualitative/get_detail_review') ?>",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
			"data": {period_code:period_code,id_employee_participant:global_id_employee_participant},
        },
	
        columns: [
            {   // Checkbox select column
                data: '',
                defaultContent: '',
				width: '10px',
                orderable: false
            },
            {   // Checkbox select column
                data: 'sequence',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', title: 'No', orderable: true},
            { data: 'soal', title: 'Soal'},
            { data: 'level_5', title: 'Level 5', className: 'dt-center'},
            { data: 'level_4', title: 'Level 4', className: 'dt-center'},
            { data: 'level_3', title: 'Level 3', className: 'dt-center'},
            { data: 'level_2', title: 'Level 2', className: 'dt-center'},
            { data: 'level_1', title: 'Level 1', className: 'dt-center'},
            { data: 'avg', title: 'Average (Level)', orderable: true, className: 'dt-center', render: function ( data, type, row ) {	
					return '<div align="center"><b>'+data+'</b></div>';
				} 
			},			
        ],
		"fnInitComplete": function (oSettings) {
            $('#detail_table_wrapper .dt-center').css('text-align','center').change();
        },
    });
	
} 
</script>
