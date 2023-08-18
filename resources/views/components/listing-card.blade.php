
<x-card>
            <h4 class="mb-3">{{ $listing->title }}</h4>
            <p class="mb-2"><i class="fa fa-user" data-toggle="tooltip" title="Author"></i> {{$listing['author']}}</p>
            <p><i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</p>

        </x-card>
