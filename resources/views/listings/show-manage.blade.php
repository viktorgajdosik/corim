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
        <x-card-heading>{{ $listing->title }}</x-card-heading>

        <p>
          <i class="fa fa-user" title="Author"></i> {{ $listing->author }} |
          <i class="fa fa-building" title="Department"></i> {{ $listing->department }} |
          <i class="fa fa-calendar" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}
        </p>

        <p class="description mt-4 mb-3">{!! nl2br(e($listing->description)) !!}</p>

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
        <p><i class="fa fa-building"></i> {{ $application->user->department }}</p>
        <p><i class="fa fa-envelope"></i> {{ $application->user->email }}</p>
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
      </x-card-form>
    @empty
      <p class="text-white">Currently no applications.</p>
    @endforelse

    <x-secondary-heading>Current Participants</x-secondary-heading>
    @if ($listing->applications->where('accepted', true)->isEmpty())
      <p class="text-white">Currently no participants.</p>
    @else
      @foreach ($listing->applications->where('accepted', true) as $application)
        <x-card-form>
          <x-card-heading>{{ $application->user->name }}</x-card-heading>
          <p><i class="fa fa-building"></i> {{ $application->user->department }}</p>
          <p><i class="fa fa-envelope"></i> {{ $application->user->email }}</p>
          <form method="POST" action="{{ route('listings.deny', ['application' => $application->id]) }}">
            @csrf
            <button class="btn btn-danger btn-sm" onclick="return confirm('Remove this participant?')">
              <i class="fa fa-trash"></i> Remove
            </button>
          </form>
        </x-card-form>
      @endforeach
    @endif
  </section>

  {{-- Tasks --}}
  <section id="tasks">
    <x-secondary-heading>Create Task</x-secondary-heading>
    <x-card-form>
      <form method="POST" action="/tasks" enctype="multipart/form-data" class="custom-floating-label">
        @csrf
        <div class="form-floating mb-3">
            <input type="text" class="form-control bg-dark text-white" id="task-name" name="task_name" placeholder="Task Name">
          <label class="text-white" for="task-name">Task Name</label>
        </div>

        <div class="form-floating mb-3">
            <textarea class="form-control bg-dark text-white" id="task-details" name="task_details" placeholder="Task Details" style="height: 200px"></textarea>
          <label class="text-white" for="task-details">Task Details</label>
        </div>

        <div class="form-group upload mb-3 rounded-pill">
          <label for="task-file"><i class="fa fa-file"></i> Upload File</label>
          <input type="file" class="form-control-file" id="task-file" name="task-file">
        </div>

        <div class="form-group">
          <label class="mb-2">Assign Task</label>
          @if ($listing->applications()->where('accepted', true)->exists())
            @foreach ($listing->applications()->where('accepted', true)->with('user')->get() as $applicant)
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="assigned_users[]" value="{{ $applicant->user->id }}" id="{{ $applicant->user->id }}">
                <label class="form-check-label" for="{{ $applicant->user->id }}">{{ $applicant->user->name }}</label>
              </div>
            @endforeach
          @else
            <p>No users have accepted applications for this listing yet.</p>
          @endif
        </div>

        <button type="submit" class="btn btn-primary btn-sm mt-3">Submit</button>
      </form>
    </x-card-form>

    <x-secondary-heading>Assigned Tasks</x-secondary-heading>
    <x-card-form>
      <x-card-heading>Research relevant papers</x-card-heading>
      <p><i class="fa fa-user"></i> John Doe</p>
      <p><i class="fa fa-building"></i> Student</p>
      <p><i class="fa fa-envelope"></i> student@osu.cz</p>
      <p><i class="fa fa-gears"></i> Status</p>
      <p><i class="fa fa-file"></i> File</p>
    </x-card-form>

    <x-secondary-heading>Finished Tasks</x-secondary-heading>
    <x-card-form>
      <x-card-heading>Research relevant papers</x-card-heading>
      <p><i class="fa fa-user"></i> John Doe</p>
      <p><i class="fa fa-building"></i> Student</p>
      <p><i class="fa fa-envelope"></i> student@osu.cz</p>
      <p><i class="fa fa-gears"></i> Status</p>
      <p><i class="fa fa-file"></i> File</p>
    </x-card-form>
  </section>
</div>
@endsection
