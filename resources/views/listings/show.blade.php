<x-search></x-search>
<x-layout>

    <h3 class="font-weight-bold">Apply for the research work</h3>
    <br>
    <x-card>
        <div class="date-container">
            <p class="date-created">
                <i class="fa fa-calendar" data-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}
            </p>
        </div>
        <h4 class="listing-title mb-2">{{ $listing->title }}</h4>
            <span>
                <i class="fa fa-user" data-toggle="tooltip" title="Author"></i> {{ $listing->author }}
            </span>
            <span> | </span>
            <span>
                <i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}
            </span>
        <p class="description mt-2 mb-0">{{ $listing->description }}</p>
    </x-card>

    <!-- Check if the user has an unprocessed application -->
    @if ($userHasUnprocessedApplication)
        <x-card>
            <h4>Awaiting Application Results</h4>
            <p>You have already applied for this research work. Please wait for the author's response.</p>
        </x-card>
    @else
        <x-card>
            <form action="{{ route('listings.apply', ['listing' => $listing->id]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <h4 class="mb-2">Message the Author</h4>
                    <p>Tell the author something about yourself.</p>
                    <textarea class="form-control bg-light border-0" id="message" name="message" rows="5" placeholder="Enter message" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Apply</button>
            </form>
        </x-card>
    @endif

</x-layout>
