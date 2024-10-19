
<x-layout>

            <h3>Your Listings<i
                class="fa fa-info-circle ms-1 info-icon"
                data-bs-toggle="popover"
                data-bs-trigger="hover"
                data-bs-placement="bottom"
                data-bs-content="This section displays the listings you have created."
                ></i></h3>

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
