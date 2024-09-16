<x-search></x-search>
<x-layout>
    <div class="row align-items-stretch">

        <!-- Personal Information Section -->
        <div class="col-md-4 mb-5"> <!-- Set to 4 for 1/3 width -->
            <h3>Personal Information</h3>
            <br>
            <x-card class="personal-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-3">{{ $user->name }} </h5>
                        <p><i class="mr-1 fa fa-envelope"></i> {{ $user->email }} </p>
                        <p><i class="mr-1 fa fa-building"></i> {{ $user->department }} </p>
                        <p><i class="mr-1 fa fa-star"
                            data-toggle="popover"
                            data-trigger="hover"
                            data-placement="bottom"
                            data-content="You receive research points from the authors after successfully completing individual research tasks."></i> 35</p>
                    </div>
                    <a href="/users/edit-profile" class="btn btn-secondary btn-circle">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
            </x-card>
                <div class="mt-3">
                    <a href="" class="btn btn-secondary mb-2">Download Information</a>
                    <form method="POST" action="/users/profile">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your profile?')">Delete Profile</button>
                    </form>
                </div>
        </div>

        <!-- Research Participation Section -->
        <div class="col-md-8 mb-5"> <!-- Set to 8 for 2/3 width -->
            <h3>Research Participation
                <i class="fa fa-info-circle ml-2 info-icon"
                   data-toggle="popover"
                   data-trigger="hover"
                   data-placement="bottom"
                   data-content="This section displays your participation in the research work created by other authors. You can see your own research listings by clicking 'Management' in the menu.">
                </i>
            </h3>
            <br>
            <div class="scrollable-listings h-75">
                @if ($user->acceptedApplications->isEmpty())
                    <p>You have not participated in other author's research work yet.</p>
                @else
                    @foreach ($user->acceptedApplications as $application)
                        <x-listing-card :listing="$application->listing" />
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-layout>
