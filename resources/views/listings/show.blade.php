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


@if (!$application)
    <x-card-form>
        <x-card-heading class="mb-2">Message the Author</x-card-heading>
        <x-text>Tell the author something about yourself.</x-text>

        <form action="{{ route('listings.apply', ['listing' => $listing->id]) }}" method="POST" class="custom-floating-label">
            @csrf
            <div class="form-floating mb-3">
                <textarea class="form-control text-white bg-dark border-1 @error('message') is-invalid @enderror"
                          id="message"
                          name="message"
                          placeholder="Enter message"
                          style="height: 150px" required>{{ old('message') }}</textarea>
                <label for="message">Enter message</label>

                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-sm mb-2">Apply</button>
        </form>
    </x-card-form>

@elseif (!$isAccepted)
    <x-card-form>
        <x-card-heading>Awaiting Application Results</x-card-heading>
        <x-text>You have already applied for this research work. Please wait for the author's response.</x-text>
    </x-card-form>
@endif

@if ($isAccepted)

    @livewire('show-student-tasks', ['listing' => $listing], key('student-tasks-' . $listing->id))
@endif

@endsection
