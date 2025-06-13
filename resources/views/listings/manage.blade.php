@extends('components.layout')
@section('content')


            <x-secondary-heading>Your Listings<i
                class="fa fa-info-circle ms-1 info-icon"
                data-bs-toggle="popover"
                data-bs-trigger="hover"
                data-bs-placement="bottom"
                data-bs-content="This section displays the listings you have created."
                ></i></x-secondary-heading>

            @if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

              @unless($listings->isEmpty())
              @foreach($listings as $listing)
              <a href="{{ url('/listings/show-manage', ['listing' => $listing]) }}" class="custom-link">
                  <x-listing-card :listing="$listing"/>
              </a>
          @endforeach

              @else

              <x-text>You have not created any listing yet.</x-text>

              @endunless

@endsection
