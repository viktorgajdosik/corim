@extends('components.layout')

@section('content')

{{-- Scrollspy Nav --}}
<nav id="scrollspy-nav" class="navbar navbar-dark bg-dark rounded border border-black sticky-top px-3 mb-3 d-block d-md-flex justify-content-between align-items-center">
    <ul class="nav nav-pills flex-wrap justify-content-center justify-content-md-start gap-2">
        <li class="nav-item">
            <x-text tag="a" class="nav-link rounded-pill fs-6 px-4 py-0 h-100 d-flex align-items-center" href="#listing-management">Listing</x-text>
        </li>
        <li class="nav-item">
          <x-text tag="a" class="nav-link rounded-pill fs-6 px-4 py-0 h-100 d-flex align-items-center" href="#applications-participants">Participants</x-text>
        </li>
        <li class="nav-item">
          <x-text tag="a" class="nav-link rounded-pill fs-6 px-4 py-0 h-100 d-flex align-items-center" href="#tasks">Tasks</x-text>
        </li>
      </ul>
    <span class="navbar-brand d-none d-md-block text-secondary">
        <a href="{{ route('listings.manage') }}" class="text-decoration-none text-secondary fs-6">My Listings</a>
        <span class="mx-1 fs-6">/</span>
        <span class="text-white fs-6">Manage</span>
    </span>
</nav>

{{-- Scrollspy Container --}}
<div data-bs-spy="scroll"
     data-bs-target="#scrollspy-nav"
     data-bs-root-margin="0px 0px -65%"
     data-bs-smooth-scroll="true"
     class="scrollspy-example"
     tabindex="0">

  {{-- Listing Management --}}

  <section id="listing-management"
  x-data="{ modalOpen: false, countdown: 5, timer: null, confirming: false }"
  class="relative text-white"
  x-cloak>
  <x-card-form>

    <!-- Content shown only when modal is closed -->
    <template x-if="!modalOpen">
      <div>
        <x-card-heading class="listing-title mb-3">{{ $listing->title }}</x-card-heading>
        <small><i class="fa fa-user me-1" title="Author"></i> {{ $listing->author }}</small>
        <small> <i class="fa fa-calendar ms-3 me-1" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}</small>
            <small class="ms-3" title="Department">
                <x-department-dot :department="$listing->department" />
            </small>

        <x-text class="description mt-3 mb-3">{!! nl2br(e($listing->description)) !!}</x-text>

        <!-- Edit Button -->
        <button type="button" class="btn btn-primary btn-sm d-inline"
                onclick="window.location.href='/listings/{{$listing->id}}/edit'">
          <i class="fa fa-pencil"></i> Edit
        </button>

        <!-- Trigger Delete Button -->
        <button type="button"
                class="btn btn-danger btn-sm"
                @click="
                  modalOpen = true;
                  window.scrollTo({ top: 0, behavior: 'smooth' });
                ">
          <i class="fa fa-trash"></i> Delete
        </button>
      </div>
    </template>

    <!-- Modal shown only when modalOpen is true -->
    <template x-if="modalOpen">
      <div class="p-3 border border-danger bg-transparent rounded">

        <!-- First Step: Confirm -->
        <div x-show="!confirming">
          <p class="text-danger">Are you sure you want to delete this listing? This action is irreversible.</p>
          <div class="d-flex gap-2 justify-content-end">
            <button class="btn btn-outline-light rounded-pill btn-sm" @click="modalOpen = false">Cancel</button>
            <button class="btn btn-outline-danger rounded-pill btn-sm" @click="
                confirming = true;
                countdown = 5;
                timer = setInterval(() => {
                  if (countdown > 1) {
                    countdown--;
                  } else {
                    clearInterval(timer);
                    $refs.form.submit();
                  }
                }, 1000);
              ">
              Yes, Delete
            </button>
          </div>
        </div>

        <!-- Countdown Step -->
        <div x-show="confirming">
          <p class="text-danger">Deleting in <strong x-text="countdown"></strong> seconds...</p>
          <div class="d-flex justify-content-end">
            <button class="btn btn-outline-light rounded-pill btn-sm" @click="
                clearInterval(timer);
                confirming = false;
                countdown = 5;
                modalOpen = false;
              ">Cancel Deletion</button>
          </div>
        </div>

        <!-- Hidden Delete Form -->
        <form method="POST" action="/listings/{{ $listing->id }}" x-ref="form" class="d-none">
          @csrf
          @method('DELETE')
        </form>
      </div>
    </template>

  </x-card-form>
</section>



  {{-- Applications & Participants --}}
  <section id="applications-participants">
    <x-secondary-heading>Applications</x-secondary-heading>
    @forelse ($listing->applications->where('accepted', false) as $application)
      <x-card-form>
        <x-card-heading>{{ $application->user->name }}</x-card-heading>
           <div class="d-flex gap-3 mb-1">
                 <small title="Email address"><i class="fa fa-envelope me-1"></i> {{ $application->user->email }}</small>
            <small title="Department">
                <x-department-dot :department="$application->user->department" />
                </small>
           </div>
        <p><i class="fa fa-edit"></i> {{ $application->message }}</p>
        <div class="d-flex">
          <form method="POST" action="{{ route('listings.accept', ['application' => $application->id]) }}">
            @csrf
            <button class="btn btn-primary btn-sm me-1" onclick="return confirm('Accept this application?')">
              <i class="fa fa-check"></i> Accept
            </button>
          </form>
          <form method="POST" action="{{ route('listings.deny', ['application' => $application->id]) }}">
            @csrf
            <button class="btn btn-danger btn-sm" onclick="return confirm('Deny this application?')">
              <i class="fa fa-times"></i> Deny
            </button>
          </form>
        </div>
         </div>
      </x-card-form>
    @empty

      <x-text class="text-white mb-5">Currently no applications.</x-text>

    @endforelse

    <x-secondary-heading>Current Participants</x-secondary-heading>
    @if ($listing->applications->where('accepted', true)->isEmpty())

      <x-text class="text-white mb-5">Currently no participants.</x-text>

    @else
      @foreach ($listing->applications->where('accepted', true) as $application)
       <x-card-form>
  <div class="d-flex justify-content-between align-items-center">
    {{-- Left content: Name, email, department --}}
    <div>
      <x-card-heading>{{ $application->user->name }}</x-card-heading>
      <div class="d-flex gap-3 mb-1">
        <small title="Email address">
          <i class="fa fa-envelope me-1"></i> {{ $application->user->email }}
        </small>
        <small title="Department">
          <x-department-dot :department="$application->user->department" />
        </small>
      </div>
    </div>

    {{-- Right content: Remove button --}}
    <form method="POST" action="{{ route('listings.deny', ['application' => $application->id]) }}">
      @csrf
      <button class="btn btn-danger btn-sm" onclick="return confirm('Remove this participant?')">
        <i class="fa fa-trash"></i> Remove
      </button>
    </form>
  </div>
</x-card-form>

      @endforeach
    @endif
  </section>

{{-- Tasks --}}
@livewire('show-manage-tasks', ['listing' => $listing])

</div>
@endsection
