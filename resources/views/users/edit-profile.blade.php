@extends('components.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <x-secondary-heading>Edit Profile</x-secondary-heading>
  <nav class="text-secondary fs-6 mb-3">
    <a href="{{ route('users.profile') }}" class="text-decoration-none text-secondary">Profile</a>
    <span class="mx-1">/</span>
    <span class="text-white">Edit</span>
  </nav>
</div>

@livewire('edit-profile-panel', [], key('edit-profile-panel'))

@endsection
