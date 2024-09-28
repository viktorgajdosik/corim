<x-search/>
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
    <a href="{{ route('register') }}">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="d-flex w-100 bg-primary" style="height: 300px; border-radius: 15px;">
                <div class="carousel-caption d-flex justify-content-center" style="height: 100%; display: flex; flex-direction: column; bottom: 0; padding-bottom: 0; padding-top: 1rem;">
                    <h1 class="display-6 text-uppercase text-start"><span style="color: white;">CORI.M</span><span class="text-body-secondary"> Collaborative Research Initiative in Medicine.</span></h1>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-flex w-100 bg-primary" style="height: 300px; border-radius: 15px;">
                <div class="carousel-caption d-flex justify-content-center" style="height: 100%; display: flex; flex-direction: column; bottom: 0; padding-bottom: 0; padding-top: 1rem;">
                    <h1 class="display-6 text-uppercase text-start"><span class="text-body-secondary">Proactive individuals facilitating</span><span style="color: white;"> collaborative progress</span><span class="text-body-secondary"> in medical research.</span></h1>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-flex w-100 bg-secondary" style="height: 300px; border-radius: 15px;">
                <div class="carousel-caption d-flex justify-content-center" style="height: 100%; display: flex; flex-direction: column; bottom: 0; padding-bottom: 0; padding-top: 1rem;">
                    <h1 class="display-6 text-uppercase text-start"><span class="text-body-secondary">Researchers can</span> <span style="color: white;">create and showcase listings</span><span class="text-body-secondary"> of their ongoing research projects.</span></h1>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-flex w-100 bg-primary" style="height: 300px; border-radius: 15px;">
                <div class="carousel-caption d-flex justify-content-center" style="height: 100%; display: flex; flex-direction: column; bottom: 0; padding-top: 1rem; padding-bottom: 0;">
                    <h1 class="display-6 text-uppercase text-start"><span class="text-body-secondary"> Students participate by </span><span style="color: white;">completing research tasks</span> <span class="text-body-secondary">set by the author of the project.</span></h1>
                </div>
            </div>
        </div>
    </div>
</a>

    <!-- Previous and Next Buttons (Hidden but Functional) -->
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

    <p class="text-center mt-3">
        Displaying {{ $listings->firstItem() }} - {{ $listings->lastItem() }} of {{ $listings->total() }} listings
    </p>
</div>
</x-layout>
