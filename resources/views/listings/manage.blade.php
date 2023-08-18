<x-layout>

            <h1>Your Listings</h1>

            @if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

            <br>

              @unless($listings->isEmpty())
              @foreach($listings as $listing)
              <a href="{{ url('/listings/show-manage', ['listing' => $listing]) }}" class="custom-link">
                  <x-listing-card :listing="$listing"/>
              </a>
          @endforeach

              @else

              <h4>No Listings Found</h4>

              @endunless

</x-layout>
