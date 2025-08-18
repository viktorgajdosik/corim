// resources/js/app.js
import * as bootstrap from 'bootstrap';
window.bootstrap = window.bootstrap || bootstrap;

// Initialize popovers
$(document).ready(function () {
  $('[data-bs-toggle="popover"]').popover({
    html: true,
    trigger: 'hover',
    container: 'body'
  });
});

// DEBUG guard so hooks aren’t installed twice
window.__LW_DEBUG_INSTALLED__ = window.__LW_DEBUG_INSTALLED__ || false;

document.addEventListener('alpine:init', () => {
  console.debug('[ui] Alpine store init');

  // ===== Spinner UI store =====
  Alpine.store('ui', {
    createLoading: false,
    updateLoading: {}, // { [taskId]: boolean }
    modLoading: {},    // { [taskId]: boolean }

    startCreate() { this.createLoading = true;  console.debug('[ui] startCreate'); },
    stopCreate()  { this.createLoading = false; console.debug('[ui] stopCreate'); },

    startUpdate(id) { this.updateLoading[id] = true;  console.debug('[ui] startUpdate', id); },
    stopUpdate(id)  { this.updateLoading[id] = false; console.debug('[ui] stopUpdate', id); },

    startMod(id) { this.modLoading[id] = true;  console.debug('[ui] startMod', id); },
    stopMod(id)  { this.modLoading[id] = false; console.debug('[ui] stopMod', id); },

    stopAll() {
      this.createLoading = false;
      this.updateLoading = {};
      this.modLoading = {};
      console.debug('[ui] stopAll');
    },
  });

  // ===== Persisted panel state (details/edit/mod) =====
  Alpine.store('state', {
    details: {}, edit: {}, mod: {},
    setDetails(id, open) { this.details[id] = !!open; },
    toggleDetails(id) { this.setDetails(id, !this.details[id]); },
    setEdit(id, open) { this.edit[id] = !!open; },
    toggleEdit(id) { this.setEdit(id, !this.edit[id]); },
    setMod(id, open) { this.mod[id] = !!open; },
    toggleMod(id) { this.setMod(id, !this.mod[id]); },
  });

  // Safer Alpine helper for task cards (optional)
  Alpine.data('taskCardState', (id) => ({
    id,
    get _state() {
      try {
        return Alpine.store('state') ?? { details:{}, edit:{}, mod:{},
          setDetails(){}, setEdit(){}, setMod(){} };
      } catch {
        return { details:{}, edit:{}, mod:{},
          setDetails(){}, setEdit(){}, setMod(){} };
      }
    },
    get detailsOpen() { return !!this._state.details[this.id]; },
    set detailsOpen(v) { this._state.setDetails(this.id, !!v); },
    get editOpen() { return !!this._state.edit[this.id]; },
    set editOpen(v) { this._state.setEdit(this.id, !!v); },
    get modOpen() { return !!this._state.mod[this.id]; },
    set modOpen(v) { this._state.setMod(this.id, !!v); },
  }));

  // ===== Livewire debug hooks (optional) =====
  if (!window.__LW_DEBUG_INSTALLED__ && window.Livewire?.hook) {
    window.__LW_DEBUG_INSTALLED__ = true;
    console.debug('[lw] installing global hooks');
    Livewire.hook('message.sent',     (m,c) => console.debug('[lw] message.sent',     { comp: c.id, method: m?.updateQueue?.[0]?.payload?.method }));
    Livewire.hook('message.failed',   (m,c) => console.debug('[lw] message.failed',   { comp: c.id }));
    Livewire.hook('message.received', (m,c) => console.debug('[lw] message.received', { comp: c.id }));
    Livewire.hook('message.processed',(m,c) => console.debug('[lw] message.processed',{ comp: c.id }));
  }

  // ===== DOM-confirmed stop logic (success path) =====
  function waitForTaskVersion(taskId, updatedAt, { timeout = 5000 } = {}) {
    return new Promise((resolve, reject) => {
      const selector = `[data-task-id="${taskId}"][data-updated-at="${updatedAt}"]`;

      // Fast path
      if (document.querySelector(selector)) {
        requestAnimationFrame(() => requestAnimationFrame(resolve));
        return;
      }

      // Observe DOM for the new version
      const obs = new MutationObserver(() => {
        if (document.querySelector(selector)) {
          obs.disconnect();
          requestAnimationFrame(() => requestAnimationFrame(resolve));
        }
      });
      obs.observe(document.body, { childList: true, subtree: true });

      // Safety timeout
      setTimeout(() => {
        try { obs.disconnect(); } catch {}
        reject(new Error(`Timeout waiting for task ${taskId} @ ${updatedAt}`));
      }, timeout);
    });
  }

  // Single place where we stop spinners AND show toast after paint (success path)
  window.addEventListener('taskDomShouldReflect', async (e) => {
    const { taskId, updatedAt, flash } = e.detail || {};
    if (taskId == null || updatedAt == null) return;

    try {
      await waitForTaskVersion(taskId, String(updatedAt));
      const ui = Alpine.store('ui');

      // Stop only relevant spinners
      ui.stopUpdate?.(taskId);
      ui.stopMod?.(taskId);
      ui.stopCreate?.();

      console.debug('[ui] DOM confirmed for task', taskId, '@', updatedAt);

      // Toast AFTER DOM confirm (if provided)
      if (flash?.message) {
        const { message, type = 'success', delay = 3000 } = flash;
        const container = document.querySelector('.toast-container');
        if (container) {
          const bg = type === 'error' ? 'danger'
                  : type === 'warning' ? 'warning'
                  : type === 'info' ? 'info'
                  : 'success';

          const el = document.createElement('div');
          el.className = `toast align-items-center text-bg-${bg} border-0`;
          el.setAttribute('role', 'alert');
          el.setAttribute('aria-live', 'assertive');
          el.setAttribute('aria-atomic', 'true');
          el.innerHTML = `
            <div class="d-flex">
              <div class="toast-body">${message}</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto"
                      data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
          `;

          container.appendChild(el);
          const toast = new bootstrap.Toast(el, { delay, autohide: true });
          toast.show();
          el.addEventListener('hidden.bs.toast', () => el.remove());
        }
      }
    } catch (err) {
      console.debug('[ui] DOM confirm timeout:', err?.message);
      Alpine.store('ui').stopAll?.(); // never leave UI stuck
    }
  });
});

