@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@section('plugins.Select2', true)
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
@php( $login_url = $login_url ? route($login_url) : '' )
@php( $register_url = $register_url ? route($register_url) : '' )
@php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
@php( $login_url = $login_url ? url($login_url) : '' )
@php( $register_url = $register_url ? url($register_url) : '' )
@php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_body')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

<form action="{{ $login_url }}" method="post">
    {{ csrf_field() }}

    {{-- Email field --}}
    <div class="input-group mb-3">
        <input type="text" name="username" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" placeholder="Username" autofocus>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>
        @if($errors->has('username'))
        <div class="invalid-feedback">
            <strong>{{ $errors->first('username') }}</strong>
        </div>
        @endif
    </div>

    {{-- Password field --}}
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
               placeholder="Password">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>
        @if($errors->has('password'))
        <div class="invalid-feedback">
            <strong>{{ $errors->first('password') }}</strong>
        </div>
        @endif
    </div>

    {{-- Company field --}}
    <div class="input-group mb-3">   
    
		<div class="input-group">
			<select name="id_company" id="company" class="form-control select2 {{ $errors->has('id_company') ? 'is-invalid' : '' }}"></select>
			<input type="hidden" name="company_name" id="company_name" class="form-control" style="width: 100%;">					
			<div class="input-group-append">
				<span class="input-group-text">
					<span class="fas fa-search"></span>
				</span>
			</div>
			@if($errors->has('id_company'))
			<div class="invalid-feedback">
				<strong>{{ $errors->first('id_company') }}</strong>
			</div>
			@endif
		</div>
        <!-- input type="hidden" name="company_logo" id="company_logo" class="form-control" style="width: 100%;" -->
        
    </div>

    {{-- Login field --}}
    <div class="row">
        <div class="col-7">
            <div class="icheck-danger">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>
        </div>
        <div class="col-5">
            <button type=submit class="btn btn-block btn-flat btn-danger">
                <span class="fas fa-sign-in-alt"></span>
                Login
            </button>
        </div>
    </div>

    {{-- Forgot Password--}}
    <div class="row" style="padding-top:20px;">
        <div class="col align-self-end">
            <a href="{{ route('forgotPassword') }}" style="color:red;">Forgot Your Password ?</a>
        </div>
    </div>

</form>
@stop

@section('js')
<script type="text/javascript">
    $(function () {      
		getCompany();
    });
	function getCompany(){
		$.getJSON('<?= url('getCompany') ?>', function (data) {
            $('#company').select2({
                placeholder: "Company",
            //    allowClear: true,
                data: data
            }).on('change', function (e) {
                $('#company_name').val($(this).select2('data')[0].text);
           //     $('#company_logo').val($(this).select2('data')[0].company_logo);
            }).trigger('change');;
        }).fail(function (data) { // Call failed
            getCompany();
		});
	}
</script>
@stop