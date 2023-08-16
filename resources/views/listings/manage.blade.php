<x-layout>

            <h1>Your Listings</h1>

            <br>

              @unless($listings->isEmpty())
              @foreach($listings as $listing)

              <a href="" class="custom-link">

              <x-listing-card :listing="$listing"/>

              </a>

              @endforeach

              @else

              <h4>No Listings Found</h4>

              @endunless

</x-layout>