window.taskCard = function taskCard(taskId) {
  return {
    detailsOpen: false,
    editOpen: false,
    modOpen: false,
    init() {
      console.log(`✅ Alpine initialized for task ${taskId}`);

      const setup = () => {
        const isMobile = window.innerWidth < 768;
        const cardRoot = this.$el;

        // Dispose existing popovers in this card
        cardRoot.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
          const inst = window.bootstrap?.Popover?.getInstance(el);
          if (inst) inst.dispose();
        });

        // (Re)init popovers only on mobile
        if (isMobile) {
          cardRoot.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
            new window.bootstrap.Popover(el);
          });
        }
      };

      setup();
      window.addEventListener('resize', setup);
    }
  };
};

// Usage in Blade: x-data="studentTaskCard({{ $task->id }})" x-init="init()"
window.studentTaskCard = function studentTaskCard(taskId) {
  return {
    detailsOpen: false,
    init() {
      console.log(`🎓 Alpine initialized for student task ${taskId}`);

      const setup = () => {
        const isMobile = window.innerWidth < 768;
        const cardRoot = this.$el;

        // Dispose existing popovers in this card
        cardRoot.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
          const inst = window.bootstrap?.Popover?.getInstance(el);
          if (inst) inst.dispose();
        });

        // (Re)init popovers only on mobile
        if (isMobile) {
          cardRoot.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
            new window.bootstrap.Popover(el);
          });
        }
      };

      setup();
      window.addEventListener('resize', setup);
    }
  };
};
