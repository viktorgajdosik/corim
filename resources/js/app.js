// resources/js/app.js
import * as bootstrap from 'bootstrap';
window.bootstrap = window.bootstrap || bootstrap;

/* --- Popovers (vanilla, no jQuery) --- */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-bs-toggle="popover"]').forEach((el) => {
    new bootstrap.Popover(el, {
      html: true,
      trigger: 'hover',
      container: 'body'
    });
  });
});

/* ========================================================================== */
/* ============================  SIMPLE CHAT HELPERS  ======================= */
/* ========================================================================== */

/** Minimal, generic scroll-to-bottom (initial + on Livewire events) */
function scrollChatLogToBottom() {
  const log = document.querySelector('[x-ref="log"]');
  if (log) { log.scrollTop = log.scrollHeight; }
}
document.addEventListener('DOMContentLoaded', scrollChatLogToBottom);
window.addEventListener('chat:scrollBottom', scrollChatLogToBottom);

/** Focus input when Livewire asks */
window.addEventListener('chat:focusInput', () => {
  const input = document.querySelector('.chat-card .chat-input');
  if (input) input.focus();
});

/** When chat input is focused, explicitly close any dropdowns within the same chat card */
document.addEventListener('focusin', (e) => {
  const input = e.target.closest('.chat-input');
  if (!input) return;
  const card = input.closest('.chat-card');
  if (!card) return;
  card.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((btn) => {
    const dd = bootstrap.Dropdown.getOrCreateInstance(btn, { autoClose: false });
    dd.hide();
  });
});

/* ========================================================================== */
/* ============================  ALPINE / STORES  ============================ */
/* ========================================================================== */

window.__LW_DEBUG_INSTALLED__ = window.__LW_DEBUG_INSTALLED__ || false;

