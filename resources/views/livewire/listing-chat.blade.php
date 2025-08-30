<div class="mt-4 mb-4"
     x-data
     @if(!$editingId) wire:poll.7s="refreshChat" @endif>

  <style>
    .chat-card{border-radius:.75rem;background:#151515;border:1px solid rgba(255,255,255,.1)}
    .chat-log{max-height:360px;overflow-y:auto;padding:.75rem;scroll-behavior:smooth}

    .bubble{display:inline-block;padding:.5rem .75rem;border-radius:.75rem;max-width:85%;word-wrap:break-word;white-space:pre-wrap;color:#fff}
    .bubble.me{background:rgba(157,157,157,.18);border:1px solid rgba(255,255,255,.1)}
    .bubble.them{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1)}
    .meta{font-size:.75rem;color:rgba(255,255,255,.6)}
    .to-tag{font-size:.7rem;color:rgba(255,255,255,.85);background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:.5rem;padding:.1rem .4rem;margin-left:.4rem}

    .msg-row { margin-bottom: 1rem; }

    .kebab-xs{background:transparent;border:none;color:rgba(255,255,255,.75);padding:2px;line-height:0}
    .kebab-xs:hover{color:#fff}
    .kebab-xs .fa-ellipsis-v{font-size:14px}

    .chat-input-wrap{position:relative;height:52px}
    .chat-input{
      height:100%;border-radius:999px;
      background:rgb(44,44,44);color:#fff;border:1px solid rgba(255,255,255,.12);
      padding-left:56px;
      padding-right:56px;
    }
    .chat-input::placeholder{color:rgba(255,255,255,.55)}
    .chat-input:focus,.chat-input:focus-visible{
      background:rgb(44,44,44);color:#fff;border-color:rgba(255,255,255,.2);box-shadow:none;
    }

    .edit-wrap{border:1px solid rgba(255,255,255,.18);border-radius:12px;background:#0f0f10;padding:8px}
    .edit-topbar{display:flex;align-items:center;justify-content:space-between;padding:4px 6px 6px 6px}
    .edit-topbar .label{color:#fff;font-weight:600;font-size:.9rem}
    .edit-topbar .close-btn{background:transparent;border:none;color:#fff;line-height:0;padding:4px}
    .edit-topbar .close-btn:hover{opacity:.85}

    .audience-anchor{
      position:absolute;left:8px;top:50%;transform:translateY(-50%);
      display:flex;align-items:center;justify-content:center;height:36px;width:36px;
    }
    .audience-trigger{
      width:36px;height:36px;border-radius:999px;
      background:rgb(66,66,66);color:#fff;
      display:flex;align-items:center;justify-content:center;border:none;padding:0;line-height:0;
    }
    .audience-trigger .fa-ellipsis-v{font-size:15px}

    .send-inset{
      position:absolute;right:8px;top:50%;transform:translateY(-50%);
      width:36px;height:36px;border-radius:999px;
      display:inline-flex;align-items:center;justify-content:center;padding:0;
    }

    .audience-menu,
    .msg-menu {
      --bs-dropdown-bg:#0f0f10;
      --bs-dropdown-color:#fff;
      --bs-dropdown-link-color:#fff;
      --bs-dropdown-link-hover-color:#fff;
      --bs-dropdown-link-hover-bg:rgba(255,255,255,.08);
      --bs-dropdown-border-color:rgba(255,255,255,.18);
      background-color:var(--bs-dropdown-bg);
      color:var(--bs-dropdown-color);
      border:1px solid var(--bs-dropdown-border-color);
      z-index:2147483647;
      min-width:280px;
    }
    .audience-menu .form-check{user-select:none}
  </style>

  <div class="chat-card p-3" @unless($isReady) wire:init="ready" @endunless>
    <div class="d-flex align-items-center justify-content-between mb-2">
      <span class="text-muted-60">Chat</span>
      <small class="text-muted-60">
        @php $count = count($audience); @endphp
        {{ $count }} {{ Str::plural('member', $count) }}
      </small>
    </div>

    {{-- Log --}}
    <div class="chat-log"
         x-ref="log"
         x-init="
           $nextTick(() => {
             const el = $refs.log;
             const scroll = () => requestAnimationFrame(() => { el.scrollTop = el.scrollHeight });
             scroll();
             const mo = new MutationObserver(scroll);
             mo.observe(el, { childList: true, subtree: true });
           })
         ">
      @forelse ($messages as $m)
        @php
          $me = $m->user_id === auth()->id();
          $isLastVisible = ($lastVisibleId !== null && $m->id === $lastVisibleId);
          $canKebab = $me && $isLastVisible;
          $to = [];
          if (!$m->is_broadcast) {
            $to = $m->recipients->pluck('name')->take(4)->all();
            if ($m->recipients->count() > 4) $to[] = '…';
          }
        @endphp

        <div class="msg-row {{ $me ? 'text-end' : 'text-start' }}" wire:key="msg-{{ $m->id }}">
          <div class="meta mb-1">
            <span>{{ $me ? 'You' : $m->sender->name }}</span>
            <span class="ms-2">{{ $m->created_at->format('d/m H:i') }}</span>
            @if (!$m->is_broadcast)
              <span class="to-tag">to {{ implode(', ', $to) }}</span>
            @endif
          </div>

          @if ($me)
            <div class="d-inline-flex align-items-center gap-1">
              <div class="bubble me">{{ $m->body }}</div>

              @if ($canKebab)
                <div class="dropdown">
                  <button class="kebab-xs"
                          type="button"
                          id="msgKebab-{{ $m->id }}"
                          data-bs-toggle="dropdown"
                          data-bs-auto-close="false"
                          aria-expanded="false"
                          aria-label="Message actions">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end msg-menu"
                      id="msgMenu-{{ $m->id }}"
                      aria-labelledby="msgKebab-{{ $m->id }}">
                    <li>
                      <button class="dropdown-item" wire:click="startEdit({{ $m->id }})">
                        <i class="fa fa-pencil me-2"></i> Edit
                      </button>
                    </li>
                    <li>
                      <button class="dropdown-item text-danger"
                              x-on:click.prevent="if (confirm('Delete this message?')) { $wire.deleteMessage({{ $m->id }}) }">
                        <i class="fa fa-trash me-2"></i> Delete
                      </button>
                    </li>
                  </ul>
                </div>
              @endif
            </div>
          @else
            <div class="bubble them">{{ $m->body }}</div>
          @endif
        </div>
      @empty
        <div class="text-center text-muted py-3">No messages yet.</div>
      @endforelse
    </div>

    {{-- Input area (with optional Edit frame) --}}
    <div class="mt-3 {{ $editingId ? 'edit-wrap' : '' }}">
      @if($editingId)
        <div class="edit-topbar">
          <span class="label">Edit</span>
          <button type="button" class="close-btn" wire:click="cancelEdit" aria-label="Cancel edit">
            <i class="fa fa-times"></i>
          </button>
        </div>
      @endif

      <div
        class="chat-input-wrap"
        x-data="{
          sendAll: @entangle('sendToAll').live,
          selected: @entangle('recipientIds').live
        }"
      >
  <div class="dropdown dropup audience-anchor">
  <button
    type="button"
    id="audienceBtn"
    class="audience-trigger"
    data-bs-toggle="dropdown"
    data-bs-auto-close="false"   {{-- keep open on inside/outside clicks --}}
    aria-expanded="false"
    aria-label="Message audience">
    <i class="fa fa-ellipsis-v"></i>
  </button>

  {{-- ⬇️ Tell Livewire to leave this DOM alone so it stays open while toggling checkboxes --}}
  <div
    id="audienceMenu"
    class="dropdown-menu dropdown-menu-end audience-menu p-2"
    aria-labelledby="audienceBtn"
    x-on:click.stop
    wire:ignore
  >
    <div class="form-check form-switch mb-2">
      <input class="form-check-input"
             id="sendAll"
             type="checkbox"
             x-model="sendAll"
             @change="if(sendAll){ selected=[] }">
      <label class="form-check-label" for="sendAll">Send to all</label>
    </div>

    <div class="d-flex flex-column gap-1" style="max-height: 180px; overflow-y: auto;">
      @foreach($audience as $u)
        @if($u['id'] !== auth()->id())
          <div class="form-check" wire:key="aud-{{ $u['id'] }}">
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

{{-- Keep validation message OUTSIDE the wire:ignore block so it can still update --}}
@error('recipientIds')
  <div class="text-danger small mt-2">{{ $message }}</div>
@enderror


        {{-- Textbox --}}
        <input
          type="text"
          placeholder="Write a message…"
          class="form-control chat-input"
          x-ref="input"
          wire:model.defer="body"
          @keydown.enter.prevent="$wire.{{ $editingId ? 'saveEdit()' : 'send()' }}"
        >

        {{-- Send / Save button --}}
        <button
          type="button"
          class="btn btn-primary send-inset"
          :aria-label="'{{ $editingId ? 'Save edit' : 'Send message' }}'"
          wire:click="{{ $editingId ? 'saveEdit' : 'send' }}"
          wire:loading.attr="disabled"
          wire:target="send,saveEdit"
        >
          <span wire:loading.remove wire:target="send,saveEdit">
            @if($editingId)
              <i class="fa fa-check"></i>
            @else
              <i class="fa fa-paper-plane"></i>
            @endif
          </span>
          <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                wire:loading wire:target="send,saveEdit"></span>
        </button>
      </div>
    </div>
  </div>
</div>
