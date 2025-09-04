<div class="mt-4 mb-4" data-chat-root>
  {{-- Run the lightweight init on first paint; hide real UI until isReady --}}
  <div wire:init="ready" class="position-relative">

    {{-- REAL CONTENT (appears only after ready() completes) --}}
    <div @unless($isReady) class="d-none" @endunless x-cloak>

      <div class="chat-card p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-60">Chat</span>
          <small class="text-muted-60">
            @php $count = count($audience); @endphp
            {{ $count }} {{ Str::plural('member', $count) }}
          </small>
        </div>

        {{-- Log --}}
        <div class="chat-log" data-chat-log>
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

            <div class="msg-row {{ $me ? 'me text-end' : 'them text-start' }}" wire:key="msg-{{ $m->id }}">
              <div class="meta mb-1">
                <span>{{ $me ? 'You' : $m->sender->name }}</span>
                <span class="ms-2">{{ $m->created_at->format('d/m H:i') }}</span>
                @if (!$m->is_broadcast)
                  <span class="to-tag">to {{ implode(', ', $to) }}</span>
                @endif
              </div>

              @if ($me)
                <div class="msg-line">
                  <div class="bubble me">{{ $m->body }}</div>

                  @if ($canKebab)
                    <div class="dropdown">
                      <button
                        class="kebab-xs"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="false"
                        aria-expanded="false"
                        aria-label="Message actions">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end msg-menu">
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
                <div class="msg-line">
                  <div class="bubble them">{{ $m->body }}</div>
                </div>
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
            {{-- Audience picker (sticky-open; closes only on input focus or toggle) --}}
            <div class="dropdown dropup audience-anchor">
              <button
                type="button"
                id="audienceBtn"
                class="audience-trigger"
                data-bs-toggle="dropdown"
                data-bs-auto-close="false"
                aria-expanded="false"
                aria-label="Message audience">
                <i class="fa fa-ellipsis-v"></i>
              </button>

              <div
                id="audienceMenu"
                class="dropdown-menu dropdown-menu-end audience-menu p-2"
                aria-labelledby="audienceBtn"
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

                @error('recipientIds')
                  <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Textbox --}}
            <input
              type="text"
              placeholder="Write a message…"
              class="form-control chat-input"
              data-chat-input
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
      {{-- /REAL CONTENT --}}
    </div>
    {{-- /ready gate --}}
  </div>
</div>
