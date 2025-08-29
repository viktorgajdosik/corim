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

  <section id="listing-management" class="relative text-white">
    @livewire('listing-manage-card', ['listing' => $listing], key('listing-manage-'.$listing->id.'-'.optional($listing->updated_at)->timestamp))
  </section>

  {{-- Chat (author) --}}
  @livewire('listing-chat', ['listing' => $listing], key('listing-chat-'.$listing->id))

  {{-- Applications & Participants --}}
<section id="applications-participants">
  @livewire('manage-applicants', ['listing' => $listing], key('manage-applicants-'.$listing->id))
</section>


{{-- Tasks --}}
@livewire('show-manage-tasks', ['listing' => $listing])

</div>
@endsection
