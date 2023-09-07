<x-layout>

   @guest

    <div class="jumbotron">
        <h1 class="mb-3">Hello, research community!</h1>
        <p>CORI.M, the Collaborative Research Initiative in Medicine, fosters collaboration among researchers and students from all medical fields.
        Researchers can create and showcase listings of their ongoing research projects, providing a platform for others to engage and contribute.
        CORI.M aims to accelerate the advancement of medical research by connecting proactive individuals and facilitating collaborative breakthroughs.</p>
        <hr class="my-4">
        <p>
            Become a part of a vibrant community driving innovation and progress in medical science.</p>
        <a class="btn btn-primary btn-lg" href="/register" role="button"><i class="fa detone fa-handshake-o fa-bounce mr-2"></i>Join CORI.M</a>
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
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $listings->previousPageUrl() }}" aria-label="Previous">&laquo;</a>
                </li>
            @endif

            @for ($page = max(1, $listings->currentPage() - 2); $page <= min($listings->lastPage(), $listings->currentPage() + 2); $page++)
                <li class="page-item {{ $page == $listings->currentPage() ? 'active custom-active-page' : '' }}">
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
