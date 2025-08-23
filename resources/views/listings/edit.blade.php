@extends('components.layout')

@section('content')

{{-- Breadcrumb Navigation --}}
<div class="d-flex justify-content-between align-items-center">
  <x-secondary-heading>Edit Listing</x-secondary-heading>
  <nav class="text-secondary fs-6 mb-3">
    <a href="{{ route('listings.manage') }}" class="text-decoration-none text-secondary">My Listings</a>
    <span class="mx-1">/</span>
    <a href="{{ route('listings.show-manage', ['listing' => $listing->id]) }}" class="text-decoration-none text-secondary">Manage</a>
    <span class="mx-1">/</span>
    <span class="text-white">Edit</span>
  </nav>
</div>

{{-- Livewire wrapper panel with skeleton on first paint + the form inside --}}
@livewire('edit-listing-panel', ['listing' => $listing], key('edit-listing-panel-'.$listing->id))

@endsection
