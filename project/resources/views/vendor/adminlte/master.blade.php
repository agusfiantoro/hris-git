<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        {{-- Base Meta Tags --}}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" /> -->
		
<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-DMB7J06VJB"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-DMB7J06VJB');
		</script>

        <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/datatables-plugins/buttons/css/buttons.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/datatables-plugins/select/css/select.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/datepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
		<!-- link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/summernote-bs4.min.css') }}" -->
		<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
		<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/daterangepicker.css') }}">
		<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/rowReorder.dataTables.min.css') }}">
		<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/fixedColumns.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/responsive.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/splide/dist/css/splide.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/ekko-lightbox/ekko-lightbox.css') }}">
        <link rel="shortcut icon" href="{{ asset('vendor/adminlte/dist/img/icon.ico') }}" />
        
        <style>

             .lds-dual-ring.hidden { 
				display: none;
			}
			.lds-dual-ring {
			  display: inline-block;
			  width: 80px;
			  height: 80px;
			}
			.lds-dual-ring:after {
			  content: " ";
			  display: block;
			  width: 64px;
			  height: 64px;
			  margin: 15% auto;
			  border-radius: 50%;
			  border: 10px dotted #fff;
			  border-color: #fff #fff #fff transparent;
			  animation: lds-dual-ring 1.8s linear infinite;
			}
			@keyframes lds-dual-ring {
			  0% {
				transform: rotate(0deg);
			  }
			  100% {
				transform: rotate(360deg);
			  }
			}

			.lds-dual-ring-black.hidden { 
				display: none;
			}
			.lds-dual-ring-black {
			  display: inline-block;
			  width: 80px;
			  height: 80px;
			}
			.lds-dual-ring-black:after {
			  content: " ";
			  display: block;
			  width: 64px;
			  height: 64px;
			  margin: 15% auto;
			  border-radius: 50%;
			  border: 10px dotted #000;
			  border-color: #000 #000 #000 transparent;
			  animation: lds-dual-ring-black 1.8s linear infinite;
			}
			@keyframes lds-dual-ring-black {
			  0% {
				transform: rotate(0deg);
			  }
			  100% {
				transform: rotate(360deg);
			  }
			}

			.overlay {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100vh;
				background: rgba(0,0,0,.9);
				z-index: 9999;
				opacity: 1;
				transition: all 0.5s;
			}
			.column-filter-widgets { float:left; border:1px solid #dee2e6;}
			.column-filter-widget { float:left; padding: 5px;}
			.column-filter-widget select { display: block; }
			.column-filter-widgets a.filter-term { display: block; text-decoration: none; padding-left: 10px; font-size: 90%; }
			.column-filter-widgets a.filter-term:hover { text-decoration: line-through !important; }
			.column-filter-widget-selected-terms { clear:left; }
			
			.company-s {font-weight:bold;}
			
			.space{
				white-space:nowrap;
			}
			.dtr-title{
				width:200px;
			}
			
			.swal-red {
				color:#f27474;
				font-weight:bold;
			}

            .module-items {
                cursor: pointer;
                width: 5rem;
            }

            .module-items-icon {
                cursor: pointer;
                width: 4rem;
            }

            @media(min-width: 768px) {
                .module-items {
                    cursor: pointer;
                    width: 6rem;
                }

                .module-items-icon {
                    cursor: pointer;
                    width: 5rem;
                }
            }

            

            .module-items:hover {
                scale: 1.05;
                transition-duration: 200ms;
                transition-timing-function: cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .modal-responsibility-dialog, .modal-dialog.modal-responsibility-dialog {
                max-width: 100%;
                margin: 0;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                min-height: 100vh;
                display: flex;
            }
	
        </style>

        {{-- Custom Meta Tags --}}
        @yield('meta_tags')

        {{-- Title --}}
        <title>
            @yield('title_prefix', config('adminlte.title_prefix', ''))
            @yield('title', config('adminlte.title', 'AdminLTE 3'))
            @yield('title_postfix', config('adminlte.title_postfix', ''))
        </title>

        {{-- Custom stylesheets (pre AdminLTE) --}}

        @yield('adminlte_css_pre')

        {{-- Base Stylesheets --}}
        @if(!config('adminlte.enabled_laravel_mix'))

        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/font-awesome.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/global/css/main.css') }}">

        <link rel="stylesheet" href="{{ asset('vendor/iconpicker/css/fontawesome-iconpicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker_new.css') }}">


        {{-- Configured Stylesheets --}}
        @include('adminlte::plugins', ['type' => 'css'])
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
        <!-- link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" -->

        @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">


        @endif

        {{-- Livewire Styles --}}
        @if(config('adminlte.livewire'))
        @if(app()->version() >= 7)
        @livewireStyles
        @else
    <livewire:styles />
    @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}

    @yield('adminlte_css')

    {{-- Favicon --}}
    @if(config('adminlte.use_ico_only'))
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @elseif(config('adminlte.use_full_favicon'))
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('favicons/android-icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('favicons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    @endif
	
	<style>
		.custom-file-label,
		.custom-file-label::after {
			height: auto;
			padding-top: 5px;
			padding-bottom: 6px;
		}
        .--icon-change-responsibility {
            margin-right: 0.4rem!important;
        }
        .--menu-btn-change-responsibility:hover {
            background-color: #ddd;
        }
	</style>
    <script type="text/javascript">
        //variabel ini ditempatkan disini utk pengkondisian survey dan announcement yg barengan, lihat di file views/time_attendance/attendance/attendance.blade pasti ada variabel id_show
        let id_show = [];
    </script>
</head>

<body class="@yield('classes_body')" @yield('body_data')>
	
    {{-- Body Content --}}
    @yield('body')
	

<div id="modal_announcement_new" class="modal fade"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:800px;">
		<div class="modal-content">
		 <form method="post" id="announcement_newForm">
                {{ csrf_field() }}
			<div class="modal-header">
				<h4 class="modal-title" id="desc_announ"></h4>
			</div>
			<div class="modal-body">
				<div align="center">
					<span align="center" id="loader_announ" class="lds-dual-ring-black hidden"></span>
				</div>
					<input name="id_announ_relation" id="id_announ_relation" type="hidden">	
					
					<div id="content_letter_announ"></div>
			</div>
			<div class="modal-footer justify-content-between">
				<div class="mr-auto" id="attachment_announ"></div>
				<button type="button" class="close_read btn btn-sm btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="save_read btn btn-sm btn-success" id="save_read">As Read</button>&nbsp;
			</div>
		 </form>
		</div>
	</div>
</div>	

<div id="modal_change_responsibility" class="modal fade"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-responsibility-dialog">
		<div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Change Module</h4>
                    <button type="button" class="close" data-dismiss="modal" onclick="closeResponsibilityModal()" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body d-flex flex-wrap justify-content-start px-3 py-5 p-md-5" style="width:100%">
                    <div class="lds-dual-ring">Loading...</div>
                </div>
            </form>
		</div>
	</div>
</div>	
	
		
<div id="overlay">
  <div class="cv-spinner">
	<span class="spinner"></span>
  </div>
</div>
<div id="loader" class="lds-dual-ring hidden overlay"></div>

    {{-- Base Scripts --}}
    @if(!config('adminlte.enabled_laravel_mix'))

        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

		<?php if(\Request::path() == 'setting/responsibility/responsibility_group'){ ?>
			 <script src="{{ asset('vendor/iconpicker/js/jquery-2.2.1.min.js') }}"></script>
		<?php } ?>
        
		<script src="{{ asset('vendor/iconpicker/js/fontawesome-iconpicker.js') }}"></script>
	
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

        {{-- Configured Scripts --}}
        @include('adminlte::plugins', ['type' => 'js'])

        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif
	
    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
        @if(app()->version() >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif
<?php 
use App\Models\Setting\Responsibility\MasterMenu;

$masmenu = MasterMenu::where('address_menu',Request::path())->get()->toArray();
if(session('address_menu_access')){
    $accessThisMenu = Illuminate\Support\Js::from(session('address_menu_access')[\Request::path()][session('id_company')]);
} else {
    $accessThisMenu = 'null';
}
?>
    {{-- Custom Scripts --}}
<script>
var access_create = null;
var access_edit = null;
var access_delete = null;
var access_print = null;
let global_url_server = '';
let global_index = 0;
let currentPath = "<?= \Request::path() ?>";
let accessThisMenu = {{ $accessThisMenu }};
var responsibilityModules = [];

$(document).ready(function () {
    setResponsibilityModules();
 /*   document.addEventListener("visibilitychange", function(event) {
        //UNTUK mendeteksi jika user berpindah2 tab
        let selectedCompany = $('#company_session_full').find(':selected').val();
        $.ajax({
            type: 'GET',
            url: "{{ url('/checkSession')}}",
            data: {selected_company:selectedCompany, path:currentPath},
            dataType: 'json',
            success: function (result) {
                if(result.status == false){
                    location.reload();
                }
            }
        });
    });
*/
    if(localStorage.getItem("dataabsen")!=null){
        $.ajax({
            type: 'POST',
            url: "<?= url('/time_attendance/attendance/sinkron_absen_localstorage?id=') ?>"+JSON.parse(localStorage.getItem("dataabsen"))[0]['id_workdays'],
            dataType: 'json',
            data: {
                id_workdays: JSON.parse(localStorage.getItem("dataabsen"))[0]['id_workdays'],
                current_latitude: JSON.parse(localStorage.getItem("dataabsen"))[0]['current_latitude'],
                current_longitude: JSON.parse(localStorage.getItem("dataabsen"))[0]['current_longitude'],
                image_attachment: JSON.parse(localStorage.getItem("dataabsen"))[0]['image_attachment'],
                shift: JSON.parse(localStorage.getItem("dataabsen"))[0]['shift'],
                _token: "{{ csrf_token() }}",
                mapboxAccessToken: JSON.parse(localStorage.getItem("dataabsen"))[0]['mapboxAccessToken'],
                starttimeabsen: JSON.parse(localStorage.getItem("dataabsen"))[0]['start_time'],
                endtimeabsen: JSON.parse(localStorage.getItem("dataabsen"))[0]['end_time'],
                map_place: JSON.parse(localStorage.getItem("dataabsen"))[0]['map_place'],
                map_timezone: JSON.parse(localStorage.getItem("dataabsen"))[0]['map_timezone']
            },
            success: function (result) {
                swal({
                    icon: 'success',
                    title: "Attendance has been sent",
                    text: ' ',
                    dangerMode: true,
                }).then(ok => {
                    location.reload();
                });
                localStorage.removeItem("dataabsen");
            }
        });
    }

extendDatatable();
  
 
  global_url_server = currentPath;
  glob = currentPath+'/forgot';
  
  if(currentPath != "" && currentPath != "login" && currentPath != "forgot_password" && glob != 'reset/forgot'){
	get_role();
  }

  if("<?= session('id_user') ?>" != ""){
    if(!("{{ Request::get('s') }}" != '' && id_show.length > 0)){
        //pengondisian utk survey yg bukan berasal dari mobile agar bs menampilkan announcement dulu (jika barengan)
        setLocalStorageAnnouncement()
    }
    else {
        //pengondisian utk survey yg berasal dari mobile agar jika user melakukan skip survey langsung menampilkan announcement (jika announcement dan survey muncul barengan)
        $(document).on('click', '#skip_survey', function () {
            if("{{ Request::get('s') }}" != ''){
                if(localStorage.getItem("announ_old") != localStorage.getItem("announ_count")){
                    get_announ_new(global_index);  
                }
            }
        });
    }
  }
  
});

function setResponsibilityModules() {
    $.ajax({
        url: "{{ route('get_modules') }}",
        success: (res) => {
            responsibilityModules = res;

            let html = '';
			if(accessThisMenu){
				res.forEach((module) => {
					html += `<div onclick="changeResponsibilty('${module.module_code}')" class="module-items d-flex flex-column align-items-center mt-3 ml-3 mr-3 mt-md-5 ml-md-5 mr-md-5">
							<img src="data:image/png;base64,${module.module_icon}" class="rounded shadow p-2 module-items-icon" />
							<span class="module-caption mt-2 text-center">${module.module_name}</span>
						</div>`
				});
				$('#modal_change_responsibility').find('.modal-body').html(html);
			}
        },
        error: (err) => {
            setResponsibilityModules();
        }
    })
}

const extendDatatable = async () => {

  let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
  let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
  let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
  let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
  let printButtonTrans = '{{ trans('global.datatables.print') }}'
  let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'

  let languages = {
    'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json'
  };
	
	
  $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, { className: 'btn' })  
  $.extend(true, $.fn.dataTable.defaults, {
    language: {
      url: languages['{{ app()->getLocale() }}']
    },
    columnDefs: [{
        orderable: false,
    //    className: 'select-checkbox',
        targets: 1,
		 render: function(data, type, row, meta){            
                  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
               return data;
            },
		checkboxes: {
               selectRow: true,
			   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
            }
    }, {
        orderable: false,
        searchable: false,
        targets: -1
    }],
    select: {
      style:    'multi+shift',
      selector: 'td:nth-child(2)'
    },
    order: [],
    scrollX: false,
    pageLength: 100,
    dom: 'lBfWrtip<"clear">',
	searching: true,
//	dom: 'lBfWrtp',
    buttons: [
      {
        extend: 'copy',
        className: 'btn-default',
        text: copyButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'csv',
        className: 'btn-default',
        text: csvButtonTrans,
		title: "<?= @$masmenu[0]['menu_name'].'('.session('company_name').')' ?>",
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'excel',
        className: 'btn-default',
        text: excelButtonTrans,
		title: "<?= @$masmenu[0]['menu_name'].'('.session('company_name').')' ?>",
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'pdf',
        className: 'btn-default',
        text: pdfButtonTrans,
		title: "<?= @$masmenu[0]['menu_name'].'('.session('company_name').')' ?>",
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        className: 'btn-default',
        text: printButtonTrans,
		title: "<?= @$masmenu[0]['menu_name'].'('.session('company_name').')' ?>",
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'colvis',
        className: 'btn-default',
        text: colvisButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      }
    ]
  });	
}

function setLocalStorageAnnouncement() {
    var now_date = "<?php echo date('Y-m-d'); ?>";
    if(localStorage.getItem("now_date") == null || localStorage.getItem("now_date") != now_date || localStorage.getItem("announ_old") != localStorage.getItem("announ_count")){
        get_announ_new(global_index);  
    }
    else if(localStorage.getItem("announ_old") != localStorage.getItem("announ_count") && localStorage.getItem("announ_old") != 0){
        get_announ_new(global_index); 
    }
    if("{{ Request::get('s') }}" == ''){
        //pengondisian jika ada survey yg bukan berasal dari mobile
        localStorage.setItem("announ_old", localStorage.getItem("announ_count"));   
    }
    localStorage.setItem("now_date", now_date); 
}

function get_role() {
	var url_role  = '<?= url('setting/responsibility_menu/access_right_user/role') ?>';
	 $.ajax({
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
		dataType:"json",
		url: url_role,
		data: {
			url : global_url_server
		},
		success: function (data) {
			if(data.length > 0){
				access_create = data[0].can_create;
				access_edit = data[0].can_update;
				access_delete = data[0].can_delete;
				access_print = data[0].can_print;
				
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
			}	
		},	
		 error: function (data) {
				get_role()
        }
	});
}
function get_announ_new(n) {	    
            $.ajax({
                url: "<?= url('employee/employee/announcement/get_announcement_new') ?>",
                method: "GET",
                success: function (response) {
					global_index = n;
					if(response.length > 0 && n < response.length){
						get_announ_show(response[n].id_announcement);
					}
					else{
                        if(!("{{ Request::get('s') }}" != '' && id_show.length > 0)){
                            window.location.reload();
                        } 
					}
                },
                error: function (xhr) {
                    get_announ_new(n)
                }
            });
}

function get_announ_show(item) {
            let id_announcement = item;
            $.ajax({
                url: "<?= url('employee/employee/announcement/get_announcement_show') ?>",
                method: "GET",
                data: {id_announcement: id_announcement},
				beforeSend: function () {
					$('#loader_announ').removeClass('hidden');
				},
                success: function (response) {
                    let attachment = '';
					$('#id_announ_relation').val(response.id_announcement).trigger('change');
					if(response.attachment_type == null && response.attachment != null){
                        attachment =  '<a href="../../project/storage/app/public/upload/announcement/'+response.attachment+'" class="btn btn-xs btn-info" style="color:white;" target="_blank"><b>Download Lampiran</b></a>';
                    }
                    else if(response.attachment_type != null && response.attachment != null){
                        const type = {
                            'pdf' : {
                                'data' : 'data:application/pdf;base64,'+response.attachment, 
                                'name' : Date.now() + '.pdf'
                            },
                            'image' : {
                                'data' : 'data:image;base64,'+response.attachment, 
                                'name' : Date.now() + '.jpg'
                            },
                        };
                        attachment =  '<a download="${type[response.attachment_type]["name"]}" href="${type[response.attachment_type]["data"]}" class="btn btn-xs btn-info" style="color:white;"><b>Download Lampiran</b></a>';
                    }
                    $('#attachment_announ').html(attachment);
                    $('#desc_announ').html(response.description);
                    $("#content_letter_announ").html(response.content_letter);
                },
				complete: function(){
					$('#loader_announ').addClass('hidden');
				},
                error: function (xhr) {
                    get_announ_show(item);
                    // swal({
                    //     icon: 'error',
                    //     title: 'Oops...',
                    //     dangerMode: true,
                    //     text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                    // });
                }
            });
            $('#modal_announcement_new').modal('show');
}

$(function () {
	var m = 1;
	$('#announcement_newForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url:"{{ route('announ.save_read') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function(){ 
								$('#modal_announcement_new').modal('hide');
									localStorage.setItem("announ_old", global_index+1);
									get_announ_new(global_index);
								}
							);
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }
                    }					
                });
            
        });
		
	   $(document).on('click', '.close_read', function () {
            if("{{ Request::get('s') }}" != ''){
                $('#modal_announcement_new').hide();
            }

            let formData = $('#announcement_newForm').serializeArray();
                $.ajax({
                    type: 'GET',
                    headers: {
                        Accept: "application/json",
                    },
					url:"{{ route('announ.close_read') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							localStorage.setItem("announ_old", m);
							get_announ_new(m);
                        } 
					m++;
                    }					
                });
        });
    });
    
    $(document).on("click", ".showCampaign", function () {
        let attachment_path = $(this).attr('attachment_path');
        let link = $(this).attr('link');
        $(".div_linkCampaign").hide().html('');

        if(link != ''){
            $(".div_linkCampaign").html(`<button onclick="return false;" class="btn btn-sm btn-success linkCampaign" link="${link}">See more</button>`).show();
        }
        if(attachment_path != ''){
            $("#modalCampaign").modal('show');
            $(".div_campaign").html(`<a href="javascript:;" class="linkCampaign" link="${link}"><img src="${attachment_path}" style="width:100%;" class="img-responsive" /></a>`)
        }
    });

    $(document).on("click", ".linkCampaign", function () {
        let link = $(this).attr('link');
        if(link != ''){
            window.open(link, '_blank');
        }
    });
    
    $(document).on('click', '.collapse_all', function () {
        if($(this).hasClass('menu-opened')){
            $(this).removeClass('menu-opened');
            $(this).addClass('fa-angle-double-down').removeClass('fa-angle-double-up');
            $('.nav-item.has-treeview').removeClass('menu-open');
            $('.nav.nav-treeview').hide();
        } else {
            $(this).addClass('menu-opened');
            $(this).addClass('fa-angle-double-up').removeClass('fa-angle-double-down');
            $('.nav-item.has-treeview').addClass('menu-open');
            $('.nav.nav-treeview').show();
        }
    });

    $(document).on('click', '.--menu-btn-change-responsibility', function () {
        // alert('Click change responsibility WIP#240625 view master.blade.php:703');
        $('#modal_change_responsibility').modal('show');
        $('#modal_change_responsibility').css('padding-right', '0px');
        let html = '';
        responsibilityModules.forEach((module) => {
            html += `<div onclick="changeResponsibilty('${module.module_code}')" class="module-items d-flex flex-column align-items-center mt-3 ml-3 mr-3 mt-md-5 ml-md-5 mr-md-5">
                        <img src="data:image/png;base64,${module.module_icon}" class="rounded shadow p-2 module-items-icon" />
                        <span class="module-caption mt-2 text-center">${module.module_name}</span>
                    </div>`
        });
        $('#modal_change_responsibility').find('.modal-body').html(html);
    });

    function changeResponsibilty(moduleCode = 'HRS') {
        window.location.href = "{{ url('change-responsibility') }}/"+moduleCode;
    }

    function closeResponsibilityModal() {
        $('#modal_change_responsibility').modal('hide');
    }

    </script>

    @yield('adminlte_js')

    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/ColumnFilterWidgets.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.html5.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('vendor/bs-custom-file-input/bs-custom-file-input.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/checkbox/js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/moment/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/dataTables.rowReorder.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/dataTables.responsive.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('vendor/datatables/js/dataTables.fixedColumns.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('vendor/daterangepicker/jquery.daterangepicker.js') }}"></script>
	<script type="text/javascript" src="{{ asset('vendor/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <script src="{{ asset('vendor/splide/dist/js/splide.min.js') }}"></script>
    <script src="{{ asset('vendor/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
	<!-- script src="//cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script -->
    <script src="{{ asset('vendor/spell-number/spell-number-1.0.min.js') }}"></script>
	
	@yield('scripts')
</body>

</html>
