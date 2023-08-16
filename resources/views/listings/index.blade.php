<x-layout>

    <h1>Explore Research Offers</h1>

    <br>
    @unless (count($listings) == 0)
    @foreach($listings as $listing)
    <a href="/listings/{{$listing->id}}" class="custom-link">
    @props(['listing'])
    <x-listing-card :listing="$listing"/>
    </a>
    @endforeach
    @else
    <p>No listings found</p>
    @endunless
    <div class="mt-6 p-4">
        <ul class="pagination justify-content-center">
            @if ($listings->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $listings->previousPageUrl() }}" aria-label="Previous">&laquo;</a>
                </li>
            @endif

            @for ($page = max(1, $listings->currentPage() - 2); $page <= min($listings->lastPage(), $listings->currentPage() + 2); $page++)
                <li class="page-item {{ $page == $listings->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $listings->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            @if ($listings->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $listings->nextPageUrl() }}" aria-label="Next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>

        <p class="text-center mt-3">
            Displaying {{ $listings->firstItem() }} - {{ $listings->lastItem() }} of {{ $listings->total() }} listings
        </p>
    </div>
</x-layout>
