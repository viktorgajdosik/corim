<x-layout>

    <h2>Apply for the research work<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="right"
        data-content="You can apply for this research work by sending an application message to the author of this research listing."
        ></i></h2>
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
                    <textarea class="form-control bg-light border-0" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Apply</button>
            </form>
        </x-card>
    @endif

</x-layout>
