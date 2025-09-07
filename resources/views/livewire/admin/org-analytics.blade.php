@php
  $oaState = [
    'listingsMonthlyByDept'              => $charts['listingsMonthlyByDept'] ?? ['labels'=>[], 'datasets'=>[], 'mine'=>['label'=>'','data'=>[]]],
    'tasksMonthlyByDept'                 => $charts['tasksMonthlyByDept'] ?? ['labels'=>[], 'datasets'=>[], 'mine'=>['label'=>'','data'=>[]]],
    'participantsAcceptedPerDeptMonthly' => $charts['participantsAcceptedPerDeptMonthly'] ?? ['labels'=>[], 'datasets'=>[], 'mine'=>['label'=>'','data'=>[]]],
    'usersPerDeptMonthly'                => $charts['usersPerDeptMonthly'] ?? ['labels'=>[], 'datasets'=>[], 'mine'=>['label'=>'','data'=>[]]],
    'openListingsMonthlyByDept'          => $charts['openListingsMonthlyByDept'] ?? ['labels'=>[], 'datasets'=>[], 'mine'=>['label'=>'','data'=>[]]],
    'currentUserDept'                    => $currentUserDept,
    'window'                             => $window,
  ];
@endphp

<div
  x-data="orgAnalyticsComponent(@js($oaState))"
  x-init="init()"
  x-on:oa:scope.window="setScope($event.detail)"
  class="mt-3"