document.addEventListener('alpine:init', () => {
  console.debug('[ui] Alpine store init');

  Alpine.store('ui', {
    // tasks
    createLoading: false,
    updateLoading: {}, // { taskId: bool }
    modLoading: {},    // { taskId: bool }
    deleteLoading: {}, // { taskId: bool }

    // applications/participants — separate maps per action
    appAcceptLoading: {}, // { appId: bool }
    appDenyLoading:   {}, // { appId: bool }
    appRemoveLoading: {}, // { appId: bool }

    // student-side apply loading per listing
    appApplyLoading: {},  // { listingId: bool }

    // listing edit loading per listing
    listingUpdateLoading: {}, // { listingId: bool }

    // profile edit & password edit loading per user
    profileUpdateLoading: {},   // { userId: bool }
    passwordUpdateLoading: {},  // { userId: bool }

    // participant ghosts per listing
    participantGhostsByListing: {},

    ensureListing(id) {
      if (this.participantGhostsByListing[id] == null) this.participantGhostsByListing[id] = 0;
    },
    addParticipantGhost(id) {
      this.ensureListing(id);
      this.participantGhostsByListing[id] += 1;
    },
    removeParticipantGhost(id) {
      this.ensureListing(id);
      this.participantGhostsByListing[id] = Math.max(0, this.participantGhostsByListing[id] - 1);
    },

    // TASK helpers
    startCreate() { this.createLoading = true; },
    stopCreate()  { this.createLoading = false; },

    startUpdate(id) { this.updateLoading[id] = true; },
    stopUpdate(id)  { this.updateLoading[id] = false; },

    startMod(id) { this.modLoading[id] = true; },
    stopMod(id)  { this.modLoading[id] = false; },

    // Delete helpers
    startDelete(id) { this.deleteLoading[id] = true; },
    stopDelete(id)  { this.deleteLoading[id] = false; },

    // APP helpers
    startAppAccept(id) { this.appAcceptLoading[id] = true; },
    stopAppAccept(id)  { this.appAcceptLoading[id] = false; },

    startAppDeny(id) { this.appDenyLoading[id] = true; },
    stopAppDeny(id)  { this.appDenyLoading[id] = false; },

    startAppRemove(id) { this.appRemoveLoading[id] = true; },
    stopAppRemove(id)  { this.appRemoveLoading[id] = false; },

    // Apply helpers
    startAppApply(listingId) { this.appApplyLoading[listingId] = true; },
    stopAppApply(listingId)  { this.appApplyLoading[listingId] = false; },

    // Listing edit helpers
    startListingUpdate(listingId) { this.listingUpdateLoading[listingId] = true; },
    stopListingUpdate(listingId)  { this.listingUpdateLoading[listingId] = false; },

    // Profile helpers
    startProfileUpdate(userId) { this.profileUpdateLoading[userId] = true; },
    stopProfileUpdate(userId)  { this.profileUpdateLoading[userId] = false; },

    startPasswordUpdate(userId) { this.passwordUpdateLoading[userId] = true; },
    stopPasswordUpdate(userId)  { this.passwordUpdateLoading[userId] = false; },

    stopAll() {
      this.createLoading = false;
      this.updateLoading = {};
      this.modLoading = {};
      this.appAcceptLoading = {};
      this.appDenyLoading   = {};
      this.appRemoveLoading = {};
      this.deleteLoading = {};
      this.appApplyLoading = {};
      this.listingUpdateLoading = {};
      this.profileUpdateLoading = {};
      this.passwordUpdateLoading = {};
      // participantGhostsByListing left intact
    },
  });

  // ========== ORG ANALYTICS ==========
  Alpine.data('orgAnalyticsComponent', (payload) => ({
    charts: {
      openListings: null,
      listings: null,
      tasks: null,
      participantsAccepted: null,
      usersAll: null,
    },
    data: payload,
    demoMode: false,
    scopes: {
      openListings: 'all',
      listings: 'all',
      tasks: 'all',
      participantsAccepted: 'all',
      usersAll: 'all',
    },
    stateObserver: null,

    async init() {
      if (this.$el.__oaInit) return;
      this.$el.__oaInit = true;

      await this.ensureChartJs();
      this.readState();
      await this.nextPaint();
      this.renderAll();
      this.observeState();

      if (window.Livewire?.hook) {
        Livewire.hook('message.processed', async (m, c) => {
          const root = this.$el.closest('[wire\\:id]');
          if (root && root.getAttribute('wire:id') === c.id) {
            if (!this.demoMode) {
              this.readState();
              this.observeState();
            }
            await this.nextPaint();
            this.renderAll();
          }
        });
      }
    },

    setScope(detail) {
      const { key, scope } = detail || {};
      if (!key || !scope) return;
      if (!(key in this.scopes)) return;
      this.scopes[key] = scope;
      this.renderAll();
    },

    ensureChartJs() {
      if (window.Chart) return Promise.resolve();
      return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js';
        s.async = true;
        s.onload = resolve;
        s.onerror = () => reject(new Error('Chart.js failed'));
        document.head.appendChild(s);
      });
    },

    nextPaint() { return new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r))); },

    readState() {
      try {
        const el = this.$el.querySelector('script[x-ref="state"]');
        const json = el?.textContent;
        if (json) this.data = JSON.parse(json);
      } catch {}
    },

    observeState() {
      try { this.stateObserver?.disconnect?.(); } catch {}
      const el = this.$el.querySelector('script[x-ref="state"]');
      if (!el) return;
      this.stateObserver = new MutationObserver(() => { this.readState(); this.renderAll(); });
      this.stateObserver.observe(el, { childList: true, characterData: true, subtree: true });
    },

    hexToRgba(hex, alpha) {
      const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex || '');
      if (!m) return `rgba(153,153,153,${alpha})`;
      const r = parseInt(m[1], 16), g = parseInt(m[2], 16), b = parseInt(m[3], 16);
      return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    },

    destroyChart(canvas) {
      const e = window.Chart?.getChart?.(canvas);
      if (e) try { e.destroy(); } catch {}
    },

    renderLine(canvas, key, labels, datasets) {
      if (!canvas || !window.Chart || !labels) return;
      if (!document.contains(canvas)) return;

      const parent = canvas.parentElement;
      canvas.width  = (parent?.clientWidth || 600);
      canvas.height = (parent?.clientHeight || 300);
      this.destroyChart(canvas);

      if (!datasets?.length) return;

      const chartDatasets = datasets
        .map(d => ([
          d.label || '',
          (d.data || []).map(v => Number(v) || 0),
          d.color ? this.hexToRgba(d.color, 0.95) : 'rgba(255,255,255,.9)',
          d.color ? this.hexToRgba(d.color, 0.25) : 'rgba(255,255,255,.25)'
        ]))
        .map(([label, data, borderColor, backgroundColor]) => ({
          label, data, fill: false, borderColor, backgroundColor,
          borderWidth: 2, pointRadius: 2, tension: 0.25
        }));

      const ctx = canvas.getContext('2d'); if (!ctx) return;
      this.charts[key] = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: chartDatasets },
        options: {
          responsive: true, maintainAspectRatio: false, animation: false,
          plugins: {
            legend: { display: true, labels: { color: 'rgba(255,255,255,.85)', boxWidth: 12 } },
            tooltip: { intersect: false, mode: 'index' }
          },
          scales: {
            x: { ticks: { color: 'rgba(255,255,255,.85)', font: { size: 11 }, maxRotation: 0, autoSkip: true }, grid: { display: false } },
            y: { ticks: { color: 'rgba(255,255,255,.7)', precision: 0, font: { size: 11 } }, grid: { color: 'rgba(255,255,255,.08)' }, beginAtZero: true }
          }
        }
      });
    },

    pickDatasets(block, scopeKey) {
      const scope = this.scopes[scopeKey];
      if (scope === 'mine') {
        const filtered = this.filterDatasetsForDept(block?.datasets, this.data.currentUserDept);
        if (filtered.length) return filtered;
        if (block?.mine?.data?.length) return [block.mine];
        return [];
      }
      if (scope === 'total') {
        const total = this.makeTotalDataset(block);
        return total ? [total] : [];
      }
      return block?.datasets || [];
    },

    makeTotalDataset(block) {
      const datasets = block?.datasets || [];
      const labels   = block?.labels || [];
      if (!datasets.length || !labels.length) return null;
      const len = Math.max(...datasets.map(d => (d?.data?.length || 0)));
      const series = new Array(len).fill(0);
      datasets.forEach(d => (d?.data || []).forEach((v,i) => { series[i] += Number(v||0); }));
      return { label: 'Total', data: series.slice(0, labels.length), color: '#FFD166' };
    },

    filterDatasetsForDept(datasets, deptName) {
      if (!datasets?.length) return [];
      const want = String(deptName || '').trim().toLowerCase();
      return datasets.filter(d => String(d?.label || '').trim().toLowerCase() === want);
    },

    renderAll() {
      this.renderOpenListings();
      this.renderListings();
      this.renderTasks();
      this.renderParticipantsAccepted();
      this.renderUsersAll();
    },

    renderOpenListings() {
      const block = this.data.openListingsMonthlyByDept || {};
      const use   = this.pickDatasets(block, 'openListings');
      this.renderLine(this.$refs.cOpenListings, 'openListings', block.labels || [], use);
    },
    renderListings() {
      const block = this.data.listingsMonthlyByDept || {};
      const use   = this.pickDatasets(block, 'listings');
      this.renderLine(this.$refs.cListings, 'listings', block.labels || [], use);
    },
    renderTasks() {
      const block = this.data.tasksMonthlyByDept || {};
      const use   = this.pickDatasets(block, 'tasks');
      this.renderLine(this.$refs.cTasks, 'tasks', block.labels || [], use);
    },
    renderParticipantsAccepted() {
      const block = this.data.participantsAcceptedPerDeptMonthly || {};
      const use   = this.pickDatasets(block, 'participantsAccepted');
      this.renderLine(this.$refs.cParticipantsAccepted, 'participantsAccepted', block.labels || [], use);
    },
    renderUsersAll() {
      const block = this.data.usersPerDeptMonthly || {};
      const use   = this.pickDatasets(block, 'usersAll');
      this.renderLine(this.$refs.cUsersAll, 'usersAll', use ? (block.labels || []) : [], use);
    },
  }));
  // ========== /ORG ANALYTICS ==========
});

