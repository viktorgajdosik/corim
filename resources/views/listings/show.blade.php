<x-layout>

    <h2>Apply for the research work</h2>
    <br>
    <x-card>
        <h4 class="mb-3"><strong>{{ $listing['title'] }}</strong></h4>
        <p>{{ $listing['description'] }} </p>
        <p class="mb-2"><i class="fa fa-user" data-toggle="tooltip" title="Author"></i> <strong>{{ $listing['author'] }}</strong></p>
        <p><i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</p>
    </x-card>

    <!-- Check if the user has an unprocessed application -->
    @if ($userHasUnprocessedApplication)
        <x-card>
            <h4><strong>Awaiting Application Results</strong></h4>
            <p>You have already applied for this research work. Please wait for the author's response.</p>
        </x-card>
    @else
        <x-card>
            <form action="{{ route('listings.apply', ['listing' => $listing->id]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <h4 class="mb-3"><strong>Message the Author</strong></h4>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Apply</button>
            </form>
        </x-card>
    @endif

    <a href="/" class="btn btn-secondary">Back</a>

</x-layout>
