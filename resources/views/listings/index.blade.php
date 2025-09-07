@extends('components.layout')

@section('content')

    <!-- Search Bar -->
    <livewire:search-dropdown />

    @guest
 @php
  $slidesRaw = \App\Models\Setting::get('home_carousel_slides');
  $slides = [];
  if (is_string($slidesRaw) && $slidesRaw !== '') {
      $slides = json_decode($slidesRaw, true) ?: [];
  }
  if (!$slides) {
      $slides = [
          ['title'=>'CORIM','subtitle'=>'Collaborative Research Initiative in Medicine','cta_text'=>'Participate','cta_url'=>route('register'),'enabled'=>true],
          ['title'=>'Join Featured Research','subtitle'=>'Contribute to active clinical and basic science projects.','cta_text'=>'Participate','cta_url'=>route('register'),'enabled'=>true],
          ['title'=>'Earn Experience & Credits','subtitle'=>'Build your CV with verifiable research tasks.','cta_text'=>'Participate','cta_url'=>route('register'),'enabled'=>true],
          ['title'=>'Collaborate With Mentors','subtitle'=>'Work directly with researchers and clinicians.','cta_text'=>'Participate','cta_url'=>route('register'),'enabled'=>true],
      ];
  }
  // filter & REINDEX
  $slides = array_values(collect($slides)->filter(fn($s)=>(bool)($s['enabled'] ?? true))->all());
@endphp


    @if(count($slides))
    <!-- Gradient Carousel (under search, above listings) -->
    <div class="my-4">
        <div id="heroCarousel"
             class="carousel slide carousel-fade"
             data-bs-ride="carousel"
             data-bs-interval="6000">

            <!-- Dots / Indicators -->
            <div class="carousel-indicators mb-0" style="z-index: 20;">
              @foreach($slides as $idx => $s)
                <button type="button"
                        data-bs-target="#heroCarousel"
                        data-bs-slide-to="{{ $idx }}"
                        class="{{ $idx===0 ? 'active' : '' }}"
                        @if($idx===0) aria-current="true" @endif
                        aria-label="Slide {{ $idx+1 }}"></button>
              @endforeach
            </div>

            <div class="carousel-inner rounded-4 shadow overflow-hidden position-relative">
              @foreach($slides as $idx => $s)
                <div class="carousel-item {{ $idx===0 ? 'active' : '' }}">
                  <div class="animated-gradient d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="p-4 p-md-5 position-relative" style="z-index: 15;">
                      <h2 class="fw-bold display-6 text-white mb-2">{{ $s['title'] ?? '' }}</h2>
                      @if(!empty($s['subtitle']))
                        <p class="text-white-50 fs-6 mb-4">{{ $s['subtitle'] }}</p>
                      @endif
                      @if(!empty($s['cta_text']) && !empty($s['cta_url']))
                        <a href="{{ $s['cta_url'] }}" class="btn btn-primary px-4">{{ $s['cta_text'] }}</a>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Side-click surfaces (only empty sides, don't block buttons/dots) -->
            <button class="carousel-control-surface start-0" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous"></button>
            <button class="carousel-control-surface end-0" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next"></button>
        </div>
    </div>
    @endif
    @endguest

    <!-- Default Listings -->
    <livewire:default-listings />

@endsection
