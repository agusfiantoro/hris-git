<li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#"
        @if(config('adminlte.sidebar_collapse_remember'))
            data-enable-remember="true"
        @endif
        @if(!config('adminlte.sidebar_collapse_remember_no_transition'))
            data-no-transition-after-reload="false"
        @endif
        @if(config('adminlte.sidebar_collapse_auto_size'))
            data-auto-collapse-size="{{ config('adminlte.sidebar_collapse_auto_size') }}"
        @endif>
        <i class="fas fa-bars"></i>
        <span class="sr-only">{{ __('adminlte::adminlte.toggle_navigation') }}</span>
    </a>
</li>

<li class="nav-item dropdown notification" style="margin-left:-15px;">
    
</li>
<li class="nav-item dropdown notification_announ" style="margin-left:-15px;">
   
</li>
<li class="nav-item dropdown notification_settlement" style="margin-left:-15px;">

</li>

<li style="margin-left:-10px;">
	<a href="http://docs.borwita.co.id/hris/" class="nav-link" target="_blank" title="Guide Book">
           <i class="fa fa-book" alt="Guide Book"></i>
    </a>
</li>
