{{-- resources/views/livewire/org-analytics.blade.php --}}
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

<style>
  /* Default heights (desktop/tablet) */
  :root { --oa-h: 320px; --oa-h-tall: 340px; }
  /* Taller on mobile so legends fit */
  @media (max-width: 767.98px) {
    :root { --oa-h: 420px; --oa-h-tall: 480px; }
  }
  .oa-chart { position: relative; height: var(--oa-h); }
  .oa-chart--tall { height: var(--oa-h-tall); }
</style>

<div
  x-data="orgAnalyticsComponent(@js($oaState))"
  x-init="init()"
  x-on:oa:scope.window="setScope($event.detail)"
  class="mt-4"
>
  {{-- Alpine re-reads latest state here after Livewire updates --}}
  <script type="application/json" x-ref="state">
    @json($oaState)
  </script>

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <x-secondary-heading>Organization Analytics</x-secondary-heading>

    <div class="d-flex flex-wrap align-items-center gap-2">
      {{-- Time range --}}
      <div class="d-flex align-items-center gap-2">
        <label for="window"
               class="text-muted-60 small mb-0 text-nowrap"
               style="min-width: 92px;">Time range</label>
        <select id="window" class="form-select form-select-sm bg-dark text-white"
                style="min-width: 160px;"
                wire:model.live="window">
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

  @if (!$hasOrg)
    <x-card-form>
      <div class="text-muted-60">No organization set for your account yet. Add one in your profile to see org-level analytics.</div>
    </x-card-form>
  @else
    <div class="row g-3">

      {{-- 0) Open Listings (closed excluded) — Trend --}}
      <div class="col-12">
        <x-card-form>
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
        </x-card-form>
      </div>

      {{-- 1) Listings per Department — Trend --}}
      <div class="col-12">
        <x-card-form>
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
        </x-card-form>
      </div>

      {{-- 2) Tasks per Department — Trend --}}
      <div class="col-12">
        <x-card-form>
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
        </x-card-form>
      </div>

      {{-- 3) Participating Users (ACCEPTED) per Department — Trend --}}
      <div class="col-12">
        <x-card-form>
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
        </x-card-form>
      </div>

      {{-- 4) All Users (REGISTERED) per Department — Trend --}}
      <div class="col-12">
        <x-card-form>
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
        </x-card-form>
      </div>

    </div>
  @endif
</div>
