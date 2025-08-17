@php
    $isAuthor = auth()->check() && auth()->id() === $listing->user_id;
@endphp

@props(['listing'])

@livewire('listing-card', ['listing' => $listing], key('listing-card-'.$listing->id.'-'.optional($listing->updated_at)->timestamp))
