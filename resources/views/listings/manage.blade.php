<x-layout>

            <h2>Your Listings<i
                class="fa fa-info-circle ml-2 info-icon"
                data-toggle="popover"
                data-trigger="hover"
                data-placement="bottom"
                data-content="This section displays the listings you have created. You can see the research work you participate in on right side of your Profile page. You can get to your profile page by clicking 'Profile' in the menu."
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
