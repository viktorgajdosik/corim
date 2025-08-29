<div class="mt-4 mb-4"
     x-data
     wire:poll.7s="refreshChat">

  {{-- Wrapper with init to show skeleton first --}}
  <div class="position-relative" wire:init="ready">

    {{-- ===== Real content ===== --}}
    <div @unless($isReady) class="d-none" @endunless>
      <div class="chat-card p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-60">Chat</span>
          <small class="text-muted-60">
            @php $count = count($audience); @endphp
            {{ $count }} {{ Str::plural('member', $count) }}
          </small>
        </div>

        {{-- Log --}}
        <div class="chat-log" x-ref="log">
          @forelse ($messages as $m)
            @php
              $me = $m->user_id === auth()->id();
              $to = [];
              if (!$m->is_broadcast) {
                $to = $m->recipients->pluck('name')->take(4)->all();
                if ($m->recipients->count() > 4) $to[] = '…';
              }
            @endphp

            <div class="mb-2 {{ $me ? 'text-end' : 'text-start' }}">
              <div class="meta mb-1">
                <span>{{ $me ? 'You' : $m->sender->name }}</span>
                <span class="ms-2">{{ $m->created_at->format('d/m H:i') }}</span>
                @if (!$m->is_broadcast)
                  <span class="to-tag">to {{ implode(', ', $to) }}</span>
                @endif
              </div>
              <div class="bubble {{ $me ? 'me' : 'them' }}">{{ $m->body }}</div>
            </div>
          @empty
            <div class="text-center text-muted py-3">No messages yet.</div>
          @endforelse
        </div>

        {{-- Input (pill with embedded controls) --}}
        <div
          class="mt-3 chat-input-wrap"
          x-data="{
            sendAll: @entangle('sendToAll').live,
            selected: @entangle('recipientIds').live
          }"
        >

          {{-- Audience picker (opens UP, stays open while clicking inside, doesn't re-render) --}}
          <div class="dropdown dropup audience-anchor">
            <button
              type="button"
              id="audienceBtn"
              class="audience-trigger"
              data-bs-toggle="dropdown"
              data-bs-auto-close="outside"
              aria-expanded="false"
              aria-label="Message audience">
              <i class="fa fa-ellipsis-v"></i>
            </button>

            <div
              id="audienceMenu"
              class="dropdown-menu dropdown-menu-end audience-menu p-2"
              aria-labelledby="audienceBtn"
              wire:ignore
              x-on:click.stop
            >
              <div class="form-check form-switch mb-2">
                <input class="form-check-input"
                       id="sendAll"
                       type="checkbox"
                       x-model="sendAll"
                       @change="if(sendAll){ selected=[] }">
                <label class="form-check-label" for="sendAll">Send to all</label>
              </div>

              <div class="small text-muted mb-2">Or choose specific recipients:</div>
              <div class="d-flex flex-column gap-1" style="max-height: 180px; overflow-y: auto;">
                @foreach($audience as $u)
                  @if($u['id'] !== auth()->id())
                    <div class="form-check">
                      <input class="form-check-input"
                             type="checkbox"
                             id="rcp-{{ $u['id'] }}"
                             value="{{ $u['id'] }}"
                             :disabled="sendAll"
                             x-model="selected"
                             @change="sendAll=false">
                      <label class="form-check-label" for="rcp-{{ $u['id'] }}">{{ $u['name'] }}</label>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>

          {{-- Textbox (dark bg, pill) --}}
          <input
            type="text"
            placeholder="Write a message…"
            class="form-control chat-input"
            wire:model.defer="body"
            @keydown.enter.prevent="$wire.send()"
            x-on:livewire:navigated.window="$nextTick(()=>{ $refs.log?.scrollTop=$refs.log?.scrollHeight })">

          {{-- Send (embedded circle on right) --}}
          <button
            type="button"
            class="btn btn-primary send-inset"
            aria-label="Send message"
            wire:click="send"
            wire:loading.attr="disabled"
            wire:target="send">

            <i class="fa fa-paper-plane" wire:loading.remove wire:target="send"></i>
            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                  wire:loading wire:target="send"></span>
          </button>
        </div>
      </div>
    </div>

  </div>

  <script>
    document.addEventListener('livewire:initialized', () => {
      // initial scroll-to-bottom
      queueMicrotask(() => {
        const el = document.querySelector('[x-ref="log"]');
        if (el) el.scrollTop = el.scrollHeight;
      });

      // after send (triggered from PHP)
      Livewire.on('chat:scrollBottom', () => {
        const el = document.querySelector('[x-ref="log"]');
        if (el) el.scrollTop = el.scrollHeight;
      });
    });
  </script>
</div>
