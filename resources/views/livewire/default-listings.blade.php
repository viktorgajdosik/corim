<div class="mt-2">
    <div class="listings-container">
        @foreach($listings as $listing)
            <x-listing-card :listing="$listing" />
        @endforeach
    </div>

    <!-- Load More Button -->
    @if($listings->hasMorePages())
        <div class="text-center mt-4">
            <button wire:click="loadMore" class="btn btn-primary btn-md">
                Load More
                <span wire:loading wire:target="loadMore" class="spinner-grow spinner-grow-sm ms-2"></span>
            </button>
        </div>
    @endif
</div>
