<x-search></x-search>
<x-layout>

            <h2>Your Listings<i
                class="fa fa-info-circle ml-2 info-icon"
                data-toggle="popover"
                data-trigger="hover"
                data-placement="bottom"
                data-content="This section displays the listings you have created."
                ></i></h2>

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

              <p>You have not created any listing yet.</p>

              @endunless

</x-layout>
