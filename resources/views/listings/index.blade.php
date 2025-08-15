@extends('components.layout')

@section('content')

    <!-- Search Bar -->
    <livewire:search-dropdown />

    @guest

    <!-- Gradient Carousel (under search, above listings) -->
    <div class="my-4">
        <div id="heroCarousel"
             class="carousel slide carousel-fade"
             data-bs-ride="carousel"
             data-bs-interval="6000">

            <!-- Dots / Indicators -->
            <div class="carousel-indicators mb-0" style="z-index: 20;">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>

            <div class="carousel-inner rounded-4 shadow overflow-hidden position-relative">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <div class="animated-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5 position-relative" style="z-index: 15;">
                            <h2 class="fw-bold display-6 text-white mb-2">CORIM</h2>
                            <p class="text-white-50 fs-6 mb-4">Collaborative Research Initiative in Medicine</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4">Participate</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <div class="animated-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5 position-relative" style="z-index: 15;">
                            <h2 class="fw-bold display-6 text-white mb-2">Join Featured Research</h2>
                            <p class="text-white-50 fs-6 mb-4">Contribute to active clinical and basic science projects.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4">Participate</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item">
                    <div class="animated-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5 position-relative" style="z-index: 15;">
                            <h2 class="fw-bold display-6 text-white mb-2">Earn Experience & Credits</h2>
                            <p class="text-white-50 fs-6 mb-4">Build your CV with verifiable research tasks.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4">Participate</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="carousel-item">
                    <div class="animated-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5 position-relative" style="z-index: 15;">
                            <h2 class="fw-bold display-6 text-white mb-2">Collaborate With Mentors</h2>
                            <p class="text-white-50 fs-6 mb-4">Work directly with researchers and clinicians.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4">Participate</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side-click surfaces (only empty sides, don't block buttons/dots) -->
            <button class="carousel-control-surface start-0" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous"></button>
            <button class="carousel-control-surface end-0" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next"></button>
        </div>
    </div>
      @endguest

    <!-- Default Listings -->
    <livewire:default-listings />

@endsection
