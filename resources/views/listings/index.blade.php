<x-layout>


    @guest
    <div id="carouselAutoplaying" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselAutoplaying" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselAutoplaying" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselAutoplaying" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselAutoplaying" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>

        <!-- Carousel Items -->
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active" style="background-image: url('{{ asset('images/car-bg-1.jpg') }}'); background-size: cover; background-position: center; height: 50vh; border-radius: 15px;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center" style="height: 100%; text-align: center;">
                    <h1 class="display-5 text-white" style="font-weight: normal;">CORIM - Collaborative Research Initiative in Medicine.</h1>
                    <a href="{{ route('register') }}" class="btn btn-primary mt-3">Participate</a>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item" style="background-image: url('{{ asset('images/car-bg-2.jpg') }}'); background-size: cover; background-position: center; height: 50vh; border-radius: 15px;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center" style="height: 100%; text-align: center;">
                    <h1 class="display-5 text-white" style="font-weight: normal;">Researchers can create and showcase listings of their ongoing research projects.</h1>
                    <a href="{{ route('register') }}" class="btn btn-primary mt-3">Participate</a>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-item" style="background-image: url('{{ asset('images/car-bg-3.jpg') }}'); background-size: cover; background-position: center; height: 50vh; border-radius: 15px;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center" style="height: 100%; text-align: center;">
                    <h1 class="display-5 text-white" style="font-weight: normal;">Students participate by completing research tasks set by the author of the project.</h1>
                    <a href="{{ route('register') }}" class="btn btn-primary mt-3">Participate</a>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="carousel-item" style="background-image: url('{{ asset('images/car-bg-4.jpg') }}'); background-size: cover; background-position: center; height: 50vh; border-radius: 15px;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center" style="height: 100%; text-align: center;">
                    <h1 class="display-5 text-white" style="font-weight: normal;">Proactive individuals facilitating collaborative progress in medical research.</h1>
                    <a href="{{ route('register') }}" class="btn btn-primary mt-3">Participate</a>
                </div>
            </div>
        </div>

        <!-- Previous and Next Buttons -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselAutoplaying" data-bs-slide="prev" style="opacity: 0; pointer-events: auto;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselAutoplaying" data-bs-slide="next" style="opacity: 0; pointer-events: auto;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
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

    <div class="pt-4 pb-4">
        <ul class="pagination justify-content-center">
            @if ($listings->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link border-0 mr-3 bg-transparent">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0" href="{{ $listings->previousPageUrl() }}" aria-label="Previous">&laquo;</a>
                </li>
            @endif

            @for ($page = max(1, $listings->currentPage() - 2); $page <= min($listings->lastPage(), $listings->currentPage() + 2); $page++)
                <li class="page-item {{ $page == $listings->currentPage() ? 'active custom-active-page' : '' }}">
                    <a class="page-link jajco border-0 rounded" href="{{ $listings->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            @if ($listings->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0" href="{{ $listings->nextPageUrl() }}" aria-label="Next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link border-0 ml-3 bg-transparent">&raquo;</span>
                </li>
            @endif
        </ul>

        <p class="text-center pagination-text mt-3">
            Displaying {{ $listings->firstItem() }} - {{ $listings->lastItem() }} of {{ $listings->total() }} listings
        </p>
    </div>
</x-layout>
