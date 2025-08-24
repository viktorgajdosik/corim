<li class="nav-item position-relative">
  <a class="nav-link {{ request()->is('notifications') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
    <i class="fa fa-bell"></i>
    @if ($count > 0)
      <span
        class="position-absolute badge rounded-pill bg-danger"
        style="
          top:8px; right:8px;
          font-size:.6rem;
          padding:.1rem .3rem;
          line-height:1;
          min-width:1rem;
          text-align:center;
        "
      >
        {{ $count }}
      </span>
    @endif
  </a>
</li>

