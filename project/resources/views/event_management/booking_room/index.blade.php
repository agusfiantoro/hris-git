@extends('adminlte::page')
@section('title', $type == 'booking' ? 'Booking Room' : 'Event Room History')

@section('content')
<link rel="stylesheet" href="{{ asset('vendor/timetablejs/dist/styles/timetablejs.css') }}">
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #e8ebed;
        box-shadow: none;
    }
    .select2-container--open {
        z-index: 1650
    }
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }

    legend {
        font-size: 10pt;
    }

    .error {
        color: #ff0000;
        font-weight: 600;
    }

    .timetable .time-entry {
        height: 60px!important;
    }

    .timetable ul.room-timeline li {
        height: 60px!important;
    }

    .timetable aside li, .timetable time li {
        height: 60px!important;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">{{ $type == 'booking' ? 'Booking Room' : 'Event Room History' }}
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    @if($type == 'booking')
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Booking</button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <button class="btn btn-success col-md-1" onclick="showPrev()" id="prev-btn"><i class="fa fa-chevron-circle-left"></i> Prev</button>
                    <div class="col-md-10 text-center" id="dateControl">
                        <input type="text" class="form-control form-control-sm date text-center" id="targetDate" style="display:none;">
                        <button id="currentDate" class="btn btn-default" style="width:100%;font-weight:700;"></button>
                    </div>
                    <button class="btn btn-success col-md-1" onclick="showNext()" id="next-btn">Next <i class="fa fa-chevron-circle-right"></i></button>
                </div>
                <div class="calendar timetable"></div>
                @if($type == 'history')
                <div class="row">
                    <table id="historyTable" class="table table-striped" style="width:100%"></table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAdd" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditTitle">Add Room</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post" class="row">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_event_management" id="id_event_management" class="pk">
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_event_room">Room</label>
                            </div>
                            <div class="col">
                                <select name="id_event_room" id="id_event_room" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="description">Meeting Description</label>
                            </div>
                            <div class="col">
                                <input type="text" name="description" id="description" class="form-control form-control-sm">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="date">Date</label>
                            </div>
                            <div class="col">
                                <input name="date" id="date" class="form-control form-control-sm date">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="time">Time</label>
                            </div>
                            <div class="col">
                                <input name="time" id="time" class="form-control form-control-sm">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="long_description">Invitation Content</label>
                            </div>
                            <div class="col">
                                <textarea name="long_description" id="long_description" class="form-control form-control-sm" rows="3">
                                </textarea>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="name_organized_by">Organized By</label>
                            </div>
                            <div class="col">
                                <input name="name_organized_by" id="name_organized_by" class="form-control form-control-sm" disabled>
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_event_type">Event Type</label>
                            </div>
                            <div class="col">
                                <select name="id_event_type" id="id_event_type" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="responsible">Participants</label>
                            </div>
                            <div class="col">
                                <select name="responsible[]" id="responsible" class="form-control form-control-sm" style="width:100%" multiple>
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        {{-- <div class="alert alert-primary py-1 px-2" role="alert" id="managedByAlert" style="display:none;">
                            Selected person will be able to modify this room booking.
                        </div> --}}
                        <div class="row mb-2">
                            <div class="col">
                                <label for="status">Status</label>
                            </div>
                            <div class="col">
                                <select name="status" id="status" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                @if($type == 'booking')
                <button type="button" class="btn btn-sm btn-info btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-paper-plane"></i> <span id="saveLabel">Submit</span></button>&nbsp;
                @endif
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{asset('/vendor/timetablejs/dist/scripts/timetable.js')}}"></script>
<script type="text/javascript">
    // @include('assets.advanced_search')

    let rooms, eventTypes, responsibles = [];
    var timetable = new Timetable();
    var timetableRenderer = null;
    var timetableDate = moment();
    var maxBookingDate = moment().add(5, 'days');

    @if($type == 'history')
    $('#historyTable').DataTable({
        ajax: {
            url: "{{ route('event_management.booking_room.history') }}",
        },
        serverSide: true,
        processing: true,
        columns: [
            { data: 'event_room', title: 'Room' },
            { data: 'description', title: 'Description' },
            { data: 'start_date', title: 'Start Date' },
            { data: 'end_date', title: 'End Date' },
            { data: 'name', title: 'Organized By' },
            { data: 'status', title: 'Status' },
            { 
                data: 'id_event_management',
                title: 'Action',
                render: (data) => {
                    return `<button class="btn btn-sm btn-warning text-white" onclick="editEvent(${data})"><i class="fas fa-eye"></i> View</button>`;
                }
            }
        ],
    });
    @endif

    $('#status').empty().prepend().select2({
        data: [
            { id: 'A', text: 'Active' },
            { id: 'I', text: 'Inactive' },
        ],
    });

    function handleError(jqAjaxErrorInstance) {
        swal({
            icon: 'error',
            title: 'Error',
            text: jqAjaxErrorInstance.responseJSON.message,
        });
    }

    function getInitData(modifyDependentElements = true) {
        $.ajax({
            url: "{{ route('event_management.booking_room.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                $('.new, .edit').attr('disabled', false);
                $('#modalAdd').find('input:checkbox').trigger('change');
                rooms = res.data.rooms;
                eventTypes = res.data.event_types;
                responsibles = res.data.responsibles_available;
                if(modifyDependentElements) {
                    $('#id_event_room').empty().prepend('<option></option>').select2({
                        data: res.data.rooms,
                        allowClear: true,
                        placeholder: 'Select Event Room'
                    }).trigger('change');
                    $('#id_event_type').empty().prepend('<option></option>').select2({
                        data: res.data.event_types,
                        allowClear: true,
                        placeholder: 'Select Event Type'
                    }).trigger('change');
                    $('#responsible').empty().prepend('<option></option>').select2({
                        data: res.data.responsibles_available,
                        allowClear: true,
                        placeholder: 'Select Responsible Employees'
                    }).trigger('change');
                    $('#modalAdd').find('select').each((_i, element) => {
                        if($(element).attr('default-value')) {
                            $(element).val($(element).attr('default-value')).trigger('change');
                        }
                    });
                    $('#modalAdd').find('#time').daterangepicker({
                        autoApply: false,
                        timePicker: true,
                        timePicker24Hour: true,
                        locale: {
                            format: 'HH:mm',
                            separator: ' to ',
                        },
                    }).on('show.daterangepicker', function (ev, picker) {
                        picker.container.find(".calendar-table").hide();
                    }).on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                    });
                }
            },
            error: handleError
        })
        $('#modalAdd').find('.date').daterangepicker({
            singleDatePicker: true,
            autoApply: false,
            // autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            },
            minDate: moment().format('YYYY-MM-DD'),
            maxDate: maxBookingDate.format('YYYY-MM-DD'),
        }).on('show.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        $('#targetDate').daterangepicker({
            singleDatePicker: true,
            autoApply: true,
            // autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            },
            maxDate: maxBookingDate.format('YYYY-MM-DD'),
            @if($type == 'booking')
            minDate: "{{ date('Y-m-d') }}",
            @endif
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            createCalendar(picker.startDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    }

    getInitData(true);

    $(document).on('change', '#id_asset_group', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Save');
        } else {
            $('#saveLabel').text('Update');
        }
    })

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Add Booking');
        $('.error').text('');
        $('#assetGroupForm')[0].reset();
        $('#modalAdd').find('select').each((_i, element) => {
            $(element).val(null).trigger('change');
        });
        $('#date').val(timetableDate.format('YYYY-MM-DD')).trigger('apply.daterangepicker');
        $('#responsible').empty().prepend('<option></option>').select2({
            data: responsibles,
            allowClear: true,
            placeholder: 'Select Responsible Employees'
        }).trigger('change');
        $('#modalAdd').find('input[type=checkbox]').each((_i, element) => {
            $(element).attr('checked', false);
        });
        $('#modalAdd').find('#status').val('A').trigger('change');
        $('.pk').val(null);
        $('#modalAdd').find('input, select').attr('disabled', false);
        $('#name_organized_by').attr('disabled', true).val('{{ $employee->name }}');
        $('#responsible').val([{{ $employee->id_employee }}]).trigger('change');
        $('#submitApply').show();
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '#submitApply', function() {
        $('#assetGroupForm').submit();
    });

    $('#assetGroupForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('event_management.booking_room.save') }}",
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: () => {
                $('#loader').removeClass('hidden');
            },
            success: (res) => {
                $('#modalAdd').modal('hide');
                createCalendar(timetableDate.format('YYYY-MM-DD'));
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message
                });
            },
            error: (err) => {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: err.responseJSON.message
                });
                if(err.status == 422) {
                    Object.entries(err.responseJSON.errors).forEach((obj) => {
                        $(`#${obj[0]}`).closest('.col').find('.error').text(obj[1][0]);
                    });
                }
            },
            complete: () => {
                $('#loader').addClass('hidden');
            }
        })
    });

    $(document).on('change', '#responsible', function() {
        if($(this).val().length > 0) {
            $('#managedByAlert').show();
        } else {
            $('#managedByAlert').hide();
        }
    });

    $(document).on('click', '#targetDate', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', '#dateControl', function () {
        $('#targetDate').toggle('slow').trigger('click');
        $('#currentDate').toggle('slow');
    })

    function checkButtonAvailability() {
        @if($type == 'booking')
            if(timetableDate.format('YYYY-MM-DD') > moment().format('YYYY-MM-DD')) {
                $('#prev-btn').attr('disabled', false);
            } else {
                $('#prev-btn').attr('disabled', true);
            }
        @endif
        if(timetableDate.format('YYYY-MM-DD') < maxBookingDate.format('YYYY-MM-DD')) {
            $('#next-btn').attr('disabled', false);
        } else {
            $('#next-btn').attr('disabled', true);
        }
    }

    function showPrev() {
        timetableDate.subtract(1, 'days');
        checkButtonAvailability();
        createCalendar(timetableDate.format('YYYY-MM-DD'));
    }

    function showNext() {
        timetableDate.add(1, 'days');
        checkButtonAvailability();
        createCalendar(timetableDate.format('YYYY-MM-DD'));
    }

    function editEvent(id) {
        $('#modalEditTitle').text('Edit Booking');
        $.ajax({
            url: "{{ route('event_management.booking_room.get_edit') }}",
            data: {
                id_event_management: id,
            },
            beforeSend: () => {
                $('.error').text('');
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]);
                });
                $('#time').val(`${moment(res.data.start_date).format('HH:mm')} to ${moment(res.data.end_date).format('HH:mm')}`).trigger('change');
                $('#date').val(moment(res.data.start_date).format('YYYY-MM-DD'));
                $('#modalAdd').find('select').trigger('change');
                
                let allowed = res.data.responsible ? res.data.responsible : [];
                allowed.push(res.data.organized_by);
                if(!allowed.includes(res.data.viewer_id_employee)) {
                    $('#modalAdd').find('input, select, textarea').attr('disabled', true);
                    $('#submitApply').hide();
                } else {
                    $('#modalAdd').find('input, select, textarea').attr('disabled', false);
                    $('#submitApply').show();
                }
                @if($type == 'history')
                $('#modalAdd').find('input, select, textarea').attr('disabled', true);
                @endif
                $('#name_organized_by').attr('disabled', true);
            },
            error: (err) => {
                swal({
                    title: 'Error',
                    icon: 'error',
                    text: err.responseJSON.message
                });
            }
        })
        $('#modalAdd').modal('show');
    }

    function createCalendar(date) {
        $.ajax({
            url: "{{ route('event_management.booking_room') }}?date="+date,
            beforeSend: () => {
                timetable = new Timetable();
                $('#targetDate').hide('slow');
                $('#currentDate').show('slow');
                $('#currentDate').html('<i class="fa fa-spinner fa-pulse"></i>')
            },
            success: (res) => {
                $('#currentDate').text(res.data.date);
                timetableDate = moment(date);
                checkButtonAvailability();
                $('#targetDate').hide('slow');
                $('#currentDate').show('slow');
                timetable.addLocations(res.data.rooms);
                timetable.setScope(0, 23); // optional, only whole hours between 0 and 23
                res.data.schedule.forEach((schedule) => {
                    try {
                        timetable.addEvent(schedule.description, schedule.event_room, new Date(schedule.start_date), new Date(schedule.end_date), {
                            // url: `#id-event-management=${schedule.id_event_management}`,
                            onClick: () => editEvent(schedule.id_event_management),
                            data: {
                                'time-start': schedule.start_date,
                                'time-end': schedule.end_date,
                                organizer: `${schedule.name} (${schedule.nik})`,
                                description: schedule.description,
                                toggle: "tooltip",
                                'tooltip-content': `${schedule.description}`,
                            }
                        });
                    } catch (e) {

                    }
                    
                })
                timetableRenderer = new Timetable.Renderer(timetable);
                timetableRenderer.draw('.calendar');
                $('.timetable').find('span[data-time-start]').each((_i, element) => {
                    $(element).empty().addClass('d-flex flex-column').tooltip();
                    $(element).attr('data-original-title', $(element).data('tooltip-content'));
                    $(element).append(`<b>${$(element).data('description')}</b>`);
                    let organizer = $(element).data('organizer');
                    if(organizer && !organizer.includes('(null)')) {
                        $(element).append(`<span style="font-size:8pt;">${organizer}</span`);
                    }
                    $(element).append(`<span>${moment($(element).data('time-start')).format('HH:mm')} - ${moment($(element).data('time-end')).format('HH:mm')}</span>`);
                })
            }
        })
    }

    createCalendar(timetableDate.format('YYYY-MM-DD'));

</script>
@endsection