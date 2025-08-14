@extends('components.layout')

@section('content')

    <!-- Search Bar -->
    <livewire:search-dropdown />

    <!-- Gradient Carousel (under search, above listings) -->
    <div class="container my-4">
        <div id="heroCarousel"
             class="carousel slide carousel-fade"
             data-bs-ride="carousel"
             data-bs-interval="6000">

            <!-- Dots / Indicators -->
            <div class="carousel-indicators mb-0">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>

            <div class="carousel-inner rounded-4 shadow overflow-hidden">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <div class="carousel-card-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5">
                            <h2 class="fw-bold display-6 text-white mb-2">CORIM</h2>
                            <p class="text-white-50 fs-6 mb-4">Collaborative Research Initiative in Medicine</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4 position-relative" style="z-index: 10;">Participate</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <div class="carousel-card-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5">
                            <h2 class="fw-bold display-6 text-white mb-2">Join Featured Research</h2>
                            <p class="text-white-50 fs-6 mb-4">Contribute to active clinical and basic science projects.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4 position-relative" style="z-index: 10;">Participate</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item">
                    <div class="carousel-card-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5">
                            <h2 class="fw-bold display-6 text-white mb-2">Earn Experience & Credits</h2>
                            <p class="text-white-50 fs-6 mb-4">Build your CV with verifiable research tasks.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4 position-relative" style="z-index: 10;">Participate</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="carousel-item">
                    <div class="carousel-card-gradient d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="p-4 p-md-5">
                            <h2 class="fw-bold display-6 text-white mb-2">Collaborate With Mentors</h2>
                            <p class="text-white-50 fs-6 mb-4">Work directly with researchers and clinicians.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary px-4 position-relative" style="z-index: 10;">Participate</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side-click surfaces (prev/next over full height halves) -->
            <button class="carousel-control-surface start-0" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous"></button>
            <button class="carousel-control-surface end-0" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next"></button>
        </div>
    </div>

    <!-- Default Listings -->
    <livewire:default-listings />

    {{-- Styles --}}
    <style>
      /* Gradient card background */
      .carousel-card-gradient {
        min-height: 260px;
        background: linear-gradient(90deg, #E96479, #8A80FF);
      }

      /* Indicators */
      #heroCarousel .carousel-indicators [data-bs-target] {
        width: 10px;
        height: 10px;
        border-radius: 50%;
      }

      /* Side control click zones */
      .carousel-control-surface {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 50%;
        background: transparent;
        border: 0;
        z-index: 5;
        pointer-events: auto;
      }
      .carousel-control-surface.start-0 { left: 0; }
      .carousel-control-surface.end-0 { right: 0; }

      /* Make sure controls don't block child content */
      .carousel-inner * {
        pointer-events: auto;
      }

      /* Cursor hints */
      @media (min-width: 992px) {
        .carousel-control-surface.start-0 { cursor: w-resize; }
        .carousel-control-surface.end-0 { cursor: e-resize; }
      }
    </style>

@endsection
