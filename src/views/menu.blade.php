<ul class="nav @if ($item->level > 1) nav-second-level @endif">
  @foreach($menu->children as $item)
    <li @if ($item->isActive(true)) class="active" @endif>
      <a href="{{ $item->getUrl() }}">{!! $item->getIcon() !!} <span class="nav-label">{{ $item->title }}</span>
        @if ($item->hasChildren()) <span class="fa arrow"></span> @endif
      </a>
      @if ($item->hasChildren())
        {{-- Recursively render child items --}}
        @include('vendor.poma.blade-menu.menu', ['menu' => $item])
      @endif
    </li>
  @endforeach
</ul>
