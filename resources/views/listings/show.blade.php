@extends('components.layout')
@section('content')

    <x-secondary-heading>Apply for the Research Work</x-secondary-heading>
    <x-card-form>
        <h4 class="listing-title mb-3">{{ $listing->title }}</h4>
        <span>
            <i class="fa fa-user" data-bs-toggle="tooltip" title="Author"></i> {{ $listing->author }}
        </span>
        <span> | </span>
        <span>
            <i class="fa fa-building" data-bs-toggle="tooltip" title="Department"></i> {{ $listing->department }}
        </span>
        <span> | </span>
        <span>
            <i class="fa fa-calendar" data-bs-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}
        </span>
        <p class="description text-justify mt-4 mb-2">{!! nl2br(e($listing->description)) !!}</p>
    </x-card-form>

    <!-- Check if the user has an unprocessed application -->
    @if ($userHasUnprocessedApplication)
        <x-card-form>
            <h4>Awaiting Application Results</h4>
            <p>You have already applied for this research work. Please wait for the author's response.</p>
        </x-card-form>
    @else
        <x-card-form>
            <form action="{{ route('listings.apply', ['listing' => $listing->id]) }}" method="POST" class="custom-floating-label">
                @csrf

                <!-- Message the Author Floating Label -->
                <div class="mb-3">
                    <h4 class="mb-2">Message the Author</h4>
                    <p>Tell the author something about yourself.</p>

                    <div class="form-floating">
                        <textarea class="form-control text-white bg-dark border-1 @error('message') is-invalid @enderror"
                                  id="message"
                                  name="message"
                                  placeholder="Enter message"
                                  style="height: 150px" required>{{ old('message') }}</textarea>
                        <label for="message">Enter message</label>
                    </div>

                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mb-2">Apply</button>
            </form>
        </x-card-form>
    @endif

@endsection
