@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop

  <style>
.card0 {
    box-shadow: 0px 4px 8px 0px #757575;
    border-radius: 0px
}

.card2 {
    margin: 0px 40px
}

.logo {
    width: 150px;
    height: 25px;
    margin-top: 20px;
    margin-left: 35px
}

.image {
    width: 500px;
}

.border-line {
    border-right: 1px solid #dddddd
}

.line {
    height: 1px;
    width: 42%;
    background-color: #E0E0E0;
    margin-top: 15px
}

.or {
    width: 15%;
    font-weight: bold;
	font-size:18px;
}

.text-sm {
    font-size: 14px !important
}

::placeholder {
    color: #BDBDBD;
    opacity: 1;
    font-weight: 300
}


a {
    color: inherit;
    cursor: pointer
}
.card {
    min-height: 200px;
}

@media screen and (max-width: 991px) {
    .mobile {
        display: none;
    }
	.logo {
        margin-left: 20px;
    }

    .image {
        width: 550px;
    }

    .card2 {
        border-top: 1px solid #EEEEEE !important;
        margin: 0px 15px
    }
}</style>
@section('body')
<?php
    $param = \Request::segment(1);
    $title_content = [
        'login' => 'Login',
        'reset' => 'Reset',
        'forgot_password' => 'Reset',
        'home' => 'Login',
        '' => 'Login',
    ];
?>
<div class="container px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
    <div class="card card-danger card-outline">
        <div class="row d-flex">
		<div class="col-lg-12"> 
				<img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}" class="logo">
		</div>
            <div class="mobile col-lg-6">
                <div class="row px-3 justify-content-center mt-4 mb-5 border-line"> <img src="{{ asset('vendor/adminlte/dist/img/HRIS-logo.png') }}" class="image"> </div>
            </div>
			<div class="col-lg-5" style="margin:50px 0 0 30px;">			
                    <div class="row px-3 mb-4">
                        <div class="line"></div> 
                        <div class="or text-center">{{ @$title_content[$param]}}</div>
                        <div class="line"></div>
                    </div>
                <div class="pb-5">
                   {{-- Card Body --}}
            <div class="card-body {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
                @yield('auth_body')
            </div>

            {{-- Card Footer --}}
            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                    @yield('auth_footer')
                </div>
            @endif

                </div>
            </div>
				
         </div>
    </div>
       
    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
