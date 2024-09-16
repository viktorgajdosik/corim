<x-search></x-search>
<x-layout>

    <h3>Apply for the research work</h3>
    <br>
    <x-card>
        <h4 class="listing-title mb-3">{{ $listing->title }}</h4>
        <span>
            <i class="fa fa-user" data-toggle="tooltip" title="Author"></i> {{ $listing->author }}
        </span>
        <span> | </span>
        <span>
            <i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}
        </span>
        <span> | </span>
        <span><i class="fa fa-calendar" data-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}</span>
        <p class="description text-justify mt-4 mb-2">{!! nl2br(e($listing->description)) !!}</p>
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
