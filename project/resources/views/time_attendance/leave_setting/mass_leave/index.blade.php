@extends('adminlte::page')

@section('title', 'Mass Leave Balance')

@section('content')
<style>
    .modal { overflow: auto !important; }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Mass Leave Balance</h5>
                <div class="card-tools">
                    <!-- <button type="button" class="new btn btn-sm btn-success" data-toggle="modal" data-target="#modalshiftgroup"><i class="fas fa-plus"></i> Create Shift Group</button> -->
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Employee Name :</label>
                    <div class="col-sm-4">
                        <select id="employeename" class="form-control select2" style="width: 100%;"></select>
                        <!-- <input type="hidden" id="id_shiftgroup" value=""> -->
                    </div>
                    
                    <label class="col-sm-2 col-form-label">End Date :</label>
                    <div class="col-sm-4">
                        <input type="text" name="enddate" id="enddate" class="form-control form-control-sm datepicker" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="button" id="generatemassleave" class="new btn btn-lg btn-success" style="padding:0.5rem 2rem;"><i class="fas fa-refresh"></i> Generate Mass Leave</button>
                        <button type="button" id="loadinggeneratemassleave" class="btn btn-lg btn-success" style="padding:0.5rem 2rem;display:none;"><i class="fas fa-spinner fa-pulse"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function(){
        $('.datepicker').each(function(){
            $(this).datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
            });
        });

        const get_employee = new Promise(function(res) {
            let option = [];
            $.each(<?= json_encode($get_employee) ?>, function (i, item) {
                let desc = item.name + ' ('+ item.nik_employee +')';
                // let desc = item.name;
                option.push({id: item.id_employee, text:desc});
            });
            res(option)
        });

        get_employee.then(function(value) {
            $('#employeename').prepend('<option selected></option>').select2({
                placeholder: "Select Employee",
                data: value,
                allowClear: true,
            });
        });

    });


    $(document).on("click", "#generatemassleave", function () {
        $("#generatemassleave").hide();
        $("#loadinggeneratemassleave").show();
        
        let employee_id = $('#employeename').children("option:selected").val();
        let employee_name = $('#employeename').children("option:selected").text();
        let end_date = $('#enddate').val();
        
        if(end_date =='' )
        {
            alert("Please fill End date");
            $("#generatemassleave").show();
            $("#loadinggeneratemassleave").hide();
        }
        else
        {
            $.ajax({
                url :"{{ route('mass_leave_generatemassleave') }}",  
                method:"POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                data: {
                    employeename: employee_name,
                    employee_id: employee_id,
                    enddate: end_date
                },
                success:function(msg){ 
                    if (msg.status == 'true') {
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: msg.message
                        }).then(ok => {
                            location.reload();
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                    }
                    $("#generatemassleave").show();
                    $("#loadinggeneratemassleave").hide();
                },
                error:function(msg){
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! [Unknown Error]'
                    });
                    $("#generatemassleave").show();
                    $("#loadinggeneratemassleave").hide();
                }
            })
        }
    });
</script>
@endsection
