<li @if(isset($item['id'])) id="{{ $item['id'] }}" @endif class="nav-header {{ $item['class'] ?? '' }}">

    {{ is_string($item) ? $item : $item['header'] }}
    <a href="javascript:;"><i class="fas fa-angle-double-down float-right collapse_all" style="padding: 0.3rem 0.5rem;"></i></a>
</li>