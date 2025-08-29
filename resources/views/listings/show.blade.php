@extends('components.layout')

@section('content')

@php
    $application = $listing->applications->where('user_id', auth()->id())->first();
    $isAccepted = $application && $application->accepted;
@endphp

<x-secondary-heading>
    {{ $isAccepted ? 'Listing Information' : 'Apply for the Research Work' }}
</x-secondary-heading>

@livewire('listing-manage-card', ['listing' => $listing], key('listing-manage-'.$listing->id))

{{-- Application panel (form or awaiting) --}}
@unless ($isAccepted)
@livewire('listing-application-panel', ['listing' => $listing], key('listing-application-'.$listing->id))
@endunless

@if ($isAccepted)
    {{-- Chat (participant) --}}
  @livewire('listing-chat', ['listing' => $listing], key('listing-chat-'.$listing->id))

    @livewire('show-student-tasks', ['listing' => $listing], key('student-tasks-' . $listing->id))
@endif

@endsection
