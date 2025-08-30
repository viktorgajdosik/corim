// resources/js/app.js
import * as bootstrap from 'bootstrap';
window.bootstrap = window.bootstrap || bootstrap;

$(document).ready(function () {
  $('[data-bs-toggle="popover"]').popover({
    html: true,
    trigger: 'hover',
    container: 'body'
  });
});

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
      this.renderLine(this.$refs.cUsersAll, 'usersAll', block.labels || [], use);
    },

    // ---------- LIGHT THEME CLONE & CAPTURE ----------
    deepClone(obj) {
      try { return JSON.parse(JSON.stringify(obj)); } catch { return obj; }
    },

    buildLightOptionsForClone(orig, w, h) {
      return {
        responsive: false,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: { display: true, labels: { color: '#000', boxWidth: 12 } },
          tooltip: { enabled: false }
        },
        scales: {
          x: { ticks: { color: '#000', font: { size: 11 }, maxRotation: 0, autoSkip: true }, grid: { color: '#ddd', display: true } },
          y: { ticks: { color: '#000', font: { size: 11 } }, grid: { color: '#eee' }, beginAtZero: true }
        }
      };
    },

    captureChartToDataURL(chart, srcCanvas) {
      if (!chart || !srcCanvas) {
        if (!srcCanvas) return null;
        const w = srcCanvas.width || 1200, h = srcCanvas.height || 600;
        const white = document.createElement('canvas');
        white.width = w; white.height = h;
        const wctx = white.getContext('2d');
        wctx.fillStyle = '#fff';
        wctx.fillRect(0,0,w,h);
        wctx.drawImage(srcCanvas, 0, 0, w, h);
        return white.toDataURL('image/jpeg', 0.9);
      }

      const w = srcCanvas.width || 1200, h = srcCanvas.height || 600;
      const off = document.createElement('canvas');
      off.width = w; off.height = h;

      const clonedData = this.deepClone(chart.config.data);
      const lightOptions = this.buildLightOptionsForClone(chart.options, w, h);

      let tempChart = null;
      try {
        tempChart = new Chart(off.getContext('2d'), {
          type: chart.config.type || 'line',
          data: clonedData,
          options: lightOptions
        });
      } catch (e) {
        console.error('[oa] temp chart build failed', e);
        const white = document.createElement('canvas');
        white.width = w; white.height = h;
        const wctx = white.getContext('2d');
        wctx.fillStyle = '#fff';
        wctx.fillRect(0,0,w,h);
        wctx.drawImage(srcCanvas, 0, 0, w, h);
        return white.toDataURL('image/jpeg', 0.9);
      }

      const out = document.createElement('canvas');
      out.width = w; out.height = h;
      const octx = out.getContext('2d');
      octx.fillStyle = '#fff';
      octx.fillRect(0,0,w,h);
      octx.drawImage(off, 0, 0, w, h);

      const url = out.toDataURL('image/jpeg', 0.9);

      try { tempChart.destroy(); } catch {}
      return url;
    },

    // ----- export all canvases -----
    async exportPdf() {
      await this.ensureChartJs();

      if (!this.charts.listings || !this.charts.tasks || !this.charts.participantsAccepted || !this.charts.usersAll || !this.charts.openListings) {
        this.renderAll();
        await this.nextPaint();
      }

      const toImg = (key, refName) => {
        try {
          const canvas = this.$refs[refName];
          const chart  = this.charts[key];
          return this.captureChartToDataURL(chart, canvas);
        } catch (e) {
          console.error('[oa] capture error', key, e);
          return null;
        }
      };

      const images = {
        openListings:         toImg('openListings', 'cOpenListings'),
        listings:             toImg('listings', 'cListings'),
        tasks:                toImg('tasks', 'cTasks'),
        participantsAccepted: toImg('participantsAccepted', 'cParticipantsAccepted'),
        usersAll:             toImg('usersAll', 'cUsersAll'),
      };

      await this.$wire.exportPdf(images);
    },
  }));
  // ========== /ORG ANALYTICS ==========



  // ===== Livewire debug hooks =====
  if (!window.__LW_DEBUG_INSTALLED__ && window.Livewire?.hook) {
    window.__LW_DEBUG_INSTALLED__ = true;
    Livewire.hook('message.sent',     (m,c)=>console.debug('[lw] sent',c.id,m?.updateQueue?.[0]?.payload?.method));
    Livewire.hook('message.failed',   (m,c)=>console.debug('[lw] failed',c.id));
    Livewire.hook('message.received', (m,c)=>console.debug('[lw] recv',c.id));
    Livewire.hook('message.processed',(m,c)=>console.debug('[lw] done',c.id));
  }

  function nextPaint(){return new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));}

  function waitForSelector(selector,{appear=true,timeout=5000}={}) {
    return new Promise((resolve,reject)=>{
      const matches=()=>!!document.querySelector(selector);
      if ((appear && matches()) || (!appear && !matches())) return nextPaint().then(resolve);
      const obs=new MutationObserver(()=>{
        if ((appear && matches()) || (!appear && !matches())) { try{obs.disconnect();}catch{} nextPaint().then(resolve); }
      });
      obs.observe(document.body,{childList:true,subtree:true});
      setTimeout(()=>{try{obs.disconnect();}catch{} reject(new Error(`Timeout waiting for ${appear?'appear':'disappear'}: ${selector}`));},timeout);
    });
  }

  // ===== Tasks =====
  window.addEventListener('taskDomShouldReflect', async (e) => {
    const { taskId, updatedAt, flash } = e.detail || {};
    if (taskId == null || updatedAt == null) return;
    try {
      const sel = `[data-task-id="${taskId}"][data-updated-at="${updatedAt}"]`;
      await waitForSelector(sel,{appear:true});
      const ui = Alpine.store('ui');
      ui.stopUpdate?.(taskId);
      ui.stopMod?.(taskId);
      ui.stopCreate?.();
      if (flash?.message) showToast(flash);
    } catch(err) {
      console.debug('[ui] task wait timeout', err?.message);
      Alpine.store('ui').stopAll?.();
    }
  });

  // ===== Applications / Participants (author side) =====
  window.addEventListener('appDomShouldReflect', async (e) => {
    const { appId, action, updatedAt, flash, listingId } = e.detail || {};
    if (appId == null || !action) return;

    try {
      if (action === 'accept') {
        const sel = updatedAt != null
          ? `[data-app-id="${appId}"][data-updated-at="${updatedAt}"]`
          : `[data-app-id="${appId}"]`;
        await waitForSelector(sel,{appear:true});
        Alpine.store('ui').stopAppAccept?.(appId);
        if (listingId != null) Alpine.store('ui').removeParticipantGhost(listingId);

      } else if (action === 'deny') {
        const sel = `[data-app-id="${appId}"]`;
        await waitForSelector(sel,{appear:false});
        Alpine.store('ui').stopAppDeny?.(appId);

      } else if (action === 'remove') {
        const sel = `[data-app-id="${appId}"]`;
        await waitForSelector(sel,{appear:false});
        Alpine.store('ui').stopAppRemove?.(appId);
      }

      if (flash?.message) showToast(flash);
    } catch(err) {
      console.debug('[ui] app wait timeout', err?.message);
      const ui = Alpine.store('ui');
      ui.stopAppAccept?.(appId);
      ui.stopAppDeny?.(appId);
      ui.stopAppRemove?.(appId);
    }
  });

  // ===== Student application =====
  window.addEventListener('applicationDomShouldReflect', async (e) => {
    const { listingId, state, flash } = e.detail || {};
    if (listingId == null || !state) return;

    try {
      if (state === 'awaiting') {
        const sel = `[data-app-state="awaiting"][data-listing-id="${listingId}"]`;
        await waitForSelector(sel, { appear: true, timeout: 7000 });
        Alpine.store('ui').stopAppApply?.(listingId);
      }
      if (flash?.message) showToast(flash);
    } catch (err) {
      console.debug('[ui] application wait timeout', err?.message);
      Alpine.store('ui').stopAppApply?.(listingId);
    }
  });

  // ===== Listing edit =====
  window.addEventListener('listingDomShouldReflect', async (e) => {
    const { listingId, updatedAt, flash } = e.detail || {};
    if (listingId == null || updatedAt == null) return;

    try {
      const sel = `[data-listing-id="${listingId}"][data-updated-at="${updatedAt}"]`;
      await waitForSelector(sel, { appear: true, timeout: 7000 });
      Alpine.store('ui').stopListingUpdate?.(listingId);
      if (flash?.message) showToast(flash);
    } catch (err) {
      console.debug('[ui] listing wait timeout', err?.message);
      Alpine.store('ui').stopListingUpdate?.(listingId);
    }
  });

  // ===== Profile edit / password edit =====
  window.addEventListener('profileDomShouldReflect', async (e) => {
    const { userId, updatedAt, flash } = e.detail || {};
    if (userId == null || updatedAt == null) return;

    try {
      const sel = `[data-user-id="${userId}"][data-updated-at="${updatedAt}"]`;
      await waitForSelector(sel, { appear: true, timeout: 7000 });
      const ui = Alpine.store('ui');
      ui.stopProfileUpdate?.(userId);
      ui.stopPasswordUpdate?.(userId);
      if (flash?.message) showToast(flash);
    } catch (err) {
      console.debug('[ui] profile wait timeout', err?.message);
      const ui = Alpine.store('ui');
      ui.stopProfileUpdate?.(userId);
      ui.stopPasswordUpdate?.(userId);
    }
  });

  // ===== Task removal =====
  window.addEventListener('taskRemovedDomShouldReflect', async (e) => {
    const { taskId, flash } = e.detail || {};
    if (taskId == null) return;

    try {
      const sel = `.task-card-wrap[data-task-id="${taskId}"]`;
      await waitForSelector(sel, { appear: false, timeout: 7000 });
    } catch (err) {
      console.debug('[ui] task remove wait timeout', err?.message);
    } finally {
      Alpine.store('ui').stopDelete?.(taskId);
      if (flash?.message) showToast(flash);
    }
  });

  function showToast(flash) {
    const { message, type='success', delay=3000 } = flash || {};
    if (!message) return;
    const container = document.querySelector('.toast-container');
    if (!container) return;

    const bg = type==='error'?'danger':type==='warning'?'warning':type==='info'?'info':'success';
    const el = document.createElement('div');
    el.className = `toast align-items-center text-bg-${bg} border-0`;
    el.setAttribute('role','alert'); el.setAttribute('aria-live','assertive'); el.setAttribute('aria-atomic','true');
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>`;
    container.appendChild(el);
    const toast = new bootstrap.Toast(el,{delay,autohide:true});
    toast.show();
    el.addEventListener('hidden.bs.toast',()=>el.remove());
  }
});



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

// ===== Chat helpers =====
(function () {
  // Robust scroll helper (after two paints so content is in the DOM)
  function scrollLogToBottom(root = document) {
    const el = root.querySelector('[x-ref="log"]');
    if (!el) return;
    requestAnimationFrame(() =>
      requestAnimationFrame(() => { el.scrollTop = el.scrollHeight; })
    );
  }

  function rootByComponentId(id) {
    return document.querySelector(`[wire\\:id="${id}"]`) || document;
  }

  // Page-level scroll request fired from PHP: $this->dispatch('chat:scrollBottom')
  window.addEventListener('chat:scrollBottom', () => {
    scrollLogToBottom(document);
  });

  // Optional: focus input if you dispatch('chat:focusInput')
  window.addEventListener('chat:focusInput', () => {
    const input = document.querySelector('[x-ref="input"]');
    if (input) input.focus();
  });

  document.addEventListener('livewire:initialized', () => {
    // Keep audience & message kebab dropdowns open across Livewire morphs
    let audienceWasOpen = false;
    let openMsgMenuLabelId = null;

    Livewire.hook('morph.pre', () => {
      const audMenu = document.getElementById('audienceMenu');
      audienceWasOpen = !!(audMenu && audMenu.classList.contains('show'));

      const openMsgMenu = document.querySelector('.msg-menu.show');
      openMsgMenuLabelId = openMsgMenu?.getAttribute('aria-labelledby') || null;
    });

    Livewire.hook('morph.updated', () => {
      if (audienceWasOpen) {
        const btn = document.getElementById('audienceBtn');
        if (btn) {
          const dd = bootstrap.Dropdown.getOrCreateInstance(btn, { autoClose: false });
          dd.show();
        }
        audienceWasOpen = false;
      }

      if (openMsgMenuLabelId) {
        const btn = document.getElementById(openMsgMenuLabelId);
        if (btn) {
          const dd = bootstrap.Dropdown.getOrCreateInstance(btn, { autoClose: false });
          dd.show();
        }
        openMsgMenuLabelId = null;
      }
    });

    // Initial nudge to bottom after first paint
    requestAnimationFrame(() => scrollLogToBottom(document));

    // Auto-scroll AFTER Livewire applies DOM changes for our actions
    Livewire.hook('message.processed', (msg, comp) => {
      const root = rootByComponentId(comp.id);
      if (!root.querySelector('[x-ref="log"]')) return;

      const queue   = msg?.updateQueue || [];
      const methods = queue.map(u => u?.payload?.method).filter(Boolean);

      if (methods.includes('ready') || methods.includes('send') || methods.includes('saveEdit')) {
        scrollLogToBottom(root);
      }
    });
  });
})();
