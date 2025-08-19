<nav aria-label="breadcrumb">
  <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
    <li class="breadcrumb-item">
        <a href="{{ url('/') }}">Inicio</a>
    </li>
    @foreach ($breadcrumbs as $label => $url)
        @if ($loop->last)
            <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
        @else
            <li class="breadcrumb-item">
                <a href="{{ $url }}">{{ $label }}</a>
            </li>
        @endif
    @endforeach
  </ol>
</nav>
