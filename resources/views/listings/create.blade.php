@extends('components.layout')

@section('content')
<x-secondary-heading>Create Research Listing
        <i class="fa fa-info-circle ml-2 info-icon"
           data-bs-toggle="popover"
           data-bs-trigger="hover"
           data-bs-placement="bottom"
           data-bs-content="This section enables you to list your research and include all the necessary information for potential participants. Note that you can always edit the listing later after creation.">
        </i>
    </x-secondary-heading>
    <livewire:create-listing />
@endsection
