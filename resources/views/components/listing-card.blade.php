@props(['listing', 'status' => null])

@php
    $isAuthor = auth()->check() && auth()->id() === $listing->user_id;
@endphp

@livewire(
    'listing-card',
    ['listing' => $listing, 'status' => $status],
    key('listing-card-'.$listing->id.'-'.optional($listing->updated_at)->timestamp.'-'.($status ?? 'none'))
)
