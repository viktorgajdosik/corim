<x-search></x-search>
<x-layout>

   @guest

    <div class="jumbotron fade-in">
        <h1 class="mb-3">Collaborative Research</h1>
        <p class="lead"><strong>Collaborative Research Initiative in Medicine (CORI.M)</strong> fosters cooperation among researchers and students from all medical fields.</p><hr>
        <p class="lead">Researchers can create and showcase listings of their ongoing research projects, providing a platform for others to engage and contribute.</p><hr>
        <p class="lead">The aim is to accelerate the advancement of medical science by connecting proactive individuals and facilitating collaborative breakthroughs.</p>
        <a class="btn btn-lg mt-2 btn-primary jumbotron-button" href="/register" role="button"><i class="fa fa-handshake-o fa-bounce mr-2"></i>Join CORI.M</a>
      </div>

      <br>
      @endguest
    @unless (count($listings) == 0)
    @foreach($listings as $listing)
    <a href="{{ auth()->id() === $listing->user_id ? url('/listings/show-manage', ['listing' => $listing]) : url('/listings', ['listing' => $listing]) }}" class="custom-link">
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
                    <span class="page-link previous border-0 mr-3">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0" href="{{ $listings->previousPageUrl() }}" aria-label="Previous">&laquo;</a>
                </li>
            @endif

            @for ($page = max(1, $listings->currentPage() - 2); $page <= min($listings->lastPage(), $listings->currentPage() + 2); $page++)
                <li class="page-item {{ $page == $listings->currentPage() ? 'active custom-active-page' : '' }}">
                    <a class="page-link current border-0" href="{{ $listings->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            @if ($listings->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0" href="{{ $listings->nextPageUrl() }}" aria-label="Next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link next border-0 ml-3">&raquo;</span>
                </li>
            @endif
        </ul>

        <p class="text-center mt-3">
            Displaying {{ $listings->firstItem() }} - {{ $listings->lastItem() }} of {{ $listings->total() }} listings
        </p>
    </div>
</x-layout>
