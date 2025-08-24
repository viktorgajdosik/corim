<section x-data>

  <x-card-form>
    <div wire:init="ready"
         class="position-relative"
         style="min-height: 80px; border-radius:.5rem; overflow:hidden;">
      {{-- Initial skeleton overlay (once) --}}
      <div class="task-skeleton-overlay" wire:loading wire:target="ready" aria-hidden="true">
        <div class="skeleton-line w-100 mb-2"></div>
        <div class="skeleton-line w-75 mb-2"></div>
        <div class="skeleton-line w-25"></div>
      </div>

      {{-- Real content --}}
      <div @unless($isReady) class="d-none" @endunless>
        @forelse ($notifications as $n)
          @php $ts = optional($n->updated_at)->timestamp; @endphp

          <div
            class="notify-item d-flex justify-content-between align-items-center"
            wire:key="n-{{ $n->id }}-{{ $ts }}"
            role="button"
            tabindex="0"
            aria-label="Open notification: {{ $n->title }}"
            x-data="{ loading:false }"
            @click="
              if (!loading) {
                loading = true;
                $wire.markSeenAndGo({{ $n->id }});
              }
            "
            @keydown.enter.prevent="
              if (!loading) {
                loading = true;
                $wire.markSeenAndGo({{ $n->id }});
              }
            "
            :class="loading ? 'opacity-75 pe-none' : ''"
          >
            {{-- Left content --}}
            <div class="me-3">
              <x-card-heading class="mb-1 d-inline">{{ $n->title }}</x-card-heading>
              @if (is_null($n->seen_at))
                <small class="text-info ms-2">new</small>
              @endif

              @if ($n->body)
                <x-text class="mb-1">{{ $n->body }}</x-text>
              @endif

              <small class="text-secondary">{{ $n->created_at->diffForHumans() }}</small>
            </div>

            {{-- Right: tiny spinner while loading --}}
            <div class="ms-3 d-flex align-items-center" style="min-width: 1.25rem; min-height: 1rem;">
              <span x-show="loading" x-cloak class="spinner-grow spinner-grow-sm align-middle"></span>
            </div>
          </div>

          @unless($loop->last)
            <hr class="my-2 border-secondary opacity-25">
          @endunless
        @empty
          <x-text class="text-white">You have no notifications yet.</x-text>
        @endforelse
      </div>
    </div>
  </x-card-form>
</section>