>
  <style>
    :root { --oa-h: 320px; --oa-h-tall: 340px; }
    @media (max-width: 767.98px) { :root { --oa-h: 420px; --oa-h-tall: 480px; } }
    .oa-chart { position: relative; height: var(--oa-h); }
    .oa-chart--tall { height: var(--oa-h-tall); }
  </style>

  {{-- Alpine re-reads latest state here after Livewire updates --}}
  <script type="application/json" x-ref="state">
    @json($oaState)
  </script>

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h5 class="mb-0">Analytics</h5>

    <div class="d-flex flex-wrap align-items-center gap-2">

      {{-- Organization --}}
      <div class="d-flex align-items-center gap-2">
        <label for="org" class="text-muted-60 small mb-0 text-nowrap" style="min-width: 92px;">Organization</label>
        <select id="org" class="form-select form-select-sm bg-dark text-white" style="min-width: 200px;" wire:model.live="org">
          <option value="all">All organizations</option>
          @foreach($organizations as $o)
            <option value="{{ $o }}">{{ $o }}</option>
          @endforeach
        </select>
      </div>

      {{-- Time range --}}
      <div class="d-flex align-items-center gap-2">
        <label for="window" class="text-muted-60 small mb-0 text-nowrap" style="min-width: 92px;">Time range</label>
        <select id="window" class="form-select form-select-sm bg-dark text-white" style="min-width: 160px;" wire:model.live="window">
          <option value="6m">Last 6 months</option>
          <option value="1y">Last 12 months</option>
          <option value="5y">Last 5 years</option>
          <option value="all">All time</option>
        </select>
      </div>

      {{-- Export menu --}}
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          Export
        </button>
        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
          <li><button class="dropdown-item" @click.prevent="exportPdf()">PDF report</button></li>
          <li><button class="dropdown-item" wire:click="exportXlsx">Excel (.xlsx)</button></li>
          <li><button class="dropdown-item" wire:click="exportCsvZip">CSV (ZIP)</button></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- 0) Open Listings --}}
    <div class="col-12">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted-60 small">Listings Accepting Applications</span>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-light" :class="{'active': scopes.openListings==='mine'}"
                      @click="$dispatch('oa:scope', { key: 'openListings', scope: 'mine' })">My department</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.openListings==='all'}"
                      @click="$dispatch('oa:scope', { key: 'openListings', scope: 'all' })">All</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.openListings==='total'}"
                      @click="$dispatch('oa:scope', { key: 'openListings', scope: 'total' })">Total</button>
            </div>
          </div>
          <div class="oa-chart" wire:ignore>
            <canvas x-ref="cOpenListings"></canvas>
            <template x-if="!(data?.openListingsMonthlyByDept?.datasets?.length)">
              <div class="position-absolute top-50 start-50 translate-middle text-muted-60 small">No data</div>
            </template>
          </div>
        </div>
      </div>
    </div>

    {{-- 1) Listings --}}
    <div class="col-12">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted-60 small">All Listings</span>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-light" :class="{'active': scopes.listings==='mine'}"
                      @click="$dispatch('oa:scope', { key: 'listings', scope: 'mine' })">My department</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.listings==='all'}"
                      @click="$dispatch('oa:scope', { key: 'listings', scope: 'all' })">All</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.listings==='total'}"
                      @click="$dispatch('oa:scope', { key: 'listings', scope: 'total' })">Total</button>
            </div>
          </div>
          <div class="oa-chart" wire:ignore>
            <canvas x-ref="cListings"></canvas>
            <template x-if="!(data?.listingsMonthlyByDept?.datasets?.length)">
              <div class="position-absolute top-50 start-50 translate-middle text-muted-60 small">No data</div>
            </template>
          </div>
        </div>
      </div>
    </div>

    {{-- 2) Tasks --}}
    <div class="col-12">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted-60 small">Tasks</span>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-light" :class="{'active': scopes.tasks==='mine'}"
                      @click="$dispatch('oa:scope', { key: 'tasks', scope: 'mine' })">My department</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.tasks==='all'}"
                      @click="$dispatch('oa:scope', { key: 'tasks', scope: 'all' })">All</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.tasks==='total'}"
                      @click="$dispatch('oa:scope', { key: 'tasks', scope: 'total' })">Total</button>
            </div>
          </div>
          <div class="oa-chart" wire:ignore>
            <canvas x-ref="cTasks"></canvas>
            <template x-if="!(data?.tasksMonthlyByDept?.datasets?.length)">
              <div class="position-absolute top-50 start-50 translate-middle text-muted-60 small">No data</div>
            </template>
          </div>
        </div>
      </div>
    </div>

    {{-- 3) Participants (accepted) --}}
    <div class="col-12">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted-60 small">Participating Users</span>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-light" :class="{'active': scopes.participantsAccepted==='mine'}"
                      @click="$dispatch('oa:scope', { key: 'participantsAccepted', scope: 'mine' })">My department</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.participantsAccepted==='all'}"
                      @click="$dispatch('oa:scope', { key: 'participantsAccepted', scope: 'all' })">All</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.participantsAccepted==='total'}"
                      @click="$dispatch('oa:scope', { key: 'participantsAccepted', scope: 'total' })">Total</button>
            </div>
          </div>
          <div class="oa-chart oa-chart--tall" wire:ignore>
            <canvas x-ref="cParticipantsAccepted"></canvas>
            <template x-if="!(data?.participantsAcceptedPerDeptMonthly?.datasets?.length)">
              <div class="position-absolute top-50 start-50 translate-middle text-muted-60 small">No data</div>
            </template>
          </div>
        </div>
      </div>
    </div>

    {{-- 4) Users (registered) --}}
    <div class="col-12">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted-60 small">All Users</span>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-light" :class="{'active': scopes.usersAll==='mine'}"
                      @click="$dispatch('oa:scope', { key: 'usersAll', scope: 'mine' })">My department</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.usersAll==='all'}"
                      @click="$dispatch('oa:scope', { key: 'usersAll', scope: 'all' })">All</button>
              <button class="btn btn-outline-light" :class="{'active': scopes.usersAll==='total'}"
                      @click="$dispatch('oa:scope', { key: 'usersAll', scope: 'total' })">Total</button>
            </div>
          </div>
          <div class="oa-chart oa-chart--tall" wire:ignore>
            <canvas x-ref="cUsersAll"></canvas>
            <template x-if="!(data?.usersPerDeptMonthly?.datasets?.length)">
              <div class="position-absolute top-50 start-50 translate-middle text-muted-60 small">No data</div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
