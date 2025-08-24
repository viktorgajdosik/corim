@extends('components.layout')

@section('content')
<div class="row align-items-stretch">
  <!-- Personal Information Section -->
  <div class="col-xl-3">
    <x-secondary-heading>Personal Information</x-secondary-heading>
    @livewire('profile-info-card', ['user' => $user], key('profile-info-'.$user->id.'-'.optional($user->updated_at)->timestamp))
  </div>

  <!-- Research Participation Section -->
  <div class="col-xl-9">
    <x-secondary-heading>
      Research Participation
      <i class="fa fa-info-circle ml-2 info-icon"
         data-bs-toggle="popover"
         data-bs-trigger="hover"
         data-bs-placement="bottom"
         data-bs-content="This section displays your participation in the research work created by other authors. You can see your own research listings by clicking 'Management' in the menu.">
      </i>
    </x-secondary-heading>

    @php
      $accepted = $user->acceptedApplications ?? collect();

      // Pending (applied) applications
      $applied = $user->applications()
                      ->where('accepted', false)
                      ->with('listing')
                      ->get();
    @endphp

    <div class="scrollable-listings border-0 h-75">
      @if ($accepted->isEmpty() && $applied->isEmpty())
        <x-text>You have not participated in other author's research work yet, and you have no pending applications.</x-text>
      @else
        {{-- Show APPLIED first --}}
        @foreach ($applied as $application)
          <x-listing-card :listing="$application->listing" status="applied" />
        @endforeach

        {{-- Then PARTICIPATING (accepted) --}}
        @foreach ($accepted as $application)
          <x-listing-card :listing="$application->listing" />
        @endforeach
      @endif
    </div>
  </div>
</div>
@endsection
