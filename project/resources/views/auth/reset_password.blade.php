@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@section('plugins.Select2', true)
@stop

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

    <form action="{{ url('/reset/save') }}" method="post">
        {{ csrf_field() }}

        <h4>Hai, {{ @$data->name }}</h4>
        
        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="New Password" autofocus required>
            <input type="hidden" value="{{ @$data->id_user }}" name="id_user" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-key"></span>
                </div>
            </div>
        </div>

        {{-- Submit button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-share-square"></span>
            Submit
        </button>

    </form>

@stop

@section('js')
<script type="text/javascript">
    $(function () {      
    });
	
</script>
@stop