/* ========================================================================== */
/* ============================== COMPONENT HELPERS ========================= */
/* ========================================================================== */

// Helpers you already use (unchanged)
window.taskCard = function taskCard(taskId){ return {
  detailsOpen:false, editOpen:false, modOpen:false,
  init(){
    const setup=()=>{
      const isMobile = window.innerWidth < 768;
      const root = this.$el;
      root.querySelectorAll('[data-bs-toggle="popover"]').forEach(el=>{
        const inst = window.bootstrap?.Popover?.getInstance(el); if (inst) inst.dispose();
      });
      if (isMobile) root.querySelectorAll('[data-bs-toggle="popover"]').forEach(el=> new window.bootstrap.Popover(el));
    };
    setup(); window.addEventListener('resize',setup);
  }
};};

window.studentTaskCard = function studentTaskCard(taskId){ return {
  detailsOpen:false,
  init(){
    const setup=()=>{
      const isMobile = window.innerWidth < 768;
      const root = this.$el;
      root.querySelectorAll('[data-bs-toggle="popover"]').forEach(el=>{
        const inst = window.bootstrap?.Popover?.getInstance(el); if (inst) inst.dispose();
      });
      if (isMobile) root.querySelectorAll('[data-bs-toggle="popover"]').forEach(el=> new window.bootstrap.Popover(el));
    };
    setup(); window.addEventListener('resize',setup);
  }
};};

/* ========================================================================== */
/* ============================ LIVEWIRE DEBUG HOOKS ======================== */
/* ========================================================================== */

if (!window.__LW_DEBUG_INSTALLED__ && window.Livewire?.hook) {
  window.__LW_DEBUG_INSTALLED__ = true;
  Livewire.hook('message.sent',     (m,c)=>console.debug('[lw] sent',c.id,m?.updateQueue?.[0]?.payload?.method));
  Livewire.hook('message.failed',   (m,c)=>console.debug('[lw] failed',c.id));
  Livewire.hook('message.received', (m,c)=>console.debug('[lw] recv',c.id));
  Livewire.hook('message.processed',(m,c)=>console.debug('[lw] done',c.id));
}
