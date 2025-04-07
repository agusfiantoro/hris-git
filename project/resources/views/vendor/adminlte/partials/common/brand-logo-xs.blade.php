@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand logo-switch {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link logo-switch {{ config('adminlte.classes_brand') }}"
    @endif>

    <?php
		$company_logo = null;
        if(session('company_logo') != null){
			$company_logo = session('company_logo');
        }
	?>
    {{-- Small brand logo --}}
    <!-- img src="{{ asset('vendor/adminlte/dist/img/icon.png') }}"
         alt="{{ config('adminlte.logo_img_alt', 'Borwita') }}"
         class="{{ config('adminlte.logo_img_class', 'brand-image-xl') }} logo-xs" -->

    {{-- Large brand logo --}}
    <img src="data:image;base64,<?= $company_logo; ?>"
         alt="{{ Session::get('company_name') }}"
          class="{{ config('adminlte.logo_img_xl_class', 'brand-image-xs') }} logo-xl"
         style="opacity:.8;" height="28px">
		
</a>
<div class="div_company">
    <b>
        <select name="id_company" id="company_session" class="form-control form-control-sm select2" style="width:100%;">
        </select>
    </b>
</div>