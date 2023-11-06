<x-search></x-search>
<x-layout>

    <h2>Apply for the research work</h2>
    <br>
    <x-card>
        <h4 class="mb-3">{{ $listing['title'] }}</h4>
        <p>{{ $listing['description'] }} </p>
        <p class="mb-2"><i class="fa fa-user" data-toggle="tooltip" title="Author"></i> <strong>{{ $listing['author'] }}</strong></p>
        <p><i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</p>
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
                    <h4 class="mb-3">Message the Author</h4>
                    <p>Tell the author something about yourself.</p>
                    <textarea class="form-control bg-light border-0" id="message" name="message" rows="5" placeholder="Enter message" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Apply</button>
            </form>
        </x-card>
    @endif

</x-layout>
